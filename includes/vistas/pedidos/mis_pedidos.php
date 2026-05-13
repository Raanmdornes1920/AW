<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$pedidoSA = new PedidoSA($db_connection);
$pedidos = $pedidoSA->obtenerPedidosCliente($_SESSION['usuario']->id());

$tituloPagina = "Mis Pedidos";
$css = [];
$header = "../comun/header.php";
$claseMain = "contenedor-cliente";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

$htmlTabla = "";

if (empty($pedidos)) {
    $htmlTabla = "<section class='card shadow-sm'><div class='card-body text-center p-5'><p class='lead mb-0'>Aún no has realizado ningún pedido. ¡Anímate a probar nuestra carta!</p></div></section>";
} else {
    $htmlTabla .= '<section class="card shadow-sm"><div class="card-body"><div class="table-responsive"><table class="table table-striped table-hover align-middle mb-0">
        <thead>
            <tr>
                <th>Nº Pedido</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($pedidos as $p) {
        $estado = ucfirst(str_replace('_', ' ', $p->getEstado()));
        $num = $p->getNumeroPedido();
        $tipo = ucfirst($p->getTipo());
        $fecha = date('d/m/Y H:i', strtotime($p->getFecha()));
        $total = number_format($p->getTotal(), 2);
        $id = $p->getId();

        $htmlTabla .= "<tr>
            <td><strong>#$num</strong></td>
            <td>$fecha</td>
            <td>$tipo</td>
            <td>$total €</td>
            <td><span class='badge text-bg-secondary'>$estado</span></td>
            <td><a href='pedido_detalle.php?id={$id}' class='btn btn-sm btn-outline-primary'>Ver detalle</a></td>
        </tr>";
    }
    $htmlTabla .= "</tbody></table></div></div></section>";
}

$contenidoPrincipal = "<section><header class='mb-4'><h1 class='h2'>Historial de mis pedidos</h1></header>{$htmlTabla}</section>";
require("../comun/plantilla.php");
?>
