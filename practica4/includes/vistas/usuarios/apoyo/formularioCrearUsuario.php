<?php
require_once (__DIR__ . '/../../comun/formularioBase.php');
$SA = new UsuarioSA($db_connection);

// Hereda de formularioBase (asegúrate de que la ruta a formularioBase sea correcta)
class FormularioCrearUsuario extends formularioBase {

    public function __construct() {
        // ID del form y dónde redirigir si todo va bien
        parent::__construct('formCrearUsuario', ['urlRedireccion' => RUTA_VISTAS . '/usuarios/ajustes_admin.php', 'enctype' => 'multipart/form-data']);
    }

    protected function generaCamposFormulario(&$datos) {
        // Recuperar valores previos para no borrarlos si hay error
        $nombre = $datos['nombre'] ?? '';
        $apellidos = $datos['apellidos'] ?? '';
        $mail = $datos['mail'] ?? '';
        $usuario = $datos['usuario'] ?? '';

        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'apellidos', 'mail', 'usuario', 'password', 'password_confirm'], $this->errores);

<<<<<<< HEAD
        
        $ruta_avatares = IMAGENES_BASE;
        ob_start();
        include __DIR__ . "/../../comun/selector_imagenes.php"; 
=======

        $ruta_avatares = IMAGENES_BASE;
        ob_start();
        include __DIR__ . "/../../comun/selector_imagenes.php";
>>>>>>> angela
        $htmlSelectorImagenes = ob_get_clean();

        foreach ($erroresCampos as $key => $value) {
            $erroresCampos[$key] = $value . '<br>';
        }
<<<<<<< HEAD
        
        $ruta_volver = htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : RAIZ_APP . "/");
        $ruta_volver2 = htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : RUTA_VISTAS . '/ajustes_admin.php');
        
        $html = <<<EOF
        {$htmlErroresGlobales}
        
=======

        $ruta_volver = htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : RAIZ_APP . "/");
        $ruta_volver2 = htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : RUTA_VISTAS . '/ajustes_admin.php');

        $html = <<<EOF
        {$htmlErroresGlobales}

>>>>>>> angela
        <label>Nombre:</label>
        <br>
        <input type="text" name="nombre" required>
        <br>
        {$erroresCampos['nombre']}

        <label>Apellidos:</label>
        <br>
        <input type="text" name="apellidos" required>
        <br>
        {$erroresCampos['apellidos']}
<<<<<<< HEAD
        
=======

>>>>>>> angela
        <label>Correo Electrónico:</label>
        <br>
        <input type="email" name="mail" required>
        <br>
        {$erroresCampos['mail']}

        <label>Usuario:</label>
        <br>
        <input type="text" name="usuario" required>
        <br>
        {$erroresCampos['usuario']}

        {$htmlSelectorImagenes}

        <label>Rol:</label>
        <select name="rol" id="select-rol-usuario">
            <option value="gerente">Gerente</option>
            <option value="cocinero">Cocinero</option>
            <option value="camarero">Camarero</option>
            <option value="cliente" selected>Cliente</option>
        </select>

        <input id="password" type="hidden" name="password" value="1234" required>
        {$erroresCampos['password']}
        <input type="hidden" name="password_confirm" value="1234" required>
        {$erroresCampos['password_confirm']}

        <input type="hidden" name="modo-admin" value="Verdadero">
        <input type="hidden" name="volver" value="{$ruta_volver}">
<<<<<<< HEAD
        
=======

