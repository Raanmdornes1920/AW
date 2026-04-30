<?php
//require_once '../DTO/Usuario.php';

class UsuarioDAO {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function login($userPost, $passPost){

        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "s", $userPost);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($resultado && mysqli_num_rows($resultado) === 1) {
            $fila = mysqli_fetch_assoc($resultado);

            if ($fila && password_verify($passPost, $fila['password'])) {
            
                mysqli_free_result($resultado);
                mysqli_stmt_close($stmt);
                return new Usuario($fila['id'], $fila['nombre_usuario'], $fila['nombre'], $fila['apellidos'], $fila['email'], $fila['rol'], $fila['avatar']);
            } else {
                mysqli_free_result($resultado);
                mysqli_stmt_close($stmt);
                throw new PasswordIncorrectoException();
            }
        } else {
            if ($resultado) mysqli_free_result($resultado);
            mysqli_stmt_close($stmt);
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
            mysqli_free_result($resultado);
            mysqli_stmt_close($stmt);
            return true;
        }

        mysqli_free_result($resultado);
        mysqli_stmt_close($stmt);
        return false;
    }

    public function usuarioEnUso($usuario){
        
        $sql = "SELECT nombre_usuario FROM usuarios WHERE nombre_usuario = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "s", $usuario);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        
        if ($fila = mysqli_fetch_assoc($resultado)) {
            mysqli_free_result($resultado);
            mysqli_stmt_close($stmt);
            return true;
        }

        mysqli_free_result($resultado);
        mysqli_stmt_close($stmt);
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
            mysqli_free_result($resultado);
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
            mysqli_free_result($resultado);
            mysqli_stmt_close($stmtCheck);

            $query = "DELETE FROM usuarios WHERE id = ?";
            $stmt = mysqli_prepare($this->db, $query);
            
            mysqli_stmt_bind_param($stmt, "i", $id);
            
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                return $fila['nombre_usuario'];
            } else {
                mysqli_stmt_close($stmt);
                throw new ErrorEnConsultaException('No se ha podido eliminar al usuario con ID ' . $id . '.');
            }    
        } else {
            mysqli_free_result($resultado);
            mysqli_stmt_close($stmtCheck);
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
            mysqli_free_result($resultado);
            mysqli_stmt_close($stmt);
            return new Usuario($fila['id'], $fila['nombre_usuario'], $fila['nombre'], $fila['apellidos'], $fila['email'], $fila['rol'], $fila['avatar']); 
        }
        else{
            mysqli_free_result($resultado);
            mysqli_stmt_close($stmt);
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
            mysqli_free_result($resultado);
            mysqli_stmt_close($stmt);
            return password_verify($pass, $fila['password']);
        }
        else{
            if ($resultado) mysqli_free_result($resultado);
            mysqli_stmt_close($stmt);
            throw new UsuarioNoExisteException('El usuario ' . $usuario . ' no existe');
        }
    }

    public function cambiarPasswordUsuario($usuario, $pass){
                
        $nuevaPassHash = password_hash($pass, PASSWORD_DEFAULT);        
        $query = "UPDATE usuarios SET password = ? WHERE BINARY nombre_usuario = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "ss", $nuevaPassHash, $usuario);
        
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    public function crearUsuario($datos = array()){
        $usuario = $datos['nombre_usuario'];
        $nombre = $datos['nombre'];
        $apellidos = $datos['apellidos'];
        $email = $datos['email'];
        $rol = $datos['rol'];
        $avatar = $datos['avatar'];
        $password = $datos['password']; // Password ya hasheada

        // Verificar si el usuario ya existe con sentencia preparada
        $sqlCheck = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        $stmtCheck = mysqli_prepare($this->db, $sqlCheck);
        mysqli_stmt_bind_param($stmtCheck, "s", $usuario);
        mysqli_stmt_execute($stmtCheck);
        $resultado = mysqli_stmt_get_result($stmtCheck);
        
        if(!mysqli_fetch_assoc($resultado)){
            mysqli_free_result($resultado);
            mysqli_stmt_close($stmtCheck);

            // Insertar con sentencia preparada
            $sql = "INSERT INTO usuarios (nombre_usuario, email, nombre, apellidos, password, rol, avatar) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param($stmt, "sssssss", $usuario, $email, $nombre, $apellidos, $password, $rol, $avatar);
            
            if(mysqli_stmt_execute($stmt)){
                $id = mysqli_insert_id($this->db);
                mysqli_stmt_close($stmt);
                return new Usuario($id, $usuario, $nombre, $apellidos, $email, $rol, $avatar); 
            }
            else{
                mysqli_stmt_close($stmt);
                throw new ErrorAlInsertarBBDDException('No se ha podido crear al usuario ' . $usuario . ".");    
            }
        }
        else{
            mysqli_free_result($resultado);
            mysqli_stmt_close($stmtCheck);
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
                mysqli_stmt_close($stmt);
                throw new CampoInexistenteException('El campo ' . ucfirst($campo) . ' no existe en la clase Usuario');
                break;
        }

        mysqli_stmt_close($stmt);
        return $ret;
    }

    public function obtenerImagen($id){
        $query = "SELECT avatar FROM usuarios WHERE id = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $resultado = mysqli_stmt_get_result($stmt);
            if ($fila = mysqli_fetch_assoc($resultado)) {
                mysqli_free_result($resultado);
                mysqli_stmt_close($stmt);
                return $fila['avatar'];
            }
            else{
                mysqli_free_result($resultado);
                mysqli_stmt_close($stmt);
                throw new UsuarioNoExisteException('El usuario con ID ' . $id . ' no existe');
            }
        }
        else{
            mysqli_stmt_close($stmt);
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
                mysqli_free_result($resultado);
                mysqli_stmt_close($stmt);
                return false;
            }
            mysqli_free_result($resultado);
            mysqli_stmt_close($stmt);
            return true;
        }
        mysqli_stmt_close($stmt);
        return false;
    }

    public function validarUserMail($usuario, $email){

        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ? OR email = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $usuario, $email);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if ($resultado && mysqli_num_rows($resultado) === 0) {
            mysqli_free_result($resultado);
            mysqli_stmt_close($stmt);
            return true;
        }
        else{
            $fila = mysqli_fetch_assoc($resultado);
            mysqli_free_result($resultado);
            mysqli_stmt_close($stmt);
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