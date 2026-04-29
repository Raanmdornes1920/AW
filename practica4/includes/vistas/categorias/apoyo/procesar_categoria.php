<?php
require_once '../../../config.php';
require_once __DIR__ . '/formularioCrearCategoria.php';
require_once __DIR__ . '/formularioActualizarCategoria.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    exit("No autorizado");
}

<<<<<<< HEAD
$sa = new CategoriaSA($db_connection);
=======
>>>>>>> angela
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($accion === 'crear') {
    $formulario = new FormularioCrearCategoria();
<<<<<<< HEAD
    $datos = $formulario->saneaDatos($_POST);

    $nombreImagen = 'categoria_default.jpg';

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

} elseif ($accion === 'actualizar') {
    $formulario = new FormularioActualizarCategoria();
    $datos = $formulario->saneaDatos($_POST);

    $nombreImagen = $_POST['imagen_actual'] ?? 'categoria_default.jpg';

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
=======
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
>>>>>>> angela
    $id = $_POST['id'] ?? 0;
    $resultado = $sa->borrarCategoria($id);

    if ($resultado === true) {
        header("Location: ../categorias_gerente.php?msg=borrado_ok");
        exit;
    } else {
        header("Location: categoria_borrar.php?id=$id&error=" . urlencode($resultado));
        exit;
    }
<<<<<<< HEAD
}

header("Location: ../categorias_gerente.php");
exit;
=======
} else {
    header("Location: ../categorias_gerente.php");
    exit;
}
>>>>>>> angela
