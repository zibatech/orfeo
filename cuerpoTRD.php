<?php
/**
* @author Jairo Losada   <jlosada@gmail.com>
* @author Cesar Augusto   <aurigadl@gmail.com>
* @author Correlibre.org // Tomado de version orginal realizada por JL en SSPD, modificado.
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
*
* @copyleft

OrfeoGpl Models are the data definition of OrfeoGpl Information System
Copyright (C) 2013 Infometrika Ltda.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Fou@copyrightndation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

session_start();
$ruta_raiz = ".";
include_once $ruta_raiz."/include/tx/sanitize.php";
//require $ruta_raiz."/vendor/autoload.php";
if($_REQUEST['radicado_a_buscar']){$radicados_a_buscar = $_REQUEST['radicado_a_buscar'];}

$ruta_raiz = ".";
if (!$_SESSION['dependencia'])
header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_REQUEST as $key => $valor)   ${$key} = $valor;
foreach ($_REQUEST as $key => $valor)   ${$key} = $valor;

define('ADODB_ASSOC_CASE', 1);
define('CIRC_INTERNA', 4);
define('CIRC_EXTERNA', 5);

$verrad         = "";
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$usua_email     = $_SESSION["usua_email"];
$codusuario     = $_SESSION["codusuario"];
$tip3Nombre     = $_SESSION["tip3Nombre"];
$tip3desc       = $_SESSION["tip3desc"];
$tip3img        = $_SESSION["tip3img"];
$descCarpetasGen= $_SESSION["descCarpetasGen"] ;
$descCarpetasPer= $_SESSION["descCarpetasPer"];
$verradPermisos = "Full"; //Variable necesaria en tx/txorfeo para mostrar dependencias en transacciones

$entidad=$_SESSION["entidad"];

$_SESSION['numExpedienteSelected'] = null;

  include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
  if (!$db) $db = new ConnectionHandler($ruta_raiz);
  $db->conn->debug = false;
  $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
  $sqlFecha = $db->conn->SQLDate("Y-m-d H:i A","b.RADI_FECH_RADI");
  $medios_recepcion = $db->conn->getAll('SELECT * FROM medio_recepcion ORDER BY MREC_CODI');
  if(strlen($orderNo)==0){
      $orderNo="2";
      $order = 3;
  }else{
      $order = $orderNo +1;
  }

if (!empty($_REQUEST['fecha_inicial']) && !empty($_REQUEST['fecha_final'])  ){
    $_SESSION['fecha_inicial'] = $_REQUEST['fecha_inicial'];
    $_SESSION['fecha_final'] = $_REQUEST['fecha_final'];
}


if (!empty($_REQUEST['medio_recepcion'])){
    $_SESSION['medio_recepcion'] = $_REQUEST['medio_recepcion'];
}

if (!empty($_REQUEST['resultados_query_cuerpo'])){
    $_SESSION['resultados'] = $_REQUEST['resultados_query_cuerpo'];
}
if (!empty($_REQUEST['fecha_final_b']) && !empty($_REQUEST['fecha_inicial_b'])  && $_REQUEST['fecha_inicial_b'] == "nA" && $_REQUEST['fecha_final_b'] == "nA"){
    $_SESSION['fecha_inicial'] = date('Y-m-d', strtotime('-6 month'));
    $_SESSION['fecha_final'] = date("Y-m-d");
}
if(empty($_SESSION['fecha_inicial']) && empty($_SESSION['fecha_final'])){
    $_SESSION['fecha_inicial'] =  date('Y-m-d', strtotime('-3 month'));
    $_SESSION['fecha_final'] = date("Y-m-d");
}
if(empty($_SESSION['resultados'])){
    $_SESSION['resultados'] =1;
}

//Start::no restablecer filtro de resultados
/*
if (empty($_REQUEST['resultados_query_cuerpo'])){
    $_SESSION['resultados'] = 10;
}
*/
//End::no restablecer filtro de resultados


  if(trim($orderTipo)=="") $orderTipo=" DESC ";

  if($orden_cambio==1){
		if(trim($orderTipo)!="DESC"){
			$orderTipo="DESC";
		}else{
			$orderTipo="ASC";
		}
  }

  if(!$carpeta) $carpeta=9998;
  if($carpeta==9998) $carpeta=0;
  if(!$nomcarpeta) $nomcarpeta = "Carpeta de Entrada";

  if(!$tipo_carp) $tipo_carp=0;

  /**
  * Este if verifica si se debe buscar en los radicados de todas las carpetas.
  * @$chkCarpeta char  Variable que indica si se busca en todas las carpetas.
  *
  */
  if($chkCarpeta){
      $chkValue=" checked ";
      $whereCarpeta = " ";
  }else{
      $chkValue="";
      if($carpeta!=9999){
         $whereCarpeta  = "and b.carp_codi=$carpeta  and b.carp_per=$tipo_carp ";
      }
  }

  $fecha_hoy      = Date("Y-m-d");
  $sqlFechaHoy    = $db->conn->DBDate($fecha_hoy);

  //Filtra el query para documentos agendados
  if ($agendado==1){
    $sqlAgendado=" and (radi_agend=1 and radi_fech_agend > $sqlFechaHoy) "; // No vencidos
  }else  if ($agendado==2){
    $sqlAgendado=" and (radi_agend=1 and radi_fech_agend <= $sqlFechaHoy)  "; // vencidos
  }

  if ($agendado){
    $colAgendado = "," .$db->conn->SQLDate("Y-m-d H:i A","b.RADI_FECH_AGEND").' as "Fecha Agendado"';
    $whereCarpeta="";
  }

  //Filtra teniendo en cienta que se trate de la carpeta Vb.
  if($carpeta==11 && $codusuario !=1 && $_REQUEST['tipo_carp']!=1){
      $whereUsuario = " and  (b.radi_usu_ante ='$krd' or b.radi_usua_actu='$codusuario') ";
  }else{
    $whereUsuario = " and b.radi_usua_actu='$codusuario' ";
  }

