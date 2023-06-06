<?php
session_start();
if(!$ruta_raiz) $ruta_raiz = "..";

include_once $ruta_raiz."/include/tx/sanitize.php";

if (!$_SESSION['dependencia'])
  header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

define('ADODB_ASSOC_CASE', 1);

$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];

$_SESSION['_aux_edit'] = 0;
$_SESSION['frmh_expediente'] = $numExpediente;

if($verrad and !$numExpediente) $numExpediente=$verrad;
$usuaPermExpediente = $_SESSION['usuaPermExpediente'];

if (!$db)
    include_once "$ruta_raiz/conn.php";

include_once ("$ruta_raiz/include/tx/Expediente.php");
include_once ("$ruta_raiz/tx/verLinkArchivo.php");

$expediente = new Expediente($db);

if($verradicado  and !$numRadicado) $numRadicado = $verradicado;

if ($numRadicado){
    $exp = $expediente->consultaExp($numRadicado, $numExpediente);
}

$arrExpedientes = $expediente->expedientes;

include_once "$ruta_raiz/tx/verLinkArchivo.php";

$verLinkArchivo = new verLinkArchivo($db);

?>

<input type="hidden" name="menu_ver_tmp" value=4>
<input type="hidden" name="menu_ver"     value=4>
<?
unset($frm);
if ($numExpediente) $expediente->expedienteArchivado($verrad, $numExpediente);

$isqlDepR = "SELECT
              USUA_DOC_RESPONSABLE,
              SGD_EXP_PRIVADO,
              SGD_SEXP_PAREXP1,
              SGD_SEXP_PAREXP2
            FROM
              SGD_SEXP_SECEXPEDIENTES
            WHERE
              SGD_EXP_NUMERO = '$numExpediente' ORDER BY SGD_SEXP_FECH DESC limit 250 ";

$rsDepR      = $db->conn->Execute($isqlDepR);
$nivelExp    = $rsDepR->fields['SGD_EXP_PRIVADO'];
$docRes      = $rsDepR->fields['USUA_DOC_RESPONSABLE'];
$param1      = substr($rsDepR->fields['SGD_SEXP_PAREXP1'], 0, 30);
$param1      = $param1 . ' ' . substr($rsDepR->fields['SGD_SEXP_PAREXP2'], 0, 50);

$isqlDepR    = "SELECT USUA_NOMB from USUARIO WHERE USUA_DOC = '$docRes'";
$rsDepR      = $db->conn->Execute($isqlDepR);
$responsable = $rsDepR->fields['USUA_NOMB'];

$isql        = "SELECT USUA_PERM_EXPEDIENTE from USUARIO WHERE USUA_LOGIN = '$krd'";
$rs          = $db->conn->Execute($isql);
$krdperm     = $rs->fields['USUA_PERM_EXPEDIENTE'];
$sqlb        = "select sgd_exp_archivo from sgd_exp_expediente
                where sgd_exp_numero like '$num_expediente'";

$rsb     = $db->conn->Execute($sqlb);
$arch    = $rsb->fields['SGD_EXP_ARCHIVO'];
$mostrar = true;

if (!$tsub)
    $tsub = "0";
if (!$tdoc)
    $tdoc = "0";
if (!$codserie)
    $codserie = "0";

//Generamos los enlaces de los formularios de procesos guardados
$sqlconForm = "select initcap(fo.frm_name) as name,
                      fr.frmh_permalink as link,
                      frmh_date as datef
                    from
                      frmh_frmlog fr,
                      frm_forms fo
                    where
                      fr.frm_code   = fo.frm_code and
                      fr.expediente = '$numExpediente'";


$isql = "select
             EXP_ANEX_CREADOR
            ,EXP_ANEX_NOMB_ARCHIVO
            ,EXP_ANEX_RADI_FECH
            ,EXP_ANEX_TAMANO
            ,EXP_ANEX_DESC
        from
            sgd_exp_anexos
        where
            exp_anex_nomb_archivo like '%$numExpediente%'
            and exp_anex_borrado = 'N'";

