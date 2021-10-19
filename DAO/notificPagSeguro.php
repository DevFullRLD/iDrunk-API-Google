<?php
header("access-control-allow-origin: https://sandbox.pagseguro.uol.com.br");
include_once 'conexao.php';
include_once 'dbFunctions.php';
$notificationCode = preg_replace('/[^[:alnum:]-]/', '', $_POST["notificationCode"]);

// Dados ambiente sandbox
$token = 'AEBF129630CA46B08A48A68F7A449DA2';
$email = 'gian.jesus@etec.sp.gov.br';

// URL ambiente sandbox
$url = 'https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/' . $notificationCode . '?email=' . $email . '&token=' . $token;

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_URL, $url);
$http = curl_getinfo($curl);
$xml  = curl_exec($curl);
curl_close($curl);

if ($xml == 'Unauthorized') {
    //Insira seu código avisando que o sistema está com problemas, sugiro enviar um e-mail avisando para alguém fazer a manutenção 
    echo 'Mensagem: Seu email e/ou token est&aacute;(&atilde;o) com problema(s).<br><br>';
    
    //    header('Location: paginaDeErro.php');
    exit(); //Mantenha essa linha
}
$xml = simplexml_load_string($xml);


$reference = $xml->reference;
$status    = $xml->status;

if ($status == 1) {
    $statusiDrunk = 1;
}
if ($status == 2) {
    $statusiDrunk = 2;
}
if ($status == 3) {
    $statusiDrunk = 3;
}
if ($status == 4) {
    $statusiDrunk = 3;
}
if ($status == 5) {
    $statusiDrunk = 1;
}
if ($status == 6) {
    $statusiDrunk = 6;
}
if ($status == 7) {
    $statusiDrunk = 7;
}
if ($status == 8) {
    $statusiDrunk = 6;
}
if ($status == 9) {
    $statusiDrunk = 1;
}

if ($reference && $status) {
    $reference = preg_replace('/[^0-9]/', '', $reference);
    
    $queryAttStatus = "UPDATE Pedido set StatusPedido = '{$statusiDrunk}' where cod_pedido = '{$reference}'";
    mysqli_query($conn, $queryAttStatus);
    
    $queryPedido   = mysqli_query($conn, "SELECT * FROM Pedido where cod_pedido = '{$reference}'");
    $pedidoCliente = mysqli_fetch_array($queryPedido);
    $dados         = getDados($conn, $pedidoCliente['cod_usuario']);
    
    if ($statusiDrunk == 3) {
        $resultadoPedido = consultarPedido($conn, $reference);
        while ($dadosPedido = mysqli_fetch_assoc($resultadoPedido)) {
            $query = "update Produto set quantidade_produto = quantidade_produto - '{$dadosPedido['qtd_produto']}' where cod_produto='{$dadosPedido['cod_produto']}'";
            mysqli_query($conn, $query);
            echo "Sucesso!";
        }
    }
    
    // Criação de log contendo as informações sobre o pedido
    $today = date("Y_m_d");
    $file  = fopen("LogPagSeguro.$today.txt", "ab");
    $hour  = date("H:i:s T");
    fwrite($file, "Log de Notificações e consulta\\\\r\\\\n");
    fwrite($file, "Hora da consulta: $hour \\\\r\\\\n");
    fwrite($file, "HTTP: " . $http['http_code'] . " \\\\r\\\\n");
    fwrite($file, "Código de Notificação:" . $notificationCode . " \\\\r\\\\n");
    fwrite($file, "Código da transação:" . $xml->code . "\\\\r\\\\n");
    fwrite($file, "Status da transação:" . $xml->status . "\\\\r\\\\n");
    fwrite($file, "____________________________________ \\\\r\\\\n");
    fclose($file);
}
?>