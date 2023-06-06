<?php
/**
* @author Jairo Losada   <jlosada@gmail.com>
* @author Correlibre.org // Tomado de version orginal realizada por JL en SSPD, modificado.
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
* 
* @copyleft

OrfeoGpl / Version Argo Models are the data definition of Argo Information System
Copyright (C) 2017 Correlibre Fundacion.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Fou@copyrightndation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

session_start();
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
if (!$_SESSION['dependencia']) header ("Location: $ruta_raiz/cerrar_session.php");
$ruta_raiz= "..";
include $ruta_raiz."/processConfig.php";
include $ruta_raiz."/alertas/alertasTipoRadicadoConsolidado.php";
include $ruta_raiz."/alertas/alertasRadEntrada.php";
?>

<div class="bs-example">
    <div style="text-align: right;" onClick="$('#msgAlertas').toggle();">x</div>
    <div class="panel-group" id="accordion" >
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseAOne">Documentos con falta de tr&aacute;mite</a>
                </h4>
            </div>
            <div id="collapseAOne" class="panel-collapse collapse">
                <div class="panel-body" style="overflow:auto; height:300px;">
                    <p>
                      <?php
                        foreach($arrAlerta2 as $key => $value){
                          if(!$arrAlerta2[$key]["anexEstado"] || $arrAlerta2[$key]["anexEstado"]<=1) $imgEstado = "./imagenes/docRecibido.gif";
                          if($arrAlerta2[$key]["anexEstado"]==2) $imgEstado = "./imagenes/docRadicado.gif";
                          if($arrAlerta2[$key]["anexEstado"]==3) $imgEstado = "./imagenes/docImpreso.gif";
                          if($arrAlerta2[$key]["anexEstado"]==4) $imgEstado = "./imagenes/docEnviado.gif"; 
                          $linkVerrad = " <a href='verradicado.php?nurad=$key' target='mainFrame'> ".$arrAlerta2[$key]["radiFech"]."</a> ";
                          echo  "<img src='$imgEstado'> ".$key."-$linkVerrad- ".$arrAlerta2[$key]["dias"]." Dias -".$arrAlerta2[$key]["usuaLogin"]."<br>";
                        }
                      ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseATwo">Documentos que deben ser tramitados hoy</a>
                </h4>
            </div>
            <div id="collapseATwo" class="panel-collapse collapse">
                 <div class="panel-body" style="overflow:auto; height:300px;">
                    <p>
                      <?php
                      if (is_array($arrAlerta1)){
                        foreach($arrAlerta1 as $key => $value){
                          if(!$arrAlerta1[$key]["anexEstado"] || $arrAlerta1[$key]["anexEstado"]<=1) $imgEstado = "./imagenes/docRecibido.gif";
                          if($arrAlerta1[$key]["anexEstado"]==2) $imgEstado = "./imagenes/docRadicado.gif";
                          if($arrAlerta1[$key]["anexEstado"]==3) $imgEstado = "./imagenes/docImpreso.gif";
                          if($arrAlerta1[$key]["anexEstado"]==4) $imgEstado = "./imagenes/docEnviado.gif"; 
                      
                          $linkVerrad = " <a href='verradicado.php?nurad=$key' target='mainFrame'> ".$arrAlerta1[$key]["radiFech"]."</a> ";
                          echo  "<img src='$imgEstado'> ".$key."-$linkVerrad - - ".$arrAlerta1[$key]["usuaLogin"]."<br>";
                        }
                      }
                      ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" >
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseAThreee">Documentos aún en tr&aacute;mite</a>
                </h4>
            </div>
            <div id="collapseAThreee" class="panel-collapse collapse">
                <div class="panel-body" style="overflow:auto; height:300px;">
                    <p>
                      <?php
                      if ($arrAlerta3){
                        foreach($arrAlerta3 as $key => $value){
                          if(!$arrAlerta3[$key]["anexEstado"] || $arrAlerta3[$key]["anexEstado"]<=1) $imgEstado = "./imagenes/docRecibido.gif";
                          if($arrAlerta3[$key]["anexEstado"]==2) $imgEstado = "./imagenes/docRadicado.gif";
                          if($arrAlerta3[$key]["anexEstado"]==3) $imgEstado = "./imagenes/docImpreso.gif";
                          if($arrAlerta3[$key]["anexEstado"]==4) $imgEstado = "./imagenes/docEnviado.gif"; 

                          $linkVerrad = " <a href='verradicado.php?nurad=$key' target='mainFrame'> ".$arrAlerta3[$key]["radiFech"]."</a> ";
                          echo  "<img src='$imgEstado'> ".$key."-$linkVerrad - ".$arrAlerta3[$key]["usuaLogin"]."<br>";
                        }
                        }
                      ?>
                    </p>
                </div>
            </div>
        </div>
         <div class="panel panel-default">
            <div class="panel-heading" >
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseAThree">Documentos incompletos</a>
                </h4>
            </div>
            <div id="collapseAThree" class="panel-collapse collapse in">
                <div class="panel-body" style="overflow:auto; height:400px;">
                   <canvas id="alertasChart" width="600" height="500" class="panel-title"></canvas>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" >
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThreeb">Estado de Documentos en Bandeja</a>
                </h4>
            </div>
            <div id="collapseThreeb" class="panel-collapse collapse">
                <div class="panel-body" style="overflow:auto; height:300px;">
                   <canvas id="chartRadicadosEstado" width="300" height="200" class="panel-title" style="font-size:14px;"></canvas>
                </div>
            </div>
        </div>
    </div> 
  <p><strong>Nota:</strong> Los c&aacute;lculos son realizados seg&uacute;n la parametrizaci&oacute;n de alertas, la fecha de radicaci&oacute;n y d&iacute;as h&aacute;biles preestablecidos. Los d&iacute;as que han pasado luego de su vencimiento para terminar el tr&aacute;mite se calculan en d&iacute;as Calendario.</p>
</div>
<script>
var oilCanvas = document.getElementById("alertasChart");

Chart.defaults.global.defaultFontFamily = "Lato";
Chart.defaults.global.defaultFontSize = 18;

var alertasRadicados = {
    labels: [
        "Vencimientos Hoy",
        "Vencidos",
        "Ok"
    ],
    datasets: [
        {
            data: [<?=$countAlerta1?>, <?=$countAlerta2?>, <?=$countAlerta3?>],
            backgroundColor: [
                "#FF6384",
                "#630084",
                "#84FF63"
            ]
        }]
};

var pieChart = new Chart(oilCanvas, {
  type: 'pie',
  data: alertasRadicados,
  options: {
  legend: {
      display: true,
      labels: {
          fontSize: 14
      }
  }
 }
});


<?php

 echo $charRadicadosEstado;
?>
</script>
