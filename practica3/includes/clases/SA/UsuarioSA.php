<?php

class UsuarioSA {
    private $DAO;

    public function __construct($db_connection) {
        $this->DAO = new UsuarioDAO($db_connection);
    }

    public function login($nombreUsuario, $password) {
        return $this->DAO->login($nombreUsuario, $password);
    }

    public function usuarioValido($usuario){
        return $this->DAO->usuarioValido($usuario);
    }

    public function usuarioEnUso($usuario){
        return $this->DAO->usuarioEnUso($usuario);
    }
    
    public function getListaUsuarios() {
        return $this->DAO->listaUsuarios();
    }

    public function modificarusuario($id, $campo, $valor) {
        return $this->DAO->modificarUsuario($id, $campo, $valor);
    }

    public function obtenerImagen($id) {
        return $this->DAO->obtenerImagen($id);
    }

    public function usoImagen($id, $img_actual){
        return $this->DAO->usoImagen($id, $img_actual);
    }
}
?>