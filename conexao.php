<?php

$usuario = 'root';
$senha = '';
$database = 'face';
$host = 'localhost';

$mysqli = new mysqli($host, $usuario, $senha, $database);

if($mysqli->error) {
    die("Erro ao banco de dados" . $mysqli->error);
}