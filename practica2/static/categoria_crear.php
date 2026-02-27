<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['login']) || $_SESSION['roles'] !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}


$nombre      = $_POST['nombre'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$nombreImagen = "categoria_default.png";

$nombreEscaped = mysqli_real_escape_string($db_connection, $nombre);
$descEscaped   = mysqli_real_escape_string($db_connection, $descripcion);

// Comprobamos si ya existe una categoría con ese nombre
$sqlCheck = "SELECT id FROM categorias WHERE nombre = '$nombreEscaped'";
$resCheck = mysqli_query($db_connection, $sqlCheck);

if ($resCheck && mysqli_num_rows($resCheck) > 0) {
    die("Error: Ya existe una categoría llamada " . htmlspecialchars($nombre));
}

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['imagen']['tmp_name'];
    $fileName = $_FILES['imagen']['name'];
    
    $fileNameClean = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
    
    $dest_path = "../img/categorias/" . $fileNameClean;
    
    if(move_uploaded_file($fileTmpPath, $dest_path)) {
        $nombreImagen = $fileNameClean;
        chmod($dest_path, 0666); 
    }
}

$sqlInsert = "INSERT INTO categorias (nombre, descripcion, imagen, activa) 
              VALUES ('$nombreEscaped', '$descEscaped', '$nombreImagen', 1)";

if (mysqli_query($db_connection, $sqlInsert)) {
    header("Location: " . RUTA_VISTAS . "/categorias.php");
    exit;
} else {
    die("Error al crear la categoría: " . mysqli_error($db_connection));
}