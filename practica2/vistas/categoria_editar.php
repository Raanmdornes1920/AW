<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../index.php");
    exit;
}

require_once '../static/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: categorias.php");
    exit;
}

// Buscar la categoría
$sql = "SELECT id, nombre, descripcion FROM categorias WHERE id = ?";
$stmt = mysqli_prepare($db_connection, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$categoria = mysqli_fetch_assoc($resultado);

if (!$categoria) {
    echo "Categoría no encontrada.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar categoría</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
    <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
</head>
<body>

<h1>Editar categoría</h1>

<form action="../static/categoria_editar.php" method="post">
    <input type="hidden" name="id" value="<?php echo (int)$categoria['id']; ?>">

    <label>Nombre:</label><br>
    <input type="text" name="nombre" required
           value="<?php echo htmlspecialchars($categoria['nombre']); ?>"><br><br>

    <label>Descripción:</label><br>
    <textarea name="descripcion" rows="4" cols="40"><?php
        echo htmlspecialchars($categoria['descripcion'] ?? '');
    ?></textarea><br><br>

    <button type="submit">Guardar</button>
    <a href="categorias.php">Cancelar</a>
</form>

</body>
</html>