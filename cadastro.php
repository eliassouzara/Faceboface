<?php

if(isset($_POST['email'], $_POST['nome'], $_POST['datet'], $_POST['senha'])){
    include('conexao.php');

    // Validar e Sanitizar os dados do formulário
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
    $datet = filter_var($_POST['datet'], FILTER_SANITIZE_STRING);
    $senha = password_hash ($_POST['senha'], PASSWORD_DEFAULT);

    // Verificar se os dados foram validados corretamente
    if($email && $nome && $datet && $senha) {
        // Verificar se o e-mail já está cadastrado
        $stmt_check = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if($stmt_check->num_rows > 0) {
            echo "<center><h1>O e-mail já está cadastrado.<h1>";
        } else {
            // Preparar e executar a consulta usando prepared statement
            $stmt_insert = $mysqli->prepare("INSERT INTO usuarios (email, senha, nome, datet) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("ssss", $email, $senha, $nome, $datet);
            $stmt_insert->execute();
            
            // Redirecionar após o cadastro
            header("Location: index.php");
            exit; // Encerrar o script após o redirecionamento
        }
    } else {
        echo "Dados do formulário inválidos.";
    }
}

?>



<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tela de cadastro Fecebook</title>
    <link rel="stylesheet" href="style.css" />
    
  </head>
  <body>
    <div class="content">
      <div class="flex-div">
        <div class="name-content">
          <h1 class="logo">Facebook</h1>
          <p>Faça login e Conecte-se com amigos e com o mundo ao seu redor no Facebook.  </p>
        </div>
          <form action="" method="POST">
            <input type="text" placeholder="Email" name="email" required />
            <input type="text" placeholder="Nome" name="nome" required />
            <input type="date" name="datet" id="datet">
            <input type="password" placeholder="Password" name="senha" required>
            <button class="login" type="submit">Fazer conta</button>

            
            <a href="index.php">Já tenho conta</a>
            
           
          </form>
      </div>
    </div>
 
  </body>
</html>