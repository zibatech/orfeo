<?php 

/**
* Clase qeu permite sanitizr las variables
* @autor correlibre.org - version ArgoBPM
*
*/

class classSanitize
{
	
	function __construct()
	{
		# code...
	}

	/* Funcion que evita la injection de codigo */
	function noSql($string){

		//$string = mysql_real_escape_string($string);
		$string = htmlspecialchars($string);
		$string = self::satinizeString($string);
		$string = self::notSlashes($string);
		return $string;

	}


	function satinizeString($string){
		$string =  strip_tags($string);    // -> not a tag < 5
		$string = filter_var ( $string, FILTER_SANITIZE_STRING); // -> not a tag
		return $string;
	}


	function sanitizeInt($number){
		$number = filter_var($number, FILTER_SANITIZE_NUMBER_INT);
		echo $number;
	}


	function notSlashes($string){
			//Escapar comillas simples
			$string = addslashes($string);
			$string = str_replace("'","\'",$string);
			$string = str_replace('"','\"',$string);
			return $string;
	}


}
?>