<?php
require_once (__DIR__ . '/../../comun/formularioBase.php');

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

        
        $ruta_avatares = IMAGENES_BASE;
        ob_start();
        include __DIR__ . "/../../comun/selector_imagenes.php"; 
        $htmlSelectorImagenes = ob_get_clean();

        foreach ($erroresCampos as $key => $value) {
            $erroresCampos[$key] = $value . '<br>';
        }
        
        $ruta_volver = htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : RAIZ_APP . "/");
        $ruta_volver2 = htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : RUTA_VISTAS . '/ajustes_admin.php');
        
        $html = <<<EOF
        {$htmlErroresGlobales}
        
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
        
        <div class="contenedor-botones">
            <button type="submit" id="boton_aceptar">Crear Usuario</button>
            <button onclick="window.location.href='{$ruta_volver2}'" type="button" id="boton_cancelar">Volver</button>
        </div>
        EOF;

        return $html;
    }

    protected function procesaFormulario(&$datos) {
        $this->errores = [];
        
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
                    
                    $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$userPost'";

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
                    $this->errores['usuario'] = 'Usuaro ya ocupado';
                } else if ($fila['email'] === $mailPost) {
                    $this->errores['mail'] = 'Email ya ocupado';
                }
            }
        }
    }
}/*
?>






















<form action="<?php echo RUTA_STATIC ?>/procesarRegistro.php" method="POST" enctype="multipart/form-data">
    <label>Nombre:</label>
    <br>
    <input type="text" name="nombre" required>
    <br>

    <label>Apellidos:</label>
    <br>
    <input type="text" name="apellidos" required>
    <br>
    
    <label>Correo Electrónico:</label>
    <br>
    <input type="email" name="mail" required>
    <br>

    <label>Usuario:</label>
    <br>
    <input type="text" name="username" required>
    <br>

    <label>Foto de perfil:</label>
    <br>
    <div class="seleccion-avatares">
        <?php foreach (IMAGENES_BASE as $indice => $archivo): ?>
            <label class="opcion-avatar">
                <img class="opcion-imagen-avatar" src="../img/perfiles/<?= $archivo; ?>" alt="Avatar <?= $indice; ?>">
                <input type="radio" name="foto_perfil" value="<?= $archivo; ?>" required>
            </label>
        <?php endforeach; ?>

        <label class="opcion-avatar">
            <div class="cuadro-subir-archivo">
                <p>Elegir<br>Archivo</p>
            </div>
            <input type="radio" name="foto_perfil" value="custom" id="radio-custom" required>
        </label>
    </div>

    <div id="archivo-avatar">
        <br>
        <input type="file" name="foto_perfil" accept="image/*">
        <br>
        <br>
        <br>
    </div>
    
    <label>Rol:</label>
    <select name="rol" id="select-rol-usuario">
        <option value="gerente">Gerente</option>
        <option value="cocinero">Cocinero</option>
        <option value="camarero">Camarero</option>
        <option value="cliente" selected>Cliente</option>
    </select>

    <input id="password" type="hidden" name="password" value="1234" required>
    <input type="hidden" name="password_confirm" value="1234" required>
    
    <input type="hidden" name="modo-admin" value="Verdadero">
    <input type="hidden" name="volver" value="<?php echo htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : RAIZ_APP . "/"); ?>">
    
    <div class="contenedor-botones">
    <button type="submit" id="boton_aceptar">Crear Usuario</button>
    
    <button onclick="window.location.href='<?php echo htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : RUTA_VISTAS . '/ajustes_admin.php'); ?>'" type="button" id="boton_cancelar">Volver</button>
    </div>
</form>

*/ ?>