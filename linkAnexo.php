<?php
session_start();
if(!$ruta_raiz) $ruta_raiz = "./";

if (!$_SESSION['dependencia'])
  header ("Location: $ruta_raiz/cerrar_session.php");

//header("location: $file");   // caso de ver imagen sin Seguridad. !!!!!
$file = base64_decode($_GET['linkAnexo']);
$file = "$ruta_raiz/bodega/".$file;
$tmpExt = explode('.',$file);

// Si se tiene una extension
if(count($tmpExt)>1){
    $filedatatype = array_pop($tmpExt);
}

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    switch($filedatatype) {
    case 'odt':
        header('Content-Type: application/vnd.oasis.opendocument.text');
        break;
    case 'doc':
        //case 'docx':
        header('Content-Type: application/msword');
        break;
    case 'docx':
        //header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Type: application/msword');
        break;
    case 'tif':
        header('Content-Type: image/TIFF');
        break;
    case 'pdf':
        header('Content-Type: application/pdf');
        break;
    case 'xls':
        header('Content-Type: application/vnd.ms-excel');
        break;
    case 'csv':
        header('Content-Type: application/vnd.ms-excel');
        break;
    case 'ods':
        header('Content-Type: application/vnd.ms-excel');
        break;
    case 'html':
        header('Content-Type: text/html');
        break;
    case 'jpg':
    case 'jpeg':
        header('Content-Type: image/jpeg');
        break;
    case 'png':
        header('Content-Type: image/png');
        break;
    case 'gif':
        header('Content-Type: image/gif');
        break;
    default :
        header('Content-Type: application/octet-stream');
        break;
    }

    if ($filedatatype == 'html') {
        header('Content-Disposition: inline; filename='.basename($file));
    }else{
        header('Content-Disposition: attachment; filename='.basename($file));
    }

    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    exit;
}else {
    $msj="NO se encontro el Archivo. ";
}
