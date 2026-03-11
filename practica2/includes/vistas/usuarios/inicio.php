<article id="contenedor-descripcion">
    <h1 id="titulo-descripcion">Bienvenido <?php echo $_SESSION['usuario']->usuario()?></h1>
    <?php 
    if($_SESSION['usuario']->rol() ==='cliente'){
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
    } elseif($_SESSION['usuario']->rol() ==='camarero') {
        // Mostrar resumen de camarero
    } elseif($_SESSION['usuario']->rol() ==='cocinero') {
        // Mostrar resumen de cocinero
    } elseif($_SESSION['usuario']->rol() ==='gerente') {
        // Mostrar resumen de gerente
    } else {
        // Mostrar Error Rol no deifinido
    }
    ?>
</article>