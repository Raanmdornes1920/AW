<?php
require_once (__DIR__ . '/../../../config.php');

class FormularioActualizarProducto {

    private $db;
    private $producto;

    public function __construct($db_connection, $producto = null) {
        $this->db = $db_connection;
        $this->producto = $producto;
    }

    public function gestiona() {
        return $this->generaFormulario();
    }

    public function saneaDatos($datos) {
        $datosSaneados = [];
        $datosSaneados['id'] = filter_var($datos['id'], FILTER_SANITIZE_NUMBER_INT);
        $datosSaneados['nombre'] = filter_var($datos['nombre'], FILTER_SANITIZE_SPECIAL_CHARS);
        $datosSaneados['descripcion'] = filter_var($datos['descripcion'], FILTER_SANITIZE_SPECIAL_CHARS);
        $datosSaneados['id_categoria'] = filter_var($datos['id_categoria'], FILTER_SANITIZE_NUMBER_INT);
        $datosSaneados['precio_base'] = filter_var($datos['precio_base'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $datosSaneados['iva'] = filter_var($datos['iva'], FILTER_SANITIZE_NUMBER_INT);
        $datosSaneados['disponible'] = isset($datos['disponible']) ? 1 : 0;
        $datosSaneados['ofertado'] = isset($datos['ofertado']) ? 1 : 0;
        $datosSaneados['accion'] = $datos['accion'];
        return $datosSaneados;
    }

    private function generaFormulario() {
        $catSA = new CategoriaSA($this->db);
        $categorias = $catSA->obtenerTodas();

        $optionsCategorias = "";
        foreach($categorias as $cat) {
            $selected = ($cat->getId() == $this->producto->getIdCategoria()) ? "selected" : "";
            $optionsCategorias .= '<option value="'.$cat->getId().'" '.$selected.'>'.htmlspecialchars($cat->getNombre()).'</option>';
        }

        $ivaActual = $this->producto->getIva();
        $sel4 = ($ivaActual == 4) ? "selected" : "";
        $sel10 = ($ivaActual == 10) ? "selected" : "";
        $sel21 = ($ivaActual == 21) ? "selected" : "";

        $nombreVal = htmlspecialchars($this->producto->getNombre());
        $descVal = htmlspecialchars($this->producto->getDescripcion());
        $precioVal = $this->producto->getPrecioBase();
        $idVal = $this->producto->getId();
        $checkDisp = $this->producto->getDisponible() ? 'checked' : '';
        $checkOfert = $this->producto->getOfertado() ? 'checked' : '';

        return <<<EOF
        <form action="procesar_producto.php" method="POST" enctype="multipart/form-data" class="form-estilizado">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id" value="$idVal">

            <h2>Editar: $nombreVal</h2>
            
            <label>Categoría:</label>
            <select name="id_categoria">
                $optionsCategorias
            </select>

            <label>Nombre:</label> 
            <input type="text" name="nombre" value="$nombreVal" required>
            
            <label>Descripción:</label> 
            <textarea name="descripcion" rows="4" required>$descVal</textarea>
            
            <label>Precio Base (€):</label> 
            <input type="number" step="0.01" name="precio_base" id="p_base" value="$precioVal" oninput="recalcular()" required>
            
            <label>IVA:</label>
            <select name="iva" id="p_iva" onchange="recalcular()">
                <option value="4" $sel4>4%</option>
                <option value="10" $sel10>10%</option>
                <option value="21" $sel21>21%</option>
            </select>

            <div class="precio-final-destacado">
                Precio Final (con IVA): <strong id="p_final">0.00 €</strong>
            </div>

            <div class="grupo-checkbox">
                <label><input type="checkbox" name="disponible" value="1" $checkDisp> Stock disponible</label>
                <label><input type="checkbox" name="ofertado" value="1" $checkOfert> Ofertado en carta</label>
            </div>

            <label>Añadir más imágenes:</label> 
            <input type="file" name="imagenes[]" accept="image/*" multiple>
            
            <div class="acciones" style="margin-top: 30px;">
                <button type="submit">Actualizar Producto</button>
                <a href="../productos_gerente.php" class="boton-borrar">Cancelar</a>
            </div>
        </form>
EOF;
    }
}