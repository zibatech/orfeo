<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/ContratoAnexo.php');
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

if ($contrato)
{
	$anexo = ContratoAnexo::upload($contrato, $_POST['tipo'], $_FILES['anexo']);
	if (!$anexo)
	{
		$_SESSION['upload_errors'][] = $_FILES['anexo']['name'];
	}
}

header('Location: '.$ruta_raiz.'/contratistas/modal_contratos_anexos.php?status=1&cod='.$contrato->id);