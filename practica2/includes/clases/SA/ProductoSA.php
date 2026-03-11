<?php

class ProductoSA {
    private $dao;
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
        $this->dao = new ProductoDAO($db_connection);
    }

    public function getCatalogoCliente() {
        $todos = $this->dao->listarTodos();
        // El cliente solo ve los ofertados
        return array_filter($todos, function($p) { return $p->getOfertado() == 1; });
    }

    public function getGestionAdmin() {
        return $this->dao->listarTodos();
    }

    public function buscarProducto($id) {
        return $this->dao->obtenerPorId($id);
    }

    public function obtenerCategoriasActivas() {
        // Método auxiliar para no crear un SA de categorías solo para esto ahora mismo
        $res = mysqli_query($this->db, "SELECT id, nombre FROM categorias WHERE activa = 1");
        $categorias = [];
        while($row = mysqli_fetch_assoc($res)) { $categorias[] = $row; }
        return $categorias;
    }

    public function guardarProducto($datos) {
        $disponible = isset($datos['disponible']) ? 1 : 0;
        $ofertado = isset($datos['ofertado']) ? 1 : (isset($datos['id']) ? 0 : 1); // Por defecto ofertado al crear
        
        $p = new Producto(
            $datos['id'] ?? null, 
            $datos['id_categoria'], 
            trim($datos['nombre']), 
            trim($datos['descripcion']), 
            $datos['precio_base'], 
            $datos['iva'], 
            $disponible, 
            $ofertado
        );
        return $this->dao->guardar($p);
    }

    public function toggleOferta($id) {
        $p = $this->dao->obtenerPorId($id);
        if ($p) {
            $nuevoOfertado = $p->getOfertado() ? 0 : 1;
            $pActualizado = new Producto($p->getId(), $p->getIdCategoria(), $p->getNombre(), $p->getDescripcion(), 
                                         $p->getPrecioBase(), $p->getIva(), $p->getDisponible(), $nuevoOfertado);
            return $this->dao->guardar($pActualizado);
        }
        return false;
    }
}