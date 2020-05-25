<?php
	require_once('./conexao.php');

	$operacao = isset($_GET["op"]) ? $_GET["op"] : "";
	$id = isset($_GET["id"]) ? $_GET["id"] : "";

	if (!isset($operacao) || ($operacao == "editar" && !is_numeric($id)) ) {
		echo 
		"
			<h1>Erro - $operacao</h1>
			<p>Registro não encontrado, clique <a href='./index.php'>aqui para voltar</a></p>
		";
		exit();
	}

	//Identifica metodo POST
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$con = conectar();

		$form_tipo_op = $_POST["tipo_op"];
		$form_id = $_POST["id"];
		$formatacao_cpf = array('.','-', '/');
		$dt_atual = new DateTime();

		if (mysqli_con){
			$form_nome = $con->real_escape_string($_POST["nome"]);
			$form_cpf = $con->real_escape_string(str_replace($formatacao_cpf,'', $_POST["cpf"]));
			$form_nascimento = $con->real_escape_string($_POST["nascimento"]);
			$form_endereco = $con->real_escape_string($_POST["endereco"]);
			$form_descricao = $con->real_escape_string($_POST["descricao"]);
			$form_valor = $con->real_escape_string($_POST["valor"]);
			$form_vencimento = $con->real_escape_string($_POST["vencimento"]);

			$update_sql = "";

			if($form_tipo_op == "editar") {
				$update_sql = "
					UPDATE cliente 
					SET nome = '$form_nome' , cpf_cnpj = '$form_cpf' ,
					nascimento = '$form_nascimento' , endereco = '$form_endereco' ,
					desc_titulo = '$form_descricao' , valor = '$form_valor' ,
					vencimento = '$form_vencimento', atualizado_em = '{$dt_atual->format('Y-m-d h:i')}'
					WHERE id_cliente = $form_id;
				";

				$update = atualiza_bd($update_sql);
			}

			if($form_tipo_op == "cadastrar") {
				$update_sql = "INSERT INTO `crud_php_puro`.`cliente` (`nome`, `cpf_cnpj`, `nascimento`, `endereco`, `desc_titulo`, `valor`, `vencimento`, `criado_em`, `atualizado_em`) VALUES ('$form_nome', '$form_cpf', '$form_nascimento', '$form_endereco', '$form_descricao', '$form_valor', '$form_vencimento', '{$dt_atual->format('Y-m-d h:i')}', '{$dt_atual->format('Y-m-d h:i')}');";

				$insert = registra_bd($update_sql);
			}
		}
		else
		{
			$form_nome = $_POST["nome"];
			$form_cpf = str_replace($formatacao_cpf,'', $_POST["cpf"]);
			$form_nascimento = $_POST["nascimento"];
			$form_endereco = $_POST["endereco"];
			$form_descricao = $_POST["descricao"];
			$form_valor = $_POST["valor"];
			$form_vencimento = $_POST["vencimento"];

			$update_sql = "";

			if($form_tipo_op == "editar") {
				$update_sql = "
					UPDATE cliente 
					SET nome = ? , cpf_cnpj = ? , nascimento = ? , endereco = ? ,
					desc_titulo = ? , valor = ? , vencimento = ?, atualizado_em = ?
					WHERE id_cliente = ? ;
				";
				$dados_up = array(
					$form_nome,
					$form_cpf,
					$form_nascimento,
					$form_endereco,
					$form_descricao,
					$form_valor,
					$form_vencimento,
					$atualizado_em->format('Y-m-d h:i'),
					$form_id
				);

				$update = atualiza_bd($update_sql, $dados_up);
			}

			if($form_tipo_op == "cadastrar") {
				$update_sql = "INSERT INTO `crud_php_puro`.`cliente` (`nome`, `cpf_cnpj`, `nascimento`, `endereco`, `desc_titulo`, `valor`, `vencimento`, `criado_em`, `atualizado_em`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";

				$dados_in = array(
					$form_nome,
					$form_cpf,
					$form_nascimento,
					$form_endereco,
					$form_descricao,
					$form_valor,
					$form_vencimento,
					$dt_atual->format('Y-m-d h:i'),
					$dt_atual->format('Y-m-d h:i')
				);

				$insert = registra_bd($update_sql, $dados_in);
			}
		}
		

		header('Location: ./index.php');
		exit();

	}

	//carrega os dados
	$registro_sql = "SELECT * FROM cliente WHERE id_cliente = '$id'; ";

	$campo_nome = "";
	$campo_cpf = "";
	$campo_nascimento = "";
	$campo_endereco = "";
	$campo_descricao = "";
	$campo_valor = "";
	$campo_vencimento = "";

	$dados = consulta_registro_bd($registro_sql);

	$num_row = mysqli_con ? $dados->num_rows : sizeof($dados);

	if($num_row === 0 && $operacao == "editar") {
		echo 
		"
			<h1>Erro</h1>
			<p>Registro não encontrado, clique <a href='./index.php'>aqui para voltar</a></p>
		";
		exit();
	}

	foreach ($dados as $registro) {
		$campo_nome = $registro["nome"] ? $registro["nome"] : "";
		$campo_cpf = $registro["cpf_cnpj"] ? formatCnpjCpf($registro["cpf_cnpj"]) : "";
		$campo_nascimento = $registro["nascimento"] ? $registro["nascimento"] : "";
		$campo_endereco = $registro["endereco"] ? $registro["endereco"] : "";
		$campo_descricao = $registro["desc_titulo"] ? $registro["desc_titulo"] : "";
		$campo_valor = $registro["valor"] ? number_format($registro["valor"],2,",","") : "";
		$campo_vencimento = $registro["vencimento"] ? $registro["vencimento"] : "";
	}
	
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Clientes</title>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light mb-2 d-flex" style="background-color: #e9ecef;">
        <h2>CRUD Clientes - <?= $operacao ?></h2>
    </nav>
    <div class="container">

	    <form id="form_cadastro" method="POST">
	        <input type="hidden" name="id" value="<?= $id ?>">
	        <input type="hidden" name="tipo_op" value="<?= $operacao ?>">
	        <div class="form-group">
	            <label for="nome">Nome</label>
	            <input type="text" class="form-control" name="nome" id="nome" aria-describedby="Nome do cliente" placeholder="Nome do Cliente" required="required" value="<?= $campo_nome ?>">
	        </div>
	        <div class="form-group">
	            <label for="cpf">CPF/CNPJ</label>
	            <input type="text" class="form-control" name="cpf" id="cpf" aria-describedby="cpf do cliente" placeholder="CPF do Cliente" required="required" value="<?= $campo_cpf ?>">
	        </div>
	        <div class="form-group">
	            <label for="nascimento">Data de nascimento</label>
	            <input type="date" class="form-control" name="nascimento" id="nascimento" aria-describedby="data de nascimento do cliente" required="required" value="<?= $campo_nascimento ?>">
	        </div>
	        <div class="form-group">
	            <label for="endereco">Endereço</label>
	            <input type="text" class="form-control" name="endereco" id="endereco" aria-describedby="Endereço" placeholder="Endereço" required="required" value="<?= $campo_endereco ?>">
	        </div>
	        <div class="form-group">
	            <label for="descricao">Descrição</label>
	            <input type="text" class="form-control" name="descricao" id="descricao" aria-describedby="Descrição" placeholder="Descrição" required="required" value="<?= $campo_descricao ?>">
	        </div>
	        <div class="form-group">
	            <label for="valor">Valor (R$)</label>
	            <input type="number" step="any" class="form-control" name="valor" id="valor" aria-describedby="valor da dívida" placeholder="Valor da dívida" required="required" value="<?= $campo_valor ?>">
	        </div>
	        <div class="form-group">
	            <label for="vencimento">Vencimento</label>
	            <input type="date" class="form-control" name="vencimento" id="vencimento" aria-describedby="vencimento" required="required" value="<?= $campo_vencimento ?>">
	        </div>
	        <div class="form-group">
	            <input type="submit" class="btn btn-primary text-capitalize" id="editar" value="<?= $operacao ?>" />
	            <a href="./" class="btn btn-primary text-capitalize ml-4">Voltar</a>
	        </div>
	    </form>
	</div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>