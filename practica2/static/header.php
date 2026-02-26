<?php
require_once 'config.php';

$pagina_actual = basename($_SERVER['PHP_SELF']);

// Función para decidir qué clase aplicar
function clase_activa($nombre_archivo, $pagina_actual) {
    return ($nombre_archivo == $pagina_actual) ? 'enlace_seleccionado' : 'enlaces';
}
?>
    
<header class="navegacion">
    <div class="header-izquierdo"></div>

    <nav class="navegacion_principal" aria-label="Navegación principal">
        
        <?php echo '<a class="'.clase_activa('index.php', $pagina_actual).'" href="'.RAIZ_APP.'/">Inicio</a>'; ?>
        
        <?php if(in_array('gerente', $_SESSION['roles'])) {
            echo '<a class="'.clase_activa('categorias.php', $pagina_actual).'" href="'.RUTA_VISTAS.'/categorias.php">Categorias</a>';
            echo '<a class="'.clase_activa('productos.php', $pagina_actual).'" href="'.RUTA_VISTAS.'/productos.php">Productos</a>';
        } ?>

        <a class="<?php echo clase_activa('prueba1.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS ?>/prueba1.php">Prueba1</a>
        <a class="<?php echo clase_activa('prueba2.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS ?>/prueba2.php">Prueba2</a>
        <a class="<?php echo clase_activa('prueba3.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS ?>/prueba3.php">Prueba3</a>
        <a class="<?php echo clase_activa('prueba4.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS ?>/prueba4.php">Prueba4</a>
        <a class="<?php echo clase_activa('prueba5.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS ?>/prueba5.php">Prueba5</a>
        
    </nav>
    <div class="header-derecho">
        <div class="usuario-menu-container">
            <figure class="avatar-container" onclick="toggleMenu()">
                <img src="<?php echo RUTA_IMG . '/perfiles/' . $_SESSION['foto_perfil']; ?>" alt="Icono de usuario" class="avatar">
            </figure>

            <div id="menuDesplegable" class="dropdown-content">
                <p class="usuario-nombre"><?php echo $_SESSION['nombre']; ?></p>
                <hr>
                <a href="<?php echo RUTA_VISTAS; ?>/editar_perfil.php">Mi Perfil</a>
                <?php if(in_array('gerente', $_SESSION['roles'])): ?>
                    <a href="<?php echo RUTA_VISTAS; ?>/ajustes_admin.php">Ajustes</a>
                <?php endif; ?>
                <a href="<?php echo RUTA_STATIC; ?>/logout.php" class="logout-link">Cerrar Sesión</a>
            </div>
        </div>    
    </div>
</header>