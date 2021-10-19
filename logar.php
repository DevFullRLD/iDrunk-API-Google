<?php
	ob_start();
	session_start();
	include ("DAO/conexao.php");
	include ("DAO/dbFunctions.php");
	
	$carrinho = 0;
	?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Login</title>
		<!-- CSS & Javascript -->
		<link type="text/css" rel="stylesheet" href="css/custom.css"/>
		<script type='text/javascript' src="js/jquery-3.2.1.min.js"></script>
		<script type='text/javascript' src="js/modernizr.js"></script>
		<script type='text/javascript' src='js/loadPage.js'></script>
		<style>
			.form-control{
			margin-bottom: 3px;
			}
		</style>
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
			<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
			<br>
			<a class="text-white">
				<h3>Por favor,</h3>
				<h6>efetue login a seguir</h6>
			</a>
			<hr>
			<a href="index.php"><i class="fa fa-home"></i> Home <span class="sr-only">(current)</span></a>
			<a href="catalogo.php?categ=0&tipo=0&filtro=0"><i class="fa fa-beer"></i> Catálogo</a>
			<a href="contato.php"><i class="fa fa-phone"></i> Contato</a>
			<a href="sobre.php"><i class="fa fa-info-circle"></i> Sobre o app</a>
			<a href="cadastro.php"><i class="fa fa-user-plus"></i> Cadastrar</a>
			<a href="appConfig.php"><i class="fa fa-cogs"></i> Configura&ccedil;&otilde;es</a>
		</div>
		<!-- Fim -->
		<?php
			if(isset($_REQUEST['loginStatus'])){
				if($_REQUEST['loginStatus'] == 2){
			?>
		<div class="container text-center" style="margin-top: 50px" id="infoMsg" onClick="$('#infoMsg').hide(250);">
			<div class="alert">
				<span class="closebtn">&times;</span> 
				Usuário ou senha incorretos!
			</div>
		</div>
		<?php
			}
			if($_REQUEST['loginStatus'] == 4){
			?>
		<div class="container text-center" style="margin-top: 50px" id="infoMsg" onClick="$('#infoMsg').hide(250);">
			<div class="alert warning">
				<span class="closebtn">&times;</span> 
				Para usar o carrinho, favor efetuar login!
			</div>
		</div>
		<?php
			$carrinho = 1;
			}
			if($_REQUEST['loginStatus'] == 5){
			?>
		<div class="container text-center" style="margin-top: 50px" id="infoMsg" onClick="$('#infoMsg').hide(250);">
			<div class="alert warning">
				<span class="closebtn">&times;</span> 
				Para adicionar itens ao carrinho, efetuar login!
			</div>
		</div>
		<?php
			$carrinho = 2;
			$cod_produto = $_REQUEST['codProduto'];
			}
			}
			?>
		<!-- Conteúdo -->
		<div class="container" style="margin-top: 60px;">
			<br>
			<div class="row">
				<div class="col-12">
					<form action="DAO/insertLogin.php" method="post">
						<h2 align="center">Login</h2>
						<br>
						<div class="form-group">
							<h5>Insira seus dados</h5>
							<hr>
							<label for="txtUser" class="sr-only">E-mail</label>
							<input type="email" name="txtEmail" id="txtEmail" class="form-control" placeholder="E-mail" value="<?php if(isset($_COOKIE["user_email"])) { echo $_COOKIE["user_email"]; } ?>" required autofocus>
							<label for="txtPass" class="sr-only">Senha</label>
							<input type="password" name="txtPass" id="txtPass" class="form-control" placeholder="Senha" value="<?php if(isset($_COOKIE["user_pass"])) { echo base64_decode($_COOKIE["user_pass"]); } ?>" required>
							<div class="checkbox">
								<label>
								<input type="checkbox" name="remember" id="remember" <?php if(isset($_COOKIE["user_email"])) { ?> checked <?php } ?> /> Lembrar-me
								</label>
							</div>
							<br>
							<button class="btn btn-lg btn-warning btn-block" type="submit" style="color:white;">Logar</button>
							<input type="hidden" name="logar" value="login">
							<input type="hidden" name="statusCarrinho" id="statusCarrinho" value="<?php echo $carrinho; ?>">
							<input type="hidden" name="codProduto" id="codProduto" value="<?php echo $cod_produto; ?>">
						</div>
					</form>
					<p class="text-center"><a href="cadastro.php" style="color: #ffad5b;">Não possui conta? Registre-se!</a><br>
						<a href="resetsenha.php" style="color: #ffad5b;">Esqueci minha senha</a>
					</p>
				</div>
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
			
			/* Tema */
			$(document).ready(function() {
				if(getCookie("setTheme") == 1){
					$('body').toggleClass('night');
				} else {
				}
			});
		</script>
	</body>
</html>
<?php
	ob_end_flush();
	?>