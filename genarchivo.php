<?php

session_start();

foreach ($_GET as $key => $valor) ${$key} = $valor;
foreach ($_POST as $key => $valor) ${$key} = $valor;

define('ADODB_ASSOC_CASE', 1);

$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$dependencia_nombre = $_SESSION["depe_nomb"];
$usua_doc = $_SESSION["usua_doc"];
$usua_nomb = $_SESSION["usua_nomb"];
$codusuario = $_SESSION["codusuario"];
$nivelus = $_SESSION["nivelus"];
$tip3Nombre = $_SESSION["tip3Nombre"];
$tip3desc = $_SESSION["tip3desc"];
$tip3img = $_SESSION["tip3img"];
$clave=$_REQUEST['clave'];

if (!$ruta_raiz) $ruta_raiz = ".";
include("$ruta_raiz/processConfig.php");
if (isset($db)) unset($db);
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

require_once("$ruta_raiz/class_control/anexo.php");
require_once("$ruta_raiz/class_control/CombinaError.php");
require_once("$ruta_raiz/class_control/Sancionados.php");
require_once("$ruta_raiz/class_control/Dependencia.php");
require_once("$ruta_raiz/class_control/Esp.php");
require_once("$ruta_raiz/class_control/TipoDocumento.php");
require_once("$ruta_raiz/class_control/Radicado.php");
require_once("$ruta_raiz/include/tx/Radicacion.php");
require_once("$ruta_raiz/include/tx/Historico.php");
require_once("$ruta_raiz/class_control/ControlAplIntegrada.php");
require_once("$ruta_raiz/include/tx/Expediente.php");
require_once("$ruta_raiz/include/tx/Historico.php");
require_once("$ruta_raiz/include/tx/Tx.php");

$hist = new Historico($db);
$Tx = new Tx($db);

/** @var $answer Respuesta Ajax variable de notificacion a la solicitud  */
$answer = array();
function saveMessage($type, $message){
    if(!empty($type) and !empty($message)){
        global $answer;
        $answer[$type][] = $message;
        return true;
    }else{
        return false;
    }
}

$noDigitosDep = isset($_SESSION['digitosDependencia'])? $_SESSION['digitosDependencia']: 4;

#Lógica para cambiar borrador por el número de radicado
$numRadicadoPadreInicial = substr($numrad, 0, 4);
$tipo_radicado = substr($numrad, -1);
$debeReasignarBorrador = false;
if($numRadicadoPadreInicial >= 3000 &&($tipo_radicado == 1 || $tipo_radicado == 3 || $tipo_radicado == 4 || $tipo_radicado == 5 || $tipo_radicado == 6 || $tipo_radicado == 7)) {
    
    $debeReasignarBorrador = true;
    $radAux = new Radicacion($db);    
    $dependenciaRadica = substr($numrad, 4, $noDigitosDep);
    $radAux->radiDepeRadi  = $dependenciaRadica;
    $numRadicadoPadreAnt = $numrad;
    $numrad = $radAux->generateNewRadicadoNotificacion($tipo_radicado, $dependenciaRadica);
    $tipoRadPadre     = substr($numrad, -1);
    $radAux ->borradorArradicadoAnexo($numRadicadoPadreAnt, $numrad, $ruta_raiz);

    $anexo = substr($anexo, -5);
    $anexo = $numrad . $anexo;
    if($radicar_a != "si")
        $radicar_a = $anexo;
    $radicar_documento = $numrad;

    $extCodigoCn = explode('.', $linkarchivo);
    $directorioAnoNuevo  = substr($anexo, 0, 4);

    $dependenciaNuevo = ltrim(substr($anexo, 4, $noDigitosDep), '0');
    $consecuntivoNuevo = substr($anexo, -5);
    $linkarchivo = './bodega/' . $directorioAnoNuevo . '/' . $dependenciaNuevo . '/docs/1' . $numrad . '_' . $consecuntivoNuevo . '.' .$extCodigoCn[2];

    $radicadosSelBorr[0] = $numrad;
    $hist->insertarHistorico($radicadosSelBorr, $dependencia, $codusuario, $dependencia, $codusuario, 
            'De Borrador a radicado No ' . $numrad, 104);

    saveMessage('borrador', '' . $numrad);
    saveMessage('borrador', '' . $numRadicadoPadreAnt);
    saveMessage('borrador', '' . $tipo_radicado);

}



header('Content-Type: application/json');

$dep = new Dependencia($db);
$espObjeto = new Esp($db);
$radObjeto = new Radicado($db);
$radObjeto->radicado_codigo($numrad);

//objeto que maneja el tipo de documento del anexos
$tdoc = new TipoDocumento($db);

//objeto que maneja el tipo de documento del radicado
$tdoc2 = new TipoDocumento($db);
$tdoc2->TipoDocumento_codigo($radObjeto->getTdocCodi());

$fecha_dia_hoy = Date("Y-m-d");
//$sqlFechaHoy = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
$sqlFechaHoy = $db->conn->sysTimeStamp;
if ($db->driver == "postgres") $sqlFechaHoy = "now()";
//OBJETO CONTROL DE APLICACIONES INTEGRADAS.
$objCtrlAplInt = new ControlAplIntegrada($db);
//OBJETO EXPEDIENTE
$objExpediente = new Expediente($db);
$expRadi = $objExpediente->consulta_exp($numrad);

$dep->Dependencia_codigo($dependencia);
$dep_sigla = $dep->getDepeSigla();
$nurad = trim($nurad);
$numrad = trim($numrad);
$hora = date("H") . "_" . date("i") . "_" . date("s");
// var que almacena el dia de la fecha
$ddate = date('d');
// var que almacena el mes de la fecha
$mdate = date('m');
// var que almacena el a�o de la fecha
$adate = date('Y');
// var que almacena  la fecha formateada
$fechaArchivo = $adate . "_" . $mdate . "_" . $ddate;
//var que almacena el nombre que tendr� la pantilla
$archInsumo = "tmp_" . $usua_doc . "_" . $fechaArchivo . "_" . $hora . ".txt";
//Var que almacena el nombre de la ciudad de la territorial
$terr_ciu_nomb = $dep->getTerrCiuNomb();
//Var que almacena el nombre corto de la territorial
$terr_sigla = $dep->getTerrSigla();
//Var que almacena la direccion de la territorial
$terr_direccion = $dep->getTerrDireccion();
//Var que almacena el nombre largo de la territorial
$terr_nombre = $dep->getTerrNombre();
//Var que almacena el nombre del recurso
$nom_recurso = $tdoc2->get_sgd_tpr_descrip(); //

if (!$numrad) {
    $numrad = $verrad;
}
if (strlen(trim($radicar_a)) == 13 or strlen(trim($radicar_a)) == 18) {
    $no_digitos = 5;
} else {
    $no_digitos = 6;
}
$linkArchSimple = strtolower($linkarchivo);
$linkArchivoTmpSimple = strtolower($linkarchivotmp);

$linkarchivo = "$ruta_raiz/" . strtolower($linkarchivo);
$linkarchivotmp = "$ruta_raiz/" . strtolower($linkarchivotmp);
$fechah = date("Ymd") . "_" . time("hms");
$trozosPath = explode("/", $linkarchivo);
$nombreArchivo = $trozosPath[count($trozosPath) - 1];

//CAMBIAR LA DESCRIPCION AL RADICAR UN ANEXO
$_set_anex_des = false;

// ABRE EL ARCHIVO
$a = new Anexo($db);
$a->anexoRadicado($numrad, $anexo);
$apliCodiaux = $a->get_sgd_apli_codi();
$anex = $a;
$secuenciaDocto = $a->get_doc_secuencia_formato($dependencia);
$fechaDocumento = $a->get_sgd_fech_doc();
$tipoDocumento = $a->get_sgd_tpr_codigo();
$tdoc->TipoDocumento_codigo($tipoDocumento);
$_sgd_dir_tipo = $a->get_sgd_dir_tipo();

//Start::Multiples destinatarios y copia
$multiSql = "
SELECT 
    string_agg(DISTINCT CONCAT(SGD_DIR_DRECCIONES.sgd_dir_nombre,'(',SGD_DIR_DRECCIONES.sgd_dir_direccion,')'), ',') as destinatarios
FROM
    SGD_DIR_DRECCIONES 
WHERE
    radi_nume_radi = '$verradicado' ";
//End::Obtener los destinatarios multiples
//Start::Obtener los con copia multiples
$multiSqlCopia = "
SELECT 
  string_agg(DISTINCT CONCAT(SGD_DIR_DRECCIONES.sgd_dir_nombre,'(',SGD_DIR_DRECCIONES.sgd_dir_direccion,')'), ',') as destinatarios
FROM
  SGD_DIR_DRECCIONES 
WHERE
  radi_nume_radi = '$verradicado' and sgd_dir_tipo != 1 ";
$rsMultiple = $db->conn->Execute("$multiSql");
$rsConCopia = $db->conn->Execute("$multiSqlCopia");
//End::Obtener los con copia multiples
$destinatarios        =  $rsMultiple->fields["DESTINATARIOS"];
$copias               =  $rsConCopia->fields["DESTINATARIOS"];
//End::Multiples destinatarios y copia
$tipoDocumentoDesc = $tdoc->get_sgd_tpr_descrip();


