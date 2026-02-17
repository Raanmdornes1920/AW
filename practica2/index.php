<?php 
public function login(Usuario $user) {
    $_SESSION['login'] = true;
    $_SESSION['nombre'] = $user->username();
    $_SESSION['roles'] = $user->roles();
}?>

<!DOCTYPE html>
<html>
    <head>
        <title>PHP</title>
    </head>
    <body>
        <?php
            echo '<h1>Prueba PHP</h1>';
        ?>
        
    </body>
</html>