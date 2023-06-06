<?php
session_start();
/**
 * Modulo de Formularios Web para atencion a Ciudadanos.
 * @autor Carlos Barrero   carlosabc81@gmail.com SuperSolidaria
 * @author Sebastian Ortiz Vasquez 2012
 * @fecha 2009/05
 * @Fundacion CorreLibre.org
 * @licencia GNU/GPL V2
 *
 * Se tiene que modificar el post_max_size, max_file_uploads, upload_max_filesize
 */
$ruta_raiz = "..";

require_once("$ruta_raiz/include/db/ConnectionHandler.php");
require_once("$ruta_raiz/processConfig.php");
require_once('funciones.php');
include_once('./adjuntarArchivos.php');
include_once("$ruta_raiz/include/tx/roles.php");
$post_data = http_build_query(
    array(
        'secret' => '6LcRhc4ZAAAAANKl9X8MkRfhya1tGDks4hXj6jSA',
        'response' => $_POST['g-recaptcha-response'],
        'remoteip' => $_SERVER['REMOTE_ADDR']
    )
);

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $post_data
    )
);
$context  = stream_context_create($opts);
$response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
$result = json_decode($response);
if (!$result->success) {
    header("Location: index.php");
}

foreach ($_GET as $key => $valor)   ${$key} = $valor;//iconv("ISO-8859-1","UTF-8",$valor);
foreach ($_POST as $key => $valor)   ${$key} = $valor; //iconv("ISO-8859-1","UTF-8",$valor);

// if(true) {
//     echo '<pre>';
//         var_dump($_POST); exit;
//     echo '</pre>';
// }

$pais_formulario = $pais;
define('ADODB_ASSOC_CASE', 2);
$ADODB_COUNTRECS = false;

$db   = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

if($logoEntidad){
  $log = "$ruta_raiz/bodega/$logoEntidad";
}else{
  $log = "$ruta_raiz/img/orfeo.png";
}

$errorFormulario = 0;
/*
cambio a favor de recaptcha

if(strcasecmp ($captcha ,$_SESSION['captcha_formulario']['code'] ) != 0 || strcasecmp ($idFormulario ,$_SESSION["idFormulario"] ) != 0){
	$errorFormulario = 1;
}*/
if($errorFormulario==0){
	$uploader = new Uploader($_FILES);
    $uploader->FILES = $_FILES;
	$adjuntosSubidos = json_decode($adjuntosSubidos);
	$uploader->subidos = $adjuntosSubidos;
	$uploader->adjuntarArchivos();
}

$_SESSION['depeRadicaFormularioWeb']=$depeRadicaFormularioWeb;

/* Si el usuario selecciona un grupo, el radicado es direccionado a esta
 * dependencia
 */
$coddepe = '3100';
if($coddepe){
    $rol  = new Roles($db);
    //El grupo numero 2 corresponde a Jefe de grupo
    //en el listado predefinido de perfiles
    if($rol->buscarUsuariosGrupoDepen(2, $coddepe)){
        $_SESSION['depeRadicaFormularioWeb'] = $coddepe;
        $_SESSION['usuaRecibeWeb'] = '11513';
    }
}



$num_id = $id_afectado;
$tipoDocumento = $tipo_identificacion_afectado;
$depto = isset($departamento_afectado) && $departamento_afectado != ''  ? $departamento_afectado : '11';
$muni = isset($ciudad_afectado) && $ciudad_afectado != ''  ? $ciudad_afectado : '1';
$nombre_afectado = trim($nombre_afectado_1.' '.$nombre_afectado_2);
$apellidos_afectado = trim($apellidos_afectado_1.' '.$apellidos_afectado_2);
$email = $correo_afectado.'@'.$dominio_afectado;

if($pais_afectado == 'Colombia')
{
    $depto_afectado = "\nDepartamento: ".$db->conn->getOne("SELECT DPTO_NOMB FROM departamento WHERE dpto_codi = ?", [$departamento_afectado]);
    $muni_afectado = "\nMunicipio: ".$db->conn->getOne("SELECT MUNI_NOMB FROM municipio WHERE muni_codi = ? and dpto_codi = ? ", [$ciudad_afectado, $departamento_afectado]);
} else {
    $depto_afectado = '';
    $muni_afectado = "\nCiudad / Estado / Provincia: ".$provincia_afectado;
}

$tipo_doc_direccion = $db->conn->getOne("SELECT TDID_DESC FROM tipo_doc_identificacion WHERE tdid_codi = ?", [$tipo_identificacion_afectado]);
$nombre_direccion = $nombre_afectado;
$apellido_direccion = $apellidos_afectado;  
$email_direccion = $email;
$depto_direccion = $departamento_afectado;
$muni_direccion = $ciudad_afectado;
$dir_direccion = $direccion_afectado;
$celular_direccion = $celular_afectado;
$telefono_direccion = $telefono_afectado;

$nombre_afect .= "\n\nAfectado\nNombre: ".$nombre_afectado.' '.$apellidos_afectado;
$edad = isset($edad) ? "\nEdad: ".$edad : '';
$rango_edad = isset($rango_edad) ? "\nRango edad: ".$rango_edad : '';
$sexo = isset($sexo) ? "\nSexo: ".$sexo : '';
$gestante = isset($gestante) ? "\nMadre gestante" : '';
$poblacion_especial = isset($poblacion_especial) ? "\nPoblación especial: ".$poblacion_especial : '';
$grupo_etnico = isset($grupo_etnico) ? "\nGrupo étnico: ".$grupo_etnico : '';
$p_afectado = "\nPaís: ".$pais_afectado;
$tel_afectado = "\nTeléfono: ".$telefono_afectado;
$cel_afectado = "\nCelular: ".$celular_afectado;
$email_afectado = "\nCorreo: ".$email;
$doc_afectado = "\nDocumento: ".$tipo_doc_direccion." ".$num_id;

