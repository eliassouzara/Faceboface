<?php
	require_once './system/config.php';
	require_once './system/database.php';

	if (!isset($_GET['id']) || empty($_GET['id']))
		header('Location: index.php');
	else {

		$id 	= DBEscape (strip_tags(trim($_GET['id'])));
		$post 	= DBRead('posts', "WHERE id = '{$id}' LIMIT 2");

		if ($post) {
			$post = $post[0];
			$upVisitas = array(
				'visitas' => $post['visitas'] + 1
			);
			DBUpdate('posts', $upVisitas, "id = '{$id}'");

            $_SESSION['visited_post_' . $id] = true;
         
		}

	}

?>
<script>
    // Verifica se a página foi carregada pela primeira vez usando sessionStorage
    if (!sessionStorage.getItem('pageLoaded')) {
        // Incrementa o número de visitas no lado do cliente
        // Envie uma requisição para atualizar o contador no lado do servidor, se necessário
        console.log('Atualizar contador de visitas no lado do cliente');

        // Define sessionStorage para indicar que a página foi carregada
        sessionStorage.setItem('pageLoaded', 'true');
    }
</script>

	

</body>
</html>

<?php
include('../conexao.php');
?>

<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>sua vaga</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- <link rel="manifest" href="site.webmanifest"> -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.png">
    <!-- Place favicon.ico in the root directory -->

    <!-- CSS here -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/magnific-popup.css">
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/themify-icons.css">
    <link rel="stylesheet" href="../css/nice-select.css">
    <link rel="stylesheet" href="../css/flaticon.css">
    <link rel="stylesheet" href="../css/gijgo.css">
    <link rel="stylesheet" href="../css/animate.min.css">
    <link rel="stylesheet" href="../css/slicknav.css">
    <link rel="shortcut icon" type="image/x-icon" href="img/favicoin.png">
    <link rel="stylesheet" href="../css/style.css">
    <!-- <link rel="stylesheet" href="../css/responsive.css"> -->
</head>

<body>
    <!--[if lte IE 9]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
        <![endif]-->

    <!-- header-start -->
    <header>
        <div class="header-area ">
            <div id="sticky-header" class="main-header-area">
                <div class="container-fluid ">
                    <div class="header_bottom_border">
                        <div class="row align-items-center">
                            <div class="col-xl-3 col-lg-2">
                                <div class="logo">
                                    <a href="index.html">
                                        <img src="img/logo.png" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-7">
                                <div class="main-menu  d-none d-lg-block">
                                   
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 d-none d-lg-block">
                                <div class="Appointment">
                                    <div class="phone_num d-none d-xl-block">
                                      
                                    </div>
                                  
                                       
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mobile_menu d-block d-lg-none"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </header>




<?php
		require_once './system/config.php';
		require_once './system/database.php';

	if (!isset($_GET['id']) || empty($_GET['id']))
		header('Location: list.php');
	else {

		$id 	= DBEscape (strip_tags(trim($_GET['id'])));
		$post 	= DBRead('posts', "WHERE id = '{$id}' LIMIT 1");

		if ($post) {
			$post = $post[0];
			$upVisitas = array(
				'visitas' => $post['visitas'] + 1
			);
			DBUpdate('posts', $upVisitas, "id = '{$id}'");
		}

	}
?>



	<?php if ($post): ?>

	

	<?php endif; ?>


 <!-- bradcam_area  -->
 
    <!--/ bradcam_area  -->
    <?php
include_once('../config.php');




if(!empty($_GET['id']))

{
  

  $id = $_GET['id'];

  $sqlSelect = "SELECT * FROM usuariosy WHERE id=$id";

  $result = $conn ->query($sqlSelect);

  if($result->num_rows > 0)
  {

  }




while($user_data = mysqli_fetch_assoc($result))
{
  $cidade = $user_data['cidade'];
  
}
}




