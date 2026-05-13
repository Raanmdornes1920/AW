<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php");
    exit;
}

$sa = new ProductoSA($db_connection);
$productos = $sa->getGestionAdmin();

$tituloPagina = "Gestión de Productos - Bistro FDI";
$css = [];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";

$filas = "";
foreach($productos as $p) {
    $img = RUTA_IMG . "/productos/" . htmlspecialchars($p->getImagen());
    $nombre = htmlspecialchars($p->getNombre());
    $cat = htmlspecialchars($p->getNombreCategoria());
    $desc = htmlspecialchars($p->getDescripcion());
    $precio = number_format($p->getPrecioFinal(), 2);
    $id = $p->getId();

    $badgeStock = $p->getDisponible()
        ? '<span class="badge text-bg-success">Disponible</span>'
        : '<span class="badge text-bg-danger">Sin stock</span>';

    $estado = $p->getOfertado() ? '<span class="badge text-bg-primary">En carta</span>' : '<span class="badge text-bg-secondary">Retirado</span>';

    // NUEVO CAMPO: Comprobamos si el producto es cocinable para ponerle un icono
    $esCocina = $p->getCocinable() ? "Sí" : "No";

    $filas .= <<<EOF
    <tr>
        <td><img src="$img" class="rounded table-img" alt="$nombre"></td>
        <td>$nombre</td>
        <td>$cat</td>
        <td>$esCocina</td>
        <td class="text-truncate" style="max-width: 260px;">$desc</td>
        <td>$precio €</td>
        <td>$badgeStock</td>
        <td>$estado</td>
        <td>
            <div class="btn-group btn-group-sm" role="group">
                <a href="apoyo/productos_actualizar.php?id=$id" class="btn btn-outline-primary">Editar</a>
                <a href="apoyo/productos_borrar.php?id=$id" class="btn btn-outline-danger">Retirar</a>
            </div>
        </td>
    </tr>
EOF;
}

$contenidoPrincipal = <<<EOF
    <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 align-items-sm-center mb-4">
        <h1 class="h2 mb-0">Gestión de Productos</h1>
        <a href="apoyo/productos_crear.php" class="btn btn-success">Nuevo Producto</a>
    </div>

    <div class="card shadow-sm">
    <div class="card-body">
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Cocina</th> <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                $filas
            </tbody>
        </table>
    </div>
    </div>
    </div>
EOF;

$js = [RAIZ_APP . "/js/script.js"];
require("../comun/plantilla.php");
?>
