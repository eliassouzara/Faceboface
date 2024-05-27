<?php
session_start();
require '../system/config.php';
require '../system/database.php';

if (!isset($_SESSION['id'])) {
    exit;
}

$usuarioId = $_SESSION['id'];
$nomeUsuario = $_SESSION['nome'];

if (isset($_POST['publicar'])) {
    $form['titulo'] = DBEscape(strip_tags(trim($_POST['titulo'])));
    $form['autor'] = $nomeUsuario;
    $form['id_autoi'] = $usuarioId;
    $form['status'] = DBEscape(strip_tags(trim($_POST['status'])));
    $form['data'] = date('Y-m-d');
    $form['conteudo'] = str_replace('\r\n', "\n", DBEscape(trim($_POST['conteudo'])));

    // Verificar se a imagem foi enviada e tratar o upload
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $imagemTmpPath = $_FILES['imagem']['tmp_name'];
        $imagemNome = basename($_FILES['imagem']['name']);
        $imagemTipo = strtolower(pathinfo($imagemNome, PATHINFO_EXTENSION));
        $permitidos = ['jpg', 'jpeg', 'png', 'gif'];

        // Verificar se o arquivo é uma imagem válida
        $check = getimagesize($imagemTmpPath);
        if ($check !== false && in_array($imagemTipo, $permitidos)) {
            $imagemNovoNome = uniqid('img_') . '.' . $imagemTipo;
            $destinoImagem = '../uploads/' . $imagemNovoNome;

            // Verificar se o diretório de destino existe
            if (!is_dir('../uploads')) {
                mkdir('../uploads', 0777, true);
            }

            if (move_uploaded_file($imagemTmpPath, $destinoImagem)) {
                $form['imagem'] = $imagemNovoNome;
            } else {
                echo "Erro ao mover a imagem enviada.";
                echo '<a href="85684f34fb-15456948da-a4d9-082c26.php" class="button-link">Voltar</a>';
                exit;
            }
        } else {
            echo "Tipo de arquivo não permitido ou arquivo não é uma imagem válida.";
            echo '<a href="85684f34fb-15456948da-a4d9-082c26.php" class="button-link">Voltar</a>';
            exit;
        }
    } else {
        echo "Erro no upload da imagem.";
        echo '<a href="85684f34fb-15456948da-a4d9-082c26.php" class="button-link">Voltar</a>';
        exit;
    }

    $form = DBEscape($form);

    if (empty($form['titulo'])) {
        echo "Preencha o campo Título.";
        echo '<a href="85684f34fb-15456948da-a4d9-082c26.php" class="button-link">Voltar</a>';
    } else if (!isset($form['status'])) {
        echo "Preencha o campo Status.";
        echo '<a href="85684f34fb-15456948da-a4d9-082c26.php" class="button-link">Voltar</a>';
    } else if (empty($form['conteudo'])) {
        echo "Preencha o campo Conteúdo.";
        echo '<a href="85684f34fb-15456948da-a4d9-082c26.php" class="button-link">Voltar</a>';
    } else if (empty($form['imagem'])) {
        echo "Envie uma imagem.";
        echo '<a href="85684f34fb-15456948da-a4d9-082c26.php" class="button-link">Voltar</a>';
    } else {
        if (DBCreate('posts', $form)) {
            echo "Sua postagem foi enviada com sucesso!";
            header("Location: ../../Telaini/profile.php");
            exit;
        } else {
            echo "Desculpe, ocorreu um erro ao salvar sua postagem.";
            echo '<a href="85684f34fb-15456948da-a4d9-082c26.php" class="button-link">Voltar</a>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Adicionar Postagem</title>
</head>
<body>
    <h2>Adicionar Postagem</h2>
    <hr>
   
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card">
                    <div class="card-body">
                        <div class="app-brand justify-content-center">
                            <a href="index.html" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <!-- SVG LOGO AQUI -->
                                </span>
                                <span class="app-brand-text demo text-body fw-bolder">
                                    Adicionar Postagem
                                </span>
                            </a>
                        </div>
                        <h4 class="mb-2">Insira aqui á baixo as informações adicionais desejadas</h4>
                        <form id="formAuthentication" class="mb-3" method="POST" action="" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="titulo" class="form-label">Título do anúncio</label>
        <input type="text" class="form-control" id="titulo" name="titulo" autofocus required />
    </div>
    <div class="mb-3">
        <label class="form-label" for="status">Status</label>
        <select name="status" id="status" class="form-control">
            <option value="1" selected>Ativo</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="conteudo" class="form-label">Conteúdo</label>
        <textarea class="form-control" name="conteudo" cols="50" rows="15" required></textarea>
    </div>
    <div class="mb-3">
        <label for="imagem" class="form-label">Imagem</label>
        <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*" required/>
    </div>
    <button type="submit" class="btn btn-primary d-grid w-100" name="publicar">Publicar</button>
</form>






                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
