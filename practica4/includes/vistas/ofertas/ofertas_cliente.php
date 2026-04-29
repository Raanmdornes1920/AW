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
$claseMain  = "contenedor-cliente";
$js         = [RAIZ_APP . "/js/script.js"];

// Mensaje de feedback (e.g. "no aplicable")
$flash = "";
if (isset($_GET['error'])) {
    $msg = htmlspecialchars($_GET['error']);
    $flash = "<div class='mensaje mensaje-error'>{$msg}</div>";
}
if (isset($_GET['ok'])) {
    $msg = htmlspecialchars($_GET['ok']);
    $flash = "<div class='mensaje mensaje-exito'>{$msg}</div>";
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
    $listaProds = "<ul class='lista-pack'>";
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
        $accion = "<form class='form-accion-oferta' method='POST' action='" . RUTA_VISTAS . "/pedidos/apoyo/procesar_oferta.php'>
                       <input type='hidden' name='accion' value='quitar'>
                       <input type='hidden' name='id_oferta' value='{$id}'>
                       <button type='submit' class='boton-borrar'>Quitar oferta</button>
                   </form>";
        $estadoTarjeta = "tarjeta-oferta-aplicada";
    } elseif ($aplicable) {
        $badge  = "<span class='badge badge-success'>¡Aplicable x{$veces}!</span>";
        $accion = "<form class='form-accion-oferta' method='POST' action='" . RUTA_VISTAS . "/pedidos/apoyo/procesar_oferta.php'>
                       <input type='hidden' name='accion' value='aplicar'>
                       <input type='hidden' name='id_oferta' value='{$id}'>
                       <button type='submit' class='boton-nuevo'>Aplicar al pedido</button>
                   </form>";
        $estadoTarjeta = "tarjeta-oferta-disponible";
    } else {
        $badge  = "<span class='badge'>No aplicable aún</span>";
        $accion = "<a href='" . RUTA_VISTAS . "/productos/productos_cliente.php' class='boton-editar'>Ver productos necesarios</a>";
        $estadoTarjeta = "tarjeta-oferta-pendiente";
    }

    $tarjetas .= <<<EOF
        <article class="tarjeta-oferta {$estadoTarjeta}">
            <header class="cabecera-tarjeta-oferta">
                <h2>{$nombre}</h2>
                {$badge}
            </header>
            <p class="descripcion-oferta">{$desc}</p>

            <strong>El pack incluye:</strong>
            {$listaProds}

            <div class="resumen-oferta">
                <div class="dato-oferta">
                    <small>Precio sin descuento</small><br>
                    <span class="texto-tachado">{$precioPack} €</span>
                </div>
                <div class="dato-oferta">
                    <small>Descuento</small><br>
                    <strong class="texto-descuento">-{$descuento}%</strong>
                </div>
                <div class="dato-oferta">
                    <small>Precio del pack</small><br>
                    <strong class="texto-total">{$precioFinal} €</strong>
                </div>
                <div class="dato-oferta">
                    <small>Te ahorras</small><br>
                    <strong class="texto-ahorro">{$ahorro} €</strong>
                </div>
            </div>

            <small class="fecha-oferta">Disponible hasta el {$hasta}</small>

            <div class="acciones acciones-oferta">
                <a href="oferta_detalle.php?id={$id}" class="boton-editar">Ver detalles</a>
                {$accion}
            </div>
        </article>
EOF;
}

if (empty($tarjetas)) {
    $tarjetas = "<p class='estado-vacio'>No hay ofertas disponibles en este momento.</p>";
}

// Resumen de descuento actual (si hay ofertas aplicadas)
$resumenHtml = "";
if (!empty($activadas)) {
    $totalSin    = number_format($resumen['total_sin_descuento'], 2);
    $descTotal   = number_format($resumen['descuento_total'], 2);
    $totalFinal  = number_format($resumen['total_final'], 2);
    $resumenHtml = <<<EOF
        <aside class="panel-cliente panel-resumen-ofertas">
            <h2>Resumen de tus ofertas activas</h2>
            <div class="resumen-total-carrito">
                <div>Subtotal carrito: <strong>{$totalSin} €</strong></div>
                <div class="linea-descuento">Descuento aplicado: <strong>-{$descTotal} €</strong></div>
                <div class="linea-total">Total estimado: <strong>{$totalFinal} €</strong></div>
            </div>
            <div class="acciones">
                <a href="../pedidos/carrito.php" class="boton-nuevo">Ir al carrito</a>
                <form class="form-accion-oferta" action="../pedidos/apoyo/procesar_oferta.php" method="POST">
                    <input type="hidden" name="accion" value="clear">
                    <button type="submit" class="boton-borrar">Quitar ofertas</button>
                </form>
            </div>
        </aside>
EOF;
}

$contenidoPrincipal = <<<EOF
    <section class="pagina-cliente pagina-ofertas">
        <header class="cabecera-pagina">
            <h1>Nuestras Ofertas</h1>
            <p>Descuentos activos para tu pedido</p>
        </header>

        {$flash}
        {$resumenHtml}

        <section class="rejilla-ofertas">
            {$tarjetas}
        </section>
    </section>
EOF;

require("../comun/plantilla.php");
?>
