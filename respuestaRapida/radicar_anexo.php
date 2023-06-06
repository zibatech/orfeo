<?php

define('ADODB_ASSOC_CASE', 1);
define('SALIDA', 1);
define('MEMORANDO', 3);
define('CIRC_INTERNA', 4);
define('CIRC_EXTERNA', 5);
define('RESOLUCION', 6);
define('AUTO', 7);

require_once($ruta_raiz."/processConfig.php");
include_once($ruta_raiz."/include/tx/Expediente.php");
include_once($ruta_raiz."/class_control/anexo.php");
include_once($ruta_raiz."/include/tx/Tx.php");
include_once($ruta_raiz."/include/tx/Radicacion.php");
require_once($ruta_raiz."/tcpdf/tcpdf.php");
include_once($ruta_raiz."/include/tx/TipoDocumental.php");

require_once($ruta_raiz."/include/db/ConnectionHandler.php");
$db      = new ConnectionHandler($ruta_raiz);
$hist    = new Historico($db);
$Tx      = new Tx($db);

$sqlFechaHoy      = $db->sysdate();
$numRadicadoPadre = $_POST["radPadre"];
$tipoRadPadre     = substr($numRadicadoPadre, -1);
$tipo_radicado  = (isset($_POST['tipo_radicado'])) ? $_POST['tipo_radicado'] : null;
$anexo   = $_POST['anexo'];

#Lógica para cambiar borrador por el número de radicado
$numRadicadoPadreInicial = substr($numRadicadoPadre, 0, 4);
if($numRadicadoPadreInicial >= 3000 &&($tipo_radicado == SALIDA || $tipo_radicado == CIRC_INTERNA || $tipo_radicado == CIRC_EXTERNA ||
    $tipo_radicado == RESOLUCION || $tipo_radicado == AUTO || $tipo_radicado == MEMORANDO)) {

    $radAux = new Radicacion($db);
    $dependenciaRadica = substr($numRadicadoPadre, 4, $digitosDependencia);
    $radAux->radiDepeRadi  = $dependenciaRadica;
    $numRadicadoPadreAnt = $numRadicadoPadre;
    $numRadicadoPadre = $radAux->generateNewRadicadoNotificacion($tipo_radicado, $dependenciaRadica);

    $tipoRadPadre     = substr($numRadicadoPadre, -1);
    $radAux ->borradorArradicado($numRadicadoPadreAnt, $numRadicadoPadre, $ruta_raiz);

    $anexo = substr($anexo, -5);
    $anexo = $numRadicadoPadre . $anexo;

    $radicadosSelBorr[0] = $numRadicadoPadre;
    $hist->insertarHistorico(
        $radicadosSelBorr,
        $_SESSION["dependencia"] * 1,
        $_SESSION["codusuario"],
        $_SESSION["dependencia"] * 1,
        $_SESSION["codusuario"],
        'De Borrador a radicado No ' . $numRadicadoPadre,
        104
    );

}

$tamanoMax      = 7 * 1024 * 1024; // 7 megabytes
$fechaGrab      = trim($date1);
$numramdon      = rand(0, 100000);
$contador       = 0;
$regFile        = array();
$conCopiaA      = '';
$enviadoA       = '';
$cCopOcu        = '';

$ddate          = date('d');
$mdate          = date('m');
$adate          = date('Y');
$fechproc4      = substr($adate, 2, 4);
$fecha1         = time();
$fecha          = fechaFormateada($fecha1);
$tdoc           = NO_DEFINIDO;
$pais           = 170; //OK, codigo pais
$cont           = 1; //id del continente
$radicado_rem   = 7;
$auxnumero      = str_pad($auxnumero, 5, "0", STR_PAD_LEFT);
$tipo           = ARCHIVO_PDF;
$tamano         = 1000;
$auxsololect    = 'N';
$radicado_rem   = 1;
$descr          = 'Pdf respuesta';
$fechrd         = $ddate.$mdate.$fechproc4;
$coddepe        = $_SESSION["dependencia"] ;
$usua_actu      = $_SESSION["codusuario"];
$usua           = $_SESSION["krd"];
$codigoCiu      = $_SESSION["usua_doc"];
$ln             = $_SESSION["digitosDependencia"];

