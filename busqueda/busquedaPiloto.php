<?php
session_start();
include_once "../tx/diasHabiles.php";

if (!isset($ruta_raiz) || !$ruta_raiz) {
    $ruta_raiz = "..";
}
if (!$_SESSION['dependencia']) {
    header("Location: $ruta_raiz/cerrar_session.php");
}

foreach ($_GET as $key => $valor) {
    ${$key} = $valor;
}

foreach ($_POST as $key => $valor) {
    ${$key} = $valor;
}

$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$nivelus = $_SESSION["nivelus"];

if (isset($_REQUEST["flds_TDOC_CODI"])) {
    $flds_TDOC_CODI = $_REQUEST["flds_TDOC_CODI"];
}

//-------------------------------
// busqueda CustomIncludes begin
include "common.php";
$fechah = date("ymd") . "_" . time("hms");
// busqueda CustomIncludes end

// Save Page and File Name available into variables

//-------------------------------
$sFileName = "busquedaPiloto.php";
//===============================
// busqueda PageSecurity begin
$usu = $krd;
$niv = $nivelus;
if (strlen($niv)) {
}

//===============================
//Save the name of the form and type of action into the variables
//-------------------------------
$sAction = isset($_REQUEST["FormAction"]) ? $_REQUEST["FormAction"] : '';
$sForm = isset($_REQUEST["FormName"]) ? $_REQUEST["FormName"] : '';
$flds_ciudadano = isset($_REQUEST["s_ciudadano"]) ? $_REQUEST["s_ciudadano"] : '';
$flds_empresaESP = isset($_REQUEST["s_empresaESP"]) ? $_REQUEST["s_empresaESP"] : '';
$flds_oEmpresa = isset($_REQUEST["s_oEmpresa"]) ? $_REQUEST["s_oEmpresa"] : '';
$flds_FUNCIONARIO = isset($_REQUEST["s_FUNCIONARIO"]) ? $_REQUEST["s_FUNCIONARIO"] : '';
//Proceso de vinculacion al vuelo
$indiVinculo = isset($_GET["indiVinculo"]) ? $_GET["indiVinculo"] : '';
$verrad = isset($_GET["verrad"]) ? $_GET["verrad"] : '';
$carpAnt = isset($_GET["carpAnt"]) ? $_GET["carpAnt"] : '';
$nomcarpeta = isset($_GET["nomcarpeta"]) ? $_GET["nomcarpeta"] : '';
?>
<html>

<head>
    <?php //include_once "$ruta_raiz/htmlheader.inc.php";?>
    <?php// include_once "$ruta_raiz/js/funtionImage.php";?>
    <link rel="stylesheet" type="text/css" media="screen" href="../img/favicon.png">
    <!-- Bootstrap core CSS-->
    <!--<link rel="stylesheet" type="text/css" media="screen" href="../estilos/smartadmin-production.css">-->
    <!--<link rel="stylesheet" type="text/css" media="screen" href="../estilos/smartadmin-skins.css"> -->
    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/bootstrap-select.min.css">
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="../include/DataTables/datatables.css">
    
    <link rel="stylesheet" type="text/css" media="screen" href="<?=$ruta_raiz?>/estilos/bootstrap4.min.css">
    
    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/argo.css">
    <style>
    .text-mm{
      min-width: 182;
    }</style>
</head>


<script type="text/javascript">

function downloadXLS(){
	//console.log(data_array);
	headers=['Radicado','Borrador','Fecha Radicación','Expediente','Asunto','Referencia','Tipo de Documento','Direccion contacto','Telefono contacto','Mail Contacto','Dignatario','Nombre','Documento','Usuario Actual',' Dependencia Actual',' Usuario Anterior','Dias Restante']
	var formxls=document.createElement('form');
	formxls.setAttribute('action', 'xls-download.php');
	formxls.setAttribute('method', 'POST');
	formxls.setAttribute('style', 'display: none;');

	var input1 = document.createElement('input');
	input1.setAttribute('type', 'text');
	input1.setAttribute('name', 'type');
	input1.setAttribute("value", "array");
	input1.setAttribute('id', 'in_type');

	var input2 = document.createElement('input');
	input2.setAttribute('type', 'text');
	input2.setAttribute('name', 'data');
	input2.setAttribute("value", JSON.stringify(data_array));
	input2.setAttribute('id', 'in_data');

	var input3 = document.createElement('input');
	input3.setAttribute('type', 'text');
	input3.setAttribute('name', 'hs');
	input3.setAttribute("value", JSON.stringify(headers));
	input3.setAttribute('id', 'in_headers');

	formxls.appendChild(input1);
	formxls.appendChild(input2);
	formxls.appendChild(input3);

	document.body.appendChild(formxls);
	formxls.submit();
}

    
function cambioVersionTrd(){    
    document.getElementById('hTrdVers').value = document.getElementById('s_Version_Trd').value;
    document.getElementById('ChangeVersionTrd').submit();
}


function limpiar() {
    document.Search.elements['s_RADI_NUME_RADI'].value = "";
    document.Search.elements['s_RADI_NOMB'].value = "";
    document.Search.elements['s_RADI_DEPE_ACTU'].value = "";
    document.Search.elements['s_TDOC_CODI'].value = "9999";
    /**
     * Limpia el campo expediente
     * Fecha de modificacion: 30-Junio-2006
     * Modificador: Supersolidaria
     */
    document.Search.elements['s_SGD_EXP_SUBEXPEDIENTE'].value = ""; <?php
    $dia = intval(date("d"));
    $mes = intval(date("m"));
    $ano = intval(date("Y")); ?>
    document.Search.elements['s_desde_dia'].value = "<?=$dia?>";
    document.Search.elements['s_hasta_dia'].value = "<?=$dia?>";
    document.Search.elements['s_desde_mes'].value = "<?=($mes - 1)?>";
    document.Search.elements['s_hasta_mes'].value = "<?=$mes?>";
    document.Search.elements['s_desde_ano'].value = "<?=$ano?>";
    document.Search.elements['s_hasta_ano'].value = "<?=$ano?>";
    for (i = 4; i < document.Search.elements.length; i++) document.Search.elements[i].checked = 1;
}

function selTodas() {
    if (document.Search.elements['s_Listado'].checked == true) {
        document.Search.elements['s_ciudadano'].checked = false;
        document.Search.elements['s_empresaESP'].checked = false;
        document.Search.elements['s_oEmpresa'].checked = false;
        document.Search.elements['s_FUNCIONARIO'].checked = false;
    } else {
        document.Search.elements['s_ciudadano'].checked = true;
        document.Search.elements['s_empresaESP'].checked = false;
        document.Search.elements['s_oEmpresa'].checked = false;
        document.Search.elements['s_FUNCIONARIO'].checked = false;
    }

}

function delTodas() {
    document.Search.elements['s_Listado'].checked = false;
    document.Search.elements['s_ciudadano'].checked = false;
    document.Search.elements['s_empresaESP'].checked = false;
    document.Search.elements['s_oEmpresa'].checked = false;
    document.Search.elements['s_FUNCIONARIO'].checked = false;
}

function selListado() {
    if (document.Search.elements['s_ciudadano'].checked == true || document.Search.elements['s_empresaESP'].checked ==
        true || document.Search.elements['s_oEmpresa'].checked == true || document.Search.elements['s_FUNCIONARIO']
        .checked == true) {
        document.Search.elements['s_Listado'].checked = false;
    }

}

function noPermiso() {
    alert("No tiene permiso para acceder");
}

function pasar_datos(fecha, num) {
    <?php
    echo "if(num==1){";
    echo "opener.document.VincDocu.numRadi.value = fecha\n";
    echo "opener.focus(); window.close();\n }";
    echo "if(num==2){";
    echo "opener.document.insExp.numeroExpediente.value = fecha\n";
    echo "opener.focus(); window.close();}\n"; ?>
}
</script>

<body onLoad='document.getElementById("cajarad").focus();' style="margin-bottom: 30px;">
    <header class="page-title txt-color-blueDark"> </header>
    <?php Search_show()?>
    <?php
if (isset($Busqueda) || isset($s_entrada)) {
    if ($s_Listado == "VerListado") {
        if ($flds_ciudadano == "CIU") {
            $whereFlds .= "1,";
        }

        if ($flds_empresaESP == "ESP") {
            $whereFlds .= "2,";
        }

        if ($flds_oEmpresa == "OEM") {
            $whereFlds .= "3,";
        }

        if ($flds_FUNCIONARIO == "FUN") {
            $whereFlds .= "4,";
        }

        $whereFlds .= "0";
        Ciudadano_show($nivelus, 9, $whereFlds);
    } else {
        if (!$etapa) {
            if ($flds_ciudadano == "CIU") {
                Ciudadano_show($nivelus, 1, 1);
            } else {
                if (!strlen($flds_ciudadano)
                    && !strlen($flds_empresaESP)
                    && !strlen($flds_oEmpresa)
                    && !strlen($flds_FUNCIONARIO)) {
                    Ciudadano_show($nivelus, 9, 1);
                }
            }
        }

        if ($flds_empresaESP == "ESP") {
            Ciudadano_show($nivelus, 3, 3);
        }

        if ($flds_oEmpresa == "OEM") {
            Ciudadano_show($nivelus, 2, 2);
        }

        if ($flds_FUNCIONARIO == "FUN") {
            Ciudadano_show($nivelus, 4, 4);
        }

    }
}?>
    <?php

