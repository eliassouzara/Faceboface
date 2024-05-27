<?php
include('../conexao.php');
include('../protect.php');
include('config.php');

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}

$search_query = $_POST['search_query'];
$user_id = $_SESSION['id'];

$posts = [];
$users = [];

// Procurar postagens
$stmt = $conn->prepare("SELECT p.id, p.titulo, p.data, u.nome AS autor
                        FROM ve_posts p
                        JOIN usuarios u ON p.autor = u.nome
                        WHERE p.titulo LIKE ? AND p.status = 1
                        ORDER BY p.data DESC");
$search_term = "%" . $search_query . "%";
$stmt->bind_param("s", $search_term);
$stmt->execute();
$result = $stmt->get_result();
if ($result !== false) {
    $posts = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();

// Procurar usuários
$stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE nome LIKE ? AND id != ?");
$search_term = "%" . $search_query . "%";
$stmt->bind_param("si", $search_term, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result !== false) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resultados da Pesquisa</title>
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

    <main class="container">
        <h2>Resultados da Pesquisa</h2>
        <div class="row">
            <div class="col-md-6">
                <h4>Postagens</h4>
                <?php if (count($posts) > 0) { ?>
                    <ul>
                        <?php foreach ($posts as $post) { ?>
                            <li>
                            <p><?php echo $post['titulo']; ?></p>
        <p><strong>Autor:</strong> <?php echo $post['autor']; ?> <strong>Data:</strong> <?php echo date('d/m/Y', strtotime($post['data'])); ?></p>
        <p><a href="post.php?post_id=<?php echo $post['id']; ?>">Ver postagem completa</a></p> <!-- Adiciona o link para a postagem completa -->
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p>Nenhuma postagem encontrada.</p>
                <?php } ?>
            </div>
            <div class="col-md-6">
                <h4>Usuários</h4>
                <?php if (count($users) > 0) { ?>
                    <ul>
                        <?php foreach ($users as $user) { ?>
                            <li>
                                <a href="perfi.php?user_id=<?php echo $user['id']; ?>"><?php echo $user['nome']; ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p>Nenhum usuário encontrado.</p>
                <?php } ?>
            </div>
        </div>
    </main>

    <footer class="container text-center">
        <ul class="nav nav-pills pull-right">
            <li></li>
        </ul>
    </footer>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/script.js"></script>
</body>
</html>
