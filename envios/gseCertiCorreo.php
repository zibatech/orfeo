<?php
ini_set('display_errors',1);
set_time_limit(0);

$socket = @stream_socket_server("tcp://0.0.0.0:7701", $errno, $errstr);
if (!$socket) {
    echo "already in use\n";
    exit;
}

$ruta_raiz = '..';
$saveFiles = "$ruta_raiz/bodega/cert/";

if ( !is_dir( $saveFiles ) ) {
    if (!mkdir($saveFiles, 0700)){
        die('Failed to create folders... /bodega/cert');
    }
}

include_once("$ruta_raiz/processConfig.php");

$urlLogin    = $conf_certificadoUrl.'v1/auth/login';
$urlListCert = $conf_certificadoUrl.'v1/emailAPI/';
$urlCertDoc  = $conf_certificadoUrl.'v1/emailAPI/{{emailID}}/record/';
$urlCertEst  = $conf_certificadoUrl.'v1/emailAPI/{{emailID}}';

$credenciales = "{
                   \"email\" : \"$conf_certificadoCorreo\",
                   \"password\" : \"$conf_certificadoPassword\"
                 }";

$db = new ConnectionHandler("$ruta_raiz");

$dataCer     = array();

/***************************************************************/
/************** login ******************************************/
/***************************************************************/

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $urlLogin);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

curl_setopt($ch, CURLOPT_POSTFIELDS, $credenciales);

//Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

//Execute the request.
$data = curl_exec($ch);

//Close the cURL handle.
curl_close($ch);

$arrLogin = json_decode($data);

if($arrLogin->statusCode !== 200){
    var_dump($arrLogin);
    throw new Exception('No se realizo la consulta de login.' . $arrLogin->statusCode );
}

$token = $arrLogin->result->token;

/***************************************************************/
/************** TRAER LISTADO DE CERITIFICACIONES **************/
/***************************************************************/

$ch = curl_init();
//Set the URL that you want to GET by using the CURLOPT_URL option.
//
$date1 =  strtotime("-5 day", time());
$dateBefore = date('Y-m-d', $date1);


$params   = http_build_query(array('startDate' => $dateBefore ,
                       'endDate' => date('Y-m-d', time())));

curl_setopt($ch, CURLOPT_URL, $urlListCert.'?'.$params);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Token {$token}"));

curl_setopt($ch, CURLOPT_POSTFIELDS, );
//Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

//Execute the request.
$data = curl_exec($ch);

//Close the cURL handle.
curl_close($ch);

$arrCertificados = json_decode($data);

echo "************** parametros del reporte *********** \n";
print_r($params. "\n");

if($arrCertificados->statusCode !== 200){
    var_dump($arrCertificados);
    throw new Exception('No se realizo la consulta del listado de certificados.'. $arrCertificados->statusCode);
}

