<?
/*************************************************************************************/
/* ORFEO GPL:Sistema de Gestion Documental		http://www.orfeogpl.org	     */
/*	Idea Original de la SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS     */
/*				COLOMBIA TEL. (57) (1) 6913005  orfeogpl@gmail.com   */
/* ===========================                                                       */
/*                                                                                   */
/* Este programa es software libre. usted puede redistribuirlo y/o modificarlo       */
/* bajo los terminos de la licencia GNU General Public publicada por                 */
/* la "Free Software Foundation"; Licencia version 2. 			             */
/*                                                                                   */
/* Copyright (c) 2005 por :	  	  	                                     */
/* SSPS "Superintendencia de Servicios Publicos Domiciliarios"                       */
/*   Jairo Hernan Losada  jlosada@gmail.com                Desarrollador             */
/*   Sixto Angel Pinzón López --- angel.pinzon@gmail.com   Desarrollador             */
/* C.R.A.  "COMISION DE REGULACION DE AGUAS Y SANEAMIENTO AMBIENTAL"                 */ 
/*   Liliana Gomez        lgomezv@gmail.com                Desarrolladora            */
/*   Lucia Ojeda          lojedaster@gmail.com             Desarrolladora            */
/* D.N.P. "Departamento Nacional de Planeación"                                      */
/*   Hollman Ladino       hladino@gmail.com                Desarrollador             */
/*                                                                                   */
/* Colocar desde esta lInea las Modificaciones Realizadas Luego de la Version 3.5    */
/*  Nombre Desarrollador   Correo     Fecha   Modificacion                           */
/*************************************************************************************/
?>
<? 
if(!$db)
{
$krdOld = $krd;
$carpetaOld = $carpeta;
$tipoCarpOld = $tipo_carp;
if(!$tipoCarpOld) $tipoCarpOld= $tipo_carpt;
session_start();
if(!$krd) $krd=$krdOsld;
$ruta_raiz = "..";
include "$ruta_raiz/rec_session.php";
include "$ruta_raiz/envios/paEncabeza.php";
?>
<table><tr><TD></TD></tr></table>
<?
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
require_once("$ruta_raiz/class_control/Mensaje.php");
include("$ruta_raiz/class_control/usuario.php");
$db = new ConnectionHandler($ruta_raiz);	 

$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$objUsuario = new Usuario($db);
if (isset($dependencia_busq)) {
	$dependencia_busq = $HTTP_GET_VARS["dependencia_busq"];
} 
if ($dependencia_busq == 99999){
$whereDependencia = " AND DEPE_CODI NOT IN(900,905,910,999) ";
}
else{
	$whereDependencia = " AND b.DEPE_CODI=$dependencia_busq AND DEPE_CODI NOT IN(900,905,910,999) ";
	}
$datosaenviar = "fechaf=$fechaf&genDetalle=$genDetalle&tipoEstadistica=$tipoEstadistica&codus=$codus&krd=$krd&dependencia_busq=$dependencia_busq&ruta_raiz=$ruta_raiz&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&tipoRadicado=$tipoRadicado&tipoDocumento=$tipoDocumento&codUs=$codUs&fecSel=$fecSel"; 
}
$whereTipoRadicado = "";
	if($tipoRadicado)
	{
		$whereTipoRadicado=" AND r.RADI_NUME_RADI LIKE '%$tipoRadicado'";
	}
if($tipoRadicado and ($tipoEstadistica==1 or $tipoEstadistica==6))
	{
		$whereTipoRadicado=" AND r.RADI_NUME_RADI LIKE '%$tipoRadicado'";
	}	
	
if($codus)
	{
		$whereTipoRadicado.=" AND b.USUA_CODI = $codus ";
	}elseif(!$codus and $usua_perm_estadistica<1)
	{
	    $whereTipoRadicado.=" AND b.USUA_CODI = $codusuario ";
	}
if($tipoDocumento and ($tipoDocumento!='9999' and $tipoDocumento!='9998' and $tipoDocumento!='9997'))
	{
		$whereTipoRadicado.=" AND t.SGD_TPR_CODIGO = $tipoDocumento ";
	}elseif ($tipoDocumento=="9997")	
	{
		$whereTipoRadicado.=" AND t.SGD_TPR_CODIGO = 0 ";
	}
	include "$ruta_raiz/include/query/archivo/queryReportePorRadicados.php";
	$generar = "ok";
	
	if($generar == "ok") {
		if($genDetalle==1) $queryUs = $queryEDetalle;
		if($genTodosDetalle==1) $queryUs  = $queryETodosDetalle;
		 $rsE = $db->conn->Execute($queryUs );
		include ("tablaHtml.php");
	}
   ?>