if ($radicar_documento) {
    //GENERACION DE LA SECUENCIA PARA DOCUMENTOS ESPECIALES  *******************************
    //Generar el Numero de Radicacion
    if (($ent != 2) and $nurad and $vpppp == "ddd") {
        $sec = $nurad;
        $anoSec = substr($nurad, 0, 4);
        // @tipoRad define el tipo de radicado el -X
        $tipoRad = substr($radicar_documento, -1);
    } else {
        if ($vp == "n" and $radicar_a == "si") {
            if ($generar_numero == "no") {
                $sec = substr($nurad, 7, $no_digitos);
                $anoSec = substr($nurad, 0, 4);
                $tipoRad = substr($radicar_documento, -1);
            } else {
                $isql = "select * from ANEXOS where ANEX_CODIGO='$anexo' AND ANEX_RADI_NUME=$numrad";
                $rs = $db->query($isql);
                if (!$rs->EOF) {
                    $radicado_salida = $rs->fields['RADI_NUME_SALIDA'];
                    $expAnexoActual = $rs->fields['SGD_EXP_NUMERO'];
                    if ($expAnexoActual != '') {
                        $expRadi = $expAnexoActual;
                    }
                } else {
                    saveMessage('error',"No se ha podido obtener la informacion del radicado.");
                    die(json_encode($answer));
                }

                if (!$radicado_salida) {
                    $no_digitos = 6;
                    $tipoRad = "1";
                } else {
                    $sec = substr($radicado_salida, 7, $no_digitos);
                    $tipoRad = substr($radicar_documento, -1);
                    $anoSec = substr($radicado_salida, 0, 4);
                    saveMessage('error', 'Ya estaba radicado');
                    die(json_encode($answer));
                    $radicar_a = $radicado_salida;/****/
                }
            }
        } else {
            if ($vp == "s") {
                $sec = "XXX";
            } else {
                // EN ESTA PARTE ES EN LA CUAL SE ENTRA A ASIGNAR EL NUMERO DE RADICADO
                $sec = substr($radicar_a, 7, $no_digitos);
                $anoSec = substr($radicar_a, 0, 4);
                $tipoRad = substr($radicar_a, 13, 1);
            }
        }
        // GENERACION DE NUMERO DE RADICADO DE SALIDA
        $sec = str_pad($sec, $no_digitos, "0", STR_PAD_LEFT);
        $plg_comentarios = "";
        $plt_codi = $plt_codi;
        if (!$anoSec) {
            $anoSec = date("Y");
        }
        if (!$tipoRad) {
            $tipoRad = "1";
        }

        //Adicion para que no reemplace el numero de radicado de un anexo al ser reasignado a otra dependencia
        if ($generar_numero == "no") {
            $rad_salida = $numrad;
        } else {
            //Es un anexo radicado en otra dependencia y no queremos que le genere un nuevo numero
            if ($radicar_a != null && $radicar_a != 'si') {
                $rad_salida = $radicar_a;
            } else {
                $rad_salida = $anoSec . $dependencia . $sec . $tipoRad;
            }
        }


        if ($numerar == 1) {
            $numResol = $a->get_doc_secuencia_formato();
            $rad_salida = date("Y") . $dependencia . str_pad($a->sgd_doc_secuencia(), 6, "0", STR_PAD_left) . $a->get_sgd_tpr_codigo();
        }
    }
    //**********************************************************************************************************************************
    // * FIN GENERACION DE NUMERO DE RADICADO DE SALIDA
    $ext = substr(trim($linkarchivo), -3);
    $extx = explode('.', $linkarchivo);
    $ultimoValor = count($extx)-1;/***/
    $ext = $extx[count($extx) - 1];

    $extVal = strtoupper($ext);
    if ($extVal == "XLS" or $extVal == "PPT" or $extVal == "PDF") {
        saveMessage('error', 'Sobre formato ($ext) no se puede realizar combinaci&oacute;n de correspondencia');
        die(json_encode($answer));
    } else {
        require "$ruta_raiz/jh_class/funciones_sgd.php";
        $verrad = $numrad;
        $radicado_p = $verrad;
        $no_tipo = "true";
        require "$ruta_raiz/ver_datosrad.php";
        include "$ruta_raiz/radicacion/busca_direcciones.php";
        $a = new LOCALIZACION($codep_us1, $muni_us1, $db);
        $dpto_nombre_us1 = $a->departamento;
        $muni_nombre_us1 = $a->municipio;
        $a = new LOCALIZACION($codep_us2, $muni_us2, $db);
        $dpto_nombre_us2 = $a->departamento;
        $muni_nombre_us2 = $a->municipio;
        $a = new LOCALIZACION($codep_us3, $muni_us3, $db);
        $dpto_nombre_us3 = $a->departamento;
        $muni_nombre_us3 = $a->municipio;
        $espObjeto->Esp_nit($cc_documento_us3);
        $nuir_e = $espObjeto->getNuir();

        // Inicializacion de la fecha que va a pasar al reemplazable *F_RAD_S*
        $fecha_hoy_corto = "";
        include "$ruta_raiz/class_control/class_gen.php";

        $b = new CLASS_GEN();
        $date = date("m/d/Y");
        $fecha_hoy = $b->traducefecha($date);
        $fecha_e = $b->traducefecha($radi_fech_radi);
        $fechaDocumento2 = $b->traducefecha_sinDia($fechaDocumento);
        $fechaDocumento = $b->traducefechaDocto($fechaDocumento);

        if ($vp == "n") $archivoFinal = $linkArchSimple;
        else $archivoFinal = $linkArchivoTmpSimple;

        //almacena la extension del archivo a procedar
        $extension = (strrchr($archivoFinal, "."));
        $archSinExt = substr($archivoFinal, 0, strpos($archivoFinal, $extension));
        //Almacena el path completo hacia el archivo a producirse luego de la combinacion

        if (substr($archSinExt, -1) == "d") {
            $caracterDefinitivo = "";
        } else {
            $caracterDefinitivo = "d";
        }

        if ($ext == 'xml' || $ext == 'XML' || $ext == 'odt' || $ext == 'ODT' || $ext == 'DOCX' || $ext == 'docx') {
            $archivoFinal = $archSinExt . "." . $ext;
        } else {
            $archivoFinal = $archSinExt . $caracterDefinitivo . "." . $ext;
        }

        //Almacena el nombre de archivo a producirse luego de la combinacion y que ha de actualizarce en la tabla de anexos
        $archUpdate = substr($archivoFinal,
            strpos($archivoFinal, strrchr($archivoFinal, "/")) + 1,
            strlen($archivoFinal) - strpos($archivoFinal,
                strrchr($archivoFinal, "/")) + 1);
        //Almacena el path de archivo a producirse luego de la combinacion y que ha de actualizarce en la tabla de radicados
        $archUpdateRad = substr_replace($archivoFinal, "", 0, strpos($archivoFinal, "bodega") + strlen("bodega"));
    }
    //****************************************************************************************************
    //$db->conn->debug = true;
    $tipo_docto = $anex->get_sgd_tpr_codigo();
    if (!$tipo_docto) $tipo_docto = 0;

//ESTE INCLUDE PERMITE PASAR HERENCIA A UN ANEXO
include 'datos_rad_padre.php';

    if ($sec and $vp == "n") {

        if ($generar_numero != "no" and $radicar_a == "si") {
            if (!$tpradic) {
                $tpradic = 'null';
        }

        $rad = new Radicacion($db);
            $rad->radiTipoDeri = 0;
            $rad->radiCuentai = "''";
            $rad->eespCodi = $espcodi;
            $rad->mrecCodi = 1;
            $rad->radiFechOfic = $sqlFechaHoy;
            $rad->radiNumeDeri = trim($verrad);
            $rad->descAnex = $desc_anexos;
            $rad->radiPais = "$pais";
            $rad->raAsun = $asunto;

            if ($tpradic == 1) {
                if ($entidad_depsal != 0) {
                    $rad->radiDepeActu = $entidad_depsal;
                    $rad->radiUsuaActu = 1;
                } else {
                    $rad->radiDepeActu = $conf_DependenciaArchivo;
                    $rad->radiUsuaActu = $conf_usuarioArchivo;
                }
            } else {
                $rad->radiDepeActu = $dependencia;
                $rad->radiUsuaActu = $codusuario;
            }

            $rad->radiDepeRadi = $dependencia;
            $rad->trteCodi = "null";
            $rad->tdocCodi = $tipo_docto;
            $rad->tdidCodi = "null";
            $rad->carpCodi = $tpradic; //por revisar como recoger el valor
            $rad->carPer = 0;
            $rad->trteCodi = "null";
            $rad->ra_asun = "'$asunto'";
            $rad->radiPath = "$archUpdateRad";

            if (strlen(trim($apliCodiaux)) > 0 && $apliCodiaux > 0)
                $aplinteg = $apliCodiaux;
            else $aplinteg = "0";

            $rad->sgd_apli_codi = $aplinteg;
            $codTx = 2;
            $flag = 1;

            // Se genera el numero de radicado del anexo
            $noRad = $rad->newRadicado($tpradic, $tpDepeRad[$tpradic]);

	    //Personalizo el codigo de transaccion y el comentario
	    
	    /*CONTROL DE VERSIONES - TRAZABILIDAD  */
	    /*Insertar en el historico cuando se inserta un anexo como nuevo*/

      //  if($numRadicadoPadreInicial )

        if(substr($numrad, -1) == 2) {

                $TX_CODIGO = 2;
                $TX_COMENTARIO = "Radicación No. $noRad para radicación No. $numrad desde Anexos";

        	    $_numrad[0]=$noRad;
        	    $hist->insertarHistorico( $_numrad, $dependencia , $codusuario, $dependencia, $codusuario,$TX_COMENTARIO,$TX_CODIGO);

                if(substr($noRad, -1) == 1) 
                    $codTxSec = 3;
                else
                    $codTxSec = 4;                
                
                $_numrad[0]=$numrad;
                $hist->insertarHistorico($_numrad,
                    $dependencia,
                    $codusuario,
                    $dependencia,
                    $codusuario,
                    "Se genera respuesta " . $noRad . " desde Anexos",
                    $codTxSec);      
        }  else{

            $TX_CODIGO = 2;
            $TX_COMENTARIO = "Radicación Anexo No .$anexo a $noRad";
            $_numrad[0]=$numrad;
            $hist->insertarHistorico( $_numrad, $dependencia , $codusuario, $dependencia, $codusuario,$TX_COMENTARIO,$TX_CODIGO);
        }

  		//le incluyo a la tabla sgd_dir_drecciones el radicado con sus dignatarios
        include 'dignatario_radicado_anexo.php';
            if(substr($noRad,0,1)==0) saveMessage('error', 'No se genero el radicado. '.$noRad.'');

            // Se instancia un objeto para el radicado generado y obtener la fecha real de radicacion
            $radGenerado = new Radicado($db);
            $radGenerado->radicado_codigo($noRad);

            // Asgina la fecha de radicacion

            $fecha_hoy_corto = $radGenerado->getRadi_fech_radi("d-m-Y");

            //BUSCA QUERYS ADICIONALES RESPECTO DE APLICATIVOS INTEGRADOS
            $campos["P_RAD_E"] = $noRad;
            $campos["P_USUA_CODI"] = $codusuario;
            $campos["P_DEPENDENCIA"] = $dependencia;
            $campos["P_USUA_DOC"] = $usua_doc;
            $campos["P_COD_REF"] = $anexo;

            //El nuevo radicado hereda la informacion del expediente del radicado padre
            if (isset($expRadi) && $expRadi != 0) {
                $resultadoExp = $objExpediente->insertar_expediente($expRadi, $noRad, $dependencia, $codusuario, $usua_doc);
				unset($radicados);
                if ($resultadoExp == 1) {
                    $observa = "Se ingresa al expediente del radicado padre ($numrad)";
                    include_once "$ruta_raiz/include/tx/Historico.php";
                    $radicados[] = $noRad;
                    $tipoTx = 53;
                    $Historico = new Historico($db);
                    $Historico->insertarHistoricoExp($expRadi,
                        $radicados,
                        $dependencia,
                        $codusuario,
                        $observa,
                        $tipoTx, 0, 0);
                } else {
                    saveMessage('error', 'No se anexo este radicado al expediente. Verifique que el numero del expediente exista e intente de nuevo.');
                    die(json_encode($answer));
                }
            }

            $estQueryAdd = $objCtrlAplInt->queryAdds($noRad, $campos, $MODULO_RADICACION_DOCS_ANEXOS);
            if ($estQueryAdd == "0") {
                saveMessage('error', 'Error al realizar proceso con anexos');
                die(json_encode($answer));
            }


            /*$radicadosSel[0] = $noRad;
            $hist->insertarHistorico($radicadosSel, $dependencia, $codusuario, $dependencia, $codusuario, " ", $codTx);*/

            if ($noRad == "-1") {
                saveMessage('error', 'Error no genero un Numero de Secuencia o inserto el radicado.');
                die(json_encode($answer));
            }
            $rad_salida = $noRad;
        } else {
            $linkarchivo_grabar = str_replace("bodega", "", $linkarchivo);
            $linkarchivo_grabar = str_replace("./", "", $linkarchivo_grabar);
            $extdoctmp = explode('.', $linkarchivo_grabar);
            $extdoc = $extdoctmp[count($extdoctmp) - 1];
            if ($extdoc == 'doc') {
                $posExt = strpos($linkarchivo_grabar, 'd.doc');
                if ($posExt === false) {

                    $temp = $linkarchivo_grabar;
                    $ruta = str_replace('.doc', 'd.doc', $temp);
                    $linkarchivo_grabar = $ruta;
                }
            }

            $isql = "update RADICADO
                   set RADI_PATH='$linkarchivo_grabar'
                  where RADI_NUME_RADI = $rad_salida";

            $radGenerado = new Radicado($db);
            $radGenerado->radicado_codigo($rad_salida);
            // Asgina la fecha de radicacion
            $fecha_hoy_corto = $radGenerado->getRadi_fech_radi("d-m-Y");
            $rs = $db->query($isql);
            if (!$rs) {
                saveMessage('error', 'No se ha podido Actualizar el Radicado.');
                die(json_encode($answer));
            } else {
		    $archUpdate = $linkarchivo_grabar;

		 
	    //Personalizo el codigo de transaccion y el comentario
	    $TX_CODIGO = 97;
	    $TX_COMENTARIO = "Regenera Radicado Anexo No .$anexo";
	    
	    /*CONTROL DE VERSIONES - TRAZABILIDAD  */
	    /*Insertar en el historico cuando se inserta un anexo como nuevo*/

	    $_numrad[0]=$numrad;
	    $hist->insertarHistorico( $_numrad, $dependencia , $codusuario, $dependencia, $codusuario,$TX_COMENTARIO,$TX_CODIGO);

        $TX_COMENTARIO = "Radicación Anexo No .$anexo a $rad_salida";
        $TX_CODIGO = 2;
        $hist->insertarHistorico( $_numrad, $dependencia , $codusuario, $dependencia, $codusuario,$TX_COMENTARIO,$TX_CODIGO);
	    
            }
        }


        if ($ent == 1) $rad_salida = $nurad;
        // Update Anexos
        $archUpdateFinal = basename($archUpdate);


if ($_set_anex_des == false ){
        $isql = "update ANEXOS set RADI_NUME_SALIDA=$rad_salida,
                  ANEX_SOLO_LECT = 'S',
                  ANEX_RADI_FECH = $sqlFechaHoy,
                  ANEX_ESTADO = 2,
                  ANEX_NOMB_ARCHIVO = '$archUpdateFinal',
                  ANEX_TIPO='$numextdoc',
                  SGD_DEVE_CODIGO = null
		  where ANEX_CODIGO='$anexo' AND ANEX_RADI_NUME=$numrad";
}else{
	$isql = "update ANEXOS set RADI_NUME_SALIDA=$rad_salida,
		ANEX_SOLO_LECT = 'S',
		ANEX_RADI_FECH = $sqlFechaHoy,
		anex_desc = '$asunto',
		ANEX_ESTADO = 2,
		ANEX_NOMB_ARCHIVO = '$archUpdateFinal',
		ANEX_TIPO='$numextdoc',
		SGD_DEVE_CODIGO = null
		where ANEX_CODIGO='$anexo' AND ANEX_RADI_NUME=$numrad";
}

        $rs = $db->query($isql);
        if (!$rs) {
            saveMessage('error', 'No se ha podido actualizar la informacion de anexos. $isql ');
            die(json_encode($answer));
        }


        $isql = "select * from ANEXOS where ANEX_CODIGO='$anexo' AND ANEX_RADI_NUME=$numrad";
        $rs = $db->query($isql);

        if ($rs == false) {
            saveMessage('error','No se ha podido obtener la informacion de anexo');
            die(json_encode($answer));
        }

        $sgd_dir_tipo = $rs->fields["SGD_DIR_TIPO"];
        $anex_desc = $rs->fields["ANEX_DESC"];
        $anex_numero = $rs->fields["ANEX_NUMERO"];
        $direccionAlterna = $rs->fields["SGD_DIR_DIRECCION"];
        $pasar_direcciones = true;
        $dep_radicado = substr($rad_salida, 4, $digitosDependencia);
        $carp_codi = 1;

        if (!$tipo_docto) $tipo_docto = 0;

        $linkarchivo_grabar = str_replace("bodega", "", $linkarchivo);
        $linkarchivo_grabar = str_replace("./", "", $linkarchivo_grabar);

        if ($sgd_dir_tipo == 1) {
            $grbNombresUs1 = $nombret_us1_u;
        }

        //Adiciones para DIR_E SSPD, no quieren que se reemplace DIR_R con DIR_E
        $campos = array();
        $datos = array();
        $anex->obtenerArgumentos($campos, $datos);
       // $vieneDeSancionados = 0;


        //Trae la informacion de Sancionados y genera los campos de combinacion
        $camposSanc = array();
        $datosSanc = array();

        if ($sgd_dir_tipo == 2 ) {
            $dir_tipo_us1 = $dir_tipo_us2;
            $tipo_emp_us1 = $tipo_emp_us2;
            $nombre_us1 = $nombre_us2;
            $grbNombresUs1 = $nombre_us2;
            $documento_us1 = $documento_us2;
            $cc_documento_us1 = $cc_documento_us2;
            $prim_apel_us1 = $prim_apel_us2;
            $seg_apel_us1 = $seg_apel_us2;
            $telefono_us1 = $telefono_us2;
            $direccion_us1 = $direccion_us2;
            $mail_us1 = $mail_us2;
            $muni_us1 = $muni_us2;
            $codep_us1 = $codep_us2;
            $tipo_us1 = $tipo_us2;
            $otro_us1 = $otro_us2;
        }
        if ($sgd_dir_tipo == 3 ) {
            $dir_tipo_us1 = $dir_tipo_us3;
            $tipo_emp_us1 = $tipo_emp_us3;
            $nombre_us1 = $nombre_us3;
            $grbNombresUs1 = $nombre_us3;
            $documento_us1 = $documento_us3;
            $cc_documento_us1 = $cc_documento_us3;
            $prim_apel_us1 = $prim_apel_us3;
            $seg_apel_us1 = $seg_apel_us3;
            $telefono_us1 = $telefono_us3;
            $direccion_us1 = $direccion_us3;
            $mail_us1 = $mail_us3;
            $muni_us1 = $muni_us3;
            $codep_us1 = $codep_us3;
            $tipo_us1 = $tipo_us3;
            $otro_us1 = $otro_us3;
        }
        if ($direccionAlterna and $sgd_dir_tipo == 3) {
            $direccion_us3 = $direccionAlterna;
            $muni_us3 = $muniCodiAlterno;
            $codep_us3 = $dptoCodiAlterno;
        }

        $nurad = $rad_salida;
        $documento_us2 = "";
        $documento_us3 = "";
        $conexion = $db;

//SI EL USUARIO QUE RADICA ES EL PRINCIPAL
if ($_sgd_dir_tipo == 1 ){ 

        if ($numerar != 1) include "$ruta_raiz/radicacion/grb_direcciones.php";

        $actualizados = 4;
        $sgd_dir_tipo = 1;


        // Borro todo lo generando anteriormete .....  para el caso de regenerar
        //$isql = "delete from ANEXOS where RADI_NUME_SALIDA=$nurad
        //       and CAST( sgd_dir_tipo AS VARCHAR(4) ) like '7%' and sgd_dir_tipo !=7 ";
        //$rs = $db->query($isql);
        //if (!$rs) {
        //    saveMessage('error','No se ha borrar los datos previos del radicado');
        //    die(json_encode($answer));
        //}

        $isql = "select ANEX_NUMERO from ANEXOS where ANEX_RADI_NUME = $nurad Order by ANEX_NUMERO desc ";
        $rs = $db->query($isql);
        if (!$rs->EOF)
            $i = $rs->fields['ANEX_NUMERO'];
       

        include_once "./include/query/queryGenarchivo.php";
        $isql = $query1;
        //echo "--->".$isql; exit;

        $rs = $db->query($isql);
        $k = 0;

        while (!$rs->EOF) {
            $anexo_new = $rad_salida . substr("00000" . ($i + 1), -5);
            $sgd_dir_codigo = $rs->fields['SGD_DIR_CODIGO'];
            $radi_nume_radi = $rs->fields['RADI_NUME_RADI'];
            $sgd_dir_tipo = $rs->fields['SGD_DIR_TIPO'];
            $sgd_dir_nombreCompleto =  $rs->fields['NOMBRE_COMPLETO'];
            $sgd_dir_direccion = trim($rs->fields['SGD_DIR_DIRECCION']);

            $sgd_muni_codi = $rs->fields['MUNI_CODI'];
            $sgd_dpto_codi = $rs->fields['DPTO_CODI'];
            $sgd_id_pais = $rs->fields['ID_PAIS'];
            $sgd_id_cont = $rs->fields['ID_CONT'];


            $anex_tipo = "20";
            $anex_creador = $krd;
            $anex_borrado = "N";
            $anex_nomb_archivo = " ";
            $anexo_num = $i + 1;

            $isql = "insert into ANEXOS (ANEX_RADI_NUME,RADI_NUME_SALIDA,ANEX_SOLO_LECT,ANEX_RADI_FECH,ANEX_ESTADO,ANEX_CODIGO  ,anex_tipo   ,ANEX_CREADOR  ,ANEX_NUMERO    ,ANEX_NOMB_ARCHIVO   ,ANEX_BORRADO   ,sgd_dir_tipo)
        VALUES ($verrad       ,$rad_salida     ,'S'           ,$sqlFechaHoy       ,2          ,'$anexo_new','$anex_tipo','$anex_creador','$anexo_num','$anex_nomb_archivo','$anex_borrado','$sgd_dir_tipo')";
           /** $rs2 = $db->query($isql);
            if (!$rs2) {
                saveMessage('error','No se pudo insertar en la tabla de anexos');
                die(json_encode($answer));
            }
            $isql = "UPDATE sgd_dir_drecciones
                 set RADI_NUME_RADI=$rad_salida
                     where sgd_dir_codigo=$sgd_dir_codigo ";
            $rs2 = $db->query($isql);
            if (!$rs2) {
                saveMessage('error','No se pudo actualizar las direcciones');
                die(json_encode($answer));
            } */
            $sgd_dir_tipo++;
            $i++;
            $k++;
            $rs->MoveNext();
        }
        saveMessage('success',"$k copias ");

        if ($actualizados > 0) {
            if ($ent != 1) {
                if ($numerar != 1) {
                    $numerar = $numerar;
                    saveMessage('success'," actualizado $rad_salida ");
                }
            }
        } else {
            saveMessage('error',"No se ha podido radicar el Documento con el N&uacute;mero");
            die(json_encode($answer));
        }
	}else{//if 


$sgd_ciu_codigo = "";
include_once "./include/query/queryGenarchivo.php";
$isql = $query1;
//echo "--->".$isql; exit;
//$db->conn->debug= true;
$rs = $db->query($isql);

while (!$rs->EOF) {
 $sgd_dir_codigo = $rs->fields['SGD_DIR_CODIGO'];
 $sgd_dir_direccion = trim($rs->fields['SGD_DIR_DIRECCION']);
 $sgd_dir_telefono = $rs->fields['SGD_DIR_TELEFONO'];
 $sgd_dir_codigo = $rs->fields['SGD_DIR_CODIGO'];
 $sgd_dir_mail =trim($rs->fields['SGD_DIR_MAIL']);
 $sgd_dir_nombre = $rs->fields['NOMBRE'];
 $sgd_dir_nombreCompleto =  $rs->fields['NOMBRE_COMPLETO'];
 $sgd_dir_apell1 = $rs->fields['APELL1'];
 $sgd_dir_apell2 = $rs->fields['APELL2'];
 $sgd_ciu_cedula = $rs->fields['SGD_CIU_CEDULA'];
 $sgd_dir_tipo = $rs->fields['SGD_DIR_TIPO'];
 $sgd_dir_tipo = 1;
 $sgd_ciu_codigo = $rs->fields['SGD_CIU_CODIGO'];
 $sgd_muni_codi = $rs->fields['MUNI_CODI'];
 $sgd_dpto_codi = $rs->fields['DPTO_CODI'];
 $sgd_id_pais = $rs->fields['ID_PAIS'];
 $sgd_id_cont = $rs->fields['ID_CONT'];
 if($rs->fields['SGD_TRD_CODIGO']) $sgd_trd_codigo = $rs->fields['SGD_TRD_CODIGO']; else $sgd_trd_codigo = "0";
 $a = new LOCALIZACION($sgd_dpto_codi, $sgd_muni_codi, $db);
 $dpto_nombre_us1 = $a->departamento;
 $muni_nombre_us1 = $a->municipio;
$rs->MoveNext();
}

$sgd_dir_nombre = $sgd_dir_nombre." ".$sgd_dir_apell1." ".$sgd_dir_apell2;


//si aun no encuentra el codigo mande errror
if ($sgd_ciu_codigo == ""){
             saveMessage('error',"No se ha podido Actualizar el destinatario, debe tener direccion o email");
             die(json_encode($answer));
}else{
//Actualizo el sgd_dir_direcciones

$grbNombresUs1 = $sgd_dir_nombre;
$cc_documento_us1 = $sgd_ciu_cedula;
$muni_tmp1 = $sgd_muni_codi;
$dpto_tmp1 = $sgd_dpto_codi;
$idpais1 = $sgd_id_pais;
$idcont1 = $sgd_id_cont;
$sgd_ciu_codigo = $sgd_ciu_codigo;
$nurad ;
$direccion_us1 = $sgd_dir_direccion;
$telefono_us1 = $sgd_dir_telefono;
$mail_us1 = $sgd_dir_mail;
$otro_us1 = $sgd_dir_nombre;


if ($numerar != 1) include "$ruta_raiz/radicacion/grb_direcciones.php";
 $actualizados = 4;
 $sgd_dir_tipo = 1;

}

}//if
    }//************
} ////////////**************//////////

