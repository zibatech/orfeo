<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/

//$funcion = $_POST['funcion'];
$depeRadica   = $_POST['depeRadica'];
$usuRadica   = $_POST['usuRadica'];

$depeEnvio   = $_POST['depeEnvio'];
$usuEnvio   = $_POST['usuEnvio'];

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

if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

if ($_SESSION["krd"])
    $krd = $_SESSION["krd"];
else
    $krd = "";

//Se elimina archivos temporales
$filesDel = glob($ABSOL_PATH . '/bodega/tmp/workDir/tasaTmp/*'); // get all file names
foreach($filesDel as $file){ // iterate files
  if(is_file($file)) {
    unlink($file); // delete file
  }
}

// Se crea archivo de prueba
$file = $ABSOL_PATH . '/bodega/tasaPDF.log.txt';
if(!is_file($file)){    
    $myfile = fopen($file, "w");
    fclose($myfile);
}

$db = new ConnectionHandler($ruta_raiz);

$fecha = explode(" ", date("d F Y")); 
//$fecha = explode(" ", date("d m Y")); 
$_mes = array(
    "January"   => "Enero",
    "February"  => "Febrero",
    "March"     => "Marzo",
    "April"     => "Abril",
    "May"       => "Mayo",
    "June"      => "Junio",
    "July"      => "Julio",
    "August"    => "Agosto",
    "September" => "Septiembre",
    "October"   => "Octubre",
    "November"  => "Noviembre",
    "December"  => "Diciembre"
);  

$dia = $fecha[0];
$mes = $_mes[$fecha[1]];
$anho = $fecha[2];


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();

$rutaExcel = $ABSOL_PATH. '/bodega/tmp/workDir/tasa.xlsx';
$spreadsheet = $reader->load($rutaExcel);


$sheetData = $spreadsheet->getActiveSheet()->toArray();

$i=1;
unset($sheetData[0]);
$data_from_db=array();
$data_from_db[0]=array();  

