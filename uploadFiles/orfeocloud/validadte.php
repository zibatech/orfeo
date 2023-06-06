<?php
/** 
 * Validatde
 * @author Hardy Deimont Niño  Velasquez
 * Este scripit realiza validacion de session de usuario.
 * @version	1.0
 */

//validar cookie

if($out){
if ($_COOKIE ['krd']) {
//	echo 1;
	//die ();
	if (!$_SESSION ['krd']) {
		include_once "$ruta_raiz/core/clases/usuarioOrfeo.php";
		$usuarioObj = new usuarioOrfeo ( $ruta_raiz );
		$usuarioObj->setLogin ( $_COOKIE ['krd'] );
		$usuarioObj->consultar_usuario ();
		//echo 2;
		//	echo session_id()."!=".$usuarioObj->getUsua_session();
		/*echo $_COOKIE ['PHPSESSID'] ."!=". $usuarioObj->getUsua_session ();
		die();*/
		
		if ($_COOKIE ['PHPSESSID'] != $usuarioObj->getUsua_session ()) {
			$mensajeCierre = 'La Sesi&oacute;n ha Terminado Se ha conectado  en otro equipo';
			include_once "../../cerrar_session.php";
			die ();
		
		}
		else {

			//recupera la session
			$variables = explode ( ';', $_COOKIE ['ORF4'] );
			
			for($i = 0; $i <= count ( $variables ); $i ++) {
				$d = explode ( '=', $variables [$i] );
				if($d [1]!='Array'){
				  $_SESSION [$d [0]] = $d [1];
				}
			}	
						include_once $ruta_raiz . '/core/clases/SessionOrfeo.php';
			$sessionOrfeo = new sessionOrfeo ( $ruta_raiz );
			$sessionOrfeo->setDepecodi ( $_SESSION['dependecia'] );
	///		$sessionOrfeo->setRol ( $usuario->getRol() );
			$sessionOrfeo->traerDatos ();
						$_SESSION ["tpNumRad"] = $sessionOrfeo->getTpNumRad ();
			$_SESSION ["tpDescRad"] = $sessionOrfeo->getTpDescRad ();
			$_SESSION ["tpImgRad"] = $sessionOrfeo->getTpImgRad ();
			$_SESSION ["tip3Nombre"] = $sessionOrfeo->getTip3Nombre ();
			$_SESSION ["tip3desc"] = $sessionOrfeo->getTip3desc ();
			$_SESSION ["tip3img"] = $sessionOrfeo->getTip3img;
		
		}
	}
} else {
	$mensajeCierre = 'La Session ha Terminado';
	$marco = 1;
	include_once "$ruta_raiz/core/vista/cerrar_session.php";
	die ();
}
}
?>
