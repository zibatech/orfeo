<?php
session_start();

$ruta_raiz = "..";
require_once($ruta_raiz.'/contratistas/permisos.php');
require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/Estado.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');

$db = new ConnectionHandler($ruta_raiz);

$informes = [];
$usuario = null;
$informes = Informe::getForPayment();

if(!puede_procesar_pagos())
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
	<title>Pagos</title>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col">
				<h2>Pagos</h2>
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
							<th width="250">Usuario</th>					
							<th width="">Informe</th>					
						</tr>
					</thead>
					<tbody>
						<?php foreach ($informes as $key => $informe): ?>
							<?php 
								$contrato = Contrato::find($informe->contrato_id); 
								$usuario = $db->conn->getRow('SELECT * FROM usuario WHERE id = ?', [$contrato->usuario_id]);
							?>
							<tr>
								<td>
									<div class="dropdown">
										<button class="btn btn-sm btn-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
											<i class="bi bi-menu-down"></i>
										</button>
										<ul class="dropdown-menu">
											<?php if (in_array($informe->estado, [
												InformeEstado::$APROBADO
											])): ?>
												<li>
													<a href="#" data-requerimiento="<?= $requerimiento_id ?>" data-id="<?= $informe->id ?>" class="modal_adjuntos_requeridos dropdown-item">
														Documentos pago
													</a>
												</li>
												<li>
													<a href="#" data-id="<?= $informe->id ?>" class="modal_revisiones dropdown-item">
														Revisiones
													</a>
												</li>
												<li><hr class="dropdown-divider"></li>
												<li>
													<a href="#" data-informe="<?= $informe->numero ?>" data-id="<?= $informe->id ?>" class="dropdown-item proceder_para_pago"><i class="bi bi-arrow-bar-right"></i> Proceder para pago</a>
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
								<td>
									<small>
										<?= $contrato->contrato ?>
									</small>
								</td>
								<td>
									<small><?= $usuario['USUA_NOMB'] ?></small>
								</td>
								<td><small>Informe Nº. <?= $informe->numero ?> desde <?= $informe->fecha_inicio ?> hasta <?= $informe->fecha_fin ?></small></td>
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

			$('.modal_adjuntos_requeridos').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');
		    	var requerimiento = $(this).data('requerimiento');

	    		modal(ruta_raiz+'/contratistas/modal_adjuntos_requeridos.php?inf='+id+'&req='+requerimiento, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

			$('.modal_revisiones').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');

	    		modal(ruta_raiz+'/contratistas/modal_revisiones_consulta.php?inf='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

		    $('a.proceder_para_pago').on('click', function(e) {
		    	var ruta = "<?= $ruta_raiz.'/contratistas/modal_informe_procede_pago.php?inf=' ?>"
				var informe = $(this).data('informe');
				var id = $(this).data('id');
				var resultado = confirm("Esta seguro que desea proceder para pago el informe Nº: "+informe);
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