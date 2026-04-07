<?php
//require_once '../DTO/Usuario.php';

class UsuarioDAO {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function listaUsuarios() {
        $lista = new SplDoublyLinkedList();
        
        $sql = "SELECT id, nombre_usuario, nombre, apellidos, email, rol, avatar FROM usuarios ORDER BY rol DESC, id";
        $resultado = mysqli_query($this->db, $sql);
        if($resultado){
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $lista->push(new Usuario($fila['id'], $fila['nombre_usuario'], $fila['nombre'], $fila['apellidos'], $fila['email'], $fila['rol'], $fila['avatar']));
            }
            $lista->rewind();
            
            return $lista; 
        }
        else{
            throw new ErrorEnConsultaException('Ha habido un error al consultar la tabla usuarios');
        }
    }

    public function buscaUsuario($username){
        $sql = "SELECT id, nombre_usuario, nombre, apellidos, email, rol, avatar FROM usuarios WHERE nombre_usuario = '$username'";
        $resultado = mysqli_query($this->db, $sql);
        
        if($fila = mysqli_fetch_assoc($resultado)){
            return new Usuario($fila['id'], $fila['nombre_usuario'], $fila['nombre'], $fila['apellidos'], $fila['email'], $fila['rol'], $fila['avatar']); 
        }
        else{
            throw new UsuarioNoExisteException('El usuario ' . $username . ' no existe');
        }
    }

    public function crearUsuario($datos = array()){
        $id = $datos['id'];
        $usuario = $datos['nombre_usuario'];
        $nombre = $datos['nombre'];
        $apellidos = $datos['apellidos'];
        $email = $datos['email'];
        $rol = $datos['rol'];
        $avatar = $datos['avatar'];
        $password = $datos['password']; // Password ya hasheada

        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$usuario'";
        $resultado = mysqli_query($this->db, $sql);
        
        if(!mysqli_fetch_assoc($resultado)){
            $sql = "INSERT INTO usuarios (nombre_usuario, email, nombre, apellidos, password, rol, avatar) VALUES ('$usuario', '$email', '$nombre', '$apellidos', '$password', '$rol', '$avatar')";
            if(mysqli_query($this->db, $sql)){
                return new Usuario($id, $usuario, $nombre, $apellidos, $email, $rol, $avatar); 
            }
            else{
                throw new ErrorAlInsertarBBDDException('No se ha podido crear al usuario ' . $usuario . ".");    
            }
        }
        else{
            throw new UsuarioYaExisteException('El usuario ' . $usuario . ' ya esta en uso');
        }
    }

    public function modificarUsuario($id, $campo, $valor){
        // gestión de modificación
        $ret = false;
        $query = "UPDATE usuarios SET $campo = ? WHERE id = ?";
        $stmt = mysqli_prepare($db, $query);

        switch (strtolower($campo)) {
            case 'nombre_usuario':
                mysqli_stmt_bind_param($stmt, "si", $valor, $id);
                $ret = (mysqli_stmt_execute($stmt)?true:false);

                $usuario->set_usuario($valor);
                break;

            case 'nombre':
                mysqli_stmt_bind_param($stmt, "si", $valor, $id);
                $ret = (mysqli_stmt_execute($stmt)?true:false);

                $usuario->set_nombre($valor);
                break;

            case 'apellidos':
                mysqli_stmt_bind_param($stmt, "si", $valor, $id);
                $ret = (mysqli_stmt_execute($stmt)?true:false);
                
                $usuario->set_apellidos($valor);
                break;

            case 'email':
                mysqli_stmt_bind_param($stmt, "si", $valor, $id);
                $ret = (mysqli_stmt_execute($stmt)?true:false);
                
                $usuario->set_email($valor);
                break;

            case 'rol':
                mysqli_stmt_bind_param($stmt, "si", $valor, $id);
                $ret = (mysqli_stmt_execute($stmt)?true:false);
                
                $usuario->set_rol($valor);
                break;

            case 'avatar':
                mysqli_stmt_bind_param($stmt, "si", $valor, $id);
                $ret = (mysqli_stmt_execute($stmt)?true:false);
                
                $usuario->set_avatar($valor);
                break;
            
            case 'password':
                // Modificación en BBDD
                break;    

            default:
                throw new CampoInexistenteException('El campo ' . ucfirst($campo) . ' no existe en la clase Usuario');
                break;
        }

        return $ret;
    }

    public function obtenerImagen($id){
        $query = "SELECT avatar FROM usuarios WHERE id = ?";
        $stmt = mysqli_prepare($db_connection, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $resultado = mysqli_stmt_get_result($stmt);
            if ($fila = mysqli_fetch_assoc($resultado)) {
                return $fila['avatar'];
            }
            else{
                throw new UsuarioNoExisteException('El usuario ' . $username . ' no existe');
            }
        }
        else{
            throw new ErrorEnConsultaException();
        }
    }

    public function usoImagen($id, $img_actual){
        $query = "SELECT * FROM usuarios WHERE avatar = ? AND id != ?";
        $stmt = mysqli_prepare($db_connection, $query);
        mysqli_stmt_bind_param($stmt, "si", $img_actual, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $resultado = mysqli_stmt_get_result($stmt);
            if (!($fila = mysqli_fetch_assoc($resultado))) {
                return false;
            }
            return true;
        }
        return false;
    }
}
?>