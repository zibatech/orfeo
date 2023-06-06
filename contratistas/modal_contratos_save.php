<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/Contrato.php');

if ($_POST['id'] == 0)
{
	$contrato = new Contrato;
} else {
	$contrato = Contrato::find($_POST['id']);
}

$contrato->contrato = $_POST['contrato'];
$contrato->objeto = $_POST['objeto'];
$contrato->fecha_inicio = $_POST['fecha_inicio'];
$contrato->fecha_fin = $_POST['fecha_fin'];
$contrato->valor = $_POST['valor'];
$contrato->honorarios_mensuales = $_POST['honorarios_mensuales'];
$contrato->usuario_id = $_POST['usuario_id'];
$contrato->expediente = $_POST['expediente'];
$contrato->rp = $_POST['rp'];
$contrato->fecha_rp = $_POST['fecha_rp'];
$contrato->cdp = $_POST['cdp'];
$contrato->fecha_cdp = $_POST['fecha_cdp'];
$contrato->save();

header('Location: '.$ruta_raiz.'/contratistas/modal_contratos.php?status=1&usu='.$contrato->usuario_id.'&cod='.$contrato->id);
?>