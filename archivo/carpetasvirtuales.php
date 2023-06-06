<?php
session_start();
$ruta_raiz = "../";
//if($_SESSION['usua_admin_sistema'] !=1 ) die(include "$ruta_raiz/errorAcceso.php");
include("$ruta_raiz/processConfig.php");                       // incluir configuracion.
include_once "$ruta_raiz/htmlheader.inc.php";
include($ADODB_PATH.'/adodb.inc.php');  // $ADODB_PATH configurada en config.php
$error = 0;
$dsn = $driver."://".$usuario.":".$contrasena."@".$servidor."/".$db;
$conn = NewADOConnection($dsn);
?>

<?php 

if (!empty($_POST['slc_anio'])){
$where =$_POST['slc_anio'];

$isql = "select radi_nume_radi as Radicado,
radi_fech_radi as Fecha,
radi_depe_radi as Dependencia 
from radicado
where radi_nume_radi like '$where%' and radi_depe_radi not in (900,999,360)
order by Dependencia";

				 $ok=$conn->Execute($isql);}
 ?>  

<html>
<meta http-equiv="Content-Type" content="text/ HTML; charset=CHARSET" />
<head>

<script type="text/javascript" src="../reportesCRA/jquery-1.3.2.min.js"></script>
<script language="javascript">
$(document).ready(function() {
	$(".botonExcel").click(function(event) {
		$("#datos_a_enviar").val( $("<div>").append( $("#Exportar_a_Excel").eq(0).clone()).html());
		$("#FormularioExportacion").submit();
});
});
</script>
<style type="text/css">
.botonExcel{cursor:pointer;}
</style>

<link rel="stylesheet" href="../estilos/orfeo.css">
<body>
<?php 
if(!$fecha_sel) $fecha_sel=date("Y-m-d");
for($i=2005;$i<=2012;$i++)
{   $sel = ($_POST['slc_anio']==$i) ? "selected" : "";
    $filtro .="<option value='$i' $sel>$i</option>";
}
?>

<?
$params = session_name()."=".session_id()."&krd=$krd";
?>
<center><table width="550" class='table table-striped table-bordered table-hover dataTable no-footer smart-form'><tr><td class='titulos4' align="center">Carpetas Con Radicados Virtuales</td></tr></table></center>
<form action="carpetasvirtuales.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formSeleccion" id="formSeleccion">
<center>
<table width="550" class='borde_tab'>

        <td height="26" class='titulos5'>Selecciona Año De Carpeta Virtual</td>
        <td height="26" class='titulos5'>
            <select name="slc_anio" id="slc_anio" class="select"  onchange="this.form.submit();">
                <option value="">&lt;&lt Todos los a&ntilde;os &gt;&gt;</option>
                <?echo $filtro;?>
            </select>
        </td>
        <td height="26" class='titulos5' align="center">
            <input type="submit" name='btn_accion' id="btn_accion" Value='Mostrar Radicados' class=botones_mediano>
        </td>
  
  </center>
  
<tr>
<table border=2 align="center" class="borde_tab" width="80%" id="Exportar_a_Excel">
                       <tr>
                <td class="titulos2" align="center">RADICADO</td>
                <td class="titulos2" align="center">FECHA DE RADICACION</td>
                <td class="titulos2" align="center">DEPENDENCIA</td>
                        </tr>
</form>

<?php 
if (!empty($_POST['slc_anio'])){
$i=0;
	while(!$ok->EOF){ 
		
	  				$numreginf++;
					
$radicados            = $ok->fields['RADICADO'];
$fechas        = $ok->fields['FECHA'];
$dependencia       = $ok->fields['DEPENDENCIA'];
$path        = $ok->fields['RADI_PATH'];
?>
<tr>
<td class="listado2" align="center"><a href="/orfeo1/bodega/<?=$path?>"><?=$radicados?></a></td>
                <!--<td class="listado2" align="center"><?=$radicados?></td>-->
                <td class="listado2" align="center"><?=$fechas?></td>
                <td class="listado2" align="center"><?=$dependencia?></td>             
 <?php  
$ok->MoveNext();
}
			
	$ind=round($i/$numreginf*100);
	$ind= "$ind %";
		echo "<p><span class=listado2>Número de Registros: " . $numreginf."</span>";	}
	?>	
	
	<form action="../reportesCRA/ficheroExcel.php" method="post" target="_blank" id="FormularioExportacion">
<p>Exportar a Excel  <img src="../reportesCRA/export_to_excel.gif" class="botonExcel" /></p>
<input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
</form>
	
</table>

  </body>
  </html>
