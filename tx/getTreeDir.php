<?php
   $anoContrato = "2015";
   $contenido = file_get_contents("../bodega/treeContratos$anoContrato.txt");
   $contenido = str_replace("│", "*", $contenido);
   $contenido = str_replace("└", "*", $contenido);
   $contenido = str_replace("├", "*", $contenido);
   $contenido = str_replace("─", "*", $contenido);
   $contenido = str_replace(" ", " ", $contenido);
   $contenido = str_replace(" ", " ", $contenido);
   
   $arrContenido = explode("\n", $contenido);
   //var_dump($arrContenido);
   foreach($arrContenido as $key => $value) {
		 //echo substr($value,0,4). "\n";
		 if(substr($value,0,3)=='***'){
			  //echo substr($value, 14,3) ."- ". substr($value, 29,150) ."\n";
			  //echo $value . "\n";
			  $NoContrato = substr($value, 13,3); 
        $noRadicado = "20159990".$NoContrato."9";
			  $noAnexo = 0;
			  //$anoContrato = substr($value, 23,4); 
			  //echo "$anoContrato\n";
			  $noExpediente = $anoContrato."1402104".str_pad($NoContrato,5,"0", STR_PAD_LEFT)."E";
			  $arrExpediente[]=$noExpediente;
			  $arrNombreContrato[$NoContrato] = str_replace( 'CONTRATISTA', '', substr($value, 17,550));
			  $arrCarpetaContrato[$NoContrato] = str_replace( '*** ', '', $value);
			  $arrRutaContrato[$NoContrato] = $noExpediente;
				$carpeta = "";
				
				$arrExpContrato[$NoContrato] = $value;
				//echo $noExpediente ."," .$arrNombreContrato[$NoContrato]." \n";
				
				
		 }
		 if(substr($value,1,6)=='   ***' and substr($value,-3)!="pdf"){
				 $carpeta =  urldecode( substr($value,8,150));
		 }
		 if(substr($value,1,10)=='       ***' and substr($value,-3)!="pdf"){
				 $carpeta .=  "/".urldecode(substr($value,12,150));
		 }
		 //echo ">".substr($value,-3). "\n";
		 $value2 = $value;
		 if(substr($value,-3)=='pdf'){
			   $noAnexo++;
			   $noRadAnexo = $noRadicado."_". str_pad($noAnexo,5,'0', STR_PAD_LEFT);
			   $pos = strripos( $value2, '*')+2;
				 $archivo = html_entity_decode(substr($value,$pos,150));
				 //echo "ruta:(($NoContrato)($noRadAnexo))".$arrRutaContrato[$NoContrato]."/$carpeta/$archivo  (".$arrNombreContrato[$NoContrato].") \n";
				 //echo "cp './".$arrCarpetaContrato[$NoContrato]."/$carpeta/$archivo' /datos/bodegaorfeo/$anoContrato/900/docs/".$noRadAnexo.".pdf \n";  // cOPIA DE LOS ANEXOS
				 echo "$noRadicado , $noRadAnexo , $noAnexo, $carpeta, ".str_replace('.pdf', '', $archivo)." , '$arrCarpetaContrato[$NoContrato]./$carpeta/$archivo', '/$anoContrato/900/docs/".$noRadAnexo.".pdf' \n";  // INSERT DE LOS ANEXOS
				 
		 }
   	
   }
   
 
 //print_r($arrExpediente);


?>
