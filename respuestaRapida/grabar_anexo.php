<?php
session_start();
/**
 * @author Cesar Augusto <aurigadl@gmail.com>
 * @author Jairo Losada  <jlosada@gmail.com>
 * @author Correlibre.org // Tomado de version orginal realizada por JL en SSPD, modificado.
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

 You should have received a copy of the GNU Afferoz General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$ruta_raiz = '..';
include_once($ruta_raiz.'/processConfig.php');
require_once("$ruta_raiz/include/tx/Historico.php");

define ('YEAR_INICIO', 0);
define ('YEAR_LENGTH', 4);
define ('RADI_LENGTH', $digitosDependencia);
define ('RADI_INICIO', 4);
define ('TIPO_PDF',    7);
define ('APP_NO_INTEGRADA',    0);

define('CIRC_INTERNA', 4);
define('CIRC_EXTERNA', 5);
define('RESOLUCION', 6);
define('AUTO', 7);



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
$CONTENT_PATH = $_SESSION["CONTENT_PATH"];

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
$anexo_record['anex_creador']     = "'$krd'";
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
$anexo_record['sgd_exp_numero']    = "'$expAnexo'";
$anexo_record['anex_tipo_final']   = 2;
$anexo_record['sgd_dir_mail']      = "'$mails'";
$anexo_record['anex_tipo_envio']   = (!empty($anex_tipo_envio)) ? "'$anex_tipo_envio'" : "0"; //Si es Notificacion este campo llega vacio.
$anexo_record['anex_adjuntos_rr']  = "'$anexosCodigo'";

if($flagUploadLog == "1" && (substr_count($archivo_grabar, '.') >= 4 || 
            substr_count($archivo_grabar_txt , '.') >= 4)) {
        $fileUploadLog = $CONTENT_PATH . '/uploadRespuesPdf.log';
        if(!is_file($fileUploadLog)){    
            $myfileUploadLog = fopen($fileUploadLog, "w");
            fclose($myfileUploadLog);
        }

            $out = "numero_anexo: " . $numero_anexo . " > " . "directorio: " . $directorio . " > " . "archivo_txt " . $archivo_txt . " > " . "archivo_final " . $archivo_final . " > " . "archivo_grabar " . $archivo_grabar . " > " . "archivo_grabar_txt " . $archivo_grabar_txt . " \n";
            error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $fileUploadLog);
}


if($editar == true && $idPlantilla == 0) {

   $sqlGetIdPlantilla = "select idPlantilla from anexos where anex_codigo = '$anexo'";
   $rsPlan = $db->conn->Execute($sqlGetIdPlantilla);
  if (!$rsPlan->EOF) {
    $idPlantilla = $rsPlan->fields["IDPLANTILLA"];
  }
}

$anexo_record['idPlantilla']  = "'$idPlantilla'";

$filtro = "anex_codigo = '$numero_anexo'";
$numero_campos = count($anexo_record);
$i = 0;
$sql_insert = 'INSERT into ' . $tabla_anexos . ' (';
$sql_update = 'UPDATE '  . $tabla_anexos . ' set ';
$sql_values = 'VALUES (';
foreach ($anexo_record as $campo => $valor) {
    $i++;

    $sql_insert .= ($i != $numero_campos) ? $campo . ', ' : $campo . ') ';
    $sql_update .= ($i != $numero_campos) ? $campo . '=' . $valor . ', ' : $campo . '=' . $valor . ' ';
    $sql_values .= ($i != $numero_campos) ? $valor . ', ' : $valor . ')';
}

$sql_insert .= $sql_values;
$sql_update .= 'WHERE ' . $filtro;


$result = ($editar == false)? $db->conn->Execute($sql_insert) : $db->conn->Execute($sql_update);



$ultimoDigito = substr($radPadre, -1);
if($ultimoDigito == CIRC_INTERNA || $ultimoDigito == CIRC_EXTERNA || 
        $ultimoDigito == RESOLUCION || $ultimoDigito == AUTO) {
    $esNotificaciones = true;
} else {
    $esNotificaciones = false;
}

//Si el radicado es devuelto y se modificó se pasa a estado 3 de nuevo
if($radicadoDevuelto  == 'true' && $esNotificaciones == false) {
    $sqlUpdateRadic = "update anexos set anex_estado = 3 
    where  anex_codigo = '$numero_anexo'";
    $db->conn->Execute($sqlUpdateRadic);
}

$enviar_editar = "index.php?PHPSESSID=" . session_id() .
    "&radicadopadre=" . $radPadre .
    "&krd=" . $krd .
    "&editar=" . $editar .
    "&anexo=" . $anexo;


// Si hay resultado en base de datos.


$recargar_anexos = './lista_anexos.php';

if ($result) {

    $id_anexo = $db->conn->getOne("SELECT id FROM anexos WHERE anex_codigo = '$numero_anexo'");
    $destinatarios = explode(',', $id_dre);

    foreach($destinatarios as $destinatario)
    {
        $envio = $_POST['envio_'.$destinatario];
        if(isset($_POST['validez_'.$destinatario]) && $_POST['validez_'.$destinatario]== 'on')
            $validez = 'true';
        else
            $validez = 'false';

        if($envio != '' && $esNotificaciones == false)
        {
            if($envio == 'E-mail' || $envio == 'Ambos')
            {
                $envio_email = $db->conn->Execute("INSERT INTO sgd_rad_envios (id_anexo, id_direccion, tipo, estado,certificado) VALUES ($id_anexo, $destinatario, 'E-mail', 1,'$validez')"); 
                
                $TX_COMENTARIO = "Anexo $numero_anexo medio de envio: $envio ".(($validez=='true')?' certificado':'');
                $Historico = new Historico($db);
                $Historico->insertarHistorico(array($radPadre), $dependencia , $codusuario, $dependencia, $codusuario,$TX_COMENTARIO,91);
               

            }

            if($envio == 'Físico' || $envio == 'Ambos')
            {
                $envio_fisico = $db->conn->Execute("INSERT INTO sgd_rad_envios (id_anexo, id_direccion, tipo, estado,certificado) VALUES ($id_anexo, $destinatario, 'Físico', 1,'$validez')");
                
                $TX_COMENTARIO = "Anexo $numero_anexo medio de envio: $envio ";
                $Historico = new Historico($db);
                $Historico->insertarHistorico(array($radPadre), $dependencia , $codusuario, $dependencia, $codusuario,$TX_COMENTARIO,91);

            }
        }
    }

    include './crear_pdf.php';
    echo '<br>
        <script>
javascript:window.parent.opener.$.fn.cargarPagina("' . $recargar_anexos . '","tabs-c");
window.parent.close();
            </script>';
  }
