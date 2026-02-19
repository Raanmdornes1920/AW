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

$es_local = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost');

if ($es_local) {
    console_log("Entorno local detectado");
    $host = "127.0.0.1";
    $pass = "";
} else {
    console_log("Entorno remoto detectado");
    $host = "vm016.db.swarm.test"; 
    $pass = "d5J40AQKX1qVwwSGwr05";
}

$user = "root";
$db   = "database";

console_log("Iniciando conexión a la base de datos con host: $host");
$db_connection = mysqli_connect($host, $user, $pass, $db);
?>