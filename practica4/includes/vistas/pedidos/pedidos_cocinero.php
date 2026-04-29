<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || !in_array($_SESSION['usuario']->rol(), ['cocinero', 'gerente'])) {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$pedidoSA = new PedidoSA($db_connection);
$pedidos = $pedidoSA->obtenerPedidosCocinero(); 

$tituloPagina = "Panel de Cocina";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-fullwidth";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

$htmlTabla = "";

if (empty($pedidos)) {
    $htmlTabla = "<p>¡Buen trabajo! No hay pedidos pendientes en cocina.</p>";
} else {
    $htmlTabla .= '<table class="tabla-gestion">
        <thead>
            <tr>
                <th>Nº Pedido</th>
                <th>Fecha/Hora</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>';
        
    foreach ($pedidos as $p) {
        $estado = $p->getEstado();
        $id = $p->getId();
        $num = $p->getNumeroPedido();
        $tipo = ucfirst($p->getTipo());
        $fecha = date('H:i:s', strtotime($p->getFecha())); 
        
        $botonAccion = "";
        
        if ($estado === 'en_preparacion') {
            $botonAccion = "
                <a href='pedido_detalle.php?id={$id}' class='boton-editar' style='display:block; text-align:center; margin-bottom:8px;'>Ver detalle</a>
                <form action='apoyo/procesar_estado_pedido.php' method='POST'>
                    <input type='hidden' name='id_pedido' value='$id'>
                    <input type='hidden' name='nuevo_estado' value='cocinando'>
                    <button type='submit' class='boton-nuevo' style='background-color:#E91E63;'>Empezar a Cocinar</button>
                </form>";
        } elseif ($estado === 'cocinando') {
            $botonAccion = "<a href='pedido_detalle.php?id={$id}' class='boton-nuevo' style='display:block; text-align:center;'>Abrir comanda</a>";
        }

        $estadoVisual = ucfirst(str_replace('_', ' ', $estado));

        $htmlTabla .= "<tr>
            <td><strong style='font-size: 1.4em; color: #d32f2f;'>#$num</strong></td>
            <td>$fecha</td>
            <td>$tipo</td>
            <td><span class='badge'>$estadoVisual</span></td>
            <td>$botonAccion</td>
        </tr>";
    }
    $htmlTabla .= "</tbody></table>";
}

$contenidoPrincipal = "<h1>👩‍🍳 Comandas de Cocina 👨‍🍳</h1>" . $htmlTabla;
require("../comun/plantilla.php");
?>
