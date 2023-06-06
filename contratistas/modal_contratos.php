<?php
session_start();

$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/permisos.php');
if(!puede_administrar_contratos())
	header('Location: '.$ruta_raiz.'/contratistas/sin_permisos.php');

require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');

$db = new ConnectionHandler($ruta_raiz);

$id = isset($_GET['cod']) ? $_GET['cod'] : '0';

$contrato = Contrato::find($id);
$usuario = null;

if(isset($_GET['usu']))
	$usuario = $db->conn->getRow('SELECT * FROM usuario WHERE id = ?', $_GET['usu']);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- CSS only -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
	<title>Contratos</title>
</head>
<body>
	<form action="modal_contratos_save.php" method="post">
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
					<label for="documento">Documento</label>
					<input type="text" class="form-control" id="documento" name="documento" value="<?= $usuario['USUA_DOC'] ?>" disabled>
				</div>
				<div class="col mt-3">
					<label for="nombre">Nombre</label>
					<input type="text" class="form-control" id="nombre" name="nombre" value="<?= $usuario['USUA_NOMB'] ?>" disabled>
				</div>
			</div>
			<div class="row">
				<div class="col mt-3">
					<label for="expediente" class="form-label">Expediente</label>
					<input type="text" class="form-control" id="expediente" name="expediente" aria-describedby="Contrato" value="<?= $contrato ? $contrato->expediente : ''?>" required>
				</div>
				<div class="col mt-3">
					<label for="contrato" class="form-label">Contrato</label>
					<input type="text" class="form-control" id="contrato" name="contrato" aria-describedby="Contrato" value="<?= $contrato ? $contrato->contrato : ''?>" required>
				</div>
			</div>
			<div class="row">
				<div class="col mt-3">
					<label for="rp" class="form-label">RP</label>
					<input type="text" class="form-control" id="rp" name="rp" aria-describedby="Contrato" value="<?= $contrato ? $contrato->rp : ''?>" required>
				</div>
				<div class="col mt-3">
					<label for="fecha_rp" class="form-label">Fecha RP</label>
					<input type="date" class="form-control" id="fecha_rp" name="fecha_rp" aria-describedby="Contrato" value="<?= $contrato ? $contrato->fecha_rp : ''?>" required>
				</div>
			</div>
			<div class="row">
				<div class="col mt-3">
					<label for="cdp" class="form-label">CDP</label>
					<input type="text" class="form-control" id="cdp" name="cdp" aria-describedby="Contrato" value="<?= $contrato ? $contrato->cdp : ''?>" required>
				</div>
				<div class="col mt-3">
					<label for="fecha_cdp" class="form-label">Fecha CDP</label>
					<input type="date" class="form-control" id="fecha_cdp" name="fecha_cdp" aria-describedby="Contrato" value="<?= $contrato ? $contrato->fecha_cdp : ''?>" required>
				</div>
			</div>
			<div class="row">
				<div class="col mt-3">
					<label for="objeto" class="form-label">Objeto del contrato</label>
					<textarea class="form-control" rows="6" id="objeto"  name="objeto" required><?= $contrato ? $contrato->objeto : ''?></textarea>
				</div>
			</div>
			<div class="row">
				<div class="col mt-3">
					<label for="fecha_inicio" class="form-label">Fecha inicio</label>
					<input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" aria-describedby="Fecha inicio" value="<?= $contrato ? $contrato->fecha_inicio : ''?>" required>
				</div>
				<div class="col mt-3 mb-3">
					<label for="fecha_fin" class="form-label">Fecha fin</label>
					<input type="date" class="form-control" id="fecha_fin" name="fecha_fin" aria-describedby="Fecha fin" value="<?= $contrato ? $contrato->fecha_fin : ''?>" required>
				</div>
			</div>
			<div class="row">
				<div class="col mt-3">
					<label for="valor" class="form-label">Valor</label>
					<input type="number" class="form-control" id="valor" name="valor" aria-describedby="Valor" value="<?= $contrato ? $contrato->valor : ''?>" required>
				</div>
				<div class="col mt-3">
					<label for="honorarios_mensuales" class="form-label">Valor mensual</label>
					<input type="number" class="form-control" id="honorarios_mensuales" name="honorarios_mensuales" aria-describedby="Valor" value="<?= $contrato ? $contrato->honorarios_mensuales : ''?>" required>
				</div>
			</div>
			<div class="row">
				<div class="col mt-3 mb-3">
					<input type="hidden" name="id" value="<?= $contrato ? $contrato->id : '0'?>">
					<input type="hidden" name="usuario_id" value="<?= $usuario['ID'] ?>">
					<button class="btn btn-primary" type="submit">
						<i class="bi bi-save"></i> Guardar
					</button>
				</div>
			</div>
		</div>
	</form>
	<!-- JavaScript Bundle with Popper -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
</body>
</html>