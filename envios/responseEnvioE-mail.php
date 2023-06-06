<?php
session_start();

$ruta_raiz = "..";
if (!$_SESSION['dependencia'])
	header ("Location: $ruta_raiz/cerrar_session.php");

$krd=$_SESSION["krd"];

foreach ($_POST as $key => $valor)   ${$key} = $valor;
foreach ($_GET as $key => $valor)   ${$key} = $valor;

$usua_doc    = $_SESSION["usua_doc"];
$dependencia = $_SESSION["dependencia"];
$arrAnexos   = "";
$adjuntosAnex= "";

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
include_once "$ruta_raiz/processConfig.php";
include_once "$ruta_raiz/class_control/anexo.php";

$db = new ConnectionHandler("$ruta_raiz");

define('ADODB_ASSOC_CASE', 1);

$isql = <<<EOF
SELECT
	a.SGD_DIR_TIPO as "radDestino",
	a.RADI_NUME_SALIDA as "radSalida",
	b.RADI_NUME_DERI as "radPadre",
	b.RA_ASUN as "radAsunto",
	b.RADI_PATH as "radPath"
FROM
	ANEXOS a,
	RADICADO b
WHERE
	a.radi_nume_salida=b.radi_nume_radi
	and a.anex_codigo = ('$codigo')
	AND anex_estado>=3
	AND a.sgd_dir_tipo <> 7
ORDER BY a.SGD_DIR_TIPO
EOF;

if(isset($anexo_radicado)){
	$isql = <<<EOF
	SELECT
		a.SGD_DIR_TIPO as "radDestino",
		a.RADI_NUME_SALIDA as "radSalida",
		b.RADI_NUME_DERI as "radPadre",
		b.RA_ASUN as "radAsunto",
		b.RADI_PATH as "radPath",
		a.ANEX_DEPE_CREADOR as "depe_creador",
		a.ANEX_NOMB_ARCHIVO as "anexPath"
	FROM
		ANEXOS a,
		RADICADO b
	WHERE
		a.radi_nume_salida=b.radi_nume_radi
		and a.anex_codigo = ('$codigo')
		AND anex_estado>=2
		AND a.sgd_dir_tipo <> 7
	ORDER BY a.SGD_DIR_TIPO
	EOF;
}

$rs=$db->conn->GetAll($isql);

$datos_envio=$db->conn->getRow("SELECT * FROM sgd_rad_envios WHERE id = $envio");
$id_anexo = $datos_envio['ID_ANEXO'];
$id_direccion = $datos_envio['ID_DIRECCION'];
$radSalida  = $rs[0]["RADSALIDA"];
$radPadre   = $rs[0]["RADPADRE"];

if(isset($anexo_radicado)){
	$depe_anex =$rs[0]["DEPE_CREADOR"];
	$nom_anex =$rs[0]["ANEXPATH"];
	$radiPath = "/2020/$depe_anex/docs/$nom_anex";
} else {
	$radiPath   = $rs[0]["RADPATH"];
}

$radDestino = $rs[0]["RADDESTINO"];
$asu        = $radAsun = $rs[0]["RADASUNTO"];

include "$ruta_raiz/clasesComunes/datosDest.php";

$dat     = new DATOSDEST($db,$radSalida,$espcodi,$radDestino,$radDestino);
$pCodDep = $dat->codep_us;
$pCodMun = $dat->muni_us;
$pNombre = $dat->nombre_us;
$pPriApe = $dat->prim_apel_us;
$pSegApe = $dat->seg_apel_us;
$nombre_us    = substr($pNombre . " " . $pPriApe . " " . $pSegApe,0 ,33);
$direccion_us = $dat->direccion_us;

include "$ruta_raiz/jh_class/funciones_sgd.php";
$a = new LOCALIZACION($pCodDep,$pCodMun,$db);
$departamento_us = $a->departamento;
$destino = $a->municipio;
$pais_us = $a->GET_NOMBRE_PAIS($dat->idpais,$db);
$dir_codigo = $dat->documento_us;

$sql_selectAnexos="select * from anexos where anex_codigo='$codigo' and sgd_dir_tipo <>7";
$rs_selectAnexos=$db->conn->Execute($sql_selectAnexos);

