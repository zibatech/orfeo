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

//$db->conn->debug=true;
if($_GET["tipoDocumentos"]) $tipoDocumentos=$_GET["tipoDocumentos"];
if(!$_GET["tipoDocumentos"] && $_GET["tipoDocumento"] ) $tipoDocumentos=$_GET["tipoDocumento"];

$whereTipoDocumento = "";

$queryE = "		SELECT
    da.DEPE_CODI || ' - ' || da.DEPE_NOMB as DEPENDENCIA_ACTUAL,
    count(DISTINCT r.radi_nume_radi)as RADICADOS,
    da.DEPE_CODI as CODI_DEPE_ACTUAL
    FROM
    radicado r LEFT JOIN (
        select
        h1.radi_nume_radi as radi_nume_radi,
        h1.id,
        h1.sgd_ttr_codigo as sgd_ttr_codigo,
        h1.usua_doc as usua_doc,
        h1.depe_codi as depe_codi
        from
        hist_eventos h1 INNER JOIN (
            select
            distinct hist_eventos.radi_nume_radi as radi_nume_radi,
            min(hist_eventos.id) as id
            from
            hist_eventos
            where
            hist_eventos.sgd_ttr_codigo = 2
            GROUP BY hist_eventos.radi_nume_radi) h2
            on h1.id = h2.id
        ) htev

        ON r.radi_nume_radi = htev.radi_nume_radi
        LEFT JOIN dependencia da
        ON r.radi_depe_actu = da.depe_codi
        WHERE
        ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'
        $whereTipoRadicado
        $whereDependencia
        GROUP BY CODI_DEPE_ACTUAL
        ORDER BY $orno $ascdesc";

/** CONSULTA PARA VER DETALLES
 */
$redondeo="date_part('days', r.radi_fech_radi-".$db->conn->sysTimeStamp.")+floor(t.sgd_tpr_termino * 7/5)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between r.radi_fech_radi and ".$db->conn->sysTimeStamp.")";

$queryEDetalle = "SELECT
    DISTINCT cast(r.radi_nume_radi as varchar(20)) as RADICADO ,
    r.RADI_FECH_RADI as FECHA_RADICADO,
    r.RA_ASUN as ASUNTO ,
    t.SGD_TPR_DESCRIP as TIPO_DE_DOCUMENTO,
    da.DEPE_CODI as CODIGO_DEPENDENCIA_ACTUAL ,
    da.DEPE_NOMB as DEPENDENCIA_ACTUAL ,
    c.usua_nomb AS USUARIO_ACTUAL,
    b.usua_nomb as Usuario ,
    dir.SGD_DIR_NOMBRE || ' ' || dir.SGD_DIR_APELLIDO as REMITENTE ,
    dir.SGD_DIR_NOMREMDES as DIGNATARIO,
    df.DEPE_NOMB as DEPENDENCIA_INICIAL ,
    r.RADI_NUME_FOLIO  AS NUMERO_FOLIOS,
    r.RADI_NUME_ANEXO  AS NUMERO_ANEXO,
    r.RADI_DESC_ANEX   AS DESCRIPCION_ANEXO,
    $redondeo					as DIAS_RESTANTES
    FROM
    dependencia df,
    dependencia da,
    radicado r
    left JOIN
    (select
	    h1.radi_nume_radi as radi_nume_radi,
	    h1.id,
	    h1.sgd_ttr_codigo as sgd_ttr_codigo,
	    h1.usua_codi as usua_codi,
	    h1.usua_doc as usua_doc,
	    h1.depe_codi as depe_codi
        FROM hist_eventos h1
	WHERE
		h1.sgd_ttr_codigo = 2
	ORDER BY h1.id LIMIT 1) htev ON r.radi_nume_radi = htev.radi_nume_radi
        LEFT JOIN usuario b ON
        htev.usua_doc = b.usua_doc
        LEFT OUTER JOIN SGD_TPR_TPDCUMENTO t ON r.tdoc_codi=t.SGD_TPR_CODIGO
        LEFT OUTER JOIN SGD_DIR_DRECCIONES dir ON r.radi_nume_radi = dir.radi_nume_radi and dir.sgd_dir_tipo = '1'
        LEFT JOIN USUARIO c ON r.radi_usua_actu=c.usua_CODI AND r.radi_depe_actu=c.depe_codi
        WHERE
        r.radi_depe_actu=da.depe_codi
        AND r.RADI_DEPE_RADI=df.DEPE_CODI
        AND TO_CHAR(r.radi_fech_radi,'YYYY/MM/DD') BETWEEN '$fecha_ini' AND '$fecha_fin'
        $whereTipoRadicado
        $whereDependencia ";

/** CONSULTA PARA VER TODOS LOS DETALLES
 */
$queryETodosDetalle = $queryEDetalle . $orderE;
$queryEDetalle .= $condicionUS . $orderE;

if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1)
{
	$titulos=array("#","1#RADICADO","2#FECHA RADICACION","5#ASUNTO","6#REMITENTE","3#CODIGO DEPENDENCIA ACTUAL","7#DEPENDENCIA_ACTUAL","4#USUARIO ACTUAL","8#NUMERO_FOLIOS","9#NUMERO_ANEXO","10#DESCRIPCION_ANEXO","11#DIAS_RESTANTES");
}
else
{

	$titulos=array("#","1#DEPENDENCIA","2#RADICADOS","2#VER");

}

