<h1>Inicio de Sesion</h1>
<form action="<?php echo RUTA_INCLUDES ?>/procesarLogin.php" method="POST">
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