<?php
class PedidoSA {
    private $dao;
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
        $this->dao = new PedidoDAO($db_connection); 
    }

    /**
     * $carrito debe ser un array con el id_producto como clave y la cantidad como valor:
     * Ejemplo: [ 5 => 2, 9 => 1 ] (Dos ensaladas césar y una carbonara)
     */
    public function procesarCompra($id_usuario, $tipo, $carrito) {
        if (empty($carrito)) {
            return false;
        }

        $total = 0;
        $lineas_procesadas = [];
        $prodDAO = new ProductoDAO($this->db);

        // Validamos productos y recalculamos total real en backend
        foreach ($carrito as $id_producto => $cantidad) {
            $producto = $prodDAO->obtenerPorId($id_producto);
            
            if ($producto && $producto->getDisponible() == 1) {
                $precio_final = $producto->getPrecioFinal(); // Precio con IVA
                $total += ($precio_final * $cantidad);
                
                $lineas_procesadas[] = [
                    'id_producto' => $id_producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio_final
                ];
            }
        }

        if (empty($lineas_procesadas)) return false;

        // El estado inicial al guardarse es 'recibido' según el enunciado
        $pedido = new Pedido(null, null, $id_usuario, null, 'recibido', $tipo, $total);
        
        return $this->dao->guardar($pedido, $lineas_procesadas);
    }

    public function obtenerPedidosCliente($id_usuario) {
        return $this->dao->listarPorUsuario($id_usuario);
    }

    public function obtenerPedidosCamarero() {
        // Camarero cobra (recibido -> en_preparacion) y entrega (listo_cocina -> terminado -> entregado)
        return $this->dao->listarPorEstado(['recibido', 'listo_cocina', 'terminado']);
    }

    public function obtenerPedidosCocinero() {
        // Cocinero prepara (en_preparacion -> cocinando -> listo_cocina)
        return $this->dao->listarPorEstado(['en_preparacion', 'cocinando']);
    }

    public function cambiarEstadoPedido($id_pedido, $nuevo_estado) {
        $estados_validos = ['recibido', 'en_preparacion', 'cocinando', 'listo_cocina', 'terminado', 'entregado', 'cancelado'];
        if (in_array($nuevo_estado, $estados_validos)) {
            return $this->dao->actualizarEstado($id_pedido, $nuevo_estado);
        }
        return false;
    }

    public function obtenerPorId($id) {
        return $this->dao->obtenerPorId($id);
    }

    public function obtenerDetallesPedido($id_pedido) {
        return $this->dao->obtenerLineasPedido($id_pedido);
    }

    public function marcarProductoComoPreparado($id_linea) {
        return $this->dao->marcarLineaPreparada($id_linea);
    }

    public function sePuedeFinalizarPedido($id_pedido) {
        return $this->dao->estanTodasLineasPreparadas($id_pedido);
    }

    public function obtenerPedidosPendientes() {
        return $this->dao->listarPorEstado(['recibido', 'en_preparacion', 'cocinando', 'listo_cocina', 'terminado']);
    }
}