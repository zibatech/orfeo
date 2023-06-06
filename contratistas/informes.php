<?php
session_start();

$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/permisos.php');
require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/Estado.php');

$contrato = Contrato::find($_GET['cod']);
$informes = Informe::getFromContract($contrato->id);

if(!puede_administrar_sus_contratos() && !tiene_acceso_a_contrato($contrato->id))
	header('Location: '.$ruta_raiz.'/contratistas/sin_permisos.php');

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
		<div class="row">
			<div class="col">
				<a href="<?= $ruta_raiz.'/contratistas/mis_contratos.php' ?>" class="btn btn-default">
					<i class="bi bi-arrow-left-short"></i>
				</a>
				<a data-contrato="<?= $contrato->id ?>" data-id="0" class="modal_informe btn btn-primary">
					<i class="bi bi-plus"></i> Nuevo
				</a>
			</div>
		</div>
		<div class="row">
			<div class="col mt-3">
				<table class="table table-sm">
					<thead>
						<tr>
							<th width="30"></th>
							<th width="100">Estado</th>					
							<th width="100">Contrato</th>
							<th width="">Informe</th>
							<th width="100"></th>					
						</tr>
					</thead>
					<tbody>
						<?php foreach ($informes as $key => $informe): ?>
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
												
											<?php if (in_array($informe->estado, [InformeEstado::$REVISION,
												InformeEstado::$MODIFICAR,
												InformeEstado::$APROBADO])): ?>
												<li>
													<a href="#" class="modal_revisiones dropdown-item" data-id="<?= $informe->id ?>">
														Consultar revisiones
													</a>
												</li>
											<?php endif; ?>

											<?php if (in_array($informe->estado, [InformeEstado::$BORRADOR,
												InformeEstado::$MODIFICAR])): ?>
												<li>
													<a href="#" class="enviar_a_revision dropdown-item" data-informe="<?= $informe->numero ?>" data-id="<?= $informe->id ?>">
														Enviar a revisión
													</a>
												</li>
												<li><hr class="dropdown-divider"></li>
												<li>
													<a href="#" class="modal_informe dropdown-item" data-contrato="<?= $contrato->id ?>" data-id="<?= $informe->id ?>"><i class="bi bi-pencil"></i> Editar</a>
												</li>
											<?php endif; ?>

											<?php if (in_array($informe->estado, [InformeEstado::$BORRADOR])): ?>
												<li>
													<a href="#" data-informe="<?= $informe->numero ?>" data-id="<?= $informe->id ?>" class="dropdown-item eliminar"><i class="bi bi-trash"></i> Eliminar</a>
												</li>
											<?php endif; ?>
										</ul>
									</div>
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

		    	if(id == 0)
		    		modal(ruta_raiz+'/contratistas/modal_informes.php?cod='+contrato_id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    	else
		    		modal(ruta_raiz+'/contratistas/modal_informes.php?cod='+contrato_id+'&inf='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

			$('.modal_adjuntos_requeridos').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');

	    		modal(ruta_raiz+'/contratistas/modal_adjuntos_requeridos.php?inf='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

			$('.modal_revisiones').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');

	    		modal(ruta_raiz+'/contratistas/modal_revisiones.php?inf='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

		    $('a.enviar_a_revision').on('click', function(e) {
		    	var ruta = "<?= $ruta_raiz.'/contratistas/modal_informes_enviar.php?inf=' ?>"
				var informe = $(this).data('informe');
				var id = $(this).data('id');
				var resultado = confirm("Esta seguro que desea eliminar el informe Nº: "+informe);
				if (resultado) 
				{
					window.location.href = ruta+id;
				} else {

				}

				e.preventDefault();
			});

		    $('a.eliminar').on('click', function(e) {
		    	var ruta = "<?= $ruta_raiz.'/contratistas/modal_informes_delete.php?inf=' ?>"
				var informe = $(this).data('informe');
				var id = $(this).data('id');
				var resultado = confirm("Esta seguro que desea eliminar el informe Nº: "+informe);
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