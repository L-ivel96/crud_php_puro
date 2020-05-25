<?php
	require_once('./conexao.php');

	$operacao = isset($_GET["op"]) ? $_GET["op"] : "";
	$id = isset($_GET["id"]) ? $_GET["id"] : "";

	if ($operacao == "excluir" && !empty($id) && is_numeric($id)) {
		$deletar_sql = "DELETE FROM cliente WHERE id_cliente='$id'; ";
		$deletar = atualiza_bd($deletar_sql);
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
        <h2>CRUD Clientes</h2>
    </nav>
    <div class="container">

	    <table id="tabela_clientes" class="table table-striped table-bordered">
	        <thead class="thead-dark">
	            <tr>
	                <th>Nome</th>
	                <th>CPF</th>
	                <th>E-mail</th>
	                <th>Editar</th>
	                <th>Deletar</th>
	            </tr>
	        </thead>
	        <tbody>
	        	<?php 
	        		$consulta = "SELECT * FROM cliente";
	        		$clientes = consulta_bd($consulta);
	        	?>
	        	<?php foreach ($clientes as $cliente): ?>
	        	<tr>
	        		<td><?= $cliente["nome"]; ?></td>
	        		<td><?= $cliente["cpf"]; ?></td>
	        		<td><?= $cliente["email"]; ?></td>
	        		<td>
	        			<a class="btn btn-warning" href="./pagina_editar?op=editar&id=<?= $cliente['id_cliente'] ?>">Editar</a>
	        		</td>
	        		<td>
	        			<a class="btn btn-danger" href="./index.php?op=excluir&id=<?= $cliente['id_cliente'] ?>">Excluir</a>
	        		</td>
	        	</tr>	        			
	        	<?php endforeach?>
	        </tbody>
	        <tfoot>
	            <tr>
	                <td colspan="4"><a href="./pagina_editar?op=cadastrar" class="btn btn-info">Adicionar Cliente</a></td>
	            </tr>
	        </tfoot>
	    </table>
	</div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>