//comentario con datos afectado
$comentario = "\n\nDetalles del caso: ".$comentarios.$nombre_afect.$doc_afectado.$edad.$rango_edad.$sexo.$gestante.$poblacion_especial.$grupo_etnico.$p_afectado.$depto_afectado.$muni_afectado.$tel_afectado.$cel_afectado.$email_afectado;

if($afectado == "No")
{
    switch($tipo)
    {
        case '1':
        case '4':
        case '2':
            $anonimo = 0;
            $nombre_peticionario = $tipo == '1' ? trim($nombre_peticionario_1.' '.$nombre_peticionario_2) : $rs;
            $apellidos_peticionario = $tipo == '1' ? trim($apellidos_peticionario_1.' '.$apellidos_peticionario_2) : '';
            
            $num_id = $id_peticionario;
            $tipoDocumento = $tipo_identificacion_peticionario;
            $tipo_doc_direccion = $db->conn->getOne("SELECT TDID_DESC FROM tipo_doc_identificacion WHERE tdid_codi = ?", [$tipo_identificacion_peticionario]);
            $nombre_direccion = $nombre_peticionario;
            $apellido_direccion = $apellidos_peticionario;
            $email_direccion = $correo_peticionario.'@'.$dominio_peticionario;
            $depto_direccion = isset($departamento_peticionario) && $departamento_peticionario != ''  ? $departamento_peticionario : '11';
            $muni_direccion = isset($ciudad_peticionario) && $ciudad_peticionario != ''  ? $ciudad_peticionario : '1';
            $dir_direccion = $direccion_peticionario;
            $celular_direccion = $celular_peticionario;
            $telefono_direccion = $telefono_peticionario;
            $doc_peticionario = "\nDocumento: ".$tipo_doc_direccion." ".$num_id;
        break;
        case '3':
            $anonimo = 1;
            $tipo_doc_direccion = '';
            
            $num_id = '';
            $tipoDocumento = '';
            $nombre_peticionario = "Anónimo";
            $apellidos_peticionario = "";
            $doc_peticionario = "";
        break;
    }

    switch($tipo)
    {
        case '1':
            $tipo = "Natural";
        break;
        case '2':
            $tipo = "Jurídica";
        break;
        case '4':
            $tipo = "Niños, niñas y adolecentes";
        break;
        case '3':
            $tipo = "Anónimo";
        break;
    }
    
    if($pais_peticionario == 'Colombia')
    {
        $depto_peticionario = "\nDepartamento: ".$db->conn->getOne("SELECT DPTO_NOMB FROM departamento WHERE dpto_codi = ?", [$departamento_peticionario]);
        $mpio_peticionario = "\nMunicipio: ".$db->conn->getOne("SELECT MUNI_NOMB FROM municipio WHERE muni_codi = ?  and dpto_codi = ? ", [$ciudad_peticionario, $departamento_peticionario]);
    } else {
        $depto_peticionario = '';
        $mpio_peticionario = "\nCiudad / Estado / Provincia: ".$provincia_afectado;
    }

    $p_peticionario = "\nPaís: ".$pais_peticionario;
    $dreccion_peticionario = isset($direccion_peticionario) ? "\nDirección: ".$direccion_peticionario : '';
    $dreccion_peticionario_2 = isset($direccion_peticionario_2) ? "\nDirección comercial: ".$direccion_peticionario_2 : '';
    $tel_peticionario = isset($telefono_peticionario) ? "\nTeléfono: ".$telefono_peticionario : '';
    $cel_peticionario = isset($celular_peticionario) ? "\nCelular: ".$celular_peticionario : '';
    $email_peticionario = "\nCorreo: ".$correo_peticionario.'@'.$dominio_peticionario;

    $comentario .= "\n\nPeticionario";
    $comentario .= "\nTipo persona: ".$tipo;
    $comentario .= "\nNombre: ".$nombre_peticionario.' '.$apellidos_peticionario.$doc_peticionario;

    //comentario con datos peticionario
    $comentario .= $p_peticionario.$depto_peticionario.$mpio_peticionario.$dreccion_peticionario.$dreccion_peticionario_2.$tel_peticionario.$cel_peticionario.$email_peticionario;
}
$pqrsFacebook = 0;
$tipoUsuario = isset($tipoUsuario) ? $tipoUsuario : '';
$asunto = $tipoSolicitud.' '.$tipoUsuario;
$entidad_solicitud = $db->conn->getOne("SELECT nombre_tipo FROM sgd_tipo_eps WHERE id = ?", [$tipo_entidad]).' - '.$db->conn->getOne("SELECT nombre_eps FROM sgd_eps WHERE id = ?", [$entidad]);
$asunto = $asunto.($entidad_solicitud != '' ? ' - '.$entidad_solicitud : '');
$ips = isset($ips_afiliacion) ? "\nInstitución prestadora IPS: ".$ips_afiliacion : "";
if($tipoSolicitud == 'Solicitud')
{
    $comentario .= "\n\nDatos de la entidad denunciada:";
    $comentario .= "\n".$entidad_solicitud." \nMunicipio afiliación: ".$ciudad_afiliacion.$ips;
}


