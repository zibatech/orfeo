<?
/*************************************************************************************/
/* ORFEO GPL:Sistema de Gestion Documental		http://www.orfeogpl.org	             */
/*	Idea Original de la SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS         */
/*				COLOMBIA TEL. (57) (1) 6913005  orfeogpl@gmail.com                   */
/* ===========================                                                       */
/*                                                                                   */
/* Este programa es software libre. usted puede redistribuirlo y/o modificarlo       */
/* bajo los terminos de la licencia GNU General Public publicada por                 */
/* la "Free Software Foundation"; Licencia version 2. 			                     */
/*                                                                                   */
/* Copyright (c) 2005 por :	  	  	                                                 */
/* SSPD "Superintendencia de Servicios Publicos Domiciliarios"                       */
/*   Jairo Hernan Losada  jlosada@gmail.com                Desarrollador             */
/*   Sixto Angel Pinzón López --- angel.pinzon@gmail.com   Desarrollador           */
/* C.R.A.  "COMISION DE REGULACION DE AGUAS Y SANEAMIENTO AMBIENTAL"                 */ 
/*   Liliana Gomez        lgomezv@gmail.com                Desarrolladora            */
/*   Lucia Ojeda          lojedaster@gmail.com             Desarrolladora            */
/* D.N.P. "Departamento Nacional de Planeación"                                     */
/*   Hollman Ladino       hladino@gmail.com                Desarrollador             */
/*                                                                                   */
/* Colocar desde esta lInea las Modificaciones Realizadas Luego de la Version 3.5    */
/*  Nombre Desarrollador   Correo     Fecha   Modificacion                           */
/*  Fabian Mauricio Losada Florez      4-Enero-2007 						     */
/*************************************************************************************/
 $krdOld = $krd;
$per=1;
session_start();
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
if(!$krd) $krd = $krdOld;
if (!$ruta_raiz) $ruta_raiz = "..";
include "$ruta_raiz/rec_session.php";
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once "$ruta_raiz/include/tx/Historico.php";
$db = new ConnectionHandler("$ruta_raiz");
$objHistorico= new Historico($db);
$encabezado = session_name()."=".session_id()."&krd=$krd&nomcarpeta=$nomcarpeta";
?>
<html height=50,width=150>
<head>
<title>Cambio Estado Expediente</title>
<link rel="stylesheet" href="../estilos/orfeo.css">
<body bgcolor="#FFFFFF">

<form name=cambiar action="lista_expediente.php?<?=$encabezado?>" method='get'>
<?
$dat=date("Y-m-d");
$sqle="update SGD_EXP_EXPEDIENTE set SGD_EXP_ARCHIVO='$est',SGD_EXP_FECHFIN='$dat' where SGD_EXP_NUMERO = '$expediente'";
$rs=$db->query($sqle);
if ($est==2)$cer=1;
else $cer=0;
$sqlue="update sgd_sexp_secexpedientes set sgd_cerrado=".$cer." where  SGD_EXP_NUMERO = '$expediente'";
$rs=$db->query($sqlue);
$arrayRad[0]=$numRad;
$isqlDepR = "SELECT USUA_CODI
			FROM usuario
			WHERE USUA_LOGIN = '$krd'";
	$rsDepR = $db->conn->Execute($isqlDepR);
	$codusua = $rsDepR->fields['USUA_CODI'];
if ($est==2){
	$observa = "Se Cerro el Expediente  ";
	$objHistorico->insertarHistoricoExp($expediente,$arrayRad,$dependencia, $codusua,$observa,58,1);
?>
<center>El Expediente fue Cerrado
<?
}
if ($est==1){
	$observa = "Se Reabrio el Expediente  ";
	$objHistorico->insertarHistoricoExp($expediente,$arrayRad,$dependencia, $codusua,$observa,59,1);
?>

<center>El Expediente fue Reabierto
<?
}
?>

<input type="button" value="Cerrar" class="botones_3" onClick="opener.regresar();window.close()">
</center>
</form>
</html>
