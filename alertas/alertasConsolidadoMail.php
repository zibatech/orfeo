<?php
/**
* @author Jairo Losada   <jlosada@gmail.com>
* @author Correlibre.org // Tomado de version orginal realizada por JL en SSPD, modificado.
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
* 
* @copyleft

OrfeoGpl / Version Argo Models are the data definition of Argo Information System
Copyright (C) 2017 Correlibre Fundacion.

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
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
$ruta_raiz = "/var/www/html/orfeo14";
include $ruta_raiz."/alertas/alertasTipoRadicadoConsolidadoMail.php";
//$ruta_raiz = "/var/www/html/orfeo47b";
//uta_raiz= "..";
?>
