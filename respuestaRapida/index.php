<?php
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

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

session_start();
$ruta_raiz = "..";

if (!empty($_GET['a'])) {
    include("$ruta_raiz/include/utils/Utils.php");
    $res_id = Utils::auth($_SESSION['krd'], $_POST['pass']);
    if ($res_id===true){
        $err = null;
    } else {
        $err = $res_id ?: 'error';
    }
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode(['error'=>$err]);
    exit;
}

define('RAD_ENTRADA', '2');
define('ADODB_ASSOC_CASE', 1);
define('COLOMBIA', 170);
define('AMERICA', 1);
define('MEMORANDO', 3);
define('CIRC_INTERNA', 4);
define('CIRC_EXTERNA', 5);
define('RESOLUCION', 6);
define('AUTO', 7);

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
$editar = ($editar == 'true')? true : false;

if (!$_SESSION['dependencia'])
  header ("Location: ".$ruta_raiz."/cerrar_session.php");

$ruta_libs = "../respuestaRapida/";
require "$ruta_raiz/processConfig.php";


// Variable que almacena los tipos de radicados que se encuentran en DB
$tipos_radicados = array();

require (SMARTY_DIR.'Smarty.class.php');

//require_once 'libs/htmlpurifier/HTMLPurifier.auto.php';

$mostrar_error = $_GET['error_radicacion'];

//formato para fecha en documentos
function fechaFormateada($FechaStamp) {
  $ano      = date('Y', $FechaStamp); //<-- Ano
  $mes      = date('m', $FechaStamp); //<-- número de mes (01-31)
  $dia      = date('d', $FechaStamp); //<-- Día del mes (1-31)
  $dialetra = date('w', $FechaStamp); //Día de la semana(0-7)

  $arreglo_dias = array();
  $arreglo_dias[] = 'domingo';
  $arreglo_dias[] = 'lunes';
  $arreglo_dias[] = 'martes';
  $arreglo_dias[] = 'miercoles';
  $arreglo_dias[] = 'jueves';
  $arreglo_dias[] = 'viernes';
  $arreglo_dias[] = 'sabado';

  $dialetra = (isset($arreglo_dias[$dialetra]))? $arreglo_dias[$dialetra] : null;

  $arreglo_meses['01'] = 'enero';
  $arreglo_meses['02'] = 'febrero';
  $arreglo_meses['03'] = 'marzo';
  $arreglo_meses['04'] = 'abril';
  $arreglo_meses['05'] = 'mayo';
  $arreglo_meses['06'] = 'junio';
  $arreglo_meses['07'] = 'julio';
  $arreglo_meses['08'] = 'agosto';
  $arreglo_meses['09'] = 'septiembre';
  $arreglo_meses['10'] = 'octubre';
  $arreglo_meses['11'] = 'noviembre';
  $arreglo_meses['12'] = 'diciembre';

  $mesletra = (isset($arreglo_meses[$mes]))? $arreglo_meses[$mes] : null;

  return htmlentities("$dialetra, $dia de $mesletra de $ano");
}

$smarty = new Smarty;
$smarty->template_dir = './templates';
$smarty->compile_dir = '../bodega/tmp/';
$smarty->config_dir = './configs/';

$smarty->left_delimiter = '<!--{';
$smarty->right_delimiter = '}-->';

function byteSize($bytes) {
  $size = $bytes / 1024;
  if($size < 1024){
    $size = number_format($size, 2);
    $size .= ' KB';
  }
  else
  {
    if($size / 1024 < 1024)
    {
      $size = number_format($size / 1024, 2);
      $size .= ' MB';
    }
    else if ($size / 1024 / 1024 < 1024)
    {
      $size = number_format($size / 1024 / 1024, 2);
      $size .= ' GB';
    }
  }
  return $size;
}

$krd = (isset($_SESSION["krd"]))? $_SESSION["krd"] : '';

if (isset($_GET["radicadopadre"])){
  $radicado = $_GET["radicadopadre"];
  //Necesario para procesar plantillas
  $_SESSION["radicaResLinea"] = $radicado;
}else{
  $radicado = $_SESSION["radicaResLinea"];
}

include_once ($ruta_raiz."/include/db/ConnectionHandler.php");
include_once ($ruta_raiz."/include/tx/usuario.php");
include_once ($ruta_raiz."/class_control/anexo.php");

$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$usrsRad = new Usuario($db);
//$arrUsuarios = $usrsRad->usuariosDelRadicado($radicado);

$anexosRad = new Anexo($db);
$arrAnexos = $anexosRad->anexosRadicado($radicado);

if ($excluidosRR) $excluidos= "and sgd_trad_codigo not in ($excluidosRR)";

$sql_tipo_rad = "SELECT sgd_trad_codigo,
                        sgd_trad_descr
                      FROM SGD_TRAD_TIPORAD
                      where sgd_trad_codigo <>" . RAD_ENTRADA."$excluidos";
