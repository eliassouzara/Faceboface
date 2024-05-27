
<!---Neste codigo caso o id estiver
logado permita a session,
caso não estiver enviar um menssagem
 pela qual não está mais logado.-->
<?php
if(!isset($_SESSION)){
    session_start();
}

if(!isset($_SESSION['id'])){
  
    die("Desculpa mais você não pode acessar está pagina porque você não está logado.<p><a href=\"../index.php\">Entrar</a></p>");
}

?>