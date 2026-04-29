<?php
require_once __DIR__ . '/../../comun/formularioBase.php';

class FormularioActualizarProducto extends formularioBase {

    private $db;
    private $producto;

    public function __construct($db_connection, $producto) {
        // Inicializamos el formulario base
        parent::__construct('formActualizarProducto', [
            'enctype' => 'multipart/form-data',
<<<<<<< HEAD
            'urlRedireccion' => '../productos_gerente.php'
=======
            'urlRedireccion' => RAIZ_APP . '/includes/vistas/productos/productos_gerente.php'
>>>>>>> angela
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
<<<<<<< HEAD

        $erroresGlobales = self::generaListaErroresGlobales($this->errores);

        // IMPORTANTE: Aquí NO ponemos <form> porque la clase base ya lo pone.
        // Usamos un DIV con la clase para que el CSS lo trate igual que al de Crear.
        return <<<EOF
        $erroresGlobales
=======
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
>>>>>>> angela
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

<<<<<<< HEAD
=======
            <label>Imágenes actuales:</label>
            <div style="display:flex; gap:8px; flex-wrap:wrap; margin:8px 0 18px 0;">
                $htmlImagenesActuales
            </div>

>>>>>>> angela
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
<<<<<<< HEAD
        // Aquí iría tu lógica de ProductoSA->actualizar(...)
        // Similar a la que tenías en el procesar_producto.php
    }
}
=======
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
>>>>>>> angela
