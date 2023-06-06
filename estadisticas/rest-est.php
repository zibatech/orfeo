<?php
session_start();
set_time_limit(1200);
ini_set("memory_limit", "2048M");
//ini_set('display_errors', '7');
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
$ruta_raiz = "..";
if (!$_SESSION['dependencia']) {
    $fallo['session'] = 'off';
    json_encode($fallo);
    die();//prueba
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
include_once "$ruta_raiz/include/db/ConnectionHandler.php";

include_once "$ruta_raiz/estadisticas/estadisticas.class.php";

$db = new ConnectionHandler($ruta_raiz);
//$db->conn->debug =true;
//print_r($_POST);
$reportesClass = new estadisiticas($ruta_raiz);
$datos = array();
switch ($fn) {
    case 'serie':
//echo $dep_busq;
        //$db->conn->debug =true;
        if ($dep_busq == "" || $dep_busq == "99999") {
            $where = "";
        } else {
            //$where = "and (cast(m.depe_codi as varchar(10)) = '$dep_busq' or cast(m.depe_codi_aplica as varchar(100)) like '%$coddepe%' or cast(m.depe_codi as varchar(10))='$depDireccion')";
            $where = "and (cast(m.depe_codi as varchar(10)) = '$dep_busq') ";
        }
        $iSql = "select distinct lower(s.sgd_srd_descrip||'-'||s.sgd_srd_codigo) as nomb, s.sgd_srd_codigo cod,id
                from sgd_mrd_matrird m, sgd_srd_seriesrd s
                where
                s.id = m.sgd_srd_id
                $auxsqlser
                and s.sgd_srd_estado  = '1'
                and " . $db->sysdate() . " between s.sgd_srd_fechini and s.sgd_srd_fechfin $where
                order by  1,2";

        $rs = $db->conn->query($iSql);
        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {
                foreach ($rs->fields as $key => $value) {
                    $datos[$i][strtoupper($key)] = $value;
                }
                $i++;
                $rs->MoveNext();

            }
        }
        break;
    case 'subSeries':
        //$db->conn->debug =true;
        $iSql = "SELECT distinct (s.sgd_sbrd_descrip||'-'||s.sgd_sbrd_codigo) as nomb, s.sgd_sbrd_codigo cod,s.id FROM sgd_sbrd_subserierd s WHERE s.sgd_srd_id='$serie' and  s.sgd_sbrd_estado  = '1' ";
        $rsSbrd = $db->conn->query($iSql);
        $rs = $db->conn->query($iSql);
        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {
                foreach ($rs->fields as $key => $value) {
                    $datos[$i][strtoupper($key)] = $value;
                }
                $i++;
                $rs->MoveNext();

            }
        }
        break;
    case 'tpdoc':
        //    print_r($_POST);
        if ($serie == 0 && $subserie) {
            $iSql = "SELECT SGD_TPR_DESCRIP nomb, SGD_TPR_CODIGO cod from SGD_TPR_TPDCUMENTO WHERE SGD_TPR_CODIGO<>0 and SGD_TPR_estado=1  order by  SGD_TPR_DESCRIP";
        } else {
            $where = "and (cast(m.depe_codi as varchar(10)) = '$depe') ";
            $iSql = "select distinct (tpr.SGD_TPR_DESCRIP) as nomb, tpr.SGD_TPR_CODIGO cod,tpr.SGD_TPR_CODIGO id
                from sgd_mrd_matrird m, SGD_TPR_TPDCUMENTO tpr
                where
                 m.sgd_srd_id =$serie and m.sgd_tpr_codigo = tpr.sgd_tpr_codigo and  m.sgd_sbrd_id=$subserie   $where
                order by  tpr.SGD_TPR_DESCRIP";
        }
        $rs = $db->conn->query($iSql);
        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {
                foreach ($rs->fields as $key => $value) {
                    $datos[$i][strtoupper($key)] = $value;
                }
                $i++;
                $rs->MoveNext();

            }
        }
        break;
    case 'usuarios':
        $whereDep = ($dependencia_busq != 99999) ? "  u.DEPE_CODI = " . $depe : '';

        $whereUsSelect = $tpus == '0' ? " u.USUA_ESTA = '1' " : "";
        $whereUsSelect = ($usua_perm_estadistica < 1) ?(($whereUsSelect != "") ? $whereUsSelect . " AND u.USUA_LOGIN='$krd' " : " u.USUA_LOGIN='$krd' ") : $whereUsSelect;
        if ($depe != 99999) {

            $whereUsSelect = ($whereUsSelect == "") ? $whereDep : $whereUsSelect . " and  " . $whereDep;
            /*
			$iSql = "select lower(u.USUA_NOMB) nomb,u.USUA_CODI cod,u.USUA_ESTA,u.usua_doc from USUARIO u
                    where  $whereUsSelect
                    order by u.USUA_NOMB";
			*/		
			$iSql = "select lower(u.USUA_NOMB) nomb,u.USUA_CODI cod,u.USUA_ESTA,u.usua_doc from USUARIO u
                    where  u.USUA_ESTA='1' and u.DEPE_CODI=".$depe."
                    order by u.USUA_NOMB";

            $rs = $db->conn->query($iSql);
            if (!$rs->EOF) {
                $i = 0;
                while (!$rs->EOF) {
                    foreach ($rs->fields as $key => $value) {
                        $datos[$i][strtoupper($key)] = $value;
                    }
                    $i++;
                    $rs->MoveNext();

                }
            }
        }
        break;
        case 'depe':
            $sqlConcat = $db->conn->Concat("depe_codi ", "' - '", " lower(depe_nomb)");
            if ($usua_perm_estadistica > 1 || $tpd==9) {
                $sql = "select $sqlConcat nomb ,depe_codi id from dependencia  order by depe_codi";
            } else {
                $sql = "select $sqlConcat nomb,depe_codi id from dependencia where DEPE_CODI=$dependencia order by depe_codi"; 
            }
                $rs = $db->conn->query($sql);
                if (!$rs->EOF) {
                    $i = 1;
                    $datos[0]=array('ID'=>'99999','NOMB'=>'-- Todas las Dependencias --');
                    while (!$rs->EOF) {
                        foreach ($rs->fields as $key => $value) {
                            $datos[$i][strtoupper($key)] = $value;
                        }
                        $i++;
                        $rs->MoveNext();
    
                    }
                }
            
            break;
    case 'rp':
        //  print_r($_POST);
      //  $datos = $reportesClass->rp1($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin);
        $funct='rp'.$reporte;
        $datos = $reportesClass->$funct($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin,$tpRad);
        break;
    case 'dtrp1':
      //  print_r($_POST);
      if($depe==99999 && $tpbusq<>'T')
         $depe=$btns;
        $datos=$reportesClass->dtrp1($depe,$tpAds,$tpdoc,$serie,$subserie,$usu,$fini,$ffin,$tpbusq, $tpRad);

        break;
        case 'dtrp2':
            //  print_r($_POST);
            /*  if($depe==99999 && $tpbusq<>'T')
                $depe=$btns;*/
                $d=$reportesClass->dtrp2($depe,$tpAds,$tpdoc,$serie,$subserie,$usu,$fini,$ffin,$tpbusq, $tpRad);
                $datos=$d;
                break;
    case 'dtrp3':
        //  print_r($_POST);
        /*  if($depe==99999 && $tpbusq<>'T')
            $depe=$btns;*/
            $d=$reportesClass->dtrp3($depe,$tpAds,$tpdoc,$serie,$subserie,$usu,$fini,$ffin,$tpbusq);
            $resp['ENVIADOS']=$d['ENVIADOS'];
            $resp['DEVUELTOS']=$d['DEVUELTOS'];
            $datos=$d['datos'];
            break;
    case 'dtrp4':
            //  print_r($_POST);
            if($depe==99999 && $tpbusq<>'T')
                $depe=$btns;
                $datos=$reportesClass->dtrp4($depe,$tpAds,$tpdoc,$serie,$subserie,$usu,$fini,$ffin,$tpbusq, $tpRad);
        
         break;

    case 'dtrp6':
      if($depe==99999 && $tpbusq<>'T')
         $depe=$btns;
        $datos=$reportesClass->dtrp6($depe,$tpAds,$tpdoc,$serie,$subserie,$usu,$fini,$ffin,$tpbusq, $tpRad);
        break;
    case 'dtrp7':
        $datos=$reportesClass->dtrp7($depe,$tpAds,$tpdoc,$serie,$subserie,$usu,$fini,$ffin,$tpbusq, $tpRad);
        break;

    case 'rp9':
        //  print_r($_POST);
        $datos = $reportesClass->rp9($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin);
        break;
    case 'dtrp9':
        //print_r( $_POST);
        $datos = $reportesClass->dtrp9($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq);
        break;