//===============================
// Display Search Form
//-------------------------------
function Search_show()
{
    global $db;
    global $styles;
    global $db2;
    global $db3;
    global $sForm;
    $sFormTitle = "Consulta Cl&aacute;sica";
    $ss_desde_RADI_FECH_RADIDisplayValue = "";
    $ss_hasta_RADI_FECH_RADIDisplayValue = "";
    $ss_TDOC_CODIDisplayValue = "Todos los Tipos";
    $ss_TRAD_CODIDisplayValue = "Todos los Tipos (-1,-2,-3,-5, . . .)";
    $ss_TRAD_CODIDisplayNotiValue = "Notificaciones";
    $ss_RADI_DEPE_ACTUDisplayValue = "Todas las Dependencias";
//Con esta variable se determina si la busqueda corresponde a vinculacion documentos
    $indiVinculo = isset($_GET["indiVinculo"]) ? $_GET["indiVinculo"] : '';
    $verrad = isset($_GET["verrad"]) ? $_GET["verrad"] : '';
    $carpeAnt = isset($_GET["carpeAnt"]) ? $_GET["carpeAnt"] : '';
    $nomcarpeta = isset($_GET["nomcarpeta"]) ? $_GET["nomcarpeta"] : '';
    $krd = $_SESSION["krd"];
    $dependencia = $_SESSION["dependencia"];
    $usua_doc = $_SESSION["usua_doc"];
    $codusuario = $_SESSION["codusuario"];
    $nivelus = $_SESSION["nivelus"];
    $flds_TDOC_CODI = isset($_REQUEST["flds_TDOC_CODI"]) ? $_REQUEST["flds_TDOC_CODI"] : '';
    foreach ($_GET as $key => $valor) {
        ${$key} = $valor;
    }

    foreach ($_POST as $key => $valor) {
        ${$key} = $valor;
    }

    if ($indiVinculo == 1) {
        $sFormTitle = $sFormTitle . "  Anexo  al Vuelo ";
    }
    if ($indiVinculo == 2) {
        $sFormTitle = $sFormTitle . "  Incluir Expediente ";
    }
//-------------------------------
    // Set variables with search parameters
    //-------------------------------
    $flds_RADI_NUME_RADI = isset($_GET["s_RADI_NUME_RADI"]) ? $_GET["s_RADI_NUME_RADI"] : '';
    $flds_DOCTO = isset($_GET["s_DOCTO"]) ? $_GET["s_DOCTO"] : '';
    $flds_CUENTAINTERNA = isset($_GET["s_CUENTAINTERNA"]) ? $_GET["s_CUENTAINTERNA"] : '';
    $flds_GUIA = isset($_GET["s_GUIA"]) ? $_GET["s_GUIA"] : '';
    $flds_RADI_NOMB = isset($_GET["s_RADI_NOMB"]) ? $_GET["s_RADI_NOMB"] : '';
    $flds_ciudadano = isset($_GET["s_ciudadano"]) ? $_GET["s_ciudadano"] : '';
    if ($flds_ciudadano) {
        $checkCIU = "checked";
    }

    $flds_empresaESP = isset($_GET["s_empresaESP"]) ? $_GET["s_empresaESP"] : '';
    if ($flds_empresaESP) {
        $checkESP = "checked";
    }

    $flds_oEmpresa = isset($_GET["s_oEmpresa"]) ? $_GET["s_oEmpresa"] : '';
    if ($flds_oEmpresa) {
        $checkOEM = "checked";
    }

    $flds_FUNCIONARIO = isset($_GET["s_FUNCIONARIO"]) ? $_GET["s_FUNCIONARIO"] : '';
    if ($flds_FUNCIONARIO) {
        $checkFUN = "checked";
    }

    $flds_entrada = isset($_GET["s_entrada"]) ? $_GET["s_entrada"] : '';
    $flds_salida = isset($_GET["s_salida"]) ? $_GET["s_salida"] : '';
    $flds_solo_nomb = isset($_GET["s_solo_nomb"]) ? $_GET["s_solo_nomb"] : '';
    $Busqueda = isset($_GET["Busqueda"]) ? $_GET["Busqueda"] : '';
    $flds_desde_dia = isset($_GET["s_desde_dia"]) ? $_GET["s_desde_dia"] : '';
    $flds_hasta_dia = isset($_GET["s_hasta_dia"]) ? $_GET["s_hasta_dia"] : '';
    $flds_desde_mes = isset($_GET["s_desde_mes"]) ? $_GET["s_desde_mes"] : '';
    $flds_hasta_mes = isset($_GET["s_hasta_mes"]) ? $_GET["s_hasta_mes"] : '';
    $flds_desde_ano = isset($_GET["s_desde_ano"]) ? $_GET["s_desde_ano"] : '';
    $flds_hasta_ano = isset($_GET["s_hasta_ano"]) ? $_GET["s_hasta_ano"] : '';
    $flds_TDOC_CODI = isset($_GET["s_TDOC_CODI"]) ? $_GET["s_TDOC_CODI"] : '';
    $s_Listado = isset($_GET["s_Listado"]) ? $_GET["s_Listado"] : '';
    $flds_RADI_DEPE_ACTU = isset($_GET["s_RADI_DEPE_ACTU"]) ? $_GET["s_RADI_DEPE_ACTU"] : '';

    /**
     * Busqueda por expediente
     * Fecha de modificacion: 30-Junio-2006
     * Modificador: Supersolidaria
     */
    $flds_SGD_EXP_SUBEXPEDIENTE = isset($_GET["s_SGD_EXP_SUBEXPEDIENTE"]) ? $_GET["s_SGD_EXP_SUBEXPEDIENTE"] : '';

    if (strlen($flds_desde_dia) && strlen($flds_hasta_dia) &&
        strlen($flds_desde_mes) && strlen($flds_hasta_mes) &&
        strlen($flds_desde_ano) && strlen($flds_hasta_ano)) {
        $desdeTimestamp = mktime(0, 0, 0, $flds_desde_mes, $flds_desde_dia, $flds_desde_ano);
        $hastaTimestamp = mktime(0, 0, 0, $flds_hasta_mes, $flds_hasta_dia, $flds_hasta_ano);
        $flds_desde_dia = Date('d', $desdeTimestamp);
        $flds_hasta_dia = Date('d', $hastaTimestamp);
        $flds_desde_mes = Date('m', $desdeTimestamp);
        $flds_hasta_mes = Date('m', $hastaTimestamp);
        $flds_desde_ano = Date('Y', $desdeTimestamp);
        $flds_hasta_ano = Date('Y', $hastaTimestamp);
    } else { /*DESDE HACE UN MES HASTA HOY */
        $desdeTimestamp = mktime(0, 0, 0, Date('m') - 1, Date('d'), Date('Y'));
        $flds_desde_dia = Date('d', $desdeTimestamp);
        $flds_hasta_dia = Date('d');
        $flds_desde_mes = Date('m', $desdeTimestamp);
        $flds_hasta_mes = Date('m');
        if (Date('m') == 1) {
            $flds_desde_ano = Date('Y') - 1;
        } else {
            $flds_desde_ano = Date('Y');
        }

        $flds_hasta_ano = Date('Y');
    }
//-------------------------------
    // Search Show begin
    //-------------------------------

//-------------------------------
    // Search Show Event begin
    // Search Show Event end
    //-------------------------------
    ?>
    <br>
    <form method="get"
        action="busquedaPiloto.php?<?=session_name() . "=" . session_id()?>&indiVinculo=<?=$indiVinculo?>&verrad=<?=$verrad?>&carpeAnt=<?=$carpeAnt?>&nomcarpeta=<?=$nomcarpeta?>"
        name="Search">
        <input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'>
        <input type="hidden" name="FormName" value="Search"><input type="hidden" name="FormAction" value="search">
        
        <div class="col-sm-12">
            <!-- widget grid -->
            <section id="widget-grid">
                <!-- row -->
                <div class="row">
                    <!-- NEW WIDGET START -->
                    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <!-- Widget ID (each widget will need unique ID)-->
                        <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1"
                            data-widget-editbutton="false">
                            <!-- widget div-->
                            <header>
                                <h2 class="pl-2"> <?=$sFormTitle?></h2>
                            </header>
                            <div>
                                <!-- widget content -->
                                <div class="widget-body">

                                    <div class="row">
                                        <div class='col-md-6 col-lg-6 col-sm-12'>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text text-left text-mm" id="basic-addon1">Radicado o Borrador</span>
                                                </div>
                                                <input title='buscar por número radicado' class="form-control"  type="text" name="s_RADI_NUME_RADI" maxlength=""  value="<?=tohtml($flds_RADI_NUME_RADI)?>" size="" id="cajarad">
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text text-left text-mm" id="basic-addon1">Identificación (T.I.,C.C.,Nit) </span>
                                                </div>
                                                <input class="form-control" type="text" name="s_DOCTO" maxlength=""  value="<?=tohtml($flds_DOCTO)?>" size="">
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text text-left text-mm " id="basic-addon1">Expediente</span>
                                                </div>
                                                <input title='buscar por Expediente' class="form-control" type="text" name="s_SGD_EXP_SUBEXPEDIENTE" maxlength="" value="<?=tohtml($flds_SGD_EXP_SUBEXPEDIENTE)?>" size="">
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    
                                                    <span class="input-group-text text-left text-mm " id="basic-addon1">Referencia (T.I.,C.C.,Nit) </span>
                                                </div>
                                                <input title='buscar por Referencia'  class="form-control" type="text"  name="s_CUENTAINTERNA" maxlength=""   value="<?=tohtml($flds_CUENTAINTERNA)?>" size="">
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text text-left text-mm " id="basic-addon1">Número de guía </span>
                                                <input title='buscar por Referencia'  class="form-control" type="text"  name="s_GUIA" maxlength=""   value="<?=tohtml($flds_GUIA)?>" size="">
                                            </div>
                                            </div>
                                        </div>
                               
                                    <div class="col-6 col-md-6 col-lg-6 col-sm-12">
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text text-left text-mm" id="basic-addon1">Desde Fecha(dd/mm/yyyy)</span>
                                            </div>
                                                                              <select title='dia inicial de busqueda' class="custom-select" name="s_desde_dia">
                                                                                  <?
                                                      for($i = 1; $i <= 31; $i++)
                                                      {
                                                        if($i == $flds_desde_dia) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
                                                        else $option="<option value=\"" . $i . "\">" . $i . "</option>";
                                                        echo $option;
                                                      }
                                                      ?>
                                                      </select>
                                                       <select title='mes inicial de busqueda' class="custom-select" name="s_desde_mes">
                                                                                  <?
                                                      for($i = 1; $i <= 12; $i++)
                                                      {
                                                        if($i == $flds_desde_mes) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
                                                        else $option="<option value=\"" . $i . "\">" . $i . "</option>";
                                                        echo $option;
                                                      }
                                                      ?>
                                                      </select>
                                                      <select title='año inicial de busqueda' class="custom-select" name="s_desde_ano">
                                                                                  <?
                                                      $agnoactual=Date('Y');
                                                      for($i = 2020; $i <= $agnoactual; $i++)
                                                      {
                                                        if($i == $flds_desde_ano) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
                                                        else $option="<option value=\"" . $i . "\">" . $i . "</option>";
                                                        echo $option;
                                                      }
                                                      ?>
                                            </select>
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text text-left text-mm" id="basic-addon1">Hasta Fecha(dd/mm/yyyy)</span>
                                            </div>
                                            <select class="custom-select" title='dia final de busqueda' name="s_hasta_dia">
                                                <?
            for($i = 1; $i <= 31; $i++)
            {
              if($i == $flds_hasta_dia) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
              else $option="<option value=\"" . $i . "\">" . $i . "</option>";
              echo $option;
            }
            ?>
                                            </select>
                                            <select class="custom-select" title='mes final de busqueda' name="s_hasta_mes">
                                                <?
            for($i = 1; $i <= 12; $i++)
            {
              if($i == $flds_hasta_mes) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
              else $option="<option value=\"" . $i . "\">" . $i . "</option>";
              echo $option;
            }
        ?>
                                            </select>
                                            <select class="custom-select" title='año final de busqueda' name="s_hasta_ano">
                                                <?
            for($i = 2020; $i <= $agnoactual; $i++)
            {
              if($i == $flds_hasta_ano) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
              else $option="<option value=\"" . $i . "\">" . $i . "</option>";
              echo $option;
            }
            ?>
                                            </select>


                                            </div>

                                            <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text text-left text-mm" id="basic-addon1">Buscar en Radicados de</span>
                                            </div>
                                            <select title='Buscar en Radicados de' class="custom-select" name="s_entrada">
                                                                            <?
                                                if(!$s_Listado) $s_Listado="VerListado";
                                                if ($flds_entrada==0) $flds_entrada="9999";
                                                echo "<option value=\"9999\">" . $ss_TRAD_CODIDisplayValue . "</option>";
                                                echo "<option value=\"9998\">" . $ss_TRAD_CODIDisplayNotiValue . "</option>";                                                
                                                $lookup_s_entrada = db_fill_array("select SGD_TRAD_CODIGO, SGD_TRAD_DESCR from SGD_TRAD_TIPORAD order by 2");

                                                if(is_array($lookup_s_entrada))
                                                {
                                                  reset($lookup_s_entrada);
                                                  while(list($key, $value) = each($lookup_s_entrada))
                                                  {
                                                    if($key == $flds_entrada) $option="<option SELECTED value=\"$key\">$value</option>";
                                                    else $option="<option value=\"$key\">$value</option>";
                                                    echo $option;
                                                  }
                                                }
                                                ?>
                                            </select>
                                            </div>
                                            <div class="input-group input-group-sm">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text text-left text-mm" id="basic-addon1"> Tipo de Documento</span>
                                            </div>
                                            <select class="custom-select" name="s_TDOC_CODI" title='tipo de documento' style='width:200px'>
                                              <?
                                                if ($flds_TDOC_CODI==0) $flds_TDOC_CODI="9999";
                                                echo "<option value=\"9999\">" . $ss_TDOC_CODIDisplayValue . "</option>";
                                                $lookup_s_TDOC_CODI = db_fill_array("select SGD_TPR_CODIGO, SGD_TPR_DESCRIP from SGD_TPR_TPDCUMENTO where sgd_tpr_codigo <= 9000 order by 2");

                                                if(is_array($lookup_s_TDOC_CODI))
                                                {
                                                  reset($lookup_s_TDOC_CODI);
                                                  while(list($key, $value) = each($lookup_s_TDOC_CODI))
                                                  {
                                                    if($key == $flds_TDOC_CODI) $option="<option SELECTED value=\"$key\">$value</option>";
                                                    else $option="<option value=\"$key\">$value</option>";
                                                    echo $option;
                                                  }
                                                }
                                                ?>
                                            </select>
                                            </div>

                                    </div>
                                    
                                    <div class='col-12'>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text text-left text-mm" id="basic-addon1"> <INPUT type="radio" NAME="s_solo_nomb" value="All" CHECKED
                                                <?if($flds_solo_nomb=="All" ){ echo ("CHECKED");} ?>>
                                            Buscar Por<br>
                                            </span>
                                                </div>
                                                <input class="form-control" type="text" name="s_RADI_NOMB" maxlength="250"
                                                value="<?=tohtml($flds_RADI_NOMB)?>" style='width:100%'>
                                            </div>
                                            </div>
                                            <div class='col-12'>
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text text-left text-mm" id="basic-addon1">Dependencia Actual</span>
                                                </div>
                                                <select class="custom-select" name="s_RADI_DEPE_ACTU" title='Dependencia Actual'
                                                style='width:100%'>
                                                                                  <?
                                                      $l= strlen($flds_RADI_DEPE_ACTU);

                                                      if ($l==0){
                                                        echo "<option value=\"\" SELECTED>" . $ss_RADI_DEPE_ACTUDisplayValue . "</option>";
                                                      }else{
                                                        echo "<option value=\"\">" . $ss_RADI_DEPE_ACTUDisplayValue . "</option>";
                                                      }
                                                    $lookup_s_RADI_DEPE_ACTU = db_fill_array("select DEPE_CODI, DEPE_NOMB from DEPENDENCIA where depe_estado=1 order by 1");

                                                    if(is_array($lookup_s_RADI_DEPE_ACTU))
                                                    {
                                                      reset($lookup_s_RADI_DEPE_ACTU);
                                                      while(list($key, $value) = each($lookup_s_RADI_DEPE_ACTU))
                                                      {
                                                        if($l>0 && $key == $flds_RADI_DEPE_ACTU) $option="<option SELECTED value=\"$key\">$key - $value</option>";
                                                        else $option="<option value=\"$key\">$key - $value</option>";
                                                        echo $option;
                                                      }
                                                    }
                                                      ?>
                                            </select>
                                            <input class="btn btn-danger btn-sm" type="button" value="Limpiar" onclick="limpiar();">
                                            <input class="btn btn-warning btn-sm" type="submit" name=Busqueda value="B&uacute;squeda">
                                           </div>
                                      </div>

                                      </div>
                                </div>
                            </div>
                        </div>
                </div>
                </article>
        </div>
        </section>
        </div>
    </form>

    <?php

//-------------------------------
    // Search Show end
    //-------------------------------
    //===============================
}

