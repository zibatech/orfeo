<?php
session_start();
$ruta_raiz = ".";
include_once "$ruta_raiz/processConfig.php";

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

function return_bytes($val){
  $val    = trim($val);
  $ultimo = strtolower($val{strlen($val)-1});
  $radicado_rem_p = $_SESSION["radicado_rem_p"];
  switch($ultimo){
  // El modificador 'G' se encuentra disponible desde PHP 5.1.0
      case 'g':	$val *= 1024;
      case 'm':	$val *= 1024;
      case 'k':	$val *= 1024;
  }
  return $val;
}

/*
  $servicio:
  'upload_image': $file, $numrad, $subir_archivo
*/
switch($servicio){
  case 'cargar_imagen_radicado':
    if ($subir_archivo)
    {	
      // Variables de configuración
      try {
        $ln = $digitosDependencia;
        $dependencia = substr(trim($numrad),4,$ln);
        $lnr = 11+$ln;
        
        // Carga de archivo
        $directorio = $CONTENT_PATH.substr(trim($numrad),0,4)."/".intval(substr(trim($numrad),4,$ln))."/";
        $file = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];

        if(!is_dir($directorio))
        {    
          if(!mkdir($directorio, 0755, true)) {
            $out = "Se intentó crear directorio inexistente y generó error";
            error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $file);                    
          }
        }  
        $bien2 = move_uploaded_file($file_tmp,$directorio.trim(strtolower($file)));
        echo json_encode([
          'servicio' => 'cargar_imagen_radicado', 
          'ruta_absoluta' => $directorio.trim(strtolower($file)), 
          'ruta' => '/'.substr(trim($numrad),0,4)."/".intval(substr(trim($numrad),4,$ln)).'/'.$file
        ]);
      } catch (\Exception $e) {
        echo json_encode([
          'servicio'=>'cargar_imagen_radicado', 
          'error'=>$e
        ]);
      }
    }
  break;

  default:
    break;
}
