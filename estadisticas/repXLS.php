<?php
session_start();

set_time_limit(440);
ini_set("memory_limit", "3072M");
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
//print_r($_POST);
$ruta_raiz = "..";
if (!$_SESSION['dependencia']) {
    $fallo['session'] = 'off';
    json_encode($fallo);
    die();//valida si tiene session
}

$krd = $_SESSION["krd"];
foreach ($_GET as $key => $valor) {
    ${$key} = $valor;
}

foreach ($_POST as $key => $valor) {
    ${$key} = $valor;
}

$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$tip3Nombre = $_SESSION["tip3Nombre"];
$tip3desc = $_SESSION["tip3desc"];
$tip3img = $_SESSION["tip3img"];
$usua_perm_estadistica = $_SESSION["usua_perm_estadistica"];
$ruta_raiz = '../';

include_once "$ruta_raiz/estadisticas/estadisticas.class.php";
$tupid = array('T' => 'Total', '1' => 'Finalizado', '2' => 'En trámite');
$tads = array('1' => 'Si', '0' => 'No');
//print_r($_POST);
$reportesClass = new estadisiticas($ruta_raiz);
$resp['error'] = '';
switch ($fn) {
    case 'dtrp1':
        //  print_r($_POST);
        //$tipoEstadistica = 'Radicados del usuario ' . $tupid[$tpbusq];
        if ($depe == 99999 && $tpbusq != 'T') {
            $depe = $btns;
            $tipoEstadistica = 'Radicados del usuario ' . $tupid[$tpbusq];
        }

        $datos = $reportesClass->dtrp1($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq, $tpRad);
        
        $nomR = 'dtrp1';
        break;
        case 'dtrp2':
            $tipoEstadistica = 'Medio de Recepción ';
            $datos= $reportesClass->dtrp2($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq, $tpRad);          
            $nomR = 'dtrp2';
        break;
        case 'dtrp3':
            $tipoEstadistica = 'Envios de Radicados ';
            $dat= $reportesClass->dtrp3($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq);
            $datos=$dat['datos'];
            $ENVIADOS=$dat['ENVIADOS'];
            $DEVUELTOS=$dat['DEVUELTOS'] ? $dat['DEVUELTOS']:0;
            $nomR = 'dtrp3';
            break;
    case 'dtrp4':

                $datos=$reportesClass->dtrp4($tit,$tpAds,$tpdoc,$serie,$subserie,$usu,$fini,$ffin,$tpbusq, $tpRad);
                $nomR = 'dtrp4';
                break;
    case 'dtrp6':

                $datos=$reportesClass->dtrp6($tpbusq,$tpAds,$tpdoc,$serie,$subserie,$usu,$fini,$ffin,$tpbusq, $tpRad);
                $nomR = 'dtrp6';
            	$tipoEstadistica = 'Radicados de la dependencia ' . $tit;
                break;
    case 'dtrp7':

                $datos=$reportesClass->dtrp7($depe,$tpAds,$tpdoc,$serie,$subserie,$usu,$fini,$ffin,$tpbusq, $tpRad);
		$nomR = 'dtrp7';
            	$tipoEstadistica = 'Radicados de la dependencia ' . $tit;
                break;
    case 'rp9':
        //  print_r($_POST);
        $datos = $reportesClass->rp9($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin);
//      $tipoEstadistica='Detalles '+$tupid[$tpbusq];
        $nomR = 'rp9';
        break;
    case 'dtrp9':
        //print_r( $_POST);
        $datos = $reportesClass->dtrp9($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq);
        $tipoEstadistica = 'Detalles ' . $tupid[$tpbusq];
        $nomR = 'dtrp9';
        break;
/// detalles reporte fa-rotate-180
    case 'dtrp10':
        $datos = $reportesClass->dtrp10($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq);
        $tipoEstadistica = 'Detalles ' . $tupid[$tpbusq];
        $titulo = 'Gestión de Radicados de Entrada';
        $nomR = 'dtrp10';
        break;
    case 'rp11':
        //  print_r($_POST);
        $datos = $reportesClass->rp11($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, 1);
        $tipoEstadistica = 'Detalles '+$tupid[$tpbusq];
        $nomR = 'rp11';
        break;
    case 'dtrp11':
        $datos = $reportesClass->dtrpCons($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq, 1);
        $titulo = 'Gestión de Radicados de Salida';
        $tipoEstadistica = 'Detalles '+$tupid[$tpbusq];
        $nomR = 'dtrp11';
        break;
    case 'rp12':
        //  print_r($_POST);
        $datos = $reportesClass->rp12($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, 2);
        $titulo = 'Gestión de Radicados de Memorandos';
        $tipoEstadistica = 'Detalles '+$tupid[$tpbusq];
        $nomR = 'rp12';
        break;
    case 'dtrp12':
        $datos = $reportesClass->dtrpCons($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq, 3);
        $titulo = 'Gestión de Radicados de Memorando';
        $tipoEstadistica = 'Detalles '+$tupid[$tpbusq];
        $nomR = 'dtrp12';
        break;
    default:
        $resp['error'] = 'FAIL';
        echo json_encode($resp);
        die();
        break;
}
//echo "dd";
//print_r($datos);
//var_dump($datos);
//die();
//Se incluye nuevo tratamiento de exportacion del resultado
include_once $ruta_raiz . '/include/tbs/tbs_class.php'; // Load the TinyButStrong template engine
include_once $ruta_raiz . '/include/tbs/tbs_plugin_opentbs.php'; // Load the OpenTBS plugin
// prevent from a PHP configuration problem when using mktime() and date()
if (version_compare(PHP_VERSION, '5.1.0') >= 0) {
    if (ini_get('date.timezone') == '') {
        date_default_timezone_set('UTC');
    }
}

