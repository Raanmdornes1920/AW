<?php
require_once '../../../config.php';
session_start();

<<<<<<< HEAD
// Ahora dejamos pasar también a los camareros
if (!isset($_SESSION['login']) || !in_array($_SESSION['usuario']->rol(), ['cocinero', 'gerente', 'camarero'])) {
=======
if (!isset($_SESSION['login']) || !in_array($_SESSION['usuario']->rol(), ['cocinero', 'gerente', 'camarero'], true)) {
>>>>>>> angela
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$id_linea = filter_input(INPUT_POST, 'id_linea', FILTER_SANITIZE_NUMBER_INT);

if ($id_linea) {
    $pedidoSA = new PedidoSA($db_connection);
    $pedidoSA->marcarProductoComoPreparado($id_linea);
}

<<<<<<< HEAD
// 2. LA SALIDA: Devolvemos al usuario exactamente a la página de donde venía
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    
    if ($_SESSION['usuario']->rol() === 'camarero') {
        header("Location: ../pedidos_camarero.php");
    } else {
        header("Location: ../pedidos_cocinero.php");
    }
}
exit;
?>
=======
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} elseif ($_SESSION['usuario']->rol() === 'camarero') {
    header("Location: ../pedidos_camarero.php");
} else {
    header("Location: ../pedidos_cocinero.php");
}
exit;
?>
>>>>>>> angela
