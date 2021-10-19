<?php
	session_start();
	ob_start();
	include ("DAO/conexao.php");
	include ("DAO/dbFunctions.php");
	?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Cadastro</title>
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
				<h6>cadastre-se a seguir</h6>
			</a>
			<hr>
			<a href="index.php"><i class="fa fa-home"></i> Home <span class="sr-only">(current)</span></a>
			<a href="catalogo.php?categ=0&tipo=0&filtro=0"><i class="fa fa-beer"></i> Catálogo</a>
			<a href="contato.php"><i class="fa fa-phone"></i> Contato</a>
			<a href="sobre.php"><i class="fa fa-info-circle"></i> Sobre o app</a>
			<a href="logar.php"><i class="fa fa-sign-in"></i> Logar</a>
			<a href="appConfig.php"><i class="fa fa-cogs"></i> Configura&ccedil;&otilde;es</a>
		</div>
		<!-- Fim -->
		<?php
			if(isset($_REQUEST['cadastroStatus'])){
			?>
		<div class="container text-center" style="margin-top: 50px" id="infoMsg" onClick="$('#infoMsg').hide(250);">
			<?php
				if($_REQUEST['cadastroStatus'] == 1){
				?>
			<div class="alert success">
				<span class="closebtn">&times;</span> 
				Cadastro realizado com sucesso!
			</div>
			<?php
				}
				if($_REQUEST['cadastroStatus'] == 2){
				?>
			<div class="alert">
				<span class="closebtn">&times;</span> 
				Erro desconhecido ao efetuar cadastro!
			</div>
			<?php
				}
				if($_REQUEST['cadastroStatus'] == 3){
				?>
			<div class="alert">
				<span class="closebtn">&times;</span> 
				E-mail ou CPF informado já cadastrado!
			</div>
			<?php
				}
				?>
		</div>
		<?php
			}
			?>
		<!-- Conteúdo -->
		<div class="container" style="margin-top: 50px;">
			<br>
			<div class="container">
				<form method="post" action="DAO/insertCadastro.php">
					<h2 align="center">Cadastro</h2>
					<br>
					<div class="form-group">
						<h5>Dados pessoais</h5>
						<hr>
						<small id="emailHelp" class="form-text text-muted" style="font-size: 10.5px">* Nunca compartilharemos seus dados pessoais com ninguém.</small>
						<label for="txtName" class="sr-only">Nome</label>
						<input type="text" name="txtName" id="txtName" class="form-control" placeholder="Nome completo" oninput="checarNome(this)" required autofocus>
						<label for="txtEmail" class="sr-only">E-mail</label>
						<input type="email" name="txtEmail" id="txtEmail" class="form-control" placeholder="Endereço de e-mail" required autofocus>
						<label for="txtDtNasc" class="sr-only">Data de Nascimento</label>
						<input type="text" name="txtDtNasc" id="txtDtNasc" class="form-control" placeholder="Data de nascimento" maxlength="10" required autofocus>
						<label for="txtCPF" class="sr-only">CPF</label>
						<input type="text" name="txtCPF" id="txtCPF" class="form-control" placeholder="CPF" maxlength="14" oninput="validarCPF(this)" required autofocus>
						<label for="txtPass" class="sr-only">Senha</label>
						<input type="password" name="txtPass" id="txtPass" class="form-control" placeholder="Senha" minlength="8" required>
						<label for="txtConfirmPass" class="sr-only">Confirmar senha</label>
						<input type="password" name="txtConfirmPass" id="txtConfirmPass" class="form-control" placeholder="Confirmação de senha" oninput="checarSenha(this)" required>
					</div>
					<br>
					<div class="form-group">
						<h5>Contato</h5>
						<hr>
						<label for="txtDDD" class="sr-only">DDD</label>
						<input type="number" name="txtDDD" id="txtDDD" class="form-control" placeholder="DDD" maxlength="2" required autofocus>
						<label for="txtTel" class="sr-only">Telefone</label>
						<input type="text" name="txtTel" id="txtTel" class="form-control" placeholder="Número de telefone" maxlength="10" required autofocus>
						<select class="form-control" name="opcTipoTel" id="opcTipoTel">
							<option value="Residencial">Residencial</option>
							<option value="Celular">Celular</option>
							<option value="Comercial">Comercial</option>
						</select>
					</div>
					<br>
					<div class="form-group">
						<h5>Endereço</h5>
						<hr>
						<label for="txtCEP" class="sr-only">CEP</label>
						<input type="text" name="txtCEP" id="txtCEP" class="form-control" placeholder="CEP" maxlength="9" required autofocus>
						<label for="txtNumCasa" class="sr-only">Número</label>
						<input type="number" name="txtNumCasa" id="txtNumCasa" class="form-control" placeholder="Número da residência" required autofocus>
						<label for="txtComplm" class="sr-only">Complemento</label>
						<input type="text" name="txtComplm" id="txtComplm" class="form-control" placeholder="Complemento" required autofocus>
						<label for="txtRef" class="sr-only">Referência</label>
						<input type="text" name="txtRef" id="txtRef" class="form-control" placeholder="Referência (ex: próx. a Mercadinho Big Bom)" required autofocus>
						<hr>
						<button class="btn btn-lg btn-warning btn-block" type="submit" style="color: white;">Cadastrar</button>
						<input type="hidden" name="Cadastrar" value="cadastrar">
					</div>
				</form>
				<p class="text-center"><a href="logar.php" style="color: #ffad5b;">Ir para tela de login <i class="fa fa-sign-in"></i></a></p>
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
		<script type="text/javascript" src="js/jquery.mask.js"></script>
		<script type="text/javascript" src="js/CPF.min.js"></script>
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
			
			/* Máscara dos forms */
			$(document).ready(function () { 
				var $maskCPF = $("#txtCPF");
				$maskCPF.mask('000.000.000-00', {reverse: true});
				
				var $maskTel = $("#txtTel");
				$maskTel.mask('00000-0000', {reverse: true});
				
				var $maskCEP = $("#txtCEP");
				$maskCEP.mask('00000-000', {reverse: true});
				
				var $maskDtNasc = $("#txtDtNasc");
				$maskDtNasc.mask('00/00/0000', {reverse: true});
			});
			
			/* Verificar se as senhas coincidem */
			function checarSenha (input){ 
			 	if (input.value != document.getElementById('txtPass').value) {
			 		input.setCustomValidity('As senhas não coincidem!');
				} else {
			 		input.setCustomValidity('');
				}
			}
			
			/* Verificar se as senhas coincidem */
			function checarNome (input){
				if(input.value.indexOf(" ") != -1){
					input.setCustomValidity('');
				} else {
					input.setCustomValidity('Digite seu nome completo!');
				}
			}
			/* Validar CPF */
			function validarCPF (input){
						if (CPF.validate(document.getElementById('txtCPF').value) === true ) {
			 				input.setCustomValidity('');
						}else{
					input.setCustomValidity('CPF inválido!');
						}
			};
			
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