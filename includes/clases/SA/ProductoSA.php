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

        $imagenesParaObjeto = [];

        // Si vienen imágenes nuevas (subidas en el formulario)
        if (isset($datos['imagenes']) && is_array($datos['imagenes']) && !empty($datos['imagenes'])) {
            $imagenesParaObjeto = $datos['imagenes'];
        }
        // Si no vienen nuevas pero es una edición, rescatamos las que ya tiene en BD
        elseif (!$esNuevo) {
            $productoExistente = $this->dao->obtenerPorId($id);
            if ($productoExistente) {
                $imagenesParaObjeto = $productoExistente->getImagenesArray();
            }
        }

        $disponible = isset($datos['disponible']) ? (int)$datos['disponible'] : ($esNuevo ? 1 : 0);
        $ofertado = isset($datos['ofertado']) ? (int)$datos['ofertado'] : ($esNuevo ? 1 : 0);
        $cocinable = isset($datos['cocinable']) ? (int)$datos['cocinable'] : 1;

        $p = new Producto(
            $id,
            $datos['id_categoria'],
            trim($datos['nombre']),
            trim($datos['descripcion']),
            $datos['precio_base'],
            $datos['iva'],
            $disponible,
            $ofertado,
            $cocinable,
            '',
            $imagenesParaObjeto
        );

        return $this->dao->guardar($p);
    }

    public function toggleOferta($id) {
        $p = $this->dao->obtenerPorId($id);
        if ($p) {
            $nuevoOfertado = $p->getOfertado() ? 0 : 1;

            // IMPORTANTE: Al reconstruir el objeto, debemos pasarle el array de imágenes
            // que ya tiene, de lo contrario el DAO pensará que las hemos borrado todas.
            $pActualizado = new Producto(
                $p->getId(),
                $p->getIdCategoria(),
                $p->getNombre(),
                $p->getDescripcion(),
                $p->getPrecioBase(),
                $p->getIva(),
                $p->getDisponible(),
                $nuevoOfertado,
                $p->getCocinable(), // Pasamos cocinable
                $p->getNombreCategoria(),
                $p->getImagenesArray() // Pasamos el array completo de imágenes
            );
            return $this->dao->guardar($pActualizado);
        }
        return false;
    }

    public function retirarDeCarta($id) {
        $p = $this->dao->obtenerPorId($id);
        if (!$p) {
            return false;
        }

        $pActualizado = new Producto(
            $p->getId(),
            $p->getIdCategoria(),
            $p->getNombre(),
            $p->getDescripcion(),
            $p->getPrecioBase(),
            $p->getIva(),
            $p->getDisponible(),
            0,
            $p->getCocinable(),
            $p->getNombreCategoria(),
            $p->getImagenesArray()
        );

        return $this->dao->guardar($pActualizado);
    }
}
