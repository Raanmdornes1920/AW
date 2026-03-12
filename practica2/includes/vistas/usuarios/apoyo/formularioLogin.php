<?php
require_once (__DIR__ . '/../../../config.php');
require_once (__DIR__ . '/../../comun/formularioBase.php');

// Hereda de formularioBase (asegúrate de que la ruta a formularioBase sea correcta)
class FormularioLogin extends formularioBase {

    public function __construct() {
        // ID del form y dónde redirigir si todo va bien
        parent::__construct('formLogin', ['urlRedireccion' => RAIZ_APP . '/', 'enctype' => 'multipart/form-data']);
    }

    protected function generaCamposFormulario(&$datos) {
        // Recuperar valores previos para no borrarlos si hay error
        $usuario = $datos['usuario'] ?? '';

        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['usuario', 'password', 'campos_vacios'], $this->errores);

        ob_start();
        include __DIR__ . "/vistas/comun/selector_imagenes.php"; 
        $htmlSelectorImagenes = ob_get_clean();
        
        foreach ($erroresCampos as $key => $value) {
            $erroresCampos[$key] = $value . '<br>';
        }

        $html = <<<EOF
        {$htmlErroresGlobales}
        {$erroresCampos['campos_vacios']}
        <label>Usuario:</label>
        <br>
        <input type="text" name="usuario" required>
        {$erroresCampos['usuario']}
        <br>

        <label>Contraseña:</label>
        <br>
        <input type="password" name="password" required>
        {$erroresCampos['password']}
        <br><br>

        <button type="submit">Entrar</button>
        EOF;

        return $html;
    }

    protected function procesaFormulario(&$datos) {
        global $db_connection;
        $this->errores = [];


        $userPost = $datos['usuario'];
        $passPost = $datos['password'];

        if (empty($userPost) || empty($passPost)) {
            $this->errores['campos_vacios'] = 'Por favor, rellena todos los campos';
        }

        $userEscaped = mysqli_real_escape_string($db_connection, $userPost);
        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$userEscaped'";

        $resultado = mysqli_query($db_connection, $sql);


        if ($resultado && mysqli_num_rows($resultado) === 1) {
            $fila = mysqli_fetch_assoc($resultado);

            if ($fila && password_verify($passPost, $fila['password'])) {
            
                $_SESSION['login'] = true;
                $_SESSION['usuario'] = new Usuario($fila['id'], $fila['nombre_usuario'], $fila['nombre'], $fila['apellidos'], $fila['email'], $fila['rol'], $fila['avatar']);
                
                header("Location: " . RAIZ_APP . "/");
                exit();
            } else {
                $this->errores['password'] = 'Contraseña Incorrecta';
            }
        } else {
            $this->errores['usuario'] = 'El usuario indicado no existe';
        }
    }
}