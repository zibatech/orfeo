<?php
$ruta_raiz = '..';

include_once("$ruta_raiz/processConfig.php");
include_once("$ruta_raiz/include/crypt/Crypt.php");

$urlLogin     = $conf_certificadoUrl.'v1/auth/login';
$urlSendEmail = $conf_certificadoUrl.'v1/emails/massive/send';

$credenciales = "{
                   \"email\" : \"$conf_certificadoCorreo\",
                   \"password\" : \"$conf_certificadoPassword\"
                 }";

$headers = array();
$token   = '';
$emails  = '';
$file    = '';
$addAttachment = '';
$bodyMessage = '';

$db = new ConnectionHandler("$ruta_raiz");

//Se valida la funcion por que este script
//es llamada en el bucle de adminMasivaEmail
if(!function_exists('getTipeFile1000')) {
    function getTipeFile1000($filedatatype) {
        switch($filedatatype) {
        case 'odt':
            return 'application/vnd.oasis.opendocument.text';
        case 'doc':
            return 'application/msword';
        case 'docx':
            return 'application/msword';
        case 'tif':
            return 'image/tiff';
        case 'pdf':
            return 'application/pdf';
        case 'xls':
        case 'csv':
        case 'ods':
            return 'application/vnd.ms-excel';
        case 'html':
            return 'text/html';
        case 'jpg':
        case 'jpeg':
            return 'image/jpeg';
        case 'png':
            return 'image/png';
        case 'gif':
            return 'image/gif';
        case 'zip':
            return 'application/zip, application/octet-stream';
        case 'rar':
            return 'application/x-rar-compressed, application/octet-stream';
        default :
            return 'application/octet-stream';
        }
    }
}

//Se valida la funcion por que este script
//es llamada en el bucle de adminMasivaEmail
if(!function_exists('objAttachEmail')) {
    function objAttachEmail($file){
        if (file_exists($file)) {
            $path_parts = pathinfo($file);
            $mimeType = getTipeFile1000($path_parts['extension']);
            $nameFile = $path_parts['basename'];
            $contFile = file_get_contents($file);
            $base64   = "RXN0YSBlcyBvdHJhIHBydWViYQo=";//base64_encode($contFile);
            $base64   = base64_encode($contFile);
            $filesize = strlen($contFile);
            $content = "data:$mimeType;base64,$base64";

            return "
        {
            \"content\"  : \"$content\",
            \"mimeType\" : \"$mimeType\",
            \"size\"     : \"$filesize\",
            \"filename\" : \"$nameFile\"
        } ";

        } else {
            return false;
        }
    }
}


/***************************************************************/
/************** login ******************************************/
/***************************************************************/
if(!$_session['token_envio_gse']){
    $ch = curl_init();
    $flog = fopen($ruta_raiz."/bodega/certlog/".date('Ymd')."gse.log", "a+");
    curl_setopt($ch, CURLOPT_URL, $urlLogin);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    curl_setopt($ch, CURLOPT_POSTFIELDS, $credenciales);

    //Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    //Execute the request.
    $data = curl_exec($ch);

    //Close the cURL handle.
    curl_close($ch);

    $arrLogin = json_decode($data);

    if($arrLogin->statusCode != 200){
        fwrite($flog, " No. $radSalida GSE FALLA AL OBTENER TOKEN ".date('Y-m-d h:i'));
        fclose($flog);
        throw new Exception('No se realizo la consulta de login.' . $arrLogin->statusCode );
    }
    $token = $arrLogin->result->token;
    $_session['token_envio_gse'] = $token;
}else{
    $token = $_session['token_envio_gse'];
}



/***************************************************************/
/************** Send Email *************************************/
/***************************************************************/

/**
 * Convertir el listado de direcciones
 * en un formato json para ser envido
 * en el cuerpo del mensaje
 */
$emails = explode(";", $mailDestino);

$emails = array_unique($emails);

$emailVal = array();

foreach ($emails as $email) {
    // Remove all illegal characters from email
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Validate e-mail
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailVal[] = $email;
    }
}

$emails = json_encode($emailVal);

/**
 * Desde el archivo responseEnvioE-mail.php
 * traemos la ruta de la imagen del radicado
 * la cual leemos y convertimos en base 64 para
 * ser enviada en el cuerpo del mensaje
 */
$file = "$ruta_raiz/bodega/".$radiPath;
$addAttachment = objAttachEmail($file);

//Variable que recopila datos para el envio masivo de correos
$dataMasiva .= "<li class='list-group-item'>
            <b>Imagen Principal </b> $file
           </li>";

//Variable que recopila datos para el envio masivo de correos
$dataMasiva .= "<li class='list-group-item'>
            <b>Correos para envio en gse</b> $emails
           </li>";

if($pruebas){
    var_dump($addAttachment);
}

//Variable que recopila datos para el envio masivo de correos
$textAtt = substr($addAttachment, 0, 100);
$dataMasiva .= "<li class='list-group-item'>
            <b>Existe imagen principal </b> $textAtt ...
           </li>";