$anex      = array();
$rs        = $db->conn->Execute($isql);
$depe_dir  = substr($numExpediente,4,$_SESSION['digitosDependencia']);
$uploadDir = substr($numExpediente,0,4)."/".$depe_dir."/docs/";
$linkAne   = '';

while($rs && !$rs->EOF) {
   $anex['exp_anex_creador']      = $rs->fields["EXP_ANEX_CREADOR"];
   $anex['exp_anex_nomb_archivo'] = $rs->fields["EXP_ANEX_NOMB_ARCHIVO"];
   $anex['exp_anex_radi_fech']    = $rs->fields["EXP_ANEX_RADI_FECH"];
   $anex['exp_anex_tamano']       = $rs->fields["EXP_ANEX_TAMANO"];
   $anex['exp_anex_desc']         = $rs->fields["EXP_ANEX_DESC"];
   $name = $uploadDir . $anex['exp_anex_nomb_archivo'];
   $nameb64 = base64_encode($name);

   $linkAne .= "
              <tr>
                   <td>
                       {$anex['exp_anex_creador']}
                   </td>
                   <td>
                       {$anex['exp_anex_radi_fech']}
                   </td>
                   <td>
                       {$anex['exp_anex_tamano']}
                   </td>
                   <td>
                    <a target='_blank' href='#' onclick=\"funlinkAnexo('$nameb64','.');\">{$anex['exp_anex_desc']}</a>
                   </td>
                </tr>";

   $rs->MoveNext();
}


