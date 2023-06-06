<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$ruta_raiz = "..";
session_start();
require_once($ruta_raiz."/include/db/ConnectionHandler.php");
require_once($ruta_raiz."/processConfig.php");
require_once($ruta_raiz."/include/tx/Radicacion.php");
require_once($ruta_raiz."/include/tx/Historico.php");
require_once($ruta_raiz."/include/tx/usuario.php");
require_once($ruta_raiz."/include/tx/notificacion.php");
require_once($ruta_raiz."/vendor/autoload.php");
require_once($ruta_raiz."/vendor/tmw/fpdm/fpdm.php");
require_once($ruta_raiz."/include/tx/TipoDocumental.php");  
require_once($ruta_raiz."/include/tx/Tx.php"); 
require_once($ruta_raiz."/include/tx/Expediente.php");

/*
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

$rutaExcel = $ABSOL_PATH. '/bodega/tmp/workDir/tasa.xlsx';
$spreadsheet = $reader->load($rutaExcel);


$sheetData = $spreadsheet->getActiveSheet()->toArray();

unset($sheetData[0]);
foreach ($sheetData as $t) {
	echo $t[2] . "<br>";
}	*/

/*
/*$files = glob('path/to/temp/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file)) {
    unlink($file); // delete file
  }
}

/*unlink($ABSOL_PATH . '/bodega/tmp/workDir/0_809011517_1.pdf');
unlink($ABSOL_PATH . '/bodega/tmp/workDir/tasa.xlsx');

if(is_dir($ABSOL_PATH . '/bodega/tmp/workDir/tasaTmp') === false )
{
    mkdir($ABSOL_PATH . '/bodega/tmp/workDir/tasaTmp');
}*/
//unlink($ABSOL_PATH . '/bodega/tmp/workDir/tasa.xlsx');
//echo $ABSOL_PATH;
rename($ABSOL_PATH . '/bodega/3000/900/docs/130000090000124721_00003.xlsx', $ABSOL_PATH . '/bodega/tmp/workDir/tasa.xlsx');
echo "Hola 27";

/*INSERT INTO sgd_sexp_secexpedientes(
	sgd_exp_numero, sgd_srd_codigo, sgd_sbrd_codigo, sgd_sexp_secuencia, depe_codi, usua_doc, sgd_sexp_fech, 
	sgd_fexp_codigo, sgd_sexp_ano, usua_doc_responsable, 
	sgd_sexp_parexp1, sgd_sexp_parexp2, 
	sgd_sexp_parexp3, 
	sgd_sexp_parexp4, 
	sgd_pexp_codigo, sgd_exp_privado, id, sgd_sexp_prestamo, sgd_srd_id, sgd_sbrd_id)
	VALUES ('', 19, 1, 0, 92005, '10057', CURRENT_TIMESTAMP, 
			1, 2021, '10057', 
			'TASA_2018_900345333_IPS PRIVADAS',  'TASA_2018_900345333_IPS PRIVADAS',  
			'SOLICITUD DE LIQUIDACION ADICIONAL DE TASA 2018 900345333 ENDOSTETIC S.A.S 2018 IPS PRIVADAS',
			'TASA_2018_900345333_IPS PRIVADAS',
			0, 0, id, 0, 19, 1);*/


