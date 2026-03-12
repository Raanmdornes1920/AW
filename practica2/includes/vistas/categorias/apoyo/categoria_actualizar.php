<?php
require_once '../../../config.php';

session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new CategoriaSA($db_connection);
$cat = $sa->obtenerPorId($_GET['id'] ?? 0);

if (!$cat) { header("Location: ../categorias_gerente.php"); exit; }

$tituloPagina = "Editar Categoría";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

$nombre = htmlspecialchars($cat->getNombre());
$desc = htmlspecialchars($cat->getDescripcion());
$imgActual = htmlspecialchars($cat->getImagen());
$id = $cat->getId();

$rutaImg = "../../../img/categorias/" . $imgActual;

$contenidoPrincipal = <<<EOF
    <form action="procesar_categoria.php" method="POST" enctype="multipart/form-data" class="form-estilizado">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="id" value="$id">
        <input type="hidden" name="imagen_actual" value="$imgActual">

        <h2>Editar: $nombre</h2>
        
        <label>Nombre:</label> 
        <input type="text" name="nombre" value="$nombre" required>
        
        <label>Descripción:</label> 
        <textarea name="descripcion" rows="3" required>$desc</textarea>
        
        <label>Cambiar imagen:</label> 
        <input type="file" name="imagen" accept="image/*">
        
        <div class="acciones">
            <button type="submit">Actualizar</button>
            <a href="../categorias_gerente.php" class="boton-borrar">Cancelar</a>
        </div>
    </form>
EOF;

$js = [RAIZ_APP . "/js/script.js"];

require("../../comun/plantilla.php");