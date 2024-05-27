<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../conexao.php');
include('../protect.php');
include('config.php');
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
            <h4><?php echo $_SESSION['nome']; ?></h4>
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
        <!-- add friend -->
        <div class="panel panel-default">
          <div class="panel-body">
            <h4>Adicionar amigo</h4>
            <form method="POST" action="send_friend_request.php">
              <div class="form-group">
                <label for="friend_id">Selecionar usuário:</label>
                <select class="form-control" name="friend_id" id="friend_id">
                  <?php
                  foreach ($potential_friends as $friend) {
                    echo "<option value='{$friend['id']}'>{$friend['nome']}</option>";
                  }
                  ?>
                </select>
              </div>
              <button type="submit" class="btn btn-primary">Enviar Pedido de Amizade</button>
            </form>
          </div>
        </div>
        <!-- ./add friend -->

        <!-- feed -->
        <div>
          <!-- post -->

          <?php 
          require_once '../post/system/config.php';
          require_once '../post/system/database.php';

          $posts = DBRead('posts', "WHERE status = 1 ORDER BY data DESC");

          if(!$posts) {
              echo '<h2>Nenhum post!!</h2>';
          } else {
              foreach ($posts as $post) {
                  include_once('./config.php');

                  if (!empty($post['id'])) {
                      $id = $post['id'];
                      $sqlSelect = "SELECT * FROM usuarios WHERE id= $id";
                      $result = $conn->query($sqlSelect);

                      if ($result->num_rows > 0) {
                          while ($user_data = mysqli_fetch_assoc($result)) {
                              $nome = $user_data['nome'];
                          }
                      }
                  }
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
              </div>
          </div>
          <hr>
          <?php 
              } // Fechamento do foreach
          } // Fechamento do else
          ?>
          <!-- ./post -->
        </div>
        <!-- ./feed -->
      </div>
      <div class="col-md-3">
        <!-- friends -->
        <div class="panel panel-default">
          <div class="panel-body">
            <h4>Amigos</h4>
            <ul>
              <?php
              foreach ($friends as $friend) {
                echo "<li>
                        <a href='profile.php?id={$friend['id']}'>{$friend['nome']}</a>
                        <a class='text-danger' href='unfriend.php?id={$friend['id']}'>[Não amigo]</a>
                      </li>";
              }
              ?>
            </ul>
          </div>
        </div>

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
            <a href='#'>{$row['nome']}</a>
            <a class='text-danger' href='remove_friend.php?friend_id={$row['id']}'>[Remover amigo]</a>
          </li>";
}
$stmt->close();
?>

    </ul>
  </div>
</div>









<!-- ./friends -->

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