/*$db = new ConnectionHandler($ruta_raiz);
$expediente = new Expediente($db);				

$codepe = 92005;
$sgdSrdCodigo = 19;
$sgdSbrdCodigo = 1;
$anoExp = date("Y");
$secExp = $expediente->secExpediente($codepe,$sgdSrdCodigo,$sgdSbrdCodigo,$anoExp);

$trdExp = substr("00".$sgdSrdCodigo,-2) . substr("00".$sgdSbrdCodigo,-2);
$consecutivoExp = substr("00000".$secExp,-5);
$numeroExpediente = $anoExp . $codepe . $trdExp . $consecutivoExp . 'E';

$sqlInsertExpediente = "INSERT INTO sgd_sexp_secexpedientes(
	sgd_exp_numero, sgd_srd_codigo, sgd_sbrd_codigo, sgd_sexp_secuencia, depe_codi, usua_doc, sgd_sexp_fech, 
	sgd_fexp_codigo, sgd_sexp_ano, usua_doc_responsable, 
	sgd_sexp_parexp1, sgd_sexp_parexp2, 
	sgd_sexp_parexp3, 
	sgd_sexp_parexp4, sgd_sexp_parexp5, 
	sgd_pexp_codigo, sgd_exp_privado, sgd_sexp_prestamo, sgd_srd_id, sgd_sbrd_id)
	VALUES ('$numeroExpediente', $sgdSrdCodigo, $sgdSbrdCodigo, 0, $codepe, '10127', CURRENT_TIMESTAMP
	         ,1, $anoExp, '10057', 
			'TASA_2018_900345333_IPS PRIVADAS',  '900345333',  
			'',
			'SOLICITUD DE LIQUIDACION ADICIONAL DE TASA 2018 900345333 ENDOSTETIC S.A.S 2018 IPS PRIVADAS', '92005-Angelica Rocio Rincon Almansa',
			0, 0, 0, $sgdSrdCodigo, $sgdSbrdCodigo)";

$db->conn->Execute($sqlInsertExpediente);			
$fecha_hoy = Date("Y-m-d");
$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);	


$radicado = "30000090000124721";
$asociarExpediente="insert into SGD_EXP_EXPEDIENTE(SGD_EXP_NUMERO   , RADI_NUME_RADI,SGD_EXP_FECH,DEPE_CODI   ,USUA_CODI   ,USUA_DOC ,SGD_EXP_ESTADO, SGD_FEXP_CODIGO )
	VALUES ('$numeroExpediente',$radicado,".$sqlFechaHoy.",$codepe ,5 ,'10127',0, 0)";
$db->conn->Execute($asociarExpediente);				

$historialExpediente="INSERT INTO sgd_hfld_histflujodoc(
	   sgd_fexp_codigo, sgd_exp_fechflujoant, sgd_hfld_fech, sgd_exp_numero, radi_nume_radi,
	   usua_doc, usua_codi, depe_codi, sgd_ttr_codigo, sgd_fexp_observa, sgd_hfld_observa, 
	   sgd_fars_codigo, sgd_hfld_automatico) values
	   (0,null, CURRENT_TIMESTAMP,'$numeroExpediente' ,$radicado, '10127',5,$codepe, 50, 
		null,'Creacion Expediente', null,null)";
$db->conn->Execute($historialExpediente);				

$historialExpediente="INSERT INTO sgd_hfld_histflujodoc(
	   sgd_fexp_codigo, sgd_exp_fechflujoant, sgd_hfld_fech, sgd_exp_numero, radi_nume_radi,
	   usua_doc, usua_codi, depe_codi, sgd_ttr_codigo, sgd_fexp_observa, sgd_hfld_observa, 
	   sgd_fars_codigo, sgd_hfld_automatico) values
		(0,null, CURRENT_TIMESTAMP,'$numeroExpediente' ,$radicado, '10127',5,$codepe, 53, 
		 null,'Incluir radicado en Expediente', null,null)";
$db->conn->Execute($historialExpediente);*/				

