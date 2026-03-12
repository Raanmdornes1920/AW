<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    exit("No autorizado");
}

$sa = new ProductoSA($db_connection);
$accion = $_REQUEST['accion'] ?? '';

if ($accion === 'crear' || $accion === 'actualizar') {
    $datos = $_POST;
    $nombreImagen = $_POST['imagen_actual'] ?? 'default.png';

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombreNuevo = uniqid('prod_') . '.' . $ext;
        
        $rutaDestino = __DIR__ . "/../../../../img/productos/" . $nombreNuevo;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $nombreImagen = $nombreNuevo;
        }
    }
    
    $datos['imagen'] = $nombreImagen;
    $sa->guardarProducto($datos);

} elseif ($accion === 'borrar') {
    $id = $_POST['id'] ?? $_GET['id'] ?? 0;
    $sa->toggleOferta($id);
}

header("Location: ../productos_gerente.php");
exit;