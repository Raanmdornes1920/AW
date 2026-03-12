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

    public function borrarCategoria($id) {
        if (!$id) return "ID de categoría no válido.";

        // 1. Verificación de seguridad: ¿Hay productos usando esta categoría?
        $id_esc = mysqli_real_escape_string($this->db, $id);
        $query = "SELECT COUNT(*) as total FROM productos WHERE id_categoria = '$id_esc'";
        $res = mysqli_query($this->db, $query);
        $data = mysqli_fetch_assoc($res);

        if ($data['total'] > 0) {
            return "No se puede eliminar: Esta categoría tiene {$data['total']} productos asociados. Debes moverlos o eliminarlos antes de borrar la categoría.";
        }

        // 2. Si está vacía, procedemos al DAO
        $exito = $this->dao->borrar($id);
        return $exito ? true : "Error interno al intentar eliminar la categoría.";
    }

    public function guardarCategoria($datos) {
        $c = new Categoria(
            $datos['id'] ?? null,
            trim($datos['nombre']),
            trim($datos['descripcion']),
            $datos['imagen'] ?? 'default_cat.png'
        );
        return $this->dao->guardar($c);
    }
    
}