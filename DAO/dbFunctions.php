<?php
include("conexao.php");

// Criar sessão
function startSession($conn, $email, $senha)
{
    $query  = "select * from Usuarios where login_usuario = '{$email}' and senha_usuario = '{$senha}'";
    $sql    = mysqli_query($conn, $query);
    $result = mysqli_fetch_array($sql);
    if (mysqli_num_rows($sql) <= 0) {
        die();
    } else {
        session_start();
        $_SESSION['user_email'] = $email;
        $_SESSION['user_pass']  = $senha;
        $_SESSION['user_id']    = $result['cod_pessoa'];
    }
}

// Puxar data e hora atual
function getHora()
{
    date_default_timezone_set("America/Sao_Paulo");
    $time = date("H:i");
    return date("G:i", strtotime($time));
}

function getData()
{
    date_default_timezone_set("America/Sao_Paulo");
    return date("Y-m-d");
}

// Puxar endereço a partir do CEP
function getEndereco($cep)
{
    $cep = preg_replace('/[^0-9]/', '', $cep);
    $url = "http://viacep.com.br/ws/$cep/xml/";
    $xml = simplexml_load_file($url);
    return $xml;
}

// Inserir usuários no banco de dados
function addUsuario($conn, $nome, $email, $senha, $cpf, $dtNasc, $ddd, $tel, $tipoTel, $cep, $complm, $ref, $numCasa)
{
    $query = "insert into Pessoas (cod_pessoa, nome_pessoa, cpf_pessoa, dtNasc_pessoa) values (null ,'{$nome}', '{$cpf}', '{$dtNasc}');";
    $sql   = "select * from Pessoas where cpf_pessoa='{$cpf}';";
    
    // Caso inserir Pessoa, progride para a inserção dos demais dados
    if (mysqli_query($conn, $query)) {
        $result = mysqli_query($conn, $sql);
        if ($result != null) {
            $row   = mysqli_fetch_assoc($result);
            $id    = $row['cod_pessoa'];
            $query = "insert into Usuarios (cod_usuario, login_usuario, senha_usuario, cod_pessoa) values (null, '{$email}', '{$senha}', '{$id}');";
            $query .= "insert into ContatoPessoa (id_telefone, ddd_contatoPessoa, numero_contatoPessoa, tipo_contatoPessoa, cod_pessoa) values (null, '{$ddd}', '{$tel}', '{$tipoTel}', '{$id}');";
            $query .= "insert into EnderecoPessoa (id_endereco, cep, numcomplm, complemento, referencia, cod_pessoa) values (null, {$cep}, {$numCasa}, '{$complm}', '{$ref}', {$id})";
            return mysqli_multi_query($conn, $query);
        }
    }
}

// Verificar se o email e a senha coincidem com o banco
function testarLogin($conn, $email, $senha)
{
    $cont      = 0;
    $query     = "SELECT * FROM Usuarios where login_usuario = '{$email}' and senha_usuario = '{$senha}'";
    $resultado = mysqli_query($conn, $query);
    
    while (mysqli_fetch_assoc($resultado)) {
        $cont += 1;
    }
    if ($cont != 0) {
        return true;
    } else {
        return false;
    }
}

// Verificar se o usuário já foi cadastrado anteriormente
function checarCadastro($conn, $email, $cpf)
{
    $cont       = 0;
    $query      = "SELECT * FROM Usuarios where login_usuario = '{$email}'";
    $query2     = "SELECT * FROM Pessoas where cpf_pessoa = '{$cpf}'";
    $resultado  = mysqli_query($conn, $query);
    $resultado2 = mysqli_query($conn, $query2);
    
    while (mysqli_fetch_assoc($resultado)) {
        $cont++;
    }
    while (mysqli_fetch_assoc($resultado2)) {
        $cont++;
    }
    
    if ($cont != 0) {
        return false;
    } else {
        return true;
    }
}

// Verificar se o usuário está logado
function checarLogin($conn)
{
    if (isset($_SESSION['user_email']) && isset($_SESSION['user_pass']) && isset($_SESSION['user_id'])) {
        return true;
    } else {
        return false;
    }
}

