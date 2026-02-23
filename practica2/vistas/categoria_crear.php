<?php
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
</head>
<body>

<h1>Nueva categoría</h1>

<form action="../static/categoria_crear.php" method="post">
    <label>Nombre:</label><br>
    <input type="text" name="nombre" required><br><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion" rows="4" cols="40"></textarea><br><br>

    <button type="submit">Crear</button>
    <a href="categorias.php">Cancelar</a>
</form>

</body>
</html>