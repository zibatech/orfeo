<?php
session_start();
define('ADODB_ASSOC_CASE', 1);
$ruta_raiz = "..";
$ADODB_COUNTRECS = false;
include_once("$ruta_raiz/processConfig.php");
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once("./solicitudes_sql.php");

$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

switch($_POST['servicio'])
{
	case 'ciudades':
		header('Content-Type: application/json');
		echo json_encode(ciudades($db, $_POST['id_depto']));
	break;
	case 'ciudades_tx':
		header('Content-Type: application/json');
		echo json_encode(ciudades_tx($db, $_POST['depto']));
	break;
	case 'entidades':
		header('Content-Type: application/json');
		echo json_encode(entidades($db, $_POST['id_tipo']));
	break;
	case 'ips':
		header('Content-Type: application/json');
		echo json_encode(ips($db, $_POST['dpto_muni_codi']));
	break;
	default:
		header('Content-Type: application/json'); 
		echo json_encode([]);
	break;
}