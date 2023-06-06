<?php
/**
 * @author Hardy Deimont Niño Velasquez (hninovel)
 * @fecha 2/03/2009
 * @version 1.0  
 * 
 * Clase Cipher
 * Funcionalidad Orfeo: Encriptamiento->Desencriptamiento
 * Script llamado: encript-class.php
 * 
 * -- Parametros entrada --
 * 
 * keyCryp: Llave
 * iv: Vector de inicializacion
 * 
 * -- Operaciones  --
 * 
 * Encritado
 * Desencriptado
 * hexadecimal a binario
 */

class Cipher {
	private $securekey, $iv, $td;
	function __construct($keyCryp, $iv = "fedcba9876543210") {
		$this->securekey = $keyCryp;
		$this->iv = $iv;
	}
	function encrypt($input) {
		return $input;
		return mcrypt_encrypt ( MCRYPT_RIJNDAEL_128, $this->securekey, $input, MCRYPT_MODE_CBC, $this->iv );
	}
	function decrypt($input) {
		return trim ( mcrypt_decrypt ( MCRYPT_RIJNDAEL_128, $this->securekey, $input, MCRYPT_MODE_CBC, $this->iv ) );
	}
	function hex2bin($hexdata) {
		$bindata = "";
		for($i = 0; $i < strlen ( $hexdata ); $i += 2) {
			$bindata .= chr ( hexdec ( substr ( $hexdata, $i, 2 ) ) );
		}	
		return $bindata;
	}
}