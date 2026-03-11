<?php

class ProductoDAO {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function listarTodos() {
        // Obtenemos los productos y el nombre de su categoría
        $sql = "SELECT p.*, c.nombre as cat_nombre 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id 
                ORDER BY p.nombre ASC";
        $res = mysqli_query($this->db, $sql);
        $productos = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $imagen = isset($row['imagen']) && !empty($row['imagen']) ? $row['imagen'] : 'default.png';
            $productos[] = new Producto($row['id'], $row['id_categoria'], $row['nombre'], $row['descripcion'], 
                                        $row['precio_base'], $row['iva'], $row['disponible'], $row['ofertado'], 
                                        $row['cat_nombre'], $imagen);
        }
        return $productos;
    }

    public function obtenerPorId($id) {
        $sql = "SELECT p.*, c.nombre as cat_nombre FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id WHERE p.id = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) {
            $imagen = isset($row['imagen']) && !empty($row['imagen']) ? $row['imagen'] : 'default.png';
            return new Producto($row['id'], $row['id_categoria'], $row['nombre'], $row['descripcion'], 
                                $row['precio_base'], $row['iva'], $row['disponible'], $row['ofertado'], 
                                $row['cat_nombre'], $imagen);
        }
        return null;
    }

    public function guardar($p) {
        if ($p->getId()) {
            $sql = "UPDATE productos SET nombre=?, descripcion=?, id_categoria=?, precio_base=?, iva=?, disponible=?, ofertado=? WHERE id=?";
            $stmt = mysqli_prepare($this->db, $sql);
            $id_cat = $p->getIdCategoria(); $nom = $p->getNombre(); $desc = $p->getDescripcion();
            $pb = $p->getPrecioBase(); $iva = $p->getIva(); $disp = $p->getDisponible(); 
            $ofert = $p->getOfertado(); $id = $p->getId();
            mysqli_stmt_bind_param($stmt, "ssiddiii", $nom, $desc, $id_cat, $pb, $iva, $disp, $ofert, $id);
        } else {
            $sql = "INSERT INTO productos (nombre, descripcion, id_categoria, precio_base, iva, disponible, ofertado) VALUES (?, ?, ?, ?, ?, ?, 1)";
            $stmt = mysqli_prepare($this->db, $sql);
            $id_cat = $p->getIdCategoria(); $nom = $p->getNombre(); $desc = $p->getDescripcion();
            $pb = $p->getPrecioBase(); $iva = $p->getIva(); $disp = $p->getDisponible();
            mysqli_stmt_bind_param($stmt, "ssiddi", $nom, $desc, $id_cat, $pb, $iva, $disp);
        }
        return mysqli_stmt_execute($stmt);
    }
}