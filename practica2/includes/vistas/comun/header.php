<?php
require_once (__DIR__ . '/../../config.php');

$pagina_actual = basename($_SERVER['PHP_SELF']);

// Función para decidir qué clase aplicar
function clase_activa($nombre_archivo, $pagina_actual) {
    return ($nombre_archivo == $pagina_actual) ? 'enlace_seleccionado' : 'enlaces';
}
?>
<!-- LO COMENTADO ES PARA DEBUGEAR -->
<header class="navegacion" ><?php //style="display: none;">?> 
    <div class="header-izquierdo"></div>

    <nav class="navegacion_principal" aria-label="Navegación principal">
        
        <?php echo '<a class="'.clase_activa('index.php', $pagina_actual).'" href="'.RAIZ_APP.'/">Inicio</a>'; ?>
        
        <?php if($_SESSION['usuario']->rol() === 'gerente') {
            echo '<a class="'.clase_activa('categorias_lista.php', $pagina_actual).'" href="'.RUTA_VISTAS.'/categorias/categorias_gerente.php">Categorias</a>';
            echo '<a class="'.clase_activa('productos_gerente.php', $pagina_actual).'" href="'.RUTA_VISTAS.'/productos/productos_gerente.php">Productos</a>';
        }
        else if($_SESSION['usuario']->rol() === 'cocinero') {
            echo '<a class="'.clase_activa('pedidos_cocinero.php', $pagina_actual).'" href="'.RAIZ_APP.'/">Pedidos</a>';
        }
        else if($_SESSION['usuario']->rol() === 'camarero') {
            echo '<a class="'.clase_activa('pedidos_camarero.php', $pagina_actual).'" href="'.RAIZ_APP.'/">Pedidos</a>';
        }
        else if($_SESSION['usuario']->rol() === 'cliente') {
            echo '<a class="'.clase_activa('productos_cliente.php', $pagina_actual).'" href="'.RUTA_VISTAS.'/productos/productos_lista.php">Carta / Productos</a>';

            echo '<a class="'.clase_activa('pedidos_cliente.php', $pagina_actual).'" href="'.RAIZ_APP.'/">Mis Pedidos</a>';
        }
        ?>

    </nav>
    <div class="header-derecho">
        <div class="usuario-menu-container">
            <figure class="avatar-container" onclick="toggleMenu()">
                <img src="<?php echo RUTA_IMG . '/perfiles/' . $_SESSION['usuario']->avatar(); ?>" alt="Icono de usuario" class="avatar">
            </figure>

            <div id="menuDesplegable" class="dropdown-content">
                <p class="usuario-nombre"><?php echo $_SESSION['usuario']->nombre(); ?></p>
                <hr>
                <a href="<?php echo RUTA_VISTAS; ?>/usuarios/editar_perfil.php">Mi Perfil</a>
                <!-- Rol Cliente -->
                <?php if($_SESSION['usuario']->rol() === 'cliente'): ?>
                    <a href="<?php echo RUTA_VISTAS; ?>/carrito.php">Carrito</a>
                <?php endif; ?>
                <!-- Rol Cliente -->
                 <!-- Rol Gerente -->
                <?php if($_SESSION['usuario']->rol() === 'gerente'): ?>
                    <a href="<?php echo RUTA_VISTAS; ?>/usuarios/ajustes_admin.php">Administrar Perfiles</a>
                <?php endif; ?>
                <!-- Rol Gerente -->
                <a href="<?php echo RUTA_INCLUDES; ?>/vistas/usuarios/apoyo/logout.php" class="logout-link">Cerrar Sesión</a>
            </div>
        </div>    
    </div>
</header>