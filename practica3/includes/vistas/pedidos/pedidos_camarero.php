<?php
require_once '../../config.php';
session_start();

// Validamos rol (asumimos que gerente también puede verlo por jerarquía)
if (!isset($_SESSION['login']) || !in_array($_SESSION['usuario']->rol(), ['camarero', 'gerente'])) {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$pedidoSA = new PedidoSA($db_connection);
// Obtenemos solo los pedidos que importan al camarero (recibidos, listos de cocina, y terminados)
$pedidos = $pedidoSA->obtenerPedidosCamarero(); 

$tituloPagina = "Panel de Camarero";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

$htmlTabla = "";

if (empty($pedidos)) {
    $htmlTabla = "<p>No hay pedidos pendientes para los camareros en este momento.</p>";
} else {
    $htmlTabla .= '<table class="tabla-gestion">
        <thead>
            <tr>
                <th>Nº Pedido</th>
                <th>Tipo</th>
                <th>Total</th>
                <th>Estado Actual</th>
                <th>Acción Camarero</th>
            </tr>
        </thead>
        <tbody>';
        
    foreach ($pedidos as $p) {
        $estado = $p->getEstado();
        $id = $p->getId();
        $num = $p->getNumeroPedido();
        $tipo = ucfirst($p->getTipo());
        $total = number_format($p->getTotal(), 2);
        
        // Lógica de botones según el enunciado
        $botonAccion = "";
        
        if ($estado === 'recibido') {
            // El cliente quiere pagar al camarero
            $botonAccion = "
                <form action='apoyo/procesar_estado_pedido.php' method='POST'>
                    <input type='hidden' name='id_pedido' value='$id'>
                    <input type='hidden' name='nuevo_estado' value='en_preparacion'>
                    <button type='submit' class='boton-nuevo' style='background-color:#FF9800;'>Cobrar y Enviar a Cocina</button>
                </form>";
        } elseif ($estado === 'listo_cocina') {
            // Cocina ha terminado, camarero añade bebidas/prepara bolsa
            $botonAccion = "
                <form action='apoyo/procesar_estado_pedido.php' method='POST'>
                    <input type='hidden' name='id_pedido' value='$id'>
                    <input type='hidden' name='nuevo_estado' value='terminado'>
                    <button type='submit' class='boton-editar'>Preparar Bandeja/Bolsa</button>
                </form>";
        } elseif ($estado === 'terminado') {
            // Listo para dar al cliente
            $botonAccion = "
                <form action='apoyo/procesar_estado_pedido.php' method='POST'>
                    <input type='hidden' name='id_pedido' value='$id'>
                    <input type='hidden' name='nuevo_estado' value='entregado'>
                    <button type='submit' class='boton-nuevo'>Entregar al Cliente</button>
                </form>";
        }

        $estadoVisual = ucfirst(str_replace('_', ' ', $estado));

        $htmlTabla .= "<tr>
            <td><strong style='font-size: 1.2em;'>#$num</strong></td>
            <td>$tipo</td>
            <td>$total €</td>
            <td><span class='badge'>$estadoVisual</span></td>
            <td>$botonAccion</td>
        </tr>";
    }
    $htmlTabla .= "</tbody></table>";
}

$contenidoPrincipal = <<<EOF
    <h1>Gestión de Pedidos - Camarero</h1>
    <p>Recuerda recargar la página periódicamente para ver nuevos pedidos.</p>
    $htmlTabla
EOF;

require("../comun/plantilla.php");