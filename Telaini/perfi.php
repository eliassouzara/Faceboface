<?php
include('../conexao.php');
include('../protect.php');
include('config.php');

// Verifique se o ID do usuário foi passado
if (!isset($_GET['user_id'])) {
    die("ID do usuário não fornecido.");
}

$viewUserId = intval($_GET['user_id']);
$currentUserId = $_SESSION['id'];

// Busque os dados do usuário no banco de dados

$sql = "SELECT nome, loca, descr, foto_perfil FROM usuarios WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $viewUserId);
$stmt->execute();
$stmt->bind_result($nome, $loca, $descr, $foto_perfil);
$stmt->fetch();
$stmt->close();
// Verificar o status da amizade
$sql_friend = "SELECT requester_id, status FROM friend_requests WHERE (requester_id = ? AND recipient_id = ?) OR (requester_id = ? AND recipient_id = ?)";
$stmt_friend = $conn->prepare($sql_friend);
$stmt_friend->bind_param("iiii", $currentUserId, $viewUserId, $viewUserId, $currentUserId);
$stmt_friend->execute();
$stmt_friend->bind_result($requester_id, $friend_status);
$stmt_friend->fetch();
$stmt_friend->close();

$sql_posts = "SELECT id_autoi, id, conteudo, data FROM ve_posts WHERE id_autoi=?";
$stmt_posts = $conn->prepare($sql_posts);
$stmt_posts->bind_param("i", $viewUserId);
$stmt_posts->execute();
$stmt_posts->bind_result($postid_autoi, $postId, $postc, $postData);

// Array para armazenar os posts do usuário
$posts = array();

// Obtenha os resultados da consulta
while ($stmt_posts->fetch()) {
    $posts[] = array(
        'id_autoi' => $postid_autoi,
        'id' => $postId,
        'conteudo' => $postc,
        'data' => $postData
    );
}

$stmt_posts->close();

?>

<!DOCTYPE html>
<html>
<head>
  <title>Perfil do Usuário</title>
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
        <li><a href="profile.php">Meu Perfil</a></li>
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
            <h4>Perfil de <?php echo htmlspecialchars($nome); ?></h4>
            <?php if ($currentUserId != $viewUserId): ?>
                <?php if (isset($friend_status) && $friend_status == 'accepted'): ?>
                    <form action="remove_friendd.php" method="post">
                        <input type="hidden" name="friend_id" value="<?php echo $viewUserId; ?>">
                        <button type="submit" class="btn btn-danger">Remover Amigo</button>
                    </form>
                <?php elseif (isset($friend_status) && $friend_status == 'pending' && $requester_id == $viewUserId): ?>
                    <form action="accept_friend.php" method="post">
                        <input type="hidden" name="friend_id" value="<?php echo $viewUserId; ?>">
                        <button type="submit" class="btn btn-primary">Aceitar Pedido</button>
                    </form>
                <?php elseif (isset($friend_status) && $friend_status == 'pending' && $requester_id == $currentUserId): ?>
                    <form action="cancel_request.php" method="post">
                        <input type="hidden" name="friend_id" value="<?php echo $viewUserId; ?>">
                        <button type="submit" class="btn btn-warning">Cancelar Pedido</button>
                    </form>
                <?php else: ?>
                    <form action="add_friendd.php" method="post">
                        <input type="hidden" name="friend_id" value="<?php echo $viewUserId; ?>">
                        <button type="submit" class="btn btn-primary">Adicionar Amigo</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
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
          </div>
        </div>
        <!-- user profile -->

        <hr>

        <!-- timeline -->
        <div>
          <!-- post -->
          <?php if (!empty($posts)): ?>
    <?php foreach ($posts as $p): ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <p><?php echo htmlspecialchars($p['conteudo']); ?></p>
            </div>
            <div class="panel-footer">
                <span>Postado em <?php echo htmlspecialchars($p['data']); ?> por <?php echo htmlspecialchars($nome); ?></span>
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
