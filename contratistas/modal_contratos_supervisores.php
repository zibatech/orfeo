<?php
session_start();

$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/permisos.php');
require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/ContratoSupervisor.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');
$db = new ConnectionHandler($ruta_raiz);

$id = isset($_GET['cod']) ? $_GET['cod'] : '0';

$contrato = Contrato::find($id);
$usuario = $db->conn->getRow('SELECT * FROM usuario WHERE id = ?', $contrato->usuario_id);
$supervisores = ContratoSupervisor::getFromContract($contrato->id);

if(!puede_administrar_contratos())
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
	<title>Anexos contrato <?= $contrato->nombre ?></title>
</head>
<body>
	<form action="modal_contratos_supervisores_save.php" method="POST">
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
					<label>Contrato</label>
					<input type="text" class="form-control" name="contrato" value="<?= $contrato->contrato ?>" disabled>
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
					<select class="form-control buscador_usuario" name="usuario" placeholder="Buscar" autocomplete="off" required>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col mt-3 mb-3">
					<input type="hidden" name="contrato_id" value="<?= $contrato->id ?>">
					<button class="btn btn-primary" type="submit">
						<i class="bi bi-save"></i> Guardar
					</button>
				</div>
			</div>
			<?php if(count($supervisores) > 0): ?>
				<div class="row">
					<div class="col">
						<table class="table table-sm">
							<thead>
								<tr>
									<th width="30"></th>
									<th>Supervisor</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($supervisores as $key => $supervisor): ?>
									<tr>
										<td>
											<a href="#" data-supervisor="<?= $supervisor->usuario['USUA_NOMB'] ?>"  data-id="<?= $supervisor->id ?>" class="eliminar btn btn-link btn-sm">
												<i class="bi bi-trash"></i>
											</a>
										</td>
										<td><?= $supervisor->usuario['USUA_NOMB'] ?></td>
									</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</form>

	<!-- JavaScript Bundle with Popper -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.6/js/standalone/selectize.js" integrity="sha512-X6kWCt4NijyqM0ebb3vgEPE8jtUu9OGGXYGJ86bXTm3oH+oJ5+2UBvUw+uz+eEf3DcTTfJT4YQu/7F6MRV+wbA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="<?= $ruta_raiz.'/contratistas/js/buscador_usuarios.js'?>"></script>
	<script type="text/javascript">
		$(function(e) {
			$('a.eliminar').on('click', function(e) {
		    	var ruta = "<?= $ruta_raiz.'/contratistas/modal_contratos_supervisores_delete.php?id=' ?>"
				var id = $(this).data('id');
				var supervisor = $(this).data('supervisor');
				var resultado = confirm("Esta seguro que desea eliminar el supervisor: "+supervisor);
				if (resultado) 
				{
					window.location.href = ruta+id;
				}

				e.preventDefault();
			});
		});
	</script>
</body>
