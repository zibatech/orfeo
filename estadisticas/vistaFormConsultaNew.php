<?php

session_start();

$ruta_raiz = "..";
if (!$_SESSION['dependencia']) {
    header("Location: $ruta_raiz/cerrar_session.php");
}

$krd = $_SESSION["krd"];
foreach ($_GET as $key => $valor) {
    ${$key} = $valor;
}

foreach ($_POST as $key => $valor) {
    ${$key} = $valor;
}

$nomcarpeta = isset($_GET["carpeta"]) ? $_GET["carpeta"] : '';
$tipo_carpt = isset($_GET["tipo_carpt"]) ? $_GET["tipo_carpt"] : '';
$orderNo = isset($_GET["orderNo"]) ? $_GET["orderNo"] : '';
$orderTipo = isset($_GET["orderTipo"]) ? $_GET["orderTipo"] : '';
$tipoEstadistica = isset($_REQUEST["tipoEstadistica"]) ? $_REQUEST["tipoEstadistica"] : '';
$genDetalle = isset($_GET["genDetalle"]) ? $_GET["genDetalle"] : '';
$dependencia_busq = isset($_GET["dependencia_busq"]) ? $_GET["dependencia_busq"] : '';
$fecha_ini = isset($_GET["fecha_ini"]) ? $_GET["fecha_ini"] : '';
$fecha_fin = isset($_GET["fecha_fin"]) ? $_GET["fecha_fin"] : '';
$codus = isset($_GET["codus"]) ? $_GET["codus"] : '';
$tipoRadicado = isset($_GET["tipoRadicado"]) ? $_GET["tipoRadicado"] : '';

$codUs = isset($_GET["codUs"]) ? $_GET["codUs"] : '';
$fecSel = isset($_GET["fecSel"]) ? $_GET["fecSel"] : '';
$genDetalle = isset($_GET["genDetalle"]) ? $_GET["genDetalle"] : '';
$generarOrfeo = isset($_GET["generarOrfeo"]) ? $_GET["generarOrfeo"] : '';
$dependencia_busqOri = isset($_GET["dependencia_busqOri"]) ? $_GET["dependencia_busqOri"] : '';

$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$tip3Nombre = $_SESSION["tip3Nombre"];
$tip3desc = $_SESSION["tip3desc"];
$tip3img = $_SESSION["tip3img"];
$usua_perm_estadistica = $_SESSION["usua_perm_estadistica"];
include_once "$ruta_raiz/include/db/ConnectionHandler.php";

$db = new ConnectionHandler($ruta_raiz);
//$db->conn->debug =true;
$sqlConcat = $db->conn->Concat("depe_codi ", "'-'", " lower(depe_nomb)");
if ($usua_perm_estadistica > 1) {
    $sql = "select $sqlConcat ,depe_codi from dependencia
    order by depe_codi";
    $rsDep = $db->conn->Execute($sql);
    //$dependencia
    $optionDep = $rsDep->GetMenu2("dependencia_busq", 99999, '99999:-- Todas las Dependencias --', false, "", " id='dependencia_busq' class=' text-capitalize custom-select'  data-live-search='true'");
} else {
    $sql = "select $sqlConcat ,depe_codi from dependencia where DEPE_CODI=$dependencia
    order by depe_codi";
    $rsDep = $db->conn->Execute($sql);
    $optionDep = $rsDep->GetMenu2("dependencia_busq", "$dependencia", false, false, " ", " id='dependencia_busq'  class='custom-select text-capitalize ' ");

}
$rs = $db->conn->Execute('select SGD_TRAD_DESCR, SGD_TRAD_CODIGO  from SGD_TRAD_TIPORAD order by 2');
$nmenu = "tipoRadicado";
$valor = "";
$itemBlanco = " -- Todos los Tipos de Radicado -- ";
$tipoRad = $rs->GetMenu2($nmenu, "", $blank1stItem = "$valor:$itemBlanco", false, 0, 'class="form-control text-capitalize " id="' . $nmenu . '"');

