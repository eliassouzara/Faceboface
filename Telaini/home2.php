<?php 
include('../conexao.php');
include('../protect.php');
include('config.php');
// Função para buscar todos os usuários que ainda não são amigos do usuário logado
function getPotentialFriends($user_id) {
    global $conn;
    
    // Buscar todos os usuários que não são o próprio usuário e que ainda não são amigos
    $sql = "SELECT u.id, u.nome 
            FROM usuarios u 
            WHERE u.id != ? 
            AND u.id NOT IN (
                SELECT recipient_id FROM friend_requests WHERE requester_id = ? AND status = 'accepted'
                UNION
                SELECT requester_id FROM friend_requests WHERE recipient_id = ? AND status = 'accepted'
            )";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $users;
}

$user_id = $_SESSION['id']; // Obtém o ID do usuário logado
$potential_friends = getPotentialFriends($user_id); // Obtém os usuários que podem ser adicionados como amigos
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
        
        <!-- ./profile brief -->

        <!-- friend requests -->
       
          
            <ul>
              <?php
              // Buscar pedidos de amizade pendentes
              $stmt = $conn->prepare("SELECT fr.id, u.nome 
                                      FROM friend_requests fr 
                                      JOIN usuarios u ON fr.requester_id = u.id 
                                      WHERE fr.recipient_id = ? AND fr.status = 'pending'");
              $stmt->bind_param("i", $user_id);
              $stmt->execute();
              $result = $stmt->get_result();
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
