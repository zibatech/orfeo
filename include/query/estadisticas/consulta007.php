<?php    
/** CONSUTLA 001 
  * Estadiscas por usuario
  * @autor JAIRO H LOSADA Correlibre.org
  * @version ORFEO 3.1
  * 
  * Arreglo por LIliana Gomez 2012
  */
$coltp3Esp = '"'.$tip3Nombre[3][2].'"';
if(!$orno) $orno=2;
$tmp_substr = $db->conn->substr;
 /**
   * $db-driver Variable que trae el driver seleccionado en la conexion
   * @var string
   * @access public
   */
 /**
   * $fecha_ini Variable que trae la fecha de Inicio Seleccionada  viene en formato Y-m-d
   * @var string
   * @access public
   */
/**
   * $fecha_fin Variable que trae la fecha de Fin Seleccionada
   * @var string
   * @access public
   */
/**
   * $mrecCodi Variable que trae el medio de recepcion por el cual va a sacar el detalle de la Consulta.
   * @var string
   * @access public
   */

$ln=$_SESSION["digitosDependencia"];

if(!empty($tipoRadicado)){
    // $condicionDep = "AND b.depe_codi = $tipoRadicado";
    $condicionE   = "AND u3.USUA_CODI=$codUs $condicionDep ";
}

if(!empty($depeUs)){
    
    $condicionDep = "AND u3.depe_codi = $depeUs";
    $condicionE   = "AND u3.USUA_CODI = $codUs $condicionDep ";
}

