<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || !in_array($_SESSION['usuario']->rol(), ['cocinero', 'gerente', 'camarero'], true)) {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$id_linea = filter_input(INPUT_POST, 'id_linea', FILTER_SANITIZE_NUMBER_INT);

if ($id_linea) {
    $pedidoSA = new PedidoSA($db_connection);
    $pedidoSA->marcarProductoComoPreparado($id_linea);
}

if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} elseif ($_SESSION['usuario']->rol() === 'camarero') {
    header("Location: ../pedidos_camarero.php");
} else {
    header("Location: ../pedidos_cocinero.php");
}
exit;
?>
