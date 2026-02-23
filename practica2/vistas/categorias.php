<?php
// vistas/categorias.php
// Página para listar categorías (Gerente)

// Arrancamos sesión (necesario siempre)
session_start();

// Comprobación básica: usuario logueado
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../index.php");
    exit;
}

// Conexión a BD
require_once '../static/config.php';

// Consulta de categorías
$sql = "SELECT id, nombre, descripcion, activa FROM categorias ORDER BY nombre";
$resultado = mysqli_query($db_connection, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Categorías - Bistro FDI</title>
</head>
<body>

<h1>Gestión de Categorías</h1>

<p>
    <a href="categoria_crear.php">➕ Nueva categoría</a>
</p>

<table border="1" cellpadding="6">
    <tr>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Activa</th>
        <th>Acciones</th>
    </tr>

    <?php if ($resultado) { ?>
        <?php while ($fila = mysqli_fetch_assoc($resultado)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
                <td><?php echo $fila['activa'] ? 'Sí' : 'No'; ?></td>
                <td>
                    <a href="categoria_editar.php?id=<?php echo $fila['id']; ?>">Editar</a>
                    |
                    <a href="../static/categoria_toggle.php?id=<?php echo $fila['id']; ?>">
                        <?php echo $fila['activa'] ? 'Desactivar' : 'Activar'; ?>
                    </a>
                </td>
            </tr>
        <?php } ?>
    <?php } ?>
</table>

<p>
    <a href="../index.php">⬅ Volver</a>
</p>

</body>
</html>