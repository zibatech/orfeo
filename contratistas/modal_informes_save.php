<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/Estado.php');
require_once($ruta_raiz.'/contratistas/InformeObligacion.php');
require_once($ruta_raiz.'/contratistas/InformeEvidencia.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');

$db = new ConnectionHandler($ruta_raiz);

$debug = false;

if($debug)
{
	echo '<pre>';
		var_dump($_POST);
	echo '</pre>';
	echo '<pre>';
		var_dump($_FILES);
	echo '</pre>';

	$id = 4;
	foreach ($_FILES['obligacion_evidencias']['name'][$id] as $key => $evidencia) {
		$file = [
			'filename' => $evidencia,
			'type' => $_FILES['obligacion_evidencias']['type'][$id][$key],
			'tmp_name' => $_FILES['obligacion_evidencias']['tmp_name'][$id][$key],
			'error' => $_FILES['obligacion_evidencias']['error'][$id][$key],
			'size' => $_FILES['obligacion_evidencias']['size'][$id][$key],
		];

		echo '<pre>';
			var_dump($file);
		echo '</pre>';
	}
	
	exit();
}

if ($_POST['id'] == 0)
{
	$informe = new Informe;
	$informe->estado = InformeEstado::$BORRADOR;
} else {
	$informe = Informe::find($_POST['id']);
}

$informe->contrato_id = $_POST['contrato_id'];
$informe->numero = $_POST['numero'];
$informe->fecha_inicio = $_POST['fecha_inicio'];
$informe->fecha_fin = $_POST['fecha_fin'];
$informe->valor_ejecutado = $_POST['valor_ejecutado'];
$informe->total_ejecutado = $_POST['total_ejecutado'];
$informe->fecha_informe = date('Y-m-d');
$informe->save();


function tieneRegistrosParaDocumentosDePago($db, $informe) {
	$numero_de_registros = $db->conn->getOne('SELECT count(id) FROM contratistas_informe_requerimiento WHERE informe_id = ?', [$informe->id]);

	return $numero_de_registros > 0;
}

function crearRegistrosParaDocumentosDePago($db, $informe) {
	if (!tieneRegistrosParaDocumentosDePago($db, $informe))
	{
		$documentos_requeridos_pago = $db->conn->GetAll('SELECT * FROM contratistas_requerimientos_pago WHERE activo = true');

		foreach($documentos_requeridos_pago as $documento)
		{
			$db->conn->Execute('INSERT INTO contratistas_informe_requerimiento (informe_id, requerimiento_id) VALUES (?, ?)', [$informe->id, $documento['ID']]);
		}
	}
}

crearRegistrosParaDocumentosDePago($db, $informe);

$_SESSION['upload_errors'] = [];

foreach($_POST['obligacion_descripcion'] as $id => $descripcion_obligacion) {
	$obligacion = InformeObligacion::findByReportObligation($informe->id, $id);
	
	if (!$obligacion) {
		$obligacion = new InformeObligacion;
	}

	$obligacion->informe_id = $informe->id;
	$obligacion->obligacion_id = $id;
	$obligacion->fecha_inicio = $_POST['obligacion_fecha_inicio'][$id] == '' ? null : $_POST['obligacion_fecha_inicio'][$id];
	$obligacion->fecha_fin = $_POST['obligacion_fecha_fin'][$id] == '' ? null :  $_POST['obligacion_fecha_fin'][$id];
	$obligacion->descripcion = $descripcion_obligacion;

	$obligacion->save();

	foreach ($_FILES['obligacion_evidencias']['name'][$id] as $key => $evidencia) {

		$file = [
			'filename' => $evidencia,
			'type' => $_FILES['obligacion_evidencias']['type'][$id][$key],
			'tmp_name' => $_FILES['obligacion_evidencias']['tmp_name'][$id][$key],
			'error' => $_FILES['obligacion_evidencias']['error'][$id][$key],
			'size' => $_FILES['obligacion_evidencias']['size'][$id][$key],
		];


		if ($file['filename'] != '')
		{
			$evidencia = InformeEvidencia::upload($obligacion, $file);
			var_dump($evidencia);
			if (!$evidencia)
			{
				$_SESSION['upload_errors'][] = $file['filename'];
			}
		}
	}	
}

header('Location: '.$ruta_raiz.'/contratistas/modal_informes.php?status=1&cod='.$informe->contrato_id.'&inf='.$informe->id);
?>