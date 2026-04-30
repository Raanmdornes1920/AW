<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/");
    exit;
}

$id_pedido = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id_pedido) {
    header("Location: ../productos/productos_cliente.php");
    exit;
}

$pedidoSA = new PedidoSA($db_connection);
$pedido = $pedidoSA->obtenerPorId($id_pedido);

if (!$pedido || (int)$pedido->getIdUsuario() !== (int)$_SESSION['usuario']->id()) {
    header("Location: ../productos/productos_cliente.php");
    exit;
}

$tituloPagina = "Pedido Confirmado";
$css = [];
$header = "../comun/header.php";
$claseMain = "contenedor-cliente";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

$numero_pedido = $pedido->getNumeroPedido();
$idPedido = (int)$pedido->getId();
$estado = ucfirst(str_replace('_', ' ', $pedido->getEstado()));
$tipo = ucfirst($pedido->getTipo());
$total = number_format($pedido->getTotal(), 2);
$totalSin = number_format($pedido->getTotalSinDescuento() ?? $pedido->getTotal(), 2);
$descuento = number_format($pedido->getDescuentoAplicado(), 2);
$detalleDescuento = $pedido->getDescuentoAplicado() > 0
    ? "<p><strong>Subtotal:</strong> {$totalSin} €</p><p><strong>Descuento de ofertas:</strong> -{$descuento} €</p>"
    : "";

$contenidoPrincipal = <<<EOF
<section class="row justify-content-center">
<article class="col-12 col-lg-7">
<div class="card shadow-sm text-center">
    <div class="card-body p-5">
    <h1 class="h2 text-success">Pedido realizado con éxito</h1>
    <p class="text-secondary">Tu número de pedido es</p>
    <div class="display-4 fw-bold mb-4">#{$numero_pedido}</div>

    <div class="text-start border rounded p-3 mb-4">
        <p><strong>Estado actual:</strong> <span class="badge text-bg-success">{$estado}</span></p>
        <p><strong>Tipo de consumo:</strong> {$tipo}</p>
        {$detalleDescuento}
        <p><strong>Total pagado:</strong> {$total} €</p>
    </div>

    <p>Puedes revisar el estado de tu pedido en tu perfil en cualquier momento.</p>

    <div class="d-flex flex-wrap justify-content-center gap-2">
        <a href="pedido_detalle.php?id={$idPedido}" class="btn btn-outline-primary">Ver detalle</a>
        <a href="../productos/productos_cliente.php" class="btn btn-primary">Volver al inicio</a>
    </div>
</div>
</div>
</article>
</section>
EOF;

require("../comun/plantilla.php");
?>
