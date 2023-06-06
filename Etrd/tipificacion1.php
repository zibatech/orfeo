<?
  	error_reporting(1); 
  	$krdOld=$krd;
 	session_start(); 
 	if(!$krd) $krd = $krdOld;
 	$ruta_raiz = ".."; 
 	if(!$dependencia) 
 	{
 		include "$ruta_raiz/rec_session.php";
 		//echo $usua_doc;
 	}
 	
	if (!$nurad) $nurad= $rad;
	if($nurad)
	{
		$ent = substr($nurad,-1);
	}
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	
	$db = new ConnectionHandler("$ruta_raiz");
//$db->conn->debug = true;
	
	if (!defined('ADODB_FETCH_ASSOC')) define('ADODB_FETCH_ASSOC',2);
   	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
   	
	include_once "$ruta_raiz/include/tx/Historico.php";
    include_once ("$ruta_raiz/class_control/TipoDocumental.php");
    include_once "$ruta_raiz/include/tx/Expediente.php";

	$codusua = $codusuario;
 	$trd = new TipoDocumental($db);
	$params       = session_name()."=".session_id()."&krd=$krd";
	$nurad  = (isset($_POST['radicado'])) ? $_POST['radicado'] : null;

	if (isset($_POST['codepe'])){$codepe = $_POST['codepe'];}
	if (isset($_POST['codserie'])){$codserie = $_POST['codserie'];}
	if (isset($_POST['tsub'])){$tsub = $_POST['tsub'];}
	if (isset($_POST['tdoc'])){$tdoc = $_POST['tdoc'];}
	if (isset($_POST['insertar_registro'])){$insertar_registro = $_POST['insertar_registro'];}

	?>
<html>
<head>
<title>Tipificar Documento</title>
<link href="../estilos/CRA.bootstrap.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
<script>

function regresar(){   	
	document.TipoDocu.submit();
}

</script>

</head>
<body bgcolor="#FFFFFF">

<form action="tipificacion1.php?<?=$params?>" method="POST" enctype="multipart/form-data" name="TipoDocu" id="TipoDocu">
<br>
	<table border=0 width=75% align="center" class="borde_tab" cellspacing="0">
	  <tr align="center" class="titulos2">
	    <td height="15" class="titulos2">RADICADO AL CUAL SE APLICARA LA TIPIFICACION</td>
      </tr>
	 </table> 
<br>
 	<table width="75%" border="0" cellspacing="1" cellpadding="0" align="center" class="">

 	<tr align="left" colspan="2">
		<td width="31%" class='titulos5'>RADICADO</td>
		<td width="69%"  class='listado5' align="left">
		<input name="radicado" id="radicado" type="text"  class="tex_area" size="20" maxlength="14" value="<?php echo $nurad?>">
		<input name="Buscar" type="submit"  class="btn btn-primary" id="envia22" value="Buscar">&nbsp;&nbsp;
      <font style="font-family:verdana; font-size:x-small"><b>Nota:
			<font color="Blue">
			Recuerde seleccionar algun radicado de los que se listan
			</b></font></font>	
		</tr>
	 </table> 	
	 
	
	
<br>	 
	<table border=0 width="100%"% align="left" class="borde_tab" cellspacing="0">
	  <tr align="center" class="titulos2">
	    <td height="15" class="titulos2">APLICACION DE LA TRD</td>
      </tr></table> 
	 
<br> 
<table class="" width="100%" cellpadding="0" cellspacing="">
		<td width="31%" class='titulos5'>DEPENDENCIA</td>
		<td width="69%"  class='listado5' align="left">
		
		 <? 
$sql = "SELECT DEPE_NOMB, DEPE_CODI AS DEPE_CODI FROM DEPENDENCIA
				WHERE DEPENDENCIA_ESTADO=1
				 order by DEPE_NOMB";
	//$db->conn->debug=true;
	$rsDep = $db->conn->Execute($sql);
		if(!$s_DEPE_CODI) $s_DEPE_CODI= 0;
	print $rsDep->GetMenu2("codepe", $codepe, "0:-- Seleccione --", false,""," onChange='submit()'class='select'" );
	
	$isqlDepR = "SELECT depe_codi, dep_central from dependencia WHERE DEPE_CODI = '$codepe'";
	$rsDepR = $db->conn->Execute($isqlDepR);
	if ($rsDepR)
	{	$depcentral = $rsDepR->fields['DEP_CENTRAL'];
	}
	?>
	</td>
