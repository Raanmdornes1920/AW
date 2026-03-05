<?php
require_once 'config.php';
session_start();


if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: ../index.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: ../vistas/productos.php");
    exit;
}

$sqlCheck = "SELECT id, ofertado FROM productos WHERE id = ?";
$stmtCheck = mysqli_prepare($db_connection, $sqlCheck);
mysqli_stmt_bind_param($stmtCheck, "i", $id);
mysqli_stmt_execute($stmtCheck);
$result = mysqli_stmt_get_result($stmtCheck);
$producto = mysqli_fetch_assoc($result);

if ($producto) {
    $nuevo_estado = $producto['ofertado'] ? 0 : 1;
    $sqlUpd = "UPDATE productos SET ofertado=? WHERE id=?";
    $stmtUpd = mysqli_prepare($db_connection, $sqlUpd);
    mysqli_stmt_bind_param($stmtUpd, "ii", $nuevo_estado, $id);
    mysqli_stmt_execute($stmtUpd);
}

header("Location: ../vistas/productos.php");
exit;