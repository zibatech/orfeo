<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/ContratoSupervisor.php');
$debug = false;

if($debug)
{
	echo '<pre>';
		var_dump($_POST);
	echo '</pre>';
	echo '<pre>';
		var_dump($_FILES);
	echo '</pre>';
	exit();
}

$_SESSION['upload_errors'] = [];

$contrato = Contrato::find($_POST['contrato_id']);


if ($contrato && !ContratoSupervisor::findByContractSupervisor($_POST['contrato_id'], $_POST['usuario']))
{
	$supervisor = new ContratoSupervisor;
	$supervisor->contrato_id = $contrato->id;
	$supervisor->usuario_id = $_POST['usuario'];
	$supervisor->save();
}

header('Location: '.$ruta_raiz.'/contratistas/modal_contratos_supervisores.php?status=1&cod='.$contrato->id);