/// detalles reporte fa-rotate-180
    case 'dtrp10':
        $datos = $reportesClass->dtrp10($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq);
        break;
    case 'rp11':
        //  print_r($_POST);
        $datos = $reportesClass->ConsultaRadi($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, 1);
        break;
        case 'dtrp11':
            $datos = $reportesClass->dtrpCons($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq,1);
            break;
    case 'rp12':
        //  print_r($_POST);
        $datos = $reportesClass->ConsultaRadi($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, 3);
        break;
        case 'dtrp12':
            $datos = $reportesClass->dtrpCons($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq,3);
            break;
            case 'rp13':
                //  print_r($_POST);
                $datos = $reportesClass->ConsultaRadi($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, 3);
                break;
            case 'dtrp13':
                //  print_r($_POST);
                /*  if($depe==99999 && $tpbusq<>'T')
                    $depe=$btns;*/
                    $d=$reportesClass->dtrp2($depe,$tpAds,$tpdoc,$serie,$subserie,$usu,$fini,$ffin,$tpbusq, $tpRad);
                    $datos=$d;
                    break;
        
    default:
        $db->conn->Disconnect();
        die();
        break;
}
$db->conn->Disconnect();
$reportesClass = null;
$resp['data'] = $datos;
echo json_encode($resp);