$record_updateAnexos["ANEX_ESTADO"] = 4;
$record_updateAnexos["ANEX_FECH_ENVIO"] = "now()";
$sql_updateAnexos = $db->conn->GetUpdateSQL($rs_selectAnexos,$record_updateAnexos);

$sql_codigoEnvio = "select SGD_RENV_CODIGO FROM SGD_RENV_REGENVIO ORDER BY SGD_RENV_CODIGO DESC ";
$rs_codigoEnvio = $db->conn->SelectLimit($sql_codigoEnvio,1);

$codigoEnvio = $rs_codigoEnvio->fields["SGD_RENV_CODIGO"]+1;
$sql_updateRadicado = "update RADICADO set SGD_EANU_CODIGO=9 where RADI_NUME_RADI =$radSalida";

$sgd_dir_codigo = $datos_envio['SGD_DIR_CODIGO'];
if(empty($envio)){
	$sql_mailDestino="select SGD_DIR_MAIL from sgd_dir_drecciones where RADI_NUME_RADI =$radSalida 
UNION
select SGD_DIR_MAIL from sgd_dir_drecciones where RADI_NUME_RADI
in
(
	select radi_nume_deri from radicado where radi_nume_radi=$radSalida
)
	";
}else{
	$sql_mailDestino="select SGD_DIR_MAIL from sgd_dir_drecciones join sgd_rad_envios on sgd_rad_envios.id_direccion = sgd_dir_drecciones.id where RADI_NUME_RADI =$radSalida and sgd_rad_envios.id = $envio 
UNION
select SGD_DIR_MAIL 
from sgd_dir_drecciones 
join sgd_rad_envios on sgd_rad_envios.id_direccion = sgd_dir_drecciones.id 
where RADI_NUME_RADI in
(
	select radi_nume_deri from radicado where radi_nume_radi=$radSalida
) 
and sgd_rad_envios.id = $envio";
}

$emails_destino=$db->conn->getAll($sql_mailDestino);

if(count($emails_destino)==0){
$sql_mailDestino="select distinct sgd_dir_drecciones.SGD_DIR_MAIL
from sgd_dir_drecciones
join anexos on anexos.radi_nume_salida = sgd_dir_drecciones.radi_nume_radi or anexos.anex_radi_nume = sgd_dir_drecciones.radi_nume_radi
where RADI_NUME_RADI = $radSalida
UNION
select distinct sgd_dir_drecciones.SGD_DIR_MAIL
from sgd_dir_drecciones
join anexos on anexos.radi_nume_salida = sgd_dir_drecciones.radi_nume_radi or anexos.anex_radi_nume = sgd_dir_drecciones.radi_nume_radi
where RADI_NUME_RADI in
(
	select radi_nume_deri from radicado where radi_nume_radi=$radSalida
)";

$emails_destino=$db->conn->getAll($sql_mailDestino);

}
//echo $sql_mailDestino; exit();


$email = implode(';', array_map(function ($entry) {
    return $entry['SGD_DIR_MAIL'];
}, $emails_destino));

//Variable que recopila datos para el envio masivo de correos
$dataMasiva .= "<li class='list-group-item'>
            <b>Codigo de Anexo</b> $codigo
          </li>";


$anexosRad = new Anexo($db);
$arrAnexos = $anexosRad->anexosRadicado($radSalida);

if (!empty($arrAnexos)) {
    foreach ($arrAnexos->codi_anexos as $ANEX_CODIGO => $codi_anexos) {
        $anex[] = $codi_anexos;
    }

    foreach ($arrAnexos->desc_anexos as $ANEX_CODIGO => $desc_anexos) {
        if ($desc_anexos)
            $desc_anex[$ANEX_CODIGO] = $desc_anexos;
    }

    $ano = substr($nurad, 0, 4);

    foreach ($arrAnexos->path_anexos as $ANEX_CODIGO => $path_anexos) {
        $path_anex[$ANEX_CODIGO] = $path_anexos;
    }

    $adjuntosAnex1 = $path_anex;
}

