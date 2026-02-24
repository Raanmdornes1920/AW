<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['login']) || !in_array('gerente', $_SESSION['roles'])) {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}


$nombre      = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$nombreImagen = RUTA_IMG . "/categorias/categoria_default.png";

$nombreEscaped = mysqli_real_escape_string($db_connection, $nombre);
$descEscaped   = mysqli_real_escape_string($db_connection, $descripcion);

// Comprobamos si ya existe una categoría con ese nombre
$sqlCheck = "SELECT id FROM categorias WHERE nombre = '$nombreEscaped'";
$resCheck = mysqli_query($db_connection, $sqlCheck);

if ($resCheck && mysqli_num_rows($resCheck) > 0) {
    die("Error: Ya existe una categoría llamada " . htmlspecialchars($nombre));
}

// IMPORTANTE: Añadir enctype="multipart/form-data" a tu formulario
/*if (isset($_FILES['imagen_cat']) && $_FILES['imagen_cat']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['imagen_cat']['tmp_name'];
    $fileName    = $_FILES['imagen_cat']['name'];
    $fileNameClean = "cat_" . time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
    
    // Ruta física para mover el archivo (suponiendo que estás en /static/)
    $dest_path = "../img/perfiles/" . $fileNameClean; 

    if (move_uploaded_file($fileTmpPath, $dest_path)) {
        $nombreImagen = $fileNameClean;
        chmod($dest_path, 0666); // Permisos para local/servidor
    }
}*/

$sqlInsert = "INSERT INTO categorias (nombre, descripcion, imagen, activa) 
              VALUES ('$nombreEscaped', '$descEscaped', '$nombreImagen', 1)";

if (mysqli_query($db_connection, $sqlInsert)) {
    header("Location: " . RUTA_VISTAS . "/categorias.php");
    exit;
} else {
    die("Error al crear la categoría: " . mysqli_error($db_connection));
}