//actualizar relaciones para envios
if($rad_salida)
{
    $direcciones_orginales = $db->conn->getAll("SELECT * FROM sgd_dir_drecciones WHERE RADI_NUME_RADI = $numrad");
    $id_anexo = $db->conn->getOne("SELECT id from anexos where radi_nume_salida = $rad_salida");
    foreach($direcciones_orginales as $direccion)
    {
        $id_dir_original = $direccion['ID'];
        $rs_sgd_ciu_codigo = $direccion['SGD_CIU_CODIGO'];
        $rs_sgd_oem_codigo = $direccion['SGD_OEM_CODIGO'];
        $id_nuevo = $db->conn->getOne("SELECT id FROM SGD_DIR_DRECCIONES WHERE RADI_NUME_RADI = $rad_salida AND SGD_CIU_CODIGO = $rs_sgd_ciu_codigo AND SGD_OEM_CODIGO = $rs_sgd_oem_codigo ORDER BY id LIMIT 1");
        //echo "<br>SELECT SGD_DIR_CODIGO FROM SGD_DIR_DRECCIONES WHERE RADI_NUME_RADI = $rad_salida AND SGD_CIU_CODIGO = $rs_sgd_ciu_codigo AND SGD_OEM_CODIGO = $rs_sgd_oem_codigo ORDER BY id LIMIT 1";
        //echo "<br>SELECT id from anexos where radi_nume_salida = $rad_salida";
        $db->conn->execute("UPDATE sgd_rad_envios SET id_direccion = $id_nuevo, estado = 1 WHERE id_anexo = $id_anexo AND id_direccion = $id_dir_original");
    }
}