$ano_ini = date("Y");
$mes_ini = substr("00" . (date("m") - 1), -2);
if ($mes_ini == "00") {
    $ano_ini = $ano_ini - 1;
    $mes_ini = "12";
}
$dia_ini = date("d");
if ($mes_ini == '02' && $dia_ini > 28) {
    $dia_ini = 28;
}

if (!$fecha_ini) {
    $fecha_ini = "$ano_ini-$mes_ini-$dia_ini";
}
$optResp = '';
$reportes[1]['Nomb'] = 'Radicación- Consulta de Radicados por Usuario';
$reportes[1]['leyend'] = 'Este reporte muestra la cantidad de radicados generados por usuario. Se puede discriminar por tipo de radicación.';
$reportes[2]['Nomb'] = 'Radicación- Estadística Por Medio de Recepción- Envío';
$reportes[2]['leyend'] = 'Este reporte genera la cantidad de radicados de acuerdo al medio de recepción, realizado al momento de la radicación.';
$reportes[3]['Nomb'] = 'Radicación- Estadística de Medio Envío Final de Documentos';
$reportes[3]['leyend'] ='Este reporte genera la cantidad de radicados enviados a su destino final por el área.';
$reportes[4]['Nomb'] = 'Radicación: Digitalización de Documentos';
$reportes[4]['leyend'] = 'Este reporte genera la cantidad de radicados digitalizados por usuario y el total de hojas digitalizadas. Se puede seleccionar el tipo de radicación.';
/**$reportes[5]['Nomb'] = 'Radicados de Entrada Recibos del Area de Correspondencia';
$reportes[5]['leyend'] = 'Este reporte genera la cantidad de documentos de entrada radicados del área de correspondencia a una dependencia.';
*/
$reportes[6]['Nomb'] = 'Radicados actuales en la dependencia';
$reportes[6]['leyend'] = 'Este reporte genera la cantidad de documentos de entrada radicados del área de correspondencia a una dependencia.';
$reportes[7]['Nomb'] = 'Control entrega de correspondencia recibida';
$reportes[7]['leyend'] = 'Este reporte genera la cantidad de documentos de entrada radicados del área de correspondencia a una dependencia.';
/**$reportes[8]['Nomb'] = 'ESTADISTICA POR RADICADOS Y SUS RESPUESTAS';
$reportes[8]['leyend'] = 'Este reporte genera la cantidad de documentos de entrada radicados del área de correspondencia a una dependencia.';*/
$reportes[9]['Nomb'] = 'Informe Tramite de Radicados de Entrada';
$reportes[9]['leyend'] = 'Reporte que Muestra la gestión de radicados de entrada ';
$reportes[10]['Nomb'] = 'Gestión De Radicados de Entrada';
$reportes[10]['leyend'] = 'Reporte que Muestra la gestión de radicados de entrada';
$reportes[11]['Nomb'] = 'Gestión De Radicados de Salida';
$reportes[11]['leyend'] = 'Reporte que Muestra la gestión de radicados de Salida';
$reportes[12]['Nomb'] = 'Gestión De Radicados de Memorandos';
$reportes[12]['leyend'] = 'Reporte que Muestra la gestión de radicados de Memorandos';
/*$reportes[13]['Nomb'] = 'Radicados en Bandejas';
$reportes[13]['leyend'] = 'Muestra los radicados que estan en los usuarios';
/*$reportes[14]['Nomb'] = 'Radicados de Entrada Imagén (anexos y radicados)';
$reportes[14]['leyend'] = 'Muestra los radicados que no cuentan con imagen.';
$reportes[15]['Nomb'] = 'Gestión De Radicados de Memorandos';
$reportes[15]['leyend'] = 'Reporte que Muestra la gestión de radicados de Memorandos';
/*
<!--
<option value="9">9 - INFORME TRAMITE DE RADICADOS DE ENTRADA</option>-->
<!--  <option value="23">10 - CANTIDAD DE RADICADOS EN BANDEJA JEFES</option>
<option value="24">11 - CANTIDAD DE RADICADOS EN BANDEJA POR
DEPENDENCIA/AREA </option>
<option value="25">12 - CANTIDAD DE RADICADOS QUE TRANSITAN POR
DEPENDENCIA/AREA</option>
<option value="26">13 - RADICADOS POR TIPO</option>
<option value="27">14 - RADICADOS POR USUARIO - (IMAGEN Y ANEXOS)</option>
<option value="29">15 - RADICADOS POR USUARIO - (DATOS DE ENVIO)</option>-->

 */

