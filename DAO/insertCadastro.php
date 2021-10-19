<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Cadastrando...</title>
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
session_start();
include("conexao.php");
include("dbFunctions.php");

$nome    = ($_POST["txtName"]);
$email   = ($_POST["txtEmail"]);
$cpf     = ($_POST["txtCPF"]);
$dtNasc  = ($_POST["txtDtNasc"]);
$ddd     = ($_POST["txtDDD"]);
$tel     = ($_POST["txtTel"]);
$tipoTel = ($_POST["opcTipoTel"]);
$senha   = sha1($_POST["txtPass"]);
$cep     = ($_POST["txtCEP"]);
$complm  = ($_POST["txtComplm"]);
$ref     = ($_POST["txtRef"]);
$numCasa = ($_POST["txtNumCasa"]);

$sucesso = false;

// Removendo e alterando pontuações
$cpf    = preg_replace('/[^0-9]/', '', $cpf);
$cep    = preg_replace('/[^0-9]/', '', $cep);
$tel    = preg_replace('/[^0-9]/', '', $tel);
$ref	= str_replace("'", '', $ref);

$dtNasc = implode("-", array_reverse(explode("/", $dtNasc)));

if (checarCadastro($conn, $email, $cpf)) {
    if (addUsuario($conn, $nome, $email, $senha, $cpf, $dtNasc, $ddd, $tel, $tipoTel, $cep, $complm, $ref, $numCasa)) {
        echo "<script>window.location.href='../index.php?cadastroStatus=1'</script>";
    } else {
        echo "<script>window.location.href='../cadastro.php?cadastroStatus=2'</script>";
    }
} else {
    echo "<script>window.location.href='../cadastro.php?cadastroStatus=3'</script>";
}
ob_end_flush();
?>