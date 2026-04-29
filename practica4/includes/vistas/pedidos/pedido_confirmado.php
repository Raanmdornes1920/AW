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
$css = [RAIZ_APP . "/css/default.css"];
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
<section class="pagina-cliente pagina-confirmacion">
<article class="panel-cliente pedido-confirmado">
    <h1>Pedido realizado con éxito</h1>
    <p>Tu número de pedido es</p>
    <div class="numero-pedido-confirmado">#{$numero_pedido}</div>

    <div class="resumen-confirmacion">
        <p><strong>Estado actual:</strong> <span class="badge badge-success">{$estado}</span></p>
        <p><strong>Tipo de consumo:</strong> {$tipo}</p>
        {$detalleDescuento}
        <p><strong>Total pagado:</strong> {$total} €</p>
    </div>

    <p>Puedes revisar el estado de tu pedido en tu perfil en cualquier momento.</p>

    <div class="acciones">
        <a href="pedido_detalle.php?id={$idPedido}" class="boton-editar">Ver detalle</a>
        <a href="../productos/productos_cliente.php" class="boton-nuevo">Volver al inicio</a>
    </div>
</article>
</section>
EOF;

require("../comun/plantilla.php");
?>