switch($db->driver)
{
	case 'mssql':
	case 'postgresql':	
	case 'postgres':	
	{	
		$COD_RADICACION = 2;
		$COD_DIGITALIZACION = 42;
		$queryE = "SELECT 
			u3.usua_nomb as USUARIO,
			count(radi_usua_radi) as RADICADOS,
			MIN(u3.depe_codi) as HID_DEPE_USUA,
			MIN(u3.USUA_CODI) as HID_COD_USUARIO
		FROM dependencia df,dependencia da, RADICADO r
		LEFT OUTER JOIN SGD_TPR_TPDCUMENTO t ON r.tdoc_codi=t.SGD_TPR_CODIGO 
		LEFT OUTER JOIN SGD_DIR_DRECCIONES dir ON r.radi_nume_radi = dir.radi_nume_radi	and dir.sgd_dir_tipo = '1'
		LEFT JOIN hist_eventos he1 ON r.radi_nume_radi = he1.radi_nume_radi AND he1.sgd_ttr_codigo = ".$COD_DIGITALIZACION."
			LEFT JOIN hist_eventos he2 ON r.radi_nume_radi = he2.radi_nume_radi AND he2.sgd_ttr_codigo = ".$COD_RADICACION."
			LEFT JOIN USUARIO u1 ON u1.usua_codi = he1.usua_codi_dest and u1.depe_codi = he1.depe_codi_dest
			LEFT JOIN USUARIO u2 ON u2.usua_codi = he2.usua_codi_dest and u2.depe_codi = he2.depe_codi_dest
			LEFT JOIN USUARIO u3 ON u3.usua_codi = he2.usua_codi and u3.depe_codi = he2.depe_codi
			LEFT JOIN dependencia d3 ON d3.depe_codi = he2.depe_codi
		WHERE 
		r.radi_depe_actu=da.depe_codi AND
		r.RADI_DEPE_RADI=df.DEPE_CODI AND
		r.RADI_DEPE_RADI != 900 AND
		".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'  
		$whereTipoRadicado $whereDependencia
		AND r.radi_nume_radi::text LIKE '%2'
		group by 
		u3.id,
		u3.usua_codi,
		u3.depe_codi,
		u3.usua_nomb";
	$orderE = "	ORDER BY $orno $ascdesc";
		//echo $queryE;
 		/** CONSULTA PARA VER DETALLES 
         * Se incluye una nueva restriccion para que en el detalle unicamente 
         * muestre la direccion remitente/destinatario
         * Junio 14 2012
		 */
		 
		$queryEDetalle = "SELECT DISTINCT $radi_nume_radi as RADICADO
			,r.RADI_FECH_RADI as FECHA_RADICADO
			,t.SGD_TPR_DESCRIP as TIPO_DE_DOCUMENTO
			,r.RA_ASUN as ASUNTO 
			,r.RADI_DESC_ANEX 
			,r.RADI_NUME_HOJA 
			,r.RADI_NUME_FOLIO
			,r.RADI_PATH as HID_RADI_PATH
			,dir.SGD_DIR_NOMBRE || ' ' || dir.SGD_DIR_APELLIDO as REMITENTE
			,df.DEPE_NOMB as DEPE_NOMB
			,da.DEPE_NOMB as DEPE_NOMB_ACTUAL
			,da.DEPE_CODI as DEPE_CODI_ACTUAL
			,r.RADI_USU_ANTE
			,u2.usua_nomb AS USUA_NOMB_ACTUAL
			,r.radi_nume_anexo as NUM_ANEXOS
			,he1.hist_fech as FECHA_DIGITALIZACION
			,u1.usua_nomb as DIGITALIZADOR
			,u2.usua_nomb as USUARIO
			,u3.usua_nomb as RADICADOR
			,d3.DEPE_NOMB as DEPENDENCIA_RADICADOR
			,r.RADI_DEPE_RADI as COD_DEPE
			,mr.mrec_desc as MEDIO
			FROM dependencia df, dependencia da, RADICADO r
			LEFT OUTER JOIN SGD_TPR_TPDCUMENTO t ON r.tdoc_codi=t.SGD_TPR_CODIGO 
			LEFT OUTER JOIN SGD_DIR_DRECCIONES dir ON r.radi_nume_radi = dir.radi_nume_radi	and dir.sgd_dir_tipo = '1'
			LEFT JOIN medio_recepcion mr ON r.mrec_codi = mr.mrec_codi
			LEFT JOIN hist_eventos he1 ON r.radi_nume_radi = he1.radi_nume_radi AND he1.sgd_ttr_codigo = ".$COD_DIGITALIZACION."
			LEFT JOIN hist_eventos he2 ON r.radi_nume_radi = he2.radi_nume_radi AND he2.sgd_ttr_codigo = ".$COD_RADICACION."
			LEFT JOIN USUARIO u1 ON u1.usua_codi = he1.usua_codi_dest and u1.depe_codi = he1.depe_codi_dest
			LEFT JOIN USUARIO u2 ON u2.usua_codi = he2.usua_codi_dest and u2.depe_codi = he2.depe_codi_dest
			LEFT JOIN USUARIO u3 ON u3.usua_codi = he2.usua_codi and u3.depe_codi = he2.depe_codi
			LEFT JOIN dependencia d3 ON d3.depe_codi = he2.depe_codi
			WHERE 
			r.radi_depe_actu=da.depe_codi AND
			r.RADI_DEPE_RADI=df.DEPE_CODI AND
			r.RADI_DEPE_RADI != 900 AND
            ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'  
			$whereTipoRadicado $whereDependencia
			AND r.radi_nume_radi::text LIKE '%2'";
		$orderE = "	ORDER BY $orno $ascdesc";
		$queryETodosDetalle = $queryEDetalle . $condicionDep . $orderE;
		$queryEDetalle .= $condicionE . $orderE;
		//echo $queryEDetalle;
	}break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'ocipo':
	{
		if($tipoDocumento=='9999')
		{
			$queryE = 
			"SELECT b.USUA_NOMB USUARIO, 
				count(1) RADICADOS, 
				MIN(b.USUA_CODI) HID_COD_USUARIO, 
				MIN(b.depe_codi) HID_DEPE_USUA
			FROM RADICADO r, USUARIO b, sgd_dir_drecciones dir
			WHERE 
				r.radi_nume_radi=dir.radi_nume_radi and
				r.radi_usua_radi=b.usua_CODI 
				AND r.depe_codi=b.depe_codi
				AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
				$whereDependencia 
				$whereActivos
			$whereTipoRadicado 
			GROUP BY b.USUA_NOMB
			ORDER BY $orno $ascdesc";
		}
		else
		{
			$queryE = "
		    SELECT b.USUA_NOMB USUARIO
				, t.SGD_TPR_DESCRIP TIPO_DOCUMENTO
				, count(1) RADICADOS
				, MIN(b.USUA_CODI) HID_COD_USUARIO
				, MIN(SGD_TPR_CODIGO) HID_TPR_CODIGO
				, MIN(b.depe_codi) HID_DEPE_USUA
			FROM RADICADO r, USUARIO b, SGD_TPR_TPDCUMENTO t
			WHERE 
				r.radi_usua_radi=b.usua_CODI 
				AND r.tdoc_codi=t.SGD_TPR_CODIGO (+)
				AND r.depe_codi=b.depe_codi
				AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
				$whereActivos
			$whereTipoRadicado 
			GROUP BY b.USUA_NOMB,t.SGD_TPR_DESCRIP
			ORDER BY $orno $ascdesc";
		}
 		/** CONSULTA PARA VER DETALLES 
	 	*/


	$queryEDetalle = "SELECT DISTINCT r.RADI_NUME_RADI RADICADO
			,TO_CHAR(r.RADI_FECH_RADI, 'yyyy/mm/dd') FECHA_RADICADO
			,t.SGD_TPR_DESCRIP 	TIPO_DE_DOCUMENTO
			,r.RA_ASUN ASUNTO
			,r.RADI_DESC_ANEX ANEXOS
			,r.RADI_NUME_HOJA N_HOJAS
			,b.usua_nomb USUARIO
			,r.RADI_PATH HID_RADI_PATH
			,dir.sgd_dir_nomremdes REMITENTE 
			,df.DEPE_NOMB as DEPE_NOMB
			,df.DEPE_NOMB as DEPE_NOMB_ACTUAL
			,r.RADI_USU_ANTE
			,r.RADI_NUME_FOLIO
			,b.usua_nomb AS USUA_NOMB_ACTUAL
			FROM RADICADO r, 
			    dependencia df,
				USUARIO b, 
				SGD_TPR_TPDCUMENTO t,
				sgd_dir_drecciones dir
		WHERE 
			r.radi_nume_radi = dir.radi_nume_radi 
			and (select dir.sgd_dir_codigo  from sgd_dir_drecciones dir where dir.radi_nume_radi = r.radi_nume_radi AND ROWNUM <= 1 and sgd_dir_codigo =(SELECT MAX(sgd.sgd_dir_codigo) FROM sgd_dir_drecciones sgd where sgd.radi_nume_radi = r.radi_nume_radi )) =  dir.sgd_dir_codigo
			and r.radi_usua_radi=b.usua_CODI 
			AND r.tdoc_codi=t.SGD_TPR_CODIGO 
			AND r.depe_codi=b.depe_codi
			AND r.depe_codi=df.depe_codi
			AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini' AND '$fecha_fin'
		$whereTipoRadicado";
		$orderE = "	ORDER BY $orno $ascdesc";			

		/** CONSULTA PARA VER TODOS LOS DETALLES 
	 	*/ 
		$queryETodosDetalle = $queryEDetalle . $condicionDep.$whereDependencia 	. $orderE;
		$queryEDetalle .= $condicionE . $orderE;
		

	}break;
}



