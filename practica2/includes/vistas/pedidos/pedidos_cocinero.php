<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || !in_array($_SESSION['usuario']->rol(), ['cocinero', 'gerente'])) {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$pedidoSA = new PedidoSA($db_connection);
$pedidos = $pedidoSA->obtenerPedidosCocinero(); 

$tituloPagina = "Panel de Cocina";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

$htmlTabla = "";

if (empty($pedidos)) {
    $htmlTabla = "<p>¡Buen trabajo! No hay pedidos pendientes en cocina.</p>";
} else {
    $htmlTabla .= '<table class="tabla-gestion">
        <thead>
            <tr>
                <th>Nº Pedido</th>
                <th>Fecha/Hora</th>
                <th>Tipo</th>
                <th>Estado y Detalles</th>
            </tr>
        </thead>
        <tbody>';
        
    foreach ($pedidos as $p) {
        $estado = $p->getEstado();
        $id = $p->getId();
        $num = $p->getNumeroPedido();
        $tipo = ucfirst($p->getTipo());
        $fecha = date('H:i:s', strtotime($p->getFecha())); 
        
        $botonAccion = "";
        
        if ($estado === 'en_preparacion') {
            $botonAccion = "
                <form action='apoyo/procesar_estado_pedido.php' method='POST'>
                    <input type='hidden' name='id_pedido' value='$id'>
                    <input type='hidden' name='nuevo_estado' value='cocinando'>
                    <button type='submit' class='boton-nuevo' style='background-color:#E91E63;'>Empezar a Cocinar</button>
                </form>";
        } elseif ($estado === 'cocinando') {
            // Desplegar la lista de productos a preparar
            $lineas = $pedidoSA->obtenerDetallesPedido($id);
            $htmlLineas = "<ul style='list-style:none; padding:0; text-align:left;'>";
            
            foreach ($lineas as $linea) {
                $check = $linea['preparado'] ? "✅" : "⏳";
                $nombreProducto = htmlspecialchars($linea['nombre']);
                $htmlLineas .= "<li style='margin-bottom:8px; border-bottom:1px solid #eee; padding-bottom:5px;'>
                    $check <strong>{$linea['cantidad']}x</strong> $nombreProducto ";
                
                // Botón individual por producto
                if (!$linea['preparado']) {
                    $htmlLineas .= " <form action='apoyo/procesar_linea.php' method='POST' style='display:inline; float:right;'>
                        <input type='hidden' name='id_linea' value='{$linea['id']}'>
                        <button type='submit' class='boton-editar' style='padding:2px 8px; font-size:0.8em;'>Listo</button>
                    </form>";
                }
                $htmlLineas .= "</li>";
            }
            $htmlLineas .= "</ul>";

            // Si todos los productos tienen el check verde, mostramos el botón final
            if ($pedidoSA->sePuedeFinalizarPedido($id)) {
                $htmlLineas .= "
                <form action='apoyo/procesar_estado_pedido.php' method='POST' style='margin-top:15px;'>
                    <input type='hidden' name='id_pedido' value='$id'>
                    <input type='hidden' name='nuevo_estado' value='listo_cocina'>
                    <button type='submit' class='boton-nuevo' style='background-color:#4CAF50; width:100%;'>¡Pedido Completado!</button>
                </form>";
            }
            $botonAccion = $htmlLineas;
        }

        $estadoVisual = ucfirst(str_replace('_', ' ', $estado));

        $htmlTabla .= "<tr>
            <td><strong style='font-size: 1.4em; color: #d32f2f;'>#$num</strong></td>
            <td>$fecha</td>
            <td>$tipo</td>
            <td>
                <div style='margin-bottom: 10px;'><span class='badge'>$estadoVisual</span></div>
                $botonAccion
            </td>
        </tr>";
    }
    $htmlTabla .= "</tbody></table>";
}

$contenidoPrincipal = "<h1>👩‍🍳 Comandas de Cocina 👨‍🍳</h1>" . $htmlTabla;
require("../../comun/plantilla.php");