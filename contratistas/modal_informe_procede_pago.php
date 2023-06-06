<?php
session_start();
error_reporting(E_ALL);
$ruta_raiz = "..";

require_once($ruta_raiz.'/contratistas/Informe.php');
require_once($ruta_raiz.'/contratistas/InformeObligacion.php');
require_once($ruta_raiz.'/contratistas/InformeEvidencia.php');
require_once($ruta_raiz.'/include/db/ConnectionHandler.php');
require_once($ruta_raiz.'/contratistas/log.php');
$db = new ConnectionHandler($ruta_raiz);


$inf = isset($_GET['inf']) ? $_GET['inf'] : 0;

$informe = Informe::find($inf);

if ($informe) {
	$informe->procede_pago(true);
	registrar_evento($db, $informe->id, 'Enviado a tesorería');
}

header('Location: '.$ruta_raiz.'/contratistas/pagar.php');
?>