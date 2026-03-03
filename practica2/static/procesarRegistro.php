<?php
require_once 'Usuario.php';
require_once 'config.php';

$nombrePost = $_POST['nombre'];
$apellidosPost = $_POST['apellidos'];
$mailPost = $_POST['mail'];
$fotoPost = $_POST['foto_perfil'];
$nombreImagen = "default.png";
$rolPost = (isset($_POST['rol']) && (isset($_POST['modo-admin']) && $_POST['modo-admin'] === "Verdadero") ? $_POST['rol'] : 'cliente');

$userPost = $_POST['username'];
$passPost = $_POST['password'];
$passConfPost = $_POST['password_confirm'];

$userEscaped = mysqli_real_escape_string($db_connection, $userPost);
$mailEscaped = mysqli_real_escape_string($db_connection, $mailPost);

$sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$userEscaped' OR email = '$mailEscaped'";

$resultado = mysqli_query($db_connection, $sql);


if ($resultado && mysqli_num_rows($resultado) === 0) {

    if (isset($_POST['foto_perfil']) && $_POST['foto_perfil'] === 'custom' && isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    
        $fileTmpPath = $_FILES['foto_perfil']['tmp_name'];
        $fileName = $_FILES['foto_perfil']['name'];
        
        $fileNameClean = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
        
        $dest_path = "../img/perfiles/" . $fileNameClean;
        
        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $nombreImagen = $fileNameClean;
            chmod($dest_path, 0666); 
        }
    }
    else{
        $nombreImagen = $_POST['foto_perfil'];
        $dest_path = "../img/perfiles/" . $fileNameClean;
    }

    $passwordHasheada = password_hash($passPost, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nombre_usuario, email, nombre, apellidos, password, rol, avatar) VALUES ('$userPost', '$mailPost', '$nombrePost', '$apellidosPost', '$passwordHasheada', '$rolPost', '$nombreImagen')";

    
    if (mysqli_query($db_connection, $sql)) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if(isset($_POST['modo-admin']) && $_POST['modo-admin'] === "Verdadero"){
            $_SESSION['cambio'] = "Crear Usuario";
            $_SESSION['error_editar_perfil'] = "Ninguno";
        }
        else{
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $sql = "SELECT id FROM usuarios WHERE nombre_usuario = '$userPost¡'";

            $resultado = mysqli_query($db_connection, $sql);
            $fila = mysqli_fetch_assoc($resultado);
            $idPost = $fila['id'];

            $user = new Usuario($idPost, $userPost, $nombrePost, $apellidosPost, $mailPost, $rolPost, $nombreImagen); 

            $_SESSION['login'] = true;
            $_SESSION['id'] = $user->id();
            $_SESSION['usuario'] = $user->username();
            $_SESSION['nombre'] = $user->nombre();
            $_SESSION['apellidos'] = $user->apellidos();
            $_SESSION['email'] = $user->email();
            $_SESSION['foto_perfil'] = $user->fotoPerfil();
            $_SESSION['rol'] = $user->roles();            
        }
        header("Location: " . (isset($_POST['volver']) ? $_POST['volver'] : RAIZ_APP . "/"));
        exit();
        
    }
} else {
    if(isset($_POST['modo-admin']) && $_POST['modo-admin'] === "Verdadero"){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }      
        $fila = mysqli_fetch_assoc($resultado);
    
        if ($fila['nombre_usuario'] === $userPost) {
            $_SESSION['error_crear_perfil'] = "El usuario '".$userPost."' ya existe.";
            header("Location: " . RUTA_VISTAS . "/ajustes_admin.php");
            exit();
        } else if ($fila['email'] === $mailPost) {
            $_SESSION['error_crear_perfil'] = "El correo '".$mailPost."' ya esta registrado.";
            header("Location: " . RUTA_VISTAS . "/ajustes_admin.php");
            exit();
        }
    }
    else{
        $fila = mysqli_fetch_assoc($resultado);
    
        if ($fila['nombre_usuario'] === $userPost) {
            include 'usuario_existente.php';
        } else if ($fila['email'] === $mailPost) {
            include 'correo_existente.php';
        }
    }
}
?>