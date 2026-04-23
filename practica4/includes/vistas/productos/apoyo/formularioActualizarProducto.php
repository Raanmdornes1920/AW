<?php
require_once __DIR__ . '/../../comun/formularioBase.php';

class FormularioActualizarProducto extends formularioBase {

    private $db;
    private $producto;

    public function __construct($db_connection, $producto) {
        // Inicializamos el formulario base
        parent::__construct('formActualizarProducto', [
            'enctype' => 'multipart/form-data',
            'urlRedireccion' => '../productos_gerente.php'
        ]);
        $this->db = $db_connection;
        $this->producto = $producto;
    }

    protected function generaCamposFormulario(&$datos) {
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
        $checkCocinableSi = $this->producto->getCocinable() ? 'checked' : '';
        $checkCocinableNo = !$this->producto->getCocinable() ? 'checked' : '';

        $erroresGlobales = self::generaListaErroresGlobales($this->errores);

        // IMPORTANTE: Aquí NO ponemos <form> porque la clase base ya lo pone.
        // Usamos un DIV con la clase para que el CSS lo trate igual que al de Crear.
        return <<<EOF
        $erroresGlobales
        <div class="form-estilizado">
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
            
            <div class="grupo-checkbox" style="margin-top: 15px;">
                <label>¿Requiere preparación en cocina?</label>
                <label><input type="radio" name="cocinable" value="1" $checkCocinableSi> Sí (Comidas)</label>
                <label><input type="radio" name="cocinable" value="0" $checkCocinableNo> No (Bebidas/Barra)</label>
            </div>

            <label>Sustituir imágenes actuales:</label> 
            <input type="file" name="imagenes[]" id="input_imagenes" accept="image/*" multiple onchange="previsualizarImagenes(this)">
            
            <div class="carrusel-contenedor" style="margin-top: 20px; max-width: 500px; margin-left: auto; margin-right: auto;">
                <div class="carrusel" id="carrusel_previsualizacion">
                    <p style="color: #666;">Selecciona imágenes para ver la previsualización</p>
                </div>
            </div>
            
            <div class="acciones" style="margin-top: 30px;">
                <button type="submit">Actualizar Producto</button>
                <a href="../productos_gerente.php" class="boton-borrar">Cancelar</a>
            </div>
        </div>
EOF;
    }

    protected function procesaFormulario(&$datos) {
        // Aquí iría tu lógica de ProductoSA->actualizar(...)
        // Similar a la que tenías en el procesar_producto.php
    }
}