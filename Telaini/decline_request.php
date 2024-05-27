<?php
session_start();
include('../conexao.php');

include('config.php');
if (isset($_GET['id'])) {
  $request_id = $_GET['id'];

  // Atualiza o status do pedido de amizade para "recusado"
  $stmt = $conn->prepare("UPDATE friend_requests SET status = 'declined' WHERE id = ?");
  $stmt->bind_param("i", $request_id);
  $stmt->execute();
  $stmt->close();
 

      // Atualiza o status 'removed' para 1
      $stmt2 = $conn->prepare("UPDATE friend_requests SET removed = 1 WHERE id = ?");
      $stmt2->bind_param("i", $request_id);
      $stmt2->execute();
      $stmt2->close();

      
  header("Location: home.php?success=request_declined");
  exit();
}
?>

