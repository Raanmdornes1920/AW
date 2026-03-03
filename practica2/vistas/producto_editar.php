<?php
session_start();
require_once '../static/config.php';

if (!isset($_SESSION['login']) || $_SESSION['rol'] !== 'gerente') {
    header("Location: ../index.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: productos.php");
    exit;
}

$sql = "SELECT * FROM productos WHERE id=?";
$stmt = mysqli_prepare($db_connection, $sql);
mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$producto = mysqli_fetch_assoc($resultado);

if (!$producto) {
    echo "Producto no encontrado.";
    exit;
}

$sqlCat = "SELECT id, nombre FROM categorias WHERE activa=1 ORDER BY nombre";
$categorias = mysqli_query($db_connection, $sqlCat);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
    <script> // TODO Mover a script.js
    function calcularPrecioFinal() {
        let base = parseFloat(document.getElementById("precio_base").value) || 0;
        let iva = parseInt(document.getElementById("iva").value) || 0;
        let total = base + (base * iva / 100);
        document.getElementById("precio_final").innerText = total.toFixed(2) + " €";
    }
    </script>
</head>
<body>

<?php include '../static/header.php'; ?>

<section class="centrado">
<h1 class="titulo-formulario">Editar Producto</h1>

<form action="../static/producto_editar.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">

    <label>Categoría:</label><br>
    <select name="id_categoria" required>
        <?php while($cat = mysqli_fetch_assoc($categorias)): ?>
            <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id']==$producto['id_categoria']?'selected':''; ?>>
                <?php echo htmlspecialchars($cat['nombre']); ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Nombre:</label><br>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required><br><br>

    <label>Descripción:</label><br>
    <!-- Cambiamos para interpretar saltos de linea -->
    <textarea name="descripcion" rows="4" cols="40" required><?php echo htmlspecialchars(str_replace('\r\n', "\n", $producto['descripcion'])); ?></textarea><br><br>
    <!-- <textarea name="descripcion" rows="4" cols="40" required><?php //echo htmlspecialchars($producto['descripcion']); ?></textarea><br><br> -->

    <label>Precio Base:</label><br>
    <input type="number" step="0.01" id="precio_base" name="precio_base"
           value="<?php echo $producto['precio_base']; ?>" oninput="calcularPrecioFinal()" required><br><br>

    <label>IVA:</label><br>
    <select id="iva" name="iva" onchange="calcularPrecioFinal()">
        <option value="4" <?php echo $producto['iva']==4?'selected':''; ?>>4%</option>
        <option value="10" <?php echo $producto['iva']==10?'selected':''; ?>>10%</option>
        <option value="21" <?php echo $producto['iva']==21?'selected':''; ?>>21%</option>
    </select><br><br>

    <strong>Precio Final: <span id="precio_final"><?php echo number_format($producto['precio_base']*(1+$producto['iva']/100),2); ?> €</span></strong><br><br>

    <label>Disponible:</label>
    <input type="checkbox" name="disponible" value="1" <?php echo $producto['disponible']?'checked':''; ?>><br><br>

    <label>Ofertado:</label>
    <input type="checkbox" name="ofertado" value="1" <?php echo $producto['ofertado']?'checked':''; ?>><br><br>

    <button type="submit">Guardar</button>
    <a href="productos.php">Cancelar</a>
</form>
</section>

</body>
</html>