<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Atualizando dados...</title>
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

if (checarLogin($conn)) {
} else {
    echo "<script>window.location.href='../index.php'</script>";
}

$email     = ($_POST["txtEmail"]);
$ddd       = ($_POST["txtDDD"]);
$tel       = ($_POST["txtTel"]);
$tipoTel   = ($_POST["opcTipoTel"]);
$senha     = sha1($_POST["txtPass"]);
$senhaNova = sha1($_POST["txtNewPass"]);
$cep       = ($_POST["txtCEP"]);
$complm    = ($_POST["txtComplm"]);
$ref       = ($_POST["txtRef"]);
$numCasa   = ($_POST["txtNumCasa"]);

// Removendo e alterando pontuações
$cep = preg_replace('/[^0-9]/', '', $cep);
$tel = preg_replace('/[^0-9]/', '', $tel);

$cont              = 0;
$successSenhaAtual = false;
$sucessSenhaNova   = false;

// Verificar se senha atual digitada corresponde com o banco
$querySenhaAtual = "SELECT * FROM Usuarios where senha_usuario = '{$senha}'";
$resultado       = mysqli_query($conn, $querySenhaAtual);

// Verificar se senha nova já está no banco
$querySenhaNova = "SELECT * FROM Usuarios where senha_usuario = '{$senhaNova}'";
$testeSenhaNova = mysqli_query($conn, $querySenhaNova);

// Puxar e-mail do banco
$queryEmail = "SELECT * from Usuarios where cod_usuario = '{$_SESSION['user_id']}'";
$testeEmail = mysqli_query($conn, $queryEmail);

$updateCadastro = "";
$updateUsuarios = "";

while (mysqli_fetch_assoc($resultado)) {
    $successSenhaAtual = true;
}

while (mysqli_fetch_assoc($testeSenhaNova)) {
    $sucessSenhaNova = true;
}

if ($email == "") {
    while ($usuario = mysqli_fetch_assoc($testeEmail)) {
        $email = $usuario['login_usuario'];
    }
}
if ($senhaNova == sha1("")) {
    $updateUsuarios = "update Usuarios set login_usuario='{$email}' where cod_pessoa = '{$_SESSION['user_id']}';";
} else {
    if ($senha != sha1("")) {
        if ($successSenhaAtual) {
            echo "<script>window.location.href='../alterarDados.php?msg=5'</script>";
        }
        if ($successSenhaAtual) {
            $updateUsuarios = "update Usuarios set login_usuario='{$email}', senha_usuario='{$senhaNova}' where cod_pessoa = '{$_SESSION['user_id']}';";
        } else {
            echo "<script>window.location.href='../alterarDados.php?msg=3'</script>";
        }
    } else {
        echo "<script>window.location.href='../alterarDados.php?msg=4'</script>";
    }
}
$updateContato  = "update ContatoPessoa set ddd_contatoPessoa='{$ddd}', numero_contatoPessoa='{$tel}', tipo_contatoPessoa='{$tipoTel}' where cod_pessoa = '{$_SESSION['user_id']}';";
$updateEndereco = "update EnderecoPessoa set cep='{$cep}', numcomplm='{$numCasa}', complemento='{$complm}', referencia='{$ref}' where cod_pessoa = '{$_SESSION['user_id']}'";
$updateCadastro .= $updateUsuarios;
$updateCadastro .= $updateContato;
$updateCadastro .= $updateEndereco;

if (mysqli_multi_query($conn, $updateCadastro)) {
    $cont++;
}
if ($cont == 1) {
    echo "<script>window.location.href='../alterarDados.php?msg=2'</script>";
} else {
    echo "<script>window.location.href='../alterarDados.php?msg=1'</script>";
}
ob_end_flush();
?>