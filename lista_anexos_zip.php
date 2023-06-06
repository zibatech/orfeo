<?php
session_start();

if (!$ruta_raiz) $ruta_raiz= ".";
include "$ruta_raiz/conn.php";
include_once("$ruta_raiz/class_control/anexo.php");
require_once("$ruta_raiz/class_control/TipoDocumento.php");
include "$ruta_raiz/processConfig.php";
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once "$ruta_raiz/tx/verLinkArchivo.php";
$ln = $_SESSION["digitosDependencia"];
$opt_ver_anexos_borrados = $_SESSION["opt_ver_anexos_borrados"];
$db = new ConnectionHandler("$ruta_raiz");
$verLinkArchivo = new verLinkArchivo($db);

define('ADODB_ASSOC_CASE', 1);
$objTipoDocto  = new TipoDocumento($db);
$objTipoDocto->TipoDocumento_codigo($tdoc);
$num_archivos=0;
$anex = new Anexo($db);
$sqlFechaDocto = $db->conn->SQLDate("Y-m-D H:i:s A","a.sgd_fech_doc");
$sqlFechaAnexo = $db->conn->SQLDate("Y-m-D H:i:s A","a.anex_fech_anex");
//$sqlFechaAnexo = "to_char(anex_fech_anex, 'YYYY/DD/MM HH:MI:SS')";
$sqlSubstDesc =  $db->conn->substr."(anex_desc, 0, 100)";
//include_once("include/query/busqueda/busquedaPiloto1.php");
//$db->conn->debug = true;
$db->limit(324);
$limitMsql = $db->limitMsql;
$limitOci8 = $db->limitOci8;
$limitPsql = $db->limitPsql;

$db->limit(1);
$limit2Oci8 = $db->limitOci8;
$limit2Psql = $db->limitPsql;
$verrad = trim($_GET['r']);
$isql = "select $limitMsql a.anex_codigo AS DOCU
      		,at.anex_tipo_ext AS EXT
			,a.anex_tamano AS TAMA
			,a.anex_solo_lect AS RO
      		,usua_nomb AS CREA
			,$sqlSubstDesc AS DESCR
			,a.anex_nomb_archivo AS NOMBRE
			,a.ANEX_CREADOR
			,a.ANEX_ORIGEN
			,a.ANEX_SALIDA
			,$radi_nume_salida RADI_NUME_SALIDA
			,a.ANEX_ESTADO
			,a.SGD_PNUFE_CODI
			,a.SGD_DOC_SECUENCIA
			,SGD_DIR_TIPO
			,SGD_DOC_PADRE
			,a.SGD_TPR_CODIGO
			,a.SGD_TRAD_CODIGO
			,a.ANEX_TIPO
			,a.ANEX_FECH_ANEX AANEX_FECH_ANEX
			,a.ANEX_FECH_ANEX
			,a.ANEX_RADI_NUME
			,a.ANEX_TIPO_FINAL
			,a.ANEX_ENV_EMAIL
			,tpr.SGD_TPR_DESCRIP
			,$sqlFechaDocto FECDOC
			,$sqlFechaAnexo FEANEX
			,a.ANEX_TIPO NUMEXTDOC
      ,(SELECT d.sgd_dir_nomremdes from sgd_dir_drecciones d where (d.radi_nume_radi=a.radi_nume_salida) AND a.sgd_dir_tipo=d.sgd_dir_tipo and a.anex_salida=1  limit 1) destino_radicado
      ,rsal.radi_path PATH_RAD_SALIDA
		from  anexos_tipo at ,usuario u,
		anexos a 
		   left join radicado rsal           on (a.radi_nume_salida=rsal.radi_nume_radi)
		   left join sgd_tpr_tpdcumento tpr  on (a.sgd_tpr_codigo=tpr.sgd_tpr_codigo)
      where anex_radi_nume=$verrad and a.anex_tipo=at.anex_tipo_codi
       and a.anex_codigo like '$verrad%'
		   and a.anex_creador=u.usua_login and a.anex_borrado='N' $limitOci8
	   order by a.id, a.anex_codigo, a.ANEX_FECH_ANEX, sgd_dir_tipo,a.anex_radi_nume,a.radi_nume_salida $limitPsql";

$rs = $db->conn->getArray($isql);
/*echo '<pre>';
var_dump($rs);
echo '</pre>';
exit;*/
$zip = new ZipArchive;
$filename = "bodega/tmp/anexos_$verrad.zip";
if ($zip->open($filename, (ZipArchive::CREATE | ZipArchive::OVERWRITE)) !== TRUE) {
    exit("cannot create zip <$filename>\n");
} else {
    $count = 0;
    foreach($rs as $anexo) {
        $count ++;
        $noCache = "?dateNow=".date("ymd_his");
        $noCache = "";
        $numrad = $anexo['DOCU'];
        $resulValiA = $verLinkArchivo->valPermisoAnex($numrad);
        $verImg = $resulValiA['verImg'];
        $pathImagen = $resulValiA['pathImagen'];

        if(!$pathImagen) $pathImagen=$numrad;

        if(substr(trim($numrad),0,1)==1){
            $file = "bodega/".substr(trim($numrad),1,4)."/".ltrim(substr(trim($numrad),4,$ln), '0')."/docs/".trim($pathImagen)."$noCache";
        }else{
            $file = "$ruta_raiz/bodega/".substr(trim($numrad),0,4)."/".ltrim(substr(trim($numrad),4,$ln),'0')."/docs/".trim($pathImagen)."$noCache";
        }
        if($_SESSION["usua_perm_root_email"] == 't'){
            if(substr(trim($numrad),0,1)==1){
                $file = "bodega/".substr(trim($numrad),1,4)."/". $_SESSION["dependencia"]."/docs/".trim($pathImagen)."$noCache";
            }else{
                $file = "$ruta_raiz/bodega/".substr(trim($numrad),0,4)."/". $_SESSION["dependencia"] ."/docs/".trim($pathImagen)."$noCache";
            }
        }

        $extension = pathinfo($pathImagen, PATHINFO_EXTENSION);
        $zip->addFile($file, str_replace(['/'], [' - '], $count.' '.$anexo['DESCR']).'.'.$extension);
    }

    $zip->close();
    header("Content-type: application/zip"); 
    header("Content-Disposition: attachment; filename=anexos_$verrad.zip");
    header("Content-length: " . filesize($filename));
    header("Pragma: no-cache"); 
    header("Expires: 0"); 
    readfile("$filename");
}