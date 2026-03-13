<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$pedidoSA = new PedidoSA($db_connection);
$pedidos = $pedidoSA->obtenerPedidosCliente($_SESSION['usuario']->id());

$tituloPagina = "Mis Pedidos";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";
$js = [(RAIZ_APP . "/js/pedidos.js")];

$htmlTabla = "";

if (empty($pedidos)) {
    $htmlTabla = "<p>Aún no has realizado ningún pedido. ¡Anímate a probar nuestra carta!</p>";
} else {
    $htmlTabla .= '<table class="tabla-gestion">
        <thead>
            <tr>
                <th>Nº Pedido</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>';
        
    foreach ($pedidos as $p) {
        $estado = ucfirst(str_replace('_', ' ', $p->getEstado()));
        $num = $p->getNumeroPedido();
        $tipo = ucfirst($p->getTipo());
        $fecha = date('d/m/Y H:i', strtotime($p->getFecha())); 
        $total = number_format($p->getTotal(), 2);
        
        $htmlTabla .= "<tr>
            <td><strong>#$num</strong></td>
            <td>$fecha</td>
            <td>$tipo</td>
            <td>$total €</td>
            <td><span class='badge'>$estado</span></td>
        </tr>";
    }
    $htmlTabla .= "</tbody></table>";
}

$contenidoPrincipal = "<h1>Historial de Mis Pedidos</h1>" . $htmlTabla;
require("../comun/plantilla.php");
?>