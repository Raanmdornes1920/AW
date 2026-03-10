<?php
require_once 'config.php';

class Usuario implements JsonSerializable {
    private $idUsuario;
    private $nombreUsuario;
    private $rol;
    private $fotoPerfil;
    private $nombre;
    private $apellidos;
    private $email;

    public static function listaUsuarios() {
        global $db_connection;
        $lista = new SplDoublyLinkedList();
        
        $sql = "SELECT id, nombre_usuario, nombre, apellidos, email, rol, avatar FROM usuarios ORDER BY rol DESC, id";
        $resultado = mysqli_query($db_connection, $sql);
        if($resultado){
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $lista->push(new Usuario($fila['id'], $fila['nombre_usuario'], $fila['nombre'], $fila['apellidos'], $fila['email'], $fila['rol'], $fila['avatar']));
            }
            $lista->rewind();
            
            return $lista; 
        }
        else{
            return null;
        }
    }

    public function jsonSerialize(): mixed {
        return [
            'id' => $this->idUsuario,
            'nombre_usuario' => $this->nombreUsuario,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'email' => $this->email,
            'rol' => $this->rol,
            'avatar' => $this->fotoPerfil
        ];
    }

    public function __construct($id, $usuario, $nombre, $apellidos, $email, $rol, $fotoPerfil) {
        $this->idUsuario = $id;
        $this->nombreUsuario = $usuario;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->rol = $rol;
        $this->fotoPerfil = $fotoPerfil;
    }

    public function set_value($campo, $valor){
        switch($campo){
            case 'usuario':
                $this->nombreUsuario = $valor;            
                break;
            case 'nombre':
                $this->nombre = $valor;
                break;
            case 'apellidos':
                $this->apellidos = $valor;
                break;
            case 'email':
                $this->email = $valor;
                break;
            case 'rol':
                $this->rol = $valor;
                break;
            case 'avatar':
                $this->fotoPerfil = $valor;
                break;
            default:
                return false;
        }
        return true;
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

    public function rol() {
        return $this->rol;
    }


}?>