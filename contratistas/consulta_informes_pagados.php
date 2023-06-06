<?php
session_start();

$ruta_raiz = "..";

require_once($ruta_raiz.'/vendor/autoload.php');
require_once($ruta_raiz.'/contratistas/permisos.php');
require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/Estado.php');

$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');

$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');

$informes = Informe::getPaidByDateRange($fecha_inicio, $fecha_fin);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- CSS only -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
	<title>Informes</title>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col">
				<h2>
					Informes
				</h2>
			</div>
		</div>
		<form method="get">
			<div class="row">
				<div class="col-3">
					<input type="date" name="fecha_inicio" class="form-control" placeholder="Fecha inicio" value="<?= $fecha_inicio ?>">
				</div>
				<div class="col-3">
					<input type="date" name="fecha_fin" class="form-control" placeholder="Fecha fin" value="<?= $fecha_fin ?>">
				</div>
				<div class="col">
					<button type="submit" class="btn btn-light">
						<i class="bi bi-search"></i> Buscar
					</button>
				</div>
			</div>
		</form>
		<div class="row">
			<div class="col mt-3">
				<table class="table table-sm">
					<thead>
						<tr>
							<th width="30"></th>
							<th width="30"></th>
							<th width="30"></th>
							<th width="100">Estado</th>					
							<th width="100">Contrato</th>
							<th width="">Informe</th>
							<th width="100"></th>					
						</tr>
					</thead>
					<tbody>
						<?php foreach ($informes as $key => $informe): ?>
							<?php $contrato = Contrato::find($informe->contrato_id); ?>
							<tr>
								<td>
									<a title="Ver trazabilidad pago" data-contrato="<?= $contrato->id ?>" data-id="<?= $informe->id ?>" class="modal_trazabilidad_informe btn btn-sm btn-link">
										<i class="bi bi-bar-chart-steps"></i>
									</a>
								</td>
								<td>
									<a title="Ver revisiones" data-id="<?= $informe->id ?>" class="modal_revisiones btn btn-sm btn-link">
										<i class="bi bi-card-checklist"></i>
									</a>
								</td>
								<td>
									<a title="Ver informe" data-contrato="<?= $contrato->id ?>" data-id="<?= $informe->id ?>" class="modal_informe btn btn-sm btn-link">
										<i class="bi bi-file-earmark-text"></i>
									</a>
								</td>
								<td>
									<?php
										switch ($informe->estado) {
											case InformeEstado::$BORRADOR:
												echo '<span class="badge text-bg-light">Borrador</span>';
												break;
											case InformeEstado::$REVISION:
												echo '<span class="badge text-bg-primary">En revisión</span>';
												break;
											case InformeEstado::$APROBADO:
												echo '<span class="badge text-bg-success">Aprobado</span>';
												break;
											case InformeEstado::$MODIFICAR:
												echo '<span class="badge text-bg-warning">Modificar</span>';
												break;
											default:
												break;
										}
									?>
								</td>
								<td><small><?= $contrato->contrato ?></small></td>
								<td><small>Informe Nº. <?= $informe->numero ?> desde <?= $informe->fecha_inicio ?> hasta <?= $informe->fecha_fin ?></small></td>
								<td>
									<?= $informe->procede_pago == 't' ? '<span class="badge text-bg-info">Procede para pago</span>' : ''?>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="<?= $ruta_raiz.'/contratistas/js/modal.js'?>"></script>
	<script type="text/javascript">
		$(function() {
			$('.modal_informe').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');
		    	var contrato_id = $(this).data('contrato');

	    		modal(ruta_raiz+'/contratistas/modal_informes_consulta.php?cod='+contrato_id+'&inf='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

			$('.modal_revisiones').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');

	    		modal(ruta_raiz+'/contratistas/modal_revisiones_consulta.php?inf='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

		    $('.modal_trazabilidad_informe').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');

	    		modal(ruta_raiz+'/contratistas/modal_trazabilidad.php?inf='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });
		});
	</script>
</body>