if(!$addAttachment or empty($emailVal)){

    $request = array(
        "success" => false,
        "message" => "Error al enviar correo electronico no se
        tiene imagen del radicado o el correo no es valido"
    );

    //Variable que recopila datos para el envio masivo de correos
    $textAtt = substr($addAttachment, 0, 100);
    $dataMasiva .= "<li class='list-group-item'>
            <b> No se tiene imagen o el correo del radicado. </b>
            <br> correo => $emails
            <br> Imagen => $textAtt ...
           </li>";

    if($pruebas){
        var_dump('No se tiene imagen o el correo del radicado. ', $addAttachment ,$emailVal);
    }

} else {

    /*
    foreach($adjuntosAnex as $key => $anexCodigo){
        if($anexCodigo){
            $pathAnexo = $ruta_raiz."/".$anexCodigo;
            $newAtt = objAttachEmail($pathAnexo);

            //Variable que recopila datos para el envio masivo de correos
            $textAtt = substr($newAtt, 0, 100);
            $dataMasiva .= "<li class='list-group-item'>
                        <b>Anexo: </b> $pathAnexo => $textAtt
                       </li>";

            if($newAtt === false){
                if($pruebas){
                    var_dump('Este archivo no existe en la bodega ', $pathAnexo);
                }
            }else{
                $addAttachment .= ' , '.$newAtt;
            }
        }
    }*/

    if($pruebas){
        var_dump('Imagen principal y adjuntos', $addAttachment);
    }

    $asunto = trim(htmlentities($asu, ENT_QUOTES));
    $fecha = date("Y-m-d h:i:sa");

    $secret_key = "97SUP3RC0R33UDKA7128409EJA";
    $radicadoPadreGen = $radSalida;
    $queryPadre = "select ANEX_RADI_NUME
                from ANEXOS
                where RADI_NUME_SALIDA IN( $radSalida ) OR ANEX_RADI_NUME IN ( $radSalida ) LIMIT 1";

    $rsPadre = $db->conn->query($queryPadre);

    if($rsPadre){
      $radicadoPadreGen = $rsPadre->fields["ANEX_RADI_NUME"];
    }

   
    $encripted = $encrypted_string = encrypt_decrypt('encrypt',$radicadoPadreGen,$secret_key);
    $linkAnexos = $httpOrfeoRemoto.'lista_anexos_consulta.php?radiNume='.$encripted;
  
    //End::Link en GSE

    $formato_asunto = preg_replace( "/\r|\n/", "", $asunto );
    $bodyMessage = "{
        \"to\" : $emails,
        \"subject\" : \"$entidad: $asuntoMail No. $radSalida Correo Certificado \",
        \"description\" : \" <p> Referencia. $radSalida </p> <p>Asunto: $formato_asunto </p> <p> Para los fines pertinentes en el siguiente link podr&aacute; visualizar el radicado $radSalida de la Superintendencia Nacional de Salud: </p> <a href=$linkAnexos>Ver adjuntos</a>  \",
        \"fechaSalida\" : \"$fecha\",
        \"attachments\" : [ $addAttachment  ]
    }";

    $ch = curl_init();

    $headers[] = 'Accept: application/json';
    $headers[] = 'Content-Type: application/json';
    $headers[] = "Authorization: Token {$token}";

    $flog = fopen($ruta_raiz."/bodega/certlog/".date('Ymd')."gse.log", "a+");

    curl_setopt($ch, CURLOPT_STDERR, $fp);
    curl_setopt($ch, CURLOPT_URL, $urlSendEmail);
    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyMessage);

    //Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    //Execute the request.
    $data = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($httpCode != 200) {
        fwrite($flog, "$entidad: $asuntoMail No. $radSalida GSE CAIDO ".date('Y-m-d h:i'));
        fclose($flog);
    }else
    {
        fwrite($flog, "$entidad: $asuntoMail No. $radSalida Correo Certificado ".date('Y-m-d h:i'));
        fwrite($flog, $data);
        fclose($flog);
    }


    $info = curl_getinfo($ch);

    //Close the cURL handle.
    curl_close($ch);

    if($data === false)
    {
        fwrite($flog, "$entidad: $asuntoMail No. $radSalida GSE CAIDO ".date('Y-m-d h:i').curl_error($ch));
        fclose($flog);
        throw new Exception('Error GSE' . curl_error($ch));
    }

    $arrCertificados = json_decode($data);


    //Variable que recopila datos para el envio masivo de correos
    $textAtt = substr($bodyMessage, 0, 250);
    $dataMasiva .= "<li class='list-group-item'>
                <b>Mensaje a enviar a gse</b> $textAtt
               </li>";

    $mss = $arrCertificados->statusMessage;

    if($arrCertificados->statusCode !== 200){
        if($pruebas){
            var_dump($arrCertificados, $bodyMessage);
        }

        //Variable que recopila datos para el envio masivo de correos
        $dataMasiva .= "<li class='list-group-item text-danger'>
                    <b>Error al enviar correo por gse</b>
                    respuesta de GSE => $arrCertificados->statusMessage
                   </li>";
        //Variable que recopila datos para el envio masivo de correos
        $button = "<div class='text-danger'>
                    $arrCertificados->statusMessage
                   </div>";
    }else{
        $dataMasiva .= "<li class='list-group-item text-success'>
                        <b>Se envio el correo por gse</b>
                        respuesta => $arrCertificados->statusMessage
                       </li>";
        //Variable que recopila datos para el envio masivo de correos
        $button = "<div class='text-success'>
                    $arrCertificados->statusMessage
                   </div>";
    }

}

//Borramos los anexos existente para evitar que se aniden
//en la siguiente consulta
unset($adjuntosAnex);
unset($addAttachment);
unset($token);
unset($emails);
unset($file);
unset($bodyMessage);
