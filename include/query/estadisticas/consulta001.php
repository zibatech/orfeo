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
    $condicionE   = "AND b.USUA_CODI=$codUs $condicionDep ";
}

if(!empty($depeUs)){
    
    $condicionDep = "AND b.depe_codi = $depeUs";
    $condicionE   = "AND b.USUA_CODI = $codUs $condicionDep ";
}


if(!empty($_GET["depeUs"])){
    
    $depeUsu=$_GET["depeUs"];
}

switch($db->driver)
{
	case 'mssql':
	case 'postgresql':	
	case 'postgres':	
	{	
		
		//echo $whereDependencia;
		if($tipoDocumento=='9999')
		{ 
			
			$queryE = "	SELECT
								b.USUA_NOMB as USUARIO, count(DISTINCT r.radi_nume_radi)as RADICADOS, MIN(b.USUA_CODI) as HID_COD_USUARIO, MIN(b.depe_codi) as HID_DEPE_USUA 
						FROM
							radicado r
							INNER JOIN
							(	select h1.radi_nume_radi as radi_nume_radi, h1.id, h1.sgd_ttr_codigo as sgd_ttr_codigo, h1.usua_doc as usua_doc, h1.depe_codi as depe_codi from hist_eventos h1 
								INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi, min(hist_eventos.id) as id  from hist_eventos where hist_eventos.sgd_ttr_codigo = 2  GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id  ) htev
							ON 
								r.radi_nume_radi = htev.radi_nume_radi
								
							LEFT JOIN
							usuario b
							ON 
								htev.usua_doc = b.usua_doc
								
							
						WHERE ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'  $whereDependencia
							 $whereActivos $whereTipoRadicado 
							GROUP BY b.USUA_NOMB ORDER BY $orno $ascdesc";
			
							

		}
		else
		{	
			$queryE = "	SELECT
					b.USUA_NOMB as USUARIO, count(DISTINCT r.radi_nume_radi)as RADICADOS, MIN(b.USUA_CODI) as HID_COD_USUARIO, MIN(b.depe_codi) as HID_DEPE_USUA 
			FROM
				radicado r
				INNER JOIN
				(	select h1.radi_nume_radi as radi_nume_radi, h1.id, h1.sgd_ttr_codigo as sgd_ttr_codigo, h1.usua_doc as usua_doc, h1.depe_codi as depe_codi from hist_eventos h1 
					INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi, min(hist_eventos.id) as id  from hist_eventos where hist_eventos.sgd_ttr_codigo = 2  GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id  ) htev
				ON 
					r.radi_nume_radi = htev.radi_nume_radi
				LEFT JOIN
				usuario b
				ON 
					htev.usua_doc = b.usua_doc
				LEFT OUTER JOIN SGD_TPR_TPDCUMENTO t ON r.TDOC_CODI = t.SGD_TPR_CODIGO
			WHERE ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'  $whereDependencia  AND t.SGD_TPR_CODIGO = '$tipoDocumento' 
				$whereActivos $whereTipoRadicado 
				GROUP BY b.USUA_NOMB ORDER BY $orno $ascdesc";	
		}


		//echo "".$queryE;

		
		


		
		$queryEDetalle = "SELECT
							DISTINCT cast(r.radi_nume_radi as varchar(20)) as RADICADO ,
							r.RADI_FECH_RADI as FECHA_RADICADO,
							t.SGD_TPR_DESCRIP as TIPO_DE_DOCUMENTO,
							r.RA_ASUN as ASUNTO ,
							r.RADI_PATH HID_RADI_PATH,
							b.usua_nomb as Usuario ,
							dir.SGD_DIR_NOMBRE || ' ' || dir.SGD_DIR_APELLIDO as REMITENTE ,
							dir.SGD_DIR_NOMREMDES as DIGNATARIO,
							df.DEPE_NOMB as DEPENDENCIA_INICIAL ,
							da.DEPE_NOMB as DEPENDENCIA_ACTUAL ,
							c.usua_nomb AS USUARIO_ACTUAL,
							r.RADI_NUME_FOLIO  AS NUMERO_FOLIOS
						FROM
							dependencia df, dependencia da , radicado r
							INNER JOIN
								(select h1.radi_nume_radi as radi_nume_radi, h1.id, h1.sgd_ttr_codigo as sgd_ttr_codigo, h1.usua_codi as usua_codi, h1.usua_doc as usua_doc, h1.depe_codi as depe_codi from hist_eventos h1 
								INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi, min(hist_eventos.id) as id  from hist_eventos where hist_eventos.sgd_ttr_codigo = 2  GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id  ) htev
							ON 
								r.radi_nume_radi = htev.radi_nume_radi 
							LEFT JOIN
							usuario b
							ON 
								htev.usua_doc = b.usua_doc
							LEFT OUTER JOIN SGD_TPR_TPDCUMENTO t ON r.tdoc_codi=t.SGD_TPR_CODIGO 
							LEFT OUTER JOIN SGD_DIR_DRECCIONES dir ON r.radi_nume_radi = dir.radi_nume_radi
							LEFT JOIN USUARIO c ON r.radi_usua_actu=c.usua_CODI AND r.radi_depe_actu=c.depe_codi 
						WHERE
							r.radi_depe_actu=da.depe_codi
							AND r.RADI_DEPE_RADI=df.DEPE_CODI
							AND TO_CHAR(r.radi_fech_radi,'YYYY/MM/DD') BETWEEN '$fecha_ini' AND '$fecha_fin' $whereTipoRadicado $whereDependencia";
		$orderE = "	ORDER BY $orno $ascdesc"; //
		
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
		//		echo "<pre>$queryEDetalle </pre>";                 exit;

	}break;
}

