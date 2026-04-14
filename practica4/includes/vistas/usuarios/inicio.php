<article id="contenedor-descripcion">
    <h1 id="titulo-descripcion">Bienvenido <?php echo $_SESSION['usuario']->usuario() ?></h1>
    <?php
    if ($_SESSION['usuario']->rol() === 'cliente') {
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
    } elseif ($_SESSION['usuario']->rol() === 'camarero') {
        // Mostrar resumen de camarero
        $pedidoSA = new PedidoSA($db_connection);
        $pedidos = $pedidoSA->obtenerPedidosCamarero();
        echo '<div class="recuadro-temporal-resumen-pedidos">';
        echo '<h2>Pedidos pendientes</h2>';
        $htmlTabla = "";
        if (empty($pedidos)) {
            $htmlTabla = "<p>No hay pedidos pendientes para los camareros en este momento.</p>";
        } else {
            $htmlTabla .= '<table class="tabla-gestion-usuario">
                <thead>
                    <tr>
                        <th>Nº Pedido</th>
                        <th>Tipo</th>
                        <th>Total</th>
                        <th>Estado Actual</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($pedidos as $p) {
                $estado = $p->getEstado();
                $id = $p->getId();
                $num = $p->getNumeroPedido();
                $tipo = ucfirst($p->getTipo());
                $total = number_format($p->getTotal(), 2);

                $estadoVisual = ucfirst(str_replace('_', ' ', $estado));

                $htmlTabla .= "<tr>
                    <td><strong style='font-size: 1.2em;'>#$num</strong></td>
                    <td>$tipo</td>
                    <td>$total €</td>
                    <td><span class='badge'>$estadoVisual</span></td>
                </tr>";
            }
            $htmlTabla .= "</tbody></table>";
        }
        echo $htmlTabla;
        echo '</div>';
    } elseif ($_SESSION['usuario']->rol() === 'cocinero') {
        // Mostrar resumen de cocinero
        echo '<div class="recuadro-temporal-resumen-pedidos">';
        echo '<h2>Pedidos pendientes</h2>';
        $pedidoSA = new PedidoSA($db_connection);
        $pedidos = $pedidoSA->obtenerPedidosCocinero();
        $htmlTabla = "";

        if (empty($pedidos)) {
            $htmlTabla = "<p>¡Buen trabajo! No hay pedidos pendientes en cocina.</p>";
        } else {
            $htmlTabla .= '<table class="tabla-gestion-usuario">
                    <thead>
                        <tr>
                            <th>Nº Pedido</th>
                            <th>Fecha/Hora</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($pedidos as $p) {
                $estado = $p->getEstado();
                $id = $p->getId();
                $num = $p->getNumeroPedido();
                $tipo = ucfirst($p->getTipo());
                $fecha = date('H:i:s', strtotime($p->getFecha()));

                $estadoVisual = ucfirst(str_replace('_', ' ', $estado));

                $htmlTabla .= "<tr>
                        <td><strong style='font-size: 1.4em; color: #d32f2f;'>#$num</strong></td>
                        <td>$fecha</td>
                        <td>$tipo</td>
                        <td>
                            <div style='margin-bottom: 10px;'><span class='badge'>$estadoVisual</span></div>
                        </td>
                    </tr>";
            }
            $htmlTabla .= "</tbody></table>";
        }
        echo $htmlTabla;
        echo '</div>';
    } elseif ($_SESSION['usuario']->rol() === 'gerente') {
        // Mostrar resumen de gerente
        echo '<div class="recuadro-temporal-resumen-pedidos">';
        echo '<h2>Pedidos pendientes</h2>';
        $pedidoSA = new PedidoSA($db_connection);
        $pedidos = $pedidoSA->obtenerPedidosPendientes();
        $htmlTabla = "";

        if (empty($pedidos)) {
            $htmlTabla = "<p>No hay pedidos pendientes en el restaurante. Todo está tranquilo.</p>";
        } else {
            $htmlTabla .= '<table class="tabla-gestion-usuario">
                    <thead>
                        <tr>
                            <th>Nº Pedido</th>
                            <th>Fecha/Hora</th>
                            <th>ID Cliente</th>
                            <th>Total</th>
                            <th>Estado Actual</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($pedidos as $p) {
                $estado = $p->getEstado();
                $id = $p->getId();
                $num = $p->getNumeroPedido();
                $fecha = date('d/m/Y H:i', strtotime($p->getFecha()));
                $total = number_format($p->getTotal(), 2);
                $idCliente = $p->getIdUsuario();

                $estadoVisual = ucfirst(str_replace('_', ' ', $estado));

                $htmlTabla .= "<tr>
                        <td><strong>#$num</strong></td>
                        <td>$fecha</td>
                        <td>Usuario #$idCliente</td>
                        <td>$total €</td>
                        <td><span class='badge'>$estadoVisual</span></td>
                    </tr>";
            }
            $htmlTabla .= "</tbody></table>";
        }
        echo $htmlTabla;
        echo '</div>';
    } else {
        // Mostrar Error Rol no deifinido
    }
    ?>
</article>