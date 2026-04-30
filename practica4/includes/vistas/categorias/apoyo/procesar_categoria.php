<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$accion = filter_input(INPUT_POST, 'accion', FILTER_SANITIZE_SPECIAL_CHARS) ?: '';

if ($accion !== 'eliminar_definitivo') {
    header("Location: ../categorias_gerente.php");
    exit;
}

$sa = new CategoriaSA($db_connection);
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$resultado = $sa->borrarCategoria($id);

if ($resultado === true) {
    header("Location: ../categorias_gerente.php?msg=borrado_ok");
    exit;
}

header("Location: categoria_borrar.php?id=$id&error=" . urlencode($resultado));
exit;
