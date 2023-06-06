<?php 
$query = "SELECT
a.*,
b.DEPE_NOMB,
b.DEPE_CODI_TERRITORIAL,
b.DEPE_CODI_PADRE
$queryTRad
$queryDepeRad
FROM
usuario a,
DEPENDENCIA b
WHERE
USUA_LOGIN       = '$krd'
and  a.depe_codi = b.depe_codi";

$comentarioDev  = ' Busca Permisos de Usuarios ...';
$rs             = $db->conn->Execute($query);
if (count($rs->fields) > 0){
$fechah               = date("dmy") . "_" . time("hms");
$dependencia          = $rs->fields["DEPE_CODI"];
$dependencianomb      = $rs->fields["DEPE_NOMB"];
$codusuario           = $rs->fields["USUA_CODI"];
$usua_doc             = $rs->fields["USUA_DOC"];
$usua_nomb            = $rs->fields["USUA_NOMB"];
$usua_piso            = $rs->fields["USUA_PISO"];
$usua_nacim           = $rs->fields["USUA_NACIM"];
$usua_ext             = $rs->fields["USUA_EXT"];
$usua_at              = $rs->fields["USUA_AT"];
$usua_nuevo           = $rs->fields["USUA_NUEVO"];
$usua_email           = $rs->fields["USUA_EMAIL"];
$nombusuario          = $rs->fields["USUA_NOMB"];
$contraxx             = $rs->fields["USUA_PASW"];
$depe_nomb            = $rs->fields["DEPE_NOMB"];
$usua_codi	      = $rs->fields["USUA_CODI"];
}
  $usua_dia = substr($usua_nacim,-2);
  $usua_mes = substr($usua_nacim,5,2);
  $usua_ano = substr($usua_nacim,0,4);

?>

