<?php
session_start();
require_once '../static/config.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['rol'] !== 'gerente') {
    header("Location: ".RAIZ_APP."/");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Gestionar Usuarios - BISTRO FDI</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">    
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/modales.css">
</head>
<body>
    <!-- Header -->
    <?php include '../static/header.php'; ?>
    <!-- Header -->

    <main class="contenedor-centro">    
        <h1 id="titulo-registro">Nuevo Usuario</h1>
        <form action="<?php echo RUTA_STATIC ?>/procesarRegistro.php" method="POST" enctype="multipart/form-data">
            <label>Nombre:</label>
            <br>
            <input type="text" name="nombre" required>
            <br>

            <label>Apellidos:</label>
            <br>
            <input type="text" name="apellidos" required>
            <br>
            
            <label>Correo Electrónico:</label>
            <br>
            <input type="email" name="mail" required>
            <br>

            <label>Usuario:</label>
            <br>
            <input type="text" name="username" required>
            <br>

            <label>Foto de perfil:</label>
            <br>
            <div class="seleccion-avatares">
                <?php foreach (IMAGENES_BASE as $indice => $archivo): ?>
                    <label class="opcion-avatar">
                        <img class="opcion-imagen-avatar" src="../img/perfiles/<?= $archivo; ?>" alt="Avatar <?= $indice; ?>">
                        <input type="radio" name="foto_perfil" value="<?= $archivo; ?>" required>
                    </label>
                <?php endforeach; ?>

                <label class="opcion-avatar">
                    <div class="cuadro-subir-archivo">
                        <p>Elegir<br>Archivo</p>
                    </div>
                    <input type="radio" name="foto_perfil" value="custom" id="radio-custom" required>
                </label>
            </div>

            <div id="archivo-avatar">
                <br>
                <input type="file" name="foto_perfil" accept="image/*">
                <br>
                <br>
                <br>
            </div>
            
            <label>Rol:</label>
            <select name="rol" id="select-rol-usuario">
                <option value="gerente">Gerente</option>
                <option value="cocinero">Cocinero</option>
                <option value="camarero">Camarero</option>
                <option value="cliente" selected>Cliente</option>
            </select>

            <input id="password" type="hidden" name="password" value="1234" required>
            <input type="hidden" name="password_confirm" value="1234" required>
            
            <input type="hidden" name="modo-admin" value="Verdadero">
            <input type="hidden" name="volver" value="<?php echo htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : RAIZ_APP . "/"); ?>">
            
            <div class="contenedor-botones">
            <button type="submit" id="boton_aceptar">Crear Usuario</button>
            
            <button onclick="window.location.href='<?php echo htmlspecialchars(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : RUTA_VISTAS . '/ajustes_admin.php'); ?>'" type="button" id="boton_cancelar">Volver</button>
            </div>
        </form>

    </main>
    <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
</body>
</html>