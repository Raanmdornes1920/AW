<?php
class Oferta {
    private $id;
    private $nombre;
    private $descripcion;
    private $fecha_inicio;
    private $fecha_fin;
    private $descuento; // Porcentaje de descuento (ej: 21.5 para 21.5%)
    private $productos; // Array de ['id_producto' => X, 'cantidad' => Y, 'nombre' => Z, 'precio_con_iva' => W]

    public function __construct($id, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $descuento, $productos = []) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->descuento = $descuento;
        $this->productos = $productos;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }
    public function getFechaInicio() { return $this->fecha_inicio; }
    public function getFechaFin() { return $this->fecha_fin; }
    public function getDescuento() { return $this->descuento; }
    public function getProductos() { return $this->productos; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    public function setFechaInicio($fecha_inicio) { $this->fecha_inicio = $fecha_inicio; }
    public function setFechaFin($fecha_fin) { $this->fecha_fin = $fecha_fin; }
    public function setDescuento($descuento) { $this->descuento = $descuento; }
    public function setProductos($productos) { $this->productos = $productos; }

    /**
     * Calcula el precio total del pack SIN descuento (sumando precio_con_iva * cantidad)
     */
    public function getPrecioPackSinDescuento() {
        $total = 0;
        foreach ($this->productos as $p) {
            $precio = isset($p['precio_con_iva']) ? $p['precio_con_iva'] : 0;
            $cantidad = isset($p['cantidad']) ? $p['cantidad'] : 0;
            $total += $precio * $cantidad;
        }
        return round($total, 2);
    }

    /**
     * Calcula el precio final del pack CON descuento
     */
    public function getPrecioPackConDescuento() {
        $sinDescuento = $this->getPrecioPackSinDescuento();
        return round($sinDescuento * (1 - ($this->descuento / 100)), 2);
    }

    /**
     * Calcula la cantidad de dinero descontado
     */
    public function getAhorroDescuento() {
        return round($this->getPrecioPackSinDescuento() - $this->getPrecioPackConDescuento(), 2);
    }

    /**
     * Comprueba si la oferta está activa (entre fecha_inicio y fecha_fin)
     */
    public function estaActiva() {
        $hoy = date('Y-m-d');
        return ($this->fecha_inicio <= $hoy && $this->fecha_fin >= $hoy);
    }
}