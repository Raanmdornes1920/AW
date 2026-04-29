<?php
require_once __DIR__ . '/../../comun/formularioBase.php';

class FormularioActualizarOferta extends formularioBase {

    private $db;
    private $oferta; // Objeto Oferta con datos actuales

    public function __construct($db_connection, $oferta) {
        parent::__construct('formActualizarOferta', [
            'urlRedireccion' => '../ofertas_gerente.php'
        ]);
        $this->db = $db_connection;
        $this->oferta = $oferta;
    }

    protected function generaCamposFormulario(&$datos) {
        // Obtenemos los productos disponibles para el selector
        $prodSA = new ProductoSA($this->db);
        $productos = $prodSA->listarTodos();

        $productosJSON = [];
        foreach ($productos as $p) {
            $precioFinal = number_format($p->getPrecioFinal(), 2, '.', '');
            $productosJSON[] = [
                'id' => $p->getId(),
                'nombre' => $p->getNombre(),
                'precio' => (float)$precioFinal
            ];
        }

        $jsonData = htmlspecialchars(json_encode($productosJSON), ENT_QUOTES, 'UTF-8');

        // Datos actuales de la oferta para pre-rellenar
        $nombre = htmlspecialchars($this->oferta->getNombre());
        $desc = htmlspecialchars($this->oferta->getDescripcion());
        $fechaInicio = $this->oferta->getFechaInicio();
        $fechaFin = $this->oferta->getFechaFin();
        $idOferta = $this->oferta->getId();

        // Productos actuales de la oferta como JSON para que JS los cargue
        $productosActuales = [];
        foreach ($this->oferta->getProductos() as $p) {
            $productosActuales[] = [
                'id_producto' => $p['id_producto'],
                'cantidad' => $p['cantidad']
            ];
        }
        $productosActualesJSON = htmlspecialchars(json_encode($productosActuales), ENT_QUOTES, 'UTF-8');

        $precioPackActual = number_format($this->oferta->getPrecioPackSinDescuento(), 2, '.', '');
        $precioFinalActual = number_format($this->oferta->getPrecioPackConDescuento(), 2, '.', '');
        $descuentoActual = number_format($this->oferta->getDescuento(), 2, '.', '');

        $erroresGlobales = self::generaListaErroresGlobales($this->errores);

        return <<<EOF
        {$erroresGlobales}
        <div class="panel-cliente formulario-oferta">
            <h2>Actualizar Oferta</h2>
            <input type="hidden" name="id" value="{$idOferta}">
            
            <label>Nombre de la oferta:</label>
            <input type="text" name="nombre" required value="{$nombre}">
            
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3" required>{$desc}</textarea>
            
            <label>Fecha de inicio:</label>
            <input type="date" name="fecha_inicio" required value="{$fechaInicio}">
            
            <label>Fecha de fin:</label>
            <input type="date" name="fecha_fin" required value="{$fechaFin}">
            
            <hr class="separador-formulario">
            <h3>Productos de la oferta</h3>
            <p class="texto-ayuda-formulario">Selecciona los productos y cantidades que componen esta oferta.</p>

            <input type="hidden" id="productosDisponiblesJSON" value="{$jsonData}">
            <input type="hidden" id="productosActualesJSON" value="{$productosActualesJSON}">

            <div id="contenedor-productos-oferta">
                <!-- Se cargan dinámicamente por JS con los productos actuales -->
            </div>

            <button type="button" onclick="agregarProductoOferta()" class="boton-editar boton-agregar-producto">
                + Añadir Producto
            </button>

            <hr class="separador-formulario">
            <h3>Descuento</h3>

            <div class="precio-final-destacado dato-calculo-oferta">
                Precio del pack (sin descuento): <strong id="precio_pack_sin_descuento">{$precioPackActual} €</strong>
            </div>

            <label>Precio final deseado (€):</label>
            <input type="number" step="0.01" min="0" id="precio_final_deseado" name="precio_final_deseado" 
                   oninput="recalcularDescuentoOferta()" value="{$precioFinalActual}">

            <div class="precio-final-destacado dato-calculo-oferta">
                Porcentaje de descuento: <strong id="porcentaje_descuento_calculado">{$descuentoActual}%</strong>
            </div>
            
            <input type="hidden" name="descuento_porcentaje" id="descuento_porcentaje_hidden" value="{$descuentoActual}">

            <div class="precio-final-destacado dato-calculo-oferta dato-ahorro-oferta">
                Ahorro para el cliente: <strong id="ahorro_cliente">0.00 €</strong>
            </div>

            <div class="acciones">
                <button type="submit">Actualizar Oferta</button>
                <a href="../ofertas_gerente.php" class="boton-borrar">Cancelar</a>
            </div>
        </div>
EOF;
    }

    protected function procesaFormulario(&$datos) {
        $nombre = filter_var($datos['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $descripcion = filter_var($datos['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $fecha_inicio = $datos['fecha_inicio'] ?? '';
        $fecha_fin = $datos['fecha_fin'] ?? '';
        $descuento = filter_var($datos['descuento_porcentaje'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $id = filter_var($datos['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

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
            'id' => $id,
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
            $this->errores[] = "Error al actualizar la oferta. Verifica los datos.";
        }
    }
}
