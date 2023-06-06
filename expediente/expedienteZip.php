<?php
error_reporting(7);

/**
* @author Jairo Losada   <jlosada@gmail.com>
* @author Correlibre.org // 
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
* 
* @copyleft

OrfeoGpl Models are the data definition of OrfeoGpl Information System
Copyright (C) 2017 Correlibre.org.

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

$entidad=$_SESSION["entidad"]; 

$_SESSION['numExpedienteSelected'] = null;

  include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");
  include_once "$ruta_raiz/class_control/anexo.php";
  if (!$db) $db = new ConnectionHandler($ruta_raiz);
  $db->conn->debug = false;
  $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
  
  $anexo = new Anexo($db);
  
  
  
?>  
<form method="post" >
Numero Expediente a Descargar
<input type="text" id="numExpediente" name="numExpediente" value="<?=$numExpediente?>"  />


</form>



<?php

if($numExpediente){
  $iSql = "SELECT exp.sgd_exp_numero, r.radi_nume_radi, r.radi_path, r.ra_asun radi_asunto, r.radi_nomb
           FROM
             sgd_exp_expediente exp left outer join radicado r
               ON (exp.radi_nume_radi=r.radi_nume_radi)
           WHERE sgd_exp_numero='$numExpediente'";  
  
  $rsExp = $db->conn->query($iSql);
  echo "$numExpediente<BR>";
  $cpShell = "mkdir $CONTENT_PATH/tmp/zipExps/";
  $salida = shell_exec($cpShell);
  $file = $numExpediente. "_".date("Ymd_h24is").".zip";
  $archivoZip = "$CONTENT_PATH"."tmp/zipExps/$file";
  $archivoZipX = "../bodega/tmp/zipExps/$file";
  $zip = new ZipArchive();
  
  if($zip->open($archivoZip,ZIPARCHIVE::CREATE)===true) {
    
  while(!$rsExp->EOF){
    $radiPath = $rsExp->fields["RADI_PATH"];
    $radiAsunto = $rsExp->fields["RADI_ASUNTO"];
    $radicado = $rsExp->fields["RADI_NUME_RADI"];
    $fileRad = $CONTENT_PATH."/".$radiPath;
    $fileRad = str_replace("//", "/", $fileRad);
    $fileRad = str_replace("//", "/", $fileRad);
    
  
    
    
    
    if(is_file($fileRad)) {
        
        $fileRadX = array_pop(explode("/", $fileRad));
        $fileRadXArr = explode(".", $fileRadX);
        $radiAsunto = substr(str_replace(" ","_",$radiAsunto),0, 65);
        $radiAsunto = substr(str_replace("/","_",$radiAsunto),0, 65);
        $fileRadXFinal = $fileRadXArr[0]."_".$radiAsunto.".".array_pop($fileRadXArr);
        $fileRadXFinal = str_replace("_-_","_",$fileRadXFinal);
        $zip->addFile($fileRad, $fileRadXFinal);
        echo "Ok. $fileRadXFinal \n";
        
        
        
    }else{
      echo "*** Archivo No se encuentra '$fileRad' '$radiAsunto'\n";
    }  
    
    $anexo->anexosRadicado($radicado, true);
    $anexosPaths = $anexo->path_anexos;
    if(count($anexo->codi_anexos)>=1){
     foreach($anexosPaths as $key => $path) {
        
         $tprCodigo = $anexo->tpr_codigo[$key];
         if($tprCodigo==919){
            
            $pathAnexo = $CONTENT_PATH . str_replace("/bodega/", "", $path);
            $ext = array_pop(explode(".", $pathAnexo));
            $fileRadXFinalAnexo = "A_"."$key"."_RECIBIDO_A_SATISFACCION.".$ext;
            if(!is_file($pathAnexo)) {
               echo "<br>*** ***No se encuetra Anexo $fileRadXFinalAnexo\n";
              }else{
               echo "<br>$fileRadXFinalAnexo --- $tprCodigo -- $pathAnexo \n"; 
               $zip->addFile($pathAnexo, $fileRadXFinalAnexo);
            }   
         }
       
     }
     
     
    }
    
    
    
    echo "<br>";    
    $rsExp->MoveNext();  
  }
 
  }
   $zip->close();

  
  
  
  ?>
  <br>
   <a href="<?=$archivoZipX ?>" target="<?=$archivoZip?>" >Descargar zip</a>
  
  <?php
  
}

?>