$usMailSelect   = $_POST['usMailSelect']; //correo del emisor de la respuesta
$destinat       = $_POST["destinatario"]; //correos de los destinanexnexnexnexnexnexnextarios
$correocopia    = $_POST["concopia"]; //destinatarios con copia
$conCopOcul     = $_POST["concopiaOculta"]; //con copia oculta
$anexHtml       = $_POST["anexHtml"]; //con copia oculta
$docAnex        = $_POST["docAnex"]; //con copia oculta
$medioRadicar   = $_POST["medioRadicar"]; //con copia oculta
$tipo_envio     = $_POST["anex_tipo_envio"]; //con tipo de envio (Correo ó correo y físico)
$exp            = $_POST["expAnexo"];

$asu            = $_POST["respuesta"];

$tpDepeRad      = $coddepe;
$radUsuaDoc     = $codigoCiu;
$usua_doc       = $_SESSION["usua_doc"];
$usuario        = $_SESSION["usua_nomb"];
$setAutor       = 'Sistema de Gestion Documental CRA';
$SetTitle       = 'Respuesta a solicitud';
$SetSubject     = 'Respuesta a solicitud de CRA';
$SetKeywords    = 'respuesta, salida, generar';

if ($tipo_radicado == CIRC_INTERNA || $tipo_radicado == CIRC_EXTERNA ||
    $tipo_radicado == RESOLUCION || $tipo_radicado == AUTO) {
    $esNotificacion = true;
} else {
    $esNotificacion = false;
}

//DATOS EMPRESA
$sigla          = 'null';
$iden           = $db->conn->nextId("sec_ciu_ciudadano");//uniqe key

//ENLACE DEL ANEXO
$radano = substr($numRadicadoPadre, 0, 4);
$ruta   = $anexo . '.pdf';

$desti = "SELECT
    s.id as id_direccion,
    s.sgd_dir_nomremdes,
    s.sgd_dir_direccion,
    s.sgd_dir_tipo,
    s.sgd_dir_mail,
    s.sgd_dir_telefono,
    s.sgd_sec_codigo,
    s.sgd_ciu_codigo,
    s.dpto_codi,
    s.muni_codi,
    s.id_pais,
    s.id_cont,
    r.depe_codi,
    r.radi_path,
    r.ra_asun
    FROM
    SGD_DIR_DRECCIONES s,
    RADICADO r
    WHERE
    r.RADI_NUME_RADI     = $numRadicadoPadre
    AND s.RADI_NUME_RADI = r.RADI_NUME_RADI";
$rssPatth       = $db->conn->Execute($desti);
$i=0;

while(!$rssPatth->EOF) {
    $dir_id[$i]         = $rssPatth->fields["ID_DIRECCION"];
    $dir_nombre[$i]     = $rssPatth->fields["SGD_DIR_NOMREMDES"];
    $dir_tipo[$i]       = $rssPatth->fields["SGD_DIR_TIPO"];
    $dir_mail[$i]       = $rssPatth->fields["SGD_DIR_MAIL"];
    $dir_telefono[$i]   = $rssPatth->fields["SGD_DIR_TELEFONO"];
    $dir_direccion[$i]  = $rssPatth->fields["SGD_DIR_DIRECCION"];

    if(empty($dir_direccion[$i])) {
        $dir_direccion[$i]  = $dir_mail[$i];
    }

    if($ciu_codigo[$i]) {
        $ciu_codigo[$i]  = $rssPatth->fields["SGD_CIU_CODIGO"];
    } else {
        $ciu_codigo[$i]="NULL";
    }

    if($rssPatth->fields["SGD_OEM_CODIGO"]) {
        $oem_codigo[$i]  = $rssPatth->fields["SGD_OEM_CODIGO"];
    } else {
        $oem_codigo[$i]= "NULL";
    }

    $dir_docFun[$i]     = $rssPatth->fields["SGD_DOC_FUN"];
    $dir_idPais[$i]     = $rssPatth->fields["ID_PAIS"];
    $dir_idCont[$i]     = $rssPatth->fields["ID_CONT"];
    $dir_muniCodi[$i]   = $rssPatth->fields["MUNI_CODI"];
    $dir_dptoCodi[$i]   = $rssPatth->fields["DPTO_CODI"];
    $pathPadre          = $rssPatth->fields["RADI_PATH"];
    $radi_asun          = $rssPatth->fields["RA_ASUN"];

    $rssPatth->MoveNext();
    $i++;
}

