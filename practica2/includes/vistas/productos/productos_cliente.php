<?php
require_once '../config.php';
require_once __DIR__ . '/../clases/SA/ProductoSA.php';
session_start();

$sa = new ProductoSA($db_connection);
$productos = $sa->getCatalogoCliente();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carta - Bistro FDI</title>
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
</head>
<body>
    <?php include 'comun/header.php'; ?>
    <main class="contenedor">
        <h1>Nuestra Carta</h1>
        <div class="grid-productos">
            <?php foreach($productos as $p): ?>
                <div class="tarjeta-producto">
                    <h3><?php echo htmlspecialchars($p->getNombre()); ?></h3>
                    <p class="precio"><?php echo number_format($p->getPrecioFinal(), 2); ?> €</p>
                    <a href="producto_detalle.php?id=<?php echo $p->getId(); ?>" class="boton-ver">Ver Detalles</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
</body>
</html>