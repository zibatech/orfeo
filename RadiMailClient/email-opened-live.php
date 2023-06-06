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
include ("imapFunctions.php");
if($uid){
include_once "../include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
$isql="select * from radimail where uniqueid='$uid'";	
$rs=$db->conn->GetArray($isql);
	if (empty($nurad) or !isset($nurad)){
		$buttonFiled='<button class="btn btn-primary btn-sm replythis"><i class="fa fa-reply"></i> Radicar</button>';
		$body=file_get_contents($CONTENT_PATH."tmp/radimail/$uid-0.html");
		echo $body;
	}
	else{
		include_once "../include/db/ConnectionHandler.php";
		$db = new ConnectionHandler("$ruta_raiz");
		$isql="update radimail set radi_nume_radi='$nurad' where uniqueid='$uid'";	
		$rs=$db->conn->Execute($isql);
		include "$ruta_raiz/include/tx/Historico.php";
		$hist = new Historico($db);
		$codusuario = $_SESSION["codusuario"];
		$dependencia = $_SESSION["dependencia"];
		$krd = $_SESSION["krd"];
		$nurads[] = $nurad;
		$codTx = 42;
		unset($listaAdjuntos);
		$buttonFiled='<button class="btn btn-primary btn-sm replythis"><i class="fa fa-reply"></i> Radicar</button>';
		$links="
				<a href='../verradicado.php?verrad=$nurad' target='_blank' onClick=\"window.open(this.href, this.target, 'width=800,height=500'); return false;\"><i class='fa fa-external-link'></i> Ver radicado</a>
				<a href='../radicacion/NEW.php?nurad=$nurad&Buscar=BuscarDocModUS&Submit3=ModificarDocumentos' target='_blank' onClick=\"window.open(this.href, this.target, 'width=800,height=500'); return false;\"><i class='fa fa-external-link-square'></i> Modificar</a>";
		//include "mensaje.php";
		$data=file_get_contents($CONTENT_PATH."/tmp/radimail/$uid-0.html");
		$data = str_replace("**","*$nurad*",$data);
		$data = str_replace("Radicado No.","Radicado No: $nurad",$data);
		$data = str_replace($buttonFiled,"",$data);
		//$data = str_replace("../","",$data);
		$ano=substr($nurad,0,4);
		$pathBodega="/$ano/$dependencia/$nurad.html";
		$fp = fopen("../bodega/$pathBodega", 'w');
		fwrite($fp, $data);
		fclose($fp);
		echo $data = str_replace("<!--Remplazame-->",$links,$data);
		if (file_exists("../bodega/$pathBodega")){
			$isqlRadicado = "update radicado set RADI_PATH = '$pathBodega' where radi_nume_radi = $nurad";
			$rs=$db->conn->query($isqlRadicado);
			if (!$rs)//Si actualizo BD correctamente
			{	
				echo "Fallo la Actualizacion del Path en radicado < $isqlRadicado >";
			}else{
				//include "enviarMail.php";
			}
			//$db->conn->debug=true;
			$anex_sql="select ra.name, ra.filename, ra.type from radimail_adjunto ra left join radimail r on (r.uniqueid=ra.radimail_id) where r.uniqueid='$uid' and ra.name<>'$uid-0'";
			$rs=$db->conn->GetArray($anex_sql);
			$countAttachs=count($rs);
			$i=0;
			foreach($rs as $rec){
				if (strpos($rec["FILENAME"],".")){
					$ext=end(explode(".",$rec["FILENAME"]));
				}else{
					$ext=end(explode("/",$rec["TYPE"]));
				}
				$anex=fileAdttachments($db,$nurad,$krd,$rec["FILENAME"].".$ext",++$i,$dependencia);
				$name=$rec["NAME"].".$ext";
				$pathBodega="/$ano/$dependencia/docs/".$anex["name"];
				//echo "../bodega/tmp/radimail/$name ../bodega/$pathBodega";
				copy($CONTENT_PATH."/tmp/radimail/".strtolower($name),"../bodega/$pathBodega");
				$attachments++;
			}
			if (!$rs)//Si actualizo BD correctamente
			{	
				echo "Fallo la Actualizacion del Path en radicado < $isqlRadicado >";
			}else{
				$observa = "Radicaci&oacute;n e-mail, se anexan ($countAttachs) adjuntos - ClientMail";
				$hist->insertarHistorico($nurads,  $dependencia , $codusuario, $dependencia, $codusuario, $observa, $codTx);
				//include "enviarMail.php";
			}
			$anex_sql="select ra.filename from radimail_adjunto ra left join radimail r on (r.uniqueid=ra.radimail_id) where r.uniqueid='$uid'";
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
