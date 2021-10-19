<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Logando...</title>
		<!-- CSS & Javascript -->
		<link type="text/css" rel="stylesheet" href="../css/custom.css"/>
		<script type='text/javascript' src="../js/jquery-3.2.1.min.js"></script>
		<script type='text/javascript' src="../js/modernizr.js"></script>
		<script type='text/javascript' src="../js/loadPage.js"></script>
	</head>
	<body>
		<div class="pre-load"></div>
	</body>
</html>
<?php
ob_start();
include("conexao.php");
include("dbFunctions.php");

$email      = $_POST["txtEmail"];
$senha      = sha1($_POST["txtPass"]);
$carrinho   = $_POST["statusCarrinho"];
$produto_id = $_POST["codProduto"];

if (testarLogin($conn, $email, $senha)) {
    startSession($conn, $email, $senha);
    if (!empty($_POST["remember"])) {
        setcookie("user_email", $_POST["txtEmail"], time() + (86400 * 7), "/");
        setcookie("user_pass", base64_encode($_POST["txtPass"]), time() + (86400 * 7), "/");
        //echo $_COOKIE["user_email"];
        //echo $_COOKIE["user_pass"];
    } else {
        if (isset($_COOKIE["user_email"])) {
            setcookie("user_email", "", time() - 3600, "/");
            unset($_COOKIE["user_email"]);
        }
        if (isset($_COOKIE["user_pass"])) {
            setcookie("user_pass", "", time() - 3600, "/");
            unset($_COOKIE["user_pass"]);
        }
    }
}

if (checarLogin($conn)) {
    if ($carrinho == 1) {
        echo "<script>window.location.href='../carrinho.php'</script>";
    }
    if ($carrinho == 2) {
        echo "<script>window.location.href='insertPedido.php?codProduto={$produto_id}'</script>";
    } else {
        echo "<script>window.location.href='../index.php?loginStatus=1'</script>";
    }
} else {
    echo "<script>window.location.href='../logar.php?loginStatus=2'</script>";
}
ob_end_flush();
?>