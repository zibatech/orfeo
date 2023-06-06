<?php
session_start();

$ruta_raiz = "../..";

if (!$_SESSION['dependencia'])
  header ("Location: ".$ruta_raiz."/cerrar_session.php");

if (!$_SESSION["usua_admin_sistema"])
  header ("Location: ".$ruta_raiz."/cerrar_session.php");

$krd          = (isset($_SESSION["krd"]))? $_SESSION["krd"] : '';
$verrad       = '';
$krd          = $_SESSION["krd"];
$dependencia  = $_SESSION["dependencia"];
$usua_doc     = $_SESSION["usua_doc"];
$codusuario   = $_SESSION["codusuario"];
$grupo_adm_sel = $_POST["grupo_adm"];
$grupo_adm = $_POST["grupo_adm"];
if(!$grupo_adm) $grupo_adm=900;
include_once("$ruta_raiz/include/Smarty/libs/Smarty.class.php");
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once("$ruta_raiz/include/tx/roles.php");
include $ruta_raiz . "/config.php";
include $ruta_raiz."/htmlheader.inc.php";
$smarty = new Smarty;

$smarty->template_dir = './templates';
$smarty->compile_dir  = $ruta_raiz.'/bodega/tmp';
$smarty->config_dir   = './configs/';

$smarty->left_delimiter  = '<-{';
$smarty->right_delimiter = '}->';

$db    = new ConnectionHandler($ruta_raiz);
$roles = new Roles($db);//print_r($usuarios);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug = true;

//Traemos los permisos
if($roles->retornarPermisos()){
    $permisos = $roles->permisos;
}


//Traemos las opciones sobre los permisos
$crud = $roles->retornarOpcionesPermisos();


//Traemos los grupos
if($roles->retornarGrupos()){
    $grupos = $roles->grupos;
}

//Traemos los Usuarios
if($roles->retornarUsuariosRoles(false,false, $grupo_adm)){
   $usuarios = $roles->usuarios;
}

//Traemos las Dependencias
if($roles->retornarDependencias()){
    $dependencias = $roles->dependencias;
}

//Traemos las Membresias
if($roles->retornarMembresias()){
    $membresias = $roles->membresias;
}
$smarty->assign("grupo_adm"   , $grupo_adm);
$smarty->assign("permisos"     , $permisos);
$smarty->assign("crud"         , $crud);
//Roles
$smarty->assign("grupos"       , $grupos);
$smarty->assign("dependencias" , $dependencias);

$smarty->assign("membresias"   , $membresias);
//Perfiles
$smarty->assign("usuarios"     , $usuarios);


//ASIGNAMOS A SMARTY LOS PERMISOS DEL USUARIO
$Less_edita_usuario = (int) $_SESSION['USUA_LESS_PERM_USER'];
$Less_edita_profile = (int)$_SESSION['USUA_LESS_PERM_USER_PROFILE'];
if ($_SESSION["usua_admin_sistema"]!=1) {$Perm_solo_usuario = $_SESSION['USUA_PERM_ONLY_USER'];}else {$Perm_solo_usuario = 0;}
$smarty->assign("Perm_solo_usuario"   , $Perm_solo_usuario);
$smarty->assign("Less_edita_usuario"       , $Less_edita_usuario);
$smarty->assign("Less_edita_profile" , $Less_edita_profile);

$smarty->display('index.tpl');
