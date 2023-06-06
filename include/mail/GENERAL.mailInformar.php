<?PHP
if(empty($ruta_raiz)){
    $ruta_raiz = "..";
}

$rutaRaiz = $ruta_raiz;
require_once($ruta_raiz."/include/PHPMailer_v5.1/class.phpmailer.php");
include_once("$ruta_raiz/processConfig.php");
include $rutaRaiz."/conf/configPHPMailer.php";
include_once("$ruta_raiz/include/crypt/Crypt.php");

if(!$db || !is_object($db)){
    $db = new ConnectionHandler("$ruta_raiz");
}

//Envio de Correo por Respuesta Rapida.
if($codTx!=6 and !$envioDigital){
    $query = "select u.USUA_EMAIL
            from usuario u
            where u.USUA_CODI ='$usuaCodiMail' and
                  u.depe_codi='$depeCodiMail' and
                  u.usua_email != '' ";

        $rs=$db->conn->query($query);

        if(!$rs->EOF){
            $mailDestino = $rs->fields["USUA_EMAIL"];
        }
    }
    if(isset($radicadosSelText)){
    $queryPath = "select RADI_NUME_RADI, RADI_PATH
                    from RADICADO
                    where RADI_NUME_RADI IN($radicadosSelText)";

    $rsPath = $db->conn->query($queryPath);
}

$linkImagenesTmp = "";

if($rsPath){
  while(!$rsPath->EOF){
  $radicado = $rsPath->fields["RADI_NUME_RADI"];
  $radicadoPath = $rsPath->fields["RADI_PATH"];
  if(trim($radicadoPath)){
    $linkImagenesTmp .= "<a href='".$servidorOrfeoBodega."$radicadoPath'>Imagen Radicado $radicado </a><br>";
  }else{
    $linkImagenesTmp .= "Radicado $radicado sin documetno Asociado<br>";
  }
  $rsPath->MoveNext();
  }
}

if($codTx==1983) {
    $asuntoMail =  "Radicado: ".$numeroRadicado;
    $mailDestino = $_SESSION['email'];
		$asu = "Se ha registrado en el Sistema de Información de la Corporación Autónoma Regional del Atlántico la siguiente PQR con el número de radicado ".$numeroRadicado.". Por favor conserve el n&uacute;mero para consultar el estado de su solicitud.";
		$radicadosSelText = $numeroRadicado;
		$mensaje = file_get_contents($ruta_raiz."/conf/envioDigital.html");
}

if($codTx==6){
    //Envio de Correo por Respuesta Rapida.
    $linkImagenes    = $linkImagenesTmp;
    $admPHPMailer    = $correoSalienteRR;
    $userPHPMailer   = $correoSalienteRR;
    $passwdPHPMailer = $passwordCorreoSalienteRR;
    $mensaje         = file_get_contents($rutaRaiz."/conf/MailRespuestaRapida.html");
    $asuntoMail =  $asuntoMailRespuestaRapida;
    $mailDestino = trim($mails);
}

if($codTx==8) {
    $linkImagenes = $linkImagenesTmp;
    $mensaje = file_get_contents($rutaRaiz."/conf/MailInformado.html");
    $asuntoMail =  $asuntoMailInformado;
}

if($codTx==18) {
    $linkImagenes = $linkImagenesTmp;
    $mensaje = file_get_contents($rutaRaiz."/conf/MailConjunto.html");
    $asuntoMail =  $asuntoMailConjunto;
}

if($codTx==9){
    $linkImagenes = $linkImagenesTmp;
    $mensaje = file_get_contents($rutaRaiz."/conf/MailReasignado.html");
    $asuntoMail =  $asuntoMailReasignado;
}

if($codTx==2){
    $mensaje = file_get_contents($rutaRaiz."/conf/MailRadicado.html");
    $asuntoMail =  $asuntoMailRadicado;
}

if($codTx==99){
    $asuntoMail =  "Radicado";
    $linkImagenes = $linkImagenesTmp;
    $mensaje = file_get_contents($rutaRaiz."/conf/MailRadicacionCorreo.html");
    $mensaje = str_replace("*TEXTO*", $texto, $mensaje);
    $mailDestino=$email;
}

if(isset($envioDigital)) {
    $linkImagenes = $linkImagenesTmp;
    $mensaje = file_get_contents($ruta_raiz."/conf/envioDigital.html");
    $asuntoMail =  utf8_decode("Radicado");
    $mailDestino=$email;
}

// the true param means it will throw
// exceptions on errors, which we need to catch
$mail = new PHPMailer(true);
$mail->SetLanguage( 'es', $ruta_raiz.'/include/PHPMailer_v5.1/language' );

$mail->IsSMTP(); // telling the class to use SMTP