$sql_jefe="SELECT u.usua_codi 
FROM usuario u, autm_membresias a
WHERE u.id=a.autu_id
and a.autg_id=2
and u.depe_codi=".$dependencia;
$rs_jefe = $db->conn->Execute($sql_jefe);

//$whereUsuario =" and b.radi_usua_actu='".$rs_jefe->fields['USUA_CODI']."' ";

  $sqlNoRad = "select
                        b.carp_codi as carp, count(1) as COUNT
                from
                        radicado b left outer join SGD_TPR_TPDCUMENTO c on
                        b.tdoc_codi=c.sgd_tpr_codigo left outer join SGD_DIR_DRECCIONES d on
                        b.radi_nume_radi=d.radi_nume_radi
                where
                        b.radi_nume_radi is not null
                        and d.sgd_dir_tipo = 1
                and b.radi_depe_actu= $dependencia
                        $whereUsuario
                        GROUP BY B.carp_codi";

  $sqlTotalRad = "select count(1) as TOTAL
                  from  radicado b where  b.radi_depe_actu= $dependencia
                  $whereUsuario ";
  ?>
<html>
<head>
  <title>Sistema de informaci&oacute;n <?=$entidad_largo?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap core CSS -->
  <?php include_once "htmlheader.inc.php"; ?>
</head>
<style>
.dt-wrapper {
    overflow: hidden;
    overflow: auto;
}
.enviossalida{
    background-color: #0000ffa1;
    border-radius: 20px;
    color: white;
    padding: 1px 2 0 2;
    font-size: 11px;
    position: relative;
    top: -4px;
    left: -4px;
}
</style>

<body>
<form method="get" id="filtro_fechas">
    <input type="hidden" name="c" value="<?=empty($_REQUEST['c'])?'':$_REQUEST['c']?>">
    <input type="hidden" name="nomcarpeta" value="<?=empty($_REQUEST['nomcarpeta'])?'':$_REQUEST['nomcarpeta']?>">
    <input type="hidden" name="tipo_carpt" value="<?=empty($_REQUEST['tipo_carpt'])?'':$_REQUEST['tipo_carpt']?>">
    <input type="hidden" name="order" value="<?=empty($_REQUEST['order'])?'':$_REQUEST['order']?>">
    <input type="hidden" name="carpeta" value="<?=empty($_REQUEST['carpeta'])?'':$_REQUEST['carpeta']?>">
    <input type="hidden" name="radicado_a_buscar" value="<?=empty($_REQUEST['radicado_a_buscar'])?'':$_REQUEST['radicado_a_buscar']?>">
    <input type="hidden" name="fecha_inicial" id="fecha_inicial" value="<?=empty($_SESSION['fecha_inicial'])?'':$_SESSION['fecha_inicial']?>">
    <input type="hidden" name="fecha_final" id="fecha_final" value="<?=empty($_SESSION['fecha_final'])?'':$_SESSION['fecha_final']?>">
    <input type="hidden" name="medio_recepcion" id="medio_recepcion" value="<?=empty($_SESSION['medio_recepcion'])?'':$_SESSION['medio_recepcion']?>">
    <input type="hidden" name="resultados_query_cuerpo" id="resultados_query_cuerpo" value="<?=empty($_SESSION['resultados'])?'':$_SESSION['resultados']?>">
</form>
<form method="get" id="borrar_filtro_fechas">
    <input type="hidden" name="c" value="<?=empty($_REQUEST['c'])?'':$_REQUEST['c']?>">
    <input type="hidden" name="nomcarpeta" value="<?=empty($_REQUEST['nomcarpeta'])?'':$_REQUEST['nomcarpeta']?>">
    <input type="hidden" name="tipo_carpt" value="<?=empty($_REQUEST['tipo_carpt'])?'':$_REQUEST['tipo_carpt']?>">
    <input type="hidden" name="order" value="<?=empty($_REQUEST['order'])?'':$_REQUEST['order']?>">
    <input type="hidden" name="carpeta" value="<?=empty($_REQUEST['carpeta'])?'':$_REQUEST['carpeta']?>">
    <input type="hidden" name="radicado_a_buscar" value="<?=empty($_REQUEST['radicado_a_buscar'])?'':$_REQUEST['radicado_a_buscar']?>">
    <input type="hidden" name="fecha_inicial_b" id="fecha_inicial_b" value="nA">
    <input type="hidden" name="fecha_final_b" id="fecha_final_b" value="nA">
    <input type="hidden" name="medio_recepcion_b" id="medio_recepcion" value="">
