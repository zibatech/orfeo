<?php
session_start();
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
$ruta_raiz="../";
include ("$ruta_raiz/processConfig.php");
include ("funcionesIMAP.php");
$usua_email=!isset($_SESSION['usua_email_1'])?$usua_email:$_SESSION['usua_email_1'];
$passwd_mail = $_SESSION["passwd_mail"];
$hostname = '{'."$servidor_mail:$puerto_mail/$protocolo_mail/ssl".'}';
require_once('configRadiMail.php');
$inbox = imap_open($hostname,$usua_email,$passwd_mail) or die(imap_last_error()); 
downloadAttachment($inbox, $uid, $part, $enc, $path);
