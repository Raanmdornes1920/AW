<?php
require_once '../../../config.php';
session_start();

<<<<<<< HEAD
// Solo el personal puede cambiar estados
if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() === 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$id_pedido = $_POST['id_pedido'] ?? null;
$nuevo_estado = $_POST['nuevo_estado'] ?? null;
=======
if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() === 'cliente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$id_pedido = filter_input(INPUT_POST, 'id_pedido', FILTER_SANITIZE_NUMBER_INT);
$nuevo_estado = filter_input(INPUT_POST, 'nuevo_estado', FILTER_SANITIZE_SPECIAL_CHARS);
>>>>>>> angela

if ($id_pedido && $nuevo_estado) {
    $pedidoSA = new PedidoSA($db_connection);
    $pedidoSA->cambiarEstadoPedido($id_pedido, $nuevo_estado);
}

<<<<<<< HEAD
// Redirigir a la página desde la que vino (así sirve para el panel del camarero o del cocinero)
$referer = $_SERVER['HTTP_REFERER'] ?? RAIZ_APP . '/index.php';
header("Location: " . $referer);
exit;
=======
$referer = $_SERVER['HTTP_REFERER'] ?? RAIZ_APP . '/index.php';
header("Location: " . $referer);
exit;
?>
>>>>>>> angela
