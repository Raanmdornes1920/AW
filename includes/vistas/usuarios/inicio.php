<section class="py-2">
    <div class="p-4 p-lg-5 bg-white rounded shadow-sm mb-4">
        <h1 class="display-6 mb-1">Bienvenido <?php echo htmlspecialchars($_SESSION['usuario']->usuario(), ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="text-secondary mb-0">Panel principal de <?php echo htmlspecialchars($_SESSION['usuario']->rol(), ENT_QUOTES, 'UTF-8') ?>.</p>
    </div>

    <?php
    if ($_SESSION['usuario']->rol() === 'cliente') {
        echo '<div class="card shadow-sm">';
        echo '<div class="card-body p-4">';
        echo '<h2 class="h4 mb-3">¿Cómo deseas realizar tu pedido?</h2>';
        echo '<div class="row g-3">';
        echo '<div class="col-12 col-md-6"><a href="' . RAIZ_APP . '/includes/vistas/categorias/categorias_cliente.php?tipo=llevar" class="btn btn-primary btn-lg w-100 touch-action">Para llevar</a></div>';
        echo '<div class="col-12 col-md-6"><a href="' . RAIZ_APP . '/includes/vistas/categorias/categorias_cliente.php?tipo=local" class="btn btn-outline-primary btn-lg w-100 touch-action">Para consumir en el local</a></div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    } else {
        $pedidoSA = new PedidoSA($db_connection);

        if ($_SESSION['usuario']->rol() === 'camarero') {
            $pedidos = $pedidoSA->obtenerPedidosCamarero();
            $columnas = ['Nº Pedido', 'Tipo', 'Total', 'Estado Actual'];
        } elseif ($_SESSION['usuario']->rol() === 'cocinero') {
            $pedidos = $pedidoSA->obtenerPedidosCocinero();
            $columnas = ['Nº Pedido', 'Fecha/Hora', 'Tipo', 'Estado'];
        } elseif ($_SESSION['usuario']->rol() === 'gerente') {
            $pedidos = $pedidoSA->obtenerPedidosPendientes();
            $columnas = ['Nº Pedido', 'Fecha/Hora', 'ID Cliente', 'Total', 'Estado Actual'];
        } else {
            $pedidos = [];
            $columnas = [];
        }

        echo '<div class="card shadow-sm">';
        echo '<div class="card-body p-4">';
        echo '<h2 class="h4 mb-3">Pedidos pendientes</h2>';

        if (empty($pedidos)) {
            echo '<div class="alert alert-success mb-0">No hay pedidos pendientes en este momento.</div>';
        } else {
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped table-hover align-middle mb-0">';
            echo '<thead><tr>';
            foreach ($columnas as $columna) {
                echo '<th>' . htmlspecialchars($columna, ENT_QUOTES, 'UTF-8') . '</th>';
            }
            echo '</tr></thead><tbody>';

            foreach ($pedidos as $p) {
                $estado = $p->getEstado();
                $estadoVisual = ucfirst(str_replace('_', ' ', $estado));
                $num = $p->getNumeroPedido();
                $tipo = ucfirst($p->getTipo());

                echo '<tr>';
                echo '<td><strong>#' . htmlspecialchars($num, ENT_QUOTES, 'UTF-8') . '</strong></td>';

                if ($_SESSION['usuario']->rol() === 'camarero') {
                    echo '<td>' . htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . number_format($p->getTotal(), 2) . ' €</td>';
                } elseif ($_SESSION['usuario']->rol() === 'cocinero') {
                    echo '<td>' . date('H:i:s', strtotime($p->getFecha())) . '</td>';
                    echo '<td>' . htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') . '</td>';
                } elseif ($_SESSION['usuario']->rol() === 'gerente') {
                    echo '<td>' . date('d/m/Y H:i', strtotime($p->getFecha())) . '</td>';
                    echo '<td>Usuario #' . (int)$p->getIdUsuario() . '</td>';
                    echo '<td>' . number_format($p->getTotal(), 2) . ' €</td>';
                }

                echo '<td><span class="badge text-bg-secondary">' . htmlspecialchars($estadoVisual, ENT_QUOTES, 'UTF-8') . '</span></td>';
                echo '</tr>';
            }

            echo '</tbody></table></div>';
        }

        echo '</div>';
        echo '</div>';
    }
    ?>
</section>
