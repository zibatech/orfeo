<?php
if(!$ruta_raiz)
    $ruta_raiz='../../';
$ruta_raiz2=$ruta_raiz;
$gfl=explode('old',$_SERVER['SCRIPT_NAME']);

if(count($gfl)>1){
$ruta_raiz2=$ruta_raiz.'/../';
}
include "encript-class.php";

class file {
    private $nombcrypt; 
    private $keybase;
    private $llave;
	private $cipher;
	function __construct() {
		$this->cipher = new Cipher('01234567890abcde');
	}
	function desencriptar($input) {
	//return $this->cipher->decrypt($input);
	return $this->cipher->decrypt($this->cipher->hex2bin($input));
	//return $this->cipher->hex2bin($this->cipher->decrypt($input));
	}
    function encriptar($input) {
    	
		//return $this->cipher->encrypt($input);
		return bin2hex($this->cipher->encrypt($input));
	}
    function type($input) {
    	switch ($filedatatype) {
		case 'odt' :
			$tipo='Content-Type: application/vnd.oasis.opendocument.text';
			break;
		case 'doc' :
		case 'docx' :
			$tipo='Content-Type: application/msword';
			break;
		case 'tif' :
			$tipo='Content-Type: image/TIFF';
			break;
		case 'pdf' :
			$tipo='Content-Type: application/pdf';
			break;
		case 'xls' :
			$tipo='Content-Type: application/vnd.ms-excel';
			break;
		case 'csv' :
			$tipo='Content-Type: application/vnd.ms-excel';
			break;
		case 'ods' :
			$tipo='Content-Type: application/vnd.ms-excel';
			break;
		case 'png' :
			$tipo='Content-Type: image/PNG';
			break;
		default :
			$tipo='Content-Type: application/octet-stream' ;
			break;
	}
    	
		return $tipo;
	}
}