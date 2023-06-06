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

define('RAD_ENTRADA', '2');
define('ADODB_ASSOC_CASE', 1);
define('COLOMBIA', 170);
define('AMERICA', 1);

$ruta_raiz = "..";
foreach ($_GET as $key => $valor)
	${$key} = $valor;
foreach ($_POST as $key => $valor)
	${$key} = $valor;

if (!$anexo)
	$anexo = $codAnexo;


if (!$radicado) {
	$radicado = $nurad;
}
if (!$_SESSION['dependencia'])
	header("Location: " . $ruta_raiz . "/cerrar_session.php");

require "$ruta_raiz/processConfig.php";

$anex_codigo = (isset($_GET['anexo'])) ? $_GET['anexo'] : null;

// Variable que almacena los tipos de radicados que se encuentran en DB
$tipos_radicados = array();

require (SMARTY_DIR . 'Smarty.class.php');

$mostrar_error = $_GET['error_radicacion'];

//formato para fecha en documentos
function fechaFormateada($FechaStamp) {
	$ano = date('Y', $FechaStamp); //<-- Ano
	$mes = date('m', $FechaStamp); //<-- número de mes (01-31)
	$dia = date('d', $FechaStamp); //<-- Día del mes (1-31)
	$dialetra = date('w', $FechaStamp); //Día de la semana(0-7)

	$arreglo_dias = array();
	$arreglo_dias[] = 'domingo';
	$arreglo_dias[] = 'lunes';
	$arreglo_dias[] = 'martes';
	$arreglo_dias[] = 'miercoles';
	$arreglo_dias[] = 'jueves';
	$arreglo_dias[] = 'viernes';
	$arreglo_dias[] = 'sabado';

	$dialetra = (isset($arreglo_dias[$dialetra])) ? $arreglo_dias[$dialetra] : null;

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

	$mesletra = (isset($arreglo_meses[$mes])) ? $arreglo_meses[$mes] : null;

	return htmlentities("$dialetra, $dia de $mesletra de $ano");
}

$smarty = new Smarty;
$smarty->template_dir = './templates';
$smarty->compile_dir = '../bodega/tmp/';
$smarty->config_dir = './configs/';
$smarty->cache_dir = './cache/';

$smarty->left_delimiter = '<!--{';
$smarty->right_delimiter = '}-->';

function byteSize($bytes) {
	$size = $bytes / 1024;
	if ($size < 1024) {
		$size = number_format($size, 2);
		$size .= ' KB';
	} else {
		if ($size / 1024 < 1024) {
			$size = number_format($size / 1024, 2);
			$size .= ' MB';
		} else if ($size / 1024 / 1024 < 1024) {
			$size = number_format($size / 1024 / 1024, 2);
			$size .= ' GB';
		}
	}
	return $size;
}

$krd = (isset($_SESSION["krd"])) ? $_SESSION["krd"] : '';

include_once ($ruta_raiz . "/include/db/ConnectionHandler.php");
include_once ($ruta_raiz . "/include/tx/usuario.php");
include_once ($ruta_raiz . "/class_control/anexo.php");

$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$usrsRad = new Usuario($db);
$arrUsuarios = $usrsRad->usuariosDelRadicado($nurad);

$anexosRad = new Anexo($db);
$arrAnexos = $anexosRad->anexosRadicado($radPadre);


$sql_tipo_rad = "SELECT sgd_trad_codigo,
                        sgd_trad_descr
                      FROM SGD_TRAD_TIPORAD
                      where sgd_trad_codigo <>" . RAD_ENTRADA;
$rs_tipo_rad = $db->conn->Execute($sql_tipo_rad);

while (!$rs_tipo_rad->EOF) {
	$tipos_radicados[$rs_tipo_rad->fields["SGD_TRAD_CODIGO"]] = $rs_tipo_rad->fields["SGD_TRAD_DESCR"];
	$rs_tipo_rad->MoveNext();
}

$verrad = '';
$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$encabezado = session_name() . "=" . session_id();
$encabezado .= "&krd= $krd";

$isql = "SELECT USUA_EMAIL,
                USUA_EMAIL_1,
                USUA_EMAIL_2,
                DEPE_CODI,
                USUA_CODI,
                USUA_NOMB,
                USUA_LOGIN,
                USUA_DOC
            FROM USUARIO
            WHERE USUA_LOGIN ='$krd' ";

