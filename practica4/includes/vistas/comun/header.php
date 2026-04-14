<?php
require_once (__DIR__ . '/../../config.php');

$pagina_actual = basename($_SERVER['PHP_SELF']);

// Función para decidir qué clase aplicar
function clase_activa($nombre_archivo, $pagina_actual) {
    return ($nombre_archivo == $pagina_actual) ? 'enlace_seleccionado' : 'enlaces';
}

// Sumamos las cantidades de todos los productos en el carrito
$num_items_carrito = 0;
if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    $num_items_carrito = array_sum($_SESSION['carrito']);
}
?>
<header class="navegacion" >
    
<button id="menu-toggle" class="menu-toggle">☰</button>

<img id="Logo-Header" class="logo-movil" src="<?php echo RUTA_IMG; ?>/logo1.png" alt="Logo de Bistro FDI" onclick="window.location.href='<?php echo RAIZ_APP; ?>/'">

<div class="header-izquierdo"></div>
    <nav class="navegacion_principal" id="menu" aria-label="Navegación principal">
        
        <?php echo '<a class="'.clase_activa('index.php', $pagina_actual).'" href="'.RAIZ_APP.'/">Inicio</a>'; ?>
        
        <?php if($_SESSION['usuario']->rol() === 'gerente') {
            echo '<a class="'.clase_activa('categorias_gerente.php', $pagina_actual).'" href="'.RUTA_VISTAS.'/categorias/categorias_gerente.php">Categorias</a>';
            echo '<a class="'.clase_activa('productos_gerente.php', $pagina_actual).'" href="'.RUTA_VISTAS.'/productos/productos_gerente.php">Productos</a>';
            // Añadido el enlace para que el gerente supervise todo
            echo '<a class="'.clase_activa('pedidos_gerente.php', $pagina_actual).'" href="'.RUTA_VISTAS.'/pedidos/pedidos_gerente.php">Supervisar Pedidos</a>';
        }
        else if($_SESSION['usuario']->rol() === 'cocinero') {
            // Actualizada la ruta a la carpeta pedidos
            echo '<a class="'.clase_activa('pedidos_cocinero.php', $pagina_actual).'" href="'.RUTA_VISTAS.'/pedidos/pedidos_cocinero.php">Comandas (Cocina)</a>';
        }
        else if($_SESSION['usuario']->rol() === 'camarero') {
            // Actualizada la ruta a la carpeta pedidos
            echo '<a class="'.clase_activa('pedidos_camarero.php', $pagina_actual).'" href="'.RUTA_VISTAS.'/pedidos/pedidos_camarero.php">Atender Pedidos</a>';
        }
        else if($_SESSION['usuario']->rol() === 'cliente') {
            // Añadido enlace a la carta para que puedan comprar y la ruta correcta a mis pedidos
            echo '<a class="'.clase_activa('productos_cliente.php', $pagina_actual).'" href="'.RUTA_VISTAS.'/productos/productos_cliente.php' . (isset($_GET['tipo'])?"?tipo=".$_GET['tipo']:"") . '">Nuestra Carta</a>';
            echo '<a class="'.clase_activa('mis_pedidos.php', $pagina_actual).'" href="'.RUTA_VISTAS.'/pedidos/mis_pedidos.php">Mis Pedidos</a>';
        }
        ?>

    </nav>
    <div class="header-derecho">
        <div class="usuario-menu-container">
            <figure class="avatar-container" style="position: relative;">
                <img src="<?php echo RUTA_IMG . '/perfiles/' . $_SESSION['usuario']->avatar(); ?>" alt="Icono de usuario" class="avatar">
                <?php if($_SESSION['usuario']->rol() === 'cliente' && $num_items_carrito > 0): ?>
                    <span class="carrito-badge"><?php echo $num_items_carrito; ?></span>
                <?php endif; ?>
            </figure>

            <div id="menuDesplegable" class="dropdown-content">
                <p class="usuario-nombre"><?php echo $_SESSION['usuario']->nombre(); ?></p>
                <hr>
                <a href="<?php echo RUTA_VISTAS; ?>/usuarios/editar_perfil.php">Mi Perfil</a>
                
                <?php if($_SESSION['usuario']->rol() === 'cliente'): ?>
                    <a href="<?php echo RUTA_VISTAS; ?>/pedidos/carrito.php<?= (isset($_GET['tipo'])?"?tipo=" . $_GET['tipo']:"");?>">🛒 Mi Carrito <?= ($num_items_carrito > 0) ? "($num_items_carrito)" : "" ?></a>
                <?php endif; ?>
                <?php if($_SESSION['usuario']->rol() === 'gerente'): ?>
                    <a href="<?php echo RUTA_VISTAS; ?>/usuarios/ajustes_admin.php">Administrar Perfiles</a>
                <?php endif; ?>
                <a href="<?php echo RUTA_INCLUDES; ?>/vistas/usuarios/apoyo/logout.php" class="logout-link">Cerrar Sesión</a>
            </div>
        </div>    
    </div>
</header>