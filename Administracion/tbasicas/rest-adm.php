<?php
session_start();

$ruta_raiz = "../../";
if (!$_SESSION['dependencia']) {
    $fallo['session'] = 'off';
    json_encode($fallo);
    die();
}

$krd = $_SESSION["krd"];
foreach ($_GET as $key => $valor) {
    ${$key} = $valor;
}

foreach ($_POST as $key => $valor) {
    ${$key} = $valor;
}

$nomcarpeta = isset($_GET["carpeta"]) ? $_GET["carpeta"] : '';
$tipo_carpt = isset($_GET["tipo_carpt"]) ? $_GET["tipo_carpt"] : '';
$orderNo = isset($_GET["orderNo"]) ? $_GET["orderNo"] : '';
$orderTipo = isset($_GET["orderTipo"]) ? $_GET["orderTipo"] : '';
$tipoEstadistica = isset($_REQUEST["tipoEstadistica"]) ? $_REQUEST["tipoEstadistica"] : '';
$genDetalle = isset($_GET["genDetalle"]) ? $_GET["genDetalle"] : '';
$dependencia_busq = isset($_GET["dependencia_busq"]) ? $_GET["dependencia_busq"] : '';
$fecha_ini = isset($_GET["fecha_ini"]) ? $_GET["fecha_ini"] : '';
$fecha_fin = isset($_GET["fecha_fin"]) ? $_GET["fecha_fin"] : '';
$codus = isset($_GET["codus"]) ? $_GET["codus"] : '';
$tipoRadicado = isset($_GET["tipoRadicado"]) ? $_GET["tipoRadicado"] : '';

$codUs = isset($_GET["codUs"]) ? $_GET["codUs"] : '';
$fecSel = isset($_GET["fecSel"]) ? $_GET["fecSel"] : '';
$genDetalle = isset($_GET["genDetalle"]) ? $_GET["genDetalle"] : '';
$generarOrfeo = isset($_GET["generarOrfeo"]) ? $_GET["generarOrfeo"] : '';
$dependencia_busqOri = isset($_GET["dependencia_busqOri"]) ? $_GET["dependencia_busqOri"] : '';

$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$tip3Nombre = $_SESSION["tip3Nombre"];
$tip3desc = $_SESSION["tip3desc"];
$tip3img = $_SESSION["tip3img"];
$usua_perm_estadistica = $_SESSION["usua_perm_estadistica"];
include_once "$ruta_raiz/include/db/ConnectionHandler.php";

$db = new ConnectionHandler($ruta_raiz);
$db->conn->debug =true;
$datos = array();
switch ($fn) {
    case 'add':
        $sqlInsert = "insert into sgd_noh_nohabiles  (noh_fecha) values (" . $db->conn->DBDate($fecha) . ")";
        $ok = $db->conn->Execute($sqlInsert);
        $dato['status'] = $ok ? 'ok' : 'fail';

        break;
    case 'del':
        //$tmp_val = empty($_POST[noh_fecha]) ? "" : implode("','", $noh_fecha);
        $sqlBorra = "delete from sgd_noh_nohabiles where noh_fecha = ('$fecha')";
        $ok = $db->conn->Execute($sqlBorra);
        $dato['status'] = $ok ? 'ok' : 'fail';
        break;
    case 'addFmas':
       // echo "ss";
       //$fechas = explode(';', $_POST['datos']);
       $fechas =str_replace(';',"'),('" ,trim($_POST['datos'],';'));
        //print_r($fechas );
        //for ($index = 0; $index < count($fechas); $index++) {
            $sqlInsert = "insert into sgd_noh_nohabiles  (noh_fecha) values ('" .$fechas . "')";
//            $sqlBorra = "delete from sgd_noh_nohabiles where noh_fecha in ({$fechas[$i]})";
            $ok = $db->conn->Execute($sqlInsert);
       // }
        $dato['status'] = $ok ? 'ok' : 'fail';
        break;
    case 'delFmas':
        $fechas =str_replace(';',"','" ,trim($_POST['datos'],';'));
            $sqlBorra = "delete from sgd_noh_nohabiles where noh_fecha in ('{$fechas}')";
            $ok = $db->conn->Execute($sqlBorra);

        $dato['status'] = $ok ? 'ok' : 'fail';
        break;
    default:
        $db->conn->Disconnect();
        die();
        break;
}
$db->conn->Disconnect();
$resp['data'] = $datos;
echo json_encode($resp);
