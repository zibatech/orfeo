<?
  	error_reporting(0); 
 	session_start(); 
 	error_reporting(0);
	$ruta_raiz = ".."; 
	
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	$db = new ConnectionHandler("$ruta_raiz");
	if (!defined('ADODB_FETCH_ASSOC'))	define('ADODB_FETCH_ASSOC',2);
   	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$encabezadol = "$PHP_SELF?".session_name()."=".session_id()."&krd=$krd&terminot=$terminot&codusua=$codusua&depende=$depende&ent=$ent";

include ("$ruta_raiz/busqueda/common.php");  
($_POST["s_Entrada"]) ? $flds_Entrada = $_POST["s_Entrada"] : $flds_Entrada = 0 ;
if($flds_entrada) $checkEntrada = "checked";

($_POST["s_Salida"]) ? $flds_Salida = $_POST["s_Salida"] : $flds_Salida = 0 ;
if($flds_salida) $checkSalida = "checked";

($_POST["s_Memo"]) ? $flds_Memo = $_POST["s_Memo"] : $flds_Memo = 0 ;
if($flds_memo) $checkMemo = "checked";

($_POST["s_Reso"]) ? $flds_Reso = $_POST["s_Reso"] : $flds_Reso = 0 ;
if($flds_reso) $checkReso = "checked";

($_POST["s_Proye"]) ? $flds_Proye = $_POST["s_Proye"] : $flds_Proye = 0 ;
if($flds_proye) $checkProye = "checked";	

($_POST["terminot"]) ? $terminot=$_POST["terminot"] : $terminot = 0 ;

$detatipod = strtoupper(trim($_POST['detatipod']));

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../estilos/orfeo.css">
<script>
function regresar(){   	
	document.adm_tipodoc.submit();
}
</script>
</head>
<body bgcolor="#FFFFFF">
 <div id="spiffycalendar" class="text"></div>
<table class=borde_tab width='100%' cellspacing="5"><tr><td class=titulos2><center>TIPOS DOCUMENTALES</center></td></tr></table>
<table><tr><td></td></tr></table>

<form method="post" action="<?=$encabezadol?>" name="adm_tipodoc"> 
<center>
<TABLE width="600" class="borde_tab" cellspacing="5"> 
<TR>
<? if($_POST['actua_tdoc'])
  {
?>
   <TR>
    <TD width="125" height="21"  class='titulos2'> C&oacute;digo<br> </td>
	<TD valign="top" colspan="3" align="left" class='listado2'><input type=text name=codtdocI value='<?=$codtdocI?>' class='tex_area' size=11 maxlength="7" >
	<td width="125" height="21"><input type=submit name=modi_tdoc Value='Grabar Modificacion' class=botones_largo ></td>
    </td>
	</tr>
<?
}
?>    
     <TD height="26" width="30%" class='titulos2'> Descripci&oacute;n</td>
	  <TD valign="top" colspan="4" align="left" class='listado2'><input type=text name=detatipod value='<?=$detatipod?>' class='tex_area' size=75 maxlength="75" ></td>
    </tr>
  <tr> 
    <TD height="26"  width="30%" class='titulos2'>Termino tramite<br></td>
 	<TD valign="top" colspan="4" align="left" class='listado2'><input type=text name=terminot value='<?=$terminot?>' class='tex_area' size=75 maxlength="7" >
	</TD>
  </TR>
  <tr > 
      <td class="titulos5" colspan="5" width="100%">Seleccione el tipo de documento
	  </tr > 
 
  <tr > 
      <td class="titulos5" width="20%">
	     <INPUT type="checkbox" NAME="s_Entrada" value="ENTRADA" <?=$checkEntrada?> onClick="document.adm_tipodoc.elements['s_Entrada'].checked=true;">
             Entrada</td>
      <td  class="titulos5" width="20%"> 
         <INPUT type="checkbox" NAME="s_Salida" value="SALIDA" onClick="document.adm_tipodoc.elements['s_Salida'].checked=true;" <?=$checkSalida?>>
		      Salida</td>
     <td class="titulos5" width="25%">
         <INPUT type="checkbox" NAME="s_Memo" value="MEMO" onClick="document.adm_tipodoc.elements['s_Memo'].checked= true;" <?=$checkMemo?> >
              Memorando</td>
     <td class="titulos5" width="25%">
         <INPUT type="checkbox" NAME="s_Reso" value="RESO" onClick="document.adm_tipodoc.elements['s_Reso'].checked=true;" <?=$checkReso?> >
              Resolucion</td>
     <td width="25%"  class="titulos5">
         <INPUT type="checkbox" NAME="s_Proye" value="PROYE" onClick="document.adm_tipodoc.elements['s_Proye'].checked=true;" <?=$checkProye?> >
              Proyecto</td>
          </tr>
  <tr>
       <td height="26" colspan="5" valign="top" class='titulos2'> <center>
	   <input type=submit name=buscar_dcto Value='Buscar' class=botones >
	    <input type=submit name=insertar_tdoc Value='Insertar' class=botones >
		<input type=submit name=actua_tdoc Value='Modificar' class=botones >
      <input type="reset"  name=aceptar class=botones id=envia22  value='Cancelar'>	  
   </td>
    </tr>
  </table>
