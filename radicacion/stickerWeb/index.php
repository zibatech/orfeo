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

include $ruta_raiz.'/ver_datosrad.php';
$copias = empty($copias)? 0: $copias;


if('NO DEFINIDO' != $tpdoc_nombreTRD ){
    $process = "Proceso ". $tpdoc_nombreTRD;
}
$noRad = $_REQUEST['nurad'];
$isql="
select
	d.depe_nomb,
	r.radi_cuentai
from
	radicado r,
	dependencia d
where
	r.radi_nume_radi=$noRad
	and r.radi_depe_actu=d.depe_codi";

$rs            = $db->conn->Execute($isql);
$depeNomb      = $rs->fields["DEPE_NOMB"];
$radiCuentaI   = $rs->fields["RADI_CUENTAI"];
$entidad_corto = $entidad;
$noRadBarras   = '<span style = "font-size: 43; font-family: \'Free 3 of 9\'"  align = rigth>'."*$noRad*".'</span>';
$dirPlantilla  = $ruta_raiz.'/conf/stickers/radicado/'.$entidad_corto.'.php';
$dirPlantilla  = file_exists($dirPlantilla)?$dirPlantilla:$ruta_raiz.'/conf/stickers/radicado/default.php';
$dirLogo       = $ruta_raiz.'/bodega/'.$logoEntidad;
$dirLogo       = file_exists($dirLogo)?$dirLogo:$ruta_raiz.'/img/default.jpg';

$isql="
select
	r.RADI_NUME_FOLIO,
	TO_CHAR(r.RADI_FECH_RADI,'DD-MON-YYYY HH:MI AM') FECHA,
	r.RADI_CUENTAI,
	r.RADI_DESC_ANEX,
	r.RA_ASUN,
	r.RADI_NUME_ANEXO,
	d.SGD_DIR_NOMREMDES,
	r.radi_depe_radi,
	r.radi_usua_radi,
    r.depe_codi,
    p.depe_nomb,
    r.sgd_rad_codigoverificacion as CODIGO
from
	radicado r,
    sgd_dir_drecciones d,
    dependencia p
where
	r.radi_nume_radi=d.radi_nume_radi and
    r.radi_depe_radi=p.depe_codi and
	r.radi_nume_radi=$noRad";

$rs=$db->conn->Execute($isql);
$anexos=$rs->fields["RADI_NUME_ANEXO"];
$depeNombActu=$rs->fields["DEPE_NOMB"];
$folios = $rs->fields["RADI_NUME_FOLIO"];
$anexDesc = $rs->fields["RADI_DESC_ANEX"];
$asunto = $rs->fields["RA_ASUN"];
$asunto= substr($asunto,0,35);
$referencia= $rs->fields["RADI_CUENTAI"];
$remitente= $rs->fields["SGD_DIR_NOMREMDES"];
$remitente= substr($remitente,0,35);
$radi_fech_radi=$rs->fields["FECHA"];
$depe_destino=$rs->fields["DEPE_CODI"];
$depe_=$rs->fields["RADI_DEPE_RADI"];
$usua_=$rs->fields["RADI_USUA_RADI"];
$codigo_=$rs->fields["CODIGO"];

$isql = "select u.usua_login
from usuario u
where
u.usua_codi = $usua_ and u.depe_codi = $depe_";

$rs=$db->conn->Execute($isql);
$usua_login =$rs->fields["USUA_LOGIN"];

$res_informados = $db->conn->getAll("SELECT DISTINCT depe_nomb FROM informados i JOIN dependencia d ON i.depe_codi = d.depe_codi WHERE i.radi_nume_radi = ".$noRad." LIMIT 2");
$informados = implode(',', array_map(function($depe) { return $depe['DEPE_NOMB']; }, $res_informados));
include ($dirPlantilla);
