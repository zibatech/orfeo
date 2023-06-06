<?php
$ruta_raiz = "../..";
$upload_dir = "$ruta_raiz/bodega/masiva/";
include_once "$ruta_raiz/include/tx/sanitize.php";
include_once "$ruta_raiz/include/tx/Tx.php";
include_once "$ruta_raiz/include/tx/Radicacion.php";
include_once "$ruta_raiz/include/tx/usuario.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";

class Masiva {
    private static $ruta_raiz = "../..";
    public static function radicar($radicado/*, $usuarios, $anexos, $plantilla*/)
    {
        $db = new ConnectionHandler(self::$ruta_raiz);
        $hist = new Historico($db);
        $classusua = new Usuario($db);
        $Tx = new Tx($db);
        $rad = new Radicacion($db);

        $requeridos = ['*CODIGO_DEPENDENCIA*', '*NIVEL_SEGURIDAD*', '*ASUNTO*'];
        $rad->radiTipoDeri = array_key_exists('*TIPO_DERI*', $radicado) ? trim($radicado['*TIPO_DERI*']) : '0';
        $rad->radiCuentai = array_key_exists('*CUENTA_I*', $radicado) ? trim($radicado['*CUENTA_I*']) : '';
        $rad->guia = array_key_exists('*GUIA*', $radicado) ? trim($radicado['*GUIA*']) : '';
        $rad->empTrans = array_key_exists('*EMP_TRANS*', $radicado) ? trim($radicado['*EMP_TRANS*']) : '';
        $rad->eespCodi = array_key_exists('*EESP_CODI*', $radicado) ? trim($radicado['*EESP_CODI*']) : '0';
        $rad->mrecCodi = array_key_exists('*MEDIO_RECEPCION*', $radicado) ? trim($radicado['*MEDIO_RECEPCION*']) : '0';
        $rad->radiFechOfic = array_key_exists('*FECHA_RADICACION*', $radicado) ? trim($radicado['*FECHA_RADICACION*']) : date('Y-m-d H:i:s');
        $rad->radiNumeDeri = array_key_exists('*RADICADO_PADRE*', $radicado) ? trim($radicado['*RADICADO_PADRE*']) : '';
        $rad->descAnex = array_key_exists('*DESCRIPCION_ANEXOS*', $radicado) ? trim($radicado['*DESCRIPCION_ANEXOS*']) : '';
        $rad->radiDepeRadi = array_key_exists('*CODIGO_DEPENDENCIA*', $radicado) ? trim($radicado['*CODIGO_DEPENDENCIA*']) : '900';
        $rad->trteCodi = array_key_exists('*TRTE_CODI*', $radicado) ? trim($radicado['*TRTE_CODI*']) : '0';
        $rad->nofolios = array_key_exists('*NO_FOLIOS*', $radicado) ? trim($radicado['*NO_FOLIOS*']) : '';
        $rad->noanexos = array_key_exists('*NO_ANEXOS*', $radicado) ? trim($radicado['*NO_ANEXOS*']) : '';
        $rad->sgdSpubCodigo = array_key_exists('*NIVEL_SEGURIDAD*', $radicado) ? trim($radicado['*NIVEL_SEGURIDAD*']) : '0';
        $rad->carpCodi = array_key_exists('*CARP_CODI*', $radicado) ? trim($radicado['*CARP_CODI*']) : '0';
        $rad->carPer = array_key_exists('*CARP_CODI*', $radicado) ? trim($radicado['*CARP_CODI*']) : '0';
        $rad->raAsun = substr(array_key_exists('*ASUNTO*', $radicado) ? trim($radicado['*ASUNTO*']) : '', 0, 349);
        $rad->tdocCodi = array_key_exists('*TDOC_CODI*', $radicado) ? trim($radicado['*TDOC_CODI*']) : '0';
        $rad->radiUsuaActu = $codusuario;
        $rad->radiDepeActu = $dependencia;

        $nurad = $rad->newRadicado($radicado['*TIPO*'], $radicado['*CODIGO_DEPENDENCIA*']);
        return $nurad;
    }
}
