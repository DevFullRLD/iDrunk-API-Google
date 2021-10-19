<?php
	ob_start();
	session_start();
	include ("DAO/conexao.php");
	include ("DAO/dbFunctions.php");
	
	if(checarLogin($conn)){
		$sql = "SELECT * from Pessoas where cod_pessoa = {$_SESSION['user_id']};";
		$result = mysqli_query($conn,$sql);
		$dados = mysqli_fetch_array($result);
	}
	?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Contato</title>
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
				<h3>Contato</h3>
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
			if(isset($_GET['mensagemStatus'])){
			?>
		<div class="container text-center" style="margin-top: 50px" id="infoMsg" onClick="$('#infoMsg').hide(250);">
			<?php
				if($_GET['mensagemStatus'] == 1){
				?>
			<div class="alert success">
				<span class="closebtn">&times;</span> 
				Mensagem enviada com sucesso!
			</div>
			<?php
				}
				if($_GET['mensagemStatus'] == 2){
				?>
			<div class="alert">
				<span class="closebtn">&times;</span> 
				Falha ao enviar mensagem!
			</div>
			<?php
				}
				?>
		</div>
		<?php
			}
			?>
		<!-- Conteúdo -->
		<br><br>
		<div class="container" style="margin-top: 40px;">
			<form method="post" action="DAO/enviarEmail.php">
				<h2 align="center">Contato</h2>
				<div class="form-group">
					<hr>
					<small id="emailHelp" class="form-text text-muted" style="font-size: 10.5px">* Faça seu contato iremos responder o mais breve possível.</small>
					<label for="txtNome" class="sr-only">Nome</label>
					<input type="text" name="txtNome" id="txtNome" class="form-control" placeholder="Nome completo" value="<?php if(checarLogin($conn)){ echo $dados['nome_pessoa'];} ?>" required autofocus>
					<label for="txtEmail" class="sr-only">E-mail</label>
					<input type="email" name="txtEmail" id="txtEmail" class="form-control" placeholder="Endereço de e-mail" value="<?php if(checarLogin($conn)){echo $_SESSION['user_email'];} ?>" required autofocus>
					<label for="txtAssunto" class="sr-only">Assunto</label>
					<input type="text" name="txtAssunto" id="txtAssunto" class="form-control" placeholder="Assunto" required autofocus><br>
					<label for="txtMsg">Mensagem:</label>
					<textarea class="form-control" name="txtMsg" rows="5" id="txtMsg" required></textarea>
					<hr>
					<button class="btn btn-lg btn-warning btn-block" type="submit" style="color:white;">Enviar</button>
				</div>
			</form>
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
		</script>
	</body>
	<?php
		ob_end_flush();
		?>
</html>