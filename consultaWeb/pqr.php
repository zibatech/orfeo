<?php
require '../vendor/autoload.php';
use Carbon\Carbon;
error_reporting(1);
$ruta_raiz = "../";

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
require_once($ruta_raiz."include/db/ConnectionHandler.php");
if (!$db){
    $db = new ConnectionHandler($ruta_raiz);
}
$db = ADONewConnection('mssqlnative');
$db->SetFetchMode(ADODB_FETCH_ASSOC);
$db->connect('10.161.91.191','steven.hernandez','Super.2019','GestionPqrd');
//var_dump($db);
$idRadicado = trim(strtoupper($idRadicado));
$sql = "
    SELECT
        PQR.IdPQR,
        PQR.NRORADICACION,
        PQR.FECHARADICACION,
        PQR.IdCanal,
        ORI.nom_origen,
        PQR.IdEntidadCausante,
        PQRDatosAfectado.IdCiudad,
        PQRDatosAfectado.PrimerNombre,
        PQRDatosAfectado.SegundoNombre,
        PQRDatosAfectado.PrimerApellido,
        PQRDatosAfectado.SegundoApellido,
        PQRDatosAfectado.IdTipoIdentificacion,
        PQRDatosAfectado.Identificacion,
        LC.nombre as Lugar,
        LP.nombre as Pertenece,
        AdminEntidadVigilada.AliasSNS,
        AdminSubMotivo.SubMotivo
    FROM
        PQRSNS.PQR
        LEFT JOIN PQRSNS.PQRDatosAfectado ON PQR.IdPQR = PQRDatosAfectado.IdPQR
        LEFT JOIN PQRSNS.PQRClasificacionSalud ON PQR.IdPQR = PQRClasificacionSalud.IdPQR
        LEFT JOIN PQRSNS.AdminSubMotivo ON PQRClasificacionSalud.IdSubMotivo = AdminSubMotivo.IdSubMotivo
        LEFT JOIN PQRSNS.AdminEntidadVigilada ON PQR.IdEntidadCausante = AdminEntidadVigilada.IdEntidad
        LEFT JOIN dbo.LOCALIZACION AS LC ON PQRDatosAfectado.IdCiudad = LC.id_localizacion
        LEFT JOIN dbo.LOCALIZACION AS LP ON LC.id_localizacion_pertenece = LP.id_localizacion
        LEFT JOIN dbo.ORIGEN AS ORI ON PQR.IdCanal = ORI.id_origen
    WHERE
        ( NroRadicacionSistemaExterno = '$idRadicado' OR NroRadicacionInicial = '$idRadicado' OR 'NroRadicacion' = '$idRadicado' ) 
        AND PQRDatosAfectado.Identificacion = '$id' AND PQRDatosAfectado.IdTipoIdentificacion = '$tipo'";
$data = $db->getRow($sql);
//var_dump($data);

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://pqrd.supersalud.gov.co/TMS.Solution.TMSPQRD/Pqrd/ListarSeguimientoCiudadano",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "NoRadicacion=".$data['NRORADICACION'],
        CURLOPT_COOKIE => "n=1; cookiesession1=5FD311C3VN9HKX038H3G1YJ6VVN341EE",
        CURLOPT_HTTPHEADER => [
            "Accept: */*",
            "Accept-Language: es-ES,es;q=0.9,en;q=0.8",
            "Connection: keep-alive",
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
            "Cookie: _ga=GA1.3.1261388709.1606058349; Proveedor=LDAP; n=1; _gid=GA1.3.152234913.1608125182; cookiesession1=5FD311C3NBCKMB30FBS48Q09LEKD3450",
            "Origin: https://pqrd.supersalud.gov.co",
            "Referer: https://pqrd.supersalud.gov.co/TMS.Solution.TMSPQRD/pqrd/portalciudadano",
            "Sec-Fetch-Dest: empty",
            "Sec-Fetch-Mode: cors",
            "Sec-Fetch-Site: same-origin",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36",
            "X-Requested-With: XMLHttpRequest",
            "sec-ch-ua: \"Google Chrome\";v=\"87\", \" Not;A Brand\";v=\"99\", \"Chromium\";v=\"87\"",
            "sec-ch-ua-mobile: ?0"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $seguimientos = json_decode($response, true);
    }

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://pqrd.supersalud.gov.co/TMS.Solution.TMSPQRD/PqrdAnexo/ListarCiudadano",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "id=".$data['IDPQR'],
        CURLOPT_COOKIE => "n=1; cookiesession1=5FD311C3VN9HKX038H3G1YJ6VVN341EE",
        CURLOPT_HTTPHEADER => [
            "Accept: */*",
            "Accept-Language: es-ES,es;q=0.9,en;q=0.8",
            "Connection: keep-alive",
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
            "Cookie: _ga=GA1.3.1261388709.1606058349; Proveedor=LDAP; _gid=GA1.3.152234913.1608125182; n=1; cookiesession1=5FD311C3HESBBHDIDM0BU2MTUKSP0F8E",
            "Origin: https://pqrd.supersalud.gov.co",
            "Referer: https://pqrd.supersalud.gov.co/TMS.Solution.TMSPQRD/pqrd/portalciudadano",
            "Sec-Fetch-Dest: empty",
            "Sec-Fetch-Mode: cors",
            "Sec-Fetch-Site: same-origin",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36",
            "X-Requested-With: XMLHttpRequest",
            "sec-ch-ua: \"Google Chrome\";v=\"87\", \" Not;A Brand\";v=\"99\", \"Chromium\";v=\"87\"",
            "sec-ch-ua-mobile: ?0"
        ]
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $expedientes = json_decode($response, true);
    }
    curl_close($curl);
    
