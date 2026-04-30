<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || !in_array($_SESSION['usuario']->rol(), ['cocinero', 'gerente'])) {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$pedidoSA = new PedidoSA($db_connection);
$pedidos = $pedidoSA->obtenerPedidosCocinero(); 

$tituloPagina = "Panel de Cocina";
$css = [];
$header = "../comun/header.php";
$claseMain = "contenedor-fullwidth";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

$htmlTabla = "";

if (empty($pedidos)) {
    $htmlTabla = "<div class='alert alert-success mb-0'>¡Buen trabajo! No hay pedidos pendientes en cocina.</div>";
} else {
    $htmlTabla .= '<div class="table-responsive"><table class="table table-striped table-hover align-middle mb-0">
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
                <div class='d-grid gap-2'>
                <a href='pedido_detalle.php?id={$id}' class='btn btn-outline-primary btn-lg'>Ver detalle</a>
                <form action='apoyo/procesar_estado_pedido.php' method='POST'>
                    <input type='hidden' name='id_pedido' value='$id'>
                    <input type='hidden' name='nuevo_estado' value='cocinando'>
                    <button type='submit' class='btn btn-primary btn-lg w-100'>Empezar a cocinar</button>
                </form>
                </div>";
        } elseif ($estado === 'cocinando') {
            $botonAccion = "<a href='pedido_detalle.php?id={$id}' class='btn btn-success btn-lg w-100'>Abrir comanda</a>";
        }

        $estadoVisual = ucfirst(str_replace('_', ' ', $estado));

        $htmlTabla .= "<tr>
            <td><strong class='fs-4 text-danger'>#$num</strong></td>
            <td>$fecha</td>
            <td>$tipo</td>
            <td><span class='badge text-bg-secondary'>$estadoVisual</span></td>
            <td>$botonAccion</td>
        </tr>";
    }
    $htmlTabla .= "</tbody></table></div>";
}

$contenidoPrincipal = "<section class='card shadow-sm'><div class='card-body'><h1 class='h2 mb-4'>Comandas de Cocina</h1>" . $htmlTabla . "</div></section>";
require("../comun/plantilla.php");
?>
