<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/include/db/ConnectionHandler.php');
require_once($ruta_raiz.'/contratistas/Contrato.php');
$db = new ConnectionHandler($ruta_raiz);

function puede_procesar_pagos() 
{
	return $_SESSION['contratistas_procesar_pagos'] >= 1 ? true : false;
}

function puede_administrar_contratos() 
{
	return $_SESSION['contratistas_administrar_contratos'] >= 1 ? true : false;
}

function puede_administrar_sus_contratos() 
{
	return $_SESSION['contratistas_mis_contratos'] >= 1 ? true : false;
}

function puede_certificar_contrato()
{
	return $_SESSION['contratistas_certificacion'] >= 1 ? true : false;
}

function es_supervisor_de_contrato()
{
	return $_SESSION['contratistas_supervision'] >= 1 ? true : false;
}

function tiene_acceso_a_contrato($contrato_id) 
{
	$contrato = Contrato::find($contrato_id);
	return $contrato->usuario_id == $_SESSION['usua_id'];
}