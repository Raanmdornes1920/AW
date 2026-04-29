<?php
class PedidoSA {
    private $dao;
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
<<<<<<< HEAD
        $this->dao = new PedidoDAO($db_connection); 
    }

    /**
     * $carrito debe ser un array con el id_producto como clave y la cantidad como valor:
     * Ejemplo: [ 5 => 2, 9 => 1 ] (Dos ensaladas césar y una carbonara)
     */
    /**
     * $carrito debe ser un array con el id_producto como clave y la cantidad como valor
     */
    public function procesarCompra($id_usuario, $tipo, $carrito, $metodo_pago = 'tarjeta') {
=======
        $this->dao = new PedidoDAO($db_connection);
    }

    /**
     * $carrito debe ser un array con el id_producto como clave y la cantidad como valor.
     */
    public function procesarCompra($id_usuario, $tipo, $carrito, $metodo_pago = 'tarjeta', $ids_ofertas = []) {
>>>>>>> angela
        if (empty($carrito) || !is_array($carrito)) {
            return false;
        }

<<<<<<< HEAD
        // Saneamiento de entrada
        $id_usuario = filter_var($id_usuario, FILTER_SANITIZE_NUMBER_INT);
        $tipo = htmlspecialchars(strip_tags($tipo)); 
        $metodo_pago = htmlspecialchars(strip_tags($metodo_pago));

        $total = 0;
=======
        $id_usuario = filter_var($id_usuario, FILTER_SANITIZE_NUMBER_INT);
        $tipo = htmlspecialchars(strip_tags($tipo));
        $metodo_pago = htmlspecialchars(strip_tags($metodo_pago));
        $ids_ofertas = is_array($ids_ofertas) ? $ids_ofertas : [];

        if (!in_array($tipo, ['local', 'llevar'], true)) {
            $tipo = 'local';
        }

>>>>>>> angela
        $lineas_procesadas = [];
        $prodDAO = new ProductoDAO($this->db);

        foreach ($carrito as $id_producto => $cantidad) {
            $id_producto_limpio = filter_var($id_producto, FILTER_SANITIZE_NUMBER_INT);
            $cantidad_limpia = filter_var($cantidad, FILTER_SANITIZE_NUMBER_INT);

<<<<<<< HEAD
            $producto = $prodDAO->obtenerPorId($id_producto_limpio);
            
            if ($producto && $producto->getDisponible() == 1) {
                $precio_final = $producto->getPrecioFinal(); 
                $total += ($precio_final * $cantidad_limpia);
                
=======
            if ($id_producto_limpio <= 0 || $cantidad_limpia <= 0) {
                continue;
            }

            $producto = $prodDAO->obtenerPorId($id_producto_limpio);

            if ($producto && $producto->getDisponible() == 1) {
                $precio_final = $producto->getPrecioFinal();
>>>>>>> angela
                $lineas_procesadas[] = [
                    'id_producto' => $id_producto_limpio,
                    'cantidad' => $cantidad_limpia,
                    'precio_unitario' => $precio_final
                ];
            }
        }

<<<<<<< HEAD
        if (empty($lineas_procesadas)) return false;

        // Si paga al camarero/efectivo, se queda en 'recibido'.
        // Si paga con tarjeta, salta directamente a la cocina ('en_preparacion').
        $estado_inicial = ($metodo_pago === 'camarero' || $metodo_pago === 'efectivo') ? 'recibido' : 'en_preparacion';

        $pedido = new Pedido(null, null, $id_usuario, null, $estado_inicial, $tipo, $total);
        
        return $this->dao->guardar($pedido, $lineas_procesadas);
    }

    public function obtenerPedidosCliente($id_usuario) {
        // Saneamiento de entrada
=======
        if (empty($lineas_procesadas)) {
            return false;
        }

        $ofertaSA = new OfertaSA($this->db);
        $resumenOfertas = $ofertaSA->aplicarOfertasACarrito($carrito, $ids_ofertas);

        $total_sin_descuento = $resumenOfertas['total_sin_descuento'];
        $descuento_aplicado = $resumenOfertas['descuento_total'];
        $total = $resumenOfertas['total_final'];

        // Si paga al camarero/efectivo, se queda en 'recibido'.
        // Si paga con tarjeta, salta directamente a cocina.
        $estado_inicial = ($metodo_pago === 'camarero' || $metodo_pago === 'efectivo') ? 'recibido' : 'en_preparacion';

        $pedido = new Pedido(
            null,
            null,
            $id_usuario,
            null,
            $estado_inicial,
            $tipo,
            $total,
            $total_sin_descuento,
            $descuento_aplicado
        );

        return $this->dao->guardar($pedido, $lineas_procesadas, $resumenOfertas['ofertas_aplicadas']);
    }

    public function obtenerPedidosCliente($id_usuario) {
>>>>>>> angela
        $id_usuario = filter_var($id_usuario, FILTER_SANITIZE_NUMBER_INT);
        return $this->dao->listarPorUsuario($id_usuario);
    }

    public function obtenerPedidosCamarero() {
<<<<<<< HEAD
        // Camarero cobra (recibido -> en_preparacion) y entrega (listo_cocina -> terminado -> entregado)
=======
>>>>>>> angela
        return $this->dao->listarPorEstado(['recibido', 'listo_cocina', 'terminado']);
    }

    public function obtenerPedidosCocinero() {
<<<<<<< HEAD
        // Cocinero prepara (en_preparacion -> cocinando -> listo_cocina)
=======
>>>>>>> angela
        return $this->dao->listarPorEstado(['en_preparacion', 'cocinando']);
    }

    public function cambiarEstadoPedido($id_pedido, $nuevo_estado) {
<<<<<<< HEAD
        // Saneamiento de entrada
=======
>>>>>>> angela
        $id_pedido = filter_var($id_pedido, FILTER_SANITIZE_NUMBER_INT);
        $nuevo_estado = htmlspecialchars(strip_tags($nuevo_estado));

        $estados_validos = ['recibido', 'en_preparacion', 'cocinando', 'listo_cocina', 'terminado', 'entregado', 'cancelado'];
<<<<<<< HEAD
        if (in_array($nuevo_estado, $estados_validos)) {
=======
        if (in_array($nuevo_estado, $estados_validos, true)) {
>>>>>>> angela
            return $this->dao->actualizarEstado($id_pedido, $nuevo_estado);
        }
        return false;
    }

    public function obtenerPorId($id) {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        return $this->dao->obtenerPorId($id);
    }

    public function obtenerDetallesPedido($id_pedido) {
        $id_pedido = filter_var($id_pedido, FILTER_SANITIZE_NUMBER_INT);
        return $this->dao->obtenerLineasPedido($id_pedido);
    }

<<<<<<< HEAD
=======
    public function obtenerOfertasPedido($id_pedido) {
        $id_pedido = filter_var($id_pedido, FILTER_SANITIZE_NUMBER_INT);
        return $this->dao->obtenerOfertasPedido($id_pedido);
    }

>>>>>>> angela
    public function marcarProductoComoPreparado($id_linea) {
        $id_linea = filter_var($id_linea, FILTER_SANITIZE_NUMBER_INT);
        return $this->dao->marcarLineaPreparada($id_linea);
    }

    public function sePuedeFinalizarPedido($id_pedido) {
        $id_pedido = filter_var($id_pedido, FILTER_SANITIZE_NUMBER_INT);
        return $this->dao->estanTodasLineasPreparadas($id_pedido);
    }

    public function obtenerPedidosPendientes() {
        return $this->dao->listarPorEstado(['recibido', 'en_preparacion', 'cocinando', 'listo_cocina', 'terminado']);
    }
}
<<<<<<< HEAD
?>
=======
?>
>>>>>>> angela
