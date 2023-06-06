<?php
error_reporting(7);
/** Funcion qeu permite evitar la inyecccion de codigo en sql al sanear las 
  * Variables con esta funcion.
  */

if(!$ruta_raiz) $ruta_raiz= "../..";
//if(is_file($ruta_raiz."/include/tx/classSanitize.php")) echo "Existe clase.. "; else echo "No Existe clase... ". $ruta_raiz."/include/tx/classSanitize.php";
include_once $ruta_raiz."/include/tx/classSanitize.php";

$sanitize = new classSanitize();

foreach ($_GET as $key => $valor)  {
	if (is_array($valor)){
		foreach ($valor as $key_ => $valor_) {
			$_GET[$key][$key_] = $sanitize->noSql($valor_);
		}
	}else{
		$_GET[$key] = $sanitize->noSql($valor);
	}
}
foreach ($_POST as $key => $valor) {
	if (is_array($valor)){
		foreach ($valor as $key_ => $valor_) {
			$_POST[$key][$key_] = $sanitize->noSql($valor_);
		}
	}else{
		$_POST[$key] = $sanitize->noSql($valor);
	}
}

?>
