<?php
require_once (__DIR__ . '/../../../config.php');

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
}