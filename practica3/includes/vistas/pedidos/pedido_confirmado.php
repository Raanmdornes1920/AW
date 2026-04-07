<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'cliente') {
    header("Location: " . RAIZ_APP . "/"); exit;
}

$id_pedido = $_GET['id'] ?? null;
if (!$id_pedido) {
    header("Location: ../productos/productos_cliente.php"); exit;
}

$pedidoSA = new PedidoSA($db_connection);
// NOTA: Para hacer esto perfecto, deberías añadir un método en PedidoDAO y PedidoSA llamado "obtenerPorId($id)"
// Asumiremos que lo has añadido igual que en ProductoSA.
$pedido = $pedidoSA->obtenerPorId($id_pedido); 

if (!$pedido || $pedido->getIdUsuario() !== $_SESSION['usuario']->id()) {
    header("Location: ../productos/productos_cliente.php"); exit;
}

$tituloPagina = "Pedido Confirmado";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";
$js = [(RAIZ_APP . "/js/pedidos.js"), (RAIZ_APP . "/js/script.js")];

$numero_pedido = $pedido->getNumeroPedido();
$estado = ucfirst(str_replace('_', ' ', $pedido->getEstado()));
$tipo = ucfirst($pedido->getTipo());

$contenidoPrincipal = <<<EOF
<div class="form-estilizado" style="text-align: center; margin-top: 50px;">
    <h1 style="color: #4CAF50;">¡Pedido Realizado con Éxito!</h1>
    <p style="font-size: 1.2em; margin-top: 20px;">Tu número de pedido es:</p>
    <div style="font-size: 4em; font-weight: bold; margin: 20px 0; color: #333;">#{$numero_pedido}</div>
    
    <div style="background-color: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <p><strong>Estado actual:</strong> <span class="badge badge-success">{$estado}</span></p>
        <p><strong>Tipo de consumo:</strong> {$tipo}</p>
        <p><strong>Total pagado:</strong> {$pedido->getTotal()} €</p>
    </div>
    
    <p>Puedes revisar el estado de tu pedido en tu perfil en cualquier momento.</p>
    
    <div class="acciones" style="margin-top: 30px; justify-content: center;">
        <a href="../productos/productos_cliente.php" class="boton-nuevo">Volver al inicio</a>
    </div>
</div>
EOF;

require("../comun/plantilla.php");