$retorno = "";
$contadorGeneral = 0;
foreach ($sheetData as $t) {
    $contadorGeneral++;
     
    if($t[0] != '') {
        $data_from_db[$i]=array(); 
    } else {
        if($t[3] != '') {

            $carpetaOriginal = $t[2] . '/';

            //Buscmaos el archivo en las carpetas según Excel
            $flagFile = false;
            for($k=0;$k<=3000;$k++) {
                $docOriginal = $k . '_' .$t[3] . '.pdf';
                if(is_file($ABSOL_PATH.'bodega/tasa/'. $carpetaOriginal . $docOriginal)){ 
                    $docOriginalPor = explode(".", $docOriginal);
                    $ubicacionOriginal = $ABSOL_PATH.'bodega/tasa/' . $carpetaOriginal 
                        . $docOriginal;
                    $flagFile = true;   
                    break;
                }
            }

            if($flagFile) {

                $ubicacionPdfEditable = $docOriginalPor[0] . "_1.pdf";
                $ubicacionPdfModificado = $docOriginalPor[0] . "_2.pdf";
                $ubicacionPdfParaCombinar = $docOriginalPor[0] . "_3.pdf";
                $ubicacionPdfCombinado = $docOriginalPor[0] . "_4.pdf";
                $ubicacionPdfCombinadoExp = explode(".", $ubicacionPdfCombinado);                

                $rad = new Radicacion($db);
                $rad->radiUsuaActu = $usuRadica;
                $rad->radiDepeActu = $depeRadica;
                $rad->dependencia = $depeRadica;
                $rad->dependenciaRadicacion = $depeRadica;
                $rad->radiTipoDeri = 0;
                $rad->radiCuentai  = "";
                $rad->radiFechOfic = $db->sysdate();
                $rad->descAnex     = "";
                $rad->radiDepeRadi = $depeRadica;
                $rad->nofolios      = 0;
                $rad->noanexos      = 0;
                $rad->sgdSpubCodigo = 0;
                $rad->carpCodi      = 6;
                $rad->raAsun        = "Por medio de la cual se liquida y ordena el pago del valor de la Tasa correspondiente a la Vigencia " . $t[2] . " a la entidad " . $t[4] . " con Nit N° " . $t[3];
                $rad->guia = "";
                $rad->radi_dato_001 = "";
                $rad->radi_dato_002 = "";
                $rad->esta_fisico = 1;
                $rad->tdocCodi = 258;
                $nurad = $rad->newRadicado(6, null);

                if ($nurad=="-1"){
                    $retorno = "Error al generar número radicado";
                    //echo "ocurrio un error al generar el número";                    
                    $out = "Error al generar número radicado";
                    $data_from_db[$i]=array("N° Radicado"=> "-1","Error"=>$out);
                    error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $file);
                    break;                
                } else {

                    $radicadosSel[0] = $nurad;
                    $hist      = new Historico($db);
                    $hist->insertarHistorico( $radicadosSel,
                        $depeRadica,
                        $usuRadica,
                        $depeRadica,
                        $usuRadica,
                        "Se generó radicado de forma masiva OTI/TASA",
                        2);  

                    $divipola = $t[8] . "";

                    if(strlen($divipola) == 4){
                        $dept = substr($divipola, 0, -3); 
                        $ciudad = substr($divipola, 1, 3); 
                        $dept = intval($dept);
                        $ciudad = intval($ciudad);
                    } else {
                        $dept = substr($divipola, 0, -3); 
                        $ciudad = substr($divipola, 2, 3); 
                        $dept = intval($dept);
                        $ciudad = intval($ciudad);  
                    }                    

                    $usuarioArray = array(
                                "cedula"         => $t[3] . "",
                                "nombre"         => $t[4],
                                "apellido"       => "",
                                "dignatario"     => "",
                                "telef"          => $t[7] . "",
                                "direccion"      => $t[5] . "",
                                "email"          => $t[6] . "," . $t[11],
                                "muni"           => "",
                                "muni_tmp"       => $ciudad,
                                "dep"            => "",
                                "dpto_tmp"       => $dept,
                                "pais"           => "",
                                "pais_tmp"       => 170,
                                "cont_tmp"       => 1,
                                "tdid_codi"      => 4,
                                "sgdTrd"         => 2,
                                "id_sgd_dir_dre" => "XX0",
                                "id_table"       => "XX",
                                "sgdDirTipo"     => 1,
                                "medio_envio"    => 2,
                            );    
                    $usuario = new Usuario($db);
                    $respons = $usuario->guardarUsuarioRadicado($usuarioArray, $nurad);
                    if($respons!=1){
                        $retorno = "Error al guardar destinatario: " . $nurad;
                        //echo "Ocurrio un error en guardando usuario <br>";
                        $out = "Error al guardar destinatario: " . $nurad;
                        $data_from_db[$i]=array("N° Radicado"=> "-" . $nurad,"Error"=>$out);
                        error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $file);
                        break;                    
                    }

                    $notificacion = new Notificacion($db);

                    $infoNotificacion['notifica_codi']  = '';
                    $infoNotificacion['med_public']     = 1;
                    $infoNotificacion['caracter_adtvo'] = 1;
                    $infoNotificacion['siad']           = '';
                    $infoNotificacion['prioridad']      = false;
                    $infoNotificacion['radicado'] = $nurad;

                    $respuestaNotificacion = $notificacion->creaEditaNotificacion($infoNotificacion, false);
                    if (!$respuestaNotificacion['status']) {
                        $retorno = "Error al guardar infor adicional de notificacion: " . $nurad;
                        //echo "Ocurrio un error en editarNotificacion <br>";
                        $out = "Error al guardar infor adicional de notificacion: " . $nurad;
                        $data_from_db[$i]=array("N° Radicado"=> "-" . $nurad,"Error"=>$out);
                        error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $file); 
                        break;                      
                    } 

                    $sgd_dir_drecciones_id = $usuario->result["value"];
                    $dupla = array("sgd_dir_codigo" => $sgd_dir_drecciones_id, "orden_codi" => 2);
                    $rtaOrdenNotificacion = $notificacion->creaEditaOrdenesNotificacion($dupla, false);
                    if(!$rtaOrdenNotificacion['status']){
                        $retorno = "Error al guardar orden_acto: " . $nurad;
                        //echo "Ocurrio un error en orden_acto";
                        $out = "Error al guardar orden_acto: " . $nurad;
                        $data_from_db[$i]=array("N° Radicado"=> "-" . $nurad,"Error"=>$out);
                        error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $file);
                        break;                               
                    } 
                    //echo "Radicado número: " . $nurad . "<br>";  
                    $anexo = $nurad . "00001";
                    $documento = $anexo . ".pdf";
                    $path = "/2021/". $depeRadica . "/docs/" . $documento;

                    $sqlInsertRadicado = "INSERT into anexos (sgd_rem_destino, anex_radi_nume, anex_codigo, anex_tipo, anex_tamano, anex_solo_lect, anex_creador, anex_desc, anex_numero, anex_nomb_archivo, anex_borrado, anex_salida, sgd_dir_tipo, anex_depe_creador, sgd_tpr_codigo, anex_fech_anex, sgd_apli_codi, sgd_trad_codigo, sgd_exp_numero, anex_tipo_final, sgd_dir_mail, anex_tipo_envio, anex_adjuntos_rr, idPlantilla, radi_nume_salida, anex_estado) VALUES (1, " . $nurad . ", '" . $anexo . "', 7, 1608, 'N', '" . $krd ."', 'Pdf Respuesta', 1, '" . $documento ."', 'N', 1, 1, " . $depeRadica .", 0, now(), 0, 6, '', 2, 'johans-123@hotmail.com;', 0, '', '100000', " . $nurad . ",'2') ";

                    $db->conn->Execute($sqlInsertRadicado);

                    $sqlEditarRadicado = "UPDATE radicado SET radi_path = '" . $path ."' WHERE radi_nume_radi = " . $nurad;
                    $db->conn->Execute($sqlEditarRadicado);

                    $usua_doc = $_SESSION["usua_doc"];

                    /*
                        Se agrega TRD Automático
                    */

                    $record = array(); 
                    $record["RADI_NUME_RADI"] = $nurad;
                    $record["DEPE_CODI"]      = $depeRadica;
                    $record["USUA_CODI"]      = $usuRadica;
                    $record["USUA_DOC"]       = 10127;    
                    $record["SGD_RDF_FECH"]   = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);

                    $record["SGD_MRD_CODIGO"] = 6050;
                    $nombTrd = "Resolución";
                    $sgdTprCodigo = 258;
                    
                    $insertSQL = $db->insert("SGD_RDF_RETDOCF", $record, "true");

                     $hist->insertarHistorico($radicadosSel,
                        $depeRadica ,
                        $usuRadica,
                        $depeRadica,
                        $usuRadica,
                        "Se agregó TRD Automático: " . $nombTrd,
                        32);   
                    
                    $trd = new TipoDocumental($db);
                    $trd->setFechVenci($nurad,$sgdTprCodigo);      

                    /*
                        ------------------------------------------------------------------------------
                    */

                    /*$carpetaOriginal = '2016/';
                    $docOriginal = '0_824002362.pdf';
                    $docOriginalPor = explode(".", $docOriginal);
                    $ubicacionOriginal = $ABSOL_PATH.'bodega/tasa/' . $carpetaOriginal . $docOriginal;

                    $ubicacionPdfEditable = $docOriginalPor[0] . "_1.pdf";
                    $ubicacionPdfModificado = $docOriginalPor[0] . "_2.pdf";
                    $ubicacionPdfParaCombinar = $docOriginalPor[0] . "_3.pdf";
                    $ubicacionPdfCombinado = $docOriginalPor[0] . "_4.pdf";
                    $ubicacionPdfCombinadoExp = explode(".", $ubicacionPdfCombinado);*/

                    chdir($ABSOL_PATH.'bodega/tmp/workDir/tasaTmp');
                    $commandToPDF = "pdftk " . $ubicacionOriginal . " output " . $ubicacionPdfEditable;
                    $respuesta = exec($commandToPDF, $outToPDF,$stateToPDF);
                    //echo $stateToPDF . " -- " . $respuesta . "<br>";

                    /*$commandToPDF = "pdftk 0_824002366_mod.pdf dump_data_fields output 0_824002366_mod.txt";
                    $respuesta = exec($commandToPDF, $outToPDF,$stateToPDF);
                    echo $stateToPDF . "<br>";*/    

                    if($stateToPDF != 0) {
                        $retorno = "Error en etapa de transformacion PDF para editarlo: " 
                            . $nurad;
                        $out = "Error en etapa de transformacion PDF para editarlo: " . $nurad . " " . $commandToPDF;
                        $data_from_db[$i]=array("N° Radicado"=> "-" . $nurad,"Error"=>$out);
                        error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $file);
                        break;                               
                    } else {

                        $numradNofi = substr($nurad, 0, 16) . "-" . substr($nurad, -1);

                        $fields = array(
                            'Resolucion1'    => "Resolución N° " .  $numradNofi . " de " . $anho,
                            'Resolucion2' => "Resolución N° " .  $numradNofi . " de " . $anho,
                            'Resolucion3'    => "Resolución N° " .  $numradNofi . " de " . $anho,
                            'Fecha1'    => $dia . " de " .  $mes . " de " . $anho,
                            'Fecha2'    => "Dado en Bogotá D.C., a los " . $dia ." días del mes de " . $mes . " de " . $anho,
                            /*'NombreFirmante'    => "Firmado digitalmente por CLAUDIA JANETH VELÁSQUEZ ROMERO",
                            'CargoFirmante'    => "XXXXX",*/
                            'FechaFirma'    => "Firmado digitalmente por:"
                        );

                        $pdf = new FPDM($ABSOL_PATH. '/bodega/tmp/workDir/tasaTmp/' . $ubicacionPdfEditable);
                        $pdf->Load($fields, true); 
                        $pdf->Merge();
                        $pdf->Output(F,$ABSOL_PATH.'/bodega/tmp/workDir/tasaTmp/' . $ubicacionPdfModificado);

                        chdir($ABSOL_PATH.'bodega/tmp/workDir/tasaTmp');
                        $commandFallter = "pdftk " . $ABSOL_PATH. "/bodega/tmp/workDir/tasaTmp/" . $ubicacionPdfModificado . " generate_fdf output " . $ABSOL_PATH. "/bodega/tmp/workDir/tasaTmp/" . $ubicacionPdfParaCombinar;
                        $respuesta = exec($commandFallter, $outToPDF, $stateToPDF); 
                        //echo $stateToPDF . " -- " . $respuesta . "<br>";

                        if($stateToPDF != 0){
                            $retorno = "Error en etapa de generar copia PDF: " . $nurad;
                            $out = "Error en etapa de generar copia PDF: " . $nurad;
                            $data_from_db[$i]=array("N° Radicado"=> "-" . $nurad,"Error"=>$out);
                            error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $file);
                            break;                               
                        } else {

                            chdir($ABSOL_PATH.'bodega/tmp/workDir/tasaTmp');
                            $commandFallter = "pdftk " . $ABSOL_PATH. "/bodega/tmp/workDir/tasaTmp/" . $ubicacionPdfModificado . " fill_form " . $ABSOL_PATH ."/bodega/tmp/workDir/tasaTmp/" . $ubicacionPdfParaCombinar . "  output " . $ABSOL_PATH. "/bodega/tmp/workDir/tasaTmp/" . $ubicacionPdfCombinado . " flatten";
                            $respuesta = exec($commandFallter, $outToPDF, $stateToPDF); 
                            //echo $stateToPDF . " -- " . $respuesta . "<br>";

                            if($stateToPDF != 0){
                                $retorno = "Error en etapa de aplanado PDF: " . $nurad;
                                $out = "Error en etapa de aplanado PDF: " . $nurad;
                                $data_from_db[$i]=array("N° Radicado"=> "-" . $nurad,"Error"=>$out);
                                error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $file);   
                                break;                            
                            } else {

                                $firmasd = $ABSOL_PATH . 'bodega/firmas/';
                                $P12_FILE =  $firmasd . 'server.p12';

                                if (!file_exists($P12_FILE)) {
                                    $P12_FILE = $firmasd . $usua_doc . '.p12';
                                }

                                if ($P12_PASS) {
                                    $clave = $P12_PASS;
                                }

                                //echo $usua_doc . " " . $P12_FILE . " " . $clave;
                                //echo "<br>vamos bien<br> ";
                                    
                                chdir($ABSOL_PATH.'bodega/tmp/workDir/tasaTmp');
                                $commandFirmado='java -jar '.$ABSOL_PATH.'include/jsignpdf-1.6.4/JSignPdf.jar ' . $ABSOL_PATH . "/bodega/tmp/workDir/tasaTmp/" .  $ubicacionPdfCombinado.' -kst PKCS12 -ksf ' . $P12_FILE . ' -ksp ' 
                                    . $clave . ' --font-size 7 -r \'Firmado al Radicar en ORFEO\' -V -v -llx 0 -lly 0 -urx 550 -ury 27 -d ' . $ABSOL_PATH . '/bodega/tmp/workDir/tasaTmp/';

                                      

                                //echo $commandFirmado .'<br>';

                                $out = null;
                                $ret = null;
                                $inf = exec($commandFirmado,$out,$ret);      

                                if($ret != 0) {
                                    //echo "error 1";
                                    $retorno = "Error firmando el documento: " . $nurad;
                                    $out = implode(PHP_EOL, $out);
                                    error_log(date(DATE_ATOM)." ".basename(__FILE__)." ($ret) : $out\n",3,"$ABSOL_PATH/bodega/jsignpdf.log");   

                                    $out = "Error firmando el documento: " . $nurad;
                                    $data_from_db[$i]=array("N° Radicado"=> "-" . $nurad,"Error"=>$out);
                                    error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $file);  
                                    break;                             
                                } elseif($inf=="INFO  Finished: Creating of signature failed."){
                                    //echo "error 2";
                                    $retorno = "Error creando documento firmado: " . $nurad;
                                    $out = "Error creando documento firmado: " . $nurad;
                                    $data_from_db[$i]=array("N° Radicado"=> "-" . $nurad,"Error"=>$out);
                                    error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $file); 
                                    break;                                       
                                } else {

                                    rename($ABSOL_PATH . '/bodega/tmp/workDir/tasaTmp/' . $ubicacionPdfCombinadoExp[0] . '_signed.pdf', 
                                            $ABSOL_PATH . "/bodega" . $path);

                                    /*
                                      *Se agrega histórico de imagen asociada 
                                    */

                                    $hist->insertarHistorico($radicadosSel,
                                        $depeRadica,
                                        $usuRadica,
                                        $depeRadica,
                                        $usuRadica,
                                        "Imagen asociada masiva OTI/TASA " . $nurad,
                                        42);

                                    /*
                                      --------------------------------------------------------------------
                                    */

                                    /*
                                      *Se agrega histórico de firma Digital y queda por la doctora CLAUDA secretaria actual
                                    */  

                                        $hist->insertarHistorico($radicadosSel,
                                            92000,
                                            2,
                                            92000,
                                            2,
                                            "Firmadada digitalmente la respuesta en PDF No " . $nurad, 40);  

                                    /*
                                      --------------------------------------------------------------------
                                    */     

                                    /*
                                      *Se agrega a expedientes
                                    */  

                                    /*$depeRadica   = $_POST['depeRadica'];
                                    $usuRadica   = $_POST['usuRadica'];*/

                                    $expediente = new Expediente($db);              

                                    //$codepe = 92005;
                                    $sgdSrdCodigo = 19;
                                    $sgdSbrdCodigo = 1;
                                    $anoExp = date("Y");
                                    $secExp = $expediente->secExpediente($depeRadica,$sgdSrdCodigo,$sgdSbrdCodigo,$anoExp);

                                    $trdExp = substr("00".$sgdSrdCodigo,-2) . substr("00".$sgdSbrdCodigo,-2);
                                    $consecutivoExp = substr("00000".$secExp,-5);
                                    $numeroExpediente = $anoExp . $depeRadica . $trdExp . $consecutivoExp . 'E';

                                    $sexpParexp1 = "TASA_" . $t[2] . "_" . $t[3] . "_" . $t[12];
                                    $sexpParexp2 = $t[3] . "";
                                    $sexpParexp4 = "SOLICITUD DE LIQUIDACION ADICIONAL DE TASA" . $t[2] . " " . $t[3] . " " . $t[4] . " " . $t[12];                                   

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

                                    $asociarExpediente="insert into SGD_EXP_EXPEDIENTE(SGD_EXP_NUMERO   , RADI_NUME_RADI,SGD_EXP_FECH,DEPE_CODI   ,USUA_CODI   ,USUA_DOC ,SGD_EXP_ESTADO, SGD_FEXP_CODIGO )
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
                                    /*
                                      --------------------------------------------------------------------
                                    */     

                                    /*
                                      *Se envía al área de Notificaciones
                                    */  
                                        $Tx = new Tx($db);
                                        $usCodDestino = $Tx ->reasignar( $radicadosSel, $krd, $depeEnvio, $depeRadica, $usuEnvio, 
                                            $usuRadica, "si", "Para dar trámite", 9, 0);


                                    /*
                                      --------------------------------------------------------------------
                                    */

                                    $retorno = "Creando con exito: " . $nurad;
                                    $data_from_db[$i]=array("N° Radicado"=> "-" . $nurad . "-","Error"=>"");
                                    break;                                       
                                }
                            }
                        }
                    }

                /*
                    Se elimian los archivos auxiliares
                */
                /*unlink($ABSOL_PATH . '/bodega/tmp/workDir/tasaTmp/' . $ubicacionPdfEditable);
                unlink($ABSOL_PATH . '/bodega/tmp/workDir/tasaTmp/' . $ubicacionPdfModificado);
                unlink($ABSOL_PATH . '/bodega/tmp/workDir/tasaTmp/' . $ubicacionPdfParaCombinar);
                unlink($ABSOL_PATH . '/bodega/tmp/workDir/tasaTmp/' . $ubicacionPdfCombinado);*/
                //Se elimina archivos temporales
            
                    
                }                            
            } else{
                $retorno = "No se encontró documento " .  $t[3];
                $out = "No se encontró documento " .  $t[3];
                error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $file);
                $data_from_db[$i]=array("N° Radicado"=>"-1","Error"=>"No se encontró documento");  
                break;  
            }
        } else {
            $retorno = "Sin NIT";
            $out = "Sin NIT";
            error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $file);
            $data_from_db[$i]=array("N° Radicado"=>"-1","Error"=>"Sin NIT");
            break;         
        }
    }   
    $i++;  
}

$filesDel = glob($ABSOL_PATH . '/bodega/tmp/workDir/tasaTmp/*'); // get all file names
foreach($filesDel as $file){ // iterate files
  if(is_file($file)) {
    unlink($file); // delete file
  }
}    

$sheet = $spreadsheet->getActiveSheet();
for($i=0;$i<count($data_from_db);$i++)
{

//set value for indi cell
$row=$data_from_db[$i];

//writing cell index start at 1 not 0
$j=1;

    foreach($row as $x => $x_value) {
        $sheet->setCellValueByColumnAndRow($j,$i+1,$x_value);
        $j=$j+1;
    }

}
$writer = new Xlsx($spreadsheet); 
  
// Save .xlsx file to the files directory 
$writer->save($ABSOL_PATH. '/bodega/tmp/workDir/tasa.xlsx'); 
if($contadorGeneral == count($sheetData)) {
    echo $retorno . " *FIN*";
} else {
    echo $retorno . " " . $contadorGeneral . " " . count($sheetData);
}
?>
