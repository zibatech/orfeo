<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/Contrato.php');

$contrato = Contrato::find($_GET['cod']);

if ($contrato)
{
	$usuario_id = $contrato->usuario_id;
	$contrato->delete();
}

header('Location: '.$ruta_raiz.'/contratistas/contratos.php?usuario='.$usuario_id);
?>