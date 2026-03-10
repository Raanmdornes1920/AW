<?php
require_once 'config.php';
session_start();


$userPost = $_POST['username'];
$passPost = $_POST['password'];

if (empty($userPost) || empty($passPost)) {
    die("Por favor, rellena todos los campos. <a href='" . RAIZ_APP . "/'>Volver</a>");
}

$userEscaped = mysqli_real_escape_string($db_connection, $userPost);
$sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$userEscaped'";

$resultado = mysqli_query($db_connection, $sql);


if ($resultado && mysqli_num_rows($resultado) === 1) {
    $fila = mysqli_fetch_assoc($resultado);

    if ($fila && password_verify($passPost, $fila['password'])) {
    
        $_SESSION['login'] = true;
        $_SESSION['usuario'] = new Usuario($fila['id'], $fila['nombre_usuario'], $fila['nombre'], $fila['apellidos'], $fila['email'], $fila['rol'], $fila['avatar']);
        
        header("Location: " . RAIZ_APP . "/");
        exit();
    } else {
        echo "Error: Contraseña incorrecta.";
    }
} else {
    echo "Error: El usuario no existe.";
}

echo "<br><a href='" . RAIZ_APP . "/'>Volver al inicio</a>";
?>