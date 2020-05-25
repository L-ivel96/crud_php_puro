<?php
	require_once('./conexao.php');

	$operacao = isset($_GET["op"]) ? $_GET["op"] : "";
	$id = isset($_GET["id"]) ? $_GET["id"] : "";

	if ($operacao == "excluir" && !empty($id) && is_numeric($id)) {
		$deletar_sql = "DELETE FROM cliente WHERE id_cliente='$id'; ";
		$deletar = atualiza_bd($deletar_sql);
	}

	$consulta = "SELECT * FROM cliente";
	$busca_nome_cpf = isset($_GET["busca_nome_cpf"]) ? $_GET["busca_nome_cpf"] : "";
	$ordenar_por = isset($_GET["ordenar_por"]) ? $_GET["ordenar_por"] : "";

	$busca_ordenacao = array(
		"nome_asc" =>"Nome (A-Z)",
		"nome_desc" =>"Nome (Z-A)",
		"valor_asc" =>"Valor (crescente)",
		"valor_desc" =>"Valor (decrescente)",
		"vencimento_asc" =>"Vencimento (crescente)",
		"vencimento_desc" =>"Vencimento (decrescente)"
	);
	
	if ( 
		(isset($_GET["busca_nome_cpf"]) && !empty($_GET["busca_nome_cpf"]) ) 
		|| (isset($_GET["ordenar_por"]) && !empty($_GET["ordenar_por"]) )
	) {

		if (isset($_GET["busca_nome_cpf"]) && !empty($_GET["busca_nome_cpf"])) {
			$valor_busca = $_GET["busca_nome_cpf"];
			$consulta .= " WHERE nome like '%$valor_busca%' or cpf_cnpj like '%$valor_busca%'";
		}

		if (isset($_GET["ordenar_por"]) && !empty($_GET["ordenar_por"])) {
			$ordem = explode('_', $_GET["ordenar_por"]);
			$consulta .= " ORDER BY ".$ordem[0]." ".$ordem[1]." ";

		}
	}
	
	$clientes = consulta_bd($consulta);

	$total_devedores = mysqli_con ? $clientes->num_rows : sizeof($clientes);
	$total_divida = 0;
	$total_vencidos = 0;

	$dt_atual = new DateTime();

	foreach ($clientes as $cliente) {
		$total_divida += $cliente["valor"];
		$dt_vencimento = new DateTime($cliente["vencimento"]);

		if ($dt_vencimento < $dt_atual) {
			$total_vencidos += 1;
		}

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

    	<div class="d-flex justify-content-center">
	    	<div class="col-md-4">
		    	<table id="tabela_clientes" class="table table-striped table-bordered mb-5">
			        <thead class="thead-dark">
			            <tr>
			                <th>Análise</th>
			                <th>Dados</th>
			            </tr>
			        </thead>
			        <tbody>
			        	<tr>
			        		<td>Total de devedores</td>
			        		<td><?= $total_devedores; ?></td>
			        	</tr>
			        	<tr>
			        		<td>Total da dívida</td>
			        		<td><?= "R$".number_format($total_divida,2,",","."); ?></td>
			        	</tr>
			        	<tr>
			        		<td>Total de vencidos</td>
			        		<td><?= $total_vencidos; ?></td>
			        	</tr>
			        </tbody>
			    </table>
			</div>
		</div>

		<div class="mb-2 col-md-7">
			<form class="form-inline" method="GET">
				<div class="mr-2 mb-2">
					<input type="text" class="form-control" name="busca_nome_cpf" placeholder="Buscar nome ou CPF/CNPJ" value="<?= $busca_nome_cpf; ?>">
				</div>
				<div class="mr-2 mb-2">
					<select class="form-control" name="ordenar_por">
						<option value="" <?= $ordenar_por ? "" : 'selected'; ?>  >Ordenar por:</option>
						<?php foreach ($busca_ordenacao as $key => $value) : ?>

							<option value="<?= $key; ?>" <?= $ordenar_por == $key ? 'selected=""' : ""; ?> ><?= $value; ?></option>
						<?php endforeach ?>
						
					</select>
				</div>
				<div>
					<input type="submit" class="btn btn-primary mb-2" value="Pesquisar" />
				</div>

			</form>
		</div>

	    <table id="tabela_clientes" class="table table-striped table-bordered table-responsive">
	        <thead class="thead-dark">
	            <tr>
	                <th>Nome</th>
	                <th>CPF/CNPJ</th>
	                <th>Idade</th>
	                <th>Enderço</th>
	                <th>Descrição</th>
	                <th>Valor</th>
	                <th>Vencimento</th>
	                <th>Atualizado em</th>
	                <th>Editar</th>
	                <th>Deletar</th>
	            </tr>
	        </thead>
	        <tbody>
	        	<?php foreach ($clientes as $cliente): ?>
	        	<?php
	        		//tratamento de dados
		        	$nascimento = new DateTime($cliente["nascimento"]);
		        	$idade = $nascimento->diff(new DateTime());
	        		$atualizado_em = new DateTime($cliente["atualizado_em"]);
        			$valor_br = "R$".number_format($cliente["valor"],2,",",".");
	        	?>
	        	<tr>
	        		<td><?= $cliente["nome"]; ?></td>
	        		<td><?= formatCnpjCpf($cliente["cpf_cnpj"]); ?></td>
	        		<td><?= $idade->y; ?></td>
	        		<td><?= $cliente["endereco"]; ?></td>
	        		<td><?= $cliente["desc_titulo"]; ?></td>
	        		<td><?= $valor_br; ?></td>
	        		<td><?= $cliente["vencimento"]; ?></td>
	        		<td><?= $atualizado_em->format('d/m/Y h:i:s'); ?></td>
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
	                <td colspan="10"><a href="./pagina_editar?op=cadastrar" class="btn btn-info">Adicionar Cliente</a></td>
	            </tr>
	        </tfoot>
	    </table>
	</div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>