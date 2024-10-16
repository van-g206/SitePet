<?php
$servername = "localhost";
$username = "username"; // Substitua pelo seu usuário
$password = ""; // Substitua pela sua senha
$dbname = "adoc_ani";

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Se a conexão for bem-sucedida, você pode opcionalmente descomentar a linha abaixo
// echo "Conectado com sucesso";
?>