//===============================
// Display Grid Form
//-------------------------------
function Ciudadano_show($nivelus, $tpRemDes, $whereFlds)
{
//-------------------------------
    // Initialize variables
    //-------------------------------

    global $db2;
    global $db3;
    global $sRADICADOErr;
    global $sFileName;
    global $styles;
    global $ruta_raiz;
    $usua_doc = $_SESSION["usua_doc"];
    $dependencia = $_SESSION["dependencia"];
    $codusuario = $_SESSION["codusuario"];

    $radicado = $_GET["s_RADI_NUME_RADI"];
    $tipo_radicado = $_GET["s_entrada"];
    $esNotificacionCircular = false;

    define('CIRC_INTERNA', 4);
    define('CIRC_EXTERNA', 5);

    if (!empty($tipo_radicado) && ($tipo_radicado == CIRC_INTERNA || $tipo_radicado == CIRC_EXTERNA)) {
        $esNotificacionCircular = true;
    } else {
        if (!empty($radicado)) {
            $tipo_radicado = substr($radicado, -1);
            if ($tipo_radicado == CIRC_INTERNA || $tipo_radicado == CIRC_EXTERNA) {
                $esNotificacionCircular = true;
            }

        }
    }

    $sWhere = "";

    $sOrder = "";
    $sSQL = "";
    $db = new ConnectionHandler($ruta_raiz);
    $Dha = new FechaHabil($db);
    //$db->conn->debug = true;
    if ($tpRemDes == 1) {
        $tpRemDesNombre = "Por Ciudadano";
    }if ($tpRemDes == 2) {
        $tpRemDesNombre = "Por Otras Empresas";
    }if ($tpRemDes == 3) {
        $tpRemDesNombre = "Por Entidad";
    }if ($tpRemDes == 4) {
        $tpRemDesNombre = "Por Funcionario";
    }if ($tpRemDes == 9) {
        $tpRemDesNombre = "";
        $whereTrd = "   ";
    } else {
        $whereTrd = !$esNotificacionCircular ? " and dir.sgd_trd_codigo = $whereFlds  " : "";
    }
    if ($indiVinculo == 2) {
        $sFormTitle = "Expedientes encontrados $tpRemDesNombre";
    } else {
        $sFormTitle = "Radicados encontrados $tpRemDesNombre";
    }
    $HasParam = false;
    $iRecordsPerPage = 50;
    $iCounter = 0;
    $iPage = 0;
    $bEof = false;
    $iSort = "";
    $iSorted = "";
    $sDirection = "";
    $sSortParams = "";
    $iTmpI = 0;
    $iTmpJ = 0;
    $sCountSQL = "";

    $transit_params = "";
    //Proceso de Vinculacion documentos
    $indiVinculo = $_GET["indiVinculo"];
    $verrad = $_GET["verrad"];
    $carpeAnt = $_GET["carpeAnt"];
    $nomcarpeta = $_GET["nomcarpeta"];

//-------------------------------
    // Build ORDER BY statement
    //-------------------------------
    //$sOrder = " order by r.RADI_NUME_RADI ";
    $sOrder = " order by r.radi_fech_radi desc";
    $iSort = $_GET["FormCIUDADANO_Sorting"];
    $iSorted = $_GET["FormCIUDADANO_Sorted"];
    $form_params = trim(session_name()) . "=" . trim(session_id()) . "&verrad=$verrad&indiVinculo=$indiVinculo&carpeAnt=$carpeAnt&nomcarpeta=$nomcarpeta&s_RADI_DEPE_ACTU=" . tourl($_GET["s_RADI_DEPE_ACTU"]) . "&s_RADI_NOMB=" . tourl($_GET["s_RADI_NOMB"]) . "&s_RADI_NUME_RADI=" . tourl($_GET["s_RADI_NUME_RADI"]) . "&s_TDOC_CODI=" . tourl($_GET["s_TDOC_CODI"]) . "&s_desde_dia=" . tourl($_GET["s_desde_dia"]) . "&s_desde_mes=" . tourl($_GET["s_desde_mes"]) . "&s_desde_ano=" . tourl($_GET["s_desde_ano"]) . "&s_hasta_dia=" . tourl($_GET["s_hasta_dia"]) . "&s_hasta_mes=" . tourl($_GET["s_hasta_mes"]) . "&s_hasta_ano=" . tourl($_GET["s_hasta_ano"]) . "&s_solo_nomb=" . tourl($_GET["s_solo_nomb"]) . "&s_ciudadano=" . tourl($_GET["s_ciudadano"]) . "&s_empresaESP=" . tourl($_GET["s_empresaESP"]) . "&s_oEmpresa=" . tourl($_GET["s_oEmpresa"]) . "&s_FUNCIONARIO=" . tourl($_GET["s_FUNCIONARIO"]) . "&s_entrada=" . tourl($_GET["s_entrada"]) . "&s_salida=" . tourl($_GET["s_salida"]) . "&nivelus=$nivelus&s_Listado=" . $_GET["s_Listado"] . "&s_
SGD_EXP_SUBEXPEDIENTE=" . $_GET["s_SGD_EXP_SUBEXPEDIENTE"] . "&";
    // s_Listado s_ciudadano s_empresaESP s_FUNCIONARIO
    if (!$iSort) {
        $form_sorting = "";
    } else {
        if ($iSort == $iSorted) {
            $form_sorting = "";
            $sDirection = " DESC ";
            $sSortParams = "FormCIUDADANO_Sorting=" . $iSort . "&FormCIUDADANO_Sorted=" . $iSort . "&";
        } else {
            $form_sorting = $iSort;
            $sDirection = "  ";
            $sSortParams = "FormCIUDADANO_Sorting=" . $iSort . "&FormCIUDADANO_Sorted=" . "&";
        }
        switch ($iSort) {
            case 1:$sOrder = " order by r.radi_nume_radi" . $sDirection;
                break;
            case 2:$sOrder = " order by r.radi_fech_radi" . $sDirection;
                break;
            case 3:$sOrder = " order by r.ra_asun" . $sDirection;
                break;
            case 4:$sOrder = " order by td.sgd_tpr_descrip" . $sDirection;
                break;
            case 5:$sOrder = " order by r.radi_nume_hoja" . $sDirection;
                break;
            case 6:
                if (!$esNotificacionCircular) {
                    $sOrder = " order by dir.sgd_dir_direccion" . $sDirection;
                }

                break;
            case 7:
                if (!$esNotificacionCircular) {
                    $sOrder = " order by dir.sgd_dir_telefono" . $sDirection;
                }

                break;
            case 8:
                if (!$esNotificacionCircular) {
                    $sOrder = " order by dir.sgd_dir_mail" . $sDirection;
                }

                break;
            case 9:
                if (!$esNotificacionCircular) {
                    $sOrder = " order by dir.sgd_dir_nombre" . $sDirection;
                }

                break;
            case 12:
                if (!$esNotificacionCircular) {
                    $sOrder = " order by dir.sgd_dir_telefono" . $sDirection;
                }

                break;
            case 13:
                if (!$esNotificacionCircular) {
                    $sOrder = " order by dir.sgd_dir_direccion" . $sDirection;
                }

                break;
            case 14:
                if (!$esNotificacionCircular) {
                    $sOrder = " order by dir.sgd_dir_doc" . $sDirection;
                }

                break;
            case 17:$sOrder = " order by r.radi_usu_ante" . $sDirection;
                break;
            case 20:$sOrder = " order by r.radi_pais" . $sDirection;
                break;
            case 21:$sOrder = " order by diasr" . $sDirection;
                break;
            case 22:
                if (!$esNotificacionCircular) {
                    $sOrder = " order by dir.sgd_dir_nombre" . $sDirection;
                }

                break;
            case 23:
                if (!$esNotificacionCircular) {
                    $sOrder = " order by dir.RADI_DATO_001" . $sDirection;
                }

                break;
            case 24:
                if (!$esNotificacionCircular) {
                    $sOrder = " order by dir.RADI_DATO_002" . $sDirection;
                }

                break;
        }

        /*if ($_SESSION['entidad'] == "IK" ) {
    $sOrder = " order by r.radi_fech_radi desc";
    }*/

    }
//-------------------------------
    // Encabezados HTML de las Columnas (Titulos)
    //-------------------------------
    ?>
    <div class="col-sm-12">
        <!-- row -->
        <div class="row">
            <!-- NEW WIDGET START -->
            <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <!-- Widget ID (each widget will need unique ID)-->
                <div style="width: 100%; overflow-x: scroll;" class="jarviswidget jarviswidget-color-darken"
                    id="wid-id-1" data-widget-editbutton="false">
                    <!-- widget content -->
                    <header>
                        <h2 class="pl-2"> <?=$sFormTitle?></h2>
                    </header>
                    <div style="width: 100%; overflow-x: scroll;" class="widget-body">
                        <table class="table table-bordered table-striped table-compact table-hover table-sm" id='tb-resp' style='font-size: 11px'>
                        <thead class="thead-dark">
                            <tr>
                            <th class=""></th>
                                <?
              if ($indiVinculo >= 1) {  ?>
                                <th class="">
                                    <font class="ColumnFONT">
                                </th>
                                <?  }
              if ($indiVinculo != 2)
                { ?>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=1&FormCIUDADANO_Sorted=<?=$form_sorting?>&"> <i class="fa fa-sort float-right"></i></a> Radicado
                                </th>
                                <th class="">
                                    <font class="ColumnFONT">Borrador
                                </th>                                                             
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=2&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a> Fecha Radicación</th>
                                <th class="">
                                    <font class="ColumnFONT">Expediente
                                </th>
                                <? } else   { ?>
                                <th class="">
                                    <font class="ColumnFONT">Expediente
                                </th>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=1&FormCIUDADANO_Sorted=<?=$form_sorting?>&"> <i class="fa fa-sort float-right"></i></a>Radicado vinculado al expediente</th>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=2&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Fecha Radicacion</th>
                                <? } ?>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=3&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Asunto
                                </th>
                                <!--            <th class="titulos5"><a class="vinculos" href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=23&FormCIUDADANO_Sorted=<?=$form_sorting?>&">Dignatario</a></th> -->
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=4 &FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Referencia
                                </th>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=4&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Tipo de Documento</th>
                               <!-- <th class="">
                                    <font class="ColumnFONT">Tipo
                                </th>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=5&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Paginas</th>-->
                                <? if ($esNotificacionCircular) { ?>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=1&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Destinatarios
                                </th>
                                <? } else { ?>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=6&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Direccion
                                        contacto</th>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=7&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Telefono
                                        contacto</th>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=8&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Mail
                                        Contacto</th>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=23&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Dignatario
                                </th>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=9&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Nombre</th>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=14&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Documento</th>
                                <? } ?>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=15&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Usuario  Actual</th>
                                <th class="">
                                    <font class="ColumnFONT">Dependencia Actual
                                </th>
                                <th class="">
                                    <font class="ColumnFONT">Usuario Anterior
                                </th>
                             <!--   <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=20&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Pais
                                </th>-->
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=21&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Dias Restante</th>
                               <!-- <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=22&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Apoderado
                                </th>
                                <th class=""><a class="vinculos"
                                        href="<?=$sFileName?>?<?=$form_params?>FormCIUDADANO_Sorting=23&FormCIUDADANO_Sorted=<?=$form_sorting?>&"><i class="fa fa-sort float-right"></i></a>Demandante    </th>-->
                            </tr>
                            </thead>
                            <tbody>
                            <?
              //-------------------------------
              // Build WHERE statement
              //-------------------------------
              // Se crea la $ps_desde_RADI_FECH_RADI con los datos ingresados.
              //------------------------------------
              $ps_desde_RADI_FECH_RADI = mktime(0,0,0,$_GET["s_desde_mes"],$_GET["s_desde_dia"],$_GET["s_desde_ano"]);
              $ps_hasta_RADI_FECH_RADI = mktime(23,59,59,$_GET["s_hasta_mes"],$_GET["s_hasta_dia"],$_GET["s_hasta_ano"]);

              if(strlen($ps_desde_RADI_FECH_RADI) && strlen($ps_hasta_RADI_FECH_RADI)){
                $HasParam = true;
                if($sWhere != ""){
                    $sWhere .= " and ";
                }
                $sWhere = $sWhere . $db->conn->SQLDate('Y-m-d','r.radi_fech_radi')." >= ".$db->conn->DBDate($ps_desde_RADI_FECH_RADI) ;
                //$sWhere = $sWhere . "r.radi_fech_radi>=".$db->conn->DBTimeStamp($ps_desde_RADI_FECH_RADI) ; //by HLP.
                $sWhere .= " and ";
                $sWhere = $sWhere . $db->conn->SQLDate('Y-m-d','r.radi_fech_radi')." <= ".$db->conn->DBDate($ps_hasta_RADI_FECH_RADI) ;
                //$sWhere = $sWhere . "r.radi_fech_radi<=".$db->conn->DBTimeStamp($ps_hasta_RADI_FECH_RADI); //by HLP.
              }

              /* Se recibe la dependencia actual para busqueda */
              $ps_RADI_DEPE_ACTU = $_GET["s_RADI_DEPE_ACTU"];
              if(is_number($ps_RADI_DEPE_ACTU) && strlen($ps_RADI_DEPE_ACTU))
                $ps_RADI_DEPE_ACTU = tosql($ps_RADI_DEPE_ACTU, "Number");
              else
                $ps_RADI_DEPE_ACTU = "";

              if(strlen($ps_RADI_DEPE_ACTU)){
                if($sWhere != "")
                  $sWhere .= " and ";
                $HasParam = true;
                $sWhere = $sWhere . "r.radi_depe_actu=" . $ps_RADI_DEPE_ACTU;
              }

            /* Se recibe el numero del radicado para busqueda */
              $ps_RADI_NUME_RADI = $_GET["s_RADI_NUME_RADI"];
              $ps_BORRA_NUME_RADI = $_GET["s_RADI_NUME_RADI"];
              if(!$ps_RADI_NUME_RADI) $ps_RADI_NUME_RADI="2";
              if(!$ps_BORRA_NUME_RADI) $ps_BORRA_NUME_RADI="3";
              $ps_DOCTO =  $_GET["s_DOCTO"];
              $ps_CUENTAINTERNA  =  $_GET["s_CUENTAINTERNA"];
              $ps_GUIA  =  $_GET["s_GUIA"];
              if(strlen($ps_RADI_NUME_RADI)){
                if($sWhere != "")
                  $sWhere .= " and ";
                $HasParam = true;
                $sWhere = $sWhere . "(r.radi_nume_radi::text like " . tosql("%".trim($ps_RADI_NUME_RADI) ."%", "Text") . " or r.radi_nume_borrador::text like " . tosql("%".trim($ps_BORRA_NUME_RADI) ."%", "Text") . ")";
              }

              if(strlen($ps_DOCTO) && !$esNotificacionCircular){
                if($sWhere != "")
                  $sWhere .= " and ";
                $HasParam = true;
                $sWhere = $sWhere . " dir.SGD_DIR_DOC = '$ps_DOCTO' " ;
              }

              //Busqueda realizada por este parametro cuenta interna
              if(strlen($ps_CUENTAINTERNA)){
                  $HasParam = true;
                  if($sWhere != ""){
                      $sWhere .= " and ";
                  }
                  $ps_CUENTAINTERNA = strtoupper($ps_CUENTAINTERNA);
                  $sWhere .= $sWhere." ".$db->conn->Concat("r.radi_cuentai")." iLIKE '%".$ps_CUENTAINTERNA."%' ";
              }
              if(strlen($ps_GUIA)){
                  $HasParam = true;
                  if($sWhere != ""){
                      $sWhere .= " and ";
                  }
                  $ps_GUIA = strtoupper($ps_GUIA);
                  $sWhere .= $sWhere." ".$db->conn->Concat("r.radi_nume_guia")." iLIKE '%".$ps_GUIA."%' ";
              }

              /**
                * Se recibe el numero del expediente para busqueda
                * Fecha de modificacion: 30-Junio-2006
                * Modificador: Supersolidaria
                */
                $ps_SGD_EXP_SUBEXPEDIENTE = $_GET["s_SGD_EXP_SUBEXPEDIENTE"];
                if( strlen( $ps_SGD_EXP_SUBEXPEDIENTE ) != 0 ){
                  if( $sWhere != "" ){
                    $sWhere .= " and ";
                  }
                  $HasParam = true;
                  $sWhere = $sWhere . " R.RADI_NUME_RADI = EXP.RADI_NUME_RADI";
                  $sWhere = $sWhere . " AND EXP.SGD_EXP_NUMERO = SEXP.SGD_EXP_NUMERO";
                  /**
                  * No se tienen en cuenta los radicados que han sido excluidos de un expediente.
                  * Fecha de modificacion: 12-Septiembre-2006
                  * Modificador: Supersolidaria
                  * Aqui se hace la consulta en los diferentes campos agregar campos si es nescesario
                  */
                  $sWhere = $sWhere . " AND EXP.SGD_EXP_ESTADO <> 2";
                  $sWhere = $sWhere . " AND ( EXP.SGD_EXP_NUMERO LIKE '%".str_replace( '\'', '', tosql( trim( $ps_SGD_EXP_SUBEXPEDIENTE ), "Text" ) )."%'";
                  $sWhere = $sWhere . " OR SEXP.SGD_SEXP_PAREXP1 iLIKE '%".str_replace( '\'', '', tosql( trim( $ps_SGD_EXP_SUBEXPEDIENTE ), "Text" ) )."%' ";
                  $sWhere = $sWhere . " OR SEXP.SGD_SEXP_PAREXP2 iLIKE '%".str_replace( '\'', '', tosql( trim( $ps_SGD_EXP_SUBEXPEDIENTE ), "Text" ) )."%' ";
                  $sWhere = $sWhere . " OR SEXP.SGD_SEXP_PAREXP3 iLIKE '%".str_replace( '\'', '', tosql( trim( $ps_SGD_EXP_SUBEXPEDIENTE ), "Text" ) )."%' ";
                  $sWhere = $sWhere . " OR SEXP.SGD_SEXP_PAREXP4 iLIKE '%".str_replace( '\'', '', tosql( trim( $ps_SGD_EXP_SUBEXPEDIENTE ), "Text" ) )."%' ";
                  $sWhere = $sWhere . " OR SEXP.SGD_SEXP_PAREXP5 iLIKE  '%".str_replace( '\'', '', tosql( trim( $ps_SGD_EXP_SUBEXPEDIENTE ), "Text" ) )."%' ";
                  $sWhere = $sWhere . " )";
                }

            /* Se decide si busca en radicado de entrada o de salida o ambos */
              $ps_entrada = strip($_GET["s_entrada"]);
              $eLen = strlen($ps_entrada);
              $ps_salida = strip($_GET["s_salida"]);
              $sLen = strlen($ps_salida);

              if($ps_entrada!="9999" ){
                if($sWhere != "")
                  $sWhere .= " and ";
                $HasParam = true;

                if($ps_entrada=="9998"){
                    $sWhere = $sWhere . "((r.radi_nume_radi::text like " . tosql("%4", 
                        "Text") . " or r.radi_nume_radi::text like " . tosql("%5", 
                        "Text") . " or r.radi_nume_radi::text like " . tosql("%6", 
                        "Text") . " or r.radi_nume_radi::text like " . tosql("%7", 
                        "Text") . ") and r.radi_depe_radi != 900 and r.is_borrador is false)";

                } else {
                    $sWhere = $sWhere . "r.radi_nume_radi::text like " . tosql("%".trim($ps_entrada), "Text");

                }
              }


            /* Se recibe el tipo de documento para la bsqueda */
              $ps_TDOC_CODI = $_GET["s_TDOC_CODI"];
              if(is_number($ps_TDOC_CODI) && strlen($ps_TDOC_CODI) && $ps_TDOC_CODI != "9999")
                $ps_TDOC_CODI = tosql($ps_TDOC_CODI, "Number");
              else
                $ps_TDOC_CODI = "";
              if(strlen($ps_TDOC_CODI))
              {
                if($sWhere != "")
                  $sWhere .= " and ";

                $HasParam = true;
                $sWhere = $sWhere . "r.tdoc_codi=" . $ps_TDOC_CODI;
              }

            /* Se recibe la cadena a buscar y el tipo de busqueda (All) (Any) */
              $ps_RADI_NOMB = strip($_GET["s_RADI_NOMB"]);
              $ps_solo_nomb = $_GET["s_solo_nomb"];
              $yaentro=false;

              if(strlen($ps_RADI_NOMB) && !$esNotificacionCircular) //&& $ps_solo_nomb == "Any")
              {
                if($sWhere != "")
                  $sWhere .= " and (";
                $HasParam=true;
                $sWhere .= " ";

                $ps_RADI_NOMB = strtoupper($ps_RADI_NOMB);
                $tok = strtok($ps_RADI_NOMB," ");
                $sWhere .= "(";
                while ($tok) {
                  $sWhere .= "";
                  if ($yaentro == true ) {
                    $sWhere .= " and ";
                  }
                  $sWhere .= "dir.sgd_dir_nomremdes iLIKE '%".$tok."%' ";
                    $tok = strtok(" ");
                  $yaentro=true;
                }
                $sWhere .=") or (";
                $tok = strtok($ps_RADI_NOMB," ");
                $yaentro=false;
                while ($tok) {
                  $sWhere .= "";
                  if ($yaentro == true ) {
                    $sWhere .= " and ";
                  }
                  $sWhere .= "( dir.sgd_dir_nombre iLIKE '%".$tok."%' OR dir.sgd_dir_apellido iLIKE '%".$tok."%' )";
                    $tok = strtok(" ");
                  $yaentro=true;
                }
                $sWhere .= ") or (";
                $yaentro=false;
                $tok = strtok($ps_RADI_NOMB," ");
                if ($yaentro == true ) $sWhere .= " and (";

                $sWhere .= "".$db->conn->Concat("r.ra_asun") . " iLIKE '%".$ps_RADI_NOMB."%' ";
                $sWhere .= " or ".$db->conn->Concat("r.RADI_DATO_001") . " iLIKE '%".$ps_RADI_NOMB."%' ";
                $sWhere .= " or ".$db->conn->Concat("r.RADI_DATO_002") . " iLIKE '%".$ps_RADI_NOMB."%' ";
                $sWhere .= " or ".$db->conn->Concat("r.RADI_DATO_002") . " iLIKE '%".$ps_RADI_NOMB."%' ";
                //$sWhere .= "UPPER(".$db->conn->Concat("r.radi_cuentai") . ") LIKE '%".$ps_RADI_NOMB."%' ";
                $sWhere .= "or UPPER(".$db->conn->Concat("dir.sgd_dir_mail") . ") LIKE '%".$ps_RADI_NOMB."%' ";
                //$sWhere .= "UPPER(".$db->conn->Concat("dir.sgd_dir_direccion") . ") LIKE '%".$ps_RADI_NOMB."%' ";
                //$sWhere .= "UPPER(".$db->conn->Concat("r.radi_nume_guia") . ") LIKE '%".$ps_RADI_NOMB."%' ";
                  $tok = strtok(" ");
                if ($yaentro == true ) $sWhere .= ")";

                $yaentro=true;
                $sWhere .="))";
              }
              else if (strlen($ps_RADI_NOMB))
              {
                if($sWhere != "")
                  $sWhere .= " and ";

                $sWhere .= "(".$db->conn->Concat("r.ra_asun") . " iLIKE '%".$ps_RADI_NOMB."%' ";
                $sWhere .= " or ".$db->conn->Concat("r.RADI_DATO_001") . " iLIKE '%".$ps_RADI_NOMB."%' ";
                $sWhere .= " or ".$db->conn->Concat("r.RADI_DATO_002") . " iLIKE '%".$ps_RADI_NOMB."%' ";
                $sWhere .= " or ".$db->conn->Concat("r.RADI_DATO_002") . " iLIKE '%".$ps_RADI_NOMB."%' )";
              }

              if(strlen($ps_RADI_NOMB) && $ps_solo_nomb == "AllTTT" && !$esNotificacionCircular)
              {
                if($sWhere != "")
                    $sWhere .= " AND (";
                $HasParam=true;
                $sWhere .= " ";

                $ps_RADI_NOMB = strtoupper($ps_RADI_NOMB);
                $tok = strtok($ps_RADI_NOMB," ");
                $sWhere .= "(";
                  $sWhere .= "";
                  if ($yaentro == true ) {
                    $sWhere .= " AND ";
                  }
                  $sWhere .= "dir.sgd_dir_nomremdes iLIKE '%".$ps_RADI_NOMB."%' ";
                    $tok = strtok(" ");
                  $yaentro=true;
                $sWhere .=") OR (";
                $tok = strtok($ps_RADI_NOMB," ");
                $yaentro=false;
                  $sWhere .= "";
                  if ($yaentro == true ) {
                    $sWhere .= " AND ";
                  }
                  $sWhere .= " (dir.sgd_dir_nombre iLIKE '%".$ps_RADI_NOMB."%'  OR dir.sgd_dir_apellido iLIKE '%".$ps_RADI_NOMB."%') ";
                    $tok = strtok(" ");
                  $yaentro=true;
                $sWhere .= ") OR (";
                  $yaentro=false;
                $tok = strtok($ps_RADI_NOMB," ");
                if ($yaentro == true ) $sWhere .= " AND (";
                  $sWhere .= "".$db->conn->Concat("r.ra_asun","r.radi_cuentai","dir.sgd_dir_telefono","dir.sgd_dir_direccion","dir.sgd_dir_mail","r.RADI_DATO_001","r.RADI_DATO_002")." iLIKE '%".$ps_RADI_NOMB."%' ";
                $tok = strtok(" ");
                if ($yaentro == true ) $sWhere .= ")";
                $yaentro=true;
                $sWhere .="))";
              }

              if($HasParam)
                $sWhere = " AND (" . $sWhere . ") ";

            // Se establecen los parametros del script de acuerdo a si es o no una circular interna o externa
              if ($esNotificacionCircular) {
                $select_circular = "sncd.SGD_NOTIF_CIRC_DEST_DESC";

                $from_circular = "SGD_NOTIF_NOTIFICACIONES snn,
                  SGD_NOTIF_CIRCULARES snc,
                  SGD_NOTIF_CIRCULAR_DESTINATARIO sncd";

                $where_circular = " WHERE r.RADI_NUME_RADI = snn.RADI_NUME_RADI
                  AND snn.SGD_NOTIF_CODIGO = snc.SGD_NOTIF_CODIGO
                  AND snc.SGD_NOTIF_CIRC_DEST_CODI = sncd.SGD_NOTIF_CIRC_DEST_CODI AND ";
              } else {
                $select_circular = "dir.SGD_DIR_DIRECCION,
                  dir.SGD_DIR_MAIL,
                  dir.SGD_DIR_NOMREMDES,
                  dir.SGD_DIR_TELEFONO,
                  dir.SGD_DIR_DOC,
                  dir.SGD_DIR_NOMBRE,
                  dir.SGD_DIR_APELLIDO,
                  dir.SGD_TRD_CODIGO";

                $from_circular = "sgd_dir_drecciones dir";

                $where_circular = " WHERE dir.sgd_dir_tipo = 1 AND ";
              }


            //-------------------------------
            // Build base SQL statement - Construccion de consulta
            //-------------------------------
            require_once("../include/query/busqueda/busquedaPiloto1.php");

            $sSQL = "SELECT ".
                  $radi_nume_radi." AS RADI_NUME_RADI,".
                      $db->conn->SQLDate('Y-m-d H:i:s','R.RADI_FECH_RADI')." AS RADI_FECH_RADI,
                  r.RA_ASUN,
                  td.sgd_tpr_descrip, ".
                  $redondeo." as FVCMTO,
                  r.RADI_NUME_HOJA,
                  r.fech_vcmto as FECHA_VCMTO,
                  r.RADI_PATH,
                  r.RADI_USU_ANTE,
                  r.RADI_PAIS,
                  r.RADI_DEPE_ACTU,
                  r.RADI_USUA_ACTU,
                  r.RADI_DEPE_RADI,
                  r.RADI_USUA_RADI,
                  r.CODI_NIVEL,
                  r.SGD_SPUB_CODIGO,
                  r.RADI_DATO_001,
                  r.RADI_DATO_002,
                  r.RADI_NUME_BORRADOR,
                 CASE WHEN r.is_borrador THEN 1 ELSE 0 END borrador,
                  td.sgd_tpr_codigo tprd,
                   (td.sgd_tpr_termino+(date_part('days', r.radi_fech_radi-CURRENT_TIMESTAMP)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between r.radi_fech_radi and CURRENT_TIMESTAMP)))  as diash,".
                  $select_circular;

            /**
              * Busqueda por parameto del expediente
              * Fecha de modificacion: 11-Agosto-2006
              * Modificador: Supersolidaria
              */
            if( strlen( $ps_SGD_EXP_SUBEXPEDIENTE ) != 0 )  $sSQL .= " ,EXP.SGD_EXP_NUMERO";

            $sSQL .= " FROM radicado r, sgd_tpr_tpdcumento td, ".$from_circular;

            /**
              * Busqueda por expediente
              * Fecha de modificacion: 30-Junio-2006
              * Modificador: Supersolidaria
              */
            if( strlen( $ps_SGD_EXP_SUBEXPEDIENTE ) != 0 )
            {
                $sSQL .= ", SGD_EXP_EXPEDIENTE EXP, SGD_SEXP_SECEXPEDIENTES SEXP";
            }

  //Correccion consultas
            $sSQL .= $where_circular;

      /* Consulta de CRA $sSQL .= " WHERE dir.sgd_dir_tipo = 1 AND dir.RADI_NUME_RADI=r.RADI_NUME_RADI AND r.TDOC_CODI=td.SGD_TPR_CODIGO";*/

