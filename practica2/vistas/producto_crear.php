<?php
require_once '../static/config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../index.php");
    exit();
}

$sqlCat = "SELECT id, nombre FROM categorias WHERE activa = 1 ORDER BY nombre";
$categorias = mysqli_query($db_connection, $sqlCat);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Producto</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
    <script>
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
    <h1 class="titulo-formulario">Nuevo Producto</h1>

    <form action="../static/producto_crear.php" method="post" enctype="multipart/form-data">
       
        <label>Categoria:</label><br>
        <select name="id_categoria" required>
            <?php while($cat = mysqli_fetch_assoc($categorias)) { ?>
                <option value="<?php echo $cat['id']; ?>">
                    <?php echo htmlspecialchars($cat['nombre']); ?>
                </option>
            <?php } ?>
            </select><br><br>
        
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Descripción:</label><br>
        <textarea name="descripcion" rows="4" cols="40" required></textarea><br>

        <label>Precio Base:</label><br>
        <input type="number" step="0.01" id="precio_base" name="precio_base" oninput="calcularPrecioFinal()" required><br><br>

        <label>IVA:</label><br>
        <select id="iva" name="iva" onchange="calcularPrecioFinal()">
            <option value="4">4%</option>
            <option value="10">10%</option>
            <option value="21">21%</option>
        </select><br><br>

        <strong>Precio Final: <span id="precio_final">0.00 €</span></strong><br><br>

        <label>Disponible:</label>
        <input type="checkbox" name="disponible" value="1" checked><br><br>

        <label>Imágenes:</label><br>
        <input type="file" name="imagenes[]" multiple accept="image/*"><br><br>

        <button type="submit">Crear</button>
        <a href="productos.php">Cancelar</a>
    </form>

</section>

</body>
</html>