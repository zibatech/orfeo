<?php
$dep=$_GET["dep"];
session_start();
    $ruta_raiz = "../.."; 
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");
include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$sql="select distinct sgd_srd_codigo from sgd_mrd_matrird where depe_codi=$dep";
$rs=$db->conn->GetArray($sql);
$rs=array_column($rs,"SGD_SRD_CODIGO");
$dep_srd=implode($rs,",");



$sql="select usua_login, usua_perm_td from usuario where depe_codi=$dep";
$rs=$db->conn->GetArray($sql);
foreach ($rs as $key => $value){
	$tds=$value["USUA_PERM_TD"].$dep_srd;
	$tds=substr($tds,1);
	$sql="select sgd_tpr_codigo||' - '||sgd_tpr_descrip as sgd_tpr_descrip from sgd_tpr_tpdcumento where sgd_tpr_codigo in ($tds)";
	$_rs=$db->conn->GetArray($sql);
	$_rs=array_column($_rs,"SGD_TPR_DESCRIP");
	$strTds=implode(",",$_rs);
	$data[$value["USUA_LOGIN"]]=$strTds;
}
$fp = fopen('/tmp/fichero.csv', 'w');
fwrite($fp, "USUARIO,SERIES PERMITIDAS");
fwrite($fp, "\n");
foreach ($data as $key => $val) {
	    fwrite($fp, "'$key',");
	    fwrite($fp, "'$val'");
	    fwrite($fp, "\n");
}

fclose($fp);
$f="report.csv";
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$f\"\n");
echo file_get_contents("/tmp/fichero.csv");
?>
