<?php

class Usuario implements JsonSerializable {
    private $id;
    private $usuario;
    private $nombre;
    private $apellidos;
    private $email;
    private $rol;
    private $avatar;

    public function jsonSerialize(): mixed {
        return [
            'id' => $this->id,
            'nombre_usuario' => $this->usuario,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'email' => $this->email,
            'rol' => $this->rol,
            'avatar' => $this->avatar
        ];
    }

    public function __construct($id, $usuario, $nombre, $apellidos, $email, $rol, $fotoPerfil) {
        $this->id = $id;
        $this->usuario = $usuario;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->rol = $rol;
        $this->avatar = $fotoPerfil;
    }

    public function equals(Usuario $otro): bool {
        if (!($otro instanceof self)) {
            return false;
        }
        return $this->id === $otro->id();
    }

    // Getters
    public function id() {
        return $this->id;
    }

    public function usuario() {
        return $this->usuario;
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

    public function rol() {
        return $this->rol;
    }

    public function avatar() {
        return $this->avatar;
    }

    // Setters
    public function set_usuario($usuario) {
        $this->usuario = $usuario;
    }

    public function set_nombre($nombre) {
        $this->nombre = $nombre;
    }

    public function set_apellidos($apellidos) {
        $this->apellidos = $apellidos;
    }

    public function set_email($email) {
        $this->email = $email;
    }

    public function set_rol($rol) {
        $this->rol = $rol;
    }

    public function set_avatar($avatar) {
        $this->avatar = $avatar;
    }

    public function modificarUsuario($campo, $valor){
        switch (strtolower($campo)) {
            case 'usuario':
                $this->set_usuario($valor);
                return true;
                break;

            case 'nombre':
                $this->set_nombre($valor);
                return true;
                break;

            case 'apellidos':
                $this->set_apellidos($valor);
                return true;
                break;

            case 'email':
                $this->set_email($valor);
                return true;
                break;

            case 'rol':
                $this->set_rol($valor);
                return true;
                break;

            case 'avatar':
                $this->set_avatar($valor);
                return true;
                break;

            default:
                throw new CampoInexistenteException('El campo ' . ucfirst($campo) . ' no existe en la clase Usuario');
                break;
        }

        return false;
    }

}
?>