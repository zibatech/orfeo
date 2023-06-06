<?php
session_start();

$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/permisos.php');
require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/Estado.php');
require_once($ruta_raiz.'/contratistas/InformeRequerimiento.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');
$db = new ConnectionHandler($ruta_raiz);

$informe = Informe::find($_GET['inf']);
$requerimiento_id = isset($_GET['req']) ? $_GET['req'] : 0;
$contrato = Contrato::find($informe->contrato_id);
$documentos_requeridos_pago = InformeRequerimiento::getFromReport($informe->id);

//var_dump($_SESSION);
if(!puede_certificar_contrato() && !es_supervisor_de_contrato() && !tiene_acceso_a_contrato($contrato->id))
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
	<title></title>
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
			<div class="col mt-3">
				<h6>
					Documentos requeridos para el pago correspondiente al informe Nº. <?= $informe->numero ?> desde <?= $informe->fecha_inicio ?> hasta <?= $informe->fecha_fin ?><br>
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
				</h6>
			</div>
		</div>
		<div class="row">
			<div class="col mt-3">
				<ul class="list-group">
					<?php foreach ($documentos_requeridos_pago as $documento): ?>
						<li class="list-group-item">
							<div class="d-flex justify-content-between align-items-start">
								<div class="me-auto">
									<div class="fw-bold"><?= $documento->get_doc_name() ?> <?= $documento->its_optional_doc() ? '<span class="badge text-bg-secondary">Opcional</span>' : '' ?></div>
								</div>
							</div>
							<?php 
								$habiltar_carga = false;
								if ($documento->is_from_employe())
								{
									$habiltar_carga = $_SESSION['usua_id'] == $contrato->usuario_id;
								} else {
									if (es_supervisor_de_contrato() || puede_certificar_contrato())
									{
									$habiltar_carga = $documento->requerimiento_id == $requerimiento_id || $documento->validateDependence(intval($_SESSION['dependencia']));
									} else {
										$habiltar_carga = false;
									}
								}
							?>
							<?php if(!$documento->adjunto && $habiltar_carga): ?>
								<div class="row">
									<div class="col mt-2">
										<form action="modal_adjuntos_requeridos_save.php" enctype="multipart/form-data" method="post">
											<input type="hidden" name="id" value="<?= $documento->id ?>">
											<div class="input-group input-group-sm mt-3">
												<input class="form-control form-control-sm" type="file" name="adjunto">
												<button class="btn btn-outline-secondary"><i class="bi bi-cloud-arrow-up"></i></button>
											</div>
										</form>
									</div>
								</div>
							<?php elseif($documento->adjunto): ?>
								<?php 
									$usuario = $db->conn->getRow('SELECT * FROM usuario WHERE id = ?', $documento->usuario_id);
								?>
								<div class="row">
									<div class="col mt-2">
										<small>Cargado por <strong><?= $usuario['USUA_NOMB'] ?></strong> el <?= $documento->fecha ?></small>
									</div>
								</div>
								<div class="row">
									<div class="col mt-2">
										<a class="btn btn-sm btn-default" href="<?= $ruta_raiz.'/bodega/contratistas/'.$documento->adjunto ?>">
												<i class="bi bi-cloud-arrow-down"></i> Descargar
										</a>
									</div>
								</div>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
</body>
</html>