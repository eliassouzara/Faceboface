<?php
session_start();
include('../conexao.php');
include('../protect.php');
include('config.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Verifica se o usuário está logado
  if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
  }

  $requester_id = $_SESSION['id'];
  $recipient_id = $_POST['friend_id'];

  // Verifica se o pedido de amizade já existe
  $stmt = $conn->prepare("SELECT * FROM friend_requests WHERE requester_id = ? AND recipient_id = ? AND status = 'pending'");
  $stmt->bind_param("ii", $requester_id, $recipient_id);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    // Pedido de amizade já enviado
    $stmt->close();
    header("Location: home.php?error=already_sent");
    exit();
  }

  $stmt->close();

  // Envia o pedido de amizade
  $stmt = $conn->prepare("INSERT INTO friend_requests (requester_id, recipient_id) VALUES (?, ?)");
  $stmt->bind_param("ii", $requester_id, $recipient_id);
  $stmt->execute();
  $stmt->close();

  header("Location: home.php?success=request_sent");
  exit();
}
?>
