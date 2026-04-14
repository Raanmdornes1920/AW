<?php

require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/formularioCrearProducto.php';
require_once __DIR__ . '/formularioActualizarProducto.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    exit("No autorizado");
}

$sa = new ProductoSA($db_connection);
$accion = $_REQUEST['accion'] ?? '';

if ($accion === 'crear' || $accion === 'actualizar') {
    
    if ($accion === 'actualizar') {
        $productoExistente = $sa->buscarProducto($_POST['id'] ?? 0);
        $formHandler = new FormularioActualizarProducto($db_connection, $productoExistente);
    } else {
        $formHandler = new FormularioCrearProducto($db_connection);
    }

    $datosSaneados = $formHandler->saneaDatos($_POST);
    
    $imagenesSubidas = [];
    if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION);
                $nombreNuevo = uniqid('prod_') . '_' . $key . '.' . $ext;
                $rutaDestino = __DIR__ . "/../../../../img/productos/" . $nombreNuevo;
                
                if (move_uploaded_file($tmp_name, $rutaDestino)) {
                    $imagenesSubidas[] = $nombreNuevo;
                }
            }
        }
    }
    
    if (!empty($imagenesSubidas)) {
        $datosSaneados['imagenes'] = $imagenesSubidas; 
    } else {
        $datosSaneados['imagenes'] = $_POST['imagenes_actuales'] ?? [];
    }
    
    $sa->guardarProducto($datosSaneados);

} elseif ($accion === 'borrar') {
    $id = filter_var($_POST['id'] ?? $_GET['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
    $sa->toggleOferta($id);
}

header("Location: ../productos_gerente.php");
exit;