$rs_tipo_rad  = $db->conn->Execute($sql_tipo_rad);

while (!$rs_tipo_rad->EOF) {
  $tipos_radicados[$rs_tipo_rad->fields["SGD_TRAD_CODIGO"]] = $rs_tipo_rad->fields["SGD_TRAD_DESCR"];
  $rs_tipo_rad->MoveNext();
}

$isql   = " SELECT SGD_TRAD_CODIGO
            FROM RADICADO
            WHERE RADI_NUME_RADI = '$radicado' ";
$rs = $db->conn->Execute($isql);

if ($rs){
  $radicado_sgd_trad_codigo = $rs->fields["SGD_TRAD_CODIGO"];
}

if ($radicado_sgd_trad_codigo == CIRC_INTERNA || $radicado_sgd_trad_codigo == CIRC_EXTERNA ||
    $radicado_sgd_trad_codigo == RESOLUCION || $radicado_sgd_trad_codigo == AUTO) {
  $esNotificacion = true;
} else {
  $esNotificacion = false;
}

if ($radicado_sgd_trad_codigo == CIRC_INTERNA || $radicado_sgd_trad_codigo == CIRC_EXTERNA) {
  $esNotificacionCircular = true;
} else {
  $esNotificacionCircular = false;
}

if($esNotificacion) {
  $sqlParaRadicar = "SELECT 
                      count(id)
                    FROM 
                        anexos where anex_radi_nume = " . $radicado . " and radi_nume_salida is not null";
  $rsParaRadicar = $db->conn->Execute($sqlParaRadicar); 
  if ($rsParaRadicar){
     $cantidadRadicado = $rsParaRadicar->fields["COUNT"];
    if($cantidadRadicado > 0)
      $puedeRadicar = false;
    else
      $puedeRadicar = true;
  }    
                   
} else {
  $puedeRadicar = true;
}

$verrad         = '';
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
$encabezado   = session_name()."=".session_id();
$encabezado .= "&krd= $krd";

$isql   = "SELECT USUA_EMAIL,
                USUA_EMAIL_1,
                USUA_EMAIL_2,
                DEPE_CODI,
                USUA_CODI,
                USUA_NOMB,
                USUA_LOGIN,
                USUA_DOC
            FROM USUARIO
            WHERE USUA_LOGIN ='$krd' ";

$rs   = $db->conn->Execute($isql);

if (!$rs){
  exit('ERROR, datos invalidos');
}


$emails = array();
while (!$rs->EOF) {

  $emails[] = trim(strtolower($rs->fields["USUA_EMAIL"]));
  $temEmail = trim(strtolower($rs->fields["USUA_EMAIL_1"]));
  $temEmai  = trim(strtolower($rs->fields["USUA_EMAIL_2"]));

  //buscamos el correo que inicie con web para colocarlo como primero
  if(substr($temEmail, 0, 3)== 'web'){
    array_unshift($emails, $temEmail);
  }else{
    $emails[] = $temEmail;
  }

  if(substr($temEmai, 0, 3)== 'web'){
    array_unshift($emails, $temEmai);
  }else{
    $emails[] = $temEmai;
  }

  $usuacodi  = $rs->fields["USUA_CODI"];
  $depecodi  = $rs->fields["DEPE_CODI"];
  $usuanomb  = $rs->fields["USUA_NOMB"];
  $usualog   = $rs->fields["USUA_LOGIN"];
  $codigoCiu = $rs->fields["USUA_DOC"];
  $rs->MoveNext();
}
//Eliminamos los campos vacios en el array
$emails   =  array_filter($emails);

# informacion remitente
$name  = "";
$email = "";

$isql  = "SELECT D.*
            FROM SGD_DIR_DRECCIONES D
            WHERE D.RADI_NUME_RADI = $radicado";
$rs = $db->conn->Execute($isql);

$name       = $rs->fields["SGD_DIR_NOMREMDES"];
$email      = $rs->fields["SGD_DIR_MAIL"];
$municicodi = $rs->fields["MUNI_CODI"];
$depecodi2  = $rs->fields["DPTO_CODI"];

$name     = strtoupper($name);
$depcNomb = strtoupper($depcNomb);
$fecha1   = time();
$fecha    = ucfirst(fechaFormateada($fecha1));


/*************************************Expediente******************************************************/
$isqlExp = "select sgd_exp_numero from sgd_exp_expediente where radi_nume_radi = $radicado";
$rsExp = $db->conn->Execute($isqlExp);


while(!$rsExp->EOF){
  $expedientes= $rsExp->fields['SGD_EXP_NUMERO'];
  //$rsExp->MoveNext();
  $rsExp->MoveNext();
}