/*    $sSQL .= "
    dir.sgd_dir_nombre = (select sgd_dir_nombre from sgd_dir_drecciones
    where cast(radi_nume_radi as varchar(15))  like " . tosql("%".trim($ps_RADI_NUME_RADI) ."%", "Text");

    $sSQL .= "
    order by sgd_dir_codigo asc limit 1)  and";

    $sSQL .= "
    dir.sgd_dir_direccion = (select sgd_dir_direccion from sgd_dir_drecciones
    where cast(radi_nume_radi as varchar(15))  like " . tosql("%".trim($ps_RADI_NUME_RADI) ."%", "Text");

    $sSQL .= "
    order by sgd_dir_codigo asc limit 1)  and";
*/
    if (!$esNotificacionCircular) {
      $sSQL .= "dir.RADI_NUME_RADI=r.RADI_NUME_RADI AND r.TDOC_CODI=td.SGD_TPR_CODIGO";
    } else {
      $sSQL .= "r.TDOC_CODI=td.SGD_TPR_CODIGO";
    }
/*Modificación para la CRA, sólo la dependencia 230 puede ver los radicados tipo 4*/
      if ($entidad=="CRA" and  $dependencia!=230)
        $sWhere.=" and substr(r.radi_nume_radi,-1)!=4";