$depCreadora    = ltrim(substr($numRadicadoPadre, 4, $digitosDependencia), '0');

if(empty($depCreadora)) {
    $depCreadora = $_SESSION['dependencia'];
}

$ruta2  = "/bodega/$radano/$depCreadora/docs/".$ruta;
$ruta3  = "/$radano/$depCreadora/docs/".$ruta;


// Validar si el radicado tiene una imagen asignada que corresponda
// al mismo numero de radicado como anexo. ejemplo el radicado
// 20309000000031 tiene un anexo con el mismo numero que corresponde
// a la imagen generada como respuesta pdf. Para el caso que no exista
// se crea, si ya existe puede generar otro numero de salida.

$sql1001 = "SELECT
    COUNT(1) AS NUMERO
    FROM
    ANEXOS
    WHERE
    ANEX_RADI_NUME = {$numRadicadoPadre}
    AND ANEX_RADI_NUME = RADI_NUME_SALIDA";
$rs1001 = $db->conn->Execute($sql1001);

if($tipoRadPadre == 2 || $rs1001->fields["NUMERO"] > 0) {
    // creacion del radicado respuesta
    $isql_consec = "SELECT
        DEPE_RAD_TP$tipo_radicado as secuencia
        FROM
        DEPENDENCIA
        WHERE
        DEPE_CODI = $tpDepeRad";

    $creaNoRad   = $db->conn->Execute($isql_consec);
    $tpDepeRad   = $creaNoRad->fields["secuencia"];
    $rad = new Radicacion($db);
    $rad->radiTipoDeri  = 0;        // ok ????
    $rad->radiCuentai   = 'null';   // ok, Cuenta Interna, Oficio, Referencia
    $rad->eespCodi      = $iden;    //codigo emepresa de servicios publicos bodega
    $rad->mrecCodi      = 3;        // medio de correspondencia, 3 internet
    $rad->radiFechOfic  = "now()"; // igual fecha radicado;
    $rad->radiNumeDeri  = $numRadicadoPadre; //ok, radicado padre
    $rad->radiPais      = $pais;    //OK, codigo pais
    $rad->descAnex      = '.';      //OK anexos
    $rad->raAsun        = "Respuesta al radicado " . $numRadicadoPadre; // ok asunto

    if($tipo_radicado == 1) {
        $rad->radiDepeActu = $conf_DependenciaArchivo;
        $rad->radiUsuaActu = $conf_usuarioArchivo;
    } else {
        $rad->radiDepeActu  = $coddepe;   // ok dependencia actual responsable
        $rad->radiUsuaActu  = $usua_actu; // ok usuario actual responsable
    }

    $rad->radiDepeRadi  = $coddepe;   //ok dependencia que radica
    $rad->usuaCodi      = $usua_actu; // ok usuario actual responsable
    $rad->dependencia   = $coddepe;   //ok dependencia que radica
    $rad->trteCodi      =  0;         //ok, tipo de codigo de remitente
    $rad->tdocCodi      = $tdoc;      //ok, tipo documental
    $rad->tdidCodi      = 0;          //ok, ????
    $rad->carpCodi      = 1;          //ok, carpeta entradas
    $rad->carPer        = 0;          //ok, carpeta personal
    $rad->ra_asun       = "Respuesta al radicado " . $numRadicadoPadre;
    $rad->radiPath      = 'null';
    $rad->usuaDoc       = $radUsuaDoc;

    $nurad = $rad->newRadicado($tipo_radicado, $tpDepeRad);

} else {
    $nurad = $numRadicadoPadre;

}

$codTx = 2;
$fecha_rad_salida = date("d-m-Y");

if($fecha_rad_salida) {
    $respuesta = str_replace('F_RAD_S', $fecha_rad_salida, $respuesta);
}
if($nurad) {
    $respuesta = str_replace('RAD_S', $nurad, $respuesta);
}

if($fecha_rad_salida) {
    $asu = str_replace('F_RAD_S', $fecha_rad_salida, $asu);
}
if($nurad) {
    $asu = str_replace('RAD_S', $nurad, $asu);
}

if($nurad) {
    $asu = str_replace('USUA_NOMB_S', $usuario, $asu);
}
if($nurad) {
    $asu = str_replace('DEPE_NOMB_S', $depenomb, $asu);
}

