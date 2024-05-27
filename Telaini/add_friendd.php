<?php
include('../conexao.php');
include('../protect.php');
include('config.php');
$currentUserId = $_SESSION['id'];
$friendId = intval($_POST['friend_id']);

$sql = "INSERT INTO friend_requests (requester_id, recipient_id) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $currentUserId, $friendId);
$stmt->execute();
$stmt->close();

header("Location: perfi.php?user_id=" . $friendId);
?>
