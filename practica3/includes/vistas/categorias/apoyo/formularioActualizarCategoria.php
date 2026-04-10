<?php
require_once (__DIR__ . '/../../../config.php');

class FormularioActualizarCategoria {

    private $categoria;

    public function __construct($categoria = null) {
        $this->categoria = $categoria;
    }

    public function gestiona() {
        if (!$this->categoria) {
            return "<p>Error: No se ha proporcionado una categoría para actualizar.</p>";
        }
        return $this->generaFormulario();
    }

    public function saneaDatos($datos) {
        $datosSaneados = [];

        $datosSaneados['id'] = filter_var($datos['id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $datosSaneados['nombre'] = filter_var($datos['nombre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $datosSaneados['descripcion'] = filter_var($datos['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $datosSaneados['accion'] = $datos['accion'] ?? 'actualizar';
        

        return $datosSaneados;
    }

    private function generaFormulario() {
        $id = $this->categoria->getId();
        $nombre = htmlspecialchars($this->categoria->getNombre());
        $descripcion = htmlspecialchars($this->categoria->getDescripcion());
        $imagenActual = $this->categoria->getImagen();

        return <<<EOF
        <form action="procesar_categoria.php" method="POST" enctype="multipart/form-data" class="form-estilizado">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="id" value="$id">
            <input type="hidden" name="imagen_actual" value="$imagenActual">

            <h2>Editar Categoría: $nombre</h2>
            
            <label>Nombre:</label>
            <input type="text" name="nombre" value="$nombre" required>
            
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3" required>$descripcion</textarea>

            <div class="info-imagen-actual" style="margin: 10px 0;">
                <p>Imagen actual: <strong>$imagenActual</strong></p>
            </div>

            <label>Sustituir imagen (Opcional):</label>
            <input type="file" name="imagen" accept="image/*">
            
            <div class="acciones" style="margin-top: 20px;">
                <button type="submit">Actualizar Cambios</button>
                <a href="../categorias_gerente.php" class="boton-borrar">Cancelar</a>
            </div>
        </form>
EOF;
    }
}