function pintarEstadistica($fila,$indice,$numColumna)
{
 global $ruta_raiz,$_POST,$_GET,$krd;
 $salida="";
 $titulos=array("#","1#DEPENDENCIA","2#RADICADOS","2#VER");
 switch ($titulos[$numColumna])
 {
	case  "#":
	 $salida=$indice;
	 break;
	case "1#DEPENDENCIA":
	 $salida=$fila['DEPENDENCIA_ACTUAL'];
	break;
	case "2#RADICADOS":
			$salida=$fila['RADICADOS'];
	break;
	case "2#VER":

		$dependecia=isset($fila['HID_DEPE_USUA'])?$fila['HID_DEPE_USUA']:$_GET['dependencia_busq'];
		$datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;dependencia_busq=".$fila['CODI_DEPE_ACTUAL']."&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;tipoRadicado=".$_GET['tipoRadicado']."&amp;tipoDocumentos=".$GLOBALS['tipoDocumentos']."&amp;tipoDOCumento=".$fila['HID_TPR_CODIGO']."&amp;codus=".$_GET['codus'];
		$datosEnvioDetalle=(isset($_GET['usActivos']))?$datosEnvioDetalle."&amp;usActivos=".$_GET['usActivos']:$datosEnvioDetalle;
		$salida="<a href=\"genEstadistica.php?{$datosEnvioDetalle}&amp;krd={$krd} \"  target=\"detallesSec\" >ver</a>";

break;
	case 3:
	 $dependecia=isset($fila['HID_DEPE_USUA'])?$fila['HID_DEPE_USUA']:$_GET['dependencia_busq'];
	 $datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;usua_doc=".urlencode($fila['HID_USUA_DOC'])."&amp;depeUs=".$fila['HID_DEPE_USUA']."&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;tipoRadicado=".$_GET['tipoRadicado']."&amp;tipoDocumentos=".$GLOBALS['tipoDocumentos']."&amp;codUs=".$fila['HID_COD_USUARIO']."&amp;tipoDOCumento=".$fila['HID_TPR_CODIGO'];
	$datosEnvioDetalle=(isset($_GET['usActivos']))?$datosEnvioDetalle."&amp;usActivos=".$_GET['usActivos']:$datosEnvioDetalle;
	$salida="<a href=\"genEstadistica.php?{$datosEnvioDetalle}&amp;krd={$krd}\"  target=\"detallesSec\" >".$fila['RADICADOS']."</a>";
	break;
	}
	return $salida;
}

function pintarEstadisticaDetalle($fila,$indice,$numColumna){
	global $ruta_raiz,$encabezado,$krd,$db;
        include_once "$ruta_raiz/js/funtionImage.php";
        include_once "$ruta_raiz/tx/verLinkArchivo.php";
        $verLinkArchivo = new verLinkArchivo($db);
	$numRadicado=$fila['RADICADO'];

	$titulos=array("#","1#RADICADO","2#FECHA RADICACION","5#ASUNTO","6#REMITENTE","3#CODIGO DEPENDENCIA ACTUAL","7#DEPENDENCIA_ACTUAL", "4#USUARIO ACTUAL","8#NUMERO_FOLIOS","9#NUMERO_ANEXO","10#DESCRIPCION_ANEXO","11#DIAS_RESTANTES");
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
		case "2#FECHA RADICACION":
			$radi = $fila['RADICADO'];
			$resulVali = $verLinkArchivo->valPermisoRadi($radi);
			$valImg = $resulVali['verImg'];
			if($valImg == "SI")
				$salida="<a class=\"vinculos\" href=\"{$ruta_raiz}verradicado.php?verrad=".$fila['RADICADO']."&amp;".session_name()."=".session_id()."&amp;krd=".$_GET['krd']."&amp;carpeta=8&amp;nomcarpeta=Busquedas&amp;tipo_carp=0 \" >".$fila['FECHA_RADICADO']."</a>";
			else
				$salida="<a class=vinculos href=javascript:noPermiso()>".$fila['FECHA_RADICADO']."</a>";
		break;


		case "3#CODIGO DEPENDENCIA ACTUAL":
			$salida="<span class=\"leidos\">".htmlentities($fila['CODIGO_DEPENDENCIA_ACTUAL'])."</span>";
			break;
		case "4#USUARIO ACTUAL":
			$salida="<span class=\"leidos\">".$fila['USUARIO_ACTUAL']."</span>";
			break;
		case "5#ASUNTO":
			$salida="<span class=\"leidos\">".$fila['ASUNTO']."</span>";
			break;
		case "6#REMITENTE":
				$salida="<center class=\"leidos\">".$fila['REMITENTE']."</center>";
			break;
		case "7#DEPENDENCIA_ACTUAL":
			$salida="<span class=\"leidos\">".$fila['DEPENDENCIA_ACTUAL']."</span>";
			break;
		case "8#NUMERO_FOLIOS":
			$salida="<span class=\"leidos\">".$fila['NUMERO_FOLIOS']."</span>";
			break;
		case "9#NUMERO_ANEXO":
			$salida="<span class=\"leidos\">".$fila['NUMERO_ANEXO']."</span>";
			break;
		case "10#DESCRIPCION_ANEXO":
			$salida="<span class=\"leidos\">".$fila['USUARIO_ANTERIOR']."</span>";
			break;
		case "11#DIAS_RESTANTES":
			$salida="<span class=\"leidos\">".$fila['DIAS_RESTANTES']."</span>";
			break;

			}
		return $salida;
	}
?>
