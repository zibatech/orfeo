<?php
/*Modulo de Edicion de Empresa
  Creado por: Ing. Mario Manotas Duran
*/
//session_start();
if (!isset($krd)) $krd = $_POST['krd']; else $krd = $_GET['krd'];
$ruta_raiz="../..";
if(!isset($_SESSION['dependencia']))	include "$ruta_raiz/rec_session.php";
define('ADODB_ASSOC_CASE', 1);
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
if($usModo ==2) $tituloCrear = "Editar Empresas";
//$db->conn->debug=true;
$error = 0;
?>
<html>
<head><title>Untitled Document</title>
<link rel="stylesheet" href="../../estilos/orfeo.css">
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
<script>
function limpiar()
		{
	   document.formEsp.elements['txt_esp'].value = "";
	   document.formEsp.elements['txt_esp1'].value = "";
	   document.formEsp.elements['txt_iden'].value = "";
	   document.formEsp.elements['txt_nit'].value = "";
	   document.formEsp.elements['txt_tel1'].value = "";
	   document.formEsp.elements['txt_tel2'].value = "";
	   document.formEsp.elements['txt_dir'].value = "";
	   document.formEsp.elements['txt_mail'].value = "";
	   document.formEsp.elements['txt_rep'].value = "";
	   document.formEsp.elements['txt_cargo'].value = "";

 
  }
 </script>
