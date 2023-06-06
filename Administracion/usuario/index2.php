<?php
session_start();


$ruta_raiz = "../..";

if (!$_SESSION['dependencia'])
  header ("Location: ".$ruta_raiz."/cerrar_session.php");

$krd          = (isset($_SESSION["krd"]))? $_SESSION["krd"] : '';
$verrad       = '';
$krd          = $_SESSION["krd"];
$dependencia  = $_SESSION["dependencia"];
$usua_doc     = $_SESSION["usua_doc"];
$codusuario   = $_SESSION["codusuario"];

include_once("./libs/Smarty.class.php");
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once("$ruta_raiz/include/tx/roles.php");

$smarty = new Smarty;

$smarty->template_dir = './templates';
$smarty->compile_dir  = './templates_c';
$smarty->config_dir   = './configs/';
$smarty->cache_dir    = './cache/';

$smarty->left_delimiter  = '<-{';
$smarty->right_delimiter = '}->';

$smarty->display('userRolProfile.tpl'); 