if ($usuaPermExpediente) {
?>
<table style="font-size: 16px;" class="table table-bordered table-striped" colspacing=0 cellspacing=0>
<tr>
  <td width=140>
    <span class="dropdown">
    <a class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
        &nbsp;&nbsp;Expediente&nbsp;&nbsp;<b class="caret"></b> </a>
    <ul class="dropdown-menu">
    <?
    if ($usuaPermExpediente || !$numExpediente) {
        $_SESSION['dataPresExpediente'] =$datosExp;
        $_SESSION['dataPresExpediente']['expediente'] = $numExpediente;
      ?>
      <li>
          <a onClick="insertarExpediente();">Incluir en...</a>
      </li>
      <li>
          <a onClick="excluirExpediente();">Excluir de...</a>
      </li>
      <!--
      <li>
          <a onClick="solicitarExpediente();">Solicitar prestamo...</a>
      </li>
      -->
      <li>
          <a onClick="verTipoExpediente('<?= $numExpediente ?>',<?= $codserie ?>,<?= $tsub ?>,<?= $tdoc ?>,'MODIFICAR');">Crear
              Nuevo Expediente</a>
      </li>
      <li>
          <a onClick="Responsable('<?= $numExpediente ?>');">Cambiar Responsable</a>
      </li>
      <li>
          <a onClick="CambiarE(2,'<?= $numExpediente ?>');">Cerrar Expediente</a>
      </li>
      <li>
          <a onClick="seguridadExp('<?= $numExpediente ?>','<?= $nivelExp ?>');">Seguridad</a>
      </li>
    <? if (!empty($numExpediente)) { ?>
        <li>
        <a href="javascript:void(0);"
            onClick="window.open ('expediente/stickerExp/index.php?numExp=<?= $numExpediente ?>','sticker<?= $nurad ?>','menubar=0,resizable=0,scrollbars=0,width=550,height=580,toolbar=0,location=0');">
            Sticker Expediente</a>
    </li><? }
          }
          ?>
      </ul>
      </span>
    </td>
    <td>
        <small>
            <?= $num_expediente ?>
            <input name="num_expediente" type="hidden" size="30" maxlength="18" id='num_expediente'
                   value="<?= $num_expediente ?>" class="tex_area">
            Cod :  <span class=leidos2> <? echo $param1; ?>
                Responsable: <span class=leidos2> <?= ucwords(strtolower($responsable)) ?></small>
    </td>
    <?
    }

    ?>
    <input type="hidden" name='funExpediente' id='funExpediente' value="">
    <input type="hidden" name='menu_ver_tmp' id='menu_ver_tmp' value="4">
    <?
    // CONSULTA SI EL EXPEDIENTE TIENE UNA CLASIFICACION TRD

    $codserie = "";
    $tsub = "";
    include_once("$ruta_raiz/include/tx/Expediente.php");
    $trdExp          = new Expediente($db);
    $mrdCodigo       = $trdExp->consultaTipoExpediente("$numExpediente");
    $trdExpediente   = $trdExp->descSerie . " / " . $trdExp->descSubSerie;
    $descPExpediente = $trdExp->descTipoExp;
    $procAutomatico  = $trdExpediente->pAutomatico;
    $codserie        = $trdExp->codiSRD;
    $tsub            = $trdExp->codiSBRD;
    $tdoc            = $trdExp->codigoTipoDoc;
    $texp            = $trdExp->codigoTipoExp;
    $descFldExp      = $trdExp->descFldExp;
    $codigoFldExp    = $trdExp->codigoFldExp;

    if (!$codserie)
        $codserie = 0;
    if (!$tsub)
        $tsub     = 0;
    if (!$tdoc)
        $tdoc     = 0;

    $resultadoExp = 0;

    if ($funExpediente == "INSERT_EXP") {
        $resultadoExp = $expediente->insertar_expediente($num_expediente,
            $verrad,
            $dependencia,
            $codusuario,
            $usua_doc);
        if ($resultadoExp == 1) {
            echo '<hr>Se anex&oacute; este radicado al expediente correctamente.<hr>';
        } else {
            echo '<hr><font color=red>No se anex&oacute; este radicado al expediente. V
        Verifique que el numero del expediente exista e intente de nuevo.</font><hr>';
        }
    }

    if ($funExpediente == "CREAR_EXP") {
        $resultadoExp = $expediente->crearExpediente($num_expediente,
            $verrad,
            $dependencia,
            $codusuario,
            $usua_doc);
        if ($resultadoExp == 1) {
            echo '<hr>El expediente se creo correctamente<hr>';
        } else {
            echo '<hr><font color=red>El expediente ya se encuentra creado.
        <br>A continuaci&oacute;n aparece la lista de documentos pertenecientes al expediente que intento crear
        <br>Si esta seguro de incluirlo en este expediente haga click sobre el boton  "Grabar en Expediente"
        </font><hr>';
        }
    }
    // if ($carpeta==8) {
    if ($carpeta == 99998) {
        //<input type="button"0. name="UPDATE_EXP" value="ACTUALIZAR EXPEDIENTE" class="botones_mediano" onClick="Start('buscar_usuario.php?busq_salida=',1024,400);">
    }
    if ($ASOC_EXP and !$funExpediente) {
        for ($ii = 1; $ii < $i; $ii++) {
            $expediente->num_expediente = "";
            $exp_num = $expediente->consulta_exp("$radicados_anexos[$ii]");
            $exp_num = $expediente->num_expediente;

            //echo "===>$exp_num==>".$radicados_anexos[$ii]."<br>";
            if ($exp_num == "") {
                $expediente->insertar_expediente($num_expediente,
                    $radicados_anexos[$ii],
                    $dependencia,
                    $codusuario,
                    $usua_doc);
            }
        }
    }
    ?>
    <?
    if (!$codigoFldExp)
        $codigoFldExp = "0";
    $num_expediente = $numExpediente;

    if ($numExpediente != ""){
    if ($expIncluido != "") {
        $arrTRDExp = $expediente->getTRDExp($expIncluido, "", "", "");
    } else if ($num_expediente != "") {
        $arrTRDExp = $expediente->getTRDExp($num_expediente, "", "", "");
    }
    ?>
<Tr>
    <td>
        <small>Clasificacion D.</small>
    </td>
    <td>
    <small>
        <?php echo ucwords(strtolower($arrTRDExp['serie'])) . " / " . ucwords(strtolower($arrTRDExp['subserie'])); ?>
        <br>
        <?php if ($usuaPermExpediente > 1) { ?>
            <button type="submit" name='edittemasexp_<?= $num_expediente ?>' class="btn btn-primary btn-xs"
                    id="editadorapidoexpediente">Editar ..
            </button>
            <button type='submit' name='savetemasexp_<?= $num_expediente ?>' value="<?= $num_expediente ?>"
                    id="grabadorapidoexpediente" class='btn btn-success  btn-xs' style="display: none;">Grabar ..
            </button>
        <?php } ?>
        <table style="font-size: 16px;">
            <?php
            if ($expIncluido != "") {
                $arrDatosParametro = $expediente->getDatosParamExp($expIncluido, $dependencia);
                //$arrDatosParametro = $expediente->getDatosParamExp($expIncluido, 900);
            } else if ($numExpediente != "") {
                $arrDatosParametro = $expediente->getDatosParamExp($numExpediente, $dependencia);
            }

            if ($arrDatosParametro != "") {
                foreach ($arrDatosParametro as $clave => $datos) {
                  if($clave<5){
                    echo "<tr><td><small><br><b>" . ucwords(strtolower($datos['etiqueta'])) . " : </b><span class='showfield'>" . ucwords(strtolower(htmlentities($datos['parametro']))) . "</span></small></td>
                          <td><input  class='editfield' style='display: none;' type='text' name='etique_"
                        . $numExpediente . "[]'
                          value='" . ucwords(strtolower(htmlentities($datos['parametro']))) . "'></td></tr>";
                  }
                }
            }

            ?>
        </table>
      </small>
    </td>
</tr>
<tr>
    <td>
    <span class="dropdown">

      <a class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
          &nbsp;&nbsp;Procedimiento&nbsp;&nbsp;
          <b class="caret"></b>
      </a>

      <ul class="dropdown-menu">
          <? if ($usuaPermExpediente) { ?>
              <li>
                  <a onClick="verHistExpediente('<?= $numExpediente ?>');">Historial del Proceso/Exp</a>
              </li>
              <!--<li>
                  <?if($entidad != "IGAC") {?><a onClick="verWorkFlow('<?= $numExpediente ?>','<?= $texp ?>');">Ver WorkFlow</a><?}?>
              </li>
              <li>
                  <a onClick="crearProc('<?= $num_expediente ?>');">Adicionar Proceso</a>
              </li>-->
          <? } ?>
      </ul>

    </span>

    </td>
    <td>
        <small>
            <?php
            if ($arrTRDExp['proceso'] != "") {
                echo $arrTRDExp['proceso'] . " / " . $arrTRDExp['terminoProceso'];
            }
            ?></small>

    </td>
</tr>

<?
$aristasSig = "";
$frm = "";
if ($descPExpediente) {
    $expediente->consultaTipoExpediente($num_expediente);
    include_once("$ruta_raiz/include/tx/Flujo.php");
    $objFlujo = new Flujo($db, $texp, $usua_doc);

    $kk = $objFlujo->getArista($texp, $codigoFldExp);
    $aristasSig = $objFlujo->aristasSig;
    $frm = array();
    $iA = 0;
    $ventana = "Default";
    if ($aristasSig) {
        unset($frm);
        $frm = array();
        $frms = 0;
        foreach ($aristasSig as $key => $arista) {
            if (trim($arista["FRM_NOMBRE"]) && trim($arista["FRM_LINK"])) {
                $ventana     = "Max";
                $vartochange = $aristasSig[$key]["FRM_LINKSELECT"];
                $frm[$iA]["FRM_NOMBRE"]     = $arista["FRM_NOMBRE"];
                $vartochange = str_replace("{numeroRadicado}"   , "$numRad"          , $vartochange);
                $vartochange = str_replace("{numeroExpediente}" , "$num_expediente"  , $vartochange);
                $vartochange = str_replace("{dependencia}"      , "$dependencia"     , $vartochange);
                $vartochange = str_replace("{documentoUsuario}" , "$usua_doc"        , $vartochange);
                $vartochange = str_replace("{usuarioDoc}"       , "$usua_doc"        , $vartochange);
                $vartochange = str_replace("{nombreUsuario}"    , "$usua_nomb"       , $vartochange);
                $frm[$iA]["FRM_LINK"] = './'.$arista["FRM_LINK"].'&'.$vartochange ;
                $iA++;
                $frms = 1;
            }
        } // Fin si hay Aristas....
    }
}
?>
<tr>
    <td>
  <span class="dropdown">
  <a class="btn btn-xs btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
      &nbsp;&nbsp;Estado&nbsp;&nbsp; <b class="caret"></b>
  </a>
    <ul class="dropdown-menu dropdown-menu-large row">
            <?

            if ($usuaPermExpediente) {
                ?>
            <li>
                    <a onClick="verHistExpediente('<?= $num_expediente ?>');"></a>
                </li>
                  <!--<  <li>
                    <a onClick="crearProc('<?= $num_expediente ?>');">Adicionar Proceso</a>
                </li>-->

                <li>
                    <a onClick="seguridadExp('<?= $num_expediente ?>','<?= $nivelExp ?>');">Seguridad</a>
                </li>
            <? } ?>
            <li>
                <a onClick="modFlujo('<?= $num_expediente ?>',<?= $texp ?>,<?= $codigoFldExp ?>,'<?= $ventana ?>')">Modificar
                    Estado</a>
            </li>
        </ul>
  </span>
    </Td>
    <td>
      <? if(!empty($restulh)){ ?>
        <span class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown">
            <small> _Formularios_ </small>
            <b class="caret"></b>
          </a>
          <ul class="dropdown-menu dropdown-menu-large row">
              <?= $restulh ?>
          </ul>
        </span>
      <?php
      }

      if ($frms == 1){ ?>
        <span class="dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown">
                <small><?= ucwords(strtolower($descFldExp)) ?></small>
                <b class="caret"></b>
          </a>
          <ul class="dropdown-menu dropdown-menu-large row">
              <?  foreach ($frm as $arista) { ?>
                  <li>
                      <a onClick="window.open('<?= $arista["FRM_LINK"] ?>','frm<?= date('ymdhis') ?>','fullscreen=yes, scrollbars=auto')"><?= trim($arista["FRM_NOMBRE"]) ?>
                      </a>
                  </li>
              <?php } ?>
          </ul>
        </span>
      <?php
      } else {
          ?>
          <small><?= ucwords(strtolower($descFldExp)) ?></small>
      <? } ?>
    </td>
</tr>
<tr>
  <td>
    <small>
        Fecha Inicio
    </small>
  </td>
  <td>
      <small><?php print $arrTRDExp['fecha']; ?></small>
  </td>
</tr>
<tr>
  <td>
    <small>
        <a class="btn btn-xs btn-primary dropdown-toggle"
            onClick="crearAnexoExpediente('<?= $numExpediente ?>');">
            Anexar Documento
        </a>
    </small>
  </td>
  <td>
    <table class="table" style='font-size:10px;'>
        <tr>
            <th>Usuario</th>
            <th>Fecha de creación</th>
            <th>Tamaño en Bytes</th>
            <th>Nombre</th>
        </tr>
        <?=$linkAne?>
    </table>
  </td>
</tr>
</table>
</td>
</tr>
<tr>
 <td colspan="2">
  <?php } ?>
  <table width="100%" align=left colspacing=0 cellspacing=0>
    <tr>
      <td>
      <?  include "$ruta_raiz/expediente/expedienteTree.php"; ?>
      </td>
    </tr>
  </table>
