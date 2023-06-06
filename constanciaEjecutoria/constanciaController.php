<?php
/**
 * @author JOHANS GONZALEZ MONTERO 
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright
*/

if (!$ruta_raiz)
	$ruta_raiz = "..";

session_start();
require_once($ruta_raiz."/include/db/ConnectionHandler.php");
require_once($ruta_raiz."/processConfig.php");
require_once($ruta_raiz."/constanciaEjecutoria/tx/constanciaEjecutoria.php");
require_once($ruta_raiz."/constanciaEjecutoria/tx/crearConstanciaPdf.php");
require_once($ruta_raiz."/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$db = new ConnectionHandler($ruta_raiz);
$constancia = new ConstanciaEjecutoria($db);

$funcion = $_POST['inFuncion'];
if($funcion == 1) {
    $noResolucion = $_POST['noResolucion'];
    echo json_encode($constancia->buscarDataResolucion($noResolucion));
}
if($funcion == 2) { 
    $date = str_replace('-', '/', $_POST['inFechaActo']);
    $inFechaActo = date("d/m/Y", strtotime($date));
    $date = str_replace('-', '/', $_POST['inFechaAcuse']);
    $inFechaAcuse = date("d/m/Y", strtotime($date));    
    $date = str_replace('-', '/', $_POST['inFechaApelacion']);
    $inFechaApelacion = date("d/m/Y", strtotime($date));      
    $date = str_replace('-', '/', $_POST['inFechaReposicion']);
    $inFechaReposicion = date("d/m/Y", strtotime($date));
    $date = str_replace('-', '/', $_POST['inFechaRecursoQueja']);
    $inFechaRecursoQueja = date("d/m/Y", strtotime($date));    

    $data = array (
        array($_POST['inIdple'], $_POST['inResolucionInicial'], $inFechaActo, $_POST['inNitCC'], $_POST['seRazonSocial'], 
        $_POST['seTipoNotifiacion'], $inFechaAcuse, $_POST['sePresentaRecurso'], $_POST['inResolucionApleacion'], $inFechaApelacion, $_POST['inResolucionReposicion'],
        $inFechaReposicion, $_POST['seRecursoQueja'], $_POST['inResolucionRecurosQueja'], $_POST['seNotificacionFinal'], $inFechaRecursoQueja, trim($_POST['inExpediente']),
        $_SESSION["dependencia"], $_SESSION["codusuario"])
    );
    $constancia->guardarSolicitud($data, $_SESSION["dependencia"], $_SESSION["codusuario"]);
    echo "200";
}

if($funcion == 3) {

    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($_FILES["btSolicitudMasiva"]["tmp_name"]);
    $sheetData = $spreadsheet->getActiveSheet()->toArray();
    $i=0;
    unset($sheetData[0]);
    unset($sheetData[1]);
    unset($sheetData[2]);
    unset($sheetData[3]);
    unset($sheetData[4]);
    unset($sheetData[5]);
    unset($sheetData[6]);
    $info = "";
    $data = array();
    $contadorAux = 8;
    foreach ($sheetData as $t) {

        $date = str_replace('-', '/',  $t[2]);
        $inFechaActo = date("d/m/Y", strtotime($date));
        $date = str_replace('-', '/', $t[6]);
        $inFechaAcuse = date("d/m/Y", strtotime($date));    
        $date = str_replace('-', '/', $t[9]);
        $inFechaApelacion = date("d/m/Y", strtotime($date));      
        $date = str_replace('-', '/', $t[11]);
        $inFechaReposicion = date("d/m/Y", strtotime($date));
        $date = str_replace('-', '/', $t[15]);
        $inFechaRecursoQueja = date("d/m/Y", strtotime($date));  
        
        if( $inFechaActo === '31/12/1969') $inFechaActo = '';
        if( $inFechaAcuse === '31/12/1969') $inFechaAcuse = '';
        if( $inFechaApelacion === '31/12/1969') $inFechaApelacion = '';
        if( $inFechaReposicion === '31/12/1969') $inFechaReposicion = '';
        if( $inFechaRecursoQueja === '31/12/1969') $inFechaRecursoQueja = '';
        
        $expAus = trim($t[16]);
        if($expAus === "") {
             $info = $info . "Expediente es obligario para resolución " .  $t[1] . " Fila " . $contadorAux . "</br>";
        } else {
            $res = $constancia->evaluarExpediente($t[1], $expAus, $contadorAux);
            foreach ($res as $k => $v) {
                if($k == "ok") {
                    $data[$i] = array($t[0], $t[1], $inFechaActo, $t[3], $t[4], $t[5], $inFechaAcuse, $t[7], $t[8], $inFechaApelacion,
                    $t[10], $inFechaReposicion, $t[12], $t[13], $t[14], $inFechaRecursoQueja, $expAus, $_SESSION["dependencia"],  $_SESSION["codusuario"]);
                    $i++;
                } else {
                    $info = $info . $v;
                }
            }
        }
        $contadorAux++;
    }
    $constancia->guardarSolicitud($data, $_SESSION["dependencia"], $_SESSION["codusuario"]);
    if($info == "") {
        echo "200";
    } else {
        echo $info . "</br>NO SE REALIZA REGISTRO DE ESTOS DATOS";
    }
}

if($funcion == 4) { 

    $info = $constancia->verSolicitudPorEnviar($_SESSION["dependencia"], $_SESSION["codusuario"]);
    $data = array();
    $i = 0;
    while($info && !$info->EOF){
        $date = strtotime($info->fields["FECHA_SOLICITUD"]);
        $data[$i] = array(date('d/m/Y h:i:s', $date), $info->fields["GRUPO"], $info->fields["ITEM"], $info->fields["NO_ID_PLE"], $info->fields["RESOLUCION_INICIAL"],
                        $info->fields["FECHA_ACTO"], $info->fields["IDENTIFICACION"], $info->fields["RAZON_SOCIAL"], $info->fields["TIPO_NOTIFICACION"], $info->fields["FECHA_ACUSE"],
                        $info->fields["PRESENTA_RECURO"], $info->fields["RESO_APELACION"], $info->fields["FECHA_APELACION"], $info->fields["RESO_REPOSICION"], $info->fields["FECHA_REPOSICION"],
                        $info->fields["RECURSO_QUEJA_REVOC"], $info->fields["RESO_QUEJA_REVOC"], $info->fields["TIPO_NOTIFICACION_FINAL"], $info->fields["FECHA_NOTIFICACION_FINAL"], $info->fields["EXPEDIENTE"]);
        $i++;
        $info->MoveNext();
    }
    echo json_encode($data);
}

if($funcion == 5) { 
    $constancia->proyectorARevision($_SESSION["dependencia"], $_SESSION["codusuario"]);
    echo "200";
}

if($funcion == 6) { 

    $info = $constancia->verSolicitudDevuelta($_SESSION["dependencia"], $_SESSION["codusuario"]);
    $data = array();
    $i = 0;
    while($info && !$info->EOF){
        $date = strtotime($info->fields["FECHA_SOLICITUD"]);
        $data[$i] = array($info->fields["ID"], $info->fields["ALERTA"],
                        date('d/m/Y h:i:s', $date), $info->fields["GRUPO"], $info->fields["ITEM"], $info->fields["NO_ID_PLE"], $info->fields["RESOLUCION_INICIAL"],
                        $info->fields["FECHA_ACTO"], $info->fields["IDENTIFICACION"], $info->fields["RAZON_SOCIAL"], $info->fields["TIPO_NOTIFICACION"], $info->fields["FECHA_ACUSE"],
                        $info->fields["PRESENTA_RECURO"], $info->fields["RESO_APELACION"], $info->fields["FECHA_APELACION"], $info->fields["RESO_REPOSICION"], $info->fields["FECHA_REPOSICION"],
                        $info->fields["RECURSO_QUEJA_REVOC"], $info->fields["RESO_QUEJA_REVOC"], $info->fields["TIPO_NOTIFICACION_FINAL"], $info->fields["FECHA_NOTIFICACION_FINAL"],
                        $info->fields["EXPEDIENTE"], $info->fields["COMENTARIO"], $info->fields["FECHA_NOTIFICACION_ULTIMO"], $info->fields["FECHA_EJECUTORIA"]);
        $i++;
        $info->MoveNext();
    }
    echo json_encode($data);
}

if($funcion == 7) { 
    $constancia->eliminarSolicitud($_POST['idSolicitud'], $_SESSION["dependencia"], $_SESSION["codusuario"]);
    echo "200";
}

if($funcion == 8) { 
    $constancia->cambiarEstadoLeido($_POST['idSolicitud']);
    echo json_encode($constancia->verSolicitudPorId($_POST['idSolicitud']));
}

if($funcion == 9) { 
    $date = str_replace('-', '/', $_POST['inFechaActo']);
    $inFechaActo = date("d/m/Y", strtotime($date));
    $date = str_replace('-', '/', $_POST['inFechaAcuse']);
    $inFechaAcuse = date("d/m/Y", strtotime($date));    
    $date = str_replace('-', '/', $_POST['inFechaApelacion']);
    $inFechaApelacion = date("d/m/Y", strtotime($date));      
    $date = str_replace('-', '/', $_POST['inFechaReposicion']);
    $inFechaReposicion = date("d/m/Y", strtotime($date));
    $date = str_replace('-', '/', $_POST['inFechaRecursoQueja']);
    $inFechaRecursoQueja = date("d/m/Y", strtotime($date));    

    if( $inFechaActo === '31/12/1969') $inFechaActo = '';
    if( $inFechaAcuse === '31/12/1969') $inFechaAcuse = '';
    if( $inFechaApelacion === '31/12/1969') $inFechaApelacion = '';
    if( $inFechaReposicion === '31/12/1969') $inFechaReposicion = '';
    if( $inFechaRecursoQueja === '31/12/1969') $inFechaRecursoQueja = '';

    $data = array($_POST['idSolicitudEdit'], $_POST['inIdple'], $_POST['inResolucionInicial'], $inFechaActo, $_POST['inNitCC'], $_POST['seRazonSocial'], 
        $_POST['seTipoNotifiacion'], $inFechaAcuse, $_POST['sePresentaRecurso'], $_POST['inResolucionApleacion'], $inFechaApelacion, $_POST['inResolucionReposicion'],
        $inFechaReposicion, $_POST['seRecursoQueja'], $_POST['inResolucionRecurosQueja'], $_POST['seNotificacionFinal'], $inFechaRecursoQueja, $_POST['inExpediente']);
    $constancia->editarSolicitud($data);
    echo "200";    
}

if($funcion == 10) { 
    $data = $_POST['arraySolicitud'];
    
    foreach ($data as $idSolicitud) {
        $constancia->proyectorARevisionEditado($idSolicitud, $_SESSION["dependencia"], $_SESSION["codusuario"]);
    }
    echo "200";
} 

if($funcion == 11) {
    echo json_encode($constancia->obtenerDepenenciaPorEstado($_POST['estado']));
}

if($funcion == 12) {
    echo json_encode($constancia->obtenerGrupoPorEstado($_POST['estado']));
}

if($funcion == 13) {

    if($_POST['sePrinc'] == 1) {
        $info =  $constancia->verSolicitudRolRevisor($_POST['inFechaInicio'], $_POST['inFechaFinal'], 
            $_POST['seDependencia'], $_POST['seGrupo']); 
    } else {
        $info =  $constancia->verSolicitudRolRevisorDevuelta($_POST['inFechaInicio'], $_POST['inFechaFinal'], 
            $_POST['seDependencia'], $_POST['seGrupo']);     
    }
    $data = array();
    $i = 0;
    while($info && !$info->EOF){
        $nomDependencia = $constancia->obtenerNombreDependencia($info->fields["DEPE_CODI"]);
        $nomUsuario = $constancia->obtenerNombreUsuario($info->fields["USUA_CODI"], $info->fields["DEPE_CODI"]);
        $date = strtotime($info->fields["FECHA_SOLICITUD"]);
        $data[$i] = array($info->fields["ID"], $info->fields["ALERTA"],
                        date('d/m/Y h:i:s', $date), $info->fields["GRUPO"], $info->fields["ITEM"], $nomDependencia, $nomUsuario, $info->fields["NO_ID_PLE"], $info->fields["RESOLUCION_INICIAL"],
                        $info->fields["FECHA_ACTO"], $info->fields["IDENTIFICACION"], $info->fields["RAZON_SOCIAL"], $info->fields["TIPO_NOTIFICACION"], $info->fields["FECHA_ACUSE"],
                        $info->fields["PRESENTA_RECURO"], $info->fields["RESO_APELACION"], $info->fields["FECHA_APELACION"], $info->fields["RESO_REPOSICION"], $info->fields["FECHA_REPOSICION"],
                        $info->fields["RECURSO_QUEJA_REVOC"], $info->fields["RESO_QUEJA_REVOC"], $info->fields["TIPO_NOTIFICACION_FINAL"], $info->fields["FECHA_NOTIFICACION_FINAL"],
                        $info->fields["EXPEDIENTE"], $info->fields["COMENTARIO"], $info->fields["FECHA_NOTIFICACION_ULTIMO"], $info->fields["FECHA_EJECUTORIA"], 
                        $info->fields["UBICACION"] , $info->fields["NUMERO_CONSTANCIA"]);
        $i++;
        $info->MoveNext();
    }
    echo json_encode($data);        
}

if($funcion == 14) {
    $date = str_replace('-', '/', $_POST['inFechaActo']);
    $inFechaActo = date("d/m/Y", strtotime($date));
    $date = str_replace('-', '/', $_POST['inFechaAcuse']);
    $inFechaAcuse = date("d/m/Y", strtotime($date));    
    $date = str_replace('-', '/', $_POST['inFechaApelacion']);
    $inFechaApelacion = date("d/m/Y", strtotime($date));      
    $date = str_replace('-', '/', $_POST['inFechaReposicion']);
    $inFechaReposicion = date("d/m/Y", strtotime($date));
    $date = str_replace('-', '/', $_POST['inFechaRecursoQueja']);
    $inFechaRecursoQueja = date("d/m/Y", strtotime($date));    
    $date = str_replace('-', '/', $_POST['inFechaUltimoActo']);
    $inFechaUltimoActo2 = date("d/m/Y", strtotime($date));   
    $date = str_replace('-', '/', $_POST['inFechaEjecutoria']);
    $inFechaEjecutoria2 = date("d/m/Y", strtotime($date));        

    if( $inFechaActo === '31/12/1969') $inFechaActo = '';
    if( $inFechaAcuse === '31/12/1969') $inFechaAcuse = '';
    if( $inFechaApelacion === '31/12/1969') $inFechaApelacion = '';
    if( $inFechaReposicion === '31/12/1969') $inFechaReposicion = '';
    if( $inFechaRecursoQueja === '31/12/1969') $inFechaRecursoQueja = '';
    if( $inFechaUltimoActo2 === '31/12/1969') $inFechaUltimoActo2 = '';
    if( $inFechaEjecutoria2 === '31/12/1969') $inFechaEjecutoria2 = '';

    $editValido = false;
    $infoInvalido = '';
    $res =  $constancia->evaluarExpediente($_POST['inResolucionInicial'], 
            trim($_POST['inExpediente']));

    foreach ($res as $k => $v) {
        if($k == "ok") {
            $editValido = true;     
        } else {
            $infoInvalido = $v;
        } 
    } 
    
    if($editValido == true) {
        $data = array($_POST['idSolicitudEdit'], $_POST['inIdple'], $_POST['inResolucionInicial'], $inFechaActo, $_POST['inNitCC'], $_POST['seRazonSocial'], 
        $_POST['seTipoNotifiacion'], $inFechaAcuse, $_POST['sePresentaRecurso'], $_POST['inResolucionApleacion'], $inFechaApelacion, $_POST['inResolucionReposicion'],
        $inFechaReposicion, $_POST['seRecursoQueja'], $_POST['inResolucionRecurosQueja'], $_POST['seNotificacionFinal'], $inFechaRecursoQueja, $_POST['inExpediente'],
        $inFechaUltimoActo2, $inFechaEjecutoria2);
        $constancia->editarSolicitudFunNoti($data);  
        echo "200";
    } else {
        echo $infoInvalido;
    }
}

if($funcion == 15) {
    echo $constancia->validarMismoGrupo($_POST['arraySolicitud']);
}

if($funcion == 16) {
    $constancia->retonarSolicitud($_POST['arraySolicitud'], $_POST['comentario'], $_SESSION["dependencia"], $_SESSION["codusuario"]);
    echo "200";
}

if($funcion == 17){
    $data = $_POST['arraySolicitud'];
    
    foreach ($data as $idSolicitud) {
        $constancia->envioRolesNoti($idSolicitud, $_POST['rol'], $_SESSION["dependencia"], $_SESSION["codusuario"]);
    }
    echo "200";
}

if($funcion == 18) {
    $pdf = new ConstanciaPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
    $constancia->crearPDF($_POST['idSolicitud'], $pdf, $ruta_raiz, $ABSOL_PATH);
    echo "200";
}

if($funcion == 19) {
    echo $constancia->obtenerUbicacionConstancia($_POST['idSolicitud']);
}

if($funcion == 20) {
    $info =  $constancia->verSolicitudDevueltaGeneral($_POST['inFechaInicio'], $_POST['inFechaFinal'], 
        $_POST['seDependencia'], $_POST['seGrupo']); 
    echo json_encode($info);
}

if($funcion == 21) {
    $info =  $constancia->verConstanciasGeneralNoti($_POST['inFechaInicio'], $_POST['inFechaFinal'], 
        $_POST['seDependencia'], $_POST['seGrupo'], $_POST['estadoDife']); 
    echo json_encode($info);
}

if($funcion == 22) {
    $data = $_POST['arraySolicitud'];
    
    foreach ($data as $idSolicitud) {
        $pdf = new ConstanciaPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
        $constancia->crearPDF($idSolicitud, $pdf, $ruta_raiz, $ABSOL_PATH);        
    }    
    echo "200";
}

if($funcion == 23) {

    if($_POST['sePrinc'] == 1) {
        $info =  $constancia->verSolicitudRolAprobador($_POST['inFechaInicio'], $_POST['inFechaFinal'], 
            $_POST['seDependencia'], $_POST['seGrupo']);  
    } else {
        $info =  $constancia->verSolicitudRolAprobadorDevuelta($_POST['inFechaInicio'], $_POST['inFechaFinal'], 
            $_POST['seDependencia'], $_POST['seGrupo']);    
    } 


    $data = array();
    $i = 0;
    while($info && !$info->EOF){
        $nomDependencia = $constancia->obtenerNombreDependencia($info->fields["DEPE_CODI"]);
        $nomUsuario = $constancia->obtenerNombreUsuario($info->fields["USUA_CODI"], $info->fields["DEPE_CODI"]);

        $date = strtotime($info->fields["FECHA_SOLICITUD"]);
        $data[$i] = array($info->fields["ID"], $info->fields["ALERTA"],
                        date('d/m/Y h:i:s', $date), $info->fields["GRUPO"], $info->fields["ITEM"], $nomDependencia, $nomUsuario, $info->fields["NO_ID_PLE"], $info->fields["RESOLUCION_INICIAL"],
                        $info->fields["FECHA_ACTO"], $info->fields["IDENTIFICACION"], $info->fields["RAZON_SOCIAL"], $info->fields["TIPO_NOTIFICACION"], $info->fields["FECHA_ACUSE"],
                        $info->fields["PRESENTA_RECURO"], $info->fields["RESO_APELACION"], $info->fields["FECHA_APELACION"], $info->fields["RESO_REPOSICION"], $info->fields["FECHA_REPOSICION"],
                        $info->fields["RECURSO_QUEJA_REVOC"], $info->fields["RESO_QUEJA_REVOC"], $info->fields["TIPO_NOTIFICACION_FINAL"], $info->fields["FECHA_NOTIFICACION_FINAL"],
                        $info->fields["EXPEDIENTE"], $info->fields["COMENTARIO"], $info->fields["FECHA_NOTIFICACION_ULTIMO"], $info->fields["FECHA_EJECUTORIA"], 
                        $info->fields["UBICACION"] , $info->fields["NUMERO_CONSTANCIA"], $info->fields["APROBACION"]);
        $i++;
        $info->MoveNext();
    }
    echo json_encode($data);        
}

if($funcion == 24) {
    //$var = "";
    $data = $_POST['arraySolicitud'];
    if($constancia->validarMismoGrupo($data) == 'f') {
        echo "f";
    } else {
        $validacion = 0;
        foreach ($data as $idSolicitud) {
            if($constancia->estaListoAprobacion($idSolicitud) == 'f') {
                $validacion++;
                break;
            }
        }
        if($validacion > 0) {
            echo "f2";
        } else {
            $constancia->aprobarSolicitud($data, $_SESSION["dependencia"], $_SESSION["codusuario"]);
            echo "200";
        }
    }

}

if($funcion == 25) {
    echo $constancia->validarMismoGrupoAprobador($_POST['arraySolicitud']);
}

if($funcion == 26) {
    echo $constancia->validarMismoGrupoRetornar($_POST['arraySolicitud']);
}

if($funcion == 27) {
    echo $constancia->validarMismoGrupoGenerar($_POST['arraySolicitud']);
}

if($funcion == 28) {
    $constancia->retonarSolicitudaRevisor($_POST['arraySolicitud'], $_POST['comentario'], $_SESSION["dependencia"], $_SESSION["codusuario"]);
    echo "200";
}

if($funcion == 29) {
    $info =  $constancia->verSolicitudRolFirmante($_POST['inFechaInicio'], $_POST['inFechaFinal'], 
        $_POST['seDependencia'], $_POST['seGrupo']); 
    $data = array();
    $i = 0;
    while($info && !$info->EOF){
        $nomDependencia = $constancia->obtenerNombreDependencia($info->fields["DEPE_CODI"]);
        $nomUsuario = $constancia->obtenerNombreUsuario($info->fields["USUA_CODI"], $info->fields["DEPE_CODI"]);

        $date = strtotime($info->fields["FECHA_SOLICITUD"]);
        $data[$i] = array($info->fields["ID"], $info->fields["ALERTA"],
                        date('d/m/Y h:i:s', $date), $info->fields["GRUPO"], $info->fields["ITEM"], $nomDependencia, $nomUsuario, $info->fields["NO_ID_PLE"], $info->fields["RESOLUCION_INICIAL"],
                        $info->fields["FECHA_ACTO"], $info->fields["IDENTIFICACION"], $info->fields["RAZON_SOCIAL"], $info->fields["TIPO_NOTIFICACION"], $info->fields["FECHA_ACUSE"],
                        $info->fields["PRESENTA_RECURO"], $info->fields["RESO_APELACION"], $info->fields["FECHA_APELACION"], $info->fields["RESO_REPOSICION"], $info->fields["FECHA_REPOSICION"],
                        $info->fields["RECURSO_QUEJA_REVOC"], $info->fields["RESO_QUEJA_REVOC"], $info->fields["TIPO_NOTIFICACION_FINAL"], $info->fields["FECHA_NOTIFICACION_FINAL"],
                        $info->fields["EXPEDIENTE"], $info->fields["COMENTARIO"], $info->fields["FECHA_NOTIFICACION_ULTIMO"], $info->fields["FECHA_EJECUTORIA"], 
                        $info->fields["UBICACION"] , $info->fields["NUMERO_CONSTANCIA"], $info->fields["APROBACION"]);
        $i++;
        $info->MoveNext();
    }
    echo json_encode($data);        
}

if($funcion == 30) {
    $constancia->retonarSolicitudaProbador($_POST['arraySolicitud'], $_POST['comentario'], $_SESSION["dependencia"], $_SESSION["codusuario"]);
    echo "200";
}

if($funcion == 31) {

    $usua_doc = $_SESSION["usua_doc"];
    $firmasd = $ABSOL_PATH . 'bodega/firmas/';
    $P12_FILE =  $firmasd . 'server.p12';
    
    if (!file_exists($P12_FILE)) {
        $P12_FILE = $firmasd . $usua_doc . '.p12';
    }
    
    if ($P12_PASS) {
        $clave = $P12_PASS;
    }
    
    $data = $_POST['arraySolicitud'];
    foreach ($data as $idSolicitud) {
        //$pdf = new ConstanciaPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
        //$constancia->crearPDF($idSolicitud, $pdf, $ruta_raiz, $ABSOL_PATH);
        $constancia->finalizarSolicitud($idSolicitud, $_SESSION["dependencia"], $_SESSION["codusuario"]);
        $constancia->firmaDigital($idSolicitud, $ABSOL_PATH, $P12_FILE, $clave, $_SESSION["dependencia"], $_SESSION["codusuario"]);
        $constancia->anexarExpediente($idSolicitud, $ABSOL_PATH, $_SESSION['digitosDependencia'], 
                $_SESSION["dependencia"], $_SESSION["codusuario"], $_SESSION["usua_doc"]);
        $constancia->agergarHistorial($idSolicitud, $_SESSION["dependencia"], $_SESSION["codusuario"], 'Se finaliza la solicitud');                
        sleep(2);
    }
    echo "200";
}

if($funcion == 32) {
    $info =  $constancia->verCostanciasPorUsuairo($_POST['inFechaInicio'], $_POST['inFechaFinal'], $_SESSION["dependencia"], $_SESSION["codusuario"]); 
    $data = array();
    $i = 0;
    while($info && !$info->EOF){

        $date = strtotime($info->fields["FECHA_SOLICITUD"]);

        if($info->fields["FECHA_NOTI_RESP"] == "")
            $dateRestp = "";
        else {
            $dateRestp = strtotime($info->fields["FECHA_NOTI_RESP"]);
            $dateRestp = date('d/m/Y', $dateRestp);
        }    

        $data[$i] = array($info->fields["ID"], date('d/m/Y h:i:s', $date), $info->fields["GRUPO"], $info->fields["ITEM"], $info->fields["NO_ID_PLE"], $info->fields["RESOLUCION_INICIAL"],
                        $info->fields["FECHA_ACTO"], $info->fields["IDENTIFICACION"], $info->fields["RAZON_SOCIAL"], $info->fields["TIPO_NOTIFICACION"], $info->fields["FECHA_ACUSE"],
                        $info->fields["PRESENTA_RECURO"], $info->fields["RESO_APELACION"], $info->fields["FECHA_APELACION"], $info->fields["RESO_REPOSICION"], $info->fields["FECHA_REPOSICION"],
                        $info->fields["RECURSO_QUEJA_REVOC"], $info->fields["RESO_QUEJA_REVOC"], $info->fields["TIPO_NOTIFICACION_FINAL"], $info->fields["FECHA_NOTIFICACION_FINAL"],
                        $info->fields["EXPEDIENTE"], $info->fields["FECHA_NOTIFICACION_ULTIMO"], $info->fields["FECHA_EJECUTORIA"], 
                        $dateRestp, $info->fields["UBICACION"]);
        $i++;
        $info->MoveNext();
    }
    echo json_encode($data);     

}

if($funcion == 33) {

    if($_POST['sePrinc'] == 1) {
        $info =  $constancia->verSolicitudRolRevisor($_POST['inFechaInicio'], $_POST['inFechaFinal'], 
            $_POST['seDependencia'], $_POST['seGrupo']); 
    } else {
        $info =  $constancia->verSolicitudRolRevisorDevuelta($_POST['inFechaInicio'], $_POST['inFechaFinal'], 
            $_POST['seDependencia'], $_POST['seGrupo']);     
    }    
    $data = array();
    $data[0] = array("FECHA DE SOLICITUD","GRUPO","ITEM","DEPENDENCIA","USUARIO","NO ID_PLE","RESOLUCION INICIAL",
                       "FECHA ACTO","IDENTIFICACION","RAZON SOCIAL","TIPO DE NOTIFICACION","FECHA ACUSE","PRESENTA RECURSO","RESO APELACION","FECHA APELACION",
                       "RESO REPOSICION","FECHA REPOSICION","RECUROS QUEJA REVO","RESO QUEJA REVOC","TIPO NOTIFICAION FINAL","FECHA NOTIFICACION FINAL","EXPEDIENTE",
                       "FECHA NOTIFICACION ULTIMO","FECHA EJECUTORIA", "NUMEROD DE CONSTANCIA");
    $i = 1;
    while($info && !$info->EOF){
        $nomDependencia = $constancia->obtenerNombreDependencia($info->fields["DEPE_CODI"]);
        $nomUsuario = $constancia->obtenerNombreUsuario($info->fields["USUA_CODI"], $info->fields["DEPE_CODI"]);
        $date = strtotime($info->fields["FECHA_SOLICITUD"]);
        $data[$i] = array(date('d/m/Y h:i:s', $date), $info->fields["GRUPO"], $info->fields["ITEM"], $nomDependencia, $nomUsuario, $info->fields["NO_ID_PLE"], "*". $info->fields["RESOLUCION_INICIAL"],
                        $info->fields["FECHA_ACTO"], $info->fields["IDENTIFICACION"], $info->fields["RAZON_SOCIAL"], $info->fields["TIPO_NOTIFICACION"], $info->fields["FECHA_ACUSE"],
                        $info->fields["PRESENTA_RECURO"], "*". $info->fields["RESO_APELACION"], $info->fields["FECHA_APELACION"], "*". $info->fields["RESO_REPOSICION"], $info->fields["FECHA_REPOSICION"],
                        $info->fields["RECURSO_QUEJA_REVOC"], "*". $info->fields["RESO_QUEJA_REVOC"], $info->fields["TIPO_NOTIFICACION_FINAL"], $info->fields["FECHA_NOTIFICACION_FINAL"],
                        $info->fields["EXPEDIENTE"], $info->fields["FECHA_NOTIFICACION_ULTIMO"], $info->fields["FECHA_EJECUTORIA"], $info->fields["NUMERO_CONSTANCIA"]);
        $i++;
        $info->MoveNext();
    }
    
    $spreadsheet = new Spreadsheet(); 
    $spreadsheet->getSheet(0);
    $sheet = $spreadsheet->getActiveSheet();

    for($i = 0; $i < count($data); $i++) {
        for($j = 0; $j < count($data[$i]); $j++) {
            $sheet->setCellValueByColumnAndRow($j+1 , $i+1 , $data[$i][$j]);
        }
    }

    $writer = new Xlsx($spreadsheet); 
    $writer->save($ABSOL_PATH . 'bodega/tmp/workDir/revisorSolicitudes.xlsx'); 
    echo "200";     
}

if($funcion == 34) {
 

    $data =  $constancia->verSolicitudDevueltaGeneral($_POST['inFechaInicio'], $_POST['inFechaFinal'], 
        $_POST['seDependencia'], $_POST['seGrupo']); 

    $dataHeader = array();
    $dataHeader[0] = array("FECHA DE SOLICITUD","GRUPO","ITEM","DEPENDENCIA","USUARIO","NO ID_PLE","RESOLUCION INICIAL",
                       "FECHA ACTO","IDENTIFICACION","RAZON SOCIAL","TIPO DE NOTIFICACION","FECHA ACUSE","PRESENTA RECURSO","RESO APELACION","FECHA APELACION",
                       "RESO REPOSICION","FECHA REPOSICION","RECUROS QUEJA REVO","RESO QUEJA REVOC","TIPO NOTIFICAION FINAL","FECHA NOTIFICACION FINAL","EXPEDIENTE",
                       "FECHA NOTIFICACION ULTIMO","FECHA EJECUTORIA", "COMENTARIO");
    
    $spreadsheet = new Spreadsheet(); 
    $spreadsheet->getSheet(0);
    $sheet = $spreadsheet->getActiveSheet();

    for($i = 0; $i < count($dataHeader); $i++) {
        for($j = 0; $j < count($dataHeader[$i]); $j++) {
            $sheet->setCellValueByColumnAndRow($j+1 , $i+1 , $dataHeader[$i][$j]);
        }
    }    

    for($i = 0; $i < count($data); $i++) {
        for($j = 0; $j < count($data[$i]); $j++) {
            if($j ==6)
                $data[$i][$j] = "*" . $data[$i][$j];
            if($j ==13)
                $data[$i][$j] = "*" . $data[$i][$j];   
            if($j ==15)
                $data[$i][$j] = "*" . $data[$i][$j]; 
            if($j ==18)
                $data[$i][$j] = "*" . $data[$i][$j];                                                             
            $sheet->setCellValueByColumnAndRow($j+1 , $i+2 , $data[$i][$j]);
        }
    }

    $writer = new Xlsx($spreadsheet); 
    $writer->save($ABSOL_PATH . 'bodega/tmp/workDir/revisorDevolucion.xlsx'); 
    echo "200";     
}

if($funcion == 35) {
    $data =  $constancia->verConstanciasGeneralNoti($_POST['inFechaInicio'], $_POST['inFechaFinal'], 
        $_POST['seDependencia'], $_POST['seGrupo'], 2); 

    $dataHeader = array();
    $dataHeader[0] = array("ID", "FECHA DE SOLICITUD","GRUPO","ITEM","DEPENDENCIA","USUARIO","NO ID_PLE","RESOLUCION INICIAL",
                        "FECHA ACTO","IDENTIFICACION","RAZON SOCIAL","TIPO DE NOTIFICACION","FECHA ACUSE","PRESENTA RECURSO","RESO APELACION","FECHA APELACION",
                        "RESO REPOSICION","FECHA REPOSICION","RECUROS QUEJA REVO","RESO QUEJA REVOC","TIPO NOTIFICAION FINAL","FECHA NOTIFICACION FINAL","EXPEDIENTE",
                        "FECHA NOTIFICACION ULTIMO","FECHA EJECUTORIA", "COMENTARIO", "FECHA DE RESPUESTA", "APROBADOR", "NUMERO DE CONSTANCIA", "ESTADO", "UBICACION");
    
    $spreadsheet = new Spreadsheet(); 
    $spreadsheet->getSheet(0);
    $sheet = $spreadsheet->getActiveSheet();

    for($i = 0; $i < count($dataHeader); $i++) {
        for($j = 0; $j < count($dataHeader[$i]); $j++) {
            $sheet->setCellValueByColumnAndRow($j+1 , $i+1 , $dataHeader[$i][$j]);
        }
    }    

    for($i = 0; $i < count($data); $i++) {
        for($j = 0; $j < count($data[$i]); $j++) {
            if($j ==0)
                $data[$i][$j] = "";
            if($j ==7)
                $data[$i][$j] = "*" . $data[$i][$j];
            if($j ==14)
                $data[$i][$j] = "*" . $data[$i][$j];   
            if($j ==16)
                $data[$i][$j] = "*" . $data[$i][$j]; 
            if($j ==19)
                $data[$i][$j] = "*" . $data[$i][$j];    
            if($j ==30)
                $data[$i][$j] = "";                                                                           
            $sheet->setCellValueByColumnAndRow($j+1 , $i+2 , $data[$i][$j]);
        }
    }

    $writer = new Xlsx($spreadsheet); 
    $writer->save($ABSOL_PATH . 'bodega/tmp/workDir/revisorGeneral.xlsx'); 
    echo "200";             
}

if($funcion == 36) {

    if($_POST['sePrinc'] == 1) {
        $info =  $constancia->verSolicitudRolAprobador($_POST['inFechaInicio'], $_POST['inFechaFinal'], 
            $_POST['seDependencia'], $_POST['seGrupo']);  
    } else {
        $info =  $constancia->verSolicitudRolAprobadorDevuelta($_POST['inFechaInicio'], $_POST['inFechaFinal'], 
            $_POST['seDependencia'], $_POST['seGrupo']);    
    }        
 
    $data = array();
    $data[0] = array("FECHA DE SOLICITUD","GRUPO","ITEM","DEPENDENCIA","USUARIO","NO ID_PLE","RESOLUCION INICIAL",
                        "FECHA ACTO","IDENTIFICACION","RAZON SOCIAL","TIPO DE NOTIFICACION","FECHA ACUSE","PRESENTA RECURSO","RESO APELACION","FECHA APELACION",
                        "RESO REPOSICION","FECHA REPOSICION","RECUROS QUEJA REVO","RESO QUEJA REVOC","TIPO NOTIFICAION FINAL","FECHA NOTIFICACION FINAL","EXPEDIENTE",
                        "FECHA NOTIFICACION ULTIMO","FECHA EJECUTORIA", "FECHA DE RESPUESTA", "NUMERO DE CONSTANCIA", "APROBACION"); 
    $i = 1;
    while($info && !$info->EOF){
        $nomDependencia = $constancia->obtenerNombreDependencia($info->fields["DEPE_CODI"]);
        $nomUsuario = $constancia->obtenerNombreUsuario($info->fields["USUA_CODI"], $info->fields["DEPE_CODI"]);

        $date = strtotime($info->fields["FECHA_SOLICITUD"]);
        $data[$i] = array(date('d/m/Y h:i:s', $date), $info->fields["GRUPO"], $info->fields["ITEM"], $nomDependencia, $nomUsuario, $info->fields["NO_ID_PLE"], $info->fields["RESOLUCION_INICIAL"],
                        $info->fields["FECHA_ACTO"], $info->fields["IDENTIFICACION"], $info->fields["RAZON_SOCIAL"], $info->fields["TIPO_NOTIFICACION"], $info->fields["FECHA_ACUSE"],
                        $info->fields["PRESENTA_RECURO"], $info->fields["RESO_APELACION"], $info->fields["FECHA_APELACION"], $info->fields["RESO_REPOSICION"], $info->fields["FECHA_REPOSICION"],
                        $info->fields["RECURSO_QUEJA_REVOC"], $info->fields["RESO_QUEJA_REVOC"], $info->fields["TIPO_NOTIFICACION_FINAL"], $info->fields["FECHA_NOTIFICACION_FINAL"],
                        $info->fields["EXPEDIENTE"], $info->fields["FECHA_NOTIFICACION_ULTIMO"], $info->fields["FECHA_EJECUTORIA"], $info->fields["FECHA_NOTI_RESP"], 
                        $info->fields["NUMERO_CONSTANCIA"], $info->fields["APROBACION"]);
        $i++;
        $info->MoveNext();
    }        
    
    $spreadsheet = new Spreadsheet(); 
    $spreadsheet->getSheet(0);
    $sheet = $spreadsheet->getActiveSheet();

    for($i = 0; $i < count($data); $i++) {
        for($j = 0; $j < count($data[$i]); $j++) {
            if($j ==6)
                $data[$i][$j] = "*" . $data[$i][$j];
            if($j ==13)
                $data[$i][$j] = "*" . $data[$i][$j];   
            if($j ==15)
                $data[$i][$j] = "*" . $data[$i][$j]; 
            if($j ==18)
                $data[$i][$j] = "*" . $data[$i][$j];                                                                           
            $sheet->setCellValueByColumnAndRow($j+1 , $i+1 , $data[$i][$j]);
        }
    }

    $writer = new Xlsx($spreadsheet); 
    $writer->save($ABSOL_PATH . 'bodega/tmp/workDir/aprobadorSolicitudes.xlsx'); 
    echo "200";         
}

if($funcion == 37) {
   $data =  $constancia->evaluarExpediente($_POST['resolucion'], 
            trim($_POST['inExpediente']));
   echo json_encode($data);         
}

if($funcion == 38) {
    $data = $constancia->obtenerHistorial($_POST['idSolicitud']);
    $info = array();
    $i = 0;
    while($data && !$data->EOF){
        $info[$i] = array($data->fields["FECHA"], $data->fields["OPERACION"], $data->fields["USUA_NOMB"]);
        $data->MoveNext();
        $i++;
    }
    echo json_encode($info);     
}

if($funcion == 39) { 
    $constancia->borrarSolicitudPri($_SESSION["dependencia"], $_SESSION["codusuario"]);
    echo "200";
}

?>