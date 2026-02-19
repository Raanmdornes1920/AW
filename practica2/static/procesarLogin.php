<?php
session_start();
require_once 'config.php';
require_once 'Usuario.php';

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


    if (md5($passPost) === $fila['password']) {
    
        $user = new Usuario($fila['nombre_usuario'], [$fila['rol']]); 

        $_SESSION['login'] = true;
        $_SESSION['nombre'] = $user->username();
        $_SESSION['roles'] = $user->roles();

        $return_to = !empty($_POST['return']) ? RAIZ_APP . '/' . $_POST['return'] : RAIZ_APP . '/';
        header("Location: " . $return_to);
        exit();
    } else {
        echo "Error: Contraseña incorrecta.";
    }
} else {
    echo "Error: El usuario no existe.";
}

echo "<br><a href='" . RAIZ_APP . "/'>Volver al inicio</a>";
?>