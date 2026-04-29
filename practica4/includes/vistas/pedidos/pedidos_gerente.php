<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$pedidoSA = new PedidoSA($db_connection);
$pedidos = $pedidoSA->obtenerPedidosPendientes(); 

$tituloPagina = "Supervisión de Pedidos";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

$htmlTabla = "";

if (empty($pedidos)) {
    $htmlTabla = "<p>No hay pedidos activos en este momento.</p>";
} else {
    $htmlTabla .= '<table class="tabla-gestion">
        <thead>
            <tr>
                <th>Nº Pedido</th>
                <th>Cliente</th>
                <th>Estado y Detalle</th>
                <th>Total</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>';
        
    foreach ($pedidos as $p) {
        $estado = $p->getEstado();
        $id = $p->getId();
        $num = $p->getNumeroPedido();
        $total = number_format($p->getTotal(), 2);
        $totalOriginal = $p->getTotalSinDescuento();
        $descuento = $p->getDescuentoAplicado();
        $idCliente = $p->getIdUsuario();
        
        $totalHtml = "$total €";
        if ($descuento > 0) {
            $totalHtml = "<div style='text-decoration: line-through; color: #888; font-size: 0.85em;'>".number_format($totalOriginal, 2)." €</div>";
            $totalHtml .= "<div style='color: #27ae60; font-weight: bold;'>$total €</div>";
            $totalHtml .= "<div style='font-size: 0.75em; color: #27ae60;'>Ahorro: ".number_format($descuento, 2)." €</div>";
        }
        
        // Detalle de productos si está en cocina (Requisito Funcionalidad 3)
        $detalleProductos = "";
        if ($estado === 'cocinando' || $estado === 'en_preparacion') {
            $lineas = $pedidoSA->obtenerDetallesPedido($id);
            $detalleProductos = "<div style='font-size: 0.85em; background: #eee; padding: 5px; border-radius: 4px; margin-top: 5px;'>";
            foreach ($lineas as $l) {
                $status = $l['preparado'] ? "✅" : "⏳";
                $detalleProductos .= "<div>$status {$l['cantidad']}x {$l['nombre']}</div>";
            }
            $detalleProductos .= "</div>";
        }

        $estadoVisual = ucfirst(str_replace('_', ' ', $estado));

        $htmlTabla .= "<tr>
            <td data-label='Nº Pedido'><strong>#$num</strong></td>
            <td data-label='Cliente'>ID: $idCliente</td>
            <td data-label='Estado'>
                <span class='badge'>$estadoVisual</span>
                $detalleProductos
            </td>
            <td data-label='Total'>$totalHtml</td>
            <td data-label='Acción'>
                <form action='apoyo/procesar_estado_pedido.php' method='POST' onsubmit='return confirm(\"¿Seguro que quieres cancelar?\")'>
                    <input type='hidden' name='id_pedido' value='$id'>
                    <input type='hidden' name='nuevo_estado' value='cancelado'>
                    <button type='submit' class='boton-borrar' style='padding: 5px 10px;'>Cancelar</button>
                </form>
            </td>
        </tr>";
    }
    $htmlTabla .= "</tbody></table>";
}

$contenidoPrincipal = "<h1>Panel de Gerencia: Pedidos Activos</h1>" . $htmlTabla;
require("../comun/plantilla.php");
?>