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
        <h1>Gestión de Productos</h1>
        <a href="apoyo/productos_crear.php" class="boton-nuevo">Añadir Nuevo Producto</a>
        
        <table class="tabla-gestion">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Descripción</th> <th>Precio Final</th>
                    <th>Stock</th> <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($productos as $p): ?>
                <tr>
                    <td>
                        <img src="<?php echo RUTA_IMG; ?>/productos/<?php echo htmlspecialchars($p->getImagen()); ?>" 
                             width="60" style="border-radius: 8px;">
                    </td>
                    <td><?php echo htmlspecialchars($p->getNombre()); ?></td>
                    <td><?php echo htmlspecialchars($p->getNombreCategoria()); ?></td>
                    
                    <td class="col-desc">
                        <?php echo htmlspecialchars($p->getDescripcion()); ?>
                    </td>
                    
                    <td><?php echo number_format($p->getPrecioFinal(), 2); ?> €</td>
                    
                    <td>
                        <?php if($p->getDisponible()): ?>
                            <span class="badge badge-success">Disponible</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Sin Stock</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php echo $p->getOfertado() ? 'En Carta' : 'Retirado'; ?>
                    </td>
                    
                    <td>
                        <a href="apoyo/productos_actualizar.php?id=<?php echo $p->getId(); ?>" class="boton-editar">Editar</a>
                        <a href="apoyo/productos_borrar.php?id=<?php echo $p->getId(); ?>" class="boton-borrar">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>