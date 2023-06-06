<?php
session_start();
define('ADODB_ASSOC_CASE', 1);
$ruta_raiz = "..";
$ADODB_COUNTRECS = false;
include_once("$ruta_raiz/processConfig.php");
include_once("$ruta_raiz/include/db/ConnectionHandler.php");


$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

switch($_POST['servicio'])
{
	case 'modificar_valido':
		header('Content-Type: application/json');
		$q = 'SELECT COUNT(*) as total FROM radicado WHERE radi_nume_radi = '.$_POST['radicado'];
		$total = $db->conn->getOne($q);
		echo json_encode(['total' => $total]);
	break;
	default:
		header('Content-Type: application/json'); 
		echo json_encode([]);
	break;
}