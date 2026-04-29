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
$claseMain = "contenedor-cliente";

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
        <td data-label="Nombre">{$nombre}</td>
        <td data-label="Descripción" class="col-desc">{$desc}</td>
        <td data-label="Productos">{$productosHtml}</td>
        <td data-label="Precio Pack">{$precioSinDesc} €</td>
        <td data-label="Descuento">{$descuento}%</td>
        <td data-label="Precio Final">{$precioConDesc} €</td>
        <td data-label="Inicio">{$fechaInicio}</td>
        <td data-label="Fin">{$fechaFin}</td>
        <td data-label="Estado">{$badgeEstado}</td>
        <td data-label="Acciones">
            <a href="apoyo/oferta_actualizar.php?id={$id}" class="boton-editar">Editar</a>
            <a href="apoyo/oferta_borrar.php?id={$id}" class="boton-borrar">Eliminar</a>
        </td>
    </tr>
EOF;
}

$contenidoPrincipal = <<<EOF
    <section class="pagina-cliente pagina-admin-ofertas">
        <header class="cabecera-pagina">
            <h1>Gestión de Ofertas</h1>
            <a href="apoyo/oferta_crear.php" class="boton-nuevo">Nueva Oferta</a>
        </header>

        <section class="panel-cliente">
            <div class="contenedor-tabla-scroll">
                <table class="tabla-detalle tabla-ofertas-gerente">
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
        </section>
    </section>
EOF;

$js = [RAIZ_APP . "/js/script.js"];
require("../comun/plantilla.php");
?>
