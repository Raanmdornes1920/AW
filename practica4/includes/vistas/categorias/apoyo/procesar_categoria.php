<?php
require_once '../../../config.php';
require_once __DIR__ . '/formularioCrearCategoria.php';
require_once __DIR__ . '/formularioActualizarCategoria.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    exit("No autorizado");
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($accion === 'crear') {
    $formulario = new FormularioCrearCategoria();
    $htmlForm = $formulario->gestiona();
    
    // Si gestiona devuelve HTML, es que hubo errores o es la primera carga
    if ($htmlForm !== null) {
        // En este caso, como procesar_categoria es un script de apoyo, 
        // lo ideal es que el formulario redireccione o nosotros manejemos la salida.
        // Pero siguiendo el patrón de formularioBase, si estamos aquí es porque 
        // el formulario se envió a este script.
        echo $htmlForm; 
    }

} elseif ($accion === 'actualizar') {
    $formulario = new FormularioActualizarCategoria();
    $htmlForm = $formulario->gestiona();
    
    if ($htmlForm !== null) {
        echo $htmlForm;
    }

} elseif ($accion === 'eliminar_definitivo') {
    $sa = new CategoriaSA($db_connection);
    $id = $_POST['id'] ?? 0;
    $resultado = $sa->borrarCategoria($id);

    if ($resultado === true) {
        header("Location: ../categorias_gerente.php?msg=borrado_ok");
        exit;
    } else {
        header("Location: categoria_borrar.php?id=$id&error=" . urlencode($resultado));
        exit;
    }
} else {
    header("Location: ../categorias_gerente.php");
    exit;
}