<?php
require_once (__DIR__ . '/../../../config.php');
session_start();
$SA = new UsuarioSA($db_connection);

$rutas_error_msj = [RUTA_VISTAS . "/usuarios/ajustes_admin.php", RUTA_VISTAS . "/usuarios/apoyo/crear_usuario.php", RUTA_VISTAS . "/usuarios/apoyo/eliminar_usuario.php"];

function tiene_error_msj($ruta) {
    return in_array($ruta, $rutas_error_msj);
}

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['usuario']->rol() !== 'gerente') {
    if(isset($_POST['volver']) && tiene_error_msj($_POST['volver'])){
        $_SESSION['error_editar_perfil'] = "No tienes permisos para realizar esta accion";
    }
    header("Location: " . (isset($_POST['volver']) ? $_POST['volver'] : RAIZ_APP . "/"));
    exit();
}

$usuario_propio = ($_POST['id-usuario'] === $_SESSION['usuario']->id());

if(isset($_POST['modo-admin']) && $_POST['modo-admin'] === "Verdadero"){
    
    $id_usuario = intval($_POST['id-usuario']);

    try {
        if($usuario = $SA->eliminarUsuario($id_usuario)){
            $_SESSION['cambio'] = $usuario;
            $_SESSION['error_editar_perfil'] = "Ninguno";
        }
    }catch (UsuarioNoExisteException $e1) {

        $_SESSION['error_editar_perfil'] = "El usuario con ID " . $id_usuario . " no existe.";
        
    } catch (ErrorEnConsultaException $e2) {

        $_SESSION['error_editar_perfil'] = "No se ha podido eliminar al usuario con ID " . $id_usuario . ".";

    }
}
else{
    // No viene de eliminar_usuario.php
    header("Location: " . RAIZ_APP . "/");
    exit();
}

if($usuario_propio){
    header("Location: " . RUTA_VISTAS . "/usuarios/apoyo/logout.php");
    exit();
}
else{
    header("Location: " . (isset($_POST['volver']) ? $_POST['volver'] : RAIZ_APP . "/"));
    exit();
}
?>