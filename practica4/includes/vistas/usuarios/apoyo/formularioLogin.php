<?php
require_once (__DIR__ . '/../../../config.php');
require_once (__DIR__ . '/../../comun/formularioBase.php');

class FormularioLogin extends formularioBase {
    private $SA;

    public function __construct() {
        global $db_connection;
        $this->SA  = new UsuarioSA($db_connection);

        parent::__construct('formLogin', ['urlRedireccion' => RAIZ_APP . '/', 'enctype' => 'multipart/form-data']);
    }

    protected function generaCamposFormulario(&$datos) {
        $usuario = $datos['usuario'] ?? '';
        $password = $datos['password'] ?? '';

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
        <input type="text" name="usuario" autocomplete="username" value="$usuario" required>
        {$erroresCampos['usuario']}
        <br>

        <label>Contraseña:</label>
        <br>
        <input type="password" autocomplete="current-password" name="password" value="$password" required>
        {$erroresCampos['password']}
        <br><br>

        <button type="submit">Entrar</button>
        EOF;

        return $html;
    }

    protected function procesaFormulario(&$datos) {
        global $db_connection;
        $this->errores = [];

        $userPost = filter_var($datos['usuario']);
        $passPost = filter_var($datos['password']);

        if (empty($userPost) || empty($passPost)) {
            $this->errores['campos_vacios'] = 'Por favor, rellena todos los campos';
        }
        
        try {
            
            $_SESSION['usuario'] = $usuario = $this->SA->login($userPost, $passPost);
            $_SESSION['login'] = true;
            
            header("Location: " . RAIZ_APP . "/");
            exit();

        } catch (\UsuarioNoExisteException $e1) {   

            $this->errores['usuario'] = 'El usuario indicado no existe';

        } catch (\PasswordIncorrectoException $e2) {

            $this->errores['password'] = 'Contraseña Incorrecta';
        }
    }
}