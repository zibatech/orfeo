<?php
$ruta_raiz = "../../..";
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$id = (isset($_GET['id']) && !empty($_GET['id'])) ? $_GET['id'] : 0;
if(!$id && $_POST["id"]) $id=$_POST["id"];
//if ($deta_causal and $sector) {
	$isql = "SELECT u.USUA_NOMB, u.USUA_LOGIN, u.SGD_ROL_CODIGO, u.USUA_CODI
        FROM USUARIO u
        WHERE u.DEPE_CODI=$id 
        ORDER BY u.usua_NOMB DESC";
	$rs = $db->conn->query($isql);
	if ($rs && !$rs->EOF) {
  $i=0;
  do {
    $usuaNomb =  utf8_encode($rs->fields["USUA_NOMB"]);
    $usuaLogin =  utf8_encode($rs->fields["USUA_LOGIN"]);
    $rolCodigo =  $rs->fields["SGD_ROL_CODIGO"];
    $usCodigo =  $rs->fields["USUA_CODI"];
    //$nombre_dcau =  utf8_encode($rs->fields[2]);
    $usuarios[$i] = $usuaNomb.'-'.$usuaLogin.'-'.$rolCodigo.'-'.$usCodigo;
    $i++;
    $rs->MoveNext();
  }while(!$rs->EOF);
  //}
  }
 echo json_encode($usuarios);
?>