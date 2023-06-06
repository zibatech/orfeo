<link rel="stylesheet" href="../estilos/orfeo.css">
<table width="100%"  border="0" cellpadding="0" cellspacing="5" class="table table-striped table-bordered table-hover dataTable no-footer smart-form">
<tr>
	<td class="titulos3" width="1">#</td>
<?
	$check=1;
	$fieldCount = $rsE->FieldCount();
	if($ascdesc=="") $ascdesc = " desc ";
	else $ascdesc = "";
	
	for($iE=0; $iE<=$fieldCount-1; $iE++) {	
		$fld = $rsE->FetchField($iE);
		/** El siguietne "if" Omite las columnas que venga con encabezado HID
		*/	
		if(substr($fld->name,0,3)!="HID") {
?>
	<td class="titulos3"><? $linkPaginaActual = $_SERVER['PHP_SELF'];	?>
		<a href='<?=$linkPaginaActual?>?<?=$datosaenviar?>&ascdesc=<?=$ascdesc?>&orno=<?=($iE+1)?>&generarOrfeo=Busquedasss&genDetalle=<?=$genDetalle?>&genTodosDetalle=<?=$genTodosDetalle?>&fenvCodi=<?=$fenvCodi?>&tipoDocumento=<?=$tipoDocumento?>' >
			<?	echo $fld->name; ?>
		</a>
	</td>
<?
		}
	}
	if(!$genDetalle)
	{
?>
	<td class="titulos3"></td>
<?
	}
?>
</tr> 
<?
$iRow = 1;
$datosCod = 0;
while(!$rsE->EOF)
{
/**  INICIO CICLO RECORRIDO DE LOS REGISTROS
  *	 En esta seccion se recorre todo el query solicitado
  *  @numListado Int Variable que almacena 1 O 2 dependiendo de la clase requerida.(Resultado de modulo con doos )
  */
	$numListado = fmod($iRow,2);
	if($numListado==0)
	{	$numListado = 2;	}
?>
<tr class='listado<?=$numListado?>' >
	<td width="1"><?=$iRow?></td>
<?
	$fieldCount = $rsE->FieldCount();
	for($iE=0; $iE<=$fieldCount-1; $iE++)
	{	
		$fld = $rsE->FetchField($iE);
		
		if(substr($fld->name,0,3)!="HID") 
		{
?>
	<td>
<?
			$pathImg = "";
			if($fld->name=="RADICADO") 
			{	$pathImg = $rsE->fields["HID_RADI_PATH"];
				if(trim($pathImg)) 
				{	echo "<a href=$ruta_raiz/bodega/$pathImg>";	}
			}
			
			// busca el campo de numero de expediente para asignar en ver detalles
			if ($fld->name == "SGD_EXP_NUMERO")
				$expedientes = $rsE->fields["$fld->name"];
			
			if ($fld->name == "ESTADO") 
				$rsE->fields["$fld->name"] = ($rsE->fields["$fld->name"] ==0) ? 'Sin Archivar' : 'Archivado';
			
			echo $rsE->fields["$fld->name"];
			if(trim($pathImg)) 
			{	echo "</a>";	}
?>
	</td>
<?
		} // fIN DEL IF QUE OMITE LAS COLUMNAS CON HID_
		if($fld->name=="HID_COD_USUARIO") 
		{	$datosEnvioDetalle="codUs=".$rsE->fields["$fld->name"];	} 
		if($fld->name=="USUARIO") 
		{	$nombUs[($iRow-1)]=substr($rsE->fields["$fld->name"],0,21);
			$nombXAxis = "USUARIO";
		}
		if($fld->name=="MEDIO_RECEPCION") 
		{	$nombUs[($iRow-1)]=substr($rsE->fields["$fld->name"],0,21);
			$nombXAxis = "MED RECEPCION";
		}					
		if($fld->name=="MEDIO_ENVIO") 
		{	$nombUs[($iRow-1)]=substr($rsE->fields["$fld->name"],0,21);
			$nombXAxis = "MED ENVIO";
		}										
		if($fld->name=="RADICADOS") 
		{	$data1y[($iRow-1)]=$rsE->fields["$fld->name"];
			$nombYAxis = "RADICADOS";
		}
		if($fld->name=="TOTAL_ENVIADOS") 
		{	$data1y[($iRow-1)]=$rsE->fields["$fld->name"];
			$nombYAxis = "RADICADOS";
		}					
		if($fld->name=="HOJAS_DIGITALIZADAS") 
		{	$data2y[($iRow-1)]=$rsE->fields["$fld->name"];
			$nombYAxis .= " / HOJAS DIGITALIZADAS";
		}										
		if($fld->name=="HID_MREC_CODI") $datosEnvioDetalle.="&mrecCodi=".$rsE->fields["$fld->name"];
		if($fld->name=="HID_CODIGO_ENVIO") $datosEnvioDetalle.="&fenvCodi=".$rsE->fields["$fld->name"];
		if($fld->name=="HID_TPR_CODIGO") $datosEnvioDetalle.="&tipoDOCumento=".$rsE->fields["$fld->name"];
		if($fld->name=="HID_COD_DEPE") $datosEnvioDetalle.="&depeUs=".$rsE->fields["$fld->name"];
		if($fld->name=="HID_FECH_SELEC") $datosEnvioDetalle.="&fecSel=".$rsE->fields["$fld->name"];
	}
	if(!$genDetalle)
	{	if($genTodosDetalle==1)
		{
?>
			<td align="center">
				<A href="genEstadistica.php?<?=$datosEnvioDetalle?>&genDetalle=1&<?=$datosaenviar?>" Target="VerDetalle<?=date("dmYHis")?>"></a>
			</td>
<?
		}
		else
		{
			if ($tipoEstadistica == 13 ) {
				$datosaenviarCodExp = "fechaf=$fechaf&tipoEstadistica=$tipoEstadistica&codus=$usuadoc[$datosCod]&krd=$krd&dependencia_busq=$dependencia_busq&ruta_raiz=$ruta_raiz&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&tipoRadicado=$tipoRadicado&tipoDocumento=$tipoDocumento&depCodigo=$dependencias[$datosCod]&expediente=$expedientes";
			?>
			<td align="center">
				<A href="../include/query/archivo/queryReportePorRadicados.php?<?=$datosEnvioDetalle?>&genDetalle=1&<?=$datosaenviarCodExp?>" Target="VerDetalle<?=date("dmYHis")?>" class="vinculos">VER DETALLES</a>
			</td>
			<?
			}
			elseif (isset($usuadoc)) {
				$datosaenviarCod = "fechaf=$fechaf&tipoEstadistica=$tipoEstadistica&codus=$usuadoc[$datosCod]&krd=$krd&dependencia_busq=$dependencia_busq&ruta_raiz=$ruta_raiz&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&tipoRadicado=$tipoRadicado&tipoDocumento=$tipoDocumento&depCodigo=$dependencias[$datosCod]";
				$datosCod++;
			?>
			<td align="center">
				<A href="../archivo/genEstadistica.php?<?=$datosEnvioDetalle?>&genDetalle=1&<?=$datosaenviarCod?>" Target="VerDetalle<?=date("dmYHis")?>" class="vinculos">VER DETALLES</a>
			</td>
			<?
			} else {
			?>
			<td align="center">
				<A href="../archivo/genEstadistica.php?<?=$datosEnvioDetalle?>&genDetalle=1&<?=$datosaenviar?>" Target="VerDetalle<?=date("dmYHis")?>" class="vinculos">VER DETALLES</a>
			</td>
			<?
			}
		}
	}
?>
</tr> 
<?
if($check<=20){ 
	 $check=$check+1;
	 }
$rsE->MoveNext();
/**  FIN CICLO RECORRIDO DE LOS REGISTROS
  */
 $iRow++;
 $datosEnvioDetalle="";
}
$_SESSION["data1y"] = $data1y;
$_SESSION["nombUs"] = $nombUs;
$noRegs = count($data1y);
?>
</table>

