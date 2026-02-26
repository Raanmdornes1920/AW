<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['login']) || !in_array('gerente', $_SESSION['roles'])) {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$id_categoria = (int)$_POST['id_categoria'];
$nombre = mysqli_real_escape_string($db_connection, $_POST['nombre']);
$descripcion = mysqli_real_escape_string($db_connection, $_POST['descripcion']);
$precio_base = (float)$_POST['precio_base'];
$iva = (int)$_POST['iva'];
$disponible = isset($_POST['disponible']) ? 1 : 0;

$sql = "INSERT INTO productos 
(id_categoria,nombre,descripcion,precio_base,iva,disponible,ofertado)
VALUES (?,?,?,?,?,?,1)";

$stmt = mysqli_prepare($db_connection,$sql);
mysqli_stmt_bind_param($stmt,"issdii",
$id_categoria,$nombre,$descripcion,$precio_base,$iva,$disponible);

mysqli_stmt_execute($stmt);
$id_producto = mysqli_insert_id($db_connection);

$carpetaDestino = "../img/productos/";
if (!is_dir($carpetaDestino)) {
    mkdir($carpetaDestino, 0755, true); // crea la carpeta si no existe
}

if (!empty($_FILES['imagenes']['name'][0])) {
    foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
        $nombreOriginal = basename($_FILES['imagenes']['name'][$key]);
        $nombreArchivo = time() . "_" . $nombreOriginal;
        $destino = $carpetaDestino . $nombreArchivo;

        if (move_uploaded_file($tmp_name, $destino)) {
            // Guardamos la ruta en la base de datos
            $sqlImg = "INSERT INTO productos_imagenes (id_producto,ruta_imagen,orden) VALUES (?,?,?)";
            $stmtImg = mysqli_prepare($db_connection, $sqlImg);
            mysqli_stmt_bind_param($stmtImg, "isi", $id_producto, $nombreArchivo, $key);
            mysqli_stmt_execute($stmtImg);
        } else {
            // Opcional: mostrar error si falla la subida
            error_log("Error al subir la imagen: $nombreOriginal");
        }
    }
}

header("Location: ../vistas/productos.php");
exit;