$autoriza_respuesta = '';

if(isset($medio)) 
{
    $autoriza_respuesta =  "\nAutoriza envio de información a través de: ";
    foreach($medio as $m) {
        $autoriza_respuesta .= $m.' ';
    }

    $comentario .= $autoriza_respuesta;
}

//var_dump([$tipoDocumento, $num_id]); exit;

if($errorFormulario==0){
    if($anonimo == 1){
        //Esto es anónimo
        $_SESSION['nombre_remitente']="Anónimo";
        $_SESSION['apellidos_remitente']="N.N";
        $_SESSION['cedula']=0;
        $_SESSION['nit'] = 0;
        $_SESSION['depto']=0;
        $_SESSION['muni']=0;
        $_SESSION['direccion_remitente']="No registra";
        $_SESSION['telefono_remitente']="No registra";
        $_SESSION['email']=$email_direccion==''?"":$email_direccion;
        $mediorespuesta=$_SESSION['email']==""?3:$mediorespuesta;
        //Puede ser anonima.
        if(!$_SESSION['nombre_remitente']) $_SESSION['nombre_remitente']="Anónimo";
        if(!$_SESSION['cedula']) $_SESSION['cedula']=0;
        if(!$_SESSION['depto']) $_SESSION['depto']=0;
        if(!$_SESSION['muni']) $_SESSION['muni']=0;
        if(!$_SESSION['direccion_remitente']) $_SESSION['direccion_remitente']="No registra";
        if(!$_SESSION['telefono_remitente']) $_SESSION['telefono_remitente']="No registra";
        $_SESSION['email']=$email_direccion==''?"":$email_direccion;

    } else if ($anonimo == 0){
        //No es anónimo
        $_SESSION['nombre_remitente']=$nombre_direccion;
        $_SESSION['apellidos_remitente']=$apellido_direccion;
        if($tipoDocumento == ''){
            //No selecciono tipo de documento
            $_SESSION['cedula'] = 0;
            $_SESSION['nit'] = 0;
        }else if($tipoDocumento==4){
            //Tipo de documento NIT
            $_SESSION['cedula'] = 0 ;
            $_SESSION['nit'] = $num_id!=""?$num_id:0;
        } else{
            //Tipo de documento diferente de NIT
            $_SESSION['cedula']=$num_id;
            $_SESSION['nit'] = 0;
        }

        if($depto_direccion!=0 && ($muni_direccion<1 || $muni_direccion >999)){
            $muni_direccion=1;
        }

        $_SESSION['depto']=$depto_direccion;
        $_SESSION['muni']=$muni_direccion;
        $_SESSION['direccion_remitente']=$dir_direccion==''?"No registra":$dir_direccion;
        $_SESSION['telefono_remitente']=$telefono_direccion;
        $_SESSION['email']=$email_direccion==''?"No registra":$email_direccion;
    }

    if($pqrsFacebook=="1"){
        //Medio de recepción Facebook
        $_SESSION['mrec_codi']=10;
    }else{
        //Medio de recepción Internet
        $_SESSION['mrec_codi']=3;
    }

    $_SESSION['tipo'] = 600;
    $_SESSION['asunto']=$asunto;
    $_SESSION['desc']=textoPDF($comentario);
    //TODO Imprimir el grupo de poblacional haciendo la consulta a sgd_tma_temas
    //$_SESSION['desc'].= textoPDF("Manifiesto que pertenezco al grupo pblacional: " );
    $_SESSION['desc'].= textoPDF("\n\n".$uploader->listadoImprimible);

    //TODO Revisar que hacer con todas estas otras cosas.
    //radicado.eesp_codi
    $_SESSION['codigo_orfeo']="0";

    $_SESSION['sigla']=$_GET['sigla'];
    if(!$_GET['sigla']) $_SESSION['sigla'] = "0";
    $_SESSION['usuario']=1;
    if(!$_SESSION['dependencia']) $_SESSION['dependencia']=900;
    $dependenciaRad = $_SESSION['dependencia'];
    $_SESSION['radicado']=$_GET['radicado'];
    $_SESSION['documento_destino']=$_GET['documento_destino'];
    $numero_completado = "000000".$db->conn->GenID("SECR_TP2_".$secRadicaFormularioWeb);
    $numero=substr($numero_completado , -6 );
    $num_dir=$db->conn->GenID('SEC_DIR_DRECCIONES');
    $dependenciaCompletada = "00000".$_SESSION['depeRadicaFormularioWeb'];

    /**
     * $depeRadicaFormularioWeb;  // Es radicado en la Dependencia 900
     * $usuaRecibeWeb ; // Usuario que Recibe los Documentos Web
     * $secRadicaFormularioWeb ;
     ***/
    $numeroRadicado = date('Y').substr($dependenciaCompletada,-1*$digitosDependencia).$numero."2";

    if($tipoDocumento != '' && $tipoDocumento != 4 ){
        //inserta ciudadano
        $num_ciu=$db->conn->GenID('SEC_CIU_CIUDADANO');
        $tipdoc= $tipoDocumento/*-1*/;
        $ins_ciu="insert into sgd_ciu_ciudadano values($tipdoc,".$num_ciu.",'".mb_strtoupper($_SESSION['nombre_remitente'],"utf-8")."','".mb_strtoupper($_SESSION['direccion_remitente'],"utf-8")."','".mb_strtoupper($_SESSION['apellidos_remitente'],"utf-8")."','','".$_SESSION['telefono_remitente']."','".$_SESSION['email']."',".$_SESSION['muni'].",".$_SESSION['depto'].",'".$_SESSION['cedula']."')";
        $rs_ins_ciu=$db->conn->Execute($ins_ciu);
        //inserta en sec_dir_drecciones
        $ins_dir="insert into sgd_dir_drecciones(sgd_dir_codigo,sgd_dir_tipo,sgd_oem_codigo,sgd_ciu_codigo,radi_nume_radi,sgd_esp_codi,muni_codi,dpto_codi,sgd_dir_direccion,sgd_dir_telefono,sgd_sec_codigo,sgd_dir_nombre,sgd_dir_nomremdes,sgd_trd_codigo,sgd_dir_doc,sgd_dir_mail)
            values(".$num_dir.",1,0,".$num_ciu.",$numeroRadicado,0,".$_SESSION['muni'].",".$_SESSION['depto'].",'".$_SESSION['direccion_remitente']."','".$_SESSION['telefono_remitente']."',0,'".mb_strtoupper($_SESSION['nombre_remitente'],"utf-8")." ".mb_strtoupper($_SESSION['apellidos_remitente'],"utf-8")."','".mb_strtoupper($_SESSION['nombre_remitente'],"utf-8")." ".mb_strtoupper($_SESSION['apellidos_remitente'],"utf-8")."',1,'".$_SESSION['cedula']."','".$_SESSION['email']."')";

    }else if($tipoDocumento == 4){
        //TODO preguntar como tratar la llave foranea que hay en sgc_dir_direcciones hacia ciu_ciudadano si se trata de una empresa
        $num_oem=$db->conn->GenID('SEC_OEM_OEMPRESAS');
        //insertar empresa en sgc_oem_empresas
        $tipdoc= $tipoDocumento/*-1*/;
        $ins_empresa="insert into sgd_oem_oempresas values($num_oem,$tipdoc,'".mb_strtoupper($_SESSION['nombre_remitente'],"utf-8")." ".mb_strtoupper($_SESSION['apellidos_remitente'],"utf-8")."', '','".$_SESSION['nit']."','',".$_SESSION['muni'].",".$_SESSION['depto'].",'".mb_strtoupper($_SESSION['direccion_remitente'],"utf-8")."','".$_SESSION['telefono_remitente']."')";
        $rs_ins_oem=$db->conn->Execute($ins_empresa);

        if($tipoSolicitud == 'Denuncia')
        {
            $num_ciu=$db->conn->GenID('SEC_CIU_CIUDADANO');
            $ins_ciu="insert into sgd_ciu_ciudadano values($representante_tipo_identificacion,".$num_ciu.",'".mb_strtoupper($representante_nombres,"utf-8")."','".mb_strtoupper($representante_direccion,"utf-8")."','".mb_strtoupper($representante_apellidos,"utf-8")."','','".$representante_telefono."','".$representante_correo."',".$representante_ciudad.",".$representante_departamento.",'".$representante_id."')";
            $rs_ins_ciu=$db->conn->Execute($ins_ciu);
        

            //inserta en sec_dir_drecciones
            $ins_dir="insert into sgd_dir_drecciones(sgd_dir_codigo,sgd_dir_tipo,sgd_oem_codigo,sgd_ciu_codigo,radi_nume_radi,sgd_esp_codi,muni_codi,dpto_codi,sgd_dir_direccion,sgd_dir_telefono,sgd_sec_codigo,sgd_dir_nombre,sgd_dir_nomremdes,sgd_trd_codigo,sgd_dir_doc,sgd_dir_mail)
                values (".$num_dir.",1,".$num_oem.",0,$numeroRadicado,0,".$_SESSION['muni'].",".$_SESSION['depto'].",'".$_SESSION['direccion_remitente']."','".$_SESSION['telefono_remitente']."',0,'".mb_strtoupper($_SESSION['nombre_remitente'],"utf-8")." ".mb_strtoupper($_SESSION['apellidos_remitente'],"utf-8")."','".mb_strtoupper($_SESSION['nombre_remitente'],"utf-8")." ".mb_strtoupper($_SESSION['apellidos_remitente'],"utf-8")."',1,'".$_SESSION['nit']."','".$_SESSION['email']."'), (".++$num_dir.",2,0,".$num_ciu.",".$numeroRadicado.",0,".$representante_ciudad.",".$representante_departamento.",'".$representante_direccion."','".$representante_telefono."',0,'".mb_strtoupper($representante_nombres,"utf-8")." ".mb_strtoupper($representante_apellidos,"utf-8")."','".mb_strtoupper($representante_nombres,"utf-8")." ".mb_strtoupper($representante_apellidos,"utf-8")."',1,'".$representante_id."', '".$representante_correo."')";
        } else {
            $ins_dir="insert into sgd_dir_drecciones(sgd_dir_codigo,sgd_dir_tipo,sgd_oem_codigo,sgd_ciu_codigo,radi_nume_radi,sgd_esp_codi,muni_codi,dpto_codi,sgd_dir_direccion,sgd_dir_telefono,sgd_sec_codigo,sgd_dir_nombre,sgd_dir_nomremdes,sgd_trd_codigo,sgd_dir_doc,sgd_dir_mail)
                values (".$num_dir.",1,".$num_oem.",0,$numeroRadicado,0,".$_SESSION['muni'].",".$_SESSION['depto'].",'".$_SESSION['direccion_remitente']."','".$_SESSION['telefono_remitente']."',0,'".mb_strtoupper($_SESSION['nombre_remitente'],"utf-8")." ".mb_strtoupper($_SESSION['apellidos_remitente'],"utf-8")."','".mb_strtoupper($_SESSION['nombre_remitente'],"utf-8")." ".mb_strtoupper($_SESSION['apellidos_remitente'],"utf-8")."',1,'".$_SESSION['nit']."','".$_SESSION['email']."');";
        }
    }
    else {
        //Anonimo
        $num_ciu=$db->conn->GenID('SEC_CIU_CIUDADANO');
        $tipdoc= 0;
        $ins_ciu="insert into sgd_ciu_ciudadano values($tipdoc,".$num_ciu.",'".mb_strtoupper($_SESSION['nombre_remitente'],"utf-8")."','".mb_strtoupper($_SESSION['direccion_remitente'],"utf-8")."','".mb_strtoupper($_SESSION['apellidos_remitente'],"utf-8")."','','".$_SESSION['telefono_remitente']."','".$_SESSION['email']."',".$_SESSION['muni'].",".$_SESSION['depto'].",'".$_SESSION['cedula']."')";
        $rs_ins_ciu=$db->conn->Execute($ins_ciu);
        //inserta en sec_dir_drecciones
        $ins_dir="insert into sgd_dir_drecciones(sgd_dir_codigo,sgd_dir_tipo,sgd_oem_codigo,sgd_ciu_codigo,radi_nume_radi,sgd_esp_codi,muni_codi,dpto_codi,sgd_dir_direccion,sgd_dir_telefono,sgd_sec_codigo,sgd_dir_nombre,sgd_dir_nomremdes,sgd_trd_codigo,sgd_dir_doc,sgd_dir_mail)
            values(".$num_dir.",1,0,".$num_ciu.",$numeroRadicado,0,".$_SESSION['muni'].",".$_SESSION['depto'].",'".$_SESSION['direccion_remitente']."','".$_SESSION['telefono_remitente']."',0,'".mb_strtoupper($_SESSION['nombre_remitente'],"utf-8")." ".mb_strtoupper($_SESSION['apellidos_remitente'],"utf-8")."','".mb_strtoupper($_SESSION['nombre_remitente'],"utf-8")." ".mb_strtoupper($_SESSION['apellidos_remitente'],"utf-8")."',3,'".$_SESSION['cedula']."','".$_SESSION['email']."')";
    }

    $_SESSION['codigoverificacion'] = substr(sha1(microtime()), 0 , 5);
    $descripcionAnexos = $uploader->tieneArchivos?count($uploader->subidos):0;
    $descripcionAnexos .=  " Anexos";

    //inserta en radicado
    $ins_rad="insert into radicado (radi_nume_radi,radi_fech_radi,tdoc_codi,mrec_codi,eesp_codi,radi_fech_ofic,radi_pais,muni_codi,carp_codi,dpto_codi,radi_nume_hoja,radi_nume_folio,radi_desc_anex,";
    if($_SESSION['radicado']!=NULL)
    {
        $ins_rad.=" radi_nume_deri,";
    }
    $ins_rad.="radi_path,radi_usua_actu,radi_depe_actu,ra_asun,radi_depe_radi,radi_usua_radi,codi_nivel,flag_nivel,carp_per,radi_leido,radi_tipo_deri,sgd_fld_codigo,sgd_apli_codi,sgd_ttr_codigo,sgd_spub_codigo,sgd_tma_codigo,sgd_rad_codigoverificacion,depe_codi,sgd_trad_codigo)
        values ($numeroRadicado, now(),".$_SESSION['tipo'].",".$_SESSION['mrec_codi'].",".$_SESSION['codigo_orfeo'].",
            to_date('".date('d')."/".date('m')."/".date('Y')."','dd/mm/yyyy')
            ,'COLOMBIA'
            ,".$_SESSION['muni']."
            ,0,".$_SESSION['depto']."
            ,1,0
            ,'". $descripcionAnexos ."', ";

    if($_SESSION['radicado']!=NULL){
        $ins_rad.=$_SESSION['radicado'].", ";
    }

    $depeRadicaFormularioWeb =  $_SESSION['depeRadicaFormularioWeb'];
    $anoRad = date("Y");
    if(!$tipoPoblacion) $tipoPoblacion = "0";
    $directorio = '../bodega/'.$anoRad.'/'.intval($depeRadicaFormularioWeb);
    if (!file_exists($directorio)) 
    {
        mkdir($directorio, 0777, true);
    }

    if (!file_exists($directorio.'/docs'))
    {
        mkdir($directorio.'/docs', 0777, true);
    }

    $rutaPdf ="/$anoRad/".intval($depeRadicaFormularioWeb)."/$numeroRadicado".".pdf";
    $ins_rad.="'/$anoRad/".intval($depeRadicaFormularioWeb)."/$numeroRadicado".".pdf'
        ,".$_SESSION['usuaRecibeWeb']."
        ,".$_SESSION['depeRadicaFormularioWeb']."
        ,'".mb_strtoupper($_SESSION['asunto'],"utf-8")."'
        ,".$_SESSION['depeRadicaFormularioWeb'].",1,5,1,0,0,1,0,0,0,0,$tipoPoblacion,'".$_SESSION['codigoverificacion']."',".$_SESSION['depeRadicaFormularioWeb'].",2)";
    if($rs_ins_rad=$db->conn->Execute($ins_rad)){
        $rs_ins_dir=$db->conn->Execute($ins_dir);
    }else{
        die;
    }

    $hist_doc_dest = $db->conn->getOne('SELECT usua_doc FROM usuario WHERE usua_codi = ? ',$_SESSION['usuaRecibeWeb']);
    //Inserta historico
    $ins_his="insert into hist_eventos (depe_codi,hist_fech,usua_codi,radi_nume_radi,hist_obse,usua_codi_dest,usua_doc,sgd_ttr_codigo,hist_doc_dest,depe_codi_dest)
        values($dependenciaRad,now(),6,$numeroRadicado,'RADICACION PAGINA WEB',".$_SESSION['usuario'].",'22222222',2,'".$hist_doc_dest."',".$_SESSION['dependencia'].")";
    $rs_ins_his=$db->conn->Execute($ins_his);

    //num radicado completo
    $_SESSION['radcom']=$numeroRadicado;


    $uploader->bodega_dir .= date('Y') . "/" . $_SESSION['depeRadicaFormularioWeb'] . "/docs";
    $uploader->moverArchivoCarpetaBodega($numeroRadicado);

    //trae usualogin

    $sql_login="select usua_login from usuario where usua_codi=".$_SESSION["usuaRecibeWeb"]." and depe_codi=".$_SESSION["depeRadicaFormularioWeb"];
    $rs_login=$db->conn->getOne($sql_login);

    //insertar anexos
    $fechaval=valida_fecha($db);
    $_SESSION['cantidad_adjuntos'] = 0;
    if($uploader->tieneArchivos){
        for($i=0; $i < count($uploader->subidos);$i++)
        {
            if(strlen($uploader->subidos[$i]) == 0){
                continue;
            }
            //$origen = '../bodega/tmp/'.$uploader->subidos[$i];
            //$destino = $directorio.'/docs/'.$uploader->nombreOrfeo[$i];
            //$copy = copy($origen, $destino);
            $_SESSION['cantidad_adjuntos'] = $_SESSION['cantidad_adjuntos'] + 1;
            $extension = strtolower(end(explode('.',$uploader->subidos[$i])));
            $sql_tipoAnex = "select anex_tipo_codi from anexos_tipo where anex_tipo_ext = '".$extension ."'";
            $rs_tipoAnexo = $db->conn->getOne($sql_tipoAnex);
            $tipoCodigo = 24;
            if($rs_tipoAnexo){
                $tipoCodigo = $rs_tipoAnexo;
            }else {
                $sql_tipoAnex = "select anex_tipo_codi from anexos_tipo where anex_tipo_ext = '*'";
                $rs_tipoAnexo = $db->conn->getOne($sql_tipoAnex);
                if($rs_tipoAnexo){
                    $tipoCodigo = $rs_tipoAnexo;
                }
            }

            $ins_anex="insert into anexos(anex_radi_nume, anex_codigo,anex_tipo,anex_tamano,anex_solo_lect,anex_creador,anex_desc,anex_numero,anex_nomb_archivo,anex_borrado,anex_origen,anex_salida,anex_estado,sgd_rem_destino,sgd_dir_tipo,anex_depe_creador,anex_fech_anex,sgd_apli_codi)
                values(".$numeroRadicado.",".$numeroRadicado.sprintf("%05d",($i+1)).",".$tipoCodigo.",".$uploader->sizes[$i].",'S','".$rs_login."','".$tipo_documento[$i]."',1,'".$uploader->nombreOrfeo[$i]."','N',0,0,0,1,1,".$_SESSION["depeRadicaFormularioWeb"].",now(),0)";
            $rs_ins_anex = $db->conn->Execute($ins_anex);
        }
    }

    require('barcode.php');
    $_SESSION['depeRadicaFormularioWeb']=$depeRadicaFormularioWeb;
    $depeNomb = "";
    $muniNomb = "";
    $deptNomb = "";
    $paisNomb = "";
    $sql_depeNomb = "select depe_nomb from dependencia where depe_codi = ". $_SESSION['depeRadicaFormularioWeb'];
    $rs_depeNomb = $db->conn->Execute($sql_depeNomb);
    if(!$rs_depeNomb->EOF){
        $depeNomb = substr($rs_depeNomb->fields["depe_nomb"],0,40);
    }

    $sql_muniNomb = "select muni_nomb from municipio where muni_codi = ". $_SESSION['muni'] . " and dpto_codi = " . $_SESSION['depto'] ;
    $rs_muniNomb = $db->conn->Execute($sql_muniNomb);
    if(!$rs_muniNomb->EOF){
        $muniNomb = $rs_muniNomb->fields["muni_nomb"];
    }else {
        $muniNomb = "";
    }

    $sql_deptoNomb = "select dpto_nomb from departamento where dpto_codi = ". $_SESSION['depto'] . " and id_pais = 170";
    $rs_deptoNomb = $db->conn->Execute($sql_deptoNomb);
    if(!$rs_deptoNomb->EOF){
        $deptNomb = $rs_deptoNomb->fields["DPTO_NOMB"];
    }else{
        $deptNomb = "";
    }

    $sql_paisNomb = "select nombre_pais from sgd_def_paises where id_pais = ". $pais_formulario;
    $rs_paisNomb = $db->conn->Execute($sql_paisNomb);
    if(!$rs_paisNomb->EOF){
        $paisNomb = $rs_paisNomb->fields["NOMBRE_PAIS"];
    }else{
        $paisNomb = "No Registra";
    }

    $sql_rel_entidad = "INSERT INTO sgd_rad_entidades (radi_nume_radi, ent_id) VALUES (".$_SESSION['radcom'].", ".$entidad.")";
    $db->conn->Execute($sql_rel_entidad);
    $pdf=new PDF_Code39();
    $pdf->AddPage();

    $pdf->Code39(110,25,$_SESSION['radcom'],1,8);
    $pdf->Text(130,37,textoPDF("Radicado N°. ".$_SESSION['radcom']));
    //$pdf->Image('../bodega'.$_SESSION["logoEntidad"],20,20,75);
    //$pdf->SetFont('Arial','',16);
    //$pdf->Text(110,40,textoPDF(textoPDF($entidad_largo)));
    $pdf->Text(110,41,textoPDF(date('d')." - ".date('m')." - ".date('Y')." ".date('h:i:s')) . "   Folios: N/A (WEB)   Anexos: ". $_SESSION['cantidad_adjuntos'] );
    $pdf->SetFont('Arial','',8);
    $pdf->Text(110,45,textoPDF("Destino: ". $depeRadicaFormularioWeb . " " . substr($depeNomb, 0,10) ." - Rem/D: ". substr($_SESSION['nombre_remitente'],0,10)." ".substr($_SESSION['apellidos_remitente'],0,10)));
    $pdf->SetFont('Arial','',7);
    $pdf->Text(110,48,textoPDF("Consulte el su trámite, en la pagina de la entidad"));
    $pdf->Text(135,51,textoPDF("Código de verificación: " . $_SESSION['codigoverificacion']));
    //$pdf->Text(110,51,textoPDF(strtoupper($_SESSION['nombre_remitente'])." ".strtoupper($_SESSION['apellidos_remitente'])));
    //$pdf->Text(110,55,$_SESSION['cedula']!='0'?$_SESSION['cedula']:$_SESSION['nit']);

    $pdf->Text(12,67,textoPDF("Bogotá D.C., ".date('d')." de ".nombremes(date('m'))." de ".date('Y')));
    $pdf->Text(12,81,textoPDF("Señores"));
    $pdf->SetFont('','B');
    $pdf->Text(12,85,textoPDF($entidad_largo));
    $pdf->SetFont('','');
    $pdf->Text(12,89,textoPDF("Ciudad"));
    $pdf->Text(12,99,textoPDF("Asunto : ".mb_strtoupper($_SESSION['asunto'],"utf-8")));
    $pdf->SetXY(11,105);
    //$pdf->MultiCell(0,4,textoPDF($_SESSION['desc'],0));
    $departamento_pdf = $db->conn->getOne("SELECT DPTO_NOMB FROM departamento WHERE dpto_codi = ?", [$depto_direccion]);
    $municipio_pdf = $db->conn->getOne("SELECT MUNI_NOMB FROM municipio WHERE muni_codi = ?  and dpto_codi = ? ", [$muni_direccion, $depto_direccion]);

    $_SESSION['desc'] .= "\nAtentamente, ".textoPDF(($_SESSION['nombre_remitente'])." ".$_SESSION['apellidos_remitente']).
                         "\n".textoPDF($tipo_doc_direccion.': '.($_SESSION['cedula'] != '0' ? $_SESSION['cedula'] : $_SESSION['nit'])).
                         "\n".textoPDF($_SESSION['direccion_remitente'] . " " . $municipio_pdf . ", ". $departamento_pdf . ".").
                         "\n".textoPDF($paisNomb).
                         "\n".textoPDF("Tel. " . $_SESSION['telefono_remitente']).
                         "\n".($_SESSION['email'] != '@' ? textoPDF($_SESSION['email']) : '');

    $pdf->MultiCell(0,4,$_SESSION['desc'],0);
    
    //guarda documento en un SERVIDOR
    $pdf->Output("../bodega/$rutaPdf",'F');
    //Realizar el conteo de hojas del radicado final//
    $conteoPaginas = getNumPagesPdf("../bodega/$rutaPdf");

    $sqlu = "UPDATE radicado SET radi_nume_hoja= $conteoPaginas where radi_nume_radi=" . $_SESSION['radcom'];
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $db->conn->Execute($sqlu);

    //Envio del correo electronico
		$codTx = 1983;
		include($ruta_raiz.'/include/mail/GENERAL.mailInformar.php');

    $_SESSION["idFormulario"] = "";

}
?>

