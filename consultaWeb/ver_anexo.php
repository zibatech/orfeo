<?php

$ruta_raiz = "..";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");

$sql = 'select radi_path from radicado where radi_nume_radi = ?';
$val = array($_GET['rad']);
$file = '../bodega/' . $db->conn->GetOne($sql, $val);

$sql = 'select visto from consultaweb_visto where radicado  = ?';
$val = array($_GET['rad']);
$visto = $db->conn->GetOne($sql, $val);
if ($visto >= 3) {
?>
<html>
<head>
  <link rel="stylesheet" type="text/css" media="screen" href="../estilos/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" media="screen" href="../estilos/font-awesome.css">
<link rel="stylesheet" type="text/css" media="screen" href="../estilos/smartadmin-production.css">
</head>
<body>
<div class="row" style="padding:10px; margin-bottom: 10px;">
    <div class="col-md-6" style="text-align:left;">
        <a href="http://supersalud.gov.co/" target="_blank">
            <img src="./images/supersalud.png" style="margin:0;" height="100" align="middle">
        </a>
    </div>
    <div class="col-md-6" style="text-align:right;">
        <a href="https://www.minsalud.gov.co/" target="_blank">
            <img src="./images/minsalud.png" style="margin-top:28px;" height="57" align="middle">
        </a>
    </div>
</div>
<div class="row" style="padding:100px;">
<div class="panel panel-default">
  <div class="panel-heading">Error</div>
  <div class="panel-body">
    Por favor comuniquese con la entidad para poder realizar la descarga
  </div>
</div>
</div>
</body>
</html>
<?php
    exit;
}

if (!file_exists($file)) die("Archivo no encontrado");

header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header("Content-Length: " . filesize($file));
header("Content-Type: application/octet-stream;");
readfile($file);

$sql = 'insert into consultaweb_hist (fecha,ip,radicado) values (now(),?,?)';
$val = array($_SERVER['REMOTE_ADDR'],$_GET['rad']);
$rs = $db->conn->Execute($sql,$val);

if (!$visto) {
    $sql = 'insert into consultaweb_visto (radicado,visto) values (?,1)';
}
else {
    $sql = 'update consultaweb_visto set visto=visto+1 where radicado = ?';
}
$val = array($_GET['rad']);
$rs = $db->conn->Execute($sql,$val);
