<?php
require_once __DIR__ . '/../../comun/formularioBase.php';

class FormularioCrearOferta extends formularioBase {

    private $db;

    public function __construct($db_connection) {
        parent::__construct('formCrearOferta', [
            'urlRedireccion' => '../ofertas_gerente.php'
        ]);
        $this->db = $db_connection;
    }

    protected function generaCamposFormulario(&$datos) {
        // Obtenemos los productos disponibles para el selector
        $prodSA = new ProductoSA($this->db);
        $productos = $prodSA->listarTodos();

        $optionsProductos = "";
        $productosJSON = [];
        foreach ($productos as $p) {
            $precioFinal = number_format($p->getPrecioFinal(), 2, '.', '');
            $optionsProductos .= '<option value="' . $p->getId() . '" data-precio="' . $precioFinal . '">' 
                . htmlspecialchars($p->getNombre()) . ' (' . $precioFinal . ' €)</option>';
            $productosJSON[] = [
                'id' => $p->getId(),
                'nombre' => $p->getNombre(),
                'precio' => (float)$precioFinal
            ];
        }

        $jsonData = htmlspecialchars(json_encode($productosJSON), ENT_QUOTES, 'UTF-8');

        $erroresGlobales = self::generaListaErroresGlobales($this->errores);

        $hoy = date('Y-m-d');

        return <<<EOF
        {$erroresGlobales}
        <div class="form-estilizado">
            <h2>Crear Nueva Oferta</h2>
            
            <label>Nombre de la oferta:</label>
            <input type="text" name="nombre" required placeholder="Ej: Desayuno Andaluz">
            
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3" required placeholder="Describe la oferta..."></textarea>
            
            <label>Fecha de inicio:</label>
            <input type="date" name="fecha_inicio" required value="{$hoy}">
            
            <label>Fecha de fin:</label>
            <input type="date" name="fecha_fin" required>
            
            <hr style="margin: 20px 0;">
            <h3>Productos de la oferta</h3>
            <p style="font-size: 0.9em; color: #666;">Selecciona los productos y cantidades que componen esta oferta.</p>

            <input type="hidden" id="productosDisponiblesJSON" value="{$jsonData}">

            <div id="contenedor-productos-oferta">
                <!-- Aquí se añaden dinámicamente las filas de productos -->
            </div>

            <button type="button" onclick="agregarProductoOferta()" style="margin: 10px 0; background-color: #4CAF50; padding: 8px 16px; font-size: 14px;">
                + Añadir Producto
            </button>

            <hr style="margin: 20px 0;">
            <h3>Descuento</h3>

            <div class="precio-final-destacado" style="margin-bottom: 15px;">
                Precio del pack (sin descuento): <strong id="precio_pack_sin_descuento">0.00 €</strong>
            </div>

            <label>Precio final deseado (€):</label>
            <input type="number" step="0.01" min="0" id="precio_final_deseado" name="precio_final_deseado" 
                   oninput="recalcularDescuentoOferta()" placeholder="Introduce el precio final">

            <div class="precio-final-destacado" style="margin: 15px 0;">
                Porcentaje de descuento: <strong id="porcentaje_descuento_calculado">0.00%</strong>
            </div>
            
            <input type="hidden" name="descuento_porcentaje" id="descuento_porcentaje_hidden" value="0">

            <div class="precio-final-destacado" style="margin-bottom: 15px; color: #4CAF50;">
                Ahorro para el cliente: <strong id="ahorro_cliente">0.00 €</strong>
            </div>

            <div class="acciones" style="margin-top: 30px;">
                <button type="submit">Guardar Oferta</button>
                <a href="../ofertas_gerente.php" class="boton-borrar">Cancelar</a>
            </div>
        </div>
EOF;
    }

    protected function procesaFormulario(&$datos) {
        // Validaciones
        $nombre = filter_var($datos['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $descripcion = filter_var($datos['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $fecha_inicio = $datos['fecha_inicio'] ?? '';
        $fecha_fin = $datos['fecha_fin'] ?? '';
        $descuento = filter_var($datos['descuento_porcentaje'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        if (empty($nombre)) {
            $this->errores[] = "El nombre de la oferta es obligatorio.";
        }
        if (empty($fecha_inicio) || empty($fecha_fin)) {
            $this->errores[] = "Las fechas de inicio y fin son obligatorias.";
        }
        if ($fecha_fin < $fecha_inicio) {
            $this->errores[] = "La fecha de fin no puede ser anterior a la de inicio.";
        }
        if (!isset($datos['prod_ids']) || empty($datos['prod_ids'])) {
            $this->errores[] = "Debes añadir al menos un producto a la oferta.";
        }
        if ($descuento <= 0 || $descuento >= 100) {
            $this->errores[] = "El porcentaje de descuento debe estar entre 0 y 100.";
        }

        if (!empty($this->errores)) {
            return;
        }

        $datosSaneados = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'descuento_porcentaje' => $descuento,
            'prod_ids' => $datos['prod_ids'],
            'prod_cants' => $datos['prod_cants']
        ];

        $sa = new OfertaSA($this->db);
        if (!$sa->guardarOferta($datosSaneados)) {
            $this->errores[] = "Error al guardar la oferta. Verifica los datos.";
        }
    }
}