// Initialize the TBS instance
$TBS = new clsTinyButStrong; // new instance of TBS
$TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load the OpenTBS plugin
//$TBS->PlugIn(OPENTBS_DEBUG_INFO );
//$data = array();
//$tt=explode('-',$titulo);
$nombrep = $titulo . ' (' . $tipoEstadistica . ')';
$inicio = $fini;
$fin = $ffin;
$dependencias = $depe == 99999 ? 'Todas' : $depeN;
$ascritas = $tads[$tpAds];
$Hoy = date('d/m/Y');
$template = $ruta_raiz . "/estadisticas/arch/$nomR.xlsx";
$TBS->LoadTemplate($template, OPENTBS_ALREADY_UTF8); // Also merge some [onload] automatic fields (depends of the type of document).
// ----------------------
// Debug mode of the demo
// ----------------------
if (isset($_POST['debug']) && ($_POST['debug'] == 'current')) {
    $TBS->Plugin(OPENTBS_DEBUG_XML_CURRENT, true);
}
// Display the intented XML of the current sub-file, and exit.
if (isset($_POST['debug']) && ($_POST['debug'] == 'info')) {
    $TBS->Plugin(OPENTBS_DEBUG_INFO, true);
}
// Display information about the document, and exit.
if (isset($_POST['debug']) && ($_POST['debug'] == 'show')) {
    $TBS->Plugin(OPENTBS_DEBUG_XML_SHOW);
}
// Tells TBS to display information when the document is merged. No exit.

$data = $datos;
/*$data[] = array('rank'=> 'A', 'firstname'=>'Sandra' , 'name'=>'Hill'      , 'number'=>'1523d', 'score'=>200, 'visits'=>15, 'email_1'=>'sh@tbs.com',  'email_2'=>'sandra@tbs.com',  'email_3'=>'s.hill@tbs.com');
$data[] = array('rank'=> 'A', 'firstname'=>'Roger'  , 'name'=>'Smith'     , 'number'=>'1234f', 'score'=>800, 'visits'=>33, 'email_1'=>'rs@tbs.com',  'email_2'=>'robert@tbs.com',  'email_3'=>'r.smith@tbs.com' );
$data[] = array('rank'=> 'B', 'firstname'=>'William', 'name'=>'Mac Dowell', 'number'=>'5491y', 'score'=>130, 'visits'=>16, 'email_1'=>'wmc@tbs.com', 'email_2'=>'william@tbs.com', 'email_3'=>'w.m.dowell@tbs.com' );
 */
//print($data);
// --------------------------------------------
// Merging and other operations on the template
// --------------------------------------------
// Merge data in the first sheet
$TBS->MergeBlock('a,b', $data);

// Merge cells titulos
//$TBS->MergeBlock('tit1,tit2', $dataT);
// -----------------
// Output the result
// -----------------
// Define the name of the output file
//$save_as = (isset($_POST['save_as']) && (trim($_POST['save_as'])!=='') && ($_SERVER['SERVER_NAME']=='localhost')) ? trim($_POST['save_as']) : '';
$save_as = $krd;
//$output_file_name = str_replace('.', '_'.date('Y-m-d').$save_as.'.', $template);
$output_file_name = 'Reporte_' . $dependencia . $tipoEstadistica . '_' . date('Y-m-d') . '_' . $save_as . ".xlsx";
//$base_path='tmp/'.$output_file_name;
$base_path = '../bodega/tmp/' . $output_file_name;
$radi_path_dest = $base_path;
$TBS->Show(OPENTBS_FILE, $radi_path_dest);

$resp['url'] = $radi_path_dest;
echo json_encode($resp);
//include_once $ruta_raiz . '/core/clases/file-class.php';
//$encrypt = new file();
//$linkarchivo = "../../../../core/vista/images.php?nombArchivo=" . $encrypt->encriptar($radi_path_dest);
