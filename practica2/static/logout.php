<?php
session_start();
require_once 'config.php';

$_SESSION['login'] = false;
$_SESSION['usuario'] = NULL;
$_SESSION['nombre'] = NULL;
$_SESSION['apellidos'] = NULL;
$_SESSION['email'] = NULL;
$_SESSION['foto_perfil'] = NULL;
$_SESSION['roles'] = NULL;

session_destroy();
header("Location: " . RAIZ_APP . '/');
exit();
?>