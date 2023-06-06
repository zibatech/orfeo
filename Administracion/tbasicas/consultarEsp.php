<?php
/*Modulo de Consulta de Empresa
  Creado por: Ing. Mario Manotas Duran
*/
session_start();
if (!isset($krd)) $krd = $_POST['krd']; else $krd = $_GET['krd'];
$ruta_raiz="../..";
if(!isset($_SESSION['dependencia']))	include "$ruta_raiz/rec_session.php";
define('ADODB_ASSOC_CASE', 1);
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
if($usModo ==3) $tituloCrear = "Consultar Empresas";
//$db->conn->debug=true;
$error = 0;
?>
<html>
<head><title>Untitled Document</title>
<link rel="stylesheet" href="../../estilos/orfeo.css">
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
function envio_datos()
{
	if(isWhitespace(document.forms[0].txt_esp.value))
		{	alert("El campo Nombre Empresa no ha sido diligenciado.");
			document.forms[0].txt_esp.focus();
			return false;
		}
	  	else
	{
		document.forms[0].submit();
		return true;
		 
	}
}
</script>
</head>
<?
$params = session_name()."=".session_id()."&krd=$krd";
?>
<body>
<?
    include "$ruta_raiz/processConfig.php";
	include_once "$ruta_raiz/include/db/ConnectionHandler.php";
    $db = new ConnectionHandler("$ruta_raiz");
    if (!defined('ADODB_FETCH_ASSOC'))define('ADODB_FETCH_ASSOC',2);
    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	//$db->conn->debug = true;
?>
<form action="consultarEsp.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formEsp" id="formEsp" onSubmit="return envio_datos();">

<div class="col-sm-12">
     <!-- widget grid -->
     <section id="widget-grid">
       <!-- row -->
       <div class="row">
         <!-- NEW WIDGET START -->
         <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
           <!-- Widget ID (each widget will need unique ID)-->
           <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">
 
             <header>
               <h2>
                 Administrador de tablas sencillas
               </h2>
             </header>
 
             <!-- widget div-->
             <div>
               <!-- widget content -->
               <div class="widget-body no-padding">


<table border=1 width=93% class=t_bordeGris align="center">
<tr bordercolor = "#FFFFFF">
<td width="20%" align="center" class="titulos2">NOMBRE EMPRESA E.S.P.</td>
<td width="20%" align="center"><input class="tex_area" type="text" name="txt_esp" id="txt_esp" size="60" maxlength="200" value='<?=$txt_esp?>'></td>
<td width="20%" align="center"><input name="Buscar" type="submit"  class="botones" id="envia22"   value="Buscar"></td>
</tr>
</table>
<br>
<TABLE class="borde_tab" width="100%">
<tr class=listado2><td colspan=10>
<center>RESULTADO DE BUSQUEDA</center>
</td></tr></TABLE>
<table class=borde_tab width="100%" cellpadding="0" cellspacing="5">
  <!--DWLayoutTable-->
<tr class="grisCCCCCC" align="center"> 
	<td width="11%" CLASS="titulos5" >NIT EMPRESA</td>
	<td width="11%" CLASS="titulos5" >NOMBRE</td>
	<td width="14%" CLASS="titulos5" >REPRESENTANTE</td>
	<td width="15%" CLASS="titulos5" >CARGO</td>
	<td width="14%" CLASS="titulos5">DIRECCION</td>
	<td width="9%" CLASS="titulos5" >TELEFONO</td>
	<td width="7%" CLASS="titulos5" >EMAIL</td>
	<td colspan="3" CLASS="titulos5" >DEPARTAMENTO</td>
	<td colspan="3" CLASS="titulos5" >MUNICIPIO</td>
	<td colspan="3" CLASS="titulos5" >PAIS</td>
</tr> 
<?
if(!empty($_POST['Buscar'])&& ($_POST['Buscar']=="Buscar"))	
{
$sqlesp="select b.nit_de_la_empresa,
         b.nombre_de_la_empresa,
         b.nombre_rep_legal,
         b.cargo_rep_legal,
         b.direccion,
         b.telefono_1,
         b.email,
         d.dpto_nomb,
         m.muni_nomb,
         p.nombre_pais
         from bodega_empresas b, sgd_def_paises p, departamento d, municipio m
         where  b.CODIGO_DEL_DEPARTAMENTO=d.DPTO_CODI
	     and b.CODIGO_DEL_MUNICIPIO=m.MUNI_CODI
	     and d.DPTO_CODI=m.DPTO_CODI
	     and b.id_pais = p.ID_PAIS
	     and b.nombre_de_la_empresa like '%".strtoupper($_POST['txt_esp'])."%'";
	//$db->conn->debug = true;
	$rs=$db->query($sqlesp);
	if($rs && !$rs->EOF)
	{
		while(!$rs->EOF)
		{
?>
	<tr class="grisCCCCCC" align="center"> 
	<td width="11%" CLASS="titulos5" ><font size="-3"><?=$rs->fields['NIT_DE_LA_EMPRESA']?></font></td>
	<td width="11%" CLASS="titulos5" ><font size="-3"><?=$rs->fields['NOMBRE_DE_LA_EMPRESA']?></font></td>
	<td width="14%" CLASS="titulos5" ><font size="-3"><?=$rs->fields['NOMBRE_REP_LEGAL'] ?></font></td>
	<td width="15%" CLASS="titulos5" ><font size="-3"><?=$rs->fields['CARGO_REP_LEGAL']?></font></td>
	<td width="14%" CLASS="titulos5"><font size="-3"><?=$rs->fields['DIRECCION']?></font></td>
	<td width="9%" CLASS="titulos5" ><font size="-3"><?=$rs->fields['TELEFONO_1']?></font></td>
	<td width="7%" CLASS="titulos5" ><font size="-3"><?=$rs->fields['EMAIL'] ?></font></td>
	<td colspan="3" CLASS="titulos5" ><font size="-3"><?=$rs->fields['DPTO_NOMB']?></font></td>
	<td colspan="3" CLASS="titulos5" ><font size="-3"><?=$rs->fields['MUNI_NOMB']?></font></td>
	<td colspan="3" CLASS="titulos5" ><font size="-3"><?=$rs->fields['NOMBRE_PAIS']?></font></td>
	</tr> 
<?
			$rs->MoveNext();		
		}
	}
	else
	{
?>
	<script>
		 	alert("No Se Encontraron Registros");
	</script>
<?
	}
	
}
?>
</table>

</div>
             </div>
           </div>
         </article>
       </div>
     </section>
   </div>
 </form>


</form>
</body>
</html>
