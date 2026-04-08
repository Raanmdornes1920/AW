<?php
require_once (__DIR__ . '/../../../config.php');
session_start();
$SA = new UsuarioSA($db_connection);

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['contrasena'];
    $nuevaPass = $_POST['nueva-contrasena'];
    $confirmarPass = $_POST['confirmar-contrasena'];
    $user = $_SESSION['usuario']->usuario();
    
    try{
        // Contraseña actual correcta
        if ($SA->validarPasswordUsuario($user, $pass)) {
            // Contraseñas No coinciden
            if($nuevaPass !== $confirmarPass){
                $_SESSION['error_editar_perfil'] = "Las contraseñas no coinciden.";
                header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
                exit();
            }
            // Contraseñas coinciden
            else{
                if ($SA->cambiarPasswordUsuario($user, $nuevaPass)) {
                    $_SESSION['cambio'] = 'Password';
                    $_SESSION['error_editar_perfil'] = "Ninguno";
                } else {
                    $_SESSION['error_editar_perfil'] = "No se ha podido actualizar la contraseña.";
                    header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
                    exit();
                }
            }
        }
        // Contraseña actual incorrecta
        else{
            $_SESSION['error_editar_perfil'] = "Contraseña actual incorrecta.";
            header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
            exit();
        }
    }
    catch (UsuarioNoExisteException $e){
        $_SESSION['error_editar_perfil'] = "Usuario no encontrado.";
        header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
        exit();
    }
    catch (Exception $e){
        $_SESSION['error_editar_perfil'] = "Error al cambiar la contraseña.";
        header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
        exit();
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
exit();
?>