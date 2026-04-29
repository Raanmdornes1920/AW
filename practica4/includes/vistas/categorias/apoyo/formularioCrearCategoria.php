<?php
require_once (__DIR__ . '/../../../config.php');
<<<<<<< HEAD

class FormularioCrearCategoria {

    public function __construct() {
    }

    public function gestiona() {
        return $this->generaFormulario();
    }

    public function saneaDatos($datos) {
        $datosSaneados = [];

        $datosSaneados['nombre'] = filter_var($datos['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $datosSaneados['descripcion'] = filter_var($datos['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $datosSaneados['accion'] = $datos['accion'] ?? 'crear';

        return $datosSaneados;
    }

    private function generaFormulario() {
        return <<<EOF
        <form action="procesar_categoria.php" method="POST" enctype="multipart/form-data" class="form-estilizado">
            <input type="hidden" name="accion" value="crear">
            <h2>Crear Categoría</h2>
            
            <label>Nombre:</label>
            <input type="text" name="nombre" required>
            
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3" required></textarea>

            <label>Imagen (Opcional):</label>
            <input type="file" name="imagen" accept="image/*">
            
            <div class="acciones">
                <button type="submit">Guardar</button>
                <a href="../categorias_gerente.php" class="boton-borrar">Cancelar</a>
            </div>
        </form>
EOF;
    }
=======
require_once (__DIR__ . '/../../comun/formularioBase.php');

class FormularioCrearCategoria extends formularioBase {

    private $sa;

    public function __construct() {
        global $db_connection;
        parent::__construct('formCrearCategoria', [
            'action' => RAIZ_APP . '/includes/vistas/categorias/apoyo/procesar_categoria.php',
            'enctype' => 'multipart/form-data',
            'urlRedireccion' => RAIZ_APP . '/includes/vistas/categorias/categorias_gerente.php'
        ]);
        $this->sa = new CategoriaSA($db_connection);
    }

    protected function generaCamposFormulario(&$datos) {
        $nombre = $datos['nombre'] ?? '';
        $descripcion = $datos['descripcion'] ?? '';

        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'descripcion', 'imagen'], $this->errores);

        return <<<EOF
        $htmlErroresGlobales
        <div class="form-estilizado">
            <input type="hidden" name="accion" value="crear">
            <h2>Crear Nueva Categoría</h2>
            
            <label>Nombre:</label>
            <input type="text" name="nombre" value="$nombre" required>
            {$erroresCampos['nombre']}
            
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3" required>$descripcion</textarea>
            {$erroresCampos['descripcion']}

            <label>Imagen (Opcional):</label>
            <input type="file" name="imagen" accept="image/*">
            {$erroresCampos['imagen']}
            
            <div class="acciones">
                <button type="submit">Guardar Categoría</button>
                <a href="../categorias_gerente.php" class="boton-borrar">Cancelar</a>
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

        // Procesar imagen
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
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'imagen' => $nombreImagen
        ];

        if (!$this->sa->guardarCategoria($datosCategoria)) {
            $this->errores[] = "Error al guardar la categoría en la base de datos.";
        }
    }
>>>>>>> angela
}