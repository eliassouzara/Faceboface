<?php

function sendFriendRequest($requester_id, $recipient_id) {
  // Conexão com o banco de dados
  $conn = new mysqli("localhost", "root", "", "face");

  // Inserir pedido de amizade na tabela
  $stmt = $conn->prepare("INSERT INTO friend_requests (requester_id, recipient_id) VALUES (?, ?)");
  $stmt->bind_param("ii", $requester_id, $recipient_id);
  $stmt->execute();
  $stmt->close();
  $conn->close();
}

function listFriendRequests($user_id) {
  // Conexão com o banco de dados
  $conn = new mysqli("localhost", "root", "", "face");

  // Selecionar pedidos de amizade pendentes
  $stmt = $conn->prepare("SELECT id, requester_id FROM friend_requests WHERE recipient_id = ? AND status = 'pending'");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $requests = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  $conn->close();

  return $requests;
}

function acceptFriendRequest($request_id) {
  // Conexão com o banco de dados
  $conn = new mysqli("localhost", "root", "", "face");

  // Atualizar status do pedido de amizade para "aceito"
  $stmt = $conn->prepare("UPDATE friend_requests SET status = 'accepted' WHERE id = ?");
  $stmt->bind_param("i", $request_id);
  $stmt->execute();
  $stmt->close();
  $conn->close();
}

function declineFriendRequest($request_id) {
  // Conexão com o banco de dados
  $conn = new mysqli("localhost", "root", "", "face");

  // Atualizar status do pedido de amizade para "recusado"
  $stmt = $conn->prepare("UPDATE friend_requests SET status = 'declined' WHERE id = ?");
  $stmt->bind_param("i", $request_id);
  $stmt->execute();
  $stmt->close();
  $conn->close();
}

?>
