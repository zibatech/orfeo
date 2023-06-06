<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/include/db/ConnectionHandler.php');
require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/Estado.php');
require_once($ruta_raiz.'/contratistas/ContratoSupervisor.php');
require_once($ruta_raiz.'/contratistas/log.php');

$informe = Informe::find($_GET['inf']);
$supervisores = ContratoSupervisor::getFromContract($informe->contrato_id);
$db = new ConnectionHandler($ruta_raiz);

$supervisores_registrados = $db->conn->getOne('SELECT count(id) FROM contratistas_informe_supervisor WHERE informe_id = ?', [$informe->id]);

if ($supervisores_registrados == 0)
{
	registrar_evento($db, $informe->id, 'Enviado a supervisión');
	foreach($supervisores as $supervisor)
	{
		$db->conn->Execute('INSERT INTO contratistas_informe_supervisor (informe_id, usuario_id, estado) VALUES (?, ?, ?)', [$informe->id, $supervisor->usuario_id, InformeEstadoIndividual::$PENDIENTE]);
	}
}

$informe->estado = InformeEstado::$REVISION;
$informe->save();

header('Location: '.$ruta_raiz.'/contratistas/informes.php?cod='.$informe->contrato_id);