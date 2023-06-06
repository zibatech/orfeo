<?php 
error_reporting(E_ALL);
require_once __DIR__.'/Zidoc.php';
require_once __DIR__.'/../processConfig.php';

$id_expediente = $_GET['id_exp'] ? $_GET['id_exp'] : '';

$zidoc = new Zidoc($signature_zidoc, $api_zidoc);
try {
	$resultado = $zidoc->documentos($id_expediente);
} catch (RespuestaInvalidaException $rie) {
	$resultado = null;
}

$columnas = [
	'Archivo',
	'Fecha',
	'Asunto',
	'NumeroRadicado',
	'TipoDoc',
	'folio_inicial',
	'folio_final',
	'observaciones'
]
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Zidoc</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
</head>
<body>
	<div class="container">
		<div class="row mt-3">
			<div class="col">
				<h3><?= $expediente ?></h3>
			</div>
		</div>
		<? if($resultado): ?>
				<div class="row mt-3">
					<div class="col">
						<table class="table">
							<thead>
								<tr>
									<? foreach ($resultado as $registro): ?>
										<tr>
											<? foreach ($registro as $key => $value): ?>
												<? if(in_array($key, $columnas)): ?>
													<td class="table-primary"><?= ucfirst(implode(' ',preg_split('/(?=[A-Z])/', $key))) ?></td>
												<? endif ?>
											<? endforeach ?>
										</tr>
										<?php break; ?>
									<? endforeach ?>
								</tr>
							</thead>
							<tbody>
								<? foreach ($resultado as $registro): ?>
									<tr>
										<? foreach ($registro as $key => $value): ?>
											<? if(in_array($key, $columnas)): ?>
												<td><small><?= $key == 'Archivo' ? '<a download target="_blank" href="https://zidoc.crautonoma.gov.co/zidoc/uploads-doc/'.$value.'" target="_blank"><i class="bi bi-cloud-arrow-down"></i></a>' : $value ?></small></td>
											<? endif ?>
										<? endforeach ?>
									</tr>
								<? endforeach ?>
							</tbody>
						</table>	
					</div>	
				</div>
			<? if(count($resultado) == 0): ?>
				<div class="row mt-3">
					<div class="col">
						<div class="alert alert-warning alert-dismissible fade show" role="alert">
							<strong>Atención!</strong> No se encontró ningún resultado.
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					</div>
				</div>
			<? endif ?>
		<? else: ?>
			<div class="row mt-3">
				<div class="col">
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>Error!</strong>  El API de Zidoc no responde o no esta disponible.
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				</div>
			</div>
		<? endif; ?>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
</body>
</html>