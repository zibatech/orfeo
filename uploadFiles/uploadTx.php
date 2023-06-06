<?
session_start();
/*
 * Lista Subseries documentales
 * @autor Jairo Losada Correlibre/Supersolidaria/Infometika
 * @fecha 2009/06 Modificacion Variables Globales.
 * @modificacion Se incluyen aSociacion de anexos
 */
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$CONTENT_PATH=$_SESSION["CONTENT_PATH"];
$ruta_raiz   = "..";
/**
 * Retorna la cantidad de bytes de una expresion como 7M, 4G u 8K.
 *
 * @param char $var
 * @return numeric
 */
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

/*  REALIZAR TRANSACCIONES
 *  Este archivo realiza las transacciones de radicados en Orfeo.
 */
?>
<html>
<head>
<title>Realizar Transaccion - Orfeo </title>
<?php
include_once($ruta_raiz."/htmlheader.inc.php");
?>
</head>
<?
/**
  * Inclusi/dbon de archivos para utilizar la libreria ADODB
  *
  */
   require_once("$ruta_raiz/include/db/ConnectionHandler.php");
   require_once("$ruta_raiz/processConfig.php");

   $db = new ConnectionHandler("$ruta_raiz");

   /*
	* Genreamos el encabezado que envia las variable a la paginas siguientes.
	* Por problemas en las sesiones enviamos el usuario.
	* @$encabezado  Incluye las variables que deben enviarse a la singuiente pagina.
	* @$linkPagina  Link en caso de recarga de esta pagina.
	*/
	$encabezado = "".session_name()."=".session_id()."&krd=$krd&depeBuscada=$depeBuscada&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion";

/*  FILTRO DE DATOS
 *  @$setFiltroSelect  Contiene los valores digitados por el usuario separados por coma.
 *  @$filtroSelect Si SetfiltoSelect contiene algunvalor la siguiente rutina realiza el arreglo de la condicion para la consulta a la base de datos y lo almacena en whereFiltro.
 *  @$whereFiltro  Si filtroSelect trae valor la rutina del where para este filtro es almacenado aqui.
 *
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

//echo "<hr>$filtroSelect<hr>";
//session_start();

//if (!$dependencia or !$nivelus)  include "./rec_session.php";
if($asocImgAnexo!="") $causaAccion = $asocImgAnexo;
  else $causaAccion = $asocImgRad;

if($asocImgRad){
	 $classTr = "aler alert-success";
	}else{
	 $classTr = "alert alert-block alert-info";
}
?>
<body>
<br>
<?
/**
 * Aqui se intenta subir el archivo al sitio original
 *
 */
$ruta_raiz = "..";
include ("$ruta_raiz/include/upload/upload_class.php"); //classes is the map where the class file is stored (one above the root)
include ("$ruta_raiz/class_control/anexo.php");  // Clase para agregar anexos
$max_size = return_bytes(ini_get('upload_max_filesize')); // the max. size for uploading
$my_upload = new file_upload;
$my_upload->language="es";
$my_upload->upload_dir = $CONTENT_PATH."tmp/"; // "files" is the folder for the uploaded files (you have to create this folder)
$my_upload->extensions = array(".tif",".tiff",".jpeg", ".pdf", ".jpg", ".odt", ".html",".doc",".xls",".docx",".xlsx", ".gif",".htm",".png",".svg",".pptx",".ppt",".odp",".csv",".txt",".zip",".rar",".xml"); // specify the allowed extensions here
//$my_upload->extensions = "de"; // use this to switch the messages into an other language (translate first!!!)
$my_upload->max_length_filename = 150; // change this value to fit your field length in your database (standard 100)
$my_upload->rename_file = true;