/*************************************Fin expediente******************************************************/
$estadoAnexo = -1;
if ($editar) {
  $buscar_anexo = "SELECT a.sgd_trad_codigo, a.radi_nume_salida, a.sgd_exp_numero,
                      a.anex_nomb_archivo,
                      a.ANEX_ADJUNTOS_RR,
                      a.ANEX_TIPO_ENVIO,
                      a.ANEX_ESTADO,
                      (select radi_fech_radi from radicado r where r.radi_nume_radi=a.radi_nume_salida) radi_fech_salida
                      FROM anexos a
                      WHERE a.anex_codigo = '$anexo' ";

  $anexo_result = $db->conn->Execute($buscar_anexo);
  $ano = substr($radicado, 0, 4);
  $dependencia = ltrim(substr($radicado, 4 , $digitosDependencia), '0');
  if (!$anexo_result->EOF) {
    $nombre_archivo  = $anexo_result->fields['ANEX_NOMB_ARCHIVO'];
    $numero_radicado = $anexo_result->fields['RADI_NUME_SALIDA'];
    $fecha_radicado  = $anexo_result->fields['RADI_FECH_SALIDA'];
    $tipo_radicado   = $anexo_result->fields['SGD_TRAD_CODIGO'];
    $estadoAnexo   = $anexo_result->fields['ANEX_ESTADO'];
    $anex_adjuntos   = trim($anexo_result->fields['ANEX_ADJUNTOS_RR']);
    $anex_tipo_envio = trim($anexo_result->fields['ANEX_TIPO_ENVIO']);
    $$expAnexo            = $anexo_result->fields['SGD_EXP_NUMERO'];

    $guardar_radicado = (isset($numero_radicado))? true : false;

    $nombre_archivo = rtrim($nombre_archivo, '.pdf');
    $nombre_archivo .= '.txt';
    $ruta_completa = '../bodega/' . $ano . '/' . $dependencia . '/docs/' . $nombre_archivo;

    $asunto = file_get_contents($ruta_completa, true);

    // Si error al leer el contenido del archivo finalice el programa
    if (!$asunto) {
      exit('Error al leer el anexos o radicado, por favor verificar con el administrador del sistema si existe en sistema');
    }
  } else {
    exit('Error el radicado no tiene un archivo asociado');
  }


   if($numero_radicado) $rad_salida=$numero_radicado;
   if($fecha_radicado) $fecha_rad_salida=substr($fecha_radicado,0,16);



} else {

  $asunto = "";
}

if ($rad_salida!='' ||$rad_salida!=null ) {
  $arrUsuarios = $usrsRad->usuariosDelRadicado($rad_salida);
}else{
  $arrUsuarios = $usrsRad->usuariosDelRadicado($radicado);
}

$sqlD = " SELECT  a.MUNI_NOMB,
                  b.DPTO_NOMB
          FROM    MUNICIPIO a, DEPARTAMENTO b
          WHERE (a.ID_PAIS = " . COLOMBIA .") AND
                (a.ID_CONT = " . AMERICA . ") AND
                (a.DPTO_CODI = $depecodi2) AND
                (a.MUNI_CODI = $municicodi) AND
                (a.DPTO_CODI=b.DPTO_CODI) AND
                (a.ID_PAIS=b.ID_PAIS) AND
                (a.ID_CONT=b.ID_CONT)";

$descripMuniDep = $db->conn->Execute($sqlD);
$depcNomb       = $descripMuniDep->fields["MUNI_NOMB"];
$muniNomb       = $descripMuniDep->fields["DPTO_NOMB"];

$destinatario   = trim($email);

$sql1     = " select
  anex_tipo_ext as ext
  from
  anexos_tipo";

$exte = $db->conn->Execute($sql1);

while(!$exte->EOF){
  $val  = $exte->fields["EXT"];
  $extn .= empty($extn)? $val : "|".$val;
  //arreglo para validar la extension
  $exte->MoveNext();
};

$sqlSubstDesc =  $db->conn->substr."(anex_desc, 0, 50)";

//adjuntar  la imagen html al radicado
$desti = "SELECT RADI_PATH
          FROM RADICADO
          WHERE RADI_NUME_RADI = $radicado";

$rssPatth    = $db->conn->Execute($desti);
$pathPadre   = $rssPatth->fields["RADI_PATH"];

$post        = strpos(strtolower($pathPadre),'bodega');
$pathPadre   = substr($pathPadre,$post + 6);
$rutaPadre   = trim($ruta_raiz.'/bodega/'.$pathPadre);

if(is_file($rutaPadre)  and substr($rutaPadre, -4) == "html" )
{
  $gestor     = fopen($rutaPadre, "r");
  $archtml    = fread($gestor, filesize($rutaPadre));

  $archtml    = preg_replace('/<img (.+?)>/', ' ',$archtml);
  $archtml    = preg_replace('COLOR: red;', ' ',$archtml);
  $config     = HTMLPurifier_Config::createDefault();
  $purifier   = new HTMLPurifier();
//   $clean_html = $purifier->purify($archtml)


  $asunto .= "<br><br><hr><br>
  $clean_html" ;
}



