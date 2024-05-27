<?php 
include('../conexao.php');
include('../protect.php');
include('config.php');
include('functions.php');

// Pegue o ID do usuário da sessão
$idUsuario = $_SESSION['id'];
require_once '../post/system/config.php';
require_once '../post/system/database.php';
// Busque os dados atuais do usuário no banco de dados
$sql = "SELECT nome, loca, descr, foto_perfil FROM usuarios WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$stmt->bind_result($nome, $loca, $descr, $foto_perfil);
$stmt->fetch();
$stmt->close();




$sql_posts = "SELECT id_autoi, id, conteudo, data, imagem, titulo FROM ve_posts WHERE id_autoi=?";
$stmt_posts = $conn->prepare($sql_posts);
$stmt_posts->bind_param("i", $idUsuario);
$stmt_posts->execute();
$stmt_posts->bind_result($postid_autoi, $postId, $postc, $postData, $postImagem, $posttitulo);


// Array para armazenar os posts do usuário
$post = array();

// Obtenha os resultados da consulta
while ($stmt_posts->fetch()) {
    $post[] = array(
        'id_autoi' => $postid_autoi,
        'id' => $postId,
        'conteudo' => $postc,
        'titulo' => $posttitulo,
        'data' => $postData,
        'imagem' => $postImagem
    );
}

$stmt_posts->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Clone do Facebook</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
  <!-- nav -->
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="home.php">Clone do Facebook</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="home.php">Home</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="../logout.php">Logout</a></li>
      </ul>
    </div>
  </nav>
  <!-- ./nav -->

  <!-- main -->
  <main class="container">
    <div class="row">
      <div class="col-md-3">
        <!-- edit profile -->
        <div class="panel panel-default">
          <div class="panel-body">
            <h4>Editar Perfil</h4>

            <form method="post" action="editar_perfil.php" enctype="multipart/form-data">
              <div class="form-group">
                <input class="form-control" type="text" id="nome" name="nome" placeholder="Nome" value="<?php echo htmlspecialchars($nome); ?>">
              </div>

              <div class="form-group">
                <input class="form-control" type="text" name="descr" placeholder="Descrição" value="<?php echo htmlspecialchars($descr); ?>">
              </div>
              <div class="form-group">
                <input class="form-control" type="text" name="loca" placeholder="Localização" value="<?php echo htmlspecialchars($loca); ?>">
              </div>

              <div class="form-group">
                <label for="foto_perfil">Foto de Perfil:</label>
                <input type="file" class="form-control-file" name="foto_perfil">
              </div>

              <div class="form-group">
                <input class="btn btn-primary" type="submit" name="update_profile" value="Save">
               

                <a id="removerFotoBtn" class="btn btn-danger">Remover Foto</a>

              </div>
            </form>
          </div>
        </div>

<!-- Modal de confirmação de remoção de post -->
<div id="confirmacaoRemocaoPostModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Conteúdo do modal -->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmar Remoção do Post</h4>
      </div>
      <div class="modal-body">
        <p>Tem certeza de que deseja excluir este post?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmarRemocaoPost">Excluir</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  // Quando o link "Excluir" é clicado, mostra o modal de confirmação
  $(".excluir-post").click(function(){
    $("#confirmacaoRemocaoPostModal").modal();
    // Obtenha o ID do post a ser excluído
    var postId = $(this).data('post-id');
    // Configure o botão de confirmação para redirecionar para o arquivo de remoção do post com o ID do post como parâmetro
    $("#confirmarRemocaoPost").attr("onclick", "window.location.href='excluir_post.php?id=" + postId + "'");
  });
});
</script>

<!-- Modal de confirmação -->
<div id="confirmacaoModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Conteúdo do modal -->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirmar Remoção da Foto de Perfil</h4>
      </div>
      <div class="modal-body">
        <p>Tem certeza de que deseja remover sua foto de perfil?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmarRemocao">Remover</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  // Quando o botão "Remover Foto" é clicado, mostra o modal de confirmação
  $("#removerFotoBtn").click(function(){
    $("#confirmacaoModal").modal();
  });

  // Quando o botão de confirmação do modal é clicado
  $("#confirmarRemocao").click(function(){
    // Redireciona para o arquivo remover_foto.php após a confirmação
    window.location.href = "remover_foto.php";
  });
});
</script>





        <!-- ./edit profile -->
      </div>
      <div class="col-md-6">
        <!-- user profile -->
        <div class="media">
          <div class="media-left">
    
          
            <img src="<?php echo htmlspecialchars($foto_perfil); ?>" class="media-object" style="width: 128px; height: 128px;">
          </div>
          <div class="media-body">
            <h2 class="media-heading"><?php echo htmlspecialchars($nome); ?></h2>
            <p>Descrição: <?php echo htmlspecialchars($descr); ?> Localização: <?php echo htmlspecialchars($loca); ?></p>
            <a href="../post/painel/85684f34fb-15456948da-a4d9-082c26.php" class="btn btn-success">Dashboard</a>
          </div>
        </div>
        <!-- user profile -->

        <hr>

        <!-- timeline -->
        <div>
          <!-- post -->
          <?php if (!empty($post)): ?>
          <?php foreach ($post as $p): ?>
            <div class="panel panel-default">
              <div class="panel-body">
              <?php