$rs = $db->conn->Execute($isql);

if (!$rs) {
	exit('ERROR, datos invalidos');
}

$emails = array();
while (!$rs->EOF) {

	$emails[] = trim(strtolower($rs->fields["USUA_EMAIL"]));
	$temEmail = trim(strtolower($rs->fields["USUA_EMAIL_1"]));
	$temEmai = trim(strtolower($rs->fields["USUA_EMAIL_2"]));

	//buscamos el correo que inicie con web para colocarlo como primero
	if (substr($temEmail, 0, 3) == 'web') {
		array_unshift($emails, $temEmail);
	} else {
		$emails[] = $temEmail;
	}

	if (substr($temEmai, 0, 3) == 'web') {
		array_unshift($emails, $temEmai);
	} else {
		$emails[] = $temEmai;
	}

	$usuacodi = $rs->fields["USUA_CODI"];
	$depecodi = $rs->fields["DEPE_CODI"];
	$usuanomb = $rs->fields["USUA_NOMB"];

	$usuaLogin = $rs->fields["USUA_LOGIN"];
	$codigoCiu = $rs->fields["USUA_DOC"];
	$rs->MoveNext();
}
//Eliminamos los campos vacios en el array
$emails = array_filter($emails);

# informacion remitente
$name = "";
$email = "";

$isql = "SELECT D.*
            FROM SGD_DIR_DRECCIONES D
            WHERE D.RADI_NUME_RADI = $radicado";
$rs = $db->conn->Execute($isql);

$dirCodigo = $rs->fields["SGD_DIR_CODIGO"];
$name = $rs->fields["SGD_DIR_NOMREMDES"];
//$email      = $rs->fields["SGD_DIR_MAIL"];
$municicodi = $rs->fields["MUNI_CODI"];
$depecodi2 = $rs->fields["DPTO_CODI"];

$name = strtoupper($name);
$depcNomb = strtoupper($depcNomb);
$fecha1 = time();
$fecha = ucfirst(fechaFormateada($fecha1));

$buscar_anexo = "SELECT a.radi_nume_salida, a.sgd_dir_mail, a.anex_tipo_envio,
                      a.anex_nomb_archivo, a.anex_env_email,
                      (select radi_fech_radi from radicado r 
											where r.radi_nume_radi=a.radi_nume_salida) radi_fech_salida
                      FROM anexos a
                      WHERE a.anex_codigo = '$codAnexo'";

$anexo_result = $db->conn->Execute($buscar_anexo);
$ano = substr($codAnexo, 0, 4);
$dependencia = substr($codAnexo, 4, 3);

