<?php

$root = realpath(__DIR__ . '/..');
require "$root/dbconfig.php";
$bodega = "$root/bodega";
$year = date('Y');
$yearBorrador = date('Y') + 1000;
$dirs = [
  'tmp', 'sys_img', 'fax', 'masiva', 'pdfs/guias', 'pdfs/planillas/dev',
  'pdfs/planillas/envios', 'plantillas/genericas', 'tmp/workDir/cacheODT',
  'tmp/radimail/imgs', "$year/formFiles", 'firmas/grafo'
];

is_writable($bodega) or die("No existe o no tiene permisos bodega\n");
$dbconn = pg_connect("host=$servidor dbname=$servicio user=$usuario password=$contrasena")
  or die('Could not connect: ' . pg_last_error());

$query = 'SELECT depe_codi FROM dependencia';
$result = pg_query($query) or die('Query failed: ' . pg_last_error());

while ($row = pg_fetch_assoc($result)) {
  $dirs[] = "$year/{$row['depe_codi']}/docs";
  $dirs[] = "$year/{$row['depe_codi']}/exp";
  $dirs[] = "$yearBorrador/{$row['depe_codi']}/docs";
  $dirs[] = "$yearBorrador/{$row['depe_codi']}/exp";
}

foreach ($dirs as $dir) {
  @mkdir("$bodega/$dir", 0755, true);
}

$conf = [
  'ABSOL_PATH' => "$root/",
  'ADODB_PATH' => "$root/include/class/adodb",
  'SMARTY_DIR' => "$root/include/Smarty/libs/",
  'SMARTY_LIBRARY' => "$root/include/Smarty/libs/Smarty.class.php",
  'PEAR_PATH' => "$root/pear/",
  'CONTENT_PATH' => "$root/bodega/",
  'ruta_owonclod' => "$root/owncloud/data/",
  'servidorBirt' => '',
  'entidad_largo' => 'ZIBA ASESORIAS Y ASISTENCIAS GENERALES S.A.S.',
  'entidad' => 'ZIBA',
  'entidad_dir' => '',
  'entidad_tel' => '',
  'headerRtaPdf' => '/sys_img/SNS.headerPDF.png',
  'footerRtaPdf' => '/sys_img/SNS.footerPDF.png',
  'favicon' => '/sys_img/favicon.ico',
  'logoEntidad' => '/sys_img/cc.png',
  'url_ayuda' => 'https://gitlab.com/sep1983/argogpl/-/wikis/',
  'colorFondo' => '#0406DE',
  'correoSalienteRR' => '',
  'passwordCorreoSaliente' => '',
  'passwordCorreoSalienteRR' => '',
  'apiFirmaDigital' => 'false',
  'P12_PASS' => '123',
  'conf_certificadoPassword' => '',
  'conf_certificadoPassword' => '',
];

foreach ($conf as $k => $v) {
  $query = "UPDATE sgd_config SET conf_valor='$v' WHERE conf_nombre='$k'";
  $result = pg_query($query) or die('Query failed: ' . pg_last_error());
}

pg_free_result($result);
pg_close($dbconn);
