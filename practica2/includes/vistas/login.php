<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Login - Bistro FDI</title>
    <link rel="icon" type="image/svg+xml" href="<?php echo RUTA_IMG; ?>/logo1.svg">
    <link rel="stylesheet" href="<?php echo RUTA_CSS; ?>/default.css">
    <script src="<?php echo RAIZ_APP; ?>/js/script.js"></script>
</head>
<body>
    <!-- Header -->
    <?php include 'static/header_login.php'; ?>
    <!-- Header -->
     
    <main class="contenedor-centro">    
        <h1>Inicio de Sesion</h1>
        <form action="<?php echo RUTA_STATIC ?>/procesarLogin.php" method="POST">
            <label>Usuario:</label>
            <br>
            <input type="text" name="username" required>
            <br>

            <label>Contraseña:</label>
            <br>
            <input type="password" name="password" required>
            <br><br>

            <button type="submit">Entrar</button>
        </form>
    </main>
</body>
</html>