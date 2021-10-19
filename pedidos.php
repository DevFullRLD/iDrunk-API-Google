<?php
	ob_start();
	session_start();
	include ("DAO/conexao.php");
	include ("DAO/dbFunctions.php");

	if(checarLogin($conn)){
		$dados = getDados($conn, $_SESSION['user_id']);
	} else {
		echo "<script>window.location.href='index.php'</script>";
	}
	$queryItens = "SELECT * from Pedido where cod_usuario = {$_SESSION['user_id']} and valortotal_pedido != 0";
	$pedidos = mysqli_query($conn, $queryItens);

	if(isset($_GET['cancPedido'])){
		cancelarPedido($conn, $_GET['cancPedido']);
	}
	?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Home</title>
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
			<a class="text-white">
				<br>
				<h4>Pedidos</h4>
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
		if(isset($_REQUEST['statusCanc'])){
			?>
		<div class="container text-center" style="margin-top: 50px" id="infoMsg" onClick="$('#infoMsg').hide(250);">
			<?php
				if($_REQUEST['statusCanc'] == 1){
				?>
			<div class="alert success">
				<span class="closebtn">&times;</span> 
				Pedido cancelado com sucesso!
			</div>
			<?php
				}
				if($_REQUEST['statusCanc'] == 2){
				?>
			<div class="alert warning">
				<span class="closebtn">&times;</span> 
				Para cancelar pedidos com pagamento já aprovado, favor entrar em contato conosco!
			</div>
			<?php
				}
				if($_REQUEST['statusCanc'] == 3){
				?>
			<div class="alert">
				<span class="closebtn">&times;</span> 
				Erro desconhecido ao cancelar pedido. Contate a administração.
			</div>
			<?php
				}
			?>
			</div>
			<?php
			}
			?>
			<?php
		if(isset($_REQUEST['pedido'])){
			if($_REQUEST['pedido']=='sucesso'){
		?>
		<div class="container text-center" style="margin-top: 50px" id="infoMsg" onClick="$('#infoMsg').hide(250);">
		<div class="alert success">
				<span class="closebtn">&times;</span> 
				Pedido efetuado com sucesso! Em breve você o receberá.
			</div>
		</div>
		<?php
			}
		}
				?>
		
		<!-- Conteúdo -->
		
		<?php
			$numeroPedidos = mysqli_num_rows($pedidos);
			if($numeroPedidos > 0){
		?>
		<div class="container" style="margin-top: 70px">
		<button class="btn btn-sm btn-warning text-white" onClick="window.location='appConfig.php'"><i class="fa fa-arrow-left"></i> Voltar</button>
		<br><br>
			<h5>Meus pedidos</h5>
			<?php
			if(isset($_GET['cod_pedido'])){
				$queryPedido     = "SELECT *
					FROM Usuarios u
					INNER JOIN Pedido p
						on u.cod_usuario = p.cod_usuario
					INNER JOIN ItemPedido i
						on p.cod_pedido = i.cod_pedido
					INNER JOIN Produto pr
						on i.cod_produto = pr.cod_produto
					WHERE p.cod_usuario = {$_SESSION['user_id']} and p.cod_pedido = {$_GET['cod_pedido']}";
				$resultadoPedido = mysqli_query($conn, $queryPedido);
			?>
				<div class="table-responsive" style="padding-top: 15px;" id="detalhePedido">
				<i class="fa fa-times float-right" style="margin-bottom: 5px; font-size: 22px;" onClick="$('#detalhePedido').hide(500); $('#tblPedidos').show(500);"></i>
					<table class="table table-condensed" style="">
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
					while($dadosPedido = mysqli_fetch_array($resultadoPedido)){
					?>
						<tr>
							<td style="display: none" id="cod_pedido"><?php echo $dadosPedido['cod_pedido']; ?></td>
							<td><?php echo utf8_encode($dadosPedido['descricao_produto']); ?></td>
							<td><?php echo $dadosPedido['qtd_produto'] ?></td>
							<td style='white-space: nowrap;'>R$ <?php echo $dadosPedido['preco_produto']; ?></td>
							<td style='white-space: nowrap;'>R$ <?php echo $dadosPedido['vl_total_produto']; ?></td>
						</tr>
					<?php
						$vlTotal = $dadosPedido['valortotal_pedido'];
						$statusPedido = $dadosPedido['StatusPedido'];
						$codPedido = $dadosPedido['cod_pedido'];
							if($vlTotal >= 30 && $vlTotal < 50){
								$taxaEntrega = 3.59;
								$vlTotal += $taxaEntrega;
							}
							if($vlTotal >= 50){
								$taxaEntrega = 0;
								$vlTotal += $taxaEntrega;
							}
						if($vlTotal < 30){
								$taxaEntrega = 7.25;
								$vlTotal += $taxaEntrega;
							}
						}
						?>
						<tr>
							<td colspan="2" style="font-weight: bold;">Taxa de entrega:</td>
							<td><?php if($taxaEntrega != 0){ echo "R$ ".$taxaEntrega; } else { echo "Grátis"; } ?></td>
							<td style="white-space: nowrap;">R$ <?php echo $vlTotal; ?></td>
						</tr>
					</tbody>
					</table>
					<?php
					if($statusPedido != 7){
				?>
					<button class="btn btn-sm btn-danger text-white" onclick="confirmarCancelamento();"><i class="fa fa-ban"></i> Cancelar pedido</button>
				<?php
					}
						?>
				</div>
			<?php
			}
				?>
			
			<div class="table-responsive" style="padding-top: 15px" id="tblPedidos">
			<small id="infoPedido" class="form-text text-muted" style="font-size: 10.5px">* Clique no pedido para mais detalhes.</small>
				<table class="table table-condensed">
					<thead>
						<tr>
							<th>Cód.</th>
							<th>Data</th>
							<th>Total</th>
							<th>Status</th>
						</tr>
					</thead>
					
					<tbody>
				<?php
					while ($itensPedido = mysqli_fetch_assoc($pedidos)) {
						$vlTotalPedido = $itensPedido['valortotal_pedido'];
							if($vlTotalPedido >= 30 && $vlTotalPedido <= 49.99){
								$taxaEntrega = 3.59;
								$vlTotalPedido += $taxaEntrega;
							}
							if($vlTotalPedido >= 50){
								$taxaEntrega = 0;
								$vlTotalPedido += $taxaEntrega;
							}
						if($vlTotalPedido < 30){
								$taxaEntrega = 7.25;
								$vlTotalPedido += $taxaEntrega;
							}
				?>
					<tr onClick="<?php echo "window.location.href='pedidos.php?cod_pedido=".$itensPedido['cod_pedido']."'" ?>">
						<div id="detalhesPedido" style="display: none;">
							<?php echo $itensPedido['cod_pedido']; ?>
						</div>
						<td><?php echo $itensPedido['cod_pedido']; ?></td>
						<td><?php echo implode("/",array_reverse(explode("-",$itensPedido['data_pedido'])))." às ".$itensPedido['hora_pedido']; ?></td>
						<td><?php echo "R$ ".$vlTotalPedido; ?></td>
						<?php 
							if($itensPedido['StatusPedido'] == 1){
								echo "<td>Pendente</td>";
							}
							if($itensPedido['StatusPedido'] == 2){
								echo "<td>Pagamento em análise</td>";
							}
							if($itensPedido['StatusPedido'] == 3){
								echo "<td>Pagamento aprovado</td>";
							}
							if($itensPedido['StatusPedido'] == 4){
								echo "<td>A caminho</td>";
							}
							if($itensPedido['StatusPedido'] == 5){
								echo "<td>Entregue</td>";
							}
							if($itensPedido['StatusPedido'] == 6){
								echo "<td>Estornado</td>";
							}
							if($itensPedido['StatusPedido'] == 7){
								echo "<td>Cancelado</td>";
							}
						?>
					</tr>
				<?php
					}
			} else {
				?>
				</tbody>
				</table>
			</div>
		</div>
		<div class="wrapper text-center">
			<div>
				<img src="img/logo.png" width="40%" style="margin-bottom: 10px;">
				<hr color="#ffdab5">
				<p>N&atilde;o h&aacute; pedidos registrados na sua conta.</p>
				<hr color="#ffdab5">
				<button class="btn btn-block btn-warning text-white" onclick="window.history.back()"><i class="fa fa-arrow-left"></i> Voltar</button>
			</div>
		</div>
		<?php
			}
		?>
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
			
			/* Confirmar cancelamento de pedido */
			function confirmarCancelamento(){
			 	var x = confirm("Deseja mesmo cancelar esse pedido?");
			 	if (x == true) {
					location.href='?cancPedido='+document.getElementById('cod_pedido').innerHTML;
			 	} else {
					location.href='#';
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
		</script>
	</body>
	<?php
		ob_end_flush();
		?>
</html>