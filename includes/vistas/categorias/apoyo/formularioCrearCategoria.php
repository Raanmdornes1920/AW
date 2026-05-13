<?php
require_once (__DIR__ . '/../../../config.php');
require_once (__DIR__ . '/../../comun/formularioBase.php');

class FormularioCrearCategoria extends formularioBase {

    private $sa;

    public function __construct() {
        global $db_connection;
        parent::__construct('formCrearCategoria', [
            'enctype' => 'multipart/form-data',
            'urlRedireccion' => RAIZ_APP . '/includes/vistas/categorias/categorias_gerente.php'
        ]);
        $this->sa = new CategoriaSA($db_connection);
    }

    protected function generaCamposFormulario(&$datos) {
        $nombre = htmlspecialchars($datos['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
        $descripcion = htmlspecialchars($datos['descripcion'] ?? '', ENT_QUOTES, 'UTF-8');

        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'descripcion', 'imagen'], $this->errores);

        return <<<EOF
        $htmlErroresGlobales
        <div class="card shadow-sm mx-auto" style="max-width: 760px;">
        <div class="card-body p-4">
            <input type="hidden" name="accion" value="crear">
            <h1 class="h3 mb-4">Crear nueva categoría</h1>
            
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

            <div class="mb-4">
            <label class="form-label">Imagen opcional</label>
            <input class="form-control" type="file" name="imagen" accept="image/*">
            {$erroresCampos['imagen']}
            </div>
            
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-success" type="submit">Guardar categoría</button>
                <a href="../categorias_gerente.php" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </div>
        </div>
EOF;
    }

    protected function procesaFormulario(&$datos) {
        $this->errores = [];

        // Saneamiento
        $nombre = filter_var(trim($datos['nombre'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
        $descripcion = filter_var(trim($datos['descripcion'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($nombre)) {
            $this->errores['nombre'] = "El nombre es obligatorio.";
        }
        if (empty($descripcion)) {
            $this->errores['descripcion'] = "La descripción es obligatoria.";
        }

        if (count($this->errores) > 0) {
            return;
        }

        $nombreImagen = 'categoria_default.jpg';

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
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'imagen' => $nombreImagen
        ];

        if (!$this->sa->guardarCategoria($datosCategoria)) {
            $this->errores[] = "Error al guardar la categoría en la base de datos.";
        }
    }
}
