<?php
require_once __DIR__ . '/../../comun/formularioBase.php';

class FormularioActualizarProducto extends formularioBase {

    private $db;
    private $producto;

    public function __construct($db_connection, $producto) {
        // Inicializamos el formulario base
        parent::__construct('formActualizarProducto', [
            'enctype' => 'multipart/form-data',
            'urlRedireccion' => RAIZ_APP . '/includes/vistas/productos/productos_gerente.php'
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
        $imagenesActuales = $this->producto->getImagenesArray();
        $htmlImagenesActuales = "";
        if (empty($imagenesActuales)) {
            $htmlImagenesActuales = "<p style='color:#666;'>Este producto no tiene imágenes cargadas.</p>";
        } else {
            foreach ($imagenesActuales as $imgActual) {
                $rutaImg = RUTA_IMG . "/productos/" . htmlspecialchars($imgActual);
                $htmlImagenesActuales .= "<img src='{$rutaImg}' alt='Imagen actual de {$nombreVal}' style='width:110px; height:90px; object-fit:cover; border-radius:8px; border:1px solid #ddd; margin:4px;'>";
            }
        }

        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);

        return <<<EOF
        $htmlErroresGlobales
        <div class="card shadow-sm mx-auto" style="max-width: 900px;">
        <div class="card-body p-4">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id" value="$idVal">

            <h1 class="h3 mb-4">Editar: $nombreVal</h1>

            <div class="mb-3">
            <label class="form-label">Categoría</label>
            <select class="form-select" name="id_categoria">
                $optionsCategorias
            </select>
            </div>

            <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input class="form-control" type="text" name="nombre" value="$nombreVal" required>
            </div>

            <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="4" required>$descVal</textarea>
            </div>

            <div class="row g-3">
            <div class="col-12 col-md-6">
            <label class="form-label">Precio base (€)</label>
            <input class="form-control" type="number" step="0.01" name="precio_base" id="p_base" value="$precioVal" oninput="recalcular()" required>
            </div>

            <div class="col-12 col-md-6">
            <label class="form-label">IVA</label>
            <select class="form-select" name="iva" id="p_iva" onchange="recalcular()">
                <option value="4" $sel4>4%</option>
                <option value="10" $sel10>10%</option>
                <option value="21" $sel21>21%</option>
            </select>
            </div>
            </div>

            <div class="alert alert-info mt-3">
                Precio Final (con IVA): <strong id="p_final">0.00 €</strong>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6 form-check">
                    <input class="form-check-input" id="disponible" type="checkbox" name="disponible" value="1" $checkDisp>
                    <label class="form-check-label" for="disponible">Stock disponible</label>
                </div>
                <div class="col-12 col-md-6 form-check">
                    <input class="form-check-input" id="ofertado" type="checkbox" name="ofertado" value="1" $checkOfert>
                    <label class="form-check-label" for="ofertado">Ofertado en carta</label>
                </div>
            </div>

            <fieldset class="mb-3">
                <legend class="form-label fs-6">¿Requiere preparación en cocina?</legend>
                <div class="form-check">
                    <input class="form-check-input" id="cocinable-si" type="radio" name="cocinable" value="1" $checkCocinableSi>
                    <label class="form-check-label" for="cocinable-si">Sí (comidas)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" id="cocinable-no" type="radio" name="cocinable" value="0" $checkCocinableNo>
                    <label class="form-check-label" for="cocinable-no">No (bebidas/barra)</label>
                </div>
            </fieldset>

            <label class="form-label">Imágenes actuales</label>
            <div style="display:flex; gap:8px; flex-wrap:wrap; margin:8px 0 18px 0;">
                $htmlImagenesActuales
            </div>

            <div class="mb-3">
            <label class="form-label">Sustituir imágenes actuales</label>
            <input class="form-control" type="file" name="imagenes[]" id="input_imagenes" accept="image/*" multiple onchange="previsualizarImagenes(this)">
            </div>

            <div class="position-relative bg-light border rounded p-3 mb-4" style="max-width: 520px;">
                <div id="carrusel_previsualizacion">
                    <p style="color: #666;">Selecciona imágenes para ver la previsualización</p>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-success" type="submit">Actualizar producto</button>
                <a href="../productos_gerente.php" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </div>
        </div>
EOF;
    }

    protected function procesaFormulario(&$datos) {
        $this->errores = [];

        // Saneamiento
        $id = filter_var($datos['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $id_categoria = filter_var($datos['id_categoria'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $nombre = filter_var(trim($datos['nombre'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $descripcion = filter_var(trim($datos['descripcion'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $precio_base = filter_var($datos['precio_base'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $iva = filter_var($datos['iva'] ?? 21, FILTER_SANITIZE_NUMBER_INT);
        $disponible = isset($datos['disponible']) ? 1 : 0;
        $ofertado = isset($datos['ofertado']) ? 1 : 0;
        $cocinable = filter_var($datos['cocinable'] ?? 1, FILTER_SANITIZE_NUMBER_INT);

        if (empty($nombre)) {
            $this->errores['nombre'] = "El nombre es obligatorio.";
        }

        if (count($this->errores) > 0) {
            return;
        }

        // Procesamiento de imágenes nuevas
        $imagenesSubidas = [];
        if (isset($_FILES['imagenes']) && !empty($_FILES['imagenes']['name'][0])) {
            foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION);
                    $nombreNuevo = uniqid('prod_') . '_' . $key . '.' . $ext;
                    $rutaDestino = DIR_RAIZ . "/img/productos/" . $nombreNuevo;

                    if (move_uploaded_file($tmp_name, $rutaDestino)) {
                        $imagenesSubidas[] = $nombreNuevo;
                    }
                }
            }
        }

        $datosProducto = [
            'id' => $id,
            'id_categoria' => $id_categoria,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio_base' => $precio_base,
            'iva' => $iva,
            'disponible' => $disponible,
            'ofertado' => $ofertado,
            'cocinable' => $cocinable
        ];

        // Si hay imágenes nuevas, las añadimos. Si no, ProductoSA se encarga de mantener las viejas.
        if (!empty($imagenesSubidas)) {
            $datosProducto['imagenes'] = $imagenesSubidas;
        }

        $sa = new ProductoSA($this->db);
        if (!$sa->guardarProducto($datosProducto)) {
            $this->errores[] = "Error técnico al intentar actualizar el producto.";
        }
    }
}
