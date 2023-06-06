<?php
session_start();
if (!isset($_SESSION['USUA_PERM_STICKER']))
	die('Usted no tiene permisos para ingresar a este módulo');

$ruta_raiz 		= "../..";
include_once "$ruta_raiz/processConfig.php";
$verradicado        = $_GET["verrad"];
define('ADODB_ASSOC_CASE', 1);
foreach ($_GET as $key=>$valor) ${$key} = $valor;

$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
$tip3Nombre     = $_SESSION["tip3Nombre"];
$tip3desc       = $_SESSION["tip3desc"];
$tip3img        = $_SESSION["tip3img"];

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
if ($verradicado) $verrad = $verradicado;

$numrad = $verrad;
$db     = new ConnectionHandler($ruta_raiz);

$copias = empty($copias)? 0: $copias;


if('NO DEFINIDO' != $tpdoc_nombreTRD ){
    $process = "Proceso ". $tpdoc_nombreTRD;
}
$numExp=$_GET['numExp'];
$entidad_corto=$entidad;
$noExpBarras="*$numExp*";
$dirPlantilla=$ruta_raiz.'/conf/stickers/expediente/'.$entidad_corto.'.php';
$dirLogo=$ruta_raiz.'/img/'.$entidad_corto.'.jpg';
	include_once ("$ruta_raiz/include/tx/Expediente.php");
	$trdExp 	= new Expediente($db);
	$mrdCodigo 	= $trdExp->consultaTipoExpediente($numExp);
	$serie=utf8_decode($trdExp->descSerie);
	$subSerie=utf8_decode($trdExp->descSubSerie);
	$isql="select sum(radi_nume_folio) as \"numFoliosExp\" from radicado where radi_nume_radi in (select radi_nume_radi from sgd_exp_expediente where sgd_exp_numero='$numExp')";
	$rs=$db->conn->Execute($isql);
	$isql="select SGD_SEXP_FECH as fecha from SGD_SEXP_SECEXPEDIENTES where SGD_EXP_NUMERO='$numExp'";
	$numFoliosExp=$rs->fields["numFoliosExp"];
	$rs=$db->conn->Execute($isql);
	$fecha=$rs->fields["FECHA"];
include ($dirPlantilla);

?>

