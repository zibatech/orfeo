<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/ContratoSupervisor.php');
$id = $_GET['id'];

$supervisor = ContratoSupervisor::find($id);

if ($supervisor) {
	$contrato = Contrato::find($supervisor->contrato_id);
	$supervisor->delete();
}

header('Location: '.$ruta_raiz.'/contratistas/modal_contratos_supervisores.php?status=1&cod='.$contrato->id);