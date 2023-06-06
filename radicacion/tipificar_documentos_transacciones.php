<?php
/**
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @author Jairo Losada   <jlosada@gmail.com>
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright

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

if (!$ruta_raiz) $ruta_raiz= "..";
$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$nurad       = $_GET["nurad"];

if($_GET["codserie"]) $codserie = $_GET["codserie"];
if($_GET["idSerie"]) $codserie  = $_GET["idSerie"];
if($_GET["coddepe"]) $coddepe   = $_GET["coddepe"];

if($_GET["tsub"]) $tsub       = $_GET["tsub"];
if($_GET["idSubSerie"]) $tsub = $_GET["idSubSerie"];
if($_GET["tdoc"]) $tdoc       = $_GET["tdoc"];
if($_GET["insertar_registro"]) $insertar_registro = $_GET["insertar_registro"];
if($_GET["actualizar"]) $actualizar = $_GET["actualizar"];

if($_GET["borrar"]) $borrar           = $_GET["borrar"];
if($_GET["linkarchivo"]) $linkarchivo = $_GET["linkarchivo"];
if($_GET["codiTRDEli"]) $codiTRDEli   = $_GET["codiTRDEli"];

include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once("$ruta_raiz/include/query/busqueda/busquedaPiloto1.php");
include_once("$ruta_raiz/include/tx/Historico.php");
include_once("$ruta_raiz/class_control/TipoDocumental.php");
include_once "$ruta_raiz/include/tx/Tx.php";

$db = new ConnectionHandler("$ruta_raiz");
$trd = new TipoDocumental($db);

if ($borrar){
    $sqlE = "SELECT $radi_nume_radi as RADI_NUME_RADI
        FROM SGD_RDF_RETDOCF r
        WHERE RADI_NUME_RADI = '$nurad'
        AND  SGD_MRD_CODIGO =  '$codiTRDEli'";

    $rsE=$db->conn->query($sqlE);
    $i=0;
    while(!$rsE->EOF){
        $codiRegE[$i] = $rsE->fields['RADI_NUME_RADI'];
        $i++;
        $rsE->MoveNext();
    }
    $TRD = $codiTRDEli;
    include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";

    $nrobs = $codiRegE[0];

    //TRAIGO EL RADICADO PADRE
    $_isql ="select radi_nume_deri from radicado where radi_nume_radi = $nrobs";
    $_rsisql =$db->conn->query($_isql);
    $Rad_padre = intval($_rsisql->fields['RADI_NUME_DERI']);

    if($Rad_padre > 0){
        $_Tx_id = 98;
        $observa = "*Eliminado TRD Anexo *".$deta_serie."/".$deta_subserie."/".$deta_tipodocu ." - ".$nrobs;
        $codiRegE[0]=$Rad_padre;
    }else{
        $_Tx_id = 33;
        $observa = "*Eliminado TRD*".$deta_serie."/".$deta_subserie."/".$deta_tipodocu;
    }

    $Historico = new Historico($db);

    $radiModi = $Historico->insertarHistorico($codiRegE, $dependencia, $codusuario, $dependencia, $codusuario, $observa, $_Tx_id);
    $radicados = $trd->eliminarTRD($nurad,$coddepe,$usua_doc,$codusua,$codiTRDEli);
    $mensaje="TRD eliminada<br> ";
}

if ($actualizar){
    if (!empty((int)$tdoc * (int)$tsub *(int)$codserie)){

        $sqlH = "SELECT $radi_nume_radi as RADI_NUME_RADI,
            SGD_MRD_CODIGO
            FROM SGD_RDF_RETDOCF r
            WHERE RADI_NUME_RADI = '$nurad'
            AND  DEPE_CODI       =  '$coddepe'";
        $rsH=$db->conn->query($sqlH);
        $codiActu = $rsH->fields['SGD_MRD_CODIGO'];

        while(!$rsH->EOF){
            $codiRegH = Tx::recursiveAnex(array($rsH->fields['RADI_NUME_RADI']), $db);
            $rsH->MoveNext();
        }

        $TRD = $codiActu;
        $mensaje="El Registro NO Puede Modificarse por Pertenecer a otra dependencia   <br> ";
        if ($TRD != ''){
            include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";
            $observa = "*Modificado TRD* ".$deta_serie."/".$deta_subserie."/".$deta_tipodocu;
            $Historico = new Historico($db);
            $radiModi = $Historico->insertarHistorico($codiRegH, $dependencia, $codusuario, $dependencia, $codusuario, $observa, 32);
            $radiUp = $trd->actualizarTRD($codiRegH,$tdoc);
            $mensaje="Registro Modificado";

            $isqlTRD = "select SGD_MRD_CODIGO
                from SGD_MRD_MATRIRD
                where
                    SGD_SRD_ID = '$codserie'
                and SGD_SBRD_ID = '$tsub'
                and SGD_TPR_CODIGO = '$tdoc'";

            $rsTRD = $db->conn->Execute($isqlTRD);
            $codiTRDU = $rsTRD->fields['SGD_MRD_CODIGO'];
            $sqlUA = "UPDATE SGD_RDF_RETDOCF SET SGD_MRD_CODIGO = '$codiTRDU',
                USUA_CODI = '$codusuario'
                WHERE RADI_NUME_RADI = '$nurad' AND  DEPE_CODI =  '$coddepe'";
            $rsUp = $db->conn->query($sqlUA);
            $mensaje="Registro Modificado   <br> ";
        }

    }

}
$tdoc = '';
$tsub = '';
$codserie = '';
?>
<html>
<head>
    <title>..:: Clasificar Documento ::..</title>
    <?php
    include $ruta_raiz."/htmlheader.inc.php";
    ?>
</head>
<body>
<div class="col-sm-12">
    <!-- widget grid -->
    <section id="widget-grid">
        <!-- row -->
        <div class="row">
          <!-- NEW WIDGET START -->
          <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">
              <header>
                <h2>
                   Actualización TRD
                </h2>
              </header>
              <!-- widget content -->
              <div class="widget-body">
                <div style="padding-top:30px" class="panel-body" >
                    <?=$mensaje?>
                </div>
                <div class="input-group">
                    <input  type='button'
                            value='   Cerrar   '
                            class='form-control'
                            onclick='opener.regresar();window.close();'>
                </div>
              </div>
          </article>
        </div>
    </section>
</div>
</body>
</html>