//Consulta para validar si el usuario pertenence al grupo de administrador
$sqlValidarSiAdministrador = "select u.id, u.usua_login, gr.id as grupo_id, gr.nombre
    FROM usuario u
    JOIN autm_membresias me on me.autu_id = u.id
    JOIN autg_grupos gr on gr.id = me.autg_id
    WHERE 
    u.id = " . $_SESSION["usua_id"] . " and
    gr.id =1";        

$validarSA = $db->conn->getOne($sqlValidarSiAdministrador);

//Es administrador entonces puede ver todas las opciones
if($validarSA != NULL) {
  $permPlnatill[] = array("nombre" => "Notificaciones" , "codigo" => 4);
  $permPlnatill[] = array("nombre" => "Generales"   , "codigo" => 3 );
  $permPlnatill[] = array("nombre" => "Dependencia" , "codigo" => 2);
  $permPlnatill[]   = array("nombre" => "Personales"  , "codigo" => 1);
} else {

  //Consulta para validar el permiso de agregar plantillas a general
  $sqlValidarPerPlanGen = "select u.id, u.usua_login, gr.id as grupo_id, gr.nombre, ap.nombre, ap.crud
      FROM usuario u
      JOIN autm_membresias me on me.autu_id = u.id
      JOIN autg_grupos gr on gr.id = me.autg_id
      JOIN autr_restric_grupo rg on rg.autg_id = gr.id
      JOIN autp_permisos ap on ap.id = rg.autp_id
      WHERE u.id = " . $_SESSION["usua_id"] . " 
          AND ap.nombre = 'PERM_PLANTILLA_GG'
          AND ap.crud = 3";

  $validarPGG = $db->conn->getOne($sqlValidarPerPlanGen);

  //Consulta para validar el permiso de agregar plantillas a dependencia
  $sqlValidarPerPlanDep = "select u.id, u.usua_login, gr.id as grupo_id, gr.nombre, ap.nombre, ap.crud
      FROM usuario u
      JOIN autm_membresias me on me.autu_id = u.id
      JOIN autg_grupos gr on gr.id = me.autg_id
      JOIN autr_restric_grupo rg on rg.autg_id = gr.id
      JOIN autp_permisos ap on ap.id = rg.autp_id
      WHERE u.id = " . $_SESSION["usua_id"] . " 
          AND ap.nombre = 'PERM_PLANTILLA_DEPENDENCIA'
          AND ap.crud = 3
          AND (gr.nombre = 'Administrador' OR gr.nombre = 'Admin Plantillas');";

  $validarPPD = $db->conn->getOne($sqlValidarPerPlanDep);



  $permPlnatill[]   = array("nombre" => "Personales"  , "codigo" => 1);
  if($validarPPD != NULL){
    $permPlnatill[] = array("nombre" => "Dependencia" , "codigo" => 2);
  }    
  if($validarPGG != NULL){
    $permPlnatill[] = array("nombre" => "Generales"   , "codigo" => 3 );
  }  
}

//Plantillas guardadas
//$perPlanilla = 4;//$_SESSION["usua_perm_resplantilla"];

/*if($perPlanilla > 3 && $esNotificacion){
  $permPlnatill[] = array("nombre" => "Notificaciones" , "codigo" => 4);
}


if($perPlanilla > 2 && !$esNotificacion  && $validarPGG != NULL){
  $permPlnatill[] = array("nombre" => "Generales"   , "codigo" => 3 );
}

if ($perPlanilla > 1 && !$esNotificacion) {
  $permPlnatill[] = array("nombre" => "Dependencia" , "codigo" => 2);
}

$permPlnatill[]   = array("nombre" => "Personales"  , "codigo" => 1);*/

//Si tiene algunos de estos permisos podra ver la plantilla de notificaciones

/*if (($validarSA != NULL || ($_SESSION['USUA_PRAD_TP7']  >= 3 || $_SESSION['USUA_PRAD_TP6']  >= 3 || $_SESSION['USUA_PRAD_TP5']  >= 3 || $_SESSION['USUA_PRAD_TP4']  >= 3)) && $esNotificacion){*/

