<?php
require_once 'config.php';

$pagina_actual = basename($_SERVER['PHP_SELF']);

// Función para decidir qué clase aplicar
function clase_activa($nombre_archivo, $pagina_actual) {
    return ($nombre_archivo == $pagina_actual) ? 'enlace_seleccionado' : 'enlaces';
}
?>
    
<header class="navegacion">
    <nav class="navegacion_principal" aria-label="Navegación principal">
        
        <?php echo '<a class="'.clase_activa('index.php', $pagina_actual).'" href="'.RAIZ_APP.'/">Inicio</a>'; ?>
        
        <?php if(in_array('gerente', $_SESSION['roles'])) {
            echo '<a class="'.clase_activa('categorias.php', $pagina_actual).'" href="'.RUTA_VISTAS.'/categorias.php">Categorias</a>';
        } ?>

        <a class="<?php echo clase_activa('prueba1.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS ?>/prueba1.php">Prueba1</a>
        <a class="<?php echo clase_activa('prueba2.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS ?>/prueba2.php">Prueba2</a>
        <a class="<?php echo clase_activa('prueba3.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS ?>/prueba3.php">Prueba3</a>
        <a class="<?php echo clase_activa('prueba4.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS ?>/prueba4.php">Prueba4</a>
        <a class="<?php echo clase_activa('prueba5.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS ?>/prueba5.php">Prueba5</a>
        
    </nav>
    <button class="enlaces" id="boton-cerrar-sesion" onclick="window.location.href='<?php echo RAIZ_APP; ?>/static/logout.php?return=<?php echo basename($_SERVER['PHP_SELF']); ?>'">Cerrar Sesión</button>
</header>