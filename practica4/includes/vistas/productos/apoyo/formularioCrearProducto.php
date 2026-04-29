<?php
require_once __DIR__ . '/../../comun/formularioBase.php';

class FormularioCrearProducto extends formularioBase {

    private $db;

    public function __construct($db_connection) {
        parent::__construct('formCrearProducto', [
            'enctype' => 'multipart/form-data',
            'urlRedireccion' => '../productos_gerente.php'
        ]);
        $this->db = $db_connection;
    }

    protected function generaCamposFormulario(&$datos) {
        $catSA = new CategoriaSA($this->db);
        $categorias = $catSA->obtenerTodas();

        $optionsCategorias = "";
        foreach($categorias as $cat) {
            $optionsCategorias .= '<option value="'.$cat->getId().'">'.htmlspecialchars($cat->getNombre()).'</option>';
        }

        $erroresGlobales = self::generaListaErroresGlobales($this->errores);

        return <<<EOF
        $erroresGlobales
        <div class="form-estilizado">
            <h2>Crear Nuevo Producto</h2>

            <label>Categoría:</label>
            <select name="id_categoria" required>
                $optionsCategorias
            </select>

            <label>Nombre:</label>
            <input type="text" name="nombre" required>

            <label>Descripción:</label>
            <textarea name="descripcion" rows="4" required></textarea>

            <label>Precio Base (€):</label>
            <input type="number" step="0.01" id="p_base" name="precio_base" oninput="recalcular()" required>

            <label>IVA:</label>
            <select id="p_iva" name="iva" onchange="recalcular()">
                <option value="4">4%</option>
                <option value="10">10%</option>
                <option value="21" selected>21%</option>
            </select>

            <div class="precio-final-destacado">
                Precio Final (con IVA): <strong id="p_final">0.00 €</strong>
            </div>

            <div class="grupo-checkbox">
                <label><input type="checkbox" name="disponible" value="1" checked> Stock disponible</label>
                <label><input type="checkbox" name="ofertado" value="1" checked> Ofertado en carta</label>
            </div>

            <div class="grupo-checkbox" style="margin-top: 15px;">
                <label>¿Requiere preparación en cocina?</label>
                <label><input type="radio" name="cocinable" value="1" checked> Sí (Comidas)</label>
                <label><input type="radio" name="cocinable" value="0"> No (Bebidas/Barra)</label>
            </div>

            <label>Imágenes del producto:</label>
            <input type="file" name="imagenes[]" id="input_imagenes" accept="image/*" multiple onchange="previsualizarImagenes(this)">

            <div class="carrusel-contenedor" style="margin-top: 20px; max-width: 500px; margin-left: auto; margin-right: auto;">
                <div class="carrusel" id="carrusel_previsualizacion">
                    <p style="color: #666;">Selecciona imágenes para ver la previsualización</p>
                </div>
            </div>

            <div class="acciones" style="margin-top: 30px;">
                <button type="submit">Guardar Producto</button>
                <a href="../productos_gerente.php" class="boton-borrar">Cancelar</a>
            </div>
        </div>
EOF;
    }

    protected function procesaFormulario(&$datos) {
        $datosSaneados = [];
        $datosSaneados['nombre'] = filter_var($datos['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $datosSaneados['descripcion'] = filter_var($datos['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $datosSaneados['id_categoria'] = filter_var($datos['id_categoria'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $datosSaneados['precio_base'] = filter_var($datos['precio_base'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $datosSaneados['iva'] = filter_var($datos['iva'] ?? 21, FILTER_SANITIZE_NUMBER_INT);
        $datosSaneados['disponible'] = isset($datos['disponible']) ? 1 : 0;
        $datosSaneados['ofertado'] = isset($datos['ofertado']) ? 1 : 0;
        $datosSaneados['cocinable'] = filter_var($datos['cocinable'] ?? 1, FILTER_SANITIZE_NUMBER_INT);

        // Procesamiento de imágenes
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
        $datosSaneados['imagenes'] = $imagenesSubidas;

        $sa = new ProductoSA($this->db);
        if (!$sa->guardarProducto($datosSaneados)) {
            $this->errores[] = "Error técnico al intentar guardar el producto.";
        }
    }
}