<?php

class CategoriaDAO {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function listarTodas() {
        $query = "SELECT * FROM categorias ORDER BY nombre ASC";
        $res = mysqli_query($this->db, $query);
        $categorias = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $imagen = isset($row['imagen']) && !empty($row['imagen']) ? $row['imagen'] : 'categoria_default.png';
            $categorias[] = new Categoria(
                $row['id'], 
                $row['nombre'], 
                $row['descripcion'], 
                $imagen
            );
        }
<<<<<<< HEAD
=======
        mysqli_free_result($res);
>>>>>>> angela
        return $categorias;
    }

    public function obtenerPorId($id) {
<<<<<<< HEAD
        $id = mysqli_real_escape_string($this->db, $id);
        $query = "SELECT * FROM categorias WHERE id = '$id'";
        $res = mysqli_query($this->db, $query);
        if ($row = mysqli_fetch_assoc($res)) {
            return new Categoria($row['id'], $row['nombre'], $row['descripcion'], $row['imagen']);
        }
        return null;
    }

  public function contarProductosAsociados($id) {
        $id = mysqli_real_escape_string($this->db, $id);
        $query = "SELECT COUNT(*) AS total FROM productos WHERE id_categoria = '$id'";
        $res = mysqli_query($this->db, $query);

        if ($res && $row = mysqli_fetch_assoc($res)) {
            return (int)$row['total'];
        }
        return 0;
    }

    public function borrar($id) {
        $id = mysqli_real_escape_string($this->db, $id);
        $query = "DELETE FROM categorias WHERE id = '$id'";
        return mysqli_query($this->db, $query);
=======
        $sql = "SELECT * FROM categorias WHERE id = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        
        $categoria = null;
        if ($row = mysqli_fetch_assoc($res)) {
            $categoria = new Categoria($row['id'], $row['nombre'], $row['descripcion'], $row['imagen']);
        }
        
        mysqli_free_result($res);
        mysqli_stmt_close($stmt);
        return $categoria;
    }

    public function contarProductosAsociados($id) {
        $sql = "SELECT COUNT(*) AS total FROM productos WHERE id_categoria = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        $total = 0;
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $total = (int)$row['total'];
        }
        
        mysqli_free_result($res);
        mysqli_stmt_close($stmt);
        return $total;
    }

    public function borrar($id) {
        $sql = "DELETE FROM categorias WHERE id = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
>>>>>>> angela
    }

    public function guardar(Categoria $c) {
        $id = $c->getId();
<<<<<<< HEAD
        $nombre = mysqli_real_escape_string($this->db, $c->getNombre());
        $desc = mysqli_real_escape_string($this->db, $c->getDescripcion());
        $img = mysqli_real_escape_string($this->db, $c->getImagen());

        if ($id) {
            $query = "UPDATE categorias SET nombre='$nombre', descripcion='$desc', imagen='$img' WHERE id='$id'";
        } else {
            $query = "INSERT INTO categorias (nombre, descripcion, imagen) VALUES ('$nombre', '$desc', '$img')";
        }
        return mysqli_query($this->db, $query);
=======
        $nombre = $c->getNombre();
        $desc = $c->getDescripcion();
        $img = $c->getImagen();

        if ($id) {
            $sql = "UPDATE categorias SET nombre=?, descripcion=?, imagen=? WHERE id=?";
            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param($stmt, "sssi", $nombre, $desc, $img, $id);
        } else {
            $sql = "INSERT INTO categorias (nombre, descripcion, imagen) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($this->db, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $nombre, $desc, $img);
        }
        
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
>>>>>>> angela
    }
}