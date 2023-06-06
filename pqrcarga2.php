<?php
session_start();
$ruta_raiz = ".";
include_once "$ruta_raiz/processConfig.php";
/*if (!$_SESSION['dependencia'])
  header ("Location: $ruta_raiz/cerrar_session.php");
*/
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$krd         = 'AMERICAS';
$usua_doc    = 5102020;
$codusuario  = 11513;
$ln          = $digitosDependencia;
$dependencia = substr(trim($numrad),4,$ln);
$lnr         = 11+$ln;
//Comprobamos si llega del post grabar usuario.
//var_dump(is_null($_POST['_tpradicu'])); 
//echo "->".$_POST['_tpradicu']; 
//echo "->".$_GET['tpradicu']; exit;
/*
if ($_POST['_tpradicu'] != "" ){
	$_GET['tpradic'] = $_POST['_tpradicu'];
	$_POST['tpradic'] = $_POST['_tpradicu'];
}else{
	if ($_GET['tpradicu']){
		$_GET['tpradic'] = $_GET['tpradicu'];
		$_POST['tpradic'] = $_GET['tpradicu'];
	}
}
*/
//if($_POST['tpradic'] == 0 or $_GET['tpradic'] == 0){
$NO_RADICAR = true;
//}

//echo "POST->".$_POST['tpradic'];
//echo "<br> GET->".$_GET['tpradic']; exit;

    /** * Retorna la cantidad de bytes de una expresion como 7M, 4G u 8K.
     *
     * @param char $var
     * @return numeric
     */
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

    $fechaHoy = Date("Y-m-d");

    include_once("$ruta_raiz/class_control/anexo.php");
    include_once("$ruta_raiz/class_control/anex_tipo.php");

	//incluimos ruta para las transacciones.
    include("$ruta_raiz/include/tx/Tx.php"); 	

    if (!$db)	$db = new ConnectionHandler($ruta_raiz);

    $hist      = new Historico($db);

    $sqlFechaHoy= $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
    $anex       =  new Anexo($db);
    $anexTip    =  new Anex_tipo($db);
    if (!$aplinteg)
        $aplinteg='null';
	if (!$tpradic)
		$tpradic='null';
	//if(!$cc){

		$nuevo = ($codigo)? 'no' : 'si';

		$auxsololect = ($sololect)? 'S' : 'N';
		//$db->conn->BeginTrans();
		if($nuevo_archivo==true){
			$auxnumero=$anex->obtenerMaximoNumeroAnexo($numrad);
			do {
				$auxnumero++;
				$codigo = trim($numrad).trim(str_pad($auxnumero,5,"0",STR_PAD_LEFT));
			}while ($anex->existeAnexo($codigo));
		} else {
			$bien = true;
			$auxnumero=substr($codigo,-4);
			$codigo = trim($numrad).trim(str_pad($auxnumero,5,"0",STR_PAD_LEFT));
		}
		//Esta variable es para actualizar las copias
		$codigo_copias=substr($codigo, 0, -1);


		$anex_salida = empty($radicado_salida)? 0 : 1;
		$path = $_FILES['userfile1']['name'];
		$exte = pathinfo($path, PATHINFO_EXTENSION);
		$anexTip->anex_tipo_exte($exte);
		$ext  = $anexTip->get_anex_tipo_ext();
        $tipo =  $anexTip->get_anex_tipo_codi();
		$ext = strtolower($ext);
	
		$auxnumero = str_pad($auxnumero,5,"0",STR_PAD_LEFT);
		$archivo=trim($numrad."_".$auxnumero.".".$ext);
		$archivoconversion=trim("1").trim(trim($numrad)."_".trim($auxnumero).".".trim($ext));
		
		$tamano = ($_FILES['userfile1']['size'])? ($_FILES['userfile1']['size']/1000) : 0;

		
			include "$ruta_raiz/include/query/queryUpload2.php";

			$expAnexo = ($expIncluidoAnexo)? $expIncluidoAnexo : null;
			$destino = isset($_REQUEST['emaildestino'])?$_REQUEST['emaildestino']:'';

			$isql = "insert
				into anexos
				(sgd_rem_destino
				 ,anex_radi_nume
				 ,anex_codigo
				 ,anex_tipo
				 ,anex_tamano   
				 ,anex_solo_lect
				 ,anex_creador
				 ,anex_desc
				 ,anex_numero
				 ,anex_nomb_archivo   
				 ,anex_borrado
				 ,anex_salida 
				 ,sgd_dir_tipo
				 ,anex_depe_creador
				 ,sgd_tpr_codigo
				 ,anex_fech_anex
				 ,SGD_APLI_CODI
				 ,SGD_TRAD_CODIGO
				 ,SGD_EXP_NUMERO
				 ,sgd_dir_mail)
				values (
						$radicado_rem_p  
						,$numrad         
						,$codigo    
						,$tipo   
						,$tamano     
						,'N'
						,'$krd'     
						,'$descr' 
						,$auxnumero 
						,'$archivoconversion'
						,'N'         
						,$anex_salida
						,$radicado_rem_p
						,$dependencia
						,0
						,$sqlFechaHoy
						,$aplinteg    
						,$tpradic
						,'$expAnexo'
						,'$destino')";
			$subir_archivo = true;

			//Personalizo el codigo de transaccion y el comentario
			$TX_CODIGO = 91;
			$TX_COMENTARIO = "Archivo Anexo No. $codigo ";

		

		$_POST['subir_archivo'] = $subir_archivo;
		$_POST['nuevo_archivo'] = $nuevo_archivo;
		$_POST['codigo']        = $codigo;
		
		//$db->conn->debug = true;
		$bien = $db->conn->query($isql);

		$esNotificaciones = false;
		
	
	//Si actualizo BD correctamente 
				
		             $respUpdate="OK";
		             $bien2 = false;
		             if ($subir_archivo){	
		                 $directorio        = $CONTENT_PATH.substr(trim($numrad),0,4)."/".intval(substr(trim($numrad),4,$ln))."/docs/";

		                 $userfile1_Temp    = $_FILES['userfile1']['tmp_name'];

						$file = $CONTENT_PATH . '/upload.log';
						if(!is_file($file)){    
						    $myfile = fopen($file, "w");
						    fclose($myfile);
						}

						if(!is_dir($directorio)){    
						    if(!mkdir($directorio, 0755, true)) {
						    	$out = "Se intentó crear directorio inexistente y generó error";
						    	error_log(date(DATE_ATOM)." ".basename(__FILE__)." $out\n ", 3 , $file);                    
							}
						}              

						$bien2    = move_uploaded_file($userfile1_Temp,$directorio.trim(strtolower($archivoconversion)));
						$anex_hash=hash_file('sha256',$directorio.trim(strtolower($archivoconversion)));
										 if ($bien2){
						$resp1="OK";
						 $_GET['tpradic'] = $_POST['tpradic'];
						 //$db->conn->CommitTrans();
						 //SE EJECUTO BIEN LA CONSULTA Y SUBIO CORRECTAMENTE EL ARCHIVO, SEA MODIFICADO O NUEVO

						/*CONTROL DE VERSIONES - TRAZABILIDAD  */
						 /*Insertar en el historico cuando se inserta un anexo como nuevo*/
						//		echo  $numrad." / ".$dependencia ." / ". $codusuario." /  0 /  0 / ".$TX_COMENTARIO." / ".$TX_CODIGO ; exit;
							$isql = "update 
								anexos set
								anex_hash='$anex_hash'
								where 
								anex_codigo= '$codigo'";
							$db->conn->query($isql);
						$_numrad[0]=$numrad;		
						$TX_COMENTARIO.=' con codigo de seguridad: '.$anex_hash;
						 $hist->insertarHistorico( $_numrad, $dependencia , $codusuario, $dependencia, $codusuario,$TX_COMENTARIO,$TX_CODIGO);

                 }else{ 
                  $resp1="ERROR";
                   //  $db->conn->RollbackTrans();
                 }
             }else {
                 //$db->conn->CommitTrans();
             }
        

    echo json_encode(['nombre'=>$archivoconversion,'ruta'=>$directorio.trim(strtolower($archivoconversion))])
?>
