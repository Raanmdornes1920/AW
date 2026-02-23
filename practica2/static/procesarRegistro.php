<?php
require_once 'Usuario.php';
require_once 'config.php';

$nombrePost = $_POST['nombre'];
$apellidosPost = $_POST['apellidos'];
$mailPost = $_POST['mail'];
$fotoPost = $_POST['foto_perfil'];
$nombreImagen = "default.png";


$userPost = $_POST['username'];
$passPost = $_POST['password'];
$passConfPost = $_POST['password_confirm'];

$userEscaped = mysqli_real_escape_string($db_connection, $userPost);
$mailEscaped = mysqli_real_escape_string($db_connection, $mailPost);

$sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$userEscaped' OR email = '$mailEscaped'";

$resultado = mysqli_query($db_connection, $sql);


if ($resultado && mysqli_num_rows($resultado) === 0) {

    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    
        $fileTmpPath = $_FILES['foto_perfil']['tmp_name'];
        $fileName = $_FILES['foto_perfil']['name'];
        
        $fileNameClean = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
        
        $dest_path = "../img/perfiles/" . $fileNameClean;
        
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $nombreImagen = $fileNameClean;
            chmod($dest_path, 0666); 
        }
    }

    $passwordHasheada = password_hash($passPost, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nombre_usuario, email, nombre, apellidos, password, rol, avatar) VALUES ('$userPost', '$mailPost', '$nombrePost', '$apellidosPost', '$passwordHasheada', 'cliente', '$nombreImagen')";

    
    if (mysqli_query($db_connection, $sql)) {

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $user = new Usuario($userPost, ['cliente']); 

            $_SESSION['login'] = true;
            $_SESSION['nombre'] = $user->username();
            $_SESSION['roles'] = $user->roles();

            header("Location: " . RAIZ_APP);
            exit();
    }
} else {
    $fila = mysqli_fetch_assoc($resultado);
    
    if ($fila['nombre_usuario'] === $userPost) {
        include 'usuario_existente.php';
    } else if ($fila['email'] === $mailPost) {
        include 'correo_existente.php';
    }
}
?>