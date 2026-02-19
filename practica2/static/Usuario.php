<?php
class Usuario {
    private $nombreUsuario;
    private $rol;

    public function __construct($nombre, $rol) {
        $this->nombreUsuario = $nombre;
        $this->rol = $rol;
    }

    public function username() {
        return $this->nombreUsuario;
    }

    public function roles() {
        return $this->rol;
    }
}?>