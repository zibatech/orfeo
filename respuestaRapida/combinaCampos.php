<?
$verrad = $radicado;

if(!$verradicado) $verradicado = $verrad;
if(!$verradicado) $verradicado = $numrad;
include "../ver_datosrad.php";

$dependencia_nombre =  $_SESSION["depe_nomb"];
$usuario_nombre =  $_SESSION["usua_nomb"];


$plan_plantillaOld = $plan_plantilla ;

$plan_plantilla = str_replace("RAD_E_PADRE",$radi_fech_radi, $plan_plantilla);
$plan_plantilla = str_replace("CTA_INT",$cuentai, $plan_plantilla);

$plan_plantilla = str_replace("TER",$terr_sigla, $plan_plantilla);
$plan_plantilla = str_replace("F_RAD_E",substr($radi_fech_radi, 0,11), $plan_plantilla);
$plan_plantilla = str_replace("SAN_FECHA_RADICADO",$fecha_e, $plan_plantilla);
$plan_plantilla = str_replace("NOMBRESDESTINATARIOS",$destinatarios, $plan_plantilla);
$plan_plantilla = str_replace("CONCOPIA",$copias, $plan_plantilla);
$plan_plantilla = str_replace("RA_ASUN",$ra_asun, $plan_plantilla);
$plan_plantilla = str_replace("RA_ASU_ACTUAL",$_SESSION["usua_nomb"], $plan_plantilla);
$plan_plantilla = str_replace("NOM_R",$nombret_us1_u, $plan_plantilla);
$plan_plantilla = str_replace("DIR_R",$direccion_us1, $plan_plantilla);
$plan_plantilla = str_replace("DIR_E",$direccion_us3, $plan_plantilla);
$plan_plantilla = str_replace("DEPTO_R",$dpto_nombre_us1, $plan_plantilla);
$plan_plantilla = str_replace("MPIO_R",$muni_nombre_us1, $plan_plantilla);
$plan_plantilla = str_replace("TEL_R",$telefono_us1, $plan_plantilla);
$plan_plantilla = str_replace("MAIL_R",$mail_us1, $plan_plantilla);
$plan_plantilla = str_replace("DOC_R",$cc_documentous1, $plan_plantilla);
$plan_plantilla = str_replace("NOM_P",$nombret_us2_u, $plan_plantilla);
$plan_plantilla = str_replace("DIR_P",$direccion_us2, $plan_plantilla);
$plan_plantilla = str_replace("DEPTO_P",$dpto_nombre_us2, $plan_plantilla);
$plan_plantilla = str_replace("MPIO_P",$muni_nombre_us2, $plan_plantilla);
$plan_plantilla = str_replace("TEL_P",$telefono_us1, $plan_plantilla);
$plan_plantilla = str_replace("MAIL_P",$mail_us2, $plan_plantilla);
$plan_plantilla = str_replace("DOC_P",$cc_documento_us2, $plan_plantilla);
$plan_plantilla = str_replace("NOM_E",$nombret_us3_u, $plan_plantilla);
$plan_plantilla = str_replace("DIR_E",$direccion_us3, $plan_plantilla);
$plan_plantilla = str_replace("MPIO_E",$muni_nombre_us3, $plan_plantilla);
$plan_plantilla = str_replace("DEPTO_E",$dpto_nombre_us3, $plan_plantilla);
$plan_plantilla = str_replace("TEL_E",$telefono_us3, $plan_plantilla);
$plan_plantilla = str_replace("MAIL_E",$mail_us3, $plan_plantilla);
$plan_plantilla = str_replace("NIT_E",$cc_documento_us3, $plan_plantilla);
$plan_plantilla = str_replace("NUIR_E",$nuir_e, $plan_plantilla);
// $plan_plantilla = str_replace("F_RAD_S",$fecha_hoy_corto, $plan_plantilla);
$plan_plantilla = str_replace("RAD_E",$verrad, $plan_plantilla);
$plan_plantilla = str_replace("SAN_RADICACION",$radicado_p, $plan_plantilla);			 
$plan_plantilla = str_replace("SECTOR",$sector_nombre, $plan_plantilla);
$plan_plantilla = str_replace("NRO_PAGS",$radi_nume_hoja, $plan_plantilla);
$plan_plantilla = str_replace("DESC_ANEXOS",$radi_desc_anex, $plan_plantilla);
$plan_plantilla = str_replace("F_HOY_CORTO",$fecha_hoy_corto, $plan_plantilla);
$plan_plantilla = str_replace("F_HOY",$fecha_hoy, $plan_plantilla);
$plan_plantilla = str_replace("NUM_DOCTO",$secuenciaDocto, $plan_plantilla);
$plan_plantilla = str_replace("F_DOCTO",$fechaDocumento, $plan_plantilla);
$plan_plantilla = str_replace("F_DOCTO1",$fechaDocumento2, $plan_plantilla);
$plan_plantilla = str_replace("FUNCIONARIO_ARGO",$usuario_nombre, $plan_plantilla);
$plan_plantilla = str_replace("FUNCIONARIO_ORFEO",$usuario_nombre, $plan_plantilla);
$plan_plantilla = str_replace("LOGIN_ARGO",$krd, $plan_plantilla);
$plan_plantilla = str_replace("DEP_NOMB",$dependencianomb, $plan_plantilla);
$plan_plantilla = str_replace("CIU_TER",$terr_ciu_nomb, $plan_plantilla);
$plan_plantilla = str_replace("DEP_SIGLA",$dep_sigla, $plan_plantilla);
$plan_plantilla = str_replace("DIR_TER",$terr_direccion, $plan_plantilla);
$plan_plantilla = str_replace("TER_L",$terr_nombre, $plan_plantilla);
$plan_plantilla = str_replace("NOM_REC",$nom_recurso, $plan_plantilla);
$plan_plantilla = str_replace("NUM_EXPEDIENTE",$numExpediente, $plan_plantilla);
$plan_plantilla = str_replace("EXPEDIENTE",$numExpediente, $plan_plantilla);
$plan_plantilla = str_replace("DIGNATARIO",$dignatario, $plan_plantilla);
$plan_plantilla = str_replace("DEPE_CODI",$dependencia, $plan_plantilla);
$plan_plantilla = str_replace("DEPENDENCIA_NOMBRE",$dependencia_nombre, $plan_plantilla);
//$plan_plantilla = str_replace("DEPENDENCIA",$dependencia, $plan_plantilla);
$plan_plantilla = str_replace("NOM_R",$nombret_us1_u, $plan_plantilla);
if($rad_salida) $plan_plantilla = str_replace("F_RAD_S",$fecha_rad_salida, $plan_plantilla);
if($rad_salida) $plan_plantilla = str_replace("RAD_S",$rad_salida, $plan_plantilla);
$plan_plantilla = str_replace("DEPTO_R",$dpto_nombre_us1, $plan_plantilla);
$plan_plantilla = str_replace("MPIO_R",$muni_nombre_us1, $plan_plantilla);
$plan_plantilla = str_replace("RAD_ASUNTO",$ra_asun, $plan_plantilla);
$plan_plantilla = str_replace("LOGINORFEO",$krd, $plan_plantilla);
$plan_plantilla = str_replace("DIR_R",$direccion_us1, $plan_plantilla);
$plan_plantilla = str_replace("DEPENDENCIAORFEO",$dependencia, $plan_plantilla);
$plan_plantilla = str_replace("DEPE_CODI",$dependencia, $plan_plantilla);
$plan_plantilla = str_replace("DESTINATARIO_S",$destinatarios_circ, $plan_plantilla);

//$plan_plantilla = $plan_plantillaOld ;
#Notificaciones
$plan_plantilla = str_replace("USUA_PROYECTO",$radi_usua_radi_nombre, $plan_plantilla);

#Memorandos
$plan_plantilla = str_replace("JEFE_BY_RADICADO",$jefeByRadicado, $plan_plantilla);

# Nueva Logica de cargar destinatarios para Salidas, Memos 
$plan_plantilla = str_replace("DESTINATARIO_GEN_R",$destinatrioTotalTrdCodigo, $plan_plantilla);
$plan_plantilla = str_replace("NOMBRE_GEN_R",$nombreGenR, $plan_plantilla);
$plan_plantilla = str_replace("CARGO_GEN_R",$cargoGenR, $plan_plantilla);
?>