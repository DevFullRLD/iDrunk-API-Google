<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<title>iDrunk | Enviando...</title>
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
header("Content-Type: text/html; charset=ISO-8859-1", true);

// Recebe os dados do $_POST
$name = $_POST['txtNome'];
$email = $_POST['txtEmail'];
$subject = $_POST['txtAssunto'];
$message = $_POST['txtMsg'];

$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";

// Dados que serão enviados pelo formulário.
$corpo = "Formulário enviado\n";
$corpo .= "Nome: " . $name . "\n";
$corpo .= "Email: " . $email . "\n";
$corpo .= "Assunto: " . $subject . "\n";
$corpo .= "Comentários: " . $message . "\n";

// E-mail para onde a mensagem será enviada.
$email_to = 'administrativo@idrunk.ga';

// Enviando e-mail e armazenando se deu certo ou não na variável "status".
$status = mail($email_to, $subject, $corpo, $headers);

// Caso der certo o envio, retorna sucesso, no contrário, falha.
if ($status) {
    echo "<script>window.location.href='../contato.php?mensagemStatus=1'</script>";
    
} else {
    echo "<script>window.location.href='../contato.php?mensagemStatus=2'</script>";
}
ob_end_flush();
?>