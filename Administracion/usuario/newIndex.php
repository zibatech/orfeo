<?php
session_start();
$ruta_raiz = "../..";

define('SMARTY_SYSPLUGINS_DIR', 'libs/smarty/smarty-3.1.30/libs/sysplugins/');

if (!$_SESSION['dependencia'])
  header ("Location: ".$ruta_raiz."/cerrar_session.php");

$krd          = (isset($_SESSION["krd"]))? $_SESSION["krd"] : '';
$verrad       = '';
$krd          = $_SESSION["krd"];
$dependencia  = $_SESSION["dependencia"];
$usua_doc     = $_SESSION["usua_doc"];
$codusuario   = $_SESSION["codusuario"];

//include_once("./libs/Smarty.class.php");
require "./libs/smarty/smarty-3.1.30/libs/Smarty.class.php"
;
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once("$ruta_raiz/include/tx/roles.php");
require_once "AbstractModel.php";
require_once "$ruta_raiz/classSanitize.php";

$smarty = new Smarty;

$smarty->template_dir = './templates';
$smarty->compile_dir  = './templates_c';
$smarty->config_dir   = './configs/';
$smarty->cache_dir    = './cache/';

$db    = new ConnectionHandler($ruta_raiz);
$roles = new Roles($db);
$abstract = new AbstractModel($db); 
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug = true;

//Traemos los permisos
#if($roles->retornarPermisos()){
#    $permisos = $roles->permisos;
#}


//Traemos las opciones sobre los permisos
#$crud = $roles->retornarOpcionesPermisos();


//Traemos los grupos
#if($roles->retornarGrupos()){
#    $grupos = $roles->grupos;
#}


//Traemos los Usuarios
#if($roles->retornarUsuarios()){
#   $usuarios = $roles->usuarios;
#}

//Traemos los Dependencias
#if($roles->retornarDependencias()){
#    $dependencias = $roles->dependencias;
#}

//Traemos las Membresias
#if($roles->retornarMembresias()){
#    $membresias = $roles->membresias;
#}

#$smarty->assign("permisos"     , $permisos);
#$smarty->assign("crud"         , $crud);
//Roles
#$smarty->assign("grupos"       , $grupos);
#$smarty->assign("dependencias" , $dependencias);
#$smarty->assign("membresias"   , $membresias);
//Perfiles
#$smarty->assign("usuarios"     , $usuarios);


	#Indicamos que campos deseamos, en que orden, con que alias y bajo que condiciones //d.depe_codi || ' - ' ||d.depe_nomb
	$dep = $db->concat( $db->concat('d.depe_codi'," ' - ' "), 'd.depe_nomb') ; 
	$campos = ["u.usua_codi as codigo","u.id as id", "u.usua_login as Login","u.usua_esta as Estado",$dep." as Dependencia","u.usua_nuevo as Nuevo",
	"u.usua_nomb as Nombre","u.usua_doc as Documento","u.usua_email as Correo"];
	$where = ['d.depe_codi'=>'u.depe_codi'];
	$order = "id desc";

	//Le pedimos al modelo todas las encuestas que cumplan los criterios 
	$titulousuario = $abstract->getAliasFields($campos);

	$campos = array_reverse($campos);
	#Poner esta contraseña en el config
	$sql_usuario = $abstract->encr($abstract->getStringSQL($campos,$where,null,'usuario u, dependencia d'),'Kerpq12'); 
	$num_values = count($titulousuario)-1;



//ASIGNAMOS LAS VARIABLES
#$sql_usuario = 'SELECT usua_login, usua_nuevo, usua_nomb, usua_doc, usua_email, usua_esta  FROM usuario';
#$sql_permiso = '';
#$sql_rol = '';


//Asignamos a smarty 
$smarty->assign("sql_usuario" , $sql_usuario);
$smarty->assign("sql_permiso" , $sql_permiso);
$smarty->assign("sql_rol"     , $sql_rol);

#Variables importantes para la paginacion 
$smarty->assign("sql_initial"     , $sql_usuario);
$smarty->assign("num_values"      , $num_values);

$smarty->assign("titulousuario"   , $titulousuario);

//Requerimos el htmlheader
require "../../htmlheader.inc.php";
//Incluimos clases que no hacen partes del modulo



$smarty->display('userRolProfile.tpl'); 

