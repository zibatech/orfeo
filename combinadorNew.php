<?php

session_start();

foreach ($_GET as $key => $valor) ${$key} = $valor;
foreach ($_POST as $key => $valor) ${$key} = $valor;

define('ADODB_ASSOC_CASE', 1);

$krd        = $_SESSION["krd"];
$dependencia= $_SESSION["dependencia"];
$dependencia_nombre = $_SESSION["depe_nomb"];
$usua_doc   = $_SESSION["usua_doc"];
$usua_nomb  = $_SESSION["usua_nomb"];
$codusuario = $_SESSION["codusuario"];
$nivelus = $_SESSION["nivelus"];
$tip3Nombre = $_SESSION["tip3Nombre"];
$tip3desc   = $_SESSION["tip3desc"];
$tip3img    = $_SESSION["tip3img"];
$clave      =$_REQUEST['clave'];

if (!$ruta_raiz) $ruta_raiz = ".";
include("$ruta_raiz/processConfig.php");
if (isset($db)) unset($db);
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

require_once("$ruta_raiz/include/tx/usuario.php");

$usuario = new usuario($db);

echo json_encode($answer);

?>
