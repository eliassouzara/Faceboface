<?php
include('../conexao.php');
include('../protect.php');
include('config.php');

// Pegue o ID do usuário da sessão
$idUsuario = $_SESSION['id'];

// Consulte o banco de dados para obter o nome do arquivo da foto de perfil atual
$sql = "SELECT foto_perfil FROM usuarios WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$stmt->bind_result($foto_perfil);
$stmt->fetch();
$stmt->close();

// Caminho completo para a foto de perfil atual
$caminho_foto_perfil = "" . $foto_perfil;

// Excluir a foto de perfil atual da pasta uploads
if (unlink($caminho_foto_perfil)) {
    // Atualiza a foto de perfil para a imagem padrão no banco de dados
    $sql_update = "UPDATE usuarios SET foto_perfil='uploads/default_avatar.png' WHERE id=?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("i", $idUsuario);
    
    if ($stmt_update->execute()) {
        echo "Foto de perfil removida com sucesso.";
        header('Location: profile.php');
    } else {
        echo "Erro ao atualizar a foto de perfil no banco de dados: " . $stmt_update->error;
    }

    $stmt_update->close();
} else {
    echo "Erro ao excluir a foto de perfil: " . $caminho_foto_perfil;
}

$conn->close();
?>
