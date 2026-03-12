<?php

class ProductoDAO {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function listarTodos() {
        $sql = "SELECT p.*, c.nombre as cat_nombre, i.ruta_imagen 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id 
                LEFT JOIN productos_imagenes i ON p.id = i.id_producto
                GROUP BY p.id
                ORDER BY p.nombre ASC";
                
        $res = mysqli_query($this->db, $sql);
        $productos = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $imagen = !empty($row['ruta_imagen']) ? $row['ruta_imagen'] : 'default.png';
            $productos[] = new Producto(
                $row['id'], $row['id_categoria'], $row['nombre'], $row['descripcion'], 
                $row['precio_base'], $row['iva'], $row['disponible'], $row['ofertado'], 
                $row['cat_nombre'], $imagen
            );
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
            $sql = "UPDATE productos SET id_categoria=?, nombre=?, descripcion=?, precio_base=?, iva=?, disponible=?, ofertado=? WHERE id=?";
            $stmt = mysqli_prepare($this->db, $sql);
            $id_cat = $p->getIdCategoria(); $nom = $p->getNombre(); $desc = $p->getDescripcion();
            $pb = $p->getPrecioBase(); $iva = $p->getIva(); $disp = $p->getDisponible(); 
            $ofert = $p->getOfertado(); $id = $p->getId();
            mysqli_stmt_bind_param($stmt, "issidiii", $id_cat, $nom, $desc, $pb, $iva, $disp, $ofert, $id);
            $result = mysqli_stmt_execute($stmt);
        } else {
            $sql = "INSERT INTO productos (id_categoria, nombre, descripcion, precio_base, iva, disponible, ofertado) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($this->db, $sql);
            $id_cat = $p->getIdCategoria(); $nom = $p->getNombre(); $desc = $p->getDescripcion();
            $pb = $p->getPrecioBase(); $iva = $p->getIva(); $disp = $p->getDisponible(); $ofert = $p->getOfertado();
            mysqli_stmt_bind_param($stmt, "issidii", $id_cat, $nom, $desc, $pb, $iva, $disp, $ofert);
            $result = mysqli_stmt_execute($stmt);
            $id = mysqli_insert_id($this->db);
        }
    
        if ($result && $p->getImagen() !== 'default.png') {
            $imgSql = "INSERT INTO productos_imagenes (id_producto, ruta_imagen) VALUES (?, ?)";
            $imgStmt = mysqli_prepare($this->db, $imgSql);
            $ruta = $p->getImagen();
            mysqli_stmt_bind_param($imgStmt, "is", $id, $ruta);
            mysqli_stmt_execute($imgStmt);
        }
        return $result;
    }
}