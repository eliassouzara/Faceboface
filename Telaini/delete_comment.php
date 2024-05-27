<?php
include('../conexao.php');
include('../protect.php');
include('config.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['id'])) {
        echo 'error';
        exit();
    }

    $comment_id = $_POST['comment_id'];
    $user_id = $_SESSION['id'];

    // Verificar se o usuário é o autor do comentário
    $stmt = $conn->prepare("SELECT user_id FROM post_comments WHERE id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($comment_user_id);
        $stmt->fetch();

        if ($comment_user_id == $user_id) {
            // Excluir o comentário do banco de dados
            $stmt_delete = $conn->prepare("DELETE FROM post_comments WHERE id = ?");
            $stmt_delete->bind_param("i", $comment_id);
            $stmt_delete->execute();

            // Redirecionar de volta para a página inicial após excluir o comentário
            header("Location: home.php");
            exit();
        }
    }

    // Se o usuário não for o autor do comentário, redirecionar de volta para a página inicial
    header("Location: home.php");
    exit();
}
?>
