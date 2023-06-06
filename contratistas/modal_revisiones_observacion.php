<?php
session_start();
error_reporting(E_ALL);

$ruta_raiz = "..";
require_once($ruta_raiz.'/vendor/autoload.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');
$db = new ConnectionHandler($ruta_raiz);

$informe_id = $db->conn->getOne('SELECT informe_id FROM contratistas_informe_supervisor WHERE id = ?', [$_POST['id']]);

$db->conn->Execute('INSERT INTO contratistas_informe_supervisor_observaciones (informe_supervisor_id, usuario_id, observaciones, leido, fecha_creacion) VALUES (?,?,?,?,?)', [
		$_POST['id'],
		$_SESSION['usua_id'],
		$_POST['observacion'],
		'false',
		date('Y-m-d H:i:s')
	]);

if($_POST['redirect'] == '1')
	header('Location: '.$ruta_raiz.'/contratistas/modal_observaciones.php?id='.$_POST['id']);
else
	header('Location: '.$ruta_raiz.'/contratistas/modal_revisiones.php?inf='.$informe_id);
