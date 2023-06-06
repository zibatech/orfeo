<?php
 /*
  * Invocado por una funcion javascript (funlinkArchivo(numrad,rutaRaiz))
  * Consulta el path del radicado
  * @author Liliana Gomez Velasquez
  * @since 5 de noviembre de 2009
  * @category imagenes
 */
error_reporting(0);
session_start();
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
if (!$ruta_raiz) $ruta_raiz = ".";
include("$ruta_raiz/processConfig.php");
if (isset($db)) unset($db);
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode( ADODB_FETCH_ASSOC );
include_once "$ruta_raiz/tx/verLinkArchivo.php";

$krd                = $_SESSION["krd"];
$dependencia        = $_SESSION["dependencia"];
$ln                 = $_SESSION["digitosDependencia"];
if($_SESSION["usua_perm_root_email"] == 't'){
    //$_SESSION["dependencia"] = intval(substr($numrad,5,$_SESSION["digitosDependencia"]));
    $ln                      = $digitosDependencia;
}
$digitos_totales = 5 + $_SESSION['digitosSecRad'] + $ln;

$verLinkArchivo = new verLinkArchivo($db);
$tipoDoc ="Radicado";


if (strlen( $numrad) <= $digitos_totales){
  $resulVali = $verLinkArchivo->valPermisoRadi($numrad);
  $verImg = $resulVali['verImg'];
  $pathImagen = $resulVali['pathImagen'];

 if(!$pathImagen){
   include_once "./htmlheader.inc.php";
   $htmlMsg = '<div class="alert alert-warning alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <span class="glyphicon glyphicon-info-sign"></span> <strong>Upps !</strong> Este ('.$tipoDoc.') no tiene Imagen Asociada.
  </div>';
   die($htmlMsg);
 }

 $seguridadRadicado =  $_SESSION['seguridadradicado'] ;
 unset($_SESSION['seguridadradicado'] );


  if(substr($pathImagen,0,9) == "../bodega") {
  	$pathImagen=str_replace('../bodega','./bodega',$pathImagen);
  	$file = $pathImagen;
  }elseif(substr($pathImagen,0,12) == "../../bodega") {
    $pathImagen=str_replace('../../bodega','./bodega',$pathImagen);
  	$file = $pathImagen;
  }
	else {
		$file = $ruta_raiz. "/bodega/".$pathImagen;
  }
}else {
    $tipoDoc ="Anexo";
    //Se trata de un anexo

    $noCache = "?dateNow=".date("ymd_his");
    $noCache = "";

    $resulValiA = $verLinkArchivo->valPermisoAnex($numrad);
    $verImg = $resulValiA['verImg'];
    $pathImagen = $resulValiA['pathImagen'];

    if(!$pathImagen) $pathImagen=$numrad;

    if(substr(trim($numrad),0,1)==1){
        $file = "bodega/".substr(trim($numrad),1,4)."/".ltrim(substr(trim($numrad),4,$ln), '0')."/docs/".trim($pathImagen)."$noCache";
    }else{
        $file = "$ruta_raiz/bodega/".substr(trim($numrad),0,4)."/".ltrim(substr(trim($numrad),4,$ln),'0')."/docs/".trim($pathImagen)."$noCache";
    }
    if($_SESSION["usua_perm_root_email"] == 't'){
        if(substr(trim($numrad),0,1)==1){
            $file = "bodega/".substr(trim($numrad),1,4)."/".ltrim(substr(trim($numrad),4,$ln), '0')."/docs/".trim($pathImagen)."$noCache";
        }else{
            $file = "$ruta_raiz/bodega/".substr(trim($numrad),0,4)."/". ltrim(substr(trim($numrad),4,$ln), '0') ."/docs/".trim($pathImagen)."$noCache";
        }
    }

}

$fileArchi = $file;
$tmpExt = explode('.',$pathImagen);
$es_pdf = ($tmpExt[1] == 'pdf')? 'pdf' : null;
$filedatatype = ($es_pdf)? $es_pdf : $pathImagen;

// Si se tiene una extension
if(count($tmpExt)>1){
   $filedatatype = array_pop($tmpExt);
}
//Start::publico
if ($_SESSION["usua_perm_root_email"]=="t"){
    $verImg = "SI";
    $_SESSION["nivelus"]=5;
}
//End::publico
//Start::visor v3
if(file_exists($fileArchi)){
  $filesizemb = filesize($fileArchi); // bytes
  $filesizemb = round($filesizemb/ 1024 / 1024, 1);
  if($filesizemb>50)
    goto a;
}
if( file_exists($fileArchi) && ($filedatatype == 'docx' || $filedatatype == 'xlsx' || $filedatatype == 'pptx')){
        $linkvisor = str_replace('./',"$visorurl$bodegaurl",$fileArchi);
        header("Location: $linkvisor?v=".rand(1,99999));
        exit;
}
a:

if( file_exists($fileArchi) && ($filedatatype == 'msg')){
         $linkvisor = str_replace('./',"$bodegaurl",$fileArchi);
         $url=$linkvisor;
        $filesizemb = filesize($fileArchi); // bytes
        $filesizemb = round($filesizemb/ 1024 / 1024, 1);
        if($filesizemb>5)
            goto b;
         require __DIR__."/processConfig.php";
         require __DIR__."/visormsg/visor.php";
        exit;
}
b:

if( file_exists($fileArchi) && $_SESSION["usua_perm_root_email"]=="t"){
        $linkvisor = str_replace('./',"$bodegaurl",$fileArchi);
        header("Location: $linkvisor?v=".rand(1,99999));
        exit;
}


//End::visorv3
if($verImg=="SI" or $_SESSION["nivelus"]==5){
  if (file_exists($fileArchi)) {
    header('Content-Description: File Transfer');
    //
    switch($filedatatype) {
      case 'odt':
          header('Content-Type: application/vnd.oasis.opendocument.text');
          break;
      case 'doc':
      //case 'docx':
            header('Content-Type: application/msword');
            break;
      case 'docx':
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            break;
      case 'tif':
            header('Content-Type: image/TIFF');
            break;
      case 'pdf':
            header('Content-Type: application/pdf');
            break;
      case 'xls':
      //case 'xlsx':
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
            //$file=utf8_encode($file);
            break;
      case 'jpg':
      case 'jpeg':
          header('Content-Type: image/jpeg');
          break;
      case 'png':
          header('Content-Type: image/png');
          break;http://hitnu2/orfeo/
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
  	//die ("<B><center>  NO se encontro el Archivo  </a><br>");
  }

}elseif($verImg == "NO"){
  $msj= "NO tiene permiso para acceder al Archivo. ";

	//die ("<B><CENTER>  NO tiene permiso para acceder al Archivo </a><br>");
}
else{
	$msj="NO se ha podido encontrar informacion del Documento. ";
}
if ($msj){
  include_once "./htmlheader.inc.php";
  die ('<div class="alert alert-warning alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <span class="glyphicon glyphicon-info-sign"></span> <strong>Upps !</strong> '.$msj.' ('.$tipoDoc.') </div>');
}
