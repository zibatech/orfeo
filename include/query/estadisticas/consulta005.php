<?php
/** RADICADOS DE ENTRADA RECIBIDOS DEL AREA DE CORRESPONDENCIA
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
switch($db->driver)
{	
	case 'postgres':
		{	
			$queryE = "SELECT
								usuario.USUA_NOMB as USUARIO, count(DISTINCT r.radi_nume_radi)as RADICADOS, MIN(usuario.USUA_CODI) as HID_COD_USUARIO, MIN(usuario.depe_codi) as HID_DEPE_USUA 
						FROM
							radicado r
							INNER JOIN
							(	select h1.radi_nume_radi as radi_nume_radi, h1.id, h1.sgd_ttr_codigo as sgd_ttr_codigo, h1.usua_doc as usua_doc, h1.depe_codi as depe_codi from hist_eventos h1 
								INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi, min(hist_eventos.id) as id  from hist_eventos where hist_eventos.sgd_ttr_codigo = 2  GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id  ) htev
							ON 
								r.radi_nume_radi = htev.radi_nume_radi
								$whereDependencia
							LEFT JOIN
							usuario
							ON 
							htev.usua_doc = usuario.usua_doc
							AND ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'  
							AND r.SGD_TRAD_CODIGO =  2
							$whereTipoRadicado 
							GROUP BY usuario.USUA_NOMB
							ORDER BY $orno $ascdesc";
 			/** CONSULTA PARA VER DETALLES 
			 */
			$filtroUsuario = "";
			if($genTodosDetalle!=1)
			$filtroUsuario = " htev.depe_codi=$depeUs and htev.usua_codi=$codUs AND";

			$queryEDetalle = "SELECT
						DISTINCT cast(r.radi_nume_radi as varchar(20)) as RADICADO ,
						r.RADI_FECH_RADI as FECHA_RADICADO,
						t.SGD_TPR_DESCRIP as TIPO_DE_DOCUMENTO,
						r.RA_ASUN as ASUNTO ,
						da.DEPE_CODI as CODIGO_DEPENDENCIA_ACTUAL ,
						da.DEPE_NOMB as DEPENDENCIA_ACTUAL ,
						c.usua_nomb AS USUARIO_ACTUAL,
						b.usua_nomb as Usuario ,
						dir.SGD_DIR_NOMBRE || ' ' || dir.SGD_DIR_APELLIDO as REMITENTE ,
						dir.SGD_DIR_NOMREMDES as DIGNATARIO,
						df.DEPE_NOMB as DEPENDENCIA_INICIAL ,
						r.RADI_NUME_FOLIO  AS NUMERO_FOLIOS,
						r.RADI_NUME_ANEXO  AS NUMERO_ANEXO,
						r.RADI_DESC_ANEX   AS DESCRIPCION_ANEXO
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
						LEFT OUTER JOIN SGD_DIR_DRECCIONES dir ON r.radi_nume_radi = dir.radi_nume_radi and dir.sgd_dir_tipo = '1' 
						LEFT JOIN USUARIO c ON r.radi_usua_actu=c.usua_CODI AND r.radi_depe_actu=c.depe_codi 
					WHERE
						r.radi_depe_actu=da.depe_codi
						AND r.RADI_DEPE_RADI=df.DEPE_CODI AND
					$filtroUsuario
					r.SGD_TRAD_CODIGO=2
					AND TO_CHAR(r.radi_fech_radi,'YYYY/MM/DD') BETWEEN '$fecha_ini' AND '$fecha_fin' $whereTipoRadicado $whereDependencia ";

			
                           $orderE = " ORDER BY $orno $ascdesc";
                     
                        /** CONSULTA PARA VER TODOS LOS DETALLES 
                         */ 
                       $queryETodosDetalle = $queryEDetalle . $orderE;
					   $queryEDetalle .= $condicionUS . $orderE; 
					   
					 

		 //return;
		}break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'ocipo':
		{	if ( $dependencia_busq != 99999)
			{	$condicionE = "	AND h.DEPE_CODI_DEST=$dependencia_busq AND b.DEPE_CODI=$dependencia_busq ";	}
			$queryE = "
	    		SELECT b.USUA_NOMB USUARIO
					, count(r.RADI_NUME_RADI) RADICADOS
					, MIN(b.USUA_CODI) HID_COD_USUARIO
					, MIN(b.depe_codi) HID_DEPE_USUA
				FROM RADICADO r, USUARIO b, HIST_EVENTOS h
				WHERE 
					h.HIST_DOC_DEST=b.usua_doc
					$condicionE
					AND h.RADI_NUME_RADI=r.RADI_NUME_RADI
					AND h.SGD_TTR_CODIGO=2
					AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
					AND r.SGD_TRAD_CODIGO =  2
				$whereTipoRadicado 
				GROUP BY b.USUA_NOMB
				ORDER BY $orno $ascdesc";
 			/** CONSULTA PARA VER DETALLES 
	 		*/
			$queryEDetalle = "SELECT 
					r.RADI_NUME_RADI RADICADO
					, b.USUA_NOMB USUARIO_ACTUAL
					, r.RA_ASUN ASUNTO 
					, TO_CHAR(r.RADI_FECH_RADI, 'DD/MM/YYYY HH24:MM:SS') FECHA_RADICACION
					, TO_CHAR(h.HIST_FECH, 'DD/MM/YYYY HH24:MM:SS') FECHA_DIGITALIZACION
					,r.RADI_PATH HID_RADI_PATH{$seguridad}
				FROM RADICADO r, USUARIO b, HIST_EVENTOS h
				WHERE 
					h.HIST_DOC_DEST=b.usua_doc
					$condicionE
					AND h.RADI_NUME_RADI=r.RADI_NUME_RADI
					AND h.SGD_TTR_CODIGO=2
					AND r.SGD_TRAD_CODIGO =  2
					AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
				$whereTipoRadicado 
				";
                          $orderE = " ORDER BY $orno $ascdesc";
                          $condicionUS = " AND b.USUA_CODI=$codUs
                                         AND b.depe_codi = $depeUs "; 
                        /** CONSULTA PARA VER TODOS LOS DETALLES 
                         */ 
                       $queryETodosDetalle = $queryEDetalle . $orderE;
                       $queryEDetalle .= $condicionUS . $orderE; 

		}break;
}
if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1)
	$titulos=array("#","1#RADICADO","2#FECHA RADICADO","3#CODIGO_DEPENDENCIA_ACTUAL","4#DEPENDENCIA_ACTUAL","5#USUARIO_ACTUAL","6#NUMERO_FOLIOS","7#ANEXOS","8#DESCRIPCION_ANEXOS");
