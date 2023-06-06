<?php
/** RADICADOS DE ENTRADA RECIBIDOSç
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


if($_GET["tipoDocumentos"]) $tipoDocumentos=$_GET["tipoDocumentos"];

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

if(!empty($_GET["depeUs"])){

  $depeUsu=$_GET["depeUs"];
}

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
            SELECT
                b.USUA_NOMB as USUARIO,
                count(DISTINCT r.radi_nume_radi) as RADICADOS,
                MIN(b.USUA_CODI) as HID_COD_USUARIO,
                MIN(b.depe_codi) as HID_DEPE_USUA
            FROM
                radicado r
                LEFT OUTER JOIN usuario b ON r.radi_usua_actu = b.usua_codi
                        and r.radi_depe_actu = b.depe_codi
            WHERE
             cast(r.radi_nume_radi as varchar) like '%2'
             AND ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'
             $whereDependencia
             $whereActivos
             GROUP BY b.USUA_NOMB ORDER BY $orno $ascdesc";

      /** CONSULTA PARA VER DETALLES */

      $filtroUsuario = "";
		if($genTodosDetalle!=1)
		$filtroUsuario = "and r.radi_depe_actu=$depeUsu and r.radi_usua_actu=$codUs";

		$queryEDetalle = "	SELECT
								DISTINCT cast(r.radi_nume_radi as varchar(20)) as RADICADO ,
								r.RADI_FECH_RADI as FECHA_RADICADO,
								t.SGD_TPR_DESCRIP as TIPO_DE_DOCUMENTO,
								r.RA_ASUN as ASUNTO , b.usua_nomb as Usuario ,
                                r.radi_nume_deri as ASOCIADO,
								anx.radi_nume_salida as RADICADO_SALIDA ,
								anx.anex_estado as ESTADO ,
								r.RADI_PATH as IMAGEN ,
								anx.anex_nomb_archivo as ARCHIVO_ANEXO,
								anx.anex_fech_anex as FECHA_ANEXO,
								anx.anex_creador as CREADOR,
                                da.DEPE_NOMB as DEPENDENCIA_ACTUAL ,
                                c.usua_nomb AS USUARIO_ACTUAL,
                                h2f.NOMBRE_FIRMANTE as FIRMANTE
                            FROM dependencia da
                                ,radicado r
							LEFT OUTER JOIN usuario b ON r.radi_depe_actu = b.depe_codi and r.radi_usua_actu = b.usua_codi
							LEFT OUTER JOIN SGD_TPR_TPDCUMENTO t ON r.tdoc_codi=t.SGD_TPR_CODIGO
							LEFT OUTER JOIN SGD_DIR_DRECCIONES dir ON r.radi_nume_radi = dir.radi_nume_radi and dir.sgd_dir_tipo = '1'
							LEFT OUTER JOIN USUARIO c ON r.radi_usua_actu=c.usua_CODI AND r.radi_depe_actu=c.depe_codi
							LEFT OUTER JOIN anexos anx ON r.radi_nume_radi=anx.anex_radi_nume and anx.anex_salida =1
                                                            and  anx.radi_nume_salida is not null
                            LEFT OUTER JOIN (
                                        select
                                            h2x.radi_nume_radi,
                                            us2.usua_nomb as nombre_firmante
                                        from
                                            hist_eventos h2x,
                                            usuario us2
                                        where
                                            h2x.sgd_ttr_codigo = 40 and
                                            us2.usua_codi = h2x.usua_codi and
                                            us2.depe_codi = h2x.depe_codi) h2f ON anx.radi_nume_salida = h2f.radi_nume_radi
							WHERE
                            cast(r.radi_nume_radi as varchar) like '%2'
							and r.radi_depe_actu=da.depe_codi and
                            TO_CHAR(r.radi_fech_radi,'YYYY/MM/DD') BETWEEN '$fecha_ini' AND '$fecha_fin'
							$filtroUsuario
                            $whereDependencia ";
							$orderE = "	ORDER BY $orno $ascdesc"; //

    /** CONSULTA PARA VER TODOS LOS DETALLES
    */
    $queryETodosDetalle = $queryEDetalle;
    }break;
}

