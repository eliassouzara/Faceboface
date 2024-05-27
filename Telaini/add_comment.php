<?php
include('../conexao.php');
include('../protect.php');
include('config.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['id'])) {
        echo 'error';
        exit();
    }

    $user_id = $_SESSION['id'];
    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];

    // Inserir comentário no banco de dados
    $stmt = $conn->prepare("INSERT INTO post_comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $comment);
    $stmt->execute();

    // Redirecionar de volta para a página inicial após adicionar o comentário
    header("Location: home.php");
    exit();
}
?>