if($nurad) {
    $numradNofi = substr($nurad, 0, 16) . "-" . substr($nurad, -1);
    $respuesta = str_replace('RA_NOTI_S', $numradNofi, $respuesta);
    $asu = str_replace('RA_NOTI_S', $numradNofi, $asu);
}


$sqlInfoAdicionalReasignar = "select radi_depe_radi, radi_usua_radi from radicado where radi_nume_radi = " . $numRadicadoPadre;
$rsInfoAdicionalReasignar = $db->conn->Execute($sqlInfoAdicionalReasignar);
while(!$rsInfoAdicionalReasignar->EOF) {
    $depeRadi = $rsInfoAdicionalReasignar->fields["RADI_DEPE_RADI"];
    $usuRadi = $rsInfoAdicionalReasignar->fields["RADI_USUA_RADI"];
    $rsInfoAdicionalReasignar->MoveNext();
}

$sqlRevisoAprobo = "select usua_codi_dest, depe_codi_dest FROM hist_eventos 
        where radi_nume_radi = " . $numRadicadoPadre . " and  (sgd_ttr_codigo = 16 or sgd_ttr_codigo = 9) and (usua_codi_dest != " . $usua_actu . " or depe_codi_dest != " . $coddepe . ") and (usua_codi_dest != " . $usuRadi . " or depe_codi_dest != " .  $depeRadi . ") group by usua_codi_dest, depe_codi_dest";
$rsSqlRevisoAprobo = $db->conn->Execute($sqlRevisoAprobo);
$arrayRevisoAprobo = array();
while (!$rsSqlRevisoAprobo->EOF) {
    $usuaCodiDest = $rsSqlRevisoAprobo->fields["USUA_CODI_DEST"];
    $depeCodiDest = $rsSqlRevisoAprobo->fields["DEPE_CODI_DEST"];

    $sqlRevisoAproboNom = "select usua_nomb from usuario 
            where usua_codi = " . $usuaCodiDest . " and depe_codi = " . $depeCodiDest;
    $rsSqlRevisoAproboNom = $db->conn->Execute($sqlRevisoAproboNom);
    $RevisoAproboNom = $rsSqlRevisoAproboNom->fields["USUA_NOMB"];
    array_push($arrayRevisoAprobo, $RevisoAproboNom);
    $rsSqlRevisoAprobo->MoveNext();
}

/*if ($esNotificacion) {

     $usuaReviso = "";
     $contadorRevisoAprobo = 1;
     $totalRevisoAprobo = count($arrayRevisoAprobo);
     foreach ($arrayRevisoAprobo as &$valor) {
        if($contadorRevisoAprobo == 1 && $totalRevisoAprobo != 1)
            $usuaReviso .= $valor . " - ";
        elseif ($contadorRevisoAprobo == 1 && $totalRevisoAprobo == 1) {
            $usuaReviso .= $valor;
            $usuaAprobo = $valor;
        } elseif ($contadorRevisoAprobo == $totalRevisoAprobo) {
            $usuaAprobo = $valor;
        } else {
            $usuaReviso .= $valor . " - ";
        }
        $contadorRevisoAprobo++;
     }

      $asu = str_replace('USUA_REVISO', $usuaReviso, $asu);
      $asu = str_replace('USUA_APROBO', $usuaAprobo, $asu);
} else {*/

$usuaReviso = "";
$contadorRevisoAprobo = 1;
$totalRevisoAprobo = count($arrayRevisoAprobo);
foreach ($arrayRevisoAprobo as &$valor) {
    if($contadorRevisoAprobo == $totalRevisoAprobo) {
        $usuaReviso .= $valor;
    } else {
        $usuaReviso .= $valor . " - ";
    }
    $contadorRevisoAprobo++;
}

$asu = str_replace('USUA_REVISO', $usuaReviso, $asu);
$asu = str_replace('USUA_APROBO', $usuario, $asu);
//}