<?
error_reporting(7);
$nombreGraficaTmp = "$ruta_raiz/bodega/tmp/E_$krd.png";
$rutaImagen = $nombreGraficaTmp;
$notaSubtitulo = $subtituloE[$tipoEstadistica]."\n";
$tituloGraph = $tituloE[$tipoEstadistica];
?>
<br><center><span class="listado5">
Items <?=($iRow-1)?>
</span>
<? 
if ($tipoEstadistica==1 or $tipoEstadistica==3 or $tipoEstadistica==6 or $tipoEstadistica==8 or 
	$tipoEstadistica==12  or $tipoEstadistica==13)
{

	if ($genTodosDetalle==1 or $genDetalle==1)
	{
?>
		<Br>
			<A href="genEstadistica.php?<?=$datosEnvioDetalle?>&genTodosDetalle=1&<?=$datosaenviar?>" Target="VerDetalle<?=date("dmYHis")?>"></a>
		</Br>
<?
	}
	else
	{
?>

<table border=0 cellspace=2 cellpad=2 WIDTH=100% class='borde_tab' align='center'>
<tr align="center"> 
	<td> <?	 		
	// Se calcula el numero de | a mostrar
	$rsE=$db->query($isqlCount);
	//$numerot = $rsE->fieldCount();
	$paginas = (($iRow-1) / 20);
	?><span class='vinculos'>Paginas </span> <?
	if(intval($paginas)<=$paginas)
	{$paginas=$paginas;}else{$paginas=$paginas-1;}
	// Se imprime el numero de Paginas.
	for($ii=0;$ii<$paginas;$ii++)
	{
	  if($pagina==$ii){$letrapg="<font color=green size=3>";}else{$letrapg="";}
	  echo " <a href='tablaHtml.php?pagina=$ii&$encabezado$orno'><span class=leidos>$letrapg".($ii+1)."</span></font></a>\n";
	}
 echo "<input type=hidden name=check value=$check>";
?></td>
</tr></table>
<form name=jh >
 <input type=hidDEN name=jj value=0>
  <input type=hidDEN name=dS value=0>
 </form>
		<Br>
			<A href="genEstadistica.php?<?=$datosEnvioDetalle?>&genTodosDetalle=1&<?=$datosaenviar?>" Target="VerDetalle<?=date("dmYHis")?>" class="vinculos">VER TODOS LOS DETALLES</a>
		</Br>
<?
	}
}
if($genDetalle!=1 and $noRegs>=1)
{	include "genBarras1.php";
?>
	 <br><input type=button class="botones_largo" value="Ver Grafica" onClick='window.open("./image.php?rutaImagen=<?=$rutaImagen."&fechaH=".date("YmdHis")?>" , "Grafica Estadisticas - Orfeo", "top=0,left=0,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=560,height=720");'>
<?
}
?>
</center>
</body>
</html>
