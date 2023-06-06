<?php
$ruta_raiz = "../../..";
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$id = (isset($_GET['id']) && !empty($_GET['id'])) ? $_GET['id'] : 0;

include "$ruta_raiz/include/tx/Bandejas.php";
//$db->conn->debug = true;
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$Bandejas = new Bandejas($db);

$Bandejas->codUsuario = $codUsuario;
$Bandejas->depeCodi = $depeCodi;

if($carpetaPer==1) $resultCarpetas = $Bandejas->getCarpetasPersonales();
  else $resultCarpetas = $Bandejas->getCarpetasGenerales(); 
echo json_encode($resultCarpetas);
?>