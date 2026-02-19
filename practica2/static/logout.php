<?php
session_start();

$_SESSION['login'] = false;
$_SESSION['nombre'] = NULL;
$_SESSION['roles'] = NULL;

header("Location: ../index.php"); // Redirige a una funcionalidad
exit();
?>