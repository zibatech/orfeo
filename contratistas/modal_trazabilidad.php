<?php
session_start();
error_reporting(E_ALL);

$ruta_raiz = "..";
require_once($ruta_raiz.'/vendor/autoload.php');
require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');
require_once($ruta_raiz.'/contratistas/InformeRequerimiento.php');
require_once($ruta_raiz.'/contratistas/log.php');
$db = new ConnectionHandler($ruta_raiz);

$inf = $_GET['inf'];
$informe = Informe::find($inf);
$contrato = Contrato::find($informe->contrato_id);
$eventos = obtener_eventos($db, $informe->id);
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
		<div class="row">
			<div class="col">
				<div class="col mt-3">
					<label>Contrato</label>
					<input type="text" class="form-control" name="contrato" value="<?= $contrato->contrato ?>" disabled>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col mt-3">
				<label>Objeto</label>
				<textarea class="form-control" disabled name="contrato_objeto"><?= $contrato->objeto ?></textarea>
			</div>
		</div>
		<div class="row">
			<div class="col mt-3">
				<ul class="list-group">
					<?php foreach($eventos as $evento): ?>
						<?php
							$usuario = $db->conn->getRow('SELECT * FROM usuario WHERE id = ?', [$evento['USUARIO_ID']]);
							$requerimientos = InformeRequerimiento::getFromReportUser($informe->id, $evento['USUARIO_ID']);
						?>
						<li class="list-group-item">
							<div class="d-flex justify-content-between align-items-start">
								<div class="me-auto">
									<div class="fw-bold">
										<?= $evento['FECHA'] ?>
									</div>
									<small><?= $usuario['USUA_NOMB'] ?></small>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<small><?= $evento['DESCRIPCION'] ?></small>
								</div>
							</div>
							<?php if(count($requerimientos) > 0): ?>
								<div class="row">
									<div class="col">
										<?php foreach ($requerimientos as $requerimiento): ?>
											<a class="btn btn-sm btn-default" href="<?= $ruta_raiz.'/bodega/contratistas/'.$documento->adjunto ?>">
													<i class="bi bi-cloud-arrow-down"></i> 
													<?= $requerimiento->get_doc_name() ?>
											</a>
										<?php endforeach; ?>
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
	<script src="<?= $ruta_raiz.'/contratistas/js/numeroALetras.js'?>" type="text/javascript"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
</body>
</html>