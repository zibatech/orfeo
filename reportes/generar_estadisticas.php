<?php
session_start();

	$ruta_raiz = "..";
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
/**
  * Paggina Cuerpo.php que muestra el contenido de las Carpetas
  * Creado en la SSPD en el año 2003
  * 
  * Se anadio compatibilidad con variables globales en Off
  * @autor Jairo Losada 2009-06
  * @licencia GNU/GPL V 3
  */
$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$depe_codi_territorial = $_SESSION["depe_codi_territorial"];


include_once  "../include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);

if(!$fecha_busq) $fecha_busq=date("Y-m-d");
if(!$fecha_busq2) $fecha_busq2=date("Y-m-d");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../estilos/orfeo.css">
</head>
<body>
<div id="spiffycalendar" class="text"></div>
<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
<script> function abrirArchivo(url){nombreventana='Documento'; window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');return; }</script>
<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript"><!--
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "formboton", "fecha_busq","btnDate1","<?=$fecha_busq?>",scBTNMODE_CUSTOMBLUE);
	var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "formboton", "fecha_busq2","btnDate1","<?=$fecha_busq2?>",scBTNMODE_CUSTOMBLUE);
//--></script><P>
<TABLE width="100%" class='borde_tab' cellspacing="5">
  <TR>
    <TD height="30" valign="middle"   class='titulos5' align="center">GENERACION LISTADOS DE DOCUMENTOS DEVUELTOS POR AGENCIA DE CORREO</td></tR>
	</table>
	<form name=formboton  method=post  action='generar_estadisticas.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah&fecha_busq=$fecha_busq&fecha_busq2=$fecha_busq2"?>'>
	<input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'> 
<TABLE width="550" class="borde_tab">
  <!--DWLayoutTable-->
  <TR>
    <TD width="125" height="21"  class='titulos5'> Fecha desde<br>
	<?
	  echo "($fecha_busq)";
	?>
	</TD>
    <TD width="415" align="right" valign="top" class='listado5'>

        <script language="javascript">
		        dateAvailable.date = "2003-08-05";
			    dateAvailable.writeControl();
			    dateAvailable.dateFormat="yyyy-MM-dd";
    	  </script>
</TD>
  </TR>
  <TR>
    <TD width="125" height="21"  class='titulos5'> Fecha Hasta<br>
	<?
	  echo "($fecha_busq2)";
	?>
	</TD>
    <TD width="415" align="right" valign="top" class='listado5'>
        <script language="javascript">
		        dateAvailable2.date = "2003-08-05";
			    dateAvailable2.writeControl();
			    dateAvailable2.dateFormat="yyyy-MM-dd";
    	  </script>
</TD>
  </TR>
  <tr>
	<?php /*
    <TD height="26" class='titulos5'>Tipo de Salida</TD>
    <TD valign="top" align="left" class='listado5'>
	
    <?php
	$ss_RADI_DEPE_ACTUDisplayValue = "--- TODOS LOS TIPOS ---";
	$valor = 0;
	include "../include/query/reportes/querytipo_envio.php";
	$sqlTS = "select $sqlConcat ,SGD_FENV_CODIGO from SGD_FENV_FRMENVIO 
					order by SGD_FENV_CODIGO";
	
	$rsTs = $db->conn->Execute($sqlTS);
	print $rsTs->GetMenu2("tipo_envio","$tipo_envio",$blank1stItem = "$valor:$ss_RADI_DEPE_ACTUDisplayValue", false, 0," onChange='submit();' class='select'");	
	?>

  </tr>
  <TR>
    <TD height="26" class='titulos5'>Dependencia</TD>
    <TD valign="top" class='listado5'>
	<?
	$ss_RADI_DEPE_ACTUDisplayValue = "--- TODAS LAS DEPENDENCIAS ---";
	$valor = 0;
	include "$ruta_raiz/include/query/devolucion/querydependencia.php";
	$sqlD = "select $sqlConcat ,depe_codi from dependencia 
	        where depe_codi_territorial = $depe_codi_territorial
		order by depe_codi";
		$rsDep = $db->conn->Execute($sqlD);
	       print $rsDep->GetMenu2("dep_sel","$dep_sel",$blank1stItem = "$valor:$ss_RADI_DEPE_ACTUDisplayValue", false, 0," onChange='submit();' class='select'");	
	?>
	
  </TR>*/?>
    <tr>
    <td height="26" colspan="2" valign="top" class='titulos5'> <center>
		<INPUT TYPE=SUBMIT name=generar_informe Value=' Generar Informe ' class='botones_mediano'></center>
		</td>
	</tr>
  </TABLE>
