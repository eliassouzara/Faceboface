<?php
include('config.php');
// Verificar se o usuário está autenticado
session_start();
if (!isset($_SESSION['id'])) {
    // Se não estiver autenticado, redirecione para a página de login
    header("Location: login.php");
    exit();
}

// Se o ID do post estiver presente na URL
if (isset($_GET['id'])) {
    $postId = $_GET['id'];

    // Buscar detalhes do post no banco de dados
    // Suponha que você esteja usando uma conexão MySQLi chamada $conn
    $stmt = $conn->prepare("SELECT id_autoi FROM ve_posts WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $stmt->store_result();
    
    // Verificar se a consulta retornou algum resultado
    if ($stmt->num_rows > 0) {
        // A consulta retornou um resultado, então vincule os resultados
        $stmt->bind_result($id_autoi);
        $stmt->fetch();

        // Verificar se o usuário logado é o autor do post
        if ($id_autoi == $_SESSION['id']) {
            // Se o usuário logado for o autor do post, exiba os detalhes do post normalmente
            // Resto do seu código para exibir os detalhes do post
        } else {
            // Se o usuário logado não for o autor do post, exiba uma mensagem de acesso negado
            echo "Acesso Negado: Você não tem permissão para acessar este post.";
            exit();
        }
    } else {
        // Se a consulta não retornou nenhum resultado, exiba uma mensagem de erro
        echo "Erro: O post não foi encontrado.";
        exit();
    }
} else {
    // Se o ID do post não estiver presente na URL, redirecione para uma página de erro
    header("Location: erro.php");
    exit();
}

?>