if ($esNotificacion){  

    if($radicado_sgd_trad_codigo == RESOLUCION) {
        $sql21       ="SELECT ID, PLAN_PLANTILLA, PLAN_NOMBRE, PLAN_FECHA, DEPE_CODI, USUA_CODI, PLAN_TIPO
                        FROM SGD_PLAN_PLANTILLAS WHERE PLAN_TIPO = 4 AND ID = 100000";      
    } else if ($radicado_sgd_trad_codigo == CIRC_INTERNA) {
        $sql21       ="SELECT ID, PLAN_PLANTILLA, PLAN_NOMBRE, PLAN_FECHA, DEPE_CODI, USUA_CODI, PLAN_TIPO
                        FROM SGD_PLAN_PLANTILLAS WHERE PLAN_TIPO = 4 AND ID = 100010";      
    } else if ($radicado_sgd_trad_codigo == CIRC_EXTERNA) {
        $sql21       ="SELECT ID, PLAN_PLANTILLA, PLAN_NOMBRE, PLAN_FECHA, DEPE_CODI, USUA_CODI, PLAN_TIPO
                        FROM SGD_PLAN_PLANTILLAS WHERE PLAN_TIPO = 4 AND ID = 100011";      
    } else if ($radicado_sgd_trad_codigo == AUTO) {
        $sql21       ="SELECT ID, PLAN_PLANTILLA, PLAN_NOMBRE, PLAN_FECHA, DEPE_CODI, USUA_CODI, PLAN_TIPO
                        FROM SGD_PLAN_PLANTILLAS WHERE PLAN_TIPO = 4 AND ((ID >= 100001 AND ID <= 100009) OR ID = 100016 OR ID = 100017)";      
    }   
    $plant = $db->conn->Execute($sql21);


    while(!$plant->EOF){

      $grupDepende    = array();
      $grupGeneral    = array();
      $grupPersonal   = array();

      $plan_id        = $plant->fields["ID"];
      $plan_nombre    = $plant->fields["PLAN_NOMBRE"];
      $plan_fecha     = $plant->fields["PLAN_FECHA"];
      $plan_tipo      = $plant->fields["PLAN_TIPO"];
      $plan_depend    = $plant->fields["DEPE_CODI"];
      $plan_usurio    = $plant->fields["USUA_CODI"];
      $plan_plantilla = $plant->fields["PLAN_PLANTILLA"];

      $plan_plantilla = str_replace('"', "'", $plan_plantilla);
      $plan_plantilla = str_replace("\r", '' , $plan_plantilla);
      $plan_plantilla = str_replace("\n", '' , $plan_plantilla);
      $plan_plantilla = str_replace("\t", '' , $plan_plantilla);

      include "combinaCampos.php";

      $carpetas['Notificaciones'][] = array("id"=> $plan_id,
                                        "nombre"=> $plan_nombre,
                                        "ruta"=> $plan_plantilla,
                                        "show"=> true);

      $plant->MoveNext();
      };

}

$sql21       ="SELECT
                ID,
                PLAN_PLANTILLA,
                PLAN_NOMBRE,
                PLAN_FECHA,
                DEPE_CODI,
                USUA_CODI,
                PLAN_TIPO
              FROM
                SGD_PLAN_PLANTILLAS
              WHERE
                PLAN_TIPO = 3";

$plant = $db->conn->Execute($sql21);


$sqlGeneralesCount = 0;
while(!$plant->EOF){

  $grupDepende    = array();
  $grupGeneral    = array();
  $grupPersonal   = array();

  $plan_id        = $plant->fields["ID"];
  $plan_nombre    = $plant->fields["PLAN_NOMBRE"];
  $plan_fecha     = $plant->fields["PLAN_FECHA"];
  $plan_tipo      = $plant->fields["PLAN_TIPO"];
  $plan_depend    = $plant->fields["DEPE_CODI"];
  $plan_usurio    = $plant->fields["USUA_CODI"];
  $plan_plantilla = $plant->fields["PLAN_PLANTILLA"];

  $plan_plantilla = str_replace('"', "'", $plan_plantilla);
  $plan_plantilla = str_replace("\r", '' , $plan_plantilla);
  $plan_plantilla = str_replace("\n", '' , $plan_plantilla);
  $plan_plantilla = str_replace("\t", '' , $plan_plantilla);

  include "combinaCampos.php";

  $carpetas['Generales'][] = array("id"=> $plan_id,
                                    "nombre"=> $plan_nombre,
                                    "ruta"=> $plan_plantilla,
                                    "show"=> true);

  if(!$editar && $plan_nombre == 'Plantilla_Salida.') {
    $asunto .= $plan_plantilla;
  }

  $sqlGeneralesCount++;
  $plant->MoveNext();

  
};

if($sqlGeneralesCount == 0)
  $carpetas['Generales'][] = array();




$sql21       ="SELECT
                      ID,
                      PLAN_PLANTILLA,
                      PLAN_NOMBRE,
                      PLAN_FECHA,
                      DEPE_CODI,
                      USUA_CODI,
                      PLAN_TIPO
                    FROM
                      SGD_PLAN_PLANTILLAS
                    WHERE
                      PLAN_TIPO = 2
                    AND DEPE_CODI = " . $depecodi;

$plant = $db->conn->Execute($sql21);


