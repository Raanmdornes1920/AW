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
        $nombre = htmlspecialchars($datos['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
        $apellidos = htmlspecialchars($datos['apellidos'] ?? '', ENT_QUOTES, 'UTF-8');
        $mail = htmlspecialchars($datos['mail'] ?? '', ENT_QUOTES, 'UTF-8');
        $usuario = htmlspecialchars($datos['usuario'] ?? '', ENT_QUOTES, 'UTF-8');

        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['avatar', 'nombre', 'apellidos', 'mail', 'usuario', 'password', 'password_confirm'], $this->errores);

        
        $ruta_avatares = AVATARES_INICIALES;
        ob_start();
        include __DIR__ . "/../../comun/selector_imagenes.php"; 
        $htmlSelectorImagenes = ob_get_clean();

        foreach ($erroresCampos as $key => $value) {
            $erroresCampos[$key] = $value . '<br>';
        }

        $html = <<<EOF
        {$htmlErroresGlobales}
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input class="form-control" type="text" name="nombre" value="$nombre">
            {$erroresCampos['nombre']}
        </div>
        <div class="mb-3">
            <label class="form-label">Apellidos</label>
            <input class="form-control" type="text" name="apellidos" value="$apellidos">
            {$erroresCampos['apellidos']}
        </div>
        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input class="form-control" type="email" name="mail" value="$mail" required>
            {$erroresCampos['mail']}
        </div>
        <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input class="form-control" type="text" autocomplete="username" name="usuario" value="$usuario" required>
            {$erroresCampos['usuario']}
        </div>

        {$htmlSelectorImagenes}
        {$erroresCampos['avatar']}

        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input class="form-control" id="password" type="password" autocomplete="new-password" name="password" required>
            {$erroresCampos['password']}
        </div>
        <div class="mb-4">
            <label class="form-label">Repetir contraseña</label>
            <input class="form-control" type="password" autocomplete="new-password" name="password_confirm" required oninput="passwordMatch(this)">
            {$erroresCampos['password_confirm']}
        </div>
        <button class="btn btn-primary w-100" type="submit" name="registro">Registrarme</button>
        EOF;

        return $html;
    }

    protected function procesaFormulario(&$datos) {
        $this->errores = [];
        
        global $SA;

        // Validar TODOS los campos obligatorios de forma independiente (no encadenados con else if)
        if (empty($datos['usuario'])){
            $this->errores['usuario'] = "Campo obligatorio.";
        }
        if (empty($datos['mail'])){
            $this->errores['mail'] = "Campo obligatorio.";
        }
        if (empty($datos['avatar'])){
            $this->errores['avatar'] = "Campo obligatorio.";
        }
        if (empty($datos['password'])){
            $this->errores['password'] = "Campo obligatorio.";
        }
        if(empty($datos['password_confirm'])) {
            $this->errores['password_confirm'] = "Campo obligatorio.";
        }

        if (count($this->errores) > 0) {
            $this->errores[0] = "Faltan campos obligatorios.";
            return;
        }

        // Validar que las contraseñas coincidan (validación servidor)
        if ($datos['password'] !== $datos['password_confirm']) {
            $this->errores['password_confirm'] = "Las contraseñas no coinciden.";
            $this->errores[1] = "Las contraseñas no coinciden.";
            return;
        }

        // Sanear datos de entrada con filter_var
        $nombrePost = filter_var(trim($datos['nombre'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $apellidosPost = filter_var(trim($datos['apellidos'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $mailPost = filter_var(trim($datos['mail']), FILTER_SANITIZE_EMAIL);
        $fotoPost = str_replace(' ', '\\ ', ($datos['avatar'] ?? ''));
        $nombreImagen = "default.png";
        $rolPost = ((($datos['modo-admin'] ?? 'Falso') === "Verdadero")? ($datos['rol'] ?? 'cliente'): 'cliente');

        $userPost = filter_var(trim($datos['usuario']), FILTER_SANITIZE_SPECIAL_CHARS);
        $passPost = $datos['password'];
        $passConfPost = $datos['password_confirm'];

        // Validar formato de email
        if (!filter_var($mailPost, FILTER_VALIDATE_EMAIL)) {
            $this->errores['mail'] = "El formato del correo electrónico no es válido.";
            return;
        }

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
                $this->errores['usuario'] = 'Usuario ya ocupado';
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
            $this->errores[2] = "Error al conectar con la base de datos.";
        }
    }
}
