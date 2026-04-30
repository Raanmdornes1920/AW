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
    $productos = $prodSA->getCatalogoCliente();
    $tituloPagina = "Todos los productos";
}

$css = [];
$js = [];
$header = __DIR__ . "/../comun/header.php";
$claseMain = "contenedor-centro";

$htmlProductos = "";
if (empty($productos)) {
    $htmlProductos = "<div class='col-12'><div class='alert alert-info mb-0'>No hay productos disponibles en esta sección.</div></div>";
} else {
    foreach ($productos as $p) {
        $nombre = htmlspecialchars($p->getNombre());
        $precio = number_format($p->getPrecioFinal(), 2);
        $imagen = RAIZ_APP . "/img/productos/" . $p->getImagen();
        $idProd = $p->getId();
        $tipo = (isset($_GET['tipo'])?"&tipo=".$_GET['tipo']:"");
        $_tipo = (isset($_GET['tipo'])?"?tipo=".$_GET['tipo']:"");

        $htmlProductos .= <<<EOF
        <div class="col">
        <article class="card h-100 shadow-sm">
            <a href="productos_detalle.php?id=$idProd$tipo" class="text-decoration-none text-dark">
                <img src="$imagen" class="card-img-top card-img-fixed" alt="$nombre">
                <div class="card-body pb-2">
                    <h2 class="h5 card-title">$nombre</h2>
                    <p class="fs-5 fw-semibold text-success mb-0">$precio €</p>
                </div>
            </a>
            <div class="card-footer bg-white border-0 pt-0">
            <form action="../pedidos/apoyo/procesar_carrito.php$_tipo" method="POST">
                <input type="hidden" name="accion" value="add">
                <input type="hidden" name="id_producto" value="$idProd">
                <input type="hidden" name="cantidad" value="1">
                <button type="submit" class="btn btn-primary w-100 touch-action">Añadir</button>
            </form>
            </div>
        </article>
        </div>
EOF;
    }
}

$tipo = (isset($_GET['tipo'])?"?tipo=".$_GET['tipo']:"");

$contenidoPrincipal = <<<EOF
<div class="row g-4">
    <aside class="col-12 col-lg-3">
        <div class="list-group shadow-sm">
            <a class="list-group-item list-group-item-action" href="../categorias/categorias_cliente.php$tipo">Categorías</a>
            <a class="list-group-item list-group-item-action active" href="productos_cliente.php$tipo">Todos los productos</a>
        </div>
    </aside>

    <section class="col-12 col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h2 mb-0">$tituloPagina</h1>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
            $htmlProductos
        </div>
    </section>
</div>
EOF;

$js = [RAIZ_APP . "/js/script.js"];
require("../comun/plantilla.php");
