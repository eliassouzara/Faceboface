<?php
	require_once 'system/config.php';
	require_once 'system/database.php';
	
	if(!isset($_SESSION)){
		session_start();
	}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<title></title>
</head>

<body>
	<h1>Não exete post</h1>
	<?php
		$posts = DBRead('posts', "WHERE status = 1 ORDER BY data DESC");

		if (!$posts)
			echo '<h2>Nenhuma postagem encontrada!</h2>';
		else
			foreach ($posts as $post): 
	?>
	<h2>
		<a href="./exibe.php?id=<?php echo $post['id']; ?>" title="<?php echo $post['titulo']; ?>">
			<?php echo $post['titulo']; ?>
			<?php echo $_SESSION ['nome']; ?>
		</a>
	</h2>

	<p>
		por <b><?php echo $post['autor']; ?></b>
		em <b><?php echo date('d/m/Y', strtotime($post['data'])) ?></b> |
		Visitas <b><?php echo $post['visitas']; ?></b>
	</p>
		<?php
			$str = strip_tags($post['conteudo']);
			$len = strlen($str);
			$max = 500;

			if ($len <= $max)
				echo $str;
			else
				echo substr($str, 0, $max) . '...';
		?>
	<p>
				
	</p>
	<hr>
<?php endforeach; ?>


</body>
</html>