<?php

class OfertaSA {
    private $dao;
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
        $this->dao = new OfertaDAO($db_connection); 
    }

    /**
     * Obtiene todas las ofertas (actuales y pasadas)
     */
    public function obtenerTodas() {
        return $this->dao->listarTodas();
    }

    /**
     * Busca una oferta por su ID
     */
    public function buscarPorId($id) {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        return $this->dao->obtenerPorId($id);
    }

    /**
     * Borra una oferta por su ID
     */
    public function borrarOferta($id) {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        return $this->dao->borrar($id);
    }

    /**
     * Guarda (crea o actualiza) una oferta a partir de datos del formulario
     * $datos espera: nombre, descripcion, fecha_inicio, fecha_fin, descuento_porcentaje, prod_ids[], prod_cants[]
     */
    public function guardarOferta($datos) {
        $id = (isset($datos['id']) && !empty($datos['id'])) ? $datos['id'] : null;
        
        // Estructuramos el array de productos y cantidades que viene del formulario
        $productosYCantidades = [];
        if (isset($datos['prod_ids']) && isset($datos['prod_cants'])) {
            for ($i = 0; $i < count($datos['prod_ids']); $i++) {
                $idProd = filter_var($datos['prod_ids'][$i], FILTER_SANITIZE_NUMBER_INT);
                $cant = filter_var($datos['prod_cants'][$i], FILTER_SANITIZE_NUMBER_INT);
                if ($idProd > 0 && $cant > 0) {
                    $productosYCantidades[] = [
                        'id_producto' => $idProd,
                        'cantidad' => $cant
                    ];
                }
            }
        }

        if (empty($productosYCantidades)) {
            return false; // Una oferta necesita al menos un producto
        }

        // Creamos el objeto Oferta
        $oferta = new Oferta(
            $id,
            trim($datos['nombre']),
            trim($datos['descripcion']),
            $datos['fecha_inicio'],
            $datos['fecha_fin'],
            $datos['descuento_porcentaje'],
            $productosYCantidades
        );

        return $this->dao->guardar($oferta);
    }

    /**
     * Obtiene solo las ofertas activas (para la vista del cliente)
     */
    public function obtenerOfertasActivas() {
        $todas = $this->dao->listarTodas();
        return array_filter($todas, function($oferta) {
            return $oferta->estaActiva();
        });
    }

    /**
     * Obtiene las ofertas pasadas/caducadas
     */
    public function obtenerOfertasPasadas() {
        $todas = $this->dao->listarTodas();
        $hoy = date('Y-m-d');
        return array_filter($todas, function($oferta) use ($hoy) {
            return $oferta->getFechaFin() < $hoy;
        });
    }
}