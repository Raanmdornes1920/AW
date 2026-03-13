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
        echo '<div class="recuadro-temporal-resumen-pedidos">';
                echo '<h2>';
                    echo 'MOSTRAR TABLA DE PEDIDOS CAMARERO';
                echo '</h2>';
        echo '</div>';
    } elseif($_SESSION['usuario']->rol() ==='cocinero') {
        // Mostrar resumen de cocinero
        echo '<div class="recuadro-temporal-resumen-pedidos">';
                echo '<h2>';
                    echo 'MOSTRAR TABLA DE PEDIDOS COCINERO';
                echo '</h2>';
        echo '</div>';
    } elseif($_SESSION['usuario']->rol() ==='gerente') {
        // Mostrar resumen de gerente
        echo '<div class="recuadro-temporal-resumen-pedidos">';
                echo '<h2>';
                    echo 'MOSTRAR TABLA DE ESTADO PEDIDOS GERENTE';
                echo '</h2>';
        echo '</div>';
    } else {
        // Mostrar Error Rol no deifinido
    }
    ?>
</article>