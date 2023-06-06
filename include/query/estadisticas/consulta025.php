<?php
/** RADICADOS DE ENTRADA RECIBIDOSç
  * 
  * @autor JAIRO H LOSADA - SSPD
  * @version ORFEO 3.1
  * 
  */
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


$whereTipoDocumento = "";
if(!empty($tipoDocumentos) and $tipoDocumentos!='9999' and $tipoDocumentos!='9998' and $tipoDocumentos!='9997')
	{
		$whereTipoDocumento.=" AND t.SGD_TPR_CODIGO in ( ". $tipoDocumentos . ")";
	}elseif ($tipoDocumentos=="9997")	
	{
		$whereTipoDocumento.=" AND t.SGD_TPR_CODIGO = 0 ";
	}
$whereTipoRadicado  = str_replace("A.","r.",$whereTipoRadicado);
$whereTipoRadicado  = str_replace("a.","r.",$whereTipoRadicado);


switch($db->driver)
{ 
  case 'postgres':
  case 'oracle':
  case 'oci8':
  case 'oci805':
  case 'ocipo':
    { if ( $dependencia_busq != 99999)
      { $condicionE = " AND h.DEPE_CODI_DEST=$dependencia_busq AND b.DEPE_CODI=$dependencia_busq "; 
			  $condicionE = " AND b.DEPE_CODI=$dependencia_busq ";
			 }

$queryE = "
      SELECT MIN(b.DEPE_NOMB) DEPENDENCIA
          , count(r.RADI_NUME_RADI) RADICADOS
          , MIN(b.depe_codi) HID_DEPE_CODI
          , min(b.DEPE_CODI) HID_DEPE_USUA
        FROM RADICADO r , dependencia  b, HIST_EVENTOS h
        WHERE
          h.depe_codi_dest=b.depe_codi
          $condicionE
          AND h.RADI_NUME_RADI=r.RADI_NUME_RADI
          AND H.DEPE_CODI_DEST=b.DEPE_CODI
          AND h.SGD_TTR_CODIGO in(2,9,12,16)
          AND TO_CHAR(H.HIST_FECH,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin'
        $whereTipoRadicado
        ";

      $queryE .= " GROUP BY b.DEPE_NOMB  ORDER BY $orno $ascdesc ";

	   //echo "<pre>$queryE</pre>";

      /** CONSULTA PARA VER DETALLES 
      */
      $queryEDetalle = "SELECT 
          r.RADI_NUME_RADI RADICADO
          , u.USUA_NOMB USUARIO
          , b.depe_nomb
          , r.radi_fech_radi FECHA_RADICADION
          , r.RA_ASUN ASUNTO
          , r.radi_cuentai as REF 
          , TO_CHAR(r.RADI_FECH_RADI, 'DD/MM/YYYY HH24:MM:SS') FECHA_RADICACION
          , TO_CHAR(h.HIST_FECH, 'DD/MM/YYYY HH24:MM:SS') FECHA_DIGITALIZACION
          , r.RADI_PATH HID_RADI_PATH
          , b.DEPE_CODI HID_DEPE_USUA
          , t.SGD_TPR_TERMINO
          , t.SGD_TPR_DESCRIP
          , (select  usua_nomb from usuario u2 where u2.usua_doc=h.usua_doc limit 1) USUARIO_ORIGEN 
          , U.USUA_NOMB USUARIO_DESTINO
          , H.HIST_FECH FECHA_TRANSACCION
          , TTR.SGD_TTR_DESCRIP TRANSACCION
        FROM DEPENDENCIA b,  SGD_TPR_TPDCUMENTO t
          , RADICADO r , SGD_TTR_TRANSACCION TTR,
          HIST_EVENTOS h 
          left outer join usuario u on (h.HIST_DOC_DEST=u.USUA_DOC)
        WHERE 
    r.tdoc_codi=t.sgd_tpr_codigo 
		AND h.depe_codi_dest=b.depe_codi
		AND h.sgd_ttr_codigo=ttr.sgd_ttr_codigo
		$condicionE
		AND h.RADI_NUME_RADI=r.RADI_NUME_RADI
		AND h.SGD_TTR_CODIGO in(2,9,12,16)
    AND TO_CHAR(H.HIST_FECH,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
     $whereTipoRadicado ";

    if($depeUs) $condicionUS = " AND b.depe_codi = $depeUs "; 
    $orderE = " ORDER BY $orno $ascdesc";
    /** CONSULTA PARA VER TODOS LOS DETALLES 
    */ 
    $queryETodosDetalle = $queryEDetalle . $orderE;
    $queryEDetalle .= $condicionUS . $orderE; 
//echo "<pre>$queryEDetalle</pre>";

    }break;
}

if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1)
  $titulos=array("#","1#RADICADO","2#ASUNTO","3#FECHA RADICACION", "4#USUARIO_ORIGEN", "5#USUARIO_DESTINO", "6#DEPENDENCIA", "7#FECHA_TRANSACCION", "8#TRANSACCION");
else    
  $titulos=array("#","1#Dependencia","2#Radicados");

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
    $salida=$fila['DEPENDENCIA'];
    break;
  case 2:

  $datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;usua_doc=$usua_doc&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;tipoRadicado=".$_GET['tipoRadicado']."&amp;tipoDocumento=".$_GET['tipoDocumento']."&amp;codUs=".$fila['HID_COD_USUARIO']."&amp;depeUs=".$fila['HID_DEPE_USUA'];
  $datosEnvioDetalle=(isset($_GET['usActivos']))?$datosEnvioDetalle."&amp;usActivos=".$_GET['usActivos']:$datosEnvioDetalle;
  $salida="<a href=\"genEstadistica.php?{$datosEnvioDetalle}&amp;\"  target=\"detallesSec\" >".$fila['RADICADOS']."</a>";
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
  case 1:
     $radi = $fila['RADICADO'];
                 $resulVali = $verLinkArchivo->valPermisoRadi($radi);
                 $valImg = $resulVali['verImg'];
     if($valImg == "SI")
       $salida="<a class=\"vinculos\" href=\"{$ruta_raiz}verradicado.php?verrad=".$fila['RADICADO']."&amp;".session_name()."=".session_id()."&amp;krd=".$_GET['krd']."&amp;carpeta=8&amp;nomcarpeta=Busquedas&amp;tipo_carp=0 \" >".$fila['FECHA_RADICADO']."</a>";
     else 
                   $salida="<a class=vinculos href=javascript:noPermiso()>".$fila['FECHA_RADICADO']."</a>";
          break;
  case 2:
    $salida="<center class=\"leidos\">".$fila['ASUNTO']."</center>";   
    break;
  case 3:
    $salida="<center class=\"leidos\">".$fila['FECHA_RADICACION']."</center>";     
    break;  
  case 4:
    $salida="<center class=\"leidos\">".$fila['USUARIO_ORIGEN']."</center>";     
    break;  
  case 5:
    $salida="<center class=\"leidos\">".$fila['USUARIO_DESTINO']."</center>";     
    break;
  case 6:
    $salida="<center class=\"leidos\">".$fila['DEPE_NOMB']."</center>";     
    break;    
  case 7:
    $salida="<center class=\"leidos\">".$fila['FECHA_TRANSACCION']."</center>";      
    break;
  case 8:
    $salida="<center class=\"leidos\">".$fila['TRANSACCION']."</center>";      
    break;
  }
  return $salida;
}
//$db->conn->debug = true;
//echo $queryEDetalle;
?>
