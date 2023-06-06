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

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
//print_r($_FILES);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$login       = $_SESSION["krd"];

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
$db = new ConnectionHandler("$ruta_raiz");
$fecha = $db->conn->DBTimeStamp(time());

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


if(!is_dir("$ruta_raiz/bodega/tmp")){
    try {
        mkdir("$ruta_raiz/bodega/tmp", 0765, true);
    }
    catch (Exception $e) {
        die($e->getMessage());
    }
}

$depe_dir = substr($noExpediente,4,$_SESSION['digitosDependencia']);

$path     = $_FILES['file']['name'];
$ext      = pathinfo($path, PATHINFO_EXTENSION);
$namefile = $noExpediente.'_'.strtotime("now") . '.'  . $ext;

$uploadDir = "$ruta_raiz/bodega/".substr($noExpediente,0,4)."/".$depe_dir."/docs/";

$my_upload->upload_dir = $uploadDir; // "files" is the folder for the uploaded files (you have to create this folder)
$my_upload->the_temp_file = $_FILES['file']['tmp_name'];
$my_upload->the_file = $namefile;
$my_upload->http_error = $_FILES['file']['error'];
$my_upload->replace =  "y"; // because only a checked checkboxes is true

$my_upload->extensions = $exts;
$my_upload->max_length_filename = 50; // change this value to fit your field length in your database (standard 100)

if ($my_upload->upload()){
    $full_path = $my_upload->upload_dir.$my_upload->file_copy;
    $info = $my_upload->get_uploaded_file_info($full_path);
    $anex_hash=hash_file('sha256',$full_path);
}else{
    die("Ocurrio un Error no se pudo cargar el archivo".$my_upload->show_error_string().nl2br($info));
}

$id = time();
$size = return_bytes($_FILES['file']['size']);
$tipo = array_search(strtolower('.'.$ext), $exts);

$sqlI = "insert into sgd_exp_anexos
    (id
    ,exp_anex_creador
    ,exp_anex_nomb_archivo
    ,exp_anex_radi_fech
    ,exp_anex_tamano
    ,exp_anex_tipo
    ,exp_anex_desc
    ,exp_anex_borrado)
     values (
       {$id}
    , '{$login}'
    , '{$namefile}'
    ,  {$fecha}
    , '{$size}'
    ,  {$tipo}
    , '{$path}'
    , 'N' )";

$insertSQL = $db->conn->query($sqlI);

if(empty($insertSQL)){
    die('No se puedo crear el registro en la base de datos');
}