if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1)
$titulos=array("#",
"1#RADICADO PRINCIPAL",
"2#FECHA RADICADO",
"3#TIPO DOCUMENTO",
"4#ASUNTO",
"5#USUARIO",
"6#RADICADO",
"6#ESTADO ACTUAL <BR>RADICADO",
"6#ESTADO ACTUAL",
"6#PROYECTADO POR",
"9#FECHA_ANEXO",
"6#Estado Dos<br><img src='../imagenes/docRadicado.gif' title='Se Genero Radicado. . .'>",
"7#FECHA RADICADO",
"6#Estado Cuatro<br><img src='../imagenes/docEnviado.gif' title='Archivo Enviado. . .'>",
"6#FECHA ENVIO",
"9#DEPENDENCIA ACTUAL RADI PRINCIPAL",
"10#USUARIO ACTUAL RADI PRINCIPAL",
"11#FIRMANTE",
"12#ASOCIADO");
else
  $titulos=array("#","1#USUARIO","2#RADICADOS","3#VER");

function pintarEstadistica($fila,$indice,$numColumna)
{
  global $ruta_raiz,$_POST,$_GET,$krd;
  $salida="";
  $titulos=array("#","1#USUARIO","2#RADICADOS","3#VER");
  switch ($titulos[$numColumna])
  {
    case  "#":
      $salida=$indice;
      break;
    case "1#USUARIO":
      $salida=$fila['USUARIO'];
      break;
    case "2#RADICADOS":

      $salida=$fila['RADICADOS'];
      break;
    case "3#VER":
      $datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;usua_doc=".urlencode($fila['HID_USUA_DOC'])."&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;tipoRadicado=".$_GET['tipoRadicado']."&amp;tipoDocumentos=".$GLOBALS['tipoDocumentos']."&amp;codUs=".$fila['HID_COD_USUARIO']."&amp;depeUs=".$fila['HID_DEPE_USUA'];
      $datosEnvioDetalle=(isset($_GET['usActivos']))?$datosEnvioDetalle."&codExp=$codExp&amp;usActivos=".$_GET['usActivos']:$datosEnvioDetalle;
      $salida="<a href=\"genEstadistica.php?{$datosEnvioDetalle}&codEsp=".$_GET["codEsp"]."&amp;krd={$krd}\"  target=\"detallesSec\" >ver</a>";
      break;
    case "3#Tramitados":
      $salida=$fila['TRAMITADOS'];
      break;
    default: $salida=false;
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
    $fecha_envio ="";

    $titulos=array("#",
        "1#RADICADO PRINCIPAL",
        "2#FECHA RADICADO",
        "3#TIPO DOCUMENTO",
        "4#ASUNTO",
        "5#USUARIO",
        "6#RADICADO",
        "6#IMAGEN ESTADO RADI SALIDA",
        "6#ESTADO RADI SALIDA",
        "6#Estado Uno<br><img src='../imagenes/docRecibido.gif' title='Se cargo un Archivo. . .'>",
        "9#FECHA_ANEXO",
        "6#<img src='../imagenes/docRadicado.gif' title='Se Genero Radicado. . .'>",
        "7#FECHA RADICADO",
        "6#<img src='../imagenes/docEnviado.gif' title='Archivo Enviado. . .'>",
        "6#FECHA ENVIO",
        "9#DEPENDENCIA ACTUAL RADI PRINCIPAL",
        "10#USUARIO ACTUAL RADI PRINCIPAL",
        "11#FIRMANTE",
        "12#ASOCIADO");

    if(empty($fila['RADICADO_SALIDA'])){
        $fila['RADICADO_SALIDA'] = '  ';
    }

    $isql_int = "select
        u.USUA_LOGIN  as \"USUARIO\",
        a.sgd_renv_fech as \"FECHA_ENVIO\"

        from sgd_renv_regenvio a left join dependencia b on (
            a.depe_codi=b.depe_codi)
            left join usuario u on (a.usua_doc=cast (u.usua_doc as numeric))
            , sgd_fenv_frmenvio c
            where
            a.usua_doc is not NULL and
            a.radi_nume_sal = ".$fila['RADICADO_SALIDA']."
            AND a.sgd_fenv_codigo = c.sgd_fenv_codigo
            order by a.SGD_RENV_FECH desc  limit 1 ";
    $rs3E = $db->conn->query($isql_int);

    switch ($titulos[$numColumna])
    {
    case '#':
        $salida=$indice;
        break;
    case "1#RADICADO PRINCIPAL":
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
    case "6#RADICADO":
        $salida="<center class=\"leidos\">".$fila['RADICADO_SALIDA']."</center>";
        break;
    case "6#IMAGEN ESTADO RADI SALIDA":
        switch ($fila['ESTADO']) {
        case 0:
            $img_estado = "<img src='../imagenes/docRecibido.gif' title='Se cargo un Archivo. . .'> ";
            break;
        case 1:
            $img_estado = "<img src='../imagenes/docRecibido.gif' title='Se cargo un Archivo. . .'> ";
            break;
        case 2:
            $img_estado = "<img src='../imagenes/docRadicado.gif' title='Se Genero Radicado. . .'> ";
            break;
        case 3:
            $img_estado = "<img src='../imagenes/docImpreso.gif' title='Se marco radicado y listo para enviar . . .'>";
            break;
        case 4:
            $img_estado = "<img src='../imagenes/docEnviado.gif' title='Archivo Enviado. . .'>";
            break;
        default:
            $img_estado = "n.a";
            break;
        }
        $salida="<center class=\"leidos\">".$img_estado."</center>";
        break;

    case "6#ESTADO RADI SALIDA":

        switch ($fila['ESTADO']) {
        case 0:
            $desc_estado = "Se cargo un archivo";
            break;
        case 1:
            $desc_estado = "Se cargo un archivo";
            break;
        case 2:
            $desc_estado = "Se genero radicado";
            break;
        case 3:
            $desc_estado = "Se marco radicado y listo para enviar";
            break;
        case 4:
            $desc_estado = "Archivo enviado";
            break;
        default:
            $desc_estado = "n.a";
            break;
        }
        $salida="<center class=\"leidos\">".$desc_estado."</center>";
        break;
    case "6#Estado Uno<br><img src='../imagenes/docRecibido.gif' title='Se cargo un Archivo. . .'>":
        $salida="<center class=\"leidos\">".$fila['CREADOR']."</center>";
        break;
    case "6#<img src='../imagenes/docRadicado.gif' title='Se Genero Radicado. . .'>":
        $sql_interno = "SELECT
            usua_nomb as \"USUARIO\",
            hist_eventos.\"id\"
            FROM
            hist_eventos
            INNER JOIN
            usuario
            ON
            hist_eventos.depe_codi = usuario.depe_codi AND
            hist_eventos.usua_codi = usuario.usua_codi
            WHERE
            hist_eventos.radi_nume_radi = ".$fila['RADICADO_SALIDA']." AND
            hist_eventos.sgd_ttr_codigo = 2  ORDER BY \"id\" DESC limit 1 ";

        $rsE = $db->conn->query($sql_interno);

        $salida="<center class=\"leidos\">".$rsE->fields['USUARIO']."</center>";
        break;

    case "7#FECHA RADICADO":
        $sql_interno = "SELECT
            hist_fech  AS \"FECHA\"
            FROM
            hist_eventos
            INNER JOIN
            usuario
            ON
            hist_eventos.depe_codi = usuario.depe_codi AND
            hist_eventos.usua_codi = usuario.usua_codi
            WHERE
            hist_eventos.radi_nume_radi = ".$fila['RADICADO_SALIDA']." AND
            hist_eventos.sgd_ttr_codigo = 2 limit 1 ";


        $rs2E = $db->conn->query($sql_interno);

        $salida="<center class=\"leidos\">".$rs2E->fields["FECHA"]."</center>";
        break;
    case "6#<img src='../imagenes/docEnviado.gif' title='Archivo Enviado. . .'>":

        $salida="<center class=\"leidos\">".$rs3E->fields["USUARIO"]."</center>";
        break;

    case "6#FECHA ENVIO":
        $salida="<center class=\"leidos\">".$rs3E->fields["FECHA_ENVIO"]."</center>";
        break;
    case "8#ARCHIVO_ANEXO":
        $salida="<center class=\"leidos\">".$fila['ARCHIVO_ANEXO']."</center>";
        break;
    case "9#FECHA_ANEXO":
        $salida="<center class=\"leidos\">".$fila['FECHA_ANEXO']."</center>";
        break;
    case "9#DEPENDENCIA ACTUAL RADI PRINCIPAL":
        $salida="<center class=\"leidos\">".$fila['DEPENDENCIA_ACTUAL']."</center>";
        break;
    case "10#USUARIO ACTUAL RADI PRINCIPAL":
        $salida="<center class=\"leidos\">".$fila['USUARIO_ACTUAL']."</center>";
        break;
    case "11#FIRMANTE":
        $salida="<center class=\"leidos\">".$fila['FIRMANTE']."</center>";
        break;
    case "12#ASOCIADO":
        $salida="<center class=\"leidos\">".$fila['ASOCIADO']."</center>";
        break;
    }

    return $salida;
}
//$db->conn->debug = true;
//$queryEDetalle;
?>
