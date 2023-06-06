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
if (!$_SESSION['dependencia'])
  header ("Location: $ruta_raiz/cerrar_session.php");

// Modificado 2010 aurigadl@gmail.com

/**
* Paggina index2.php que muestra respuestaRapida y paralelamente la Imagen. 
* Creado en Correlibre en 2012
* @autor Jairo Losada 2009-05
* @licencia GNU/GPL V 3
*/


foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;


define('ADODB_ASSOC_CASE', 1);

$verrad         = '';
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
$tip3Nombre     = $_SESSION["tip3Nombre"];
$tip3desc       = $_SESSION["tip3desc"];
$tip3img        = $_SESSION["tip3img"];
$descCarpetasGen= $_SESSION["descCarpetasGen"] ;
$descCarpetasPer= $_SESSION["descCarpetasPer"];


include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");

if (!$db) $db = new ConnectionHandler($ruta_raiz);

$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$isql = "select * from radicado where radi_nume_radi=$radicadopadre";
$rs = $db->conn->query($isql);

$pathImagen = $rs->fields["radi_path"];

?>
<frameset cols="99%,*"  border=4 scrolling="yes" >
  <frame name="central" src="../respuestaRapida/index.php?PHPSESSID=<?=session_id()?>&radicadopadre=<?=$radicadopadre?>&krd=<?=krd?>&editar=<?=$editar?>&anexo=<?=$anexo?>" />
  <!-- <frame name="alto" src='<?=$ruta_raiz ."/linkArchivo.php?&PHPSESSID=".session_id()."&numrad=$radicadopadre"?>' />  -->
  <!--<frame name="alto" src='<?=$ruta_raiz ."/bodega/".$pathImagen?>' /> -->
</frameset>

<script>
 function cerrar(){
  opener.cargarPagina("./lista_anexos.php","tabs-c");
  window.close();
 }
</script>
