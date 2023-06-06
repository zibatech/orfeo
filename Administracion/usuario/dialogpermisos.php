<?php
session_start();


$ruta_raiz = "../..";

if (!$_SESSION['dependencia'])
    header ("Location: ".$ruta_raiz."/cerrar_session.php");

include_once("$ruta_raiz/processConfig.php");
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
require (SMARTY_DIR.'Smarty.class.php');
include_once("$ruta_raiz/include/tx/roles.php");

$smarty = new Smarty;

$smarty->template_dir = './templates';
$smarty->compile_dir  = '../../bodega/tmp';
$smarty->config_dir   = './configs/';
$smarty->cache_dir    = './cache/';

$smarty->left_delimiter  = '<-{';
$smarty->right_delimiter = '}->';

$db    = new ConnectionHandler($ruta_raiz);
$roles = new Roles($db);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$db->conn->debug = false;

$iduser = $_GET['id'];

//Traemos las opciones sobre los permisos
$crud = $roles->retornarOpcionesPermisos();

//Traemos los permisos
if($roles->listadoDePermisosPorUsuario($iduser)){
    $permisos = $roles->permisosUsuario;
}

$smarty->assign("permisos" , $permisos);
$smarty->assign("crud"     , $crud);
$smarty->display('dialogpermisos.tpl');
