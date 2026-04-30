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
$css = [];
$js = [];
$header = __DIR__ . "/../comun/header.php";
$claseMain = "contenedor-centro";

$imagenes = $producto->getImagenesArray();
$htmlImagenes = "";
foreach ($imagenes as $key => $img) {
    $active = ($key === 0) ? "active" : "";
    $ruta = RAIZ_APP . "/img/productos/" . $img;
    $htmlImagenes .= "<div class='carousel-item {$active}'><img src='{$ruta}' class='d-block w-100 object-fit-cover rounded' style='max-height: 460px;' alt='" . htmlspecialchars($producto->getNombre()) . "'></div>";
}

$variables ="?id_producto=" . $id . "&accion=add" . (isset($_GET['tipo'])?"&tipo=".$_GET['tipo']:"");

$contenidoPrincipal = <<<EOF
<div class="row g-4 align-items-start">
    <div class="col-12 col-lg-7">
        <div id="productoCarousel" class="carousel slide shadow-sm rounded bg-white p-2">
            <div class="carousel-inner">
            $htmlImagenes
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#productoCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#productoCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
    </div>

    <div class="col-12 col-lg-5">
        <form class="card shadow-sm" action="../pedidos/apoyo/procesar_carrito.php$variables" method="POST">
            <div class="card-body">
                <h1 class="card-title">{$producto->getNombre()}</h1>
                <p class="card-text text-secondary">{$producto->getDescripcion()}</p>
                <p class="display-6 fw-semibold text-success">{$producto->getPrecioFinal()} €</p>

                <label class="form-label fw-semibold" for="cantidad">Cantidad</label>
                <div class="input-group input-group-lg mb-3">
                    <button class="btn btn-outline-secondary" type="button" onclick="modificarCantidad(-1)">-</button>
                    <input type="number" class="form-control text-center" id="cantidad" name="cantidad" value="1" min="1" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="modificarCantidad(1)">+</button>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">Añadir al carrito</button>
            </div>
        </form>
    </div>
</div>
EOF;

$js = [
    RAIZ_APP . "/js/producto.js",
    RAIZ_APP . "/js/script.js"
];

require("../comun/plantilla.php");
