<?php
class OfertaDAO {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    /**
     * Lista todas las ofertas con sus productos asociados
     */
    public function listarTodas() {
        $sql = "SELECT * FROM ofertas ORDER BY fecha_fin DESC";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_execute($stmt);
        $rs = mysqli_stmt_get_result($stmt);
        $ofertas = [];
        while ($fila = mysqli_fetch_assoc($rs)) {
            $ofertas[] = $this->crearOfertaConProductos($fila);
        }
        mysqli_free_result($rs);
        mysqli_stmt_close($stmt);
        return $ofertas;
    }

    /**
     * Obtiene una oferta por su ID con sus productos asociados
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM ofertas WHERE id = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        $oferta = null;
        if ($fila = mysqli_fetch_assoc($res)) {
            $oferta = $this->crearOfertaConProductos($fila);
        }

        mysqli_free_result($res);
        mysqli_stmt_close($stmt);
        return $oferta;
    }

    /**
     * Guarda (INSERT o UPDATE) una oferta y sus productos asociados
     */
    public function guardar($oferta) {
        mysqli_begin_transaction($this->db);
        try {
            if ($oferta->getId()) {
                // UPDATE
                $sql = "UPDATE ofertas SET nombre = ?, descripcion = ?, fecha_inicio = ?, fecha_fin = ?, descuento_porcentaje = ? WHERE id = ?";
                $stmt = mysqli_prepare($this->db, $sql);
                $nombre = $oferta->getNombre();
                $desc = $oferta->getDescripcion();
                $fi = $oferta->getFechaInicio();
                $ff = $oferta->getFechaFin();
                $descuento = $oferta->getDescuento();
                $id = $oferta->getId();
                mysqli_stmt_bind_param($stmt, "ssssdi", $nombre, $desc, $fi, $ff, $descuento, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $idOferta = $oferta->getId();

                // Borramos los productos antiguos asociados
                $delStmt = mysqli_prepare($this->db, "DELETE FROM oferta_productos WHERE id_oferta = ?");
                mysqli_stmt_bind_param($delStmt, "i", $idOferta);
                mysqli_stmt_execute($delStmt);
                mysqli_stmt_close($delStmt);
            } else {
                // INSERT
                $sql = "INSERT INTO ofertas (nombre, descripcion, fecha_inicio, fecha_fin, descuento_porcentaje) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($this->db, $sql);
                $nombre = $oferta->getNombre();
                $desc = $oferta->getDescripcion();
                $fi = $oferta->getFechaInicio();
                $ff = $oferta->getFechaFin();
                $descuento = $oferta->getDescuento();
                mysqli_stmt_bind_param($stmt, "ssssd", $nombre, $desc, $fi, $ff, $descuento);
                mysqli_stmt_execute($stmt);
                $idOferta = mysqli_insert_id($this->db);
                mysqli_stmt_close($stmt);
            }

            // Insertamos los productos de la oferta
            $productos = $oferta->getProductos();
            if (!empty($productos)) {
                $stmtP = mysqli_prepare($this->db, "INSERT INTO oferta_productos (id_oferta, id_producto, cantidad) VALUES (?, ?, ?)");
                foreach ($productos as $p) {
                    $idProd = $p['id_producto'];
                    $cant = $p['cantidad'];
                    mysqli_stmt_bind_param($stmtP, "iii", $idOferta, $idProd, $cant);
                    mysqli_stmt_execute($stmtP);
                }
                mysqli_stmt_close($stmtP);
            }

            mysqli_commit($this->db);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($this->db);
            return false;
        }
    }

    /**
     * Borra una oferta por su ID (los productos se borran por CASCADE)
     */
    public function borrar($id) {
        $stmt = mysqli_prepare($this->db, "DELETE FROM ofertas WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        $exito = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $exito;
    }

    /**
     * Crea un objeto Oferta a partir de una fila de BD, incluyendo sus productos
     */
    private function crearOfertaConProductos($fila) {
        $idOferta = $fila['id'];

        // Obtenemos los productos asociados con nombre y precio
        $sql = "SELECT op.id_producto, op.cantidad, p.nombre, p.precio_base, p.iva 
                FROM oferta_productos op 
                JOIN productos p ON op.id_producto = p.id 
                WHERE op.id_oferta = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idOferta);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        $productos = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $precioConIva = round($row['precio_base'] * (1 + ($row['iva'] / 100)), 2);
            $productos[] = [
                'id_producto' => $row['id_producto'],
                'cantidad' => $row['cantidad'],
                'nombre' => $row['nombre'],
                'precio_con_iva' => $precioConIva
            ];
        }
        mysqli_free_result($res);
        mysqli_stmt_close($stmt);

        return new Oferta(
            $fila['id'],
            $fila['nombre'],
            $fila['descripcion'],
            $fila['fecha_inicio'],
            $fila['fecha_fin'],
            $fila['descuento_porcentaje'],
            $productos
        );
    }
}
