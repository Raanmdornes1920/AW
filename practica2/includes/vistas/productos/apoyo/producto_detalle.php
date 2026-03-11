<?php
require_once '../config.php';
require_once __DIR__ . '/../clases/SA/ProductoSA.php';
session_start();

$sa = new ProductoSA($db_connection);
$producto = $sa->buscarProducto($_GET['id'] ?? 0);

if (!$producto || !$producto->getOfertado()) {
    header("Location: productos_cliente.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($producto->getNombre()); ?></title>
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
</head>
<body>
    <?php include 'comun/header.php'; ?>
    <main class="contenedor">
        <a href="productos_cliente.php">⬅ Volver a la carta</a>
        <div class="detalle-producto">
            <h1><?php echo htmlspecialchars($producto->getNombre()); ?></h1>
            <p class="categoria-etiqueta"><?php echo htmlspecialchars($producto->getNombreCategoria()); ?></p>
            <p><?php echo nl2br(htmlspecialchars($producto->getDescripcion())); ?></p>
            <h2><?php echo number_format($producto->getPrecioFinal(), 2); ?> € <small>(IVA incl.)</small></h2>
            
            <?php if($producto->getDisponible()): ?>
                <button class="boton-accion">Añadir al Carrito</button> <?php else: ?>
                <p style="color:red; font-weight:bold;">Agotado temporalmente</p>
            <?php endif; ?>
        </div>
    </main>
    <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
</body>
</html>