<?php

session_start();

$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/InformeRequerimiento.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');
require_once($ruta_raiz.'/contratistas/log.php');
$db = new ConnectionHandler($ruta_raiz);

$documento_requerido = InformeRequerimiento::find($_POST['id']);
$documento_requerido->upload($_FILES['adjunto']);

$adjuntos_pendientes = $db->conn->getOne('SELECT count(id) FROM contratistas_informe_requerimiento WHERE adjunto is null and informe_id = ?', [$documento_requerido->informe_id]);

if ($adjuntos_pendientes == 0)
{
	registrar_evento($db, $documento_requerido->informe_id, 'Enviado a dirección');
}

header('Location: '.$ruta_raiz.'/contratistas/modal_adjuntos_requeridos.php?status=1&inf='.$documento_requerido->informe_id);