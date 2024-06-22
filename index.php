<?php

echo "Olá, mundo!<br>";

// consulta sql
$host = 'mysql';        
$user = 'root';         
$password = 'senha1234';
$database = 'mydatabase';

$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_error) {
    die("Erro na conexão ao MySQL: " . $mysqli->connect_error);
}

$sql = "SELECT * FROM exemplo";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    echo "Registros encontrados:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"] . " - Nome: " . $row["nome"] . "<br>";
    }
} else {
    echo "Nenhum registro encontrado.";
}

$mysqli->close();
?>
