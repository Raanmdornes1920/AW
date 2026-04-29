<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$ofertaSA = new OfertaSA($db_connection);
$carrito  = $_SESSION['carrito']           ?? [];
$activadas = $_SESSION['ofertas_aplicadas'] ?? [];

// Ofertas activas con info de aplicabilidad respecto al carrito actual
$ofertasInfo = $ofertaSA->obtenerActivasConAplicabilidad($carrito);

// Resumen del descuento que se aplicaría con las ofertas que el cliente ya activó
$resumen = $ofertaSA->aplicarOfertasACarrito($carrito, $activadas);
$aplicadasPorId = [];
foreach ($resumen['ofertas_aplicadas'] as $aplicada) {
    $aplicadasPorId[(int)$aplicada['id']] = $aplicada;
}

$tituloPagina = "Ofertas Disponibles";
$css        = [RAIZ_APP . "/css/default.css"];
$header     = "../comun/header.php";
$claseMain  = "contenedor-centro";
$js         = [RAIZ_APP . "/js/script.js"];

// Mensaje de feedback (e.g. "no aplicable")
$flash = "";
if (isset($_GET['error'])) {
    $msg = htmlspecialchars($_GET['error']);
    $flash = "<div class='alerta-error' style='background:#fff3e0;border:1px solid #ffe0b2;padding:12px;border-radius:8px;margin-bottom:20px;color:#e65100;'>{$msg}</div>";
}
if (isset($_GET['ok'])) {
    $msg = htmlspecialchars($_GET['ok']);
    $flash = "<div class='alerta-exito' style='background:#e8f5e9;border:1px solid #c8e6c9;padding:12px;border-radius:8px;margin-bottom:20px;color:#2e7d32;'>{$msg}</div>";
}

// Tarjetas de ofertas
$tarjetas = "";
foreach ($ofertasInfo as $info) {
    $oferta    = $info['oferta'];
    $aplicable = $info['aplicable'];
    $veces     = $info['veces'];

    $id        = $oferta->getId();
    $nombre    = htmlspecialchars($oferta->getNombre());
    $desc      = htmlspecialchars($oferta->getDescripcion());
    $descuento = number_format($oferta->getDescuento(), 2);
    $precioPack = number_format($oferta->getPrecioPackSinDescuento(), 2);
    $precioFinal = number_format($oferta->getPrecioPackConDescuento(), 2);
    $ahorro    = number_format($oferta->getAhorroDescuento(), 2);
    $hasta     = date('d/m/Y', strtotime($oferta->getFechaFin()));

    // Lista de productos del pack
    $listaProds = "<ul style='margin:8px 0 12px 0; padding-left:18px;'>";
    foreach ($oferta->getProductos() as $p) {
        $listaProds .= "<li>" . (int)$p['cantidad'] . "x " . htmlspecialchars($p['nombre']) . "</li>";
    }
    $listaProds .= "</ul>";

    // Estado actual: ya aplicada, aplicable, o no aplicable
    $yaAplicada = in_array((int)$id, array_map('intval', $activadas), true);
    if ($yaAplicada) {
        $vecesAplicada = isset($aplicadasPorId[(int)$id]) ? (int)$aplicadasPorId[(int)$id]['veces'] : 0;
        $textoBadge = $vecesAplicada > 0 ? "Aplicada x{$vecesAplicada}" : "Activada, pendiente";
        $badge  = "<span class='badge badge-success'>{$textoBadge}</span>";
        $accion = "<form method='POST' action='" . RUTA_VISTAS . "/pedidos/apoyo/procesar_oferta.php'>
                       <input type='hidden' name='accion' value='quitar'>
                       <input type='hidden' name='id_oferta' value='{$id}'>
                       <button type='submit' class='boton-borrar' style='width:100%;'>Quitar oferta</button>
                   </form>";
        $borde  = "border: 2px solid #2e7d32;";
    } elseif ($aplicable) {
        $badge  = "<span class='badge badge-success'>¡Aplicable x{$veces}!</span>";
        $accion = "<form method='POST' action='" . RUTA_VISTAS . "/pedidos/apoyo/procesar_oferta.php'>
                       <input type='hidden' name='accion' value='aplicar'>
                       <input type='hidden' name='id_oferta' value='{$id}'>
                       <button type='submit' class='boton-nuevo' style='width:100%;'>Aplicar al pedido</button>
                   </form>";
        $borde  = "border: 2px solid #4CAF50;";
    } else {
        $badge  = "<span class='badge'>No aplicable aún</span>";
        $accion = "<a href='" . RUTA_VISTAS . "/productos/productos_cliente.php' class='boton-editar' style='display:block; text-align:center;'>Ver productos necesarios</a>";
        $borde  = "border: 1px solid #ccc;";
    }

    $tarjetas .= <<<EOF
        <article class="tarjeta-oferta" style="background:#fff; padding:18px; border-radius:12px; box-shadow:0 4px 8px rgba(0,0,0,0.06); margin-bottom:18px; {$borde}">
            <header style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                <h2 style="margin:0; font-size:1.3em;">{$nombre}</h2>
                {$badge}
            </header>
            <p style="color:#555; margin:8px 0;">{$desc}</p>

            <strong>El pack incluye:</strong>
            {$listaProds}

            <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px; padding:10px; background:#f9f9f9; border-radius:8px; margin-bottom:12px;">
                <div>
                    <small>Precio sin descuento</small><br>
                    <span style="text-decoration:line-through; color:#888;">{$precioPack} €</span>
                </div>
                <div>
                    <small>Descuento</small><br>
                    <strong style="color:#e74c3c;">-{$descuento}%</strong>
                </div>
                <div>
                    <small>Precio del pack</small><br>
                    <strong style="color:#2e7d32; font-size:1.2em;">{$precioFinal} €</strong>
                </div>
                <div>
                    <small>Te ahorras</small><br>
                    <strong style="color:#2e7d32;">{$ahorro} €</strong>
                </div>
            </div>

            <small style="display:block; color:#666; margin-bottom:10px;">Disponible hasta el {$hasta}</small>

            <div style="display:flex; gap:10px;">
                <a href="oferta_detalle.php?id={$id}" class="boton-editar" style="flex:1; text-align:center;">Ver detalles</a>
                <div style="flex:1;">{$accion}</div>
            </div>
        </article>
EOF;
}