foreach ($arrCertificados->result  as $value){
    $tmpUrl = $urlCertDoc;
    $error  = false;
    if(preg_match('/\d{12,}/', $value->subject, $output)){

        $namePath = $value->emailID. ".pdf";

        $sel = "select
            count(1) as NUM
            from
            sgd_renv_regenvio
            where
            radi_nume_sal = {$output[0]}
            and sgd_renv_nombre = '{$value->emailID}'";

        $rs = $db->conn->query($sel);

        if ($rs && !$rs->EOF && $rs->fields["NUM"] > 0 && file_exists($saveFiles.$namePath)) {
            print_r("Registro Existente en sgd_renv_regenvio {$output[0]} \n
                con la el archivo $saveFiles.$namePath \n ");
        }else{

            $newUrl = str_replace('{{emailID}}', $value->emailID, $urlCertDoc);

            /***************************************************************/
            /************** CERITIFICACION DE CADA CORREO ******************/
            /***************************************************************/

            $ch = curl_init();
            //Set the URL that you want to GET by using the CURLOPT_URL option.

            curl_setopt($ch, CURLOPT_URL, $newUrl );
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Token {$token}"));

            curl_setopt($ch, CURLOPT_POSTFIELDS);
            //Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            //Execute the request.
            $data = curl_exec($ch);

            //Close the cURL handle.
            curl_close($ch);

            $data = json_decode($data);

            if($data->statusCode !== 200){
                echo('Error no se realizo la consulta de los archivos del radicado '. $output[0]. "\n \r");
                $error = true ;
            }

            $namePath = $value->emailID. ".pdf";

            list($type, $data) = explode(';', $data->result->base64);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);

            if ($data === false) {
                echo('Error al decodificar el archivo del radicado '. $output[0]. "\n \r");
                $error = true ;
            }

            try{
                echo "Salvar Imagen De Certificacion \n \r";
                $saveFile = file_put_contents($saveFiles.$namePath, $data);
                echo "Linea 152 Archivo a salvar => {$saveFiles}{$namePath} \n \r";
            } catch(Exception $e) {
                var_dump('no se guardo el archivo', $saveFiles.$namePath ,$e->getMessage());
                $saveFile = '';
                $error = true ;
                echo 'Exception: Guardando el archivo',  $e->getMessage(), "\n";
            }

            $dataCer[] = array( 'cert_id_email'  => $value->emailID,
                'cert_name'      => $files->result->filename,
                'cert_path'      => $namePath,
                'cert_radi_nume' => $output[0]);

            var_dump($value->subject);

            if($error){
                $link  = "La imagen no se pudo descargar por favor solicitarla para ser descargada desde la aplicación del certificador";
            }else{
                $link  = "<a target=\"_blank\" href=\"./bodega/cert/{$namePath}\">
                    Certificación del envio de correo</a>";



            $dateF = $db->conn->OffsetDate(0,$db->conn->sysTimeStamp);
            $email = $files->result->to;

            $isql = "INSERT INTO SGD_RENV_REGENVIO(
                id,
                sgd_renv_pais,
                sgd_renv_cantidad,
                sgd_renv_depto,
                sgd_renv_mpio,
                sgd_renv_dir,
                sgd_dir_tipo,
                sgd_renv_mail,
                sgd_renv_codigo,
                sgd_renv_fech,
                radi_nume_sal,
                sgd_fenv_codigo,
                sgd_renv_nombre
            )VALUES(
                (select max(id) + 1 from sgd_renv_regenvio),
                'COLOMBIA',
                1,
                'D.C.',
                'BOGOTÁ',
                '$link',
                1,
                '$email',
                (select max(sgd_renv_codigo) + 1 from sgd_renv_regenvio),
                $dateF,
                '$output[0]',
                106,
                '$value->emailID'
            )";

            $rsInsert = $db->conn->query($isql);
			
			
			$id_envio=$value->emailID;
			$newUrl = str_replace('{{emailID}}', $value->emailID, $urlCertEst);
			
			/*valida si la dependencia tiene permiso para cerrar tramites*/

			$dependencia_radicado=substr($output[0], 4,5);
			$sql_per_cierre="SELECT dep_cierre FROM dependencia WHERE depe_codi=".$dependencia_radicado;
			$rs_per_cierre=$db->conn->query($sql_per_cierre);

			if($rs_per_cierre->fields["DEP_CIERRE"]==1)
				
				{
						/***************************************************************/
						/****CERITIFICACION ESTADO DE CADA CORREO CIERRE AUTO***********/
						/***************************************************************/

						$ch = curl_init();
						//Set the URL that you want to GET by using the CURLOPT_URL option.

						curl_setopt($ch, CURLOPT_URL, $newUrl );
						curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Token {$token}"));

						curl_setopt($ch, CURLOPT_POSTFIELDS);
						
						//Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

						//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

						//Execute the request.
						$data = curl_exec($ch);

						//$data2 = curl_exec($ch);

						//Close the cURL handle.
						curl_close($ch);

						$data = json_decode($data);
																	   

						if($data->statusCode !== 200){
								echo('Error no se realizo la consulta de los archivos del radicado '. $output[0]. "\n \r");
								$error = true ;
													}

						print_r($data->result);

						$error_envio=0;

						foreach($data->result as $value)
							{
								$mensaje=$value->eventType;
								switch ($mensaje) {
										case 'Complaint':
												$error_envio=1;
												break;
										case 'Reject':
												$error_envio=1;
												break;
										case 'Bounce':
												$error_envio=1;
												break;
										default:
												$error_envio=0;
												break;
												}
							}

						/*verifica que tenga expediente*/

						$sql_exp="SELECT COUNT(*) k  FROM sgd_exp_expediente WHERE radi_nume_radi=".$output[0];
						$rs_esp=$db->conn->query($sql_exp);

						if($rs_esp->fields["K"]==0)
							$error_envio=1;

						/*verifica que tenga trd*/
						$sql_trd="SELECT COUNT(*) k FROM sgd_rdf_retdocf WHERE radi_nume_radi=".$output[0];
						$rs_trd=$db->conn->query($sql_trd);

						if($rs_trd->fields["K"]==0)
							$error_envio=1;

						/*valida medio de envio*/
						$sql_medio="SELECT mrec_codi FROM radicado WHERE radi_nume_radi=".$output[0];
						$rs_medio=$db->conn->query($sql_medio); 

						if($rs_medio->fields["MREC_CODI"]==7)
							$error_envio=1;						

						if($error_envio==0)
							{	
					
							/*usuario y dependencia actual*/
							$sql_usu="SELECT radi_depe_actu,radi_usua_actu,(SELECT usua_doc FROM usuario where usua_codi=radi_usua_actu AND depe_codi=radi_depe_actu) cedula 
							FROM radicado WHERE radi_nume_radi=".$output[0];
							$rs_usu=$db->conn->query($sql_usu);
																					
							$sql_ins_h="
										insert
										into
										hist_eventos
											(depe_codi,
											hist_fech,
											usua_codi,
											radi_nume_radi,
											hist_obse,
											usua_codi_dest,
											usua_doc,
											usua_doc_old,
											sgd_ttr_codigo,
											hist_usua_autor,
											hist_doc_dest,
											depe_codi_dest,
											usuario_id)
											values(
													".$rs_usu->fields["RADI_DEPE_ACTU"].",
													now(),
													".$rs_usu->fields["RADI_USUA_ACTU"].",
													".$output[0].",
													'Cierre automático hecho por el Sistema. Acuse de recibido con estado: ".$mensaje."',
													15,
													'".$rs_usu->fields["CEDULA"]."',
													null,
													13,
													null,
													'999999',
													999,
													null);
										";
								$db->conn->query($sql_ins_h);
								$sql_update="UPDATE radicado SET radi_depe_actu=999,radi_usua_actu=15 WHERE radi_nume_radi=".$output[0];
								$db->conn->query($sql_update);


							/*inserta en la tabla acuses*/
							$ddate = date('Y-m-d');
							$date = new DateTime($ddate);
							$week = $date->format("W");
							$sql_ins_a="INSERT INTO acuses_usuarios VALUES(".$output[0].",".$rs_usu->fields["RADI_USUA_ACTU"].",".$rs_usu->fields["RADI_DEPE_ACTU"].",NOW(),'".$id_envio."',".$week.")";
							$db->conn->query($sql_ins_a);
						
							}
			
				}

            }

        }
    }
}
