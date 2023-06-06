<?php
/*Modulo de Creacion de Empresa
  Creado por: Ing. Mario Manotas Duran
*/
//session_start();
if (!isset($krd)) $krd = $_POST['krd']; else $krd = $_GET['krd'];
$ruta_raiz="../..";
if(!isset($_SESSION['dependencia']))	include "$ruta_raiz/rec_session.php";
define('ADODB_ASSOC_CASE', 1);
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
if($usModo ==1) $tituloCrear = "Creacion Entes";
//$db->conn->debug=true;
$error = 0;
?>
<html>
<head><title>Untitled Document</title>
<link rel="stylesheet" href="../../estilos/orfeo.css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" src="<?=$ruta_raiz?>/js/formchek.js"></script>
<script language="JavaScript" type="text/JavaScript">
function envio_datos()
{
	if(isWhitespace(document.forms[0].txt_esp.value))
		{	alert("El campo Nombre Empresa no ha sido diligenciado.");
			document.forms[0].txt_esp.focus();
			return false;
		}
	if(isWhitespace(document.forms[0].txt_dir.value))
		{	alert("El campo Dirección no ha sido diligenciado.");
			document.forms[0].txt_dir.focus();
			return false;
		}
  	else
	{
		document.forms[0].submit();
		return true;
		 
	}

}
	
	function limpiar()
		{
	   document.formEsp.elements['txt_esp'].value = "";
	   document.formEsp.elements['txt_nuir'].value = "";
	   document.formEsp.elements['txt_nit'].value = "";
	   document.formEsp.elements['txt_sigla'].value = "";
	   document.formEsp.elements['txt_tel1'].value = "";
	   document.formEsp.elements['txt_tel2'].value = "";
	   document.formEsp.elements['txt_dir'].value = "";
	   document.formEsp.elements['txt_mail'].value = "";
	   document.formEsp.elements['txt_rep'].value = "";
	   document.formEsp.elements['txt_cargo'].value = "";
	   document.formEsp.elements['cont'].value = 0;
	   document.formEsp.elements['pais'].value = 0;
	   document.formEsp.elements['dpto'].value = 0;
	   document.formEsp.elements['muni'].value = 0;
 
  }
</script>


 <SCRIPT language=Javascript>
      
      function isNumberKey(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;
      }
      
   </SCRIPT>
   
   
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
<form action="crearEnte.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formEsp" id="formEsp" onSubmit="return envio_datos();">
<table width="93%"  border="1" align="center">
  	<tr bordercolor="#FFFFFF">
    <td colspan="2" class="titulos4">
	<center>
	<p><B><span class=etexto>ADMINISTRACION DE ENTE</span></B> </p>
	<p><B><span class=etexto> <?=$tituloCrear ?></span></B> </p></center>
	</td>
	</tr>
</table>
<table border=1 width=93% class=t_bordeGris align="center">
<tr bordercolor = "#FFFFFF">
<td width="20%" align="center" class="titulos2">NOMBRE EMPRESA ENTE</td>
<td width="20%" colspan="2" align="center"><input class="tex_area" type="text" name="txt_ente" id="txt_ente" size="80" maxlength="200" value='<?=$txt_ente?>'></td>
</tr>
<tr>
<td width="30%" align="center" valign="middle" class="titulos2">TIPO EMP 4</td>
<td width="30%" align="center" valign="middle" class="titulos2">REPRESENTANTE</td>
<td width="30%" align="center" valign="middle" class="titulos2">NIT</td>
</tr>
<tr align="center">
<td width="33%"><input class="tex_area" type="text" name="txt_tipo" id="txt_tipo" maxlength="15" value='<?=$txt_tipo?>'></td>
<td width="33%"><input class="tex_area" type="text" name="txt_repre" id="txt_repre" maxlength="50" value='<?=$txt_repre?>'></td>
<td width="34%"><input class="tex_area" type="text" name="txt_nit" id="txt_nit" value='<?=$txt_nit?>' maxlength="9" onkeypress="return isNumberKey(event)"></td>
</tr>
<tr>
<td width="30%" align="center" valign="middle" class="titulos2">SIGLA EMPRESA</td>
<td width="30%" align="center" valign="middle" class="titulos2">TELEFONO</td>
<td width="30%" align="center" valign="middle" class="titulos2">EMAIL</td>
</tr>
<tr align="center">
<td width="33%"><input class="tex_area" type="text" name="txt_sigla" id="txt_sigla" maxlength="15" value='<?=$txt_sigla?>'></td>
<td width="33%"><input class="tex_area" type="text" name="txt_tel2" id="txt_tel2" maxlength="15" value='<?=$txt_tel2?>'></td>
<td width="34%" align="center" valign="bottom"><input class="tex_area" type="text" name="txt_mail" id="txt_mail" maxlength="50" size="30" value='<?=$txt_mail?>'></td>
</tr>
<tr>
<td colspan="2" align="center" valign="middle" class="titulos2">DIRECCION COMPLETA</td>
<td width="34%" align="center" valign="middle" class="titulos2">ESTADO</td>
</tr>
<tr>
<td colspan="2" align="center" valign="middle"><input class="tex_area" type="text" name="txt_dir" id="txt_dir" size="60" maxlength="160" value='<?=$txt_dir?>'></td>
<td width="34%" align="center">
<select name="Slc_act" id="Slc_act" class="select" onChange="submit()">
   	<option value=1 selected="selected"> Activa </option>
	<option value=0> Inactiva </option>