if ($esNotificacion) {
    $fecha = explode(" ", date("d F Y"));
    $_mes = array(
        "January"   => "Enero",
        "February"  => "Febrero",
        "March"     => "Marzo",
        "April"     => "Abril",
        "May"       => "Mayo",
        "June"      => "Junio",
        "July"      => "Julio",
        "August"    => "Agosto",
        "September" => "Septiembre",
        "October"   => "Octubre",
        "November"  => "Noviembre",
        "December"  => "Diciembre"
    );
    $dia = $fecha[0];
    $mes = $_mes[$fecha[1]];
    $anho = $fecha[2];

    if($fecha_rad_salida) {
        $respuesta = str_replace('DIA_S', $dia, $respuesta);
    }
    if($fecha_rad_salida) {
        $respuesta = str_replace('MES_S', $mes, $respuesta);
    }
    if($fecha_rad_salida) {
        $respuesta = str_replace('ANHO_S', $anho, $respuesta);
    }

    if($fecha_rad_salida) {
        $asu = str_replace('DIA_S', $dia, $asu);
    }
    if($fecha_rad_salida) {
        $asu = str_replace('MES_S', $mes, $asu);
    }
    if($fecha_rad_salida) {
        $asu = str_replace('ANHO_S', $anho, $asu);
    }

    $isql = " SELECT DEPE_NOMB
            FROM DEPENDENCIA
            WHERE DEPE_CODI = $coddepe ";

    $rs   = $db->conn->Execute($isql);

    if ($rs && !$rs->EOF) {
        $depenomb  = $rs->fields["DEPE_NOMB"];
    }

}

$firma_krd = !isset($desdeMasiva) ? $_SESSION['krd'] : $usuaKrdMasiva;
$grafo = $ruta_raiz . '/bodega/firmas/grafo/' . strtolower($firma_krd) . '.png';
if (file_exists($grafo)) {
    $grafo_html = '<img src="'.$grafo.'" alt="firma">';
    $respuesta = str_replace('${FIRMA}', $grafo_html, $respuesta);
    $asu = str_replace('${FIRMA}', $grafo_html, $asu);
}


$archivo_txt = $anexo . '.txt';
$archivo_grabar_txt = "../bodega/$radano/$depCreadora/docs/" . $archivo_txt;

$file_content   = fopen($archivo_grabar_txt, 'w');
$write_result   = fwrite($file_content, $respuesta);
$closing_result = fclose($file_content);

if ($nurad == "-1") {
    header("Location: salidaRespuesta.php?$encabe&error=1");
    die;
}

//datos para guardar los anexos en la carpeta del nuevo radicado
$primerno  = substr($nurad, 0, 4);
$segundono = $_SESSION["dependencia"];
$ruta1     = $primerno . "/" . $segundono . "/docs/";
$adjuntos  = 'bodega/'.$ruta1;

//se buscan los datos del radicado padre y se
//insertaran en los del radicado hijo
$direcciones = [];
if($tipoRadPadre == 2 || $rs1001->fields["NUMERO"] > 0) {
    for($iK=0;$iK<=($i-1); $iK++) {

        $nextval   = $db->nextId("sec_dir_drecciones");
        $dirTipo = $iK+1;
        $isql = "insert into SGD_DIR_DRECCIONES(
            SGD_TRD_CODIGO,
            SGD_DIR_NOMREMDES,
            SGD_DIR_DOC,
            DPTO_CODI,
            MUNI_CODI,
            id_pais,
            id_cont,
            SGD_DOC_FUN,
            SGD_OEM_CODIGO,
            SGD_CIU_CODIGO,
            SGD_ESP_CODI,
            RADI_NUME_RADI,
            SGD_SEC_CODIGO,
            SGD_DIR_DIRECCION,
            SGD_DIR_TELEFONO,
            SGD_DIR_MAIL,
            SGD_DIR_TIPO,
            SGD_DIR_CODIGO,
            SGD_DIR_NOMBRE)
            values( 1,
                '".$dir_nombre[$iK]."',
                NULL,
                ".$dir_dptoCodi[$iK].",
                ".$dir_muniCodi[$iK].",
                ".$dir_idPais[$iK].",
                ".$dir_idCont[$iK].",
                '".$dir_docFun[$iK]."',
                ".$oem_codigo[$iK].",
                ".$ciu_codigo[$iK].",
                NULL,
                $nurad,
                0,
                '".$dir_direccion[$iK]."',
                '".$dir_telefono[$iK]."',
                '".$dir_mail[$iK]."',
                $dirTipo,
                $nextval,
                '".$dir_nombre[$iK]."')";
        $rsg = $db->conn->Execute($isql);
        $direcciones[] = $db->conn->getOne("SELECT id FROM SGD_DIR_DRECCIONES WHERE SGD_DIR_CODIGO = $nextval AND RADI_NUME_RADI = $nurad");
    }
} else {
    for($iK=0;$iK<=($i-1); $iK++) {
        $direcciones[] = $dir_id[$iK];
    }
}