// Verificar se há algo na tabela Pedidos do usuário
function checarCarrinho($conn, $cod_usuario)
{
    $query = "SELECT * from Pedido where cod_usuario = '{$cod_usuario}' and StatusPedido = 1";
    if ($result = mysqli_query($conn, $query)) {
        $nresult = mysqli_num_rows($result);
        if ($nresult > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function addPedido($conn, $cod_usuario, $cod_produto)
{
    $date        = getData();
    $time        = getHora();
    $valorPedido = 0;
    $query       = "SELECT * from Pedido where cod_usuario = '{$cod_usuario}' and StatusPedido = 1";
    echo $query . "<br><br>";
    if ($resultado = mysqli_query($conn, $query)) {
        $nresults = mysqli_num_rows($resultado);
        if ($nresults > 0) {
            while ($pedido = mysqli_fetch_assoc($resultado)) {
                $queryTeste = "SELECT * from ItemPedido where cod_pedido = '{$pedido['cod_pedido']}' and cod_produto = '{$cod_produto}'";
                $result     = mysqli_query($conn, $queryTeste);
                $nresult    = mysqli_num_rows($result);
                if ($nresult > 0) {
                    return false;
                } else {
                    $queryProduto = "SELECT * from Produto where cod_produto = '{$cod_produto}'";
                    if ($resultadoProduto = mysqli_query($conn, $queryProduto)) {
                        while ($produto = mysqli_fetch_assoc($resultadoProduto)) {
                            $query = "INSERT ItemPedido (cod_itempedido, cod_pedido, cod_produto, qtd_produto, vl_total_produto) values (null, '{$pedido['cod_pedido']}', '{$cod_produto}', '1', '{$produto['preco_produto']}')";
                            if (mysqli_query($conn, $query)) {
                                $valorPedido = $pedido['valortotal_pedido'];
                                $valorPedido += $produto['preco_produto'];
                                $query = "UPDATE Pedido set valortotal_pedido = '{$valorPedido}' where cod_usuario = {$cod_usuario} and StatusPedido = 1";
                                if (mysqli_query($conn, $query)) {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $query = "insert into Pedido (cod_pedido, cod_usuario, valortotal_pedido, StatusPedido, data_pedido, hora_pedido) values (null ,'{$cod_usuario}', '{$valorPedido}', 1, '{$date}', '{$time}')";
            if (mysqli_query($conn, $query)) {
                $query = "SELECT * from Pedido where cod_usuario = '{$cod_usuario}' and StatusPedido = 1";
                if ($resultado = mysqli_query($conn, $query)) {
                    while ($pedido = mysqli_fetch_assoc($resultado)) {
                        $query = "SELECT * from Produto where cod_produto = '{$cod_produto}'";
                        if ($resultado = mysqli_query($conn, $query)) {
                            while ($produto = mysqli_fetch_assoc($resultado)) {
                                $query = "insert into ItemPedido (cod_itempedido, cod_pedido, cod_produto, qtd_produto, vl_total_produto) values (null, '{$pedido['cod_pedido']}', '{$cod_produto}', '1', '{$produto['preco_produto']}')";
                                if (mysqli_query($conn, $query)) {
                                    $valorPedido += $produto['preco_produto'];
                                    $query = "update Pedido set valortotal_pedido = '{$valorPedido}' where cod_usuario = {$cod_usuario} and StatusPedido = 1";
                                    if (mysqli_query($conn, $query)) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                } else {
                    echo "Erro!";
                }
            } else {
                echo "Erro!";
            }
        }
    }
}

// Cancelar pedido
function cancelarPedido($conn, $cod_pedido)
{
    $query  = "select * from Pedido where cod_pedido = '{$cod_pedido}' and StatusPedido = 1 or StatusPedido = 2";
    $result = mysqli_query($conn, $query);
    $nrow   = mysqli_num_rows($result);
    if ($nrow > 0) {
        $query = "update Pedido set StatusPedido = 7 where cod_pedido = '{$cod_pedido}'";
        if (mysqli_query($conn, $query)) {
            echo "<script>window.location.href='pedidos.php?statusCanc=1'</script>";
        } else {
            echo "<script>window.location.href='pedidos.php?statusCanc=3'</script>";
        }
    } else {
        echo "<script>window.location.href='pedidos.php?statusCanc=2'</script>";
    }
}

// Atualizar quantidade
function attQtd($conn, $cod_usuario, $cod_produto, $cod_pedido, $qtd, $vlTotalItem)
{
    $cont       = 0;
    $vlPedido   = 0;
    $totalItens = 0;
    $query      = "select * from Pedido where cod_usuario = '{$cod_usuario}' and StatusPedido = '1'";
    if ($result = mysqli_query($conn, $query)) {
        $nresults = mysqli_num_rows($result);
        if ($nresults > 0) {
            $query = "update ItemPedido set qtd_produto = '{$qtd}', vl_total_produto = '{$vlTotalItem}' where cod_produto = '{$cod_produto}' and cod_pedido = '{$cod_pedido}';";
            if ($result = mysqli_query($conn, $query)) {
                $query = "select * from ItemPedido where cod_pedido = '{$cod_pedido}'";
                if ($result = mysqli_query($conn, $query)) {
                    while ($itens = mysqli_fetch_assoc($result)) {
                        $vlPedido += $itens['vl_total_produto'];
                        $totalItens += $itens['qtd_produto'];
                    }
                    $cont = $totalItens;
                    if ($cont == $totalItens) {
                        $query = "update Pedido set valortotal_pedido = '{$vlPedido}' where cod_usuario = '{$cod_usuario}' and StatusPedido = '1'";
                        return mysqli_multi_query($conn, $query);
                    }
                }
            }
            
        } else {
            die("Erro, nenhum pedido encontrado!");
        }
    } else {
        die("Erro no banco de dados");
    }
}

// Função usada para inverter a data
function inverteData($data)
{
    if (count(explode("/", $data)) > 1) {
        return implode("-", array_reverse(explode("/", $data)));
    } elseif (count(explode("-", $data)) > 1) {
        return implode("/", array_reverse(explode("-", $data)));
    }
}


// Puxar somente o primeiro nome do usuário
function primeiroNome($char, $nome)
{
    return substr($nome, 0, strpos($nome, $char));
}

// Puxar dados do usuário
function getDados($conn, $cod_usuario)
{
    $sqlEndereco    = "select * from Pessoas p inner join Usuarios u ON p.cod_pessoa = u.cod_pessoa inner join EnderecoPessoa e ON p.cod_pessoa=e.cod_pessoa inner join ContatoPessoa c ON c.cod_pessoa =  p.cod_pessoa where p.cod_pessoa = {$cod_usuario}";
    $resultEndereco = mysqli_query($conn, $sqlEndereco);
    $dados          = mysqli_fetch_array($resultEndereco);
    return $dados;
}

function getPedido($conn, $cod_usuario, $status_pedido)
{
    
    if ($status_pedido == 0) {
        $queryItens     = "SELECT DISTINCT *
FROM Usuarios u
INNER JOIN Pedido p
    on u.cod_usuario = p.cod_usuario
INNER JOIN ItemPedido i
    on p.cod_pedido = i.cod_pedido
INNER JOIN Produto pr
    on i.cod_produto = pr.cod_produto
WHERE p.cod_usuario = {$cod_usuario}";
        $resultadoItens = mysqli_query($conn, $queryItens);
        return $resultadoItens;
        
    } else if ($status_pedido == 1) {
        $queryItens     = "SELECT *
FROM Usuarios u
INNER JOIN Pedido p
    on u.cod_usuario = p.cod_usuario
INNER JOIN ItemPedido i
    on p.cod_pedido = i.cod_pedido
INNER JOIN Produto pr
    on i.cod_produto = pr.cod_produto
WHERE p.cod_usuario = {$cod_usuario} and StatusPedido = 1";
        $resultadoItens = mysqli_query($conn, $queryItens);
        return $resultadoItens;
    }
}

function consultarPedido($conn, $cod_pedido)
{
    $query  = "SELECT *
FROM Usuarios u
INNER JOIN Pedido p
    on u.cod_usuario = p.cod_usuario
INNER JOIN ItemPedido i
    on p.cod_pedido = i.cod_pedido
INNER JOIN Produto pr
    on i.cod_produto = pr.cod_produto
WHERE p.cod_pedido = {$cod_pedido}";
    $result = mysqli_query($conn, $query);
    return $result;
}
?>