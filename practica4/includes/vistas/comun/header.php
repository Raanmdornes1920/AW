<?php
require_once (__DIR__ . '/../../config.php');

$pagina_actual = basename($_SERVER['PHP_SELF']);

// Función para decidir qué clase aplicar
function clase_activa($nombre_archivo, $pagina_actual) {
    return ($nombre_archivo == $pagina_actual) ? 'nav-link active fw-semibold' : 'nav-link';
}

// Sumamos las cantidades de todos los productos en el carrito
$num_items_carrito = 0;
if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    $num_items_carrito = array_sum($_SESSION['carrito']);
}
?>
<header class="sticky-top shadow-sm">
    <nav class="navbar navbar-expand-lg bg-white" aria-label="Navegación principal">
        <div class="container-fluid px-3 px-lg-4">
            
            <button class="navbar-toggler order-1" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>

            <a class="navbar-brand d-flex align-items-center gap-2 order-2 order-lg-1 mx-auto mx-lg-0" href="<?php echo RAIZ_APP; ?>/">
                <img class="brand-logo" src="<?php echo RUTA_IMG; ?>/logo1.png" alt="Logo de Bistro FDI">
            </a>

            <div class="dropdown order-3">
                <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="position-relative">
                        <img id='Logo-Usuario-Logueado-Perfil' src="<?php echo RUTA_IMG . '/perfiles/' . $_SESSION['usuario']->avatar(); ?>" alt="Icono de usuario" class="avatar-img rounded-circle">
                        <?php if($_SESSION['usuario']->rol() === 'cliente' && $num_items_carrito > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo $num_items_carrito; ?></span>
                        <?php endif; ?>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header"><?php echo strtoupper(htmlspecialchars($_SESSION['usuario']->usuario())); ?></h6></li>
                    <li><a class="dropdown-item" href="<?php echo RUTA_VISTAS; ?>/usuarios/editar_perfil.php"><i class="bi bi-person-circle"></i> Mi Perfil</a></li>
                    <?php if($_SESSION['usuario']->rol() === 'cliente'): ?>
                        <li><a class="dropdown-item" href="<?php echo RUTA_VISTAS; ?>/pedidos/carrito.php<?= (isset($_GET['tipo'])?"?tipo=" . $_GET['tipo']:"");?>"><i class="bi bi-cart3"></i> Mi Carrito <?= ($num_items_carrito > 0) ? "($num_items_carrito)" : "" ?></a></li>
                    <?php endif; ?>
                    <?php if($_SESSION['usuario']->rol() === 'gerente'): ?>
                            <li><a class="dropdown-item" href="<?php echo RUTA_VISTAS; ?>/usuarios/ajustes_admin.php"><i class="bi bi-person-gear"></i> Administrar Perfiles</a></li>
                    <?php endif; ?>
                    <li><hr class="dropdown-divider"></li>
                    <li><a href="<?php echo RUTA_INCLUDES; ?>/vistas/usuarios/apoyo/logout.php" class="dropdown-item text-danger"><i class="bi bi-box-arrow-left"></i> Cerrar Sesión</a></li>
                </ul>
            </div>

            <div class="collapse navbar-collapse order-4 order-lg-2" id="menuPrincipal">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center">
                    <li class="nav-item">
                        <a class="nav-link <?php echo clase_activa('index.php', $pagina_actual); ?>" href="<?php echo RAIZ_APP; ?>/">Inicio</a>
                    </li>
                    
                    <?php if($_SESSION['usuario']->rol() === 'gerente'): ?>
                        <li class="nav-item"><a class="nav-link <?php echo clase_activa('categorias_gerente.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS; ?>/categorias/categorias_gerente.php">Categorías</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo clase_activa('productos_gerente.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS; ?>/productos/productos_gerente.php">Productos</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo clase_activa('pedidos_gerente.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS; ?>/pedidos/pedidos_gerente.php">Supervisar Pedidos</a></li>
                    <?php elseif($_SESSION['usuario']->rol() === 'cliente'): ?>
                        <li class="nav-item"><a class="nav-link <?php echo clase_activa('productos_cliente.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS; ?>/productos/productos_cliente.php<?= (isset($_GET['tipo'])?"?tipo=".$_GET['tipo']:"") ?>">Carta</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo clase_activa('mis_pedidos.php', $pagina_actual); ?>" href="<?php echo RUTA_VISTAS; ?>/pedidos/mis_pedidos.php">Mis Pedidos</a></li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>
    </nav>
</header>
