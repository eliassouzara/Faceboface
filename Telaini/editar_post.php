<?php
// Verifique se o ID do post foi fornecido na URL
require_once 'verificar_acesso_post.php';
include('../conexao.php');

include('config.php');
if (isset($_GET['id'])) {
    $postId = $_GET['id'];
    
    // Conecte-se ao banco de dados e faça a busca dos dados do post com base no ID
    include('../conexao.php'); // Altere o caminho conforme necessário
    

    // Verifique se o formulário foi submetido
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtenha os novos dados do post do formulário
        $novoConteudo = $_POST['novo_conteudo'];
        $novoTitulo = $_POST['novo_titulo'];

        // Atualize o post no banco de dados
        $sql = "UPDATE ve_posts SET conteudo=?, titulo=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $novoConteudo, $novoTitulo, $postId);

        if ($stmt->execute()) {
            echo "Post atualizado com sucesso!";
            // Redirecione o usuário de volta à página de perfil ou a outra página desejada após a atualização
            // header("Location: profile.php");
            exit();
        } else {
            echo "Erro ao atualizar o post: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        // Busque os dados atuais do post no banco de dados
        $sql = "SELECT conteudo, titulo FROM ve_posts WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $stmt->bind_result($conteudo, $titulo);
        $stmt->fetch();
        $stmt->close();
    }
} else {
    // Redirecione o usuário de volta à página de perfil ou exiba uma mensagem de erro se o ID do post não foi fornecido
    // header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Post</title>
    <!-- Seus estilos CSS e scripts JavaScript aqui -->
</head>
<body>
    <h2>Editar Post</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $postId; ?>">
        <div class="form-group">
            <label for="novo_titulo">Novo Título:</label>
            <input type="text" class="form-control" id="novo_titulo" name="novo_titulo" value="<?php echo $titulo; ?>">
        </div>
        <div class="form-group">
            <label for="novo_conteudo">Novo Conteúdo:</label>
            <textarea class="form-control" id="novo_conteudo" name="novo_conteudo"><?php echo $conteudo; ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
</body>
</html>
