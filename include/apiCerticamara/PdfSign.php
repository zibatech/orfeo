<?php

$root = $_SESSION['RUTA_ABSOLUTA']."/include/apiCerticamara/";

$apiPath = "$root/api/wrapperSign4J/WrapperSign4J.jar";
//tipo de firma
$signType = "PADES";
//ruta del archivo properties
$xmlConfigPath = "$root/config/properties.xml";
//ruta archivo a firmar
$fileToSignPath = "$root/resources/in/Sign4J_1.pdf";
//ruta para guardar el archivo firmado
$fileSignedPath = "$root/resources/out/signed.pdf";
//ruta del certificado de firma
$signP12Path = "$root/resources/certificate/cra.p12";
//password del certificado de firma
$signP12Password = "Password1";
//si se requiere estampa
// si se requiere que tenga LTV(Long Term Validation) si se colaca true automaticamente el estampado sera "true" tambien
$ltv = "false";

if ($_SESSION["usua_perm_firma"]==1){
	$stamp = "false";
	$ltv = "false";
}
elseif ($_SESSION["usua_perm_firma"]==2){
	$stamp = "true";
	$ltv = "true";
}
/* @var $stampP12Path string Utilizado en el caso que se estampe este es mandatorio al configurado en el properties */
$stampP12Path = "null";
/* @var $stampP12Password string Utilizado en el caso que se estampe este es mandatorio al configurado en el properties */
$stampP12Password = "null";
//razon de firma
$signReason = "Razon firma";
//Locacion de firma
$signLocation = "Cundinamarca";
//frase que se puede agregar en la imagen que se le agregara al documento
$contentSignature = "";
//validacion que se coloca en la imagen por parte del lector de pdf
$imageValidation = "true";

/** numero de la pagina del documento(-1 ultima pagina) (0 en todas las paginas) o para colocarlos en varias paginas enviamos numeros separados por comas */
$numPages = "1";
/* buscar el string ingresado y colocar la imagen encima, si no se requiere buscar colocar la palabra "null" */
//$stringToFind = "7ba3a83eaec884c3596592984ee141b29a31c6af";
$stringToFind = "null";
/**
 * @var $signImageAttrs string Atributos que se configuran si se quiere la firma visible:
 * <page>,
 * <lowerLeftX>,<lowerLeftY>,
 * <upperRightX>,<upperRightY>,
 * <pdf2SignImagePath>,
 * <signFieldName>,<contentSignature>
 */

$firmaMec=$_SESSION['RUTA_ABSOLUTA'].'/bodega/firmas/'.$usua_doc;
if (file_exists($firmaMec)){
$signImageAttrs = "$numPages,0,-75,5,0," 
        . "$firmaMec,"
        . "signFieldName,$contentSignature,$imageValidation,$stringToFind";
}
