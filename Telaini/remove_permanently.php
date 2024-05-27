<?php
include('../conexao.php');
include('../protect.php');
include('config.php');

// Verifique se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Verifique se o ID do amigo foi passado
if (!isset($_POST['friend_id'])) {
    die("ID do amigo não fornecido.");
}

$friend_id = intval($_POST['friend_id']);
$user_id = $_SESSION['id'];

// Exclui a solicitação de amizade do banco de dados
$stmt = $conn->prepare("
    DELETE FROM friend_requests 
    WHERE (requester_id = ? AND recipient_id = ?) 
    OR (requester_id = ? AND recipient_id = ?)
");
if ($stmt === false) {
    die("Erro ao preparar a consulta: " . $conn->error);
}
$stmt->bind_param("iiii", $user_id, $friend_id, $friend_id, $user_id);
$stmt->execute();
if ($stmt->affected_rows === 0) {
    die("Nenhuma amizade encontrada para remover.");
}
$stmt->close();

// Redireciona de volta para a página de amigos removidos
header("Location: amigos_removidos.php");
exit();
?>
