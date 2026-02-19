<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>BISTRO FDI</title>
        <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
        <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
    </head>
    <body>
        <!-- Header -->
        <?php include 'static/header.php'; ?>
        <!-- Header -->
        
        <!-- Contenido -->
        <main class="contenedor-centro-index">
            <article id="contenedor-descripcion">
                <h1 id="titulo-descripcion">Bienvenido <?php echo $_SESSION['nombre']?></h1>
                <p id="texto-descripcion">
                    Esto es una prueba de como se vería la pagina de inicio
                </p>
            </article>
        </main>
        <!-- Contenido -->
    </body>
</html>