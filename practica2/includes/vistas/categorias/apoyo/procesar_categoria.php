<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    exit("No autorizado");
}

$sa = new CategoriaSA($db_connection);
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($accion === 'crear' || $accion === 'actualizar') {
    $datos = $_POST;
    $nombreImagen = $_POST['imagen_actual'] ?? 'default_cat.png';

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombreNuevo = uniqid('cat_') . '.' . $ext;
        $rutaDestino = __DIR__ . "/../../../../img/categorias/" . $nombreNuevo;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $nombreImagen = $nombreNuevo;
        }
    }
    
    $datos['imagen'] = $nombreImagen;
    $sa->guardarCategoria($datos);

} elseif ($accion === 'eliminar_definitivo') {
    $id = $_POST['id'] ?? 0;
    $resultado = $sa->borrarCategoria($id);
    
    if ($resultado === true) {
        header("Location: ../categorias_gerente.php?msg=borrado_ok");
        exit;
    } else {
        header("Location: categoria_borrar.php?id=$id&error=" . urlencode($resultado));
        exit;
    }
}

header("Location: ../categorias_gerente.php");
exit;