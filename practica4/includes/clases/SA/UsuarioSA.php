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

    public function buscaDatosUsuario($usuario){
        return $this->DAO->buscaDatosUsuario($usuario);
    }

    public function validarPasswordUsuario($usuario, $pass){
        return $this->DAO->validarPasswordUsuario($usuario, $pass);
    }

    public function cambiarPasswordUsuario($usuario, $pass){
        return $this->DAO->cambiarPasswordUsuario($usuario, $pass);
    }

     public function crearUsuario($datos = array()){
        return $this->DAO->crearUsuario($datos);
    }

     public function eliminarUsuario($id){
        return $this->DAO->eliminarUsuario($id);
     }

     public function validarUserMail($usuario, $email){
        return $this->DAO->validarUserMail($usuario, $email);
     }

     public function buscaUsuario($usuario){
        return $this->DAO->buscaUsuario($usuario);
     }

     public function buscaUsuarioPorID($id){
        return $this->DAO->buscaUsuarioPorID($id);
     }
}
?>