$sqlDependenciasCount = 0;
while(!$plant->EOF){

  $grupDepende    = array();
  $grupGeneral    = array();
  $grupPersonal   = array();

  $plan_id        = $plant->fields["ID"];
  $plan_nombre    = $plant->fields["PLAN_NOMBRE"];
  $plan_fecha     = $plant->fields["PLAN_FECHA"];
  $plan_tipo      = $plant->fields["PLAN_TIPO"];
  $plan_depend    = $plant->fields["DEPE_CODI"];
  $plan_usurio    = $plant->fields["USUA_CODI"];
  $plan_plantilla = $plant->fields["PLAN_PLANTILLA"];

  $plan_plantilla = str_replace('"', "'", $plan_plantilla);
  $plan_plantilla = str_replace("\r", '' , $plan_plantilla);
  $plan_plantilla = str_replace("\n", '' , $plan_plantilla);
  $plan_plantilla = str_replace("\t", '' , $plan_plantilla);
  include "combinaCampos.php";

  $carpetas['Dependencia'][] = array("id"=> $plan_id,
                                      "nombre"=> $plan_nombre,
                                      "ruta"=> $plan_plantilla,
                                      "show"=> true);
  $sqlDependenciasCount++;
  $plant->MoveNext();
  
};

if($sqlDependenciasCount == 0) 
  $carpetas['Dependencia'][]  = array();


$sqlPersonalesCount = 0;
$sql21       ="SELECT
                      ID,
                      PLAN_PLANTILLA,
                      PLAN_NOMBRE,
                      PLAN_FECHA,
                      DEPE_CODI,
                      USUA_CODI,
                      PLAN_TIPO
                    FROM
                      SGD_PLAN_PLANTILLAS
                    WHERE
                      PLAN_TIPO = 1
                    AND DEPE_CODI = " . $depecodi . "
                    AND  USUA_CODI = " . $usuacodi; 

$plant = $db->conn->Execute($sql21);



while(!$plant->EOF){

  $grupDepende    = array();
  $grupGeneral    = array();
  $grupPersonal   = array();

  $plan_id        = $plant->fields["ID"];
  $plan_nombre    = $plant->fields["PLAN_NOMBRE"];
  $plan_fecha     = $plant->fields["PLAN_FECHA"];
  $plan_tipo      = $plant->fields["PLAN_TIPO"];
  $plan_depend    = $plant->fields["DEPE_CODI"];
  $plan_usurio    = $plant->fields["USUA_CODI"];
  $plan_plantilla = $plant->fields["PLAN_PLANTILLA"];

  $plan_plantilla = str_replace('"', "'", $plan_plantilla);
  $plan_plantilla = str_replace("\r", '' , $plan_plantilla);
  $plan_plantilla = str_replace("\n", '' , $plan_plantilla);
  $plan_plantilla = str_replace("\t", '' , $plan_plantilla);
  include "combinaCampos.php";

  $carpetas['Personales'][] = array("id"=> $plan_id,
                                    "nombre"=> $plan_nombre,
                                    "ruta"=> $plan_plantilla,
                                    "show"=> true);

  $sqlPersonalesCount++;
  $plant->MoveNext();
  
};

if($sqlPersonalesCount == 0)
    $carpetas['Personales'][]  = array();


if($fecha_rad_salida) $asunto = str_replace('F_RAD_S', $fecha_rad_salida, $asunto);
if($rad_salida) $asunto = str_replace('RAD_S', $rad_salida, $asunto);

if ($esNotificacion) {
  $fecha = explode(" ", date("d F Y")); 
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

  if($fecha_rad_salida) $asunto = str_replace('DIA_S', $dia, $asunto);
  if($fecha_rad_salida) $asunto = str_replace('MES_S', $mes, $asunto);
  if($fecha_rad_salida) $asunto = str_replace('ANHO_S', $anho, $asunto);

  $isql = " SELECT DEPE_NOMB
            FROM DEPENDENCIA
            WHERE DEPE_CODI = $depecodi ";

  $rs   = $db->conn->Execute($isql);

  if ($rs && !$rs->EOF) {
    $depenomb  = $rs->fields["DEPE_NOMB"];
  }
  
  if($rad_salida) $asunto = str_replace('USUA_NOMB_S', $usuanomb, $asunto);
  if($rad_salida) $asunto = str_replace('DEPE_NOMB_S', $depenomb, $asunto);

  if ($esNotificacionCircular) {
    include_once ($ruta_raiz."/include/tx/notificacion.php");
    $notificacion = new Notificacion($db);

    $result = $notificacion->destinatariosPorRadicado($radicado);
    $destinatarios = $result[0]["DESTINATARIOS"];

    $asunto = str_replace('DESTINATARIO_S', $destinatarios, $asunto);
  }
}

foreach($usrsRad->dirMail as $dirCodigo => $dirMail) {
  if ($dirMail)$mails .= "$dirMail; ";
}

