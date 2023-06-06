<?php
/**
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @author Jairo Losada   <jlosada@gmail.com>
 * @author Correlibre.org
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright
 *
 * OrfeoGpl Models are the data definition of OrfeoGpl Information System
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
define('SALIDA', 1);
define('ENTRADA', 2);
define('MEMORANDO', 3);
define('CIRC_INTERNA', 4);
define('CIRC_EXTERNA', 5);
define('RESOLUCION', 6);
define('AUTO', 7);
define('SIIM2_RECEPCION', 10);

//VARIABLE INCREMENTAL PARA CONTROLAR LOS CAMPOS DE LOS USUARIOS
unset($_SESSION['INCREMENTAL1']);
$_SESSION['INCREMENTAL1'] = 0;

$ruta_raiz = "..";
if (!$_SESSION['dependencia']) {
    header("Location: $ruta_raiz/cerrar_session.php");
}

foreach ($_GET as $key => $valor) {
    ${$key} = $valor;
}
foreach ($_POST as $key => $valor) {
    ${$key} = $valor;
}

/**  Fin variables de session de Radicacion de Mail. **/
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once("$ruta_raiz/include/tx/usuario.php");
include_once("$ruta_raiz/include/tx/notificacion.php");
include_once("$ruta_raiz/processConfig.php");

$db = new ConnectionHandler("$ruta_raiz");

$usuario = new Usuario($db);

$showtable = 'hide';
$hidetable = '';
$showEntrada = '';
$modificar = 'hide';

if ($Submit3 == "ModificarDocumentos") {
    $hidetable = 'hide';
    $modificar = '';
}

$radMail = $_GET["radMail"];
$ddate = date('d');
$mdate = date('m');
$adate = date('Y');
$nurad = trim($nurad);
$hora = date('H:i:s');
$fechaf = $date . $mdate . $adate . $hora;
$dependencia = $_SESSION["dependencia"];
$ADODB_COUNTRECS = true;
$fecha_gen_doc = date("d-m-Y");
$coddepe = $dependencia;
$codusua = $_SESSION["codusuario"];
//valor necesario para crear enlaces de los distintos elementos
//como el sticker
$idsession = session_id(); //valor necesario para crear enlaces

$_TIPO_INFORMADO = 1;
$_enable_1 = false;
$_enable_2 = true;
$_name_2 = "Entidad";
$_name_6 = "Funcionario";
$_name_4 = "Destinatarios Circular Interna";
$_name_5 = "Destinatarios Circular Externa";
$_show_type_doc = true;
if (!$ent && $nurad) {
    $ent = substr($nurad, -1);
}
//Mostrar el tipo de radicacion que se esta realizando
$selTipoRad = "select
                  sgd_trad_codigo,
                  sgd_trad_descr,
                  sgd_trad_icono,
                  sgd_trad_genradsal
                from
                  sgd_trad_tiporad
                where sgd_trad_codigo = $ent";

$rs = $db->conn->query($selTipoRad);

if (!$rs->EOF) {
    $nomEntidad = $rs->fields["SGD_TRAD_DESCR"];
}

$med = null;

$styleFirmador = "";
if ($ent == ENTRADA || $ent == MEMORANDO || $ent == SALIDA) {
    $styleFirmador = "display:none";
}

if ($ent == MEMORANDO) {
    $usuario_selected = 'selected';
    $med = SIIM2_RECEPCION;
} else {
    $ciudadano_selected = 'selected';
}

if ($ent == CIRC_INTERNA || $ent == CIRC_EXTERNA ||
    $ent == RESOLUCION || $ent == AUTO) {
    $esNotificacion = true;
    $notificacion = new Notificacion($db);
    if ($ent == CIRC_INTERNA) {
        $circ_int_selected = 'selected';
    }
    if ($ent == CIRC_EXTERNA) {
        $circ_ext_selected = 'selected';
    }
} else {
    $esNotificacion = false;
}

if ($ent == CIRC_INTERNA || $ent == CIRC_EXTERNA) {
    $esNotificacionCircular = true;
} else {
    $esNotificacionCircular = false;
}

if ($rad0) {
    $javascriptCapDatos = 'datorad=0';
} elseif ($rad1) {
    $javascriptCapDatos = 'datorad=1';
} elseif ($rad2) {
    $javascriptCapDatos = 'datorad=2';
}

//CARGAR INFORMACION SI SE TRAE DE UN ANEXO O COPIA DE DATOS
if ($radicadopadre) {

    $query = "SELECT
          a.*
          FROM
          RADICADO A
          WHERE
          A.RADI_NUME_RADI = $radicadopadre";

    $rs = $db->conn->query($query);

    if (!$rs->EOF) {
        $asu = $rs->fields["RA_ASUN"];
        $ane = $rs->fields["RADI_DESC_ANEX"];
        $cuentai = $rs->fields["RADI_CUENTAI"];
        $tdoc = $rs->fields["TDOC_CODI"];
        $med = $rs->fields["MREC_CODI"];
        $coddepe = $rs->fields["RADI_DEPE_ACTU"];
        $codusuarioActu = $rs->fields["RADI_USUA_RADI"];
        $radi_fecha = $rs->fields["RADI_FECH_RADI"];
        $guia = $rs->fields["RADI_NUME_GUIA"];
        $empTrans = $rs->fields["EMP_TRANSPORTADORA"];
        $radi_dato_001 = $rs->fields["RADI_DATO_001"]; //Campo de uso general
        $radi_dato_002 = $rs->fields["RADI_DATO_002"]; //Campo de uso general
    }

    if (!$esNotificacionCircular) {
        //Filtro por el tipo de usuario
        $result = $usuario->usuarioPorRadicado($radicadopadre, $esNotificacion);

        if ($result) {
            $showUsers = $usuario->resRadicadoHtml(true);
            $showtable = '';
        }
    }

    //Informacion sobre Notificaciones
    if ($esNotificacion) {
        $infoNotificacion = $notificacion->cargarNotificacionAntigua($radicadopadre);
        $notifica_codi = ""; // Es un nuevo radicado
        $medio_pub = $infoNotificacion["med_public"];
        $caracter_adtvo = $infoNotificacion["caracter_adtvo"];
        $siad_preestablecido = $infoNotificacion["siad"];
        $prioridad_prestablecido = $infoNotificacion["prioridad"] === "t" ? 1 : 0;

        if ($esNotificacionCircular) {
            $result = $notificacion->destinatariosPorRadicado($radicadopadre);

            if ($result) {
                $showUsers = $notificacion->agregarDestinatarios($result, true);
                $showtable = '';
            }
        }
    }
}

