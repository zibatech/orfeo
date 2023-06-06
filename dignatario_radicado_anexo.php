<?php
 require("$ruta_raiz/include/tx/usuario.php");
 $classusua = new Usuario($db);

//Busco los valores del destinatario en el radicado padre

/**Coloco el asunto del radicado padre*/

 $sgdTrdCodigoP=0;
 $isql_ = "select * from sgd_dir_drecciones where radi_nume_radi = $numrad order by sgd_dir_tipo";
 $rs_ = $db->query($isql_);
 if($rs_)
 {
  while (!$rs_->EOF) {

  $record = array();
  $nextval = $db->nextId("sec_dir_drecciones");
  
  $rs_sgd_oem_codigo = intval ($rs_->fields['SGD_OEM_CODIGO']);
  $rs_sgd_ciu_codigo = intval($rs_->fields['SGD_CIU_CODIGO']);
  $rs_sgd_doc_fun = intval($rs_->fields['SGD_DOC_FUN']);

  if(!$rs_sgd_oem_codigo) $rs_sgd_oem_codigo="0";
  if(!$rs_sgd_ciu_codigo) $rs_sgd_ciu_codigo="0";
  if(!$rs_sgd_doc_fun) $rs_sgd_doc_fun="0";
  if(!$rs_->fields['MUNI_CODI']) $muniCodiP = 1; else $muniCodiP = $rs_->fields['MUNI_CODI'];
  if(!$rs_->fields['DPTO_CODI']) {$dptoCodiP = 11; $muniCodiP=1;} else{ $dptoCodiP = $rs_->fields['DPTO_CODI'];}
  if(!$rs_->fields['SGD_DIR_DIRECCION']) {$direccionP="";} else{ $direccionP = $rs_->fields['SGD_DIR_DIRECCION'];}
  if(!$rs_->fields['SGD_TRD_CODIGO'] or $rs_->fields['SGD_TRD_CODIGO']=='') {$sgdTrdCodigoP="0";} else{ $sgdTrdCodigoP = $rs_->fields['SGD_TRD_CODIGO'];}
  $sgdTrdCodigoP=str_replace("'","",$sgdTrdCodigoP);
  if(!$sgdTrdCodigoP) $sgdTrdCodigoP=0;
  
  
  $record['SGD_OEM_CODIGO'] = $rs_sgd_oem_codigo;
  $record['SGD_CIU_CODIGO'] = $rs_sgd_ciu_codigo;
  $record['MUNI_CODI'] = $muniCodiP;
  $record['DPTO_CODI'] = $dptoCodiP;
  $record['SGD_DIR_DIRECCION'] = $direccionP;
  $record['SGD_DIR_TELEFONO'] = $rs_->fields['SGD_DIR_TELEFONO'];
  $record['SGD_DIR_MAIL'] = $rs_->fields['SGD_DIR_MAIL'];
  $record['SGD_DIR_NOMBRE'] = $rs_->fields['SGD_DIR_NOMBRE'];
  $record['SGD_DOC_FUN'] = $rs_sgd_doc_fun;
  $record['SGD_DIR_NOMREMDES'] = $rs_->fields['SGD_DIR_NOMREMDES'];
  $record['SGD_TRD_CODIGO'] = $sgdTrdCodigoP;
  $record['SGD_DIR_DOC'] = $rs_->fields['SGD_DIR_DOC'];
  $record['ID_PAIS'] = $rs_->fields['ID_PAIS'];
  $record['ID_CONT'] = $rs_->fields['ID_CONT'];
  $record['SGD_DIR_TIPO'] = $rs_->fields['SGD_DIR_TIPO'];
  $record['SGD_DIR_CODIGO'] = $nextval;
  $record['RADI_NUME_RADI'] = $noRad;
    //$db->conn->debug = true;
      $insertSQL = $db->conn->Replace("SGD_DIR_DRECCIONES",
      $record,
      'SGD_DIR_CODIGO, RADI_NUME_RADI',
      $autoquote = true);
     
      if(!empty($insertSQL)){
        // $this->result =  array( "state"  => true, "value" => $nextval);
      //return true;
      }else{
        echo "no se pudo insertar el destinatario / dignatario_radicado_anexo.php"; 
        exit;
          //$this->db->log_error ("666-- $nurad ","No se pudo agrear usuario al radicado", $record,1);
          //$this->result = array( "error"  => 'No se puedo agregar usuario al radicado');
          //return false;
      }
    $rs_->MoveNext();
  } 
 } else {
 //saveMessage('error',"No se ha podido obtener la informacion del radicado.");
 //die(json_encode($answer));
  echo "No se pudo obtener información del destinatario"; exit;
 }

?>
