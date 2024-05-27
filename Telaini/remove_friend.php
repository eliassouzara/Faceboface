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
if (!isset($_GET['friend_id'])) {
    die("ID do amigo não fornecido.");
}

$friend_id = intval($_GET['friend_id']);
$user_id = $_SESSION['id'];

// Atualiza o status para 'declined' e o campo removed para TRUE
$stmt = $conn->prepare("
    UPDATE friend_requests 
    SET status = 'declined', removed = TRUE 
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

// Redireciona de volta para a página de amigos ou outra página
header("Location: home.php");
exit();
?>
