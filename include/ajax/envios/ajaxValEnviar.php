<?
/**
 * Busca un dato de una remitente y/o destinatario segun parametro de entrada en la tabla sgd_dir_drecciones.
 * @
 * @var uidDir  Busca por identificador unico de Tabla
 * @var nombre  Busca por Nombre
 *
 */
    //variables enviadas desde js/ajaxSessionRads.js
$radicadoEnviar = $_POST["radicadoEnviar"];
session_start();
$ruta_raiz = "../../..";
include_once $ruta_raiz."/include/tx/sanitize.php";
if (!$_SESSION['dependencia'] || $_GET['close'] ){
  die( "Debe Reiniciar Session o enviar Token de autorización....");
}
include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
$db   = new ConnectionHandler("$ruta_raiz");
//$db->conn->debug = true;
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
include $ruta_raiz . "/include/tx/Envio.php";
$arrRadicados[] = $radicadoEnviar;
$envio = new Envio($db);
echo json_encode($envio->getRestriccionEnvio($arrRadicados));
?>