/**********************************************************************************/
            //-------------------------------
            // Assemble full SQL statement
            //-------------------------------
            $sSQL .= $sWhere . $whereTrd . $sOrder;
       //     $sSQL = "select * from ($sSQL)";
            //-------------------------------
            // Execute SQL statement
            //-------------------------------
            $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//echo "<pre>".$sSQL."</pre>"; exit;
            $rs=$db->query($sSQL);
            $db->conn->SetFetchMode(ADODB_FETCH_NUM);

            //-------------------------------
            // Process empty recordset
            //-------------------------------
              if($rs->EOF || !$rs)
              {
            ?>
                            <tr>
                                <td colspan="20" class="alarmas">No hay resultados</td>
                            </tr>
                            <tr>
                                <td colspan="20" class="ColumnTD">
                                    <font class="ColumnFONT">
                                    </tbody>
                        </table>
                    </div>
                </div>
            </article>
        </div>
    </div>

    <?php

    return;
}

//-------------------------------

?>
    <!--tr>
      <td colspan="10" class="DataTD"><b>Total Registros Encontrados: <?=$fldTotal?></b></td>
     </tr-->

    <?php

//-------------------------------
// Initialize page counter and records per page
//-------------------------------
$iCounter = 0;
//-------------------------------

//-------------------------------
// Process page scroller
//-------------------------------
$iPage = $_GET["FormCIUDADANO_Page"]; //FormCiudadano es el formulario de la busqueda
//print ("<BR>($iPage)($iRecordsPerPage)");
if (strlen(trim($iPage)) == 0) {
    $iPage = 1;
} else {
    if ($iPage == "last") {
        $db_count = get_db_value($sCountSQL);
        $dResult = intval($db_count) / $iRecordsPerPage;
        $iPage = intval($dResult);
        if ($iPage < $dResult) {
            $iPage++;
        }

    } else {
        $iPage = intval($iPage);
    }

}

