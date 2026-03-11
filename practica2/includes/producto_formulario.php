<?php
require_once 'config.php';
require_once __DIR__ . '/clases/SA/ProductoSA.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: ../../index.php"); exit;
}

$sa = new ProductoSA($db_connection);
$categorias = $sa->obtenerCategoriasActivas();

$id = $_GET['id'] ?? null;
$producto = $id ? $sa->buscarProducto($id) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $producto ? 'Editar' : 'Nuevo'; ?> Producto</title>
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
    <script>
    function recalcular() {
        let base = parseFloat(document.getElementById('p_base').value) || 0;
        let iva = parseInt(document.getElementById('p_iva').value) || 0;
        document.getElementById('p_final').innerText = (base * (1 + iva/100)).toFixed(2) + " €";
    }
    window.onload = recalcular;
    </script>
</head>
<body>
    <?php include '../vistas/comun/header.php'; ?>
    <main class="contenedor centrado">
        <form action="procesar_producto.php" method="POST" class="form-estilizado">
            <input type="hidden" name="accion" value="guardar">
            <?php if($producto): ?><input type="hidden" name="id" value="<?php echo $producto->getId(); ?>"><?php endif; ?>
            
            <h2><?php echo $producto ? 'Editar Producto' : 'Crear Producto'; ?></h2>
            
            <label>Categoría:</label>
            <select name="id_categoria" required>
                <?php foreach($categorias as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo ($producto && $producto->getIdCategoria() == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Nombre:</label> 
            <input type="text" name="nombre" value="<?php echo $producto ? htmlspecialchars($producto->getNombre()) : ''; ?>" required>
            
            <label>Descripción:</label> 
            <textarea name="descripcion" rows="4" required><?php echo $producto ? htmlspecialchars($producto->getDescripcion()) : ''; ?></textarea>
            
            <label>Precio Base:</label> 
            <input type="number" step="0.01" id="p_base" name="precio_base" oninput="recalcular()" value="<?php echo $producto ? $producto->getPrecioBase() : ''; ?>" required>
            
            <label>IVA:</label>
            <select id="p_iva" name="iva" onchange="recalcular()">
                <option value="4" <?php echo ($producto && $producto->getIva()==4)?'selected':''; ?>>4%</option>
                <option value="10" <?php echo ($producto && $producto->getIva()==10)?'selected':''; ?>>10%</option>
                <option value="21" <?php echo (!$producto || $producto->getIva()==21)?'selected':''; ?>>21%</option>
            </select>
            
            <p class="precio-destacado">Precio Final: <span id="p_final">0.00 €</span></p>

            <label><input type="checkbox" name="disponible" value="1" <?php echo (!$producto || $producto->getDisponible()) ? 'checked' : ''; ?>> Hay Stock Disponible</label><br><br>
            <?php if($producto): ?>
            <label><input type="checkbox" name="ofertado" value="1" <?php echo $producto->getOfertado() ? 'checked' : ''; ?>> Ofertado en la Carta</label><br><br>
            <?php endif; ?>

            <label>Imágenes:</label><br>
            <input type="file" name="imagenes[]" multiple accept="image/*"><br><br>

            <button type="submit">Guardar Producto</button>
            <a href="productos_gerente.php" style="margin-left:10px;">Cancelar</a>
        </form>
    </main>
    <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
</body>
</html>