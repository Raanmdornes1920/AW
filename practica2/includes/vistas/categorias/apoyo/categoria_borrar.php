<?php
require_once '../../../config.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['usuario']->rol() !== 'gerente') {
    header("Location: " . RAIZ_APP . "/index.php"); exit;
}

$sa = new CategoriaSA($db_connection);
$cat = $sa->obtenerPorId($_GET['id'] ?? 0);

if (!$cat) { header("Location: ../categorias_gerente.php"); exit; }

$errorMsg = $_GET['error'] ?? null;
$nombre = htmlspecialchars($cat->getNombre());

$tituloPagina = "Eliminar Categoría";
$css = [RAIZ_APP . "/css/default.css"];
$header = "../../comun/header.php";
$claseMain = "contenedor-centro";

// PREPARACIÓN DE VARIABLES PARA EL HEREDOC
$htmlError = "";
if ($errorMsg) {
    $htmlError = "<div class='aviso-peligro'><strong>Aviso:</strong> " . htmlspecialchars($errorMsg) . "</div>";
}

// Generamos el botón solo si no hay error de productos asociados
$botonBorrar = !$errorMsg ? '<button type="submit" class="boton-peligro">Sí, eliminar definitivamente</button>' : '';

$contenidoPrincipal = <<<EOF
    <div class="alerta-borrado">
        <h2>Confirmar eliminación</h2>
        
        $htmlError

        <p>¿Estás seguro de que deseas eliminar permanentemente la categoría <strong>$nombre</strong>?</p>
        
        <div class="botones-confirmacion">
            <form action="procesar_categoria.php" method="POST" style="background:none; box-shadow:none; padding:0; width:auto; display:inline;">
                <input type="hidden" name="id" value="{$cat->getId()}">
                <input type="hidden" name="accion" value="eliminar_definitivo">
                $botonBorrar
            </form>
            <a href="../categorias_gerente.php" class="boton-cancelar">No, volver al listado</a>
        </div>
    </div>
EOF;

$js = [RAIZ_APP . "/js/script.js"];
require("../../comun/plantilla.php");