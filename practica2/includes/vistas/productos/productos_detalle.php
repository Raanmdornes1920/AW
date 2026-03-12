<?php
require_once '../../config.php';
session_start();

$prodSA = new ProductoSA($db_connection);
$id = $_GET['id'] ?? null;

$producto = $id ? $prodSA->obtenerPorId($id) : null;

if (!$producto) {
    header("Location: productos_cliente.php");
    exit;
}

$tituloPagina = $producto->getNombre();
$css = [RAIZ_APP . "/css/default.css"];
$js = []; 
$header = __DIR__ . "/../comun/header.php";
$claseMain = "contenedor-centro";

$imagenes = $producto->getImagenesArray();
$htmlImagenes = "";
foreach ($imagenes as $key => $img) {
    $active = ($key === 0) ? "active" : "";
    $ruta = RAIZ_APP . "/img/productos/" . $img;
    $htmlImagenes .= "<img src='$ruta' class='img-carrusel $active' data-index='$key'>";
}

$contenidoPrincipal = <<<EOF
<div class="detalle-producto-centrado">
    <div class="carrusel-contenedor">
        <div class="carrusel">
            $htmlImagenes
            <button class="btn-carrusel prev" onclick="cambiarImagen(-1)">&#10094;</button>
            <button class="btn-carrusel next" onclick="cambiarImagen(1)">&#10095;</button>
        </div>
    </div>
    
    <div class="info-detalle">
        <h1>{$producto->getNombre()}</h1>
        <p class="descripcion">{$producto->getDescripcion()}</p>
        <p class="precio-detalle">{$producto->getPrecioFinal()} €</p>
        
        <div class="controles-pedido">
            <div class="selector-cantidad">
                <button type="button" onclick="modificarCantidad(-1)">-</button>
                <input type="number" id="cantidad" value="1" min="1" readonly>
                <button type="button" onclick="modificarCantidad(1)">+</button>
            </div>
            <button class="boton-iniciar-pedido btn-carrito">Añadir al carrito</button>
        </div>
    </div>
</div>
EOF;

$js = [
    RAIZ_APP . "/js/producto.js",
    RAIZ_APP . "/js/script.js"
];

require("../comun/plantilla.php");