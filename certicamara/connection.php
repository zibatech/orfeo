<?
//session_start();
//if (!$_SESSION['dependencia']) header ("Location: $ruta_raiz/cerrar_session.php");
$ruta_raiz = "..";
include_once    ("$ruta_raiz/processConfig.php");
include_once    ($ruta_raiz."/include/db/ConnectionHandler.php");

try {
        $db = new ConnectionHandler($ruta_raiz);
    }catch (Exception $e) {
    echo "Ocurrió un error con la base de datos: " . $e->getMessage();
}



?>