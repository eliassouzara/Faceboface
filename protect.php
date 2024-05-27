<?php 
#Configurações de segurança da sessão
ini_set('session.cookie_httponly', 1); #Impede acesso aos cookies através de scripts de JavaScript prevenir ataques XSS
ini_set('session.cookie_secure', 1); #Envia o cookie apenas em conexões seguras (HTTPS)
ini_set('session.use_strict_mode', 1); #Impede ataques de fixação de sessão

session_start();




#Definir um tempo de expliração da sessão (exemplo: 30 minutos de inatividade)
$timeout = 1800; 
#LAST_ACTIVITY é usada para rastrear a última atividade de um usuário em uma aplicação web.
#$_SESSION['LAST_ACTIVITY'] contém o timestamp da última atividade do usuário.
#time() - $_SESSION['LAST_ACTIVITY'] calcula a diferença em segundos entre o timestamp atual e o timestamp da última atividade.
#Se essa diferença for maior que o valor de $timeout (o tempo limite de inatividade permitido), a expressão será true.
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout){
    session_unset(); #Remove todas as variáveus de sessão
    session_destroy(); #Destrói a sessão
    header('Location: index.php');
    exit();


}
$_SESSION['LAST_ACTIVITY'] = time(); #Atualiza o temestamp da última atividade

#Regenerar o ID da sessão periodicamente para evitar ataques de fixação de sessão
if (!isset($_SESSION['CREATED'])){
    $_SESSION['CREATED'] = time();

}##time() - $_SESSION['CREATED'] > $timeout: Verifica se o tempo decorrido desde a criação da sessão excede um limite ($timeout).
 elseif (time() - $_SESSION ['CREATED'] > $timeout ){
    session_regenerate_id(true); #Gera um novo ID de sessão e deleta o antigo
    $_SESSION['CREATED'] = time(); #Atualiza o temestamp da criação da sessão

}
#Verificação adicional de segurança
#Assegura que a sessão não está sendo usada por um navegador diferente
if (!isset($_SESSION['USER_AGENT'])){
    $_SESSION['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];

}elseif ($_SESSION ['USER_AGENT'] !== $_SERVER['HTTP_USER_AGENT']){
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}
#Verificar se o usuário está logado
if(!isset ($_SESSION['id'])){
    header("Location: ../index.php");
    exit();
}
