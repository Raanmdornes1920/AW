<?php
//require_once '../DTO/Usuario.php';

class UsuarioDAO {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function login($userPost, $passPost){

        $userEscaped = mysqli_real_escape_string($this->db, $userPost);
        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        $stmt = mysqli_prepare($this->db, $sql);

        mysqli_stmt_bind_param($stmt, "s", $userPost);
        mysqli_stmt_execute($stmt);
        
        $resultado = mysqli_stmt_get_result($stmt);

        if ($resultado && mysqli_num_rows($resultado) === 1) {
            $fila = mysqli_fetch_assoc($resultado);

            if ($fila && password_verify($passPost, $fila['password'])) {
            
                return new Usuario($fila['id'], $fila['nombre_usuario'], $fila['nombre'], $fila['apellidos'], $fila['email'], $fila['rol'], $fila['avatar']);
            } else {
                throw new PasswordIncorrectoException();
            }
        } else {
            throw new UsuarioNoExisteException();
        }
    }

    public function usuarioValido($usuario){
        
        $nombre_sesion = $usuario->usuario();
        $sql = "SELECT nombre_usuario FROM usuarios WHERE nombre_usuario = ?";
        $stmt = mysqli_prepare($this->db, $sql);

        mysqli_stmt_bind_param($stmt, "s", $nombre_sesion);
        mysqli_stmt_execute($stmt);
        
        $resultado = mysqli_stmt_get_result($stmt);
        
        if ($fila = mysqli_fetch_assoc($resultado)) {
            return true;
        }

        return false;
    }

    public function usuarioEnUso($usuario){
        
        $sql = "SELECT nombre_usuario FROM usuarios WHERE nombre_usuario = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($fila = mysqli_fetch_assoc($resultado)) {
            return true;
        }

        return false;
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

    public function eliminarUsuario($id){
        $queryCheck = "SELECT nombre_usuario FROM usuarios WHERE id = ?";
        $stmtCheck = mysqli_prepare($this->db, $queryCheck);
        
        mysqli_stmt_bind_param($stmtCheck, "i", $id);
        mysqli_stmt_execute($stmtCheck);
        
        $resultado = mysqli_stmt_get_result($stmtCheck);

        if ($fila = mysqli_fetch_assoc($resultado)) {
            $query = "DELETE FROM usuarios WHERE id = ?";
            $stmt = mysqli_prepare($this->db, $query);
            
            mysqli_stmt_bind_param($stmt, "i", $id);
            
            if (mysqli_stmt_execute($stmt)) {
                return $fila['nombre_usuario'];
            } else {
                throw new ErrorEnConsultaException('No se ha podido eliminar al usuario con ID ' . $id . '.');
            }    
        } else {
            throw new UsuarioNoExisteException('El usuario con ID ' . $id . ' no existe');
        }
    }
    
    public function buscaUsuario($username){
        $sql = "SELECT id, nombre_usuario, nombre, apellidos, email, rol, avatar FROM usuarios WHERE nombre_usuario = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        
        $resultado = mysqli_stmt_get_result($stmt);

        if($fila = mysqli_fetch_assoc($resultado)){
            return new Usuario($fila['id'], $fila['nombre_usuario'], $fila['nombre'], $fila['apellidos'], $fila['email'], $fila['rol'], $fila['avatar']); 
        }
        else{
            throw new UsuarioNoExisteException('El usuario ' . $username . ' no existe');
        }
    }

    public function validarPasswordUsuario($usuario, $pass){

        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if ($resultado && mysqli_num_rows($resultado) > 0) {
            $fila = mysqli_fetch_assoc($resultado);

            return password_verify($pass, $fila['password']);
        }
        else{
            throw new UsuarioNoExisteException('El usuario ' . $usuario . ' no existe');
        }
    }

