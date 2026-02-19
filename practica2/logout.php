<?php
session_start();
require_once 'Usuario.php';

$_SESSION['login'] = false;
$_SESSION['nombre'] = NULL;
$_SESSION['roles'] = NULL;

header("Location: index.php"); // Redirige a una funcionalidad
exit();
?>