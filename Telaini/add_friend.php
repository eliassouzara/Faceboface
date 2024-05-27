<?php
include('../conexao.php');
include('../protect.php');
include('config.php');
// Verifique se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_friend'])) {
    $friend_id = intval($_POST['friend_id']);

    // Atualiza o status de 'removed' para FALSE
    $stmt = $conn->prepare("
        UPDATE friend_requests 
        SET removed = 0 
        WHERE (requester_id = ? AND recipient_id = ?) 
        OR (requester_id = ? AND recipient_id = ?)
    ");
    $stmt->bind_param("iiii", $_SESSION['id'], $friend_id, $friend_id, $_SESSION['id']);
    $stmt->execute();
    $stmt->close();

    // Redireciona de volta para a página de amigos removidos
    header("Location: removed_friends.php");
    exit();
} else {
    // Redireciona para alguma página de erro, se necessário
    header("Location: home.php");
    exit();
}
?>
