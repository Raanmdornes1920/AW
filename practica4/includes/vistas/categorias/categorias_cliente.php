<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$catSA = new CategoriaSA($db_connection);
$categorias = $catSA->obtenerTodas();

$tituloPagina = "Categorías";
<<<<<<< HEAD
$css = [RAIZ_APP . "/css/default.css"]; 
$js = []; 
$header = __DIR__ . "/../comun/header.php"; 
=======
$css = [RAIZ_APP . "/css/default.css"];
$js = [];
$header = __DIR__ . "/../comun/header.php";
>>>>>>> angela
$claseMain = "contenedor-centro";

$htmlCategorias = "";
foreach ($categorias as $cat) {
    $nombre = htmlspecialchars($cat->getNombre());
    $id = $cat->getId();
    $imagen = RAIZ_APP . "/img/categorias/" . ($cat->getImagen() ?: 'categoria_default.png');
    $tipo = (isset($_GET['tipo'])?"&tipo=".$_GET['tipo']:"");
<<<<<<< HEAD
    
=======

>>>>>>> angela
    $htmlCategorias .= <<<EOF
        <a href="../productos/productos_cliente.php?id_categoria=$id$tipo" class="tarjeta-item">
            <img src="$imagen" alt="$nombre">
            <h4>$nombre</h4>
        </a>
EOF;
}
$tipo = (isset($_GET['tipo'])?"?tipo=".$_GET['tipo']:"");
$contenidoPrincipal = <<<EOF
<div class="contenedor-pedido">
    <aside class="menu-lateral">
        <h3>Menú</h3>
        <ul>
            <li><a href="categorias_cliente.php$tipo">Categorías</a></li>
            <li><a href="../productos/productos_cliente.php$tipo">Todos los productos</a></li>
        </ul>
    </aside>

    <section class="cuadricula-objetos">
        $htmlCategorias
    </section>
</div>
EOF;

$js = [RAIZ_APP . "/js/script.js"];
require("../comun/plantilla.php");