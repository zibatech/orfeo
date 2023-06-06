<?php
session_start();
error_reporting(E_ALL);

function registrar_evento($db, $informe_id, $descripcion) 
{
	$db->conn->Execute('INSERT INTO contratistas_log (informe_id, usuario_id, fecha, descripcion) VALUES (?, ?, ?, ?)', [$informe_id, $_SESSION['usua_id'], date('Y-m-d H:i:s'), $descripcion]);
}

function obtener_eventos($db, $informe_id)
{
	$eventos = $db->conn->GetAll('SELECT * FROM contratistas_log WHERE informe_id = ? ORDER BY fecha', [$informe_id]);

	return $eventos;
}