if($genDetalle==1){
	$titulos=array(
	"#",
	"1#RADICADO",
	"2#FECHA RADICADO",
	"3#ASUNTO",
	"10#NUM FOLIOS",
	"11#NUM ANEXOS",
	"12#DESCRIPCION ANEXOS",
	"6#REMITENTE",
	"5#USUARIO",
	"15#COD. DEPENDENCIA",
	"8#DEPENDENCIA_ACTUAL",
	"19#CODI_DEPE_ACTUAL",
	"13#DIGITALIZADOR",
	"14#FECHA DIGITALIZACIÓN",
	"4#RADICADOR",
	"7#DEPENDENCIA_RADICADOR",
	"16#MEDIO"
	);
}
else 		
	$titulos=array("#","1#USUARIO","2#RADICADOS","2#VER");
		
function pintarEstadistica($fila,$indice,$numColumna)
{
	global $ruta_raiz,$_POST,$_GET,$krd,$usua_doc;
	$salida="";
	$titulos=array("#","1#USUARIO","2#RADICADOS","2#VER");
	switch ($titulos[$numColumna]){
	case  "#":
		$salida=$indice;
		break;
	case "1#USUARIO":	
		$salida=$fila['USUARIO'];
		break;
	case "2#RADICADOS":
	$salida=$fila['RADICADOS'];
	break;
	case "2#VER":
		$datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;usua_doc=$usua_doc&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;tipoRadicado=".$_GET['tipoRadicado']."&amp;tipoDocumento=".$_GET['tipoDocumento']."&amp;codUs=".$fila['HID_COD_USUARIO']."&amp;depeUs=".$fila['HID_DEPE_USUA'];
		$datosEnvioDetalle=(isset($_GET['usActivos']))?$datosEnvioDetalle."&amp;usActivos=".$_GET['usActivos']:$datosEnvioDetalle;
		$salida="<a href=\"genEstadistica.php?{$datosEnvioDetalle}&amp;krd={$krd}\"  target=\"detallesSec\" >ver</a>";
		break;
	default: $salida=false;
	break;
}
	return $salida;
}

