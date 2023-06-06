<?php
/** */
session_start();
//set_time_limit(1200);
//ini_set("memory_limit", "2048M");
/*ini_set('display_errors', '7');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
$ruta_raiz = "..";
if (!$_SESSION['dependencia']) {
    $fallo['session'] = 'off';
    json_encode($fallo);
    die(); //prueba
}

$krd = $_SESSION["krd"];
foreach ($_GET as $key => $valor) {
    ${$key} = $valor;
}

foreach ($_POST as $key => $valor) {
    ${$key} = $valor;
}

$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$tip3Nombre = $_SESSION["tip3Nombre"];
$tip3desc = $_SESSION["tip3desc"];
$tip3img = $_SESSION["tip3img"];
$usua_perm_estadistica = $_SESSION["usua_perm_estadistica"];
$usua_codi = $_SESSION['codusuario'];
$usua_doc = $_SESSION['usua_doc'];
$usua_depe = $_SESSION['dependencia'];
//include_once "$ruta_raiz/include/db/ConnectionHandler.php";

include_once "$ruta_raiz/expediente/expediente.class.php";

//$db = new ConnectionHandler($ruta_raiz);
//$db->conn->debug =true;
//print_r($_POST);
$expClass = new expediente($ruta_raiz);
$datos = array();
switch ($fn) {
    case 'OldExp':
        $datos = $expClass->crearOld($exp, $usua, $depe);
        $dataExp = $expClass->consultarExp($exp);

        $data['expdata'] = $dataExp;
        $data['success'] = true;
        $datos = $data;
        break;
    case 'listar':
        $datos = $expClass->listar($tp, $usua_doc, $usua, $depe,$anoDep);

        break;
        case 'bsqexp':
           // pendiente validacion
            $datos = $expClass->bsqlistar($tp,$numExp, $radicado,$parametro, $usuar, $depe);
    
         break;
         case 'bsqexpV1':
            // pendiente validacion
             $datos = $expClass->bsqlistarV1($tp,$numExp,$parametro);
     
          break;
    case 'listaDtExp':
        $daots = $expClass->listardtexp($exp, $filtro, $orden, $atp, $page, $wbsq);
        //   print_r($daots);
        $data['anexos'] = $daots['anexos'];
        $data['numRad'] = $daots['numRad'];
        $data['numReg'] = $daots['numReg'];
        $data['pagActal'] = $daots['pagActal'];
        $data['pag'] = $daots['pag'];
        $data['dtexp'] = $daots['datos'];
        $data['success'] = true;
        $datos = $data;
        break;
    case 'listaHistExp':
        if ($_POST['tipo'] == 'tb_listaHistoexpArch') {
            $daots = $expClass->listarhistexpArch($_POST['exp']);
        } elseif ($_POST['tipo'] == 'tb_listaHistoexpCons') {
            $daots = $expClass->listarhistexp($_POST['exp']);
        } else {
            $daots = $expClass->listarhistexp($_POST['exp']);
        }

        //   print_r($daots);
        unset($daots['ERROR']);
        $datos['dtexp'] = $daots;
        $datos['success'] = true;
        break;
    case 'crearConfirmar':
        //print_r($_POST);
        $codiSRD = $_POST['serie'];
        $codiSBRD = $_POST['subserie'];
        $dependencia = $_POST['depExp'];
        $anoExp = $_POST['anoExp'];
        $seguridad=$_POST['seguridad'];
        $daots = $expClass->numExp($dependencia, $codiSRD, $codiSBRD, $anoExp);
 

        $datos['numexp'] = $daots;
        $datos['success'] = true;
        break;

    case 'listaSeguridadExp':
        $daots = $expClass->aclExpediente($_POST['exp']);
        //   print_r($daots);
        $resp['dtexp'] = $daots;
        $resp['success'] = true;
        break;
     case 'uploadAnex':
        $noExpediente=$_POST['exp'];
        $id=time();
        $exts=$expClass->getTpAnex();
        $size = return_bytes($_FILES['image']['size']);
        $path_nomb     = $_FILES['image']['name'];
        $ext      = pathinfo($path_nomb, PATHINFO_EXTENSION);
        $anextipo = array_search(strtolower('.'.$ext), $exts);
        $namefile2 = $noExpediente.'_'.strtotime("now") . '.'  . $ext;
        $namefile=$_FILES['image']['name'];
        $tmp_name=$_FILES['image']['tmp_name'];
        $cons = $expClass->consAnexNumeExpe($_POST['exp']);
        $depe_dir = substr($noExpediente,4,$_SESSION['digitosDependencia']);
        //echo strlen($noExpediente);
        if(strlen($noExpediente)==20)
            $depe_dir = intval(substr($noExpediente,4,5), 10);

        $uploadDir = "$ruta_raiz/bodega/".substr($noExpediente,0,4)."/".$depe_dir."/docs/";
        
      $full_path=$uploadDir.$namefile2;
        if(!file_exists($uploadDir))
            mkdir($uploadDir,'777',true);
            //echo $uploadDir;
            $vtrd=$_POST['vtrd']?$_POST['vtrd']:1;
            $tpdco=$_POST['tpDocAnex']!='undefined'?$_POST['tpDocAnex']:0;
          // echo"$tmp_name, $full_path";
        if (move_uploaded_file($tmp_name, $full_path)){
            $hashs=hash_file('sha256',$full_path);
            $anex=$expClass->creaanexo($noExpediente,$cons['con'],$id,$anextipo,$size,$krd,"{$_POST['descriop']}",$namefile2,$hashs,$path,'VIRTUAL','','',$tpdco,$vtrd);
          // print_r($_POST);
            // print_r($_FILES);
            hist($expClass,$noExpediente,array($cons['con']), $usua_depe, $usua_codi,'INCLUIR ANEXO DE EXPEDIENTE', 91, 0);
            $resp['conse'] = $cons['con'];
             $resp['num'] = $namefile2 ;
             $resp['success'] = true;
        }
        else{
            $resp['success'] = false;
        }

         
            break;
    case 'crear':
        $codiSRD = $_POST['serie'];
        $codiSBRD = $_POST['subserie'];
        $dependencia = $_POST['depExp'];
        $exptilulo = $_POST['dt1'];
        $arrParametro[2] = $_POST['dt2'];
        $arrParametro[3] = $_POST['dt3'];
        $arrParametro[4] = $_POST['dt4'];
        $arrParametro[5] = $_POST['dt5'];
        $anoExp = $_POST['anoExp'];
        $seg = $_POST['seguridad'];
        $resp = $_POST['responsable'];
        $fechaExp = $_POST['fechaExp'];

        $numExp = $expClass->numExp($dependencia, $codiSRD, $codiSBRD, $anoExp);
        $secExp = substr($numExp, 13, 6);
//echo "<hr>$numExp  $secExp<hr>";
        $numExp2 = $expClass->crearExpediente($numExp, $respo, $dependencia, $codiSRD, $codiSBRD, $secExp, $anoExp, $_SESSION['dependencia'], $codusuario, $exptilulo, 0, $usua_doc, $fechaExp, $arrParametro);
        //    crearExpediente($numExpediente, $coduser, $depe_codi, $codiSRD, $codiSBRD, $secExp, $anoExp,  $dependencia, $codUsuario, $titulo, $estadoExp = 0, $usua_doc, $fechaExp = null, $arrParametro = null) {
            if($seguridad==1){  $expClass->addAclExp($numExp2 , $dependencia,0, 3);}
            if($seguridad==2){ $expClass->addAclExp($numExp2 , $dependencia,$expClass->jefe($dependencia), 3);
                $expClass->addAclExp($numExp2 , 0, 0, 0);  }
                if($codiSRD==16){  $expClass->addAclExp($numExp2 , 94000,0, 3);}
                
        //  $m = $this->acladd($numExp, $dependencia, $respo, 3);
        $datos['numexp'] = $numExp2; //$this->numExp($dependencia, $codiSRD, $codiSBRD, $anoExp);
        $datos['success'] = $numExp == 0 ? false : true;
        break;
        case 'Excluir':
           // echo 1;
            $usua_codi = $_SESSION['codusuario'];
            $usua_doc = $_SESSION['usua_doc'];
            $usua_depe = $_SESSION['dependencia'];
            $radarray = explode(',', $_POST['listRad']);
            $anexarray = explode(',', $_POST['listAnex']);
            $numExpediente = $_POST['exp'];

if($_POST['listRad']){
    $expClass->ExcluirExpR($numExpediente, $_POST['listRad']);
    hist($expClass,$numExpediente,$radarray, $usua_depe, $usua_codi,'EXCLUIR RADICADO DE EXPEDIENTE', 52, 0);
}
if($_POST['listAnex']){
    $expClass->ExcluirExpAnexo($numExpediente, $_POST['listAnex']);
    /* historico */
    hist($expClass,$numExpediente,$anexarray, $usua_depe, $usua_codi,'EXCLUIR ANEXO DE EXPEDIENTE', 77, 0);

}          
            $datos['numexp'] = $numExp2; //$this->numExp($dependencia, $codiSRD, $codiSBRD, $anoExp);
            $datos['success'] =  true;
            break;
        case 'validarExp':
            $numExp = $_POST['numExp'];
            $datos1 = $expClass->consultarExpRadicadosVal($numExp);
            // print_r($datos);
            $data['radicados'] = $expClass->valExpAnu($numExp);

            $rad = '';
            $i=0;
            if ($datos1['ERROR'] != 'No se encontro radidcados en el Expediente') {
                foreach ($datos1 as $key => $value) {
                    $estado1 = '';
                    if ($value['RADI_DEPE_ACTU'] != 999) {
                        $estado1 = ' En tramite';
                    }
                    if ($value['SGD_EANU_CODIGO'] == 1) {
                        $estado1 = ' En Solicitud de anulación';
                    }
                    if ($value['SGD_EANU_CODIGO'] == 2) {
                        $estado1 = ' Anulación';
                    }
                    if ($estado1)
                        $rad .= "<tr><td>{$value['RADI_NUME_RADI']}</td><td>$estado1</td></tr>";
                }
                $i++;
            }

            $datos['pasa'] = 'si';
            $datos['message'] = '<strong>Expediente ' . $numExp . ' </strong> No contiene radicados en Gestión ';
            $datos['datosrad'] = 'sinRad';
            $datos['radicados'] = $i;
            if ($rad) {
                $datos['pasa'] = 'no';
                $datos['message'] = '<strong>Expediente ' . $numExp . ' </strong> contiene Radicados en Gestión ' . $datosx;
                $datos['datosrad'] = "<table class='listadoA'><tr><td>Radicado</td><td>Observación</td></tr>$rad</table>";
                $datos['radicados'] = $rad;
            }
            $data['success'] = $numExp == 0 ? false : true;
            break;

            case 'cambiarEst':
                //   echo 1;
               
                $codigoFldExp = 0;
                $numExpediente = $_POST['exp'];
                $oldEst = $_POST['estado'];
                $estado = $_POST['newest'];
                $expClass->ExpCambioEstado($numExpediente, $estado);
                // $data['numexp'] = $this->numExp($dependencia, $codiSRD, $codiSBRD, $anoExp);
                if ($estado == 0) {
                    $observacion = 'Se Reabrio el Expediente ';
                    $tipoTx = 59;
                    $nomEstado = 'Abierto';
                }
                if ($estado == 1) {
                    $observacion = 'Se Cerro el Expediente';
                    $tipoTx = 58;
                    $nomEstado = 'Cerrado';
                }
                if ($estado == 2) {
                    $observacion = 'Se Anula el Expediente ';
                    $tipoTx = 71;
                    $nomEstado = 'Anulado';
                    //validar para anular
                }
                if ($estado == 0 and $oldEst == 2) {
                    $observacion = 'Se Desanula el Expediente ';
                    $tipoTx = 72;
                    $nomEstado = 'Abierto'; 
                }
                $radicados = array();
                $expClass->insertarHistoricoExp($numExpediente,$radicados, $usua_depe, $usua_codi, $observacion, $tipoTx, $codigoFldExp);
                     $data['success'] = true;
                break;


     case 'saveSeguridadexp':
          //   print_r($_POST);
            $resp['estadoOper'] = 'old';
            $code = $expClass->valacldExp($_POST['exp'], $_POST['depe'], $_POST['usuario']);
          
           
            if (!$code) {
                $resp['estadoOper'] = 'new';
                $expClass->addAclExp($_POST['exp'], $_POST['depe'], $_POST['usuario'], $_POST['tpseg']); // $_POST['selsegact']
                $code = $expClass->valacldExp($_POST['exp'], $_POST['depe'], $_POST['usuario'], $_POST['tpseg']);
            }
            $rad =explode(',',$_POST['listRad']);
            hist($expClass,$numExpediente,$rad, $usua_depe, $usua_codi,'Modificacion de Seguridad ()', 110, 0);
           
            $resp['codigo'] = $code;
            $resp['success'] = true;
            break;
            case 'modSeguridadexp':
                $expClass->aclmod($_POST['aclid'],$_POST['tpseg']);
                $datos['success'] = true;
                $rad =explode(',',$_POST['listRad']);
            hist($expClass,$numExpediente,$rad, $usua_depe, $usua_codi,'Modificacion de Seguridad ()', 110, 0);
           
            break;
            case 'modMeta':
                $expClass->modExp($exp,$camp,$cambio);
                $datos['success'] = true;
             break;            
             case 'modRad':
                $expClass->modradExp($exp,$rad,$camp,$cambio);
                $datos['success'] = true;
             break;     
             case 'fisico':
                // print_r($_POST);
                $numExpediente = $_POST['exp'];
              //  $rads = explode(',', $_POST['listRad']);
                //$expanexs = explode(',', $_POST['listAnex']);
                $fisico=$_POST['fisico'];
                $expClass->fisicoexp($numExpediente, $fisico,  $_POST['listRad'], $_POST['listAnex']);
                $rad =explode(',',$_POST['listRad']);
                $this->hist($expClass,$numExpediente,$rad, $usua_depe, $usua_codi,'Modificacion de Fisico', 110, 0);
               
                $data['expantfisico'] = true;
                $data['success'] = true;
                break;  
            case 'subexp':
                    //print_r($_POST);
                    $numExpediente = $_POST['exp'];
                  //  $rads = explode(',', $_POST['listRad']);
                //    $expanexs = explode(',', $_POST['listAnex']); 
                    $expClass->modSubExp($numExpediente, $_POST['subexp'] , $_POST['listRad'], $_POST['listAnex']);
                    $data['success'] = true;
                    $rad =explode(',',$_POST['listRad']);
                    $this->hist($expClass,$numExpediente,$rad, $usua_depe, $usua_codi,'Modificacion de sub Expediente', 110, 0);
                   
            break; 
            case 'carpeta':
                //print_r($_POST);
                $numExpediente = $_POST['exp'];
                //  $rads = explode(',', $_POST['listRad']);
              //    $expanexs = explode(',', $_POST['listAnex']); 
                $expClass->modCarp($numExpediente, $_POST['carpeta'] , $_POST['listRad'], $_POST['listAnex']);
                $rad =explode(',',$_POST['listRad']);
                hist($expClass,$numExpediente,$rad, $usua_depe, $usua_codi,'Modificacion de carpeta', 110, 0);
                $data['success'] = true;
                break;

    default:
        $db->conn->Disconnect();
        die();
        break;
}
//$db->conn->Disconnect();
$expClass = null;
$resp['data'] = $datos;
echo json_encode($resp);

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
function hist($expClass,$numExpediente,$radicados= array(), $usua_depe, $usua_codi, $observacion, $tipoTx, $codigoFldExp){
        $expClass->insertarHistoricoExp($numExpediente,$radicados, $usua_depe, $usua_codi, $observacion, $tipoTx, $codigoFldExp);
}
