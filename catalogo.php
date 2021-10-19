<?php
ob_start();
session_start();
header("Content-Type: text/html; charset=ISO-8859-1", true);
include("DAO/conexao.php");
include("DAO/dbFunctions.php");

if (checarLogin($conn)) {
    $sql    = "SELECT * from Pessoas where cod_pessoa = {$_SESSION['user_id']};";
    $result = mysqli_query($conn, $sql);
    $dados  = mysqli_fetch_array($result);
}

// Puxar categorias
$strQuery = "select * from Categorias order by descricao_categoria";
$result   = mysqli_query($conn, $strQuery);

// Puxar tipo de produtos
$query     = "select * from TipoProduto order by desc_tipoProd";
$resultado = mysqli_query($conn, $query);

// Definir como os produtos serão puxados
if (isset($_REQUEST['categ']) && isset($_REQUEST['tipo']) && isset($_REQUEST['filtro'])) {
    // Caso os três valores seja diferente de 0
    if ($_REQUEST['categ'] !== '0' && $_REQUEST['tipo'] !== '0' && $_REQUEST['filtro'] !== '0') {
        if ($_REQUEST['filtro'] === '1') {
            $sql = "select * from Produto where categoria_produto = '{$_REQUEST['categ']}' and tipo_produto = '{$_REQUEST['tipo']}' order by preco_produto asc";
        } else {
            $sql = "select * from Produto where categoria_produto = '{$_REQUEST['categ']}' and tipo_produto = '{$_REQUEST['tipo']}' order by preco_produto desc";
        }
    }
    // Caso categoria e tipo seja diferente de 0, mas não o filtro
    else if ($_REQUEST['categ'] !== '0' && $_REQUEST['tipo'] !== '0' && $_REQUEST['filtro'] === '0') {
        $sql = "select * from Produto where categoria_produto = '{$_REQUEST['categ']}' and tipo_produto = '{$_REQUEST['tipo']}' order by descricao_produto";
    }
    // Caso categoria e filtro estejam definidos, mas não o tipo
    else if ($_REQUEST['categ'] !== '0' && $_REQUEST['tipo'] === '0' && $_REQUEST['filtro'] !== '0') {
        if ($_REQUEST['filtro'] === '1') {
            $sql = "select * from Produto where categoria_produto = '{$_REQUEST['categ']}' order by preco_produto asc";
        } else {
            $sql = "select * from Produto where categoria_produto = '{$_REQUEST['categ']}' order by preco_produto desc";
        }
    }
    // Caso tipo e filtro estejam definidos, mas não a categoria
    else if ($_REQUEST['categ'] === '0' && $_REQUEST['tipo'] !== '0' && $_REQUEST['filtro'] !== '0') {
        if ($_REQUEST['filtro'] == 1) {
            $sql = "select * from Produto where tipo_produto = '{$_REQUEST['tipo']}' order by preco_produto asc";
        } else {
            $sql = "select * from Produto where tipo_produto = '{$_REQUEST['tipo']}' order by preco_produto desc";
        }
    }
    // Caso apenas a categoria esteja definida, mas não os demais
    else if ($_REQUEST['categ'] !== '0' && $_REQUEST['tipo'] === '0' && $_REQUEST['filtro'] === '0') {
        $sql = "select * from Produto where categoria_produto = '{$_REQUEST['categ']}' order by descricao_produto";
    }
    // Caso apenas o tipo esteja definido, mas não os demais
    else if ($_REQUEST['categ'] === '0' && $_REQUEST['tipo'] !== '0' && $_REQUEST['filtro'] === '0') {
        $sql = "select * from Produto where tipo_produto = '{$_REQUEST['tipo']}' order by descricao_produto";
    }
    // Caso apenas o filtro esteja definido, mas não os demais
    else if ($_REQUEST['categ'] === '0' && $_REQUEST['tipo'] === '0' && $_REQUEST['filtro'] !== '0') {
        if ($_REQUEST['filtro'] === '1') {
            $sql = "select * from Produto order by preco_produto asc";
        } else {
            $sql = "select * from Produto order by preco_produto desc";
        }
    } else {
        $sql = "select * from Produto order by descricao_produto";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Cat&aacute;logo</title>
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
				<?php
					if(checarLogin($conn)){
						echo "<h3>Produtos</h3>";
					}
					else{
						?>
				<h3>Por favor,</h3>
				<h6>efetue login para comprar</h6>
				<?php
					}
						?>
			</a>
			<hr>
			<a class="text-white"><i class="fa fa-filter"></i> Filtrar por:</a>
			<a>
				<select class="form-control" name="opcFiltro" id="opcFiltro" style="max-width:85%;">
					<option value="0" <?php if(isset($_REQUEST['filtro'])){ if($_REQUEST['filtro'] == 0){ echo "selected"; }} ?>>N&atilde;o filtrar</option>
					<option value="1" <?php if(isset($_REQUEST['filtro'])){ if($_REQUEST['filtro'] == 1){ echo "selected"; }} ?>>Menor pre&ccedil;o</option>
					<option value="2" <?php if(isset($_REQUEST['filtro'])){ if($_REQUEST['filtro'] == 2){ echo "selected"; }} ?>>Maior pre&ccedil;o</option>
				</select>
			</a>
			<hr>
			<a href="index.php"><i class="fa fa-home"></i> Home <span class="sr-only">(current)</span></a>
			<a href="catalogo.php?categ=0&tipo=0&filtro=0"><i class="fa fa-beer"></i> Cat&aacute;logo</a>
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
		
		<!-- Conteúdo -->
		<br><br><br>
		<div class="container">
			<h6>Categorias</h6>
			<select class="form-control" name="opcCategoria" id="opcCategoria">
				<option value="0">Todas as categorias</option>
				<?php 
					while($categ = mysqli_fetch_assoc($result)){
					?>
				<option value="<?php echo $categ['id_categoria']; ?>" <?php if(isset($_REQUEST['categ'])){ if($_REQUEST['categ'] == $categ['id_categoria']){ echo "selected"; }} ?>><?php echo $categ['descricao_categoria']?></option>
				<?php
					}
					?>
			</select>
			<br>
			<h6>Tipo de produto</h6>
			<select class="form-control" name="opcProduto" id="opcProduto">
				<option value="0">Todos os produtos</option>
				<?php 
					while($tipoProd = mysqli_fetch_assoc($resultado)){
					?>
				<option value="<?php echo $tipoProd['cod_tipoProd']; ?>" <?php if(isset($_REQUEST['tipo'])){ if($_REQUEST['tipo'] == $tipoProd['cod_tipoProd']){ echo "selected"; }} ?>><?php echo $tipoProd['desc_tipoProd']?></option>
				<?php
					}
					?>
			</select>
			<br>
			<?php
				$resultados = mysqli_query($conn, $sql);
				$nresultados = mysqli_num_rows($resultados);
				
				if(!$nresultados>0){
					echo
					"<div class='text-center' style='margin-top: 30px'>
			<div style='padding: 30px; border: 1px solid rgba(255, 173, 91, .6);'>
				<h6 style='font-size: 14px;'>N&atilde;o h&aacute; produtos com essas caracter&iacute;sticas!</h6>
				<hr color='#ffdab5'>
			</div>
		</div>";
				}else{
					echo "<div class='container'>
						
					</div>";
					echo "<div class='row'>";
						while($produto = mysqli_fetch_assoc($resultados)){
						echo"
							<div class='container item border-bottom-0 border-right-0 border-left-0' style='padding-top: 5px; padding-bottom: 5px;'>
								<hr color=#FFAD5B>
								<ul>
									<li>
										<span class='float-left'>
										<img class='item-image' src='{$produto['img']}' class='rounded mx-auto d-block' height='120' width='120'>
										</span>
												<p>{$produto['descricao_produto']}</p>
												<br><br><br>
												<h5 class='float-left'>R$ {$produto['preco_produto']}<a style='font-size: 16px;'><br>Estoque: {$produto['quantidade_produto']}</a></h5>
												<form method='post' action='DAO/insertPedido.php'>
											<button class='btn btn-warning btn-sm text-white float-right' role='button' value='{$produto['cod_produto']}' id='btnAddCarrinho' name='btnAddCarrinho'";if($produto['quantidade_produto'] < 1){echo "disabled";} echo"><i class='fa fa-cart-plus fa-2x'></i></button>
											</form>
									</li>
								</ul>
							</div>
							";
						}
					echo"</div>";
				}
				?>
		</div>
		<!-- Fim -->
		
		<!-- CSS e Javascript -->
		<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>
		<link type="text/css" rel="stylesheet" href="css/normalize.css"/>
		<link type="text/css" rel="stylesheet" href="css/sidebar.css"/>
		<link type="text/css" rel="stylesheet" href="css/navbar.css"/>
		<link type="text/css" rel="stylesheet" href="css/font-awesome.min.css">
		<link type="text/css" rel="stylesheet" href="css/img.css">
		<link type='text/css' rel='stylesheet' href='css/themes/dark.css'>
		<script type="text/javascript" src="js/popper.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/gerirCookies.js"></script>
		<script>
			/* Atualizar categoria */
			$('#opcCategoria').change(function() {
			 			window.location = "?categ=" + $(this).val() + "&tipo=" + $('#opcProduto').val() + "&filtro=" + $('#opcFiltro').val();
			});
			
			/* Atualizar tipo de produto */
			$('#opcProduto').change(function() {
			 			window.location = "?categ=" + $('#opcCategoria').val() + "&tipo=" + $(this).val() + "&filtro=" + $('#opcFiltro').val();
			});
			
			$('#opcFiltro').change(function(){
				window.location = "?categ=" + $('#opcCategoria').val() + "&tipo=" + $('#opcProduto').val() + "&filtro=" + $(this).val();
			});
			
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