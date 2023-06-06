<?php
session_start();

$ruta_raiz = "..";
require_once($ruta_raiz.'/contratistas/permisos.php');
require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/Estado.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');

$db = new ConnectionHandler($ruta_raiz);

$informes = [];
$requerimientos = $db->conn->getAll('SELECT crp.*
			FROM contratistas_requerimientos_pago crp
			WHERE EXISTS(
              	SELECT id
              	FROM contratistas_requerimientos_pago_dependencia crpd
              	WHERE crp.id = crpd.requerimiento_id
              	GROUP BY crpd.id
            	HAVING (COUNT(id) filter (where depe_codi = ? )) > 0
          	) AND contratista = false AND activo = true', [$_SESSION['dependencia']]);

$requerimiento_id = 0;


if(isset($_GET['requerimiento_id']))
{
	$requerimiento_id = $_GET['requerimiento_id'];
	$informes = Informe::getForPaymentWithPendingFile($requerimiento_id);
}

if(!puede_certificar_contrato())
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
	<title>Certificación de pagos</title>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col">
				<h2>Certificar pagos</h2>
			</div>
		</div>

		<form method="get">
			<div class="row">
				<div class="col-6">
					<label for="requerimiento_id">Selecciona el documento que vas a cargar</label>
					<select class="form-control" name="requerimiento_id">
						<?php foreach ($requerimientos as $requerimiento): ?>
							<option <?= $requerimiento['ID'] == $requerimiento_id ? 'selected' : '' ?> value="<?= $requerimiento['ID'] ?>"><?= $requerimiento['DOCUMENTO'] ?></option>
						<?php endforeach ?>
	    			</select>
				</div>
				<div class="col">
					<button type="submit" class="btn btn-light" style="margin-top:24px">
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
							<th width="100">Estado</th>					
							<th width="100">Contrato</th>
							<th width="">Informe</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($informes as $key => $informe): ?>
							<?php $contrato = Contrato::find($informe->contrato_id); ?>
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

		});
	</script>
</body>
</html>