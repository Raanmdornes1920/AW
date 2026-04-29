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
<<<<<<< HEAD
$claseMain = "contenedor-centro";
=======
$claseMain = "contenedor-fullwidth";
>>>>>>> angela
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

// Variables para separar en dos vistas/tablas distintas
$htmlCobros = "";
$htmlPreparacion = "";

foreach ($pedidos as $p) {
    $estado = $p->getEstado();
    $id = $p->getId();
    $num = $p->getNumeroPedido();
    $tipo = ucfirst($p->getTipo());
    $total = number_format($p->getTotal(), 2);
    $estadoVisual = ucfirst(str_replace('_', ' ', $estado));

    if ($estado === 'recibido') {
        // TABLA 1: COBROS PENDIENTES
        $botonAccion = "
<<<<<<< HEAD
=======
            <a href='pedido_detalle.php?id={$id}' class='boton-editar' style='display:block; text-align:center; margin-bottom:8px;'>Ver detalle</a>
>>>>>>> angela
            <form action='apoyo/procesar_estado_pedido.php' method='POST'>
                <input type='hidden' name='id_pedido' value='$id'>
                <input type='hidden' name='nuevo_estado' value='en_preparacion'>
                <button type='submit' class='boton-nuevo' style='background-color:#FF9800;'>Cobrar y Enviar a Cocina</button>
            </form>";

        $htmlCobros .= "<tr>
            <td data-label='Nº Pedido'><strong style='font-size: 1.2em;'>#$num</strong></td>
            <td data-label='Tipo'>$tipo</td>
            <td data-label='Total'>$total €</td>
            <td data-label='Estado'><span class='badge'>$estadoVisual</span></td>
            <td data-label='Acción'>$botonAccion</td>
        </tr>";
    } elseif ($estado === 'listo_cocina' || $estado === 'terminado') {
<<<<<<< HEAD
        // TABLA 2: PREPARAR BANDEJA Y ENTREGAR
        
        // Obtenemos las líneas para verificar si el camarero tiene que añadir bebidas
        $lineas = $pedidoSA->obtenerDetallesPedido($id);
        $listaProductos = "<ul style='list-style:none; padding:0; text-align:left; margin:0;'>";
        
        $todasListas = true; // Variable para controlar si habilitamos el botón final

        foreach ($lineas as $linea) {
            $check = $linea['preparado'] ? "✅" : "⏳";
            $nombreProducto = htmlspecialchars($linea['nombre']);
            
            // Si hay algo sin preparar, la bandeja aún no está lista
            if (!$linea['preparado']) {
                $todasListas = false;
            }

            $listaProductos .= "<li style='padding-bottom:8px; border-bottom: 1px solid #eee; margin-bottom: 5px;'>
                $check <strong>{$linea['cantidad']}x</strong> $nombreProducto";

            // LÓGICA DE BEBIDAS: Si no está listo y NO es de cocina, el camarero lo prepara aquí
            if (!$linea['preparado'] && isset($linea['cocinable']) && $linea['cocinable'] == 0) {
                $listaProductos .= " <form action='apoyo/procesar_linea.php' method='POST' style='display:inline; float:right;'>
                    <input type='hidden' name='id_linea' value='{$linea['id']}'>
                    <button type='submit' class='boton-editar' style='padding:2px 8px; font-size:0.8em; background-color:#2196F3; color:white; border:none; border-radius:4px;'>Servir</button>
                </form>";
            }
            
            $listaProductos .= "</li>";
        }
        $listaProductos .= "</ul>";

        $botonAccion = "";
        if ($estado === 'listo_cocina') {
            // SOLO mostramos el botón si el camarero ha marcado las bebidas como listas
            if ($todasListas) {
                $botonAccion = "
                    <form action='apoyo/procesar_estado_pedido.php' method='POST'>
                        <input type='hidden' name='id_pedido' value='$id'>
                        <input type='hidden' name='nuevo_estado' value='terminado'>
                        <button type='submit' class='boton-editar' style='width: 100%; background-color: #4CAF50;'>Bandeja Lista</button>
                    </form>";
            } else {
                $botonAccion = "<div style='background:#fff3e0; padding:10px; border-radius:5px; border:1px solid #ffe0b2; font-size:0.85em; color:#e65100; text-align:center;'>
                    <strong>Pendiente:</strong> Añade los productos de barra (bebidas) para completar la bandeja.
                </div>";
            }
        } else { // estado === 'terminado'
            $botonAccion = "
                <form action='apoyo/procesar_estado_pedido.php' method='POST'>
                    <input type='hidden' name='id_pedido' value='$id'>
                    <input type='hidden' name='nuevo_estado' value='entregado'>
                    <button type='submit' class='boton-nuevo' style='width: 100%;'>Entregar Cliente</button>
                </form>";
        }
=======
        // TABLA 2: la bandeja se gestiona en la vista separada de detalle.
        $botonAccion = "<a href='pedido_detalle.php?id={$id}' class='boton-nuevo' style='display:block; text-align:center;'>Gestionar detalle</a>";
>>>>>>> angela

        $htmlPreparacion .= "<tr>
            <td data-label='Nº Pedido' style='vertical-align: top;'><strong style='font-size: 1.2em;'>#$num</strong></td>
            <td data-label='Estado/Tipo' style='vertical-align: top;'>$tipo<br><br><span class='badge'>$estadoVisual</span></td>
<<<<<<< HEAD
            <td data-label='Detalles' style='vertical-align: top;'>$listaProductos</td>
=======
>>>>>>> angela
            <td data-label='Acción' style='vertical-align: middle;'>$botonAccion</td>
        </tr>";
    }
}

// Componer HTML final de las tablas controlando si están vacías
$tablaCobrosFinal = empty($htmlCobros) ? "<p style='text-align:center; padding:20px;'>No hay pedidos pendientes de cobro.</p>" : "
    <table class='tabla-gestion'>
        <thead><tr><th>Nº Pedido</th><th>Tipo</th><th>Total</th><th>Estado</th><th>Acción</th></tr></thead>
        <tbody>$htmlCobros</tbody>
    </table>";

$tablaPreparacionFinal = empty($htmlPreparacion) ? "<p style='text-align:center; padding:20px;'>No hay pedidos pendientes de entrega.</p>" : "
    <table class='tabla-gestion'>
<<<<<<< HEAD
        <thead><tr><th>Nº Pedido</th><th>Tipo/Estado</th><th>Detalles del Pedido (Bebidas, etc.)</th><th>Acción</th></tr></thead>
=======
        <thead><tr><th>Nº Pedido</th><th>Tipo/Estado</th><th>Acción</th></tr></thead>
>>>>>>> angela
        <tbody>$htmlPreparacion</tbody>
    </table>";

$contenidoPrincipal = <<<EOF
    <h1 style="text-align: center; margin-bottom: 5px;">Gestión de Camareros</h1>
    <p style="text-align: center; color: #666; margin-bottom: 30px;">Recuerda recargar la página periódicamente para ver nuevos pedidos.</p>
    
    <h2 style="color: #FF9800; border-bottom: 2px solid #FF9800; padding-bottom: 5px;">1. Nuevos (Cobro y envío a Cocina)</h2>
    $tablaCobrosFinal

    <h2 style="margin-top: 40px; color: #4CAF50; border-bottom: 2px solid #4CAF50; padding-bottom: 5px;">2. Entregas (Bandejas y Bolsas)</h2>
    $tablaPreparacionFinal
EOF;

require("../comun/plantilla.php");
<<<<<<< HEAD
?>
=======
?>
>>>>>>> angela
