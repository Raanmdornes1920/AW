<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    exit("No autorizado");
}

$sa = new CategoriaSA($db_connection);
$accion = $_REQUEST['accion'] ?? '';

if ($accion === 'crear' || $accion === 'actualizar') {
    $datos = $_POST;
    $nombreImagen = $_POST['imagen_actual'] ?? 'default_cat.png';

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombreNuevo = uniqid('cat_') . '.' . $ext;
        $rutaDestino = "../../../../img/categorias/" . $nombreNuevo;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $nombreImagen = $nombreNuevo;
        }
    }
    
    $datos['imagen'] = $nombreImagen;
    $sa->guardarCategoria($datos);

} elseif ($accion === 'borrar') {
    $sa->toggleActiva($_GET['id'] ?? 0);
}

header("Location: ../categorias_lista.php");
exit;