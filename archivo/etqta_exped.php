<?
  	error_reporting(0); 
  	$krdOld=$krd;
 	session_start(); 
 	if(!$krd) $krd = $krdOld;
 	$ruta_raiz = ".."; 
  	include_once "$ruta_raiz/htmlheader.inc.php";
 	if(!$dependencia) 
 	{
 		include "$ruta_raiz/rec_session.php";
 		//echo $usua_doc;
 	}
 	
	if (!$nurad) $nurad= $rad;
	if($nurad)
	{
		$ent = substr($nurad,-1);
	}
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	
	$db = new ConnectionHandler("$ruta_raiz");
	//$db->conn->debug = true;
	
	if (!defined('ADODB_FETCH_ASSOC')) define('ADODB_FETCH_ASSOC',2);
   	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
   	
	
	$codusua = $codusuario;
 	$params       = session_name()."=".session_id()."&krd=$krd";
	$exped  = (isset($_POST['exped'])) ? $_POST['exped'] : null;
	?>
<html>
<head>
<title>Tipificar Documento</title>
<link href="../estilos/orfeo.css" rel="stylesheet" type="text/css">
<script>

function Start(URL, WIDTH, HEIGHT)
{
    windowprops = "top=0,left=0,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width="+WIDTH+",height="+HEIGHT;
    preview = window.open(URL , "preview", windowprops);
}
function regresar(){   	
	document.TipoDocu.submit();
}
</script>
</head>
<body bgcolor="#FFFFFF">

<form action="etqta_exped.php?<?=$params?>" method="post" enctype="multipart/form-data" name="TipoDocu" id="TipoDocu">

	<table border=0 width=75% align="center" class="table table-striped table-bordered table-hover dataTable no-footer smart-form" cellspacing="0">
	  <tr align="center" class="titulos2">
	    <td height="15" class="titulos2">MODIFICACION DE ETIQUETAS EXPEDIENTES</td>
      </tr>
	 </table> 
 	<table width="75%" border="0" cellspacing="1" cellpadding="0" align="center" class="borde_tab">

 	<tr align="left" colspan="2">
		<td width="31%" class='titulos5'>EXPEDIENTE</td>
		<td width="69%"  class='listado5' align="left">
		<input name="exped" id="exped" type="text"  class="tex_area" size="25" maxlength="17" value="<?php echo $exped?>"">
		<input name="Buscar" type="submit"  class="botones" id="envia22"   value="Buscar">&nbsp;&nbsp;
      <font style="font-family:verdana; font-size:x-small"><b>Nota:
			<font color="Blue">
			Recuerde digitar el n&uacute;mero completo
			</b></font></font>	
		</tr>
	 </table> 
	 
	 	
        <?php 
            	$camposConcatenar = "(" . $db->conn->Concat("sgd_sexp_parexp1",
                                                    "sgd_sexp_parexp2",
                                                    "sgd_sexp_parexp3",
                                                    "sgd_sexp_parexp4",
                                                    "sgd_sexp_parexp5") . ")";
           $isqlE="select SGD_EXP_NUMERO, $camposConcatenar as etiqueta, SGD_SEXP_PAREXP1,SGD_SEXP_PAREXP2, SGD_SEXP_PAREXP3, SGD_SEXP_PAREXP4,SGD_TIPO_CODIGO from sgd_sexp_secexpedientes where sgd_exp_numero='$exped'";                                        
         //  $db->conn->debug=true;
           $rs = $db->conn->Execute($isqlE); 
                    
           	$par1=$rs->fields['SGD_SEXP_PAREXP1'];
            $par2=$rs->fields['SGD_SEXP_PAREXP2'];
            $par3=$rs->fields['SGD_SEXP_PAREXP3'];
            $par4=$rs->fields['SGD_SEXP_PAREXP4'];
            $tipo=$rs->fields['SGD_TIPO_CODIGO'];
          
                                                     
      if(!empty($_POST['Buscar'])&& ($_POST['Buscar']=="Buscar")){  

               
            $numexped=$rs->fields['SGD_EXP_NUMERO'];
            $nexped=$rs->fields['ETIQUETA'];
         
                     
            if ($numexped==""){?>
            <script>
			alert("No existe Registro para Modificar, verifique el expediente digitado ");
			</script><?php
			
            
        }}
