<?php
  $ruta_raiz = "..";
  include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
  if (!$db) $db = new ConnectionHandler($ruta_raiz);
  //$db->conn->debug = true;
  $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
  $sqlFecha = $db->conn->SQLDate("Y-m-d H:i A","b.RADI_FECH_RADI");

  include "dataExpInsert.php";
   foreach($arrInserts as $key => $value) {
    $iSql = $value;
	  
	  echo "Insertando $key) ";
	  if($db->conn->execute($iSql)) echo "Insertando Ok\n"; else echo "Error al insertar \n $iSql \n\n";
		 
		 
		 
		 
		 
   }


?>
