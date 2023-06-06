<?php
/**
 * @module index_frame
 *
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright

 SIIM2 Models are the data definition of SIIM2 Information System
 Copyright (C) 2013 Infometrika Ltda.

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

session_start();

$ruta_raiz   = "..";
if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

if (!$_SESSION["usua_admin_sistema"])
    //header ("Location: $ruta_raiz/index.php");

$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];

$coddepe = isset($_POST['coddepe'])? $_POST['coddepe'] : null;
$codusua = isset($_POST['codusua'])? $_POST['codusua'] : null;
$nurad   = isset($_POST['nurad'])? $_POST['nurad'] : null;
$desc   = isset($_POST['desc'])? $_POST['desc'] : null;

include ("$ruta_raiz/include/db/ConnectionHandler.php");
include "$ruta_raiz/include/tx/Tx.php";

$db = new ConnectionHandler($ruta_raiz);
$tx = new Tx($db);

$query  = "
    SELECT d.DEPE_CODI || ' - ' || d.DEPE_NOMB,
        d.DEPE_CODI
    FROM
    DEPENDENCIA d
    where
        d.depe_estado = '1'
    ORDER BY d.DEPE_CODI, d.DEPE_NOMB";

$rs1 = $db->conn->Execute($query);

$depselect = $rs1->GetMenu2("coddepe",
    $coddepe,
    "0:-- Seleccione una Dependencia --",
    false,
    "",
    "onChange='submit()'  class='form-control'" );

if($coddepe){
    $query  = "SELECT
            d.USUA_NOMB || '-' || d.USUA_LOGIN,
            d.USUA_CODI
        FROM
          usuario d
        where
          d.usua_esta = '1'
        and d.depe_codi = {$_POST['coddepe']}
        ORDER BY d.USUA_NOMB";

    $rs = $db->conn->Execute($query);

    $ususelect = $rs->GetMenu2("codusua",
        $codusua,
        "0:-- Seleccione un Usuario --",
        false,
        "",
        "onChange='submit()'  class='form-control'" );
}

$arryRad = array_filter(explode(",",trim($nurad)));

if( isset($coddepe) &&
    isset($codusua) &&
    isset($arryRad) &&
    isset($desc) &&
    !empty($arryRad)){

        $observa = "Reasignado por $krd ".(isset($desc)?" Motivo: $desc":'');
        $usCodDestino = $tx->reasignar( $arryRad,
        $krd,
        $coddepe,
        $dependencia,
        $codusua,
        $codusuario,
        1,
        $observa,
        9,
        0,
        false);

        $success = "<div class='alert alert-success'>Acción realizada</div>";
}


?>
<html>
  <head>
  <title>Reasignar documentos</title>
  <?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
  </head>

<body>
  <form action=''  name="FrmBuscar" class=celdaGris method="POST">
    <div class="col-sm-12">
      <!-- widget grid -->
      <h2></h2>
      <section id="widget-grid">
          <!-- NEW WIDGET START -->
          <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">
              <header>
                <h2>
                  Traslado de documentos <br>
                </h2>
              </header>
              <!-- widget div-->
              <div>
                <!-- widget content -->
                <div class="widget-body">
                    <center><span><p><h4>  <strong> NOTA:</strong> Puede cambiar radicados de un usuario a otro y desarchivar documentos </h4> </p></span></center>
                  <div class="table-responsive">
                    <table class="table">
                      <tr>
                        <td width="25%" height="49">Dependencia destino</td>
                        <td width="55%">
                            <?=$depselect?>
                        </td>
                      </tr>

                      <?php if($coddepe){?>
                      <tr>
                        <td width="25%" height="49">Usuario Destino</td>
                        <td width="55%">
                          <?=$ususelect?>
                        </td>
                      </tr>

                      <?php }
                      if($codusua){ ?>

                      <tr>
                        <td width="25%" height="49">Numero de Radicados sepradados por ,</td>
                        <td width="55%">
                          <input type='text' name=nurad class='form-control' id=nurad>
                        </td>
                      </tr>

                      <tr>
                        <td width="25%" height="49">Motivo</td>
                        <td width="55%">
                          <input type='text' name=desc class='form-control' id=desc>
                        </td>
                      </tr>

                      <tr>
                        <td width="25%" height="49"></td>
                        <td width="55%">
                            <input
                            type="submit"
                            name="Trasladar"
                            class="btn btn-success"
                            value="Trasladar"/>
                        </td>
                      </tr>

                    <?}?>
                    </table>

                    <?=$success?>

                  </div>
              </div>
            </div>
          </div>
        </article>
    </section>
  </div>
</form>
</center>
</body>
</html>
