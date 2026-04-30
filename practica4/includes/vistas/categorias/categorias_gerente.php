<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new CategoriaSA($db_connection);
$categorias = $sa->obtenerTodas();

$tituloPagina = "Gestión de Categorías - Bistro FDI";
$css = [];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";

$filas = "";
foreach($categorias as $cat) {
    $img = !empty($cat->getImagen()) ? RUTA_IMG . "/categorias/" . htmlspecialchars($cat->getImagen()) : RUTA_IMG . "/categorias/categoria_default.jpg";
    $nombre = htmlspecialchars($cat->getNombre());
    $desc = htmlspecialchars($cat->getDescripcion());
    $id = $cat->getId();

    $filas .= <<<EOF
    <tr>
        <td><img src="$img" class="rounded table-img" alt="$nombre"></td>
        <td>$nombre</td>
        <td>$desc</td>
        <td>
            <div class="btn-group btn-group-sm" role="group">
                <a href="apoyo/categoria_actualizar.php?id=$id" class="btn btn-outline-primary">Editar</a>
                <a href="apoyo/categoria_borrar.php?id=$id" class="btn btn-outline-danger">Eliminar</a>
            </div>
        </td>
    </tr>
EOF;
}

$contenidoPrincipal = <<<EOF
    <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 align-items-sm-center mb-4">
        <h1 class="h2 mb-0">Gestión de Categorías</h1>
        <a href="apoyo/categoria_crear.php" class="btn btn-success">Nueva Categoría</a>
    </div>

    <div class="card shadow-sm">
    <div class="card-body">
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
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
