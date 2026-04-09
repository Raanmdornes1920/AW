<?php

class ProductoDAO {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    public function listarTodos() {
        $sql = "SELECT p.*, c.nombre as cat_nombre, 
                (SELECT ruta_imagen FROM productos_imagenes WHERE id_producto = p.id LIMIT 1) as ruta_imagen 
                FROM productos p 
                LEFT JOIN categorias c ON p.id_categoria = c.id 
                ORDER BY p.nombre ASC";
                
        $res = mysqli_query($this->db, $sql);
        $productos = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $imagen = !empty($row['ruta_imagen']) ? $row['ruta_imagen'] : 'default.png';
            
            // NUEVO: Sacamos el campo cocinable de la base de datos (con valor por defecto 1 por si acaso)
            $cocinable = isset($row['cocinable']) ? $row['cocinable'] : 1;
            
            // NUEVO ORDEN: Pasamos $cocinable en la 9º posición, cat_nombre en la 10º y las fotos en la 11º.
            $productos[] = new Producto(
                $row['id'], $row['id_categoria'], $row['nombre'], $row['descripcion'], 
                $row['precio_base'], $row['iva'], $row['disponible'], $row['ofertado'], 
                $cocinable, $row['cat_nombre'], [$imagen]
            );
        }
        mysqli_free_result($res); // Buena práctica del profesor ;)
        return $productos;
    }

    public function obtenerPorId($id) {
        $sql = "SELECT p.*, c.nombre as cat_nombre FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id WHERE p.id = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($res)) {
            // Buscamos todas las imágenes de este producto
            $imgSql = "SELECT ruta_imagen FROM productos_imagenes WHERE id_producto = ? ORDER BY orden";
            $imgStmt = mysqli_prepare($this->db, $imgSql);
            mysqli_stmt_bind_param($imgStmt, "i", $id);
            mysqli_stmt_execute($imgStmt);
            $imgRes = mysqli_stmt_get_result($imgStmt);
            $imagenes = [];
            while($imgRow = mysqli_fetch_assoc($imgRes)) {
                $imagenes[] = $imgRow['ruta_imagen'];
            }
            
            $cocinable = isset($row['cocinable']) ? $row['cocinable'] : 1;

            return new Producto($row['id'], $row['id_categoria'], $row['nombre'], $row['descripcion'], 
                                $row['precio_base'], $row['iva'], $row['disponible'], $row['ofertado'], 
                                $cocinable, $row['cat_nombre'], $imagenes);
        }
        return null;
    }

    public function guardar($p) {
        if ($p->getId()) {
            // Añadido cocinable=? al UPDATE
            $sql = "UPDATE productos SET id_categoria=?, nombre=?, descripcion=?, precio_base=?, iva=?, disponible=?, ofertado=?, cocinable=? WHERE id=?";
            $stmt = mysqli_prepare($this->db, $sql);
            
            $id_cat = $p->getIdCategoria(); $nom = $p->getNombre(); $desc = $p->getDescripcion();
            $pb = $p->getPrecioBase(); $iva = $p->getIva(); $disp = $p->getDisponible(); 
            $ofert = $p->getOfertado(); $cocinable = $p->getCocinable(); $id = $p->getId();
            
            // "issidiiii" -> Se añade una "i" por el cocinable (entero 1 o 0)
            mysqli_stmt_bind_param($stmt, "issidiiii", $id_cat, $nom, $desc, $pb, $iva, $disp, $ofert, $cocinable, $id);
            $result = mysqli_stmt_execute($stmt);
            $id_producto = $id;
        } else {
            // Añadido cocinable al INSERT
            $sql = "INSERT INTO productos (id_categoria, nombre, descripcion, precio_base, iva, disponible, ofertado, cocinable) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($this->db, $sql);
            
            $id_cat = $p->getIdCategoria(); $nom = $p->getNombre(); $desc = $p->getDescripcion();
            $pb = $p->getPrecioBase(); $iva = $p->getIva(); $disp = $p->getDisponible(); 
            $ofert = $p->getOfertado(); $cocinable = $p->getCocinable();
            
            mysqli_stmt_bind_param($stmt, "issidiii", $id_cat, $nom, $desc, $pb, $iva, $disp, $ofert, $cocinable);
            $result = mysqli_stmt_execute($stmt);
            $id_producto = mysqli_insert_id($this->db);
        }
    
        // Si hay imágenes nuevas, se añaden a la tabla
        if ($result && !empty($p->getImagenesArray())) {
            foreach ($p->getImagenesArray() as $orden => $ruta) {
                $orden += 1;
                if($ruta !== 'default.png') {
                    $imgSql = "INSERT INTO productos_imagenes (id_producto, ruta_imagen, orden) VALUES (?, ?, ?)";
                    $imgStmt = mysqli_prepare($this->db, $imgSql);
                    mysqli_stmt_bind_param($imgStmt, "isi", $id_producto, $ruta, $orden);
                    mysqli_stmt_execute($imgStmt);
                }
            }
        }
        return $result;
    }
}
?>