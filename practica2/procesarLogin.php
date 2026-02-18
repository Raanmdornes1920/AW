<?php
session_start();
require_once 'Usuario.php';
// 2. Aquí conectarías con la Base de Datos (Estructura BD)

$userPost = $_POST['username'];
$passPost = $_POST['password'];

// SIMULACIÓN: En tu práctica, aquí haces un SELECT a la BD
if ($userPost === "admin" && $passPost === "1234") {
    
    // Aquí defines tú el objeto $user con datos de la BD
    // Usando la clase que explicamos antes
    $user = new Usuario($userPost, ['admin']); 

    // Usamos la función de tus diapositivas para iniciar la sesión
    $_SESSION['login'] = true;
    $_SESSION['nombre'] = $user->username();
    $_SESSION['roles'] = $user->roles();

    header("Location: index.php"); // Redirige a una funcionalidad
    exit();
} else {
    echo "Error: Usuario o contraseña incorrectos.";
    echo "<a href='login.php'>Volver</a>";
}