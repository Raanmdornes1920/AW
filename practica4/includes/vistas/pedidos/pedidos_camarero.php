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
$css = [RAIZ_APP . "/css/default.css"];
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
            <a href='pedido_detalle.php?id={$id}' class='boton-editar' style='display:block; text-align:center; margin-bottom:8px;'>Ver detalle</a>
            <form action='apoyo/procesar_estado_pedido.php' method='POST'>
                <input type='hidden' name='id_pedido' value='$id'>
                <input type='hidden' name='nuevo_estado' value='en_preparacion'>
                <button type='submit' class='boton-nuevo' style='background-color:#FF9800;'>Cobrar y Enviar a Cocina</button>
            </form>";

        $htmlCobros .= "<tr>
            <td data-label='Nº Pedido'><strong style='font-size: 1.2em;'>#$num</strong></td>
            <td data-label='Tipo'>$tipo</td>
            <td data-label='Total'>$total €</td>
            <td data-label='Estado'><span class='badge'>$estadoVisual</span></td>
            <td data-label='Acción'>$botonAccion</td>
        </tr>";
    } elseif ($estado === 'listo_cocina' || $estado === 'terminado') {
        // TABLA 2: la bandeja se gestiona en la vista separada de detalle.
        $botonAccion = "<a href='pedido_detalle.php?id={$id}' class='boton-nuevo' style='display:block; text-align:center;'>Gestionar detalle</a>";

        $htmlPreparacion .= "<tr>
            <td data-label='Nº Pedido' style='vertical-align: top;'><strong style='font-size: 1.2em;'>#$num</strong></td>
            <td data-label='Estado/Tipo' style='vertical-align: top;'>$tipo<br><br><span class='badge'>$estadoVisual</span></td>
            <td data-label='Acción' style='vertical-align: middle;'>$botonAccion</td>
        </tr>";
    }
}

// Componer HTML final de las tablas controlando si están vacías
$tablaCobrosFinal = empty($htmlCobros) ? "<p style='text-align:center; padding:20px;'>No hay pedidos pendientes de cobro.</p>" : "
    <table class='tabla-gestion'>
        <thead><tr><th>Nº Pedido</th><th>Tipo</th><th>Total</th><th>Estado</th><th>Acción</th></tr></thead>
        <tbody>$htmlCobros</tbody>
    </table>";

$tablaPreparacionFinal = empty($htmlPreparacion) ? "<p style='text-align:center; padding:20px;'>No hay pedidos pendientes de entrega.</p>" : "
    <table class='tabla-gestion'>
        <thead><tr><th>Nº Pedido</th><th>Tipo/Estado</th><th>Acción</th></tr></thead>
        <tbody>$htmlPreparacion</tbody>
    </table>";

$contenidoPrincipal = <<<EOF
    <h1 style="text-align: center; margin-bottom: 5px;">Gestión de Camareros</h1>
    <p style="text-align: center; color: #666; margin-bottom: 30px;">Recuerda recargar la página periódicamente para ver nuevos pedidos.</p>
    
    <h2 style="color: #FF9800; border-bottom: 2px solid #FF9800; padding-bottom: 5px;">1. Nuevos (Cobro y envío a Cocina)</h2>
    $tablaCobrosFinal

    <h2 style="margin-top: 40px; color: #4CAF50; border-bottom: 2px solid #4CAF50; padding-bottom: 5px;">2. Entregas (Bandejas y Bolsas)</h2>
    $tablaPreparacionFinal
EOF;

require("../comun/plantilla.php");
?>
