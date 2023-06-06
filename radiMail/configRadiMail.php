<?php
session_start();
$ruta_raiz="../";
if (!$_SESSION['dependencia'])
	header ("Location: $ruta_raiz/cerrar_session.php");

if ($_SESSION["usua_perm_rademail"]>=1)
	die ("No tiene permisos para ingresar a este módulo.");

if (!extension_loaded('imap')){
	die('La extensión imap no está cargada...');
}

include ("../dbconfig.php");
include_once('../processConfig.php');
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);

define('ADODB_ASSOC_CASE', 2);
$ADODB_FETCH_MODE = 2;
$db = NewADOConnection($driver);
$db->Connect($servidor, $usuario, $contrasena, $servicio);

define('PROJECT_PATH',__DIR__);
define('SMARTY_TEMPLATES',PROJECT_PATH.'/tpl');
define('SMARTY_TMP',$ruta_raiz.'/bodega/tmp');
define('SMARTY_CACHE',PROJECT_PATH.'/cache');
define('SMARTY_CONFIG',PROJECT_PATH.'/configs');
define("RADIMAIL_PAGINATION",100);

require_once (SMARTY_LIBRARY);
$smarty=new Smarty;
$smarty->template_dir=SMARTY_TEMPLATES;
$smarty->compile_dir=SMARTY_TMP;
$smarty->cache_dir=SMARTY_CACHE;
$smarty->config_dir=SMARTY_CONFIG;

$usua_email=$_SESSION["usua_email"];
if ($_SESSION["passwd_mail"]) $passwd_mail=$_SESSION["passwd_mail"];

switch ($server_name){
	case "gmail":
		/****Configuración para Gmail, (autenticación SSL)****/
		$hostname = '{imap.gmail.com:993/imap/ssl}';
        break;

	case "exchange":
		/***Configuración para Exchange sin autenticación SSL**/
		$hostname = '{'."$servidor_mail:$puerto_mail/novalidate-cert".'}';
		$usua_email = current(explode ("@",$usua_email));
        break;

	case "outlook":
		/****Configuración para Outlook, (autenticación SSL)****/
		$hostname = '{outlook.office365.com:993/imap/ssl}';
        break;

    default:
       $hostname = $server_name;
}

$codusuario  = $_SESSION["codusuario"];
$dependencia = $_SESSION["dependencia"];
$entidad	 = $_SESSION["entidad"];
