<?php
$host = "localhost";
$user = "root";
$pass = "d5J40AQKX1qVwwSGwr05"; 
$db   = "database";

$db_connection = mysqli_connect($host, $user, $pass, $db);

if (!$db_connection) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>