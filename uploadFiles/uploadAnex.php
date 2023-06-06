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

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];

$ruta_raiz = "..";

/**
 * Retorna la cantidad de bytes de una expresion como 7M, 4G u 8K.
 * @param char $var
 * @return numeric
 */
function return_bytes($val){
    $val = trim($val);
    $ultimo = strtolower($val{strlen($val)-1});
    switch($ultimo){
        // El modificador 'G' se encuentra disponible desde PHP 5.1.0
    case 'g':	$val *= 1024;
    case 'm':	$val *= 1024;
    case 'k':	$val *= 1024;
    }
    return $val;
}

/* Realizar transacciones
 * este archivo realiza las transacciones de radicados en orfeo.
 * inclusion de archivos para utilizar la libreria adodb
 */

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
include_once("$ruta_raiz/class_control/anexo.php");
$db = new ConnectionHandler("$ruta_raiz");
$anex       =  new Anexo($db);

/*  filtro de datos
 *  $setfiltroselect  contiene los valores digitados por el usuario separados por coma.
 *  $filtroselect si setfiltoselect contiene algunvalor la siguiente rutina realiza el arreglo de la condicion para la consulta a la base de datos y lo almacena en wherefiltro.
 *  $wherefiltro  si filtroselect trae valor la rutina del where para este filtro es almacenado aqui.
 */

if($checkValue) {
    $num = count($checkValue);
    $i = 0;
    while ($i < $num) {
        $record_id = key($checkValue);
        $setFiltroSelect .= $record_id ;
        $radicadosSel[] = $record_id;
        if($i<=($num-2))
        {
            $setFiltroSelect .= ",";
        }
        next($checkValue);
        $i++;
    }
    if ($radicadosSel) {
        $whereFiltro = " and b.radi_nume_radi in($setFiltroSelect)";
    }
}

if($setFiltroSelect) {
    $filtroSelect = $setFiltroSelect;
}

$causaAccion = "Agregar Anexo a Radicado";

/*Número de anexo actual.*/
$anexNumero=$anex->obtenerMaximoNumeroAnexoConCopias($valRadio)+1;

/* Se traen los anexos_tipo */
$sql_anexosTipo="select anex_tipo_ext from anexos_tipo";
$rs_anexosTipo=$db->conn->GetArray($sql_anexosTipo);

foreach ($rs_anexosTipo as $item){
    $exts[]=".".$item["ANEX_TIPO_EXT"];
}

$ruta_raiz = "..";
include ("$ruta_raiz/include/upload/upload_class.php");
$max_size = return_bytes(ini_get('upload_max_filesize')); // the max. size for uploading
$my_upload = new file_upload;
$my_upload->language="es";
$my_upload->upload_dir = "$ruta_raiz/bodega/tmp/"; // "files" is the folder for the uploaded files (you have to create this folder)
$my_upload->extensions = $exts;
$my_upload->max_length_filename = 50; // change this value to fit your field length in your database (standard 100)
$my_upload->rename_file = true;

if(isset($Realizar)){
    $tmpFile = trim($_FILES['file']['name']);
    $anex_codigo=trim($valRadio) . trim(str_pad($anexNumero, 5, "0", STR_PAD_LEFT));
    $newFile = $valRadio."_".trim(str_pad($anexNumero, 5, "0", STR_PAD_LEFT));
    if ($anex->existeAnexo($anex_codigo)){
        $newFile = $valRadio."_".trim(str_pad($anexNumero+1, 5, "0", STR_PAD_LEFT));
    }
    $depe_dir = substr($valRadio,4,$_SESSION['digitosDependencia']);
    $depe_dir += 0;
    $uploadDir = "$ruta_raiz/bodega/".substr($valRadio,0,4)."/".$depe_dir."/docs/";
    $fileGrb = substr($valRadio,0,4)."/".$depe_dir."/$valRadio".".".end(explode(".",$tmpFile));
    $my_upload->upload_dir = $uploadDir;
    $my_upload->the_temp_file = $_FILES['file']['tmp_name'];
    $my_upload->the_file = $_FILES['file']['name'];
    $my_upload->http_error = $_FILES['file']['error'];
    $my_upload->replace = (isset($_POST['replace'])) ? $_POST['replace'] : "n"; // because only a checked checkboxes is true
    $my_upload->do_filename_check = (isset($_POST['check'])) ? $_POST['check'] : "n"; // use this boolean to check for a valid filename

    $newFile;
    $newFile="1".$newFile;
    if ($my_upload->upload($newFile)) {
        $full_path = $my_upload->upload_dir.$my_upload->file_copy;
        $info = $my_upload->get_uploaded_file_info($full_path);
        $anex_hash=hash_file('sha256',$full_path);
    }else{
        die("Ocurrio un Error la Fila no fue cargada Correctamente ".$my_upload->show_error_string().nl2br($info));
    }
}

$anex->anex_radi_nume=$valRadio;
$anex->anex_nomb_archivo=$tmpFile;
$anex->anex_tamano="50";
$anex->anex_creador=$krd;
$anex->anex_desc=$observa;
if($anex->anexarFilaRadicado()!=-1)
{
    $radicadosSel[] = $valRadio;
    $codTx = 42;//Codigo de la transaccion
    include "$ruta_raiz/include/tx/Historico.php";
    $isql = "update
        anexos set
        anex_hash='$anex_hash'
        where
        anex_codigo= '$anex_codigo'";

    $db->conn->query($isql);
    $hist = new Historico($db);
    $observa.=" con codigo de seguridad: ".$anex_hash;
    $hist->insertarHistorico($radicadosSel,  $dependencia , $codusuario, $dependencia, $codusuario, $observa, $codTx);
    $isql = "update
        anexos set
        anex_hash='$anex_hash'
        where
        anex_codigo= '$anex_codigo'";
    $db->conn->query($isql);

}else{
    echo "No actualizo la BD";
}