<?php
if(!$fecha_busq) $fecha_busq = date("Y-m-d");
if($generar_informe)
{
if ($tipo_envio == 0)
{
 $where_tipo = "";
}else
{
 $where_tipo = " and a.SGD_FENV_CODIGO = $tipo_envio ";
}
if ($dep_sel == 0)
{
/*
*Seleccionar todas las dependencias de una territorial
*/
    include "$ruta_raiz/include/query/devolucion/querydependencia.php";
	$sqlD = "select $sqlConcat ,depe_codi from dependencia 
	        where depe_codi_territorial = $depe_codi_territorial
			order by depe_codi";
	$rsDep = $db->conn->Execute($sqlD);
	while(!$rsDep->EOF)
		 {
			$depcod = $rsDep->fields["DEPE_CODI"];
		    $lista_depcod .= " $depcod,";
		    $rsDep->MoveNext();
		   }   
	$lista_depcod .= "0";
}else
{
 $lista_depcod = $dep_sel;
}
//Se limita la consulta al substring del numero de radicado de salida 27092005
include "../include/query/reportes/querydepe_selecc.php";
$generar_informe = 'generar_informe';
	error_reporting(7);
	$fecha_ini = $fecha_busq;
	$fecha_fin = $fecha_busq2;
	$fecha_ini = mktime(00,00,00,substr($fecha_ini,5,2),substr($fecha_ini,8,2),substr($fecha_ini,0,4));
	$fecha_fin = mktime(23,59,59,substr($fecha_fin,5,2),substr($fecha_fin,8,2),substr($fecha_fin,0,4));
	$guion     = "'-'";
	include "../include/query/reportes/querygenerar_estadisticas.php";
	
	/*if($tipo_envio=="101" or $tipo_envio=="108" or $tipo_envio=="109" or $tipo_envio=="111")
	{
	 $where_isql .= " and a.sgd_renv_planilla is not null and a.sgd_renv_planilla != '00'
		";
	}
	if($tipo_envio==0)
	{
	 $where_isql .= " and ((a.sgd_fenv_codigo != '101' and a.sgd_fenv_codigo != '108' and a.sgd_fenv_codigo != '109') 
	 				  or (a.sgd_renv_planilla is not null and a.sgd_renv_planilla != '00'))
		";
	}
	/* SE ELIMINA POR SOLICITUD DEL USUARIO
	$order_isql = '  ORDER BY  '.$db->conn->substr.'(a.radi_nume_sal, 5, 3), a.SGD_RENV_FECH DESC,a.SGD_RENV_PLANILLA DESC';
	
	
	$order_isql = " ORDER BY a.SGD_DEVE_FECH ASC";*/
	$query_t = $query.$where_isql/*.. $where_depe*/;
	$ruta_raiz = "..";
	error_reporting(7);
	define('ADODB_FETCH_NUM',1);
	$ADODB_FETCH_MODE = ADODB_FETCH_NUM;
	require "../anulacion/class_control_anu.php"; 
	$db->conn->SetFetchMode(ADODB_FETCH_NUM);
	$btt = new CONTROL_ORFEO($db);
	$campos_align = array("L","C","L","L","L","L","L","L","L","L","L","L","L","L","L");
	$campos_tabla = array("tipo_envio","usuario","dependencia","radicado","observaciones","fecha");
	$campos_vista = array ("Forma Envio","Usuario", "Dependencia","Radicado","Observaciones", "Fecha");
    $campos_width = array (80,110,110,200,80);	
	$btt->campos_align = $campos_align;
	$btt->campos_tabla = $campos_tabla;
	$btt->campos_vista = $campos_vista;
	$btt->campos_width = $campos_width;
	?>
	
	</center>
	<table><tr><td></td></tr></table>
	<table><tr><td></td></tr></table>	
	<table class="borde_tab" width="100%"><tr class=titulos5><td colspan="2">
	Listado de documentos Devueltos
	</td></tr>
	<tr><td width="15%" class="titulos5">Fecha Inicial </td><td width="85%" class='listado5'><?=$fecha_busq .  "  00:00:00" ?> </td></tr>
	<tr><td  class="titulos5">Fecha Final   </td><td class='listado5'><?=$fecha_busq2 . "  23:59:59" ?> </td></tr>
	<tr><td  class="titulos5">Fecha Generado </td><td class='listado5'><? echo date("Ymd - H:i:s"); ?></td></tr>
	</table>
	<table><tr><td></td></tr></table>
	<table><tr><td></td></tr></table>	
	<?php
	$btt->tabla_sql($query_t);
	error_reporting(7);
	
	$html= $btt->tabla_html;
	error_reporting(7);
	define(FPDF_FONTPATH,'../fpdf/font/');
	require("../fpdf/html_table.php");
	error_reporting(7);
	$pdf = new PDF("L","mm","A4");
	$pdf->AddPage();
	$pdf->SetFont('Arial','',7);
	$entidad = $db->entidad;
	$encabezado = "<table border=1>
			<tr>
			<td width=1120 height=30>$entidad</td>
			</tr>
			<tr>
			<td width=1120 height=30>REPORTE DE DEVOLUCION DE DOCUMENTOS ENTRE $fecha_busq   00:00:00  y $fecha_busq2   23:59:59 </td>
			</tr>
			</table>";
	$fin = "<table border=1 bgcolor='#FFFFFF'>
			<tr>
			<td width=1120 height=60 bgcolor='#CCCCCC'>FUNCIONARIO CORRESPONDENCIA</td>
			</tr>
			<tr>
			<td width=1120 height=60></td>
			</tr>
		</table>";
	
    $pdf->WriteHTML($encabezado . $html . $fin);
	$arpdf_tmp = "../bodega/pdfs/planillas/dev/$dependencia_$krd_". date("Ymd_hms") . "_dev.pdf";
	$pdf->Output($arpdf_tmp);
/*
	 * Modificacion acceso a documentos
	 * @author Liliana Gomez Velasquez
	 * @since 11 noviembre 2009
	 */
	echo "<B><a class=\"vinculos\" href=\"#\" onclick=\"abrirArchivo('". $arpdf_tmp."?time=".time() ."');\"> Abrir Archivo Pdf</a><br>";	
}

?>
</body>
</html>
