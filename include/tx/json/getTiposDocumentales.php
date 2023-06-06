<?php
$ruta_raiz = "../../..";
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
//$db->conn->debug = true;
//$tpSearch = (isset($_GET['tpSearch']) && !empty($_GET['tpSearch'])) ? $_GET['tpSearch'] : 0;
//if ($deta_causal and $sector) {
$tpSearch=trim($tpSearch);
 if(is_numeric($tpSearch)){
   $whereTipo = " t.sgd_tpr_codigo = $tpSearch ";
 }else{
   $tpSearch = str_replace(" ","%",$tpSearch);
   $whereTipo = " t.sgd_tpr_descrip ilike '%$tpSearch%' ";
 }
	$iSql = "SELECT t.sgd_tpr_codigo, t.sgd_tpr_descrip
        FROM SGD_TPR_TPDCUMENTO t 
         WHERE $whereTipo AND SGD_TPR_ESTADO!=0
        ORDER BY t.sgd_tpr_descrip ";
	$rs = $db->conn->query($iSql);
	if ($rs && !$rs->EOF) {
  $i=0;
  do {
    $tpDescrip =  $rs->fields["SGD_TPR_CODIGO"] ." | ". $rs->fields["SGD_TPR_DESCRIP"];
    $tpCodigo  =  $rs->fields["SGD_TPR_CODIGO"];

    $arrTDocumentales[$i]=array('tpCodigo'=>$tpCodigo,'tpDescrip'=>$tpDescrip);
    //echo "paso";
    $i++;
    $rs->MoveNext();
  }while(!$rs->EOF);
  //}
  }
  //var_dump($arrTDocumentales);
 echo json_encode($arrTDocumentales);
?>