$ra_asun = str_replace("\n", "-", $ra_asun);
$ra_asun = str_replace("\r", " ", $ra_asun);
$archInsumo = "tmp_" . $usua_doc . "_" . $fechaArchivo . "_" . $hora . ".txt";

$fp = fopen("$ruta_raiz/bodega/masiva/$archInsumo", 'w+');

if (!$fp) {
    $db->conn->RollbackTrans();
    saveMessage('error',"No se pudo abrir el archivo $ruta_raiz/bodega/masiva/$archInsumo");
    die(json_encode($answer));
}

$linkArchivoTxt = $linkArchSimple . ".txt";
$linkArchivoTxtactuales = $linkArchSimple . ".rads.txt";
$linkArchivoTxt = str_replace("1d.docx", "1.docx", $linkArchivoTxt);
$linkArchivoTxtactuales = str_replace("1d.docx", "1.docx", $linkArchivoTxtactuales);

if (is_file($linkArchivoTxt)) {
    $documentosFaltantes = file_get_contents($linkArchivoTxt);
    $documentosActuales = file_get_contents($linkArchivoTxtactuales);
    saveMessage('success',$documentosActuales);
}

fputs($fp, "archivoInicial=$linkArchSimple" . "\n");
fputs($fp, "archivoFinal=$archivoFinal" . "\n");
fputs($fp, "<Radicado>=$rad_salida\n");

$arr = explode("\n", $documentosFaltantes);
$arrActuales = explode("\n", $documentosActuales);

if (is_file($linkArchivoTxt)) {
    foreach ($arr as $value) {
        $docsF .= "$value<br>";
    }
    foreach ($arrActuales as $value) {
        $documentosA .= "$value<br>";
    }
}

$anoRadicado = substr($rad_salida, 0,4);
$secuenciaRadicado = substr(substr($rad_salida, -7),0,6);
$radGuiones = "".$anoRadicado."-".$dependencia."-".$secuenciaRadicado."-".$tipoRad;

$sqlNomApeDesti = "SELECT sgd_dir_nombre, COALESCE (sgd_dir_apellido, '') as sgd_dir_apellido, sgd_dir_nomremdes, sgd_dir_apellido as sgd_dir_apellido_aux,
    sgd_dir_cargo, sgd_trd_codigo, sgd_dir_direccion, sgd_dir_telefono, sgd_dir_mail, muni_codi, dpto_codi  
    FROM sgd_dir_drecciones where radi_nume_radi = $verradicado";
