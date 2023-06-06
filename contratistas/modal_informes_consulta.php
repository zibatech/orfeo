<?php
session_start();
error_reporting(E_ALL);

$ruta_raiz = "..";
require_once($ruta_raiz.'/vendor/autoload.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');
require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/Obligacion.php');
require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/InformeObligacion.php');
require_once($ruta_raiz.'/contratistas/InformeEvidencia.php');
require_once($ruta_raiz.'/contratistas/permisos.php');

$db = new ConnectionHandler($ruta_raiz);

$cod = $_GET['cod'];
$contrato = Contrato::find($cod);
$obligaciones = Obligacion::getFromContract($contrato->id);
$fecha = Carbon\Carbon::now();

$inf = $_GET['inf'];
$informe = Informe::find($inf);

if($contrato)
{
	$hoy = Carbon\Carbon::createFromFormat('Y-m-d', $informe ? $informe->fecha_informe : date('Y-m-d'));
	$fecha_inicio = Carbon\Carbon::createFromFormat('Y-m-d', $contrato->fecha_inicio);
	$fecha_fin = Carbon\Carbon::createFromFormat('Y-m-d', $contrato->fecha_fin);
	$total_dias_contrato = $fecha_fin->diffInDays($fecha_inicio);
	$total_dias_transcurridos = $hoy->diffInDays($fecha_inicio);
}

$supervisor = es_supervisor_de_contrato();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
	<title>Informe</title>
