<?php
session_start();
if (!$ruta_raiz) $ruta_raiz = "..";
include "$ruta_raiz/conn.php";
include "$ruta_raiz/class_control/anexo.php";
include "$ruta_raiz/include/tx/Historico.php";

$page         = $_GET['page']; // get the requested page
$limit        = $_GET['rows']; // get how many rows we want to have into the grid
$sidx         = $_GET['sidx']; // get index row - i.e. user click to sort
$sord         = $_GET['sord']; // get the direction
$dat          = $_GET['dat']; // get the direction
$tx           = $_GET['tx'];
$tableSearch  = $_GET['tableSearch'];
$fieldSearch  = $_GET['fieldSearch'];
$fieldsView   = $_GET['fieldsView'];
$searchTerm   = $_GET['searchTerm'];
$ln           = $_SESSION["digitosDependencia"];
function subir_Anexar($tmpName, $tmpDir){
	$bodegaTmp = "$ruta_raiz/bodega/tmp/$tmpName";
	move_uploaded_file($tmpDir,$bodegaTmp);
	//**************Anexar en radicados*****************//
	$rads=explode(",",$_REQUEST['numrad']);
	foreach($rads as $rad){
		$anex=new Anexo($db);
		$anex->anex_nomb_archivo=$tmpName;
		$anex->anex_radi_nume=$rad;
		$anex->anex_creador="'$krd'";
		$anex->anexarFilaRadicado();
		$output = "$ruta_raiz/bodega".str_replace("docs/","docs/1",$anex->anexoRutaArchivo);
		$command="cp \"$bodegaTmp\" \"$output\"";
		shell_exec($command);
		//$hist=new Historico($db);
		//$hist->insertarHistorico($rad,420,$codusuario,,,
	}
	unlink($bodegaTmp);
	//**************************************************//
}


switch ($tx) {

	case 1:

		break;

	case 2:

		$year       = date("Y");
		$output_dir = "../bodega/tmp/";

		if(isset($_FILES["fileFormDinamic"])){
			$ret = array();
			$error =$_FILES["fileFormDinamic"]["error"];
			//You need to handle  both cases
			//If Any browser does not support serializing of multiple files using FormData()
			if(!is_array($_FILES["fileFormDinamic"]["name"])) //single file
			{

				$fileName = $_FILES["fileFormDinamic"]["name"];
				$tempDir = $_SESSION['RUTA_ABSOLUTA']."/bodega/tmp/$fileName";
				move_uploaded_file($_FILES["fileFormDinamic"]["tmp_name"],$tempDir);
				//**************Anexar en radicados*****************//
				$numRad=$_REQUEST['numrad'];
				$anex=new Anexo($db);
				$anex->anex_nomb_archivo="$fileName";
				$anex->anex_radi_nume=$numRad;
				$anex->anex_creador="'$krd'";
				$anex->anex_desc="Anexo Tecnico CCU";
				$anex->anexarFilaRadicado();
				$output = $_SESSION['RUTA_ABSOLUTA']."/bodega".str_replace("docs/","docs/1",$anex->anexoRutaArchivo);
				$command="cp '$tempDir' '$output'";
				exec($command);
				unlink($tempDir);
				//**************************************************//

				$ret[]= $fileName;

			} else  //Multiple files, file[]
			{
				$fileCount = count($_FILES["fileFormDinamic"]["name"]);
				for($i=0; $i < $fileCount; $i++){
					$namefile = rand(9999, 99999);
					$fileName = $namefile.'_'.$_FILES["fileFormDinamic"]["name"][$i];
					move_uploaded_file($_FILES["fileFormDinamic"]["tmp_name"][$i],$output_dir.$fileName);
					$ret[]= $fileName;
				}
			}
			echo json_encode($ret);
			die();
		}
//echo $_FILES["fileFormDinamic"];
		break;

}

echo json_encode($response);
