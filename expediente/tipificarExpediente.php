<?
/**
 * @author Jairo Losada   <jlosada@gmail.com>
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright

 SIIM2 Models are the data definition of SIIM2 Information System
 * Copyright (C) 2013 Infometrika Ltda.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
session_start();

foreach ($_GET as $key => $valor)
    ${$key} = $valor;
foreach ($_POST as $key => $valor)
    ${$key} = $valor;

$ruta_raiz   = "..";
$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$tpNumRad    = $_SESSION["tpNumRad"];
$tpPerRad    = $_SESSION["tpPerRad"];
$tpDescRad   = $_SESSION["tpDescRad"];
$tip3Nombre  = $_SESSION["tip3Nombre"];
$tpDepeRad   = $_SESSION["tpDepeRad"];

$usuaPermExpediente = $_SESSION["usuaPermExpediente"];
$digitosDependencia = $_SESSION["digitosDependencia"];
$depDireccion = $_SESSION["depDireccion"];
$perm_crea_exp_todasdependencias = 'no';

if (!$nurad)
    $nurad = $rad;
if ($nurad) {
    $ent = substr($nurad, -1);
}
define('ADODB_ASSOC_CASE', 1);
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug = true;

include_once "$ruta_raiz/include/tx/Historico.php";
include_once("$ruta_raiz/class_control/TipoDocumental.php");
include_once "$ruta_raiz/include/tx/Expediente.php";
$trd = new TipoDocumental($db);

$encabezadol = "$PHP_SELF?" . session_name() . "=" . session_id() . "&opcionExp=$opcionExp
    &numeroExpediente=$numeroExpediente&nurad=$nurad&coddepe=$coddepe
    &codusua=$codusua&depende=$depende&ent=$ent&tdoc=$tdoc
    &codiTRDModi=$codiTRDModi&codiTRDEli=$codiTRDEli
    &codserie=$codserie&tsub=$tsub&ind_ProcAnex=$ind_ProcAnex";
?>

<html>

<head>
    <title>..:: Expediente ::..</title>
    <?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>

    <script>
        function regresar() {
            document.TipoDocu.submit();
        }

        function Start(URL, WIDTH, HEIGHT) {
            windowprops = "top=0,left=0,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=" + WIDTH + ",height=" + HEIGHT;
            preview = window.open(URL, "preview", windowprops);
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="well well-sm well-light">
            <div id="spiffycalendar" class="text"></div>
            <link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
            <form method="post" action="<?= $encabezadol ?>" name="TipoDocu" class="smart-form">
            <?

            $resTrd= $trd->consultaTRDradicado($nurad) ;
            $msgError = "
                <div class='alert alert-danger fade in alertFixed' >
                <i class='fa-fw fa fa-times'></i>
                <strong>Error!</strong>
                    Para poder crear un expediente debe primero clasificar el radicado.
                </div>";


            if($resTrd==-1) die($msgError);


            $Rquery = "Select sgd_srd_codigo, sgd_sbrd_codigo,  sgd_srd_id, sgd_sbrd_id
                from
                sgd_mrd_matrird
                where
                sgd_mrd_codigo in (select sgd_mrd_codigo from sgd_rdf_retdocf
                where
                radi_nume_radi = '$nurad' order by sgd_rdf_fech desc limit 1 )";

            $Rrs = $db->conn->Execute($Rquery);
            $R_codigo_serie = $Rrs->fields["SGD_SRD_CODIGO"];
            $R_codigo_subserie = $Rrs->fields["SGD_SBRD_CODIGO"];
            $idSerie = $Rrs->fields["SGD_SRD_ID"];
            $idSubSerie = $Rrs->fields["SGD_SBRD_ID"];

            /* * Adicion nuevo Registro */
            if ($Actualizar && $tsub != 0 && $codserie != 0) {
                if (!$digCheck) {
                    $digCheck = "E";
                }
                $codiSRD = $codserie;
                $codiSBRD = $tsub;
                $trdExp = substr("00" . $codiSRD, -2) . substr("00" . $codiSBRD, -2);
                $expediente = new Expediente($db);

                if (!$expManual) {
                    $secExp = $expediente->secExpediente($dependenciaExp, $codiSRD, $codiSBRD, $anoExp);
                } else {
                    $secExp = $consecutivoExp;
                }

                //$consecutivoExp = substr("0000000" . $secExp, -7);
                $consecutivoExp = str_pad($secExp, 5, "0", STR_PAD_LEFT);
                $cDependencia = str_pad($dependenciaExp, $digitosDependencia, "0", STR_PAD_LEFT);
                $numeroExpediente = $anoExp . $cDependencia . $trdExp . $consecutivoExp . $digCheck;

                foreach ($_POST as $elementos => $valor) {
                    if (strncmp($elementos, 'parExp_', 7) == 0) {
                        $indice = ( int )substr($elementos, 7);
                        $arrParametro[$indice] = $valor;
                    }
                }

                echo "<hr>".$sgd_mrd_codigo."<hr>";

                $expediente->idSerie = $idSerie;
                $expediente->idSubSerie = $idSubSerie;
                $numeroExpedienteE = $expediente->crearExpediente($numeroExpediente, $nurad, $dependencia, $codusuario, $usua_doc, $usuaDocExp, $codiSRD, $codiSBRD, 'false', $fechaExp, $_POST['codProc'], $arrParametro, $nivelExp);
                if ($numeroExpedienteE == 0) {
                    echo "<CENTER><table class=borde_tab><tr><td class=titulosError>EL EXPEDIENTE QUE INTENTO CREAR YA EXISTE.</td></tr></table>";
                } else {
                    $insercionExp = $expediente->insertar_expediente($numeroExpediente, $nurad, $dependencia, $codusuario, $usua_doc);
                }

                $codiTRDS = $codiTRD;
                $i++;
                $TRD = $codiTRD;
                $observa = "*TRD*" . $codserie . "/" . $codiSBRD . " (Creacion de Expediente.)";
                include_once "$ruta_raiz/include/tx/Historico.php";
                $radicados[] = $nurad;
                $tipoTx = 51;
                $Historico = new Historico($db);
                $Historico->insertarHistoricoExp($numeroExpediente, $radicados, $dependencia, $codusuario, $observa, $tipoTx, 0);
                include("$ruta_raiz/include/tx/Flujo.php");
                $codProc = $_POST['codProc'];
                if(!$codProc) $codProc=0;
                $objFlujo = new Flujo($db, $codProc, $usua_doc);
                $objFlujo->usuaDoc = $usua_doc;
                $expEstadoActual = $objFlujo->actualNodoExpediente($numeroExpediente);
                $arrayAristas = $objFlujo->aristasSiguiente($expEstadoActual);

                $aristaActual = $arrayAristas[0];
                $codProc = $_POST['codProc'];
                if(!$codProc) $codProc=0;
                $objFlujo->cambioNodoExpediente($numeroExpediente, $nurad, $expEstadoActual, $aristaActual, 1, "Creacion Expediente", $codProc);
            } ?>

                <div class="panel panel-default">
                    <div class="panel-heading">Creación del Expediente</div>
                    <?php if ($numeroExpedienteE != 0) { ?>
                    <div class="panel-body">
                        <p>
                            Se ha creado el Expediente No.
                            <?=$numeroExpedienteE?>
                            con la siguiente informaci&oacute;n:
                        </p>
                    </div>
                    <?php } ?>

                <table align="center" class="table">
                        <?php if ($numeroExpedienteE != 0) {
                        $arrTRDExp = $expediente->getTRDExp($numeroExpediente, $codserie, $tsub, $codProc);
                        $nivelSeguridad = $expediente->getSeguridad($numeroExpediente);
                        ?>
                        <tr>
                            <td>
                                <SMALL>Serie</SMALL>
                            </td>
                            <td>
                                <?php print $arrTRDExp['serie']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <SMALL>SubSerie</SMALL>
                            </td>
                            <td>
                                <?php print $arrTRDExp['subserie']; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <SMALL>Proceso</SMALL>
                            </td>
                            <td>
                                <?php print $arrTRDExp['proceso']; ?>
                            </td>
                        </tr>
                    <tr>
                            <td>
                                <SMALL>Nivel Seguridad</SMALL>
                            </td>
                            <td>
                                <?php echo $nivelSeguridad['label']; ?>
                            </td>
                        </tr>

                    <?php } ?>
                </table>
            </div>
            <?php if (!isset($Actualizar)) { ?>
                <div>
                            <table class="table table-bordered table-striped" style="width:850;" align=center>
                                    <tr>
                                            <td>
                                                    <small>Dependencia</small>
                                            </td>
                                            <td>
                                                    <small>
                                                            <label class=select>
            <?php
            
            if (!$dependenciaExp)
                $dependenciaExp = $dependencia;
            if ($perm_crea_exp_todasdependencias == 'no'){$_condicion_dependencia = " and d.depe_codi = $dependenciaExp"; };
            $queryDep = "select depe_nomb||' - '||depe_codi, d.depe_codi
                from dependencia d
                where
                d.depe_estado=1
                $_condicion_dependencia
                order by depe_nomb
            ";
            $rsD = $db->conn->Execute($queryDep);
            $comentarioDev = "Muestra las Series Docuementales";
            include "$ruta_raiz/include/tx/ComentarioTx.php";
            if (!$dependenciaExp)
                $dependenciaExp = $dependencia;
            print $rsD->GetMenu2("dependenciaExp", $dependenciaExp, "0:-- Seleccione --", false, "", "onChange='submit()' class='select'");
            ?><i></i>
                                                            </label></small>
                                            </td>
                                    </tr>
                                    <tr>
                                            <td>
                                                    <small>Serie</small>
                                            </td>
                                            <td>
                                                    <small>
                                                            <label class=select>
            <?php
            if (!$tdoc)
                $tdoc = 0;
            if (!$codserie)
                $codserie = 0;
            if (!$tsub)
                $tsub = 0;
            $fechah = date("dmy") . " " . time("h_m_s");
            $fecha_hoy = Date("Y-m-d");
            $sqlFechaHoy = $db->conn->DBDate($fecha_hoy);
            $check = 1;
            $fechaf = date("dmy") . "_" . time("hms");
            $num_car = 4;
            if (!$dependenciaExp)
                $dependenciaExp = $dependencia;
            $nomb_varc = "s.sgd_srd_codigo";
            $nomb_varde = "s.sgd_srd_descrip";
            include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
            if (!$depDireccion)
                $depDireccion = $dependencia;
            if (!$codserie)
                $codserie = $R_codigo_serie;
            $querySerie = "select distinct ($sqlConcat) as detalle, s.sgd_srd_codigo, s.sgd_srd_descrip
                from sgd_mrd_matrird m, sgd_srd_seriesrd s
                where
                (cast(m.depe_codi as varchar(" . $digitosDependencia . ")) = '$dependenciaExp' or cast(m.depe_codi_aplica as varchar(" . $digitosDependencia . ")) like '%$dependenciaExp%' or cast(m.depe_codi as varchar(" . $digitosDependencia . "))='$depDireccion')
                and s.id = m.sgd_srd_id
                and s.sgd_srd_estado  = '1' 
                and " . $db->sysdate() . " between s.sgd_srd_fechini and s.sgd_srd_fechfin
             
                order by  s.sgd_srd_descrip, s.sgd_srd_codigo
            ";
          //  $db->conn->debug = true;
            $rsD = $db->conn->Execute($querySerie);
            $comentarioDev = "Muestra las Series Docuementales";
            include "$ruta_raiz/include/tx/ComentarioTx.php";
            print $rsD->GetMenu2("codserie", $codserie, "0:-- Seleccione --", false, "", "onChange='submit()' class='select'");
            ?><i></i>
                                        </label></small>
                            </td>
                    </tr>
                    <tr>
                        <td>
                        <small>SubSerie</small>
                        </td>
                    <td>
                    <small><label class=select> <?
            $nomb_varc = "su.sgd_sbrd_codigo";
            $nomb_varde = "su.sgd_sbrd_descrip";
            include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
            $querySub = "select distinct ($sqlConcat) as detalle, su.sgd_sbrd_codigo
                from sgd_mrd_matrird m, sgd_sbrd_subserierd su
                where (cast(m.depe_codi as varchar(10)) = '$coddepe' or cast(m.depe_codi_aplica as varchar(100)) like '%$coddepe%' or cast(m.depe_codi as varchar(10))='$depDireccion')
                and m.sgd_srd_codigo = '$codserie'
                and su.sgd_srd_codigo = '$codserie'
                and su.id = m.sgd_sbrd_id
                and cast(m.sgd_mrd_esta as numeric(1))       = 1
                and su.sgd_srd_id = m.sgd_srd_id
                and " . $db->sysdate() . " between su.sgd_sbrd_fechini and su.sgd_sbrd_fechfin
                order by detalle
            ";
         
            $rsSub = $db->conn->Execute($querySub);

            $codiMrd = $rsSub->fields["SGD_MRD_CODIGO"];

            $querySub = "select distinct ($sqlConcat) as detalle, su.sgd_sbrd_codigo, sgd_mrd_codigo
                from sgd_mrd_matrird m, sgd_sbrd_subserierd su
                where (cast(m.depe_codi as varchar(10)) = '$coddepe' or cast(m.depe_codi_aplica as varchar(100)) like '%$coddepe%' or cast(m.depe_codi as varchar(10))='$depDireccion')
                and m.sgd_srd_codigo = '$codserie'
                and su.sgd_srd_codigo = '$codserie'
                and su.sgd_sbrd_codigo = m.sgd_sbrd_codigo
                and " . $db->sysdate() . " between su.sgd_sbrd_fechini and su.sgd_sbrd_fechfin
                order by detalle
            ";
            // Consulta solo para traer la MRD
            $rsSubTMP = $db->conn->Execute($querySub);
            $codiMrd = $rsSubTMP->fields["SGD_MRD_CODIGO"];


            include "$ruta_raiz/include/tx/ComentarioTx.php";
            if (!$tsub)
                $tsub = $R_codigo_subserie;
            print $rsSub->GetMenu2("tsub", $tsub, "0:-- Seleccione --", false, "", "onChange='submit()' class='select'");
            if (!$codiSRD) {
                $codiSRD = $codserie;
                $codiSBRD = $tsub;
            }

            $queryPEXP = "select SGD_PEXP_DESCRIP,SGD_PEXP_CODIGO FROM
                SGD_PEXP_PROCEXPEDIENTES
                WHERE
                SGD_SRD_CODIGO=$codiSRD
                AND SGD_SBRD_CODIGO=$codiSBRD ";
           
            $rs = $db->conn->Execute($queryPEXP);
            $texp = $rs->fields["SGD_PEXP_CODIGO"];

            ?><i></i>
                    </label>
                    </small>
                        </td>
                            </tr>
                    <tr>
                        <td><small>Seguridad</small></td>
                        <td><small><label class="select">
                        <select class="select" name="nivelExp" onchange="submit()">
            <?
            if($nivelExp==0)  $datoss = " selected "; else $datoss = "";
            ?>
            <option value=0 <?=$datoss?>>P&uacute;blico</option>
            <?
            if($nivelExp==1)  $datoss = " selected "; else $datoss = "";
            ?>
            <option value=1 <?=$datoss?>>Privado dependencia (Solo usuarios área y usuarios jefes )</option>
            <?
            if($nivelExp==2)  $datoss = " selected "; else $datoss = "";
            ?>
            <option value=2 <?=$datoss?>>Privado (Solo Usuario Responsable Y Jefe) </option>
            </select><i></i>
            </label></small>
            </td>
                    </tr>

            <?php
            if(!$rs->EOF){
            ?>
                                <tr>
                                    <td>
                                            <small>Proceso</small>
                                    </td>
                                    <td colspan="2">
                                            <label class=select>
            <?
                $comentarioDev = "Muestra los procesos segun la combinacion Serie-Subserie";
                include "$ruta_raiz/include/tx/ComentarioTx.php";

                print $rs->GetMenu2("codProc", $codProc, "0:-- Seleccione --", false, "", "onChange='submit()' class='select'");

                $rs->MoveFirst();
                while (!$rs->EOF) {
                    $arrProceso[$rs->fields["SGD_PEXP_CODIGO"]] = $rs->fields["SGD_PEXP_DESCRIP"];
                    $rs->MoveNext();
                }

                // Si se selecciono Serie-Subserie-Proceso
                if ($codProc && $codProc != "" && $codProc != 0 && $codserie != "" && $codserie != 0 && $tsub != "" && $tsub != 0) {
                    // Termino del proceso seleccionado
                    if(!$codProc) $codProc="0";
                    $queryPEXP = "select SGD_PEXP_TERMINOS";
                    $queryPEXP .= " FROM SGD_PEXP_PROCEXPEDIENTES";
                    $queryPEXP .= " WHERE SGD_PEXP_CODIGO  = " . $codProc;

                    $rs = $db->conn->Execute($queryPEXP);

                    $expTerminos = $rs->fields["SGD_PEXP_TERMINOS"];
                    if ($expTerminos != "") {
                        $expDesc = "<small> $expTerminos Dias Calendario de Termino Total </small>";
                    }
                }
                if ($expDesc)
                    print "<small>&nbsp;" . $expDesc . "</small>";
            ?>
                                                <i></i>
                                            </label>
                                    </td>
                                </tr>
            <?php
            }
            ?>
                        </table>
                    </div>
                        <div>
                                <table class="table table-bordered table-striped" style="width:850;" align=center>
                                        <tr align="center">
                                                <td width="5" colspan=4>
            <?
            if (!$digCheck) {
                $digCheck = "E";
            }
            $expediente = new Expediente($db);
            if (!$expManual) {
                if (!$anoExp)
                    $anoExp = date("Y");
                $secExp = $expediente->secExpediente($dependencia, $codiSRD, $codiSBRD, $anoExp);
            } else {
                $secExp = $consecutivoExp;
            }
            $trdExp = substr("00" . $codiSRD, -2) . substr("00" . $codiSBRD, -2);
            
            $consecutivoExp = str_pad($secExp, 5, "0", STR_PAD_LEFT);
            if (!$anoExp)
                $anoExp = date("Y");
            ?>
                                                        <section class="col col-3">
                                                                <label class=select>
                                                                        <select name=anoExp class=select onChange="submit();">
            <?
            if ($anoExp == (date('Y')))
                $datoss = " selected "; else $datoss = "";
            ?>
                                                                                <option value='<?= (date('Y')) ?>' <?= $datoss ?>>
                                                                                        <?= date('Y') ?>
                                                                                </option>
            <?
            if ($anoExp == (date('Y') - 1))
                $datoss = " selected "; else $datoss = "";
            ?>
                                                                                <option value='<?= (date('Y') - 1) ?>' <?= $datoss ?>>
                                                                                        <?= (date('Y') - 1) ?>
                                                                                </option>
            <?
            if ($anoExp == (date('Y') - 2))
                $datoss = " selected "; else $datoss = "";
            ?>
                                                                                <option value='<?= (date('Y') - 2) ?>' <?= $datoss ?>>
                                                                                        <?= (date('Y') - 2) ?>
                                                                                </option>
            <?
            if ($anoExp == (date('Y') - 3))
                $datoss = " selected "; else $datoss = "";
            ?>
                                                                                <option value='<?= (date('Y') - 3) ?>' <?= $datoss ?>>
                                                                                        <?= (date('Y') - 3) ?>
            <?
            if ($anoExp == (date('Y') - 4))
                $datoss = " selected "; else $datoss = "";
            ?>
                                                                                <option value='<?= (date('Y') - 4) ?>' <?= $datoss ?>>
                                                                                        <?= (date('Y') - 4) ?>
                                                                                </option>
                                                                        </select>
                                                                        <i></i>
                                                                </label>
                                                        </section>
                                                        <section class="col col-2"><label class=input><input type=text name=depExp value='<?= $dependenciaExp ?>' class=select maxlength="<?=$_SESSION['digitosDependencia']?>" size="2"> </label></section>
                                                        <section class="col col-2"><label class=input><input type=text name=depExp value='<?= $trdExp ?>' class=select maxlength="4" size="5"> </label></section>
                                                        <section class="col col-2"><label class=input><input type=text name=consecutivoExp value='<?= $consecutivoExp ?>'	class=select maxlength="5" size=4> </label></section>
                                                        <section class="col col-1"><label class=input><input type=text name=digCheckExp value='<?= $digCheck ?>' class=select maxlength="1" size="1"> </label></section>
                                                </td>
                                        </tr>
                            <tr>
                                    <td colspan=4 align=center>
            <?
            $cDependencia = str_pad($dependencia, $digitosDependencia, "0", STR_PAD_LEFT);
            $numeroExpediente = $anoExp . $dependenciaExp . $trdExp . $consecutivoExp . $digCheck;
            ?>
                                            </center>
                                            <small>
                                                    <b><?= $numeroExpediente ?></b> ( A&ntilde;o-Dependencia-Serie
                                                    Subserie-Consecutivo-E)<br>
                                                    El consecutivo "<?= $consecutivoExp ?>" es temporal y puede cambiar en el momento
                                                    de crear el expediente.<br>
                                                    Consecutivo de Expediente Manual
                                            </SMALL>

            <?
            if ($expManual)
                $datoss = " checked "; else $datoss = "";
            ?>
                                            <input type=checkbox name=expManual <?= $datoss ?>  >&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                            &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                            <input type="button" name="Button" value="Buscar Etiqueta"
                                                            class="btn btn-primary btn-xs"
                                                            onClick="Start('buscarParametro.php?busq_salida=<?= $busq_salida ?>&krd=<?= $krd ?>',1124,620);">
                                            <button type="button" class="btn btn-success btn-xs" id ="OcultarMostrarCampos" >Mostrar/Ocultar</button>
                                        </td>
                                        </tr><?php
            //  -- And D.Dep_Direccion In (Select Dep_Direccion From Dependencia Where Depe_Codi=$dependencia)
            //	$rsUs = $db->conn->Execute($queryUs); //var_dump($rsUS);
            //	print $rsUs->GetMenu2("usuaDocExp", "$usuaDocExp", "0:-- Seleccione --", false, "", " class='select' onChange='submit()'");
            ?>
                                            </label>
                                            <i></i>
                                    </td>
            <?php
            $sqlParExp = "SELECT SGD_PAREXP_ETIQUETA, SGD_PAREXP_ORDEN,";
            $sqlParExp .= " SGD_PAREXP_EDITABLE";
            $sqlParExp .= " FROM SGD_PAREXP_PARAMEXPEDIENTE PE";
            $sqlParExp .= " WHERE PE.DEPE_CODI = " . $dependenciaExp;
            //$sqlParExp .= " WHERE PE.DEPE_CODI = 900";
            $sqlParExp .= " ORDER BY SGD_PAREXP_ORDEN ASC";
//            $db->conn->debug=true;
            $rsParExp = $db->conn->Execute($sqlParExp);
            $auxiliar_formulario = 0;
            while (!$rsParExp->EOF) { ?>
                                <tr align="center" <?php if($auxiliar_formulario>=5){echo 'class = "my_toogle" style = "display:none" ';} ?> >
                                        <td align="left" colspan=1>
                                                <SMALL>
            <?php

                $valorTxt = "";
            print $rsParExp->fields['SGD_PAREXP_ETIQUETA'];

            if ($rsParExp->fields['SGD_PAREXP_EDITABLE'] == 1) {
                $readonly = "";
            } else {
                $readonly = "readonly";
            }

            if ($rsParExp->fields['SGD_PAREXP_ETIQUETA'] == "NIT") {
                $valorTxt = $_SESSION['DOC_US3'];
            }

            if ($rsParExp->fields['SGD_PAREXP_ETIQUETA'] == "SIGLA") {
                $valorTxt = $_SESSION['SIGLA_US3'];
                if (!$valorTxt)
                    $valorTxt = $_SESSION['NOMBRE_US3'];
                $valorTxt = trim(substr($valorTxt, 0, 120));
            }
            ?>

                                                </SMALL>
                                    </td>
                                        <td align="left" colspan=2>
            <?
            $parExpOrden = $rsParExp->fields['SGD_PAREXP_ORDEN'];
            $nombreInput = "parExp_" . $rsParExp->fields['SGD_PAREXP_ORDEN'];
            if ($_GET[$nombreInput])
                $valorTxt = $_GET[$nombreInput];
            if ($_POST[$nombreInput])
                $valorTxt = $_POST[$nombreInput];
            $valorTxt = strtoupper(trim($valorTxt));
            if ($parExpOrden == 4) { ?>
                                                        <textarea name="<?= $nombreInput ?>" rows="2"
                                                                            cols="60" <?php print $readonly; ?>><?= $valorTxt ?></textarea>
            <? } else {
            ?>
                                                        <input type="text" name="<?= $nombreInput ?>" value="<?= $valorTxt ?>"
                                                                        size="60" <?php print $readonly; ?>>
                                                <? } ?>
                                        </td>
                                </tr>
            <?php $rsParExp->MoveNext();
            $auxiliar_formulario = $auxiliar_formulario +1;
            } ?>
                        <tr>
                                <td>
                                        <SMALL>Fecha de Inicio</SMALL>
                                        <br>

                <script language="javascript">
                <?  if(!$fechaExp) $fechaExp = date("d/m/Y"); ?>
                var dateAvailable1 = new ctlSpiffyCalendarBox("dateAvailable1", "TipoDocu", "fechaExp", "btnDate1", "<?=$fechaExp?>", scBTNMODE_CUSTOMBLUE);
            </script>
                <script language="javascript">
                dateAvailable1.date = "<?=date('Y-m-d');?>";
            dateAvailable1.writeControl();
            dateAvailable1.dateFormat = "dd-MM-yyyy";
            </script>
                                </td>
                                <td>
                                        <small>Responsable</small>
                                </td>
                                <td>
                                                        <label class=select>
            <?
                $queryUs = "select d.depe_codi || '-' ||  u.usua_nomb, u.usua_doc, d.depe_codi, d.dep_direccion
                From Usuario U, Dependencia D
                Where U.Usua_Esta='1'
                And U.Depe_Codi=D.Depe_Codi
                and d.Depe_Codi=$dependenciaExp
                order by d.depe_codi, u.usua_nomb";

            if(!$usuaDocExp){
                $Uquery = "select usua_doc from usuario where usua_login = '$krd'";
                $Urs = $db->conn->Execute($Uquery);
                $U_usua_doc = $Urs->fields["USUA_DOC"];
                $usuaDocExp = $U_usua_doc;}
                //  -- And D.Dep_Direccion In (Select Dep_Direccion From Dependencia Where Depe_Codi=$dependencia)
                $rsUs = $db->conn->Execute($queryUs); //var_dump($rsUS);
            print $rsUs->GetMenu2("usuaDocExp", "$usuaDocExp", "0:-- Seleccione --", false, "", " class='select' onChange='submit()'");
            ?>
                                            </label>
                                            <i></i>
                                    </td>
                                    </tr>
                                </table>
                        </div>
                    <? if ($crearExpediente) { ?>
                    <div id='confirmacionExp' style="position:absolute;left:10px; top:20px;height=1400;width=400">
                        <table class="table table-striped smart-form dataTable " height="500">
                            <tr align="center">
                                <td align="center" height=800>
                                    <center class="alert alert-info no-margin fade in">
                                        ESTA SEGURO DE CREAR EL EXPEDIENTE ? <BR>
                                        EL EXPEDIENTE QUE VA HA CREAR ES EL :
                                    </center>
                                    <b>
                                        <center class="style1"><?= $numeroExpediente ?></center>
                                    </b>
            <?php
            if($nivelExp==0)  $datoss = "Público";
            if($nivelExp==1)  $datoss = "Privado dependencia (Solo usuarios área y usuarios jefes )";
            if($nivelExp==2)  $datoss = "Privado (Solo Usuario Responsable Y Jefe)";
            ?>
                        <center >Con el nivel de seguridad <b><?=$datoss;?></b></center>
                                    <div align="justify"><br>
                                        <small><b>Recuerde:</b>No podr&aacute; modificar el numero de expediente si hay un error en
                                            el expediente, mas adelante tendr&aacute; que excluir este radicado del expediente y si
                                            es el caso solicitar la anulaci&oacute;n del mismo. Ademas debe tener en cuenta que
                                            apenas coloca un nombre de expediente, en Archivo crean una carpeta f&iacute;sica en el
                                            cual empezaran a incluir los documentos pertenecientes al mismo.
                                        </small>
                                    </div>
                                    <footer>
                                        <input name="Actualizar" type=submit class="btn btn-primary btn-xs"
                                               value=" Confirmacion Creacion de Expediente ">
                                        <input name="Actualizar" type=button class="btn btn-success btn-xs"
                                               value=" Cancelar Creaci&oacute;n " onclick="ocultarConfirmaExp();">
                                    </footer>
                                </td>
                        </table>

                    </div>
                <? } } ?>
            <div>
                <table class="table table-bordered">
                    <tr align="center">
                        <td align="center">
                            <center>
            <?php

            if ($tsub and $codserie && !$Actualizar and $usuaDocExp) {
                #echo "---------------------------";
                if (!$crearExpediente) {
                    //if (is_array($arrProceso) && $codProc == 0) {
                    //  echo "<input name=\"crearExpediente\" type=\"button\" class=\"btn btn-primary btn-xs\" value=\" Crear Expediente \" onClick=\"alert('Por favor seleccione un proceso.'); document.TipoDocu.codProc.focus();\">";
                    //} else {
                    echo "<input name=\"crearExpediente\" type=submit class=\"btn btn-primary btn-xs\" value=\" Crear Expediente \">";
                    //}
                } else {
                    // echo "<input name=\"Actualizar\" type=submit class=\"btn btn-primary btn-xs\" value=\" Confirmacion Creacion de Expediente \">";
                }
            }
            ?>
                            </center>
                        </td>
                    </tr>
                    <tr>
                        <td width="750">
                            <center>
                                <input name="cerrar" type="button" class="btn btn-primary btn-xs" id="envia22"
                                       onClick="window.opener.$.fn.cargarPagina('expediente/lista_expedientes.php','tabs-a'); window.close();"
                                       value=" Cerrar ">
                            </center>
                        </td>
                    </tr>
                </table>
            </div>
            </div>
            </div>
            </div>
            </article>
            </div>
            </section>
            </div>
            <input type=hidden name=codiMrd value='<?=$codiMrd?>' />
            </form>

            <script>

            function borrarArchivo(anexo, linkarch) {
                if (confirm('Esta seguro de borrar este Registro ?')) {
                    nombreventana = "ventanaBorrarR1";
                    url = "tipificar_documentos_transacciones.php?borrar=1&usua=<?=$krd?>&codusua=<?=$codusua?>&coddepe=<?=$coddepe?>&nurad=<?=$nurad?>&codiTRDEli=" + anexo + "&linkarchivo=" + linkarch;
                    window.open(url, nombreventana, 'height=250,width=300');
                }
                return;
            }

            function procModificar() {
                if (document.TipoDocu.tdoc.value != 0 && document.TipoDocu.codserie.value != 0 && document.TipoDocu.tsub.value != 0) {
            <?php
            $sql = "SELECT RADI_NUME_RADI
                FROM SGD_RDF_RETDOCF
                WHERE RADI_NUME_RADI = '$nurad'
                AND  DEPE_CODI =  '$coddepe'";
            $rs=$db->conn->Execute($sql);
            $radiNumero = $rs->fields["RADI_NUME_RADI"];
            if ($radiNumero !='') {
            ?>
                if (confirm('Esta Seguro de Modificar el Registro de su Dependencia ?')) {
                    nombreventana = "ventanaModiR1";
                    url = "tipificar_documentos_transacciones.php?modificar=1&usua=<?=$krd?>&codusua=<?=$codusua?>&tdoc=<?=$tdoc?>&tsub=<?=$tsub?>&codserie=<?=$codserie?>&coddepe=<?=$coddepe?>&nurad=<?=$nurad?>";
                    window.open(url, nombreventana, 'height=200,width=300');
            }
            <?php }else { ?>
            alert("No existe Registro para Modificar ");
            <?php } ?>
                } else {
                    alert("Campos obligatorios ");
                }
                return;
            }
            function ocultarConfirmaExp() {
                // $("#confirmacionExp").addClass('hide');
                $("#confirmacionExp").hide();
            }

            </script>
            <?= $mensaje_err ?>
            <!-- Ocultamos los campos inecesarios por el momento -->
                <script>
                $( "#OcultarMostrarCampos" ).click(function() {
                    $( ".my_toogle" ).toggle( "slow", function() { });
                });
            </script>
        </div>
    </div>
</body>
</html>
