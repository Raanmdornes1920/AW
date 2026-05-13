<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$ofertaSA = new OfertaSA($db_connection);
$carrito = $_SESSION['carrito'] ?? [];
$activadas = $_SESSION['ofertas_aplicadas'] ?? [];

// Ofertas activas con info de aplicabilidad respecto al carrito actual
$ofertasInfo = $ofertaSA->obtenerActivasConAplicabilidad($carrito);

// Resumen del descuento que se aplicaría con las ofertas que el cliente ya activó
$resumen = $ofertaSA->aplicarOfertasACarrito($carrito, $activadas);
$aplicadasPorId = [];
foreach ($resumen['ofertas_aplicadas'] as $aplicada) {
    $aplicadasPorId[(int) $aplicada['id']] = $aplicada;
}

$tituloPagina = "Ofertas Disponibles";
$css = [];
$header = "../comun/header.php";
$claseMain = "contenedor-cliente";
$js = [RAIZ_APP . "/js/script.js"];

// Mensaje de feedback (e.g. "no aplicable")
$flash = "";
if (isset($_GET['error'])) {
    $msg = htmlspecialchars($_GET['error']);
    $flash = "<div class='alert alert-danger'>{$msg}</div>";
}
if (isset($_GET['ok'])) {
    $msg = htmlspecialchars($_GET['ok']);
    $flash = "<div class='alert alert-success'>{$msg}</div>";
}

// Tarjetas de ofertas
$tarjetas = "";
foreach ($ofertasInfo as $info) {
    $oferta = $info['oferta'];
    $aplicable = $info['aplicable'];
    $veces = $info['veces'];

    $id = $oferta->getId();
    $nombre = htmlspecialchars($oferta->getNombre());
    $desc = htmlspecialchars($oferta->getDescripcion());
    $descuento = number_format($oferta->getDescuento(), 2);
    $precioPack = number_format($oferta->getPrecioPackSinDescuento(), 2);
    $precioFinal = number_format($oferta->getPrecioPackConDescuento(), 2);
    $ahorro = number_format($oferta->getAhorroDescuento(), 2);
    $hasta = date('d/m/Y', strtotime($oferta->getFechaFin()));

    // Lista de productos del pack
    $listaProds = "<ul class='list-group list-group-flush mb-3'>";
    foreach ($oferta->getProductos() as $p) {
        $listaProds .= "<li class='list-group-item px-0'>" . (int) $p['cantidad'] . "x " . htmlspecialchars($p['nombre']) . "</li>";
    }
    $listaProds .= "</ul>";

    // Estado actual: ya aplicada, aplicable, o no aplicable
    $yaAplicada = in_array((int) $id, array_map('intval', $activadas), true);
    if ($yaAplicada) {
        $vecesAplicada = isset($aplicadasPorId[(int) $id]) ? (int) $aplicadasPorId[(int) $id]['veces'] : 0;
        $textoBadge = $vecesAplicada > 0 ? "Aplicada x{$vecesAplicada}" : "Activada, pendiente";
        $badge = "<span class='badge text-bg-success'>{$textoBadge}</span>";
        $accion = "<form class='form-accion-oferta' method='POST' action='" . RUTA_VISTAS . "/pedidos/apoyo/procesar_oferta.php'>
                       <input type='hidden' name='accion' value='quitar'>
                       <input type='hidden' name='id_oferta' value='{$id}'>
                       <button type='submit' class='btn btn-outline-danger w-100'>Quitar oferta</button>
                   </form>";
        $estadoTarjeta = "border-success";
    } elseif ($aplicable) {
        $badge = "<span class='badge text-bg-success'>¡Aplicable x{$veces}!</span>";
        $accion = "<form class='form-accion-oferta' method='POST' action='" . RUTA_VISTAS . "/pedidos/apoyo/procesar_oferta.php'>
                       <input type='hidden' name='accion' value='aplicar'>
                       <input type='hidden' name='id_oferta' value='{$id}'>
                       <button type='submit' class='btn btn-success w-100'>Aplicar al pedido</button>
                   </form>";
        $estadoTarjeta = "border-success";
    } else {
        $badge = "<span class='badge text-bg-secondary'>No aplicable aún</span>";
        $accion = "<a href='" . RUTA_VISTAS . "/productos/productos_oferta.php?id_oferta={$id}' class='btn btn-outline-primary w-100'>Ver productos necesarios</a>";
        $estadoTarjeta = "";
    }

    $tarjetas .= <<<EOF
        <div class="col">
        <article class="card h-100 shadow-sm {$estadoTarjeta}">
            <div class="card-body d-flex flex-column">
            <header class="d-flex justify-content-between gap-3 align-items-start mb-3">
                <h2 class="h4 card-title mb-0">{$nombre}</h2>
                {$badge}
            </header>
            <p class="card-text text-secondary">{$desc}</p>

            <strong>El pack incluye:</strong>
            {$listaProds}

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <small>Precio sin descuento</small><br>
                    <span class="text-decoration-line-through text-secondary">{$precioPack} €</span>
                </div>
                <div class="col-6">
                    <small>Descuento</small><br>
                    <strong class="text-danger">-{$descuento}%</strong>
                </div>
                <div class="col-6">
                    <small>Precio del pack</small><br>
                    <strong class="text-success">{$precioFinal} €</strong>
                </div>
                <div class="col-6">
                    <small>Te ahorras</small><br>
                    <strong>{$ahorro} €</strong>
                </div>
            </div>

            <small class="text-secondary mt-auto">Disponible hasta el {$hasta}</small>

            <div class="d-grid gap-2 mt-3">
                <a href="oferta_detalle.php?id={$id}" class="btn btn-outline-primary">Ver detalles</a>
                {$accion}
            </div>
            </div>
        </article>
        </div>
EOF;
}

if (empty($tarjetas)) {
    $tarjetas = "<div class='col-12'><div class='alert alert-info'>No hay ofertas disponibles en este momento.</div></div>";
}

// Resumen de descuento actual (si hay ofertas aplicadas)
$resumenHtml = "";
if (!empty($activadas)) {
    $totalSin = number_format($resumen['total_sin_descuento'], 2);
    $descTotal = number_format($resumen['descuento_total'], 2);
    $totalFinal = number_format($resumen['total_final'], 2);
    $resumenHtml = <<<EOF
        <aside class="card shadow-sm mb-4">
            <div class="card-body">
            <h2 class="h4">Resumen de tus ofertas activas</h2>
            <div class="row g-2 mb-3">
                <div class="col-12 col-md-4">Subtotal carrito: <strong>{$totalSin} €</strong></div>
                <div class="col-12 col-md-4 text-danger">Descuento aplicado: <strong>-{$descTotal} €</strong></div>
                <div class="col-12 col-md-4 text-success">Total estimado: <strong>{$totalFinal} €</strong></div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="../pedidos/carrito.php" class="btn btn-success">Ir al carrito</a>
                <form action="../pedidos/apoyo/procesar_oferta.php" method="POST">
                    <input type="hidden" name="accion" value="clear">
                    <button type="submit" class="btn btn-outline-danger">Quitar ofertas</button>
                </form>
            </div>
            </div>
        </aside>
EOF;
}

$contenidoPrincipal = <<<EOF
    <section>
        <header class="mb-4">
            <h1 class="h2">Nuestras Ofertas</h1>
            <p class="text-secondary">Descuentos activos para tu pedido</p>
        </header>

        {$flash}
        {$resumenHtml}

        <section class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
            {$tarjetas}
        </section>
    </section>
EOF;

require("../comun/plantilla.php");
?>