<?php

    $host = "localhost";
    $usuario = "root";
    $senha = "";
    $database = "face";

   $mysqli = new mysqli($host, $usuario, $senha, $database);
    
    if($mysqli->error){
        die ("Erro" . $mysqli->error);
   }
   

?>

