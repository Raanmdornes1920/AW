<?php
require_once '../../config.php';
session_start();

$id = $_GET['id'] ?? 0;
$sa = new ProductoSA($db_connection);
$p = $sa->buscarProducto($id);

if (!$p) {
    header("Location: productos_lista.php"); exit;
}

$tituloPagina = htmlspecialchars($p->getNombre()) . " - Bistro FDI";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";

$img = RUTA_IMG . "/productos/" . htmlspecialchars($p->getImagen());
$nombre = htmlspecialchars($p->getNombre());
$desc = nl2br(htmlspecialchars($p->getDescripcion()));
$precio = number_format($p->getPrecioFinal(), 2);

$contenidoPrincipal = <<<EOF
    <div class="detalle-producto">
        <img src="$img" width="300">
        <h1>$nombre</h1>
        <p class="precio-detalle">Precio: $precio €</p>
        <p class="descripcion-detalle">$desc</p>
        <a href="productos_lista.php" class="boton-volver">⬅ Volver a la carta</a>
    </div>
EOF;

$js = [RAIZ_APP . "/js/script.js"];

require("../comun/plantilla.php");