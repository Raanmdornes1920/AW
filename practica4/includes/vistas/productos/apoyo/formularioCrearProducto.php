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
        <div class="card shadow-sm mx-auto" style="max-width: 900px;">
        <div class="card-body p-4">
            <h1 class="h3 mb-4">Crear nuevo producto</h1>

            <div class="mb-3">
            <label class="form-label">Categoría</label>
            <select class="form-select" name="id_categoria" required>
                $optionsCategorias
            </select>
            </div>

            <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input class="form-control" type="text" name="nombre" required>
            </div>

            <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="4" required></textarea>
            </div>

            <div class="row g-3">
            <div class="col-12 col-md-6">
            <label class="form-label">Precio base (€)</label>
            <input class="form-control" type="number" step="0.01" id="p_base" name="precio_base" oninput="recalcular()" required>
            </div>

            <div class="col-12 col-md-6">
            <label class="form-label">IVA</label>
            <select class="form-select" id="p_iva" name="iva" onchange="recalcular()">
                <option value="4">4%</option>
                <option value="10">10%</option>
                <option value="21" selected>21%</option>
            </select>
            </div>
            </div>

            <div class="alert alert-info mt-3">
                Precio Final (con IVA): <strong id="p_final">0.00 €</strong>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6 form-check">
                    <input class="form-check-input" id="disponible" type="checkbox" name="disponible" value="1" checked>
                    <label class="form-check-label" for="disponible">Stock disponible</label>
                </div>
                <div class="col-12 col-md-6 form-check">
                    <input class="form-check-input" id="ofertado" type="checkbox" name="ofertado" value="1" checked>
                    <label class="form-check-label" for="ofertado">Ofertado en carta</label>
                </div>
            </div>

            <fieldset class="mb-3">
                <legend class="form-label fs-6">¿Requiere preparación en cocina?</legend>
                <div class="form-check">
                    <input class="form-check-input" id="cocinable-si" type="radio" name="cocinable" value="1" checked>
                    <label class="form-check-label" for="cocinable-si">Sí (comidas)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" id="cocinable-no" type="radio" name="cocinable" value="0">
                    <label class="form-check-label" for="cocinable-no">No (bebidas/barra)</label>
                </div>
            </fieldset>

            <div class="mb-3">
            <label class="form-label">Imágenes del producto</label>
            <input class="form-control" type="file" name="imagenes[]" id="input_imagenes" accept="image/*" multiple onchange="previsualizarImagenes(this)">
            </div>

            <div class="position-relative bg-light border rounded p-3 mb-4" style="max-width: 520px;">
                <div id="carrusel_previsualizacion">
                    <p style="color: #666;">Selecciona imágenes para ver la previsualización</p>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-success" type="submit">Guardar producto</button>
                <a href="../productos_gerente.php" class="btn btn-outline-secondary">Cancelar</a>
            </div>
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
