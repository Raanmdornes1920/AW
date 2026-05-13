<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() === 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$id_pedido = filter_input(INPUT_POST, 'id_pedido', FILTER_SANITIZE_NUMBER_INT);
$nuevo_estado = filter_input(INPUT_POST, 'nuevo_estado', FILTER_SANITIZE_SPECIAL_CHARS);

if ($id_pedido && $nuevo_estado) {
    $pedidoSA = new PedidoSA($db_connection);
    $pedidoSA->cambiarEstadoPedido($id_pedido, $nuevo_estado);
}

$referer = $_SERVER['HTTP_REFERER'] ?? RAIZ_APP . '/index.php';
header("Location: " . $referer);
exit;
?>