else 		
	$titulos=array("#","1#USUARIO","2#RADICADOS","2#VER");

function pintarEstadistica($fila,$indice,$numColumna)
{
	global $ruta_raiz,$_POST,$_GET,$krd,$usua_doc;
	$salida="";
	switch ($numColumna)
	{
		case  0:
			$salida=$indice;
			break;
		case 1:	
			$salida=$fila['USUARIO'];
			break;
		case 2:
			
			$salida=$fila['RADICADOS'];
			break;
		case 3:
			$datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;usua_doc=$usua_doc&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;tipoRadicado=".$_GET['tipoRadicado']."&amp;tipoDocumentos=".$GLOBALS['tipoDocumentos']."&amp;codUs=".$fila['HID_COD_USUARIO']."&amp;depeUs=".$fila['HID_DEPE_USUA'];
			$datosEnvioDetalle=(isset($_GET['usActivos']))?$datosEnvioDetalle."&amp;usActivos=".$_GET['usActivos']:$datosEnvioDetalle;
			$salida="<a href=\"genEstadistica.php?{$datosEnvioDetalle}&amp;krd={$krd}\"  target=\"detallesSec\" >ver</a>";
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
	
	$titulos=array("#","1#RADICADO","2#FECHA RADICADO","3#CODIGO_DEPENDENCIA_ACTUAL","4#DEPENDENCIA_ACTUAL","5#USUARIO_ACTUAL","6#NUMERO_FOLIOS","7#ANEXOS","8#DESCRIPCION_ANEXOS");
	switch ($titulos[$numColumna])
	{
	case "#":
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
	case "3#CODIGO_DEPENDENCIA_ACTUAL":
		$salida="<center class=\"leidos\">".$fila['CODIGO_DEPENDENCIA_ACTUAL']."</center>";
		break;
	case "4#DEPENDENCIA_ACTUAL":
		$salida="<center class=\"leidos\">".$fila['DEPENDENCIA_ACTUAL']."</center>";
		break;
	case "5#USUARIO_ACTUAL":
		$salida="<center class=\"leidos\">".$fila['USUARIO_ACTUAL']."</center>";
		break;
	case "6#NUMERO_FOLIOS":
		$salida="<center class=\"leidos\">".$fila['NUMERO_FOLIOS']."</center>";
		break;

	case "7#ANEXOS":
		$salida="<center class=\"leidos\">".$fila['RADI_NUME_ANEXO']."</center>";		
		break;
	
	case "8#DESCRIPCION_ANEXOS":
		$salida="<center class=\"leidos\">".$fila['DESCRIPCION_ANEXO']."</center>";		
		break;
	}
	return $salida;
	}
?>
