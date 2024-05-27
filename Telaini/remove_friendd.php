<?php
include('../conexao.php');
include('../protect.php');
include('config.php');

$currentUserId = $_SESSION['id'];
$friendId = intval($_POST['friend_id']);

$sql = "DELETE FROM friend_requests WHERE (requester_id = ? AND recipient_id = ?) OR (requester_id = ? AND recipient_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $currentUserId, $friendId, $friendId, $currentUserId);
$stmt->execute();
$stmt->close();

header("Location: perfi.php?user_id=" . $friendId);
?>
