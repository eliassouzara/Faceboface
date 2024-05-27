<?php
session_start();
include('../conexao.php');

include('config.php');
if (isset($_GET['id'])) {
  $request_id = $_GET['id'];

  // Atualiza o status do pedido de amizade para "aceito"
  $stmt = $conn->prepare("UPDATE friend_requests SET status = 'accepted' WHERE id = ?");
  $stmt->bind_param("i", $request_id);
  $stmt->execute();
  $stmt->close();

  // Você pode adicionar lógica adicional aqui, como adicionar a amizade na tabela de amigosa

  header("Location: home.php?success=request_accepted");
  exit();
}  
?>
