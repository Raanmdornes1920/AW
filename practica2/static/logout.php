<?php
require_once 'config.php';
session_start();


unset($_SESSION['login']);
unset($_SESSION['usuario']);

session_destroy();
header("Location: " . RAIZ_APP . '/');
exit();
?>