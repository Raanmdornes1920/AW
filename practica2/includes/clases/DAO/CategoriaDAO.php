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
            $categorias[] = new Categoria(
                $row['id'], 
                $row['nombre'], 
                $row['descripcion'], 
                $row['imagen'], 
                $row['activa']
            );
        }
        return $categorias;
    }

    public function obtenerPorId($id) {
        $id = mysqli_real_escape_string($this->db, $id);
        $query = "SELECT * FROM categorias WHERE id = '$id'";
        $res = mysqli_query($this->db, $query);
        if ($row = mysqli_fetch_assoc($res)) {
            return new Categoria($row['id'], $row['nombre'], $row['descripcion'], $row['imagen'], $row['activa']);
        }
        return null;
    }

    public function guardar(Categoria $c) {
        $id = $c->getId();
        $nombre = mysqli_real_escape_string($this->db, $c->getNombre());
        $desc = mysqli_real_escape_string($this->db, $c->getDescripcion());
        $img = mysqli_real_escape_string($this->db, $c->getImagen());
        $activa = $c->getActiva();

        if ($id) {
            $query = "UPDATE categorias SET nombre='$nombre', descripcion='$desc', imagen='$img', activa='$activa' WHERE id='$id'";
        } else {
            $query = "INSERT INTO categorias (nombre, descripcion, imagen, activa) VALUES ('$nombre', '$desc', '$img', '$activa')";
        }
        return mysqli_query($this->db, $query);
    }
}