<?php
include('../conexao.php');
include('../protect.php');
include('config.php');

$currentUserId = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $friendId = intval($_POST['friend_id']);

    // Delete the friend request from the database
    $sql = "DELETE FROM friend_requests WHERE requester_id = ? AND recipient_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $currentUserId, $friendId);

    if ($stmt->execute()) {
        echo "Solicitação de amizade cancelada com sucesso.";
    } else {
        echo "Erro ao cancelar a solicitação de amizade.";
    }

    $stmt->close();
    $conn->close();

    header("Location: perfi.php?user_id=$friendId");
    exit();
}
?>
