<?php
session_start();

$ruta_raiz = "..";
require_once($ruta_raiz.'/contratistas/permisos.php');
require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/Estado.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');

$db = new ConnectionHandler($ruta_raiz);

$registros = $db->conn->GetAll('SELECT * FROM contratistas_informe_supervisor WHERE usuario_id = ? AND estado IN (?,?,?)', [$_SESSION['usua_id'], InformeEstadoIndividual::$PENDIENTE, InformeEstadoIndividual::$MODIFICAR, InformeEstadoIndividual::$APROBADO]);

if(!es_supervisor_de_contrato())
	header('Location: '.$ruta_raiz.'/contratistas/sin_permisos.php');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- CSS only -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.6/css/selectize.bootstrap5.css" integrity="sha512-wD3+yEMEGhx4+wKKWd0bNGCI+fxhDsK7znFYPvf2wOVxpr7gWnf4+BKphWnUCzf49AUAF6GYbaCBws1e5XHSsg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
	<title>Supervisor</title>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col">
				<h2>Informes por revisar</h2>
			</div>
		</div>
		<div class="row">
			<div class="col mt-3">
				<table class="table table-sm">
					<thead>
						<tr>
							<th width="30"></th>
							<th width="30"></th>
							<th width="100">Estado</th>
							<th width="100">Contrato</th>
							<th width="250">Usuario</th>
							<th width="">Informe</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($registros as $key => $registro): ?>
							<?php 
								$informe = Informe::find($registro['INFORME_ID']);
								$contrato = Contrato::find($informe->contrato_id);
								$usuario = $db->conn->getRow('SELECT * FROM usuario WHERE id = ?', [$contrato->usuario_id]);
							?>
							<?php if($informe->procede_pago == 'f'): ?> 
								<tr>
									<td>
										<div class="dropdown">
											<button class="btn btn-sm btn-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
												<i class="bi bi-menu-down"></i>
											</button>
											<ul class="dropdown-menu">
												<li>
													<a href="#" data-id="<?= $informe->id ?>" class="modal_adjuntos_requeridos dropdown-item">
														Documentos pago
													</a>
												</li>
												<li><a class="modal_revisiones dropdown-item" data-id="<?= $informe->id ?>">Observaciones</a></li>
												<li><hr class="dropdown-divider"></li>
												<li><a href="#" data-id="<?= $informe->id ?>" data-informe="Informe Nº. <?= $informe->numero ?> desde <?= $informe->fecha_inicio ?> hasta <?= $informe->fecha_fin ?>" class="dropdown-item aprobar"><i class="bi bi-check-lg"></i> Aprobar</a></li>
												<li><a href="#" data-id="<?= $informe->id ?>" data-informe="Informe Nº. <?= $informe->numero ?> desde <?= $informe->fecha_inicio ?> hasta <?= $informe->fecha_fin ?>" class="dropdown-item modificar"><i class="bi bi-file-earmark-break"></i> Modificar</a></li>
											</ul>
										</div>
									</td>
									<td>
										<a title="Ver informe" data-contrato="<?= $contrato->id ?>" data-id="<?= $informe->id ?>" class="modal_informe btn btn-sm btn-link">
											<i class="bi bi-file-earmark-text"></i>
										</a>
									</td>
									<td>
										<?php
											switch ($registro['ESTADO']) {
												case InformeEstadoIndividual::$PENDIENTE:
													echo '<span class="badge text-bg-light">Pendiente</span>';
												break;
												case InformeEstadoIndividual::$APROBADO:
													echo '<span class="badge text-bg-success">Aprobado</span>';
												break;
												case InformeEstadoIndividual::$MODIFICAR:
													echo '<span class="badge text-bg-warning">Modificar</span>';
												break;
											}
										?>
									</td>
									<td>
										<small>
											<?= $contrato->contrato ?>
										</small>
									</td>
									<td>
										<small><?= $usuario['USUA_NOMB'] ?></small>
									</td>
									<td>
										<small>
										Informe Nº. <?= $informe->numero ?> desde <?= $informe->fecha_inicio ?> hasta <?= $informe->fecha_fin ?>
										</small>
									</td>
								</tr>
							<?php endif; ?>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.6/js/standalone/selectize.js" integrity="sha512-X6kWCt4NijyqM0ebb3vgEPE8jtUu9OGGXYGJ86bXTm3oH+oJ5+2UBvUw+uz+eEf3DcTTfJT4YQu/7F6MRV+wbA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="<?= $ruta_raiz.'/contratistas/js/modal.js'?>"></script>
	<script type="text/javascript">
		$(function() {

			$('.modal_adjuntos_requeridos').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');

	    		modal(ruta_raiz+'/contratistas/modal_adjuntos_requeridos.php?inf='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });
		    
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

	    		modal(ruta_raiz+'/contratistas/modal_revisiones.php?inf='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

		    $('a.aprobar').on('click', function(e) {
				e.preventDefault();
		    	var ruta = "<?= $ruta_raiz.'/contratistas/modal_informes_cambiar_estado.php?id=' ?>";
		    	var estado = "<?= InformeEstadoIndividual::$APROBADO ?>";
				var id = $(this).data('id');
				var informe = $(this).data('informe');
				var resultado = confirm("Esta seguro que desea aprobar el informe: "+informe);
				if (resultado) 
				{
					window.location.href = ruta+id+'&estado='+estado;
				}
			});

			$('a.modificar').on('click', function(e) {
				e.preventDefault();
		    	var ruta = "<?= $ruta_raiz.'/contratistas/modal_informes_cambiar_estado.php?id=' ?>";
		    	var estado = "<?= InformeEstadoIndividual::$MODIFICAR ?>";
				var id = $(this).data('id');
				var informe = $(this).data('informe');
				var resultado = confirm("Esta seguro que desea estabelecer el informe: "+informe+". para modificar");
				if (resultado) 
				{
					window.location.href = ruta+id+'&estado='+estado;
				}
			});
		});
	</script>
</body>