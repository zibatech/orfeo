<?php

session_start();
if (!$ruta_raiz)
	$ruta_raiz = ".";


if (!$_SESSION['dependencia']){
	die(header("HTTP/1.0 404 Not Found")); 
}

require_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once("$ruta_raiz/include/tx/Radicacion.php");
include_once("$ruta_raiz/include/tx/Expediente.php");
require_once("$ruta_raiz/include/tx/Historico.php");


if (!$db)
		$db = new ConnectionHandler($ruta_raiz);


$funcion = $_POST['funcion'];

if($funcion == 1) {
	$rad = new Radicacion($db);    
	$hist      = new Historico($db);
	$depeRadica = $_SESSION['dependencia'];
	$usuRadica = $_SESSION["codusuario"];
	$radicadosSel[0] = $_POST['numbor'];
	$rad->borrarBorrador($_POST['numbor']);
	$hist->insertarHistorico($radicadosSel,
                                        $depeRadica,
                                        $usuRadica,
                                        $depeRadica,
                                        $usuRadica,
                                        "Se elimina el borrador " . $_POST['numbor'],
                                        104);
	$exp = new Expediente($db);
	$num_exp=$exp->consulta_exp($_POST['numbor']);
	$exp->excluirExpediente($_POST['numbor'], $num_exp);
	$hist->insertarHistorico($radicadosSel,
                                        $depeRadica,
                                        $usuRadica,
                                        $depeRadica,
                                        $usuRadica,
                                        "Se excluye el borrador " . $_POST['numbor']." del expediente ".$num_exp,
                                        104);


	echo "200";
}

?>