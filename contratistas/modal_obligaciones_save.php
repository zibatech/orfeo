<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/Obligacion.php');

if ($_POST['id'] == 0)
{
	$obligacion = new Obligacion;
} else {
	$obligacion = Obligacion::find($_POST['id']);
}

$obligacion->contrato_id = $_POST['contrato_id'];
$obligacion->descripcion = $_POST['descripcion'];
$obligacion->numero = $_POST['numero'];
$obligacion->save();

header('Location: '.$ruta_raiz.'/contratistas/modal_obligaciones.php?status=1&cod='.$obligacion->contrato_id);
?>