$rsSqlNomApeDesti = $db->conn->Execute($sqlNomApeDesti);  
$nombApellidDestin = "";
while (!$rsSqlNomApeDesti->EOF) {
    $nombApellidDestin = $rsSqlNomApeDesti->fields["SGD_DIR_NOMBRE"] 
        . " " . $rsSqlNomApeDesti->fields["SGD_DIR_APELLIDO"];
    $dignatrio =  trim($rsSqlNomApeDesti->fields["SGD_DIR_NOMREMDES"]);

    if($rsSqlNomApeDesti->fields["SGD_DIR_APELLIDO_AUX"] == '') {
        $nomApleAux = trim($rsSqlNomApeDesti->fields["SGD_DIR_NOMBRE"]);
    } else {
        $nomApleAux = trim($rsSqlNomApeDesti->fields["SGD_DIR_NOMBRE"]) . " " . trim($rsSqlNomApeDesti->fields["SGD_DIR_APELLIDO_AUX"]);
    }

    $nomApleAux = preg_replace('/\s+/', ' ', $nomApleAux);
    $dignatrio = preg_replace('/\s+/', ' ', $dignatrio);
    if($dignatario == $nomApleAux) {
      $dignatario = "";
    }    

    $sgd_muni_codi = $rsSqlNomApeDesti->fields['MUNI_CODI'];
    $sgd_dpto_codi = $rsSqlNomApeDesti->fields['DPTO_CODI'];

    $locali = new LOCALIZACION($sgd_dpto_codi, $sgd_muni_codi, $db);
    $dpto_nombre_us1 = $locali->departamento;
    $muni_nombre_us1 = $locali->municipio;

    //Nueva Logica de cargar destinatarios para Salidas, Memos 
    $sgdTrdCodigoAux = $rsSqlNomApeDesti->fields["SGD_TRD_CODIGO"];

    if($sgdTrdCodigoAux == 0 || $sgdTrdCodigoAux == 1 || $sgdTrdCodigoAux == 6) {
        $nombreGenR = trim($rsSqlNomApeDesti->fields["SGD_DIR_NOMBRE"]) . " " .  trim($rsSqlNomApeDesti->fields["SGD_DIR_APELLIDO"]) . "";
        $cargoGenR  = (trim($rsSqlNomApeDesti->fields["SGD_DIR_CARGO"]) != "") ? trim($rsSqlNomApeDesti->fields["SGD_DIR_CARGO"]) . " " : "";
        $entdGenR = "";
        $repLegGenR = "";
        $dirGenR    = (trim($rsSqlNomApeDesti->fields["SGD_DIR_DIRECCION"]) != "") ? trim($rsSqlNomApeDesti->fields["SGD_DIR_DIRECCION"]) . " " : "";
        $mailGenR   = (trim($rsSqlNomApeDesti->fields["SGD_DIR_MAIL"] != "")) ? trim($rsSqlNomApeDesti->fields["SGD_DIR_MAIL"]) . " " : "";
        $telefonoGenR = (trim($rsSqlNomApeDesti->fields["SGD_DIR_TELEFONO"] != "")) ? trim($rsSqlNomApeDesti->fields["SGD_DIR_TELEFONO"]) . " " : "";
        $deptoGenR = (trim($dpto_nombre_us1) != "") ? trim($dpto_nombre_us1) . " " : "";
        $muniGenR = (trim($muni_nombre_us1) != "") ? trim($muni_nombre_us1) . " " : "";
      } elseif ($sgdTrdCodigoAux == 2) {
        $nombreGenR = trim($rsSqlNomApeDesti->fields["SGD_DIR_NOMREMDES"]) . " ";
        $cargoGenR = ($rsSqlNomApeDesti->fields["SGD_DIR_CARGO"] != "") ? $rsSqlNomApeDesti->fields["SGD_DIR_CARGO"] . " " : "";
        $entdGenR = ($rsSqlNomApeDesti->fields["SGD_DIR_NOMBRE"] != "") ? $rsSqlNomApeDesti->fields["SGD_DIR_NOMBRE"] . " " : "";
        $repLegGenR = ($rsSqlNomApeDesti->fields["SGD_DIR_APELLIDO"] != "") ? $rsSqlNomApeDesti->fields["SGD_DIR_APELLIDO"] . " " : "";
        $dirGenR = (trim($rsSqlNomApeDesti->fields["SGD_DIR_DIRECCION"]) != "") ? trim($rsSqlNomApeDesti->fields["SGD_DIR_DIRECCION"]) . " " : "";
        $mailGenR = (trim($rsSqlNomApeDesti->fields["SGD_DIR_MAIL"] != "")) ? trim($rsSqlNomApeDesti->fields["SGD_DIR_MAIL"]) . " " : "";
        $telefonoGenR = (trim($rsSqlNomApeDesti->fields["SGD_DIR_TELEFONO"] != "")) ? trim($rsSqlNomApeDesti->fields["SGD_DIR_TELEFONO"]) . " " : "";
        $deptoGenR = (trim($dpto_nombre_us1) != "") ? trim($dpto_nombre_us1) . " " : "";
        $muniGenR = (trim($muni_nombre_us1) != "") ? trim($muni_nombre_us1) . " " : "";
      } 

    break;
}

$sqlJefeByRadicado = "SELECT us.id, us.usua_nomb FROM usuario  us join 
  dependencia dp on dp.depe_codi = us.depe_codi join autm_membresias am on us.id = am.autu_id where 
  us.depe_codi= (select depe_codi FROM radicado where radi_nume_radi = $verradicado) 
  and am.autg_id = 2";
$rsSqlJefeByRadicado = $db->conn->Execute($sqlJefeByRadicado);  
$jefeByRadicado = "";
while (!$rsSqlJefeByRadicado->EOF) {
    $jefeByRadicado = $rsSqlJefeByRadicado->fields["USUA_NOMB"];
    break;
}

$numradNofi = substr($rad_salida, 0, 16) . "-" . substr($rad_salida, -1);

#Logica nueva para plantillas de salidas y memos
if($tipo_radicado == 1) {

}

fputs($fp, "RA_NOTI_S=$numradNofi\n");
fputs($fp, "<USUARIO>=$nombret_us1_u\n");
fputs($fp, "<SGD_CIU_DIRECCION>=$sgd_dir_direccion\n");
fputs($fp, "<DPTO_NOMB>=$dpto_nombre_us1\n");
fputs($fp, "<MUNI_NOMB>=$muni_nombre_us1\n");
fputs($fp, "<NOMBRE_DE_LA_EMPRESA>=$nombret_us3_u\n");
fputs($fp, "*DOC_FALTA*=$docsF\n");
fputs($fp, "*RAD_E_PADRE*=$radicado_p\n");
fputs($fp, "RAD_E_PADRE=$radicado_p\n");
fputs($fp, "*CTA_INT*=$cuentai\n");
fputs($fp, "CTA_INT=$cuentai\n");
fputs($fp, "RAD_GUIONES=$radGuiones\n");
fputs($fp, "*ASUNTO*=$ra_asun\n");
fputs($fp, "*F_RAD_E*=$fecha_e\n");
fputs($fp, "*SAN_FECHA_RADICADO*=$fecha_e\n");
fputs($fp, "*RA_ASUN*=$ra_asun\n");
fputs($fp, "RA_ASUN=$ra_asun\n");
//fputs($fp, "*NOM_R*=$sgd_dir_nombreCompleto\n");
//fputs($fp, "NOM_R=$sgd_dir_nombreCompleto\n");
fputs($fp, "*DIR_R*=$sgd_dir_direccion\n");
fputs($fp, "DIR_R=$sgd_dir_direccion\n");
fputs($fp, "*DEPTO_R*=$dpto_nombre_us1\n");
fputs($fp, "DEPTO_R=$dpto_nombre_us1\n");
fputs($fp, "*MPIO_R*=$muni_nombre_us1\n");
fputs($fp, "MPIO_R=$muni_nombre_us1\n");
fputs($fp, "*TEL_R*=$telefono_us1\n");
fputs($fp, "TEL_R=$telefono_us1\n");
fputs($fp, "*MAIL_R*=$mail_us1\n");
fputs($fp, "MAIL_R=$mail_us1\n");
fputs($fp, "*DOC_R*=$cc_documentous1\n");
fputs($fp, "DOC_R=$cc_documentous1\n");
fputs($fp, "*NOM_P*=$nombret_us2_u\n");
fputs($fp, "NOM_P=$nombret_us2_u\n");
fputs($fp, "*DIR_P*=$direccion_us2\n");
fputs($fp, "DIR_P=$direccion_us2\n");
fputs($fp, "*DEPTO_P*=$dpto_nombre_us2\n");
fputs($fp, "DEPTO_P=$dpto_nombre_us2\n");
fputs($fp, "*MPIO_P*=$muni_nombre_us2\n");
fputs($fp, "MPIO_P=$muni_nombre_us2\n");
fputs($fp, "*TEL_P*=$telefono_us1\n");
fputs($fp, "TEL_P=$telefono_us1\n");
fputs($fp, "*MAIL_P*=$mail_us2\n");
fputs($fp, "*DOC_P*=$cc_documento_us2\n");
fputs($fp, "*F_RAD_S*=$fecha_hoy_corto\n");
fputs($fp, "F_RAD_S=$fecha_hoy_corto\n");
fputs($fp, "*RAD_E*=$radicado_p\n");
fputs($fp, "RAD_E=$radicado_p\n");
fputs($fp, "*SAN_RADICACION*=$radicado_p\n");
fputs($fp, "*SECTOR*=$sector_nombre\n");
fputs($fp, "*NRO_PAGS*=$radi_nume_hoja\n");
fputs($fp, "*DESC_ANEXOS*=$radi_desc_anex\n");
fputs($fp, "DESC_ANEXOS=$radi_desc_anex\n");
fputs($fp, "*F_HOY_CORTO*=$fecha_hoy_corto\n");
fputs($fp, "F_HOY_CORTO=$fecha_hoy_corto\n");
fputs($fp, "*F_HOY*=$fecha_hoy\n");
fputs($fp, "F_HOY=$fecha_hoy\n");
fputs($fp, "*NUM_DOCTO*=$secuenciaDocto\n");
fputs($fp, "*F_DOCTO*=$fechaDocumento\n");
fputs($fp, "*F_DOCTO1*=$fechaDocumento2\n");
fputs($fp, "*FUNCIONARIO*=$usua_nomb\n");
fputs($fp, "*LOGIN*=$krd\n");
fputs($fp, "LOGIN=$krd\n");
fputs($fp, "*DEP_NOMB*=$dependencianomb\n");
fputs($fp, "DEP_NOMB=$dependencianomb\n");
fputs($fp, "*CIU_TER*=$terr_ciu_nomb\n");
fputs($fp, "*DEP_SIGLA*=$dep_sigla\n");
fputs($fp, "DEP_SIGLA=$dep_sigla\n");
fputs($fp, "*DIR_TER*=$terr_direccion\n");
fputs($fp, "*EXPEDIENTE*=$expRadi\n");
fputs($fp, "*NUM_EXPEDIENTE*=$expRadi\n");
fputs($fp, "NUM_EXPEDIENTE=$expRadi\n");
fputs($fp, "DIGNATARIO=$dignatario\n");
fputs($fp, "*DEPE_CODI*=$dependencia\n");
fputs($fp, "DEPE_CODI=$dependencia\n");
fputs($fp, "*DEPENDENCIA*=$dependencia\n");
fputs($fp, "*DEPENDENCIA_NOMBRE*=$dependencia_nombre\n");
fputs($fp, "DEPENDENCIA_NOMBRE=$dependencia_nombre\n");
//fputs($fp, "*NOM_R*=$nombret_us1_u\n");
//fputs($fp, "NOM_R=$nombret_us1_u\n");
fputs($fp, "*NOM_R*=$nombApellidDestin\n");
fputs($fp, "NOM_R=$nombApellidDestin\n");
fputs($fp, "*F_RAD*=$fecha_hoy_corto\n");
fputs($fp, "F_RAD=$fecha_hoy_corto\n");
fputs($fp, "*RAD_S*=$rad_salida\n");
fputs($fp, "RAD_BARRAS=*$rad_salida*\n");
fputs($fp, "RAD_S=$rad_salida\n");
fputs($fp, "*DEPTO_R*=$dpto_nombre_us1\n");
fputs($fp, "DEPTO_R=$dpto_nombre_us1\n");
fputs($fp, "*MPIO_R*=$muni_nombre_us1\n");
fputs($fp, "MPIO_R=$muni_nombre_us1\n");
fputs($fp, "*RAD_ASUNTO*=$ra_asun\n");
fputs($fp, "RAD_ASUNTO=$ra_asun\n");
fputs($fp, "*LOGINORFEO*=$krd\n");
fputs($fp, "*DIR_R*=$sgd_dir_direccion\n");
fputs($fp, "*DEPENDENCIAORFEO*=$dependencia\n");
fputs($fp, "DEPENDENCIAORFEO=$dependencia\n");
fputs($fp, "*DEPE_CODI*=$dependencia\n");
fputs($fp, "DEPE_CODI=$dependencia\n");
fputs($fp, "NOMBRESDESTINATARIOS=$destinatarios\n");
fputs($fp, "CONCOPIA=$copias\n");
fputs($fp, "USUA_NOMB_S=$usua_nomb\n");
fputs($fp, "DIA_S=$ddate\n");
fputs($fp, "MES_S=$mdate\n");
fputs($fp, "ANHO_S=$adate\n");
fputs($fp, "JEFE_BY_RADICADO=$jefeByRadicado\n");

