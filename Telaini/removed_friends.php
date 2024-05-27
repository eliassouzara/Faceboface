<?php
include('../conexao.php');
include('../protect.php');
include('config.php');

// Verifique se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Consulta para obter amigos removidos
$stmt = $conn->prepare("
    SELECT u.id, u.nome 
    FROM friend_requests fr 
    JOIN usuarios u ON (fr.requester_id = u.id OR fr.recipient_id = u.id) 
    WHERE (fr.requester_id = ? OR fr.recipient_id = ?) 
    AND u.id != ?
    AND fr.status = 'declined' 
    AND fr.removed = 1
");
$stmt->bind_param("iii", $_SESSION['id'], $_SESSION['id'], $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Amigos Removidos</title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
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

    <main class="container">
        <h2>Amigos Removidos</h2>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li>
                    <?php echo htmlspecialchars($row['nome']); ?>
                    <form method="post" action="remove_permanently.php" style="display:inline;">
                        <input type="hidden" name="friend_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="remove_friend" class="btn btn-danger">Remover Permanentemente</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    </main>

    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/script.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
