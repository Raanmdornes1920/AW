<?php

require_once '../config.php';
require_once __DIR__ . '/../clases/SA/ProductoSA.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: ../../index.php"); exit;
}

$sa = new ProductoSA($db_connection);
$productos = $sa->getGestionAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Productos</title>
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
</head>
<body>
    <?php include '../vistas/comun/header.php'; ?>
    <main class="contenedor-centro">
        <h1>Gestión de Productos</h1>
        <a href="../producto_formulario.php" class="boton-accion">➕ Añadir Producto</a>
        
        <table class="tabla-admin">
            <tr><th>Nombre</th><th>Categoría</th><th>Precio Final</th><th>Stock</th><th>Ofertado</th><th>Acciones</th></tr>
            <?php foreach($productos as $p): ?>
            <tr>
                <td><?php echo htmlspecialchars($p->getNombre()); ?></td>
                <td><?php echo htmlspecialchars($p->getNombreCategoria()); ?></td>
                <td><?php echo number_format($p->getPrecioFinal(), 2); ?> €</td>
                <td><?php echo $p->getDisponible() ? 'Sí' : 'No'; ?></td>
                <td><?php echo $p->getOfertado() ? 'En Carta' : 'Retirado'; ?></td>
                <td>
                    <a href="../producto_formulario.php?id=<?php echo $p->getId(); ?>">✏️ Editar</a> | 
                    <a href="procesar_producto.php?accion=toggle&id=<?php echo $p->getId(); ?>">🔄 Cambiar Estado</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </main>
    <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
</body>
</html>