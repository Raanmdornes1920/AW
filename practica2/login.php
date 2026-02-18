<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Bistro FDI</title>
</head>
<body>
    <h1>Acceso al Sistema</h1>
    <form action="procesarLogin.php" method="POST">
        <label>Usuario: <input type="text" name="username" required></label><br>
        <label>Contraseña: <input type="password" name="password" required></label><br>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>