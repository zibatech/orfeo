<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/Contrato.php');
require_once($ruta_raiz.'/contratistas/ContratoAnexo.php');
$id = $_GET['id'];

$anexo = ContratoAnexo::find($id);

if ($anexo) {
	$contrato = Contrato::find($anexo->contrato_id);
	$anexo->delete();
}

header('Location: '.$ruta_raiz.'/contratistas/modal_contratos_anexos.php?status=1&cod='.$contrato->id);