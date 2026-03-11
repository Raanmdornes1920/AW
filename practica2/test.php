<?php

require_once 'includes/config.php';
try {
    $lista = UsuarioDAO::listaUsuarios();
    echo "¡Funciona! Hay " . $lista->count() . " usuarios.";

    echo "<br>";

    $usr = UsuarioDAO::buscaUsuario('ramon');
    echo json_encode($usr);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>