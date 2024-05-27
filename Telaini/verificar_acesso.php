<?php
// Verificar se o usuário está autenticado
session_start();
if (!isset($_SESSION['id'])) {
    // Se não estiver autenticado, redirecione para a página de login
    header("Location: login.php");
    exit();
}

// Verificar se o ID do usuário está presente na URL
if (isset($_GET['id'])) {
    $perfilUsuario = $_GET['id'];

    // Verificar se o usuário tem permissão para acessar o perfil
    if ($perfilUsuario != $_SESSION['id']) {
        // Se o ID do perfil não corresponder ao ID do usuário na sessão, redirecione para uma página de acesso negado
        header("Location: acesso_negado.php");
        exit();
    }
} else {
    // Se o ID do usuário não estiver presente na URL, redirecione para uma página de erro
    header("Location: erro.php");
    exit();
}

// Resto do seu código para exibir o perfil do usuário
?>
