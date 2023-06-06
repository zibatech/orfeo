<?php
session_start();
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

$ruta_raiz = "..";
if (!$_SESSION['dependencia'])
    header("Location: $ruta_raiz/cerrar_session.php");

$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$tip3Nombre=$_SESSION["tip3Nombre"];
$tip3desc = $_SESSION["tip3desc"];
$tip3img =$_SESSION["tip3img"];
foreach ($_POST as $key => $valor)   ${$key} = $valor;
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
error_reporting(7);
$verrad = "";
$nurad = isset($_GET['nurad']) ? $_GET['nurad'] : '';
function return_bytes($val)
{	$val = trim($val);
	$ultimo = strtolower($val{strlen($val)-1});
	switch($ultimo)
	{	// El modificador 'G' se encuentra disponible desde PHP 5.1.0
		case 'g':	$val *= 1024;
		case 'm':	$val *= 1024;
		case 'k':	$val *= 1024;
	}
	return $val;
}
?>
<HTML>
<head>
<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/cogeinsas.css">
<?php include_once "$ruta_raiz/js/funtionImage.php"; ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?=$ruta_raiz?>/estilos/bootstrap.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="<?=$ruta_raiz?>/radiMail/css/smartadmin-production-plugins.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="<?=$ruta_raiz?>/radiMail/css/smartadmin-skins.min.css">
<link rel="stylesheet" type="text/css" media="screen" href="<?=$ruta_raiz?>/radiMail/css/smartadmin-rtl.min.css">
<script src="<?=$ruta_raiz?>/js/plugin/dropzone/dropzone.min.js"></script>
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
</head>
<BODY>
<? include "$ruta_raiz/envios/paBuscar.php"; ?>
<?
if($Buscar AND $busq_radicados_tmp){
    include "$ruta_raiz/include/query/uploadFile/queryUploadFileRad.php";
	//$db->conn->debug=true;
    $rs=$db->conn->Execute($query);
    if ($rs->EOF)  {
        echo "<hr><center><b><span class='alarmas'>No se encuentra ningun radicado con el criterio de busqueda</span></center></b></hr>";
	 }else{
		$valRadio=$busqRadicados;
	}

}
	if($valRadio){
	?>	<!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-blueLight" id="wid-id-0" data-widget-editbutton="false">
                <header>
                    <h2>Radicado No. <?=$valRadio?> </h2>
                </header>
                <!-- widget div-->
                <div>
                    <!-- widget edit box -->
                    <div class="jarviswidget-editbox">
                        <!-- This area used as dropdown edit box -->
                    </div>
                        <form action="uploadAnex.php?<?=$encabezado?>" class="dropzone" id="mydropzone" enctype="multipart/form-data">

                            <div class="input-group"  style=" position:absolute; bottom:5px; right:10px;">
                                <textarea name="observa" id="observa" cols=70 rows=3 class="form-control" maxlength='100'> Anexo: </textarea>
                            </div>

                            <div class="dz-message" data-dz-message><span>Arrastre los documentos aquí</span><br>
                                <span><b>Los formatos que se tendrán en cuenta son: doc xls ppt tif jpg gif pdf txt zip rtf dia zargo csv odt ods xml png docx avi mpg tar xlsx rar 7z pptx msg mp3 mp4 xlsm eml xlsb</b></span></div>
                            <input type=checkbox name=chkNivel checked class=ebutton style="display:none">
                            <input type='hidden' name=depsel8 value='<?=$depsel8?>'>
                            <input type='hidden' name=codTx value='<?=$codTx?>'>
                            <input type='hidden' name=EnviaraV value='<?=$EnviaraV?>'>
                            <input type='hidden' name=fechaAgenda value='<?=$fechaAgenda?>'>
                            <input type=hidden name=enviar value=enviarsi>
                            <input type=hidden name=enviara value='9'>
                            <input type=hidden name="Realizar"  value="Realizar">
                            <input type=hidden name=carpeta value=12>
                            <input type=hidden name=carpper value=10001>
                            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo return_bytes(ini_get('upload_max_filesize')); ?>"><br>
                            <input type="hidden" name="replace" value="y">
							<input type="hidden" name="valRadio" value="<?=$valRadio?>">
							<input name="check" type="hidden" value="y" checked>
                            <input type='hidden' name=depsel value='<?=$depsel?>'>
                        </form>
                        <div class=listado2 colspan="2">
                            <center>
                                <input class="btn btn-primary btn-sm"
                                type="button"
                                onclick="window.location.href='../verradicado.php?verrad=<?=$valRadio?>';"
                                value="Consultar" />
                            </center>
                        </div>
                    </div>
                    <!-- end widget content -->                                                                                                          </div>
                <!-- end widget div -->
            </div>
            <!-- end widget -->
<?	}
?>
</BODY>
</HTML>
