<?php
require_once (__DIR__ . '/../../../config.php');
require_once (__DIR__ . '/../../comun/formularioBase.php');

class FormularioActualizarCategoria extends formularioBase {

    private $sa;
    private $categoria;

    public function __construct($categoria = null) {
        global $db_connection;
        parent::__construct('formActualizarCategoria', [
            'enctype' => 'multipart/form-data',
            'urlRedireccion' => RAIZ_APP . '/includes/vistas/categorias/categorias_gerente.php'
        ]);
        $this->sa = new CategoriaSA($db_connection);
        $this->categoria = $categoria;
    }

    protected function generaCamposFormulario(&$datos) {
        if (!$this->categoria && !isset($datos['id'])) {
            return "<p>Error: No se ha proporcionado una categoría para actualizar.</p>";
        }

        $id = $this->categoria ? $this->categoria->getId() : $datos['id'];
        $nombre = htmlspecialchars($this->categoria ? $this->categoria->getNombre() : ($datos['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
        $descripcion = htmlspecialchars($this->categoria ? $this->categoria->getDescripcion() : ($datos['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
        $imagenActual = htmlspecialchars($this->categoria ? $this->categoria->getImagen() : ($datos['imagen_actual'] ?? 'categoria_default.jpg'), ENT_QUOTES, 'UTF-8');
        $rutaImagenActual = RUTA_IMG . "/categorias/" . htmlspecialchars($imagenActual);

        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'descripcion', 'imagen'], $this->errores);

        return <<<EOF
        $htmlErroresGlobales
        <div class="card shadow-sm mx-auto" style="max-width: 760px;">
        <div class="card-body p-4">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id" value="$id">
            <input type="hidden" name="imagen_actual" value="$imagenActual">

            <h1 class="h3 mb-4">Editar categoría: $nombre</h1>
            
            <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input class="form-control" type="text" name="nombre" value="$nombre" required>
            {$erroresCampos['nombre']}
            </div>
            
            <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea class="form-control" name="descripcion" rows="3" required>$descripcion</textarea>
            {$erroresCampos['descripcion']}
            </div>

            <div class="mb-3">
                <p class="mb-2">Imagen actual: <strong>$imagenActual</strong></p>
                <img src="$rutaImagenActual" alt="Imagen actual de $nombre" class="rounded border object-fit-cover" style="width:160px; height:110px;">
            </div>

            <div class="mb-4">
            <label class="form-label">Sustituir imagen opcional</label>
            <input class="form-control" type="file" name="imagen" accept="image/*">
            {$erroresCampos['imagen']}
            </div>
            
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-success" type="submit">Actualizar cambios</button>
                <a href="../categorias_gerente.php" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </div>
        </div>
EOF;
    }

    protected function procesaFormulario(&$datos) {
        $this->errores = [];

        // Saneamiento
        $id = filter_var($datos['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $nombre = filter_var(trim($datos['nombre'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $descripcion = filter_var(trim($datos['descripcion'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $nombreImagen = filter_var($datos['imagen_actual'] ?? 'categoria_default.jpg', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($nombre)) {
            $this->errores['nombre'] = "El nombre es obligatorio.";
        }
        if (empty($descripcion)) {
            $this->errores['descripcion'] = "La descripción es obligatoria.";
        }

        if (count($this->errores) > 0) {
            return;
        }

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
                $this->errores['imagen'] = "No se pudo subir la imagen.";
                return;
            }

            $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'avif'];

            if (!in_array($ext, $extensionesPermitidas, true) || getimagesize($_FILES['imagen']['tmp_name']) === false) {
                $this->errores['imagen'] = "El archivo debe ser una imagen válida.";
                return;
            }

            $nombreNuevo = uniqid('cat_') . '.' . $ext;
            $rutaDestino = DIR_RAIZ . "/img/categorias/" . $nombreNuevo;

            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                $nombreImagen = $nombreNuevo;
            } else {
                $this->errores['imagen'] = "No se pudo subir la imagen.";
                return;
            }
        }

        $datosCategoria = [
            'id' => $id,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'imagen' => $nombreImagen
        ];

        if (!$this->sa->guardarCategoria($datosCategoria)) {
            $this->errores[] = "Error al actualizar la categoría en la base de datos.";
        }
    }
}
