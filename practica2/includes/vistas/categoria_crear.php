<?php
require_once '../static/config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva categoría</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
    <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
</head>
<body>
<!-- Header -->
<?php include '../static/header.php'; ?>
<!-- Header -->
<section class="centrado">
    <h1 class="titulo-formulario">Nueva categoría</h1>

    <form action="../static/categoria_crear.php" method="post" enctype="multipart/form-data">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br>

        <label>Descripción:</label><br>
        <textarea name="descripcion" rows="4" cols="40" required></textarea><br>

        <label>Imagen:</label><br>
        <input type="file" name="imagen" accept="image/*"><br><br>

        <div id="botones_formulario">
        <button id="boton_aceptar" type="submit">Crear</button>
        <button id="boton_cancelar" type="button" onclick="window.location.href='categorias.php'">Cancelar</button>
        </div>
    </form>

</section>

</body>
</html>