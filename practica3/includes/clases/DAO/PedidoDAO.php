<?php
class PedidoDAO {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    private function obtenerSiguienteNumeroDia() {
        // Busca el pedido más alto de hoy
        $sql = "SELECT MAX(numero_pedido) as max_num FROM pedidos WHERE DATE(fecha) = CURDATE()";
        $res = mysqli_query($this->db, $sql);
        $row = mysqli_fetch_assoc($res);
        $numero = ($row['max_num'] !== null) ? $row['max_num'] + 1 : 1;
        
        mysqli_free_result($res); // ¡Liberar recurso añadido!
        return $numero;
    }

    public function guardar($pedido, $lineas_carrito) {
        // Iniciamos transacción para asegurar que todo se guarda o no se guarda nada
        mysqli_begin_transaction($this->db);
        try {
            $numero_pedido = $this->obtenerSiguienteNumeroDia();
            
            // 1. Insertar en tabla pedidos
            $sql = "INSERT INTO pedidos (numero_pedido, id_usuario, estado, tipo, total) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($this->db, $sql);
            
            $id_user = $pedido->getIdUsuario();
            $estado = $pedido->getEstado();
            $tipo = $pedido->getTipo();
            $total = $pedido->getTotal();
            
            mysqli_stmt_bind_param($stmt, "iissd", $numero_pedido, $id_user, $estado, $tipo, $total);
            mysqli_stmt_execute($stmt);
            $id_pedido = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt); // ¡Liberar recurso añadido!

            // 2. Insertar en tabla lineas_pedido
            $sql_linea = "INSERT INTO lineas_pedido (id_pedido, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
            $stmt_linea = mysqli_prepare($this->db, $sql_linea);

            foreach ($lineas_carrito as $linea) {
                $id_prod = $linea['id_producto'];
                $cant = $linea['cantidad'];
                $precio = $linea['precio_unitario'];
                mysqli_stmt_bind_param($stmt_linea, "iiid", $id_pedido, $id_prod, $cant, $precio);
                mysqli_stmt_execute($stmt_linea);
            }
            mysqli_stmt_close($stmt_linea); // ¡Liberar recurso añadido!

            // Si todo va bien, confirmamos los cambios
            mysqli_commit($this->db);
            return $id_pedido;
            
        } catch (Exception $e) {
            // Si hay un error, deshacemos todo
            mysqli_rollback($this->db);
            return false;
        }
    }

    public function listarPorUsuario($id_usuario) {
        $sql = "SELECT * FROM pedidos WHERE id_usuario = ? ORDER BY fecha DESC";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        
        $pedidos = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $pedidos[] = new Pedido($row['id'], $row['numero_pedido'], $row['id_usuario'], 
                                    $row['fecha'], $row['estado'], $row['tipo'], $row['total']);
        }
        
        mysqli_free_result($res); // ¡Liberar recurso añadido!
        mysqli_stmt_close($stmt); // ¡Liberar recurso añadido!
        return $pedidos;
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM pedidos WHERE id = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        
        $pedido = null;
        if ($row = mysqli_fetch_assoc($res)) {
            $pedido = new Pedido($row['id'], $row['numero_pedido'], $row['id_usuario'], 
                              $row['fecha'], $row['estado'], $row['tipo'], $row['total']);
        }
        
        mysqli_free_result($res); // ¡Liberar recurso añadido!
        mysqli_stmt_close($stmt); // ¡Liberar recurso añadido!
        return $pedido;
    }

    public function listarPorEstado($estados) {
        // Recibe un array de estados y devuelve los pedidos coincidentes
        $in = str_repeat('?,', count($estados) - 1) . '?';
        $types = str_repeat('s', count($estados));
        
        $sql = "SELECT * FROM pedidos WHERE estado IN ($in) ORDER BY fecha ASC";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$estados);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        
        $pedidos = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $pedidos[] = new Pedido($row['id'], $row['numero_pedido'], $row['id_usuario'], 
                                    $row['fecha'], $row['estado'], $row['tipo'], $row['total']);
        }
        
        mysqli_free_result($res); // ¡Liberar recurso añadido!
        mysqli_stmt_close($stmt); // ¡Liberar recurso añadido!
        return $pedidos;
    }

    public function actualizarEstado($id_pedido, $nuevo_estado) {
        $sql = "UPDATE pedidos SET estado = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "si", $nuevo_estado, $id_pedido);
        $exito = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt); // ¡Liberar recurso añadido!
        return $exito;
    }

    public function obtenerLineasPedido($id_pedido) {
        // Añadimos p.cocinable a la consulta
        $sql = "SELECT lp.*, p.nombre, p.cocinable FROM lineas_pedido lp 
                JOIN productos p ON lp.id_producto = p.id 
                WHERE lp.id_pedido = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_pedido);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        
        $lineas = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $lineas[] = $row;
        }
        
        mysqli_free_result($res);
        mysqli_stmt_close($stmt);
        return $lineas;
    }

    public function marcarLineaPreparada($id_linea) {
        $sql = "UPDATE lineas_pedido SET preparado = 1 WHERE id = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_linea);
        $exito = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        return $exito;
    }

    public function estanTodasLineasPreparadas($id_pedido) {
        // FILTRADO CLAVE: Solo miramos productos pendientes (preparado=0) que sean cocinables (cocinable=1)
        $sql = "SELECT COUNT(*) as pendientes 
                FROM lineas_pedido lp
                JOIN productos p ON lp.id_producto = p.id
                WHERE lp.id_pedido = ? AND lp.preparado = 0 AND p.cocinable = 1";
        
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_pedido);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        
        $terminado = ($row['pendientes'] == 0);
        
        mysqli_free_result($res);
        mysqli_stmt_close($stmt);
        return $terminado;
    }
}
?>