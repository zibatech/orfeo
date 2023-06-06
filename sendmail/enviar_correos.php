 <?php
require("phpmailer/class.phpmailer.php");

date_default_timezone_set('America/Toronto');

if ($_POST['enviar_correos']){

$correo1 = "cejebuto@gmail.com";
$correo2 = "giampieruccini@gmail.com";

$mail = new PHPMailer();
$mail->CharSet = "UTF-8"; 
$mail->IsSMTP(); // send via SMTP
$mail->Host = "192.168.100.32"; // SMTP servers
$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)

$mail->SetFrom = "orfeo@cra.gov.co";
$mail->FromName = "Prueba de Envio de correo electronico";
$mail->Subject = "PRUEBA DE ENVIO DE CORREO ELECTRONICO";
$mail->AddAddress($correo1);
if($correo2){$mail->AddAddress($correo2);}

$body = "Cordial saludo, <br><br>";
$body .= "Esto es una brueba de envio de correo electronico smtp. <br><br>";

$mail->Body = $body;
$mail->AltBody = "2015";
//$mail->AddAttachment("PLEGABLE_ENCUESTA_2015.pdf", "PLEGABLE_ENCUESTA_2015.pdf");
//$mail->AddAttachment($file, "carta.pdf");

if($correo1){
echo "<br> envio </br>";

if($mail->Send()){
    echo ' > Mensaje Numero : Enviado <br>';
}else{
    echo ' > Fail N >';
echo  $mail->ErrorInfo;
echo "<br>";
} 

}//FIN DEL IF CORREO

// FIN DEL WHILE
// FIN DEL FOPEN


}//FIN DEL IF DEL POST (CONTROLADOR)
?>
