<?php
include ("$ruta_raiz/processConfig.php");
switch ($server_name){
	case "gmail":
		/****Configuración para Gmail, (autenticación SSL)****/
		$hostname = '{'."$servidor_mail:$puerto_mail/$protocolo_mail/ssl".'}';
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
}
?>
