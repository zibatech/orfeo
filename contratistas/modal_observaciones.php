<?php
session_start();
error_reporting(E_ALL);

$ruta_raiz = "..";
require_once($ruta_raiz.'/vendor/autoload.php');
require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/Estado.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');

$db = new ConnectionHandler($ruta_raiz);
$informe = Informe::find($_GET['inf']);
$contrato = Contrato::find($informe->contrato_id);
$revisiones = $db->conn->getAll('SELECT * FROM contratistas_informe_supervisor WHERE informe_id = ?', [$informe->id]);

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- CSS only -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
	<title>Estado informe Nº: <?= $informe->numero ?></title>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col mt-3">
				<h6>
				Estado actual informe Nº. <?= $informe->numero ?> desde <?= $informe->fecha_inicio ?> hasta <?= $informe->fecha_fin ?><br>
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
					<?php foreach ($revisiones as $key => $revision): ?>
						<?php 
							$usuario = $db->conn->getRow('SELECT * FROM usuario WHERE id = ?', [$revision['USUARIO_ID']]);

							$observaciones = $db->conn->getAll('SELECT * FROM contratistas_informe_supervisor_observaciones WHERE informe_supervisor_id = ?', [$revision['ID']]);
						?>
							<li class="list-group-item">
								<div class="d-flex justify-content-between align-items-start">
									<div class="me-auto">
										<div class="fw-bold"><?= $usuario['USUA_NOMB'] ?></div>
									</div>
									 <?php
										switch ($revision['ESTADO']) {
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
								</div>
								<div class="row">
									<div class="col">
									<?php if(count($observaciones) > 0): ?>
										<?php foreach ($observaciones as $key => $observacion): ?>
											<?php $u = $db->conn->getRow('SELECT * FROM usuario WHERE id = ?', [$observacion['USUARIO_ID']]); ?>
												<div class="row">
													<div class="col mt-2">
														<span class="badge text-bg-light"><?= $u['USUA_NOMB'] ?></span>
														<br>
														<small><?= $observacion['OBSERVACIONES'] ?></small>
													</div>
												</div>
										<?php endforeach ?>
									<?php endif; ?>
									</div>
								</div>
								<div class="row">
									<form action="modal_revisiones_observacion.php" method="post">
										<input type="hidden" name="id" value="<?= $revision['ID'] ?>">
										<input type="hidden" name="redirect" value="3">
										<div class="col">
											<div class="input-group input-group-sm mt-3">
												<input type="text" class="form-control" placeholder="Dejar un comentario" aria-label="Dejar un comentario" aria-describedby="Comentario" name="observacion" required>
												<button class="btn btn-outline-secondary"><i class="bi bi-arrow-right-short"></i></button>
											</div>
										</div>
									</form>
								</div>
							</li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script type="text/javascript">
</body>