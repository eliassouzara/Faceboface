
<?php

  // Inculi o arquivo de conexão com o banco de dados.
  include('conexao.php');
  //Se o metado post email existe
  if(isset ($_POST['email'])){

  //Pega o email e a senha fornecidos pelo usuário
  $email = $_POST['email'];
  $senha = $_POST['senha'];

 //Faz uma consulta SQL para selecionar da tabela usuarios o email digitado.
  $sql_code = "SELECT * FROM usuarios WHERE email = ? LIMIT 1 ";
 //Prepara a consulta SQL.
  $stmt = $mysqli->prepare($sql_code);
  //Lifa o parâmetro da consulta ao valor do email.
  $stmt->bind_param("s", $email);
  //Executa a consulta.
  $stmt->execute();
  //Obtem o resultado da consulta.
  $result = $stmt->get_result();

  //Verifica se a consulta retornou algum resultado.
  if($result->num_rows > 0){
  //Pega os dados do usuário como um array associativo.
  $usuario = $result->fetch_assoc();
  //Verificar se a senha fornecida corresponde á senha armazenada no banco de dados.
  if(password_verify($senha, $usuario['senha'])){
    //Inicia a sessão
    if(!isset($_SESSION)){
      session_start();

    }
  //Armazena informações do usuário na sessão 
    $_SESSION['id']= $usuario['id'];
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['descr'] = $usuario['descr'];

    //Redireciona o usuário para a página inicial após logar.
    header("Location:./Telaini/home.php");
    exit;
  }else{
    // Exibe uma menssagem de erro se a senha ou email estiver incorreto.
    echo "Falha ao logar! Por favor, tente novamente";
  }
}
}

?>

<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tela de login Fecebook</title>
    <link rel="stylesheet" href="style.css" />
    
  </head>
  <body>
    <div class="content">
      <div class="flex-div">
        <div class="name-content">
          <h1 class="logo">Facebook</h1>
          <p>Conecte-se com amigos e com o mundo ao seu redor no Facebook.  </p>
        </div>
          <form action="" method="POST">
            <input type="text" placeholder="Email" name="email" required />
            <input type="password" placeholder="Password" name="senha" required>
            <button class="login" type="submit">login</button>

            
            <a href="#">Esqueceu sua senha ?</a>
            <hr>
            <a href="cadastro.php">Criar nova conta</a>
          </form>
      </div>
    </div>
 
  </body>
</html>
