<?php
	ob_start();
	session_start();
	include ("conexao.php");
	include ("dbFunctions.php");
	
	if(checarLogin($conn)){
		$dados = getDados($conn, $_SESSION['user_id']);
	} else {
		echo "<script>window.location.href='../index.php'</script>";
	}
	$cep = $dados['cep'];
	$endereco = getEndereco($cep);

	// Puxar pedido do usuário do banco, junto com os itens
	$resultadoItens = getPedido($conn, $_SESSION['user_id'], '1');
	$taxaEntrega = 7.25;
	?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Confirmar pedido</title>
		<!-- CSS & Javascript -->
		<link type="text/css" rel="stylesheet" href="../css/custom.css"/>
		<script type='text/javascript' src="../js/jquery-3.2.1.min.js"></script>
		<script type='text/javascript' src="../js/modernizr.js"></script>
		<script type='text/javascript' src='../js/loadPage.js'></script>
	</head>
	<body>
		<div class="pre-load"></div>
		<!-- Sidebar, topbar & botão para abrí-la -->
		<nav class="navbar fixed-top navbar-light bg-faded">
			<button class="navbar-toggler" type="button" id="btnSidebar" style="float:left; z-index:1" aria-expanded="false" aria-label="Toggle navigation">
			<span class="fa fa-bars"></span>
			</button>
			<a class="brand-text" style="color: white">iDrunk</a>
			<button class="navbar-toggler" type="button" style="float:right; z-index:2" aria-expanded="false" aria-label="Toggle navigation" onClick="window.location.href='../carrinho.php'">
			<span class="fa fa-shopping-cart"></span>
			</button>
		</nav>
		<div id="sidebar" class="sidenav" style="white-space: nowrap;">
			<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
			<a class="text-white">
				<br>
				<h4>Confirmar pedido</h4>
			</a>
			<hr>
			<a href="../index.php"><i class="fa fa-home"></i> Home <span class="sr-only">(current)</span></a>
			<a href="../catalogo.php?categ=0&tipo=0&filtro=0"><i class="fa fa-beer"></i> Catálogo</a>
			<a href="../contato.php"><i class="fa fa-phone"></i> Contato</a>
			<a href="../sobre.php"><i class="fa fa-info-circle"></i> Sobre o app</a>
			<?php
				if(checarLogin($conn)){
				?>
			<a href="../appConfig.php"><i class="fa fa-cogs"></i> Configura&ccedil;&otilde;es</a>
			<a onClick="sair()" style="color: white;"><i class="fa fa-sign-out"></i> Sair</a>
			<?php
				}else{
				?>
			<a href="../logar.php"><i class="fa fa-sign-in"></i> Logar</a>
			<a href="../cadastro.php"><i class="fa fa-user-plus"></i> Cadastrar</a>
			<a href="../appConfig.php"><i class="fa fa-cogs"></i> Configura&ccedil;&otilde;es</a>
			<?php
				}
				?>
		</div>
		<!-- Fim -->

		<!-- Conteúdo -->
			<div class="container" style="margin-top: 70px">
			<button class="btn btn-sm btn-warning text-white" onClick="window.location='../carrinho.php'"><i class="fa fa-arrow-left"></i> Voltar</button>
		<br><br>
			<h5>Confirmar informações</h5>
				<div class="table-responsive" style="padding-top: 15px;" id="detalhePedido">
					<table class="table table-condensed">
					<thead>
						<tr>
							<th>Produtos</th>
							<th>Qtd.</th>
							<th>Und.</th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody>
					<?php
					while ($dadosPedido = mysqli_fetch_assoc($resultadoItens)) {
					?>
						<tr>
							<td><?php echo utf8_encode($dadosPedido['descricao_produto']); ?></td>
							<td><?php echo $dadosPedido['qtd_produto'] ?></td>
							<td style='white-space: nowrap;'>R$ <?php echo $dadosPedido['preco_produto']; ?></td>
							<td style='white-space: nowrap;'>R$ <?php echo $dadosPedido['vl_total_produto']; ?></td>
						</tr>
					<?php
						$vlTotal = $dadosPedido['valortotal_pedido'];
							if($vlTotal >= 30 && $vlTotal <= 49.99){
								$taxaEntrega = 3.59;
							}
							if($vlTotal >= 50){
								$taxaEntrega = 0;
							}
						}
						?>
						<tr>
							<td colspan="2" style="font-weight: bold;">Taxa de entrega:</td>
							<td><?php if($taxaEntrega != 0){ echo "R$ ".$taxaEntrega; } else { echo "Grátis"; } ?></td>
							<td style="white-space: nowrap;">R$ <?php echo $vlTotal+=$taxaEntrega; ?></td>
						</tr>
					</tbody>
					</table>
				</div>
				<p style="font-size: 12px;"><strong>Nome do cliente:</strong> <?php echo $dados['nome_pessoa']; ?></p>
				<p style="font-size: 12px;" <?php if(isset($_GET['end'])){ if($_GET['end'] == 2){ $num=3; echo "id='local'"; $end=2; }} ?>><strong>Local de entrega:</strong> <?php if(isset($_GET['end'])){ if($_GET['end'] == 2){} else { $end=$cep; echo $endereco->logradouro.", Nº ".$dados['numcomplm']." – ".$dados['complemento'].", ".$cep; $num=$dados['numcomplm']; } }?></p>
				
				<form method="post" <?php if($_GET['end'] == 1){ echo "action='insertPagSeguro.php?end=$end&num=$num'"; } ?> name="frmFinalizar" id="frmFinalizar">
					<div class="form-group">
						<label for="txtMsg" style="font-size: 12px;">Observação:</label>
						<textarea class="form-control" name="txtObs" rows="5" id="txtObs"></textarea>
						<hr>
						<button class="btn btn-md btn-warning btn-block" type="submit" style="color:white;" onclick="window.location='gay.php'"><i class="fa fa-check-circle"></i> Ir para pagamento</button>
						<hr></hr>
					</div>
				</form>
			</div>
			<footer>
				<div class="container text-center" style="padding-bottom: 10px;">
					<small id="pagSeguro" class="form-text text-muted" style="font-size: 10.5px; margin-bottom: 5px;">Pagamento processado pelo</small>
					<img src="../img/pagseguro.png" class="img-fluid" width="37%"/>
				</div>
			</footer>
		<!-- Fim -->
		<!-- CSS e Javascript -->
		<link type="text/css" rel="stylesheet" href="../css/bootstrap.min.css"/>
		<link type="text/css" rel="stylesheet" href="../css/normalize.css"/>
		<link type="text/css" rel="stylesheet" href="../css/sidebar.css"/>
		<link type="text/css" rel="stylesheet" href="../css/navbar.css"/>
		<link type="text/css" rel="stylesheet" href="../css/font-awesome.min.css">
		<link type='text/css' rel='stylesheet' href='../css/themes/dark.css'>
		<script type="text/javascript" src="../js/popper.min.js"></script>
		<script type="text/javascript" src="../js/bootstrap.min.js"></script>
		<script type="text/javascript" src="../js/gerirCookies.js"></script>
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
					location.href='exit.php';
			 	} else {
					location.href='#';
			 	}
			}
			
			if ($('#detalhePedido').length) {
				$('#tblPedidos').hide();
			}
			
			/* Tema */
			$(document).ready(function() {
				if(getCookie("setTheme") == 1){
					$('body').toggleClass('night');
				} else {
				}
			});
			
		/* Sistema de geolocalização */
			var x=document.getElementById("local");
			
			$(document).ready(function() {
			 getLocation();
			});
			
			function getLocation()
			{
			if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        x.innerHTML = "Seu navegador não suporta geolocalização.";
    }
			}
			function showPosition(position)
			{
			var locationAPI = "https://maps.googleapis.com/maps/api/geocode/json?latlng="+position.coords.latitude+","+position.coords.longitude+"&sensor=true&key=AIzaSyD9klScLNGcs0RQ5IEOGXo6Iw4q0gJ8P38";
				/*var lat = position.coords.latitude;
				var long = position.coords.longitude;*/
			x.innerHTML=locationAPI;
			$.get({
			 url: locationAPI,
			 success: function(data){
			  console.log(data);
			  x.innerHTML = "<strong>Local de entrega: </strong>" + data.results[0].address_components[1].short_name+", "+data.results[0].address_components[2].short_name+", Nº "+data.results[0].address_components[0].long_name+" – "+data.results[0].address_components[7].long_name;
				var cep = data.results[0].address_components[7].long_name;
				var num = data.results[0].address_components[0].long_name;
				var cep = cep.replace(/[^a-zA-Z0-9 ]/g, "");
				document.getElementById('frmFinalizar').action;
				if(document.getElementById('local')){
					document.getElementById('frmFinalizar').action = "insertPagSeguro.php?end="+cep+"&num="+num;
				}
			 }
			});
			}
			function showError(error)
			{
			switch(error.code)
			 {
			 case error.PERMISSION_DENIED:
			   x.innerHTML="Usuário rejeitou a solicitação de geolocalização."
			   break;
			 case error.POSITION_UNAVAILABLE:
			   x.innerHTML="Localização indisponível."
			   break;
			 case error.TIMEOUT:
			   x.innerHTML="A requisição expirou."
			   break;
			 case error.UNKNOWN_ERROR:
			   x.innerHTML="Erro desconhecido."
			   break;
			 }
			}
			
    </script>
	</body>
	<?php
		ob_end_flush();
		?>
</html>