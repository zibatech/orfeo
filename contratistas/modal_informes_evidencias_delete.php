<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/InformeEvidencia.php');
$evi = $_GET['evi'];
$inf = $_GET['inf'];

$informe = Informe::find($inf);

$evidencia = InformeEvidencia::find($evi);
if ($evidencia)
	$evidencia->delete();

header('Location: '.$ruta_raiz.'/contratistas/modal_informes.php?cod='.$informe->contrato_id.'&inf='.$informe->id);