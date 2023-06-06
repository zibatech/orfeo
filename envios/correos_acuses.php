<?php
session_start();

//error_reporting(E_ALL);
//ini_set('display_errors', '1');
/**
 * Modulo de Formularios Web para atencion a Ciudadanos.
 * @autor Carlos Barrero   carlosabc81@gmail.com SuperSolidaria
 * @author Sebastian Ortiz Vasquez 2012
 * @fecha 2009/05
 * @Fundacion CorreLibre.org
 * @licencia GNU/GPL V2
 *
 * Se tiene que modificar el post_max_size, max_file_uploads, upload_max_filesize
 */
foreach ($_GET as $key => $valor)   ${$key} = $valor;//iconv("ISO-8859-1","UTF-8",$valor);
foreach ($_POST as $key => $valor)   ${$key} = $valor; //iconv("ISO-8859-1","UTF-8",$valor);
$pais_formulario = $pais;
define('ADODB_ASSOC_CASE', 2);


$ruta_raiz = "..";
$ADODB_COUNTRECS = false;

include_once($ruta_raiz."/include/PHPMailer_v5.1/class.phpmailer.php");


include_once("$ruta_raiz/processConfig.php");
include $rutaRaiz."/conf/configPHPMailer.php";

$db   = new ConnectionHandler($ruta_raiz);
$mail = new PHPMailer();
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);


  
    $usMailSelect  = "info@crautonoma.gov.co"; 
    $mail->IsSMTP(); // telling the class to use SMTP
    $mail->SetFrom("info@crautonoma.gov.co","RADICADOS FINALIZADOS AUTOMATICAMENTE");
    $mail->Host       = "smtp.office365.com";
    $mail->Port       = "587";
    $mail->SMTPDebug  = "1";  // 1 = errors and messages // 2 = messages only 
    $mail->SMTPAuth   = "true";
    $mail->SMTPSecure = "tls";
    $mail->Username   = "info@crautonoma.gov.co";   // SMTP account username
    $mail->Password   = "pass"; // SMTP account password
   
    $mail->AltBody    = "Para ver el mensaje, por favor use un visor de E-mail compatible!";
    $url=true;

	$ddate = date('Y-m-d');
	$date = new DateTime($ddate);
	$week = $date->format("W");


$sqlg="SELECT DISTINCT usua_codi,depe_codi FROM acuses_usuarios WHERE semana=".$week;
$rsg=$db->conn->Execute($sqlg);

while(!$rsg->EOF)
{


		$sql_usu="SELECT usua_nomb,usua_email FROM usuario WHERE depe_codi=".$rsg->fields['depe_codi']." AND usua_codi=".$rsg->fields['usua_codi'];
		$rs_usu=$db->conn->Execute($sql_usu);


		$tabla="
		Hola, ".$rs_usu->fields['usua_nomb']."<br><br>
		Los siguientes radicados fueron finalizados autom&aacute;ticamente, de su bandeja de usuario, de conformidad con la autorizaci&oacute;n dada por la dependencia.<br><br>
		<table border='1'>
		<tr bgcolor='gray'>
			<td><strong>No. Radicado</strong></td>
			<td><strong>Fecha de Cierre</strong></td>	
		</tr>
		";

		$sql_det="SELECT radi_nume_radi,fecha FROM acuses_usuarios WHERE depe_codi=".$rsg->fields['depe_codi']." AND usua_codi=".$rsg->fields['usua_codi'];
		$rs_det=$db->conn->Execute($sql_det);

		while(!$rs_det->EOF)
		{
					
						$tabla.="<tr><td>".$rs_det->fields['radi_nume_radi']."</td><td>".$rs_det->fields['fecha']."</tr>";
						$rs_det->MoveNext();	
		}

		$mail->Subject    = "RADICADOS FINALIZADOS AUTOMATICAMENTE - ORFEO";
		$mail->AddAddress($rs_usu->fields['usua_email']);
		$tabla.="</table><br><br><br><strong>Nota:</strong> Esta es una notificaci&oacute;n autom&aacute;tica, por favor no responda este mensaje.";
 		$mail->MsgHTML($tabla);
		$mail->Send();
		$mail->ClearAllRecipients( );

$rsg->MoveNext();	
}


?>
