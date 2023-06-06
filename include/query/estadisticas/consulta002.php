<?
/** CONSUTLA 002 
	* Estadiscas por medio de recepcion Entrada
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
{	case 'mssql':
	case 'postgres':
		{	
					/*$queryE = "SELECT c.mrec_desc AS MEDIO_RECEPCION, COUNT(1) AS Radicados, max(c.MREC_CODI) AS HID_MREC_CODI
						FROM RADICADO r, MEDIO_RECEPCION c, USUARIO b, hist_eventos h
						WHERE 
							r.mrec_codi=c.mrec_codi AND r.depe_codi=h.depe_codi  AND r.radi_usua_radi=h.usua_codi and b.usua_doc=h.usua_doc  and r.radi_nume_radi=h.radi_nume_radi
							AND r.mrec_codi=c.mrec_codi and h.sgd_ttr_codigo=2
							$condicionE
							AND ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'
							$whereTipoRadicado
						GROUP BY c.mrec_desc
						ORDER BY $orno $ascdesc";	*/

					$queryE = "	SELECT
							c.mrec_desc AS MEDIO_RECEPCION, count(DISTINCT r.radi_nume_radi)as RADICADOS, max(c.MREC_CODI) AS HID_MREC_CODI
					FROM
						MEDIO_RECEPCION c, radicado r 
						INNER JOIN
						(select h1.radi_nume_radi as radi_nume_radi, h1.id, h1.sgd_ttr_codigo as sgd_ttr_codigo, h1.usua_doc as usua_doc, h1.depe_codi as depe_codi from hist_eventos h1 INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi, min(hist_eventos.id) as id  from hist_eventos where hist_eventos.sgd_ttr_codigo = 2  GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id  ) htev
						ON 
							r.radi_nume_radi = htev.radi_nume_radi
							LEFT JOIN
						usuario
						ON 
							htev.usua_doc = usuario.usua_doc
					WHERE ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'  AND r.mrec_codi=c.mrec_codi 
						$whereTipoRadicado $whereDependencia
						GROUP BY c.mrec_desc ORDER BY $orno $ascdesc";

						
 			/** CONSULTA PARA VER DETALLES 
	 		*/
			$queryEDetalle = "SELECT $radi_nume_radi 								AS RADICADO, 
						".$db->conn->SQLDate('Y/m/d h:i:s','r.radi_fech_radi')."  	AS FECHA_RADICADO
						,r.RA_ASUN 	AS ASUNTO
						,c.MREC_DESC 	AS MEDIO_RECEPCION
						,b.usua_nomb 	AS USUARIO
						,r.RADI_PATH 	AS HID_RADI_PATH
						FROM
							MEDIO_RECEPCION c, radicado r 
							INNER JOIN
							(select h1.radi_nume_radi as radi_nume_radi, h1.id, h1.sgd_ttr_codigo as sgd_ttr_codigo, h1.usua_doc as usua_doc, h1.depe_codi as depe_codi from hist_eventos h1 INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi, min(hist_eventos.id) as id  from hist_eventos where hist_eventos.sgd_ttr_codigo = 2  GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id  ) htev
							ON 
								r.radi_nume_radi = htev.radi_nume_radi
							LEFT JOIN
							usuario b
							ON 
								htev.usua_doc = b.usua_doc
						WHERE ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'  AND r.mrec_codi=c.mrec_codi 
							$whereTipoRadicado $whereDependencia ";			

					$orderE = "	ORDER BY $orno $ascdesc";			

		 	/** CONSULTA PARA VER TODOS LOS DETALLES 
	 		*/ 
                        $condiMedio = "AND c.mrec_codi = $mrecCodi"; //AND c.mrec_codi = $mrecCodi 
			$queryETodosDetalle = $queryEDetalle . $orderE;
			$queryEDetalle .= $condiMedio . $orderE;

			
		}break;
	//case 'oracle':
	//case 'ocipo':
	case 'oci8':
	case 'oci805':
		{	if ( $dependencia_busq != 99999)
			{	$condicionE = "	AND r.depe_codi =$dependencia_busq AND b.depe_codi = $dependencia_busq";	}
			$queryE = "SELECT c.mrec_desc MEDIO_RECEPCION, COUNT(1) Radicados, max(c.MREC_CODI) HID_MREC_CODI
					FROM RADICADO r, MEDIO_RECEPCION c, USUARIO b
					WHERE 
						r.radi_usua_radi=b.usua_CODI 
						AND r.mrec_codi=c.mrec_codi and  b.depe_codi = r.radi_depe_radi
						$condicionE
						AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin'
						$whereTipoRadicado
						$whereUsuario
					GROUP BY c.mrec_desc
					ORDER BY $orno $ascdesc";	
 			/** CONSULTA PARA VER DETALLES 
	 		*/
  			$condicionDep = " AND b.depe_codi = {$_GET['dependencia_busq']} ";

			$queryEDetalle =
			"
			SELECT r.RADI_NUME_RADI RADICADO
			  ,TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd hh:mi:ss') FECHA_RADICADO
			    ,c.MREC_DESC MEDIO_RECEPCION
				  ,r.RA_ASUN ASUNTO
				    ,b.usua_nomb USUARIO
					  ,r.RADI_PATH HID_RADI_PATH,R.SGD_SPUB_CODIGO,B.CODI_NIVEL as  USUA_NIVEL
					  FROM RADICADO r
					  inner join usuario b on b.usua_CODI = r.radi_usua_radi and b.depe_codi = r.radi_depe_radi
					  inner join MEDIO_RECEPCION c on c.mrec_codi =r.mrec_codi
			";
		/*	"SELECT distinct r.RADI_NUME_RADI RADICADO
						,TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd hh:mi:ss') FECHA_RADICADO
						,c.MREC_DESC MEDIO_RECEPCION
						,r.RA_ASUN ASUNTO
						,b.usua_nomb USUARIO
						,r.RADI_PATH HID_RADI_PATH{$seguridad}
					FROM RADICADO r, USUARIO b, MEDIO_RECEPCION c
					WHERE 
						r.radi_usua_radi=b.usua_CODI 
						AND r.mrec_codi=c.mrec_codi
						";*/
//					echo "<pre>$queryEDetalle</pre>";	exit;
		 if($_GET['mrecCodi']) 	$queryEDetalle .= "	AND c.mrec_codi={$_GET['mrecCodi']}";
					$queryEDetalle .=	$condicionE ."
						AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin'
						$whereTipoRadicado";			

					$orderE = "	ORDER BY $orno $ascdesc";			

		 	/** CONSULTA PARA VER TODOS LOS DETALLES 
	 		*/ 

			$queryETodosDetalle = $queryEDetalle . $orderE;
			$queryEDetalle .=  $orderE;
		}break;
}
if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1)
	$titulos=array("#","1#RADICADO","2#FECHA RADICADO","3#ASUNTO","6#MEDIO DE RECEPCION","9#USUARIO");