//CARGAR INFORMACION SI SE ENVIA NUMERO DE RADICADO PARA MODIFICAR
if ($nurad) {

    $query = "SELECT
          a.*
          FROM
          RADICADO A
          WHERE
          A.RADI_NUME_RADI = $nurad";

    $rs = $db->conn->query($query);

    if (!$rs->EOF) {
        $asu            = $rs->fields["RA_ASUN"];
        $radicadopadre  = $rs->fields["RADI_NUME_DERI"];
        $ane            = $rs->fields["RADI_DESC_ANEX"];
        $cuentai        = $rs->fields["RADI_CUENTAI"];
        $tdoc           = $rs->fields["TDOC_CODI"];
        $med            = $rs->fields["MREC_CODI"];
        $coddepe        = $rs->fields["RADI_DEPE_ACTU"];
        $codusuarioActu = $rs->fields["RADI_USUA_RADI"];
        $radi_fecha     = $rs->fields["RADI_FECH_RADI"];
        $fecha_gen_doc  = $rs->fields["RADI_FECH_OFIC"];
        $guia           = $rs->fields["RADI_NUME_GUIA"];
        $empTrans       = $rs->fields["EMP_TRANSPORTADORA"];
        $numFolio       = $rs->fields["RADI_NUME_FOLIO"];
        $numAnexo       = $rs->fields["RADI_NUME_ANEXO"];
        $esta_fisico    = $rs->fields["ESTA_FISICO"];
        $radi_dato_001  = $rs->fields["RADI_DATO_001"]; //Campo de uso general
        $radi_dato_002  = $rs->fields["RADI_DATO_002"]; //Campo de uso general
        $firmador       = $rs->fields["RADI_USUA_FIRMA"] . "-" . $rs->fields["RADI_DEPE_FIRMA"];
    }

    $date1 = date_create($radi_fecha);

    list($adate, $mdate, $ddate) = explode('-', date_format($date1, 'Y-m-d'));

    if ($fecha_gen_doc) {
        list($adate1, $mdate1, $ddate1) = explode('-', substr($fecha_gen_doc, 0, 10));
        $fecha_gen_doc = "$ddate1-$mdate1-$adate1";
    }

    $ent = substr($nurad, -1);

    if (!$esNotificacionCircular) {
        //Filtro por el tipo de usuario
        $result = $usuario->usuarioPorRadicado($nurad, $esNotificacion);

        if ($result) {
            $showUsers = $usuario->resRadicadoHtml();
            $hidetable = 'hide';
            $modificar = '';
            $showtable = '';
        }
    }

    $varEnvio = session_name() . "=" . session_id() . "&nurad=$nurad&ent=$ent";
    $senddata = "<input name='nurad' value='$nurad' type=hidden>";
    $senddata .= "<input name='idCodigo' value='$nurad' type=hidden>";

    //Informacion sobre Notificaciones
    if ($esNotificacion) {
        $infoNotificacion = $notificacion->cargarNotificacionAntigua($nurad);
        $notifica_codi = $infoNotificacion["notifica_codi"];
        $medio_pub = $infoNotificacion["med_public"];
        $caracter_adtvo = $infoNotificacion["caracter_adtvo"];
        $siad_preestablecido = $infoNotificacion["siad"];
        $prioridad_prestablecido = $infoNotificacion["prioridad"] === "t" ? 1 : 0;

        if ($esNotificacionCircular) {
            $result = $notificacion->destinatariosPorRadicado($nurad);

            if ($result) {
                $showUsers = $notificacion->agregarDestinatarios($result);
                $hidetable = 'hide';
                $modificar = '';
                $showtable = '';
            }
        }
    }
}


$query = "SELECT ".
            $db->conn->Concat("d.DEPE_CODI", "'-'", "d.DEPE_NOMB").", d.DEPE_CODI
        FROM
          DEPENDENCIA d
        join DEPENDENCIA_VISIBILIDAD dv on (
          d.depe_codi = dv.dependencia_visible 
          and dv.dependencia_observa = $dependencia)
        where
          d.depe_estado = 1
        ORDER BY d.DEPE_CODI, d.DEPE_NOMB";
$rs = $db->conn->query($query);

if ($_TIPO_INFORMADO != 2) {
    $depselect = $rs->GetMenu2("coddepe", $nurad || $radicadopadre || $esNotificacion || in_array($ent, [MEMORANDO, SALIDA]) ? $coddepe : false, "0:-- Seleccione una Dependencia --", false, false, "class='form-control'  title='seleccione una dependencia'");
} elseif ($_TIPO_INFORMADO == 2) {
    $depselect = $rs->GetMenu2("coddepe", false, false, false, "class='form-control'");
}

$sqlquery = "SELECT ".
                $db->conn->Concat("d.ID", "'-'", "d.NOMBRE").", d.ID
            FROM
              SGD_EMPRESA_TRANSPORTADORA d
            ORDER BY d.NOMBRE";

$rs = $db->conn->query($sqlquery);

$transpSelect = $rs->GetMenu2("empTrans", $empTrans, false, false, '', " class='form-control'");

$queryData = "SELECT " .
    $db->conn->Concat("d.DEPE_CODI", "'-'", "d.DEPE_NOMB") . ", d.DEPE_CODI
                FROM
                DEPENDENCIA d
		where depe_estado = 1 order by 1 asc";

$rs = $db->conn->query($queryData);

if ($_TIPO_INFORMADO == 1) {
    $depselectInf = $rs->GetMenu2(
        "coddepe_informados",
        $coddepe,
        "0:-- Seleccione una Dependencia --",
        false,
        false,
        "class='form-control custom-scroll' id='informar'"
    );
} elseif ($_TIPO_INFORMADO == 2) {
    $depselectInf = $rs->GetMenu2(
        "coddepe",
        $coddepe,
        "",
        false,
        false,
        "class='form-control custom-scroll' multiple='multiple' id='informar' style='height: 15%;' "
    );
}

if($ent == MEMORANDO) {
    $query = "SELECT
                    MREC_DESC, MREC_CODI
                FROM MEDIO_RECEPCION
                WHERE MREC_CODI = 4
                ORDER BY MREC_CODI";
} else {
    $query = "SELECT
                MREC_DESC, MREC_CODI
    FROM MEDIO_RECEPCION
    WHERE MREC_CODI NOT IN (0,3)
    ORDER BY MREC_CODI";
}

$rs = $db->conn->query($query);
if ($tipoMedio == "eMail") {
    $med = 4;
}

$medioRec = $rs->GetMenu2(
    "med",
    $med,
    '',
    false,
    "",
    "required class='form-control'  title='seleccione un medio recepción/envio'"
);

$query = "SELECT
                  SGD_TPR_DESCRIP
                  ,SGD_TPR_CODIGO
                FROM
                  SGD_TPR_TPDCUMENTO
                WHERE
                  SGD_TPR_TP$ent     ='1'
                  and SGD_TPR_RADICA ='1'
                  ORDER BY SGD_TPR_DESCRIP ";

$opcMenu = "0:-- Seleccione un tipo --";
$fechaHoy = date("Y-m-d");
$fechaHoy = $fechaHoy . "";
$rs = $db->conn->query($query);
$tipoDoc = $rs->GetMenu2(
    "tdoc",
    $tdoc,
    "$opcMenu",
    false,
    "",
    "title='Seleccione el tipo documental' class='form-control'"
);

if ($esNotificacion) {
    $camposFormulario = $notificacion->cargarCamposFormulario($ent, $medio_pub, $caracter_adtvo);
    $tdoc           = !empty($camposFormulario["tdoc"]) ? $camposFormulario["tdoc"] : 'null';
    $medioPub       = $camposFormulario["medioPub"];
    $caracterAdtvo  = $camposFormulario["caracterAdtvo"];
}

$showEntrada = "
      <section class='col col-2'>
        <label class='label'>
          Referencia
        </label>
        <label class='input'>
          <input  id='cuentai' title='Coloque aquí el  número de referencia de la comunicación'
            name='cuentai' type='text'  maxlength='20' value='{$cuentai}' >
        </label>
      </section>";

if ($ent == 2) {
    $showEntrada = "
          <section class='col col-2'>
            <label class='label'>
              Referencia
            </label>
            <label class='input'>
              <input id='cuentai' title='Coloque aquí el  número de referencia de la comunicación'
                name='cuentai' type='text'  maxlength='20' value='{$cuentai}' >
            </label>
          </section>

          <section class='col col-2'>
              <label class='label'>
                Gu&iacute;a
              </label>
              <label class='input'>
                 <input type=text name='guia' title='Si tiene un número de guía digítelo.'
                 id='guia' value='{$guia}' size=35>
              </label>
          </section>

          <section class='col col-2'>
            <div class='form-group'>
              <label class='label'>
                Transportadora
              </label>
              {$transpSelect}
            </div>
          </section>";

    $showEntrada1 = "<label>Usuario quien radica</label>";
} else {
    $showEntrada = "
        <section class='col col-2'>
            <label class='label'>
            Referencia
            </label>
            <label class='input'>
            <input id='cuentai' title='Coloque aquí el  número de referencia de la comunicación'
                name='cuentai' type='text'  maxlength='20' value='{$cuentai}' >
            </label>
        </section>
    ";
}
?>

<html>
<head>
    <?php include_once("$ruta_raiz/htmlheader.inc.php") ?>
    <link rel="stylesheet" href="../tooltips/jquery-ui.css">
    <!-- Al colocar esto hace confilcto <script src="../tooltips/jquery-1.10.2.js"></script>-->
    <script src="../tooltips/jquery-ui.js"></script>
    <link rel="stylesheet" href="../tooltips/tool.css">
    <script src="../tooltips/tool.js"></script>
    <script src="../tooltips/valida_email.js"></script>
    <style>
        .inbox-download-list li > *:first-child {
            width: 250px;
        }
    </style>
