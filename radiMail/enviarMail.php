<?php
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
$url="http://".$_SERVER['HTTP_HOST'];
include_once("../processConfig.php");
include_once("../include/PHPMailer_v5.1/class.phpmailer.php");
include "..//conf/configPHPMailer.php";
  $pattern="/([\s]*)([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*([ ]+|)@([ ]+|)([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,}))([\s]*)/i";

  preg_match_all($pattern,$mailFrom, $salida);
  
 $destinatario=$_SESSION['eMailRemitente'];
$sql = "SELECT sgd_rad_codigoverificacion  FROM RADICADO  WHERE RADI_NUME_RADI =$numeroRadicado";
                # Busca el codigo verificacion para enviarselo al ciudadano para su consulta.   
                        $rs=$db->conn->query($sql);# Ejecuta la busqueda
                        $codigoverificacion = $rs->fields["SGD_RAD_CODIGOVERIFICACION"]; 
 
  //para el envío en formato HTML
  $mail = new PHPMailer(true);

//  var_dump("../bodega".$archivoRadicado);
$archivoRadicadoMail = $archivoRadicado;
//echo $archivoRadicadoMail;
echo  $cuerpo = "<br>$texto
                <br> La $entidad_largo ha recibido su correo y se ha radicado con el No. $numeroRadicado  y codigo de verificacion $codigoverificacion. Puede ser consultado en el portal Web de la Veeduria.</p>
                <br><br><b><center>Consulte el estado de su requerimiento en:
                <a href='$url/orfeo/consultaWebVeeduria'>Consulta Web Veeduria Distrital</a> 
                 <hr>Documento Recibido<hr>
                 <table>
                 <tr><td>
                 $archivoRadicadoMail
                 </td></tr>
                 </table>";

  $mail->IsSMTP(); // telling the class to use SMTP
  $mail->SetFrom($admPHPMailer, $admPHPMailer);

  $mail->Host       = $hostPHPMailer;
  $mail->Port       = $portPHPMailer;

  $mail->SMTPDebug  = 2; //          $debugPHPMailer;  // 1 = errors and messages // 2 = messages only 
  $mail->SMTPAuth   = "true";
//  $mail->SMTPSecure = "tls";

  $mail->Username   = $userPHPMailer;   // SMTP account username
  $mail->Password   = $passwdPHPMailer; // SMTP account password

  $mail->Subject = "Se ha recibido su Correo (No. $numeroRadicado)";
  $mail->AltBody = "Para ver el mensaje, porfavor use un visor de E-mail compatibles!";

  $mail->AddAddress($destinatario);
//  $mail->AddAddress("ricardoperilla@gmail.com");
  $mail->MsgHTML($cuerpo);

  //$mail->From = $usuaEmail;
  //$mail->FromName = $usuaEmail;

  echo "Destino : ".$destinatario;
  echo "<hr>";
  if(!$mail->Send()){
      echo "fallo el Envio de Correo respuesta";
  }else{
      echo "Se envio el Correo a ".$destinatario;
  }
?>

