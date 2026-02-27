<?php
session_start();
require_once 'config.php';

unset($_SESSION['login']);
unset($_SESSION['usuario']);
unset($_SESSION['nombre']);
unset($_SESSION['apellidos']);
unset($_SESSION['email']);
unset($_SESSION['foto_perfil']);
unset($_SESSION['rol']);

session_destroy();
header("Location: " . RAIZ_APP . '/');
exit();
?>