</form>

  <form name=form1 id=form1 action="./tx/formEnvio.php?<?=$encabezado?>#informados" methos=post/  >
  <div id="content" style="opacity: 1;">
    <div class="row">
      <div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
      <h1 class="page-title txt-color-blueDark">
        <i class="glyphicon glyphicon-inbox"></i> Bandeja <span> TRD </span></h1>
      </div>
    </div>

    <!-- widget grid -->
    <section id="widget-grid" class="">
      <!-- row -->
<div class="row">

	<!-- NEW WIDGET START -->
	<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<!-- Widget ID (each widget will need unique ID)-->
        <div class="jarviswidget jarviswidget-color-darken"
            id="wid-id-0"
            data-widget-editbutton="false">
			<!-- widget div-->
			<div>
					<!-- widget content -->
					<div class="actions smart-form"
                         style="position: absolute !important;
                                top: 109;
                                z-index: 1;
                                left: 246px;">
						<?php
						$controlAgenda=1;
						if($carpeta==11 and !$tipo_carp and $codusuario!=1){
						}else{?>
								<?php include "./tx/txOrfeo.php";
						}
						?>
					</div>
					<div class="widget-body no-padding">
                            <div class="widget-body-toolbar"
                                style="border: 1px #ccc solid;margin-bottom: 40px">
                                <h3>Filtrar por fechas</h3>
                                <label>Pagina </label>
                                 <input type="number" max="1000" min="0" value="<?=$_SESSION['resultados']?>" name="resultados_aux" id="resultados_aux"> de <span id="total_bandeja" style="font-weight: bolder;"> </span> paginas
                                <label>Fecha inicial</label>
                                <input type="date" name="fecha_inicial_aux" id="fecha_inicial_aux" autocomplete="off"  format="YYYY-MM-DD"
                                       value="<?=empty($_SESSION['fecha_inicial'])?'':$_SESSION['fecha_inicial']?>">
                                <label>Fecha final</label>
                                <input type="date" name="fecha_final_aux" id="fecha_final_aux" autocomplete="off" format="YYYY-MM-DD"
                                       value="<?=empty($_SESSION['fecha_final'])?'':$_SESSION['fecha_final']?>">
                                <label>Medio de recepción</label>
                                <select name="medio_recepcion_aux" id="medio_recepcion_aux">
                                    <option value="todos">Todos</option>
                                    <?php foreach($medios_recepcion as $medio): ?>
                                        <option <?= $medio['MREC_CODI'] == $_SESSION['medio_recepcion'] ? "selected" : ''?> value="<?=$medio['MREC_CODI']?>"><?=$medio['MREC_DESC']?></option>
                                    <?php endforeach; ?>
                                </select>
                                <img style="display: none" id="cargando_bandeja" src="https://cdn.shortpixel.ai/client/q_glossy,ret_img,w_800,h_600/https://codigofuente.io/wp-content/uploads/2018/09/progress.gif" width="40">
                                <input type="button" id="botongrande" value="Filtrar">
                                <input type="button" id="botongrandeBorrar" value="Borrar">
                            </div>
