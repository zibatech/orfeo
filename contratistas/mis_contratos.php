<?php
session_start();

$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/Contrato.php');

$contratos = Contrato::getFromUser($_SESSION['usua_id']);

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- CSS only -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
	<title>Mis contratos</title>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col">
				<h2>Mis contratos</h2>
			</div>
		</div>
		<div class="row">
			<div class="col mt-3">
				<table class="table table-sm">
					<thead>
						<tr>
							<th width="30"></th>
							<th width="100">Contrato</th>
							<th>Objeto</th>
							<th width="100">Fecha inicio</th>
							<th width="100">Fecha fin</th>
							<th width="100">Valor</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($contratos as $key => $contrato): ?>
							<tr>
								<td>
									<div class="dropdown">
										<button class="btn btn-sm btn-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
											<i class="bi bi-menu-down"></i>
										</button>
										<ul class="dropdown-menu">
											<li><a class="modal_obligaciones dropdown-item" data-id="<?= $contrato->id ?>">Obligaciones</a></li>
											<li><a href="<?= $ruta_raiz.'/contratistas/informes.php?cod='.$contrato->id ?>" class="dropdown-item">Informes</a></li>
											<li>
										</ul>
									</div>
								</td>
								<td><small><?= $contrato->contrato ?></small></td>
								<td><small><?= $contrato->objeto ?></small></td>
								<td><small><?= $contrato->fecha_inicio ?></small></td>
								<td><small><?= $contrato->fecha_fin ?></small></td>
								<td style="text-align: right;"><small>$<?= number_format($contrato->valor, 0, ',', '.') ?></small></td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- JavaScript Bundle with Popper -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="<?= $ruta_raiz.'/contratistas/js/modal.js'?>"></script>
	<script type="text/javascript">
		$(function() {
		    $('.modal_contrato').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');

		    	if(id == 0)
		    		modal(ruta_raiz+'/contratistas/modal_contratos.php', 'location=no,height=900,width=600,scrollbars=no,status=no');
		    	else
		    		modal(ruta_raiz+'/contratistas/modal_contratos.php?cod='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

		    $('.modal_obligaciones').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');

		    	modal(ruta_raiz+'/contratistas/modal_obligaciones.php?cod='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

		    $('a.eliminar').on('click', function(e) {
		    	var ruta = "<?= $ruta_raiz.'/contratistas/modal_contratos_delete.php?cod=' ?>"
				var contrato = $(this).data('contrato');
				var id = $(this).data('id');
				var resultado = confirm("Esta seguro que desea eliminar el contrato: "+contrato);
				if (resultado) 
				{
					window.location.href = ruta+id;
				} else {

				}

				e.preventDefault();
			});
		});
	</script>
</body>
</html>