</head>

<body>
<div class="container-fluid">

    <form method="post" name="formulario" id="formulario" action="">

        <input type=hidden name='ent' value='<?= $ent ?>'>
        <input type=hidden NAME=radicadopadre value='<?= $radicadopadre ?>'>

        <div class="row" id="content">
            <div class="col-sm-12">
                <div class="col col-1">
                    <h1 class="page-title txt-color-blueDark">
                        Radicaci&oacute;n <?= $nomEntidad ?>
                        <?= $tRadicacionDesc ?>
                        <p><small id="idrad"> '<?=$nurad?> <?= $encabezado ?></small></p>
                    </h1>
                </div>

                <div class="col col-7 smart-form">

                    <section class="col col-2">
                        <label class="label">
                            DD / MM / AAAA
                        </label>
                        <h6> <?= $ddate ?> / <?= $mdate ?> / <?= $adate ?> </h6>
                    </section>

                    <?= $showEntrada ?>

                    <section class="col col-2">
                        <label class="label">
                            Fecha Doc
                        </label>
                        <label class="input"> <i class="icon-append fa fa-calendar"></i>
                            <input  title='Escriba fecha día 2 digitos - mes 2 digitos - año 4 digitos ' type="text" id="fecha_gen_doc" name="fecha_gen_doc"
                                   placeholder="Fecha de radicacion" value="<?= $fecha_gen_doc ?>">
                        </label>
                    </section>

                </div>

                <div id="showRadicar" class="col col-2 <?= $hidetable ?>">
                    <a  title='radicar documento' data-toggle="modal" name='Submit3' value='Radicar'
                       class="btn btn-primary btn-lg pull-right header-btn radicarNuevo">
                        <i class="fa fa-circle-arrow-up fa-lg"></i>
                        Radicar documento
                    </a>
                </div>

                <div id="showModificar" class="col col-2 <?= $modificar ?>">
                    <a   title='modificar' data-toggle="modal" id="modificaRad" name="Submit44"
                       class="btn bg-color-greenDark txt-color-white btn-lg btn-block">
                        Modificar <?= $nurad ?>
                        <?= $senddata ?>
                    </a>

                    <label id="sticker">
                        <a  title='sticker' href="javascript:void(0);"
                           onClick="window.open ('./stickerWeb/index.php?<?= $varEnvio ?>&alineacion=Center','sticker<?= $nurad ?>','menubar=0,resizable=0,scrollbars=0,width=450,height=180,toolbar=0,location=0');"
                           class="btn btn-link">Sticker</a>
                    </label>

                    <label id="asociar">
                        <a  title='asociar' href="javascript:void(0);"
                           onClick="window.open ('../uploadFiles/uploadFileRadicado.php?busqRadicados=<?= $nurad ?>&Buscar=Buscar&<?= $varEnvio ?>&alineacion=Center','busqRadicados=<?= $nurad ?>','menubar=0,resizable=0,scrollbars=0,width=550,height=280,toolbar=0,location=0');"
                           class="btn btn-link">Asociar imagen</a>
                    </label>


                    <label id="subir_anexos">
                         <a title='subir anexos' href="javascript:void(0);"
                           onClick="window.open ('../uploadFiles/uploadAnexRadicado.php?nurad=<?= $nurad ?>');"
                           class="btn btn-link">Subir anexos</a>
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
            <section id="alertmessage"></section>
            </div>
        </div>

        <?php if ($esNotificacion) { ?>
        <div style="padding:15px"  class="row">
        <div class="col-sm-12 widget-body">
        <div class="panel-body well">
        <?php } ?> 

        <div class="row">
            <article class="col-sm-12">
                <div data-widget-editbutton="false" id="wid-id-0" role="widget">
                    <!-- widget content -->
                    <div class="col col-12">
											  <?=$showEntrada1?>
                        <section id="formsearch" class="form-inline smart-form">
                            <a id="idnuevo"
                               title="Solo si su destinatario no se encuentra en la búsqueda ingrese uno nuevo."
                               href="javascript:void(0);"
                               class="btn btn-sm btn-primary">
                                <i class="fa fa-plus-circle"></i> Nuevo
                            </a>

                            <a id="idconsulta"
                               title="Pulse clic para buscar el remitente o destinatario."
                               href="javascript:void(0);"
                               class="btn btn-sm btn-primary">
                                <i class="fa fa-search"></i> Buscar 
                            </a>


                              <?php if ($esNotificacionCircular) { ?>
                              <section class="col col-3">
                              <?php } else {?>
                              <section class="col col-2">
                              <?php } ?>
                                  <label class="select">
                                      <?php if ($esNotificacionCircular) { ?>
                                            <select id="tipo_usuario" class="form-control input-sm" disabled>
                                      <?php } else {?> 
                                            <select id="tipo_usuario" class="form-control input-sm">
                                      <?php } ?>                                      
                                          <?php if ($esNotificacionCircular) { ?>
                                            <option value='4' <?= $circ_int_selected ?> ><?= $_name_4 ?></option>
                                            <option value='5' <?= $circ_ext_selected ?> ><?= $_name_5 ?></option>
                                          <?php } else {?>
                                            <option value=''> Seleccionar</option>
                                            <?php if ($ent != MEMORANDO) { ?>
                                                <option value='0' <?= $ciudadano_select ?> >Solicitante</option>
                                            <?php } ?>    
                                            <?php if ($_enable_1 == true) { ?>
                                              <option value='1' <?php echo $esp_select ?> >ESP</option> 
                                            <?php } ?>
                                            <?php if ($_enable_2 == true && $ent != MEMORANDO) { ?>
                                              <option value='2' <?php echo $entidad_selected ?> > <?= $_name_2 ?> </option>
                                            <?php } ?>
                                            <option value='6' <?= $usuario_selected ?> ><?= $_name_6 ?></option>
                                          <?php } ?>
                                      </select>
                                  </label>
                              </section>

                            <?php if (!$esNotificacionCircular) { ?>
                              <section class="col col-2">
                                  <label class="input">
                                      <i class="icon-prepend fa fa-search"></i>
                                      <input type=text id='documento_us' pattern="[0-9]" class="required alphanumeric"
                                             placeholder="Identificación">
                                  </label>
                              </section>
                            <?php } ?>

                            <?php if ($esNotificacionCircular) { ?>
                            <section class="col col-6">
                                <label class="input">
                                    <i class="icon-prepend fa fa-search"></i>
                                    <input type=text id='destinatario_us' data-rel="solo-text" value="" pattern="[A-Za-z]"
                                           placeholder="Destinatarios">
                                </label>
                            </section>
                            <?php } else {?>
                            <section class="col col-2">
                                <label class="input">
                                    <i class="icon-prepend fa fa-search"></i>
                                    <INPUT type=text id='nombre_us' data-rel="solo-text" value="" pattern="[A-Za-z]"
                                           placeholder="Nombre">
                                </label>
                            </section>
                            <?php } ?>

                            <?php if (!$esNotificacionCircular) { ?>
                            <section class="col col-2">
                                <label class="input">
                                    <i class="icon-prepend fa fa-search"></i>
                                    <input type=text id='telefono_us' pattern="[0-9]" value=""
                                           placeholder="Tel&eacute;fono">
                                </label>
                            </section>
                            <?php } ?>

                            <?php if (!$esNotificacionCircular) { ?>
                            <section class="col col-2">
                                <label class="input">
                                    <i class="icon-prepend fa fa-search"></i>
                                    <INPUT type=text id='mail_us' pattern="[^@]+@[^@]+\.[a-zA-Z]{2,6}" value=""
                                           placeholder="Correo Electr&oacute;nico">
                                </label>
                            </section>
                            <?php } ?>
                        </section>

                        <!--Muestra Resultados de la busqueda-->
                        <section id="showAnswer" class="col-lg-12 hide well">
                            <ul id="resBusqueda" class="inbox-download-list"></ul>
                        </section>
                    </div>

                    <section id="tableSection" class="well col-lg-12 smart-form <?= $showtable ?>">
                        <table class="table">
                            <tbody id="tableshow"><?= $showUsers ?></tbody>
                        </table>
                    </section>
                </div>
            </article>
        </div>

        <?php if ($esNotificacion) { ?>
        </div>
        </div>
        </div>
        <?php } ?>

        <div style="padding:15px"  class="row">
            <div class="col-sm-12 widget-body">
                <div class="panel-body well">
									<fieldset>
									<div class="row">
										<section class="col-sm-3">
											<div class="form-group">
                        <?php if ($esNotificacion) { ?>
                          <label>Asunto / ep&iacute;grafe</label>
                        <?php } else { ?>
												<label>* Asunto</label>
                        <?php } ?>
												<textarea  title='Coloque aquí el  asunto' required id="asu" style="resize: none;" name="asu" pattern="[A-Za-z]" cols="70"
                                    class="form-control" rows="4" maxlength="345"><?= $asu ?></textarea>
											</div>
                      <?php if ($esNotificacion) { ?>
                        <div class="form-group">
                          <label>Medio de Publicaci&oacute;n</label>
                          <?= $medioPub ?>
                        </div>
                      <?php } ?>
										</section>
										<section class="col-sm-3">
                      <?php if ($esNotificacion) { ?>
                      <div class="form-group" style="opacity: 0.5; pointer-events: none;">
                        <label>Medio de env&iacute;o</label>
                        <input type="text" class="form-control">
                      </div>
                      <?php } else { ?>
                      <div class="form-group">
                          <label for="med"  title='seleccione medio de recepción' >Medio Recepci&oacute;n / Env&iacute;o</label>
                          <?= $medioRec ?>
                      </div>
                      <?php } ?>
											<div class="row">
												<section class="col-sm-6">
													<div class="form-group">
															<label>No. Folios</label>
															<input title='número de folios' name="nofolios" id="nofolios" type="text" pattern="[0-9]" size="10"
																		 onkeypress="return justNumbers(event);"
																		 class="form-control"
																		 maxlength="5" value="<?= $numFolio ?>">
													</div>
												</section>
												<section class="col-sm-6">
													<div class="form-group">
															<label>No. Anexos </label>
															<input title='número de anexos' name="noanexos" id="noanexos" type="text" pattern="[0-9]" size="10"
																		 onkeypress="return justNumbers(event);"
																		 class="form-control"
																		 maxlength="5" value="<?= $numAnexo ?>">
													</div>
												</section>
											</div>
                      <?php if ($esNotificacion) { ?>
                        <div class="form-group">
                          <label>Caracter acto administrativo</label>
                          <?= $caracterAdtvo ?>
                        </div>
                      <?php } ?>
										</section>
										<section class="col-sm-3">
											<div class="form-group">
													<label title='seleccione una dependencia'>*Dependencia</label>
													<?= $depselect ?>
											</div>
											<div class="form-group">
													<label>Descripci&oacute;n Anexos</label>
													<input title='descripción anexos' name="ane" id="ane" type="text"
																 class="form-control"
																 pattern="[A-Za-z]" maxlength="200" value="<?= $ane ?>">
											</div>
                      <?php if ($esNotificacion) { ?>
                        <div class="form-group">
                          <label>SIAD</label>
                          <input name="siad" id="siad" type="text" pattern="[0-9]"
                                 onkeypress="return justNumbers(event);"
                                 class="form-control"
                                 maxlength="13" value="<?= $siad_preestablecido ?>">
                        </div>
                      <?php } ?>
										</section>
										<section class="col-sm-3">
											<?php if ($_show_type_doc == true && $esNotificacion == false) { ?>
											<div class="form-group">
													<label>Clasificación Previa</label>
													<?= $tipoDoc ?>
											</div>
											<?php } else { ?>
											<input type="hidden" value="<?= $tdoc ?>" name="tdoc">
											<?php } ?>
											<div class="form-group">
												<label >Nivel de Seguridad:</label>
												<p>
														<input type="radio" title='seleccione el nivel de público' name="nivelSeguridad" id="publico" value="0" checked> Público.
                            <?php if ($esNotificacion) { ?><br><?php } ?>
														<input type="radio" title='seleccione el nivel de confidencial'  name="nivelSeguridad" id="confidencial" value="1"> Reservado.
                            <?php if ($esNotificacion) { ?><br><?php } ?>
														<input type="radio" title='seleccione el nivel de clasificado' name="nivelSeguridad" id="clasificada" value="2"> Clasificado.
												</p>
											</div>
                      <?php if ($esNotificacion) { ?>
                      <!--<div class="form-group">
                        <label>Prioridad:</label>
                        <p>
                            <?php if (empty($prioridad_prestablecido)) { ?>
                              <input type="radio" name="prioridad" id="prioritario" value="1"> S&iacute;
                              <input type="radio" name="prioridad" id="noPrioritario" value="0" checked> No
                            <?php } else { ?>
                              <input type="radio" name="prioridad" id="prioritario" value="1" checked> S&iacute;
                              <input type="radio" name="prioridad" id="noPrioritario" value="0"> No
                            <?php } ?>
                        </p>
                      </div>-->
                      <?php } ?>
										</section>
										<?php if ($_SESSION["varEstaenfisico"] == 1) { ?>
										<section class="col-sm-3">
										<label>Fisico en archivo</label>
										<input name="esta_fisico" id="esta_fisico"
													 type="checkbox"
														<?php if ($esta_fisico == 1) {
														    echo " checked";
														}?>>
										</section>
										<?php } ?>


									</div>
									</fieldset>

                  <div class="form-group" style="<?= $styleFirmador ?>" >
                        <label >Funcionario que firma:</label>
                        
                        <?php
                                $sqlFirmador = "SELECT u.usua_nomb, (u.usua_codi || '-' || u.depe_codi) as id  
                                  FROM  usuario u
                                  JOIN autm_membresias me on me.autu_id = u.id
                                  JOIN autg_grupos gr on gr.id = me.autg_id
                                  JOin autr_restric_grupo rg on rg.autg_id = gr.id
                                  JOin autp_permisos ap on ap.id = rg.autp_id
                                  WHERE ap.nombre = 'USUA_PERM_FIRMA' and  u.depe_codi != 900
                                  GROUP BY u.usua_nomb, u.usua_codi, u.depe_codi  order by u.usua_nomb";
