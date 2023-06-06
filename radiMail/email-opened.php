<?php
session_start();
$ruta_raiz="../";
include ("$ruta_raiz/processConfig.php");
if (!$_SESSION['dependencia'])
	die ("<center>Sesion terminada, vuelve a iniciar sesion <a href='../cerrar_session.php'>aqui.<a></center>");
$nurad=$_GET['nurad'];
$usua_email=!isset($_SESSION['usua_email_1'])?$usua_email:$_SESSION['usua_email_1'];
$passwd_mail = $_SESSION["passwd_mail"];
$codusuario  = $_SESSION["codusuario"];
$dependencia = $_SESSION["dependencia"];
$uid=$_REQUEST["uid"];
require_once('configRadiMail.php');
include ("funcionesIMAP.php");
if($uid){
	$inbox = imap_open($hostname,$usua_email,$passwd_mail) or die(imap_last_error()); 
	// start define  headers
	$msgNo=imap_msgno($inbox,$uid);
	$head = imap_header($inbox,$msgNo);
	$email=$head->from[0]->mailbox."@".$head->from[0]->host;
	$name=imap_utf8($head->from[0]->personal);
	$date=$head->date;
	$date=new DateTime($date);
	date_default_timezone_set('America/Bogota');
	$date=date("Y-m-d h:iA", $date->format('U'));
	
	$subject=imap_utf8($head->subject);
	// end define  headers
	$msgNo=imap_msgno($inbox,$uid);
	$structure =  imap_fetchstructure($inbox,$msgNo);
	json_encode($structure);
	$section = getSection($structure);
	//echo getMimetype($structure);
	//var_dump($_SESSION);
	//$charset = getCharset($section,$structure);
	$msgNo=imap_msgno($inbox,$uid);
	//$body = getBody($inbox,$msgNo,$section,$charset); 
	$body = getBody($uid,$inbox); 
	$attachments = getAttachments($inbox, $msgNo, $structure, "");
	foreach ($attachments as $attachment) {
		$listaAdjuntos.= "<a href='downloadAttachment.php?func=$func&folder=$folder&uid=$uid&part=".$attachment["partNum"]."&enc=".$attachment["enc"]."' target='_blank'>".imap_utf8($attachment["name"])."</a><br>";
	}
	if (empty($nurad) or !isset($nurad)){
		$buttonFiled='<button class="btn btn-primary btn-sm replythis"><i class="fa fa-reply"></i> Radicar</button>';
		include "mensaje.php";
	}
	else{
		include_once "../include/db/ConnectionHandler.php";
		$db = new ConnectionHandler("$ruta_raiz");
		include "$ruta_raiz/include/tx/Historico.php";
		$hist = new Historico($db);
		$codusuario = $_SESSION["codusuario"];
		$dependencia = $_SESSION["dependencia"];
		$krd = $_SESSION["krd"];
		$nurads[] = $nurad;
		$codTx = 42;
		unset($listaAdjuntos);
		foreach ($attachments as $attachment) {
			$anex=fileAdttachments($db,$nurad,$krd,imap_utf8($attachment["name"]),++$i,$dependencia);
			downloadAttachment($inbox, $uid, $attachment["partNum"], $attachment["enc"], $path, $anex['name'], $nurad);
			$listaAdjuntos.= "<a href='javascript:void(0)' onclick='funlinkArchivo(\"".$anex['code']."\",\"./\")'>".imap_utf8($attachment["name"])."</a><br>";
		}
		ob_start();
		$links="
				<a href='../verradicado.php?verrad=$nurad' target='_blank' onClick=\"window.open(this.href, this.target, 'width=800,height=500'); return false;\"><i class='fa fa-external-link'></i> Ver radicado</a>
				<a href='../radicacion/NEW.php?nurad=$nurad&Buscar=BuscarDocModUS&Submit3=ModificarDocumentos' target='_blank' onClick=\"window.open(this.href, this.target, 'width=800,height=500'); return false;\"><i class='fa fa-external-link-square'></i> Modificar</a>
                <a   href='../vinculacion/mod_vinculacion.php?verrad=$nurad&codusuario=$codusuario&dependencia=$dependencia' target='_blank' onClick=\"window.open (this.href,this.target,'menubar=0,resizable=0,width=750,height=500,scrollbars=yes'); return false;\"'>Asociar Radicado</a>";
		include "mensaje.php";
		$data = ob_get_flush();
		$data = str_replace($links,"",$data);
		//$data = str_replace("../","",$data);
		$fp = fopen('../bodega/tmp/'.$date.'.html', 'w');
		fwrite($fp, $data);
		fclose($fp);
		$ano=substr($nurad,0,4);
		$pathBodega="/$ano/$dependencia/$nurad.html";
		if (copy("../bodega/tmp/$date.html","../bodega/$pathBodega")){
			$isqlRadicado = "update radicado set RADI_PATH = '$pathBodega' where radi_nume_radi = $nurad";
			$rs=$db->conn->query($isqlRadicado);
			if (!$rs)//Si actualizo BD correctamente
			{	
				echo "Fallo la Actualizacion del Path en radicado < $isqlRadicado >";
			}else{
				$observa = "Radicaci&oacute;n e-mail, se anexa (".count($attachments).") adjunto(s).";
				$hist->insertarHistorico($nurads,  $dependencia , $codusuario, $dependencia, $codusuario, $observa, $codTx);
				//include "enviarMail.php";
			}
		}
		else{
			echo "Error al copiar imagen de radicado a la bodega";
		}
	}
}
else{
	print("No hay Correo disponible");
}
?>