<table id="dt_basic" class="table table-bordered table-hover dataTable no-footer table-sm smart-form" width="100%">
	<thead>
     <tr>
      <th  style="width: 10px;">
          <label class="checkbox">
              <input type="checkbox" onclick="markAll();" value="checkAll" name="checkAll" id="checkAll">
              <i></i>
          </label>
      </th>
			<th style="">Radicado</th>
			<th style="">Alerta</th>
			<th style="">Fecha</th>
			<th style="">Asunto</th>
			<th style="">Remitente / Destinatario</th>
            <th style="">Identificación</th>
			<th style="">Expediente</th>
			<th style="">Enviado Por</th>
			<th style="">Tipo Documento</th>
			<th style="">Dias Restantes</th>
			<th style="">Ref</th>
            <th style="">Medio de recepción</th>

		</tr>
	</thead>
	<tbody>
	<?php
    include "$ruta_raiz/include/query/queryCuerpoTRD.php";
		$rs = $db->conn->Execute($isql);
     
    if(!empty($isqlconteo))
      $rs_conteo = $db->conn->Execute($isqlconteo);

		include_once "$ruta_raiz/tx/diasHabiles.php";
		$a = new FechaHabil($db);
    
    $contadorImagenes = 0;
    $aux= '';
    while(!$rs->EOF){
        $numeroRadicado        = $rs->fields["HID_RADI_NUME_RADI"];
        $fechaRadicado         = $rs->fields["HID_RADI_FECH_RADI"];
        $refRadicado           = $rs->fields["REFERENCIA"];
        $asuntoRadicado        = $rs->fields["ASUNTO"];
        $remitenteRadicado     = $rs->fields["REMITENTE"];
        $tipoDocumentoRadicado = $rs->fields["TIPO DOCUMENTO"];
        $fech_vcmto            = $rs->fields["FECHA_VCMTO"];
        $enviadoPor            = $rs->fields["ENVIADO POR"];
        $radiPath              = $rs->fields["HID_RADI_PATH"];
        $documentoUsuario      = $rs->fields["DOCUMENTO_USUARIO"];
        $tipo_rad              = $rs->fields["TIPO_RAD"];
        $mrec_desc             = $rs->fields["RADI_MREC_DESC"];

        if($aux === $rs->fields["HID_RADI_NUME_RADI"])
            goto siguiente;
        //  $radiLeido             = $rs->fields["HID_RADI_LEIDO"];
        $radianulado       = $rs->fields["HID_EANU_CODIGO"];
        //Datos obtenidos para pintar los radicados
        //Start::multiple
            $es_multiple = false;
            $es_multiple_radicado = false;
            $iSqlMemorandoMultipleCuerpo= "SELECT 
                count(*)	as TOTAL,
                string_agg(DISTINCT SGD_DIR_DRECCIONES.sgd_dir_nombre, ',') AS DESTINATARIOS,
                (SELECT count(*) FROM ANEXOS WHERE ANEXOS.radi_nume_salida ='$numeroRadicado' AND ANEX_ESTADO >= 2 ) AS RADICADO 
            FROM
                SGD_DIR_DRECCIONES 
            WHERE
                radi_nume_radi = '$numeroRadicado' 
                AND radi_nume_radi::text LIKE'%3' ";

            $iSqlMemorandoMultipleFinalizado= "
            SELECT count(t.*) as TOTAL
            FROM public.hist_eventos t
            WHERE radi_nume_radi =  '$numeroRadicado' and usua_doc = '$documentoUsuario' and sgd_ttr_codigo = '13'";

            $rsMemorandoMultipleCuerpo = $db->conn->query($iSqlMemorandoMultipleCuerpo);
            $rsMemorandoMultipleFinalizado = $db->conn->query($iSqlMemorandoMultipleFinalizado);
            $tieneAsignacion = 0;
            if ($rsMemorandoMultipleCuerpo) {

                if($rsMemorandoMultipleCuerpo->fields["TOTAL"] > 1 && $rs->fields["RADI_USUA_ACTU"]!=$codusuario){
                    $iSqlMemorandoMultipleFinalizadoPropio= "
                    SELECT count(t.*) as TOTAL
                    FROM public.hist_eventos t
                    WHERE radi_nume_radi =  '$numeroRadicado' and usua_doc = '$documentoUsuario' and sgd_ttr_codigo = '9'";
                    $rsMemorandoMultipleFinalizadopropio = $db->conn->query($iSqlMemorandoMultipleFinalizadoPropio);
                    if($rsMemorandoMultipleFinalizadopropio){
                        if($rsMemorandoMultipleFinalizadopropio->fields["TOTAL"] > 0){
                                goto siguiente;
                        }
                    }
                }
                if($rsMemorandoMultipleCuerpo->fields["TOTAL"] > 1 && $rs->fields["RADI_USUA_ACTU"]==$codusuario){
                    $remitenteRadicado = "Varios destinatarios";
                    $es_multiple = true;
                    if($rsMemorandoMultipleCuerpo->fields["RADICADO"]>0){
                        $es_multiple_radicado = true;
                        //goto siguiente;
                    }
                }
                if($rsMemorandoMultipleCuerpo->fields["TOTAL"] > 1){
                    $remitenteRadicado = "Varios destinatarios";
                    $es_multiple = true;
                    if($rsMemorandoMultipleCuerpo->fields["RADICADO"]>0){
                        $es_multiple_radicado = true;
                        //goto siguiente;
                    }
                }
                if($rsMemorandoMultipleFinalizado){
                    if($rsMemorandoMultipleFinalizado->fields["TOTAL"] > 0 && $rsMemorandoMultipleCuerpo->fields["TOTAL"] > 1){
                            goto siguiente;
                    }
                }
               
                if($rs->fields["RADI_USUA_ACTU"]==$codusuario && $rs->fields["HID_CARP_CODI"] == 12 && $carpeta != 12 && $rsMemorandoMultipleCuerpo->fields["TOTAL"] > 1 ){
                            goto siguiente;
                }
            }   
        //End::multiple
       
        //Start::expediente
        $iSqlexpTot= "select * from sgd_exp_expediente where radi_nume_radi in ($numeroRadicado) limit 1;";
        $rsiSqlexpTot = $db->conn->query($iSqlexpTot);
        $numExpediente = $rsiSqlexpTot->fields["SGD_EXP_NUMERO"];
        //End::expediente            

        if (empty($remitenteRadicado) && ($tipo_rad == CIRC_INTERNA || $tipo_rad == CIRC_EXTERNA)) {
          include_once("$ruta_raiz/include/tx/notificacion.php");
          $notificacion = new Notificacion($db);
          $destinatarios_circ = $notificacion->destinatariosPorRadicado($numeroRadicado);
          $remitenteRadicado = $destinatarios_circ[0]["DESTINATARIOS"];
        }

        $anexEstado = $rs->fields["ANEX_ESTADO"];
        $_radiLeido = $rs->fields["HID_RADI_LEIDO"];
        //$numExpediente = $rs->fields["SGD_EXP_NUMERO"];
        $diasRadicado = $a->getDiasRestantes($numeroRadicado,$fech_vcmto,$tipoDocumentoRadicado);

        unset($TipoAlerta);
        unset($ColorAlerta);
        unset($MensajeAlerta);

        unset($TipoAlerta2);
        unset($ColorAlerta2);
        unset($MensajeAlerta2);

        /**************** Script que colorea los radicados nuevos, leidos , por vencer y vencidos *******************/

        switch($_radiLeido){
        case 0:
            $TipoAlerta = "class='fa fa-circle'";
            $ColorAlerta =  "style='color:#356635;cursor:help'";
            $ColorAlertaNoLeido =  "<b>";
            $MensajeAlerta = "Radicado Nuevo";

            break;
        case 1:
            $TipoAlerta = "class='fa fa-circle'";
            $ColorAlerta =  "style='font-weight: bold; color:#3276B1;cursor:help'";
            $ColorAlertaleido =  "";
            $MensajeAlerta = "Leido";

            break;
        }

        //Debo calcular los dias del radicado antes
        if ($diasRadicado != "" ){
            if ($diasRadicado == "-" or $diasRadicado == "N/A ó termino no definido" ){
                #No se pintan.
            }else{
                if ($diasRadicado <= 0 ){
                    $TipoAlerta2 = "class='fa fa-circle'";
                    $ColorAlerta2 =  "style='color:#FE2E2E;cursor:help'";
                    $MensajeAlerta2 = "Vencido";
                }else{
                    if ($diasRadicado > 0 and $diasRadicado <= 3 ){
                        $TipoAlerta2 = "class='fa fa-circle'";
                        $ColorAlerta2 =  "style='color:#8A2908;cursor:help'";
                        $MensajeAlerta2 = "Por Vencer";
                    }
                }
            }
        }

        /*******************Script que colorea los radicados con anex_estado=4 (envíos)*******************/

        unset($anexEstadoEstilo);
        unset($anexEstadoEstiloLink);

        switch($anexEstado){
        case 3:
            $TipoAlerta = "class='fa fa-circle'";
            //$ColorAlerta =  "style='color:#FF8000;cursor:help'";
            $MensajeAlerta = "Marcado como Impreso";
            break;

        case 4: //(envios)
            //@anexEstadoEstilo estilo para el <tr>
            //@anexEstadoEstiloLink estilo para enlaces <a>
            $anexEstadoEstilo=" style='color: #356635'";
            $anexEstadoEstiloLink=" style='color: #356635'";
            break;
        }

        if($linkVerRadicado != ''){
            // $anexEstado_linkradi = " style='text-decoration: underline'";
        }

        /****************Mostrar icono (folder) para radicados dentro de Expedientes****************************/

        unset($radInExpStyle);

        if (strlen($numExpediente) > 0){

            $radInExpStyle="<img src='img/icon-folder-open-big.png' width=15 alt='Expediente : $numExpediente' title='Expedientes: $numExpediente'>";
        }

        /*******************************************************************************************************/

        if(strpos($radiPath,"/") != 0){
          $radiPath = "/".$radiPath;
        }

        $linkVerRadicado = "./verradicado.php?verrad=$numeroRadicado";
        $linkImagen = "$ruta_raiz/bodega".$radiPath;
        $contadorImagenes++;

        unset($leido);
        if($_radiLeido==0){
            $leido = "success";
        }
        unset($colorAnulado);
        if($radianulado == 2){
            $colorAnulado = " text-danger ";
        }
  ?>
        <tr  <?=$anexEstadoEstilo?> class="<?=$leido?> ">
          <td class="inbox-table-icon sorting_1 ">
             <div>
              <?php 
              if($es_multiple_radicado == false){
              ?>
                  <label class="checkbox">
                      <input id="<?=$numeroRadicado?>" name="checkValue[<?=$numeroRadicado?>]" value="CHKANULAR" type="checkbox">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <i></i>
                      <?php
                        $iSqlEstadoAnexos= null;
                        $anex_estado= null;
                        $envio_estado= null;
                        $img_estado = null;
                        $anex_estado = $rs->fields["ANEX_ESTADO"];
                        $envio_estado = $rs->fields["SGD_DEVE_CODIGO"];

                         if($anex_estado=='4') {$img_estado = "<img src='./bodega/sys_img/enviado.png' width=15 title='Archivo Enviado. . .'>"; }

                         if($envio_estado<>0 && $anex_estado=='2') {$img_estado = "<img src='./bodega/sys_img/devuelto.png' width=15 title='Archivo devuelto. . .'>"; }

                         if($envio_estado<>0 && $anex_estado=='3') {$img_estado = "<img src='./bodega/sys_img/devuelto.png' width=15 title='Archivo devuelto. . .'>"; }

                           $ultimoDigito = str_split($numeroRadicado);
                            if(end($ultimoDigito) == '2'){
                                      //Start::enviado en entradas
                                        $iSqlEntradaConteo= "
                                        SELECT count(a.*) as TOTAL
                                        FROM anexos a
                                        WHERE 
                                        anex_radi_nume =  '$numeroRadicado'
                                        and a.radi_nume_salida::text like '%1'
                                        and a.sgd_deve_codigo != 0 and  a.anex_estado in(2,3)";
                                                  $rsEntradaConteo = $db->conn->query($iSqlEntradaConteo);

                                      //End::enviado en entradas
                                      //Start::enviado en entradas
                                        $iSqlEntradaConteoEnviados= "
                                        SELECT count(a.*) as TOTAL
                                        FROM anexos  a
                                        WHERE
                                        anex_radi_nume =  '$numeroRadicado'
                                        and a.radi_nume_salida::text like '%1' 
                                        and anex_estado = 4";
                                                  $rsEntradaConteoEnviados = $db->conn->query($iSqlEntradaConteoEnviados);

                                        $iSqlEntradaConteoTotal= "
                                                SELECT count(a.*) as TOTAL
                                                FROM anexos  a
                                                WHERE
                                                anex_radi_nume =  '$numeroRadicado'
                                                and a.radi_nume_salida::text like '%1' 
                                                and anex_estado >= 2";
                                        $rsEntradaConteoTotal = $db->conn->query($iSqlEntradaConteoTotal);
                                                    $img_estado = '';
                                                    /*
                                                  for ($i=0;$i<$rsEntradaConteo->fields['TOTAL'];$i++){
                                                      $img_estado .= "<img src='https://cdn.iconscout.com/icon/premium/png-256-thumb/send-1984305-1677351.png' width=15 title='Archivo Enviado. . .'>";
                                                  }*/

                                          $img_estado .=  "<img src='./bodega/sys_img/enviado.png' width=15 title='Enviados . . .'> <span class='enviossalida' >".$rsEntradaConteoEnviados->fields['TOTAL']."</span>";
                                          $img_estado .=  "<img src='./bodega/sys_img/devuelto.png' width=15 title='Devueltos . . .'> <span class='enviossalida' >".$rsEntradaConteo->fields['TOTAL']."</span>";
                                          $img_estado .=  "<img src='./bodega/sys_img/bandejasalida.svg' width=15 title='Total salidas. . .'> <span class='enviossalida' >".$rsEntradaConteoTotal->fields['TOTAL']."</span>";



                              //End::enviado en entradas
                            }
                      ?>
                  </label>
              <?php
                }else{
              ?>
                  <div class="checkbox"  data-toggle="tooltip" data-placement="top" title="Solo lectura trámite conjunto">
                      <i></i>
                  </label> 
                  
              <?php
                }
              ?>
             </div>
          </td>
          <?php
              $fechasymd = date('ymdhis');
              if(!empty($radiPath)){
                  $extension = explode('.',$radiPath);
                  if ($extension[1] == 'pdf') {
                    //Muestra el archivo en una nueva pestanha sin usar el modal visor
                    //echo "<td class='inbox-data-from'> <div><small> <a target='_blank' href='$linkImagen'>$numeroRadicado</a></small> </div></td>";

                    //Muestra el pdf en el visor modal
                    echo "<td class='inbox-data-from'> 
                            <div><small> 
                              <a href='javascript:void(0)' class='abrirVisor' contador=$contadorImagenes link=$linkImagen>$numeroRadicado
                              </a>
                            </small>$radInExpStyle</div>$img_estado
                          </td>";

                    //Modal Visor
                    $visorId = "visor_".$contadorImagenes;
                    echo "<div id=$visorId style='display:none; 
                            position:fixed;
                            padding:26px 30px 30px;
                            top:0;
                            left:0;
                            right:0;
                            bottom:0;
                            z-index:2'>
                            <button class='cerrarVisor' type='button' style='float:right; background-color:red;' contador=$contadorImagenes><b>x</b></button>  
                            <!--iframe></iframe-->
                            $img_estado
                          </div>";
                  } else {
                    //Funcionalidad para descargar el archivo.
                    echo "<td > <div > <small> <a $anexEstado_linkradi  $anexEstadoEstiloLink
                        href='javascript:void(0)' onclick=\"funlinkArchivo('$numeroRadicado','$ruta_raiz');\">$numeroRadicado</a></small> $radInExpStyle</div> $img_estado</td>";
                  }
              }else{
                  echo "<td > <div > <small> $numeroRadicado</small> $radInExpStyle</div> $img_estado</td>";
              }
          ?>

        	<td align="center" > <a <?=$ColorAlerta?> title="<?=$MensajeAlerta?>" ><div <?=$TipoAlerta?>  ></div></a>
        	<?php if ($MensajeAlerta2 != ""){ ?>  <a <?=$ColorAlerta2?> title="<?=$MensajeAlerta2?>" ><div <?=$TipoAlerta2?>  ></div></a> <?php } ?></td>

    			<td > <div><small><a title="click para ver radicado <?=$numeroRadicado?>"  <?=$anexEstadoEstiloLink?> href="<?=$linkVerRadicado?>" target="mainFrame"><?=$fechaRadicado?></a></small></div></td>
    			<td width="250px"> <div><span><small><?=$asuntoRadicado?></small></span> </div> </td>
    			<td><div> <small><?=$remitenteRadicado?></small> </div> </td>
                <td><div><span><small><?=$documentoUsuario?></small></span> </div> </td>
    			<td><div><span><small><?=$numExpediente?></small></span> </div> </td>
    			<td><div> <small><?=$enviadoPor?></small> </div> </td>
    			<td><div> <small><?=$tipoDocumentoRadicado?></small> </div> </td>
    			<td><div> <small><?=$diasRadicado?></small> </div> </td>
    			<td><div><span><small><?=$refRadicado?></small></span> </div> </td>
    			<td><div><span><small><?=$mrec_desc?></small></span> </div> </td>

    		</tr>
  <?php
        siguiente:
        $aux= $rs->fields["HID_RADI_NUME_RADI"];
        $rs->MoveNext();
  	} 
  ?>
	</tbody>