if (isset($p['imagem'])) {
    $imagePath = "../post/uploads/" . htmlspecialchars($p['imagem']);
    echo "<img src=\"$imagePath\" alt=\"Imagem da Postagem\" style=\"max-width: 100%; height: auto;\">";
} else {
    echo "";
}

?><br>
<br>
<h3><?php echo htmlspecialchars($p['titulo'])?></h3>
                <p><?php echo htmlspecialchars($p['conteudo']); ?></p>
                
              </div>
              <div class="panel-footer">
                <span>Postado em <?php echo htmlspecialchars($p['data']); ?> por <?php echo htmlspecialchars($_SESSION['nome']); ?> </span> 
                <span class="pull-right">
                  <a class="text-danger" href="editar_post.php?id=<?php echo htmlspecialchars($p['id']); ?>">[Editar]</a>
                  <a class="text-danger excluir-post" href="#" data-post-id="<?php echo htmlspecialchars($p['id']); ?>">[Excluir]</a>

                </span>
              </div>
            </div>
          <?php endforeach; ?>
         
<?php else: ?>
    <div class="alert alert-info" role="alert">
        Nenhuma postagem disponível.
    </div>
<?php endif; ?>
          <!-- ./post -->
        </div>
        <!-- ./timeline -->
      </div>
      <div class="col-md-3">
        <!-- friends -->
        <div class="panel panel-default">
          <div class="panel-body">
            <h4>Amigos</h4>
            <ul>
              <?php
              // Consulta para obter os amigos do usuário
              $stmt = $conn->prepare("SELECT u.id, u.nome 
                                      FROM friend_requests fr 
                                      JOIN usuarios u ON fr.recipient_id = u.id 
                                      WHERE fr.requester_id = ? AND fr.status = 'accepted'
                                      UNION
                                      SELECT u.id, u.nome 
                                      FROM friend_requests fr 
                                      JOIN usuarios u ON fr.requester_id = u.id 
                                      WHERE fr.recipient_id = ? AND fr.status = 'accepted'");
              if ($stmt === false) {
                  die("Erro ao preparar a consulta: " . $conn->error);
              }
              $stmt->bind_param("ii", $user_id, $user_id);
              $stmt->execute();
              $result = $stmt->get_result();
              if ($result === false) {
                  die("Erro ao obter resultado: " . $stmt->error);
              }
              while ($row = $result->fetch_assoc()) {
                  echo "<li>
                          <a href='perfi.php?user_id={$row['id']}'>{$row['nome']}</a>
                          <a class='text-danger' href='remove_friend.php?friend_id={$row['id']}'>[Remover amigo]</a>
                        </li>";
              }
              $stmt->close();
              ?>
            </ul>
          </div>
        </div>
        <script>
    $(document).ready(function() {
        $('.see-more-button').click(function() {
            var fullContent = $(this).prev('.full-content');
            var postContent = $(this).siblings('.post-content');

            if (fullContent.is(':visible')) {
                fullContent.hide();
                postContent.show();
                $(this).text('Ver Mais');
            } else {
                fullContent.show();
                postContent.hide();
                $(this).text('Ver Menos');
            }
        });

        $('.like-button').click(function() {
            var post_id = $(this).data('post-id');
            var likeButton = $(this);
            var likesCountSpan = likeButton.siblings('.likes-count');

            $.ajax({
                url: 'like_post.php',
                type: 'POST',
                data: { post_id: post_id },
                success: function(response) {
                    if (response === 'liked') {
                        likeButton.text('Descurtir');
                        var currentLikes = parseInt(likesCountSpan.text());
                        likesCountSpan.text(currentLikes + 1 + ' curtidas');
                    } else if (response === 'unliked') {
                        likeButton.text('Curtir');
                        var currentLikes = parseInt(likesCountSpan.text());
                        likesCountSpan.text(currentLikes - 1 + ' curtidas');
                    }
                }
            });
        });
    });
</script>
        <!-- ./friends -->
      </div>
    </div>
  </main>
  <!-- ./main -->

  <!-- footer -->
  <footer class="container text-center">
    <ul class="nav nav-pills pull-right">
      <li></li>
    </ul>
  </footer>
  <!-- ./footer -->
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/script.js"></script>
</body>
</html>

<?php
$conn->close();
?>