    public function cambiarPasswordUsuario($usuario, $pass){
                
        $nuevaPassHash = password_hash($pass, PASSWORD_DEFAULT);        
        $query = "UPDATE usuarios SET password = ? WHERE BINARY nombre_usuario = ?";
        $stmt = mysqli_prepare($this->db, $query);
        
        mysqli_stmt_bind_param($stmt, "ss", $nuevaPassHash, $usuario);
        
        return mysqli_stmt_execute($stmt);
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

        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        if(!mysqli_fetch_assoc($resultado)){
            $sql = "INSERT INTO usuarios (nombre_usuario, email, nombre, apellidos, password, rol, avatar) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($this->db, $sql);
            
            mysqli_stmt_bind_param($stmt, "sssssss", 
                $usuario, 
                $email, 
                $nombre, 
                $apellidos, 
                $password, 
                $rol, 
                $avatar
            );

            if(mysqli_stmt_execute($stmt)){
                $sql = "SELECT id FROM usuarios WHERE nombre_usuario = ?";
                $stmt = mysqli_prepare($this->db, $sql);
                
                mysqli_stmt_bind_param($stmt, "s", $usuario);
                mysqli_stmt_execute($stmt);

                $resultado = mysqli_stmt_get_result($stmt);
                return new Usuario($resultado['id'], $usuario, $nombre, $apellidos, $email, $rol, $avatar); 
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
        $stmt = mysqli_prepare($this->db, $query);

        switch (strtolower($campo)) {
            case 'nombre_usuario':
                mysqli_stmt_bind_param($stmt, "si", $valor, $id);
                $ret = (mysqli_stmt_execute($stmt)?true:false);
                break;

            case 'nombre':
                mysqli_stmt_bind_param($stmt, "si", $valor, $id);
                $ret = (mysqli_stmt_execute($stmt)?true:false);
                break;

            case 'apellidos':
                mysqli_stmt_bind_param($stmt, "si", $valor, $id);
                $ret = (mysqli_stmt_execute($stmt)?true:false);
                break;

            case 'email':
                mysqli_stmt_bind_param($stmt, "si", $valor, $id);
                $ret = (mysqli_stmt_execute($stmt)?true:false);
                break;

            case 'rol':
                mysqli_stmt_bind_param($stmt, "si", $valor, $id);
                $ret = (mysqli_stmt_execute($stmt)?true:false);
                break;

            case 'avatar':
                mysqli_stmt_bind_param($stmt, "si", $valor, $id);
                $ret = (mysqli_stmt_execute($stmt)?true:false);
                break;
            
            case 'password':
                mysqli_stmt_bind_param($stmt, "si", $valor, $id);
                $ret = (mysqli_stmt_execute($stmt)?true:false);
                break;    

            default:
                throw new CampoInexistenteException('El campo ' . ucfirst($campo) . ' no existe en la clase Usuario');
                break;
        }

        return $ret;
    }

    public function obtenerImagen($id){
        $query = "SELECT avatar FROM usuarios WHERE id = ?";
        $stmt = mysqli_prepare($this->db, $query);
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
        $stmt = mysqli_prepare($this->db, $query);
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

    public function validarUserMail($usuario, $email){

        $userEscaped = mysqli_real_escape_string($this->db, $usuario);
        $mailEscaped = mysqli_real_escape_string($this->db, $email);
        
        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ? OR email = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        
        mysqli_stmt_bind_param($stmt, "ss", $userEscaped, $mailEscaped);
        mysqli_stmt_execute($stmt);
        
        $resultado = mysqli_stmt_get_result($stmt);

        if ($resultado && mysqli_num_rows($resultado) === 0) {
            return true;
        }
        else{
            $fila = mysqli_fetch_assoc($resultado);
            if($fila['nombre_usuario'] === $usuario){
                throw new UsuarioOcupadoException($usuario, 'El usuario ' . $usuario . ' ya existe en base de datos');
            }
            else if($fila['email'] === $email){
                throw new MailOcupadoException($email, 'El email ' . $email . ' ya existe en base de datos');
            }
        }

        throw new ErrorEnConsultaException('Ha habido un error al consultar la tabla usuarios');
    }
}
?>