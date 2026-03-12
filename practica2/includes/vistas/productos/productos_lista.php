<?php
require_once '../../config.php'; // Cambiado a ../ porque está en vistas/productos/
session_start();

$sa = new ProductoSA($db_connection);
// Usamos el método que filtra solo los productos ofertados/activos
$productos = $sa->getCatalogoCliente();

$tituloPagina = "Nuestra Carta - Bistro FDI";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";

$htmlProductos = "";

if (empty($productos)) {
    $htmlProductos = "<p>Actualmente no hay productos disponibles en la carta.</p>";
} else {
    foreach($productos as $p) {
        $img = RUTA_IMG . "/productos/" . htmlspecialchars($p->getImagen());
        $nombre = htmlspecialchars($p->getNombre());
        $precio = number_format($p->getPrecioFinal(), 2);
        $id = $p->getId();

        // Diseño tipo "Card" para el cliente, sin botones de edición
        $htmlProductos .= <<<EOF
        <div class="tarjeta-producto">
            <div class="imagen-contenedor">
                <img src="$img" alt="$nombre">
            </div>
            <div class="info-producto">
                <h3>$nombre</h3>
                <p class="precio">$precio €</p>
                <a href="producto_detalle.php?id=$id" class="boton-ver">Ver detalles</a>
            </div>
        </div>
EOF;
    }
}

$contenidoPrincipal = <<<EOF
    <section class="seccion-carta">
        <h1>🍽️ Nuestra Carta</h1>
        <p class="subtitulo">Descubre nuestras especialidades seleccionadas para ti.</p>
        
        <div class="grid-productos">
            $htmlProductos
        </div>
    </section>
EOF;

$js = [RAIZ_APP . "/js/script.js"];

require("../comun/plantilla.php");