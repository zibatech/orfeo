<?php

function paises($db) {
	$sql = "select distinct * from paises order by nombre";
	return $db->conn->getAll($sql);
}

function departamentos($db) {
	$sql = "select distinct * from departamento where id_pais=170 and dpto_codi not in (0, 1) order by dpto_nomb";
	return $db->conn->getAll($sql);
}

function ciudades($db, $id_depto) {
	$sql="select distinct * from municipio where dpto_codi = ? order by muni_nomb";
	return $db->conn->getAll($sql, $id_depto);
}

function ciudades_tx($db, $depto) {
	$sql="select distinct m.* from municipio m, departamento d where m.dpto_codi = d.dpto_codi and d.dpto_nomb = ? order by muni_nomb";
	return $db->conn->getAll($sql, $depto);
}

function ciudades_tx_all($db) {
	$sql="select distinct m.*, d.dpto_nomb from municipio m, departamento d where m.dpto_codi = d.dpto_codi order by d.dpto_nomb, m.muni_nomb";
	return $db->conn->getAll($sql);
}

function tipos_entidades($db) {
	$sql = "SELECT * FROM sgd_tipo_eps ORDER BY nombre_tipo";
	return $db->conn->getAll($sql);
}

function entidades($db, $id_tipo) {
	$sql = "SELECT * FROM sgd_eps WHERE tipo_vig_sns = ? ORDER BY nombre_eps";
	return $db->conn->getAll($sql, $id_tipo);
}

function tipos_documentos($db) {
	$sql = "SELECT * FROM tipo_doc_identificacion WHERE tdid_codi != '4' ORDER BY tdid_desc";
	return $db->conn->getAll($sql);
}

function ips($db, $dpto_muni_codi) {
	$sql = 'SELECT s.* FROM sgd_ips s WHERE s."depaMuni" = ? ORDER BY nombre';
	return $db->conn->getAll($sql, $dpto_muni_codi);
}