</table>
    <?php
    if(isset($krd) && $krd=="AMERICAS")
        $paginacion = 1000;
    else
        $paginacion = 100;

      if(!empty($rs_conteo->fields['COUNT']) && ($rs_conteo->fields['COUNT'] /$paginacion) >1)
        $conteo_paginas =  ceil($rs_conteo->fields['COUNT'] /$paginacion);
      else
        $conteo_paginas = 1;
    
    ?>
    <script type="text/javascript">
      document.getElementById("total_bandeja").textContent="<?=$conteo_paginas?>";
    </script>

<?php
$xsql=serialize($isql);
$_SESSION['xsql']=$xsql;
echo "<a style='border:0px' href='./adodb/adodb-doc.inc.php?".session_name()."=".session_id()."' target='_blank'><img src='./adodb/compfile.png' width='40' heigth='    40' border='0' ></a>";
echo "<a href='./adodb/adodb-xls.inc.php?".session_name()."=".session_id()."' target='_blank'><img src='./adodb/spreadsheet.png' width='40' heigth='40' border='0'></a>";
?>
					</div>
					<!-- end widget content -->

				</div>
				<!-- end widget div -->

				</div>
			<!-- end widget -->
			</article>

	</div>
	<!-- end row -->

</section>
<!-- end widget grid -->
</div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
      $('.abrirVisor').click(function(){
        var contador = $(this).attr('contador');
        var link = $(this).attr('link');
        var visorId = "#visor_" + contador;
        $(visorId ).append("<iframe style='width:100%; height:100%; z-index:-2;' src=" + link + "></iframe>");
        $(visorId).dialog();
      });
      
      $('.cerrarVisor').click(function(){
        var visorId = "#visor_" + $(this).attr('contador');
        $(visorId).dialog('destroy');
      });
    });

    // Muestra las imagenes de los radicados
    function funlinkArchivo(numrad, rutaRaiz){
        var nombreventana = "linkVistArch";
        var url           = rutaRaiz + "/linkArchivo.php?<?php echo session_name()."=".session_id()?>"+"&numrad="+numrad;
        var ventana       = window.open(url,nombreventana,'scrollbars=1,height=50,width=250');
        //setTimeout(nombreventana.close, 70);
        return;
    }

    // DO NOT REMOVE : GLOBAL FUNCTIONS!
    pageSetUp();

    // PAGE RELATED SCRIPTS

    loadDataTableScripts();
    function loadDataTableScripts() {

			loadScript("js/plugin/datatables/jquery.dataTables-cust.js", dt_2);

			function dt_2() {
					loadScript("js/plugin/datatables/ColReorder.min.js", dt_3);
			}

			function dt_3() {
					loadScript("js/plugin/datatables/FixedColumns.min.js", dt_4);
			}

			function dt_4() {
					loadScript("js/plugin/datatables/ColVis.min.js", dt_5);
			}

			function dt_5() {
					loadScript("js/plugin/datatables/ZeroClipboard.js", dt_6);
			}

			function dt_6() {
					loadScript("js/plugin/datatables/media/js/TableTools.min.js", dt_7);
			}

			function dt_7() {
					loadScript("js/plugin/datatables/DT_bootstrap.js", runDataTables);
			}

	}

    function runDataTables() {

        /*
         * BASIC
         */
        $('#dt_basic').dataTable({
             //"sScrollX": "100%",
             //"bScrollCollapse": true,
             "bInfo": null,
             "iDisplayLength" : 27,
             "paging": false,
             "bPaginate": false,
             "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
             "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
             "oLanguage": {
				           "oPaginate": {
				             "sPrevious" : "Anterior",
                             "sNext"     : "Siguiente",
                             "sLast"     : "Ultima",
                             "sFirst"    : "Primera"
				           }
				         }
        });

        /* END BASIC */

        /* Add the events etc before DataTables hides a column */
        $("#datatable_fixed_column thead input").keyup(function() {
            oTable.fnFilter(this.value, oTable.oApi._fnVisibleToColumnIndex(oTable.fnSettings(), $("thead input").index(this)));
        });

        $("#datatable_fixed_column thead input").each(function(i) {
            this.initVal = this.value;
        });
        $("#datatable_fixed_column thead input").focus(function() {
            if (this.className == "search_init") {
                this.className = "";
                this.value = "";
            }
        });
        $("#datatable_fixed_column thead input").blur(function(i) {
            if (this.value == "") {
                this.className = "search_init";
                this.value = this.initVal;
            }
        });

        $("#botongrandeBorrar").click(function()
        {
            $("#botongrande").prop( "disabled", true );
            $("#botongrandeBorrar").prop( "disabled", true );
            $("#cargando_bandeja").css( "display", "initial" );
            $("#cargando_bandeja").show();
            $("#borrar_filtro_fechas").submit();
        });
        $("#botongrande").click(function()
        {
            $(this).prop( "disabled", true );
            $("#botongrandeBorrar").prop( "disabled", true );
            $("#cargando_bandeja").css( "display", "initial" );
            $("#cargando_bandeja").show();
            $("#fecha_inicial").val($("#fecha_inicial_aux").val())
            $("#fecha_final").val($("#fecha_final_aux").val())
            $("#medio_recepcion").val($("#medio_recepcion_aux").val())
            $("#resultados_query_cuerpo").val($("#resultados_aux").val())
            $("#filtro_fechas").submit();
        });

        var oTable = $('#datatable_fixed_column').dataTable({
            "sDom" : "<'dt-top-row'><'dt-wrapper't><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
            //"sDom" : "t<'row dt-wrapper'<'col-sm-6'i><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'>>",
            "oLanguage" : {
                "sSearch" : "Search all columns:"
            },
            "bSortCellsTop" : true
        });



        /*
         * COL ORDER
         */
        $('#datatable_col_reorder').dataTable({
            "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            "paging": false,
             "bPaginate": false,
            "sDom" : "R<'dt-top-row'Clf>r<'dt-wrapper't><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
            "fnInitComplete" : function(oSettings, json) {
                $('.ColVis_Button').addClass('btn btn-default btn-sm').html('Columns <i class="icon-arrow-down"></i>');
            }
        });

        /* END COL ORDER */

        /* TABLE TOOLS */
        $('#datatable_tabletools').dataTable({
            "sDom" : "<'dt-top-row'Tlf>r<'dt-wrapper't><'dt-row dt-bottom-row'<'row'<'col-sm-6'i><'col-sm-6 text-right'p>>",
            "oTableTools" : {
                "aButtons" : ["copy", "print", {
                "sExtends" : "collection",
                "sButtonText" : 'Save <span class="caret" />',
                "aButtons" : ["csv", "xls", "pdf"]
                }],
                "sSwfPath" : "js/plugin/datatables/media/swf/copy_csv_xls_pdf.swf"
            },
            "fnInitComplete" : function(oSettings, json) {
                $(this).closest('#dt_table_tools_wrapper').find('.DTTT.btn-group').addClass('table_tools_group').children('a.btn').each(function() {
                    $(this).addClass('btn-sm btn-default');
                });
            }
        });

        // Modal Link
        $('#AccionCaliope').on('change',function(event){
            var optionSelected = $(this).find("option:selected");
            var valueSelected  = optionSelected.val();
            if(valueSelected  == 21 || valueSelected  == 20){
                var text;
                $("input[name^='checkValue']:checked").each(function(index,value){
                    text =  (text === undefined)? $(value).attr('id') : text + ','+$(value).attr('id');
                });

                if(text !== undefined){
                    $('<div>').dialog({
                        modal: true,
                        open: function (){
                            if(valueSelected  == 21){
                                $(this).load('accionesMasivas/masivaAsignarTrd.php?radicados=' + text);
                            }
                            if(valueSelected  == 20){
                                $(this).load('accionesMasivas/masivaIncluirExp.php?radicados=' + text);
                            }
                        },
                        title: 'Acción Masiva',
                        width: "600px"
                    })
                }
            }

        });

    }
</script>
</body>
</html>
