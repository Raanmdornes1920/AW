<?php
class Pedido {
    private $id;
    private $numero_pedido;
    private $id_usuario;
    private $fecha;
    private $estado;
    private $tipo; // 'local' o 'llevar'
    private $total;
    private $total_sin_descuento;
    private $descuento_aplicado;
    private $lineas = []; // Array para guardar los productos del pedido

    public function __construct($id, $numero_pedido, $id_usuario, $fecha, $estado, $tipo, $total, $total_sin_descuento = null, $descuento_aplicado = 0, $lineas = []) {
        $this->id = $id;
        $this->numero_pedido = $numero_pedido;
        $this->id_usuario = $id_usuario;
        $this->fecha = $fecha;
        $this->estado = $estado;
        $this->tipo = $tipo;
        $this->total = $total;
        $this->total_sin_descuento = $total_sin_descuento;
        $this->descuento_aplicado = $descuento_aplicado;
        $this->lineas = $lineas;
    }

    public function getId() { return $this->id; }
    public function getNumeroPedido() { return $this->numero_pedido; }
    public function getIdUsuario() { return $this->id_usuario; }
    public function getFecha() { return $this->fecha; }
    public function getEstado() { return $this->estado; }
    public function getTipo() { return $this->tipo; }
    public function getTotal() { return $this->total; }
    public function getTotalSinDescuento() { return $this->total_sin_descuento; }
    public function getDescuentoAplicado() { return $this->descuento_aplicado; }
    public function getLineas() { return $this->lineas; }
    
    public function setLineas($lineas) { $this->lineas = $lineas; }
}