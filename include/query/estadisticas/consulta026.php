<?php
/** RADICADOS DE ENTRADA RECIBIDOS DEL AREA DE CORRESPONDENCIA
	* 
	* @autor JAIRO H LOSADA - SSPD
	* @version ORFEO 3.1
	* 
	*/
$coltp3Esp = '"'.$tip3Nombre[3][2].'"';	
if(!$orno) $orno=1;
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

if($_GET["tipoDocumentos"]) $tipoDocumentos=$_GET["tipoDocumentos"];
if(!$_GET["tipoDocumentos"] && $_GET["tipoDocumento"] ) $tipoDocumentos=$_GET["tipoDocumento"];

$whereTipoDocumento = "";

switch($db->driver)
{

	case 'postgres':	
	{	
               if ( $dependencia_busq != 99999)
		{	$condicionE = "	b.DEPE_CODI=$dependencia_busq AND r.RADI_DEPE_ACTU=$dependencia_busq ";
		}else {
			$condicionE = "	r.radi_depe_actu=b.depe_codi";	
		}
		$whereDate=" date_part('year',r.radi_fech_radi)='".$ano_secuencia."' ";
      //echo "Entro ...por aka...";
		$queryE = "SELECT $ano_secuencia as YEAR,
				   sgd_trad_codigo as TRAD,
			           count(CASE when radi_path is null then 1 end) 			as RADICADOS,
				   min(SUBSTR(cast (radi_nume_radi as text), 8,7)) as INICIO,
				    max(SUBSTR(cast (radi_nume_radi as text), 8,7)) as FIN
				FROM RADICADO r 
			WHERE
				$whereDate
				$whereTipoRadicado 
			GROUP BY TRAD
			ORDER BY $orno $ascdesc"; 
		/** CONSULTA PARA VER DETALLES 
		*/

		if (!is_null($codUs))	$condicionE .= " and b.depe_codi = r.radi_depe_actu ";
		if(!empty($tipoDocumentos) and $tipoDocumentos!='9999' and $tipoDocumentos!='9998' and $tipoDocumentos!='9997')
		   {
		       $condicionE .= " AND t.SGD_TPR_CODIGO in ( ". $tipoDocumentos . ")";
		   }elseif ($tipoDocumentos=="9998")	
		    {
		      $condicionE .= " AND t.SGD_TPR_CODIGO = $tipoDOCumento ";
		   } 
                  
        $redondeo="date_part('days', r.radi_fech_radi-".$db->conn->sysTimeStamp.")+floor(t.sgd_tpr_termino * 7/5)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between r.radi_fech_radi and ".$db->conn->sysTimeStamp.")";
	$queryEDetalle = "SELECT DISTINCT 
			$radi_nume_radi	as RADICADO
			, r.radi_fech_radi::date		as FECHA_RADICADO
			,t.SGD_TPR_DESCRIP 			as TIPO_DE_DOCUMENTO
			, b.USUA_NOMB 				as USUARIO
			, r.RA_ASUN 				as ASUNTO
			, ".$db->conn->SQLDate('Y/m/d H:i:s','r.radi_fech_radi')." as FECHA_RADICACION
			, dir.SGD_DIR_NOMREMDES 	as REMITENTE
			, r.radi_usu_ante 		as U_ANTERIOR
			,r.RADI_PATH 				as HID_RADI_PATH{$seguridad},
			$redondeo					as \"Dias Restantes\" 
			FROM RADICADO r
				INNER JOIN USUARIO b ON r.RADI_USUA_ACTU=b.USUA_CODI AND r.RADI_DEPE_ACTU=b.DEPE_CODI
				LEFT OUTER JOIN SGD_TPR_TPDCUMENTO t ON r.tdoc_codi = t.SGD_TPR_CODIGO 
				LEFT OUTER JOIN SGD_DIR_DRECCIONES dir ON r.radi_nume_radi = dir.radi_nume_radi AND dir.sgd_dir_tipo = 1
		WHERE
		  r.RADI_PATH is NULL and
                  $whereDate
		  $whereTipoRadicado 
                  ";
		$orderE = "	ORDER BY FECHA_RADICADO $ascdesc";
                       $queryETodosDetalle = $queryEDetalle . $orderE;
                       $queryEDetalle .= $condicionUS . $orderE;
		}break;
}
		
if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1)
{
	$titulos=array("#","1#RADICADO","2#FECHA RADICACION","3#TIPO DE DOCUMENTO","4#USUARIO ACTUAL","5#ASUNTO","6#REMITENTE","7#USUARIO ANTERIOR");
}
else
{

	 $titulos=array("#","1#Año","2#Tipo","3#Inicio","4#Fin","5#Radicados sin Imagen");
}
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
    $salida=$fila['YEAR'];
    break;
  case 2:
    $salida=$fila['TRAD'];
    break;
  case 3:
    $salida=$fila['INICIO'];
    break;
  case 4:
    $salida=$fila['FIN'];
    break;
  case 5:
  $datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;usua_doc=$usua_doc&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;tipoRadicado=".$fila['TRAD']."&amp;tipoDocumento=".$_GET['tipoDocumento']."&amp;codUs=".$fila['HID_COD_USUARIO']."&amp;depeUs=".$fila['HID_DEPE_USUA']."&ano_secuencia=".$_GET['ano_secuencia'];
  $datosEnvioDetalle=(isset($_GET['usActivos']))?$datosEnvioDetalle."&amp;usActivos=".$_GET['usActivos']:$datosEnvioDetalle;
  $salida="<a href=\"genEstadistica.php?{$datosEnvioDetalle}&amp;krd={$krd}\"  target=\"detallesSec\" >".$fila['RADICADOS']."</a>";
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
  switch ($numColumna)
  {
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
     $radi = $fila['RADICADO'];
                 $resulVali = $verLinkArchivo->valPermisoRadi($radi);
                 $valImg = $resulVali['verImg'];
     if($valImg == "SI")
       $salida="<a class=\"vinculos\" href=\"{$ruta_raiz}verradicado.php?verrad=".$fila['RADICADO']."&amp;".session_name()."=".session_id()."&amp;krd=".$_GET['krd']."&amp;carpeta=8&amp;nomcarpeta=Busquedas&amp;tipo_carp=0 \" >".$fila['FECHA_RADICADO']."</a>";
     else 
                   $salida="<a class=vinculos href=javascript:noPermiso()>".$fila['FECHA_RADICADO']."</a>";
          break;
  case 3:
    $salida="<center class=\"leidos\">".$fila['TIPO_DE_DOCUMENTO']."</center>";   
    break;
  case 4:
    $salida="<center class=\"leidos\">".$fila['ASUNTO']."</center>";
    break;
  case 5:
    $salida="<center class=\"leidos\">".$fila['USUARIO']."</center>";     
    break;  
  case 6:
    $salida="<center class=\"leidos\">".$fila['REMITENTE']."</center>";     
    break;
  case 7:
    $salida="<center class=\"leidos\">".$fila['U_ANTERIOR']."</center>";     
    break;
  }
  return $salida;
}

                     //   $db->conn->debug = true;
?>