if(isset($_POST['Realizar'])) {
	$tmpFile = strtolower(trim($_FILES['upload']['name']));
	$newFile = $valRadio;

//cho "<hr> $valRadio <hr>";
	//Trick to remove leading zeros from dependencia for the upload dir
    $depe_dir = substr($valRadio,4,$_SESSION['digitosDependencia']);
    $depe_dir = ltrim($depe_dir, '0');

	$uploadDir = $CONTENT_PATH.substr($valRadio,0,4)."/".$depe_dir."/docs/";
	$ext =  array_pop(explode(".",$tmpFile));
	$fileGrb = substr($valRadio,0,4)."/".$depe_dir."/docs/$valRadio".".".$ext;
    $fileGrb = strtolower($fileGrb);
    $my_upload->the_file = $_FILES['upload']['name'];

    if($asocImgAnexo){
        $anexo = new Anexo($db);
        //$anexo->anex_nomb_archivo = $tmpFile;
        $anexo->anex_nomb_archivo = $fileGrb;
        $anexo->anex_radi_nume = $valRadio;
        $anexo->anex_tamano = number_format((filesize($_FILES['upload']['tmp_name'])/1024),2, '.','');
        $anexo->anex_solo_lect = "'N'";
        $anexo->anex_creador = $_SESSION["krd"];
        $anexo->anex_desc = $observa;
        $anexo->anex_borrado = "'N'";
        $anexo->rename_file = true;
        $numeroAnexo = $anexo->anexarFilaRadicado();

        $uploadDir = $CONTENT_PATH.$anexo->uploadDir;


        $fileGrb = $anexo->anexoRutaArchivo;
        $nuevoArchivo = str_replace($anexo->uploadDir, "", "".$fileGrb );
        $my_upload->the_file = $nuevoArchivo;


        //$anexo->anex_tamano = filesize($nuevoArchivo);
        //$newFile = "../bodega".$fileGrb;
        $anexo->obtenerTipoExtension($tmpFile);
        $newFile = str_replace(".".$anexo->anexoExtension, "", $nuevoArchivo);
    }

	$my_upload->upload_dir = $uploadDir;
	$my_upload->the_temp_file = $_FILES['upload']['tmp_name'];
	$my_upload->http_error = $_FILES['upload']['error'];
	$my_upload->replace = (isset($_POST['replace'])) ? $_POST['replace'] : "n"; // because only a checked checkboxes is true
	$my_upload->do_filename_check = (isset($_POST['check'])) ? $_POST['check'] : "n"; // use this boolean to check for a valid filename

	if ($my_upload->upload($newFile)) {
		// new name is an additional filename information, use this to rename the uploaded file
		$full_path = $my_upload->upload_dir.$my_upload->file_copy;
		//$info = $my_upload->get_uploaded_file_info($full_path);
    $info = "Nombre de Archivo: " . $valRadio . ".pdf<br>Tamaño de Archivo: " . number_format((filesize($_FILES['upload']['tmp_name'])/1024),2, '.','') . " KB"; 

		// ... or do something like insert the filename to the database
		$img_hash=hash_file('sha256',$full_path);

    $tipo_radicado = substr($valRadio, -1);
/*    if($tipo_radicado == 2) {

        //Firma para Entradas
        $firmasd = $ABSOL_PATH.'/bodega/firmas/';

        $P12_FILE =  $firmasd . 'server.p12';
        $usua_doc = $_SESSION["usua_doc"];

        if (!file_exists($P12_FILE)) {
            $P12_FILE = $firmasd . $usua_doc . '.p12';
        }

        if ($P12_PASS) {
            $clave = $P12_PASS;
        }

        $commandFirmado='java -jar '.$ABSOL_PATH.'include/jsignpdf-1.6.4/JSignPdf.jar ' . $full_path . " -kst PKCS12 -ksf " . $P12_FILE . ' -ksp ' . $clave . ' --font-size 7 -r \'Firmado al asociar imagen en CRA\' -V -v -llx 0 -lly 0 -urx 550 -ury 27 -d ' . $uploadDir;// . ' -ta PASSWORD -ts ' . $tsUrlTimeStamp . ' -tsu ' . $tsuUserTimeStamp . ' -tsp ' . $tspPasswordTimeStamp;


        $out = null;
        $ret = null;
        $inf = exec($commandFirmado,$out,$ret);

        // si falla la ejecución de jsign guardar error en bodega/jsignpdf.log
        if ($ret != 0) {
            $out = implode(PHP_EOL, $out);
            error_log(date(DATE_ATOM)." ".basename(__FILE__)." ($ret)  $valRadio: $out\n",3,"$ABSOL_PATH/bodega/jsignpdf.log");

            error_log(date(DATE_ATOM)." ".basename(__FILE__)."  comando $commandFirmado \n",3,"$ABSOL_PATH/bodega/jsignpdf.log");


            die("<table class='table table-bordered'><tr><td class=titulosError>Ocurrio un error al firmar el documento<br><blockquote></blockquote></td></tr></table>");
        } else if($inf=="INFO  Finished: Creating of signature failed."){
            $out = implode(PHP_EOL, $out);
            error_log(date(DATE_ATOM)." ".basename(__FILE__)." ($ret) $valRadio: $out\n",3,"$ABSOL_PATH/bodega/jsignpdf.log");

            error_log(date(DATE_ATOM)." ".basename(__FILE__)."  $commandFirmado \n",3,"$ABSOL_PATH/bodega/jsignpdf.log");


            die("<table class='table table-bordered'><tr><td class=titulosError>Ocurrio un error al firmar el documento<br><blockquote></blockquote></td></tr></table>");

        } else {
            rename($uploadDir . $valRadio . '_signed.pdf', 
                $full_path);
        }   
    }*/
      
    }else {
        die("<table class='table table-bordered'><tr><td class=titulosError>Ocurrio un Error la Fila no fue cargada Correctamente <p>".$my_upload->show_error_string()."<br><blockquote>".nl2br($info)."</blockquote></td></tr></table>");
    }
}
?>
<table cellspace=2 WIDTH=60% id=tb_general align="left" class="table table-bordered ">
<tr>
	<td colspan="2" class="<?=$classTr?>"><b><CENTER>ACCION REQUERIDA --> <?=$causaAccion ?></CENTER></b></td>
