<?php
include('../conexao.php');
include('../protect.php');
include('config.php');

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}

$post_id = $_GET['post_id']; // Obtém o ID da postagem da URL

// Consulta ao banco de dados para recuperar os detalhes da postagem com base no ID

$stmt = $conn->prepare("SELECT p.conteudo, p.data, u.nome AS autor
                        FROM ve_posts p
                        JOIN usuarios u ON p.autor = u.nome
                        WHERE p.id = ? AND p.status = 1");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <title>Clone do Facebook</title>
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
    
        <!-- profile brief -->
        
        <!-- ./profile brief -->

        <!-- friend requests -->
      
          <div class="panel-body">
            
             
            </ul>
          </div>
        </div>
        <!-- ./friend requests -->
      </div>
     
        <!-- post form -->
        <form method="post" action="search.php">
    <div class="input-group">
        <input class="form-control" type="text" name="search_query" placeholder="Pesquise...">
        <span class="input-group-btn">
            <button class="btn btn-success" type="submit" name="search">Buscar</button>
        </span>
    </div>
</form>
<hr>
        <!-- ./post form -->

        <!-- feed -->
        <div>
       <!-- post -->
<?php

require_once '../post/system/config.php';
require_once '../post/system/database.php';
include('config.php');

$posts = DBRead('posts', "WHERE status = 1 ORDER BY data DESC");

if (!$posts) {
  echo '    <div class="alert alert-info" role="alert">
  Nenhuma postagem disponível.
</div>
<?php endif; ?>';
} else {
  foreach ($posts as $post) {
      $user_id = $_SESSION['id'];

      // Caminho padrão da foto de perfil
      $author_profile_picture = './uploads/default_avatar.png';
      
      // Consulta para buscar o caminho da foto de perfil do autor do post
      $sql = "SELECT foto_perfil FROM usuarios WHERE id = ?";
      $stmt = $conn->prepare($sql);
      if ($stmt === false) {
          die("Erro ao preparar a consulta: " . $conn->error);
      }
      $stmt->bind_param("i", $post['id_autoi']);
      $stmt->execute();
      $stmt->store_result();

      if ($stmt->num_rows > 0) {
          $stmt->bind_result($foto_perfil);
          $stmt->fetch();
          
          if ($foto_perfil) {
              $author_profile_picture = '' . $foto_perfil;
              // Verificação adicional para verificar se o arquivo existe
              if (!file_exists($author_profile_picture)) {
                  $author_profile_picture = './uploads/default_avatar.png';
                  
              }
          }
      } else {
          echo "<p>Nenhuma foto de perfil encontrada para o usuário ID {$post['id_autoi']}.</p>";
      }
      $stmt->close();

      // Verifica se o usuário já curtiu o post
      $sql = "SELECT * FROM post_likes WHERE id = ? AND post_id = ?";
      $stmt = $conn->prepare($sql);
      if ($stmt === false) {
          die("Erro ao preparar a consulta: " . $conn->error);
      }
      $stmt->bind_param("ii", $user_id, $post['id']);
      $stmt->execute();
      $result = $stmt->get_result();
      $liked = $result->num_rows > 0;
      $stmt->close();

?>
<div class="post-container" style="margin-left:3;">
    <div class="panel panel-default">
        <div class="panel-footer">
            <img src="../post/uploads/<?php echo htmlspecialchars($post['imagem']); ?>" alt="Imagem da Postagem" style="max-width: 100%; height: auto;">
            <h2>
                <img src="<?php echo htmlspecialchars($author_profile_picture); ?>" class="media-object" style="width: 37px;height: 36px;float: left;margin-right: 10px;border-radius: 28px;">
                <b><?php echo $post['titulo']; ?></b>
            </h2>
            <div class="panel-body">
                <p>
                    
                    <?php
                    $str = strip_tags($post['conteudo']);
                    $len = strlen($str);
                    $max = 100; // Ajuste o valor conforme necessário

                    if ($len <= $max) {
                        echo '<div class="post-content">' . $str . '</div>';
                    } else {
                        $shortenedContent = substr($str, 0, $max);
                        echo '<div class="post-content">' . $shortenedContent . '...</div>';
                        echo '<div class="full-content" style="display: none;">' . $str . '</div>';
                        echo '<button class="btn btn-primary see-more-button" style="margin-left: -15px;">Ver Mais</button>';
                    }
                    ?>
                </p>
                <span>Postado em <b><?php echo date('d/m/Y', strtotime($post['data'])) ?></b> por <?php echo $post['autor']; ?></span>
                <button class="btn btn-primary like-button" data-post-id="<?php echo $post['id']; ?>">
                    <?php echo $liked ? 'Descurtir' : 'Curtir'; ?>
                </button>



                
                <span class="likes-count"><?php echo $post['likes_count']; ?> curtidas</span>
                <hr>
                <!-- Seção de comentários -->
                <div class="comments-section" data-post-id="<?php echo $post['id']; ?>">
                    <h4>Comentários:</h4>
                    <?php
                    // Consulta para buscar os comentários associados a esta postagem
                    $stmt = $conn->prepare("SELECT pc.id, pc.comment, pc.created_at, pc.user_id, u.nome AS commenter_name
                                            FROM post_comments pc
                                            JOIN usuarios u ON pc.user_id = u.id
                                            WHERE pc.post_id = ?
                                            ORDER BY pc.created_at DESC
                                            LIMIT 5"); // Limitar inicialmente a 5 comentários
                    $stmt->bind_param("i", $post['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Exibir os comentários
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

                    // Adicionar botão Ver Mais se houver mais comentários
                    if ($result->num_rows == 5) {
                        echo "<button class='btn btn-primary btn-load-more-comments' data-offset='5'>Ver Mais</button>";
                    }

                    $stmt->close();
                    ?>
                </div>
                <!-- Formulário para adicionar um novo comentário -->
                <form method="post" action="add_comment.php">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <textarea name="comment" class="form-control" placeholder="Adicione um comentário" required></textarea>
                    <button type="submit" class="btn btn-primary">Enviar Comentário</button>
                </form>
            </div>
        </div>
        
    </div>
</div>

<?php
    }
}
?>

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

      </div>
  </div>
      <div class="col-md-3">
 


  </main>
  <!-- ./main -->

  <!-- load more comments script -->
  <script>
    $(document).ready(function() {
        // Quando o botão "Ver Mais" for clicado
        $('.comments-section').on('click', '.btn-load-more-comments', function() {
            const postId = $(this).closest('.comments-section').data('post-id');
            const offset = $(this).data('offset');
            const button = $(this);
            
            // Esconder o botão "Ver Mais"
            button.hide();
            
            // Fazer uma solicitação AJAX para carregar mais comentários
            $.ajax({
                url: 'load_more_comments.php', // O arquivo PHP que carrega mais comentários
                type: 'POST',
                data: {
                    post_id: postId, // Passar o ID da postagem para o PHP
                    offset: offset // Passar o número de comentários já carregados
                },
                success: function(response) {
                    // Adicionar os novos comentários ao final da lista de comentários existente
                    button.before(response);
                    // Atualizar o offset
                    button.data('offset', offset + 5);
                    // Mostrar o botão "Ver Mais" novamente, caso ainda haja mais comentários para carregar
                    if ($(response).find('.comment').length == 5) {
                        button.show();
                    }
                },
                error: function(xhr, status, error) {
                    // Lidar com erros, se necessário
                    console.error(error);
                }
            });
        });
    });
  </script>
  <!-- ./load more comments script -->

</body>
</html>
 




