<?
session_start();
	foreach ($_GET as $key => $valor)   ${$key} = $valor;
	foreach ($_POST as $key => $valor)   ${$key} = $valor;
	foreach ($_SESSION as $key => $valor)   ${$key} = $valor;
	include "../rec_session.php";	
include_once  "../include/db/ConnectionHandler.php";
$ruta_raiz = '..';
include ("../processConfig.php");
   	$db = new ConnectionHandler("..");	 
?>
<form name="form2" action="" method=post>
<?
if (!$dependencia_busq or intval($dependencia_busq) == 0) die ("<table class=borde_tab width='100%'><tr><td class=titulosError><center>Debe seleccionar una dependencia</center></td></tr></table>");
if($generarOrfeo)
{
   	//error_reporting(7);
	//$db->conn->debug=true;
	//print_r($GLOBALS);
	$rskrd=$db->conn->Execute("select depe_codi from usuario where usua_login like '$krd'");
	$dependencia=$rskrd->fields['DEPE_CODI'];
   	if (!defined('ADODB_FETCH_NUM'))	define('ADODB_FETCH_NUM',1);
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM; 
	
	// Si la variable $generar_listado_existente viene entonces este if genera la planilla existente
	$order_isql = " ORDER BY DE.DEPE_NOMB, R.RADI_NUME_RADI";
	include "./oracle_pdf.php";

	//$tim1=$fecha_ini." ".$hi.":".$mi." ".$ti;
	//$tim2=$fecha_fin." ".$hf.":".$mf." ".$tf;
	//$time1="TO_DATE('$tim1','yyyy/mm/dd HH:MI AM')";
	//$time2="TO_DATE('$tim2','yyyy/mm/dd HH:MI AM')";
	$to=0;
	$total_total=0;
//echo $_SESSION["usua_perm_impresion"];
	if($dependencia_busq!=99999){
	$pdf = new PDF('L','pt','legal');
	$pdf->lmargin = 0.2;
	//echo "entro2";
	$pdf->SetFont('helvetica','',8);
	$pdf->AliasNbPages();
	$head_table=array("RADICADO","TIPO","FECHA_RADICADO","ENTIDAD_REMITENTE","ASUNTO","FOLIOS","DIRIGIDO_A","HORA RECIBIDO","FECHA RECIBIDO","FIRMA RECIBIDO");
        $head_table_size=array(80,120,90,120,120,60,120,70,70,90);
	$attr=array('titleFontSize'=>8,'titleText'=>'');
	//$arpdf_tmp = "../bodega/pdfs/planillas/$dependencia_". date("Ymd_hms") . "_jhlc.pdf"; Comentariada Por HLP.
	$pdf->SetFont('helvetica','',8);
	
		/*
	NUMERO PLANILLA
	*/
	$pla=str_pad($db->conn->GenID('SECR_PLANILLAS'),6,"0",STR_PAD_LEFT);
	$plan=date('Y').$pla;
	
	$sqlFechaHoy=$db->conn->OffsetDate(0,$db->conn->sysTimeStamp);

	
	$pdf->numplanilla=$plan;
	$pdf->usuario = $usua_nomb;
	$pdf->fechi=date("Y-m-d");
	//$pdf->fechf=$tim2;
	$pdf->entidad_largo = $entidad_largo;
	$total_registros = 0;
	$pdf->lmargin = 0.2;
	$i_total3 = 0;
	$radini=$radin."2";
	if($radfin!="")$radfini=$radfin."2";
	else $radfini="";
	if($codus==0)$codusu="";
	else $codusu=$codus;
	$arpdf_tmp[0] = "../bodega/pdfs/reportes/".$dependencia."_".date("Ymd_hms")."_corres".$numrep.".pdf";
	$rsd=$db->conn->Execute("select depe_nomb from dependencia where depe_codi = $dependencia_busq");
	$dependencianomb=$rsd->fields[0];
	$pdf->dependencia = $dependencianomb;
	$depen=$dependencia_busq;

//	$sql_ip="insert into planillas(pla_codigo,usu_cre_codigo,dep_cre_codigo,pla_fec_creacion,est_codigo,cod_dep_destino,tip_planilla)values(".$plan.",".$_SESSION['codusuario'].",".$_SESSION['dependencia'].",".$sqlFechaHoy.",1,".$depen.",1)";
//	$rs_ip=$db->conn->Execute($sql_ip);
//

	include "../include/query/estadisticas/queryListado.php";	
	
	$query_t = $query . $where_isql1 . $where_isql2 .$order_isql;
	$query_tc = $queryc . $where_isql1 . $where_isql2 ;
	
	$rsc=$db->conn->Execute($query_tc);

	//$numf=$db->RowCount();
        $numf=$rsc->fields[0];
	$pdf->oracle_report($db,$query_t,false,$attr,$head_table,$head_table_size,$arpdf_tmp,0,$numf);
	$total_total= $pdf->numrows;
	$pdf->Output($arpdf_tmp[0]);
	$to=0;



					/*INSERTA RADICADO POR PLANILLA*/
				
				include "genradpla.php";		

}
	
	else{
	$P=1;
	$qur=$db->conn->Execute("select depe_codi from dependencia");
	while(!$qur->EOF){
		$dep[$P]=$qur->fields[0];
		$P++;
		$qur->MoveNext();
	}
	
	for($po=1;$po<=$P;$po++){
	$depen=$dep[$po];
	
	
					/*INSERTA ENCABEZADO PLANILLA*/
				$sqlFechaHoy=$db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
	
	include "$ruta_raiz/include/query/estadisticas/queryListado.php";	
		$query_t = $query . $where_isql1 . $where_isql2 .$order_isql;
		$query_tc = $queryc . $where_isql1 . $where_isql2;

	$rsc=$db->conn->getOne($query_tc);
	//$numt=$db->RowCount();
		$numt=$rsc;
			if($numt!=0){
			$pdf = new PDF('L','pt','legal');
	$pdf->lmargin = 0.2;
	$pdf->SetFont('helvetica','',8);
	$pdf->AliasNbPages();
//$db->conn->debug=true;
	$head_table=array("RADICADO","TIPO","FECHA_RADICADO","ENTIDAD_REMITENTE","ASUNTO","FOLIOS","DIRIGIDO_A","HORA RECIBIDO","FECHA RECIBIDO","FIRMA RECIBIDO");
	$head_table_size=array(80,120,90,120,120,60,120,70,70,90);
	$attr=array('titleFontSize'=>8,'titleText'=>'');
	$pdf->SetFont('helvetica','',8);
	
	/*
	NUMERO PLANILLA
	*/
	$pla=str_pad($db->conn->GenID('SECR_PLANILLAS'),6,"0",STR_PAD_LEFT);
	$plan=date('Y').$pla;
	
	$pdf->numplanilla=$plan;
	$pdf->usuario = $usua_nomb;
	$pdf->fechi=date("Y-m-d");
	//$pdf->fechf=$tim2;
	$pdf->entidad_largo = $entidad_largo;
	$total_registros = 0;
	$pdf->lmargin = 0.2;
	$i_total3 = 0;
	$radini=$radin."2";
	if($radfin!="")$radfini=$radfin."2";
	else $radfini="";
	if($codus==0)$codusu="";
	else $codusu=$codus;
				$depe_busq=$dep[$po];
				$rsd=$db->conn->Execute("select depe_nomb from dependencia where depe_codi = $depe_busq");
				$dependencianomb=$rsd->fields[0];
				$pdf->dependencia = $dependencianomb;
				$depen=$depe_busq;
				
				/*INSERTA ENCABEZADO PLANILLA*/
				$sqlFechaHoy=$db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
				if($depen !=999 || $depen !='')
				{
//				$sql_ip="insert into planillas(pla_codigo,usu_cre_codigo,dep_cre_codigo,pla_fec_creacion,est_codigo,cod_dep_destino,tip_planilla)values(".$plan.",".$_SESSION['codusuario'].",".$_SESSION['dependencia'].",".$sqlFechaHoy.",1,".$depen.",1)";
//				
//				
//				$rs_ip=$db->conn->Execute($sql_ip);
				
				/*FIN*/
				}
				
				
				
				
				include "$ruta_raiz/include/query/estadisticas/queryListado.php";
				$query_t = $query . $where_isql1 . $where_isql2 .$order_isql;
				
				/*INSERTA RADICADO POR PLANILLA*/
				
//				include "genradpla.php";
								
				$arpdf_tmp[$to] = "../bodega/pdfs/reportes/".$dependencia."_".date("Ymd_hms")."_corres".$numrep.$to.".pdf";
				$pdf->oracle_report($db,$query_t,false,$attr,$head_table,$head_table_size,$arpdf_tmp,0,$numt);
				$total_registros = $pdf->numrows;
				$pdf->Output($arpdf_tmp[$to]);
				$total_total+=$total_registros;
				$to++;
				

				include "genradpla.php";

				/* ACTUALIZA ESTADO DE RADICACION*/
				/*
				$upradpla="update radicado set cod_planilla=1 where radi_nume_radi=".$rs_radpla->fields[0];
				$db->conn->Execute($upradpla);	
				*/


			}
		}
	}
	
	$arpdf_tmp[0];
	//$db->conn->debug=true;
	}
?>
		<TABLE BORDER=0 WIDTH=100% class="borde_tab">
		<TR><TD class="listado2"  align="center"><center>
Se han Generado <b><?=$total_total?>  Registros</b> <br>
<? if($dependencia_busq!=99999)$to=$to+1;
for($eo=0;$eo<$to;$eo++){ ?>
<a target="_blank" href='<?=$arpdf_tmp[$eo]?>' >Abrir planilla <?=$eo?> archivo PDF</a>
<br></center>
<?
}
/*$it=0;
	$rscont=$db->conn->Execute($query_t);	
	while(!$rscont->EOF){
			$radit[$it]=$rscont->fields[0];
			$it++;
			$rscont->MoveNext();
		}
		
for($cot=0;$cot<$it;$cot++){
		$ert="update radicado set radi_arch1=1 where  radi_nume_radi like '".$radit[$cot]."'";
		$wet=$db->query($ert);
}*/
?>
</form>
</td>
</TR>
</TABLE>
</body>
