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

if (!$producto) { header("Location: ../admin_productos.php"); exit; }

$tituloPagina = "Actualizar Producto";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

// 1. Lógica para las categorías (marcar la seleccionada)
$optionsCategorias = "";
foreach($categorias as $cat) {
    $selected = ($cat->getId() == $producto->getIdCategoria()) ? "selected" : "";
    $optionsCategorias .= '<option value="'.$cat->getId().'" '.$selected.'>'.htmlspecialchars($cat->getNombre()).'</option>';
}

// 2. Lógica para el IVA (marcar el seleccionado)
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

$contenidoPrincipal = <<<EOF
    <form action="procesar_producto.php" method="POST" enctype="multipart/form-data" class="form-estilizado">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="id" value="$idVal">
        <input type="hidden" name="imagen_actual" value="$imgActual">

        <h2>Editar: $nombreVal</h2>
        
        <label>Categoría:</label>
        <select name="id_categoria">
            $optionsCategorias
        </select>

        <label>Nombre:</label> <input type="text" name="nombre" value="$nombreVal" required>
        <label>Descripción:</label> <textarea name="descripcion" rows="4" required>$descVal</textarea>
        
        <label>Precio Base (€):</label> 
        <input type="number" step="0.01" name="precio_base" id="p_base" value="$precioVal" oninput="recalcular()" required>
        
        <label>IVA:</label>
        <select name="iva" id="p_iva" onchange="recalcular()">
            <option value="4" $sel4>4%</option>
            <option value="10" $sel10>10%</option>
            <option value="21" $sel21>21%</option>
        </select>

        <p>Precio Final (con IVA): <span id="p_final">0.00 €</span></p>

        <label><input type="checkbox" name="disponible" value="1" $checkDisp> Stock disponible</label><br>
        <label><input type="checkbox" name="ofertado" value="1" $checkOfert> Ofertado en carta</label><br><br>

        <label>Imagen actual:</label><br>
        <img src="../../../../img/productos/$imgActual" width="100" style="border-radius: 8px; margin-bottom: 10px;"><br>
        <label>Cambiar imagen:</label> <input type="file" name="imagen" accept="image/*">
        
        <button type="submit">Actualizar Producto</button>
        <a href="../admin_productos.php" class="boton-cancelar">Cancelar</a>
    </form>
EOF;

// Añadimos el JS para que el precio se calcule nada más cargar la página y al cambiar valores
$contenidoAdicional = <<<EOF
    <script>
        function recalcular() {
            let base = parseFloat(document.getElementById('p_base').value) || 0;
            let iva = parseInt(document.getElementById('p_iva').value) || 0;
            let total = base + (base * (iva / 100));
            document.getElementById('p_final').innerText = total.toFixed(2) + " €";
        }
        // Ejecutar al cargar para mostrar el precio actual
        window.onload = recalcular;
    </script>
EOF;

$js = [RAIZ_APP . "/js/script.js"];

require("../../comun/plantilla.php");