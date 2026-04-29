<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new OfertaSA($db_connection);
$ofertas = $sa->obtenerTodas();

$tituloPagina = "Gestión de Ofertas - Bistro FDI";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";

$filas = "";
$hoy = date('Y-m-d');

foreach($ofertas as $oferta) {
    $id = $oferta->getId();
    $nombre = htmlspecialchars($oferta->getNombre());
    $desc = htmlspecialchars($oferta->getDescripcion());
    $descuento = number_format($oferta->getDescuento(), 2);
    $fechaInicio = date('d/m/Y', strtotime($oferta->getFechaInicio()));
    $fechaFin = date('d/m/Y', strtotime($oferta->getFechaFin()));
    
    // Listar productos de la oferta
    $productosHtml = "";
    foreach ($oferta->getProductos() as $p) {
        $nombreProd = htmlspecialchars($p['nombre']);
        $cantProd = $p['cantidad'];
        $productosHtml .= "<div>{$cantProd}x {$nombreProd}</div>";
    }
    if (empty($productosHtml)) {
        $productosHtml = "<em>Sin productos</em>";
    }

    // Precio del pack
    $precioSinDesc = number_format($oferta->getPrecioPackSinDescuento(), 2);
    $precioConDesc = number_format($oferta->getPrecioPackConDescuento(), 2);

    // Estado activa/caducada
    if ($oferta->estaActiva()) {
        $badgeEstado = '<span class="badge badge-success">Activa</span>';
    } elseif ($oferta->getFechaFin() < $hoy) {
        $badgeEstado = '<span class="badge badge-danger">Caducada</span>';
    } else {
        $badgeEstado = '<span class="badge">Programada</span>';
    }

    $filas .= <<<EOF
    <tr>
        <td>{$nombre}</td>
        <td class="col-desc">{$desc}</td>
        <td>{$productosHtml}</td>
        <td>{$precioSinDesc} €</td>
        <td>{$descuento}%</td>
        <td>{$precioConDesc} €</td>
        <td>{$fechaInicio}</td>
        <td>{$fechaFin}</td>
        <td>{$badgeEstado}</td>
        <td>
            <a href="apoyo/oferta_actualizar.php?id={$id}" class="boton-editar">Editar</a>
            <a href="apoyo/oferta_borrar.php?id={$id}" class="boton-borrar">Eliminar</a>
        </td>
    </tr>
EOF;
}

$contenidoPrincipal = <<<EOF
    <h1>Gestión de Ofertas</h1>
    <a href="apoyo/oferta_crear.php" class="boton-nuevo">Nueva Oferta</a>
    
    <div class="contenedor-tabla-scroll">
        <table class="tabla-gestion-productos">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Productos</th>
                    <th>Precio Pack</th>
                    <th>Descuento</th>
                    <th>Precio Final</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                {$filas}
            </tbody>
        </table>
    </div>
EOF;

$js = [RAIZ_APP . "/js/script.js"];
require("../comun/plantilla.php");
?>