?>
	

    <div class="job_details_area">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="job_details_header">
                        <div class="single_jobs white-bg d-flex justify-content-between">
                            <div class="jobs_left d-flex align-items-center">
                                <div class="thumb">
                                    <img src="../img/svg_icon/5.svg" alt="">
                                </div>
                                <div class="jobs_conetent">
                                    <a href="#"><h4><?php echo $post['titulo']; ?></h4></a>
                                    <div class="links_locat d-flex align-items-center">
                                        <div class="location">
                                            <p> <i class="fa fa-map-marker"></i><?php echo $cidade; ?></p>
                                        </div>
                                        <div class="location">
                                            <p> <i class="fa fa-clock-o"></i> Tempo parcial</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          
                        </div>
                    </div>
                    <div class="descript_wrap white-bg">
                        <div class="single_wrap">
                        <p>já foram vistos: <?php echo $post['visitas']; ?> pessoas</p>
                            <h4>Descrição do trabalho</h4>
                            <p><?php echo $post['conteudo']; ?></p>
                            <p></p>
                        </div>
                        <div class="single_">
                            <h4></h4>
                            <ul>
                                <li> 
                                </li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                        </div>
                        <div class="single_">
                            <h4></h4>
                            <ul>
                                <li>
                                </li>
                                <li></li>
                                <li></li>
                                <li></li>
                            </ul>
                        </div>
                        <div class="single_wrap">
                            <h4></h4>
                            <p></p>
                        </div>
                    </div>
                    <div class="apply_job_form white-bg">
                        <h4>Candidate-se
                        </h4>
                        <form action="">
                            <div class="row">
                                <div class="col-md-6">
                                  
                                </div>
                             
                                <div class="col-md-12">
                                    <div class="submit_btn">
									<a  style="float: right;" href="<?php echo $post['autor']; ?>" class="boxed-btn3 w-100"> Fale comigo Agora </a>
										 
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="job_sumary">
                        <div class="summery_header">
                            <h3>Trabalho de verão</h3>
                        </div>
                        <div class="job_content">
                            <ul>
                                <li>Publicado em:
                                    <span><?php echo date('d/m/Y', strtotime($post['data'])) ?></span></li>
                                <li>Quando precisa?: <span>Hoje</span></li>
                          
                                <li>Localização: <span><?php echo $cidade; ?></span></li>
                                <li>Natureza do trabalho: <span> parcial</span></li>
                            </ul>
                        </div>
                    </div>
					<?php
	require_once './system/config.php';
	require_once './system/database.php';

	if (isset($_GET['action']) && isset($_GET['id']) && !empty($_GET['action']) && !empty($_GET['id'])) {
		$id = DBEscape(strip_tags(trim($_GET['id'])));
		switch ($_GET['action']) {
			case 1:
				DBUpDate('posts', array('status' => 1), "id = '{$id}'");
				break;
			case 2:
				DBUpDate('posts', array('status' => 0), "id = '{$id}'");
				break;
			case 3:
				DBDelete('posts', "id = '{$id}'");
				break;
		}
		header('Location: index.php');
	}
?>
				
	
		<a style="padding: 2.375rem 6.75rem; "class="btn btn-danger" href="?action=3&&id=<?php echo $post['id'];?>" title="Deletar">Deletar</a>
        <a style="padding: 2.375rem 6.75rem; "class="btn btn-warning" href="../dashboard/dashboard.php">voltar</a>
	</p>
	
                    <div class="share_wrap d-flex">
                        <span></span>
                       
                    </div>
                    <div class="job_location_wrap">
                        <div class="job_lok_inner">
                            
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- footer start -->
  
<!--/ footer end  -->

    <!-- link that opens popup -->
    <!-- JS here -->
    <script src="./js/vendor/modernizr-3.5.0.min.js"></script>
    <script src="./js/vendor/jquery-1.12.4.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/owl.carousel.min.js"></script>
    <script src="./js/isotope.pkgd.min.js"></script>
    <script src="./js/ajax-form.js"></script>
    <script src="./js/waypoints.min.js"></script>
    <script src="./js/jquery.counterup.min.js"></script>
    <script src="./js/imagesloaded.pkgd.min.js"></script>
    <script src="./js/scrollIt.js"></script>
    <script src="./js/jquery.scrollUp.min.js"></script>
    <script src="./js/wow.min.js"></script>
    <script src="./js/nice-select.min.js"></script>
    <script src="./js/jquery.slicknav.min.js"></script>
    <script src="./js/jquery.magnific-popup.min.js"></script>
    <script src="./js/plugins.js"></script>
    <script src="./js/gijgo.min.js"></script>



    <!--contact js-->
    <script src="./js/contact.js"></script>
    <script src="./js/jquery.ajaxchimp.min.js"></script>
    <script src="./js/jquery.form.js"></script>
    <script src="./js/jquery.validate.min.js"></script>
    <script src="./js/mail-script.js"></script>


    <script src="./js/main.js"></script>
</body>

</html>