if (!$anexo_result->EOF) {
	$nombre_archivo = $anexo_result->fields['ANEX_NOMB_ARCHIVO'];
	$numero_radicado = $anexo_result->fields['RADI_NUME_SALIDA'];
	$email = $anexo_result->fields['SGD_DIR_MAIL'];
	$tipo_envio = $anexo_result->fields['ANEX_TIPO_ENVIO'];
	$env_email = $anexo_result->fields['anex_env_email'];
	$fecha_radicado = $anexo_result->fields['RADI_FECH_SALIDA'];


	$guardar_radicado = (isset($numero_radicado)) ? true : false;

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

if ($numero_radicado)
	$rad_salida = $numero_radicado;
if ($fecha_radicado)
	$fecha_rad_salida = substr($fecha_radicado, 0, 16);

$sqlD = " SELECT  a.MUNI_NOMB,
                  b.DPTO_NOMB
          FROM    MUNICIPIO a, DEPARTAMENTO b
          WHERE (a.ID_PAIS = " . COLOMBIA . ") AND
                (a.ID_CONT = " . AMERICA . ") AND
                (a.DPTO_CODI = $depecodi2) AND
                (a.MUNI_CODI = $municicodi) AND
                (a.DPTO_CODI=b.DPTO_CODI) AND
                (a.ID_PAIS=b.ID_PAIS) AND
                (a.ID_CONT=b.ID_CONT)";

$descripMuniDep = $db->conn->Execute($sqlD);
$depcNomb = $descripMuniDep->fields["MUNI_NOMB"];
$muniNomb = $descripMuniDep->fields["DPTO_NOMB"];

$destinatario = trim($mails);

$sql1 = " select
  anex_tipo_ext as ext
  from
  anexos_tipo";

$exte = $db->conn->Execute($sql1);

while (!$exte->EOF) {
	$val = $exte->fields["EXT"];
	$extn .= empty($extn) ? $val : "|" . $val;
	$exte->MoveNext();
};

$sqlSubstDesc = $db->conn->substr . "(anex_desc, 0, 50)";

//adjuntar  la imagen html al radicado
$desti = "SELECT RADI_PATH
          FROM RADICADO
          WHERE RADI_NUME_RADI = $radicado";

$rssPatth = $db->conn->Execute($desti);
$pathPadre = $rssPatth->fields["RADI_PATH"];

$post = strpos(strtolower($pathPadre), 'bodega');
$pathPadre = substr($pathPadre, $post + 6);
$rutaPadre = trim($ruta_raiz . '/../../bodega/2017/' . $pathPadre);

if (is_file($rutaPadre) and substr($rutaPadre, -4) == "html") {
	$gestor = fopen($rutaPadre, "r");
	$archtml = fread($gestor, filesize($rutaPadre));

	$archtml = preg_replace('/<img (.+?)>/', ' ', $archtml);
	$archtml = preg_replace('COLOR: red;', ' ', $archtml);
	$config = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier();

	$asunto .= "<br><br><hr><br>
  $clean_html";
}

if ($perPlanilla > 2) {
	$permPlnatill[] = array("nombre" => "Generales", "codigo" => 3);
}

if ($fecha_rad_salida)
	$asunto = str_replace('F_RAD_S', $fecha_rad_salida, $asunto);
if ($rad_salida)
	$asunto = str_replace('RAD_S', $rad_salida, $asunto);
//$asunto = str_replace('*RAD_S*', $nurad, $asunto);

$verradicado = $nurad;
include $ruta_raiz . "/ver_datosrad.php";


$desc_anex = array();
$path_anex = array();

foreach ($usrsRad->dirMail as $dirCodigo => $dirMail) {
	if ($dirMail)
		$mails .= "$dirMail; ";
}

if (!empty($arrAnexos)) {
	foreach ($arrAnexos->codi_anexos as $ANEX_CODIGO => $codi_anexos) {
		$anex[] = $codi_anexos;
	}

	foreach ($arrAnexos->desc_anexos as $ANEX_CODIGO => $desc_anexos) {
		if ($desc_anexos)
			$desc_anex[$ANEX_CODIGO] = $desc_anexos;
	}
	$ano = substr($nurad, 0, 4);

	foreach ($arrAnexos->path_anexos as $ANEX_CODIGO => $path_anexos) {
		$path_anex[$ANEX_CODIGO] = $path_anexos;
	}

	$adjuntosAnex = explode(",", $arrAnexos->adjuntos[$anexo]);
}

if ($sendMail == "Enviar Correo" and $env_email != 1) {

	// if($_SESSION["enviarMailMovimientos"]==1){
	$codTx = 6;
	$depsel8 = explode('-', $value);
	$usuaCodiMail = $depsel8[1];
	$depeCodiMail = $depsel8[0];

	$mailDestino = "$mails";
	$radicadosSelText = $nurad . ",";

	$desti = "SELECT RADI_PATH
          FROM RADICADO
          WHERE RADI_NUME_RADI = $nurad";

	if ($tipo_envio == 2) {
		$anex_estado = 4;
	} else {
		$anex_estado = 2;
	}
	$db3 = $db;

	$rssPatth = $db->conn->Execute($desti);

	$pathRadicado = $ruta_raiz . "/bodega" . $rssPatth->fields["RADI_PATH"];
	$asuntoMailRespuestaRapida = "Se ha generado una Respuesta con No. $nurad";
	include_once("$ruta_raiz/include/mail/GENERAL.mailInformar.php");

	if ($envioOk == "ok") {

		$envCorreo = "UPDATE ANEXOS SET ANEX_ESTADO=$anex_estado, ANEX_ENV_EMAIL=1 WHERE RADI_NUME_SALIDA=$nurad";
		$envioCorreo = $db3->conn->query($envCorreo);

		include $ruta_raiz . "/include/tx/Envio.php";

		$envio = new Envio($db3);

		$isql = "SELECT D.*
            FROM SGD_DIR_DRECCIONES D
            WHERE D.RADI_NUME_RADI = $nurad";
		$rsDir = $db3->conn->Execute($isql);

		while (!$rsDir->EOF) {

			$dirMail = $rsDir->fields['SGD_DIR_MAIL'];
			$dirNombre = $rsDir->fields['SGD_DIR_NOMREMDES'];
			$dirCodigo = $rsDir->fields['SGD_DIR_CODIGO'];
			$dirTipo = $rsDir->fields['SGD_DIR_TIPO'];

			if ($dirMail) {
				$envio->usuaDoc = $_SESSION["usua_doc"];
				$envio->ciudadDestino = "";
				$envio->formaEnvio = 108;
				$envio->radicadoAEnviar = $nurad;
				$envio->ciudadDestino = "";
				$envio->telefono = "";
				$envio->mail = $dirMail;
				$envio->peso = 0;
				$envio->valorUnitario = 0;
				$envio->nombre = $dirNombre;
				$envio->codigoDir = $dirCodigo;
				$envio->codigoDependencia = $depecodi;
				$envio->dirTipo = $dirTipo;
				$envio->RadicadoGrupoMasiva = null;
				$envio->numeroPlanilla = "";
				$envio->direccion = $dirMail;
				$envio->nombreDepartamento = "";
				$envio->nombreMunicipio = "";
				$envio->nombrePais = "";
				$envio->Observaciones = "";
				$envio->generarEnvio();
			}
			$rsDir->MoveNext();
		}
		$envio->usuaDoc = $_SESSION["usua_doc"];
		$envio->ciudadDestino = "";
		$envio->formaEnvio = 106;
		$envio->radicadoAEnviar = $nurad;
		$envio->ciudadDestino = "";
		$envio->telefono = "";
		$envio->mail = $mails;
		$envio->peso = "";
		$envio->valorUnitario = "";
		$envio->nombre = $destinatario;
		$envio->codigoDir = $dirCodigo;
		$envio->codigoDependencia = $depecodi;
		$envio->dirTipo = 1;
		$envio->RadicadoGrupoMasiva = null;
		$envio->numeroPlanilla = "";
		$envio->direccion = "";
		$envio->nombreDepartamento = "";
		$envio->nombreMunicipio = "";
		$envio->nombrePais = "";
		$envio->Observaciones = "";
		$envio->generarEnvio();
	}
	echo '<script>
					javascript:window.parent.opener.$.fn.cargarPagina("./lista_anexos.php","tabs-c");
					window.parent.close();
				</script>';
}

$smarty->assign("sid", SID); //Envio de session por get
$smarty->assign("TIPOS_RADICADOS", $tipos_radicados);
$smarty->assign("GUARDAR_RADICADO", $guardar_radicado);
$smarty->assign("MOSTRAR_ERROR", $mostrar_error);
$smarty->assign("usuacodi", $usuacodi);
$smarty->assign("editar", $editar);
$smarty->assign("extn", $extn);
$smarty->assign("depecodi", $depecodi);
$smarty->assign("codigoCiu", $codigoCiu);
$smarty->assign("nurad", $nurad);
$smarty->assign("radicado", $nurad);
$smarty->assign("radPadre", $radPadre);
$smarty->assign("rutaPadre", $rutaPadre);
$smarty->assign("usuanomb", $usuanomb);
$smarty->assign("usualog", $usualog);
$smarty->assign("destinatario", $destinatario);
$smarty->assign("asunto", $ra_asun);	// variable respuesta por POST
$smarty->assign("email", $mails);
$smarty->assign("carpetas", $carpetas);
$smarty->assign("perm_carps", $permPlnatill);
$smarty->assign("perm_carps", $permPlnatill);
$smarty->assign("fecha_rad_salida", $fecha_rad_salida);
$smarty->assign("mails", $mails);
$smarty->assign("ano", $ano);
$smarty->assign("rad_salida", $rad_salida);
$smarty->assign("arrAnexos", $arrAnexos);
$smarty->assign("anex", $anex);
$smarty->assign("desc_anex", $desc_anex);
$smarty->assign("path_anex", $path_anex);
$smarty->assign("anexo", $anexo);
$smarty->assign("desc_anex", $desc_anex);
$smarty->assign("envioOk", $envioOk);
$smarty->assign("adjuntosAnex", $adjuntosAnex);
$smarty->assign("anex_tipo_envio", $anex_tipo_envio);
$smarty->assign("rutaPadre", $rutaPadre);
$smarty->assign("logoEntidad", '../bodega'.$logoEntidad ); //Variable de processConfig

$smarty->display('sendRespMail.tpl');