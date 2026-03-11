<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new ProductoSA($db_connection);
$productos = $sa->getGestionAdmin();

$tituloPagina = "Panel de Control - Productos";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";

$filasProductos = "";
foreach($productos as $p) {
    $img = RUTA_IMG . "/productos/" . htmlspecialchars($p->getImagen());
    $nombre = htmlspecialchars($p->getNombre());
    $cat = htmlspecialchars($p->getNombreCategoria());
    $precio = number_format($p->getPrecioFinal(), 2);
    $estado = $p->getOfertado() ? 'En Carta' : 'Retirado';
    $id = $p->getId();

    $filasProductos .= <<<EOF
    <tr>
        <td><img src="$img" width="50"></td>
        <td>$nombre</td>
        <td>$cat</td>
        <td>$precio €</td>
        <td>$estado</td>
        <td>
            <a href="apoyo/producto_actualizar.php?id=$id">✏️ Editar</a>
            <a href="apoyo/procesar_producto.php?accion=borrar&id=$id" class="btn-toggle">🔄 Toggle</a>
        </td>
    </tr>
EOF;
}

$contenidoPrincipal = <<<EOF
    <h1>Gestión de Inventario</h1>
    <a href="apoyo/producto_crear.php" class="boton-nuevo">➕ Añadir Nuevo Producto</a>
    
    <table class="tabla-gestion">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            $filasProductos
        </tbody>
    </table>
EOF;

$js = [RAIZ_APP . "/js/script.js"];

require("../comun/plantilla.php");