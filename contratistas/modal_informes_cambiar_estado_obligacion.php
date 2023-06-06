<?php
session_start();

$ruta_raiz = "..";
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');
$db = new ConnectionHandler($ruta_raiz);

$sql = 'INSERT INTO contratistas_informe_obligacion_estado (obligacion_informe_id, supervisor_id, estado, observacion, fecha) VALUES (?,?,?,?,?)';

$datos = [
	$_POST['id'],
	$_SESSION['usua_id'],
	$_POST['accion'],
	'',
	date('Y-m-d H:i:s')
];

$db->conn->Execute($sql, $datos);

header('Location: '.$ruta_raiz.'/contratistas/modal_informes_consulta.php?status=1&cod='.$_POST['cod'].'&inf='.$_POST['inf']);