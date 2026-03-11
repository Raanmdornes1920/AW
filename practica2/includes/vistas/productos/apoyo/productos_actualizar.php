<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new ProductoSA($db_connection);
$producto = $sa->buscarProducto($_GET['id'] ?? 0);
$categorias = $sa->obtenerCategoriasActivas();

if (!$producto) { header("Location: ../admin_productos.php"); exit; }

$tituloPagina = "Actualizar Producto";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

$optionsCategorias = "";
foreach($categorias as $cat) {
    $selected = ($producto->getIdCategoria() == $cat['id']) ? 'selected' : '';
    $optionsCategorias .= '<option value="'.$cat['id'].'" '.$selected.'>'.htmlspecialchars($cat['nombre']).'</option>';
}

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
        <label>Precio Base:</label> <input type="number" step="0.01" name="precio_base" value="$precioVal" required>
        
        <label><input type="checkbox" name="disponible" value="1" $checkDisp> Stock disponible</label><br>
        <label><input type="checkbox" name="ofertado" value="1" $checkOfert> Ofertado en carta</label><br><br>

        <label>Imagen actual:</label><br>
        <img src="../../img/productos/$imgActual" width="100"><br>
        <label>Cambiar imagen:</label> <input type="file" name="imagen" accept="image/*">
        
        <button type="submit">Actualizar</button>
        <a href="../admin_productos.php">Cancelar</a>
    </form>
EOF;

$js = [RAIZ_APP . "/js/script.js"];

require("../../comun/plantilla.php");