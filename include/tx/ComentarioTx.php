<?
	/** Archivo que se utiliza para colocar informacion de acciones realizadas en el codigo html 
	  * @$numRegs  int 	Numero de registros afectados en la transaccion
	  * @$varQuery String 	Consulta utilizada
	  * @$pOrfeo   int	Numero de secuencia utilizada para estos comentarios
		*/
	$pOrfeo = isset($pOrfeo) ? $pOrfeo++ : 0;
	$numRegs = isset($numRegs) ? " Registros $numRegs \n" : '';
	$cVarQuery = isset($cVarQuery) ? "Consulta: $varQuery \n" : '';
echo "<!-- \n************************************************************************\n (Dev$pOrfeo)$comentarioDev  \n $numRegs $cVarQuery ************************************************************************ -->\n ";
$varQuery="";
?>