foreach ($reportes as $key => $value) {
    $optResp .= "<option value='$key' data-inforp='{$value['leyend']}'>$key - {$value['Nomb']}</option>";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="screen" href="../img/favicon.png">
    <!-- Bootstrap core CSS-->
    <!--<link rel="stylesheet" type="text/css" media="screen" href="../estilos/smartadmin-production.css">-->
    <!--<link rel="stylesheet" type="text/css" media="screen" href="../estilos/smartadmin-skins.css"> -->
    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/bootstrap-select.min.css">
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" media="screen" href="../include/DataTables/datatables.css">

    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/argo.css">
    <!--<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />-->

    <title>Estadisiticas - ORFEO</title>

</head>

<body>
    <noscript>
        <span class="warningjs">Aviso: La ejecución de JavaScript está deshabilitada en su navegador. Es posible que no
            pueda responder todas las preguntas de la encuesta. Por favor, verifique la configuración de su
            navegador.</span>
    </noscript>
    <br>
    <div class="col-12">

        <section id="widget-grid">
            <!-- row -->
            <div class="row">
                <!-- NEW WIDGET START -->
                <article class="col-12">
                    <!-- Widget ID (each widget will need unique ID)-->
                    <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">

                        <header class='pl-2'>
                            <h2> Estadisticas</h2>
                        </header>
                        <!-- widget content -->
                        <div class="widget-body">
                            <div class="row">
                                <div class="col-sm-12 col-md-8 ">
                                    <div class='row'>
                                        <div class="col-12 ">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text text-left" id="basic-addon1">Tipo de
                                                        Estadistica</span>
                                                </div>
                                                <select name="tipoEstadistica" id='tipoEstadistica'
                                                    class="custom-select text-capitalize">
                                                    <option value=0>-- Selecione --</option>
                                                    <?php echo $optResp; ?>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='row'>

                                        <div class="col-12 ">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text  text-left"
                                                        id="basic-addon1">Dependencias</span>
                                                    <span class="input-group-text">Adscritas
                                                        <input id="CHKseldep" type="checkbox"
                                                            aria-label="Checkbox for following text input">
                                                </div>
                                                <?php //echo $optionDep; ?>
                                                <select name="dependencia_busq" id="dependencia_busq" class="custom-select text-capitalize ">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class="col-12">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text tamCab text-left"
                                                        id="basic-addon1">Serie</span>
                                                </div>
                                                <select name="selSerie" id="selSerie" class="custom-select ">
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class="col-12">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text tamCab text-left"
                                                        id="basic-addon1">Subserie</span>
                                                </div>
                                                <select name="selSubSerie" id="selSubSerie" class="custom-select">
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class="col-12">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text text-left"
                                                        id="basic-addon1">Usuario</span>
                                                    <span class="input-group-text"> Incluir Inactivos
                                                        <input id="CHKselUsuario" onclick='usuario();' type="checkbox"
                                                            aria-label="Checkbox for following text input">
                                                </div>
                                                </span>
                                                <select name="selUsuario" id="selUsuario"
                                                    class="custom-select text-capitalize">
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            <div class='col-md-4 col-sm-12 '>
                                <div class='row'>
                                    <div class="col-12 ">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text tamCab text-left" id="basic-addon1">Fecha
                                                    Desde</span>
                                            </div>
                                            <input type="date" name="fecha_ini" id="fecha_ini"
                                                placeholder="Fecha Inicial" value="<?php echo date($fecha_ini); ?>"
                                                class="form-control">

                                        </div>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class="col-12 ">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text tamCab text-left" id="basic-addon1">Fecha
                                                    Hasta</span>
                                            </div>
                                            <input type="date" name="fecha_fin" id="fecha_fin" placeholder="Fecha FIn"
                                                value="<?php echo date('Y-m-d'); ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class="col-12">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text tamCab text-left" id="basic-addon1">Tipo
                                                    Radicado</span>
                                            </div>
                                            <?php echo $tipoRad; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class="col-12">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text tamCab text-left " id="basic-addon1">Tipo
                                                    Documento</span>
                                            </div>
                                            <select id="selTipoDoc" id="selTipoDoc"
                                                class="form-control text-capitalize " data-live-search="true">>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class="col-12">
                                        <span class="input-group-btn ">
                                            <button class="btn btn-sm btn-primary pull-right btn-generar" type="button"
                                                id='generar'><i class='fa fa-play'></i>
                                                Generar</button>
                                            <button class="btn btn-sm btn-primary pull-right" type="button"><i
                                                    class='fa fa-eraser'></i> Limpiar</button>

                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">


                        </div>

                        <div class="row">


                        </div>

                        <div class="row">


                        </div>
                        <div class="row">


                        </div>
                    </div>

            </div>
    </div>
    </article>
    </div>
    </section>
    </div>

    <div class="col-12">
        <div class="alert alert-warning INFOalert ">
            <strong>Esta nueva interfase migrara los reportes o estadisticas a médida que estén aprobados</strong>
            <!-- <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>-->
            </button>
        </div>
    </div>
    <div class="col-12" id='resulEstdatos' style='display:none'>
        <section id="widget-grid">
            <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">
                <header class='pl-2'>
                    <h2 id="nomReport"> Resultado</h2>
                </header>


                <!-- widget content -->
                <div class="widget-body">
                    <div class="" id='resultado'> resultado</div>
                </div>

            </div>
        </section>
    </div>
    <div class="modal  show" id="DetEsta" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-argo modal-xl" style='min-width: 99%;'>
            <div class="modal-content">
                <div class="modal-header p-2">
                    <label class="modal-title " id="titDet"></label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" style='overflow: auto;'>
                    <div id='mdRespiues' style='height: 80vh;'>
                        <div id="imageLoad"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal  static fade" data-backdrop="static" id="processing-modal" aria-modal="true" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body" style='min-height:300px;' >
                    <div class="text-center">                        
                            <h5><span class="modal-text">Procesando, Espere por favor... </span></h5>
                            <div id="imageLoad"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<iframe name='excel_desc' id='excel_desc' style='display:none' ></iframe>
    <script type="text/javascript" src="../js/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap/popper.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap/bootstrap4.min.js"></script>
    <script type="text/javascript" src="../js/axios.min.js"></script>
    <script type="text/javascript" src="../include/DataTables/datatables.min.js"></script>
    <script type="text/javascript" src="../include/DataTables/Buttons-1.7.0/js/buttons.html5.js"></script>
    <script type="text/javascript" src="estadisticas.js?<?=uniqid('h');?>"></script>
    <!--<script type="text/javascript" src="../js/bootstrap/bootstrap-select.min.js"></script>-->

    <script>
    series();
    tipodoc();
    usuario();
    
    //$('#fecha_ini').val(<?php echo date($fecha_ini); ?>);
    /*  var $disabledResults = $(".selectpicker"); $disabledResults.select2();*/
    </script>
    <div id="animationload" class="animationload" style="display: none;">
        <div id="imageLoad"></div>
    </div>
    <script type="text/javascript" language="javascript">
    var aniLoad = document.getElementById('animationload');
    aniLoad.style.display = 'block';
    </script>

</body>

</html>
