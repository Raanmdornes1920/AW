<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new ProductoSA($db_connection);
$productos = $sa->getGestionAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Productos</title>
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
</head>
<body>
    <?php include '../comun/header.php'; ?>
    <main class="contenedor-centro">
        <h1>Gestión de Inventario</h1>
        <a href="apoyo/productos_crear.php" class="boton-nuevo">Añadir Nuevo Producto</a>
        
        <table class="tabla-gestion">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($productos as $p): ?>
                <tr>
                    <td><img src="<?php echo RUTA_IMG; ?>/productos/<?php echo htmlspecialchars($p->getImagen()); ?>" width="50"></td>
                    <td><?php echo htmlspecialchars($p->getNombre()); ?></td>
                    <td><?php echo htmlspecialchars($p->getNombreCategoria()); ?></td>
                    <td><?php echo number_format($p->getPrecioFinal(), 2); ?> €</td>
                    <td><?php echo $p->getOfertado() ? 'En Carta' : 'Retirado'; ?></td>
                    <td>
                        <a href="apoyo/productos_actualizar.php?id=<?php echo $p->getId(); ?>">Editar</a>
                        <a href="apoyo/procesar_producto.php?accion=borrar&id=<?php echo $p->getId(); ?>" class="btn-toggle">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
    <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
</body>
</html>