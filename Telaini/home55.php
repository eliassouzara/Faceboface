<?php 
include('../conexao.php');
include('../protect.php');
include('config.php');
include('functions.php'); // Inclui o arquivo com as funções PHP

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
  header("Location: ../login.php");
  exit();
}

$user_id = $_SESSION['id']; // Obtém o ID do usuário logado
$friend_requests = listFriendRequests($user_id); // Lista os pedidos de amizade pendentes

function getPotentialFriends($user_id) {
  global $conn;

  $sql = "SELECT u.id, u.nome 
          FROM usuarios u 
          WHERE u.id != ? 
          AND u.id NOT IN (
              SELECT recipient_id FROM friend_requests WHERE requester_id = ? AND status = 'accepted'
              UNION
              SELECT requester_id FROM friend_requests WHERE recipient_id = ? AND status = 'accepted'
          )";
  $stmt = $conn->prepare($sql);
  if ($stmt === false) {
      die("Erro ao preparar a consulta: " . $conn->error);
  }
  $stmt->bind_param("iii", $user_id, $user_id, $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result === false) {
      die("Erro ao obter resultado: " . $stmt->error);
  }
  $users = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();

  return $users;
}

function getFriends($user_id) {
  global $conn;

  $sql = "SELECT u.id, u.nome 
          FROM usuarios u 
          WHERE u.id IN (
              SELECT recipient_id FROM friend_requests WHERE requester_id = ? AND status = 'accepted'
              UNION
              SELECT requester_id FROM friend_requests WHERE recipient_id = ? AND status = 'accepted'
          )";
  $stmt = $conn->prepare($sql);
  if ($stmt === false) {
      die("Erro ao preparar a consulta: " . $conn->error);
  }
  $stmt->bind_param("ii", $user_id, $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result === false) {
      die("Erro ao obter resultado: " . $stmt->error);
  }
  $friends = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();

  return $friends;
}

$user_id = $_SESSION['id']; // Obtém o ID do usuário logado
$potential_friends = getPotentialFriends($user_id); // Obtém os usuários que podem ser adicionados como amigos
$friends = getFriends($user_id); // Obtém a lista de amigos do usuário logado

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
      <div class="col-md-3">
        <!-- profile brief -->
        <div class="panel panel-default">
          <div class="panel-body">
            <h4><?php echo $_SESSION['nome'];?></h4>
            <p>Descrição: <?php echo $_SESSION['descr']; ?></p>
          </div>
        </div>
        <!-- ./profile brief -->

        <!-- friend requests -->
        <div class="panel panel-default">
          <div class="panel-body">
            <h4>Pedidos de amizade</h4>
            <ul>
              <?php
              $stmt = $conn->prepare("SELECT fr.id, u.nome 
                                      FROM friend_requests fr 
                                      JOIN usuarios u ON fr.requester_id = u.id 
                                      WHERE fr.recipient_id = ? AND fr.status = 'pending'");
              if ($stmt === false) {
                  die("Erro ao preparar a consulta: " . $conn->error);
              }
              $stmt->bind_param("i", $user_id);
              $stmt->execute();
              $result = $stmt->get_result();
              if ($result === false) {
                  die("Erro ao obter resultado: " . $stmt->error);
              }
              while ($row = $result->fetch_assoc()) {
                echo "<li>
                        <a href='#'>{$row['nome']}</a>
                        <a class='text-success' href='accept_request.php?id={$row['id']}'>[accept]</a> 
                        <a class='text-danger' href='decline_request.php?id={$row['id']}'>[decline]</a>
                      </li>";
              }
              $stmt->close();
              ?>
            </ul>
          </div>
        </div>
        <!-- ./friend requests -->
      </div>
      <div class="col-md-6">
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

$posts = DBRead('posts', "WHERE status = 1 ORDER BY data DESC");

if (!$posts) {
    echo '<h2>Nenhum post!</h2>';
} else {
    foreach ($posts as $post) {
        $user_id = $_SESSION['id'];
        
        // Verifica se o usuário já curtiu o post
        $sql = "SELECT * FROM post_likes WHERE user_id = ? AND post_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $post['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $liked = $result->num_rows > 0;
        $stmt->close();
?>
        <div class="panel panel-default">
            <div class="panel-body">
                <p><?php
                    $str = strip_tags($post['conteudo']);
                    $len = strlen($str);
                    $max = 20;

                    if ($len <= $max)
                        echo $str;
                    else
                        echo substr($str, 0, $max) . '...';
                ?></p>
            </div>
            <div class="panel-footer">
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
                    <textarea name="comment" class="form-control" placeholder="Adicione um comentário"></textarea>
                    <button type="submit" class="btn btn-primary">Enviar Comentário</button>
                </form>
            </div>
        </div>
<?php
    }
}
?>
        </div>
        <!-- ./feed -->
      </div>
      <div class="col-md-3">
        <!-- add friends -->
        <div class="panel panel-default">
          <div class="panel-body">
          <label for="friend_id">Opições:</label>
  </select>
  <a href="home2.php"><button  type="submit">Enviar Pedido de Amizade</button></a>

<br>
<br>
  <a href="removed_friends.php"> <button  type="submit">Adicionar amigos removidos</button></a>


          </div>
        </div>
        <!-- ./add friends -->

        <!-- friends list -->
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
        <!-- ./friends list -->
      </div>
    </div>
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
