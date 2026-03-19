<?php

class UsuarioSA {
    private $DAO;

    public function __construct($db_connection) {
        $this->DAO = new UsuarioDAO($db_connection);
    }

    public function login($nombreUsuario, $password) {
        $usuario = $DAO->buscaUsuario($nombreUsuario);

        if ($usuario) {
            if (password_verify($password, $usuario->getPasswordHash())) {
                return $usuario;
            }
        }
        return false;
    }

    public function getListaUsuarios() {
        return $DAO->listaUsuarios();
    }

    public function modificarusuario(Usuario $usuario, $campo, $valor) {
        return $DAO->modificarUsuario($usuario, $campo, $valor);
    }

    public function obtenerImagen($id) {
        return $DAO->obtenerImagen($id);
    }
}
?>