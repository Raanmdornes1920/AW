<?php
require_once __DIR__ . '/../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new ProductoSA($db_connection);
$producto = $sa->buscarProducto($_GET['id'] ?? 0);

if (!$producto) { 
    header("Location: ../productos_gerente.php"); 
    exit; 
}

$tituloPagina = "Retirar Producto";
$css = [];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

$js = [RAIZ_APP . "/js/script.js"];

$nombre = htmlspecialchars($producto->getNombre());

$contenidoPrincipal = <<<EOF
    <div class="alerta-borrado">
        <h2>Confirmar Retirada de Carta</h2>
        <p>¿Estás seguro de que deseas retirar el producto <strong>$nombre</strong> de la carta?</p>
        <p><small>Nota: El producto dejará de estar visible para los clientes pero se mantendrá en el historial del inventario.</small></p>
        
        <div class="botones-confirmacion">
            <form action="procesar_producto.php" method="POST" style="display: inline;">
                <input type="hidden" name="id" value="{$producto->getId()}">
                <input type="hidden" name="accion" value="borrar">
                <button type="submit" class="boton-peligro">Sí, retirar de la carta</button>
            </form>
            <a href="../productos_gerente.php" class="boton-cancelar">No, cancelar</a>
        </div>
    </div>
EOF;

require("../../comun/plantilla.php");