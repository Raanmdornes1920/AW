<?php

class CategoriaSA {
    private $dao;

    public function __construct($db_connection) {
        $this->dao = new CategoriaDAO($db_connection);
    }

    public function obtenerTodas() {
        return $this->dao->listarTodas();
    }

    public function obtenerPorId($id) {
        return $this->dao->obtenerPorId($id);
    }

    public function borrarCategoria($id) {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        if (!$id || $id <= 0) return "ID de categoría no válido.";

        $totalProductos = $this->dao->contarProductosAsociados($id);

        if ($totalProductos > 0) {
            return "No se puede eliminar: Esta categoría tiene {$totalProductos} productos asociados. Debes moverlos o eliminarlos antes de borrar la categoría.";
        }

        $exito = $this->dao->borrar($id);
        return $exito ? true : "Error interno al intentar eliminar la categoría.";
    }

    public function guardarCategoria($datos) {
        $imagen = $datos['imagen'] ?? 'categoria_default.jpg';


        $c = new Categoria(
            $datos['id'] ?? null,
            trim($datos['nombre']),
            trim($datos['descripcion']),
           $imagen
        );
        return $this->dao->guardar($c);
    }

}
