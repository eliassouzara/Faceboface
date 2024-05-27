<?php
include('../conexao.php');
include('../protect.php');
include('config.php');
// Pegue o ID do usuário da sessão
$idUsuario = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $descr = $_POST['descr'];
    $loca = $_POST['loca'];
    $foto_perfil = '';

    // Verifica se um arquivo foi enviado
    if (!empty($_FILES['foto_perfil']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["foto_perfil"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verifica se o arquivo é uma imagem
        $check = getimagesize($_FILES["foto_perfil"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "O arquivo não é uma imagem.";
            $uploadOk = 0;
        }

        // Verifica se o arquivo já existe
        if (file_exists($target_file)) {
            echo "Desculpe, o arquivo já existe.";
            $uploadOk = 0;
        }

        // Verifica o tamanho do arquivo
        if ($_FILES["foto_perfil"]["size"] > 500000) {
            echo "Desculpe, o seu arquivo é muito grande.";
            $uploadOk = 0;
        }

        // Permite certos formatos de arquivo
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
            echo "Desculpe, apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
            $uploadOk = 0;
        }

        // Verifica se $uploadOk é 0 devido a um erro
        if ($uploadOk == 0) {
            echo "Desculpe, seu arquivo não foi enviado.";
        // Se tudo estiver ok, tenta fazer o upload do arquivo
        } else {
            if (move_uploaded_file($_FILES["foto_perfil"]["tmp_name"], $target_file)) {
                echo "O arquivo ". htmlspecialchars(basename($_FILES["foto_perfil"]["name"])) . " foi enviado.";
                $foto_perfil = $target_file;
            } else {
                echo "Desculpe, houve um erro ao enviar seu arquivo.";
            }
        }
    }

    // Atualiza os dados do usuário no banco de dados
    if (!empty($foto_perfil)) {
        $sql = "UPDATE usuarios SET nome=?, descr=?, loca=?, foto_perfil=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nome, $descr, $loca, $foto_perfil, $idUsuario);
    } else {
        $sql = "UPDATE usuarios SET nome=?, descr=?, loca=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nome, $descr, $loca, $idUsuario);
    }

    if ($stmt->execute()) {
        echo "Perfil atualizado com sucesso.";
        header('Location: profile.php');
    } else {
        echo "Erro ao atualizar o perfil: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
