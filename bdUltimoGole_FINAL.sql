drop database bdultimogole;
create database bdultimogole;
use bdultimogole;

create table Pessoas(
	cod_pessoa int(5) not null primary key auto_increment,
	nome_pessoa varchar(100) not null,
	cpf_pessoa varchar (11) not null,
	dtNasc_pessoa date not null
 );
 
 create table Usuarios(
	cod_usuario int(5) not null primary key auto_increment,
	login_usuario varchar (150) not null,
	senha_usuario varchar (150) not null,
	cod_pessoa int(5) not null,
	foreign key (cod_pessoa) references Pessoas (cod_pessoa)
);

 create table Funcionarios(
	cod_func int(5) not null primary key auto_increment,
	login_funcionario varchar (10) not null,
	senha_funcionario varchar (150) not null,
	cod_pessoa int(5) not null,
	privilegiosadm_funcionario varchar(30) not null default "false",
	foreign key (cod_pessoa) references Pessoas (cod_pessoa)
);

 create table Fornecedor(
	cod_fornec int(5) not null primary key,
	nome_fornecedor varchar(100) not null,
	nome_fantasia varchar(100) not null,
	cnpj varchar(14) not null
);

create table EnderecoFornecedor(
	id_endFornecedor int (5) not null primary key,
	cep_endFornecedor varchar(8) not null,
	num_endFornecedor int(9) not null,
	complemento_endFornecedor varchar (30) not null,
	referencia_endFornecedor varchar(50),
	cod_fornec int (5) not null,
	foreign key (cod_fornec) references Fornecedor (cod_fornec)
);


create table EnderecoPessoa(
	id_endereco int (5) not null primary key auto_increment,
	cep varchar(8) not null,
	numcomplm int(9) not null,
	complemento varchar (30) not null,
	referencia varchar(50),
	cod_pessoa int (5) not null,
	foreign key (cod_pessoa) references Pessoas (cod_pessoa)
);

create table ContatoPessoa(
	id_telefone int (5) primary key not null auto_increment,
	ddd_contatoPessoa int (2) not null,
	numero_contatoPessoa int (9) not null,
	tipo_contatoPessoa varchar (15),
	cod_pessoa int (5) not null,
	foreign key (cod_pessoa) references Pessoas (cod_pessoa)
);

create table ContatoFornecedor(
	id_telefoneForn int (5) primary key not null auto_increment,
	ddd_contatoFornecedor int (2) not null,
	numero_contatoFornecedor int (9) not null,
	tipo_contatoFornecedor varchar (15),
	cod_fornec int (5) not null,
	foreign key (cod_fornec) references Fornecedor (cod_fornec)
);

create table Categorias (
	id_categoria int (2) primary key auto_increment not null,
	descricao_categoria varchar (30)
);

create table TipoProduto(
	cod_tipoProd int(2) not null primary key auto_increment,
	desc_tipoProd varchar(30) not null
);

create table Produto(
	cod_produto int (5) not null primary key auto_increment,
	descricao_produto varchar (100) not null,
	preco_produto decimal (7,2) not null,
	categoria_produto int (2) not null,
	tipo_produto int(2) not null,
	quantidade_produto int (3),
	img varchar (130) default "",
	foreign key (categoria_produto) references Categorias (id_categoria),
	foreign key (tipo_produto) references TipoProduto (cod_tipoProd)
);

create table ProdutoFornecedor(
	cod_prodforn int(5) not null primary key,
	cod_produto int (5) not null,
	cod_fornec int(5) not null,
	quantidade_prodForn int(5) not null,
	foreign key (cod_produto) references Produto (cod_produto),
	foreign key (cod_fornec) references Fornecedor (cod_fornec)
);

create table StatusPedido(
	cod_status int(1) primary key not null auto_increment,
	descricao_status varchar(34)
);

create table Pedido(
	cod_pedido int (11) primary key not null auto_increment,
	cod_usuario int (5) not null,
	valortotal_pedido decimal (7,2) null,
	StatusPedido int(2),
	data_pedido date not null,
	hora_pedido time not null,
	observacao varchar (255) null,
	foreign key (cod_usuario) references Usuarios (cod_usuario),
	foreign key (StatusPedido) references StatusPedido(cod_status)
);

create table ItemPedido(
	cod_itempedido int (11) primary key not null auto_increment,
	cod_pedido int (11) not null,
	cod_produto int (10) not null,
	qtd_produto int (3) not null,
	vl_total_produto decimal (7,2) not null,
	foreign key (cod_produto) references Produto (cod_produto),
	foreign key (cod_pedido) references Pedido (cod_pedido)
);

create table Estorno(
	cod_estorno int (10) not null,
	cod_pedido int (11) not null,
	foreign key (cod_pedido) references Pedido (cod_pedido)
);

