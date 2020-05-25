<?php

define('bd_host', 'localhost');
define('bd_usuario', 'root');
define('bd_senha', '');
define('bd_nome', 'crud_php_puro');

define('bd_dsn', 'mysql:host=localhost;charset=utf8;dbname='.bd_nome);

define('mysqli_con', false);

function conectar()
{
	if(mysqli_con) {
		$conexao = new mysqli(bd_host, bd_usuario, bd_senha, bd_nome);

		if(!$conexao) {
			echo "Error: Unable to connect to MySQL." . PHP_EOL;
		    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
		    exit;
		}

		return $conexao;
	}
	else {
		try {
			$conexao = new PDO(bd_dsn, bd_usuario, bd_senha);

			return $conexao;
		}
		catch(PDOException $e) {
			echo "Erro de conexão: ". $e->getMessage();
		}
		
	}
}

function fechar_conexao($con)
{
	if(mysqli_con) {
		mysqli_close($con);
	}
}

function consulta_bd($query)
{
	if(mysqli_con) {
		$con = conectar();
		$result = $con->query($query);
		$result->fetch_array(MYSQLI_ASSOC);
		fechar_conexao($con);

		return $result;
	}
	else {
		$con = conectar();
		$busca = $con->prepare($query);
		$busca->execute();
		$result = $busca->fetchAll();

		return $result;

	}
}

function registra_bd($query, $dados = null)
{
	if(mysqli_con) {
		$con = conectar();
		$result = $con->query($query);
		$id = mysqli_insert_id($con);
		fechar_conexao($con);

		return $id;
	}
	else {
		$con = conectar();
		$busca = $con->prepare($query);
		$j = 0;
		if (!is_null($dados)) {
			for ($i=1; $i <= sizeof($dados); $i++) { 
				$busca->bindParam($i, $dados[$j]);
				$j++;
			}
		}

		return  $busca->execute();
	}
}

function atualiza_bd($query, $dados = null)
{
	if(mysqli_con) {
		$con = conectar();
		$result = $con->query($query);
		fechar_conexao($con);

		return $result;
	}
	else {
		$con = conectar();
		$busca = $con->prepare($query);
		$j = 0;
		if (!is_null($dados)) {
			for ($i=1; $i <= sizeof($dados); $i++) { 
				$busca->bindParam($i, $dados[$j]);
				$j++;
			}
		}

		return  $busca->execute();
	}
}

function consulta_registro_bd($query)
{
	if(mysqli_con) {
		$con = conectar();
		$result = $con->query($query);
		$result->fetch_row();
		fechar_conexao($con);

		return $result;
	}
	else {
		$con = conectar();
		$busca = $con->prepare($query);
		$busca->execute();
		$result = $busca->fetchAll();

		return $result;
	}
}


/* Cria Banco de dados

CREATE SCHEMA `crud_php_puro` DEFAULT CHARACTER SET utf8 ;
CREATE TABLE `crud_php_puro`.`cliente` (
  `id_cliente` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(200) NULL,
  `cpf` VARCHAR(25) NULL,
  `email` VARCHAR(150) NULL,
  PRIMARY KEY (`id_cliente`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT INTO `crud_php_puro`.`cliente` (`nome`, `cpf`, `email`) VALUES ('teste', '123456789', 'mail@mail.com');
INSERT INTO `crud_php_puro`.`cliente` (`nome`, `cpf`, `email`) VALUES ('teste 2', '123456789', 'mail2@mail.com');
INSERT INTO `crud_php_puro`.`cliente` (`nome`, `cpf`, `email`) VALUES ('teste 3', '123456789', 'mail3@mail.com');
INSERT INTO `crud_php_puro`.`cliente` (`nome`, `cpf`, `email`) VALUES ('teste 4', '123456789', 'mail4@mail.com');

*/