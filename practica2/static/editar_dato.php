<?php
session_start();
require_once '../static/config.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campo = $_POST['campo-editar'];
    $nuevoValor = $_POST['nuevo-valor'];
    $user = $_SESSION['usuario'];
    
    if (strtolower($campo) === 'usuario'){
        $sqlCheck = "SELECT id FROM usuarios WHERE BINARY nombre_usuario = ?";
        $stmtCheck = mysqli_prepare($db_connection, $sqlCheck);
        mysqli_stmt_bind_param($stmtCheck, "s", $nuevoValor);
        mysqli_stmt_execute($stmtCheck);
        $resCheck = mysqli_stmt_get_result($stmtCheck);

        if ($resCheck && mysqli_num_rows($resCheck) > 0) {
            $_SESSION['error_editar_perfil'] = "El nombre de usuario ya existe.";
            header("Location: " . RUTA_VISTAS . "/editar_perfil.php");
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

    // Actualizar el campo en la base de datos
    $query = "UPDATE usuarios SET $columna = ? WHERE BINARY nombre_usuario = ?";
    $stmt = mysqli_prepare($db_connection, $query);
    mysqli_stmt_bind_param($stmt, "ss", $nuevoValor, $user);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION[strtolower($campo)] = $nuevoValor;
        $_SESSION['cambio'] = $campo;
        $_SESSION['error_editar_perfil'] = "Ninguno";
    } else {
        $_SESSION['error_editar_perfil'] = "No se ha podido actualizar el dato $campo con el valor $nuevoValor";
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
header("Location: " . RUTA_VISTAS . "/editar_perfil.php");
exit();
?>