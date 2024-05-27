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

// Função para obter potenciais amigos
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

// Função para obter amigos
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
include('config.php');

$posts = DBRead('posts', "WHERE status = 1 ORDER BY data DESC");

if (!$posts) {
  echo '<h2>Nenhum post!</h2>';
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
<div class="post-container" style="margin-bottom: 20px;">
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
                    if (strlen($str) > 200) {
                        $str = substr($str, 0, 200) . '... <a href="./post/single.php?id=' . $post['id'] . '">Ver mais</a>';
                    }
                    echo $str;
                    ?>
                </p>
            </div>
            <hr>
            <div style="display: flex; align-items: center;">
                <form action="curtir.php" method="post" style="margin-right: 10px;">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <button type="submit" style="border: none; background: none;">
                        <i class="glyphicon glyphicon-thumbs-up" style="color:
                         <?php echo $liked ? 'blue' : 'inherit'; ?>">
                         </i> Curtir
                    </button>
                </form>
                <span id="like-count-<?php echo $post['id']; ?>">
                    <?php
                    // Consulta para obter a contagem de curtidas
                    $sql = "SELECT COUNT(*) AS like_count FROM post_likes WHERE post_id = ?";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        die("Erro ao preparar a consulta: " . $conn->error);
                    }
                    $stmt->bind_param("i", $post['id']);
                    $stmt->execute();
                    $stmt->bind_result($like_count);
                    $stmt->fetch();
                    $stmt->close();

                    echo $like_count . ' curtidas';
                    ?>
                </span>
                <a href="./post/single.php?id=<?php echo $post['id']; ?>" style="margin-left: auto;">Comentários</a>
            </div>
        </div>
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
        <!-- friend list -->
        <div class="panel panel-default">
          <div class="panel-body">
            <h4>Adicionar Amigos</h4>
            <ul>
              <?php foreach ($potential_friends as $user) : ?>
                <li>
                  <?php echo htmlspecialchars($user['nome']); ?> 
                  <a href="add_friend.php?id=<?php echo $user['id']; ?>">[adicionar]</a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <!-- ./friend list -->

        <!-- current friends -->
        <div class="panel panel-default">
          <div class="panel-body">
            <h4>Amigos</h4>
            <ul>
              <?php foreach ($friends as $friend) : ?>
                <li>
                  <?php echo htmlspecialchars($friend['nome']); ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
        <!-- ./current friends -->
      </div>
    </div>
  </main>
  <!-- ./main -->
</body>
</html>
