<?php
require_once 'verificar_acesso_post.php';
// Verifique se o ID do post foi fornecido na URL
if (isset($_GET['id'])) {
    $postId = $_GET['id'];
    
    // Conecte-se ao banco de dados
    include('../conexao.php'); // Altere o caminho conforme necessário
    

    include('config.php');
    
    // Primeiro, obtenha o nome do arquivo de imagem do post que será excluído
    $sql_select_image = "SELECT imagem FROM ve_posts WHERE id=?";
    $stmt_select_image = $conn->prepare($sql_select_image);
    $stmt_select_image->bind_param("i", $postId);
    $stmt_select_image->execute();
    $stmt_select_image->store_result();
    
    if ($stmt_select_image->num_rows > 0) {
        $stmt_select_image->bind_result($imageName);
        $stmt_select_image->fetch();
        
        // Exclua o post do banco de dados
        $sql_delete_post = "DELETE FROM ve_posts WHERE id=?";
        $stmt_delete_post = $conn->prepare($sql_delete_post);
        $stmt_delete_post->bind_param("i", $postId);

        if ($stmt_delete_post->execute()) {
            // Exclua a imagem do post da pasta de uploads
            $uploadDirectory = '../post/uploads/';
            $imagePath = $uploadDirectory . $imageName;
            
            if (file_exists($imagePath)) {
                unlink($imagePath);
                echo "Post e imagem excluídos com sucesso!";
                header('Location: profile.php');
            } else {
                echo "Post excluído, mas houve um problema ao excluir a imagem.";
                header('Location: profile.php');
            }
        } else {
            echo "Erro ao excluir o post: " . $stmt_delete_post->error;
        }

        $stmt_delete_post->close();
    } else {
        echo "Post não encontrado.";
    }

    $stmt_select_image->close();
    $conn->close();
} else {
    // Redirecione o usuário de volta à página de perfil ou exiba uma mensagem de erro se o ID do post não foi fornecido
    // header("Location: profile.php");
    exit();
}
?>
