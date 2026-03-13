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
                echo '<button onclick="window.location.href=\'' . RAIZ_APP . '/includes/vistas/categorias/categorias_cliente.php?tipo=llevar\'" class="boton-iniciar-pedido">Para llevar</button>';
                echo '<button onclick="window.location.href=\'' . RAIZ_APP . '/includes/vistas/categorias/categorias_cliente.php?tipo=local\'" class="boton-iniciar-pedido">Para consumir en el local</button>';
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