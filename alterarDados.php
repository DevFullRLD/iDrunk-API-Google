<?php
	ob_start();
	session_start();
	header('Content-Type: text/html; charset=utf-8');
	include ("DAO/conexao.php");
	include ("DAO/dbFunctions.php");
	
	if(checarLogin($conn)){
		$dados = getDados($conn, $_SESSION['user_id']);
	}else{
		echo "<script>window.location.href='index.php'</script>";
	}
	?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Alterar dados da conta</title>
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
	<body onLoad="formatarCPF();">
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
			<a class="text-white">
				<h4>Alterar Dados</h4>
				<h3><?php if(isset($dados['cod_pessoa'])){ echo "Olá, ".primeiroNome(" ", $dados['nome_pessoa']); } else{echo "ERRO! Não logado.";} ?></h3>
			</a>
			<hr>
			<a href="index.php"><i class="fa fa-home"></i> Home <span class="sr-only">(current)</span></a>
			<a href="catalogo.php?categ=0&tipo=0&filtro=0"><i class="fa fa-beer"></i> Catálogo</a>
			<a href="contato.php"><i class="fa fa-phone"></i> Contato</a>
			<a href="sobre.php"><i class="fa fa-info-circle"></i> Sobre o app</a>
			<a href="appConfig.php"><i class="fa fa-cogs"></i> Configura&ccedil;&otilde;es</a>
			<?php
				if(checarLogin($conn)){
				?>
			<a onClick="sair()" style="color: white;"><i class="fa fa-sign-out"></i> Sair</a>
			<?php
				}
				?>
		</div>
		<!-- Fim -->
		<?php
			if(isset($_REQUEST['msg'])){
			?>
		<div class="container text-center" style="margin-top: 50px" id="infoMsg" onClick="$('#infoMsg').hide(250);">
			<?php
				if($_REQUEST['msg'] == 1){
				?>
			<div class="alert">
				<span class="closebtn">&times;</span> 
				Erro ao alterar dados!
			</div>
			<?php
				}
				if($_REQUEST['msg'] == 2){
				?>
			<div class="alert success">
				<span class="closebtn">&times;</span> 
				Dados alterados com sucesso!
			</div>
			<?php
				}
				if($_REQUEST['msg'] == 3){
				?>
			<div class="alert">
				<span class="closebtn">&times;</span> 
				Senha atual incorreta!
			</div>
			<?php
				}
				if($_REQUEST['msg'] == 4){
				?>
			<div class="alert warning">
				<span class="closebtn">&times;</span>
				Preencha sua senha atual para trocar!
			</div>
			<?php
				}
				if($_REQUEST['msg'] == 5){
				?>
			<div class="alert warning">
				<span class="closebtn">&times;</span> 
				A nova senha não pode ser igual a anterior!
			</div>
			<?php
				}
				?>
		</div>
		<?php
			}
			?>
		<!-- Conteúdo -->
		<div class="container" style="margin-top: 70px">
		<button class="btn btn-sm btn-warning text-white" onClick="window.location='appConfig.php'"><i class="fa fa-arrow-left"></i> Voltar</button>
		<br><br>
			<div class="row">
				<div class="col-sm-12">
					<form method="post" action="DAO/insertAttDados.php">
						<div class="form-group">
							<small id="emailHelp" class="form-text text-muted float-left" style="font-size: 11px;">Habilitar alteração de dados    </small>
							<label class="switch" id='switch'>
							<input type="checkbox" value="Off" onClick="habilitarForms();">
							<span class="slider round"></span>
							</label>
							<hr>
							<h5>Dados pessoais</h5>
							<hr>
							<label for="txtName" class="sr-only">Nome</label>
							<input type="text" name="txtName" id="txtName" class="form-control" value="<?php if(isset($dados['cod_pessoa'])){ echo $dados['nome_pessoa']; }?>" disabled>
							<label for="txtDtNasc" class="sr-only">Data de Nascimento</label>
							<input type="text" name="txtDtNasc" id="txtDtNasc" class="form-control" value="<?php if(isset($dados['cod_pessoa'])){ echo implode("/",array_reverse(explode("-",$dados['dtNasc_pessoa']))); } ?>" maxlength="10" disabled>
							<label for="txtCPF" class="sr-only">CPF</label>
							<input type="text" name="txtCPF" id="txtCPF" class="form-control" value="<?php if(isset($dados['cod_pessoa'])){ echo $dados['cpf_pessoa']; } ?>" maxlength="14" oninput="formatarCPF(this)" disabled>
						</div>
						<div class="form-group">
							<hr>
							<h5>Login</h5>
							<hr>
							<label for="txtEmail" class="sr-only">E-mail</label>
							<input type="email" name="txtEmail" id="txtEmail" class="form-control" value="<?php if(isset($dados['cod_pessoa'])){ echo $dados['login_usuario']; } ?>" placeholder="Novo e-mail" disabled>
							<label for="txtPass" class="sr-only">Senha</label>
							<input type="password" name="txtPass" id="txtPass" class="form-control" value="userpass" placeholder="Digite sua senha atual" minlength="8" disabled>
							<label for="txtNewPass" class="sr-only">Nova senha</label>
							<input type="password" name="txtNewPass" id="txtNewPass" class="form-control" placeholder="Nova senha" minlength="8" disabled>
							<label for="txtConfirmPass" class="sr-only">Confirmar senha</label>
							<input type="password" name="txtConfirmPass" id="txtConfirmPass" class="form-control" placeholder="Confirmação de senha" oninput="checarSenha(this)" minlength="8" disabled>
						</div>
						<div class="form-group">
							<hr>
							<h5>Contato</h5>
							<hr>
							<label for="txtDDD" class="sr-only">DDD</label>
							<input type="text" name="txtDDD" id="txtDDD" class="form-control" value="<?php if(isset($dados['cod_pessoa'])){ echo $dados['ddd_contatoPessoa']; } ?>" placeholder="DDD" maxlength="2" disabled>
							<label for="txtTel" class="sr-only">Telefone</label>
							<input type="text" name="txtTel" id="txtTel" class="form-control" value="<?php if(isset($dados['cod_pessoa'])){ echo $dados['numero_contatoPessoa']; } ?>" placeholder="Número de telefone" maxlength="9" disabled>
							<select class="form-control" name="opcTipoTel" id="opcTipoTel" disabled>
								<option value="<?php if(isset($dados['cod_pessoa'])){ echo $dados['tipo_contatoPessoa']; } ?>"><?php if(isset($dados['cod_pessoa'])){ echo "Atual: ".$dados['tipo_contatoPessoa']; }else{echo "**Tipo de Telefone**";} ?></option>
								<option value="Residencial">Residencial</option>
								<option value="Celular">Celular</option>
								<option value="Comercial">Comercial</option>
							</select>
						</div>
						<div class="form-group">
							<hr>
							<h5>Endereço</h5>
							<hr>
							<label for="txtCEP" class="sr-only">CEP</label>
							<input type="text" name="txtCEP" id="txtCEP" class="form-control" value="<?php if(isset($dados['cod_pessoa'])){ echo $dados['cep']; } ?>" placeholder="CEP" maxlength="8" disabled>
							<label for="txtNumCasa" class="sr-only">Número</label>
							<input type="number" name="txtNumCasa" id="txtNumCasa" class="form-control" value="<?php if(isset($dados['cod_pessoa'])){ echo $dados['numcomplm']; } ?>" placeholder="Número da residência" disabled>
							<label for="txtComplm" class="sr-only">Complemento</label>
							<input type="text" name="txtComplm" id="txtComplm" class="form-control" value="<?php if(isset($dados['cod_pessoa'])){ echo $dados['complemento']; } ?>" placeholder="Complemento" disabled>
							<label for="txtRef" class="sr-only">Referência</label>
							<input type="text" name="txtRef" id="txtRef" class="form-control" value="<?php if(isset($dados['cod_pessoa'])){ echo $dados['referencia']; } ?>" placeholder="Referência (ex: próx. a Mercadinho Big Bom)" disabled>
							<hr>
						</div>
						<button class="btn btn-lg btn-warning btn-block" id="btnAlterar" type="submit" style="color: white;" disabled>Alterar</button>
						<input type="hidden" name="btnAlterar" value="alterar">
						<br>
					</form>
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
		<script type="text/javascript" src="js/CPF.min.js"></script>
		<script type="text/javascript" src="js/jquery.mask.js"></script>
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
			
			/* Máscara dos forms */
			$(document).ready(function () { 
				var $maskTel = $("#txtTel");
				$maskTel.mask('00000-0000', {reverse: true});
				
				var $maskCEP = $("#txtCEP");
				$maskCEP.mask('00000-000', {reverse: true});
				
				var $maskDtNasc = $("#txtDtNasc");
				$maskDtNasc.mask('00/00/0000', {reverse: true});
			});
			
			/* Ativar alteração de dados */
			function habilitarForms()
				{
					currentvalue = document.getElementById('switch').value;
							if(currentvalue != "Off"){
			 					document.getElementById('switch').value="Off";
						document.getElementById('txtEmail').disabled = false;
						document.getElementById('txtPass').disabled = false;
						document.getElementById('txtPass').value="";
						document.getElementById('txtNewPass').disabled = false;
						document.getElementById('txtConfirmPass').disabled = false;
						document.getElementById('txtDDD').disabled = false;
						document.getElementById('txtComplm').disabled = false;
						document.getElementById('txtCEP').disabled = false;
						document.getElementById('txtTel').disabled = false;
						document.getElementById('txtNumCasa').disabled = false;
						document.getElementById('opcTipoTel').disabled = false;
						document.getElementById('txtRef').disabled = false;
						document.getElementById('btnAlterar').disabled= false;
							}else{
			 					document.getElementById('switch').value="On";
						document.getElementById('txtEmail').disabled = true;
						document.getElementById('txtPass').disabled = true;
						document.getElementById('txtNewPass').disabled = true;
						document.getElementById('txtConfirmPass').disabled = true;
						document.getElementById('txtDDD').disabled = true;
						document.getElementById('txtComplm').disabled = true;
						document.getElementById('txtCEP').disabled = true;
						document.getElementById('txtTel').disabled = true;
						document.getElementById('txtNumCasa').disabled = true;
						document.getElementById('opcTipoTel').disabled = true;
						document.getElementById('txtRef').disabled = true;
						document.getElementById('btnAlterar').disabled= true;
							}
				}
			
			/* Verificar se as senhas coincidem */
			function checarSenha (input){ 
			 			if (input.value != document.getElementById('txtNewPass').value) {
			 				input.setCustomValidity('As senhas não coincidem!');
						} else {
			 				input.setCustomValidity('');
						}
			}
			
			/* Deslogar */
			function sair(){
			 	var r = confirm("Deseja mesmo sair?");
			 	if (r == true) {
					location.href='DAO/exit.php';
			 	} else {
					location.href='#';
			 	}
			}
			
			/* Formatar CPF */
			function formatarCPF() {
				document.getElementById('txtCPF').value = CPF.format(document.getElementById('txtCPF').value);
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