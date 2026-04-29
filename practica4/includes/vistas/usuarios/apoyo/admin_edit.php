<?php
require_once (__DIR__ . '/../../../config.php');
session_start();
$SA = new UsuarioSA($db_connection);

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['usuario']->rol() !== 'gerente') {
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
    $usuario_actual = ($_SESSION['usuario']->id() == $id?true:false);
    try {
        $img_actual = $SA->obtenerImagen($id);
    } catch (\Exception $e) {
        $_SESSION['error_editar_perfil'] = $e->getMessage();
        header("Location: " . RUTA_VISTAS . "/usuarios/ajustes_admin.php");
        exit();
    }


    if (!$img_actual){

    }

    switch($campo){
        case 'Usuario':
            $campo = 'usuario';
            // Comprobar usuario libre
            try {
                if ($usuarioExistente = $SA->buscaUsuario($valor)) {
                    if($usuarioExistente->id() !== $id && $usuarioExistente->usuario() === $valor){
                        // Usuario existente con id diferente
                        $_SESSION['error_editar_perfil'] = "El usuario ".$valor." ya existe.";
                        header("Location: " . RUTA_VISTAS . "/usuarios/ajustes_admin.php");
                        exit();
                    }
                }
            } catch (\UsuarioNoExisteException $e) {
                // Usuario no existe, por lo que el nombre de usuario es válido
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

        try {
            if ($SA->modificarusuario($id, ($campo === 'usuario' ? 'nombre_usuario' : $campo), $valor)) {
                // Si el usuario es el de la sesión
                if($usuario_actual && $campo !== 'password'){
                    // Corregimos sesion
                    switch (strtolower($campo)) {
                        case 'usuario':
                            $_SESSION['usuario']->set_usuario($valor);
                            break;
                        case 'nombre':
                            $_SESSION['usuario']->set_nombre($valor);
                            break;
                        case 'apellidos':
                            $_SESSION['usuario']->set_apellidos($valor);
                            break;
                        case 'email':
                            $_SESSION['usuario']->set_email($valor);
                            break;
                        case 'rol':
                            $_SESSION['usuario']->set_rol($valor);
                            break;
                        default:
                            break;
                    }
                }

                $_SESSION['cambio'] = ucfirst($campo);
                $_SESSION['error_editar_perfil'] = "Ninguno";
            }
            else{
                $_SESSION['error_editar_perfil'] = "Error al intentar modificar ".ucfirst($campo)." a '".$valor."'.";
                header("Location: " . RUTA_VISTAS . "/usuarios/ajustes_admin.php");
                exit();
            }

        } catch (\CampoInexistenteException $th) {
            $_SESSION['error_editar_perfil'] = "Error al intentar modificar ".ucfirst($campo)." a '".$valor."'.";
                header("Location: " . RUTA_VISTAS . "/usuarios/ajustes_admin.php");
                exit();
        }
    }
    else{// Editar Avatar
        // Verificamos si seleccionó alguna imagen default
        if (isset($_POST['foto_perfil']) && $_POST['foto_perfil'] !== 'custom'){

            $rutaAlArchivo = DIR_RAIZ . "/img/perfiles/" . $img_actual;
            $nombreImagen = mysqli_real_escape_string($db_connection, $_POST['foto_perfil']);

            if ($SA->modificarusuario($id, $campo, $nombreImagen)) {
                // Si el usuario es el de la sesión
                if($usuario_actual){
                    // Corregimos sesion
                    $_SESSION['usuario']->set_value('avatar',$nombreImagen);
                }
                $_SESSION['cambio'] = 'Avatar';
                $_SESSION['error_editar_perfil'] = "Ninguno";
            }
            else{
                $_SESSION['error_editar_perfil'] = "Error al subir la imagen.";
                header("Location: " . RUTA_VISTAS . "/usuarios/ajustes_admin.php");
                exit();
            }

            // Si nadie mas tiene la imagen, y no es de las basicas la eliminamos
            if(!in_array($img_actual, IMAGENES_BASE)){
                // Comprobar imagen libre. Si nadie usa la imagen la eliminamos
                if (!($SA->usoImagen($id, $img_actual))) {
                    if (file_exists($rutaAlArchivo)) {
                        unlink($rutaAlArchivo);
                    }
                }
            }
        }
        // Si no eligio una default la procesamos
        elseif (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {

            $rutaAlArchivo = DIR_RAIZ . "/img/perfiles/" . $img_actual;

            $fileTmpPath = $_FILES['foto_perfil']['tmp_name'];
            $fileName = $_FILES['foto_perfil']['name'];

            $fileNameClean = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);

            $dest_path = DIR_RAIZ . "/img/perfiles/" . $fileNameClean;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $nombreImagen = $fileNameClean;
                chmod($dest_path, 0666);

                if ($SA->modificarUsuario($id, 'avatar', $nombreImagen)) {
                    // Si el usuario es el de la sesión
                    if($usuario_actual){
                        // Corregimos sesion
                        $_SESSION['usuario']->set_avatar($nombreImagen);
                    }
                    $_SESSION['cambio'] = 'Avatar';
                    $_SESSION['error_editar_perfil'] = "Ninguno";
                }
                else{
                    $_SESSION['error_editar_perfil'] = "Error al subir la imagen.";
                    header("Location: " . RUTA_VISTAS . "/usuarios/ajustes_admin.php");
                    exit();
                }


                // Si nadie mas tiene la imagen, y no es de las basicas la eliminamos
                if(!in_array($img_actual, IMAGENES_BASE)){

                    // Comprobar imagen libre. Si nadie usa la imagen la eliminamos
                    if (!($SA->usoImagen($id, $img_actual))) {
                        if (file_exists($rutaAlArchivo)) {
                            unlink($rutaAlArchivo);
                        }
                    }
                }
            }
            else {
                $_SESSION['error_editar_perfil'] = "Error al subir la imagen.";
                header("Location: " . RUTA_VISTAS . "/usuarios/ajustes_admin.php");
                exit();
            }
        }
    }
    header("Location: " . RUTA_VISTAS . "/usuarios/ajustes_admin.php");
    exit();
}

?>