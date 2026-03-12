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

        <label>Nombre:</label> <input type="text" name="nombre" required>
        <label>Descripción:</label> <textarea name="descripcion" rows="4" required></textarea>
        
        <label>Precio Base:</label> 
        <input type="number" step="0.01" id="p_base" name="precio_base" oninput="recalcular()" required>
        
        <label>IVA:</label>
        <select id="p_iva" name="iva" onchange="recalcular()">
            <option value="4">4%</option>
            <option value="10">10%</option>
            <option value="21" selected>21%</option>
        </select>
        <p>Precio Final: <span id="p_final">0.00 €</span></p>

        <label>Imagen:</label> <input type="file" name="imagen" accept="image/*">
        <button type="submit">Guardar Producto</button>
        <a href="../admin_productos.php">Cancelar</a>
    </form>
EOF;

$js = [RAIZ_APP . "/js/script.js"];

$contenidoAdicional = <<<EOF
<script>
    function recalcular() {
        let base = parseFloat(document.getElementById('p_base').value) || 0;
        let iva = parseInt(document.getElementById('p_iva').value) || 0;
        document.getElementById('p_final').innerText = (base * (1 + iva/100)).toFixed(2) + " €";
    }
</script>
EOF;

require("../../comun/plantilla.php");