>>>>>>> angela
        <div class="contenedor-botones">
            <button type="submit" id="boton_aceptar">Crear Usuario</button>
            <button onclick="window.location.href='{$ruta_volver2}'" type="button" id="boton_cancelar">Volver</button>
        </div>
        EOF;

        return $html;
    }

    protected function procesaFormulario(&$datos) {
        $this->errores = [];
        global $SA;
<<<<<<< HEAD
        
=======

>>>>>>> angela
        $nombrePost = ($datos['nombre'] ?? '');
        $apellidosPost = ($datos['apellidos'] ?? '');
        $mailPost = trim($datos['mail'] ?? '');
        $fotoPost = str_replace(' ', '\ ', ($datos['avatar'] ?? ''));
        $nombreImagen = "default.png";
        $rolPost = ((($datos['modo-admin'] ?? 'Falso') === "Verdadero")? ($datos['rol'] ?? 'cliente'): 'cliente');

        $userPost = trim($datos['usuario'] ?? '');
        $passPost = ($datos['password'] ?? 'password');
        $passConfPost = ($datos['password_confirm'] ?? 'password_confirm');

        try {
            if ($SA->validarUserMail($userPost, $mailPost)) {
<<<<<<< HEAD
            
                if (isset($datos['avatar']) && $datos['avatar'] === 'custom' && isset($_FILES['avatar-custom']) && $_FILES['avatar-custom']['error'] === UPLOAD_ERR_OK) {
                    
                    $fileTmpPath = $_FILES['avatar-custom']['tmp_name'];
                    $fileName = $_FILES['avatar-custom']['name'];
                    
                    $fileNameClean = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
                    
                    $dest_path = DIR_RAIZ . "/img/perfiles/" . $fileNameClean;
                    
                    if(move_uploaded_file($fileTmpPath, $dest_path)) {
                        
                        $nombreImagen = $fileNameClean;
                        chmod($dest_path, 0666); 
                    }
                }
                else{
                    
=======

                if (isset($datos['avatar']) && $datos['avatar'] === 'custom' && isset($_FILES['avatar-custom']) && $_FILES['avatar-custom']['error'] === UPLOAD_ERR_OK) {

                    $fileTmpPath = $_FILES['avatar-custom']['tmp_name'];
                    $fileName = $_FILES['avatar-custom']['name'];

                    $fileNameClean = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);

                    $dest_path = DIR_RAIZ . "/img/perfiles/" . $fileNameClean;

                    if(move_uploaded_file($fileTmpPath, $dest_path)) {

                        $nombreImagen = $fileNameClean;
                        chmod($dest_path, 0666);
                    }
                }
                else{

>>>>>>> angela
                    $nombreImagen = $datos['avatar'];
                    $dest_path = "../img/perfiles/" . $nombreImagen;
                }


                $datosUsuario = [
                    'nombre_usuario' => $userPost,
                    'email' => $mailPost,
                    'nombre' => $nombrePost,
                    'apellidos' => $apellidosPost,
                    'password' => password_hash($passPost, PASSWORD_DEFAULT),
                    'rol' => $rolPost,
                    'avatar' => $nombreImagen
                ];
<<<<<<< HEAD
                
=======

>>>>>>> angela
                if ($SA->crearUsuario($datosUsuario)) {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
<<<<<<< HEAD
                    
=======

>>>>>>> angela
                    if(isset($datos['modo-admin']) && $datos['modo-admin'] === "Verdadero"){
                        $_SESSION['cambio'] = "Crear Usuario";
                        $_SESSION['error_editar_perfil'] = "Ninguno";
                    }
<<<<<<< HEAD
                    else{                        
=======
                    else{
>>>>>>> angela
                        $_SESSION['login'] = true;
                        $_SESSION['usuario'] = $SA->buscaUsuario($userPost);
                    }
                    header("Location: " . (isset($datos['volver']) ? $datos['volver'] : RAIZ_APP . "/"));
                    exit();
<<<<<<< HEAD
                    
=======

>>>>>>> angela
                }
            }
            // No hay else, porque la función validarUserMail devuelve true, o lanza una excepción si el usuario o el mail ya existen.
        } catch (UsuarioOcupadoException $e1) {

            if(isset($datos['modo-admin']) && $datos['modo-admin'] === "Verdadero"){
                $_SESSION['error_crear_perfil'] = "El usuario '".$e1->usuario()."' ya existe.";
                header("Location: " . RUTA_VISTAS . "/usuarios/ajustes_admin.php");
                exit();
            }
            else{
                $this->errores['usuario'] = 'Usuaro ya ocupado';
            }
<<<<<<< HEAD
            
=======

>>>>>>> angela
        } catch (MailOcupadoException $e2) {

            if(isset($datos['modo-admin']) && $datos['modo-admin'] === "Verdadero"){
                $_SESSION['error_crear_perfil'] = "El correo '".$e2->mail()."' ya esta registrado.";
                header("Location: " . RUTA_VISTAS . "/usuarios/ajustes_admin.php");
                exit();
            }
            else{
                $this->errores['mail'] = 'Email ya ocupado';
            }

        } catch (Exception $e) {
            $this->errores['general'] = "Error al conectar con la base de datos.";
        }
    }
}
?>