<script>
cc_documento = new Array();
nombre = new Array();
repre = new Array();
cargo = new Array();
direccion = new Array();
tel1 = new Array();
tel2 = new Array();
email = new Array();
iden_empresa = new Array();
function pasar(indice)
{
<?
    error_reporting( 0 );
		
    print "document.formEsp.txt_iden.value = iden_empresa[indice];
    	   document.formEsp.txt_nit.value = cc_documento[indice];
	       document.formEsp.txt_esp1.value = nombre[indice];
	       document.formEsp.txt_rep.value = repre[indice];
	       document.formEsp.txt_cargo.value = cargo[indice];
	       document.formEsp.txt_dir.value = direccion[indice];
	       document.formEsp.txt_tel1.value = tel1[indice];
	       document.formEsp.txt_tel2.value = tel2[indice];
	       document.formEsp.txt_email.value = email[indice];"

?>
 
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
<form action="editar.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formEsp" id="formEsp" onSubmit="return envio_datos();">

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



<table class="table table-bordered table-striped" width="93%"  border="1" align="center">
  	<tr bordercolor="#FFFFFF">
    <td colspan="2" class="titulos4">
	<center>
	<p><B><span class=etexto>ADMINISTRACION DE EMPRESAS E.S.P.</span></B> </p>
	<p><B><span class=etexto> <?=$tituloCrear ?></span></B> </p></center>
	</td>
	</tr>
</table>
<table border=1 width=93% class=t_bordeGris align="center">
<tr bordercolor = "#FFFFFF">
<td width="20%" align="center" class="titulos2">NOMBRE EMPRESA E.S.P. A MODIFICAR</td>
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
	<td width="11%" CLASS="titulos5" >IDENTIFICADOR</td>
	<td width="11%" CLASS="titulos5" >NIT EMPRESA</td>
	<td width="11%" CLASS="titulos5" >NOMBRE</td>
	<td width="14%" CLASS="titulos5" >REPRESENTANTE</td>
	<td width="15%" CLASS="titulos5" >CARGO</td>
	<td width="14%" CLASS="titulos5">DIRECCION</td>
	<td width="9%" CLASS="titulos5" >TELEFONO</td>
	<td width="9%" CLASS="titulos5" >TELEFONO_2</td>
	<td width="7%" CLASS="titulos5" >EMAIL</td>
	<td colspan="3" CLASS="titulos5" >SELECCION </td>
</tr> 
<?
if(!empty($_POST['Buscar'])&& ($_POST['Buscar']=="Buscar"))	
{
	$sqlesp="select nit_de_la_empresa,IDENTIFICADOR_EMPRESA, nombre_de_la_empresa,nombre_rep_legal,cargo_rep_legal,direccion,telefono_1,telefono_2,email
	from bodega_empresas where nombre_de_la_empresa like '%".strtoupper($_POST['txt_esp'])."%' and activa=1";
	//$db->conn->debug = true;
	$rsBuscar=$db->query($sqlesp);
	if($rsBuscar && !$rsBuscar->EOF)
	{
		$i=0;
		while(!$rsBuscar->EOF)
		{
?>
	<tr class="grisCCCCCC" align="center"> 
	<td width="11%" CLASS="titulos5" ><font size="-3"><?=$rsBuscar->fields['IDENTIFICADOR_EMPRESA'] ?></font></td>
	<td width="11%" CLASS="titulos5" ><font size="-3"><?=$rsBuscar->fields['NIT_DE_LA_EMPRESA']?></font></td>
	<td width="11%" CLASS="titulos5" ><font size="-3"><?=$rsBuscar->fields['NOMBRE_DE_LA_EMPRESA']?></font></td>
	<td width="14%" CLASS="titulos5" ><font size="-3"><?=$rsBuscar->fields['NOMBRE_REP_LEGAL'] ?></font></td>
	<td width="15%" CLASS="titulos5" ><font size="-3"><?=$rsBuscar->fields['CARGO_REP_LEGAL'] ?></font></td>
	<td width="14%" CLASS="titulos5" ><font size="-3"><?=$rsBuscar->fields['DIRECCION'] ?></font></td>
	<td width="9%" CLASS="titulos5" ><font size="-3"><?=$rsBuscar->fields['TELEFONO_1'] ?></font></td>
	<td width="9%" CLASS="titulos5" ><font size="-3"><?=$rsBuscar->fields['TELEFONO_2'] ?></font></td>
	<td width="7%" CLASS="titulos5" ><font size="-3"><?=$rsBuscar->fields['EMAIL']?></font></td>
	<td width="7%" CLASS="titulos5" ><input name="Envio" type="submit"  class="botones" id="Envio"   value="..." onClick="pasar('<?=$i ?>');"></td>  
  
	</tr>
  <script>
		<?  $iden_empresa=$rsBuscar->fields['IDENTIFICADOR_EMPRESA']; 
			$cc_documento = $rsBuscar->fields['NIT_DE_LA_EMPRESA'];
			$nombre =$rsBuscar->fields['NOMBRE_DE_LA_EMPRESA'];
			$repre =$rsBuscar->fields['NOMBRE_REP_LEGAL'];
                     $direccion=$rsBuscar->fields['DIRECCION'];
			$cargo =$rsBuscar->fields['CARGO_REP_LEGAL'];
			$tel1 = $rsBuscar->fields['TELEFONO_1'];
			$tel2 = $rsBuscar->fields['TELEFONO_2'];
			$email=$rsBuscar->fields['EMAIL'];
			
		?>  iden_empresa[<?=$i?>]= "<?=$iden_empresa?>";
			cc_documento[<?=$i?>]= "<?=$cc_documento?>";
			nombre[<?=$i?>]= "<?=$nombre?>";
			repre[<?=$i?>]= "<?=$repre?>";
			direccion[<?=$i?>]= "<?=$direccion?>";
			cargo[<?=$i?>]= "<?=$cargo?>";
			tel1[<?=$i?>]= "<?=$tel1?>";
			tel2[<?=$i?>]= "<?=$tel2?>";
			email[<?=$i?>]= "<?=$email?>";
			
 </script>	
  <? 	
	$i++;
	$rsBuscar->MoveNext();

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
<br>
<table class=borde_tab width="100%" cellpadding="0" cellspacing="4">
<tr align="center" > 
	<td CLASS=titulos5>ID</td>
	<td CLASS=titulos5>NIT EMPRESA</td>
	<td CLASS=titulos5>NOMBRE EMPRESA</td>
	<td CLASS=titulos5>REPRESENTANTE</td>

	<td CLASS=titulos5>CARGO_REPRESENTANTE</td>
	<td CLASS=titulos5>DIRECCION</td>
	<td CLASS=titulos5>TELEFONO_1</td>
	<td CLASS=titulos5>TELEFONO_2</td>
	<td CLASS=titulos5>EMAIL</td>
	
</tr>
<tr class='listado5' align="center"> 
<td><input type="text" name="txt_iden" class="e_cajas" size="15" value='<?=$txt_iden?>' readonly></td>
<td><input type="text" name="txt_nit" class="e_cajas" size="15" value='<?=$txt_nit?>' maxlength="9" onkeypress="return isNumberKey(event)"></td>
<td><input type="text" name="txt_esp1" class="e_cajas" size="20" value='<?=$txt_esp1?>'></td>
<td><input type="text" name="txt_rep" class="e_cajas" size="20" value='<?=$txt_rep?>'></td>
<td><input type="text" name="txt_dir" class="e_cajas" size="20" value='<?=$txt_dir?>'></td>
<td><input type="text" name="txt_cargo" class="e_cajas" size="20" value='<?=$txt_cargo?>'></td>
<td><input type="text" name="txt_tel1" class="e_cajas" size="20" value='<?=$txt_tel1?>'></td>
<td><input type="text" name="txt_tel2" class="e_cajas" size="15" value='<?=$txt_tel2?>'></td>
<td><input type="text" name="txt_email" class="e_cajas" size="15" value='<?=$txt_email?>'></td>

</tr>
<tr>
	
<td CLASS=titulos5>CONTINENTE</td>
	<td CLASS=titulos5>PAIS</td>
	<td CLASS=titulos5>DPTO</td>
	<td CLASS=titulos5>MUNICIPIO</td>
	<td colspan="2" rowspan="2" CLASS=grisCCCCCC>
		<center>
			<input name="Modificar" type="submit"  class="botones" id="envia22"   value="Modificar">&nbsp;&nbsp;
			<input class="botones" type="button" value="Limpiar" onClick="limpiar();">
		</center>
	</td>
	</tr>
<tr align="center">
<td width="33%">
<?php

$sqlcont0 ="Select B.id_cont, C.nombre_cont from bodega_empresas B, sgd_def_continentes C 
			where identificador_empresa=".$_POST['txt_iden']." 
			AND B.id_cont=C.id_cont";
//$db->conn->debug = true;
$rscont0 = $db->conn->Execute($sqlcont0);
$cont_e=$rscont0->fields["ID_CONT"];
$cont_en=$rscont0->fields["NOMBRE_CONT"];

$sqlcont ="Select nombre_cont, id_cont, $cont_e as cont_id,'$cont_en' as cont_nombre 
			from sgd_def_continentes  ";
//$db->conn->debug = true;
$rscont = $db->conn->Execute($sqlcont);

if(!$id_cont) $id_cont= $cont_e;
	print $rscont->GetMenu2("cont",$cont_e,false,0,"onchange= submit() class='select'");
?></td>
<td width="33%">
<?php
$sqlpais0 ="Select B.id_pais, C.nombre_pais from bodega_empresas B, sgd_def_paises C 
			where identificador_empresa='".$_POST['txt_iden']."' 
			AND B.id_pais=C.id_pais and B.id_cont='$cont_e'";
//$db->conn->debug = true;
$rspais0 = $db->conn->Execute($sqlpais0);
$pais_e=$rspais0->fields["ID_PAIS"];
$pais_en=$rspais0->fields["NOMBRE_PAIS"];

$sqlpais ="Select nombre_pais, id_pais, $pais_e as pais_id,'$pais_en' as pais_nombre  from sgd_def_paises ";
$rspais = $db->conn->Execute($sqlpais);
if(!$id_pais) $id_pais= $pais_e;
print $rspais->GetMenu2("pais",$pais_e,false, 0,"onchange= submit() class='select'");
?></td>
<td width="34%" align="center" valign="bottom">
<?php
$sqldpto0 ="Select B.CODIGO_DEL_DEPARTAMENTO, C.dpto_nomb from bodega_empresas B, departamento C 
			where identificador_empresa='".$_POST['txt_iden']."' 
			AND B.CODIGO_DEL_DEPARTAMENTO=C.dpto_codi and B.id_pais='$pais_e'";
//$db->conn->debug = true;
$rsdpto0 = $db->conn->Execute($sqldpto0);
$dep_e=$rsdpto0->fields["CODIGO_DEL_DEPARTAMENTO"];
$dep_en=$rsdpto0->fields["DPTO_NOMB"];

$sqldpto="Select dpto_nomb, dpto_codi, $dep_e as dep, '$dep_en' as nomdep from departamento order by dpto_nomb ";
$rsdpto=$db->conn->Execute($sqldpto);
if(!s_dpto) $s_dpto=$dep_e;
print $rsdpto->GetMenu2("dpto",$dep_e,false, 0,"onchange= submit() class='select'");
?></td>
<td width="34%" align="center" valign="bottom">
<?php
$sqlmuni0 ="Select B.CODIGO_DEL_DEPARTAMENTO, B.CODIGO_DEL_MUNICIPIO, C.muni_nomb from bodega_empresas B, municipio C 
			where identificador_empresa='".$_POST['txt_iden']."' 
			AND B.CODIGO_DEL_MUNICIPIO=C.muni_codi 
			AND B.CODIGO_DEL_DEPARTAMENTO=C.dpto_codi";
//$db->conn->debug = true;
$rsmuni0 = $db->conn->Execute($sqlmuni0);
$muni_e=$rsmuni0->fields["CODIGO_DEL_MUNICIPIO"];
$muni_en=$rsmuni0->fields["MUNI_NOMB"];

$sqlmuni="Select muni_nomb, muni_codi,$muni_e as codmuni, '$muni_en' as nommuni  from municipio where dpto_codi='$dep_e' order by muni_nomb";
$rsmuni=$db->conn->Execute($sqlmuni);
if(!s_muni) $s_muni=$muni_e;
print $rsmuni->GetMenu2("muni",$muni_e,false, 0,"onchange= submit() class='select'");
?></td>
<?
if(!empty($_POST['Modificar'])&& ($_POST['Modificar']=="Modificar"))
{
	$supdate= ("update bodega_empresas set nit_de_la_empresa ='".ltrim($_POST['txt_nit'])."', nombre_de_la_empresa ='".strtoupper($_POST['txt_esp1'])."',nombre_rep_legal='".strtoupper($_POST['txt_rep'])."',cargo_rep_legal='".strtoupper($_POST['txt_dir'])."',direccion='".strtoupper($_POST['txt_cargo'])."',telefono_1='".$_POST['txt_tel1']."',telefono_2='".$_POST['txt_tel2']."',email='".strtolower($_POST['txt_email'])."',id_cont=".$cont.",id_pais=".$pais.",codigo_del_departamento=".$dpto.",codigo_del_municipio=".$muni." where identificador_empresa=".$_POST['txt_iden']);
        $rsupdate=$db->conn->Execute($supdate);
		$rsupdate=$db->conn->CompleteTrans();
?>
    <script>
		 		 alert("Empresa Actualizada Con Exito");
	</script>
<?
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
</body>
</html>
