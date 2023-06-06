<?
/*
CARLOS BARRERO 17-03-2010
INCLUYE RADICADOS POR PLANILLA GENERADA
*/
$rs_radpla=$db->conn->Execute($query_t);
while(!$rs_radpla->EOF)
{
/*
VALIDA SI EL RADICADO CORRESPONDE A UNA RADICACION VIA WEB
*/
$sql_valida_web="SELECT COUNT(*) k 
FROM radicado 
WHERE radi_depe_radi='624' AND radi_usua_radi=26
AND radi_nume_radi=".$rs_radpla->fields[0];
$rs_valida_web=$db->conn->Execute($sql_valida_web);
/**/
if($rs_valida_web->fields['K']==0)
{

//	$ins_radpla="insert into rad_planilla(pla_codigo,radi_nume_radi,est_codigo,rad_fec_anexo,blo_planilla,rad_fec_registro,usua_codi,depe_codi,cod_externo,tip_externo)
//	values(".$plan.",".$rs_radpla->fields[0].",1,".$sqlFechaHoy.",1,".$sqlFechaHoy.",".$_SESSION['codusuario'].",".$_SESSION['dependencia'].",0,1)";
//	$db->conn->Execute($ins_radpla);

				$upradpla="update radicado set esta_codi='3',cod_planilla='2' where radi_nume_radi=".$rs_radpla->fields[0];
				$db->conn->Execute($upradpla);	

				$sqlInsHis="insert into HIST_EVENTOS (depe_codi,hist_fech,usua_codi,radi_nume_radi,usua_codi_dest,usua_doc,sgd_ttr_codigo,hist_doc_dest,depe_codi_dest,hist_obse) values ('".$_SESSION['dependencia']."',".$sqlFechaHoy.",'".$_SESSION['codusuario']."',".$rs_radpla->fields[0].",'".$_SESSION['codusuario']."','".$_SESSION['usua_doc']."',73,'".$_SESSION['usua_doc']."','".$_SESSION['dependencia']."','RADICADO INCLUIDO EN LA PLANILLA CDI No. ".$plan."')";
				$rsInsHis=$db->conn->Execute($sqlInsHis);

}
	$rs_radpla->MoveNext();
}
?>
