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
                echo json_encode(['error_editar_perfil' => "Las contraseñas no coinciden."]);
                exit();
            }
            // Contraseñas coinciden
            else{
                if ($SA->cambiarPasswordUsuario($user, $nuevaPass)) {
                    echo json_encode(['error_editar_perfil' => "Ninguno", 'cambio' => "Password"]);
                } else {
                    echo json_encode(['error_editar_perfil' => "No se ha podido actualizar la contraseña."]);
                    exit();
                }
            }
        }
        // Contraseña actual incorrecta
        else{
            echo json_encode(['error_editar_perfil' => "Contraseña actual incorrecta."]);
            exit();
        }
    }
    catch (UsuarioNoExisteException $e){
        echo json_encode(['error_editar_perfil' => "Usuario no encontrado."]);
        exit();
    }
    catch (Exception $e){
        echo json_encode(['error_editar_perfil' => "Error al cambiar la contraseña."]);
        exit();
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
exit();
?>