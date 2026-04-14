<?php
require_once (__DIR__ . '/../../comun/formularioBase.php');
$SA = new UsuarioSA($db_connection);

// Hereda de formularioBase (asegúrate de que la ruta a formularioBase sea correcta)
class FormularioRegistro extends formularioBase {

    public function __construct() {
        // ID del form y dónde redirigir si todo va bien
        parent::__construct('formRegistro', ['urlRedireccion' => RUTA_VISTAS . '/usuarios/registro.php', 'enctype' => 'multipart/form-data']);
    }

    protected function generaCamposFormulario(&$datos) {
        // Recuperar valores previos para no borrarlos si hay error
        $nombre = $datos['nombre'] ?? '';
        $apellidos = $datos['apellidos'] ?? '';
        $mail = $datos['mail'] ?? '';
        $usuario = $datos['usuario'] ?? '';

        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'apellidos', 'mail', 'usuario', 'password', 'password_confirm'], $this->errores);

        
        $ruta_avatares = AVATARES_INICIALES;
        ob_start();
        include __DIR__ . "/../../comun/selector_imagenes.php"; 
        $htmlSelectorImagenes = ob_get_clean();

        foreach ($erroresCampos as $key => $value) {
            $erroresCampos[$key] = $value . '<br>';
        }

        $html = <<<EOF
        {$htmlErroresGlobales}
        <div>
            <label>Nombre:</label><br>
            <input type="text" name="nombre" value="$nombre" required>
            {$erroresCampos['nombre']}
        </div>
        <br>
        <div>
            <label>Apellidos:</label><br>
            <input type="text" name="apellidos" value="$apellidos" required>
            {$erroresCampos['apellidos']}
        </div>
        <br>
        <div>
            <label>Correo Electrónico:</label><br>
            <input type="email" name="mail" value="$mail" required>
            {$erroresCampos['mail']}
        </div>
        <br>
        <div>
            <label>Usuario:</label><br>
            <input type="text" name="usuario" value="$usuario" required>
            {$erroresCampos['usuario']}
        </div>
        <br>

        {$htmlSelectorImagenes}

        <br>
        <div>
            <label>Contraseña:</label><br>
            <input id="password" type="password" name="password" required>
            {$erroresCampos['password']}
        </div>
        <br>
        <div>
            <label>Repetir Contraseña:</label><br>
            <input type="password" name="password_confirm" required oninput="
                if(document.getElementById('password').value != this.value) {
                    this.setCustomValidity('Las contraseñas no coinciden');
                } else {
                    this.setCustomValidity('');
                }
            ">
            {$erroresCampos['password_confirm']}
        </div>
        <br>
        <button type="submit" name="registro">Registrarme</button>
        EOF;

        return $html;
    }

    protected function procesaFormulario(&$datos) {
        $this->errores = [];
        
        global $SA;

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
                
                if ($SA->crearUsuario($datosUsuario)) {
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    
                    if(isset($datos['modo-admin']) && $datos['modo-admin'] === "Verdadero"){
                        $_SESSION['cambio'] = "Crear Usuario";
                        $_SESSION['error_editar_perfil'] = "Ninguno";
                    }
                    else{                        
                        $_SESSION['login'] = true;
                        $_SESSION['usuario'] = $SA->buscaUsuario($userPost);
                    }
                    header("Location: " . (isset($datos['volver']) ? $datos['volver'] : RAIZ_APP . "/"));
                    exit();
                    
                }
            }
            // No hay else, porque la función validarUserMail devuelve true, o lanza una excepción si el usuario o el mail ya existen.
        } catch (UsuarioOcupadoException $e1) {

            if(isset($datos['modo-admin']) && $datos['modo-admin'] === "Verdadero"){
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['error_crear_perfil'] = "El usuario '".$e1->usuario()."' ya existe.";
                header("Location: " . RUTA_VISTAS . "/usuarios/ajustes_admin.php");
                exit();
            }
            else{
                $this->errores['usuario'] = 'Usuaro ya ocupado';
            }
            
        } catch (MailOcupadoException $e2) {

            if(isset($datos['modo-admin']) && $datos['modo-admin'] === "Verdadero"){
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
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