?>
        
         
<br>	 
	<TABLE class="titulos4" align="center" width="75%" ><tr align="left">
  <td width="33%" class="borde_tab" height="25" align="left"><input type="radio" name=par value="1" <? if ($par1) echo "checked"; else echo "";?> >ESP</td>
<td width="33%" class="borde_tab" height="25" align="left"><input type="radio" name=par value="2" <? if ($par2) echo "checked"; else echo "";?>>Otras Empr/Asunto</td>
<td width="33%" class="borde_tab" height="25" align="left"><input type="radio" name=par value="3" <? if ($par3) echo "checked"; else echo "";?>>Dependencia</td>
<td width="33%" class="borde_tab" height="25" align="left"><input type="radio" name=par value="4"<? if ($par4) echo "checked"; else echo "";?>>Func/Contratista</td> 
</tr></table>
<TABLE class="titulos4" align="center" width="75%" >
   <td width="33%" class="borde_tab" height="25" align="left">NOMBRE DE EXPEDIENTE:  <input name="nexped" id="nexped" type="text"  class="tex_area" size="100"  value="<?php print $nexped?>">
   <td width="33%" class="borde_tab" height="25" align="center">
   <input type="button" name="Button" value="MODIFICAR POR.." class="botones_largo" onClick="Start('buscarParametro_etqta.php?krd=<?=$krd?>',1024,420);"> </td></td>
   <tr >
<td width="62%" class="borde_tab" >TIPO EXPEDIENTE

<?


$sql = "SELECT 'GENERAL TERCEROS' as ESTA_DESC, 1 AS ESTA_CODI FROM ESTADO
                 UNION  SELECT 'GENERAL E.S.P.' as ESTA_DESC, 2 AS ESTA_CODI FROM ESTADO
                 UNION  SELECT 'ESPECIFICO' as ESTA_DESC, 3 AS ESTA_CODI FROM ESTADO
				 order by ESTA_DESC DESC";

	$rstipo = $db->conn->Execute($sql);
	print $rstipo->GetMenu2("tipo",$tipo, "$opcMenu", false,"","class='select' " );
?>

</td>
</tr>
</table>
<br>
	<table border=0 width=75% align="center" class="borde_tab">
	  <tr align="center">
		<td width="33%" height="25" class="listado2" align="center">
       	 <center><input name="Limpiar" type="submit" class="botones" id="envia22" value="Limpiar"></center></TD>
       	<td width="33%" height="25" class="listado2" align="center"><center><input name="Actualizar" type="submit" class="botones" id="envia22" value="Actualizar"></center></TD>
	   </tr>
	</table>
	

      </td>
	</tr>
    		
	
<?php

if(!empty($_POST['Actualizar'])&& ($_POST['Actualizar']=="Actualizar")){

$sel = $_POST['par'];

if ($sel && $exped){
	
$db->conn->Execute( "UPDATE SGD_SEXP_SECEXPEDIENTES
							SET SGD_SEXP_PAREXP$sel='".$_POST['nexped']."',
							SGD_TIPO_CODIGO='".$_POST['tipo']."'
							WHERE SGD_EXP_NUMERO='$exped'");
//$db->conn->debug=true;
?>
            <script>
			alert("Se ha modificado el nombre de etiqueta del expediente");
			regresar();
			</script><?php
 
}else {
	?>
            <script>
			alert("Indique el Numero o el Grupo de Expediente");
			regresar();
			</script><?php
}

}

if(!empty($_POST['Limpiar'])&& ($_POST['Limpiar']=="Limpiar")){

$sel = $_POST['par'];

if ($sel && $exped){
	
$db->conn->Execute( "UPDATE SGD_SEXP_SECEXPEDIENTES
							SET SGD_SEXP_PAREXP$sel=NULL
							WHERE SGD_EXP_NUMERO='$exped'");
//$db->conn->debug=true;
?>
            <script>
			alert("Se ha borrado el nombre de la etiqueta, introduzca uno nuevo");
			</script><?php
 
}else {
	?>
            <script>
			alert("Indique el Numero o el Grupo de Expediente");
			</script><?php
}

}
?>
  	 
		

	
	
		
</form>
</span>
</span>
</body>
</html>