if (($iPage - 1) * $iRecordsPerPage != 0) {
    //print ("<BR>($iPage)($iRecordsPerPage)");
    do {
        $iCounter++;
        $rs->MoveNext();
        //print("Entra......");
    } while ($iCounter < ($iPage - 1) * $iRecordsPerPage && (!$rs->EOF && $rs));

}

$iCounter = 0;
//-------------------------------

//$ruta_raiz ="..";
//include "../processConfig.php";
//include "../jh_class/funciones_sgd.php";
//-------------------------------
// Display grid based on recordset
//-------------------------------.
$i = 1;

$fldRADI_NUME_RADI_old = null;
$fldsSGD_EXP_SUBEXPEDIENTE_old = null;
$contadorImagenes = 0;
$dataArray=array();
while ((!$rs->EOF && $rs) && $iCounter < $iRecordsPerPage) {
	$dataArrayTmp=array();
    $fldRADI_NUME_RADI = $rs->fields['RADI_NUME_RADI'];
	$dataArrayTmp[]=$fldRADI_NUME_RADI;
    $fldRADI_FECH_RADI = $rs->fields['RADI_FECH_RADI'];
    $fldRADI_BORRADOR_RADI = $rs->fields['RADI_NUME_BORRADOR'];
	$dataArrayTmp[]=$fldRADI_BORRADOR_RADI;
	$dataArrayTmp[]=$fldRADI_FECH_RADI;
    $fldsSGD_EXP_SUBEXPEDIENTE = $rs->fields['SGD_EXP_NUMERO'];

    //Busca los radicados que son diferentes entre la anterior
    //iteracion y la nueva si la nueva es igual a la anterior
    //se evalua el expediente y si estos son diferentes o nulos
    //se imprime el resultado de lo contrario se salta.
    //Esto se debe a que la consulta muestra varias veces el
    //resultado si tiene varios usuario y se quiere es mostrar un

    $numRadicadoPadreInicial = substr($rs->fields['RADI_NUME_RADI'], 0, 4);
    if ($fldRADI_NUME_RADI_old != $fldRADI_NUME_RADI
        && (
            ($fldsSGD_EXP_SUBEXPEDIENTE_old == null || $fldsSGD_EXP_SUBEXPEDIENTE == null)
            || ($fldsSGD_EXP_SUBEXPEDIENTE_old != $fldsSGD_EXP_SUBEXPEDIENTE)
        )
    ) {
        $fldRADI_NUME_RADI_old = $fldRADI_NUME_RADI;
        $fldsSGD_EXP_SUBEXPEDIENTE_old = $fldsSGD_EXP_SUBEXPEDIENTE;
    } else { 
        $iCounter++;
        $rs->MoveNext();
        continue;
    }

    if($numRadicadoPadreInicial < 3000 || ($numRadicadoPadreInicial >= 3000 && $rs->fields['RADI_DEPE_ACTU'] != 999)) {

    } else {
        $iCounter++;
        $rs->MoveNext();
        continue;   
    }

    $borrador = $rs->fields['BORRADOR'];
    $fldASUNTO = $rs->fields['RA_ASUN'];
    $fldTIPO_DOC = $rs->fields['SGD_TPR_DESCRIP'];
    $fldNUME_HOJAS = $rs->fields['RADI_NUME_HOJA'];
    $fldRADI_PATH = $rs->fields['RADI_PATH'];
    $fldCUENTAINTERNA = $rs->fields['RADI_CUENTAI'];
    $aRADI_DEPE_ACTU = $rs->fields['RADI_DEPE_ACTU'];
    $aRADI_USUA_ACTU = $rs->fields['RADI_USUA_ACTU'];
    $fldUSUA_ANTE = $rs->fields['RADI_USU_ANTE'];
    $fldPAIS = $rs->fields['RADI_PAIS'];
    $FECHA_VCMTO = $rs->fields['FECHA_VCMTO'];
    $nivelRadicado = $rs->fields['CODI_NIVEL'];
    $seguridadRadicado = $rs->fields['SGD_SPUB_CODIGO'];
    $seguridadExpediente = $rs->fields['SGD_EXP_PRIVADO'];
    $fldDato001 = $rs->fields['RADI_DATO_001'];
    $fldDato002 = $rs->fields['RADI_DATO_002'];
    $diasrestantes=$rs->fields['DIASH'];
    $tprd=$rs->fields['TPRD'];
    $USUA_CODI_PROYECTO    = $rs->fields['RADI_USUA_RADI'];
    $DEPE_CODI_PROYECTO    = $rs->fields['RADI_DEPE_RADI'];






    if ($tipoReg == 1) {
        $tipoRegDesc = "Ciudadano";
    }

    if ($tipoReg == 2) {
        $tipoRegDesc = "Empresa";
    }

    if ($tipoReg == 3) {
        $tipoRegDesc = "Entidad";
    }

    if ($tipoReg == 4) {
        $tipoRegDesc = "Funcionario";
    }

    $fldNOMBRE = str_replace($ps_RADI_NOMB, "<font color=green><b>$ps_RADI_NOMB</b></FONT>", tohtml($fldNOMBRE));
    $fldAPELLIDO = str_replace($ps_RADI_NOMB, "<font color=green><b>$ps_RADI_NOMB</b></FONT>", tohtml($fldAPELLIDO));
    $fldASUNTO = str_replace($ps_RADI_NOMB, "<font color=green><b>$ps_RADI_NOMB</b></FONT>", tohtml($fldASUNTO));

//-------------------------------
    // Busquedas Anidadas
    //-------------------------------
    $queryDep = "select DEPE_NOMB from dependencia where DEPE_CODI=$aRADI_DEPE_ACTU";
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $rs2 = $db->query($queryDep);
    $fldDEPE_ACTU = $rs2->fields['DEPE_NOMB'];

    $queryUs = "select USUA_NOMB from USUARIO where DEPE_CODI=$aRADI_DEPE_ACTU and USUA_CODI=$aRADI_USUA_ACTU ";
    $rs3 = $db->query($queryUs);
    $fldUSUA_ACTU = $rs3->fields['USUA_NOMB'];

    $queryCod = "select DEPE_CODI from USUARIO where USUA_CODI = " . $_SESSION["codusuario"];
    $rs4 = $db->query($queryCod);
    $depe_codi = $rs4->fields['DEPE_CODI'];
    $db->conn->SetFetchMode(ADODB_FETCH_NUM);

    if (strpos($fldRADI_PATH, "/") != 0) {
        $fldRADI_PATH = "/" . $fldRADI_PATH;
    }
    $linkImagen = "$ruta_raiz/bodega" . $fldRADI_PATH;
    $extension = explode('.', $fldRADI_PATH);
    $contadorImagenes++;

    if ($seguridadRadicado == 0
            or ($seguridadRadicado == 1 && $_SESSION["dependencia"] == $aRADI_DEPE_ACTU)
            or ($seguridadRadicado == 2 && (($_SESSION["dependencia"] == $aRADI_DEPE_ACTU && ($_SESSION["USUA_JEFE_DE_GRUPO"] == true) || ($_SESSION["dependencia"] == $aRADI_DEPE_ACTU && $_SESSION["codusuario"] == $aRADI_USUA_ACTU)) || ($_SESSION["dependencia"] == $DEPE_CODI_PROYECTO && $_SESSION["codusuario"] == $USUA_CODI_PROYECTO) ) ) ) {
        if ($extension[1] == 'pdf') {
            //Muestra el pdf en el visor modal
            $linkDocto = "<a href='javascript:void(0)' class='abrirVisor' contador=$contadorImagenes link=$linkImagen>";
        } else {
            //Funcionalidad para descargar el archivo.
            $linkDocto = "<a class=\"vinculos\" href=\"#2\" onclick=\"funlinkArchivo('$fldRADI_NUME_RADI','$ruta_raiz');\" target\"Imagen$iii\">";
        }
        $linkInfGeneral = "<a class='vinculos' href='../verradicado.php?verrad=$fldRADI_NUME_RADI&nomcarpeta=Busquedas'>";
        $verImg = true;
        $noPermisoFlag = 1;
    } else {
        $linkDocto = "<a class='btn btn-danger btn-xs' href='javascript:noPermiso()' > ";
        $linkInfGeneral = "<a class='btn btn-danger btn-xs' href='javascript:noPermiso()' > ";
        $noPermisoFlag = 0;
    }

/*
 * Ajuste validacion permisos unificados
 * @author Liliana Gomez Velasquez
 * @fecha 11 septiembre 2009
 */

    include_once "$ruta_raiz/tx/verLinkArchivo.php";

    $verLinkArchivo = new verLinkArchivo($db);

    $resulVali = $verLinkArchivo->valPermisoRadi($fldRADI_NUME_RADI);
    $valImg = $resulVali['verImg'];
    $pathImagen = $resulVali['pathImagen'];
    $fldsSGD_EXP_SUBEXPEDIENTE = $resulVali['numExpe'];

    //Fin Modificacion
    /*if  ($nivelRadicado <=$nivelus)
    {
    $linkDocto = "<a class='vinculos' href='../bodega/$fldRADI_PATH' target='Imagen$iii'>";
    $linkInfGeneral = "<a class='vinculos' href='../verradicado.php?verrad=$fldRADI_NUME_RADI&".session_name()."=".session_id()."&carpeta=8&nomcarpeta=Busquedas&tipo_carp=0'>";
    }
     */

    if (strlen($ps_SGD_EXP_SUBEXPEDIENTE) == 0) {
        $consultaExpediente = "SELECT exp.SGD_EXP_NUMERO, sexp.sgd_exp_privado  , u.usua_doc DOC_RESPONSABLE, u.DEPE_CODI DEPE_RESPONSABLE
                     FROM SGD_EXP_EXPEDIENTE exp,
      sgd_sexp_secexpedientes sexp left join usuario u
                           on sexp.usua_doc_responsable=u.usua_doc
         WHERE
          exp.sgd_exp_estado != 2 and
          exp.radi_nume_radi= $fldRADI_NUME_RADI
          AND exp.sgd_exp_numero=sexp.sgd_exp_numero
          AND exp.sgd_exp_fech=(SELECT MIN(exp2.SGD_EXP_FECH) as minFech from sgd_exp_expediente exp2 where exp2.radi_nume_radi= $fldRADI_NUME_RADI)";
      // $db->debug = true;
        //echo "->".$consultaExpediente; exit;
        $rsE = $db->query($consultaExpediente);
        $fldsSGD_EXP_SUBEXPEDIENTE = $rsE->fields["SGD_EXP_NUMERO"];
        $fldsSGD_EXP_PRIVADO = $rsE->fields["SGD_EXP_PRIVADO"];
        $depeResponsable = $rsE->fields["DEPE_RESPONSABLE"];
        $docResponsable = $rsE->fields["DOC_RESPONSABLE"];
        $imgExp = "";
        if ($fldsSGD_EXP_PRIVADO == 1) {
            $valImg = "No";
            // si es admon ó es el usuario responsable ó es un usuario de la dependencia
            if (($codusuario == 1 || $usua_doc == $docResponsable) || $dependencia == $depeResponsable) {
                $valImg = "SI";
            } else {
                $imgExp = "<img src='../img/expCandado.png' height=18>";
                $noPermisoFlag = 0;
            }
        }
        if ($fldsSGD_EXP_PRIVADO == 2) {
            $valImg = "No";
            // si es el responsable ó es jefe del area de la dependencia
            if (($codusuario == 1 || $usua_doc == $docResponsable) || ($dependencia == $depeResponsable && $_SESSION['USUA_JEFE_DE_GRUPO'])) {
                $valImg = "SI";
            } else {
                $imgExp = "<img src='../img/expCandado.png' height=18>";
                $noPermisoFlag = 0;
            }
        }
        if ($fldsSGD_EXP_PRIVADO == 3) {
            $valImg = "No";
            if (($codusuario == 1 || $usua_doc == $docResponsable) && $dependencia == $depeResponsable) {
                $valImg = "SI";
            } else {
                $imgExp = "<img src='../img/expCandado.png' height=18>";
                $noPermisoFlag = 0;
            }

        }
    }
	$dataArrayTmp[]=$fldsSGD_EXP_SUBEXPEDIENTE;
	$dataArrayTmp[]=$noPermisoFlag == 1?$fldASUNTO:'';
	$dataArrayTmp[]=$fldCUENTAINTERNA;
	$dataArrayTmp[]=$fldTIPO_DOC;
    if ($esNotificacionCircular) {
        $fldDESTINATARIOS = $rs->fields['SGD_NOTIF_CIRC_DEST_DESC'];
	$dataArrayTmp[]=$noPermisoFlag == 1?$fldDESTINATARIOS:'';
    } else {
        $fldDIRECCION_C = $rs->fields['SGD_DIR_DIRECCION'];
	$dataArrayTmp[]=$noPermisoFlag == 1?$fldDIRECCION_C:'';
        $fldDIGNATARIO = $rs->fields['SGD_DIR_NOMBRE'];
        $fldAPELLIDO = $rs->fields['SGD_DIR_APELLIDO'];
        $fldTELEFONO_C = $rs->fields['SGD_DIR_TELEFONO'];
	$dataArrayTmp[]=$noPermisoFlag == 1?$fldTELEFONO_C:'';
        $fldMAIL_C = $rs->fields['SGD_DIR_MAIL'];
	$dataArrayTmp[]=$noPermisoFlag == 1?$fldMAIL_C:'';
	$dataArrayTmp[]=$noPermisoFlag == 1?$fldDIGNATARIO:'';
        $fldNOMBRE = $rs->fields['SGD_DIR_NOMREMDES'];
	$dataArrayTmp[]=$noPermisoFlag == 1?$fldNOMBRE:'';
        $fldCEDULA = $rs->fields['SGD_DIR_DOC'];
	$dataArrayTmp[]=$noPermisoFlag == 1?$fldCEDULA:'';
        $tipoReg = $rs->fields['SGD_TRD_CODIGO'];
    }
    $dataArrayTmp[]=$fldUSUA_ACTU;
    $dataArrayTmp[]=$fldDEPE_ACTU;
    $dataArrayTmp[]=$fldUSUA_ANTE;
    $verImg = ($seguridadRadicado == 1) ? ($fldUSUA_ACTU != $_SESSION['usua_nomb'] ? false : true) : ($nivelRadicado > $nivelus ? false : true);

    if ($valImg == "SI" && $noPermisoFlag == 1) {
        if ($extension[1] == 'pdf') {
            //Muestra el pdf en el visor modal
            $linkDocto = "<a href='#'  class='btn btn-success btn-xs btn-visorimage' data-toggle='modal' data-target='DetEsta' contador=$contadorImagenes data-link='$linkImagen' data-rad='$fldRADI_NUME_RADI'>";
        } else {
            //Funcionalidad para descargar el archivo.
            $linkDocto = "<a class=\"btn btn-success btn-xs\" href=\"#2\" onclick=\"funlinkArchivo('$fldRADI_NUME_RADI','$ruta_raiz');\" target\"Imagen$iii\">";
        }
        $linkInfGeneral = "<a class='btn btn-primary btn-xs' href='../verradicado.php?verrad=$fldRADI_NUME_RADI&nomcarpeta=Busquedas'>";
        $verImg = true;
    } else {
        $linkDocto = "<a class='btn btn-danger btn-xs' href='javascript:noPermiso()' > ";
        $linkInfGeneral = "<a class='btn btn-danger btn-xs' href='javascript:noPermiso()' > ";
    }

//$verImg= $verImg && !($fila['SGD_SPUB_CODIGO']==1);

//$linkInfGeneralVin = "<a class='vinculos' href='../vinculacion/mod_vinculacion.php?numRadi=$fldRADI_NUME_RADI&carpeta=$carpeAnt&nomcarpeta=$nomcarpeta&verrad=$verrad&".session_name()."=".session_id()."&krd=$krd&carpeta=$carpeAnt&nomcarpeta=$nomcarpeta&tipo_carp=0' >";

//$linkInfGeneral =
    //-------------------------------
    // Process the HTML controls  //Dispaly html de leidos.
    //-------------------------------
   /* if ($i == 1) {
        $formato = "listado1";
        $i = 2;
    } else {
        $formato = "listado2";
        $i = 1;
    }*/ 
    $formato=" ";
    if($borrador=='1'){
        $formato="table-warning";
    }
    ?>
    <tr class="<?=$formato?>">
    <td>
        <?php if ($indiVinculo == 1)
    {
    ?>
        <td class="leidos" align="center" width="70">
            <A href="javascript:pasar_datos('<?=$fldRADI_NUME_RADI?>');">
                Vincular
        </td>
        <?php
    }
    if ($indiVinculo == 2)
    {
    ?>
        <td class="leidos" align="center" width="70">
            <A href="javascript:pasar_datos('<?=$fldsSGD_EXP_SUBEXPEDIENTE?>',2);">
                Vincular
        </td>

        <?php
    }
  ?>
     <?php if (strlen($fldRADI_PATH)){ $iii = $iii +1;?>
            <? echo ($linkDocto);}?>
            <?if (strlen($fldRADI_PATH)){?>
              <i class="fa fa-eye"></i>
            </a>
            <?}
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
          </div>";
    ?><?=$linkInfGeneral?> <i class="fa fa-align-justify"></i></a>
    
  </td>
  <td><?=$fldRADI_NUME_RADI?></td>
        <td class="leidos">
            <?=tohtml($fldRADI_BORRADOR_RADI)?></td>            
        <td class="leidos">
            <?=tohtml($fldRADI_FECH_RADI)?></td>
        <td class="leidos">
            <?=$fldsSGD_EXP_SUBEXPEDIENTE?>&nbsp; <?=$imgExp?></td>
        <td class="leidos">
            <? if($noPermisoFlag == 1) { ?>                                
                <?=$fldASUNTO?> &nbsp;</td>
            <? } else { ?>      
                 &nbsp;</td>                          
            <? } ?> 
        <td class="leidos">
            <?=tohtml($fldCUENTAINTERNA)?>&nbsp;</td>
        <td class="leidos">
            <?=tohtml($fldTIPO_DOC)?>&nbsp;</td>
        <!--<td class="leidos">
            <?=$tipoRegDesc;?>&nbsp;</td>
        <td class="leidos">
            <?=tohtml($fldNUME_HOJAS)?>&nbsp;</td>-->
        <? if ($esNotificacionCircular) { ?>
        <td class="leidos">
            <? if($noPermisoFlag == 1) { ?>                                
                <?=tohtml($fldDESTINATARIOS)?>&nbsp;</td>
            <? } else { ?>      
                &nbsp;</td>                          
            <? } ?>                         
        <? } else { ?>
        <td class="leidos">
            <? if($noPermisoFlag == 1) { ?>                                
                <?=tohtml($fldDIRECCION_C)?>&nbsp;</td>
            <? } else { ?>      
                &nbsp;</td>                          
            <? } ?>                                                 
        <td class="leidos">
            <? if($noPermisoFlag == 1) { ?>                                
                <?=tohtml($fldTELEFONO_C)?>&nbsp;</td>
            <? } else { ?>      
                &nbsp;</td>                          
            <? } ?>                                                                        
        <td class="leidos">
            <? if($noPermisoFlag == 1) { ?>                                
                <?=tohtml($fldMAIL_C)?>&nbsp;</td>
            <? } else { ?>      
                &nbsp;</td>                          
            <? } ?>                                                                         
        <td class="leidos">
            <? if($noPermisoFlag == 1) { ?>                                
                <?=tohtml($fldDIGNATARIO)?>&nbsp;</td>
            <? } else { ?>      
                &nbsp;</td>                          
            <? } ?>        
        <td class="leidos">
            <? if($noPermisoFlag == 1) { ?>                                
                <?=$fldNOMBRE?>&nbsp;</td>
            <? } else { ?>      
                &nbsp;</td>                          
            <? } ?>                                
        <td class="leidos">
            <? if($noPermisoFlag == 1) { ?>                                
                <?=tohtml($fldCEDULA)?>&nbsp;</td>
            <? } else { ?>      
                &nbsp;</td>                          
            <? } ?>                                                        
        <? } ?>

        <td class="leidos">
            <?=tohtml($fldUSUA_ACTU)?>&nbsp;</td>
        <td class="leidos">
            <?=tohtml($fldDEPE_ACTU)?>&nbsp;</td>
        <td class="leidos">
            <?=tohtml($fldUSUA_ANTE)?>&nbsp;</td>
        <!--<td class="leidos">
            <?=tohtml($fldPAIS);?>&nbsp;</td>-->
        <?php

//Calculamos dias HABILES
    /*
    $sqlsum="SELECT NOH_FECHA,SUMDIAS FROM SGD_NOH_NOHABILES WHERE NOH_FECHA= '$fvcmto'";
    //$db->conn->debug = true;
    $rssum = $db->query($sqlsum);
    $regfecha            = $rssum->fields['NOH_FECHA'];
    $sumdia            = $rssum->fields['SUMDIAS'];

    if ($regfecha!=0){
    $fvcmto=date('Y-m-d', strtotime("$fvcmto + $sumdia days"));
    }

    $hoy = date("Y-m-d");
    if ($hoy <= $fvcmto){
    $sqlnh="select count(NOH_FECHA)AS TD from SGD_NOH_NOHABILES where NOH_FECHA BETWEEN to_date('".date('d/m/y', strtotime("$hoy"))."', 'dd/mm/yy') AND to_date('".date('d/m/y', strtotime("$fvcmto"))."', 'dd/mm/yy')";
    //$db->conn->debug = true;
    $rsnh = $db->query($sqlnh);
    $tnh = $rsnh->fields['TD'];
    $s = strtotime($fvcmto)-strtotime($hoy);
    $d = intval($s/86400);
    $s -= $d*86400;
    $h = intval($s/3600);
    $s -= $h*3600;
    $m = intval($s/60);
    $s -= $m*60;
    $drc= $d.$space;
    $drh= $drc-$tnh;
    }else {
    $sqlnh="select count(NOH_FECHA)AS TD from SGD_NOH_NOHABILES where NOH_FECHA BETWEEN to_date('".date('d/m/y', strtotime("$fvcmto"))."', 'dd/mm/yy') AND to_date('".date('d/m/y', strtotime(    "$hoy"))."', 'dd/mm/yy')";
    $rsnh = $db->query($sqlnh);
    $tnh = $rsnh->fields['TD'];
    $s = strtotime($fvcmto)-strtotime($hoy);
    $d = intval($s/86400);
    $s -= $d*86400;
    $h = intval($s/3600);
    $s -= $h*3600;
    $m = intval($s/60);
    $s -= $m*60;
    $drc= $d.$space;
    $drh= $drc+$tnh;}

    //fin de CALCULOS DE DIAS HABILES
     */
    ?>

        <td class="leidos">
            <?=$tprd!=0?$diasrestantes :'N/A ó termino no definido '; //.$Dha->getDiasRestantes($fldRADI_NUME_RADI,$FECHA_VCMTO,$fldTIPO_DOC); // if ($fldRADI_DEPE_ACTU!=999){ echo tohtml($fldDIASR.$drh);} else {echo "Sal";} 
            ?>&nbsp;
        </td>
        <!--<td class="leidos"><?=tohtml($fldDato001);?>&nbsp;</td>
        <td class="leidos"><?=tohtml($fldDato002);?>&nbsp;</td>-->
    </tr>
    <?
	$dataArrayTmp[]=$tprd!=0?$diasrestantes :'N/A ó termino no definido ';
	$dataArray[]=$dataArrayTmp;
    $iCounter++;
    $rs->MoveNext();
}


