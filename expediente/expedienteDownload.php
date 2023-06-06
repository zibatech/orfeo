<?php
//echo "Entrooooo";
error_reporting(7);
/**
* @author Jairo Losada   <jlosada@gmail.com>
* @author Correlibre.org // 
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
* 
* @copyleft

OrfeoGpl Models are the data definition of OrfeoGpl Information System
Copyright (C) 2018 Correlibre.org.

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
if($_POST['radicado_a_buscar']){$radicados_a_buscar = $_POST['radicado_a_buscar'];}

$ruta_raiz = "..";
if (!$_SESSION['dependencia'])
header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

define('ADODB_ASSOC_CASE', 1);

$verrad         = "";
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
$tip3Nombre     = $_SESSION["tip3Nombre"];
$tip3desc       = $_SESSION["tip3desc"];
$tip3img        = $_SESSION["tip3img"];
$descCarpetasGen= $_SESSION["descCarpetasGen"] ;
$descCarpetasPer= $_SESSION["descCarpetasPer"];
$verradPermisos = "Full"; //Variable necesaria en tx/txorfeo para mostrar dependencias en transacciones
$CONTENT_PATH = $_SESSION["CONTENT_PATH"];
$CONTENT_PATH = $ruta_raiz."/bodega/";

$entidad=$_SESSION["entidad"]; 
//echo $CONTENT_PATH;
$_SESSION['numExpedienteSelected'] = null;

  include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
  include_once "$ruta_raiz/class_control/anexo.php";
  if (!$db) $db = new ConnectionHandler($ruta_raiz);
  $db->conn->debug = false;
  $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
  
  $anexo = new Anexo($db);
  
  
  
?>  
<header>
<?php include_once $ruta_raiz."/htmlheader.inc.php"; ?>
</header>
<form method="post" >
<center><b></b></center>
<br>
<div style="width:400px;">
<TABLE align="center" width="40%" class="table table-bordered table-hover dataTable no-footer smart-form">
<tr>
  <th colspan="2">
    <center>Descarga de documentos de expediente
  </th>
</tr>
<tr>
  <th>
    Usuario
  </th>
  <td>
   <?php
     echo $_SESSION["usua_nomb"]. "(".$_SESSION["krd"].")";
   ?>
  </td>
</tr>
<tr>
  <th>
    Area
  </th>
  <td>
   <?php
     echo $_SESSION["depe_nomb"]. "(".$_SESSION["dependencia"].")";
   ?>
  </td>
</tr>

<tr>
  <th>
    Numero Expediente a Descargar
  </th>
  <td>
   <input type="text" id="numExpediente" name="numExpediente" value="<?=$numExpediente?>"  /><br>
  </td>
</tr>
<tr>
  <th>
    Observaciones
  </th>
  <td>
   <textarea name="observa" rows="5" cols="80" ></textarea>
  </td>
</tr>
<tr>
  <th colspan="2">
    <center><input type="submit" name="Enviar" class="btn btn-success" align="center"></center>
  </th>
</tr>
</table>
</div>
</form>



<?php
$CONTENT_PATH = $ruta_raiz."/bodega/";
if($numExpediente){
   $numExpediente = strtoupper(trim($numExpediente));
  $iSql = "SELECT exp.sgd_exp_numero, r.radi_nume_radi, r.radi_path, r.ra_asun radi_asunto, r.radi_nomb, r.radi_fech_radi, r.sgd_spub_codigo
           FROM
             sgd_exp_expediente exp left outer join radicado r
               ON (exp.radi_nume_radi=r.radi_nume_radi)
           WHERE (sgd_eanu_codigo not in (1,2) or sgd_eanu_codigo is null)and sgd_exp_numero='$numExpediente'";  
  //$db->conn->debug = true;
  $rsExp = $db->conn->query($iSql);
  echo "$numExpediente<BR>";
  if(!$rsExp->EOF){
  $observa=$_REQUEST["observa"];
  include_once "$ruta_raiz/include/tx/Historico.php";
  $radicados[] = "";
  $tipoTx = 62;
  //$db->conn->debug = true;
  $Historico = new Historico($db);
  $Historico->insertarHistoricoExp($numExpediente, $radicados, $dependencia, $codusuario, $observa, $tipoTx, 0);

  $cpShell = "mkdir $CONTENT_PATH/tmp/zipExps/";
  $salida = shell_exec($cpShell);
  $file = $numExpediente. "_".date("Ymd_h24is").".zip";
  $archivoZip = "$CONTENT_PATH"."tmp/zipExps/$file";
  $archivoZipX = "../bodega/tmp/zipExps/$file";
  $zip = new ZipArchive();
  $logExpediente = "Documentos del Expediente No $numExpediente\n";
  if($zip->open($archivoZip,ZIPARCHIVE::CREATE)===true) {
    
  while(!$rsExp->EOF){
    $radiPath = $rsExp->fields["RADI_PATH"];
    $radiAsunto = $rsExp->fields["RADI_ASUNTO"];
    $radiAsunto =  substr($radiAsunto,0,71);
    $radicado = $rsExp->fields["RADI_NUME_RADI"];
    $radPublico = $rsExp->fields["SGD_SPUB_CODIGO"];
    $radiFech = substr($rsExp->fields["RADI_FECH_RADI"],0, 16);
    $fileRad = $CONTENT_PATH."/".$radiPath;
    $fileRad = str_replace("//", "/", $fileRad);
    $fileRad = str_replace("//", "/", $fileRad);
    $ext = array_pop(explode(".",$radiPath));
    if($radPublico==0){
    if((is_file($fileRad) && $ext!="docx" && $ext!="doc" && $ext!="odt")   ) {
         
        $fileRadX = array_pop(explode("/", $fileRad));
        $fileRadXArr = explode(".", $fileRadX);
        $radiAsunto = substr(str_replace(" ","_",$radiAsunto),0, 65);
        $radiAsunto = substr(str_replace("/","_",$radiAsunto),0, 65);
        $fileRadXFinal = $fileRadXArr[0]."_".$radiAsunto.".".array_pop($fileRadXArr);
        $fileRadXFinal = str_replace("_-_","_",$fileRadXFinal);
        $zip->addFile($fileRad, $fileRadXFinal);
        $logExpediente .= str_pad($fileRadXFinal, 60, " ", STR_PAD_RIGHT) . "\t\tDocumento No. $radicado \tFecha: $radiFech  Asunto: $radiAsunto \n";
        $logExpedienteTable .= "<tr><td>".$fileRadXFinal . "</td><td>Documento No. $radicado </td><td> $radiFech </td><td> Asunto: $radiAsunto </td></tr>";

    }else{
        $logExpediente .= str_pad("Documento Sin Imagen Digitalizada", 60, " ", STR_PAD_RIGHT) . "\t\tDocumento No. $radicado \tFecha: $radiFech  $radiAsunto \n";
        $logExpedienteTable .= "<tr><td>Documento Sin Imagen Digitalizada</td><td>Documento No. $radicado </td><td>$radiFech </td><td>$radiAsunto </td></tr>";
    }
    }else{
      /* Si el radicado es privado
       */
        //$logExpediente .= str_pad("Documento sin permisos.", 60, " ", STR_PAD_RIGHT) . "\t\tDocumento No. $radicado \tFecha: $radiFech  $radiAsunto \n";
        $logExpedienteTable .= "<tr><td>Documento Sin permisos.</td><td>Documento No. $radicado </td><td>$radiFech </td><td>$radiAsunto </td></tr>";
      

    }
    $anexo->anexosRadicado($radicado, true);
    $anexosPaths = $anexo->path_anexos;  // 1 14 18 
    if(count($anexo->codi_anexos)>=1){
     $dataAnexos = $anexo->dataAnexos;
     foreach($anexosPaths as $key => $path) {
        
         $tprCodigo = $anexo->tpr_codigo[$key];
         
         $anexTipo     = $dataAnexos[$key]["anex_tipo"];
         $anexBorrado  = $dataAnexos[$key]["anex_borrado"];
         $anexRadSalida= $dataAnexos[$key]["anex_salida"];
         $anexDesc  = substr($dataAnexos[$key]["anex_desc"],0,71);
         // (($anexTipo!=18 || $anexTipo!=1) && $anexRadSalida==0) // Si el radicado es diferente a docx y doc y ademas no es un radicado de salida.
         if($anexTipo!=1 && $anexTipo!=14 && (($anexTipo!=18 || $anexTipo!=1) && $anexRadSalida==0) && $anexBorrado != "S"){
            
            $pathAnexo = $CONTENT_PATH . str_replace("/bodega/", "", $path);
            $ext = array_pop(explode(".", $pathAnexo));
            $fileRadXFinalAnexo = "A_"."$key".".".$ext;
            if(!is_file($pathAnexo)) {
               $logExpedienteTable .= "<tr><td></td><td>No se encuetra Anexo $fileRadXFinalAnexo</td></tr>";
              }else{
               $zip->addFile($pathAnexo, $fileRadXFinalAnexo);
               $logExpediente .= "\t".str_pad($fileRadXFinalAnexo, 60, " ", STR_PAD_RIGHT) . "\tAnexo a Doc.  $radicado\tDescripcion Anexo: $anexDesc \n";
               $logExpedienteTable .= "<tr><td>$fileRadXFinalAnexo</td><td>Anexo a Doc.  $radicado</td><td>Descripcion Anexo: $anexDesc </td></tr>";
            }
         }
     }
    }
    $rsExp->MoveNext();  
  }
 }
}
  $zip->addFromString('DetalleExpediente.txt', $logExpediente);
  $zip->close();
  ?>
  <br>
   <a href="<?=$archivoZipX ?>" target="<?=$archivoZip?>" >Descargar zip</a>
  <table class="table table-bordered table-hover">
   <tr><th>Documento</th><th>Numero de Documento / Radicado </th><th>Fecha Radicado </th><th>$radiAsunto </th></tr>
   <?=$logExpedienteTable?>
  </table>
  <?php
  
}


$_SESSION["tableInformeRadicados"] = $logExpedienteTable;
?>
<a href="expedienteDownloadPrint.php" target="expediente<?=$numExpediente?>">Hoja detalle impresion</a>

