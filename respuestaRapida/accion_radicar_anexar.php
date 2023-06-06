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

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

define('ADODB_ASSOC_CASE', 1);
#echo "estoy entrando a radicar anexar"
// var $anexosCodigo csv Guarda los codigos de anexos que van a enviarse como adjuntos.
foreach ($_GET as $key => $valor)
	${$key} = $valor;
foreach ($_POST as $key => $valor)
	${$key} = $valor;
if ($_POST["anex_codigo"])
	$anexosCodigo = implode(",", array_keys($_POST["anex_codigo"]));

define('RADICAR', 0);
define('ANEXAR', 1);
define('GUARDAR_CAMBIOS', 2);
define('PREVISUALIZAR', 3);

$acciones = array('Radicar' => RADICAR,
	'Grabar como Anexo' => ANEXAR,
	'Guardar Cambios' => GUARDAR_CAMBIOS,
	'Previsualizar' => PREVISUALIZAR); // Revisar HTML Button tipo submit

$accion = 0;

$krd = (isset($_SESSION["krd"])) ? $_SESSION["krd"] : null;

$accion = $acciones[$Button];

if (!isset($puedeRadicar)) {
	$puedeRadicar = true;
}

if ($accion == RADICAR) {
	#Logica de notificacioes
	if($puedeRadicar == true){
		include (__DIR__.'/procRespuesta.php');
	}
	else{
		echo '<script type="text/javascript">
						alert("Solo se puede generar un Radicado");
						window.parent.close();
			  </script>';
	}
} elseif ($accion == ANEXAR || $accion == GUARDAR_CAMBIOS) {
	include (__DIR__.'/grabar_anexo.php');
}

?>