$rsSqlFirmador = $db->conn->Execute($sqlFirmador);
print $rsSqlFirmador->GetMenu2("s_firmador", "$firmador", "0-0:-- Seleccione el funcionario --", false, "", "class='form-control'");
?>

                  </div>                  
                    
                  <legend class="<?= $modificar ?>" >Informar a:</legend>
                  <fieldset>
                      <div class="row <?= $modificar ?>" id="inforshow">
                          <section class="col-sm-3">
                              <div class="form-group">
                                      <label title='seleccione dependencia'>Dependencia</label>
                                      <?= $depselectInf ?>
                              </div>
                          </section>
                          <?php if ($_TIPO_INFORMADO == 1) { ?>
                              <section class="col-sm-3">
                                  <div class="form-group">
                                      <label>Usuario</label>
                                      <select title='usuario a informar' name="usuarios_informar" multiple="multiple" class="form-control" id="informarUsuario">
                                              <option value="0">-- Seleccione un Usuario --</option>
                                      </select>
                                  </div>
                              </section>
                          <?php } ?>

                          <section class="col-sm-3 smart-form">
                              <label class="label">
                                    Usuarios Seleccionados para notificar
                              </label>

                              <div class="inline-group" id="showusers"></div>

                              <div class="alert alert-block alert-success hide">
                                  <a class="close" data-dismiss="alert" href="#">×</a>
                                  <div class="inline-group" id="showresult"></div>
                              </div>
                          </section>
                          <section class="col-sm-3">
                              <label>
                                  <!-- Button trigger modal -->
                                  <a title='informar' data-toggle="modal" id="accioninfousua"
                                      class="btn btn-success btn-sm header-btn hidden-mobile">
                                          <i class="fa fa-circle-arrow-up fa-lg"></i> Informar
                                  </a>
                              </label>
                          </section>
                      </div>
                  </fieldset>

                </div>
            </div>
        </div>

        <div class="col-lg-9"></div>
        <div id="copyradicar"></div>
        <input type="hidden" name="errormail" id="errormail" value="0">
        <br><br><br><br><br>
    </form>

    <a title='Sticker' id="skeleton" href="javascript:void(0);"
       onclick="window.open ('./stickerWeb/index.php?<?= $idsession ?>&nurad=xxxxxx&ent=<?= $ent ?>','stickerxxxxxx','menubar=0,resizable=0,scrollbars=0,width=450,height=180,toolbar=0,location=0');"
       class="btn btn-link hide">Sticker</a>

    <a title='ver radicado' id="skeleton8" href="../verradicado.php?verrad=xxxxxx&nomcarpeta=Busquedas"
       class="btn btn-link hide">Ver radicado</a>

    <a title='asociar imagen' id="skeleton9" href="javascript:void(0);"
       onClick="window.open ('../uploadFiles/uploadFileRadicado.php?<?= $idsession ?>&busqRadicados=xxxxxx&Buscar=Buscar&alineacion=Center','busqRadicados=xxxxxx','menubar=0,resizable=0,scrollbars=0,width=550,height=280,toolbar=0,location=0');"
       class="btn btn-link hide">Asociar imagen</a>

     <a title='subir anexos' id="skeleton11" href="javascript:void(0);"
       onClick="window.open ('../uploadFiles/uploadAnexRadicado.php?nurad=xxxxxx');"
       class="btn btn-link hide">Subir anexos</a>

    <a title='tipificar' id="skeleton10" href="javascript:void(0);"
       onClick="window.open ('../radicacion/tipificar_documento.php?nurad=xxxxxx&ind_ProcAnex=N&codusua=<?= $codusua ?>&coddepe=<?= $coddepe ?>&codusuario=<?= $codusua ?>&dependencia=<?= $coddepe ?>&tsub=0&codserie=0','busqRadicados=<?= $nurad ?>','menubar=0,resizable=0,scrollbars=0,width=650,height=480,toolbar=0,location=0');"
       class="btn btn-link hide">Tipificar</a>

    <label class='radio userinfo hide'>
        <input type="checkbox" checked name='radio[]' value=''><i></i>
    </label>

    <script type="text/javascript">
        function justNumbers(e) {
            var keynum = window.event ? window.event.keyCode : e.which;
            if ((keynum == 8) || (keynum == 46))
                return true;
            return /\d/.test(String.fromCharCode(keynum));
        }

        $(document).ready(function () {

            var TIPO_RADICADO = '<?= $ent ?>';

            if(TIPO_RADICADO == 1) {
                document.getElementById("asu").setAttribute('maxlength', '510');
            }


            var ALLDATA;
            var INCREMENTAL1 = 0;
            var EJECUCION = false;
            var RADICACION_NOTIFICACION = '<?= $esNotificacion ?>';
            var RADICACION_CIRCULAR = '<?= $esNotificacionCircular ?>';

            // DO NOT REMOVE : GLOBAL FUNCTIONS!
            pageSetUp();

            $('#copyradicar').html($('#showRadicar').clone());

            //Datepicker muestra fecha
            $('#fecha_gen_doc').datepicker({
                dateFormat: 'dd-mm-yy',
                onSelect: function (selectedDate) {
                    $('#date').datepicker('option', 'maxDate', selectedDate);
                }
            });

            /**
             * Generacion de eventos para los usuarios seleccionados
             * permitiendo cambiar la informacion antes de ser enviada al
             * servidor. Guardando de esta manera los datos del usuario con
             * las modificiaciones necesarias
             */
            $("body").on("click", '.fa-check', function () {
                $('label[name^="inp_"]').addClass('hide');
                $('div[name^="div_"]').removeClass('hide');
                var iddiv = $(this).parent().attr('name').substring(4);
                var tex_nuevo = $('label[name=inp_' + iddiv + ']').find('input').val();
                var div_nuevo = $('div[name=div_' + iddiv + ']').clone();
                $('div[name=div_' + iddiv + ']').text(tex_nuevo);
                $('div[name=div_' + iddiv + ']').append(div_nuevo.children());
            });

            /**
             * Si el formulario es llamado desde anexos para modificar la información
             * o si se acaba de radicar y debemos modificar datos mostrarmos el boton
             * de modificacion duplicado en la parte superior y en la inferior.
             */
            <?php if($modificar != 'hide') { ?>
            $('#copyradicar').html($('#showModificar').clone());
            <?php } ?>

            /**
             * Generacion de eventos para los usuarios seleccionados
             * permitiendo cambiar la informacion antes de ser enviada al
             * servidor. Guardando de esta manera los datos del usuario con
             * las modificiaciones necesarias
             */
            $("body").on("change", '#informar', function () {

                var values = $(this).val();
                <?php if($_TIPO_INFORMADO == 1) { ?>
                $.post("./ajax_buscarUsuario.php", {searchUserInDep: values}).done(
                    function (data) {
                        $('#informarUsuario').html(data[0]);
                    }
                );
                <?php } elseif ($_TIPO_INFORMADO == 2) {?>
                $.post("./ajax_buscarUsuario.php", {MsearchUserInDep: values}).done(
                    function (data) {
                        $('#showusers').html(data[0]);
                    }
                );
                <?php } ?>
            });

            /**
             * Generacion de eventos para los usuarios seleccionados
             * Selecciona los usuarios y los muestra para informar con
             * el radicado seleccionado.
             */
            <?php if($_TIPO_INFORMADO == 1) { ?>
            $("body").on("change", '#informarUsuario', function () {
                $('#informarUsuario :selected').each(function (i, selected) {
                    var newUser = $('.userinfo').last().clone();
                    var text = $(selected).text();
                    var value = $(selected).val();

                    newUser.removeClass('hide');
                    newUser.append(text);
                    newUser.find('input').val($('#informar').val() + '_' + value);

                    $('#showusers').append(newUser);
                });
            });
            <?php } ?>
            $("body").on("click", '#accioninfousua', function () {
                var text = [];
                <?php if($_TIPO_INFORMADO == 1) { ?>
                $('#showusers').find('input').each(function (index, value) {
                    text.push($(value).val());
                });
                <?php } elseif ($_TIPO_INFORMADO == 2) { ?>
                $('#showusers').find('input:checked').each(function (index, value) {
                    text.push($(value).val());
                });
                <?php } ?>
                var nurad = $('input[name="nurad"]').val();

                $.post("./ajax_informarUsuario.php", {addUser: text, radicado: nurad}).done(
                    function (data) {
                        $('#showresult').text(data['true']);
                        $('#showresult').parent().removeClass('hide')
                    }
                );
            });

            $("body").on("click", '.fa-pencil', function () {
                var texto = $(this).parent().attr('name');
                $.each($('[name^="inp_' + texto + '"]'), function (index, value) {
                    $(value).removeClass('hide');
                });

                $.each($('[name^="div_' + texto + '"]'), function (index, value) {
                    $(value).addClass('hide');
                });
            });
            <?php if($_TIPO_INFORMADO == 1) { ?>
            $("body").on("change", '.informarusuarios', function () {
                var content = $(this).val();
                $('#showusers').append("<label class='radio'><input type='radio' name='radio-inline' checked=''><i></i>" +
                    content + "</label>");
            });
            <?php } ?>

            /**
             * Permite crear un nuevo usurio mostrando los campos vacios y
             * dejando que el usuario registre los datos de la persona que necesita.
             * las modificiaciones necesarias
             * Se envia en el codigo dos xx para identificar que es un usuario nuevo.
             * Cuando se carga el usuario de un radicado ya existente en cambio de las dos xx
             * se muestra el codigo con el cual se guardo.
             */


            $("#idnuevo").on("click", function () {
                var tipo = $('#tipo_usuario').val();
                if(tipo != '')
                {
                    if(RADICACION_CIRCULAR){
                        var iddata = [{
                            "CODIGO_DESTINATARIOS": "",
                            "DESTINATARIOS": "",
                            "TIPO_CIRCULAR": $('#tipo_usuario').val()
                        }];

                        $.post("./ajax_buscarUsuario.php", {addDestinatariosCircular: JSON.stringify(iddata)}).done(
                            function (data) {
                                $('#tableshow').append(data[0]);
                                $('#tableSection').removeClass('hide');
                            }
                        );

                        INCREMENTAL1++; 

                    } else {
                        var iddata = [{
                            "CODIGO": 'XX' + INCREMENTAL1,
                            "NOMBRE": "",
                            "TELEF": "",
                            "EMAIL": "",
                            "CEDULA": "",
                            "PAIS": "COLOMBIA",
                            "PAIS_CODIGO": "170",
                            "DEP": "ATLÁNTICO",
                            "DEP_CODIGO": "8",
                            "MUNI": "BARRANQUILLA",
                            "MUNI_CODIGO": "1",
                            "TIPO": $('#tipo_usuario').val(),
                            "APELLIDO": "",
                            "NECESITA_NOTIFICACION": RADICACION_NOTIFICACION,
                            "TIPO_RADICADO": TIPO_RADICADO,
                            "CARGO": ""
                        }];

                        $.post("./ajax_buscarUsuario.php", {addUser: JSON.stringify(iddata)}).done(
                            function (data) {
                                $('#tableshow').append(data[0]);
                                $('#tableSection').removeClass('hide');
                            }
                        );

                        INCREMENTAL1++;
                    }
                } else {
                    alert('Por favor seleccione el tipo de usuario que desea crear.');
                }

            });


            $("body").on("keyup", 'input[name$="muni"], input[name$="dep"], input[name$="pais"]', function () {
                if ($(this).attr('autocomplete') === undefined) {
                    addAutocomple(this);
                }
                ;
            });

            $("#asu").keypress(function () {
                if ($("#asu").val().length <= 10) {
                    $('#asu').parent().removeClass('state-success').addClass('state-error');
                } else {

                    $('#asu').parent().removeClass('state-error').addClass('state-success');
                }
            });

            function addAutocomple(element) {
                var accion = $(element).attr('name').split("_")[4];
                var group = $(element).attr('name').split("_")[2]+"_"+$(element).attr('name').split("_")[3];
                console.log(group);
                $(element).autocomplete({
                    source: function (request, response) {
                        if (accion == "muni" && $('input[name$="' + group + '_dep_codigo"]').val() == 0) {
                            alert("Debe seleccionar primero un Departamento de manera correcta." + accion);
                            $('input[name$="' + group + '_dep"]').focus();
                        }
                        $.ajax({
                            url: "./ajax_buscarDivipola.php",
                            dataType: "json",
                            type: 'POST',
                            maxRows: 12,
                            data: {
                                'action': accion,
                                'search': request.term,
                                'muni': $('input[name$="' + group + '_muni"]').val(),
                                'dep': $('input[name$="' + group + '_dep"]').val(),
                                'pais': $('input[name$="' + group + '_pais"]').val()
                            },
                            success: function (data) {

                                response($.map(data, function (item) {
                                    return {
                                        label: item.NOMBRE,
                                        id: item.CODIGO
                                    }

                                }));
                                if (accion == "dep") {
                                    $('input[name$="' + group + '_dep_codigo"]').val('0');
                                    $('input[name$="' + group + '_dep"]').parent().removeClass('state-success').addClass('state-error');
                                }
                                if (accion == "muni") {
                                    $('input[name$="' + group + '_muni_codigo"]').val('0');
                                    $('input[name$="' + group + '_muni"]').parent().removeClass('state-success').addClass('state-error');
                                }


                                $('.ui-autocomplete-input').removeClass('ui-autocomplete-loading');
                            }

                        });
                    },
                    minLength: 1,
                    select: function (event, ui) {
                        var setempty = $(this).attr('name').split("_")[4];
                        var namehiddent = $(this).attr('name') + "_codigo";
                        var nameinput = $(this).attr('name');
                        $("input[name=" + namehiddent + "]").val(ui.item.id);
                        switch (setempty) {
                            case 'muni':
                                $('input[name$="' + group + '_muni"]').parent().removeClass('state-error').addClass('state-success');
                                $('#asu').focus();
                                break;
                            case 'dep':
                                $('input[name$="' + group + '_muni"]').val('');
                                $('input[name$="' + group + '_muni_codigo"]').val('');
                                $('input[name$="' + group + '_dep"]').parent().removeClass('state-error').addClass('state-success');
                                $('input[name$="' + group + '_muni"]').focus();
                                break;

                            case 'pais':
                                $('input[name$="' + group + '_muni"]').val('');
                                $('input[name$="' + group + '_muni_codigo"]').val('');
                                $('input[name$="' + group + '_dep"]').val('');
                                $('input[name$="' + group + '_dep_codigo"]').val('');
                                $('input[name$="' + group + '_pais"]').parent().removeClass('state-error').addClass('state-success');
                                $('input[name$="' + group + '_dep"]').focus();
                                break;

                        }
                    }
                });
            }


            //Deja en blanco los campos de busqueda al seleccionar
            //un nuevo usuario.
            $("#tipo_usuario").on('change', function () {
                $('#documento_us, #nombre_us, #telefono_us, #mail_us').val("").parent().removeClass('state-success state-error');
                $('#resBusqueda').empty();
                $('#showAnswer').addClass('hide');
            });

            function uppFirs(txt) {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            }

            //Valida los campos antes de ser enviados al servidor
            function validate(objData) {
                var pass = false;
                var min = 3;
                var allempty =
                    alldata = 0;
                if (!$.isEmptyObject(objData)) {

                    $.each(objData, function (key, val) {
                        var valdata = val.value;
                        alldata++;
                        if ((valdata.length < min && valdata.length != 0) || /^a-zA-Z0-9áéíóúÁÉÍÓÚÑñ ]+$/.test(valdata)) {
                            $('#' + objData[key].id).parent().removeClass('state-success').addClass('state-error');
                            delete objData[key];
                        } else if (valdata.length == 0) {
                            $('#' + objData[key].id).parent().removeClass('state-success state-error');
                            delete objData[key];
                            allempty++;
                        } else {
                            $('#' + objData[key].id).parent().removeClass('state-error').addClass('state-success');
                            pass = true;
                        }
                    });

                }

                if (alldata === allempty) {
                    $('#resBusqueda').empty();
                    $('#showAnswer').addClass('hide');
                }
                return pass;
            };


            /**
             * Funcion para retornar los usuarios seleccionados y mostrarlos
             * en la tabla seleccionado con las opciones de modificaciones individuales
             * @iddata array de los datos ya seleccionados
             * @returns inserta html procesado a la tabla de usuarios seleccionados
             */
            function passDataToTable(iddata) {
                ALLDATA[iddata]["NECESITA_NOTIFICACION"] = RADICACION_NOTIFICACION;
                ALLDATA[iddata]["TIPO_RADICADO"] = TIPO_RADICADO;
                var trTable = [ALLDATA[iddata]];
                $.post("./ajax_buscarUsuario.php", {addUser: JSON.stringify(trTable)}).done(
                    function (data) {
                        $('#tableshow').append(data[0]);
                        $('#tableSection').removeClass('hide');
                    }
                );
            }

            //Modifica respuesta del servidor para presentarla
            //con formato.
            function formatAnswer(data) {
                var dataformat;
                var indiv = $('#resBusqueda');

                indiv.empty();

                $.each(data, function (i) {

                    var li = $('<li/>').appendTo(indiv);
                    var nombre = (data[i].NOMBRE === null) ? '' : data[i].NOMBRE.replace(/\w\S*/g, uppFirs);
                    var apell = (data[i].APELLIDO === null) ? '' : data[i].APELLIDO.replace(/\w\S*/g, uppFirs);
                    var telef = data[i].TELEF;
                    var email = (data[i].EMAIL) ? data[i].EMAIL.toLowerCase() : '';
                    var cedula = data[i].CEDULA;
                    var direccion = data[i].DIRECCION;

                    var div = $('<div/>')
                        .addClass('well well-sm')
                        .html('<div  class="col col-12" >'
                            + '<h6 class=" text-success semi-bold">'
                            + cedula
                            + ' <i title="agregar a ' + nombre + ' ' + apell + '"  class="fa fa-plus-square"></i>'
                            + '</h6>'
                            + '</div>'
                            + '<div class="showdot176" ><b>' + nombre + ' ' + apell + '</b></div>'
                            + '<div class="showdot176">' + telef + '</div>'
                            + '<div class="showdot176">' + email + '</div>'
                            + '<div class="showdot176">' + direccion + '</div>')
                        .attr('name', 'cod_' + i)
                        .attr('tabindex', 5)
                        .on("click", function () {
                            var codUser = $(this).attr('name').substring(4);
                            var count = 0;
                            var datali = $('#showAnswer').children('ul').children('li');
                            passDataToTable(codUser);
                            $(this).addClass('hide');

                            datali.each(function () {
                                var ishide = $(this).children('div').hasClass("hide");
                                if (ishide) {
                                    count++;
                                }
                            });

                            $('#showAnswer').addClass('hide');

                        })
                        .appendTo(li);
                });
                $('#showAnswer').removeClass('hide');
            };

            /**
             * Funcion para retornar los destinatarios seleccionados y mostrarlos
             * en la tabla con las opciones de modificaciones individuales
             * @iddata int indice del array correspondiente al destinatario escogido
             * @returns inserta html procesado al campo de destinarios seleccionados
             */
            function passDestinatariosDataToTable(iddata) {
                var trTable = [ALLDATA[iddata]];
                $.post("./ajax_buscarUsuario.php", {addDestinatariosCircular: JSON.stringify(trTable)}).done(
                    function (data) {
                      $('#tableshow').append(data[0]);
                      $('#tableSection').removeClass('hide');
                    }
                );
            }

            //Modifica respuesta del servidor para presentarla
            //con formato.
            function formatAnswerDestinatario(data) {
                var dataformat;
                var indiv = $('#resBusqueda');
                var boton = "Usar destinatarios";

                indiv.empty();

                $.each(data, function (i) {
                    var li = $('<li style="display:inline; list-style-type:none;"/>').appendTo(indiv);
                    var div = $('<div style="width:100%;"/>')
                        .addClass('well well-sm')
                        .html('<div  class="col col-12">'
                            + '<h6 class=" text-success semi-bold">'
                            + boton
                            + ' <i class="fa fa-plus-square"></i>'
                            + '</h6>'
                            + '</div>'
                            + '<div><b>' + data[i].DESTINATARIOS + '</b></div>')
                        .attr('name', 'cod_' + i)
                        .on("click", function () {
                            var codUser = $(this).attr('name').substring(4);
                            var count = 0;
                            var datali = $('#showAnswer').children('ul').children('li');
                            passDestinatariosDataToTable(codUser);
                            $(this).addClass('hide');

                            datali.each(function () {
                                var ishide = $(this).children('div').hasClass("hide");
                                if (ishide) {
                                    count++;
                                }
                            });

                            $('#showAnswer').addClass('hide');

                        })
                        .appendTo(li);
                });
                $('#showAnswer').removeClass('hide');
            };

            //Autocomplete busqueda de usuarios
            $("#documento_us, #nombre_us, #telefono_us, #mail_us").on('keyup', function(e) {
                var tipo = $('#tipo_usuario').val();
                if(tipo == '')
                {
                    e.preventDefault();
                    alert('Por favor seleccione el tipo de usuario que desea buscar.');
                }
            });
/*
            $("#documento_us, #nombre_us, #telefono_us, #mail_us").on('keyup', function (e) {
                var data = {};

                data.docu = {value: $("#documento_us").val(), id: "documento_us"};
                data.name = {value: $("#nombre_us").val(), id: "nombre_us"};
                data.tele = {value: $("#telefono_us").val(), id: "telefono_us"};
                data.mail = {value: $("#mail_us").val(), id: "mail_us"};

                if (validate(data)) {
                    data.tdoc = $("#tipo_usuario").val();
                    $.post("./ajax_buscarUsuario.php", {search: JSON.stringify(data)}).done(
                        function (data) {
                            ALLDATA = data;
                            if (data !== null) {
                                formatAnswer(data);
                            }
                        }
                    );
                }
            });

*/
            $("#idconsulta").on('click', function (e) {
                var data = {};

                data.docu = {value: $("#documento_us").val(), id: "documento_us"};
                data.name = {value: $("#nombre_us").val(), id: "nombre_us"};
                data.tele = {value: $("#telefono_us").val(), id: "telefono_us"};
                data.mail = {value: $("#mail_us").val(), id: "mail_us"};

                if (validate(data)) {
                    data.tdoc = $("#tipo_usuario").val();
                    $.post("./ajax_buscarUsuario.php", {search: JSON.stringify(data)}).done(
                        function (data) {
                            ALLDATA = data;
                            if (data !== null) {
                                formatAnswer(data);
                            }
                        }
                    );
                }
            });













            $("#destinatario_us").on('keyup', function (e) {
                var data = {};
                data.name = {value: $("#destinatario_us").val(), id: "destinatario_us"};

                if (validate(data)) {
                    data.tdoc = $("#tipo_usuario").val();
                    $.post("./ajax_buscarUsuario.php", {searchDestinatarios: JSON.stringify(data)}).done(
                        function (data) {
                            ALLDATA = data;
                            if (data !== null) {
                                formatAnswerDestinatario(data);
                            }
                        }
                    );
                }
            });

            //Mostrar validacion del formulario
            function mostrarAlert(objAlert) {
                var type = objAlert.type;
                var message = objAlert.message;

                var div = $('<div/>')
                    .addClass('alert alert-block alert-' + type)
                    .html(
                        '<a class="close" data-dismiss="alert" href="#">×</a>'
                        + '<h4 class="alert-heading">' + message + '</h4>'
                    ).appendTo('#alertmessage');
            };

            function borrarAlert() {
                $('#alertmessage').empty();
            }

            //Radicar documento nuevo
            $('body').on("click", '.radicarNuevo, #modificaRad', EJECUCION, function () {
                var acction = $(this).attr("id");
                var pass = true;
                var idsession = '<?=$idsession?>';

                /* Realizar validaciones antes de enviar el radicado*/

                $('#alertmessage').empty();

                //Folios y Anexos
                if (/[A-Za-z]+$/.test($("#nofolios").val()) ||
                    /[A-Za-z]+$/.test($("#noanexos").val())) {
                    mostrarAlert({
                        type: 'danger'
                        , message: 'Escriba un número válido en No de folios o anexos.'
                    });
                    pass = false;
                }

                //Fecha del radicado
                var fechaActual = new Date();
                var fecha_doc = $('#fecha_gen_doc').val();
                var dias_doc = fecha_doc.substring(0, 2);
                var mes_doc = fecha_doc.substring(3, 5);
                var ano_doc = fecha_doc.substring(6, 10);

                var fecha = new Date(ano_doc, mes_doc - 1, dias_doc);
                var tiempoRestante = fechaActual.getTime() - fecha.getTime();
                var dias = Math.floor(tiempoRestante / (1000 * 60 * 60 * 24));


                if (dias > 960 && dias < 1500) {
                    mostrarAlert({type: 'danger', message: 'El documento tiene fecha anterior a 60 dias!!.'});
                    pass = false;
                } else if (dias > 1500) {
                    mostrarAlert({type: 'danger', message: 'Verifique la fecha del documento!!'});
                    pass = false;
                } else if (dias < 0) {
                    mostrarAlert({
                        type: 'danger',
                        message: 'Verifique la fecha del documento !!, es Una fecha Superior a la Del dia de Hoy'
                    });
                    pass = false;
                }
                ;

                if (RADICACION_CIRCULAR) {
                    if ($("#id_destinatario").length === 0) {
                        mostrarAlert({type: 'danger', message: 'Seleccione un destinatario'});
                        pass = false;
                    }
                } else {
                    //Usuarios
                    if ($('input[name^="usuario"]').length === 0) {
                        mostrarAlert({type: 'danger', message: 'Seleccione un usuario'});
                        pass = false;
                    }
                    ;
                }

                //Asunto
                var asu = $('#asu').val();
                //Tamanao del asunto Constante
               
                var min = 5;
                if (asu.length < min) {
                    mostrarAlert({type: 'danger', message: 'Asunto no es mayor de ' + min + ' Caracteres. '});
                    pass = false;
                } else{
                    asu = asu.replace(/[^\x20-\x7E]+/g, '');
                }              

                if(TIPO_RADICADO == 1) {
                    var max = 510;
                } else {
                    var max = 350;
                }  
                if (asu.length > max) {
                    mostrarAlert({type: 'danger', message: 'Asunto no es mayor de ' + max + ' Caracteres. '});
                    pass = false;
                }
                ;                

                //Email
                var emaile = $('#errormail').val();
                if (emaile==1) {
                    mostrarAlert({type: 'danger', message: 'Error en el correo electrónico ingresado. '});
                    pass = false;
                }
                ;                

                //DIRECCION Ò EMAIL EN UN USUARIO NUEVO
                if (!RADICACION_CIRCULAR) {
                    var inc = 0;
                    $("tr[name='item_usuario']").each(function () {
                        for (var inc = 0; inc < 100; inc++) {
                            if (($("#id_dir_" + inc).val() == '') && ($("#id_ema_" + inc).val() == '')) {
                                mostrarAlert({
                                    type: 'danger',
                                    message: 'El destinatario No. ' + (inc + 1) + ' Le falta Direccion - Si no reporta escribir Desconocida'
                                });
                                pass = false;
                            }
                            if (($("#id_nombre_" + inc).val() == '') && ($("#id_nombre_" + inc).val() == '')) {
                                mostrarAlert({
                                    type: 'danger',
                                    message: 'El destinatario No. ' + (inc + 1) + ' Le falta Nombre - Si no reporta escribir Anonimo'
                                });
                                pass = false;
                            }
                            if (($("#id_muni_" + inc).val() == '') || ($("#id_muni_" + inc).val() == 0) || ($("#id_muni_cod_" + inc).val() == 0)) {
                                mostrarAlert({
                                    type: 'danger',
                                    message: 'El destinatario No. ' + (inc + 1) + ' Le Falta el Municipio'
                                });
                                pass = false;
                            }
                            if (($("#id_dep_" + inc).val() == '') || ($("#id_dep_" + inc).val() == 0) || ($("#id_dep_cod_" + inc).val() == 0)) {
                                mostrarAlert({
                                    type: 'danger',
                                    message: 'El destinatario No. ' + (inc + 1) + ' Le Falta el Departamento'
                                });
                                pass = false;
                            }
                            /*if (($("#id_documen_" + inc).val() == '') || ($("#id_documen_" + inc).val() == 0) || ($("#id_documen_" + inc).val() == 0)) {
                                mostrarAlert({
                                    type: 'danger',
                                    message: 'El destinatario No. ' + (inc + 1) + ' Le Falta el documento - Si no reporta crear un usuario Anonimo'
                                });
                                pass = false;
                            }*/

                            inc++;
                        }
                    });
                }

                //GUIA
                if ($('#guia').length > 0 && $('#guia').val().length > 20) {
                    mostrarAlert({type: 'danger', message: 'Gu&iacute;a con mas de 20 caracteres'});
                    pass = false;
                }

                //REFERENCIA CUENTA_I
                if ($('#cuentai').length > 0 && $('#cuentai').val().length > 20) {
                    mostrarAlert({type: 'danger', message: 'Referencia con mas de 20 caracteres'});
                    pass = false;
                }

                //Dependencia
                if (parseInt($('select[name="coddepe"]').val()) === 0) {
                    mostrarAlert({type: 'danger', message: 'Selecciona una dependencia'});
                    pass = false;
                }

                //SIAD
                if ($('#siad').length > 0 && 
                    $('#siad').val().length > 0 &&
                    $('#siad').val().length < 13) {
                    mostrarAlert({type: 'danger', message: 'SIAD con menos de 13 d&iacute;gitos'});
                    pass = false;
                }

                if(!pass && acction== 'nuevobtnradicar'){
                  $( ".radicarNuevo" ).show();
                }

                if (pass && !EJECUCION) {
                    //Dejar alertas en blanco
                    borrarAlert();
                    EJECUCION = true;
                    var datos = $("form").serialize();
                    var radicado = '';
                    <?php
if (datos) {
    echo "datos = datos + '&$javascriptCapDatos;'";
}
?>
 
                    $('#showRadicar').remove();

                    if (acction === "modificaRad") {
                        datos = datos + "&modificar=true";
                    }

                    if (RADICACION_NOTIFICACION) {
                      <?php if(!empty($notifica_codi)) { ?>
                        datos = datos + "&notifica_codi=<?= $notifica_codi ?>";
                      <?php } ?>
                    }

                    //console.log("datos: ", datos);

                    var jqxhr = $.post("./ajax_radicarNuevo.php", datos, function (data) {
                        for (var k in data) {
                            if (data[k].error !== undefined) {
                                mostrarAlert({type: 'danger', message: data[k].error});
                            } else {
                                if (acction !== "modificaRad") {
                                    radicado = data[k].answer;
                                    $('#modificaRad').append(data[k].answer);
                                    $('#modificaRad').append("<input type=\"hidden\" name=\"nurad\" value=\"" + data[k].answer + "\" />");

                                    $('#idrad').append(data[k].answer);
                                } else {
                                    mostrarAlert({type: 'success', message: data[k].answer});
                                }

                                $('#showModificar').removeClass('hide');
                            }
                        }
                        
                        if (acction !== "modificaRad") {
                            var contentstiker = $('#skeleton').clone().removeClass('hide')[0].outerHTML.replace(/xxxxxx/g, radicado);
                            var contentverrad = $('#skeleton8').clone().removeClass('hide')[0].outerHTML.replace(/xxxxxx/g, radicado);
                            var contentasocia = $('#skeleton9').clone().removeClass('hide')[0].outerHTML.replace(/xxxxxx/g, radicado);
                            var contenttipifica = $('#skeleton10').clone().removeClass('hide')[0].outerHTML.replace(/xxxxxx/g, radicado);
                            var contentsubir_anexos = $('#skeleton11').clone().removeClass('hide')[0].outerHTML.replace(/xxxxxx/g, radicado);
                            $('#sticker').html(contentstiker + contentverrad);
                            $('#asociar').html(contentasocia);
                            $('#tipificar').html(contenttipifica);
                            $('#subir_anexos').html(contentsubir_anexos);
                        }

                        <?if (isset($uid)){//El uid representa una radicadion de email, la siguiente linea permite automatizar la radicacion de emails?>
                        window.parent.filed(radicado,<?=$uid?>);
                        <?}?>

                        $("#inforshow").removeClass('hide');
                        $('#showModificar').removeClass('hide');
                        $('#copyradicar').html($('#showModificar').clone());

                    }).fail(function (err) {
                        var errMsg = 'Error de creación/modificación del radicado. Reporte al administrador código http: ' + err.status;
                        mostrarAlert({type: 'danger', message: errMsg})
                    })

                    EJECUCION = false;
                }

            });

            //No permitir escribir caracteres extraños
            /*$('#nombre_us').keydown(function (e) {
                var key = e.keyCode;
                if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40)
                    || (key >= 65 && key <= 90) || (key == 9) || (key == 16)) || (key == 81)){
                    e.preventDefault();
                }
            });*/

            $('body').on('keypress', '*[data-rel="solo-text"]', function (event) {
                var regex = /^[a-zA-ZáÁéÉíÍóÓúÚñÑ ]+$/;
                var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
                if (!regex.test(key)) {
                    event.preventDefault();
                    return false;
                }
            });

            $('body').on('click', '*[data-rel="remove"]', function(e) {
                $(this).parent('tr.item_usuario').remove();
            });

            //Eliminar usuarios y borrar el campo de seleccionados
            //si no existe ningun usuario
            $("body").on("click", ".search-table-icon", function () {
                $(this).closest('.item_usuario').remove();

                /*var codUser = $(this).parent().remove();
                var tds = $('table').children('tbody').children('tr').length;
                if (tds === 0) {
                    $('#tableSection').addClass('hide');
                }
                ;*/
            });

            /*$('body').on('blur', '*[data-rel="solo-mail"]', function (event) {
                if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test($(this).val()
                ))
                {
                    return (true)
                }
                    alert("You have entered an invalid email address!")
                    return (false)
            });*/

            //No permitir escribir sino numeros
            $('#documento_us').keydown(function (e) {
                var key = e.keyCode;
                if (!( (key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40)
                    || (key >= 48 && key <= 57) || (key >= 96 && key <= 105)
                    || (key == 9)) || (key == 81) || (key == 225) || (key == 16)) {
                    e.preventDefault();
                }
            });

        });
    </script>

</div>
</body>
</html>