</tr>	
<tr >
	  <td class="titulos5" >SERIE</td>
	  <td>
        <?php
  
    if(!$tdoc) $tdoc = 0;
    if(!$codserie) $codserie = 0;
	if(!$tsub) $tsub = 0;
	$fechah=date("dmy") . " ". time("h_m_s");
	$fecha_hoy = Date("Y-m-d");
	$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
	$check=1;
	$fechaf=date("dmy") . "_" . time("hms");
	$num_car = 4;
	$nomb_varc = "s.sgd_srd_codigo";
	$nomb_varde = "s.sgd_srd_descrip";
   	include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
	$querySerie = "select distinct ($sqlConcat) as detalle, s.sgd_srd_codigo 
	         from sgd_mrd_matrird m, sgd_srd_seriesrd s
			 where m.depe_codi = '$depcentral'
			 	   and s.sgd_srd_codigo = m.sgd_srd_codigo
			       and m.sgd_mrd_esta=1
			 order by detalle
			  ";
	//$db->conn->debug=true;
	$rsD=$db->conn->query($querySerie);
	$comentarioDev = "Muestra las Series Documentales";
	include "$ruta_raiz/include/tx/ComentarioTx.php";
	print $rsD->GetMenu2("codserie", $codserie, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
 ?>   
      </td>
     </tr>
   <tr>
     <td class="titulos5" >SUBSERIE</td>
	 <td class=listado5 >
	<?
	$nomb_varc = "su.sgd_sbrd_codigo";
	$nomb_varde = "su.sgd_sbrd_descrip";
	include "$ruta_raiz/include/query/trd/queryCodiDetalle.php"; 
   	$querySub = "select distinct ($sqlConcat) as detalle, su.sgd_sbrd_codigo 
	         from sgd_mrd_matrird m, sgd_sbrd_subserierd su
			 where m.depe_codi = '$depcentral'
			       and m.sgd_srd_codigo = '$codserie'
				   and su.sgd_srd_codigo = '$codserie'
			       and su.sgd_sbrd_codigo = m.sgd_sbrd_codigo
			       and m.sgd_mrd_esta=1
 			       and ".$sqlFechaHoy." between su.sgd_sbrd_fechini and su.sgd_sbrd_fechfin
			 order by detalle
			  ";
   	//$db->conn->debug=true;
	$rsSub=$db->conn->query($querySub);
	include "$ruta_raiz/include/tx/ComentarioTx.php";
	print $rsSub->GetMenu2("tsub", $tsub, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );

?> 
     </td>
     </tr>
   <tr>
     <td class="titulos5" >TIPO DE DOCUMENTO</td>
 	 <td class=listado5 >
        <?
$ultdig=substr($nurad, -1);

        if ($ultdig==1) {
			$where.= " AND t.sgd_tpr_tp1=1";
			}elseif ($ultdig==2){
			$where.= " AND t.sgd_tpr_tp2=1";
			}
           
	$nomb_varc = "t.sgd_tpr_codigo";
	$nomb_varde = "t.sgd_tpr_descrip";
	include "$ruta_raiz/include/query/trd/queryCodiDetalle.php"; 
	$queryTip = "select distinct ($sqlConcat) as detalle, t.sgd_tpr_codigo 
	         from sgd_mrd_matrird m, sgd_tpr_tpdcumento t
			 where m.depe_codi = '$depcentral'
			       and m.sgd_mrd_esta = '1'
 			       and m.sgd_srd_codigo = '$codserie'
			       and m.sgd_sbrd_codigo = '$tsub'
 			       and t.sgd_tpr_codigo = m.sgd_tpr_codigo
 			       {$where}
	  			   order by detalle
			 ";
//$db->conn->debug=true;
	$rsTip=$db->conn->query($queryTip);
	$ruta_raiz = "..";
	include "$ruta_raiz/include/tx/ComentarioTx.php";
	print $rsTip->GetMenu2("tdoc", $tdoc, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
	
?> 	
		

    </td>
    </tr>
<tr>
     <td class="titulos5" >TERMINO</td>
 	 <td class=titulos5 >
        <?

	
	$queryTerm = "select  t.sgd_tpr_termino 
	         from sgd_tpr_tpdcumento t
			 where t.sgd_tpr_codigo = '$tdoc'";
//$db->conn->debug=true;
	$rsTerm=$db->conn->query($queryTerm);
	$termino = $rsTerm->fields["SGD_TPR_TERMINO"];
	echo "$termino Dias Habiles";
	
?> 	
		

    </td>
    </tr>    
 <tr>
     <td class="titulos5" >OBSERVACION (APLICA PARA MODIFICAR TRD)</td>
<td class="listado2" height="1"> <input type=text  id="desc" name="desc" size="100" value='<?=$desc?>'></textarea> </td>
		

    </td>
    </tr>   
       </table>
<br>
	<table border=0 width=70% align="center" class="borde_tab">
	  <tr align="center">
		<td width="33%" height="25" class="listado2" align="center">
         <center><input name="insertar_registro" type=submit class="btn btn-primary" value=" Insertar "></center></TD>
		 <td width="33%" class="listado2" height="25">
		 <center><input name="modificar" type=submit class="btn btn-success" value=" Modificar "></center></TD>
	   </tr>
	</table>
	<table width="70%" border="0" cellspacing="1" cellpadding="0" align="center" class="borde_tab">
	  <tr align="center">
	    <td>
		 	</td>
	   </tr>
	</table>

  <?
  /*
  * Adicion nuevo Registro
  */
  
if ($insertar_registro && $tdoc !=0 && $tsub !=0 && $codserie !=0 )
{
	include_once("../include/query/busqueda/busquedaPiloto1.php");
 	$sql = "SELECT $radi_nume_radi AS RADI_NUME_RADI 
					FROM SGD_RDF_RETDOCF r 
					WHERE RADI_NUME_RADI = '$nurad'";
	//$db->conn->debug = true;
		$rs=$db->conn->query($sql);
		$radiNumero = $rs->fields["RADI_NUME_RADI"];
		if ($radiNumero !='') {
		   $codserie = 0 ;
  		   $tsub = 0  ;
  		   $tdoc = 0;
		   echo $mensaje_err = "<HR>
		   <center><B><FONT COLOR=RED>
		   	Ya existe una Clasificacion para este radicado
		   	</FONT></B></center>
		   	<HR>";
		  }
		else
			{
  						
		$isqlTRD = "select SGD_MRD_CODIGO 
					from SGD_MRD_MATRIRD 
					where DEPE_CODI = '$depcentral'
				 	   and SGD_SRD_CODIGO = '$codserie'
				       and SGD_SBRD_CODIGO = '$tsub'
					   and SGD_TPR_CODIGO = '$tdoc'"; 
			$rsTRD = $db->conn->Execute($isqlTRD);
			$i=0;
			while(!$rsTRD->EOF)
			{
	    		$codiTRDS[$i] = $rsTRD->fields['SGD_MRD_CODIGO'];
				$codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];
	    		$i++;
				$rsTRD->MoveNext();
			}    
		
			$radicados = $trd->insertarTRD($codiTRDS,$codiTRD,$nurad,$dependencia, $codusua);
			//$db->conn->debug = true;
		    $TRD = $codiTRD;
			include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";
			$sqlH = "SELECT $radi_nume_radi RADI_NUME_RADI
					FROM SGD_RDF_RETDOCF r 
					WHERE r.RADI_NUME_RADI = '$nurad'
				    AND r.SGD_MRD_CODIGO =  '$codiTRD'";
			$rsH=$db->conn->query($sqlH);
			$i=0;
			while(!$rsH->EOF)
			{
	    		$codiRegH[$i] = $rsH->fields['RADI_NUME_RADI'];
	    		$i++;
				$rsH->MoveNext();
			}
  		  	$Historico = new Historico($db);		  
  		  	$observa="Tipificar Documento";
			$radiModi = $Historico->insertarHistorico($codiRegH, $dependencia, $codusuario, $dependencia, $codusuario, $observa, 32); 
		  	/*
		  	*Actualiza el campo tdoc_codi de la tabla Radicados
		  	*/
		 	$radiUp = $trd->actualizarTRD($codiRegH,$tdoc);
		
  			$codserie = 0 ;
  			$tsub = 0  ;
  			$tdoc = 0;
  				
		 }
  	}
	   
 
  if ($modificar && $tdoc !=0 && $tsub !=0 && $codserie !=0 )
			{
				$sqlH = "SELECT $radi_nume_radi RADI_NUME_RADI,
				        SGD_MRD_CODIGO 
						FROM SGD_RDF_RETDOCF r
						WHERE RADI_NUME_RADI = '$nurad'";
				//$db->conn->debug = true;
				$rsH=$db->conn->query($sqlH);
				$codiActu = $rsH->fields['SGD_MRD_CODIGO'];
				$i=0;
				while(!$rsH->EOF)
				{
	    			$codiRegH[$i] = $rsH->fields['RADI_NUME_RADI'];
	    			$i++;
					$rsH->MoveNext();
				}
				$TRD = $codiActu;
		        include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";
				
		  		$observa = "*Modificado TRD* ".$deta_serie."/".$deta_subserie."/".$deta_tipodocu."<BR>".$desc;
  		  		$Historico = new Historico($db);		  
  		  		$radiModi = $Historico->insertarHistorico($codiRegH, $dependencia, $codusuario, $dependencia, $codusuario, $observa, 32); 
		   		/*
		  		*Actualiza el campo tdoc_codi de la tabla Radicados
		  		*/
		 		$radiUp = $trd->actualizarTRD($codiRegH,$tdoc);
				$mensaje="Registro Modificadoo $desc";
				$isqlTRD = "select SGD_MRD_CODIGO 
					from SGD_MRD_MATRIRD 
					where DEPE_CODI = '$codepe'
				 	   and SGD_SRD_CODIGO = '$codserie'
				       and SGD_SBRD_CODIGO = '$tsub'
					   and SGD_TPR_CODIGO = '$tdoc'";
			
				$rsTRD = $db->conn->Execute($isqlTRD);
				$codiTRDU = $rsTRD->fields['SGD_MRD_CODIGO'];
				$sqlUA = "UPDATE SGD_RDF_RETDOCF SET SGD_MRD_CODIGO = '$codiTRDU',
						  USUA_CODI = '$codusua',
						  DEPE_CODI = '$codepe'
						  WHERE RADI_NUME_RADI = '$nurad' ";
				$rsUp = $db->conn->query($sqlUA); 
		$mensaje="Registro Modificado  $desc  <br> ";

		
		
} 	
			
		?>
		
<script>

</script>
	<TABLE class="borde_tab"><tr><td class="titulos4" width="100%" align="center">
   CLASIFICACION DEL RADICADO No. <?=$_POST['radicado']?></td>
	<br>
	<table class="table table-hover" width="100%" cellpadding="0" cellspacing="">
  	<tr class="tpar" align="center">
    	<td width="20%"  class="titulos3">SERIE</td>
		<td width="20%"  class="titulos3">SUBSERIE</td>
 		<td width="20%"  class="titulos3">TIPO DE DOCUMENTO</td>
    	<td width="20%"  class="titulos3">DEPENDENCIA</td>
  		</tr>
  		
 <?

$nurad=$_POST['radicado']; 	
if(!empty($_POST['Buscar'])&& ($_POST['Buscar']=="Buscar")){
	
$sql = "SELECT RADI_NUME_RADI
					FROM RADICADO 
					WHERE RADI_NUME_RADI = '$nurad'";
       //$db->conn->debug = true;
		$rs=$db->conn->query($sql);
		$radiNumero = $rs->fields["RADI_NUME_RADI"];
		if ($radiNumero =='') {
			?><script>  alert("NO EXISTE RADICADO, VERIFIQUE EL DATO INGRESADO");</script> <? $nurad="";} }

$sqlFechaDocto =  $db->conn->SQLDate("Y-m-D H:i:s A","mf.sgd_rdf_fech");
$sqlSubstDescS =  $db->conn->substr."(s.sgd_srd_descrip, 0, 30)";
$sqlSubstDescSu = $db->conn->substr."(su.sgd_sbrd_descrip, 0, 30)";
$sqlSubstDescT =  $db->conn->substr."(t.sgd_tpr_descrip, 0, 30)";
$sqlSubstDescD =  $db->conn->substr."(d.depe_nomb, 0, 30)";

include "$ruta_raiz/include/query/trd/querylista_tiposAsignados.php"; 
$isqlC = 'select 
			  '. $sqlConcat .  '      AS "CODIGO" 
			, '. $sqlSubstDescS .  '  AS "SERIE" 
			, '. $sqlSubstDescSu .  ' AS "SUBSERIE" 
			, '. $sqlSubstDescT .  '  AS "TIPO_DOCUMENTO" 
			, '. $sqlSubstDescD .  '  AS "DEPENDENCIA"
			, m.sgd_mrd_codigo        AS "CODIGO_TRD"
			, mf.usua_codi             AS "USUARIO"
			, mf.depe_codi             AS "DEPE"
			from 
				SGD_RDF_RETDOCF mf,
	   			SGD_MRD_MATRIRD m, 
	   			DEPENDENCIA d,
	   			SGD_SRD_SERIESRD s,
	   			SGD_SBRD_SUBSERIERD su, 
	   			SGD_TPR_TPDCUMENTO t
	   		where d.depe_codi     = mf.depe_codi 
	   			and s.sgd_srd_codigo  = m.sgd_srd_codigo 
	   			and su.sgd_sbrd_codigo = m.sgd_sbrd_codigo 
				and su.sgd_srd_codigo = m.sgd_srd_codigo
	  			and t.sgd_tpr_codigo  = m.sgd_tpr_codigo
	   			and mf.sgd_mrd_codigo = m.sgd_mrd_codigo
			    and mf.radi_nume_radi = '. $nurad;
     error_reporting(0);
     //$db->conn->debug = true;

	 	$rsC=$db->query($isqlC);
 	
   		while(!$rsC->EOF)
			{
      			$dserie   =$rsC->fields["SERIE"]; 
				$dsubser  =$rsC->fields["SUBSERIE"];
				$dtipodo  =$rsC->fields["TIPO_DOCUMENTO"];
                $ddepend  =$rsC->fields["DEPENDENCIA"];
				$codiTRDEli  =$rsC->fields["CODIGO_TRD"];
				$codiTRDModi =$codiTRDEli;
				
		?> 
				<td class="listado5"> <font size=2><?=$dserie?></font> </td>
				<td class="listado5"> <font size=2><?=$dsubser?></font> </td>
				<td class="listado5"> <font size=2><?=$dtipodo?></font> </td>
				<td class="listado5"> <font size=2><?=$ddepend?></font> </td>
	 			
	</font>
	</td>
	</tr>
	
	<?
				
	if ($dserie=="" and $insertar_registro){
		
		echo $mensaje_err . "<HR> <center><B><FONT COLOR=RED>  RADICADO SIN TIPIFICAR	</FONT></B></center><HR>";
	}?> 	
</table>


<!--  Consulta de TRD AUTOMATICO --> 
<table class=borde_tab width='100%' cellspacing="5"><tr><td class=titulos2><center><p class="text-warning" >RADICADOS SIN TRD</p></center></td></tr></table>
	<br>
	<table class="table table-hover"  width="100%" cellpadding="0" cellspacing="">
  	<tr class="tpar" align="center">
    	<td width="10%"  class="titulos3">RADICADO</td>
		<td width="7%"  class="titulos3">FECHA</td>
 		<td width="55%"  class="titulos3">ASUNTO</td>
		<td width="10%"  class="titulos3">Dependencia</td>
    	<td width="5%"  class="titulos3">Seleccion</td>
  		</tr>
  		
 <?

$nurad=$_POST['radicado']; 	

//require_once($ruta_raiz."include/myPaginador.inc.php");
$encabezado = "depedencia=$dep&fechainicio=$fecha_ini&fechafinal=$fecha_fin";
$linkPagina = "$PHP_SELF?$encabezado&orderTipo=$orderTipo&orderNo=";


	
$sql = "SELECT RADI_NUME_RADI
					FROM RADICADO 
					WHERE RADI_NUME_RADI = '$nurad'";
					
       //$db->conn->debug = true;
		$rs=$db->conn->query($sql);
		$radiNumero = $rs->fields["RADI_NUME_RADI"];
		
//$sqlFechaDocto =  $db->conn->SQLDate("Y-m-D H:i:s A","mf.sgd_rdf_fech");
//$sqlSubstDescS =  $db->conn->substr."(s.sgd_srd_descrip, 0, 30)";
//$sqlSubstDescSu = $db->conn->substr."(su.sgd_sbrd_descrip, 0, 30)";
//$sqlSubstDescT =  $db->conn->substr."(t.sgd_tpr_descrip, 0, 30)";
//$sqlSubstDescD =  $db->conn->substr."(d.depe_nomb, 0, 30)";
//include "$ruta_raiz/include/query/trd/querylista_tiposAsignados.php"; 


$isqlC = "SELECT RADI_NUME_RADI,RADI_FECH_RADI,RA_ASUN,RADI_PATH,D.DEPE_NOMB AS NOMBRE
FROM RADICADO R , DEPENDENCIA D
WHERE TDOC_CODI=0
and substr(radi_nume_radi, 14, 1)=2
and radi_depe_radi not in(900,999)
AND D.DEPE_CODI=radi_depe_radi
AND RADI_FECH_RADI >= (TO_DATE('2013-11-21, 12:00:00 AM', 'RRRR-MM-DD, HH:MI:SS AM'))";




     error_reporting(0);
     //$db->conn->debug = true;

	 	$rsC1=$db->query($isqlC);
 	
   		while(!$rsC1->EOF)
			{
      			$dserie1   =$rsC1->fields["RADI_NUME_RADI"]; 
				$dsubser1  =$rsC1->fields["RADI_FECH_RADI"];
				$dtipodo1  =$rsC1->fields["RA_ASUN"];
                $path      =$rsC1->fields["RADI_PATH"];
				$depe      =$rsC1->fields["NOMBRE"];
	            $var1='/orfeonew/bodega/'.$path ;
				//$var2='/orfeo1/bodega'.$path ;
				//$var1= $ruta_raiz.$var2 ;
				
				//echo $var1;
		?> 
		
				<td class="listado2"> <font size=2><a href='<?echo $var1?>'><?echo $dserie1?></a></font> </td>
				<td class="listado2"> <font size=2><?=$dsubser1?></font> </td>
				<td class="listado2"> <font size=2><?=$dtipodo1?></font> </td>
				<td class="listado2"> <font size=2><?=$depe?></font> </td>
				<td class="listado2"> <font size=2><CENTER><input type=radio onclick='javascript:document.TipoDocu.radicado.value="<? echo $dserie1 ?>";'></CENTER> </font> </td>
				
 <?
 //if ($OK == "RADI_NUM_RADI") {
//echo "<B>RADICADO SELECCIONADO ES  =</B>".$RADI_NUME_RADI"<br>";
//};
?> 



	</font>
	</td>
	</tr>
	
	<?
				$rsC1->MoveNext();
/*
	$pager = new ADODB_Pager($db,$isqlC,'adodb', true,$adodb_next_page,$orderTipo);
		$pager->checkAll = false;
        $pager->checkTitulo = true;
		$pager->toRefLinks = $linkPagina;
		$pager->toRefVars = $encabezado;
		$pager->Render($rows_per_page=20,$linkPagina,$checkbox=chkEnviar);
		*/
	}	
	$rsC->MoveNext();
			}
			 
	
	?> 



	
</form>
</span>
</span>
</body>
</html>
