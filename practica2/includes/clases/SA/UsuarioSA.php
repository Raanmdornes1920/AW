<?php

class UsuarioSA {

    public function login($nombreUsuario, $password) {
        $usuario = UsuarioDAO::buscaUsuario($nombreUsuario);

        if ($usuario) {
            // 2. Comprobar contraseña (suponiendo que usas password_verify)
            if (password_verify($password, $usuario->getPasswordHash())) {
                return $usuario; // Login correcto
            }
        }
        return false; // Login fallido
    }

    public static function getListaUsuarios() {
        return UsuarioDAO::listaUsuarios();
    }

    public static function modificarusuario(Usuario $usuario, $campo, $valor) {
        return UsuarioDAO::modificarUsuario($usuario, $campo, $valor);
    }
}
?>