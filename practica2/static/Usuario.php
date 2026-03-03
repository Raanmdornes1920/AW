<?php
class Usuario {
    private $nombreUsuario;
    private $rol;
    private $fotoPerfil;
    private $nombre;
    private $apellidos;
    private $email;

    public function __construct($id, $usuario, $nombre, $apellidos, $email, $rol, $fotoPerfil) {
        $this->idUsuario = $id;
        $this->nombreUsuario = $usuario;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->rol = $rol;
        $this->fotoPerfil = $fotoPerfil;
    }

    public function id() {
        return $this->idUsuario;
    }

    public function username() {
        return $this->nombreUsuario;
    }

    public function nombre() {
        return $this->nombre;
    }

    public function apellidos() {
        return $this->apellidos;
    }

    public function email() {
        return $this->email;
    }

    public function fotoPerfil() {
        return $this->fotoPerfil;
    }

    public function roles() {
        return $this->rol;
    }
}?>