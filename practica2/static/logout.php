<?php
session_start();
require_once 'config.php';

$_SESSION['login'] = false;
$_SESSION['nombre'] = NULL;
$_SESSION['roles'] = NULL;

session_destroy();
header("Location: " . RAIZ_APP . '/');
exit();
?>