fputs($fp, "NOMBRE_GEN_R=$nombreGenR\n");
fputs($fp, "CARGO_GEN_R=$cargoGenR\n");
fputs($fp, "ENT_GEN_R=$entdGenR\n");
fputs($fp, "REP_LEG_GEN_R=$repLegGenR\n");
fputs($fp, "DIR_GEN_R=$dirGenR\n");
fputs($fp, "MAIL_GEN_R=$mailGenR\n");
fputs($fp, "TELE_GEN_R=$telefonoGenR\n");
fputs($fp, "DEPTO_GEN_R=$deptoGenR\n");
fputs($fp, "MUNI_GEN_R=$muniGenR\n");

$sqlInfoAuxiliar = "select radi_depe_radi, radi_usua_radi from radicado where
      radi_nume_radi = " . $_numrad[0];
$rsSqlInfoAuxiliar = $db->conn->Execute($sqlInfoAuxiliar);
while(!$rsSqlInfoAuxiliar->EOF){
    $radi_depe_radi = $rsSqlInfoAuxiliar->fields["RADI_DEPE_RADI"];
    $radi_usua_radi = $rsSqlInfoAuxiliar->fields["RADI_USUA_RADI"];
    $rsSqlInfoAuxiliar->MoveNext();
}   

$isql  = "select usua_nomb from usuario where usua_codi = $radi_usua_radi 
  and depe_codi = $radi_depe_radi ";
$rsUsu = $db->conn->Execute($isql);
$radi_usua_radi_nombre = $rsUsu->fields["USUA_NOMB"];

$sqlRevisoAprobo = "select usua_codi_dest, depe_codi_dest FROM hist_eventos where radi_nume_radi = " . $_numrad[0] . " and  (sgd_ttr_codigo = 16 or sgd_ttr_codigo = 9) and (usua_codi_dest != " . $codusuario . " or depe_codi_dest != " .  $dependencia . ") and (usua_codi_dest != " . $radi_usua_radi . " or depe_codi_dest != " .  $radi_depe_radi . ") group by usua_codi_dest, depe_codi_dest";

$rsSqlRevisoAprobo = $db->conn->Execute($sqlRevisoAprobo);
$arrayRevisoAprobo = array();
 while (!$rsSqlRevisoAprobo->EOF) {
    $usuaCodiDest = $rsSqlRevisoAprobo->fields["USUA_CODI_DEST"];
    $depeCodiDest = $rsSqlRevisoAprobo->fields["DEPE_CODI_DEST"];

    $sqlRevisoAproboNom = "select usua_nomb from usuario 
        where usua_codi = " . $usuaCodiDest . " and depe_codi = " . $depeCodiDest;
    $rsSqlRevisoAproboNom = $db->conn->Execute($sqlRevisoAproboNom);
    $RevisoAproboNom = $rsSqlRevisoAproboNom->fields["USUA_NOMB"];
    array_push($arrayRevisoAprobo, $RevisoAproboNom);
    $rsSqlRevisoAprobo->MoveNext();
 }

if($tipo_radicado == 4 || $tipo_radicado == 5) {
        include_once("$ruta_raiz/include/tx/notificacion.php");
        $notificacion = new Notificacion($db);
        $destinatarios_circ = $notificacion->destinatariosPorRadicado($numrad);
        $destinatarios_circ = $destinatarios_circ[0]["DESTINATARIOS"];
        fputs($fp, "DESTINATARIO_S=$destinatarios_circ\n");
} 

 /*if($tipo_radicado == 4 || $tipo_radicado == 5 || $tipo_radicado == 6 
    || $tipo_radicado == 7) {




     $usuaReviso = "";
     $contadorRevisoAprobo = 1;
     $totalRevisoAprobo = count($arrayRevisoAprobo);
     foreach ($arrayRevisoAprobo as &$valor) {
        if($contadorRevisoAprobo == 1 && $totalRevisoAprobo != 1)
            $usuaReviso .= $valor . " -- ";
        elseif ($contadorRevisoAprobo == 1 && $totalRevisoAprobo == 1) {
            $usuaReviso .= $valor;
            $usuaAprobo = $valor;
        } elseif ($contadorRevisoAprobo == $totalRevisoAprobo) {
            $usuaAprobo = $valor;
        } else {
            $usuaReviso .= $valor . " -- ";
        }
        $contadorRevisoAprobo++;    
     }

    fputs($fp, "USUA_PROYECTO=$radi_usua_radi_nombre\n");
    fputs($fp, "USUA_REVISO=$usuaReviso\n");
    fputs($fp, "USUA_APROBO=$usuaAprobo\n");
} else {*/

     $usuaReviso = "";
     $contadorRevisoAprobo = 1;
     $totalRevisoAprobo = count($arrayRevisoAprobo);
     foreach ($arrayRevisoAprobo as &$valor) {
        if($contadorRevisoAprobo == $totalRevisoAprobo) {
            $usuaReviso .= $valor;       
        } else {
            $usuaReviso .= $valor . ' -- ';
        }
        $contadorRevisoAprobo++;    
     }

    fputs($fp, "USUA_PROYECTO=$radi_usua_radi_nombre\n");
    fputs($fp, "USUA_REVISO=$usuaReviso\n");
    fputs($fp, "USUA_APROBO=$usua_nomb\n");   

//}

for ($i_count = 0; $i_count < count($camposSanc); $i_count++) {
    fputs($fp, trim($camposSanc[$i_count]) . "=" . trim($datosSanc[$i_count]) . "\n");
}

for ($i_count = 0; $i_count < count($campos); $i_count++) {
    fputs($fp, trim($campos[$i_count]) . "=" . trim($datos[$i_count]) . "\n");
}

fclose($fp);
//El include del servlet hace que se altere el valor
//de la variable $estadoTransaccion como 0 si se
//pudo procesar el documento, -1 de lo contrario
$estadoTransaccion = -1;


