<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new ProductoSA($db_connection);
$catSA = new CategoriaSA($db_connection);
$categorias = $catSA->obtenerTodas();

$tituloPagina = "Crear Producto";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

$optionsCategorias = "";
foreach($categorias as $cat) {
    $optionsCategorias .= '<option value="'.$cat->getId().'">'.htmlspecialchars($cat->getNombre()).'</option>';
}

$contenidoPrincipal = <<<EOF
    <form action="procesar_producto.php" method="POST" enctype="multipart/form-data" class="form-estilizado">
        <input type="hidden" name="accion" value="crear">
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

        <label>Imagen del producto:</label> 
        <input type="file" name="imagenes[]" accept="image/*" multiple>

        <div class="acciones" style="margin-top: 30px;">
            <button type="submit">Guardar Producto</button>
            <a href="../productos_gerente.php" class="boton-borrar">Cancelar</a>
        </div>
    </form>
EOF;

$js = [
    RAIZ_APP . "/js/producto.js",
    RAIZ_APP . "/js/script.js"
];

require("../../comun/plantilla.php");