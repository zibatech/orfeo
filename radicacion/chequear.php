<?php
/**
 * @module chequear
 *
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @author Jairo Losada   <jlosada@gmail.com>
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright

 SIIM2 Models are the data definition of SIIM2 Information System
 Copyright (C) 2013 Infometrika Ltda.

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

session_start();
define('CIRC_EXTERNA', 5);

$ruta_raiz = "..";
if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$tpNumRad    = $_SESSION["tpNumRad"];
$tpPerRad    = $_SESSION["tpPerRad"];
$tpDescRad   = $_SESSION["tpDescRad"];
$tpDepeRad   = $_SESSION["tpDepeRad"];
$radMail     = $_GET["radMail"];
$tip3Nombre  = $_SESSION["tip3Nombre"];

$nombreTp1   = $tip3Nombre[1][$ent];
$nombreTp2   = $tip3Nombre[2][$ent];
$nombreTp3   = $tip3Nombre[3][$ent];

include_once "../include/db/ConnectionHandler.php";

$tipoMed = $_SESSION['tipoMedio'];

$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$fechah = date("dmy") . "_" . time("hms");
if($buscar_por_radicado) $buscar_por_radicado = trim($buscar_por_radicado);

if( !$_SESSION['tipoMedio']){
    $tipoMedio = $_GET['tipoMedio'];
    if(!$tipoMedio) $_POST['tipoMedio'];
}

if ($tipoMedio=='eMail' or $_SESSION['tipoMedio']=='eMail'){
    if($_GET['eMailPid']){
        $eMailAmp=$_GET['eMailAmp'];
        $eMailMid=$_GET['eMailMid'];
        $eMailPid=$_GET['eMailPid'];
        $_SESSION['eMailPid'] = $_GET['eMailPid'];
        $_SESSION['eMailMid'] = $_GET['eMailMid'];
    }else{
        $eMailPid = $_SESSION['eMailPid'];
        $eMailMid = $_SESSION['eMailMid'];
        $eMailAmp = $_SESSION['eMailAmp'];
    }
    $fileeMailAtach=$_GET['fileeMailAtach'];
}

$ano_ini = date("Y");
$mes_ini = substr("00".(date("m")-1),-2);

if ($mes_ini=="00"){
    $ano_ini=$ano_ini-1;
    $mes_ini="12";
}

$dia_ini = date("d");
if(!$fecha_ini) $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
$fecha_busq = date("Y/m/d") ;
if(!$fecha_fin) $fecha_fin = $fecha_busq;


if(!empty($Submit) and
    empty($buscar_por_cuentai or
    $buscar_por_radicado or
    $buscar_por_asunto or
    $buscar_por_correo or
    $buscar_por_nombres or
    $buscar_por_doc or
    $buscar_por_dep_rad or
    $buscar_por_exp )){

    $messEmpty = ("<div id='alertmessage'>
            <div class='alert alert-block alert-danger'>
                <a class='close' data-dismiss='alert' href='#'>×</a>
                <h4 class='alert-heading'>¡Debe digitar un Dato para realizar la b&uacute;squeda!</h4>
            </div>
        </div>");
}

?>
<html>

<head>
    <?php if (!isset($radMail)) include_once("$ruta_raiz/htmlheader.inc.php") ?>
    <?php include_once("$ruta_raiz/js/funtionImage.php")?>
    <link rel="stylesheet" href="../tooltips/jquery-ui.css">
    <script src="../tooltips/jquery-ui.js"></script>
    <link rel="stylesheet" href="../tooltips/tool.css">
    <script src="../tooltips/tool.js"></script>

    <style>
       #dataRad li {
         list-style-position:inside;
         white-space: nowrap;
         overflow: hidden;
         text-overflow: ellipsis;
       }
    </style>

<body>

<div id="content" style="opacity: 1;">

    <div class='row'>
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="well">
                <h1 class="page-title txt-color-blueDark">
                    Radicaci&oacute;n previa <?=$tRadicacionDesc ?>
                </h1>
                <p> Este paso previo de la radiación es para buscar datos ya existentes en
                    el sistema y que  permitan copiar la información para el nuevo radicado.
                    Al terminar la búsqueda puede escoger uno de la lista de radicados
                    encontrados y relacionar el que va a crear como un anexo de este,
                    asociarlo o simplemente copiar sus datos para generar el nuevo documento.
                </p>
            </div>
        </div>
    </div>
    <?=$messEmpty?>
    <div class='row'>
        <div class="col-sm-12 col-md-12 col-lg-12">
            <form   name="formulario" method="post"  class="smart-form"
                    action='chequear.php?<?=session_name()."=".session_id()?>
                    &krd=<?=$krd?>&dependencia=<?=$dependencia?>&krd=<?=$krd?>&faxPath=<?=$faxPath?>'>
                <input type=hidden name=ent value='<?=$ent?>'>
                <?php include "formRadPrevia.php"; ?>
            </form>
        </div>
    </div>

<?

if(!empty($messEmpty)){die;}

if(!$busq)  $busq = 1;
if(!$tip_rem){$tip_rem=3;}

if($Submit){
    if($busq ==1){$cuentai = $buscar_por;}
    if($busq ==2){$noradicado = $buscar_por;}
    if($busq ==3){$documento = $buscar_por;}
    if($busq ==4){$nombres = $buscar_por;}
}

$query = "select SGD_TRAD_CODIGO
    , SGD_TRAD_DESCR from sgd_trad_tiporad
    where SGD_TRAD_CODIGO=$ent";

$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$rs = $db->conn->query($query);

$tRadicacionDesc = " - " .strtoupper($rs->fields["SGD_TRAD_DESCR"]);
$datos_enviar = session_name()."=".trim(session_id())."&krd=$krd&fechah=$fechah&faxPath=$faxPath";
?>

<form action='NEW.php?<?=session_name()."=".trim(session_id())?>&dependencia=<?=$dependencia?>&faxPath=<?=$faxPath?>'
    method="post" name="form1">
    <?php if($Submit) { ?>
    <div class='row'>
        <div id="acciones" class="col-sm-12 col-md-12 col-lg-12">
        <div class="well">
            <input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'>
            <input type=hidden name=ent value='<?=$ent?>'>
                    <h4 class="txt-color-blueDark">Se ha encontrado información, desea crear el nuevo radicado como:</h4>
                    <h6 class="txt-color-blueDark"> Selecciona una opci&oacute;n para copiar los datos y crear un anexo apartir de uno anterior ó crea uno nuevo.</h6>
                    <input class='btn btn-success btn-sm' name='rad1' type=submit value='Nuevo (Copia Datos)'>
                    <input class='btn btn-success btn-sm' name='rad0' type=submit value='Como Anexo'>
                    <input class='btn btn-success btn-sm' name='rad2' type=submit value='Asociado'>
        </div>
        </div>
    </div>
    <?php }?>
<?php
$accion    = "&accion=buscar";
$variables = "&pnom=".strtoupper($pnom)."&papl = ".$papl."$sapl = ".$sapl."&numdoc = ".$numdoc.$accion;
$target    = "_parent";
$hoy       = date('d/m/Y');
$where     = " ";
$hace_catorce_dias = date ('d/m/Y', mktime (0,0,0,date('m'),date('d')-14,date('Y')));

$fecha_ini = mktime(00,00,00,substr($fecha_ini,5,2),substr($fecha_ini,8,2),substr($fecha_ini,0,4));
$fecha_fin = mktime(23,59,59,substr($fecha_fin,5,2),substr($fecha_fin,8,2),substr($fecha_fin,0,4));
$where_fecha = " and (a.radi_fech_radi >= ". $db->conn->DBTimeStamp($fecha_ini) ." and a.radi_fech_radi <= ". $db->conn->DBTimeStamp($fecha_fin).") " ;

$dato1=1;

$where_general  = " $where_fecha ";

if(!$and_cuentai){ $and_cuentai = " and ";}else { $and_cuentai = " or ";}
if(!$and_radicado){ $and_radicado = " and ";}else { $and_radicado = " or ";}
if(!$and_asunto){ $and_asunto = " and ";} else { $and_asunto = " or ";}
if(!$and_correo){ $and_correo = " and ";} else { $and_correo = " or ";}
if(!$and_exp){ $and_exp = " and ";}else { $and_exp = " or ";}
if(!$and_doc){ $and_doc = " and ";}else { $and_doc = " or ";}
if(!$and_nombres){ $and_nombres = " and ";}else { $and_nombres = " or ";}
$buscar_por_correo = trim($buscar_por_correo);
if($buscar_por_cuentai){ $where_general .= " $and_cuentai a.radi_cuentai like '%$buscar_por_cuentai%' ";}
if($buscar_por_radicado){ $where_general .= " $and_radicado a.radi_nume_radi = $buscar_por_radicado ";}
if($buscar_por_asunto){ $where_general .= " $and_asunto a.ra_asun like '%$buscar_por_asunto%'";}
if($buscar_por_dep_rad){ $where_general .= " $and a.radi_depe_radi in($buscar_por_dep_rad) ";}
if($buscar_por_correo){ $where_general .= " $and_correo d.SGD_DIR_MAIL like '%$buscar_por_correo%'";}

if($buscar_por_doc){
    $where_ciu .= " $and_doc d.SGD_DIR_DOC = '$buscar_por_doc'";
}

if($buscar_por_exp){
    $where_ciu .= " $and_exp  g.SGD_EXP_NUMERO LIKE '%$buscar_por_exp%' ";
}

$nombres = strtoupper(trim($buscar_por_nombres));
$nombres = ConnectionHandler::fullUpper($nombres);

if(trim($nombres)) {
    $array_nombre = explode(" ",$nombres);
    $strCamposConcat = $db->conn->Concat("UPPER(d.SGD_DIR_NOMREMDES)","UPPER(d.SGD_DIR_NOMBRE)");
    for($i=0;$i<count($array_nombre);$i++){
        $nombres = trim($array_nombre[$i]);
        $where_ciu .= " and $strCamposConcat LIKE '%$nombres%' ";
    }
}

$query_direcciones = "";

if(($buscar_por_doc) or trim($nombres)){
    $query_direcciones = " and a.radi_nume_radi in ( select d2.radi_nume_radi from sgd_dir_drecciones d2 where a.radi_nume_Radi = d2.radi_nume_radi";
    $query_direcciones .= " $where_ciu )";
}

$dato=2;
$estoybuscando = 0;

if ($ent == CIRC_EXTERNA) {
    $select_circular_ext = "sncd.sgd_notif_circ_dest_desc";

    $from_circular_ext = "SGD_NOTIF_NOTIFICACIONES snn, 
        SGD_NOTIF_CIRCULARES snc, 
        SGD_NOTIF_CIRCULAR_DESTINATARIO sncd,
        SGD_DIR_DRECCIONES d,";

    $where_circular_ext = " a.radi_nume_radi = snn.radi_nume_radi 
        and snn.sgd_notif_codigo = snc.sgd_notif_codigo 
        and snc.sgd_notif_circ_dest_codi = sncd.sgd_notif_circ_dest_codi ";
} else {
    $select_circular_ext = "d.SGD_DIR_NOMREMDES,
        d.SGD_DIR_DIRECCION,
        d.SGD_DIR_TELEFONO,
        d.SGD_DIR_MAIL,
        d.SGD_DIR_TIPO,
        d.SGD_DIR_NOMBRE";

    $from_circular_ext = "SGD_DIR_DRECCIONES d,";

    $where_circular_ext = " a.radi_nume_radi =d.radi_nume_radi ";
}

if($Submit=="Buscar" and
    ($buscar_por_cuentai or $buscar_por_radicado or
     $buscar_por_asunto or $buscar_por_correo or
     $buscar_por_nombres or $buscar_por_doc or
     $buscar_por_dep_rad or $buscar_por_exp )) {
    $estoybuscando = 1;
    $sqlFecha = $db->conn->SQLDate("d-m-Y H:i","a.RADI_FECH_RADI");

    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
    $wheretipo ="";
    if($ent != 1 || $ent != 2 || $ent != 3)
        $wheretipo = "and a.radi_nume_radi::text like '%$ent'";

    include "$ruta_raiz/include/query/queryChequear.php";
    $query   = $query1;
    $rsCheck = $db->query($query);
    $varjh   = 1;
    $cards   = '';

    if (!$rsCheck and $tpBuscarSel=="ok"){
        echo "<center><img src='img_alerta_1.gif' alt='No se encontraron los datos, intente buscar con otro nombre, apellido o No. IDD'>";
        echo "<center><font size='3' face='arial' class='etextomenu'><b>No se encontraron datos con las caracteristicas solicitadas</b></font>";
    } else {
        if($tpBuscarSel=="ok"){
            echo "<label class='input'>
                <input name='radicadopadre' type='radio' value='' title='Radicado No {$nume_radi}'> No tiene padre
                </label>";
        }

        $cent              = 0;
        $varjh             = 2;
        $radicado_anterior = 0;

        $ruta_raiz = "..";

        include_once "$ruta_raiz/tx/verLinkArchivo.php";

        $verLinkArchivo = new verLinkArchivo($db);

        while(!$rsCheck->EOF){
            $nombret_us1 = "";
            $dignatario1 = "";
            $nume_radi   = "";
            $nombret_us1 = "";
            $nombret_us2 = "";
            $nombret_us3 = "";
            $dato        = "";
            $fecha       = "";
            $cuentai     = "";
            $asociado    = "";
            $asunto      = "";
            $panel       = "";
            $cent        = 0;
            $sess        = session_name()."=".trim(session_id());
            $nume_radi   = $rsCheck->fields["RADI_NUME_RADI"];

            $nurad        = $nume_radi;
            $verrad       = $nume_radi;
            $verradicado  = $nume_radi;

            if ($ent == CIRC_EXTERNA) {
                $destinatario = $rsCheck->fields['SGD_NOTIF_CIRC_DEST_DESC'];
            }else {
                $nomb     = $rsCheck->fields['SGD_DIR_NOMBRE'];
                $dire     = $rsCheck->fields['SGD_DIR_DIRECCION'];
                $tele     = $rsCheck->fields['SGD_DIR_TELEFONO'];
                $mail1    = $rsCheck->fields['SGD_DIR_MAIL'];
            }

            $nume_deri    = $rsCheck->fields['RADI_NUME_DERI'];
            $imagenf      = $rsCheck->fields['RADI_PATH'];
            $hoj          = $rsCheck->fields['RADI_NUME_HOJA'];
            $asunto       = $rsCheck->fields['RA_ASUN'];
            $fecha        = $rsCheck->fields['RADI_FECH_RADI'];
            $derivado     = $rsCheck->fields['RADI_NUME_DERI'];
            $tipoderivado = $rsCheck->fields['RADI_TIPO_DERI'];
            $dir_tipo     = $rsCheck->fields['SGD_DIR_TIPO'];
            $cuentai      = $rsCheck->fields['RADI_CUENTAI'];
            $nume_exp     = $rsCheck->fields['SGD_EXP_NUMERO'];
            $tip_doc_des  = $rsCheck->fields['SGD_TPR_DESCRIP'];
            $usua_actu    = $rsCheck->fields['USUA_NOMB'];
            $depe_actu    = $rsCheck->fields['DEPE_ACTUAL'];
            $depe_radi    = $rsCheck->fields['DEPE_RADICA'];

            $no_tipo      = "true";
            $resulVali    = $verLinkArchivo->valPermisoRadi($nume_radi);
            $valImg       = $resulVali['verImg'];

            include "../ver_datosrad.php";

            $dignatario = $rsCheck->fields['SGD_DIR_NOMBRE'];

            if (trim($derivado)){
                switch ($tipoderivado) {
                case 0:
                    $asociado = "<strong>Anexos:</strong> $derivado";
                    break;
                case 1:
                    $asociado = "";
                    break;
                case 2:
                    $asociado = "<strong>Asociado:</strong> $derivado";
                    break;
                }
            }

            if(trim($imagenf)==""){
                $dato="Radicado sin imagen";
            }elseif($valImg == "SI"){
                $link = "{$ruta_raiz}/linkArchivo.php?numrad={$nume_radi}";
                $dato="<a class='vinculos' target='_blank' href='{$link}'>Ver Imagen</a>";
            }else{
                $dato="No tiene permiso para acceder a la imagen";
            }

            $panel .= "<li class='list-group-item'> $dato </li>";


            if($radicado_anterior!=$nume_radi){
                $panelH = "
                    <input name='radicadopadre'
                    type='radio'
                    value='{$nume_radi}'
                    title='Radicado No {$nume_radi}'>
                    Radicado: <strong>$nume_radi</strong> ";


                $panel .= "<li class='list-group-item'>
                    <strong> Asunto: </strong> $asunto </li>";

                if($nume_exp){
                    $panel .= "<li class='list-group-item'>
                     <strong> Expediente: </strong>  $nume_exp </li>";
                }else{
                    $panel .= "<li class='list-group-item'>
                     <strong> Expediente: </strong> No esta incluido en un expediente </li>";
                }

                if ($ent == CIRC_EXTERNA) {
                    $panel .= "<li class='list-group-item'>
                                <p><strong>Destinatarios: </strong> $destinatario</p>
                                <p><strong>Asociado: </strong> $asociado</p>
                               </li>";
                } else {
                    $panel .= "<li class='list-group-item'>
                                <p><strong>Remitente: </strong> $nomb</p>
                                <p><strong>Dirección: </strong> $dire</p>
                                <p><strong>Telefono: </strong> $tele</p>
                                <p><strong>Correo: </strong>  $mail1</p>
                                <p><strong>Asociado: </strong> $asociado</p>
                               </li>";
                }

                $panel .= "<li class='list-group-item'>
                    <strong> Fecha de Radicación </strong> $fecha </li>";

                if(!empty(trim($cuentai))){
                    $panel .= "<li class='list-group-item'>
                   <strong> Referencia: </strong> $cuentai </li>";
                }

                if(!empty(trim($nombret_us3))){
                    $panel .= "<li class='list-group-item'> $nombret_us3 </li>";
                }

                if(!empty($tip_doc_des)){
                    $panel .= "<li class='list-group-item'>
                        <strong> Tipo de documento: </strong> $tip_doc_des </li>";
                }

                if($usua_actu){
                    $panel .= "<li class='list-group-item'>
                        <strong> Usuario actual:</strong> $usua_actu </li>";
                }

                if($depe_actu){
                    $panel .= "<li class='list-group-item'>
                        <strong> Dependencia Actual:</strong> $depe_actu </li>";
                }

                if($depe_radi){
                    $panel .= "<li class='list-group-item'>
                        <strong> Dependencia que radicó:</strong> $depe_radi </li>";
                }

            }

            $cards .= "
            <div class='col-sm-6 col-md-6 col-lg-3'>
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        {$panelH}
                    </div>
                    <div class='panel-body'>
                        <ul id='dataRad' class='list-group'>
                            {$panel}
                        </ul>
                    </div>
                </div>
            </div>";


            $cent ++;
            $radicado_anterior=$nume_radi;
            $rsCheck->MoveNext();
        }

        echo "
            <div class='row'>
                $cards
            </div> ";

        if ($cent == 0 ){
            echo "<div id='alertmessage'>
                    <div class='alert alert-block alert-info'>
                        <a class='close' data-dismiss='alert' href='#'>×</a>
                        <h4 class='alert-heading'>¡No se encontraron resultados!</h4>
                    </div>
                </div>";

            echo "<script>
              var elem = document.getElementById(\"acciones\");
                  elem.parentNode.removeChild(elem);
                </script>";
            exit;
        }

    }
}

    echo"<input type='hidden' name='usr' value='$usr'>";
    echo"<input type='hidden' name='ent' value='$ent'>";
    echo"<input type='hidden' name='depende' value='$depende'>";
    echo"<input type='hidden' name='contra' value='$contra'>";
    echo"<input type='hidden' name='pnom' value='$pnom'>";
    echo"<input type='hidden' name='sapl' value='$sapl'>";
    echo"<input type='hidden' name='papl' value='$papl'>";
    echo"<input type='hidden' name='numdoc' value='$numdoc'>";
    echo"<input type='hidden' name='tip_doc' value='$tip_doc'>";
    echo"<input type='hidden' name='tip_rem' value='$tip_rem'>";
    echo"<input type='hidden' name='codusuario' value='$codusuario'>";
    echo"<input type='hidden' name='pcodi' value='$pcodi'>";
    echo"<input type='hidden' name='hoj' value='$hoj'>";
    echo "<input type=hidden name=drde value=$drde>";
    echo "<input type=hidden name=krd value=$krd>";
?>

    <!-- JARVIS WIDGETS -->
    <script type="text/javascript">
    $(document).ready(function() {
        // START AND FINISH DATE
        $('#startdate').datepicker({
        dateFormat : 'yy/mm/dd',
            prevText : '<i class="fa fa-chevron-left"></i>',
            nextText : '<i class="fa fa-chevron-right"></i>',
            onSelect : function(selectedDate) {
                $('#startdate').datepicker('option', 'maxDate', selectedDate);
            }
        });

        $('#finishdate').datepicker({
        dateFormat : 'yy/mm/dd',
            prevText : '<i class="fa fa-chevron-left"></i>',
            nextText : '<i class="fa fa-chevron-right"></i>',
            onSelect : function(selectedDate) {
                $('#finishdate').datepicker('option', 'minDate', selectedDate);
            }
        });
    });
    </script>
    </form>
    </div>
</body>
</html>