/*
SELECT sgd_exp_numero, sgd_srd_codigo, sgd_sbrd_codigo, sgd_sexp_secuencia, depe_codi, usua_doc, sgd_sexp_fech, sgd_fexp_codigo, sgd_sexp_ano, usua_doc_responsable, sgd_sexp_parexp1, sgd_sexp_parexp2, sgd_sexp_parexp3, sgd_sexp_parexp4, sgd_sexp_parexp5, sgd_pexp_codigo, sgd_exp_fech_arch, sgd_fld_codigo, sgd_exp_fechflujoant, sgd_mrd_codigo, sgd_exp_subexpediente, sgd_exp_privado, sgd_sexp_fechafin, sgd_exp_caja, id, sgd_sexp_parexp6, sgd_sexp_parexp7, sgd_sexp_parexp8, sgd_sexp_parexp9, sgd_sexp_parexp10, sgd_sexp_prestamo, sgd_cerrado, sgd_sexp_estado, sgd_srd_id, sgd_sbrd_id
	FROM sgd_sexp_secexpedientes where sgd_exp_numero = '202192005190100001E'
	
SELECT * from sgd_exp_expediente where sgd_exp_numero = '202192005190100001E'	
select * from sgd_hfld_histflujodoc where sgd_exp_numero = '202192005190100001E'	

SELECT id, usua_codi, depe_codi, usua_login, usua_fech_crea, usua_pasw, usua_esta, usua_nomb, perm_radi, usua_admin, usua_nuevo, usua_doc, codi_nivel, usua_sesion, usua_fech_sesion, usua_ext, usua_nacim, usua_email, usua_at, usua_piso, perm_radi_sal, usua_admin_archivo, usua_masiva, usua_perm_dev, usua_perm_numera_res, usua_doc_suip, usua_perm_numeradoc, sgd_panu_codi, usua_prad_tp1, usua_prad_tp2, usua_prad_tp3, usua_perm_envios, usua_perm_modifica, usua_perm_impresion, usua_prad_tp9, sgd_aper_codigo, usu_telefono1, usua_encuesta, sgd_perm_estadistica, usua_perm_sancionados, usua_admin_sistema, usua_perm_trd, usua_perm_firma, usua_perm_prestamo, usuario_publico, usuario_reasignar, usua_perm_notifica, usua_perm_expediente, usua_login_externo, id_pais, id_cont, perm_tipif_anexo, perm_vobo, perm_archi, perm_borrar_anexo, usua_perm_adminflujos, usua_perm_comisiones, usua_exp_trd, usua_perm_rademail, sgd_rol_codigo, usua_email_1, usua_email_2, usua_perm_respuesta, idacapella1, idacapella, usua_archivo_dig, usua_auth_ldap, usua_login_ldap, usua_prad_tpx1, usua_prad_tpx2, usua_prad_tpx3, usua_prad_tp8, usua_perm_td, usua_prad_tp4, usua_prad_tp5, usua_prad_tp6, usua_prad_tp7
	FROM usuario WHERE depe_codi = 92005
	
INSERT INTO sgd_sexp_secexpedientes(
	sgd_exp_numero, sgd_srd_codigo, sgd_sbrd_codigo, sgd_sexp_secuencia, depe_codi, usua_doc, sgd_sexp_fech, 
	sgd_fexp_codigo, sgd_sexp_ano, usua_doc_responsable, 
	sgd_sexp_parexp1, sgd_sexp_parexp2, 
	sgd_sexp_parexp3, 
	sgd_sexp_parexp4, 
	sgd_pexp_codigo, sgd_exp_privado, id, sgd_sexp_prestamo, sgd_srd_id, sgd_sbrd_id)
	VALUES ('', 19, 1, 0, 92005, '10057', CURRENT_TIMESTAMP, 
			1, 2021, '10057', 
			'TASA_2018_900345333_IPS PRIVADAS',  'TASA_2018_900345333_IPS PRIVADAS',  
			'SOLICITUD DE LIQUIDACION ADICIONAL DE TASA 2018 900345333 ENDOSTETIC S.A.S 2018 IPS PRIVADAS',
			'TASA_2018_900345333_IPS PRIVADAS',
			0, 0, id, 0, 19, 1);	
			
			
INSERT INTO sgd_hfld_histflujodoc(
	   sgd_fexp_codigo, sgd_exp_fechflujoant, sgd_hfld_fech, sgd_exp_numero, radi_nume_radi,
	   usua_doc, usua_codi, depe_codi, sgd_ttr_codigo, sgd_fexp_observa, sgd_hfld_observa, 
	   sgd_fars_codigo, sgd_hfld_automatico) values
	   (0,null, CURRENT_TIMESTAMP,'20218150190100012E' ,202143100110493, '10127',4,8150, 50, 
		null,'Creacion Expediente', null,null)

INSERT INTO sgd_hfld_histflujodoc(
	   sgd_fexp_codigo, sgd_exp_fechflujoant, sgd_hfld_fech, sgd_exp_numero, radi_nume_radi,
	   usua_doc, usua_codi, depe_codi, sgd_ttr_codigo, sgd_fexp_observa, sgd_hfld_observa, 
	   sgd_fars_codigo, sgd_hfld_automatico) values
		(0,null, CURRENT_TIMESTAMP,'20218150190100012E' ,202143100110493, '10127',4,8150, 53, 
		 null,'Incluir radicado en Expediente', null,null),


			
*/
?>