</tr>
<tr>
	<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">RADICADOS INVOLUCRADOS :
	</td>
<td  width="65%" height="25" class="listado2_no_identa"><?=$valRadio?>
</td>
</tr>
<tr>
<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">Datos Fila Asociada :
</td>
<td  width="65%" height="25" class="listado2_no_identa">
<?=$info?>
</td>
</tr>
<tr>
<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">FECHA Y HORA :
</td>
<td  width="65%" height="25" class="listado2_no_identa">
<?=date("m-d-Y  H:i:s")?>
</td>
</tr>
<tr>
<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">USUARIO ORIGEN:
</td>
<td  width="65%" height="25" class="listado2_no_identa">
<?=$_SESSION['usua_nomb']?>
</td>
</tr>
<tr>
<td align="right" bgcolor="#CCCCCC" height="25" class="titulos2">DEPENDENCIA ORIGEN:
</td>
<td  width="65%" height="25" class="listado2_no_identa">
<?=$_SESSION['depe_nomb']?>
</td>
</tr>
</table>
<table class="borde_tab">
<tr><td class="titulosError">
<?
 if($asocImgRad!=""){
  $query = "update radicado
			set radi_path='$fileGrb',
			radi_imagen_hash='$img_hash'
  			where radi_nume_radi=$valRadio";
  if($db->conn->Execute($query) )
  {
	 $radicadosSel[] = $valRadio;
	 $codTx = 42;	//Codigo de la transaccion
	 include "$ruta_raiz/include/tx/Historico.php";
	 $hist = new Historico($db);
	 $observa.=" - Se carga imagen principal digitalizada con código de seguridad:".$img_hash;
	 $hist->insertarHistorico($radicadosSel,  $dependencia , $codusuario, $dependencia, $codusuario, $observa, $codTx);
	}else{
   echo "<hr>No actualizo la BD Radicado <hr>";
  }
 }else{
  // Si son anexos....
  if(strlen($numeroAnexo) >= 8)
  {

   $radicadosSel[] = $valRadio;
   $codTx = 29; //Codigo de la transaccion 42
   include "$ruta_raiz/include/tx/Historico.php";

   $hist = new Historico($db);
   $hist->insertarHistorico($radicadosSel,  $dependencia , $codusuario, $dependencia, $codusuario, $observa, $codTx);
  }else{
   echo "<hr>No actualizo la BD Anexo<hr>";
  }

 }
?>
</td></tr>
</table>
</form>
</body>
</html>
