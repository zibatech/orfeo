<?php
session_start();
/*
 * Lista Subseries documentales
 * @autor Jairo Losada SuperSOlidaria 
 * @fecha 2009/06 Modificacion Variables Globales.
 */
	foreach ($_GET as $key => $valor)   ${$key} = $valor;
	foreach ($_POST as $key => $valor)   ${$key} = $valor;
	$krd = $_SESSION["krd"];
	$dependencia = $_SESSION["dependencia"];
	$usua_doc = $_SESSION["usua_doc"];
	$codusuario = $_SESSION["codusuario"];
if (!$ruta_raiz) $ruta_raiz= "..";
$sqlFechaDocto =  $db->conn->SQLDate("Y-m-D H:i:s A","mf.sgd_rdf_fech");
$sqlSubstDescS =  $db->conn->substr."(SGD_SRD_DESCRIP, 0, 40)";
$sqlFechaD = $db->conn->SQLDate("Y-m-d H:i A","SGD_SRD_FECHINI");
$sqlFechaH = $db->conn->SQLDate("Y-m-d H:i A","SGD_SRD_FECHFIN");
$isqlC = 'select ID,
			  SGD_SRD_CODIGO          AS "CODIGO",
			'. $sqlSubstDescS .  '    AS "SERIE",
			'.$sqlFechaD.' 			  as "DESDE",
			'.$sqlFechaH.' 			  as "HASTA" 
			from 
				SGD_SRD_SERIESRD
				'.$whereBusqueda.'
			order by  '. $sqlSubstDescS;
     error_reporting(7);
?>
<br><br>
<table  width='100%' cellspacing="5"><tr><td class=titulos2><center><p class="text-success" >SERIES DOCUMENTALES</p></center></td></tr></table>
<br>
<table><tr><td></td></tr></table>
<br>
<TABLE width="850" class="table table-bordered table-hover dataTable smart-form"  cellspacing="5">
  <tr > 
   <th align=center>ID </th>
   <th align=center>CODIGO </th>
   <th align=center>DESCRIPCION </th>
   <th align=center>DESDE </th>
   <th align=center>HASTA </th>
  </tr>
  	<?php
	 	$rsC=$db->query($isqlC);
   		while(!$rsC->EOF)
			{
      	$codserie  =$rsC->fields["CODIGO"];
      	$idSerie  =$rsC->fields["ID"];
	  		$dserie   =$rsC->fields["SERIE"]; 
				$fini     =substr($rsC->fields["DESDE"],0,10);
				$ffin     =substr($rsC->fields["HASTA"],0,10);				
		?> 
    	<tr class=paginacion>
    		<td align=center> <small><?=$idSerie?></small> <i class="fa fa-pencil"  aria-hidden="true" title="Modificar (<?=$codserie?>)<?=$dserie?>" onClick="modificarSerie(<?=$idSerie?>,<?=$codserie?>,'<?=$dserie?>','<?=$fini?>','<?=$ffin?>');"></i></td>  
				<td align=center> <?=$codserie?></td>
				<td align=left> <?=$dserie?> </td>
				<td align=center > <?=$fini?> </td>
				<td align=center > <?=$ffin?> </td>
			</tr>
	<?
				$rsC->MoveNext();
  		}
		//<font face="Arial, Helvetica, sans-serif" class="etextomenu">
		 ?>
   </table>
   
   <script>
    function modificarSerie(idSerie, codSerie, dSerie,fInicio,fFin){
		  $('#idSerieGrb').val(idSerie);
		  $('#idSerieGrbLabel').html(idSerie);
		  $('#codserieI').val(codSerie);
		  $('#detaserie').val(dSerie);
		  $('#fecha_busq').val(fInicio);
		  $('#fecha_busq2').val(fFin);
		  
			
		}
   </script>
