<?php
include('../conexao.php');
include('../protect.php');
include('config.php');
// Verifique se o usuário está logado, se não redirecione para a página de login

if (isset($_GET['friend_id'])) {
    $friend_id = intval($_GET['friend_id']);

    // Atualiza o status de 'removed' para FALSE
    $stmt = $conn->prepare("
        UPDATE friend_requests 
        SET removed = FALSE, status = 'pending' 
        WHERE (requester_id = ? AND recipient_id = ?) 
        OR (requester_id = ? AND recipient_id = ?)
    ");
    $stmt->bind_param("iiii", $_SESSION['id'], $friend_id, $friend_id, $_SESSION['id']);
    $stmt->execute();
    $stmt->close();

    // Redireciona de volta para a página de amigos removidos
    header("Location: amigos_removidos.php");
    exit();
} else {
    // Redireciona para alguma página de erro, se necessário
    header("Location: home.php");
    exit();
}
?>