create table TipoConta (
	id_tipoConta int (2) not null primary key auto_increment,
	descricao_conta varchar (30) not null default ""
);

create table StatusConta (
	cod_statusConta int (1) not null primary key auto_increment,
	descricao_statusconta varchar (20)
);

create table ContasPagar(
	cod_conta int(5) not null primary key,
	favorecido varchar(100) not null,
	valor decimal(7,2) not null,
	vencimento date not null,
	meio_pag varchar(15) not null,	
	tipo_conta int (2) not null,
	cod_statusConta int (1) not null,
	foreign key (tipo_conta) references TipoConta (id_tipoConta),
	foreign key (cod_statusConta) references StatusConta (cod_statusConta)
);

create table ContasReceber(
	cod_conta int(5) not null primary key,
	favorecido varchar(100) not null,
	valor decimal(7,2) not null,
	vencimento date not null,
	meio_pag varchar(15) not null,
	tipo_conta int (2) not null,
	cod_statusConta int (1) not null,
	foreign key (tipo_conta) references TipoConta (id_tipoConta),
	foreign key (cod_statusConta) references StatusConta (cod_statusConta)
);


insert into Categorias values (null, "Cerveja"), (null, "Whisky"), (null, "Sucos e Refrigerantes"), (null, "Vodkas"), (null, "Aperitivos");
insert into TipoProduto values (null, "Não-alcoólico"), (null, "Alcoólico");
insert into TipoConta values (null, "Energia"),(null, "Água"), (null, "Telefonia"), (null, "Fornecedor"), (null, "Aluguel");
insert into StatusPedido values (null, "Pendente"), (null, "Aguardando aprovação do pagamento"), (null, "Aprovado"), (null,"A caminho"), (null, "Entregue"), (null, "Estornado"), (null, "Cancelado");
insert into StatusConta values (null, "Pendente"), (null, "Paga");

insert into Produto values (null, 'Vodka Absolut Natural 1l', 94.99, 4, 2, 0, 'img/Produtos/absolut.png');
insert into Produto values (null, 'Vodka Skyy 980ml', 34.99, 4, 2, 5, 'img/Produtos/sky.png');
insert into Produto values (null, 'Cerveja Stella Artois 550ml', 7.49, 1, 2, 3, 'img/Produtos/stella.png');
insert into Produto values (null, 'Cerveja Budweiser 600ml', 6.99, 1, 2, 0, 'img/Produtos/bud.png');
insert into Produto values (null, 'Whisky Johnnie Walker Blue Label 750ml', 1099.99, 2, 2, 1, 'img/Produtos/blulabel.png');
insert into Produto values (null, 'Vodka Stolichnaya 750ml', 89.99, 4, 2, 2, 'img/Produtos/stolich.png');
insert into Produto values (null, 'Vodka Ciroc 750ml', 169.99, 4, 2, 1, 'img/Produtos/ciroc.png');
insert into Produto values (null, 'Refrigerante Guaraná Antarctica 2l', 5.99, 3, 1, 13, 'img/Produtos/antartica.png');
insert into Produto values (null, 'Cerveja Skol Beats Senses Long Neck 313ml - Caixa c/ 6 un.', 35.99, 1, 2, 1, 'img/Produtos/skol.png');
insert into Produto values (null, 'Refrigerante Sukita 2l', 5.99, 3, 1, 1,  'img/Produtos/sukita.png');
insert into Produto values (null, 'Whisky Johnnie Walker Black Label 1l', 183.99, 2, 2, 1, 'img/Produtos/blabel.png');
insert into Produto values (null, 'Cerveja Corona Extra 355ml', 7.49, 1, 2, 1, 'img/Produtos/corona.png');
insert into Produto values (null, 'Whisky Johnnie Walker Red Label 1l', 109.99, 2, 2, 1, 'img/Produtos/rlabel.png');
insert into Produto values (null, 'Refrigerante Coca Cola 3l', 8.99, 3, 1, 27, 'img/Produtos/coca.png');
insert into Produto values (null, 'Refrigerante Fanta Guaraná 2l', 6.49, 3, 1, 8, 'img/Produtos/fanta.png');
insert into Produto values (null, 'Cerveja Heineken 600ml', 7.49, 1, 2, 9, 'img/Produtos/heineken.png');
insert into Produto values (null, 'Elma Chips Doritos 400g', 19.99, 5, 1, 11, 'img/Produtos/doritos.png');
insert into Produto values (null, 'Elma Chips Ruffles 400g', 19.49, 5, 1, 7, 'img/Produtos/ruffles.png');
insert into Produto values (null, 'M&M Amendoim 200g', 14.49, 5, 1, 14, 'img/Produtos/mm_amend.png');
insert into Produto values (null, 'M&M Chocolate 200g', 13.99, 5, 1, 22, 'img/Produtos/mm_choc.png');