if($tipoRadPadre == 2) {
    $mensajeHistorico  = "Radicación No ". $nurad . " para radicado No. $numRadicadoPadre desde Respuesta en PDF";
} else {
    $mensajeHistorico  = "Radicación Respuesta en PDF No ". $nurad;
}

if(!empty($regFile)) {
    $mensajeHistorico .= ", con archivos adjuntos";
}

if($tipoRadPadre == 2) {
    $radicadosSel[0] = $nurad;

    $hist->insertarHistorico(
        $radicadosSel,
        $coddepe,
        $usua_actu,
        $coddepe,
        $usua_actu,
        $mensajeHistorico,
        $codTx
    );

    //Inserta el evento del radicado de respuesta nuevo.
    //$radicadosSel[0] = $nurad;
    //Agregar un nuevo evento en el historico para que
    //muestre como contestado y no genere alarmas.
    //A la respuesta se le agrega el siguiente evento
    $hist->insertarHistorico(
        $radicadosSel,
        $coddepe,
        $usua_actu,
        $coddepe,
        $usua_actu,
        "Imagen asociada desde respuesta rapida " . $nurad,
        42
    );

    if($tipo_radicado == 1) {
        $codTxSec = 3;
    } else {
        $codTxSec = 4;
    }

    $radicadosSel[0] = $numRadicadoPadre;
    $hist->insertarHistorico(
        $radicadosSel,
        $coddepe,
        $usua_actu,
        $coddepe,
        $usua_actu,
        "Se genera respuesta " . $nurad . " desde Respuesta en PDF",
        $codTxSec
    );


} else {
    //inserta el evento del radicado padre.
    $radicadosSel[0] = $numRadicadoPadre;

    $hist->insertarHistorico(
        $radicadosSel,
        $coddepe,
        $usua_actu,
        $coddepe,
        $usua_actu,
        $mensajeHistorico,
        $codTx
    );

    //Inserta el evento del radicado de respuesta nuevo.
    //$radicadosSel[0] = $nurad;


    //Agregar un nuevo evento en el historico para que
    //muestre como contestado y no genere alarmas.
    //A la respuesta se le agrega el siguiente evento
    $hist->insertarHistorico(
        $radicadosSel,
        $coddepe,
        $usua_actu,
        $coddepe,
        $usua_actu,
        "Imagen asociada desde respuesta rapida " . $nurad,
        42
    );
}




$db3      = new ConnectionHandler($ruta_raiz);
$expediente    = new Expediente($db3);

$expediente->insertar_expediente($exp, $nurad, $coddepe, $usua_actu, $usua_doc);

if($idPlantilla == 0) {

    $sqlGetIdPlantilla = "select idPlantilla from anexos where anex_codigo = '$anexo'";
    $rsPlan = $db->conn->Execute($sqlGetIdPlantilla);
    if (!$rsPlan->EOF) {
        $idPlantilla = $rsPlan->fields["IDPLANTILLA"];
    }
}

$isqlDepR = "SELECT r.radi_depe_actu, r.radi_usua_actu ,d.depe_nomb 
    FROM dependencia d
    JOIN radicado r on r.radi_depe_actu = d.depe_codi
    WHERE r.radi_nume_radi = '$nurad'";

//Sin funcionalidad
/*$rsDepR = $db->conn->Execute($isqlDepR);

$coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
$depnombAux = $rsDepR->fields['DEPE_NOMB'];
$codusua = $rsDepR->fields['RADI_USUA_ACTU'];*/



