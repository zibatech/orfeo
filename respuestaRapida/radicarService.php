<?php

    if(
           empty($_REQUEST['radPadre'])
        && empty($_REQUEST['dependencia'])
        && empty($_REQUEST['respuesta'])
        && empty($_REQUEST['codusuario'])
        && empty($_REQUEST['usua_log'])
        && empty($_REQUEST['usua_nomb'])
        && empty($_REQUEST['usua_doc'])
        && empty($_REQUEST['tipoRad'])
    )
    die(404);
    session_start();

    function return_bytes($val) {
        $val    = trim($val);
        $ultimo = strtolower($val{strlen($val) - 1});
        switch ($ultimo) {
            // El modificador 'G' se encuentra disponible desde PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
        }
        return $val;
    }
    
    define ('YEAR_INICIO', 0);
    define ('YEAR_LENGTH', 4);
    define ('RADI_LENGTH', 4);
    define ('RADI_INICIO', 4);
    define ('TIPO_PDF',    7);
    define ('APP_NO_INTEGRADA',    0);
    $radPadre = $_REQUEST['radPadre'];
    $directorio_ano  = substr($radPadre, YEAR_INICIO, YEAR_LENGTH);
    $depe_radi_padre = ltrim(substr($radPadre, RADI_INICIO, RADI_LENGTH), '0');
    $ruta_raiz = "../";
    
    include_once($ruta_raiz . '/class_control/anexo.php');
    include_once($ruta_raiz . '/class_control/anex_tipo.php');
    include_once($ruta_raiz. '/include/db/ConnectionHandler.php');
    include_once($ruta_raiz. '/processConfig.php');
    include_once($ruta_raiz. '/include/tx/Envio.php');
    include_once($ruta_raiz. '/processConfig.php');
    include_once($ruta_raiz. '/vendor/autoload.php');

    $krd =  $_REQUEST['usua_log'];
    $_SESSION['USUA_JEFE_DE_GRUPO'] = true;
    $_SESSION['USUA_TRAMITADOR'] = false;
    $_SESSION['USUA_ACTOR_ADMINISTRATIVOS'] = false;
    $_SESSION['usua_id'] = $_REQUEST['codusuario'];
    $_SESSION['entidad'] = "CRA";
    $_SESSION['varTramiteConjunto'] = "0";
    $_SESSION['entidad_largo'] = "Corporación Autónoma Regional del Atlántico";
    $_SESSION['apiFirmaDigital'] = "false";
    $_SESSION['drde'] = "77f66340da28983f211494e50f";
    $_SESSION['usua_doc'] = $_REQUEST['usua_doc'];
    $_SESSION['dependencia'] = $_REQUEST['dependencia'];
    $_SESSION['codusuario'] = $_REQUEST['codusuario'];
    $_SESSION['depe_nomb'] = "DIRECCIÓN ATENCIÓN AL USUARIO ";
    $_SESSION['cod_local'] = "1-170-011-001";
    $_SESSION['depe_municipio'] = "BOGOTÁ. D.C.";
    $_SESSION['usua_email'] = "info@crautonoma.gov.co";
    $_SESSION['usua_email_1'] = null;
    $_SESSION['usua_email_2'] = null;
    $_SESSION['usua_at'] = null;
    $_SESSION['usua_ext'] = null;
    $_SESSION['usua_piso'] = null;
    $_SESSION['usua_nacim'] = null;
    $_SESSION['usua_nomb'] = $_REQUEST['usua_nomb'];
    $_SESSION['usua_nuevo'] = "1";
    $_SESSION['usua_admin_archivo'] = null;
    $_SESSION['usua_masiva'] = "1";
    $_SESSION['usua_perm_dev'] = null;
    $_SESSION['usua_perm_anu'] = "1";
    $_SESSION['perm_radi_sal'] = null;
    $_SESSION['depecodi'] = $_REQUEST['dependencia'];
    $_SESSION['fechah'] = "20210914_";
    $_SESSION['crea_plantilla'] = "1";
    $_SESSION['verrad'] = 0;
    $_SESSION['menu_ver'] = 3;
    $_SESSION['depe_codi_padre'] = null;
    $_SESSION['depe_codi_territorial'] = "3000";
    $_SESSION['nivelus'] = "3";
    $_SESSION['usua_perm_envios'] = null;
    $_SESSION['usua_perm_modifica'] = "4";
    $_SESSION['usuario_reasignacion'] = null;
    $_SESSION['usuario_reasigna_jefes'] = null;
    $_SESSION['descCarpetasPer'] = null;
    $_SESSION['tip3desc'];
    $_SESSION['tip3img'];
    $_SESSION['usua_admin_sistema'] = null;
    $_SESSION['usua_perm_root'] = null;
    $_SESSION['perm_radi'] = null;
    $_SESSION['usua_perm_sancionad'] = null;
    $_SESSION['usua_perm_impresion'] = "3";
    $_SESSION['usua_perm_intergapps'] = null;
    $_SESSION['usua_perm_estadistica'] = "3";
    $_SESSION['usua_perm_archi'] = null;
    $_SESSION['usua_perm_trd'] = null;
    $_SESSION['usua_perm_adminasig'] = null;
    $_SESSION['usua_perm_admin_email_masive'] = null;
    $_SESSION['usua_perm_firma'] =  "1";
    $_SESSION['usua_perm_prestamo'] = null;
    $_SESSION['usuaPermExpediente'] = "2";
    $_SESSION['perm_tipif_anexo'] = null;
    $_SESSION['perm_borrar_anexo'] = null;
    $_SESSION['usua_auth_ldap'] = null;
    $_SESSION['usuaPermRadFax'] = null;
    $_SESSION['usuaPermRadEmail'] = "3";
    $_SESSION['USUA_PRAD_TP1'] = "3";
    $_SESSION['USUA_PRAD_TP2'] = "4";
    $_SESSION['USUA_PRAD_TP3'] = "1";
    $_SESSION['USUA_PRAD_TP4'] = null;
    $_SESSION['USUA_PRAD_TP5'] = null;
    $_SESSION['USUA_PRAD_TP6'] = null;
    $_SESSION['USUA_PRAD_TP7'] = null;
    $_SESSION['USUA_PRAD_TP8'] = null;
    $_SESSION['USUA_PERM_RADEMAIL_AUTO'] = null;
    $_SESSION['USUA_PERM_RADIMAILCLIENT'] = null;
    $_SESSION['USUA_PERM_EXPORTEXP'] = null;
    $_SESSION['varEstaenfisico'] = null;
    $_SESSION['headerRtaPdf'] = "/sys_img/SNS.headerPDF.png";
    $_SESSION['footerRtaPdf'] = "/sys_img/SNS.footerPDF.png";
    $_SESSION['PERM_DESCARGAEXP'] = "1";
    $_SESSION['usua_perm_scor'] = "1";
    $_SESSION['usua_perm_owncloud'] = null;
    $_SESSION['usua_perm_respuesta'] = "1";
    $_SESSION['USUA_PERM_STICKER'] = null;
    $_SESSION['busquedaFullOrfeo'] = null;
    $_SESSION['USUA_PERM_RAD_ESPECIAL'] = null;
    $_SESSION['USUA_PERM_TRANS_RAD'] = null;
    $_SESSION['USUA_PERM_RECOVER_RAD'] = null;
    $_SESSION['USUA_PERM_TODOS_REASIGNA'] = null;
    $_SESSION['USUA_PERM_ONLY_USER'] = null;
    $_SESSION['USUA_LESS_PERM_USER'] = null;
    $_SESSION['USUA_LESS_PERM_USER_PROFILE'] = null;
    $_SESSION['USUA_PERM_ENRUTADOR_TRD'] = null;
    $_SESSION['USUA_PERM_ENRUTADOR'] = null;
    $_SESSION['USUA_PERM_ADM_ESP'] = null;
    $_SESSION['krd'] = $_REQUEST['usua_nomb'];

    $radPadre = $_REQUEST['radPadre'];
    $dependencia = $_REQUEST['dependencia'];
    $respuesta = $_REQUEST['respuesta'];
    $codusuario = $_REQUEST['codusuario'];
    $usua_log = $_REQUEST['usua_log'];
    $usua_nomb = $_REQUEST['usua_nomb'];
    $usua_doc = $_REQUEST['usua_doc'];
    $tipoRad = $_REQUEST['tipoRad'];
    $destinatarios = isset($_REQUEST['destinatarios'])?$_REQUEST['destinatarios']:'';

    $db = new ConnectionHandler($ruta_raiz);
    $sqlFechaHoy = $db->sysdate();
    $anex = new Anexo($db);
    $anexTip = new Anex_tipo($db);
  

    $codigo = null;
    $nuevo = ($codigo)? 'no' : 'si';

    $auxnumero = $anex->obtenerMaximoNumeroAnexo($radPadre);

    // Busca el ultimo radicado
    do {
        $auxnumero += 1;
        $codigo = trim(str_pad($auxnumero, 5, "0", STR_PAD_LEFT));
    } while ($anex->existeAnexo($codigo));

    $numero_anexo = $radPadre . $codigo; 
    $radicado_rem = 1;
    $directorio     = $ruta_raiz.'/bodega/' . $directorio_ano . '/' . $depe_radi_padre . '/docs/';
    if (!file_exists($directorio)) 
    {
        mkdir($directorio, 0777, true);
    }

    $archivo_txt    = $numero_anexo . '.txt';
    $archivo_final  = $numero_anexo . '.pdf';
    $archivo_grabar = $directorio . $archivo_final;
    $archivo_grabar_txt = $directorio . $archivo_txt;
    $file_content   = fopen($archivo_grabar_txt, 'w');
    $write_result   = fwrite($file_content, $respuesta);
    $closing_result = fclose($file_content);
    $tamano         = filesize($archivo_grabar_txt);
    $tamano         = return_bytes($tamano);
    $descr          = 'Pdf Respuesta';
    $anex_salida    = 1;
    $tabla_anexos   = 'anexos';
    $anexo_record['sgd_rem_destino']  = $radicado_rem;
    $anexo_record['anex_radi_nume']   = $radPadre;
    $anexo_record['anex_codigo']      = $numero_anexo;
    $anexo_record['anex_tipo']        = TIPO_PDF;
    $anexo_record['anex_tamano']      = $tamano;
    $anexo_record['anex_solo_lect']   = (isset($auxsololect))?"'$auxsololect'":"'N'";
    $anexo_record['anex_creador']     = "'$usua_log'";
    $anexo_record['anex_desc']        = "'$descr'";
    $anexo_record['anex_numero']      = $auxnumero;
    $anexo_record['anex_nomb_archivo'] = "'$archivo_final'";
    $anexo_record['anex_borrado']      = "'N'";
    $anexo_record['anex_salida']       = $anex_salida;
    $anexo_record['sgd_dir_tipo']      = $radicado_rem;
    $anexo_record['anex_depe_creador'] = $dependencia;
    $anexo_record['sgd_tpr_codigo']    = 0;
    $anexo_record['anex_fech_anex']    = $sqlFechaHoy;
    $anexo_record['sgd_apli_codi']     = APP_NO_INTEGRADA;
    if($tipo_radicado) $anexo_record['sgd_trad_codigo']   = $tipo_radicado;
    $anexo_record['sgd_exp_numero']    = "''";
    $anexo_record['anex_tipo_final']   = 2;
    $anexo_record['sgd_dir_mail']      = "'correo@mail.com'";
    $anexo_record['anex_tipo_envio']   = '1';
    $anexo_record['anex_adjuntos_rr']  = "''";

    $numero_campos = count($anexo_record);
    $i = 0;
    $sql_insert = 'INSERT into ' . $tabla_anexos . ' (';
    $sql_values = 'VALUES (';
    foreach ($anexo_record as $campo => $valor) {
        $i++;
        $sql_insert .= ($i != $numero_campos) ? $campo . ', ' : $campo . ') ';
        $sql_values .= ($i != $numero_campos) ? $valor . ', ' : $valor . ')';
    }
    $sql_insert .= $sql_values;

 
    $db->conn->Execute($sql_insert);
    $jefe_sql = 'SELECT u.usua_login FROM usuario u JOIN autm_membresias m ON m.autu_id = u.id AND autg_id = 2 WHERE u.depe_codi = '.$dependencia.' LIMIT 1';
    $jefe = $db->conn->getOne($jefe_sql);
  
    //radicar anexo..
    
    $_POST['usuanomb'] = $usua_nomb;
    $_POST['usualog'] = $usua_log;
    $_POST['editar'] = '1';
    $_POST['radPadre'] = $radPadre;
    $_POST['usuacodi'] = $codusuario;
    $_POST['depecodi'] = $dependencia;
    $_POST['codigoCiu'] = $usua_doc;
    $_POST['coddepe'] =$dependencia;
    $_POST['usua_actu'] =$usua_log;
    $_POST['coddepe'] =$codusuario;
    $_POST['nurad'] = '';
    $_POST['rutaPadre'] = $ruta_raiz.'/bodega/';
    $_POST['anexo'] = $numero_anexo;
    $_POST['anex_tipo_envio'] = 1;
    $_POST['TIPOS_RADICADOS'] = 'Array';
    $_POST['expAnexo'] = '';
    $_POST['tipo_radicado'] = $tipoRad;
    $_POST['mailsl'] = '';
    $_POST['mails'] = '';
    $_POST['Button'] = 'Radicar';
    $_POST['respuesta'] = $respuesta;
    $_POST['destinatarios'] =$destinatarios;
    $_POST['desdeMasiva'] = '1';
    $_POST['usuaKrdMasiva'] = $jefe;
    
    include_once(__DIR__.'/accion_radicar_anexar.php');
?>
