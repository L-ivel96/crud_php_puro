<?php

date_default_timezone_set('America/Sao_Paulo');

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

		$conexao->set_charset("utf8");

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
				var_dump($dados[$j]);
				$j++;
			}
			var_dump($dados);
		}

		return  $busca->execute() or die(print_r($busca->errorInfo(), true));
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

		return $busca->execute() or die(print_r($busca->errorInfo(), true));
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

$formatacao_cpf = array('.','-', '/');

function formatCnpjCpf($value)
{
	$cnpj_cpf = preg_replace("/\D/", '', $value);

	if (strlen($cnpj_cpf) === 11) {
		return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
	} 

	return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
}


/* Cria Banco de dados

CREATE SCHEMA `crud_php_puro` DEFAULT CHARACTER SET utf8 ;
CREATE TABLE `crud_php_puro`.`cliente` (
  `id_cliente` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(200) NULL,
  `cpf_cnpj` VARCHAR(20) NULL,
  `nascimento` DATE NULL,
  `endereco` VARCHAR(250) NULL,
  `desc_titulo` TEXT NULL,
  `valor` DECIMAL(10,4) NULL,
  `vencimento` DATE NULL,
  `criado_em` DATETIME NULL,
  `atualizado_em` DATETIME NULL,
  PRIMARY KEY (`id_cliente`)
);

INSERT INTO `crud_php_puro`.`cliente` (`nome`, `cpf_cnpj`, `nascimento`, `endereco`, `desc_titulo`, `valor`, `vencimento`, `criado_em`, `atualizado_em`) VALUES ('Teste 1', '12345678909', '1996-01-31', 'rua teste, São Paulo, SP', 'Esta devendo pagamento de 2 parcelas da pós graduação', '1200', '2020-06-10', '2020-05-25 10:15:00', '2020-05-25 10:15:00');

INSERT INTO `crud_php_puro`.`cliente` (`nome`, `cpf_cnpj`, `nascimento`, `endereco`, `desc_titulo`, `valor`, `vencimento`, `criado_em`, `atualizado_em`) VALUES ('Teste 2', '12345678909', '1996-01-31', 'rua teste, São Paulo, SP', 'Esta devendo pagamento de do cartão de crédito', '2300.49', '2020-06-10', '2020-05-25 10:15:00', '2020-05-25 10:15:00');




*/