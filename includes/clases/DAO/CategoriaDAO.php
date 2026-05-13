<?php

class CategoriaDAO {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function listarTodas() {
        $query = "SELECT * FROM categorias ORDER BY nombre ASC";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        $categorias = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $imagen = isset($row['imagen']) && !empty($row['imagen']) ? $row['imagen'] : 'categoria_default.jpg';
            $categorias[] = new Categoria(
                $row['id'], 
                $row['nombre'], 
                $row['descripcion'], 
                $imagen,
                $row['activa'] ?? 1
            );
        }

        mysqli_free_result($res);
        mysqli_stmt_close($stmt);
        return $categorias;
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM categorias WHERE id = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        
        $categoria = null;
        if ($row = mysqli_fetch_assoc($res)) {
            $imagen = !empty($row['imagen']) ? $row['imagen'] : 'categoria_default.jpg';
            $categoria = new Categoria($row['id'], $row['nombre'], $row['descripcion'], $imagen, $row['activa'] ?? 1);
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
    }

    public function guardar(Categoria $c) {
        $id = $c->getId();
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
    }
}