$arrAnexos = $anexosRad->anexosRadicado($radPadre);

if (!empty($arrAnexos)) {
	foreach ($arrAnexos->codi_anexos as $ANEX_CODIGO => $codi_anexos) {
		$anex[] = $codi_anexos;
	}

	foreach ($arrAnexos->desc_anexos as $ANEX_CODIGO => $desc_anexos) {
		if ($desc_anexos)
			$desc_anex[$ANEX_CODIGO] = $desc_anexos;
	}

	$ano = substr($nurad, 0, 4);

	foreach ($arrAnexos->path_anexos as $ANEX_CODIGO => $path_anexos) {
		$path_anex[$ANEX_CODIGO] = $path_anexos;
	}

	$adjuntosAnex2 = $path_anex;
}

if(!empty($adjuntosAnex2) && !empty($adjuntosAnex1)){
    $adjuntosAnex = array_merge($adjuntosAnex1, $adjuntosAnex2);
}elseif(!empty($adjuntosAnex1)){
    $adjuntosAnex = $adjuntosAnex1;
}elseif(!empty($adjuntosAnex2)){
    $adjuntosAnex = $adjuntosAnex2;
}

//Variable que recopila datos para el envio masivo de correos
$data_adjuntos = implode("<br>", $adjuntosAnex);
$dataMasiva .= "<li class='list-group-item'>
            <b>Adjuntos</b><br> $data_adjuntos
           </li>";

if($pruebas){
    echo 'anexos1.';
    var_dump($adjuntosAnex1);
    echo 'anexos2.';
    var_dump($adjuntosAnex2);
    var_dump('Listado de Adjuntos: ', $adjuntosAnex);
}


/*Valida si ya existe un registro de un envio*/

$b_email=str_replace(";","|",$email);
$b_emailma=strtolower($b_email);
$b_emailmi=strtoupper($b_email);

$sql_registro_previo="SELECT COUNT(*) k FROM sgd_renv_regenvio 
WHERE radi_nume_sal=".$radSalida." AND (sgd_renv_mail similar to '%(".$b_emailmi.")%' OR sgd_renv_mail similar to '%(".$b_emailma.")%')";



$rs_registro_previo=$db->conn->Execute($sql_registro_previo);
$rp=$rs_registro_previo->fields['K'];



$record_insertRegenvio["USUA_DOC"]=$usua_doc;
$record_insertRegenvio["SGD_RENV_CODIGO"]=$codigoEnvio;
$record_insertRegenvio["SGD_FENV_CODIGO"]=106;
$record_insertRegenvio["SGD_RENV_FECH"]="now()";
$record_insertRegenvio["RADI_NUME_SAL"]=$radSalida;
$record_insertRegenvio["SGD_RENV_DESTINO"]=$email;
$record_insertRegenvio["SGD_RENV_MAIL"]=$email;
$record_insertRegenvio["SGD_RENV_CERTIFICADO"]=0;
$record_insertRegenvio["SGD_RENV_ESTADO"]=1;
$record_insertRegenvio["SGD_RENV_NOMBRE"]=$nombre_us;
$record_insertRegenvio["SGD_DIR_CODIGO"]=$dir_codigo;
$record_insertRegenvio["DEPE_CODI"]=$dependencia;
$record_insertRegenvio["SGD_DIR_TIPO"]=$radDestino;
$record_insertRegenvio["RADI_NUME_GRUPO"]=$radSalida;
$record_insertRegenvio["SGD_RENV_PLANILLA"]=0;
$record_insertRegenvio["SGD_RENV_DIR"]="email: $email";
$record_insertRegenvio["SGD_RENV_DEPTO"]=$departamento_us;
$record_insertRegenvio["SGD_RENV_MPIO"]=$destino;
$record_insertRegenvio["SGD_RENV_PAIS"]=$pais_us;
$record_insertRegenvio["SGD_RENV_OBSERVA"]="";
$record_insertRegenvio["SGD_RENV_CANTIDAD"]=1;
$keys=array_keys($record_insertRegenvio);
$keys=implode(',',$keys);
$values="'".implode("','",$record_insertRegenvio)."'";
$values=str_replace("'(SYSDATE+0)'","(SYSDATE+0)",$values);

