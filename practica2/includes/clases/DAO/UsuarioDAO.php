<?php
//require_once '../DTO/Usuario.php';

class UsuarioDAO {

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
            throw new ErrorEnConsultaException('Ha habido un error al consultar la tabla usuarios');
        }
    }

    public static function buscaUsuario($username){
        global $db_connection;
        
        $sql = "SELECT id, nombre_usuario, nombre, apellidos, email, rol, avatar FROM usuarios WHERE nombre_usuario = '$username'";
        $resultado = mysqli_query($db_connection, $sql);
        
        if($fila = mysqli_fetch_assoc($resultado)){
            return new Usuario($fila['id'], $fila['nombre_usuario'], $fila['nombre'], $fila['apellidos'], $fila['email'], $fila['rol'], $fila['avatar']); 
        }
        else{
            throw new UsuarioNoExisteException('El usuario ' . $username . ' no existe');
        }
    }

    public static function crearUsuario($datos = array()){
        global $db_connection;
        
        $id = $datos['id'];
        $usuario = $datos['nombre_usuario'];
        $nombre = $datos['nombre'];
        $apellidos = $datos['apellidos'];
        $email = $datos['email'];
        $rol = $datos['rol'];
        $avatar = $datos['avatar'];
        $password = $datos['password']; // Password ya hasheada

        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = '$usuario'";
        $resultado = mysqli_query($db_connection, $sql);
        
        if(!mysqli_fetch_assoc($resultado)){
            $sql = "INSERT INTO usuarios (nombre_usuario, email, nombre, apellidos, password, rol, avatar) VALUES ('$usuario', '$email', '$nombre', '$apellidos', '$password', '$rol', '$avatar')";
            if(mysqli_query($db_connection, $sql)){
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

    public static function modificarUsuario(Usuario $usuario, $campo, $valor){
        // gestión de modificación
        switch (strtolower($campo)) {
            case 'usuario':
                // Modificación en BBDD
                $usuario->set_usuario($valor);
                break;

            case 'nombre':
                // Modificación en BBDD
                $usuario->set_nombre($valor);
                break;

            case 'apellidos':
                // Modificación en BBDD
                $usuario->set_apellidos($valor);
                break;

            case 'email':
                // Modificación en BBDD
                $usuario->set_email($valor);
                break;

            case 'rol':
                // Modificación en BBDD
                $usuario->set_rol($valor);
                break;

            case 'avatar':
                // Modificación en BBDD
                $usuario->set_avatar($valor);
                break;
            
            case 'password':
                // Modificación en BBDD
                break;    

            default:
                throw new CampoInexistenteException('El campo ' . ucfirst($campo) . ' no existe en la clase Usuario');
                break;
        }
    }

}
?>