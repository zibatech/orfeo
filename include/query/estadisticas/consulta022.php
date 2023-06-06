<?php
/** CONSUTLA 001 
 *Estadisticas por medio de envio -Salida *******
 *se tienen en cuenta los registros enviados por la dep xx contando la masiva ----
 *
 * @autor JAIRO H LOSADA - SSPD
 * @version ORFEO 3.1
 * 
 */
$coltp3Esp = '"'.$tip3Nombre[3][2].'"';	
if(!$orno) $orno=2;
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
$seguridad=",B.CODI_NIVEL USUA_NIVEL,R.SGD_SPUB_CODIGO";
$whereDependencia = str_replace("DEPE_CODI", "d.DEPE_CODI", $whereDependencia);

switch($db->driver)
{
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'ocipo':
		{
			if ($whereDependencia && $dependencia_busq != 99999)
			{
				$wdepend = " AND b.depe_codi = $dependencia_busq ";
			}
			$queryE = 
				"SELECT 
				b.USUA_DOC HID_COD_USUARIO
				, HID_DEPE_USUA
				,b.USUARIO
				,b.sgd_fenv_codigo CODIGO_ENVIO
				,c.sgd_fenv_descrip MEDIO_ENVIO
				,b.tot_reg TOTAL_ENVIADOS
				,b.sgd_fenv_codigo HID_CODIGO_ENVIO
				FROM 
				(SELECT COUNT(c.SGD_RENV_CANTIDAD) tot_reg,c.sgd_fenv_codigo , b.USUA_NOMB USUARIO, MIN(b.depe_codi) HID_DEPE_USUA, MIN(b.usua_doc) USUA_DOC
				 FROM SGD_RENV_REGENVIO c, USUARIO b, radicado a
				 WHERE 
				 TO_CHAR(c.SGD_RENV_FECH,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin'
				 AND a.radi_nume_radi = c.radi_nume_sal
				 $wdepend
				 AND substr(c.usua_doc,1,15) = b.usua_doc
				 AND (c.sgd_renv_planilla != '00' or c.sgd_renv_planilla is null)
				 and (c.sgd_renv_observa not like 'Masiva%' or  c.sgd_renv_observa is null)
				 $whereTipoRadicado
				 GROUP BY b.USUA_NOMB, c.sgd_fenv_codigo) b, SGD_FENV_FRMENVIO c
				WHERE b.sgd_fenv_codigo=c.sgd_fenv_codigo
				ORDER BY $orno $ascdesc";

			$queryE="select u.usua_nomb as USUARIO, count(r.radi_nume_radi) RADICADOS, max(u.USUA_CODI) HID_COD_USUARIO,
				max(u.depe_Codi) HID_DEPE_USUA 
					from anexos ax inner join radicado r on ax.anex_radi_nume=r.radi_nume_radi left join usuario u on r.radi_usua_actu=u.usua_codi and r.radi_depe_actu=u.depe_codi left join dependencia d on r.radi_depe_actu=d.depe_codi where ax.anex_estado >=3 and substr(ax.anex_radi_nume, -1)=1  AND ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'   $whereDependencia
					group by u.usua_nomb ";
 			$queryE="select max(SUBSTR(r.radi_nume_radi, 1, 4)) ANO, max(TO_CHAR(RADI_FECH_RADI, 'mm')) MES, max(SUBSTR(r.radi_nume_radi, -1)) as Tipo_radicacion, count(r.radi_nume_radi) as cantidad from  sgd_rdf_retdocf rdf,
sgd_mrd_matrird mrd,
   radicado r left join usuario u on r.radi_depe_actu=u.depe_codi and r.radi_usua_actu = u.usua_codi left join dependencia d on r.radi_depe_actu=d.depe_codi left join carpeta ca on r.carp_codi=ca.carp_codi where    ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'  and rdf.radi_nume_radi=r.radi_nume_radi
and mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo
$whereDependenciaTRD
$whereCodserie
$whereTsub
$whereTdoc
 $whereDependencia
group by SUBSTR(r.radi_nume_radi, 1, 4), TO_CHAR(RADI_FECH_RADI, 'mm'), SUBSTR(r.radi_nume_radi, -1)
";
			/** CONSULTA PARA VER DETALLES 
			 */ 


			//$condicionDep = " AND  d.depe_codi = $depeUs ";
			if($_GET['dependencia_busq']==99999)
				 $whereDependencia="";
			else
				$whereDependencia="AND radi_depe_actu= ".$_GET['dependencia_busq'];
			$condicionE = "  AND  SUBSTR(r.radi_nume_radi, -1)=$tipoRadicado $whereDependencia AND SUBSTR(r.radi_nume_radi, 1, 4)= ".$_GET['ano']." AND TO_CHAR(RADI_FECH_RADI, 'mm')=".$_GET['mes'];

			
			$orderE = "	ORDER BY $orno $ascdesc ";
 			$queryEDetalle="select r.radi_nume_radi radicado,  substr(r.radi_nume_radi,-1) tipo,r.radi_usua_actu codigo_usuario, r.ra_asun asunto, u.usua_nomb nombre_usuario,  r.radi_depe_actu dependencia_actual from  usuario u, sgd_rdf_retdocf rdf,
sgd_mrd_matrird mrd,
  radicado r where  ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin' and rdf.radi_nume_radi=r.radi_nume_radi
and mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo
$whereDependenciaTRD
$whereCodserie
$whereTsub
$whereTdoc
and r.radi_usua_actu=u.usua_codi and r.radi_depe_actu=u.depe_codi
   ";
			// group by ax.anex_radi_nume
			//";	
			/** CONSULTA PARA VER TODOS LOS DETALLES 
			 */ 
//			$queryETodosDetalle = $queryEDetalle . $orderE;
			$queryEDetalle .= $condicionE . $condicionDep . $orderE;
		}break;
	default:
		{	// Este default trabaja con Mssql 2K, 2K5.
			if ($whereDependencia && $dependencia_busq != 99999)	$wdepend = " AND b.depe_codi = $dependencia_busq ";
			$queryE = " SELECT b.USUA_DOC AS HID_COD_USUARIO, HID_DEPE_USUA, b.USUARIO, b.sgd_fenv_codigo AS CODIGO_ENVIO,
				c.sgd_fenv_descrip AS MEDIO_ENVIO, b.tot_reg AS TOTAL_ENVIADOS, b.sgd_fenv_codigo AS HID_CODIGO_ENVIO
					FROM  (SELECT COUNT(c.SGD_RENV_CANTIDAD) AS tot_reg, c.sgd_fenv_codigo, b.USUA_NOMB AS USUARIO,
							MIN(b.depe_codi) AS HID_DEPE_USUA, MIN(b.usua_doc) AS USUA_DOC
							FROM  SGD_RENV_REGENVIO c, USUARIO b, radicado a
							WHERE ".$db->conn->SQLDate('Y/m/d', 'a.radi_fech_radi')." BETWEEN '$fecha_ini'  AND '$fecha_fin' AND
							a.radi_nume_radi = c.radi_nume_sal $wdepend AND c.usua_doc = cast(b.usua_doc as numeric) AND
							(c.sgd_renv_planilla != '00' or c.sgd_renv_planilla is null) and
							(c.sgd_renv_observa not like 'Masiva%' or  c.sgd_renv_observa is null)
							$whereTipoRadicado
							GROUP BY b.USUA_NOMB, c.sgd_fenv_codigo
					      ) b, SGD_FENV_FRMENVIO c
					WHERE b.sgd_fenv_codigo=c.sgd_fenv_codigo
					ORDER BY $orno $ascdesc";

			/** CONSULTA PARA VER DETALLES   */ 
			$condicionDep = ($dependencia_busq == 99999) ? '' : " and b.depe_codi = ".$dependencia_busq;
			$condicionE = " AND c.sgd_fenv_codigo = $fenvCodi AND b.usua_doc = '".trim($codUs)."' ";
			$queryEDetalle = "SELECT cast(c.RADI_NUME_SAL as varchar(20)) AS RADICADO, d.sgd_fenv_descrip AS ENVIO_POR,
				b.USUA_NOMB AS USUARIO_QUE_ENVIO, c.sgd_renv_fech AS FECHA_ENVIO, c.sgd_renv_planilla AS PLANILLA,
				c.sgd_fenv_codigo AS HID_CODIGO_ENVIO				
					FROM SGD_RENV_REGENVIO c, SGD_FENV_FRMENVIO d, USUARIO b, radicado a
					WHERE c.sgd_fenv_codigo=d.sgd_fenv_codigo AND 
					".$db->conn->SQLDate('Y/m/d', 'a.radi_fech_radi')." BETWEEN '$fecha_ini'  AND '$fecha_fin' AND 
					a.radi_nume_radi = c.radi_nume_sal and cast(c.usua_doc as varchar(15)) =  b.USUA_doc $wdepend AND
					(c.sgd_renv_planilla != '00' or c.sgd_renv_planilla is null) and
					(c.sgd_renv_observa not like 'Masiva%' or  c.sgd_renv_observa is null)
					$whereTipoRadicado ";

			$orderE = "	ORDER BY $orno $ascdesc ";

			/** CONSULTA PARA VER TODOS LOS DETALLES */
			$queryETodosDetalle = $queryEDetalle . $orderE;
			$queryEDetalle .= $condicionE . $condicionDep . $orderE;
			//$db->conn->debug = true;	
		}break;
}

if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1)

