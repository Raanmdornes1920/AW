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
        $catDAO = new CategoriaDAO($this->db);
        $todas = $catDAO->listarTodas();
        return array_filter($todas, function($c) { return $c->getActiva() == 1; });
    }

    public function buscarPorCategoria($id_cat) {
        $todos = $this->dao->listarTodos();
        return array_filter($todos, function($p) use ($id_cat) {
            return $p->getIdCategoria() == $id_cat && $p->getOfertado() == 1;
        });
    }

    public function listarTodos() {
        return $this->dao->listarTodos();
    }

    public function obtenerPorId($id) {
        return $this->dao->obtenerPorId($id);
    }

    public function guardarProducto($datos) {
        $id = (isset($datos['id']) && !empty($datos['id'])) ? $datos['id'] : null;
        
        $esNuevo = ($id === null);
    
        $disponible = isset($datos['disponible']) ? 1 : ($esNuevo ? 1 : 0);
        $ofertado = isset($datos['ofertado']) ? 1 : ($esNuevo ? 1 : 0); 
        
        $imagenes = $datos['imagenes'] ?? [];
        
        $p = new Producto(
            $id, 
            $datos['id_categoria'], 
            trim($datos['nombre']), 
            trim($datos['descripcion']), 
            $datos['precio_base'], 
            $datos['iva'], 
            $disponible, 
            $ofertado,
            '', 
            $imagenes
        );
        return $this->dao->guardar($p);
    }

    public function toggleOferta($id) {
        $p = $this->dao->obtenerPorId($id);
        if ($p) {
            $nuevoOfertado = $p->getOfertado() ? 0 : 1;
            
            $pActualizado = new Producto(
                $p->getId(), 
                $p->getIdCategoria(), 
                $p->getNombre(), 
                $p->getDescripcion(), 
                $p->getPrecioBase(), 
                $p->getIva(), 
                $p->getDisponible(), 
                $nuevoOfertado,
                $p->getNombreCategoria(), 
                $p->getImagen()           
            );
            return $this->dao->guardar($pActualizado);
        }
        return false;
    }
}