if(!empty($arrAnexos)){
  foreach($arrAnexos->codi_anexos as $ANEX_CODIGO => $codi_anexos) {
    $anex[] = $codi_anexos;
  }

  foreach($arrAnexos->desc_anexos as $ANEX_DESC => $desc_anexos) {
    $desc_anex[] = $desc_anexos;
  }


  foreach($arrAnexos->path_anexos as $ANEX_PATH => $path_anexos) {
    $path_anex[] = $ruta_raiz.$path_anexos;
  }

  $adjuntosAnex = explode(",", $arrAnexos->adjuntos[$anexo]);
}

$val = (ini_get('upload_max_filesize'));
$ultimo = strtolower($val{strlen($val)-1});
switch($ultimo)
{ // El modificador 'G' se encuentra disponible desde PHP 5.1.0
    case 'g': $val *= 1024;
    case 'm': $val *= 1024;
    case 'k': $val *= 1024;
}


if($rad_salida){
    $anexosRadSal = new Anexo($db);
    $arrAnexosSal = $anexosRadSal->anexosRadicado($rad_salida);
    if(!empty($arrAnexosSal)){
      foreach($arrAnexosSal->codi_anexos as $ANEX_CODIGO => $codi_anexos) {
        $anexSal[] = $codi_anexos;
      }

      foreach($arrAnexosSal->desc_anexos as $ANEX_DESC => $desc_anexos) {
        $desc_anexSal[] = $desc_anexos;
      }


      foreach($arrAnexosSal->path_anexos as $ANEX_PATH => $path_anexos) {
        $path_anexSal[] = $ruta_raiz.$path_anexos;
      }
    }
}

if ($_SESSION['apiFirmaDigital']=='true' && $_SESSION["usua_perm_firma"] >= 1){
   $smarty->assign("PERM_FIRMA", true);
}else{
   $smarty->assign("PERM_FIRMA", false);
}

if(!$esNotificacion && $radicado_sgd_trad_codigo != MEMORANDO) {
   $idDre = [];
    $iSql = "SELECT * FROM SGD_DIR_DRECCIONES WHERE RADI_NUME_RADI = '$radicado'";
    //$db->conn->debug = true;
    $rsDestinatarios = $db->conn->query($iSql);
    $tablaHtmlDestinatarios = "<table border=0 class='table'>";  
    $tablaHtmlDestinatarios .= "<thead><tr><th>Destinatario</th><th >Medio de envío</th><th >¿Requiere certificado electrónico del envío? </th></tr></thead>";
    if(!$radicado_rem) $radicado_rem=1;
    while($rsDestinatarios && !$rsDestinatarios->EOF){
      $sgdDirId = $rsDestinatarios->fields["ID"];
      $sgdDirTipoRad = trim($rsDestinatarios->fields["SGD_DIR_TIPO"]);
      $idDre[] = $rsDestinatarios->fields["ID"];
      $nombreRem[$sgdDirTipoRad] = trim($rsDestinatarios->fields["SGD_DIR_NOMBRE"]);
      $direccionRem[$sgdDirTipoRad] = trim($rsDestinatarios->fields["SGD_DIR_DIRECCION"]);
      $emailRem[$sgdDirTipoRad] = trim($rsDestinatarios->fields["SGD_DIR_MAIL"]);

      $muniCodi = trim($rsDestinatarios->fields["MUNI_CODI"]);
      $dptoCodi = trim($rsDestinatarios->fields["DPTO_CODI"]);

      $a = new LOCALIZACION($dptoCodi,$muniCodi,$db);
      $dpto_nombre = $a->departamento;
      $muni_nombre = $a->municipio;
      $dpto_nombre_us[$sgdDirTipoRad] = $dpto_nombre;
      $muni_nombre_us[$sgdDirTipoRad] = $muni_nombre;
      
      $tablaHtmlDestinatarios .= "<!-- Destinatario valor de seleccion - $sgdDirTipoRad - -->"; 
      $tablaHtmlDestinatarios .= "<tr valign='top'> ";
      $tablaHtmlDestinatarios .= "  <td valign='top' colspan='1'><small> ";
      $arrayusuario = $arrayusuario."-"."1";
      if($radicado_rem==$sgdDirTipoRad){$datoss =  " checked ";}else{$datoss =  " ";}
      $tablaHtmlDestinatarios .= "<input type='radio'   name='radicado_rem_p' value='$sgdDirTipoRad'  id='rusuario' $datoss > ";
      $tablaHtmlDestinatarios .= $nombreRem[$sgdDirTipoRad];
      $tablaHtmlDestinatarios .= " <br> ".$direccionRem[$sgdDirTipoRad].' '.$dpto_nombre_us[$sgdDirTipoRad] . " / " . $muni_nombre_us[$sgdDirTipoRad];
      $tablaHtmlDestinatarios .= "</td>";
      $tablaHtmlDestinatarios .= "<td><select name='envio_$sgdDirId' class=''><option value='E-mail'>E-mail</option></select></td>";
      
      if ($radicado_sgd_trad_codigo == MEMORANDO)
          $tablaHtmlDestinatarios .= "<td></td>";
      else
          $tablaHtmlDestinatarios .= "<td><input name='validez_$sgdDirId' type=checkbox checked/></td>";
      $tablaHtmlDestinatarios .= "</tr>";
      $rsDestinatarios->MoveNext();
  } 
  $tablaHtmlDestinatarios .= "</table>";
  $idDre=implode(',', $idDre);
}


