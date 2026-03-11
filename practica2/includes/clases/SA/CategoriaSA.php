<?php

class CategoriaSA {
    private $dao;
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
        $this->dao = new CategoriaDAO($db_connection);
    }

    public function obtenerTodas() {
        return $this->dao->listarTodas();
    }

    public function obtenerPorId($id) {
        return $this->dao->obtenerPorId($id);
    }

    public function guardarCategoria($datos) {
        $activa = isset($datos['activa']) ? 1 : (isset($datos['id']) ? 0 : 1);
        $imagen = !empty($datos['imagen']) ? $datos['imagen'] : 'default_cat.png';

        $c = new Categoria(
            $datos['id'] ?? null,
            trim($datos['nombre']),
            trim($datos['descripcion']),
            $imagen,
            $activa
        );
        return $this->dao->guardar($c);
    }

    public function toggleActiva($id) {
        $cat = $this->dao->obtenerPorId($id);
        if ($cat) {
            $nuevoEstado = $cat->getActiva() ? 0 : 1;
            $catActualizada = new Categoria(
                $cat->getId(),
                $cat->getNombre(),
                $cat->getDescripcion(),
                $cat->getImagen(),
                $nuevoEstado
            );
            return $this->dao->guardar($catActualizada);
        }
        return false;
    }
}