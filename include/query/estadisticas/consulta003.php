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
$whereTipoRadicado  = str_replace("r.","a.",$whereTipoRadicado);
$whereTipoRadicado  = str_replace("R.","a.",$whereTipoRadicado);

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
			/** CONSULTA PARA VER DETALLES 
	 		*/ 
			$condicionDep = " AND b.depe_codi = $depeUs ";
			$condicionE = " AND c.sgd_fenv_codigo = $fenvCodi AND b.USUA_doc = '".trim($codUs)."' ";

			$queryEDetalle = "SELECT  c.RADI_NUME_SAL RADICADO
				,d.sgd_fenv_descrip ENVIO_POR
				,b.USUA_NOMB USUARIO_QUE_ENVIO
				,c.sgd_renv_fech FECHA_ENVIO
				,c.sgd_renv_planilla PLANILLA
				,c.sgd_fenv_codigo HID_CODIGO_ENVIO				
				FROM SGD_RENV_REGENVIO c, SGD_FENV_FRMENVIO d, USUARIO b, radicado a
				WHERE 
				    c.sgd_fenv_codigo=d.sgd_fenv_codigo
					AND TO_CHAR(c.SGD_RENV_FECH,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin'
					AND a.radi_nume_radi = c.radi_nume_sal
					and substr(c.usua_doc,1,15) =  b.USUA_doc
					$wdepend
					AND (c.sgd_renv_planilla != '00' or c.sgd_renv_planilla is null)
					and (c.sgd_renv_observa not like 'Masiva%' or  c.sgd_renv_observa is null)
					
					$whereTipoRadicado ";

			$orderE = "	ORDER BY $orno $ascdesc ";
 			/** CONSULTA PARA VER TODOS LOS DETALLES 
	 		*/ 
			$queryETodosDetalle = $queryEDetalle . $orderE;
			$queryEDetalle .= $condicionE . $condicionDep . $orderE;
		}break;
	default:
		{	// Este default trabaja con Mssql 2K, 2K5.
			if ($whereDependencia && $dependencia_busq != 99999)	$wdepend = " AND b.depe_codi = $dependencia_busq ";
			$queryE = " SELECT 
								b.USUA_DOC AS HID_COD_USUARIO, 
								HID_DEPE_USUA, 
								b.USUARIO, b.sgd_fenv_codigo AS CODIGO_ENVIO,
								c.sgd_fenv_descrip AS MEDIO_ENVIO, 
								b.tot_reg AS TOTAL_ENVIADOS, 
								b.sgd_fenv_codigo AS HID_CODIGO_ENVIO
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
			$queryEDetalle = "SELECT 
								anex.anex_radi_nume as RADI_PADRE,
								cast(c.RADI_NUME_SAL as varchar(20)) AS RADICADO, 
								a.RADI_PATH 	AS HID_RADI_PATH,
								d.sgd_fenv_descrip AS ENVIO_POR,
	                        	b.USUA_NOMB AS USUARIO_QUE_ENVIO, 
								c.sgd_renv_fech AS FECHA_ENVIO, 
								c.sgd_renv_mail as MAIL_ENVIO , 
								c.sgd_renv_dir as DIRECCION_ENVIO,  
								sgd_renv_observa as OBSERVACION,	
								c.sgd_renv_planilla AS PLANILLA,
	                        	c.sgd_fenv_codigo AS HID_CODIGO_ENVIO	   
					       FROM  SGD_FENV_FRMENVIO d, USUARIO b, radicado a, SGD_RENV_REGENVIO c
						   INNER JOIN
							anexos anex
							ON 
								c.radi_nume_sal = anex.radi_nume_salida
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
			//echo "Otroooo--->".$queryEDetalle;
		}break;
}

if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1)
	$titulos=array("#","1#RADI_PADRE","1#RADICADO","2#ENVIO POR","3#USUARIO QUE ENVIO","4#FECHA ENVIO","5#MAIL DESTINO","6#DIRRECCION DESTINO","7#OBSERVACIONES","8#PLANILLA");
