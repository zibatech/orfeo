<?php

$ruta_raiz = "../";

session_start();
//require $ruta_raiz.'kint.phar';
require_once($ruta_raiz."include/db/ConnectionHandler.php");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$params = session_name()."=".session_id()."&krd=$krd";
$file = $ruta_raiz.'/bodega/supercore/'.$num.'.pdf';

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}