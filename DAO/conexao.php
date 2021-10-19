<?php
	$servername = "localhost:3306";
	$username = "root";
	$password = "";
	$database = "bdUltimoGole";
	
	// Criando conexão
	$conn = new mysqli($servername, $username, $password, $database);
	
	// Verificando conexão
	if ($conn->connect_error){
		die("Connection failed: ".$conn->connect_error);
	}
?>