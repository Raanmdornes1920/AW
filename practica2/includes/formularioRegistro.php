<?php
require_once (__DIR__ . '/config.php');
require_once (__DIR__ . '/vistas/comun/formularioBase.php');

// Hereda de formularioBase (asegúrate de que la ruta a formularioBase sea correcta)
class FormularioRegistro extends formularioBase {

    public function __construct() {
        // ID del form y dónde redirigir si todo va bien
        parent::__construct('formRegistro', ['urlRedireccion' => RAIZ_APP . '/', 'enctype' => 'multipart/form-data']);
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
        include __DIR__ . "/vistas/comun/selector_imagenes.php"; 
        $htmlSelectorImagenes = ob_get_clean();

        $html = <<<EOF
        {$htmlErroresGlobales}
        <fieldset class="fieldset_form">
            <legend>Registro Bistro FDI</legend>
            
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
        </fieldset>
        EOF;

        return $html;
    }

    protected function procesaFormulario(&$datos) {
        $this->errores = [];
        // ----------------------------------------------------------------------------------------------------------------------------------------------------
        global $db_connection;
        $nombrePost = ($datos['nombre'] ?? '');
        $apellidosPost = ($datos['apellidos'] ?? '');
        $mailPost = trim($datos['mail'] ?? '');
        $fotoPost = str_replace(' ', '\ ', ($datos['avatar'] ?? ''));
        $nombreImagen = "default.png";
        $rolPost = ((($datos['modo-admin'] ?? 'Falso') === "Verdadero")? ($datos['rol'] ?? 'cliente'): 'cliente');

        $userPost = trim($datos['usuario'] ?? '');
        $passPost = ($datos['password'] ?? 'password');
        $passConfPost = ($datos['password_confirm'] ?? 'password_confirm');

        $userEscaped = mysqli_real_escape_string($db_connection, $userPost);
        $mailEscaped = mysqli_real_escape_string($db_connection, $mailPost);

        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$userEscaped' OR email = '$mailEscaped'";

        $resultado = mysqli_query($db_connection, $sql);


        if ($resultado && mysqli_num_rows($resultado) === 0) {
        
            if (isset($datos['avatar']) && $datos['avatar'] === 'custom' && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            
                $fileTmpPath = $_FILES['avatar']['tmp_name'];
                $fileName = $_FILES['avatar']['name'];
                
                $fileNameClean = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
                
                $dest_path = DIR_RAIZ . "/img/perfiles/" . $fileNameClean;
        // ----------------------------------------------------------------------------------------------------------------------------------------------------        
                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    $nombreImagen = $fileNameClean;
                    chmod($dest_path, 0666); 
                }
            }
            else{
                $nombreImagen = $datos['avatar'];
                $dest_path = "../img/perfiles/" . $fileNameClean;
            }

            $passwordHasheada = password_hash($passPost, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (nombre_usuario, email, nombre, apellidos, password, rol, avatar) VALUES ('$userPost', '$mailPost', '$nombrePost', '$apellidosPost', '$passwordHasheada', '$rolPost', '$nombreImagen')";

            
            if (mysqli_query($db_connection, $sql)) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                if(isset($datos['modo-admin']) && $datos['modo-admin'] === "Verdadero"){
                    $_SESSION['cambio'] = "Crear Usuario";
                    $_SESSION['error_editar_perfil'] = "Ninguno";
                }
                else{
                    
                    $sql = "SELECT id FROM usuarios WHERE nombre_usuario = '$userPost¡'";

                    $resultado = mysqli_query($db_connection, $sql);
                    $fila = mysqli_fetch_assoc($resultado);
                    $idPost = $fila['id'];
                    
                    $_SESSION['login'] = true;
                    $_SESSION['usuario'] = new Usuario($idPost, $userPost, $nombrePost, $apellidosPost, $mailPost, $rolPost, $nombreImagen);
                }
                header("Location: " . (isset($datos['volver']) ? $datos['volver'] : RAIZ_APP . "/"));
                exit();
                
            }
        } else {
            if(isset($datos['modo-admin']) && $datos['modo-admin'] === "Verdadero"){
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
        // ----------------------------------------------------------------------------------------------------------------------------------------------------
        
        /* Ejemplo de como indicar errores
        $usuario = trim($datos['usuario'] ?? '');
        
        if (strlen($usuario) < 4) {
            $this->errores['usuario'] = 'El nombre de usuario debe tener 4 caracteres.';
        }

        // 2. Gestión de la imagen (Tu lógica de move_uploaded_file)
        $nombreImagen = "default.png";
        if (isset($datos['avatar']) && $datos['avatar'] === 'custom' && isset($_FILES['avatar'])) {
            // ... Aquí pones tu código de subir imagen ...
            // $nombreImagen = $fileNameClean;
        }

        // 3. Llamada al SA (Lógica de negocio)
        if (count($this->errores) === 0) {
            try {
                $usuarioSA = new UsuarioSA();
                $usuario = $usuarioSA->registrar(
                    $usuario, $datos['password'], $datos['nombre'], 
                    $datos['apellidos'], $datos['mail'], $nombreImagen
                );
                
                // Si el SA no lanza excepción, logueamos
                $_SESSION['login'] = true;
                $_SESSION['usuario'] = $usuario;
                
            } catch (Exception $e) {
                // Si el SA lanza "Usuario ya existe", se muestra como error global
                $this->errores[] = $e->getMessage();
            }
        }*/
    }
}