$titulos=array("#","2#RADICADO","3#ASUNTO","4#NOMBRE USUARIO","3#CODIGO USUARIO","3#DEPENDENCIA ACTUAL","4#TIPO RADICADO");
else             
$titulos=array("#","1#ANO","2#MES","3#TIPO RADICACION","4#CANTIDAD");
function pintarEstadistica($fila,$indice,$numColumna){ 
$codserie=$_GET["codserie"];
$tsub=$_GET["tsub"];
	global $ruta_raiz,$_GET,$_GET,$krd,$usua_doc; 
	$salida=""; 
	switch ($numColumna){ 
		case  0: 
			$salida=$indice; 
			break;
		case  1: 
			$salida=$fila['ANO'];
			break;
		case 2:
			$salida=$fila['MES'];
			break;
		case 3:
			$salida=$fila['TIPO_RADICACION'];
			break;
		case 4:
			$datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;codserie=".$codserie."&amp;tsub=".$tsub."&amp;genDetalle=1&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;codUs=".$fila['CODIGO_USUARIO']."&amp;depeUs=".$fila['CODIGO_DEPENDENCIA']."&amp;carpCod=".$fila['CODIGO_CARPETA']."&amp;fenvCodi=".$fila['HID_CODIGO_ENVIO']."&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;tipoRadicado=".$fila['TIPO_RADICACION']."&amp;mes=".$fila['MES']."&amp;ano=".$fila['ANO']."&amp;tipoDocumento=".$_GET['tipoDocumento'];
			$datosEnvioDetalle=(isset($_GET['usActivos']))?$datosEnvioDetalle."&amp;usActivos=".$_GET['usActivos']:$datosEnvioDetalle;
			$salida="<a href=\"genEstadistica.php?{$datosEnvioDetalle}&amp;krd={$krd}\"  target=\"detallesSec\" >".$fila['CANTIDAD']."</a>";
			break;
		default: $salida=false;
	}
	return $salida;
}
function pintarEstadisticaDetalle($fila,$indice,$numColumna){
	global $ruta_raiz,$encabezado,$krd,$db;
	include_once "$ruta_raiz/js/funtionImage.php";
	include_once "$ruta_raiz/tx/verLinkArchivo.php";
	$verLinkArchivo = new verLinkArchivo($db);
	$numRadicado=$fila['RADICADO'];
	switch ($numColumna){
		case 0:
			$salida=$indice;
			break;
		case 1:
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
		case 2:
			$salida="<center class=\"leidos\">".$fila['ASUNTO']."</center>"; 
			break;
		case 3:  
			$salida="<center class=\"leidos\">".$fila['NOMBRE_USUARIO']."</center>";
			break; 
		case 4:
			$salida="<center class=\"leidos\">".$fila['CODIGO_USUARIO']."</center>";
			break;
		case 5:
			$salida="<center class=\"leidos\">".$fila['DEPENDENCIA_ACTUAL']."</center>";
			break;
		case 6:
			$salida="<center class=\"leidos\">".$fila['TIPO']."</center>";
			break;
	}
	return $salida;               

}
?>
