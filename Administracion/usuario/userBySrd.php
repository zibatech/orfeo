<?php
$dep=$_GET["dep"];
$srd=$_GET["srd"];
session_start();
    $ruta_raiz = "../.."; 
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");
include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$sql="
select 
	distinct u.depe_codi, 
	usua_login
from usuario u 
left join sgd_mrd_matrird m on (m.depe_codi=u.depe_codi) 
where u.usua_perm_td ilike '%$srd%' 
or m.sgd_srd_codigo=99
";
$rs=$db->conn->GetArray($sql);
/*echo "<pre>";
var_dump($rs);
echo "</pre>";
die;*/
$fp = fopen('/tmp/ficheroReport.csv', 'w');
fwrite($fp, "DEPENDENCIA,USUARIO,SERIE");
fwrite($fp, "\n");
foreach ($rs as $key){
	    fwrite($fp, "'".$key["DEPE_CODI"]."',");
	    fwrite($fp, "'".$key["USUA_LOGIN"]."',");
	    fwrite($fp, "'".$srd."'");
	    fwrite($fp, "\n");
}
fclose($fp);
$f="report.csv";
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$f\"\n");
echo file_get_contents("/tmp/ficheroReport.csv");
?>