//Radicar según id de plantilla
if($idPlantilla == 100000) {
    include('./generadorpdf/resolucion/resolucionradanexo.php');
} elseif($idPlantilla == 100001) {
    include('./generadorpdf/ADFL03/ADFL03radanexo.php');
} elseif($idPlantilla == 100002) {
    include('./generadorpdf/AIFT02/AIFT02radanexo.php');
} elseif($idPlantilla == 100003) {
    include('./generadorpdf/CJFL01/CJFL01radanexo.php');
} elseif($idPlantilla == 100004) {
    include('./generadorpdf/CJFL02/CJFL02radanexo.php');
} elseif($idPlantilla == 100005) {
    include('./generadorpdf/CJFL04/CJFL04radanexo.php');
} elseif($idPlantilla == 100006) {
    include('./generadorpdf/CJFL11/CJFL11radanexo.php');
} elseif($idPlantilla == 100007) {
    include('./generadorpdf/CJFL14/CJFL14radanexo.php');
} elseif($idPlantilla == 100008) {
    include('./generadorpdf/CJFL17/CJFL17radanexo.php');
} elseif($idPlantilla == 100009) {
    include('./generadorpdf/CJFL22/CJFL22radanexo.php');
} elseif($idPlantilla == 100010) {
    include('./generadorpdf/GDFL02/GDFL02radanexo.php');
} elseif($idPlantilla == 100011) {
    include('./generadorpdf/GDFL03/GDFL03radanexo.php');
} elseif($idPlantilla == 100012) {
    include('./generadorpdf/Salida/salidapradanexo.php');
} elseif($idPlantilla == 100013) {
    include('./generadorpdf/Memorando/memorandopradanexo.php');
} elseif($idPlantilla == 100016) {
    include('./generadorpdf/CJFL12/CJFL12radanexo.php');
} elseif($idPlantilla == 100017) {
    include('./generadorpdf/CJFL13/CJFL13radanexo.php');
} else {


    // REMPLAZAR DATOS EN EL ASUNTO
    // Extend the TCPDF class to create custom Header and Footer
    if (!class_exists('MYPDF')) {
        class MYPDF extends TCPDF
        {
            //Page header
            public function Header()
            {
                // Logo
                $this->Image(
                    dirname(__DIR__, 1).'/bodega'.$_SESSION["headerRtaPdf"],
                    25,
                    3,
                    160,
                    0,
                    'png',
                    '',
                    'T',
                    false,
                    300,
                    '',
                    false,
                    false,
                    0,
                    false,
                    false,
                    false
                );


            }

            // Page footer
            public function Footer()
            {
                global $entidad_dir, $entidad_tel, $httpWebOficial;
                // Position at 15 mm from bottom
                $tbl = '
                <table style="width:100%">
                    <tr>
                    <td colspan="3" width:80% ><img src="'.dirname(__DIR__, 1).'/bodega/sys_img/FooterUpLine.PNG"/></td>
                    </tr>
                    <tr>
                    <td style="width:80%">
                        <br>
                        Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages() . '<br>
                        '.$entidad_dir.' | '.$entidad_tel.' <br>
                        '.$httpWebOficial.'<br>
                        CIFL02
                    </td>
                    <td style="width:2%"><img src="'.dirname(__DIR__, 1).'/bodega/sys_img/FooterSideLine.PNG"/></td>
                    <td style="width:18%" align="center">
                        
                    </td>
                    </tr>
                </table>';
                $this->SetY(-25);
                $this->SetFont('helvetica', '', 8, '', 'default', true);
                $this->writeHTML($tbl, true, false, false, false, '');
                $this->Image(dirname(__DIR__, 1).'/bodega/sys_img/FooterLogoSGS.PNG', 170, 257, 30, 22, 'PNG', '', 'T', false, 200, '', false, false, 0, false, false, false);

            }
        }
    }
    // create new PDF document
    $pdf = new MYPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($setAutor);
    $pdf->SetTitle($SetTitle);
    $pdf->SetSubject($SetSubject);
    $pdf->SetKeywords($SetKeywords);

    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    //set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    //set auto page breaks
    $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //set some language-dependent strings
    $pdf->setLanguageArray($l);

    // set default font subsetting mode
    $pdf->setFontSubsetting(true);

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 10);

    // define barcode style
    $style = array(
        'position' => '',
        'align' => 'C',
        'stretch' => true,
        'fitwidth' => true,
        'cellfitalign' => '',
        'border' => false,
        'hpadding' => 'auto',
        'vpadding' => 'auto',
        'fgcolor' => array(0,0,0),
        'bgcolor' => false, //array(255,255,255),
        'text' => false,
        'font' => 'helvetica',
        'fontsize' => 8,
        'stretchtext' => 4
    );
    // echo "Entro a Radicar Anexo";
    $style['position'] = 'R';
    //$pdf->write1DBarcode($nurad, 'C39', '', '', '', 7, 0.2, $style, 'N');
    // output the HTML content
    $pdf->writeHTML($asu, true, false, true, false, '');

    // Close and output PDF document
    // This method has several options, check the source code documentation for more information.
    $pdf->Output($ruta_raiz.$ruta2, 'F');
}

