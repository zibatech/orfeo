<?php
session_start();
if (!$ruta_raiz) $ruta_raiz= "..";
$sqlFechaDocto =  $db->conn->SQLDate("Y-m-D H:i:s A","mf.sgd_rdf_fech");
$sqlSubstDescS =  $db->conn->substr."(SGD_TPR_DESCRIP, 0, 75)";
$isqlC = "select 
			  SGD_TPR_CODIGO          AS CODIGO,
			 $sqlSubstDescS    AS TIPOD,
			  SGD_TPR_TERMINO		  as TERMINO,
			  SGD_TPR_TP2   		  as ENTRADA,
			  SGD_TPR_TP1   		  as SALIDA,
			  SGD_TPR_TP3   		  as MEMORANDO,
			  SGD_TPR_TP5   		  as RESOLUCION,
			  SGD_TPR_ESTADO 	      as ESTADO 
			from 
				SGD_TPR_TPDCUMENTO
				
				WHERE SGD_TPR_estado in(1,0)
                     order by CODIGO";
			
			
			//$whereBusqueda	
			//and SGD_TPR_estado=1
			//order by  $sqlSubstDescS
			//echo $isqlC;
$db->conn->debug=true;
     error_reporting(0);
?>
<table class=borde_tab width='100%' cellspacing="5"><tr><td class=titulos2><center>TIPOS DOCUMENTALES</center></td></tr></table>
<table><tr><td></td></tr></table>
<br>
<TABLE width="850" class="borde_tab" cellspacing="6">
  <tr class=tpar> 
   <td class=titulos3 align=center>CODIGO </td>
   <td class=titulos3 align=center>DESCRIPCION </td>
   <td class=titulos3 align=center>TERMINO </td>
   <td class=titulos3 align=center>ENTRADA </td>
   <td class=titulos3 align=center>SALIDA </td>
   <td class=titulos3 align=center>MEMORANDO </td>
   <td class=titulos3 align=center>RESOLUCION </td>
   <td class=titulos3 align=center>ESTADO </td>
  </tr>
  	<?php
	 	$rsC=$db->query($isqlC);
   		while(!$rsC->EOF)
			{
      			$codserie  =$rsC->fields["CODIGO"];
	  			$dtipod   =$rsC->fields["TIPOD"]; 
				$vtermi   =$rsC->fields["TERMINO"];
				$ventrad  =$rsC->fields["ENTRADA"];	
				$vsalida  =$rsC->fields["SALIDA"];				
				$vmemo    =$rsC->fields["MEMORANDO"];
				$vmemo    =$rsC->fields["RESOLUCION"];
				$vestado  =$rsC->fields["ESTADO"];				
		?> 
    		  <tr class=paginacion>
				<td> <?=$codserie?></td>
				<td align=left> <?=$dtipod?> </td>
				<td> <?=$vtermi?> </td>
				<td> <?=$ventrad?> </td>
				<td> <?=$vsalida?> </td>
				<td> <?=$vmemo?> </td>
				<td> <?=$vreso?> </td>
				<td> <?=$vproye?> </td>
				<td> <?=$vestado?> </td>
		 	  </tr>
	<?
				$rsC->MoveNext();
  		}
		//<font face="Arial, Helvetica, sans-serif" class="etextomenu">
		 ?>
   </table>