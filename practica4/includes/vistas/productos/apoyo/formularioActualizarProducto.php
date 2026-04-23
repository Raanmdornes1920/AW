<?php
require_once __DIR__ . '/../../comun/formularioBase.php';

class FormularioActualizarProducto extends formularioBase {

    private $db;
    private $producto;

    public function __construct($db_connection, $producto) {
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

        return <<<EOF
        $erroresGlobales
        <fieldset class="form-estilizado">
            <input type="hidden" name="id" value="$idVal">
            <h2>Editar Producto: $nombreVal</h2>
            
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

            <label>Añadir más imágenes (Se sumarán a las actuales):</label> 
            <input type="file" name="imagenes[]" accept="image/*" multiple>
            
            <div class="acciones" style="margin-top: 30px;">
                <button type="submit">Actualizar Producto</button>
                <a href="../productos_gerente.php" class="boton-borrar">Cancelar</a>
            </div>
        </fieldset>
EOF;
    }

    protected function procesaFormulario(&$datos) {
        $datosSaneados = [];
        $datosSaneados['id'] = filter_var($datos['id'], FILTER_SANITIZE_NUMBER_INT);
        $datosSaneados['nombre'] = filter_var($datos['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $datosSaneados['descripcion'] = filter_var($datos['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $datosSaneados['id_categoria'] = filter_var($datos['id_categoria'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $datosSaneados['precio_base'] = filter_var($datos['precio_base'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $datosSaneados['iva'] = filter_var($datos['iva'] ?? 21, FILTER_SANITIZE_NUMBER_INT);
        $datosSaneados['disponible'] = isset($datos['disponible']) ? 1 : 0;
        $datosSaneados['ofertado'] = isset($datos['ofertado']) ? 1 : 0;
        $datosSaneados['cocinable'] = filter_var($datos['cocinable'] ?? 1, FILTER_SANITIZE_NUMBER_INT);

        // Procesamiento de imágenes nuevas
        $imagenesSubidas = [];
        if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
            foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION);
                    $nombreNuevo = uniqid('prod_') . '_' . $key . '.' . $ext;
                    $rutaDestino = __DIR__ . "/../../../../img/productos/" . $nombreNuevo;
                    
                    if (move_uploaded_file($tmp_name, $rutaDestino)) {
                        $imagenesSubidas[] = $nombreNuevo;
                    }
                }
            }
        }

        // Si se han subido imágenes, las pasamos. Si no, ProductoSA ya se encarga 
        // de mantener las anteriores si le llega el array vacío.
        $datosSaneados['imagenes'] = $imagenesSubidas;

        $sa = new ProductoSA($this->db);
        if (!$sa->guardarProducto($datosSaneados)) {
            $this->errores[] = "No se pudieron guardar los cambios en la base de datos.";
        }
    }
}