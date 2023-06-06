<?php
require_once("configRadiMail.php");
extract($_REQUEST,EXTR_OVERWRITE);

$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];

include_once "htmlheader.inc.php";
$error=$_SESSION["RADIMAIL"]["ERROR"];
if($passwd_mail and !$error) {
	if (!$page) $page=1;
	$_SESSION["passwd_mail"]=$passwd_mail;
	$inbox=$_SESSION['inbox'];
	if (!$inbox){
	  $inbox = imap_open($hostname,$usua_email,$passwd_mail, OP_READONLY, 1,array('DISABLE_AUTHENTICATOR' => 'GSSAPI'));
		$_SESSION['inbox']=$inbox;
	  $error = imap_last_error();
	  $_SESSION["RADIMAIL"]["ERROR"]=$error;
	}
	if(!$index and $error){
	 header('Location: index.php');
	}else{
         $smarty->display('index.tpl');
	}
}else{
	$logo="$ruta_raiz/img/$entidad.favicon.png";
	if (!file_exists($logo)){
		$logo="$ruta_raiz/img/favicon.png";
	}
	$username=current(explode('@',$usua_email));
	$smarty->assign('username',$username);
	$smarty->assign('email',$usua_email);
	$smarty->assign('error',$_SESSION['RADIMAIL']['ERROR']);
	$smarty->assign('logo',$logo);
	$smarty->display('lock.tpl');
	unset($_SESSION['RADIMAIL']['ERROR']);
}
function error($error){
	$_SESSION["RADIMAIL"]["ERROR"]=$error;
	header('Location: index.php');
}

imap_close ($inbox, 0 );
