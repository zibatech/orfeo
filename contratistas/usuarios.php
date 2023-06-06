<?php
session_start();
error_reporting(E_ALL);

$rutaRaiz = "..";

require_once("$rutaRaiz/include/db/ConnectionHandler.php");

$key = $_GET['q'];

$filters = [
	'%'.strtoupper($key).'%', 
	'%'.strtoupper($key).'%'
];

$dependencia = '';

if (isset($_GET['depe']))
{
	$dependencia = 'AND depe_codi = ?';
	$filters[] = $_GET['depe'];
}

$db = new ConnectionHandler($rutaRaiz);
$data = $db->conn->GetAll("SELECT id, usua_doc as doc, usua_nomb as nombre FROM usuario WHERE (UPPER(usua_doc) LIKE ? OR UPPER(usua_nomb) LIKE ? ) ".$dependencia." LIMIT 100", $filters);

header("Content-type: application/json");

echo json_encode([
	'results' => $data
]);