</head>
<body>
	<div class="container-fluid">
		<?php if(isset($_GET['status']) && $_GET['status'] == '1'): ?>
		<div class="row">
			<div class="col mt-3">
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					<h4 class="alert-heading">Bien!</h4>
					<p>Se guardo el contrato satisfactoriamente.</p>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>		
			</div>
		</div>
		<?php endif; ?>
		<div class="row">
			<div class="col-sm-3 mt-3">
				<label for="numero" class="form-label">Informe Nº</label>
				<input type="number" class="form-control" id="numero" name="numero" aria-describedby="Informe Nº" value="<?= $informe ? $informe->numero : '' ?>" disabled>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 mt-3">
				<label>Fecha de suscripcion</label>
				<input type="text" class="form-control" disabled name="contrato_fecha_inicio" value="<?= $contrato->fecha_inicio ?>">
			</div>
			<div class="col-sm-6 mt-3">
				<label>Plazo de Ejecución</label>
				<input type="text" class="form-control" disabled name="contrato_fecha_fin" value="<?= $contrato->fecha_fin ?>">
			</div>
		</div>
		<div class="row">
			<div class="col mt-3">
				<label>Objeto</label>
				<textarea class="form-control" disabled name="contrato_objeto"><?= $contrato->objeto ?></textarea>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<hr>
			</div>
		</div>
		<div class="row">
			<div class="col mt-3" id="valor">
				<label>Valor total del contrato</label>
				<input type="text" class="form-control" disabled name="contrato_valor_total" value="<?= $contrato->valor ?>">
				<small class="en_letras"></small>
			</div>
			<div class="col mt-3" id="valor_mensual">
				<label>Valor honorarios mensuales</label>
				<input type="text" class="form-control" disabled name="contrato_valor_mensual" value="<?= $contrato->honorarios_mensuales ?>">
				<small class="en_letras"></small>
			</div>
		</div>
		<div class="row">
			<div class="col mt-3" id="valor_ejecutado">
				<label>Valor a ejecutar con el informe</label>
				<input type="text" class="form-control" disabled name="valor_ejecutado" value="<?= $informe ? $informe->valor_ejecutado : '' ?>">
				<small class="en_letras"></small>
			</div>
			<div class="col mt-3" id="total_ejecutado">
				<label>Valor ejecutado</label>
				<input type="text" class="form-control" disabled name="total_ejecutado" data-initial-value="<?= $informe ? $informe->total_ejecutado - $informe->valor_ejecutado : '' ?>" value="<?= $informe ? $informe->total_ejecutado : '' ?>">
				<small class="en_letras"></small>
			</div>
		</div>
		<div class="row">
			<div class="col mt-3">
				<label>Porcentaje de ejecución</label>
				<p class="form-control-static" id="porcentaje_ejecutado"></p>
			</div>
			<div class="col mt-3">
				<label>Porcentaje por ejecutar</label>
				<p class="form-control-static" id="porcentaje_pendiente"></p>
			</div>
		</div>
		<div class="row">
			<div class="col mt-3">
				<label for="fecha_inicio" class="form-label">Fecha inicio</label>
				<input type="date" class="form-control" disabled id="fecha_inicio" name="fecha_inicio" aria-describedby="Fecha inicio" value="<?= $informe ? $informe->fecha_inicio : '' ?>" required>
			</div>
			<div class="col mt-3">
				<label for="fecha_fin" class="form-label">Fecha fin</label>
				<input type="date" class="form-control" disabled id="fecha_fin" name="fecha_fin" aria-describedby="Fecha fin" value="<?= $informe ? $informe->fecha_fin : '' ?>" required>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<hr>
			</div>
		</div>
		<?php foreach ($obligaciones as $key => $obligacion): ?>
			<?php
				$obligacion_informe = $informe ? InformeObligacion::findByReportObligation($informe->id, $obligacion->id) : null;
			?>
			<div class="row">
				<div class="col mt-3">
					<label for="obligacion_<?= $obligacion->id ?>">Obligación Nº .<?= $obligacion->numero.': '.$obligacion->descripcion ?></label> 
						<?php if($supervisor): ?>
							<form method="post" action="modal_informes_cambiar_estado_obligacion.php">
								<input type="hidden" name="cod" value="<?= $cod ?>">
								<input type="hidden" name="inf" value="<?= $inf ?>">
								<input type="hidden" name="id" value="<?= $obligacion_informe->id ?>">
								<div class="row">
									<div class="col mb-3">
										<input type="submit" name="accion" value="Aprobada" class="btn btn-sm btn-outline-primary">

										<input type="submit" name="accion" value="Corregir" class="btn btn-sm btn-outline-danger">
										<?php
											$estado_obligacion = $db->conn->getRow('SELECT * FROM contratistas_informe_obligacion_estado WHERE obligacion_informe_id = ? ORDER BY id DESC LIMIT 1', [$obligacion_informe->id]);
										?>
										<?php if($estado_obligacion): ?>
											<?php 
												$usuario_estado = $db->conn->getRow('SELECT * FROM usuario WHERE id = ?', [$estado_obligacion['SUPERVISOR_ID']])
											?>
											<small><strong class="<?= $estado_obligacion['ESTADO'] == 'Aprobada' ? 'text-success' : 'text-danger' ?>"><?= $estado_obligacion['ESTADO'] ?></strong> por <?= $usuario_estado['USUA_NOMB'] ?> el <?= $estado_obligacion['FECHA'] ?></small>
										<?php endif; ?>
									</div>
								</div>
							</form>
						<?php endif; ?>
					<textarea id="obligacion_descripcion_<?= $obligacion->id ?>" disabled class="form-control" name="obligacion_descripcion[<?= $obligacion->id ?>]" required><?= $obligacion_informe ? $obligacion_informe->descripcion : '' ?></textarea>
				</div>
			</div>
			<div class="row" style="display:none;">
				<div class="col mt-3">
					<label id="obligacion_fecha_inicio_<?= $obligacion->id ?>">Desde</label>
					<input type="date" id="obligacion_fecha_inicio_<?= $obligacion->id ?>" class="form-control" disabled name="obligacion_fecha_inicio[<?= $obligacion->id ?>]" value="<?= $obligacion_informe ? $obligacion_informe->fecha_inicio : '' ?>">
				</div>

				<div class="col mt-3">
					<label id="obligacion_fecha_fin_<?= $obligacion->id ?>">Hasta</label>
					<input type="date" id="obligacion_fecha_fin_<?= $obligacion->id ?>" class="form-control" disabled name="obligacion_fecha_fin[<?= $obligacion->id ?>]" value="<?= $obligacion_informe ? $obligacion_informe->fecha_inicio : '' ?>">
				</div>
			</div>
			<?php if($obligacion_informe): ?>
				<?php $evidencias = InformeEvidencia::getFromObligation($obligacion_informe->id); ?>
				<?php if(count($evidencias) > 0): ?>
					<div class="row">
						<div class="col">
							<table class="table table-sm">
								<thead>
									<tr>
										<th>Descripción</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($evidencias as $key => $evidencia): ?>
										<tr>
											<td>
												<a class="btn btn-sm btn-default" href="<?= $ruta_raiz.'/bodega/contratistas/'.$evidencia->archivo ?>">
													<i class="bi bi-cloud-arrow-down"></i> <?= $evidencia->nombre ?>
												</a>
											</td>
										</tr>
									<?php endforeach ?>
								</tbody>
							</table>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		<?php endforeach ?>
		<div class="row">
			<div class="col">
				<hr>
			</div>
		</div>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="<?= $ruta_raiz.'/contratistas/js/numeroALetras.js'?>" type="text/javascript"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
	<script type="text/javascript">
		$(function(e) {

			var valor_en_letras = function(selector) {
				var total = selector.find('input').val() * 1;
				var numero_a_letras = numeroALetras(total, {
					plural: 'PESOS',
					singular: 'PESO',
					centPlural: 'centavos',
					centSingular: 'centavo'
				});
				selector.find('.en_letras').text(numero_a_letras)
			}

			valor_en_letras($('#valor'));
			valor_en_letras($('#valor_mensual'));

			$('#valor_ejecutado input').on('keyup', function(e) {
				valor_en_letras($('#valor_ejecutado'));
				var total_ejecutado = parseInt($('#total_ejecutado input').data('initial-value')) + parseInt($(this).val());

				$('#total_ejecutado input').val(total_ejecutado);
				valor_en_letras($('#total_ejecutado'));

				var total_contrato = $('input[name="contrato_valor_total"]').val();
				var porcentaje = Math.round(total_ejecutado * 100 / total_contrato);
				
				$('#porcentaje_ejecutado').text(porcentaje+' %');
				$('#porcentaje_pendiente').text(100 - porcentaje+' %');
			});

			$('#valor_ejecutado input').trigger('keyup');
		});
	</script>
</body>
</html>