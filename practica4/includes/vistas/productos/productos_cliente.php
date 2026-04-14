<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/");
    exit;
}

$prodSA = new ProductoSA($db_connection);
$id_cat = $_GET['id_categoria'] ?? null;

if ($id_cat) {
    $productos = $prodSA->buscarPorCategoria($id_cat); 
    $tituloPagina = "Productos de la categoría";
} else {
    $productos = $prodSA->listarTodos(); 
    $tituloPagina = "Todos los productos";
}

$css = [];
$js = []; 
$header = __DIR__ . "/../comun/header.php"; 
$claseMain = "contenedor-centro";

$htmlProductos = "";
if (empty($productos)) {
    $htmlProductos = "<p>No hay productos disponibles en esta sección.</p>";
} else {
    foreach ($productos as $p) {
        $nombre = htmlspecialchars($p->getNombre());
        $precio = number_format($p->getPrecioFinal(), 2);
        $imagen = RAIZ_APP . "/img/productos/" . $p->getImagen();
        $idProd = $p->getId();
        $tipo = (isset($_GET['tipo'])?"&tipo=".$_GET['tipo']:"");
        $_tipo = (isset($_GET['tipo'])?"?tipo=".$_GET['tipo']:"");

        $htmlProductos .= <<<EOF
        <div class="tarjeta-item">
            <a href="productos_detalle.php?id=$idProd$tipo" style="text-decoration:none; color:inherit;">
                <img src="$imagen" alt="$nombre">
                <h4>$nombre</h4>
            </a>
            <p class="precio">$precio €</p>
            <form action="../pedidos/apoyo/procesar_carrito.php$_tipo" method="POST" class="form-boton-pedido-prod">
                <input type="hidden" name="accion" value="add">
                <input type="hidden" name="id_producto" value="$idProd">
                <input type="hidden" name="cantidad" value="1">
                <button type="submit" class="boton-iniciar-pedido" style="margin-bottom: 15px;">Añadir</button>
            </form>
        </div>
EOF;
    }
}

$tipo = (isset($_GET['tipo'])?"?tipo=".$_GET['tipo']:"");

$contenidoPrincipal = <<<EOF
<div class="contenedor-pedido">
    <aside class="menu-lateral">
        <h3>Menú</h3>
        <ul>
            <li><a href="../categorias/categorias_cliente.php$tipo">Categorías</a></li>
            <li><a href="productos_cliente.php$tipo">Todos los productos</a></li>
        </ul>
    </aside>

    <section class="cuadricula-objetos">
        $htmlProductos
    </section>
</div>
EOF;

$js = [RAIZ_APP . "/js/script.js"];
require("../comun/plantilla.php");