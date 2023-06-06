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

$db = new ConnectionHandler($ruta_raiz);
$expediente = new Expediente($db);              

//$codepe = 92005;
$depeRadica = 92005;
$usuRadica = 5;
$sgdSrdCodigo = 19;
$sgdSbrdCodigo = 1;
$anoExp = date("Y");
$secExp = $expediente->secExpediente($depeRadica,$sgdSrdCodigo,$sgdSbrdCodigo,$anoExp);

$trdExp = substr("00".$sgdSrdCodigo,-2) . substr("00".$sgdSbrdCodigo,-2);
$consecutivoExp = substr("00000".$secExp,-5);
$numeroExpediente = $anoExp . $depeRadica . $trdExp . $consecutivoExp . 'E';

$sexpParexp1 = "TASA_2019_900549826_IPS PRIVADAS";
$sexpParexp2 = "900549826";
$sexpParexp4 = "SOLICITUD DE LIQUIDACION ADICIONAL DE TASA 2019 900549826 CLINICA DE ESPECIALIDADES ODONTOLOGICAS HAPPY DENTS SAS IPS PRIVADAS";                                   

$sqlInsertExpediente = "INSERT INTO sgd_sexp_secexpedientes(
    sgd_exp_numero, sgd_srd_codigo, sgd_sbrd_codigo, sgd_sexp_secuencia, depe_codi, usua_doc, sgd_sexp_fech, 
    sgd_fexp_codigo, sgd_sexp_ano, usua_doc_responsable, 
    sgd_sexp_parexp1, sgd_sexp_parexp2, 
    sgd_sexp_parexp3, 
    sgd_sexp_parexp4, sgd_sexp_parexp5, 
    sgd_pexp_codigo, sgd_exp_privado, sgd_sexp_prestamo, sgd_srd_id, sgd_sbrd_id)
    VALUES ('$numeroExpediente', $sgdSrdCodigo, $sgdSbrdCodigo, 0, $depeRadica, '10127', CURRENT_TIMESTAMP
             ,1, $anoExp, '10057', 
            '$sexpParexp1',  '$sexpParexp2',  
            '',
            '$sexpParexp4', '92005-Angelica Rocio Rincon Almansa', 0, 0, 0, $sgdSrdCodigo, $sgdSbrdCodigo)";

$db->conn->Execute($sqlInsertExpediente);   
$fecha_hoy = Date("Y-m-d");
$sqlFechaHoy=$db->conn->DBDate($fecha_hoy); 
$nurad = 	20219200500159346;

$asociarExpediente="insert into SGD_EXP_EXPEDIENTE(SGD_EXP_NUMERO, RADI_NUME_RADI,SGD_EXP_FECH,DEPE_CODI   ,USUA_CODI   ,USUA_DOC ,SGD_EXP_ESTADO, SGD_FEXP_CODIGO )
    VALUES ('$numeroExpediente',$nurad,".$sqlFechaHoy.",
      $depeRadica ,$usuRadica ,'10127',0, 0)";
$db->conn->Execute($asociarExpediente);   

$historialExpediente="INSERT INTO sgd_hfld_histflujodoc(
       sgd_fexp_codigo, sgd_exp_fechflujoant, sgd_hfld_fech, sgd_exp_numero, radi_nume_radi,
       usua_doc, usua_codi, depe_codi, sgd_ttr_codigo, sgd_fexp_observa, sgd_hfld_observa, 
       sgd_fars_codigo, sgd_hfld_automatico) values
       (0,null, CURRENT_TIMESTAMP,'$numeroExpediente' ,$nurad, '10127',$usuRadica,$depeRadica, 50, 
        null,'Creacion Expediente', null,null)";
$db->conn->Execute($historialExpediente);


$historialExpediente="INSERT INTO sgd_hfld_histflujodoc(
       sgd_fexp_codigo, sgd_exp_fechflujoant, sgd_hfld_fech, sgd_exp_numero, radi_nume_radi,
       usua_doc, usua_codi, depe_codi, sgd_ttr_codigo, sgd_fexp_observa, sgd_hfld_observa, 
       sgd_fars_codigo, sgd_hfld_automatico) values
        (0,null, CURRENT_TIMESTAMP,'$numeroExpediente' ,$nurad, '10127',$usuRadica,$depeRadica, 53, 
         null,'Incluir radicado en Expediente', null,null)";
