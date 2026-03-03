<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>BISTRO FDI</title>
        <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
        <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
        <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
    </head>
    <body>
        <!-- Header -->
        <?php include 'static/header.php'; ?>
        <!-- Header -->
        
        <!-- Contenido -->
        <main class="contenedor-centro-index">
            <article id="contenedor-descripcion">
                <h1 id="titulo-descripcion">Bienvenido <?php echo $_SESSION['usuario']?></h1>
                <?php 
                if($_SESSION['rol']==='cliente'){
                    echo '<div class="recuadro-iniciar-pedido">';
                        echo '<div class="titulo-iniciar-pedido">';
                            echo '<h2>';
                                echo '¿Como deseas realizar tu pedido?';
                            echo '</h2>';
                        echo '</div>';

                        echo '<div class="contenedor-botones-iniciar-pedido">';
                            echo '<button class="boton-iniciar-pedido">Para llevar</button>';
                            echo '<button class="boton-iniciar-pedido">Para consumir en el local</button>';
                        echo '</div>';
                    echo '</div>';
                } elseif($_SESSION['rol']==='camarero') {
                    // Mostrar resumen de camarero
                } elseif($_SESSION['rol']==='cocinero') {
                    // Mostrar resumen de cocinero
                } elseif($_SESSION['rol']==='gerente') {
                    // Mostrar resumen de gerente
                } else {
                    // Mostrar Error Rol no deifinido
                }
                ?>
            </article>
        </main>
        <!-- Contenido -->
    </body>
</html>