<?php
require_once 'config.php';
session_start();

$rutas_error_msj = [RUTA_VISTAS . "/ajustes_admin.php", RUTA_VISTAS . "/crear_usuario.php", RUTA_VISTAS . "/eliminar_usuario.php"];

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
    
    $sql = "SELECT nombre_usuario FROM usuarios WHERE id = $id_usuario";
    $resultado = mysqli_query($db_connection, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    
    $usuario = $fila['nombre_usuario'];
    
    if ($id_usuario > 0) {
        $query = "DELETE FROM usuarios WHERE id = ?";
        $stmt = mysqli_prepare($db_connection, $query);
        
        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['cambio'] = $usuario;
            $_SESSION['error_editar_perfil'] = "Ninguno";
        } else {
            $_SESSION['error_editar_perfil'] = "No se ha podido eliminar al usuario " . $usuario . ".";
        }
    }
}
else{
    // No viene de eliminar_usuario.php
    header("Location: " . RAIZ_APP . "/");
    exit();
}

if($usuario_propio){
    header("Location: " . RUTA_STATIC . "/logout.php");
    exit();
}
else{
    header("Location: " . (isset($_POST['volver']) ? $_POST['volver'] : RAIZ_APP . "/"));
    exit();
}
?>