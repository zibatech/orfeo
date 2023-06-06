<?php 
error_reporting(E_ALL);
require_once __DIR__.'/Zidoc.php';
require_once __DIR__.'/../processConfig.php';

$expediente = $_GET['exp'] ? $_GET['exp'] : '';
$nombre = $_GET['nom'] ? $_GET['nom'] : '';
$referencia = $_GET['ref'] ? $_GET['ref'] : '';
$consecutivo= $_GET['con'] ? $_GET['con'] : '';

$zidoc = new Zidoc($signature_zidoc, $api_zidoc);
try {
	$resultado_expediente = $expediente != '' ? 
		$zidoc->buscar('NumeroExpediente', $expediente) : [];

	$resultado_nombre = $nombre != '' ? 
		$zidoc->buscar('nombre_expediente', $nombre) : [];

	$resultado_referencia = $referencia != '' ? 
		$zidoc->buscar('ReferenciaDoc', $referencia) : [];

	$resultado_consecutivo = $consecutivo != '' ? 
		$zidoc->buscar('ConsecutivoInicial', $consecutivo) : [];
} catch (RespuestaInvalidaException $rie) {
	$resultado = null;
}

header('Content-Type: application/json; charset=utf-8');

echo json_encode([
	$resultado_expediente,
	$resultado_nombre,
	$resultado_referencia,
	$resultado_consecutivo
]);