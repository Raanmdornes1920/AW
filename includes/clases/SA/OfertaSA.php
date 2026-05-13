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

    // =================================================================
    // === LÓGICA DE APLICACIÓN DE OFERTAS AL CARRITO (Cliente) ========
    // =================================================================

    /**
     * Comprueba cuántas veces se puede aplicar una oferta al carrito
     * teniendo en cuenta los productos ya "ocupados" por otras ofertas.
     *
     * @param Oferta $oferta La oferta a comprobar
     * @param array  $carrito Array [id_producto => cantidad]
     * @param array  $consumidos Array [id_producto => cantidad_ya_usada]
     * @return int Número de veces que se puede aplicar la oferta
     */
    public function vecesAplicable($oferta, $carrito, $consumidos = []) {
        $productosOferta = $oferta->getProductos();
        if (empty($productosOferta)) {
            return 0;
        }

        $minVeces = PHP_INT_MAX;
        foreach ($productosOferta as $p) {
            $idProd     = $p['id_producto'];
            $cantOferta = (int)$p['cantidad'];

            $cantCarrito    = isset($carrito[$idProd]) ? (int)$carrito[$idProd] : 0;
            $cantConsumida  = isset($consumidos[$idProd]) ? (int)$consumidos[$idProd] : 0;
            $cantDisponible = $cantCarrito - $cantConsumida;

            if ($cantDisponible <= 0 || $cantOferta <= 0) {
                return 0;
            }

            $veces = intdiv($cantDisponible, $cantOferta);
            if ($veces < $minVeces) {
                $minVeces = $veces;
            }
        }

        return ($minVeces === PHP_INT_MAX) ? 0 : $minVeces;
    }

    /**
     * Aplica la lista de ofertas activadas por el cliente al carrito y
     * devuelve un resumen con el total sin descuento, el descuento total y
     * el detalle de qué oferta se aplicó cuántas veces.
     *
     * Reglas (enunciado):
     *  - Una oferta se aplica automáticamente las veces que cumpla el carrito.
     *  - Múltiples ofertas no pueden aplicarse a los mismos productos
     *    (consumimos los productos según se aplican, en el orden recibido).
     *
     * @param array $carrito          [id_producto => cantidad]
     * @param array $idsOfertas       Array de ids de ofertas activadas por el cliente
     * @return array {
     *      total_sin_descuento: float,
     *      descuento_total:     float,
     *      total_final:         float,
     *      ofertas_aplicadas:   array de [id, nombre, veces, ahorro_unitario, ahorro_total],
     *      ofertas_no_aplicables: array de [id, nombre, motivo]
     *  }
     */
    public function aplicarOfertasACarrito($carrito, $idsOfertas) {
        // Aseguramos tipos
        $carrito = is_array($carrito) ? $carrito : [];
        $idsOfertas = is_array($idsOfertas) ? array_values(array_unique(array_map('intval', $idsOfertas))) : [];

        // 1) Calculamos el total bruto del carrito (sin descuento)
        $prodSA = new ProductoSA($this->db);
        $totalSinDescuento = 0;
        foreach ($carrito as $idProd => $cant) {
            $producto = $prodSA->obtenerPorId($idProd);
            if ($producto) {
                $totalSinDescuento += $producto->getPrecioFinal() * $cant;
            }
        }
        $totalSinDescuento = round($totalSinDescuento, 2);

        $descuentoTotal     = 0;
        $ofertasAplicadas   = [];
        $ofertasNoAplicables = [];

        // 2) Por cada oferta seleccionada, calculamos cuántas veces se puede
        //    aplicar respetando los productos ya "consumidos" por ofertas previas
        $consumidos = [];
        foreach ($idsOfertas as $idOferta) {
            $oferta = $this->buscarPorId($idOferta);
            if (!$oferta || !$oferta->estaActiva()) {
                $ofertasNoAplicables[] = [
                    'id'     => $idOferta,
                    'nombre' => $oferta ? $oferta->getNombre() : 'Oferta ' . $idOferta,
                    'motivo' => 'No está disponible.'
                ];
                continue;
            }

            $veces = $this->vecesAplicable($oferta, $carrito, $consumidos);
            if ($veces <= 0) {
                $ofertasNoAplicables[] = [
                    'id'     => $oferta->getId(),
                    'nombre' => $oferta->getNombre(),
                    'motivo' => 'Tu carrito no tiene los productos necesarios (o ya los están usando otras ofertas).'
                ];
                continue;
            }

            // Marcamos los productos como consumidos por esta oferta
            foreach ($oferta->getProductos() as $p) {
                $idProd = $p['id_producto'];
                $cantUsada = (int)$p['cantidad'] * $veces;
                if (!isset($consumidos[$idProd])) {
                    $consumidos[$idProd] = 0;
                }
                $consumidos[$idProd] += $cantUsada;
            }

            $ahorroUnit  = $oferta->getAhorroDescuento(); // ahorro de aplicar la oferta UNA vez
            $ahorroTotal = round($ahorroUnit * $veces, 2);
            $descuentoTotal += $ahorroTotal;

            $ofertasAplicadas[] = [
                'id'              => $oferta->getId(),
                'nombre'          => $oferta->getNombre(),
                'veces'           => $veces,
                'ahorro_unitario' => $ahorroUnit,
                'ahorro_total'    => $ahorroTotal
            ];
        }

        $descuentoTotal = round($descuentoTotal, 2);
        $totalFinal     = round($totalSinDescuento - $descuentoTotal, 2);

        return [
            'total_sin_descuento'  => $totalSinDescuento,
            'descuento_total'      => $descuentoTotal,
            'total_final'          => $totalFinal < 0 ? 0 : $totalFinal,
            'ofertas_aplicadas'    => $ofertasAplicadas,
            'ofertas_no_aplicables'=> $ofertasNoAplicables
        ];
    }

    /**
     * Marca cada oferta activa con un flag "aplicable" según el carrito actual.
     * Útil para destacarlas en la vista del cliente.
     *
     * @return array de pares [oferta => Oferta, aplicable => bool, veces => int]
     */
    public function obtenerActivasConAplicabilidad($carrito) {
        $activas = $this->obtenerOfertasActivas();
        $resultado = [];
        foreach ($activas as $oferta) {
            $veces = $this->vecesAplicable($oferta, $carrito);
            $resultado[] = [
                'oferta'    => $oferta,
                'aplicable' => $veces > 0,
                'veces'     => $veces
            ];
        }
        return $resultado;
    }
}
