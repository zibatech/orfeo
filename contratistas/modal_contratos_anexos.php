<?php
session_start();

$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/permisos.php');
if(!puede_administrar_contratos())
	header('Location: '.$ruta_raiz.'/contratistas/sin_permisos.php');

require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/ContratoAnexo.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');
$db = new ConnectionHandler($ruta_raiz);

$id = isset($_GET['cod']) ? $_GET['cod'] : '0';

$contrato = Contrato::find($id);
$anexos = ContratoAnexo::getFromContract($contrato->id);
$expediente = $contrato->expediente;

$depe = intval(substr($expediente, 4, 5));
$serie = intval(substr($expediente, 9, 2));
$subserie = intval(substr($expediente, 11, 2));

$matriz_trd_expediente = $db->conn->GetAll('SELECT DISTINCT(sgd_tpr_codigo) FROM sgd_mrd_matrird WHERE depe_codi = ? and sgd_srd_codigo = ? and sgd_sbrd_codigo = ?', [$depe, $serie, $subserie]);

$id_tipos_documentos = [];
foreach($matriz_trd_expediente as $id_tipo_doc) 
{
	$id_tipos_documentos[] = $id_tipo_doc['SGD_TPR_CODIGO'];
}

$tipos_documentos = $db->conn->GetAll('SELECT sgd_tpr_descrip FROM sgd_tpr_tpdcumento WHERE sgd_tpr_codigo IN ('.implode(',', $id_tipos_documentos).')', []);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- CSS only -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
	<title>Anexos contrato <?= $contrato->nombre ?></title>
</head>
<body>
	<form action="modal_contratos_anexos_save.php" method="POST" enctype="multipart/form-data">
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
					<label for="tipo">Tipo</label>
					<select name="tipo" class="form-control" required>
						<option value="">Seleccionar</option>
						<?php foreach ($tipos_documentos as $key => $tipo): ?>
							<option value="<?= $tipo['SGD_TPR_DESCRIP'] ?>"><?= $tipo['SGD_TPR_DESCRIP'] ?></option>
						<?php endforeach ?>
					</select>
				</div>
				<div class="col mt-3">
					<label>Anexo</label>
					<input type="file" name="anexo" class="form-control" required>
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
			<?php if(count($anexos) > 0): ?>
				<div class="row">
					<div class="col">
						<table class="table table-sm">
							<thead>
								<tr>
									<th width="30"></th>
									<th width="150">Tipo</th>
									<th>Anexo</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($anexos as $key => $anexo): ?>
									<tr>
										<td>
											<a href="#" data-anexo="<?= $anexo->nombre ?>"  data-id="<?= $anexo->id ?>" class="eliminar btn btn-link btn-sm">
												<i class="bi bi-trash"></i>
											</a>
										</td>
										<td>
											<small style="line-height:30px;"><?= $anexo->tipo ?></small>
										</td>
										<td>
											<a class="btn btn-sm btn-default" href="<?= $ruta_raiz.'/bodega/contratistas/'.$anexo->archivo ?>">
												<i class="bi bi-cloud-arrow-down"></i> <?= $anexo->nombre ?>
											</a>
										</td>
									</tr>
								<?php endforeach ?>
							</tbody>
						</table>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</form>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
	<script type="text/javascript">
		$(function(e) {
			$('a.eliminar').on('click', function(e) {
		    	var ruta = "<?= $ruta_raiz.'/contratistas/modal_contratos_anexos_delete.php?id=' ?>"
				var id = $(this).data('id');
				var anexo = $(this).data('anexo');
				var resultado = confirm("Esta seguro que desea eliminar el anexo: "+anexo);
				if (resultado) 
				{
					window.location.href = ruta+id;
				}

				e.preventDefault();
			});
		});
	</script>
</body>
</html>