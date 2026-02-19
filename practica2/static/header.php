<header class="navegacion">
    <nav class="navegacion_principal" aria-label="Navegación principal">
        <a class="enlace_seleccionado" href="#">Inicio</a>
        <a class="enlaces" href="#">Prueba1</a>
        <a class="enlaces" href="#">Prueba2</a>
        <a class="enlaces" href="#">Prueba3</a>
        <a class="enlaces" href="#">Prueba4</a>
        <a class="enlaces" href="#">Prueba5</a>
        
    </nav>
    <button class="enlaces" id="boton-cerrar-sesion" onclick="window.location.href='<?php echo RAIZ_APP; ?>/static/logout.php?return=<?php echo basename($_SERVER['PHP_SELF']); ?>'">Cerrar Sesión</button>
</header>