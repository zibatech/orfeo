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

$ruta_raiz = "../";
if (!$_SESSION['dependencia'])
  header ("Location: ".$ruta_raiz."cerrar_session.php");

include_once ($ruta_raiz."include/db/ConnectionHandler.php");

$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

$usuario     = $_SESSION["usua_nomb"];
$dependencia = $_SESSION["depecodi"];
$dep_code    = $_SESSION["depecodi"];
$usu_code    = $_SESSION["codusuario"];

//Borramos la planilla
if(!empty($_POST['delPlant'])){
  $idPlanila = $_POST["planaborrar"];

  foreach ($idPlanila as $valor){
    $sql21 ="DELETE FROM
              SGD_PLAN_PLANTILLAS
            WHERE
            ID        = $valor AND
            DEPE_CODI = $dep_code AND
            USUA_CODI = $usu_code" ;

    $rsg   = $db->conn->Execute($sql21);
  }
}

//Creamos la planilla
if ($_POST['plantillas']) {

  $nombre    = $_POST["nombre"];
  $nivel     = $_POST["nivel"];
  $contenido = $_POST["contplant"];
  $fecha     = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);

#Creo la consulta dependiendo del driver.
switch ($db->driver)
 {case 'mssql':   break;
  case 'oracle':  break;
  case 'oci8': $sql21 = " INSERT INTO  SGD_PLAN_PLANTILLAS(ID, plan_plantilla, plan_nombre, plan_fecha, depe_codi, usua_codi, plan_tipo)
  VALUES (SEC_SGD_PLAN_PLANTILLAS.NEXTVAL,'$contenido','$nombre', $fecha,'$dep_code','$usu_code','$nivel')"; break;
   case 'postgres': $sql21 = " INSERT INTO  SGD_PLAN_PLANTILLAS(  plan_plantilla, plan_nombre, plan_fecha, depe_codi, usua_codi, plan_tipo)
  VALUES ('$contenido','$nombre',$fecha,'$dep_code','$usu_code','$nivel')"; break;
  }
  $rsg   = $db->conn->Execute($sql21);
}

header('Location: index.php');
?>
