<?php
require_once '../static/config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = $_POST['contrasena'];
    $nuevaPass = $_POST['nueva-contrasena'];
    $confirmarPass = $_POST['confirmar-contrasena'];
    $user = $_SESSION['usuario']->username();
    
    $userEscaped = mysqli_real_escape_string($db_connection, $user);
    $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$userEscaped'";

    $resultado = mysqli_query($db_connection, $sql);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);

        // Contraseña actual correcta
        if (password_verify($pass, $fila['password'])) {
            // Contraseñas No coinciden
            if($nuevaPass !== $confirmarPass){
                $_SESSION['error_editar_perfil'] = "Las contraseñas no coinciden.";
                header("Location: " . RUTA_VISTAS . "/editar_perfil.php");
                exit();
            }
            // Contraseñas coinciden
            else{
                $nuevaPassHash = password_hash($nuevaPass, PASSWORD_DEFAULT);
                $query = "UPDATE usuarios SET password = ? WHERE BINARY nombre_usuario = ?";
                $stmt = mysqli_prepare($db_connection, $query);
                mysqli_stmt_bind_param($stmt, "ss", $nuevaPassHash, $user);
                
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['cambio'] = 'Password';
                    $_SESSION['error_editar_perfil'] = "Ninguno";
                } else {
                    $_SESSION['error_editar_perfil'] = "No se ha podido actualizar la contraseña.";
                    header("Location: " . RUTA_VISTAS . "/editar_perfil.php");
                    exit();
                }
            }
        }
        // Contraseña actual incorrecta
        else{
            $_SESSION['error_editar_perfil'] = "La contraseña introducida es incorrecta.";
            header("Location: " . RUTA_VISTAS . "/editar_perfil.php");
            exit();
        }
    }
    else{
        $_SESSION['error_editar_perfil'] = "Usuario no encontrado.";
        header("Location: " . RUTA_VISTAS . "/editar_perfil.php");
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
header("Location: " . RUTA_VISTAS . "/editar_perfil.php");
exit();
?>