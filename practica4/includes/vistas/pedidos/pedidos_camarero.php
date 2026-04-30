<?php
require_once '../../config.php';
session_start();

// Validamos rol (asumimos que gerente también puede verlo por jerarquía)
if (!isset($_SESSION['login']) || !in_array($_SESSION['usuario']->rol(), ['camarero', 'gerente'])) {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$pedidoSA = new PedidoSA($db_connection);
// Obtenemos solo los pedidos que importan al camarero (recibidos, listos de cocina, y terminados)
$pedidos = $pedidoSA->obtenerPedidosCamarero(); 

$tituloPagina = "Panel de Camarero";
$css = [];
$header = "../comun/header.php";
$claseMain = "contenedor-fullwidth";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

// Variables para separar en dos vistas/tablas distintas
$htmlCobros = "";
$htmlPreparacion = "";

foreach ($pedidos as $p) {
    $estado = $p->getEstado();
    $id = $p->getId();
    $num = $p->getNumeroPedido();
    $tipo = ucfirst($p->getTipo());
    $total = number_format($p->getTotal(), 2);
    $estadoVisual = ucfirst(str_replace('_', ' ', $estado));

    if ($estado === 'recibido') {
        // TABLA 1: COBROS PENDIENTES
        $botonAccion = "
            <div class='d-grid gap-2'>
            <a href='pedido_detalle.php?id={$id}' class='btn btn-outline-primary btn-lg'>Ver detalle</a>
            <form action='apoyo/procesar_estado_pedido.php' method='POST'>
                <input type='hidden' name='id_pedido' value='$id'>
                <input type='hidden' name='nuevo_estado' value='en_preparacion'>
                <button type='submit' class='btn btn-warning btn-lg w-100'>Cobrar y enviar a cocina</button>
            </form>
            </div>";

        $htmlCobros .= "<tr>
            <td><strong class='fs-4'>#$num</strong></td>
            <td>$tipo</td>
            <td>$total €</td>
            <td><span class='badge text-bg-secondary'>$estadoVisual</span></td>
            <td>$botonAccion</td>
        </tr>";
    } elseif ($estado === 'listo_cocina' || $estado === 'terminado') {
        // TABLA 2: la bandeja se gestiona en la vista separada de detalle.
        $botonAccion = "<a href='pedido_detalle.php?id={$id}' class='btn btn-success btn-lg w-100'>Gestionar detalle</a>";

        $htmlPreparacion .= "<tr>
            <td><strong class='fs-4'>#$num</strong></td>
            <td>$tipo<br><span class='badge text-bg-secondary'>$estadoVisual</span></td>
            <td>$botonAccion</td>
        </tr>";
    }
}

// Componer HTML final de las tablas controlando si están vacías
$tablaCobrosFinal = empty($htmlCobros) ? "<div class='alert alert-info mb-0'>No hay pedidos pendientes de cobro.</div>" : "
    <div class='table-responsive'><table class='table table-striped table-hover align-middle mb-0'>
        <thead><tr><th>Nº Pedido</th><th>Tipo</th><th>Total</th><th>Estado</th><th>Acción</th></tr></thead>
        <tbody>$htmlCobros</tbody>
    </table></div>";

$tablaPreparacionFinal = empty($htmlPreparacion) ? "<div class='alert alert-info mb-0'>No hay pedidos pendientes de entrega.</div>" : "
    <div class='table-responsive'><table class='table table-striped table-hover align-middle mb-0'>
        <thead><tr><th>Nº Pedido</th><th>Tipo/Estado</th><th>Acción</th></tr></thead>
        <tbody>$htmlPreparacion</tbody>
    </table></div>";

$contenidoPrincipal = <<<EOF
    <header class="text-center mb-4">
        <h1 class="h2">Gestión de Camareros</h1>
        <p class="text-secondary">Recuerda recargar la página periódicamente para ver nuevos pedidos.</p>
    </header>
    
    <section class="card shadow-sm mb-4">
        <div class="card-header bg-warning-subtle">
            <h2 class="h4 mb-0">1. Nuevos (Cobro y envío a Cocina)</h2>
        </div>
        <div class="card-body">
            $tablaCobrosFinal
        </div>
    </section>

    <section class="card shadow-sm">
        <div class="card-header bg-success-subtle">
            <h2 class="h4 mb-0">2. Entregas (Bandejas y Bolsas)</h2>
        </div>
        <div class="card-body">
            $tablaPreparacionFinal
        </div>
    </section>
EOF;

require("../comun/plantilla.php");
?>
