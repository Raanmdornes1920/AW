<?php
require_once '../../../../config.php';
session_start();

if (!isset($_SESSION['login']) || !in_array($_SESSION['usuario']->rol(), ['cocinero', 'gerente'])) {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$id_linea = $_POST['id_linea'] ?? null;

if ($id_linea) {
    $pedidoSA = new PedidoSA($db_connection);
    $pedidoSA->marcarProductoComoPreparado($id_linea);
}

header("Location: " . ($_SERVER['HTTP_REFERER'] ?? RAIZ_APP . '/index.php'));
exit;