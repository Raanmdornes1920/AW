<?php
function console_log($data) {
    $output = json_encode($data);
    echo "<script>console.log('PHP Debug: " . addslashes($output) . "');</script>";
    // Esto "empuja" el código al navegador inmediatamente
    ob_flush();
    flush();
}
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "vm016.db.swarm.test"; 
$user = "root";
$pass = "d5J40AQKX1qVwwSGwr05";
$db   = "database";

console_log("Intentando conectar");
$db_connection = mysqli_connect($host, $user, $pass, $db);

if ($db_connection) {
    echo "¡CONECTADO CON ÉXITO!";
} else {
    echo "Fallo: " . mysqli_connect_error();
}
?>