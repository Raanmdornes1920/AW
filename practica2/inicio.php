<!DOCTYPE html>

<html lang="es">
    <!-- Header -->
    <head>
        <meta charset="utf-8">
        <title>BISTRO FDI</title>
        <link rel="icon" type="image/png" href="img/logo1.svg">
        <link rel="stylesheet" href="css/default.css">
    </head>
    <!-- Header -->
    <body>
        <!-- Barra navegación -->
        <header class="navegacion">
            <nav aria-label="Navegación principal">
                <a class="enlace_seleccionado" href="#">Inicio</a>
            </nav>
        </header>
        <!-- Barra navegación -->
        
        <!-- Contenido -->
        <main class="contenedor-centro-index">
            <article id="contenedor-descripcion">
                <h1 id="titulo-descripcion">Bienvenido a Bistro FDI <?php echo $_SESSION['nombre']?></h1>
                
                <p id="texto-descripcion">
                    En Bistro FDI, hemos redefinido el concepto de comida rápida para aquellos que no están dispuestos a sacrificar calidad por tiempo. Nos alejamos de lo genérico para ofrecer una propuesta gastronómica donde la frescura de los ingredientes y la agilidad del servicio convergen en una experiencia superior.
                </p>
            </article>
        </main>
        <!-- Contenido -->
    </body>
</html>