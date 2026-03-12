<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$tituloPagina = "Nueva Categoría";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

$contenidoPrincipal = <<<EOF
    <form action="procesar_categoria.php" method="POST" enctype="multipart/form-data" class="form-estilizado">
        <input type="hidden" name="accion" value="crear">
        <h2>Crear Categoría</h2>
        
        <label>Nombre:</label> 
        <input type="text" name="nombre" required>
        
        <label>Descripción:</label> 
        <textarea name="descripcion" rows="3" required></textarea>

        <label>Imagen (Opcional):</label> 
        <input type="file" name="imagen" accept="image/*">
        
        <div class="acciones">
            <button type="submit">Guardar</button>
            <a href="../categorias_gerente.php" class="boton-borrar">Cancelar</a>
        </div>
    </form>
EOF;

$js = [RAIZ_APP . "/js/script.js"];

require("../../comun/plantilla.php");