if($rp==0){
$sql_insertRegenvio="insert into SGD_RENV_REGENVIO ($keys) values ($values)";	
}

if (!$db->conn->HasFailedTrans()){#Verificando errores en consultas de DB
	$envioDigital=true;
	$radicadosSelText=$radSalida;

	ob_start();
	$mailDestino=$email;
	
	if($rp==0){
		include ("$ruta_raiz/include/mail/mailInformar.php");
	}

    if($pruebas){
        var_dump($success, $conf_certificadoCorreo);
	}


	$sql_val_cert="select cast(certificado as int) as cert from sgd_rad_envios where id=".$envio;
	$rs_val_cert=$db->conn->Execute($sql_val_cert);
	$val_cert=$rs_val_cert->fields['CERT'];


    if($conf_certificadoCorreo and $rp==0 and $val_cert==1){

		//include ("$ruta_raiz/include/mail/mailInformarCerticamara.php");
	    include ("$ruta_raiz/envios/gseEnvioCertiCorreo.php");
	    
    	//include ("$ruta_raiz/include/mail/mailInformar.php");

	}

    if($pruebas){
        var_dump($arrCertificados);
    }else{
	    $buffer = ob_get_flush();
    }

	if ($success===true){
		$request = array(
			"success" => true,
			"message" => "Enviado correctamente"
		);
		//Start::Registro de envio exitoso
		if($pruebas){
        var_dump($sql_updateAnexos);
        var_dump($sql_updateRadicado);
        var_dump($sql_insertRegenvio);
	    }else{
	        $db->conn->Execute("UPDATE sgd_rad_envios SET estado = 2 WHERE id = $envio");
	        $pendientes = $db->conn->getOne("SELECT count(id_anexo) as pendientes FROM sgd_rad_envios where estado != 2 and id_anexo = $id_anexo");
	        if($pendientes == 0) {
	            $rs_updateAnexos = $db->conn->Execute($sql_updateAnexos);
	            $rs_updateRadicado = $db->conn->Execute($sql_updateRadicado);
	        }
	        $rs_insertRegenvio = $db->conn->Execute(utf8_encode($sql_insertRegenvio));
	        //Start::se cambia el estado del anexo
	        $db->conn->Execute("UPDATE anexos SET anex_estado = 4 WHERE id = $id_anexo");
	        //End::se cambia el estado del anexo
	    }
	    //End::Registro de envio exitoso
	} else {
		    $db->conn->Execute("UPDATE sgd_rad_envios SET estado = 2 WHERE id = $envio");
	        $pendientes = $db->conn->getOne("SELECT count(id_anexo) as pendientes FROM sgd_rad_envios where estado != 2 and id_anexo = $id_anexo");
	        if($pendientes == 0) {
	            $rs_updateAnexos = $db->conn->Execute($sql_updateAnexos);
	            $rs_updateRadicado = $db->conn->Execute($sql_updateRadicado);
	        }
	        $rs_insertRegenvio = $db->conn->Execute(utf8_encode($sql_insertRegenvio));
	        //Start::se cambia el estado del anexo
	        $db->conn->Execute("UPDATE anexos SET anex_estado = 4 WHERE id = $id_anexo");
	        //End::se cambia el estado del anexo

		if($rp>0){	
	    	$msgenvio=". Este correo ya fue enviado";
    	}

		$request = array(
			"success" => false,
			"message" => " Error al enviar correo electronico favor devolver, para evitar el reenvio de este mismo radicado error: ".$buffer.$msgenvio,
			"debug" => $buffer
		);
	}

} else {
	$request = array(
		"success" => false,
		"message" => "Error al ejecutar la consulta"
	);
}

if($pruebas){
    var_dump($conf_certificadoCorreo);
}else{
    ob_end_clean();
}

//radicaMasiva Variable que recopila datos para el envio masivo de correos
if(empty($radicaMasiva)){
    print json_encode($request);
}else{
    $text = json_encode($request);
    $dataMasiva .= "<li class='list-group-item'>
                <b>Correo enviado: </b> $text
              </li>";
}
?>
