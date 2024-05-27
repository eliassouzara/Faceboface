<?php
include('../conexao.php');
include('../protect.php');
include('config.php');
if (!isset($_SESSION['id'])) {
    die("Acesso negado");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];
    $offset = $_POST['offset'];
    
    $stmt = $conn->prepare("SELECT pc.id, pc.comment, pc.created_at, pc.user_id, u.nome AS commenter_name
                            FROM post_comments pc
                            JOIN usuarios u ON pc.user_id = u.id
                            WHERE pc.post_id = ?
                            ORDER BY pc.created_at DESC
                            LIMIT 5 OFFSET ?");
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }
    $stmt->bind_param("ii", $post_id, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result === false) {
        die("Erro ao obter resultado: " . $stmt->error);
    }
    while ($comment = $result->fetch_assoc()) {
        echo "<div class='comment'>
                <p><strong>{$comment['commenter_name']}:</strong> {$comment['comment']}</p>";
                
        // Adicionar botão Excluir se o usuário for o autor do comentário
        if ($comment['user_id'] == $_SESSION['id']) {
            echo "<form method='post' action='delete_comment.php'>
                    <input type='hidden' name='comment_id' value='{$comment['id']}'>
                    <button type='submit' class='btn btn-danger'>Excluir</button>
                  </form>";
        }

        echo "</div>";
    }
    $stmt->close();
}
?>