<?PHP
if($flds_Entrada === 'ENTRADA')	$flds_Entrada = 1;
if($flds_Salida === 'SALIDA')	$flds_Salida = 1;
if($flds_Memo === 'MEMO')	$flds_Memo = 1;
if($flds_Reso === 'RESO')	$flds_Reso = 1;
if($flds_Proye === 'PROYE')	$flds_Proye = 1;

$whereBusqueda = "";
if($buscar_dcto && $detatipod !='')
{	$whereBusqueda = " where upper(sgd_tpr_descrip) like '%$detatipod%'";	}   

if($_POST['insertar_tdoc'] && $detatipod !='')
{	$isqlB = "select * from sgd_tpr_tpdcumento where rtrim(sgd_tpr_descrip) = '$detatipod' "; 
	# Selecciona el registro a actualizar
	$rs = $db->query($isqlB); # Executa la busqueda y obtiene el registro a actualizar.
	
	$rs->debug=true;
	
	$radiNumero = $rs->fields["SGD_TPR_CODIGO"];
	if ($radiNumero !='')
	{	$mensaje_err = "<HR><center><B><FONT COLOR=RED>El Tipo Documento < $radiNumero $detatipod > YA EXISTE. <BR>  VERIFIQUE LA INFORMACION E INTENTE DE NUEVO</FONT></B></center><HR>";
	} 
	else 
	{	$isql = "select max(sgd_tpr_codigo) as NUME from sgd_tpr_tpdcumento"; 
		$rs = $db->query($isql); # Executa la busqueda y obtiene el Codigo del documento.
	  	$radiNumero = $rs->fields["NUME"];
		$radiNumero =$radiNumero + 1;
		$query="insert into SGD_TPR_TPDCUMENTO(SGD_TPR_CODIGO, SGD_TPR_DESCRIP,SGD_TPR_TERMINO,SGD_TPR_TP2,SGD_TPR_TP1,SGD_TPR_TP3,SGD_TPR_TP5,SGD_TPR_TP9,SGD_TPR_ESTADO )
				VALUES ('$radiNumero','$detatipod','$terminot',$flds_Entrada,$flds_Salida,$flds_Memo,$flds_Reso,$flds_Proye,1)";
		$rsIN = $db->conn->query($query);
		if ($rsIN) $mensaje_err = "<HR><center><B><FONT COLOR=RED>Tipo Documental Creado<FONT></B></center><HR>";
		else $mensaje_err = "<HR><center><B><FONT COLOR=RED>Error al crear Tipo Documental</FONT></B></center><HR>";
		$terminot = '' ;
		$detatipod = '';
		?>
		<script language="javascript">
			document.adm_tipodoc.detatipod.value ='';
			document.adm_tipodoc.terminot.value ='';
		</script>
		<?
	}
}
	//Modificacion Datos Tipo Documental
if($_POST['modi_tdoc'] && ($detatipod != '') && ($codtdocI !=0) )
{	
    $isqlB = "select * from sgd_tpr_tpdcumento where upper(rtrim(sgd_tpr_descrip)) = '$detatipod' and sgd_tpr_codigo != $codtdocI"; 
	# Selecciona el registro a actualizar
	$rs = $db->query($isqlB); # Executa la busqueda y obtiene el registro a actualizar.
	$rs->debug=true;
	$radiNumero = $rs->fields["SGD_TPR_CODIGO"];
	if ($radiNumero != '')
	{	$mensaje_err = "<HR><center><B><FONT COLOR=RED>El Tipo Documento < $detatipod > YA EXISTE PARA EL CODIGO < $radiNumero > <BR>  VERIFIQUE LA INFORMACION E INTENTE DE NUEVO</FONT></B></center><HR>";
	} 
	else 
	{	$query = "update SGD_TPR_TPDCUMENTO set SGD_TPR_DESCRIP ='$detatipod'
		         ,SGD_TPR_TERMINO = '$terminot',SGD_TPR_TP2 ='$flds_Entrada'  
			  ,SGD_TPR_TP1 = '$flds_Salida',SGD_TPR_TP3 = '$flds_Memo'
			  ,SGD_TPR_TP5 = '$flds_Reso',SGD_TPR_TP9 = '$flds_Proye'
		      where  sgd_tpr_codigo = $codtdocI ";
		$rsIN = $db->conn->query($query);
		$terminot = '' ;
		$detatipod = '';
		$mensaje_err ="<HR><center><B><FONT COLOR=RED>SE MODIFICO EL TIPO DOCUMENTAL</FONT></B></center><HR>";
		?>
		<script language="javascript">
			document.adm_tipodoc.detatipod.value ='';
			document.adm_tipodoc.terminot.value ='';
		</script>
		<?
	}
}
//
echo $mensaje_err;
include_once "$ruta_raiz/Etrd/lista_tiposdocu.php";
?>
</form>
<p>
<?=$mensaje_err?>
</p>
</body>
</html>
