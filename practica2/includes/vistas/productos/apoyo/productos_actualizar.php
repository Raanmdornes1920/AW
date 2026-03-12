<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new ProductoSA($db_connection);
$producto = $sa->buscarProducto($_GET['id'] ?? 0);
$catSA = new CategoriaSA($db_connection);
$categorias = $catSA->obtenerTodas();

if (!$producto) { header("Location: ../productos_gerente.php"); exit; }

$tituloPagina = "Actualizar Producto";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

// 1. Lógica para las categorías
$optionsCategorias = "";
foreach($categorias as $cat) {
    $selected = ($cat->getId() == $producto->getIdCategoria()) ? "selected" : "";
    $optionsCategorias .= '<option value="'.$cat->getId().'" '.$selected.'>'.htmlspecialchars($cat->getNombre()).'</option>';
}

// 2. Lógica para el IVA
$ivaActual = $producto->getIva();
$sel4 = ($ivaActual == 4) ? "selected" : "";
$sel10 = ($ivaActual == 10) ? "selected" : "";
$sel21 = ($ivaActual == 21) ? "selected" : "";

$nombreVal = htmlspecialchars($producto->getNombre());
$descVal = htmlspecialchars($producto->getDescripcion());
$precioVal = $producto->getPrecioBase();
$idVal = $producto->getId();
$imgActual = htmlspecialchars($producto->getImagen());
$checkDisp = $producto->getDisponible() ? 'checked' : '';
$checkOfert = $producto->getOfertado() ? 'checked' : '';

$fotosActualesHtml = "<div style='display:flex; gap:10px; margin-bottom:15px;'>";
foreach($producto->getImagenesArray() as $img) {
    $fotosActualesHtml .= "<img src='".RUTA_IMG."/productos/$img' width='70' style='border:1px solid #ccc; border-radius:5px;'>";
}
$fotosActualesHtml .= "</div>";

$contenidoPrincipal = <<<EOF
    <form action="procesar_producto.php" method="POST" enctype="multipart/form-data" class="form-estilizado">
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

        <label>Añadir más imágenes:</label> 
        <input type="file" name="imagenes[]" accept="image/*" multiple>
        
        <div class="acciones">
            <button type="submit">Actualizar Producto</button>
            <a href="../productos_gerente.php" class="boton-borrar">Cancelar</a>
        </div>
    </form>
EOF;

$contenidoAdicional = <<<EOF
    <script>
        function recalcular() {
            let base = parseFloat(document.getElementById('p_base').value) || 0;
            let iva = parseInt(document.getElementById('p_iva').value) || 0;
            let total = base + (base * (iva / 100));
            document.getElementById('p_final').innerText = total.toFixed(2) + " €";
        }
        window.onload = recalcular;
    </script>
EOF;

$js = [RAIZ_APP . "/js/script.js"];

require("../../comun/plantilla.php");