else 		
	$titulos=array("#","1#MEDIO","2#RADICADOS","2#VER");


function pintarEstadistica($fila,$indice,$numColumna)
{
	global $ruta_raiz,$_GET,$_GET,$krd,$usua_doc;
	$salida="";
	$titulos=array("#","1#MEDIO","2#RADICADOS","2#VER");
	switch ($titulos[$numColumna])
	{
		case  "#":
			$salida=$indice;
			break;
		case "1#MEDIO":
			$salida=$fila['MEDIO_RECEPCION'];
			break;
		case "2#RADICADOS":
			$salida=$fila['RADICADOS'];
			break;
		case "2#VER":
			$datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;codus=".$_GET['codus']."&amp;tipoRadicado=".$_GET['tipoRadicado']."&amp;tipoDocumento=".$_GET['tipoDocumento']."&amp;mrecCodi=".$fila['HID_MREC_CODI']."&amp;mrecCodi=".$fila['HID_MREC_CODI'];
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
			$titulos=array("#","1#RADICADO","2#FECHA RADICADO","3#ASUNTO","6#MEDIO DE RECEPCION","9#USUARIO");
			switch ($titulos[$numColumna]){
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
					
					case "6#MEDIO DE RECEPCION":
						$salida="<center class=\"leidos\">".$fila['MEDIO_RECEPCION']."</center>";			
						break;	
					case "9#USUARIO":
						$salida="<center class=\"leidos\">".$fila['USUARIO']."</center>";			
						break;
			}
			return $salida;
		}
