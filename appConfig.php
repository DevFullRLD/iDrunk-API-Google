<?php
	ob_start();
	session_start();
	include("DAO/conexao.php");
	include("DAO/dbFunctions.php");
	
	if (checarLogin($conn)) {
	    $sql    = "SELECT * from Pessoas where cod_pessoa = {$_SESSION['user_id']};";
	    $result = mysqli_query($conn, $sql);
	    $dados  = mysqli_fetch_array($result);
	}
	?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Configurações</title>
		<!-- CSS & Javascript -->
		<link type="text/css" rel="stylesheet" href="css/custom.css"/>
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
			<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
			<br>
			<a class="text-white">
				<h4>Configurações</h4>
				<h3><?php if(isset($dados['cod_pessoa'])){ echo "Olá, ".primeiroNome(" ", $dados['nome_pessoa']); }?></h3>
			</a>
			<hr>
			<a href="index.php"><i class="fa fa-home"></i> Home <span class="sr-only">(current)</span></a>
			<a href="catalogo.php?categ=0&tipo=0&filtro=0"><i class="fa fa-beer"></i> Catálogo</a>
			<a href="contato.php"><i class="fa fa-phone"></i> Contato</a>
			<a href="sobre.php"><i class="fa fa-info-circle"></i> Sobre o app</a>
			<?php
				if (checarLogin($conn)) {
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
		<!-- Conteúdo -->
		<div class="wrapper text-center">
			<div class="div-style" style="margin-top: 50px;">
			<h5 align="justify">Conta</h5>
					<hr>
					<?php
					if(!checarLogin($conn)){
						?>
						<div class="alert info" id="infoMsg" onClick="$('#infoMsg').hide(250);">
				<span class="closebtn">&times;</span>
				Efetue login para essas opções.
			</div>
					<?php
					}
				?>
				<a href="pedidos.php"><button class="btn btn-warning btn-lg btn-block" type="submit" style="color: white;" <?php if(!checarLogin($conn)) echo "disabled"; ?>>Meus pedidos</button></a>
					<br>
					<a href="alterarDados.php"><button class="btn btn-warning btn-lg btn-block" type="submit" style="color: white;" <?php if(!checarLogin($conn)) echo "disabled"; ?>>Alterar dados cadastrais</button></a>
					<br>
					<a href="#"><button class="btn btn-danger btn-lg btn-block" type="submit" style="color: white;" <?php if(!checarLogin($conn)) echo "disabled"; ?>>Excluir conta</button></a>
					<br>
					<h5 align="justify">Tema</h5>
					<hr>
					<div class="dntoggle fa "><a id="theme" style="font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif"></a></div>
			</div>
		</div>
		<p style="bottom: 10px; font-size: 11px; text-align: center">iDrunk v1.0 Beta<br>&copy; 2017 iDrunk ltda., todos os direitos reservados.</p>
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
			  $('.dntoggle').click(function(){
				  if($('body').hasClass('night')){
					  eraseCookie("setTheme");
					  	$('body').toggleClass('night'),
					  	$('.dntoggle').toggleClass('fa-sun-o');
					  	$('.dntoggle').toggleClass('fa-moon-o');
					  	document.getElementById("theme").innerHTML = " Noturno";
					  
				  } else {
					  createCookie("setTheme", 1, 365);
						$('body').toggleClass('night'),
						$('.dntoggle').toggleClass('fa-moon-o');
						$('.dntoggle').toggleClass('fa-sun-o');
					  document.getElementById("theme").innerHTML = " Padrão";
				  }
				 /* $('body').toggleClass('night'),
      			$('.dntoggle').toggleClass('fa-sun-o');
               $('.dntoggle').toggleClass('fa-moon-o');*/
              }
			);

			$(document).ready(function() {
				if(getCookie("setTheme") == 1){
					$('body').toggleClass('night'),
					$('.dntoggle').toggleClass('fa-sun-o');
					document.getElementById("theme").innerHTML = " Padrão";
				} else {
					$('.dntoggle').toggleClass('fa-moon-o');
					
					document.getElementById("theme").innerHTML = " Noturno";
				}
			});
		</script>
	</body>
</html>
<?php
	ob_end_flush();
	?>