?>
<!DOCTYPE html>
<html>
    <head>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js" integrity="sha512-RdSPYh1WA6BF0RhpisYJVYkOyTzK4HwofJ3Q7ivt/jkpW6Vc8AurL1R+4AUcvn9IwEKAPm/fk7qFZW3OuiUDeg==" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    </head>

    <body>
        <div id="pqr" data-val="<?= $data['NRORADICACION'] ?>" class="container">
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
            </div>
            <div class="col-sm-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">PQR</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="true">Detalles del caso</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab3-tab" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="true">Datos del afectado</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab4-tab" data-toggle="tab" href="#tab4" role="tab" aria-controls="tab4" aria-selected="true">Seguimiento al caso</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="tab5-tab" data-toggle="tab" href="#tab5" role="tab" aria-controls="tab5" aria-selected="true">Expediente</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <br>
                            </div>
                            <div class="col-md-12">
                                <h4>Información correspondiente al estado de la solicitud</h4>
                            </div>
                            <div class="col-md-12">
                                <br>
                            </div>
                            <div class="col-md-12">
                                <h6><?= $data['NRORADICACION'] ?></h6>
                            </div>
                            <div class="col-md-12">
                                Radicado el día <?= substr($data['FECHARADICACION'], 0, 10) ?> a la(s) <?= substr($data['FECHARADICACION'], 11, 5) ?>
                                <br>
                                Canal: <?= $data['NOM_ORIGEN'] ?>
                            </div>
                            <div class="col-md-12">
                                <br>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info" role="alert">
                                    Estado: <strong><?= $data['PQR_ESTADO'] || '?' ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade show" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <br>
                            </div>
                            <div class="col-md-12">
                                <h4>Detalle del caso</h4>
                            </div>
                            <div class="col-md-12">
                                <br>
                            </div>
                            <div class="col-md-12">
                                Entidad vigilada: <?= $data['ALIASSNS'] ?>
                                <br>
                                Lugar: <?= $data['LUGAR'].' / '.$data['PERTENECE'] ?>
                                <br><br>
                                Descripción: <?= $data['SUBMOTIVO'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade show" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <br>
                            </div>
                            <div class="col-md-12">
                                <h4>Información del afectado</h4>
                            </div>
                            <div class="col-md-12">
                                <br>
                            </div>
                            <div class="col-md-12">
                                Nombre: <?= $data['PRIMERNOMBRE'].' '.$data['SEGUNDONOMBRE'].' '.$data['PRIMERAPELLIDO'].' '.$data['SEGUNDOAPELLIDO']?>
                                <br>
                                <?php
                                    $tipo = '';
                                    switch($data['IDTIPOIDENTIFICACION'])
                                    {
                                        case "CC":
                                            $tipo = "Cédula de ciudadania";
                                        break;
                                        case "CE":
                                            $tipo = "Cédula de extranjeria";
                                        break;
                                        case "MS":
                                            $tipo = "Menor sin identificación";
                                        break;
                                        case "NIT":
                                            $tipo = "NIT";
                                        break;
                                        case "PA":
                                            $tipo = "Pasaporte";
                                        break;
                                        case "PE":
                                            $tipo = "Permiso especial de permanencia";
                                        break;
                                        case "RC":
                                            $tipo = "Registro civil";
                                        break;
                                        case "TI":
                                            $tipo = "Tarjeta de identidad";
                                        break;
                                    }
                                ?>
                                <?= $tipo.' '.$data['IDENTIFICACION'] ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade show" id="tab4" role="tabpanel" aria-labelledby="tab4-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <br>
                            </div>
                            <div class="col-md-12">
                                <h4>Seguimientos</h4>
                            </div>
                            <div class="col-md-12">
                                <br>
                            </div>
                            <div class="col-md-12">
                                <ul class="list-group">
                                    <?php foreach($seguimientos['Data'] as $seguimiento): ?>
                                        <li class="list-group-item">
                                            <?php 
                                                $time = preg_replace('/[^0-9]/', '', $seguimiento['Fecha']);
                                                $date = Carbon::createFromTimestampMs($time);
                                                
                                            ?>
                                            <small><?= $date->format('Y/m/d H:i') ?></small>
                                            <p>
                                                <?= $seguimiento['Comentario'] ?>
                                            </p>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade show" id="tab5" role="tabpanel" aria-labelledby="tab5-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <br>
                            </div>
                            <div class="col-md-12">
                                <h4>Expedientes</h4>
                            </div>
                            <div class="col-md-12">
                                <br>
                            </div>
                            <div class="col-md-12">
                                <ul class="list-group">
                                    <?php foreach($expedientes['Data'] as $expediente): ?>
                                        <li class="list-group-item">
                                            <?php 
                                                $time = preg_replace('/[^0-9]/', '', $expediente['Fecha']);
                                                $date = Carbon::createFromTimestampMs($time);
                                                
                                            ?>
                                            <small><?= $date->format('Y/m/d H:i') ?></small>
                                            <p>
                                                <a href="#" onclick="cargarExpediente('<?=$expediente['Id']?>')"><?= $expediente['Nombre'] ?></a>
                                                <br>
                                                <small><strong>autor: </strong> <?= $expediente['Usuario'] ?></small>
                                            </p>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function cargarExpediente(id) {
                if (!id) {
                    swal(
                        'Acceso denegado',
                        'No tiene los permisos necesarios para ver este archivo',
                        'error'
                    );
                    return;
                }
                var dialog = bootbox.dialog({
                    title: 'Anexo',
                    message: '<iframe style="height:600px; width:100%; border:0;" src="https://pqrd.supersalud.gov.co/TMS.Solution.TMSPQRD/DocumentoAnexo/Ver/' + id + '"></iframe>',
                    onEscape: true,
                    closeButton: true,
                    size: 'large'
                });
            }
        </script>
    </body>
</html>