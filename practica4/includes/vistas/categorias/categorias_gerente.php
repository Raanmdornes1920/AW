<?php
require_once '../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new CategoriaSA($db_connection);
$categorias = $sa->obtenerTodas();

$tituloPagina = "Gestión de Categorías - Bistro FDI";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../comun/header.php";
$claseMain = "contenedor-centro";

$filas = "";
foreach($categorias as $cat) {
    $img = !empty($cat->getImagen()) ? RUTA_IMG . "/categorias/" . htmlspecialchars($cat->getImagen()) : RUTA_IMG . "/categorias/default_cat.png";
    $nombre = htmlspecialchars($cat->getNombre());
    $desc = htmlspecialchars($cat->getDescripcion());
    $id = $cat->getId();

    $filas .= <<<EOF
    <tr>
        <td><img src="$img" width="50" height="50" style="object-fit: cover;"></td>
        <td>$nombre</td>
        <td>$desc</td>
        <td>
            <a href="apoyo/categoria_actualizar.php?id=$id" class="boton-editar">Editar</a>
            <a href="apoyo/categoria_borrar.php?id=$id" class="boton-borrar">Eliminar</a>
        </td>
    </tr>
EOF;
}

$contenidoPrincipal = <<<EOF
    <h1>Gestión de Categorías</h1>
    <a href="apoyo/categoria_crear.php" class="boton-nuevo">Nueva Categoría</a>

    <div class="contenedor-tabla-scroll">
        <table class="tabla-gestion-categorias">
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
EOF;

$js = [RAIZ_APP . "/js/script.js"];
require("../comun/plantilla.php");