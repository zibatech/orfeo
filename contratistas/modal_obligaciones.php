<?php
session_start();

$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/permisos.php');
require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/Obligacion.php');
$id_contrato = isset($_GET['cod']) ? $_GET['cod'] : '0';
$id_obligacion = isset($_GET['obl']) ? $_GET['obl'] : '0';

$contrato = Contrato::find($id_contrato);
$obligacion = Obligacion::find($id_obligacion);

$obligaciones_contrato = Obligacion::getFromContract($contrato->id);

if(!puede_administrar_contratos() && !puede_administrar_sus_contratos())
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
	<title>Obligaciones</title>
</head>
<body>
	<div class="container-fluid">
		<form action="modal_obligaciones_save.php" method="post">
			<?php if(isset($_GET['status']) && $_GET['status'] == '1'): ?>
			<div class="row">
				<div class="col mt-3">
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<h4 class="alert-heading">Bien!</h4>
						<p>Se actualizaron las obligaciones satisfactoriamente.</p>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>		
				</div>
			</div>
			<?php endif; ?>
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
				<div class="col-2 mt-3">
					<label for="numero" class="form-label">Número</label>
					<input type="number" class="form-control" min="1" max="100" step="1" id="numero" name="numero" aria-describedby="Número" value="<?= $obligacion ? $obligacion->numero : ''?>" required>
				</div>
			</div>
			<div class="row">
				<div class="col mt-3">
					<label for="descripcion" class="form-label">Obligación</label>
					<textarea class="form-control" rows="2" id="descripcion"  name="descripcion" required><?= $obligacion ? $obligacion->descripcion : ''?></textarea>
				</div>
			</div>
			<div class="row">
				<div class="col mt-3">
					<input type="hidden" name="id" value="<?= $obligacion ? $obligacion->id : '0'?>">
					<input type="hidden" name="contrato_id" value="<?= $obligacion ? $obligacion->contrato_id : $contrato->id ?>">
					<button class="btn btn-primary" type="submit">
						<i class="bi bi-save"></i> Guardar
					</button>
				</div>
			</div>
		</form>
		<div class="row">
			<div class="col mt-3 mb-3">
				<table class="table table-sm">
					<thead>
						<tr>
							<th width="30"></th>
							<th width="30">#</th>
							<th>Descripción</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($obligaciones_contrato as $key => $obligacion): ?>
						<tr>
							<td>
								<div class="dropdown">
									<button class="btn btn-sm btn-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
										<i class="bi bi-menu-down"></i>
									</button>
									<ul class="dropdown-menu">
										<li>
											<a href="<?= $ruta_raiz.'/contratistas/modal_obligaciones.php?cod='.$obligacion->contrato_id.'&obl='.$obligacion->id ?>" class="dropdown-item" class="btn btn-light">
												<i class="bi bi-pencil"></i> Editar
											</a>
										</li>
										<li>
											<a href="#" data-id="<?= $obligacion->id ?>" data-numero="<?= $obligacion->numero ?>" class="eliminar dropdown-item">
												<i class="bi bi-trash"></i> Eliminar
											</a>
										</li>
									</ul>
								</div>
							</td>
							<td><small><?= $obligacion->numero ?></small></td>
							<td><small><?= $obligacion->descripcion ?></small></td>
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
	<script type="text/javascript">
		$(function() {
		    $('a.eliminar').on('click', function(e) {
		    	var ruta = "<?= $ruta_raiz.'/contratistas/modal_obligaciones_delete.php?cod=' ?>"
				var numero = $(this).data('numero');
				var id = $(this).data('id');
				var resultado = confirm("Esta seguro que desea eliminar la obligación: "+numero);
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