$isql   = "select radi_path from radicado where radi_nume_radi = $radicado";
$rs = $db->conn->Execute($isql);

if ($rs){
  $radi_path_firma = $rs->fields["RADI_PATH"];
}

if($radi_path_firma != NULL) {
    $debeFirmar = 'SI';
} else {

    $isql   = "select radi_usua_firma, radi_depe_firma 
                  FROM radicado where radi_nume_radi = $radicado";
    $rs = $db->conn->Execute($isql);

    if ($rs){
      $radi_usua_firma = $rs->fields["RADI_USUA_FIRMA"];
      $radi_depe_firma = $rs->fields["RADI_DEPE_FIRMA"];
    }

    if($radi_usua_firma == NULL) {
          $debeFirmar = 'SI';
    } else {
        if($_SESSION["dependencia"] == $radi_depe_firma && $_SESSION["codusuario"] == $radi_usua_firma) {
          $debeFirmar = 'SI';
        } else {
          $debeFirmar = 'NO';
        }
    }
}

$smarty->assign("sid"              , SID); //Envio de session por get
$smarty->assign("maxFile"          , $val);
$smarty->assign("TIPOS_RADICADOS"  , $tipos_radicados);
$smarty->assign("GUARDAR_RADICADO" , $guardar_radicado);
$smarty->assign("MOSTRAR_ERROR"    , $mostrar_error);
$smarty->assign("usuacodi"         , $usuacodi);
$smarty->assign("editar"           , $editar);
$smarty->assign("extn"             , $extn);
$smarty->assign("depecodi"         , $depecodi);
$smarty->assign("codigoCiu"        , $codigoCiu);
$smarty->assign("radPadre"         , $radicado);
$smarty->assign("rutaPadre"        , $rutaPadre);
$smarty->assign("usuanomb"         , $usuanomb);
$smarty->assign("usualog"          , $usualog);
$smarty->assign("destinatario"     , $destinatario);
$smarty->assign("asunto"           , $asunto);  // variable respuesta por POST
$smarty->assign("emails"           , $emails);
$smarty->assign("carpetas"         , $carpetas);
$smarty->assign("perm_carps"       , $permPlnatill);
$smarty->assign("ano"              , $ano);
$smarty->assign("fecha_rad_salida" , $fecha_rad_salida);
$smarty->assign("mails"            , trim($mails));
$smarty->assign("rad_salida"       , $rad_salida);
$smarty->assign("arrAnexos"        , $arrAnexos);
$smarty->assign("anexo"            , $anexo);
$smarty->assign("rad_sgd_trad_cod" , $radicado_sgd_trad_codigo);
$smarty->assign("esNotificacion"   , $esNotificacion);

$smarty->assign("anex"             , $anex);
$smarty->assign("desc_anex"        , $desc_anex);
$smarty->assign("path_anex"        , $path_anex);

$smarty->assign("anexSal"          , $anexSal);
$smarty->assign("desc_anexSal"     , $desc_anexSal);
$smarty->assign("path_anexSal"     , $path_anexSal);

$smarty->assign("adjuntosAnex"     , $adjuntosAnex);
$smarty->assign("tipo_radicado"    , $tipo_radicado);
$smarty->assign("anex_tipo_envio"  , $anex_tipo_envio);
$smarty->assign("expedientes"      , $expedientes);
$smarty->assign("sgd_exp_numero"   , $expAnexo);
$smarty->assign("excluidosRR"      , $excluidosRR);
$smarty->assign("estadoAnexo", $estadoAnexo);
$smarty->assign("puedeRadicar"      , $puedeRadicar);

$smarty->assign("debeFirmar", $debeFirmar);

if(!$esNotificacion && $radicado_sgd_trad_codigo != MEMORANDO) {
  $smarty->assign("tablaHtmlDestinatarios",$tablaHtmlDestinatarios);
  $smarty->assign("idDre",$idDre);
}

// si se utiliza firma digital permitir radicar los que tienen el permiso
$smarty->assign("firma", ($_SESSION['apiFirmaDigital'] != ''));
$smarty->assign("firma_usuario", ($_SESSION["usua_perm_firma"]?true:false));

$smarty->display('index.tpl');
