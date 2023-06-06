<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/Obligacion.php');

$obligacion = Obligacion::find($_GET['cod']);

if ($obligacion)
	$contrato_id = $obligacion->contrato_id;
	$obligacion->delete();



header('Location: '.$ruta_raiz.'/contratistas/modal_obligaciones.php?status=1&cod='.$contrato_id);
?>