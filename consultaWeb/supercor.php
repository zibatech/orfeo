<?php

$ruta_raiz = "../";

session_start();
require_once($ruta_raiz."include/db/ConnectionHandler.php");

if (!$db){
    $db = new ConnectionHandler($ruta_raiz);
}

$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

//include ("common.php");
$fechah = date("ymd") . "_" . time("hms");

$params = session_name()."=".session_id()."&krd=$krd";
$url = "http://10.161.80.155:9999/SuperCor-WS/SuperCorWS?wsdl";

$client = new SoapClient($url, array("trace" => 1, "exception" => 0));
$result = null;
$t = substr($idRadicado, 0, 1);
$tipo = $t == '2' ? 'ConsultaNumeroRadSalida' : 'ConsultaNumeroRadicacion';

$p = [
    'validador' => "BPM",
    'operador' => "9999",
    'rad_Numero' => $idRadicado,
    'dependencia' => ''
];

$result = $client->__soapCall($tipo, ['parameters' => $p]);

$data = $client->__soapCall('ConsultarImagenRadicado', ['parameters' => $p]);

if (!file_exists($ruta_raiz.'/bodega/supercore')) {
    mkdir($ruta_raiz.'/bodega/supercore', 0777, true);
}

if(strlen($data->return)> 10) {
    $decoded = base64_decode($data->return);
    $file = $ruta_raiz.'/bodega/supercore/'.$idRadicado.'.pdf';
    file_put_contents($file, $decoded);
}
?>
<!DOCTYPE html>
<html>
    <head>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    </head>

    <body>
        <div class="container">
            <div class="col-sm-12">
                <div class="row justify-content-between" style="margin-top:10px;">
                    <div class="col-4" style="text-align:left;">
                        <a href="http://www.supersalud.gov.co/" target="_blank">
                            <img src="./images/supersalud.png" height=100 style="margin:0;" align="center">
                        </a>
                    </div>
                    <div class="col-4" style="text-align:right;">
                        <a href="https://www.minsalud.gov.co/" target="_blank">
                            <img src="./images/minsalud.png" height=57 style="margin-top:28px;" align="center">
                        </a>
                    </div>
                </div>
                <?php if($result) { ?>
                    <div class="row">
                        <section id="widget-grid">
                            <div class="col-md-12">
                                <article>
                                    <div class="jarviswidget jarviswidget-color-darken" id="wid-id-2" data-widget-editbutton="false">
                                            <div class="row">
                                                <div class="col-md-12">            
                                                    <h4>
                                                        Resultados
                                                    </h4>
                                                </div>
                                                <?php if($result->return->codigoAccion !== ' ') { ?>
                                                    <div class="col-md-12">
                                                        Sin resultados (<?=$result->return->codigoAccion?>)
                                                    </div>
                                                <?php } else { ?>
                                                    <?php
                                                        $fechaRadicacion = DateTime::createFromFormat('Y-m-d\TH:i:sP', $result->return->fechaRadicacionRadicado);
                                                    ?>
                                                    <div class="col-md-12">
                                                        <h5>Radicado Nº <?=$result->return->numeroRadicado?> 
                                                            <small>
                                                                <?php if(strlen($data->return)> 10) { ?>
                                                                    <a href="supercord_fdl.php?<?=$params?>&num=<?=$idRadicado?>"><i class="icon-download-alt"></i>DESCARGAR</a>
                                                                <?php } ?>
                                                            </small>
                                                        </h5><br>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label for=""><strong>Fecha radicación:</strong></label>
                                                        <p><small><?=$fechaRadicacion->format('Y-m-d H:i:s')?>&nbsp;</small></p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label for=""><strong>Dependencia remitente:</strong></label>
                                                        <p><small><?=$result->return->nombreDependenciaRemitente?>&nbsp;</small></p>
                                                    </div>
                                                    <?php if($t != '2') { ?>
                                                        <div class="col-md-12">
                                                            <label for=""><strong>Dependencia destino:</strong></label>
                                                            <p><small><?=$result->return->nombreDependenciaDestino?>&nbsp;</small></p>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="col-md-12">
                                                            <label for=""><strong>Entidad destino:</strong></label>
                                                            <p><small><?=$result->return->nombreEntidadDestino?>&nbsp;</small></p>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="col-md-12">
                                                        <label for=""><strong>Estado:</strong></label>
                                                        <p><small><?=$result->return->estadoRadicado?>&nbsp;</small></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for=""><strong>Remitente:</strong></label>
                                                        <p><small><?=$result->return->nombreRemitente?>&nbsp;</small></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for=""><strong>Destino:</strong></label>
                                                        <p><small><?=$result->return->nombreDestino?>&nbsp;</small></p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label for=""><strong>Asunto:</strong></label>
                                                        <p><small><?=$result->return->asuntoRadicado?></small></p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label for=""><strong>Observaciones:</strong></label>
                                                        <p><small><?=$result->return->observacionesRadicado?>&nbsp;</small></p>
                                                    </div>
                                                    <?php if($t != '2') { ?>
                                                        <div class="col-md-6">
                                                            <label for=""><strong>Cedula remitente:</strong></label>
                                                            <p><small><?=$result->return->cedulaRemitente?>&nbsp;</small></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for=""><strong>Cedula destino:</strong></label>
                                                            <p><small><?=$result->return->cedulaDestino?>&nbsp;</small></p>
                                                        </div>
                                                    <?php } else { ?>
                                                        <div class="col-md-6">
                                                            <label for=""><strong>Dirección destino:</strong></label>
                                                            <p><small><?=$result->return->direccionDestino?>&nbsp;</small></p>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </section>
                    </div>
                <?php } ?>
            </div>
        </div>
        <script>
            $(function(){
                $('#limpiar').click(function(){
                    $(':input','#formSeleccion')
                        .not(':button, :submit, :reset, :hidden')
                        .val('')
                        .removeAttr('checked')
                        .removeAttr('selected');
                });
            });
        </script>
    </body>
</html>