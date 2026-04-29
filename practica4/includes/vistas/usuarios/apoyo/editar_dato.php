<?php
require_once (__DIR__ . '/../../../config.php');
session_start();
$SA = new UsuarioSA($db_connection);

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campo = $_POST['campo-editar'];
    $nuevoValor = $_POST['nuevo-valor'];
    $nuevoRol = $_POST['nuevo-rol'] ?? null; // Para el caso de editar rol
    $user = $_SESSION['usuario']->usuario();
<<<<<<< HEAD
    
=======

>>>>>>> angela
    if (strtolower($campo) === 'usuario'){

        if ($SA->usuarioEnUso($nuevoValor)) {
            $_SESSION['error_editar_perfil'] = "El nombre de usuario ya existe.";
            header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
            exit();
        }
    }

    // Validar el campo a editar
    $camposPermitidos = ['Usuario', 'Nombre', 'Apellidos', 'Email'];
    if (!in_array($campo, $camposPermitidos)) {
        $_SESSION['error_editar_perfil'] = "El campo '$campo' no está permitido.";
        exit();
    }

    $columna = '';
    if ($campo === 'Usuario'){
        $columna = 'nombre_usuario';
    }
    else{
        $columna = strtolower($campo);
    }
<<<<<<< HEAD
    
=======

>>>>>>> angela
    if ($SA->modificarUsuario($_SESSION['usuario']->id(), strtolower($columna),$nuevoValor)) {
        try {

            $_SESSION['usuario']->modificarUsuario($campo, $nuevoValor);
            $_SESSION['cambio'] = $campo;
            $_SESSION['error_editar_perfil'] = "Ninguno";

        } catch (CampoInexistenteException $e) {
            $_SESSION['error_editar_perfil'] = "Error al actualizar el dato $campo con el valor $nuevoValor";
        }
<<<<<<< HEAD
        
=======

>>>>>>> angela
    } else {
        $_SESSION['error_editar_perfil'] = "No se ha podido actualizar el dato $campo con el valor $nuevoValor";
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
exit();
?>