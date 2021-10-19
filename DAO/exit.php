<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Saindo...</title>
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
session_start();
include("conexao.php");
include("dbFunctions.php");

if (checarLogin($conn)) {
} else {
    echo "<script>window.location.href='../index.php'</script>";
}

$cont = 0;

if (isset($_SESSION['user_email'])) {
    unset($_SESSION['user_email']);
    $cont++;
}
if (isset($_SESSION['user_pass'])) {
    unset($_SESSION['user_pass']);
    $cont++;
}
if (isset($_SESSION['user_id'])) {
    unset($_SESSION['user_id']);
    $cont++;
}

if ($cont == 3) {
    session_destroy();
    echo "<script>window.location.href='../index.php?loginStatus=3'</script>";
} else {
    echo "Não há usuário logado!";
}
?>