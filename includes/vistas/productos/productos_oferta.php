<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/");
    exit;
}

$prodSA = new ProductoSA($db_connection);
$ofertaSA = new OfertaSA($db_connection);

$id_cat = $_GET['id_categoria'] ?? null;
$id_oferta = filter_input(INPUT_GET, 'id_oferta', FILTER_SANITIZE_NUMBER_INT);
$oferta = $id_oferta ? $ofertaSA->buscarPorId($id_oferta) : null;

$productos = [];
if ($oferta) {
    $tituloPagina = "Productos de la oferta: " . htmlspecialchars($oferta->getNombre());
    //la oferta tiene array de prodcutos, iteramos sobre ellos
    foreach ($oferta->getProductos() as $p_oferta) {
        $productoDTO = $prodSA->buscarProducto($p_oferta['id_producto']);
        if ($productoDTO) {
            $productos[] = $productoDTO;
        }
    }
} else {
    $tituloPagina = "Oferta no encontrada";
}



// Se han eliminado las líneas que sobreescribían $productos

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
        $tipo = (isset($_GET['tipo']) ? "&tipo=" . $_GET['tipo'] : "");
        $_tipo = "?id_oferta=" . $id_oferta . (isset($_GET['tipo']) ? "&tipo=" . $_GET['tipo'] : "");

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
            <form class="form-ajax-carrito" action="../pedidos/apoyo/procesar_carrito.php$_tipo" method="POST">
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

$tipo = (isset($_GET['tipo']) ? "?tipo=" . $_GET['tipo'] : "");

$accionOferta = "";
if ($oferta) {
    $carrito = $_SESSION['carrito'] ?? [];
    $veces = $ofertaSA->vecesAplicable($oferta, $carrito);

    if ($veces > 0) {
        $accionOferta = <<<EOF
        <div class="alert alert-success d-flex flex-column flex-sm-row justify-content-between align-items-center mb-4 gap-3">
            <div>
                <i class="bi bi-check-circle-fill me-2"></i><strong>¡Requisitos cumplidos!</strong> Puedes aplicar esta oferta a tu pedido.
            </div>
            <form method="POST" action="../pedidos/apoyo/procesar_oferta.php" class="m-0">
                <input type="hidden" name="accion" value="aplicar">
                <input type="hidden" name="id_oferta" value="{$id_oferta}">
                <button type="submit" class="btn btn-success text-nowrap">Aplicar oferta x{$veces}</button>
            </form>
        </div>
EOF;
    } else {
        $accionOferta = <<<EOF
        <div class="alert alert-warning d-flex justify-content-between align-items-center mb-4">
            <div>
                <i class="bi bi-info-circle-fill me-2"></i>Añade las cantidades indicadas en la oferta para poder aplicarla.
            </div>
            <a href="../ofertas/oferta_detalle.php?id={$id_oferta}" class="btn btn-outline-dark btn-sm text-nowrap">Ver detalles</a>
        </div>
EOF;
    }
}

$contenidoPrincipal = <<<EOF
<div class="row g-4">
    <section class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h2 mb-0">$tituloPagina</h1>
            <a href="../ofertas/ofertas_cliente.php" class="btn btn-outline-secondary">Volver a ofertas</a>
        </div>
        <div id="contenedor-banner-oferta">
            $accionOferta
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
            $htmlProductos
        </div>
    </section>
</div>
EOF;

$js = [RAIZ_APP . "/js/script.js"];
require("../comun/plantilla.php");