<?php
session_start();
require_once '../static/config.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../index.php");
    exit();
}

$sql = "SELECT p.id, p.nombre, p.descripcion, p.precio_base, p.iva, p.disponible, p.ofertado, c.nombre AS categoria
        FROM productos p
        JOIN categorias c ON p.id_categoria = c.id
        ORDER BY p.nombre";
$resultado = mysqli_query($db_connection, $sql); 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos - Bistro FDI</title>
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
    <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
</head>
<body>

    <?php include '../static/header.php'; ?>
    
    <h1>Gestión de Productos</h1>
    <p>
        <a href="producto_crear.php">➕ Nuevo producto</a>
    </p>

    <table border="1" cellpadding="6">
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Categoría</th>
            <th>Precio Final</th>
            <th>Disponible</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>

        <?php while ($fila = mysqli_fetch_assoc($resultado)):
            $precioFinal = $fila['precio_base'] * (1 + $fila['iva']/100);
        ?>
        <tr>
            <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
            <td><?php echo htmlspecialchars($fila['descripcion']); ?></td>
            <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
            <td><?php echo number_format($precioFinal,2); ?> €</td>
            <td><?php echo $fila['disponible'] ? 'Sí' : 'No'; ?></td>
            <td>
                <?php if ($fila['ofertado']): ?>
                    <span style="color: green; font-weight: bold;">Ofertado</span>
                <?php else: ?>
                    <span style="color: red; font-weight: bold;">Retirado</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="producto_editar.php?id=<?php echo $fila['id']; ?>">Editar</a> |
                <a href="../static/producto_toggle.php?id=<?php echo $fila['id']; ?>">
                    <?php echo $fila['ofertado'] ? 'Retirar' : 'Ofertar'; ?>
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p>
        <a href="../index.php">⬅ Volver</a>
    </p>
</body>
</html>