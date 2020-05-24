<?php
	require_once('./conexao.php');

	$operacao = isset($_GET["op"]) ? $_GET["op"] : "";
	$id = isset($_GET["id"]) ? $_GET["id"] : "";

	if(
		!isset($id) || !isset($operacao) || !is_numeric($id) 
		|| empty($id) || empty($operacao)
	) {
		echo 
		"
			<h1>Erro</h1>
			<p>Registro não encontrado, clique <a href='./index.php'>aqui para voltar</a></p>
		";
		exit();
	}

	//Identifica metodo POST
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$form_tipo_op = $_POST["tipo_op"];
		$form_id = $_POST["id"];
		$form_nome = $_POST["nome"];
		$form_cpf = $_POST["cpf"];
		$form_email = $_POST["email"];

		$update_sql = "";

		if($form_tipo_op == "editar") {
			$update_sql = "UPDATE cliente SET nome='$form_nome', cpf='$form_cpf', email='$form_email' WHERE id_cliente='$form_id';";

			$update = atualiza_bd($update_sql);
		}

		header('Location: ./index.php');
		exit();

	}

	//carrega os dados
	$registro_sql = "SELECT * FROM cliente WHERE id_cliente = '$id'; ";

	$campo_nome = "";
	$campo_cpf = "";
	$campo_email = "";

	$dados = consulta_registro_bd($registro_sql);

	if($dados->num_rows === 0 && $operacao == "editar") {
		echo 
		"
			<h1>Erro</h1>
			<p>Registro não encontrado, clique <a href='./index.php'>aqui para voltar</a></p>
		";
		exit();
	}

	foreach ($dados as $registro) {
		$campo_nome = $registro["nome"] ? $registro["nome"] : "";
		$campo_cpf = $registro["cpf"] ? $registro["cpf"] : "";
		$campo_email = $registro["email"] ? $registro["email"] : "";
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
        <h2>CRUD Clientes - Editar</h2>
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
	            <label for="cpf">CPF</label>
	            <input type="text" class="form-control" name="cpf" id="cpf" aria-describedby="cpf do cliente" placeholder="CPF do Cliente" required="required" value="<?= $campo_cpf ?>">
	        </div>
	        <div class="form-group">
	            <label for="email">E-mail</label>
	            <input type="text" class="form-control" name="email" id="email" aria-describedby="E-mail" placeholder="E-mail" required="required" value="<?= $campo_email ?>">
	        </div>
	        <div class="form-group">
	            <input type="submit" class="btn btn-primary text-capitalize" id="editar" value="<?= $operacao ?>" />
	        </div>
	    </form>
	</div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>