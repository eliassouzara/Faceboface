<?php
include('../conexao.php');
include('../protect.php');
include('config.php');

if (!isset($_SESSION['id'])) {
    echo 'error';
    exit();
}

$user_id = $_SESSION['id'];
$post_id = $_POST['post_id'];

// Verificar se o usuário já curtiu o post
$stmt = $conn->prepare("SELECT * FROM post_likes WHERE user_id = ? AND post_id = ?");
$stmt->bind_param("ii", $user_id, $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Se já curtiu, descurtir e atualizar o contador
    $stmt = $conn->prepare("DELETE FROM post_likes WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();

    // Atualizar o contador de curtidas no post
    $stmt = $conn->prepare("UPDATE ve_posts SET likes_count = likes_count - 1 WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    echo 'unliked';
} else {
    // Se não curtiu, curtir e atualizar o contador
    $stmt = $conn->prepare("INSERT INTO post_likes (user_id, post_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $post_id);
    $stmt->execute();

    // Atualizar o contador de curtidas no post
    $stmt = $conn->prepare("UPDATE ve_posts SET likes_count = likes_count + 1 WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    echo 'liked';
}

$stmt->close();
$conn->close();
?>
