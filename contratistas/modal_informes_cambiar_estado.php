<?php
session_start();

$ruta_raiz = "..";
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');
require_once($ruta_raiz.'/contratistas/Estado.php');
require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/log.php');

$db = new ConnectionHandler($ruta_raiz);
$db->conn->Execute('UPDATE contratistas_informe_supervisor SET estado = ?, fecha = ? WHERE informe_id = ? AND usuario_id = ?', [$_GET['estado'], date('Y-m-d H:i:s'), $_GET['id'], $_SESSION['usua_id']]);

$total_verificaciones = $db->conn->getOne('SELECT count(id) FROM contratistas_informe_supervisor WHERE informe_id = ?', [$_GET['id']]);

$total_aprobadas = $db->conn->getOne('SELECT count(id) FROM contratistas_informe_supervisor WHERE informe_id = ? AND estado = ?', [$_GET['id'], InformeEstadoIndividual::$APROBADO]);

if ($_GET['estado'] == InformeEstadoIndividual::$MODIFICAR)
{
	$informe = Informe::find($_GET['id']);
	$informe->estado = InformeEstado::$MODIFICAR;
	$informe->save();
}

if ($total_verificaciones == $total_aprobadas)
{
	$informe = Informe::find($_GET['id']);
	$informe->estado = InformeEstado::$APROBADO;
	$informe->save();
	registrar_evento($db, $informe->id, 'Enviado a financiera');
}

header('Location: '.$ruta_raiz.'/contratistas/supervision.php');