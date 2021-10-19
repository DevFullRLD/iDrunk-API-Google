<?php
	ob_start();
	session_start();
	header("Content-Type: text/html; charset=utf8");
	include("DAO/conexao.php");
	include("DAO/dbFunctions.php");
	
	$cont    = 1;
	$sucesso = false;
	$taxaEntrega = 7.25;
	
	// Puxar dados do usuário
	if (checarLogin($conn)) {
	    $dados = getDados($conn, $_SESSION['user_id']);
	} else {
	    echo "<script>window.location.href='logar.php?loginStatus=4'</script>";
	}
	
	$cep = $dados['cep'];
	$endereco = getEndereco($cep);
	
	// Puxar produto adicionado
	if (isset($_GET['statusProduto'])) {
	    $query = "select * from Produto where cod_produto = {$_GET['statusProduto']}";
	    if ($resultadoProdutos = mysqli_query($conn, $query)) {
	        while ($produto = mysqli_fetch_assoc($resultadoProdutos)) {
	            $descProduto = $produto['descricao_produto'];
	        }
	    }
	}
	
	// Alterar para "cancelado" pedido com valor zerado
	$queryAttStatus = "select * from Pedido where cod_usuario = '{$_SESSION['user_id']}' and StatusPedido = '1'";
	// Execução da query
	if ($resultAttStatus = mysqli_query($conn, $queryAttStatus)) {
	    while ($pedStat = mysqli_fetch_assoc($resultAttStatus)) {
	        @$vlPedido = $pedStat['valortotal_pedido'];
	        @$codPedido = $pedStat['cod_pedido'];
	        if ($vlPedido == '0.00') {
	            $queryAttStatus = "Update Pedido set StatusPedido = 7 where cod_usuario = '{$_SESSION['user_id']}' and StatusPedido = 1";
	            // Segunda execução da query
	            if (mysqli_query($conn, $queryAttStatus)) {
	                echo "<script>window.location.href='carrinho.php'</script>";
	            }
	        }
	    }
	}
	// Atualizar quantidade
	if (isset($_GET['codprod']) && isset($_GET['attQtd'])) {
	    $cont      = 0;
	    $queryProd = "select * from Produto where cod_produto = '{$_GET['codprod']}' and quantidade_produto >= '{$_GET['attQtd']}'";
	    if ($resultProd = mysqli_query($conn, $queryProd)) {
			$numResult = mysqli_num_rows($resultProd);
			if($numResult > 0){
				while ($getProduto = mysqli_fetch_assoc($resultProd)) {
	            $precoProduto = $getProduto['preco_produto'];
	            $cont         = 1;
	        }
			}else{
				 echo "<script>window.location.href='carrinho.php?msgStatus=2'</script>";
			}
	        if ($cont == 1) {
	            $precoProduto *= $_GET['attQtd'];
	            if (attQtd($conn, $_SESSION['user_id'], $_GET['codprod'], $codPedido, $_GET['attQtd'], $precoProduto)) {
	                echo "<script>window.location.href='carrinho.php'</script>";
	            }
	        }
	    }
	}
	// Puxar pedido do usuário do banco, junto com os itens
	$resultadoItens = getPedido($conn, $_SESSION['user_id'], '1');
	
	// Deletar produto do carrinho
	if (isset($_GET['delete'])) {
	    $valorItem    = 0;
	    $valorPedido  = 0;
	    $queryDelete1 = "select * from ItemPedido ip inner join Pedido p ON ip.cod_pedido=p.cod_pedido inner join Usuarios u ON p.cod_usuario=u.cod_usuario where ip.cod_produto = '{$_GET['delete']}' AND u.cod_usuario = '{$_SESSION['user_id']}'";
	    // Primeira execução da query
	    try {
	        if ($resultDelete1 = mysqli_query($conn, $queryDelete1)) {
	            while ($prod = mysqli_fetch_assoc($resultDelete1)) {
	                $valorItem    = $prod['vl_total_produto'];
	                $queryDelete2 = "select * from Pedido where cod_usuario = '{$_SESSION['user_id']}' and StatusPedido = 1";
	                // Segunda execução da query
	                if ($resultDelete2 = mysqli_query($conn, $queryDelete2)) {
	                    while ($ped = mysqli_fetch_assoc($resultDelete2)) {
	                        $valorPedido = $ped['valortotal_pedido'];
	                        $valorPedido -= $valorItem;
	                        $queryDelete3 = "update Pedido set valortotal_pedido = '{$valorPedido}' where cod_usuario = '{$_SESSION['user_id']}' and StatusPedido = 1;";
	                        $queryDelete3 .= "delete ip from ItemPedido ip inner join Pedido p ON ip.cod_pedido=p.cod_pedido inner join Usuarios u ON p.cod_usuario= u.cod_usuario where ip.cod_produto = '{$_GET['delete']}' AND u.cod_usuario= '{$_SESSION['user_id']}'";
	                        // Terceira execução da query
	                        if (mysqli_multi_query($conn, $queryDelete3)) {
	                            echo "<script>window.location.href='?delSuccess=1'</script>";
	                        } else {
	                            echo "<script>window.location.href='?delSuccess=2'</script>";
	                        }
	                    }
	                }
	            }
	        }
	    }
	    catch (\Exception $e) {
	        var_dump($e->getMessage());
	    }
	}
	?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Carrinho</title>
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
				<h3>Carrinho</h3>
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
				}else{
					echo "<script>window.location.href='logar.php?loginStatus=4'</script>";
				}
				?>
		</div>
		<!-- Fim -->
		<?php 
			if(isset($_GET['delSuccess'])){
			?>
		<div class="container text-center" style="margin-top: 50px" id="infoMsg" onClick="$('#infoMsg').hide(250);">
			<?php
				if($_GET['delSuccess'] == 1){
				?>
			<div class="alert success">
				<span class="closebtn">&times;</span> 
				Item excluído do carrinho com sucesso!
			</div>
			<?php
				} else {
				?>
			<div class="alert">
				<span class="closebtn">&times;</span> 
				Falha ao excluir item do carrinho!
			</div>
			<?php
				}
				?>
		</div>
		<?php
			}
			?>
		<?php
			if(isset($_GET['statusProduto'])){
			?>
		<div class="container text-center" style="margin-top: 50px" id="infoMsg" onClick="$('#infoMsg').hide(250);">
			<div class="alert success">
				<span class="closebtn">&times;</span> 
				Item adicionado ao carrinho com sucesso!
			</div>
		</div>
		<?php
			} if(isset($_GET['msgStatus'])){
				if($_GET['msgStatus'] == 1){
			?>
		<div class="container text-center" style="margin-top: 50px" id="infoMsg" onClick="$('#infoMsg').hide(250);">
			<div class="alert warning">
				<span class="closebtn">&times;</span> 
				Item já adicionado ao carrinho!
			</div>
		</div>
		<?php
			}
				if($_GET['msgStatus'] == 2){
		?><div class="container text-center" style="margin-top: 50px" id="infoMsg" onClick="$('#infoMsg').hide(250);">
			<div class="alert warning">
				<span class="closebtn">&times;</span> 
				Não há estoque para a quantidade selecionada!
			</div>
		</div>
		
		<?php	
				}
			}
			?>
		<!-- Conteúdo -->
		<?php 
			if(checarCarrinho($conn, $_SESSION['user_id'])){
			?>
		<div class="container" style="padding-top: 75px">
			<button class="btn btn-sm btn-warning text-white" onclick="window.location.href='catalogo.php?categ=0&tipo=0&filtro=0'"><i class="fa fa-arrow-left"></i> Continuar comprando</button>
			<div class="table-responsive" style="padding-top: 15px">
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
							while ($itensCarrinho = mysqli_fetch_assoc($resultadoItens)) {
								?>
						<tr>
							<td><?php echo utf8_encode($itensCarrinho['descricao_produto']); ?></td>
							<td align="center">
								<input id="<?php echo $itensCarrinho['cod_produto']; ?>" name="<?php echo $itensCarrinho['cod_produto']; ?>" size="1" value="<?php echo $itensCarrinho['qtd_produto'] ?>" onblur="setQtd(this);" type="number" style="width: 23px; margin-bottom: 2px;" maxlength="2">
								<a href="?delete=<?php echo $itensCarrinho['cod_produto']; ?>" style="font-size: 15.5px; color: #FFAD5B;"><i class="fa fa-minus-circle"></i></a>
							</td>
							<td style='white-space: nowrap;'>R$ <?php echo $itensCarrinho['preco_produto']; ?></td>
							<td style='white-space: nowrap;'>R$ <?php echo $itensCarrinho['vl_total_produto']; ?></td>
						</tr>
						<?php
							$cont++;
							$vlTotal = $itensCarrinho['valortotal_pedido'];
							
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
			<br>
			<p style="font-size: 12px;">Entregar em: </p>
			<select class="form-control" name="opcEndereco" id="opcEndereco" style="font-size: 10.3px;">
				<option value="1" id="enderecCad" selected>Endereço cadastrado: <?php echo $endereco->logradouro.", Nº ".$dados['numcomplm']." – ".$dados['complemento']; ?></option>
				<option value="2" id="posicAtual"></option>
			</select>
			<br>
				<button class="btn btn-block btn-warning text-white" onClick="prosseguirCompra();">Prosseguir compra <i class="fa fa-arrow-right"></i></button>
		</div>
		<?php
			} else {
				?>
		<div class="wrapper text-center">
			<div>
				<img src="img/logo.png" width="40%" style="margin-bottom: 10px;">
				<hr color="#ffdab5">
				<p>N&atilde;o h&aacute; produtos no carrinho.</p>
				<hr color="#ffdab5">
				<button class="btn btn-block btn-warning text-white" onclick="window.location.href='catalogo.php?categ=0&tipo=0&filtro=0'"><i class="fa fa-arrow-left"></i> Ir para catálogo</button>
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
			
			/* Deslogar */
			function sair(){
			 	var r = confirm("Deseja mesmo sair?");
			 	if (r == true) {
					location.href='DAO/exit.php';
			 	} else {
					location.href='#';
			 	}
			}
			/* Definir quantidade */
			function setQtd (input){
				if (input.value != "") {
					var qtd = parseInt(input.value);
					if(!isNaN(qtd)){
						location.href='?attQtd=' + input.value + '&codprod=' + input.id;
					} else {
				   		alert('Digite somente números!');
					}	
				} else {
					alert('Quantidade não pode estar em branco!');
				}
			}
			
			/* Tema */
			$(document).ready(function() {
				if(getCookie("setTheme") == 1){
					$('body').toggleClass('night');
				} else {
				}
			});
			
			/* Sistema de geolocalização */
			var x=document.getElementById("posicAtual");
			
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
			  x.innerHTML = "Posição atual: "+data.results[0].address_components[1].short_name+", "+data.results[0].address_components[2].short_name+", Nº "+data.results[0].address_components[0].long_name+" – "+data.results[0].address_components[7].long_name;
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
			
			function prosseguirCompra(){
				window.location = "DAO/confirmarPedido.php?end=" + $('#opcEndereco').val();
			}
		</script>
	</body>
	<?php
		ob_end_flush();
		?>
</html>