function pintarEstadisticaDetalle($fila,$indice,$numColumna)
{
	$titulos=array(
		"#",
		"1#RADICADO",
		"2#FECHA RADICADO",
		"3#ASUNTO",
		"10#NUM FOLIOS",
		"11#NUM ANEXOS",
		"12#DESCRIPCION ANEXOS",
		"6#REMITENTE",
		"5#USUARIO",
		"15#COD. DEPENDENCIA",
		"8#DEPENDENCIA_ACTUAL",
		"19#CODI_DEPE_ACTUAL",
		"13#DIGITALIZADOR",
		"14#FECHA DIGITALIZACIÓN",
		"4#RADICADOR",
		"7#DEPENDENCIA_RADICADOR",
		"16#MEDIO"
	);
	global $ruta_raiz,$encabezado,$krd, $db;
        include_once "$ruta_raiz/js/funtionImage.php";
        include_once "$ruta_raiz/tx/verLinkArchivo.php";
        $verLinkArchivo = new verLinkArchivo($db);
		$numRadicado=$fila['RADICADO'];	
	switch ($titulos[$numColumna])
	{
	case '#':
		$salida=$indice;
		break;
	case "1#RADICADO":
		 if(!is_null($fila['HID_RADI_PATH']) && $fila['HID_RADI_PATH'] != '')
                  {
                    $radi = $fila['RADICADO'];
                    $resulVali = $verLinkArchivo->valPermisoRadi($radi);
                    $valImg = $resulVali['verImg'];
                     if($valImg == "SI")
                       $salida="<center><a class=\"vinculos\" href=\"#2\" onclick=\"funlinkArchivo('$radi','$ruta_raiz');\">".$fila['RADICADO']."</a></center>";
                     else
		        $salida="<center><a class=vinculos href=javascript:noPermiso()>".$fila['RADICADO']."</a></center>";
                   } else   
                       $salida="<center class=\"leidos\">{$numRadicado}</center>";
	         break;
	case "2#FECHA RADICADO":
		 $radi = $fila['RADICADO'];
                 $resulVali = $verLinkArchivo->valPermisoRadi($radi);
                 $valImg = $resulVali['verImg'];
		 if($valImg == "SI")
		   $salida="<a class=\"vinculos\" href=\"{$ruta_raiz}verradicado.php?verrad=".$fila['RADICADO']."&amp;".session_name()."=".session_id()."&amp;krd=".$_GET['krd']."&amp;carpeta=8&amp;nomcarpeta=Busquedas&amp;tipo_carp=0 \" >".$fila['FECHA_RADICADO']."</a>";
		 else 
                   $salida="<a class=vinculos href=javascript:noPermiso()>".$fila['FECHA_RADICADO']."</a>";
	        break;
	case "3#ASUNTO":
		$salida="<center class=\"leidos\">".$fila['ASUNTO']."</center>";
	break;
	case "4#RADICADOR":
		$salida="<center class=\"leidos\">".$fila['RADICADOR']."</center>";		
		break;
	case "5#USUARIO":
		$salida="<center class=\"leidos\">".$fila['USUARIO']."</center>";			
		break;	
	case "6#REMITENTE":
		$salida="<center class=\"leidos\">".$fila['REMITENTE']."</center>";			
		break;
	case "7#DEPENDENCIA_RADICADOR":
		$salida="<center class=\"leidos\">".$fila['DEPENDENCIA_RADICADOR']."</center>";			
		break;		
	case "8#DEPENDENCIA_ACTUAL":
		$salida="<center class=\"leidos\">".$fila['DEPE_NOMB_ACTUAL']."</center>";			
		break;
	case "19#CODI_DEPE_ACTUAL":
		$salida="<center class=\"leidos\">".$fila['DEPE_CODI_ACTUAL']."</center>";			
		break;
	case "9#USUARIO ACTUAL":
		$salida="<center class=\"leidos\">".$fila['USUA_NOMB_ACTUAL']."</center>";			
		break;
	case "10#NUM FOLIOS":
		$salida="<center class=\"leidos\">".$fila['RADI_NUME_FOLIO']."</center>";			
		break;
	case "11#NUM ANEXOS":
		$salida="<center class=\"leidos\">".$fila['NUM_ANEXOS'].".</center>";			
		break;
	case "12#DESCRIPCION ANEXOS":
		$salida="<center class=\"leidos\">".$fila['RADI_DESC_ANEX']."</center>";			
		break;
	case "13#DIGITALIZADOR":
		$salida="<center class=\"leidos\">".$fila['DIGITALIZADOR']."</center>";			
		break;
	case "14#FECHA DIGITALIZACIÓN":
		$salida="<center class=\"leidos\">".$fila['FECHA_DIGITALIZACION']."</center>";			
		break;
	case "15#COD. DEPENDENCIA":
		$salida="<center class=\"leidos\">".$fila['COD_DEPE']."</center>";			
		break;
	case "16#MEDIO":
		$salida="<center class=\"leidos\">".$fila['MEDIO']."</center>";			
		break;
	}
	return $salida;
}
?>
