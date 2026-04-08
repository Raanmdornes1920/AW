<?php

class UsuarioNoExisteException extends \Exception {
    public function __construct($message = "El usuario no existe en la base de datos", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class CampoInexistenteException extends \Exception {
    public function __construct($message = "El usuario no existe en la base de datos", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class ErrorEnConsultaException extends \Exception {
    public function __construct($message = "No se ha podido realizar la consulta a base de datos", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class ErrorAlInsertarBBDDException extends \Exception {
    public function __construct($message = "No se ha podido realizar la inserción a base de datos", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class UsuarioYaExisteException extends \Exception {
    public function __construct($message = "El usuario ya existe en base de datos", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class PasswordIncorrectoException extends \Exception {
    public function __construct($message = "Contraseña Incorrecta", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class UsuarioOcupadoException extends \Exception {
    private $usuario;
    public function __construct($usuario, $message = "El usuario ya existe en base de datos", $code = 0, Throwable $previous = null) {
        $this->usuario = $usuario;
        parent::__construct($message, $code, $previous);
    }

    public function usuario() {
        return $this->usuario;
    }
}

class MailOcupadoException extends \Exception {
    private $mail;

    public function __construct($mail, $message = "El email ya existe en base de datos", $code = 0, Throwable $previous = null) {
        $this->mail = $mail;
        parent::__construct($message, $code, $previous);
    }

    public function mail() {
        return $this->mail;
    }
}

?>