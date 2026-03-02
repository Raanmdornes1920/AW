<?php
session_start();
require_once 'config.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['rol'] !== 'gerente') {
    header("Location: ".RAIZ_APP."/");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id = $_POST['id-usuario'];
    $campo = $_POST['campo-editar'];
    $usuario_actual = false;
    $valor = NULL; 
    $img_actual = NULL;

    if (isset($_POST['nuevo-valor'])){
        $valor = $_POST['nuevo-valor'];
    }

    
    // Validamos si el id corresponde al usuario actual
    $query = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = mysqli_prepare($db_connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $resultado = mysqli_stmt_get_result($stmt);
        if ($fila = mysqli_fetch_assoc($resultado)) {
            $usuario_actual = ($fila['nombre_usuario'] === $_SESSION['usuario']);
            $img_actual = $fila['avatar'];
        }
    }
    else{
        $_SESSION['error_editar_perfil'] = "Ha habido un error al intentar editar al usuario.";
        header("Location: " . RUTA_VISTAS . "/ajustes_admin.php");
        exit();
    }

    switch($campo){
        case 'Usuario':
            $campo = 'nombre_usuario';
            
            // Comprobar usuario libre
            $query = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
            $stmt = mysqli_prepare($db_connection, $query);
            mysqli_stmt_bind_param($stmt, "s", $valor);
            
            if (mysqli_stmt_execute($stmt)) {
                $resultado = mysqli_stmt_get_result($stmt);
                if ($fila = mysqli_fetch_assoc($resultado)) {
                    if($fila['id'] !== $id && $fila['nombre_usuario'] === $valor){
                        // Usuario existente con id diferente
                        $_SESSION['error_editar_perfil'] = "El usuario ".$valor." ya existe.";
                        header("Location: " . RUTA_VISTAS . "/ajustes_admin.php");
                        exit();
                    }
                }
            }        
            break;
        case 'Rol':
            $campo = 'rol'; 
            $valor = strtolower($valor);
            break;
        case 'Password':
            $campo = 'password'; 
            $valor = password_hash('1234', PASSWORD_DEFAULT);
            break;
        default:
            $campo = strtolower($campo);
            break;
    }
    
    // Editar Todo Excepto Avatar
    if($campo != 'avatar'){
        $query = "UPDATE usuarios SET $campo = ? WHERE id = ?";
        $stmt = mysqli_prepare($db_connection, $query);
        mysqli_stmt_bind_param($stmt, "si", $valor, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Corregimos nomenclatura
            $campo = ($campo === 'nombre_usuario') ? 'usuario' : $campo;
            
            // Si el usuario es el de la sesión
            if($usuario_actual && $campo !== 'password'){
                // Corregimos sesion
                $_SESSION[$campo] = $valor;
            }
            
            $_SESSION['cambio'] = ucfirst($campo);
            $_SESSION['error_editar_perfil'] = "Ninguno";
        }
        else{
            $_SESSION['error_editar_perfil'] = "Error al intentar modificar ".($campo === 'nombre_usuario' ? "Usuario" : ucfirst($campo))." a '".$valor."'.";
            header("Location: " . RUTA_VISTAS . "/ajustes_admin.php");
            exit();
        }
    }
    else{// Editar Avatar
        // Verificamos si seleccionó alguna imagen default
        if (isset($_POST['foto_perfil']) && $_POST['foto_perfil'] !== 'custom'){
            
            $rutaAlArchivo = "../img/perfiles/" . $img_actual;
            $nombreImagen = mysqli_real_escape_string($db_connection, $_POST['foto_perfil']);

            $query = "UPDATE usuarios SET avatar = ? WHERE id = ?";
            $stmt = mysqli_prepare($db_connection, $query);
            mysqli_stmt_bind_param($stmt, "si", $nombreImagen, $id);
            
            if (mysqli_stmt_execute($stmt)) {
                // Si el usuario es el de la sesión
                if($usuario_actual){
                    // Corregimos sesion
                    $_SESSION['foto_perfil'] = $nombreImagen;
                }
                $_SESSION['cambio'] = 'Avatar';
                $_SESSION['error_editar_perfil'] = "Ninguno";
            }
            else{
                $_SESSION['error_editar_perfil'] = "Error al subir la imagen.";
                header("Location: " . RUTA_VISTAS . "/ajustes_admin.php");
                exit();
            }
            
            // Si nadie mas tiene la imagen, y no es de las basicas la eliminamos
            if(!in_array($img_actual, IMAGENES_BASE)){
                // Comprobar imagen libre
                $query = "SELECT * FROM usuarios WHERE avatar = ? AND id != ?";
                $stmt = mysqli_prepare($db_connection, $query);
                mysqli_stmt_bind_param($stmt, "si", $img_actual, $id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $resultado = mysqli_stmt_get_result($stmt);
                    // Si nadie usa la imagen la eliminamos
                    if (!($fila = mysqli_fetch_assoc($resultado))) {
                        if (file_exists($rutaAlArchivo)) {
                            unlink($rutaAlArchivo);
                        }   
                    }
                }   
            }
        }
        // Si no eligio una default la procesamos
        elseif (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            
            $rutaAlArchivo = "../img/perfiles/" . $img_actual;

            $fileTmpPath = $_FILES['foto_perfil']['tmp_name'];
            $fileName = $_FILES['foto_perfil']['name'];
            
            $fileNameClean = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
            
            $dest_path = "../img/perfiles/" . $fileNameClean;
            
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $nombreImagen = $fileNameClean;
                chmod($dest_path, 0666);

                $query = "UPDATE usuarios SET avatar = ? WHERE id = ?";
                $stmt = mysqli_prepare($db_connection, $query);
                mysqli_stmt_bind_param($stmt, "si", $nombreImagen, $id);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Si el usuario es el de la sesión
                    if($usuario_actual){
                        // Corregimos sesion
                        $_SESSION['foto_perfil'] = $nombreImagen;
                    }
                    $_SESSION['cambio'] = 'Avatar';
                    $_SESSION['error_editar_perfil'] = "Ninguno";
                }
                else{
                    $_SESSION['error_editar_perfil'] = "Error al subir la imagen.";
                    header("Location: " . RUTA_VISTAS . "/ajustes_admin.php");
                    exit();
                }
                
                // Si nadie mas tiene la imagen, y no es de las basicas la eliminamos
                if(!in_array($img_actual, IMAGENES_BASE)){
                    // Comprobar imagen libre
                    $query = "SELECT * FROM usuarios WHERE avatar = ? AND id != ?";
                    $stmt = mysqli_prepare($db_connection, $query);
                    mysqli_stmt_bind_param($stmt, "si", $img_actual, $id);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $resultado = mysqli_stmt_get_result($stmt);
                        // Si nadie usa la imagen la eliminamos
                        if (!($fila = mysqli_fetch_assoc($resultado))) {
                            if (file_exists($rutaAlArchivo)) {
                                unlink($rutaAlArchivo);
                            }   
                        }
                    }   
                }
                
            }
            else {
                $_SESSION['error_editar_perfil'] = "Error al subir la imagen.";
                header("Location: " . RUTA_VISTAS . "/ajustes_admin.php");
                exit();
            }
        }
    }
    header("Location: " . RUTA_VISTAS . "/ajustes_admin.php");
    exit();
}
    
?>