try {
    $mail->Host       = $hostPHPMailer;  // SMTP server
    $mail->Port       = $portPHPMailer;  // set the SMTP port for the GMAIL server
    $mail->SMTPDebug  = $debugPHPMailer; // enables SMTP debug information (for testing) 2 debuger
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = $SMTPSecure;     // enable SMTP authentication
    $mail->Username   = $userPHPMailer;  // SMTP account username
    $mail->Password   = $passwdPHPMailer;// SMTP account password

    $emails = explode(";", $mailDestino);

    $emails = array_unique($emails);

    foreach($emails as $key => $emailDestino) {
        if($emailDestino) $mail->AddAddress(trim($emailDestino));
    }

    $secret_key = "97SUP3RC0R33UDKA7128409EJA";
    $radicadoPadreGen = $radicadosSelText;
    $queryPadre = "select ANEX_RADI_NUME
                from ANEXOS
                where RADI_NUME_SALIDA IN( $radicadosSelText ) OR ANEX_RADI_NUME IN ( $radicadosSelText ) LIMIT 1";

    $rsPadre = $db->conn->query($queryPadre);

    if($rsPadre){
      $radicadoPadreGen = $rsPadre->fields["ANEX_RADI_NUME"];
    }
   
    $encripted = $encrypted_string = encrypt_decrypt('encrypt',$radicadoPadreGen,$secret_key);
    $linkAnexos = $httpOrfeoRemoto.'lista_anexos_consulta.php?radiNume='.$encripted;
   
    $mensaje      = str_replace("*RAD_S*", $radicadosSelText, $mensaje);
    $mensaje      = str_replace("*USUARIO*", $krd, $mensaje);
    $linkImagenes = str_replace("*SERVIDOR_IMAGEN*",$servidorOrfeoBodega,$linkImagenes);
    $mensaje      = str_replace("*LINK_ANEXOS*",$linkAnexos,$mensaje);
    $mensaje      = str_replace("*IMAGEN*", $linkImagenes, $mensaje);
    $mensaje      = str_replace("*ASUNTO*", htmlentities($asu, ENT_QUOTES | ENT_IGNORE, "UTF-8"), $mensaje);
    $mensaje      = str_replace("*ENTIDAD_LARGO*", $_SESSION["entida_largo"], $mensaje);
    $mensaje      = str_replace("*DEPENDENCIA_NOMBRE*", $_SESSION["depe_nomb"], $mensaje);
    $mensaje      = str_replace("*RADICADO_PADRE*", $radPadre, $mensaje);
    $nom_r        = $nombre_us1 ." ". $prim_apel_us1." ". $seg_apel_us1. " - ". $otro_us1;
    $mensaje = str_replace("*NOM_R*", $nom_r, $mensaje);
    $mensaje = str_replace("*RADICADO_PADRE*", $radicadopadre, $mensaje);
    $mensaje = str_replace("*MENSAJE*", $observa, $mensaje);

    //Email::calculo de la encriptacion de email
    $mail->MsgHTML($mensaje);
    $mail->SetFrom($admPHPMailer, $admPHPMailer);
    $mail->Subject = "$entidad: $asuntoMail";
    $mail->AltBody = 'Para ver correctamente el mensaje, por
        favor use un visor de mail compatible con HTML!'; // optional - MsgHTML will create an alternate automatically

    //Start::Sin anexos
    /*
    if(isset($envioDigital)) {
        $ruta_tmp = $ruta_raiz."/bodega/$radiPath";
        $ruta = str_replace("//","/",$ruta_tmp);
        $mail->AddAttachment($ruta);

        if(!empty($adjuntosAnex) ){
            foreach($adjuntosAnex as $key => $anexCodigo){

                $pathAnexo = $ruta_raiz.$anexCodigo;
                if($anexCodigo && file_exists($pathAnexo)){
                    $mail->AddAttachment($pathAnexo);     // attachment
                }
            }
        }
    }
    */
    //End::Sin anexos

    if($codTx==1983) {
        $mail->AddAttachment($ruta_raiz."/bodega/$rutaPdf");
    }

    // Envio de adjuntos en respuesta Rapida.
    if($codTx==6 ){
        $ext = array_pop(explode(".",$pathRadicado));
        $mail->AddAttachment($pathRadicado , 'Respuesta N.'.$nurad.".".$ext);

        if(!empty($adjuntosAnex) ){
            $anex_index= 0;
            foreach($adjuntosAnex as $key => $anexCodigo){
                $pathAnexo = $ruta_raiz.$anexCodigo;
                if($anexCodigo && file_exists($pathAnexo)){
                    $mail->AddAttachment($pathAnexo , "Anexo_".substr($pathAnexo,-9,10));      // attachment
                }
            }
        }
    }


    if($mail->Send()){
        //echo "<br><b>Enviado correctamente";
        $envioOk = "ok";
        $success=true;
    }else{
        $envioOk = "Error";
        //echo "<font color=red><b>No se envio Correo</font><br>";
    }

} catch (phpmailerException $e) {
    $e->errorMessage() . " " .$mailDestino; //Pretty error messages from PHPMailer
} catch (Exception $e) {
    $e->getMessage() . " " .$mailDestino; //Boring error messages from anything else!
}

?>
