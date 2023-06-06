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

if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

$ruta_raiz = "..";

$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];

foreach ($_GET as $key => $valor)   ${$key} = $valor;

require_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);

function return_bytes($val){
    $val = trim($val);
    $ultimo = strtolower($val{strlen($val)-1});
    switch($ultimo){
        // El modificador 'G' se encuentra disponible desde PHP 5.1.0
    case 'g':	$val *= 1024;
    case 'm':	$val *= 1024;
    case 'k':	$val *= 1024;
    }
    return $val;
}
?>

<html>

<head>
<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/cogeinsas.css">
<?php include_once "$ruta_raiz/js/funtionImage.php"; ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?=$ruta_raiz?>/estilos/bootstrap.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="<?=$ruta_raiz?>/radiMail/css/smartadmin-production-plugins.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="<?=$ruta_raiz?>/radiMail/css/smartadmin-skins.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="<?=$ruta_raiz?>/radiMail/css/smartadmin-rtl.min.css">
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
<script src="<?=$ruta_raiz?>/js/plugin/dropzone/dropzone.min.js"></script>
</head>

<body>
    <?php
    $varBuscada = "RADI_NUME_RADI";
    $valRadioArr = explode(",", $valRadio);
    ?>

    <div class="jarviswidget jarviswidget-color-blueLight" id="wid-id-0" data-widget-editbutton="false">
            <header>
                <h2>Documentos para ser anexados al Expediente no <b>
                    <?=$numeroExpediente?></b>
                </h2>
            </header>
            <!-- widget div-->
            <div>
            <!-- widget edit box -->
            <div class="jarviswidget-editbox">
                <!-- This area used as dropdown edit box -->
            </div>
            <!-- end widget edit box --><!-- widget content -->
            <div class="widget-body">

                <form action="uploadAnexExp.php?<?=$encabezado?>" class="dropzone" id="mydropzone" enctype="multipart/form-data">
                    <input type='hidden' name=noExpediente value='<?=$numeroExpediente?>'>
                </form>

                <div class=listado2 colspan="2">
                    <center>
                        <input  class="btn btn-primary btn-sm"
                                type="button"
                                onclick="opener.location.reload(); top.close();"
                                value="Cerrar" />
                    </center>
                </div>

            </div>
            <!-- end widget content -->
            </div>
            <!-- end widget div -->
        </div>
        <!-- end widget -->
    </body>
    <script>
        $( document ).ready(function() {
          // Now that the DOM is fully loaded, create the dropzone, and setup the
          // event listeners
          var myDropzone = new Dropzone("#mydropzone");

          myDropzone.on("success", function(file, response) {
              if(response.length > 0){
                alert(response);
              }
          });
        });
    </script>
</html>
