<?php
session_start();

$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/permisos.php');
if(!puede_administrar_contratos())
	header('Location: '.$ruta_raiz.'/contratistas/sin_permisos.php');

require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');

$db = new ConnectionHandler($ruta_raiz);

$contratos = [];
$usuario = null;

if(isset($_GET['usuario']))
{
	$contratos = Contrato::getFromUser($_GET['usuario']);
	$usuario = $db->conn->getRow('SELECT * FROM usuario WHERE id = ?', $_GET['usuario']);
}

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
	<title>Contratos</title>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col">
				<h2>Contratos</h2>
			</div>
		</div>

		<form method="get">
			<div class="row">
				<div class="col-6">
					<select class="form-control buscador_usuario" name="usuario" placeholder="Buscar" autocomplete="off">
	    			</select>
				</div>
				<div class="col">
					<button type="submit" class="btn btn-light">
						<i class="bi bi-search"></i> Buscar
					</button>
					<button data-id="0" data-usu="<?= $usuario ? $usuario['ID'] : '' ?>" <?= !$usuario ? 'disabled' : '' ?> class="modal_contrato btn btn-primary">
						<i class="bi bi-plus"></i> Nuevo
					</button>
				</div>
			</div>
		</form>
		<?php if($usuario): ?>
			<div class="row">
				<div class="col mt-3">
					<p>
						<?= $usuario['USUA_NOMB'] ?><br>
						<small><?= $usuario['USUA_DOC'] ?></small>
					</p>
				</div>
			</div>
		<?php endif; ?>
		<div class="row">
			<div class="col mt-3">
				<table class="table table-sm">
					<thead>
						<tr>
							<th width="30"></th>
							<th width="100">Contrato</th>
							<th>Objeto</th>
							<th width="100">Fecha inicio</th>
							<th width="100">Fecha fin</th>
							<th width="100">Valor</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($contratos as $key => $contrato): ?>
							<tr>
								<td>
									<div class="dropdown">
										<button class="btn btn-sm btn-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
											<i class="bi bi-menu-down"></i>
										</button>
										<ul class="dropdown-menu">
											<li><a class="modal_anexos dropdown-item" data-id="<?= $contrato->id ?>">Anexos</a></li>
											<li><a class="modal_supervisores dropdown-item" data-id="<?= $contrato->id ?>">Supervisores</a></li>
											<li><a class="modal_obligaciones dropdown-item" data-id="<?= $contrato->id ?>">Obligaciones</a></li>
											<li><hr class="dropdown-divider"></li>
											<li><button data-id="<?= $contrato->id ?>" data-usu="<?= $usuario ? $usuario['ID'] : '' ?>" class="modal_contrato dropdown-item"><i class="bi bi-pencil"></i> Editar</button></li>
											<li><a href="#" data-contrato="<?= $contrato->contrato ?>" data-id="<?= $contrato->id ?>" class="dropdown-item eliminar"><i class="bi bi-trash"></i> Eliminar</a></li>
										</ul>
									</div>
								</td>
								<td><small><?= $contrato->contrato ?></small></td>
								<td><small><?= $contrato->objeto ?></small></td>
								<td><small><?= $contrato->fecha_inicio ?></small></td>
								<td><small><?= $contrato->fecha_fin ?></small></td>
								<td style="text-align: right;"><small>$<?= number_format($contrato->valor, 0, ',', '.') ?></small></td>
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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.6/js/standalone/selectize.js" integrity="sha512-X6kWCt4NijyqM0ebb3vgEPE8jtUu9OGGXYGJ86bXTm3oH+oJ5+2UBvUw+uz+eEf3DcTTfJT4YQu/7F6MRV+wbA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="<?= $ruta_raiz.'/contratistas/js/modal.js'?>"></script>
	<script src="<?= $ruta_raiz.'/contratistas/js/buscador_usuarios.js'?>"></script>
	<script type="text/javascript">
		$(function() {

		    $('.modal_contrato').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');
		    	var usu = $(this).data('usu');

		    	if(id == 0)
		    		modal(ruta_raiz+'/contratistas/modal_contratos.php?usu='+usu, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    	else
		    		modal(ruta_raiz+'/contratistas/modal_contratos.php?usu='+usu+'&cod='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

		    $('.modal_obligaciones').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');

		    	modal(ruta_raiz+'/contratistas/modal_obligaciones.php?cod='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

		    $('.modal_anexos').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');

		    	modal(ruta_raiz+'/contratistas/modal_contratos_anexos.php?cod='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

		    $('.modal_supervisores').on('click', function(e) {
		    	e.preventDefault();
		    	var ruta_raiz = '<?= $ruta_raiz ?>';
		    	var id = $(this).data('id');

		    	modal(ruta_raiz+'/contratistas/modal_contratos_supervisores.php?cod='+id, 'location=no,height=900,width=600,scrollbars=no,status=no');
		    });

		    $('a.eliminar').on('click', function(e) {
		    	var ruta = "<?= $ruta_raiz.'/contratistas/modal_contratos_delete.php?cod=' ?>"
				var contrato = $(this).data('contrato');
				var id = $(this).data('id');
				var resultado = confirm("Esta seguro que desea eliminar el contrato: "+contrato);
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