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
    $estado = $cat->getActiva() ? 'Activa' : 'Inactiva';

    $filas .= <<<EOF
    <tr>
        <td><img src="$img" width="50" height="50" style="object-fit: cover;"></td>
        <td>$nombre</td>
        <td>$desc</td>
        <td>$estado</td>
        <td>
            <a href="apoyo/categoria_actualizar.php?id=$id">Editar</a>
            <a href="apoyo/procesar_categoria.php?accion=borrar&id=$id" class="btn-toggle">Eliminar</a>
        </td>
    </tr>
EOF;
}

$contenidoPrincipal = <<<EOF
    <h1>Gestión de Categorías</h1>
    <a href="apoyo/categoria_crear.php" class="boton-nuevo">Nueva Categoría</a>
    
    <table class="tabla-gestion">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            $filas
        </tbody>
    </table>
EOF;

$js = [RAIZ_APP . "/js/script.js"];
require("../comun/plantilla.php");