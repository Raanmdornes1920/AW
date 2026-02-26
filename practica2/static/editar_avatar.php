<?php
session_start();
require_once '../static/config.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_FILES['avatar-nuevo']) && $_FILES['avatar-nuevo']['error'] === UPLOAD_ERR_OK) {
        
        $rutaAlArchivo = "../img/perfiles/" . $_SESSION['foto_perfil'];

        $fileTmpPath = $_FILES['avatar-nuevo']['tmp_name'];
        $fileName = $_FILES['avatar-nuevo']['name'];
        
        $fileNameClean = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
        
        $dest_path = "../img/perfiles/" . $fileNameClean;
        
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $nombreImagen = $fileNameClean;
            chmod($dest_path, 0666);

            $query = "UPDATE usuarios SET avatar = ? WHERE BINARY nombre_usuario = ?";
            $stmt = mysqli_prepare($db_connection, $query);
            mysqli_stmt_bind_param($stmt, "ss", $nombreImagen, $_SESSION['usuario']);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['foto_perfil'] = $nombreImagen;
            }
            else{
                $_SESSION['error_editar_perfil'] = "Error al subir la imagen.";
                header("Location: " . RUTA_VISTAS . "/editar_perfil.php");
                exit();
            }
            
            
            if (file_exists($rutaAlArchivo)) {
                unlink($rutaAlArchivo);
            }
        }
        else {
            $_SESSION['error_editar_perfil'] = "Error al subir la imagen.";
            header("Location: " . RUTA_VISTAS . "/editar_perfil.php");
            exit();
        }
    }
    else {
        $_SESSION['error_editar_perfil'] = "Error al subir la imagen.";
        header("Location: " . RUTA_VISTAS . "/editar_perfil.php");
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
$_SESSION['cambio'] = "Avatar";
$_SESSION['error_editar_perfil'] = "Ninguno";
header("Location: " . RUTA_VISTAS . "/editar_perfil.php");
exit();
?>