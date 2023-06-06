<?php
session_start();
/**
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 *
 * @copyleft

 OrfeoGpl Models are the data definition of OrfeoGpl Information System
 Copyright (C) 2020

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Fou@copyrightndation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$ruta_raiz = '..';
include_once($ruta_raiz.'/processConfig.php');

define ('YEAR_INICIO', 0);
define ('YEAR_LENGTH', 4);
define ('RADI_LENGTH', $digitosDependencia);
define ('RADI_INICIO', 4);
define ('TIPO_PDF',    7);
define ('APP_NO_INTEGRADA',    0);


foreach ($_GET as $key => $valor)  ${$key} = $valor;
foreach ($_POST as $key => $valor) ${$key} = $valor;

$krd         = $_SESSION['krd'];
$dependencia = $_SESSION['dependencia'];
$usua_doc    = $_SESSION['usua_doc'];
$codusuario  = $_SESSION['codusuario'];
$tpNumRad    = $_SESSION['tpNumRad'];
$tpPerRad    = $_SESSION['tpPerRad'];
$tpDescRad   = $_SESSION['tpDescRad'];
$tip3Nombre  = $_SESSION['tip3Nombre'];
$dependencia = $_SESSION['dependencia'];
$ln          = $_SESSION['digitosDependencia'];
$lnr         = 11 + $ln;

if (isset($radPadre)) {
    $directorio_ano  = substr($radPadre, YEAR_INICIO, YEAR_LENGTH);
    $depe_radi_padre = ltrim(substr($radPadre, RADI_INICIO, RADI_LENGTH), '0');
} else {
    var_dump('Error');
    exit();
}

/** * Retorna la cantidad de bytes de una expresion como 7M, 4G u 8K.
 *
 * @param char $var
 * @return numeric
 */
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

$fechaHoy = Date('Y-m-d');

include_once($ruta_raiz . '/class_control/anexo.php');
include_once($ruta_raiz . '/class_control/anex_tipo.php');

if (!$db)
    $db = new ConnectionHandler($ruta_raiz);

$sqlFechaHoy = $db->sysdate();
$anex = new Anexo($db);
$anexTip = new Anex_tipo($db);

if (!$tpradic)
    $tpradic = 'null';

$codigo = (empty($anexo))? null : $anexo;

$nuevo = ($codigo)? 'no' : 'si';
// Si es nuevo busque el ultimo anexo para asignar el codigo de radicacion


if ($editar == false) {
    $auxnumero = $anex->obtenerMaximoNumeroAnexo($radPadre);

    // Busca el ultimo radicado
    do {
        $auxnumero += 1;
        $codigo = trim(str_pad($auxnumero, 5, "0", STR_PAD_LEFT));
    } while ($anex->existeAnexo($codigo));
} else {

    $bien      = true;

    $auxnumero = substr($anexo, -4, 5);
    $codigo = trim(str_pad($auxnumero, 5, "0", STR_PAD_LEFT));

    $numero_anexo = $anexo;
}
$buscar_anexo = "SELECT a.radi_nume_salida,
    a.anex_nomb_archivo,
    a.ANEX_ADJUNTOS_RR,
    (select radi_fech_radi from radicado r where r.radi_nume_radi=a.radi_nume_salida) radi_fech_salida
    FROM anexos a
    WHERE a.anex_codigo = '$anexo'";

$anexo_result = $db->conn->Execute($buscar_anexo);

if (!$anexo_result->EOF) {
    $radicado_salida= $anexo_result->fields['RADI_NUME_SALIDA'];
}

$anex_salida = ($radicado_salida) ? 1 : 0;

$bien = 'si';

if ($bien and $tipo) {
    $anexTip->anex_tipo_codigo($tipo);
    $ext               = $anexTip->get_anex_tipo_ext();
    $ext               = strtolower($ext);
    $auxnumero         = str_pad($auxnumero, 5, "0", STR_PAD_LEFT);
    $archivo           = trim($numrad . "_" . $auxnumero . "." . $ext);
    $archivoconversion = trim("1") . trim(trim($numrad) . "_" . trim($auxnumero) . "." . trim($ext));
}

$numero_anexo = $radPadre . $codigo;

if (!$radicado_rem) $radicado_rem = 1;

$directorio     = '../bodega/' . $directorio_ano . '/' . $depe_radi_padre . '/docs/';

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

if ($write_result) {
    include './crear_pdf.php';
    echo "<html><head><style>body, html {width: 100%; height: 100%; margin: 0; padding: 0}</style></head><body><iframe src=\"$archivo_grabar\" style=\"height:calc(100% - 4px);width:calc(100% - 4px)\"></iframe></html></body>";    
} else {
        echo 'Ha ocurrido un error previsualizando el documento. Use el boton "Grabar como Anexo" para guardarlo y posteriormente use el visor de documentos para visualizarlo.';
}