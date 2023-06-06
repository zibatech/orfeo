<?php
session_start();

    $ruta_raiz = "."; 
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");
/**
  * Se añadio compatibilidad con variables globales en Off
  * @autor Jairo Losada 2009-05
  * @licencia GNU/GPL V 3
  */
define('ADODB_ASSOC_CASE', 1);
$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$tip3Nombre=$_SESSION["tip3Nombre"];
$tip3desc = $_SESSION["tip3desc"];
$tip3img =$_SESSION["tip3img"];
$resent = (isset($_REQUEST["checkValue"]) && is_array($_REQUEST["checkValue"])) ? array_keys($_REQUEST["checkValue"]) : [];
if ($resent){
	//echo "<pre>";
	//var_dump($resent);
	foreach ($resent as $key){
		$exps.="doc$i=$key&";
		++$i;
	}
	$exps=substr($exps,"0","-1");
	//echo "</pre>";
	header('Location: http://200.69.106.149:8080/birt/frameset?__report=fuid.rptdesign&__format=html&__svg=true&__locale=es_CO&__timezone=America%2FBogota&__masterpage=true&__rtl=false&__cubememsize=10&__resourceFolder=%2Fhome%2Fwho%2Fworkspace%2Ffuid&-1892753268&'.$exps);
	die("ok");
}

if(isset($_GET["carpeta"]))
    $nomcarpeta = $_GET["carpeta"];
else
    $nomcarpeta = "";

if (isset($_GET["tipo_carpt"]))
    $tipo_carpt = $_GET["tipo_carpt"];
else
    $tipo_carpt = "";

if(isset($_GET["adodb_next_page"]))
    $adodb_next_page = $_GET["adodb_next_page"];
else
    $adodb_next_page = "";
if(isset($_GET["orderNo"])) $orderNo=$_GET["orderNo"];
if(isset($_GET["orderTipo"])) $orderTipo=$_GET["orderTipo"];
if(isset($_GET["busqExp"])) $busqExp=$_GET["busqExp"];
if(isset($_GET["busq_exp"])) $busq_exp=$_GET["busq_exp"];
if(isset($_GET["depeBuscada"])) $depeBuscada=$_GET["depeBuscada"];
if(isset($_GET["filtroSelec"])) $filtroSelec=$_GET["filtroSelec"];

if (isset($_GET["carpeta"]))
    $carpeta = $_GET["carpeta"];
else
    $carpeta = "";

$ruta_raiz = ".";
$ADODB_COUNTRECS = false;
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
require_once("$ruta_raiz/include/combos.php");
include_once "$ruta_raiz/js/funtionImage.php";

$ADODB_COUNTRECS = false;
error_reporting(7);

$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->conn->debug=true;

