<?php
if(isset($actu_mtrd) && $coddepe !=0 && $idSerie !=0 && $idSubSerie !=0) {
  $num = count($checkValue);
  $i   = 0;
  
	$iSql = "SELECT * FROM sgd_sbrd_subserierd WHERE sgd_srd_id='$idSerie' and id='$idSubSerie'";
	$rsSbrd = $db->conn->query($iSql);
	if(!$rsSbrd->EOF){
		$codserie=$rsSbrd->fields["SGD_SRD_CODIGO"];	
		$tsub=$rsSbrd->fields["SGD_SBRD_CODIGO"];	
	}

  while ($i < $num) {
    $record_id = key($checkValue);
    $radicados_asig .= $record_id .",";
    $radicados_sel = $record_id;
    $chkt = $radicados_sel;
    $isqlB = "select t.sgd_tpr_codigo as CODIGO, m.sgd_mrd_codigo as MRDCODIGO
      from sgd_mrd_matrird m, sgd_tpr_tpdcumento t
      where m.depe_codi     = '$coddepe'
      and m.sgd_srd_id  = '$idSerie'
      and m.sgd_sbrd_id = '$idSubSerie'
      and m.sgd_tpr_codigo  = t.sgd_tpr_codigo
      and t.sgd_tpr_codigo  = '$chkt'
      ";
    $rs = $db->query($isqlB); # Executa la busqueda y obtiene el registro a actualizar.
    $TPR_CODIGO = $rs->fields["CODIGO"];
    $SGD_MRD_CODIGO = $rs->fields["MRDCODIGO"];

    if ($TPR_CODIGO !='') {
      $record = array(); # Inicializa el arreglo que contiene los datos a insertar
      $record["DEPE_CODI_APLICA"] = "'".$depeCodiAplica."'";
      $record["SGD_MRD_CODIGO"] = "'".$SGD_MRD_CODIGO."'";

      $insertSQL =  $db->conn->Replace("SGD_MRD_MATRIRD",
        $record,
        'SGD_MRD_CODIGO',
        $autoquote = true);
    }else{
      $isqlCount = "select max(sgd_mrd_codigo) as NUMREGT from sgd_mrd_matrird";
      $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
      $rsC = $db->query($isqlCount);
      $numreg = $rsC->fields["NUMREGT"];
      $numreg = $numreg+1;

      $record = array(); # Inicializa el arreglo que contiene los datos a insertar
      $record["SGD_MRD_CODIGO"]   = $numreg;
      $record["DEPE_CODI"]        = $coddepe;
      $record["SGD_SRD_CODIGO"]   = $codserie;
      $record["SGD_SBRD_CODIGO"]  = $tsub;
      $record["SGD_SRD_ID"]   = $idSerie;
      $record["SGD_SBRD_ID"]  = $idSubSerie;      
      $record["SGD_TPR_CODIGO"]   = $chkt;
      $record["SOPORTE"]          = $med;
      $record["SGD_MRD_ESTA"]     = '1';
      $record["SGD_MRD_FECHINI"]  = $db->conn->OffsetDate(0);
      $record["DEPE_CODI_APLICA"] = "'".$depeCodiAplica."'";
      //	$insertSQL = $db->insert("SGD_MRD_MATRIRD", $record, "true");
    }
    next($checkValue);
    $i++;
  }
}
?>