else             
	$titulos=array("#","1#USUARIO","4#TOTAL ENVIADOS","2#CODIGO ENVIO","3#MEDIO DE ENVIO","4#VER");
                 
	function pintarEstadistica($fila,$indice,$numColumna){ 
        global $ruta_raiz,$_GET,$_GET,$krd,$usua_doc; 
		$salida=""; 
		$titulos=array("#","1#USUARIO","4#TOTAL ENVIADOS","2#CODIGO ENVIO","3#MEDIO DE ENVIO","4#VER");
			switch ($titulos[$numColumna]){ 
					case  '#':
							$salida=$indice; 
							break;
					case '1#USUARIO':
							$salida=$fila['USUARIO'];
							break;
					case "4#TOTAL ENVIADOS":
						$salida=$fila['TOTAL_ENVIADOS'];
					break;
					case "2#CODIGO ENVIO":
							$salida=$fila['CODIGO_ENVIO'];
					break;
					case "3#MEDIO DE ENVIO":
						$salida=$fila['MEDIO_ENVIO'];
					break;
					case "4#VER":
						$datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;codUs=".$fila['HID_COD_USUARIO']."&amp;depeUs=".$fila['HID_DEPE_USUA']."&amp;fenvCodi=".$fila['HID_CODIGO_ENVIO']."&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;tipoRadicado=".$_GET['tipoRadicado']."&amp;tipoDocumento=".$_GET['tipoDocumento'];
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
                $numRadicadoPadre=$fila['RADI_PADRE'];
				
                $titulos=array("#","1#RADI_PADRE","1#RADICADO","2#ENVIO POR","3#USUARIO QUE ENVIO","4#FECHA ENVIO","5#MAIL DESTINO","6#DIRRECCION DESTINO","7#OBSERVACIONES","8#PLANILLA");
					switch ($titulos[$numColumna]){ 
							case "#":
									$salida=$indice;
									break;
							case "1#RADI_PADRE":
								if(!is_null($fila['HID_RADI_PATH']) && $fila['HID_RADI_PATH'] != '')
									{
										$radi = $fila['RADI_PADRE'];

										$resulVali = $verLinkArchivo->valPermisoRadi($radi);

										$valImg = $resulVali['verImg'];
										if($valImg == "SI")
										$salida="<center><a class=\"vinculos\" href=\"#2\" onclick=\"funlinkArchivo('$radi','$ruta_raiz');\">".$fila['RADI_PADRE']."</a></center>";
										else
									$salida="<center><a class=vinculos href=javascript:noPermiso()>".$fila['RADI_PADRE']."</a></center>";
										} else   
										$salida="<center class=\"leidos\">{$numRadicadoPadre}</center>";
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
							case "2#ENVIO POR":
									$salida=$fila['ENVIO_POR'];
									break;
							case "3#USUARIO QUE ENVIO":  
									$salida="<center class=\"leidos\">".$fila['USUARIO_QUE_ENVIO']."</center>";                                                 
									break;
							case "4#FECHA ENVIO":
									$salida="<center class=\"leidos\">".$fila['FECHA_ENVIO']."</center>";
									break;
							case "5#MAIL DESTINO":
									$salida="<center class=\"leidos\">".$fila['MAIL_ENVIO']."</center>";
									break;
							case "6#DIRRECCION DESTINO":
								$salida="<center class=\"leidos\">".$fila['DIRECCION_ENVIO']."</center>";
								break;
							case "7#OBSERVACIONES":
								$salida="<center class=\"leidos\">".$fila['OBSERVACION']."</center>";
								break;
							case "8#PLANILLA":
									$salida="<center class=\"leidos\">".$fila['PLANILLA']."</center>";
									break;
					}
					return $salida;               
		
	}
?>
