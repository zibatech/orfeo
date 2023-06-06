<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function img($login, $text) {
    $font = './arial.ttf';
    $w = 384;
    $h = 70;
    $im = imagecreatetruecolor ($w, $h);
    $white = imagecolorallocate ($im, 255, 255, 255);
    $black = imagecolorallocate ($im, 0, 0, 0);
    $border_color = imagecolorallocate ($im, 50, 50, 50);

    imagefilledrectangle($im,0,0,$w-1,$h-1,$white);
    //imagerectangle($im,0,0,$w-1,$h-1,$border_color);
    imagettftext ($im, 8, 0, 10, 20, $black, $font,$text);

    imagepng ($im,"bodega/firmas/grafo/$login.png");
}

require 'dbconfig.php';
$dbconn = pg_connect("host=$servidor dbname=$servicio user=$usuario password=$contrasena")
    or die('Could not connect: ' . pg_last_error());
$query = 'select u.usua_login, u.usua_nomb, d.depe_nomb from usuario u left join dependencia d on d.depe_codi = u.depe_codi';
$ret = pg_query($query) or die('Query failed: ' . pg_last_error());
while($row = pg_fetch_array($ret, NULL, PGSQL_ASSOC)) {
    echo $row['usua_login'], PHP_EOL;
    echo $row['usua_nomb'], PHP_EOL;
//     echo $row['depe_nomb'], PHP_EOL;
    img(strtolower($row['usua_login']),
        "Firmado electrónicamente por:\n{$row['usua_nomb']}");//\nDependencia: {$row['depe_nomb']}");
}
pg_close($dbconn);