//-------------------------------
//  Record navigator.
//-------------------------------
?>
    <tr>
        <td colspan="20" class="ColumnTD">
            <font class="ColumnFONT">
                <?php
// Navigation begin
    $bEof = $rs;

    if (($bEof && !$bEof->EOF) || $iPage != 1) {

        $iCounter = 1;
        $iHasPages = $iPage;
        $sPages = "";
        $iDisplayPages = 0;
        $iNumberOfPages = 60; /* El nmero de paginas que aparecera en el navegador al pie de la pagina */

        while (!$rs->EOF && $rs) {
            if ($iCounter == $iRecordsPerPage) {
                $iCounter = 0;
                $iHasPages = $iHasPages + 1;
            }
            $iCounter++;
            $rs->MoveNext();
        }
        if (($rs->EOF || !$rs) && $iCounter > 1) {
            $iHasPages++;
        }

        if (($iHasPages - $iPage) < intval($iNumberOfPages / 2)) {
            $iStartPage = $iHasPages - $iNumberOfPages;
        } else {
            $iStartPage = $iPage - $iNumberOfPages + intval($iNumberOfPages / 2);
        }

        if ($iStartPage < 0) {
            $iStartPage = 0;
        }

        for ($iPageCount = $iPageCount + 1; $iPageCount <= $iPage - 1; $iPageCount++) {
            $sPages .= "<a href=" . $sFileName . "?" . $form_params . $sSortParams . "FormCIUDADANO_Page=" . $iPageCount . "#RADICADO\"><font " . "class=\"ColumnFONT\"" . ">" . $iPageCount . "</a>&nbsp;";
            $iDisplayPages++;
        }

        $sPages .= "<font " . "class=\"paginacion\"" . "><b>" . $iPage . "</b>&nbsp;";
        $iDisplayPages++;

        $iPageCount = $iPage + 1;

        while ($iDisplayPages < $iNumberOfPages && $iStartPage + $iDisplayPages < $iHasPages) {
            $sPages .= "<a href=\"" . $sFileName . "?" . $form_params . $sSortParams . "FormCIUDADANO_Page=" . $iPageCount . "#RADICADO\"><font " . "class=\"ColumnFONT\"" . ">" . $iPageCount . "</a>&nbsp;";
            $iDisplayPages++;
            $iPageCount++;
        }

        if ($iPage == 1) {
            echo "<font class='paginacion'> &nbsp;[Primero] &nbsp;<font class='paginacion'> &nbsp;[Anterior] &nbsp;";
        } else {
            ?>
                <a href="<?=$sFileName?>?<?=$form_params?><?=$sSortParams?>FormCIUDADANO_Page=1#RADICADO">
                    <font class="paginacion">Primero
                </a>
                <a href="<?=$sFileName?>?<?=$form_params?><?=$sSortParams?>FormCIUDADANO_Page=<?=$iPage - 1?>#RADICADO">
                    <font class="paginacion">Anterior
                </a>
                <?php
}
        echo "&nbsp;[&nbsp;" . $sPages . "]&nbsp;";
        if ($rs->EOF) {
            echo "<font class='ColumnFONT'>&nbsp; [Siguiente] &nbsp;<font class='ColumnFONT'> &nbsp;[Ultimo]&nbsp; ";
        } else {
            ?>
                <a href="<?=$sFileName?>?<?=$form_params?><?=$sSortParams?>FormCIUDADANO_Page=<?=$iPage + 1?>#RADICADO">
                    <font class="ColumnFONT">Siguiente
                </a>
                <?php
}
    }
    ?>
        </td>
    </tr>
    </tbody>
    </table>
    <?php
    echo '<script  type="text/javascript"> var data_array='.json_encode($dataArray).';</script>';
   // echo "<a style='border:0px' href='$ruta_raiz/adodb/adodb-doc.inc.php?".session_name()."=".session_id()."' target='_blank'><img src='$ruta_raiz/adodb/compfile.png' width='40' heigth='    40' border='0' ></a>";
   // echo "<a href='$ruta_raiz/adodb/adodb-xls.inc.php?".session_name()."=".session_id()."' target='_blank'><img src='$ruta_raiz/adodb/spreadsheet.png' width='40' heigth='40' border='0'></a>";
    $xsql=serialize($sSQL);
    $_SESSION['xsql']=$xsql;
