<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/InformeObligacion.php');
require_once($ruta_raiz.'/contratistas/InformeEvidencia.php');

$inf = isset($_GET['inf']) ? $_GET['inf'] : 0;

$informe = Informe::find($inf);

if ($informe) {
	$contrato_id = $informe->contrato_id;

	$obligaciones_informe = InformeObligacion::getFromReport($informe->id);
	foreach ($obligaciones_informe as $obligacion) {
		$evidencias = InformeEvidencia::getFromObligation($obligacion_id);

		foreach($evidencias as $evidencia) {
			$evidencia->delete();
		}

		$obligacion->delete();
	}

	$informe->delete();
}

header('Location: '.$ruta_raiz.'/contratistas/informes.php?cod='.$contrato_id);
?>