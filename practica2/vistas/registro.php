<?php
session_start();
require_once '../static/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Registro - Bistro FDI</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
</head>
<body>
    <!-- Header -->
    <?php include '../static/header_registro.php'; ?>
    <!-- Header -->

    <main class="contenedor-centro">    
        <h1 id="titulo-registro">Registro</h1>
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
            <input type="file" name="foto_perfil" accept="image/*">
            <br><br>

            <label>Contraseña:</label>
            <br>
            <input id="password" type="password" name="password" required>
            <br>

            <label>Repetir Contraseña:</label>
            <br>
            <input type="password" name="password_confirm" required oninput="
            if(document.getElementById('password').value != this.value) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }">
            <br><br>

            <button type="submit">Registrarme</button>
        </form>

    </main>
</body>
</html>