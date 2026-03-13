<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$pedidoSA = new PedidoSA($db_connection);
$pedidos = $pedidoSA->obtenerPedidosPendientes(); 

$tituloPagina = "Supervisión de Pedidos";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

$htmlTabla = "";

if (empty($pedidos)) {
    $htmlTabla = "<p>No hay pedidos pendientes en el restaurante. Todo está tranquilo.</p>";
} else {
    $htmlTabla .= '<table class="tabla-gestion">
        <thead>
            <tr>
                <th>Nº Pedido</th>
                <th>Fecha/Hora</th>
                <th>ID Cliente</th>
                <th>Total</th>
                <th>Estado Actual</th>
                <th>Acción Gerente</th>
            </tr>
        </thead>
        <tbody>';
        
    foreach ($pedidos as $p) {
        $estado = $p->getEstado();
        $id = $p->getId();
        $num = $p->getNumeroPedido();
        $fecha = date('d/m/Y H:i', strtotime($p->getFecha())); 
        $total = number_format($p->getTotal(), 2);
        $idCliente = $p->getIdUsuario();
        
        $estadoVisual = ucfirst(str_replace('_', ' ', $estado));

        $htmlTabla .= "<tr>
            <td><strong>#$num</strong></td>
            <td>$fecha</td>
            <td>Usuario #$idCliente</td>
            <td>$total €</td>
            <td><span class='badge'>$estadoVisual</span></td>
            <td>
                <form action='apoyo/procesar_estado_pedido.php' method='POST' onsubmit='return confirm(\"¿Estás seguro de cancelar este pedido?\");'>
                    <input type='hidden' name='id_pedido' value='$id'>
                    <input type='hidden' name='nuevo_estado' value='cancelado'>
                    <button type='submit' class='boton-borrar'>Forzar Cancelación</button>
                </form>
            </td>
        </tr>";
    }
    $htmlTabla .= "</tbody></table>";
}

$contenidoPrincipal = "<h1>Panel de Gerente - Pedidos en Curso</h1>" . $htmlTabla;
require("../../comun/plantilla.php");