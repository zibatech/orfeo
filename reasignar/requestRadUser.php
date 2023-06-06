<?php
session_start();
$ruta_raiz = ".."; 
if (!$_SESSION['dependencia'])
	header ("Location: $ruta_raiz/cerrar_session.php");

include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
//$db->conn->debug=true;
$user=strtoupper($_REQUEST['usua_login']);
$user=explode('-',$user);
$user=end($user);
//var_dump($user);
$isql="
	select r.RADI_NUME_RADI
	FROM RADICADO r
	left join usuario u on (r.radi_depe_actu=u.depe_codi and r.radi_usua_actu=u.usua_codi)
	where u.usua_login='$user'
	";
$rs=$db->conn->GetArray($isql);
$rs=array_column($rs,'RADI_NUME_RADI');
echo implode(',',$rs);

?>
