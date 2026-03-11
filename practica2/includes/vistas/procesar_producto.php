<?php
require_once '../config.php';
require_once __DIR__ . '/../clases/SA/ProductoSA.php';
session_start();

// Validar que sea gerente (ajusta la validación según tu sistema de usuarios)
if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: ../../index.php");
    exit;
}

$sa = new ProductoSA($db_connection);
$accion = $_REQUEST['accion'] ?? '';

if ($accion === 'guardar') {
    $sa->guardarProducto($_POST);
} elseif ($accion === 'toggle') {
    $sa->toggleOferta($_GET['id']);
}

// Redirigir de vuelta al panel
header("Location: productos_gerente.php");
exit;