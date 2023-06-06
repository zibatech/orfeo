<?php
//Controlador para Ajax Comité Expertos <-Desarrollo de CRA->
//Actualizar el estado de RADICADO.COMITE_EXPERTOS
session_start();
$ruta_raiz = "../../"; 
if (!$_SESSION['dependencia'])
	header("Location: $ruta_raiz/cerrar_session.php");
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$numrad=$_POST['numRad'];
$isql = "select COMITE_EXPERTOS from RADICADO where radi_nume_radi=$numrad";
$rs=$db->conn->Execute($isql);
$checkVal=$rs->fields["COMITE_EXPERTOS"]==1?0:1;
$isql = "update RADICADO set COMITE_EXPERTOS=$checkVal where radi_nume_radi=$numrad";
$rs=$db->conn->Execute($isql);
echo json_encode(array("checkVal"=>$checkVal));
?>
