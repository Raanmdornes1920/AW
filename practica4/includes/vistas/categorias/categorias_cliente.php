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
$css = [];
$js = [];
$header = __DIR__ . "/../comun/header.php";
$claseMain = "contenedor-centro";

$htmlCategorias = "";
foreach ($categorias as $cat) {
    $nombre = htmlspecialchars($cat->getNombre());
    $id = $cat->getId();
    $imagen = RAIZ_APP . "/img/categorias/" . ($cat->getImagen() ?: 'categoria_default.jpg');
    $tipo = (isset($_GET['tipo'])?"&tipo=".$_GET['tipo']:"");

    $htmlCategorias .= <<<EOF
        <div class="col">
        <a href="../productos/productos_cliente.php?id_categoria=$id$tipo" class="card h-100 text-decoration-none text-dark shadow-sm">
            <img src="$imagen" class="card-img-top card-img-fixed" alt="$nombre">
            <div class="card-body">
                <h2 class="h5 card-title mb-0">$nombre</h2>
            </div>
        </a>
        </div>
EOF;
}
$tipo = (isset($_GET['tipo'])?"?tipo=".$_GET['tipo']:"");
$contenidoPrincipal = <<<EOF
<div class="row g-4">
    <aside class="col-12 col-lg-3">
        <div class="list-group shadow-sm">
            <a class="list-group-item list-group-item-action active" href="categorias_cliente.php$tipo">Categorías</a>
            <a class="list-group-item list-group-item-action" href="../productos/productos_cliente.php$tipo">Todos los productos</a>
        </div>
    </aside>

    <section class="col-12 col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h2 mb-0">Categorías</h1>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
            $htmlCategorias
        </div>
    </section>
</div>
EOF;

$js = [RAIZ_APP . "/js/script.js"];
require("../comun/plantilla.php");
