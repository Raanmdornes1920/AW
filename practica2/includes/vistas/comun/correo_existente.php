<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro - Bistro FDI</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/ventana_mensaje.css">
</head>
<body>
    <div id="contenedor-centro">
        <p id="texto">
            <p id="texto-error">Error:</p> El correo electrónico ya está registrado.
        </p>
        <br>
        <button id="boton-volver" onclick='window.location.href="<?php echo RUTA_VISTAS; ?>/registro.php"'>Volver</button>
    </div>
</body>
</html>