<?php include ('header.php') ?>
    <div class="container" style="height: 1080px;">
        <div class="row justify-content-between" style="margin-top:10px;">
            <div class="col-4" style="text-align:left;">
                <a href="http://www.supersalud.gov.co/" target="_blank">
                    <img src="./images/supersalud.png" height=100 style="margin:0;" align="center">
                </a>
            </div>
            <div class="col-4" style="text-align:right;">
                <a href="https://www.minsalud.gov.co/" target="_blank">
                    <img src="./images/minsalud.png" height=57 style="margin-top:28px;" align="center">
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <hr>
            </div>
        </div>
        <div class="row justify-content-end">
            <div class="col-sm">
                <p class="fecha">
                    <small>Fecha radicación <?= date('d/m/Y H:i') ?></small>
                </p>
            </div>
        </div>
        <?php if($errorFormulario==0) { ?>
            <div class="row">
                <div class="col-sm">
                    <div class="alert alert-success" role="alert" style="text-align: justify">
                        <h4>Bien!</h4>
                        Su solicitud ha sido registrada de forma exitosa con el radicado Nº. <strong><?=$numeroRadicado?></strong>. Por favor tenga en cuenta estos datos para que realice la consulta del estado a su solicitud.
                        <br><br>
                        Para conocer los costos de reproducción de la información pública que reposa en la Superintendencia Nacional de Salud, 
                        <a href="http://docs.supersalud.gov.co/PortalWeb/planeacion/OtrosDocumentosPlaneacin/Precios%20fotocopiado%20REV%20SG.docx" target="_blank">consulte la Certificación de actualización de precios de reproducción de la información pública vigencia 2021</a>, que se 
                        encuentra establecida según lo dispuesto en el artículo 4 de la <a href="https://docs.supersalud.gov.co/PortalWeb/Juridica/Resoluciones/res%205015%20de%202018.pdf" target="_blank">Resolución número 005015 de 2018</a>.
                    </div>
                    <p class="lead">
                        <small>
                            Pulse continuar para terminar la solicitud y visualizar el documento en formato PDF. Si desea almacenelo en su disco duro o imprímalo.
                        </small>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <input type="button" name="Submit" value="Continuar" class ="btn btn-success" onclick="window.open('../bodega/<?=$rutaPdf?>')" />
                    <?php if ($tipoSolicitud == 'Denuncia') { ?>
                        <a class="btn btn-default" href="index.php">Volver</a>
                    <?php } else { ?>
                        <a class="btn btn-default" href="index.php">Volver</a>
                    <?php } ?>
                </div>
            </div>
        <?php }  else if ($errorFormulario==1) { ?>
            <div class="row">
                <div class="col-sm">
                    <div class="alert alert-danger" role="alert">
                        <h4>Error!</h4>
                        Existe un error en su código de verificación o está intentando enviar una petición de nuevo.
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <form name=back action="javascript:history.go(-1)()" method=post>
                        <input type="button" class="btn btn-default" value="Atrás">
                    </form>
                </div>
            </div>
        <?php } else if($errorFormulario==2) { ?>
            <div class="row">
                <div class="col-sm">
                    <div class="alert alert-danger" role="alert">
                        <h4>Error!</h4>
                        Ocurrió un error en al subida de archivo
                    </div>
                    <p class="lead">
                        <small>
                            <?php echo implode($uploader->messages); ?>
                        </small>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <form name=back action="javascript:history.go(-1)()" method=post>
                        <input type="button" class="btn btn-default" value="Atrás">
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
<?php include ('footer.php') ?>

<?php /*
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- Meta Tags -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<!--Deshabilitar modo de compatiblidad de Internet Explorer-->
<meta http-equiv="X-UA-Compatible" content="IE=edge" >

<title>Entidad Usuaria de Orfeo -</title>
<link rel="stylesheet" href="css/structure.css" type="text/css" />
</head>

<body>
<p>&nbsp;</p>
<table width="80%" border="0" align="center" cellpadding="0"
	cellspacing="0" bgcolor="#FFFFFF" style="padding: 30px;" >
	<tr>
		<td align="center"><br />
        <img src='<?=$log?>' height='150' align=center>
        </td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>

	<?php if($errorFormulario==0){?>
	<tr>
	<td align="center">
	Su solicitud ha sido registrada de forma exitosa con el radicado No. <b><?=$numeroRadicado?></b> y código de verificación <b><?=$_SESSION['codigoverificacion'] ?></b>.
	Por favor tenga en cuenta estos datos para que realice la consulta del estado a su solicitud.
	</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center">Pulse continuar para <b>terminar la solicitud</b> y
		visualizar el documento en formato PDF. Si desea almacenelo en su
		disco duro o imprímalo.</td>
	</tr>
	<tr>
		<td align="center">&nbsp;</td>
	</tr>
	<tr>
        <td align="center">
            <input type="button" name="Submit"
			value="Ver documento"
			onclick="window.open('../bodega/<?=$rutaPdf?>')" />
	</tr>
	<tr>
		<td align="center">&nbsp;</td>
	</tr>
	<?php } else if ($errorFormulario==1){?>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td><font color=red><b>Existe un error en su c&oacute;digo de
		verificaci&oacute;n o est&aacute; intentando enviar una petici&oacute;n de nuevo.</b></font></td>


		<tr />
		<td>
		<form name=back action="javascript:history.go(-1)()" method=post><input
			type=submit value="Atr&aacute;s"></form>
		</td>
		<?php } else if($errorFormulario==2){?>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td><font color=red><b>Ocurrió un error en al subida de archivo</b></font></td>
			<tr>
			<td>
			<?php echo implode($uploader->messages);?>
			</td>
			</tr>
		</tr>
		<td>
		<form name=back action="javascript:history.go(-1)()" method=post><input
			type=submit value="Atr&aacute;s"></form>
		</td>

		<?php }?>

</table>
</body>
</html>
*/?>
