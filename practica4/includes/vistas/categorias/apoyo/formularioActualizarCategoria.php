<?php
require_once (__DIR__ . '/../../../config.php');
require_once (__DIR__ . '/../../comun/formularioBase.php');

class FormularioActualizarCategoria extends formularioBase {

    private $sa;
    private $categoria;

    public function __construct($categoria = null) {
        global $db_connection;
        parent::__construct('formActualizarCategoria', [
            'action' => RAIZ_APP . '/includes/vistas/categorias/apoyo/procesar_categoria.php',
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
        $nombre = htmlspecialchars($this->categoria ? $this->categoria->getNombre() : ($datos['nombre'] ?? ''));
        $descripcion = htmlspecialchars($this->categoria ? $this->categoria->getDescripcion() : ($datos['descripcion'] ?? ''));
        $imagenActual = $this->categoria ? $this->categoria->getImagen() : ($datos['imagen_actual'] ?? 'categoria_default.jpg');
        $rutaImagenActual = RUTA_IMG . "/categorias/" . htmlspecialchars($imagenActual);

        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'descripcion', 'imagen'], $this->errores);

        return <<<EOF
        $htmlErroresGlobales
        <div class="form-estilizado">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id" value="$id">
            <input type="hidden" name="imagen_actual" value="$imagenActual">

            <h2>Editar Categoría: $nombre</h2>
            
            <label>Nombre:</label>
            <input type="text" name="nombre" value="$nombre" required>
            {$erroresCampos['nombre']}
            
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3" required>$descripcion</textarea>
            {$erroresCampos['descripcion']}

            <div class="info-imagen-actual" style="margin: 10px 0;">
                <p>Imagen actual: <strong>$imagenActual</strong></p>
                <img src="$rutaImagenActual" alt="Imagen actual de $nombre" style="width:160px; height:110px; object-fit:cover; border-radius:8px; border:1px solid #ddd;">
            </div>

            <label>Sustituir imagen (Opcional):</label>
            <input type="file" name="imagen" accept="image/*">
            {$erroresCampos['imagen']}
            
            <div class="acciones" style="margin-top: 20px;">
                <button type="submit">Actualizar Cambios</button>
                <a href="../categorias_gerente.php" class="boton-borrar">Cancelar</a>
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

        // Procesar nueva imagen si existe
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
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
