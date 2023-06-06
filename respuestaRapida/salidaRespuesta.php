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
if($_SESSION["krd"])
    $krd=$_SESSION["krd"];

if(!isset($_SESSION['dependencia']))include"../rec_session.php";

$dependencia=$_SESSION["dependencia"]*1;

$ruta_raiz="..";
include$ruta_raiz."/htmlheader.inc.php";
include "$ruta_raiz/processConfig.php";
require_once($ruta_raiz."/include/db/ConnectionHandler.php");

$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

require(SMARTY_DIR.'Smarty.class.php');

$smarty = new Smarty;
$smarty->template_dir='./templates';
$smarty->compile_dir='../bodega/tmp';
$smarty->config_dir='./configs/';
$smarty->cache_dir='./cache/';

$smarty->left_delimiter='<!--{';
$smarty->right_delimiter='}-->';

$errores = $_GET['error'];
$nurad = $_GET['nurad'];
$sali = array();



function errores($errores) {
	$arreglo_errores = array();
	$arreglo_errores[1] = 'Nosegeneroelradicado.';
	$arreglo_errores[] = 'Errornosecreolacarpetaparalosadjuntos/bodega/adjuntos/.';
	$arreglo_errores[] = 'Unarchivonoseenvio(Extensioninvalida)';
	$arreglo_errores[] = 'Elformatomimedeldocumentonoexiste';
	$arreglo_errores[] = 'Eltamanodelarchivoadjuntosuperoellimitepermitido';
	return$arreglo_errores[$errores];
}

if (!empty($nurad)) {

	$isqlDepR = "SELECT
								ANEX_NOMB_ARCHIVO AS NOMBRE
								,ANEX_DESC
							FROM
								ANEXOS
							WHERE
								ANEX_RADI_NUME='$nurad'
								AND ANEX_BORRADO='N'";

	$rsDepR = $db->conn->Execute($isqlDepR);
	
	$file = $rsDepR->fields['NOMBRE'];

	while ($rsDepR && !$rsDepR->EOF) {
		$sali[] = array('path' => $ruta_raiz . "bodega/" 
			. substr($file, 0, 4) . "/" . $dependencia . "/docs/" . $file,
			'desc' => $rsDepR->fields['ANEX_DESC']);
		$rsDepR->MoveNext();
	}
	
}

$datoserror=explode('-',$errores);

$noerrores=$datoserror[0];

for($i=0;$i<count($datoserror);$i++){
  $error1.=errores($datoserror[$i]).'';
}

if(empty($errores)){
    $salida='ok';
}

$smarty->assign("krd", $krd);
$smarty->assign("respuesta_rap", 'true');
$smarty->assign("noerror", $noerrores);
$smarty->assign("error", $error1);
$smarty->assign("nurad", $nurad);
$smarty->assign("sali", $sali);
$smarty->assign("salida", $salida);
$smarty->assign("estilosCaliope", $estilosCaliope);
$smarty->assign("sid", SID);
$smarty->assign("dependencia", $dependencia);
$smarty->display('salidaRespuesta.tpl');