// Procedimiento para filtro de expesdientes....
if($busq_exp)
{
	$busq_exp= trim($busq_exp);
	$textElements = explode(",", $busq_exp);
	$newText = "";
	$dep_sel = $dependencia;
	foreach ($textElements as $item)
	{	$item = trim ( $item );
		if ( strlen ( $item ) != 0)
		{	if(strlen($item)<=6)
			{	$sec = str_pad($item,6,"0",STR_PAD_left);
				//$item = date("Y") . $dep_sel . $sec;
			}
			//else
			//{
				//$busq_exp.= " (cast(a.radi_nume_radi as varchar(14)) like '%$item%') or";
			//}
		}
	}
	if(substr($busq_exp,-2)=="or")   $busq_exp= substr($busq_exp,0,strlen($busq_exp)-2);
	if(trim($busq_exp))  $where_filtro .= "and ( $busq_exp) ";
}
?>
<html><head><title>.: Modulo total :.</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="estilos/bootstrap.min.css">
<style type="text/css">
<!--
.textoOpcion {  font-family: Arial, Helvetica, sans-serif; font-size: 8pt; color: #000000; text-decoration: underline}
-->
</style>
<!-- seleccionar todos los checkboxes-->
<script>
function window_onload()
{
   document.getElementById('depsel8').style.display = 'none';
}

function markAll()
{
	if(document.form1.elements['checkAll'].checked)
		for(i=10;i<document.form1.elements.length;i++)
			document.form1.elements[i].checked=1;
	else
		for(i=10;i<document.form1.elements.length;i++)
			document.form1.elements[i].checked=0;
}

function changedepesel()
{
 	if(document.getElementById('enviara').value==7){
		document.getElementById('depsel8').style.display = 'none';
		document.form1.cambioInf.value = 'B';
 	} else {
		document.getElementById('depsel8').style.display = '';
		document.form1.cambioInf.value = 'I';
	}
}

function enviar()
{
document.form1.codTx.value = document.getElementById('enviara').value;
sw = 0;
cnt_notinf = 0;
cnt_inf = 0;
for(i=3;i<document.form1.elements.length;i++)
	if (document.form1.elements[i].checked)
	{	sw=1;
		if (document.form1.elements[i].name[11] == '0')	cnt_notinf += 1;
		else	cnt_inf += 1;
	}
if (sw==0)
{	alert ("Debe seleccionar uno o mas informados");
	return;
}
if (cnt_inf > 0 && cnt_notinf > 0 && document.getElementById('enviara').value == 7)
{	alert ("Los informados seleccionados ... o todos tienen informador o no tienen informador.");
	return;
}
document.form1.submit();
}
</script>
<?php
$tbbordes = "#CEDFC6";
$tbfondo = "#FFFFCC";
$imagen="flechadesc.gif";
?>
<SCRIPT>
<?
	include "libjs.php";
?>
</SCRIPT>
<script>
function regresar(){
	window.location.reload();
	window.close();
}
function send(tx){
	if (tx=='open'){
		document.getElementById("est").value = "0";
		win = window.open('','myWin',"height=200,width=400,scrollbars=yes");
		document.form1.target='myWin';
		document.form1.action="archivo/cambiar.php"
		document.form1.submit();

	}
	if (tx=='close'){
		document.getElementById("est").value = "1";
		win = window.open('','myWin',"height=200,width=400,scrollbars=yes");
		document.form1.target='myWin';
		document.form1.action="archivo/cambiar.php"
		document.form1.submit();
	}
	if (tx=='indiceXml'){
		document.getElementById("xml").value=true;
		document.getElementById("form1").action="archivo/indiceExp.php"
		document.form1.submit();
	}
	if (tx=='indice'){
		document.getElementById("xml").value=false;
		document.getElementById("form1").action="archivo/indiceExp.php"
		document.form1.submit();
	}
	if (tx=='arch'){
		win = window.open('','myWin',"height=200,width=400,scrollbars=yes");
		win = window.open('','myWin',"height=200,width=400,scrollbars=yes");
		document.form1.target='myWin';
		document.getElementById("form1").action="archivo/trans.php"
		document.form1.submit();
	}
	if (tx=='report'){
		document.getElementById("form1").action=""
		document.form1.submit();
	}
}
</script>
</head>
<body color="#F5F5F5" topmargin="0" onLoad="setupDescriptions();window_onload();">
<p>
<?php
$krd=strtoupper($krd);
$check=1;
$numeroa=0;$numero=0;$numeros=0;$numerot=0;$numerop=0;$numeroh=0;
$fechaf=date("dmy") . time("hms");
$fechah=date("dmy") . time("hms");
$encabezado="".session_name()."=".session_id()."&depeBuscada=$depeBuscada&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion&carpeta=$carpeta";

?>
<table border=0 width=100% class="t_bordeGris">
<tr>
	<td>
		<!-- Inicia tabla de cabezote -->
		<table BORDER=0  cellpad=2 cellspacing='0' WIDTH=98% class='t_bordeGris' valign='top' align='center' >
		<TR>
			<td width='33%' >
      			<table width='100%' border='0' cellspacing='1' cellpadding='0'>
      			<tr>
      				<td height="20" bgcolor="377584"><div align="left" class="titulo1">LISTADO DE: </div></td>
      			</tr>
      			<tr class="info">
      				<td height="20">Expedientes</td>
      			</tr>
      			</table>
      		</td>
			<td width='33%' >
      			<table width='100%' border='0' cellspacing='1' cellpadding='0'>
      			<tr>
      				<td height="20" bgcolor="377584"><div align="left" class="titulo1">USUARIO </div></td>
      			</tr>
      			<tr class="info">
      				<td height="20"><?=$_SESSION['usua_nomb'] ?></td>
      			</tr>
      			</table>
      		</td>
      		<td width='34%' >
      			<table width='100%' border='0' cellspacing='1' cellpadding='0'>
      			<tr>
      				<td height="20" bgcolor="377584"><div align="left" class="titulo1">DEPENDENCIA </div></td>
      			</tr>
      			<tr class="info">
      				<td height="20"><?=$_SESSION['depe_nomb'] ?></td>
      			</tr>
      			</table>
      		</td>
      	</TR>
		</table>
		<TABLE width="98%" align="center" cellspacing="0" cellpadding="0">
		<tr class="tablas">
			<TD>
			<FORM style="background-color: #DDDDDD;" name=form_busq_rad action='cuerpoExp.php?krd=<?=$krd?>&<?=session_name()."=".trim(session_id()).$encabezado?>' method=GET>
				Buscar radicado(s) informado(s) (Separados por coma)<input name="busq_exp" type="text" size="70" class="tex_area" value="<?=$busq_exp?>">
				<input type=submit value='Buscar ' name=Buscar valign='middle' class='botones' onChange="form_busq_rad.submit()";>
	            <input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'> 
			</FORM>
			</td>
		</tr>
 		</table>
		<form name='form1' action='' method='GET' id=form1>
		<div align='right'><input type='submit' value='Reporte Transferencia'  onclick=send('report')></input></div>
		<div align='right'><input type='submit' value='Cerrar' onclick=send('close')></input></div>
<?php
if ($_SESSION["usuaPermExpediente"]==2){
?>
		<div align='right'><input type='button' value='Re-abrir' onclick=send('open')></input></div>
<?php
}
?>
		<div align='right'><input type='button' value='Índice electrónico' onclick=send('indice')></input></div>
		<div align='right'><input type='button' value='Índice electrónico (Xml)' onclick=send('indiceXml')></input></div>
		<div align='right'><input type='button' value='Transferencia definitiva' onclick=send('arch')></input></div>
	    <input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'> 
		<input name="cambioInf" value="I" type="hidden">
		<input name="xml" id=xml value="false" type="hidden">
		<br>
		<!-- Finaliza tabla de cabezote --> <!-- Inicia tabla de datos -->
		<?
		$imagen="img_flecha_sort.gif";
		$row = array();
		echo "<input type=hidden name=contra value=$drde> ";
		echo "<input type=hidden name=est id=est value=2> ";
		echo "<input type=hidden name=sesion value=".md5($krd)."> ";
		echo "<input type=hidden name=krd value=$krd>";
		echo "<input type=hidden name=drde value=$drde>";
		echo "<input type=hidden name=carpeta value=$carpeta>";
		?>
		<table width="98%" align="center" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td colspan="2" height="80">
				<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" class="borde_tab">
				<tr>
					<td width="50%"  class="titulos2">
						<table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
						<tr class="titulos2">
							<td width="20%" class="titulos2">LISTAR POR:</td>
							<td height="30">
								<a href='cuerpoExp.php?<? echo "$encabezado&orderNo=9&orderTipo=desc"; ?>' alt='Ordenar Por Leidos'><span class='leidos'>Le&iacute;dos</span></a>&nbsp;
								<?=$img7 ?>
								<a href='cuerpoExp.php?<? echo "$encabezado&orderNo=9&orderTipo=asc"; ?>' alt='Ordenar Por Leidos'><span class='no_leidos'>No Le&iacute;dos</span></a>
							</td>
						</tr>
						</table>
					</td>
					<td width="50%" align=right class="titulos2" ><BR>
<?
$ADODB_COUNTRECS = true;
$isql="select depe_codi,depe_nomb from DEPENDENCIA ORDER BY DEPE_NOMB";
$rs = $db->conn->query($isql);
$ADODB_COUNTRECS = false;
$numerot=$rs->RecordCount();
?>
<!--					<span class='etitulos'><b>
						<select name='enviara' id='enviara' class='select'  language='javascript' onchange=changedepesel()>
						<option value=7>Borrar Documento informado</option>
						<option value=8>Informar (Enviar copia de documentos)</option>
						</select>
<br>
						<select name='depsel8[]' id='depsel8' class='select' multiple size="5" height=30>
<?
  while(!$rs->EOF)
  {
	$dependenciaCodi = $rs->fields["DEPE_CODI"];
	$dependenciaNombre = $rs->fields["DEPE_NOMB"];
    echo "<OPTION VALUE=$dependenciaCodi >$dependenciaNombre</OPTION>";
	$rs->MoveNext();
  }
?>
</select>
-->
<?
$filtroExp="and e.sgd_exp_numero like '%$busq_exp%' ";

include "$ruta_raiz/include/query/queryCuerpoExp.php";
$a = new combo($db);
$concatSQL=$db->conn->Concat($concatenar,"' '","depe_nomb");
$s = "SELECT DEPE_CODI,$concatSQL as NOMBRE  from dependencia order by depe_nomb asc ";
$r = "DEPE_CODI";
$t = "NOMBRE";
$v = $dependencia;
$sim = 0;
//$a->conectar($s,$r,$t,$v,$sim,$sim);
?>
<!--						<BR>
						<input type=button value="REALIZAR" name=Enviar valign="middle" class="botones" onClick="enviar();">
						<input type=hidden name=codTx>
					</td>
				</tr>
-->
				<tr>
					<td  colspan="2">
<?
$iusuario = " and us_usuario='$krd'";
//Modificado idrd marzo 3
//$sqlFecha = $db->conn->SQLDate("d-m-Y H:i A","a.RADI_FECH_RADI");
$sqlFecha = $db->conn->SQLDate("Y-m-d H:i A","a.RADI_FECH_RADI");
$systemDate = $db->conn->sysTimeStamp;

$sqlOffset = $db->conn->OffsetDate("b.sgd_tpr_termino","radi_fech_radi");
$concatSQL=$db->conn->Concat("a.RADI_NOMB","' '","a.RADI_PRIM_APEL","' '","a.RADI_SEGU_APEL");
if(strlen($orderNo)==0)
{
	$orderNo='0';
	$order = 1;
}
else
	$order = $orderNo +1;

if($orden_cambio==1)
{	if(trim( strtoupper($orderTipo))!="DESC")
		$orderTipo="DESC";
	else
	   $orderTipo="ASC";
}
?>
<frame name="frame">
</frame>
<?

include "$ruta_raiz/include/query/queryCuerpoExp.php";
$linkPagina = "$PHP_SELF?$encabezado&orderTipo=$orderTipo&orderNo=$orderNo";
$encabezado .= "&adodb_next_page=1&orderTipo=$orderTipo&orderNo=";

if($chk_carpeta) $chk_value=" checked "; else  $chk_value=" ";
//echo "<pre>$isql</pre>";
$pager = new ADODB_Pager($db,$isql,'adodb', false,$orderNo,$orderTipo);
$pager->toRefLinks = $linkPagina;
$pager->toRefVars = $encabezado;
$pager->checkAll = false;
$pager->checkTitulo = true;
if($_GET["adodb_next_page"]) $pager->curr_page = $_GET["adodb_next_page"];
$pager->Render($rows_per_page=20,$linkPagina,$checkbox='checkValue');

?>
					</td>
				</tr>
		</table>
		</form>
	</td>
</tr>
</table>
</body>
</html>
