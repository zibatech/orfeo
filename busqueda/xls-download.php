<?php
$ruta_raiz="..";

$nomfile="argoBusqueda-".date("Y-m-d").".xlsx";
header("Content-type: application/msexcel; ");
header("Content-Disposition: filename=\"$nomfile\";");

	/*
	 echo "<pre>";
	var_dump(json_decode($_POST['data']));
	echo "<pre>";
	 */

$type=is_null($_POST['type'])?$_GET['type']:$_POST['type'];
		
if($type=='array'){
	$data=json_decode($_POST['data']);
	$headers=json_decode($_POST['hs']);
}
elseif($type=='sql'){
	session_start();

	$headers=['Radicado','Borrador','Fecha Radicación','Expediente','Asunto','Referencia','Tipo de Documento','Direccion contacto','Telefono contacto','Mail Contacto','Dignatario','Nombre','Documento','Usuario Actual',' Dependencia Actual',' Usuario Anterior','Dias Restante'];
	$data=array();





	include_once("$ruta_raiz/processConfig.php"); 			// incluir configuracion.
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	include_once("$ruta_raiz/adodb/adodb-paginacion.inc.php");
	$db = new ConnectionHandler("$ruta_raiz");
	if ($db){	
		$ADODB_COUNTRECS = false;
		$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);	
		$query=unserialize($_SESSION['xsql']);
		$rs=$db->conn->execute($query);
		//var_dump($rs->fields);
		while(!$rs->EOF){
			$dataArrayTmp=array();
			$fldRADI_NUME_RADI = $rs->fields['RADI_NUME_RADI'];
			$dataArrayTmp[]=$fldRADI_NUME_RADI;
			$fldRADI_FECH_RADI = $rs->fields['RADI_FECH_RADI'];
			$fldRADI_BORRADOR_RADI = $rs->fields['RADI_NUME_BORRADOR'];
			$dataArrayTmp[]=strlen($fldRADI_BORRADOR_RADI)>0 ? "'".$fldRADI_BORRADOR_RADI:'';
			$dataArrayTmp[]=$fldRADI_FECH_RADI;
			$seguridadRadicado = $rs->fields['SGD_SPUB_CODIGO'];
			$aRADI_DEPE_ACTU = $rs->fields['RADI_DEPE_ACTU'];
			$aRADI_USUA_ACTU = $rs->fields['RADI_USUA_ACTU'];
			$USUA_CODI_PROYECTO    = $rs->fields['RADI_USUA_RADI'];
			$DEPE_CODI_PROYECTO    = $rs->fields['RADI_DEPE_RADI'];

			$consultaExpediente = "SELECT exp.SGD_EXP_NUMERO, sexp.sgd_exp_privado  , u.usua_doc DOC_RESPONSABLE, u.DEPE_CODI DEPE_RESPONSABLE
				     FROM SGD_EXP_EXPEDIENTE exp,
		      sgd_sexp_secexpedientes sexp left join usuario u
					   on sexp.usua_doc_responsable=u.usua_doc
			 WHERE
			  exp.sgd_exp_estado != 2 and
			  exp.radi_nume_radi= $fldRADI_NUME_RADI
			  AND exp.sgd_exp_numero=sexp.sgd_exp_numero
			  AND exp.sgd_exp_fech=(SELECT MIN(exp2.SGD_EXP_FECH) as minFech from sgd_exp_expediente exp2 where exp2.radi_nume_radi= $fldRADI_NUME_RADI)";

			if ($seguridadRadicado == 0
				or ($seguridadRadicado == 1 && $_SESSION["dependencia"] == $aRADI_DEPE_ACTU)
				or ($seguridadRadicado == 2 && (($_SESSION["dependencia"] == $aRADI_DEPE_ACTU && ($_SESSION["USUA_JEFE_DE_GRUPO"] == true) || ($_SESSION["dependencia"] == $aRADI_DEPE_ACTU && $_SESSION["codusuario"] == $aRADI_USUA_ACTU)) || ($_SESSION["dependencia"] == $DEPE_CODI_PROYECTO && $_SESSION["codusuario"] == $USUA_CODI_PROYECTO) ) ) ) {
				$noPermisoFlag = 1;
			} else {
				$noPermisoFlag = 0;
			}
	//		$fldsSGD_EXP_SUBEXPEDIENTE = $rs->fields['SGD_EXP_NUMERO'];
			$rsE = $db->query($consultaExpediente);
			$fldsSGD_EXP_SUBEXPEDIENTE = $rsE->fields["SGD_EXP_NUMERO"];
			$dataArrayTmp[]=$fldsSGD_EXP_SUBEXPEDIENTE;
			$fldASUNTO = $rs->fields['RA_ASUN'];
			$dataArrayTmp[]=$noPermisoFlag == 1?$fldASUNTO:'';
			$fldCUENTAINTERNA = $rs->fields['RADI_CUENTAI'];
			$dataArrayTmp[]=$fldCUENTAINTERNA;
			$dataArrayTmp[]=$fldTIPO_DOC;

			if ($esNotificacionCircular) {
				$fldDESTINATARIOS = $rs->fields['SGD_NOTIF_CIRC_DEST_DESC'];
				$dataArrayTmp[]=$noPermisoFlag == 1?$fldDESTINATARIOS:'';
			} else {
				$fldDIRECCION_C = $rs->fields['SGD_DIR_DIRECCION'];
				$dataArrayTmp[]=$noPermisoFlag == 1?$fldDIRECCION_C:'';
				$fldDIGNATARIO = $rs->fields['SGD_DIR_NOMBRE'];
				$fldAPELLIDO = $rs->fields['SGD_DIR_APELLIDO'];
				$fldTELEFONO_C = $rs->fields['SGD_DIR_TELEFONO'];
				$dataArrayTmp[]=$noPermisoFlag == 1?$fldTELEFONO_C:'';
				$fldMAIL_C = $rs->fields['SGD_DIR_MAIL'];
				$dataArrayTmp[]=$noPermisoFlag == 1?$fldMAIL_C:'';
				$dataArrayTmp[]=$noPermisoFlag == 1?$fldDIGNATARIO:'';
				$fldNOMBRE = $rs->fields['SGD_DIR_NOMREMDES'];
				$dataArrayTmp[]=$noPermisoFlag == 1?$fldNOMBRE:'';
				$fldCEDULA = $rs->fields['SGD_DIR_DOC'];
				$dataArrayTmp[]=$noPermisoFlag == 1?$fldCEDULA:'';
				$tipoReg = $rs->fields['SGD_TRD_CODIGO'];
			    }
			$queryDep = "select DEPE_NOMB from dependencia where DEPE_CODI=$aRADI_DEPE_ACTU";
			$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
			$rs2 = $db->query($queryDep);
			$fldDEPE_ACTU = $rs2->fields['DEPE_NOMB'];

			$queryUs = "select USUA_NOMB from USUARIO where DEPE_CODI=$aRADI_DEPE_ACTU and USUA_CODI=$aRADI_USUA_ACTU ";
			$rs3 = $db->query($queryUs);
			$fldUSUA_ACTU = $rs3->fields['USUA_NOMB'];

			$dataArrayTmp[]=$fldUSUA_ACTU;
			$dataArrayTmp[]=$fldDEPE_ACTU;
			$dataArrayTmp[]=$fldUSUA_ANTE;
			$dataArrayTmp[]=$tprd!=0?$diasrestantes :'N/A ó termino no definido ';
			$data[]=$dataArrayTmp;
			$rs->moveNext();	
		}
	}
}

echo "
<html>
<meta charset=\"UTF-8\">
<body>
	<table border=1>
	<tr>";
foreach($headers as $h){
	echo "<td>$h</td>";
}
echo "</tr>";
foreach($data as $dataRow){
	if(substr($dataRow[0],0,1)=='3'){
		continue;
	}
	$dataRow[0]="'".$dataRow[0];
	if(substr($dataRow[1],0,1)=='3')
		$dataRow[1]="'".$dataRow[1];
	echo "<tr>";
	foreach($dataRow as $dc){
		echo "<td>$dc</td>";
	}
	echo "</tr>";
}
?>
	</table>
</body>
</html>