if($genDetalle==1){
	$titulos=array("#","1#RADICADO","2#FECHA RADICADO","3#TIPO DOCUMENTO","4#ASUNTO","5#USUARIO","6#REMITENTE","7#DIGNATARIO","8#DEPENDENCIA_INICIAL","9#DEPENDENCIA_ACTUAL","10#USUARIO ACTUAL","11#NUM FOLIOS");
}
else 		
	$titulos=array("#","1#Usuario","2#Radicados","2#VER");
		
function pintarEstadistica($fila,$indice,$numColumna)
{
	global $ruta_raiz,$_POST,$_GET,$krd,$usua_doc;
	$salida="";
	$titulos=array("#","1#Usuario","2#Radicados","2#VER");
	switch ($titulos[$numColumna])
	{
	case  "#":
		$salida=$indice;
		break;
	case "1#Usuario":	
		$salida=$fila['USUARIO'];
		break;
	case "2#Radicados":
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
	global $ruta_raiz,$encabezado,$krd, $db;
        include_once "$ruta_raiz/js/funtionImage.php";
        include_once "$ruta_raiz/tx/verLinkArchivo.php";
        $verLinkArchivo = new verLinkArchivo($db);
		$numRadicado=$fila['RADICADO'];	
	
	$titulos=array("#","1#RADICADO","2#FECHA RADICADO","3#TIPO DOCUMENTO","4#ASUNTO","5#USUARIO","6#REMITENTE","7#DIGNATARIO","8#DEPENDENCIA_INICIAL","9#DEPENDENCIA_ACTUAL","10#USUARIO ACTUAL","11#NUM FOLIOS");
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
	case "3#TIPO DOCUMENTO":
		$salida="<center class=\"leidos\">".$fila['TIPO_DE_DOCUMENTO']."</center>";		
		break;
	case "4#ASUNTO":
		$salida="<center class=\"leidos\">".$fila['ASUNTO']."</center>";
		break;
	case "5#USUARIO":
		$salida="<center class=\"leidos\">".$fila['USUARIO']."</center>";			
		break;	
	case "6#REMITENTE":
		$salida="<center class=\"leidos\">".$fila['REMITENTE']."</center>";			
		break;
	case "7#DIGNATARIO":
		$salida="<center class=\"leidos\">".$fila['DIGNATARIO']."</center>";			
		break;
	case "8#DEPENDENCIA_INICIAL":
		$salida="<center class=\"leidos\">".$fila['DEPENDENCIA_INICIAL']."</center>";			
		break;		
	case "9#DEPENDENCIA_ACTUAL":
		$salida="<center class=\"leidos\">".$fila['DEPENDENCIA_ACTUAL']."</center>";			
		break;
	case "10#USUARIO ACTUAL":
		$salida="<center class=\"leidos\">".$fila['USUARIO_ACTUAL']."</center>";			
		break;
	case "11#NUM FOLIOS":
		$salida="<center class=\"leidos\">".$fila['NUMERO_FOLIOS']."</center>";			
		break;
	
	}
	return $salida;
}
?>
