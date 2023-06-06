<?
$ruta_raiz = ".."; 
$verrad=$_GET['verrad'];
$dependencia = $_SESSION['dependencia'];
$codusuario = $_SESSION['codusuario'];
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);	 

?>
<html>
<head>

<body >
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
  <tr bgcolor="#006699">
    <td class="titulos4" colspan="1" ><center>HISTORICO INFORMADOS PARA <?=$verrad?>  </center></td>
	 </tr>
</table>

<?php
	require_once("$ruta_raiz/class_control/Transaccion.php");
	require_once("$ruta_raiz/class_control/Dependencia.php");
	require_once("$ruta_raiz/class_control/usuario.php");
	$trans = new Transaccion($db);
	$objDep = new Dependencia($db);
	$objUs = new Usuario($db);
?>
<table  width="100%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab" >
  <tr   align="center">
    <td width=10% class="titulos2" height="24">DEPENDENCIA </td>
    <td  width=5% class="titulos2" height="24">FECHA</td>
     <td  width=15% class="titulos2" height="24">TRANSACCION </td>  
    <td  width=15% class="titulos2" height="24" >US. ORIGEN</td>
     <td  width=40% height="24" class="titulos2">COMENTARIO</td>
 <td  width=15% class="titulos2" height="24" >US. DESTINO</font></td>
    </tr>
  <?
  $sqlFecha = $db->conn->SQLDate("d-m-Y H:i A","a.HIST_FECH");

	$isql = "select $sqlFecha HIST_FECH1
      , a.DEPE_CODI
			, a.USUA_CODI
			,a.RADI_NUME_RADI
			,a.HIST_OBSE 
			,a.USUA_CODI_DEST
			,a.USUA_DOC
			,a.HIST_OBSE
			,a.SGD_TTR_CODIGO
			,a.hist_doc_dest
			from hist_eventos a
		 where 
			a.radi_nume_radi =$verrad
			and a.SGD_TTR_CODIGO in (7,8)
			order by hist_fech desc ";  
	//echo "<pre>$isql</pre>";
	//$db->conn->debug=true;
	$i=1;
	$rs = $db->query($isql);
	IF($rs)
	{
    while(!$rs->EOF)
	 {
		$usua_doc_dest = "";
		$usua_doc_hist = "";
		$usua_nomb_historico = "";
		$usua_destino = "";
		$numdata =  trim($rs->fields["CARP_CODI"]);
		if($data =="") $rs1->fields["USUA_NOMB"];
	   		$data = "NULL";
		$numerot = $rs->fields["NUM"];
		$usua_doc_hist = $rs->fields["USUA_DOC"];
		$usua_codi_dest = $rs->fields["USUA_CODI_DEST"];
		$usua_dest=intval(substr($usua_codi_dest,3,3));
		$depe_dest=intval(substr($usua_codi_dest,0,3));
		$usua_codi = $rs->fields["USUA_CODI"];
		$depe_codi = $rs->fields["DEPE_CODI"];
		$codTransac = $rs->fields["SGD_TTR_CODIGO"];
		$descTransaccion = $rs->fields["SGD_TTR_DESCRIP"];
		$histDoctDest=$rs->fields["HIST_DOC_DEST"];
    if(!$codTransac) $codTransac = "0";
		$trans->Transaccion_codigo($codTransac);
		$objUs->usuarioDocto($usua_doc_hist);
		$objDep->Dependencia_codigo($depe_codi);
  
		if($carpeta==$numdata)
			{
			$imagen="usuarios.gif";
			}
		else
			{
			$imagen="usuarios.gif";
			}
		if($i==1)
			{
		?>
  <tr class='tpar'> <?  
		    $i=1;
			}
			 ?>
    <td class="listado2" >
	<?=$objDep->getDepe_nomb()?></td>
    <td class="listado2">
	<?=$rs->fields["HIST_FECH1"]?>
 </td>
<td class="listado2"  >
  <?=$trans->getDescripcion()?>
</td>
<td class="listado2"  >
   <?=$objUs->get_usua_nomb()?>
</td>
 <td class="listado2"><?=$rs->fields["HIST_OBSE"]?></td>
 
 <td class="listado2"><?
  $isqln = "select USUA_NOMB from usuario where usua_doc=$histDoctDest";
	   $rsn = $db->query($isqln);			      	   
	   $usuario_actual = $rsn->fields["USUA_NOMB"];
	print $usuario_actual;   ?></td> 
  </tr>
  <?
	$rs->MoveNext();
  	}
}
  // Finaliza Historicos
	?>
</table>

</body>
</html>
