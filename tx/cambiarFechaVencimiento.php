<?php
/**
* @author Jairo Losada   <jlosada@gmail.com>
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
if($_POST['radicado_a_buscar']){$radicados_a_buscar = $_POST['radicado_a_buscar'];}

$ruta_raiz = "..";
if (!$_SESSION['dependencia'])
header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

define('ADODB_ASSOC_CASE', 1);
$verrad         = "";
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
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
  //  $db->conn->debug = true;

$radi_fech_vcmto=$_GET["radi_fech_vcmto"];
$newFechaVencimiento=$_GET["newFechaVencimiento"];
$numRad=$_GET["numRad"];

?>
<!DOCTYPE html>
<head>
  <title>Sistema de informaci&oacute;n <?=$entidad_largo?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
</head>

<body>
<form method="GET" action="cambiarFechaVencimiento.php" >

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
                    Nueva Fecha de Vencimiento
                </h2>
              </header>
              <!-- widget content -->
              <div class="widget-body">
                  <div style="padding-top:30px" class="panel-body" >
                        <div class="form-group">
                            <label>Fecha de vencimiento:</label>
                            <input  id="newFechaVencimiento"
                                    name="newFechaVencimiento"
                                    type="text"
                                    class="form-control"
                                    data-provide="datepicker"
                                    data-date-format="YYYY/MM/DD"
                                    data-date-end-date="0d" value="<?=$radi_fech_vcmto;?>">
                        </div>

                        <div class="form-group">

                            <input  type="hidden" name=numRad
                                    value="<?=$numRad?>">

                            <input type="hidden" id="my_hidden_input">

                            <input name="Cerrar"
                                    type="button"
                                    class="btn btn-default"
                                    id="envia22"
                                    onClick="opener.regresar(); window.close();"
                                    value="Cerrar">
                            <input type=submit
                                  value="Cambiar Fecha de Vencimiento"
                                  name="cambiarFecha"
                                  class="btn btn-primary">

                        </div>
                  </div>
              </div>
            </div>
          </article>
        </div>
    </section>
</div>

<script>
    $('#newFechaVencimiento').datepicker({
        dateFormat: 'yy-mm-dd',
        minDate: 0,
        format: {
            /*
             * Say our UI should display a week ahead,
             * but textbox should store the actual date.
             * This is useful if we need UI to select local dates,
             * but store in UTC
             */
            toDisplay: function (date, format, language) {
                var d = new Date('<?=$radi_fech_vcmto?>');
                d.setDate(d.getDate() - 7);
                return d.toISOString();
            },
            toValue: function (date, format, language) {
                var d = new Date('<?=$radi_fech_vcmto?>');
                d.setDate(d.getDate() + 7);
                return new Date(d);
            }
        },
        autoclose: true
    });

    $("#newFechaVencimiento").datepicker( "setDate" , "<?=$radi_fech_vcmto?>" );

    <?php
    if($newFechaVencimiento && $numRad && $cambiarFecha){
        $iSql = "UPDATE
                    radicado
                 set fech_vcmto='$newFechaVencimiento'
                 where radi_nume_radi=$numRad";
        $db->conn->query($iSql);
        echo "opener.regresar(); window.close();";
    }
    ?>

</script>
</form>
</body>
</html>