$sqlE = "UPDATE
    RADICADO
    SET
    RADI_PATH = '$ruta3'
    WHERE
    RADI_NUME_RADI = $nurad";

$db->conn->Execute($sqlE);

$anex_estado = $esNotificacion ? 2 : 3;

$actualizar_anexo = "UPDATE ANEXOS
    SET RADI_NUME_SALIDA = '$nurad',
    ANEX_ESTADO = '$anex_estado',
    SGD_dir_mail = '$mails',
    anex_tipo_envio = '$tipo_envio',
    sgd_trad_codigo = '$tipo_radicado'
    WHERE ANEX_CODIGO = '$anexo'";

$db->conn->Execute($actualizar_anexo);

//insertar registros que no sean notificaciones en tabla de envios
if (!$esNotificacion) {
    $id_anexo = $db->conn->getOne("SELECT id FROM anexos WHERE radi_nume_salida = $nurad");
    foreach($direcciones as $direccion) {
        //$db->conn->execute("INSERT INTO sgd_rad_envios (id_anexo, id_direccion, tipo, estado) VALUES ($id_anexo, $direccion, 'E-mail', 1)");
    }
}




if($fecha_rad_salida) {
    $respuesta = str_replace('F_RAD_S', $fecha_rad_salida, $respuesta);
}
if($nurad) {
    $respuesta = str_replace('RAD_S', $nurad, $respuesta);
}

$respuesta = str_replace('RAD_S', $nurad, $respuesta);
$respuesta = str_replace('*DIGNATARIO*', $dignatario, $respuesta);
$respuesta = str_replace('*REFERENCIA*', $referencia, $respuesta);
$respuesta = str_replace("\xe2\x80\x8b", '', $respuesta);

$archivo_txt = $anexo . '.txt';
$archivo_grabar_txt = "../bodega/$radano/$depCreadora/docs/" . $archivo_txt;
$file_content   = fopen($archivo_grabar_txt, 'w');
$write_result   = fwrite($file_content, $respuesta);
$closing_result = fclose($file_content);



// firma digital
if ($_SESSION['apiFirmaDigital']=='false' && $_SESSION["usua_perm_firma"] >= 1) {

    //Agregar un nuevo evento en el historico para que
    //muestre como firmado.
    //A la respuesta se le agrega el siguiente evento

    if($tipoRadPadre == 2) {
        $radicadosSelAux[0] = $nurad;
    } else {
        $radicadosSelAux[0] = $numRadicadoPadre;
    }


    $hist->insertarHistorico(
        $radicadosSelAux,
        $coddepe,
        $usua_actu,
        $coddepe,
        $usua_actu,
        "Firmadada digitalmente la respuesta en PDF No " . $nurad,
        40
    );

    $firmasd = $ABSOL_PATH.'/bodega/firmas/';

    $P12_FILE =  $firmasd . 'server.p12';

    if (!file_exists($P12_FILE)) {
        $P12_FILE = $firmasd . $usua_doc . '.p12';
    }

    if ($P12_PASS) {
        $clave = $P12_PASS;
    }

    $nombreArchivo = $ABSOL_PATH.$ruta2;
    chdir($ABSOL_PATH.'/bodega/tmp/workDir/');
    $commandFirmado='java -jar '.$ABSOL_PATH.'/include/jsignpdf-1.6.4/JSignPdf.jar '.$nombreArchivo.' -kst PKCS12 -ksf '.$P12_FILE.' -ksp '.$clave.' --font-size 7 -r \'Firmado al Radicar en CRA\' -V -v -llx 0 -lly 0 -urx 550 -ury 27';

    $out = null;
    $ret = null;
    $inf = exec($commandFirmado, $out, $ret);

    // si falla la ejecución de jsign guardar error en bodega/jsignpdf.log
    if ($ret != 0) {
        $out = implode(PHP_EOL, $out);
        error_log(date(DATE_ATOM)." ".basename(__FILE__)." ($ret) $numRadicadoPadre > $nurad: $out\n", 3, "$ABSOL_PATH/bodega/jsignpdf.log");
    }

    if ($inf=="INFO  Finished: Creating of signature failed.") {
        unset($answer);
        $answer=array();
        //saveMessage('error',"Clave de firma digital erronea");
        die(json_encode($answer));
    }

    rename($anexo.'_signed.pdf', $nombreArchivo);
}

//prueba
