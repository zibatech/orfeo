<?php
/**
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @author Jairo Losada   <jlosada@gmail.com>
 * @author Correlibre.org
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright
 *
 * OrfeoGpl Models are the data definition of OrfeoGpl Information System
 * Copyright (C) 2013 Infometrika Ltda.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

session_start();

foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}

$ruta_raiz = "..";

if (!$_SESSION['dependencia'])
    header("Location: $ruta_raiz/cerrar_session.php");

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
include_once("$ruta_raiz/include/combos.php");

if (!$db)   $db = new ConnectionHandler($ruta_raiz);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

/**
 * Retorna la cantidad de bytes de una expresion como 7M, 4G u 8K.
 *
 * @param char $var
 * @return numeric
 */
function return_bytes($val)
{   $val = trim($val);
    $ultimo = strtolower($val{strlen($val)-1});
    switch($ultimo)
    {   // El modificador 'G' se encuentra disponible desde PHP 5.1.0
        case 'g':   $val *= 1024;
        case 'm':   $val *= 1024;
        case 'k':   $val *= 1024;
    }
    return $val;
}

if(!$tipo) $tipo = 3;

$paramsTRD=$phpsession."&krd=$krd&codiEst=$codiEst&dependencia=$dependencia&usua_nomb=$usua_nomb&"
                ."depe_nomb=$depe_nomb&usua_doc=$usua_doc&codusuario=$codusuario";

$params="dependencia=$dependencia&codiEst=$codiEst&usua_nomb=$usua_nomb&depe_nomb=$depe_nomb&usua_doc=$usua_doc&tipo=$tipo&codusuario=$codusuario";

$coddepe=$_SESSION['dependencia'];

if($codEmp!=0){
    $queryTRD = "select distinct sgd_tidm_codi AS CODIESTR from sgd_cob_campobliga
        where sgd_tidm_codi = '$codEmp'";
    $rsTRD=$db->conn->query($queryTRD);
    if($rsTRD){
        $codiEst = $rsTRD->fields['CODIESTR'];
    }
}

$num_car = 4;

$comentarioDev = "Despliega las Posibles Estructuras";
include "$ruta_raiz/include/tx/ComentarioTx.php";
?>



<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
    <script language="JavaScript" type="text/JavaScript">
    /**
    * Valida que el formulario desplegado se encuentre adecuadamente diligenciado
    */
    function validar() {
        archDocto = document.formAdjuntarArchivos.archivoPlantilla.value;
        if ( (archDocto.substring(archDocto.length-1-3,archDocto.length)).indexOf(".xls") == -1){
            alert ("El archivo de datos debe ser .xls");
            return false;
        }

        if (document.formAdjuntarArchivos.archivoPlantilla.value.length<1){
            alert ("Debe ingresar el archivo XLS con los datos");
            return false;
        }

        return true;
    }

    function enviar() {

        if (!validar())
            return;

        document.formAdjuntarArchivos.accion.value="PRUEBA";
        document.formAdjuntarArchivos.submit();
    }

    </script>
</head>
<body>
<div class='container'>
    <div class="col-sm-12">
        <!-- widget grid -->
        <section id="widget-grid">
            <!-- row -->
            <div class="row">
              <!-- NEW WIDGET START -->
              <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <!-- Widget ID (each widget will need unique ID)-->
                <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">
                  <header>
                    <h2>
                      Cargue de resultado envios<br>
                      <small><?=$tituloCrear ?></small>
                    </h2>
                  </header>
                  <!-- widget content -->
                  <div class="widget-body">
                    <form name = formaTRD action="uploadPlanos.php?<?=$paramsTRD?>" method="post">
                        <table class="table" style="display: none">
                            <tr align="center">
                                <td height="35" colspan="2" class="titulos4">Cargar acuse de recibo</td>
                            </tr>
                            <tr align="center" >
                                <td width="36%" class="titulos2">EMPRESA</td>
                                <td width="64%" height="35" class="listado2">
                                    <select name="codEmp" onchange="submit()" class="select">
                                        <option value="0">-- Seleccione --</option>
                                        <option value="2"<?if($codEmp == 2){echo "selected";}?>>Generica</option>
                                        <option value="3" <?if($codEmp == 3){echo "selected";}?>>4-72</option>
                                    </select>
                                </td>
                            </tr>
                       </table>
                    </form>
                    <form action="adjuntar_PlanoEnvio.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formAdjuntarArchivos">
                        <input type=hidden name=<?=session_name()?>  value='<?=session_id()?>'>
                        <input type=hidden name=codiEst value='<?=$codiEst?>'>
                        <table class="table">
                            <tr align="center">
                                <td height="25" colspan="2" class="titulos4">
                                    SELECCIONAR ARCHIVO PLANO
                                    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo return_bytes(ini_get('upload_max_filesize')); ?>">
                                    <input name="accion" type="hidden" id="accion">
                                </td>
                            </tr>
                             <tr align="center">
                                <td width="10%" class="titulos2">Plantilla carga </td>
                                <td width="90%" height="30" class="listado2">
                                   <a href="../bodega/plantillas/Formato_masivo_planillas_guias.xls" target="_blank">Descargar pantilla base</a>
                                </td>
                            </tr>
                            <tr align="center">
                                <td width="16%" class="titulos2">ARCHIVO </td>
                                <td width="84%" height="30" class="listado2">
                                    <input name="archivoPlantilla" type="file" value='<?=$archivoPlantilla?>' class="tex_area"  id=archivoPlantilla accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                                </td>
                            </tr>
                            <tr align="center">
                                <td height="30" colspan="2" class="celdaGris">
                                    <span class="celdaGris"> <span class="e_texto1">
                                    <input name="enviaPrueba" type="button"  class="botones" id="envia22"  onClick="enviar();" value="Cargar">
                                    </span></span>
                                </td>
                            </tr>
                            <tr align="center">
                                <td height="30" colspan="2" class="celdaGris">
                                    Esta operaci&oacute;n permite registrar
                                    la informaci&oacute;n suministrada por la empresa de
                                                        Correo.
                                </td>
                            </tr>
                        </table>
                    </form>
                  </div>
                </div>
              </article>
            </div>
        </section>
    </div>
</div>
</body>
</html>
