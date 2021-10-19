<?php
	//Inicia a session
	session_start();
	include("conexao.php");
	include("dbFunctions.php");

	$obs = $_POST['txtObs'];
	
	if(checarLogin($conn)){
		$dados = getDados($conn, $_SESSION['user_id']);
		
	} else {
		echo "<script>window.location.href = '../index.php'</script>";
	}
	$resultadoPedido = getPedido($conn, $_SESSION['user_id'], 1);
	$cep = $_GET['end'];
	$endereco = getEndereco($cep);
	$vlEntrega = 7.25;
?>
<div id="PagSeguro">
	<table width="1024" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="32" height="40" align="center" valign="middle"></td>
			<td colspan="4" height="40" align="center" valign="middle" class="clsTituloLista">iDrunk - PagSeguro</td>
			<td width="32" height="40" align="center" valign="middle"></td>
		</tr>
		<tr>
			<td width="32" height="40" align="center" valign="middle"></td>
			<td colspan="4" height="40" align="center" valign="middle" class="clsMsgPagSeuro">
			<?php
			try
			{
				include("conexao.php");
				if(checarLogin($conn))
				{
					if($_SESSION["user_id"] != "")
					{
						if ($dados)
						{
							$nomecliente = $dados["nome_pessoa"];
							$emailcliente = $dados["login_usuario"];
							$areatelefonecliente = $dados["ddd_contatoPessoa"];
							$telefonecliente = str_replace("-","",substr($dados["numero_contatoPessoa"],5));
						}
						else
						{	
							$nomecliente = "";
							$emailcliente = "";
							$areatelefonecliente = "";
							$telefonecliente = "";
						}			
						$dadosPedido = mysqli_fetch_assoc($resultadoPedido);
						//Dados do pedido
						$pedid = $dadosPedido['cod_pedido'];
						if($dadosPedido['valortotal_pedido'] >= 30 && $dadosPedido['valortotal_pedido'] < 50){
							$vlEntrega = 3.59;
						}
						if($dadosPedido['valortotal_pedido'] >= 50){
							$vlEntrega = 0.01;
						}
						
						/*Dados ambiente real
						$email = 'tccidrank@gmail.com';
						$token = 'C143F8596A58476CB82224B4E6DB6DF2';
						$url = 'https://ws.pagseguro.uol.com.br/v2/checkout/?email=' . $email . '&token=' . $token;*/
						
						// Dados ambiente sandbox
						
						$email = 'gian.jesus@etec.sp.gov.br';
						$token = 'AEBF129630CA46B08A48A68F7A449DA2';
						$url = 'https://ws.sandbox.pagseguro.uol.com.br/v2/checkout/?email=' . $email . '&token=' . $token;
						$xml = '<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>
						<checkout>
							<currency>BRL</currency>
							<items>';
						while($dadosPedido)
						{
							$id      = $dadosPedido['cod_produto'];
							$produto = $dadosPedido["descricao_produto"];
							$qtd     = $dadosPedido["qtd_produto"];
							$preco   = str_replace(",",".",$dadosPedido['preco_produto']);
							$xml .='	<item>
										<id>' . $id . '</id>
										<description>' . str_replace('&', '&amp;', $produto) . '</description>
										<amount>' . $preco . '</amount>
										<quantity>' . $qtd . '</quantity>
									</item>';
							$dadosPedido = mysqli_fetch_assoc($resultadoPedido);
						}
						$xml .='
						<item>
							<id>999</id>
							<description>Taxa de Entrega</description>
							<quantity>1</quantity>
							<amount>'.$vlEntrega.'</amount>
						</item>
						</items>
								<reference>' . "Ped" . $pedid . '</reference>
								<shipping>
									<type>3</type>';
						$xml.=' <address>
        								<street>'.$endereco->logradouro.'</street>
										<number>'.$_GET['num'].'</number>
										<district>'.$endereco->bairro.'</district>
										<postalCode>'.$cep.'</postalCode>
										<city>Hortolândia</city>
										<state>SP</state>
										<country>BRA</country>
									</address>  
								</shipping>
							</checkout>';
						$curl = curl_init($url);
						curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($curl, CURLOPT_HTTPHEADER, Array('Content-Type: application/xml; charset=ISO-8859-1'));
						curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
						$xml= curl_exec($curl);

						if($xml == 'Unauthorized'){
							//Insira seu código avisando que o sistema está com problemas, sugiro enviar um e-mail avisando para alguém fazer a manutenção 
							echo 'Mensagem: Seu email e/ou token est&aacute;(&atilde;o) com problema(s).';

							//    header('Location: paginaDeErro.php');
							exit();//Mantenha essa linha
						}

						curl_close($curl);

						$xml= simplexml_load_string($xml);

						if(count($xml -> error) > 0){
							//Insira seu código avisando que o sistema está com problemas, sugiro enviar um e-mail avisando para alguém fazer a manutenção, talvez seja útil enviar os códigos de erros.
							echo 'Mensagem: O c&oacute;digo xml enviado est&aacute; com problemas, por avisar o administrador do site.<br><br>';
							print_r($xml);
							exit();
						}
						$nome = $dados['nome_pessoa'];
						$email = $_SESSION["user_email"];
						$msg = 'Sr(a),' . $nome . ', recebemos em nosso sistema o pedido no. ' . $pedid . ', ';
						$msg.= 'em breve estaremos enviando os seus produtos. <br><br> Obrigado pela preferencia.<br><br><br><br> Equipe iDrunk';

						$origem = "administrativo@idrunk.ga";
						$assunto = "Contato iDrunk - Pedido no. - " . $pedid;

						$headers  = 'MIME-Version: 1.0'."\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
						$headers .= 'From: '.$origem."\r\n";

						$message = "<style type='text/css'>
						.tabela {
							border-bottom: solid 0px;
							border-left: solid 0px;
						}
						.tabela td {
							border-top: solid 0px;
							border-right: solid 0px;
						}
						.tabela, .tabela td {
							font: 10pt Myriad Pro;
							border-color: #555555;
						}
						</style>
						<table width='800' cellpadding='0' cellspacing='0' border='0'>
						<tr><td colspan='2' align='left' valign='middle'><b>Nome:</b></td><td>$nome</td></tr>
						<tr><td colspan='2' align='left' valign='middle'><b>E-mail:</b></td><td>$email</td></tr>
						<tr><td colspan='2' align='left' valign='middle' valign='top'><b>Mensagem:</b></td><td>$msg</td></tr>
						</table>";

						mail($email, $assunto, $message, $headers);

						/* Ambiente real
						$strURL = 'https://pagseguro.uol.com.br/v2/checkout/payment.html?code=' . $xml->code;
						echo "<script>window.location.href = '" . $strURL . "';</script>";*/
						$strURL = 'https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html?code=' . $xml->code;
						echo "<script>window.location.href = '" . $strURL . "';</script>";
						mysqli_close($conn);
					}
					else
					{
						echo "<script>window.location.href = '../index.php'</script>";
					}
				}
				else
				{
					echo "<script>window.location.href = '../index.php'</script>";
				}
			}
			catch (InvalidArgumentException $e) {
				echo 'Mensagem: ' . $e->getMessage();
				exit();
			}
			catch (RangeException $e) {
				echo 'Mensagem: ' . $e->getMessage();
				exit();
			}
			catch (Exception $e) {
				echo 'Mensagem: ' . $e->getMessage();
				exit();
			}
			?>
			<td width="32" height="40" align="center" valign="middle"></td>
		</tr>
		<tr>
			<td width="32" height="40" align="center" valign="middle"></td>
			<td colspan="4" height="40" align="center" valign="middle">&nbsp;</td>
			<td width="32" height="40" align="center" valign="middle"></td>
		</tr>
		<tr>
			<td width="32" height="40" align="center" valign="middle"></td>
			<td colspan="4" height="40" align="center" valign="middle">
				<div>
					<a href="../catalogo.php?categ=0&tipo=0&filtro=0">
						voltar
					</a>
				</div>
			</td>
			<td width="32" height="40" align="center" valign="middle"></td>
		</tr>
	</table>
</div>