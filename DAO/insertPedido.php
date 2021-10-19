<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Inserindo pedido...</title>
		<!-- CSS & Javascript -->
		<link type="text/css" rel="stylesheet" href="../css/custom.css"/>
		<script type='text/javascript' src="../js/jquery-3.2.1.min.js"></script>
		<script type='text/javascript' src="../js/modernizr.js"></script>
		<script type='text/javascript' src="../js/loadPage.js"></script>
	</head>
	<body>
		<div class="pre-load"></div>
<?php
ob_start();
session_start();
include("conexao.php");
include("dbFunctions.php");

if (isset($_GET['codProduto'])) {
    $cod_produto = $_GET['codProduto'];
} else {
    $cod_produto = $_POST["btnAddCarrinho"];
}

if (checarLogin($conn)) {
    $cod_usuario = $_SESSION['user_id'];
} else {
    echo "<script>window.location.href='../logar.php?loginStatus=5&codProduto={$cod_produto}'</script>";
}

if (addPedido($conn, $cod_usuario, $cod_produto)) {
    echo "<script>window.location.href='../carrinho.php?statusProduto={$cod_produto}'</script>";
} else {
    echo "<script>window.location.href='../carrinho.php?msgStatus=1'</script>";
}

ob_end_flush();
?>
	</body>
</html>