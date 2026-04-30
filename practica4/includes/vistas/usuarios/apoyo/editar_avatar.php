<?php
require_once(__DIR__ . '/../../../config.php');
session_start();
$SA = new UsuarioSA($db_connection);

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../index.php");
    exit();
}
// TODO: Adaptar para AJAX

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verificamos si seleccionó alguna imagen default
    if (isset($_POST['foto_perfil']) && $_POST['foto_perfil'] !== 'custom') {
        $nombreImagen = mysqli_real_escape_string($db_connection, $_POST['foto_perfil']);
        $antiguoAvatar = $_SESSION['usuario']->avatar();
        $rutaAntiguo = DIR_RAIZ . "/img/perfiles/" . $antiguoAvatar;

        if (!$SA->modificarUsuario($_SESSION['usuario']->id(), 'avatar', $nombreImagen)) {
            $_SESSION['error_editar_perfil'] = "Error al cambiar la imagen en la base de datos.";
            header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
            exit();
        } else {
            $_SESSION['usuario']->set_avatar($nombreImagen);
            // Borramos el anterior si no era un base
            if (!in_array($antiguoAvatar, IMAGENES_BASE) && file_exists($rutaAntiguo)) {
                unlink($rutaAntiguo);
            }
        }
    }
    // Si no eligio una default la procesamos
    elseif (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {

        $antiguoAvatar = $_SESSION['usuario']->avatar();
        $rutaAntiguo = DIR_RAIZ . "/img/perfiles/" . $antiguoAvatar;

        $fileTmpPath = $_FILES['foto_perfil']['tmp_name'];
        $fileName = $_FILES['foto_perfil']['name'];
        $fileNameClean = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);

        $dest_path = DIR_RAIZ . "/img/perfiles/" . $fileNameClean;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $nombreImagen = $fileNameClean;
            chmod($dest_path, 0666);

            if (!$SA->modificarUsuario($_SESSION['usuario']->id(), 'avatar', $nombreImagen)) {
                $_SESSION['error_editar_perfil'] = "Error al actualizar la base de datos.";
                header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
                exit();
            } else {
                $_SESSION['usuario']->set_avatar($nombreImagen);
                // Borramos el anterior si no era un base
                if (!in_array($antiguoAvatar, IMAGENES_BASE) && file_exists($rutaAntiguo)) {
                    unlink($rutaAntiguo);
                }
            }
        } else {
            $_SESSION['error_editar_perfil'] = "Error al mover el archivo al servidor.";
            header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
            exit();
        }
    } else {
        $_SESSION['error_editar_perfil'] = "Error al subir la imagen.";
        header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
$_SESSION['cambio'] = "Avatar";
$_SESSION['error_editar_perfil'] = "Ninguno";
header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
exit();
