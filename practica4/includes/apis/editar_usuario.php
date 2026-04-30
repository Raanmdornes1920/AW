<?php 
require_once '../config.php';
session_start();

$SA = new UsuarioSA($db_connection);

$campo = $_POST['campo-editar'] ?? "ninguno";
$nuevoValor = $_POST['nuevo-valor'] ?? "ninguno";
$nuevoRol = $_POST['nuevo-rol'] ?? null; // Para el caso de editar rol
$user = $_SESSION['usuario']->usuario();

if (strtolower($campo) === 'usuario'){

    if ($SA->usuarioEnUso($nuevoValor)) {
        $_SESSION['error_editar_perfil'] = "El nombre de usuario ya existe.";
        header("Location: " . RUTA_VISTAS . "/usuarios/editar_perfil.php");
        exit();
    }
}