if (empty($tarjetas)) {
    $tarjetas = "<p style='text-align:center; padding:30px;'>No hay ofertas disponibles en este momento.</p>";
}

// Resumen de descuento actual (si hay ofertas aplicadas)
$resumenHtml = "";
if (!empty($activadas)) {
    $totalSin    = number_format($resumen['total_sin_descuento'], 2);
    $descTotal   = number_format($resumen['descuento_total'], 2);
    $totalFinal  = number_format($resumen['total_final'], 2);
    $resumenHtml = <<<EOF
        <aside class="form-estilizado" style="background:#e8f5e9; border:1px solid #c8e6c9; padding:18px; border-radius:12px; margin-bottom:25px;">
            <h2 style="margin-top:0; color:#2e7d32;">Resumen de tus ofertas activas</h2>
            <p>Subtotal carrito: <strong>{$totalSin} €</strong></p>
            <p>Descuento aplicado: <strong style="color:#e74c3c;">-{$descTotal} €</strong></p>
            <p style="font-size:1.2em;">Total estimado: <strong style="color:#2e7d32;">{$totalFinal} €</strong></p>
            <div class="acciones" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;">
                <a href="../pedidos/carrito.php" class="boton-nuevo">Ir al carrito</a>
                <form action="../pedidos/apoyo/procesar_oferta.php" method="POST">
                    <input type="hidden" name="accion" value="clear">
                    <button type="submit" class="boton-borrar">Quitar ofertas</button>
                </form>
            </div>
        </aside>
EOF;
}

$contenidoPrincipal = <<<EOF
    <h1 style="text-align:center;">Nuestras Ofertas</h1>
    <p style="text-align:center; color:#666; max-width:700px; margin:0 auto 25px auto;">
        Activa las ofertas que cumplas con tu pedido y ahorra automáticamente. Una misma unidad de producto solo puede usarse en una oferta.
    </p>

    {$flash}
    {$resumenHtml}

    <section style="max-width:800px; margin:0 auto;">
        {$tarjetas}
    </section>
EOF;

require("../comun/plantilla.php");
?>