if($iNumberOfPages > 1){
    echo "<a href='$ruta_raiz/busqueda/xls-download.php?type=sql' target='_blank'><img src='$ruta_raiz/adodb/spreadsheet.png' width='40' heigth='40' border='0' title='Todos'></a>";
}    
    echo "<img src='$ruta_raiz/adodb/spreadsheet.png' width='40' heigth='40' border='0' onclick='downloadXLS()' title='Sólo visibles'>";

}

//===============================
// Display Grid Form
//-------------------------------
function EmpresaESP_show($nivelus)
{
//-------------------------------
    // Initialize variables
    //-------------------------------

}

//===============================
// Display Grid Form
//-------------------------------
function OtrasEmpresas_show($nivelus)
{

}

function FUNCIONARIO_show($nivelus)
{

}

function resolverTipoCodigo($tipo)
{
    $salida;
    switch ($tipo) {
        case 1:
            $salida = "Ciudadano";
            break;
        case 2:
            $salida = "Empresa";
            break;
        case 3:
            $salida = "Entidad";
            break;
        case 4:
            $salida = "Funcionario";
            break;
    }
    return $salida;
}

function resalaltarTokens(&$tkens, $busqueda)
{
    $salida = $busqueda;
    $tok = explode(" ", $tkens);
    foreach ($tok as $valor) {
        $salida = eregi_replace($valor, "<font color=\"green\"><b>" . strtoupper($valor) . "</b></font>", $salida);
    }
    return $salida;
}

function pintarResultadoConsultas(&$fila, $indice, $numColumna) //aqui se dibujan los resultados de las consultas

{
    global $ruta_raiz, $ps_RADI_NOMB;
    $ps_RADI_NOMB = trim($_GET["s_RADI_NOMB"]);
    $verImg = ($fila['SGD_SPUB_CODIGO'] == 1) ? ($fila['USUARIO_ACTUAL'] != $_SESSION['usua_nomb'] ? false : true) : ($fila['USUA_NIVEL'] > $_SESSION['nivelus'] ? false : true);
    $verImg = $verImg && !($fila['SGD_EXP_PRIVADO'] == 1);
    $salida = "<span class=\"leidos\">";
    switch ($numColumna) {
        case 0:
            $salida = $indice;
            break;
        case 1:
            if ($fila['RADI_PATH'] && $verImg) {
                $salida = "<a class=\"vinculos\" href=\"{$ruta_raiz}bodega" . $fila['RADI_PATH'] . "\" target=\"imagen" . (strlen($fila['RADI_PATH']) + 1) . "\">" . $fila['RADI_NUME_RADI'] . "</a>";
            } else {
                $salida .= $fila['RADI_NUME_RADI'];
            }

            break;
        case 2:
            if ($verImg) {
                $salida = "<a class=\"vinculos\" href=\"{$ruta_raiz}verradicado.php?verrad=" . $fila['RADI_NUME_RADI'] . "&amp;&amp;nomcarpeta=Busquedas \" >" . $fila['RADI_FECH_RADI'] . "</a>";
            } else {
                $salida = "<a class=\"vinculos\" href=\"#\" onclick=\"noPermiso();\">" . $fila['RADI_FECH_RADI'] . "</a>";
            }

            break;
        case 3:
            $salida .= $fila['SGD_EXP_NUMERO'];
            break;
        case 4:
            if ($ps_RADI_NOMB) {
                $salida .= resalaltarTokens($ps_RADI_NOMB, $fila['RA_ASUN']);
            } else {
                $salida .= htmlentities($fila['RA_ASUN']);
            }

            break;
        case 5:
            $salida .= tohtml($fila['SGD_TPR_DESCRIP']); //resolverTipoDocumento($fila['TD']);
            break;
        case 6:
            $salida .= resolverTipoCodigo($fila['SGD_TRD_CODIGO']);
            break;
        case 7:
            $salida .= tohtml($fila['RADI_NUME_HOJA']);
            break;
        case 8:
            if ($ps_RADI_NOMB) {
                $salida .= resalaltarTokens($ps_RADI_NOMB, $fila['SGD_DIR_DIRECCION']);
            } else {
                $salida .= htmlentities($fila['SGD_DIR_DIRECCION']);
            }

            break;
        case 9:
            $salida .= tohtml($fila['SGD_DIR_TELEFONO']);
            break;
        case 10:
            $salida .= tohtml($fila['SGD_DIR_MAIL']);
            break;
        case 11:
            if ($ps_RADI_NOMB) {
                $salida .= resalaltarTokens($ps_RADI_NOMB, $fila['SGD_DIR_NOMBRE']);
            } else {
                $salida .= tohtml($fila['SGD_DIR_NOMBRE']);
            }

            break;
        case 12:
            if ($ps_RADI_NOMB) {
                $salida .= resalaltarTokens($ps_RADI_NOMB, $fila['SGD_DIR_NOMREMDES']);
            } else {
                $salida .= tohtml($fila['SGD_DIR_NOMREMDES']);
            }

            break;
        case 13:
            $salida .= tohtml($fila['SGD_DIR_DOC']);
            break;
        case 14:
            if ($ps_RADI_NOMB) {
                $salida .= resalaltarTokens($ps_RADI_NOMB, $fila['USUARIO_ACTUAL']);
            } else {
                $salida .= tohtml($fila['USUARIO_ACTUAL']);
            }

            break;
        case 15:
            $salida .= tohtml($fila['DEPE_NOMB']);
            break;
        case 16:
            if ($ps_RADI_NOMB) {
                $salida .= resalaltarTokens($ps_RADI_NOMB, $fila['USUARIO_ANTERIOR']);
            } else {
                $salida .= htmlentities(tohtml($fila['USUARIO_ANTERIOR']));
            }

            break;
        case 17:
            $salida .= tohtml($fila['RADI_PAIS']);
            break;
        case 18:
            $salida .= ($fila['RADI_DEPE_ACTU'] != 999) ? tohtml($fila['DIASR']) : "Sal";
            break;
    }
    return $salida . "</span>";
}

function buscar($nivelus, $tpRemDes, $whereFlds)
{
    Ciudadano_show($nivelus, $tpRemDes, $whereFlds);
}
?>
  <div class="modal  show" id="DetEsta" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-argo modal-xl" style='min-width: 99%;'>
            <div class="modal-content" style='min-height: 90vh'>
                <div class="modal-header p-2">
                    <label class="modal-title " id="titDet">Vista Radicado</label>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body p-0" style='overflow: auto;height:100'>
                <iframe name="mainFrameView" id="mainFrameView" src="" border="-1" cellpadding="0" style="width: 100%;border-top-left-radius: 10px" cellspacing="0" marginwidth="0" marginheight="0" scrolling="auto" width="100%" height="100%" frameborder="0" framespacing="0" allowtransparency="0"></iframe>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?=$ruta_raiz?>/js/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap/popper.min.js"></script>
    <script type="text/javascript" src="<?=$ruta_raiz?>/js/bootstrap/bootstrap4.min.js"></script>
    <script>
    $(document).ready(function() {
    $('.abrirVisor').click(function() {
        var contador = $(this).attr('contador');
        var link = $(this).attr('link');
        var visorId = "#visor_" + contador;
        $(visorId).append("<iframe style='width:100%; height:89vh; z-index:-2;' src=" + link +
            "></iframe>");
        $(visorId).dialog();
    });

    $('.cerrarVisor').click(function() {
        var visorId = "#visor_" + $(this).attr('contador');
        $(visorId).dialog('destroy');
    });
});

    $("#tb-resp").on("click", ".btn-visorimage", function () {
    
      var link = $(this).data('link');
      var rad = $(this).data('rad');
      console.log(rad+' '+link);
      $('#titDet').html('Ver radicado '+rad);
      $('#DetEsta').modal('show');
      document.getElementById('mainFrameView').src = link;
      


    });
    </script>
</body>

</html>