if ($ext == "ODT" || $ext == "odt") {
    //Se incluye la clase que maneja la combinacion masiva
    include("$ruta_raiz/radsalida/masiva/OpenDocText.class.php");
    define ('WORKDIR', './bodega/tmp/workDir/');
    define ('CACHE', WORKDIR . 'cacheODT/');
    //Se abre archivo de insumo para lectura de los datos
    if (file_exists("$ruta_raiz/bodega/masiva/$archInsumo")) {
        $contenidoCSV = file("$ruta_raiz/bodega/masiva/$archInsumo");
    } else {
        saveMessage('error',"No hay acceso para crear el archivo $archInsumo");
        die(json_encode($answer));
    }

    $accion = false;
    $odt    = new OpenDocText();
    //$odt->debug = true;

    //Se carga el archivo odt Original
    $archivoACargar = str_replace('../', '', $linkarchivo);
    $odt->cargarOdt("$archivoACargar", $nombreArchivo);
    $odt->setWorkDir(WORKDIR);
    $accion = $odt->abrirOdt();

    /*if (!$accion) {
        saveMessage('error',"Problemas en el servidor abriendo archivo ODT para combinaci&oacute;n.");
        die(json_encode($answer));
    }*/

    $odt->cargarContenido();

    //Se recorre el archivo de insumo
    foreach ($contenidoCSV as $line_num => $line) {
        if ($line_num > 1) {
            $cadaLinea = explode("=", $line);
            $cadaVariable[$line_num - 2] = $cadaLinea[0];
            $cadaValor[$line_num - 2] = $cadaLinea[1];
        }
    }

    $tipoUnitario = '1';

    if ($vp == "s") {
        $linkarchivo_grabar = str_replace("bodega/", "", $linkarchivotmp);
        $linkarchivo_grabar = str_replace("./", "", $linkarchivo_grabar);
        $odt->setVariable($cadaVariable, $cadaValor);
        $archivoDefinitivo = $odt->salvarCambios(null, $linkarchivo_grabar, '1');
    } else {
        $linkarchivo_grabar = str_replace("..", "", $linkarchivo_grabar);
        $odt->setVariable($cadaVariable, $cadaValor);
        $archivoDefinitivo = $odt->salvarCambios(null, $linkarchivo_grabar, '1');
    }

    $db->conn->CommitTrans();



if (isset($_REQUEST['clave']) && isset($_SESSION["usua_perm_firma"])){
    $archPdfC= str_replace("./.",$ABSOL_PATH,$linkarchivo);
    $archPdf= str_replace("$nombreArchivo","",$archPdfC);
    $archPdfFirma= str_replace(".odt",".pdf",$archPdfC);
    $nombreArchivoFinal=str_replace('odt','pdf',$nombreArchivo);
    $pathFinal = "/" . substr($rad_salida,0,4) . "/" . substr($rad_salida,4,3) . "/" . "docs/".$nombreArchivoFinal;

    $tmp_sf = '/tmp/'.microtime(true);
    $commandToPDF="soffice --headless -env:UserInstallation=file://$tmp_sf --convert-to pdf ".$archPdfC;
    exec($commandToPDF,$outToPDF,$stateToPDF);
    exec("rm -rf $tmp_sf");

    if ($stateToPDF!=0){
		    unset($answer);
		    $answer=array();
                    saveMessage('error',"Error al convertir el anexo a PDF");
                    die(json_encode($answer));
}
if ($_SESSION['apiFirmaDigital']=='false'){

    $commandFirmado='java  -jar '.$ABSOL_PATH.'/include/jsignpdf-1.6.4/JSignPdf.jar '.str_replace('odt','pdf',$nombreArchivo).' -kst PKCS12  -ksf '.$ABSOL_PATH.'/bodega/firmas/'.$usua_doc.'.p12   -ksp '.$clave.' --font-size 7    -r \'Firmado al Radicar en CRA\'  -V -v   --img-path '.$ABSOL_PATH.'/imagenes/gnu.gif --render-mode  GRAPHIC_AND_DESCRIPTION -llx 0 -lly 0 -urx 550 -ury 27';

//die (exec($commandFirmado));
    if (exec($commandFirmado)=="INFO  Finished: Creating of signature failed."){
			unset($answer);
			$answer=array();
                    saveMessage('error',"Clave de firma digital erronea");
                    die(json_encode($answer));
    }
    rename(str_replace(".odt","_signed.pdf",$nombreArchivo), "../../$pathFinal" );
}elseif ($_SESSION['apiFirmaDigital']=='certicamara') {
include ("include/apiCerticamara/PdfSign.php");
//ruta archivo a firmar
$fileToSignPath = str_replace(".odt",".pdf",$ABSOL_PATH."/bodega/tmp/workDir/$nombreArchivo");
//ruta para guardar el archivo firmado
$fileSignedPath = $ABSOL_PATH."/bodega/$pathFinal";
//ruta del certificado de firma
//$signP12Path = "$root/resources/certificate/cra.p12";
$signP12Path = $ABSOL_PATH.'/bodega/firmas/'.$usua_doc.'.p12';
//password del certificado de firma
//$signP12Password = "Password1";
$signP12Password = $clave;



//ruta del certificado de firma
//$signP12Path = $ABSOL_PATH.'/bodega/firmas/'.$usua_doc.'.p12';
//password del certificado de firma

$comand = "java -jar "
        . "\"$apiPath\" "
        . "\"$signType\" "
        . "\"$xmlConfigPath\" "
        . "\"$fileToSignPath\" "
        . "\"$fileSignedPath\" "
        . "\"$signP12Path\" "
        . "\"$signP12Password\" "
        . "\"$stamp\" "
        . "\"$stampP12Path\" "
        . "\"$stampP12Password\" "
        . "\"$signReason\" "
        . "\"$signLocation\" "
        . "\"$signImageAttrs\" "
		. "\"$ltv\" ";

//echo "Comando: $comand" . PHP_EOL;

try {
    $response = exec($comand, $returns);
    //echo $comand;
    //echo "Response: ";
    //print_r($returns);

    //echo "Returns: ";
    if (end($returns)!='success'){
	$answer='';
	saveMessage('error', utf8_decode(end($returns)));
	echo json_encode($answer);
	die;
    }
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}
}

    if($tipo_radicado == 4 || $tipo_radicado == 5 || $tipo_radicado == 6 
        || $tipo_radicado == 7) { 
        $anexEstado = 2;
    } else {
        $anexEstado = 3;
    }

    $isql="update anexos set ANEX_NOMB_ARCHIVO='$nombreArchivoFinal', ANEX_TIPO=7, ANEX_ESTADO=$anexEstado, SGD_FECH_IMPRES= (SYSDATE+0), ANEX_FECH_ENVIO=(SYSDATE+0), SGD_DEVE_FECH = NULL, SGD_DEVE_CODIGO=NULL where RADI_NUME_SALIDA=$rad_salida";
	$db->conn->Execute($isql);
	$isql="select radi_path from radicado where RADI_NUME_RADI=$rad_salida";
	$rs=$db->conn->GetAll($isql);
	$newRuta=str_replace("odt","pdf",$rs[0]["RADI_PATH"]);
    $isql="update radicado set radi_firma='1', radi_path='$newRuta' where RADI_NUME_RADI=$rad_salida";
$db->conn->Execute($isql);
}
$linkArchivo=substr(str_replace("_","",str_replace('.pdf','',$nombreArchivoFinal)),1);
$linkArchivo=(!empty($linkArchivo))?$linkArchivo:$anexo;
    $scriptNewRad = "$('#codRadi$linkArchivo').text('** $rad_salida'); $('#iconoBorrar$linkArchivo').hide('slow');";
    saveMessage('success', "<script> function abrirArchivo(url){nombreventana='Documento'; window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');return; } $scriptNewRad</script> combinaci&oacute;n de correspondencia realizada <a class='vinculos' href=javascript:void(0) onclick=funlinkArchivo('".$linkArchivo."','.')> Ver Archivo </a>");
    $odt->borrar();
} elseif ($ext == "DOCX" || $ext == "docx") {
    //Se incluye la clase que maneja la combinacion masiva
    include("$ruta_raiz/radsalida/masiva/ooxml.class.php");
    define ('WORKDIR', './bodega/tmp/workDir/');
    define ('CACHE', WORKDIR . 'cacheODT/');
    //Se abre archivo de insumo para lectura de los datos
    $fp = fopen("$ruta_raiz/bodega/masiva/$archInsumo", 'r');

    if ($fp) {
        $contenidoCSV = file("$ruta_raiz/bodega/masiva/$archInsumo");
        fclose($fp);
    } else {
        saveMessage('error',"No hay acceso para crear el archivo $archInsumo ");
        die(json_encode($answer));
    }

    $accion = false;
    $docx = new OoXml();
    //Se carga el archivo odt Original
    $archivoACargar = str_replace('../', '', $linkarchivo);
    $docx->cargarOdt("$archivoACargar", $nombreArchivo);
    $docx->setWorkDir(WORKDIR);
    $accion = $docx->abrirOdt();
    if (!$accion) {
        saveMessage('error',"Problemas en el servidor abriendo archivo DOCX para combinaci&oacute;n.");
    }
    $docx->cargarContenido();

    //Se recorre el archivo de insumo
    foreach ($contenidoCSV as $line_num => $line) {
        if ($line_num > 1) { //Desde la linea 2 hasta el final del archivo de insumo estan los datos de reemplazo
            $cadaLinea = explode("=", $line);
            $cadaVariable[$line_num - 2] = $cadaLinea[0];
            $cadaValor[$line_num - 2] = $cadaLinea[1];
        }
    }
    $tipoUnitario = '1';
    if ($vp == "s") {
        $linkarchivo_grabar = str_replace("bodega/", "", $linkarchivotmp);
        $linkarchivo_grabar = str_replace("./", "", $linkarchivo_grabar);
        $docx->setVariable($cadaVariable, $cadaValor);
        $archivoDefinitivo = $odt->salvarCambios(null, $linkarchivo_grabar, '1');
    } else {
        $docx->setVariable($cadaVariable, $cadaValor);
        $linkarchivo_grabar = str_replace("..", "", $linkarchivo_grabar);
        $docx->salvarCambios(null, $linkarchivo_grabar, '1');
    }
    $db->conn->CommitTrans();

    // firma mecánica
    $firmasd = $ABSOL_PATH.'/bodega/firmas/';
    $grafo = $firmasd.'grafo/'.strtolower($_SESSION['krd']).'.png';
    if (file_exists($grafo)) {
        require_once 'vendor/autoload.php';
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $template= $phpWord->loadTemplate($ABSOL_PATH.'/bodega/'.$linkarchivo_grabar);
        $template->setImageValue('FIRMA', array('path' => $grafo, 'width' => 384, 'height' => 70, 'ratio' => false));
        $template->saveAs($ABSOL_PATH.'/bodega/'.$linkarchivo_grabar);
    }

    /*************************CODIGO PARA FIRMA DIGITAL*****************************/
    $P12_FILE =  $firmasd . 'server.p12';
    $P12_SERVER = file_exists($P12_FILE);

if ( (isset($_REQUEST['clave']) || $P12_SERVER) && isset($_SESSION["usua_perm_firma"])){
    $archPdfC= str_replace("./.",$ABSOL_PATH,$linkarchivo);
    $archPdf= str_replace("$nombreArchivo","",$archPdfC);
    $archPdfFirma= str_replace(".docx",".pdf",$archPdfC);
    $nombreArchivoFinal=str_replace('docx','pdf',$nombreArchivo);
    $pathFinal = "/" . substr($rad_salida,0,4) . "/" . ltrim(substr($rad_salida,4,$digitosDependencia),'0') . "/" . "docs/".$nombreArchivoFinal;

    $tmp_sf = '/tmp/'.microtime(true);
    $commandToPDF="soffice --headless -env:UserInstallation=file://$tmp_sf --convert-to pdf ".$archPdfC;
    exec($commandToPDF,$outToPDF,$stateToPDF);
    exec("rm -rf $tmp_sf");

    if ($stateToPDF!=0){
        $outToPDF = implode(PHP_EOL, $outToPDF);
        error_log(date(DATE_ATOM)." ".basename(__FILE__)." (soffice $stateToPDF) $radicado_p > $nurad: $outToPDF\n",3,"$ABSOL_PATH/bodega/jsignpdf.log");
		    unset($answer);
		    $answer=array();
                    saveMessage('error',"Error al convertir el anexo a PDF");
                    die(json_encode($answer));
    }


if ($_SESSION['apiFirmaDigital']=='false'){
    if (!$P12_SERVER) {
        $P12_FILE = $firmasd . $usua_doc . '.p12';
    }
    if ($P12_PASS) {
        $clave = $P12_PASS;
    }
    $commandFirmado='java -jar '.$ABSOL_PATH.'/include/jsignpdf-1.6.4/JSignPdf.jar '.str_replace('docx','pdf',$nombreArchivo).' -kst PKCS12 -ksf '.$P12_FILE.' -ksp '.$clave.' --font-size 7 -r \'Firmado al Radicar en CRA\' -V -v -llx 0 -lly 0 -urx 550 -ury 27';
    $out = null;
    $ret = null;
    $inf = exec($commandFirmado,$out,$ret);

    // si falla la ejecución de jsign guardar error en bodega/jsignpdf.log
    if ($ret != 0) {
        $out = implode(PHP_EOL, $out);
        error_log(date(DATE_ATOM)." ".basename(__FILE__)." ($ret) $radicado_p > $nurad: $out\n",3,"$ABSOL_PATH/bodega/jsignpdf.log");
    }

    if ($inf=="INFO  Finished: Creating of signature failed."){
			unset($answer);
			$answer=array();
                    saveMessage('error',"Clave de firma digital erronea");
                    die(json_encode($answer));
    }
    $linkarchivo_grabar = str_replace('.docx','.pdf',$linkarchivo_grabar);
    rename(str_replace(".docx","_signed.pdf",$nombreArchivo), $CONTENT_PATH .$linkarchivo_grabar);

    if(substr($numrad, -1) == 2) 
        $_numrad_aux[0]=$noRad;
    else     
        $_numrad_aux[0]=$numrad;

    $hist->insertarHistorico($_numrad_aux, $dependencia, $codusuario, $dependencia, $codusuario, 
    "Firmadada digitalmente el anexo No " . $nurad, 40);

}elseif ($_SESSION['apiFirmaDigital']=='certicamara') {
include ("include/apiCerticamara/PdfSign.php");
//ruta archivo a firmar
$fileToSignPath = str_replace(".docx",".pdf",$ABSOL_PATH."/bodega/tmp/workDir/$nombreArchivo");
//ruta para guardar el archivo firmado
$fileSignedPath = $ABSOL_PATH."/bodega/$pathFinal";
//ruta del certificado de firma
//$signP12Path = "$root/resources/certificate/cra.p12";
$signP12Path = $ABSOL_PATH.'/bodega/firmas/'.$usua_doc.'.p12';
//password del certificado de firma
//$signP12Password = "Password1";
$signP12Password = $clave;



//ruta del certificado de firma
//$signP12Path = $ABSOL_PATH.'/bodega/firmas/'.$usua_doc.'.p12';
//password del certificado de firma

$comand = "java -jar "
        . "\"$apiPath\" "
        . "\"$signType\" "
        . "\"$xmlConfigPath\" "
        . "\"$fileToSignPath\" "
        . "\"$fileSignedPath\" "
        . "\"$signP12Path\" "
        . "\"$signP12Password\" "
        . "\"$stamp\" "
        . "\"$stampP12Path\" "
        . "\"$stampP12Password\" "
        . "\"$signReason\" "
        . "\"$signLocation\" "
        . "\"$signImageAttrs\" "
		. "\"$ltv\" ";

//echo "Comando: $comand" . PHP_EOL;

try {
    $response = exec($comand, $returns);
    //echo "Response: ";
    //print_r($response);

    //echo "Returns: ";
     if (end($returns)!='success'){
	$answer='';
	saveMessage('error', utf8_decode(end($returns)));
	echo json_encode($answer);
	die;
    }   //print_r($returns);
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}
}

    if($tipo_radicado == 4 || $tipo_radicado == 5 || $tipo_radicado == 6 
        || $tipo_radicado == 7) { 
        $anexEstado = 2;
    } else {
        $anexEstado = 3;
    }

    $fecha = $db->conn->DBTimeStamp(time());
    $isql="update anexos set ANEX_NOMB_ARCHIVO='$nombreArchivoFinal', ANEX_TIPO=7, ANEX_ESTADO=$anexEstado, SGD_FECH_IMPRES=$fecha, ANEX_FECH_ENVIO=$fecha, SGD_DEVE_FECH = NULL, SGD_DEVE_CODIGO=NULL where RADI_NUME_SALIDA=$rad_salida";
$db->conn->Execute($isql);
    $isql="select radi_path from radicado where RADI_NUME_RADI=$rad_salida";
	$rs=$db->conn->GetAll($isql);
	$newRuta=str_replace("docx","pdf",$rs[0]["RADI_PATH"]);
    $isql="update radicado set radi_firma='1', radi_path='$newRuta' where RADI_NUME_RADI=$rad_salida";
$db->conn->Execute($isql);
}
    /******************************************************/

$linkArchivo=substr(str_replace("_","",str_replace('.pdf','',$nombreArchivoFinal)),1);
$linkArchivo=(!empty($linkArchivo))?$linkArchivo:$anexo;
    $scriptNewRad = "$('#codRadi$linkArchivo').text('** $rad_salida'); $('#iconoBorrar$linkArchivo').hide('slow');";
    saveMessage('success', "<script> function abrirArchivo(url){nombreventana='Documento'; window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');return; } $scriptNewRad</script> combinaci&oacute;n de correspondencia realizada <a class='vinculos' href=javascript:void(0) onclick=funlinkArchivo('".$linkArchivo."','.')> Ver Archivo </a>");

    $docx->borrar();
} elseif ($ext == "XML" || $ext == "xml") {
    //Se incluye la clase que maneja la combinacion masiva
    include("$ruta_raiz/include/AdminArchivosXML.class.php");
    define ('WORKDIR', './bodega/tmp/workDir/');
    define ('CACHE', WORKDIR . 'cacheODT/');

    //Se abre archivo de insumo para lectura de los datos
    $fp = fopen("$ruta_raiz/bodega/masiva/$archInsumo", 'r');
    if ($fp) {
        $contenidoCSV = file("$ruta_raiz/bodega/masiva/$archInsumo");
        fclose($fp);
    } else {
        saveMessage('error',"No hay acceso para crear el archivo $archInsumo ");
        die(json_encode($answer));
    }
    $accion = false;
    $xml    = new AdminArchivosXML();
    //Se carga el archivo odt Original
    $archivoACargar = str_replace('../', '', $linkarchivo);
    $xml->cargarXML("$archivoACargar", $nombreArchivo);
    $xml->setWorkDir(WORKDIR);
    $accion = $xml->abrirXML();
    $xml->cargarContenido();

    //Se recorre el archivo de insumo
    foreach ($contenidoCSV as $line_num => $line) {
        if ($line_num > 1) { //Desde la linea 2 hasta el final del archivo de insumo estan los datos de reemplazo
            $cadaLinea = explode("=", $line);
            //$cadaLinea[1] = str_replace("<", "'", $cadaLinea[1]);
            //$cadaLinea[1] = str_replace(">", "'", $cadaLinea[1]);
            $cadaVariable[$line_num - 2] = $cadaLinea[0];
            $cadaValor[$line_num - 2] = $cadaLinea[1];
        }
    }
    if ($vp == "s") {
        $linkarchivo_grabar = str_replace("bodega", "", $linkarchivotmp);
        $linkarchivo_grabar = str_replace("./", "", $linkarchivo_grabar);
    }

    $xml->setVariable($cadaVariable, $cadaValor);
    $xml->salvarCambios(null, $linkarchivo_grabar);
    $db->conn->CommitTrans();

    saveMessage('error',"<script> function abrirArchivo(url){nombreventana='Documento'; window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');return; }</script>
        Combinacion de Correspondencia Realizada <a class='vinculos' href=javascript:abrirArchivo('./bodega" . $linkarchivo_grabar . "')> Ver Archivo </a> ");
}

/** Este Procedimiento Asegura si se realizo la combinacion
 * Primero verifica que el archivo Generado Exista
 * Luego si no existe deja la Plantilla Original.
 **/

#Logica Notificacion
if($debeReasignarBorrador == true  && ($tipo_radicado == 4 || $tipo_radicado == 5 || $tipo_radicado == 6 
    || $tipo_radicado == 7)){

    #Se valida si hay alguien con el rol Pre Gestor Notificacion para asigarselo, En caso de que no este se envia a quien lo creo.

   $sqlInfoAdicionalReasignar = "select radi_depe_radi, radi_usua_radi from radicado where radi_nume_radi = " . $_numrad[0];
    $rsInfoAdicionalReasignar = $db->conn->Execute($sqlInfoAdicionalReasignar);
    while(!$rsInfoAdicionalReasignar->EOF){
        $depeDestino = $rsInfoAdicionalReasignar->fields["RADI_DEPE_RADI"];
        $usuDestino = $rsInfoAdicionalReasignar->fields["RADI_USUA_RADI"];
        $rsInfoAdicionalReasignar->MoveNext();
    }   



    $sqlPreGestor = "SELECT u.usua_codi, u.depe_codi FROM usuario u
            JOIN autm_membresias me on me.autu_id = u.id
            JOIN autg_grupos gr on gr.id = me.autg_id
            WHERE gr.nombre = 'Pre Gestor Notificación' AND
                  u.depe_codi = " . $depeDestino . " AND
                  gr.id != 2;   ";
    $rsSqlPreGestor = $db->conn->Execute($sqlPreGestor);
    while(!$rsSqlPreGestor->EOF){
        $depeDestino = $rsSqlPreGestor->fields["DEPE_CODI"];
        $usuDestino = $rsSqlPreGestor->fields["USUA_CODI"]; 
        $contadorPreGestor++;   
        $rsSqlPreGestor->MoveNext();
    }           

    $usCodDestino = $Tx ->reasignar($_numrad, $krd, $depeDestino, $dependencia *1, $usuDestino, 
         $codusuario, "si", "Para agregar expediente y enviar a Notificaciones", 9, 0);


}

$link         = trim($ABSOL_PATH) . "/bodega/" . $linkarchivo_grabar;
$link         = str_replace("//", "/", $link);
$tam          = filesize($link);
$linkarchivo_grabar         = str_replace("//", "/", $linkarchivo_grabar);
$linkFuente   = str_replace("d.", ".", $linkarchivo_grabar);
$linkF        = trim($ABSOL_PATH) . "/bodega/" . $linkFuente;
$tamFuente    = filesize($linkF);

saveMessage('success', " ".($tam) / 1000 . " kb");
$isql = "update RADICADO
set RADI_PATH='$linkFuente'
where RADI_NUME_RADI = $rad_salida";

$db->conn->query($isql);
if ($linkarchivo_grabar) {
    $filaGrabar = filedata($archUpdateFuente);
    $isql = "update anexos
    set ANEX_NOMB_ARCHIVO='" . basename($linkFuente) . "'
    where ANEX_NOMB_ARCHIVO like '%" . basename($link) . "%'";
    $db->conn->query($isql);
}
if ($tam >= 100) {
    saveMessage('success'," archivo Final Ok."); 
    die(json_encode($answer));
} else {
    saveMessage('error',"No se realizo Combinacion. Retornado Archivo Original.");
    die(json_encode($answer));
}

function filedata($path){
    // Vaciamos la caché de lectura de disco
    clearstatcache();
    // Comprobamos si el fichero existe
    $data["exists"] = is_file($path);
    // Comprobamos si el fichero es escribible
    $data["writable"] = is_writable($path);
    // Leemos los permisos del fichero
    $data["chmod"] = ($data["exists"] ? substr(sprintf("%o", fileperms($path)), -4) : FALSE);
    // Extraemos la extensión, un sólo paso
    $data["ext"] = substr(strrchr($path, "."), 1);
    // Primer paso de lectura de ruta
    $data["path"] = array_shift(explode("." . $data["ext"], $path));
    // Primer paso de lectura de nombre
    $data["name"] = array_pop(explode("/", $data["path"]));
    // Ajustamos nombre a FALSE si está vacio
    $data["name"] = ($data["name"] ? $data["name"] : FALSE);
    // Ajustamos la ruta a FALSE si está vacia
    $data["path"] = ($data["exists"] ? ($data["name"] ? realpath(array_shift(explode($data["name"], $data["path"]))) : realpath(array_shift(explode($data["ext"], $data["path"])))) : ($data["name"] ? array_shift(explode($data["name"], $data["path"])) : ($data["ext"] ? array_shift(explode($data["ext"], $data["path"])) : rtrim($data["path"], "/"))));
    // Ajustamos el nombre a FALSE si está vacio o a su valor en caso contrario
    $data["filename"] = (($data["name"] OR $data["ext"]) ? $data["name"] . ($data["ext"] ? "." : "") . $data["ext"] : FALSE);
    // Devolvemos los resultados
    return $data;
}



echo json_encode($answer);

?>
