<?php
	//settings
	$cache_ext  = '.html'; //file extension
	$cache_time     = 604800;  //Cache file expires afere these seconds (1 hour = 3600 sec)
	$cache_folder   = 'cache/'; //folder to store Cache files
	$ignore_pages   = array('', '');
	
	$dynamic_url    = 'http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . $_SERVER['QUERY_STRING']; // requested dynamic page (full url)
	$cache_file     = $cache_folder.md5($dynamic_url).$cache_ext; // construct a cache file
	$ignore = (in_array($dynamic_url,$ignore_pages))?true:false; //check if url is in ignore list
	
	if (!$ignore && file_exists($cache_file) && time() - $cache_time < filemtime($cache_file)) { //check Cache exist and it's not expired.
	   ob_start('ob_gzhandler'); //Turn on output buffering, "ob_gzhandler" for the compressed page with gzip.
	   readfile($cache_file); //read Cache file
	   echo '<!-- cached page - '.date('l jS \of F Y h:i:s A', filemtime($cache_file)).', Page : '.$dynamic_url.' -->';
	   ob_end_flush(); //Flush and turn off output buffering
	   exit(); //no need to proceed further, exit the flow.
	}
	//Turn on output buffering with gzip compression.
	ob_start('ob_gzhandler'); 
	######## Your Website Content Starts Below #########
	
	session_start();
	include ("DAO/conexao.php");
	include ("DAO/dbFunctions.php");
	
	if(checarLogin($conn)){
		$dados = getDados($conn, $_SESSION['user_id']);
	}
	?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Home</title>
		<!-- CSS & Javascript -->
		<link type="text/css" rel="stylesheet" charset="utf-8" href="css/custom.css"/>
		<link rel="stylesheet" type="text/css" href="slick/slick.css"/>
		<link rel="stylesheet" type="text/css" href="slick/slick-theme.css"/>
		<script type='text/javascript' src="js/jquery-3.2.1.min.js"></script>
		<script type='text/javascript' src="js/modernizr.js"></script>
		<script type='text/javascript' src='js/loadPage.js'></script>
	</head>
	<body>
		<div class="pre-load"></div>
		<!-- Sidebar, topbar & botão para abrí-la -->
		<nav class="navbar fixed-top navbar-light bg-faded">
			<button class="navbar-toggler" type="button" id="btnSidebar" style="float:left; z-index:1" aria-expanded="false" aria-label="Toggle navigation">
			<span class="fa fa-bars"></span>
			</button>
			<a class="brand-text" style="color: white">iDrunk</a>
			<button class="navbar-toggler" type="button" style="float:right; z-index:2" aria-expanded="false" aria-label="Toggle navigation" onClick="window.location.href='carrinho.php'">
			<span class="fa fa-shopping-cart"></span>
			</button>
		</nav>
		<div id="sidebar" class="sidenav" style="white-space: nowrap;">
			<a href="javascript:void(0)" class="closebtn" onClick="closeNav();">&times;</a>
			<a class="text-white">
				<br>
				<h4>Bem vindo,</h4>
				<h3><?php if(isset($dados['cod_pessoa'])){ echo primeiroNome(" ", $dados['nome_pessoa']); } else{echo "caro usuário";} ?></h3>
			</a>
			<hr>
			<a href="index.php"><i class="fa fa-home"></i> Home <span class="sr-only">(current)</span></a>
			<a href="catalogo.php?categ=0&tipo=0&filtro=0"><i class="fa fa-beer"></i> Catálogo</a>
			<a href="contato.php"><i class="fa fa-phone"></i> Contato</a>
			<a href="sobre.php"><i class="fa fa-info-circle"></i> Sobre o app</a>
			<?php
				if(checarLogin($conn)){
				?>
			<a href="appConfig.php"><i class="fa fa-cogs"></i> Configura&ccedil;&otilde;es</a>
			<a onClick="sair()" style="color: white;"><i class="fa fa-sign-out"></i> Sair</a>
			<?php
				}else{
				?>
			<a href="logar.php"><i class="fa fa-sign-in"></i> Logar</a>
			<a href="cadastro.php"><i class="fa fa-user-plus"></i> Cadastrar</a>
			<a href="appConfig.php"><i class="fa fa-cogs"></i> Configura&ccedil;&otilde;es</a>
			<?php
				}
				?>
		</div>
		<!-- Fim -->
		<?php
			if(isset($_REQUEST['loginStatus'])){
			?>
		<div class="container text-center" style="margin-top: 50px" id="infoMsg" onClick="$('#infoMsg').hide(250);">
			<?php
				if($_REQUEST['loginStatus'] == 1){
				?>
			<div class="alert success">
				<span class="closebtn">&times;</span> 
				Login efetuado com sucesso!
			</div>
			<?php
				}
				if($_REQUEST['loginStatus'] == 3){
				?>
			<div class="alert info">
				<span class="closebtn">&times;</span> 
				Você deslogou!
			</div>
			<?php
				}
				?>
		</div>
		<?php
			}
			?>
		<?php
			if(isset($_REQUEST['cadastroStatus'])){
			?>
		<div class="container text-center" style="margin-top: 50px" id="infoMsg2" onClick="$('#infoMsg2').hide(250);">
			<?php
				if($_REQUEST['cadastroStatus'] == 1){
				?>
			<div class="alert success">
				<span class="closebtn">&times;</span> 
				Cadastro efetuado com sucesso!
			</div>
			<?php
				}
				?>
		</div>
		<?php
			}
			?>
		<!-- Conteúdo -->
		<div class="container" style="padding-top: 60px;">
			<div class="container" style="text-align: center;">
				<img src="img/logo.png" width="35%" style="padding-bottom: 30px;">
				<h6>Aqui a bebida te acompanha!</h6>
				<hr>
			</div>
		</div>
		<div class="carousel">
			<div class="text-center">
				<img src="img/Carrosel/bg3.png" width="100%"/>
				Direto do depósito
			</div>
			<div class="text-center">
				<img src="img/Carrosel/bg4.png" width="100%"/>
				Produtos com qualidade
			</div>
			<div class="text-center">
				<img src="img/Carrosel/bg5.png" width="100%"/>
				Bebida entregue gelada
			</div>
		</div>
		<!-- Fim -->
		<!-- CSS e Javascript -->
		<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>
		<link type="text/css" rel="stylesheet" href="css/normalize.css"/>
		<link type="text/css" rel="stylesheet" href="css/sidebar.css"/>
		<link type="text/css" rel="stylesheet" href="css/navbar.css"/>
		<link type="text/css" rel="stylesheet" href="css/font-awesome.min.css">
		<link type='text/css' rel='stylesheet' href='css/themes/dark.css'>
		<script type="text/javascript" src="js/popper.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/jquery-migrate-1.2.1.min.js"></script>
		<script type="text/javascript" src="slick/slick.min.js"></script>
		<script type="text/javascript" src="js/gerirCookies.js"></script>
		<script>
			/* Configurações da sidebar */
			function closeNav() {
				document.getElementById("sidebar").style.width = "0";
			}
			
			window.addEventListener('click', function(e){   
			if (document.getElementById('sidebar').contains(e.target)){
			 // Clicked in box
			} else{
			 document.getElementById("sidebar").style.width = "0";
			}
			});
			
			window.addEventListener('click', function(e){   
			if (document.getElementById('btnSidebar').contains(e.target)){
			 document.getElementById("sidebar").style.width = "250px";
			} else{
			 
			}
			});
			
			/* Deslogar */
			function sair(){
			 	var r = confirm("Deseja mesmo sair?");
			 	if (r == true) {
					location.href='DAO/exit.php';
			 	} else {
					location.href='#';
			 	}
			}
			
			/* Tema */
			$(document).ready(function() {
				if(getCookie("setTheme") == 1){
					$('body').toggleClass('night');
				} else {
				}
			});
			
			/* Carousel */
			$(document).ready(function(){
			$('.carousel').slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			autoplay: true,
			autoplaySpeed: 2000,
				dots: true,
			 prevArrow: false,
			 nextArrow: false,
				fade: true,
				useCSS: true,
			});
				});
		</script>
	</body>
	<?php
		ob_end_flush();
		?>
</html>