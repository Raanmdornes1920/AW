<?php 
session_start(); 
require_once '../static/config.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ".RAIZ_APP."/");
    exit();
}
?>
<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Carrito - BISTRO FDI</title>
        <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
        <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
    </head>
    <body>
        <!-- Header -->
        <?php include '../static/header.php'; ?>
        <!-- Header -->
        
        <!-- Contenido -->
        <main>
            
        </main>
        <!-- Contenido -->
        <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
    </body>
</html>