</select></td>
</tr>
<tr>
<td width="30%" align="center" valign="middle" class="titulos2">CONTINENTE</td>
<td width="30%" align="center" valign="middle" class="titulos2">PAIS</td>
<td width="30%" align="center" valign="middle" class="titulos2">DEPARTAMERNTO</td>
</tr>
<tr align="center">
<td width="33%">
<?php
//print $_POST['Slc_act'];
$sqlcont ="Select nombre_cont, id_cont  from sgd_def_continentes order by id_cont";
$rscont = $db->conn->Execute($sqlcont);
if(!$id_cont) $id_cont= 0;
$valor="";
	print $rscont->GetMenu2("cont",$cont,"0:-- Seleccione --", false,0,"onchange= submit() class='select'");
?></td>

<td width="33%">
<?php
$sqlpais ="Select nombre_pais, id_pais from sgd_def_paises where id_cont= '$cont'";
$rspais = $db->conn->Execute($sqlpais);
if(!$id_pais) $id_pais= 0;
print $rspais->GetMenu2("pais",$pais,"0:-- Seleccione --",false, 0,"onchange= submit() class='select'");
?></td>
<td width="34%" align="center" valign="bottom">
<?php
$sqldpto="Select dpto_nomb, dpto_codi from departamento
          where id_cont='$cont'
		  and id_pais='$pais'
		  and dpto_codi <> 1
		  order by dpto_nomb";
$rsdpto=$db->conn->Execute($sqldpto);
if(!s_dpto) $s_dpto=0;
print $rsdpto->GetMenu2("dpto",$dpto,"0:-- Seleccione --",false, 0,"onchange= submit() class='select'");
?></td>
</tr>
<tr>
<td width="30%" align="center" valign="middle" class="titulos2">MUNICIPIO</td>

</tr>
<tr align="center">
<td width="33%">
<?php
$sqlmuni="Select muni_nomb, muni_codi from municipio
          where id_cont='$cont'
		  and id_pais='$pais'
		  and dpto_codi = '$dpto'
		  order by muni_nomb";
$rsdpto=$db->conn->Execute($sqlmuni);
if(!s_muni) $s_muni=0;
print $rsdpto->GetMenu2("muni",$muni,"0:-- Seleccione --",false, 0,"onchange= submit() class='select'");
?></td>


</table>
<center>
	<input name="Incluir" type="submit"  class="botones" id="envia22"   value="Incluir">&nbsp;&nbsp;
	<input class="botones" type="button" value="Limpiar" onClick="limpiar();">
</center>
<?
if(!empty($_POST['Incluir'])&& ($_POST['Incluir']=="Incluir"))
{
	$sql2="select SGD_OEM_NIT, SGD_OEM_OEMPRESA FROM SGD_OEM_OEMPRESAS WHERE SGD_OEM_NIT= '".ltrim($_POST['txt_nit'])."' OR SGD_OEM_OEMPRESA like '%".strtoupper($_POST['txt_ente'])."%'";
 //$db->conn->debug = true;
 	$rscod = $db->conn->Execute($sql2);
	$carnit=$rscod->fields['SGD_OEM_NIT'];
	$caresp=$rscod->fields['SGD_OEM_OEMPRESA'];
	
	if ($carnit=="" || $caresp==""){
		$sql="select max(SGD_OEM_CODIGO) as ID from SGD_OEM_OEMPRESAS";
		$rsconse=$db->conn->Execute($sql);
		$conse=$rsconse->fields['ID'];
		$conse=$conse+1;
		$sinsert=("INSERT INTO SGD_OEM_OEMPRESAS(SGD_OEM_CODIGO,TDID_CODI,SGD_OEM_OEMPRESA,SGD_OEM_REP_LEGAL,SGD_OEM_NIT,SGD_OEM_SIGLA,MUNI_CODI,DPTO_CODI,SGD_OEM_DIRECCION,SGD_OEM_TELEFONO,ID_PAIS,ID_CONT,SGD_OEM_ESTADO,EMAIL) VALUES (".$conse.",'".ltrim($_POST['txt_tipo'])."', '".strtoupper($_POST['txt_ente'])."', '".strtoupper($_POST['txt_repre'])."','".ltrim($_POST['txt_nit'])."', '".strtoupper($_POST['txt_sigla'])."', ".$muni.",".$dpto.",'".strtoupper($_POST['txt_dir'])."', '".$_POST['txt_tel2']."', ".$pais.", ".$cont.", ".$_POST['Slc_act'].",'".strtolower($_POST['txt_mail'])."')");
		$rsinser=$db->conn->Execute($sinsert);
		$rsinser=$db->conn->CompleteTrans();		
		
?>
 		<script>
		 		 alert("Empresa Creada Con Exito");
		</script>
<?
}
	else{
?>
		<script>
		 		 alert("Esta Empresa ya Existe, no es Posible su Creación");
		</script>
<?
}
}
?>
</form>
</body>
</html>