$db->conn->Execute($historialExpediente);


echo $numeroExpediente . "<br>";


$secExp = $expediente->secExpediente($depeRadica,$sgdSrdCodigo,$sgdSbrdCodigo,$anoExp);

$trdExp = substr("00".$sgdSrdCodigo,-2) . substr("00".$sgdSbrdCodigo,-2);
$consecutivoExp = substr("00000".$secExp,-5);
$numeroExpediente = $anoExp . $depeRadica . $trdExp . $consecutivoExp . 'E';

$sexpParexp1 = "TASA_2019_901076925_IPS PRIVADAS";
$sexpParexp2 = "901076925";
$sexpParexp4 = "SOLICITUD DE LIQUIDACION ADICIONAL DE TASA 2019 901076925 CENTRO DE ESPECIALIDADES ODONTOLOGICAS INNOVADENTS IPS PRIVADAS";                                   

$sqlInsertExpediente = "INSERT INTO sgd_sexp_secexpedientes(
    sgd_exp_numero, sgd_srd_codigo, sgd_sbrd_codigo, sgd_sexp_secuencia, depe_codi, usua_doc, sgd_sexp_fech, 
    sgd_fexp_codigo, sgd_sexp_ano, usua_doc_responsable, 
    sgd_sexp_parexp1, sgd_sexp_parexp2, 
    sgd_sexp_parexp3, 
    sgd_sexp_parexp4, sgd_sexp_parexp5, 
    sgd_pexp_codigo, sgd_exp_privado, sgd_sexp_prestamo, sgd_srd_id, sgd_sbrd_id)
    VALUES ('$numeroExpediente', $sgdSrdCodigo, $sgdSbrdCodigo, 0, $depeRadica, '10127', CURRENT_TIMESTAMP
             ,1, $anoExp, '10057', 
            '$sexpParexp1',  '$sexpParexp2',  
            '',
            '$sexpParexp4', '92005-Angelica Rocio Rincon Almansa', 0, 0, 0, $sgdSrdCodigo, $sgdSbrdCodigo)";

$db->conn->Execute($sqlInsertExpediente);   
$fecha_hoy = Date("Y-m-d");
$sqlFechaHoy=$db->conn->DBDate($fecha_hoy); 
$nurad = 	20219200500159716;

$asociarExpediente="insert into SGD_EXP_EXPEDIENTE(SGD_EXP_NUMERO, RADI_NUME_RADI,SGD_EXP_FECH,DEPE_CODI   ,USUA_CODI   ,USUA_DOC ,SGD_EXP_ESTADO, SGD_FEXP_CODIGO )
    VALUES ('$numeroExpediente',$nurad,".$sqlFechaHoy.",
      $depeRadica ,$usuRadica ,'10127',0, 0)";
$db->conn->Execute($asociarExpediente);   

$historialExpediente="INSERT INTO sgd_hfld_histflujodoc(
       sgd_fexp_codigo, sgd_exp_fechflujoant, sgd_hfld_fech, sgd_exp_numero, radi_nume_radi,
       usua_doc, usua_codi, depe_codi, sgd_ttr_codigo, sgd_fexp_observa, sgd_hfld_observa, 
       sgd_fars_codigo, sgd_hfld_automatico) values
       (0,null, CURRENT_TIMESTAMP,'$numeroExpediente' ,$nurad, '10127',$usuRadica,$depeRadica, 50, 
        null,'Creacion Expediente', null,null)";
$db->conn->Execute($historialExpediente);


$historialExpediente="INSERT INTO sgd_hfld_histflujodoc(
       sgd_fexp_codigo, sgd_exp_fechflujoant, sgd_hfld_fech, sgd_exp_numero, radi_nume_radi,
       usua_doc, usua_codi, depe_codi, sgd_ttr_codigo, sgd_fexp_observa, sgd_hfld_observa, 
       sgd_fars_codigo, sgd_hfld_automatico) values
        (0,null, CURRENT_TIMESTAMP,'$numeroExpediente' ,$nurad, '10127',$usuRadica,$depeRadica, 53, 
         null,'Incluir radicado en Expediente', null,null)";
$db->conn->Execute($historialExpediente);

echo $numeroExpediente;

?>