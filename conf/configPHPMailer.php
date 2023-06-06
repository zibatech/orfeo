<?php
if(!$ruta_raiz) $ruta_raiz = "..";
include "$ruta_raiz/processConfig.php";

if(!$debugPHPMailer)  $debugPHPMailer=2;
if(!$SMTPSecure)  $SMTPSecure="tls";
$smtpAuth             = trim($smtpAuth);
$admPHPMailer         = trim($correoSaliente);
$userPHPMailer        = trim($correoSaliente);
$passwdPHPMailer      = trim($passwordCorreoSaliente);
$hostPHPMailer        = trim($servidorSmtp); // Para fuera Gmail es "ssl://smtp.gmail.com"
$portPHPMailer        = trim($puertoSmtp); // Para Gmail el Puerto es "465"
$debugPHPMailer       = trim($debugPHPMailer);  // Si esta en 2 mostrara una depuracion al enviar correo.  En 1 los evita.
$asuntoMailReasignado = "Llegada de Radicado $radicadosSelText";
$asuntoMailRadicado   = "Llegada de Radicado nuevo $radicadosSelText";
$asuntoMailInformado  = "Se ha informado de un radicado $radicadosSelText";
//Start::asunto tramite conjunto
$asuntoMailConjunto  = "Se ha informado de un radicado $radicadosSelText";
//End::asunto tramite conjunto
$servidorOrfeoBodega  = $httpOrfeoLocal.'/bodega/';

// Datos para correo de respuesta emailRespuestaRapida
$correoSalienteRR          = trim($correoSalienteRR);
$passwordCorreoSalienteRR  = trim($passwordCorreoSalienteRR);

// Datos para correo de respuesta Pqrs
$usuarioEmailPQRS     = trim($correoSaliente);
$emailPQRS            = trim($correoSaliente);
$passwordEmailPQRS    = trim($passwordCorreoSaliente);
?>
