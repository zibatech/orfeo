<?php
session_start();
/**
* @author Cesar Augusto   <aurigadl@gmail.com>
* @author Correlibre.org // Tomado de version orginal realizada por JL en SSPD, modificado.
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
*
* @copyleft

OrfeoGpl Models are the data definition of OrfeoGpl Information System
Copyright (C) 2013 Infometrika Ltda.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Fou@copyrightndation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

$ruta_raiz=".."; 

if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_POST as $key => $valor) ${$key} = $valor;
foreach ($_GET as $key => $valor)  ${$key} = $valor;

$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$esjefe      = $_SESSION["USUA_JEFE_DE_GRUPO"];
$reasAjefes  = $_SESSION["usuario_reasigna_jefes"];

$varTramiteConjunto = $_SESSION["varTramiteConjunto"];

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");

$usuarioreasignatodos = $_SESSION["USUA_PERM_TODOS_REASIGNA"];
//$usuarioreasignar = $_SESSION["USUARIO_REASIGNAR"];

//PARAMETROS
$archivado_requiere_exp = $_SESSION["archivado_requiere_exp"];
$reasigna_requiere_exp = $_SESSION["reasigna_requiere_exp"];
 if ($_SESSION['entidad']=='METROVIVIENDA'){$archivado_requiere_exp = true; }


if (isset($_POST['depuser'])) {
    header("Content-Type: application/json; charset=UTF-8");
    include("$ruta_raiz/include/tx/Tx.php");
    $hist      = new Historico($db);
    $codTx=99;
    //$envioDigital=true;
    //$email = $omail->fromAddress;
    $depuser = explode('-',$_POST['depuser']);
    $sql="select usua_email from usuario where depe_codi = {$depuser[0]} and usua_codi = {$depuser[1]}";
    $rs = $db->conn->Execute($sql);
    $dstusr =  $rs->fetchRow();
    $email =  $dstusr['USUA_EMAIL'];
    $asuntoMailRespuestaRapida='answer';
    $hist->insertarHistorico([$_POST['radicados']],
        $_SESSION['dependencia'],
        $_SESSION['codusuario'],
        $depuser[0],
        $depuser[1],
        'Se informa por correo electrónico que tiene enlace de descarga que vence en 24 horas',
        103);
    $texto = 'Estimado usuario, le informamos que se ha radicado a trav&eacute;s de ORFEO el documento *RAD_S*, <span style="font-size:130%;font-weight:bold;background-color:yellow;">que contiene un enlace de descarga que vence en 24 horas.</span><br><br>Por lo expuesto, se recomienda revisar el radicado y efectuar la descarga respectiva para no perder la informaci&oacute;n all&iacute; contenida.';
    $radicadosSelText = $_POST['radicados'];
    if ($_emailUser == ""){$_emailUser="no-reply@test.com";}

    $nombre_fichero = "GENERAL.mailInformar.php";
    $ruta_fichero = $ruta_raiz.'/include/mail/'.$nombre_fichero;
    require("$ruta_raiz/include/mail/GENERAL.mailInformar.php");
    echo json_encode(['ret'=>'ok']);
    exit;
}


//Reasignar Requiere Expediente
 if ($_SESSION['entidad']=='CRA'){$reasigna_requiere_exp = true; if ($_SESSION["nivelus"]==5){$reasigna_requiere_exp = false;} }

if(!$codTx) $codTx = $AccionCaliope;
if($esjefe || $usuario_reasignacion==1){
    $jaScipt = "
        if(document.realizarTx.chkNivel){
            if(document.realizarTx.chkNivel.checked==1){
               marcados = marcados -1 ;
            }
        }
    ";
}


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Enviar Datos</title>
<?php
  include_once "$ruta_raiz/js/funtionImage.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=uft-8">
<?php
 include_once $ruta_raiz."/htmlheader.inc.php";
?>
<!-- Cargando los temas para el calendario Java -->
<link rel="stylesheet" href="../include/zpcal/themes/fancyblue.css" />

<!-- cargando los Javascripts para el calendario -->
<script type="text/javascript" src="../include/zpcal/src/utils.js"></script>
<script type="text/javascript" src="../include/zpcal/src/calendar.js"></script>
<script type="text/javascript" src="../include/zpcal/src/calendar-setup.js"></script>

<!-- Cargando los archivos de definicion -->
<script type="text/javascript" src="../include/zpcal/lang/calendar-sp.js"></script>
</head>
<?php

$mensaje_error = false;

$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
require_once("$ruta_raiz/class_control/Dependencia.php");
include_once "$ruta_raiz/include/tx/Tx.php";

$arrRadicados = array_keys($checkValue);
$regsTx =  implode(",", $arrRadicados);

$tx = new Tx($db);

// Logica de Notificaciones: Antes de enviar cualquier notificacion al grupo de notificaciones, los 
// radicados deben estar clasificados documentalmente y dentro de un expediente. Tambien si se va 
// a archivar el acto aministrativo, el sistema debe mostrar la opcion de informar funcionarios.
define('CIRC_INTERNA', 4);
define('CIRC_EXTERNA', 5);
define('RESOLUCION', 6);
define('AUTO', 7);
$validarTRD = false;
$validarExpediente = true;
$informarTerminacionNotificacion = false;
if (($codTx == 9 && $depsel == 8010) || $codTx == 13) {
    foreach($arrRadicados as $radicado) {
        $tipoRad = substr($radicado, -1);
        if ($tipoRad == CIRC_INTERNA || $tipoRad == CIRC_EXTERNA ||
            $tipoRad == RESOLUCION || $tipoRad == AUTO) {
            $validarTRD = true;
            $validarExpediente = true;
            $informarTerminacionNotificacion = true;
            break;
        }
    }
}

$_rads_=array_keys($_REQUEST["checkValue"]);
$no_entrada=false;
for ($i=0; $i<count($_rads_); $i++){
	if (substr($_rads_[$i],-1)!=2){
		$no_entrada=true;
		break;
	}
}
if($codTx==8 || $codTx==9 || $codTx==14 || $codTx==13 || $validarTRD){
if ($_SESSION["USUA_JEFE_DE_GRUPO"]==false and $_SESSION["USUA_TRAMITADOR"]==false){
if(!$tx->validateTrdSend($checkValue)){

  if(count($tx->regsTrdFalse)>=1){
		  $regs = implode(",",$tx->regsTrdFalse);
		  $msgRegs = "<strong>Radicados sin Clasificacion TRD:</strong><br> $regs";
		}
	if(count($tx->regsATrdFalse)>=1){
		  $msgAnexos = "<strong>Anexos de Radicados sin Clasificacion :</strong><br> ".implode("<br>",$tx->regsATrdFalse);
		}

	$msgSend = "<p class='alert alert-warning'>
	<i class='fa fa-warning fa-fw fa-lg'></i><strong>Opps !</strong><br>$msgRegs <br><br>$msgAnexos<br>  Por lo tanto no se puede realizar la Transacción solicitada.
  </p>";
	die( $msgSend);

}
}else{
	if ($no_entrada){
		if(!$tx->validateTrdSend($checkValue)){
		
		  if(count($tx->regsTrdFalse)>=1){
				  $regs = implode(",",$tx->regsTrdFalse);
				  $msgRegs = "<strong>Radicados sin Clasificacion TRD:</strong><br> $regs";
				}
			if(count($tx->regsATrdFalse)>=1){
				  $msgAnexos = "<strong>Anexos de Radicados sin Clasificacion :</strong><br> ".implode("<br>",$tx->regsATrdFalse);
				}
		
			$msgSend = "<p class='alert alert-warning'>
			<i class='fa fa-warning fa-fw fa-lg'></i><strong>Opps !</strong><br>$msgRegs <br><br>$msgAnexos<br>  Por lo tanto no se puede realizar la Transacción solicitada.
		  </p>";
			die( $msgSend);
	}
}
}
}

$objDep = new Dependencia($db);
$encabezado = "".session_name()."=".session_id()."&depeBuscada=$depeBuscada&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion";
$linkPagina = "$PHP_SELF?$encabezado&orderTipo=$orderTipo&orderNo=";

$sql_cont = "SELECT
					sgd_msg_desc,
					sgd_msg_codi,
					sgd_msg_etiqueta
				FROM
				sgd_msg_mensaje";

$salida   = $db->conn->query($sql_cont);
$select1  = "<select name='idmensaje' id='idmensaje' class='select'>";
$select1 .= "<option value=''>   </option>";
$i        = 0;

while (!$salida->EOF){
    $i++;
    $checked  = '';
    $seleDesc = $salida->fields['SGD_MSG_DESC'];
    $seleEtiq = $salida->fields['SGD_MSG_ETIQUETA'];

    $select2 .= "<option value='$seleDesc'>$seleEtiq => $seleDesc</option>";
    $salida->MoveNext ();

    if($i < 7 and $i%2 == 0){
        $buttacc1  .= "<button type='button' class='btn btn-default attrtext' attrtext='$seleDesc'> $seleEtiq </button>";
    }elseif($i < 7 and $i%2 != 0){
        $buttacc2  .= "<button type='button' class='btn btn-default attrtext' attrtext='$seleDesc'> $seleEtiq </button>";
    }
}

$select3 = "</select>";
$select  = $select1.$select2.$select3;

// Filtro de datos

 if(!$codTx) $codTx = $AccionCaliope;
$EnviaraV=($codTx==14)?'VoBo':$EnviaraV;
$codTx=($codTx==14)?9:$codTx;

if($checkValue) {
    $num = count($checkValue);
	reset($checkValue);
	$i = 0;
	$jglCounter = 0;
	$resultadoJGL = "";
    while (list($recordid,$tmp) = each($checkValue)){
        $record_id = $recordid;
        $radicadosTx .= empty($radicadosTx)? $record_id : ",$record_id";
        switch ($codTx)
        {	
        case  7:
        case  8:
        {	if (strpos($record_id,'-'))
        {	//Si trae el informador concatena el informador con el radicado sino solo concatena los radicados.
            $tmp = explode('-',$record_id);
            if ($tmp[0]) {
                $whereFiltro .= ' (b.radi_nume_radi = '.$tmp[1].' and i.info_codi='.$tmp[0].') or';
                $tmp_arr_id=2;
            }
            else
            {	$whereFiltro .= ' b.radi_nume_radi = '.$tmp[1].' or';
            $tmp_arr_id=1;
            }

        } else {
            $whereFiltro .= ' b.radi_nume_radi = '.$record_id.' or';
            $tmp_arr_id=0;
        }
            $record_id = $tmp[1];
        }break;

        case  18:
        {   if (strpos($record_id,'-'))
        {   //Si trae el informador concatena el informador con el radicado sino solo concatena los radicados.
            $tmp = explode('-',$record_id);
            if ($tmp[0]) {
                $whereFiltro .= ' (b.radi_nume_radi = '.$tmp[1].' and i.info_codi='.$tmp[0].') or';
                $tmp_arr_id=2;
            }
            else
            {   $whereFiltro .= ' b.radi_nume_radi = '.$tmp[1].' or';
            $tmp_arr_id=1;
            }

        } else {
            $whereFiltro .= ' b.radi_nume_radi = '.$record_id.' or';
            $tmp_arr_id=0;
        }
            $record_id = $tmp[1];
        }break;

        case  9:
            /**
             * Si el radicado esta en borrador y es un acto administrativo no requiere expediente
             */

            $indicaBorrador = substr($record_id, 0, 4);
            $indicadorTipoRadi = substr($record_id, -1);

            if($indicaBorrador > 3000 && ($indicadorTipoRadi >= 4 and $indicadorTipoRadi <= 7)) {

            } else {

                    if ($validarExpediente){
                        $isqlExp = "select SGD_EXP_NUMERO as NumExpediente from SGD_EXP_EXPEDIENTE
                            where RADI_NUME_RADI = '$record_id'";
                        $rsExp = $db->conn->Execute($isqlExp);
                        $resultadoJGL .= "CONSULTA: $isqlExp ";


                        if ( $rsExp && !$rsExp->EOF ) {

                            $expNumero = $rsExp->fields[0];

                            if ($expNumero== ''){
                                $expNumero = $rsExp->fields['NUMEXPEDIENTE'];
                            }

                            if ( $expNumero =='' || $expNumero == null )
                            {
                                $setFiltroSinEXP .= $record_id ;
                                if($jglCounter<=($num))
                                {
                                    $setFiltroSinEXP .= ",";
                                }
                                break;
                            }

                            $rsExp->MoveNext();
                        }else {
                            $setFiltroSinEXP .= $record_id ;
                            if($jglCounter<=($num)){
                                $setFiltroSinEXP .= ",";
                            }
                        }
                        $jglCounter++;
                    }
                }
        case 12:
            if($codTx == 12){
                $isqlw="select b.RADI_USU_ANTE as RADI_USU_ANTE, u.USUA_ESTA  from radicado  b, usuario u where b.radi_nume_radi = ".$record_id." AND b.RADI_USU_ANTE=u.USUA_LOGIN";
                $UsuIn  = $db->query($isqlw);
                $usuInAct=$UsuIn->fields["RADI_USU_ANTE"];
                $usuaEsta=$UsuIn->fields["USUA_ESTA"];
                if ($usuaEsta != 1)
                {
                    $pasaFiltro2 = "No";
                }else{
                    $pasaFiltro2 = "Si";

                }
            }

        case 13:
        {
            $reasigna_requiere_exp = false;
            $condicionAnexBorrados =  " and anex_borrado = 'N'";
            /** Se verifica si el usuario tiene permiso de Enrutador
             * Si contiene este permiso los radicados de entrada que se generen iran al usuario con dicho permiso.
             * El permiso es "USUA_PERM_ENRUTADOR"
             *
             * @author jlosada - correlibre 2016-03
             */

            include_once "$ruta_raiz/include/tx/Radicacion.php";

            $rad = new Radicacion($db);
            $usuarioEnrutador =  $rad->getEsEnrutador($dependencia, $codusuario);


            if ((($codTx == 9 or $codTx == 12)  and (!$esjefe and $codusuario != $usuarioEnrutador)) or $codTx == 16 or $codTx == 13){
                $aux_tiene_exp = false;
                if ($reasigna_requiere_exp==true and $codTx == 9 ){
                    $isqlExp = "select SGD_EXP_NUMERO as NumExpediente from SGD_EXP_EXPEDIENTE  where RADI_NUME_RADI = '$record_id'";
                    $rsExp = $db->conn->Execute($isqlExp);
                    if ( $rsExp && !$rsExp->EOF )
                    {
                        $expNumero = $rsExp->fields[0];
                        if ( $expNumero !='' || $expNumero != null ){$aux_tiene_exp = true;}
                        $rsExp->MoveNext();
                    }
                }else{$aux_tiene_exp = true;}
                if ($aux_tiene_exp){
                    include_once("../include/query/busqueda/busquedaPiloto1.php");
                    /*
                     * Condicion Radicado Padre
                     */
                    $anoRad = substr($record_id,0,4);
                    $isqlTRDP = "select $radi_nume_radi as RADI_NUME_RADI from SGD_RDF_RETDOCF r where r.RADI_NUME_RADI = '$record_id'";

                    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
                    $rsTRDP = $db->conn->Execute($isqlTRDP);
                    $radiNumero = $rsTRDP->fields["RADI_NUME_RADI"];

                    if( !($anoRad == "2005" or $anoRad == "2004" or $anoRad == "2012" or $anoRad == "2013")  && strlen (trim($radiNumero)==0)){
                        $pasaFiltro = "No";
                        $setFiltroSinTRD .= $record_id ;
                        if($i<=($num))
                        {  $setFiltroSinTRD .= ",";
                        }
                        break;
                    }
                    $pasaFiltro="Si";
                }else{
                    $setFiltroSinEXP = "No se permite reasignar";
                    $pasaFiltro="No";
                }
                if($codTx == 12){
                    $isqlw="select b.RADI_USU_ANTE as RADI_USU_ANTE  from radicado  b, usuario u
                        where b.radi_nume_radi = ".$record_id." AND b.RADI_USU_ANTE=u.USUA_LOGIN and  u.usua_esta='1'";
                    $UsuIn  = $db->conn->query($isqlw);
                    $usuInAct=$UsuIn->fields["RADI_USU_ANTE"];
                    if (empty($usuInAct))
                    {
                        $pasaFiltro2 = "No";
                    }else{
                        $pasaFiltro2 = "Si";

                    }
                }
                $pasaFiltro = "Si";
                /*
                 * Condicion Anexos Radicados
                 */
                $isqlTRDA = "select $radi_nume_salida as RADI_NUME_SALIDA from anexos
                    where ANEX_RADI_NUME = '$record_id' and RADI_NUME_SALIDA != 0
                    and RADI_NUME_SALIDA not in(select RADI_NUME_RADI from SGD_RDF_RETDOCF)";
                $rsTRDA = $db->conn->Execute($isqlTRDA);

                while($rsTRDA && !$rsTRDA->EOF && $pasaFiltro!="No")
                {	$radiNumero = $rsTRDA->fields["RADI_NUME_SALIDA"];
                $anoRadsal=substr($radiNumero,0,4);

                if ($radiNumero !='' && !($anoRadsal == "2005" or $anoRadsal == "2004" or $anoRadsal == "2003"))
                {	$pasaFiltro="No";
                $setFiltroSinTRD .= $record_id ;
                if($i<=($num))
                {
                    $setFiltroSinTRD .= ",";
                }break;
                }
                $rsTRDA->MoveNext();
                }
                $i++;
            }
            $whereFiltro.= ' b.radi_nume_radi = '.$record_id.' or';
            $pasaFiltro = "Si";


            /**
             * Modificaciones Febrero de 2007, por SSPD para el DNP
             * Archivar:
             * Se verifica si el radicado se encuentra o no en un expediente,
             * si es negativa la verificacion, ese radicado no se puede archivar
             */
            if ( $codTx == 13 && $archivado_requiere_exp ){
                $isqlExp = "select SGD_EXP_NUMERO as NumExpediente from SGD_EXP_EXPEDIENTE
                    where RADI_NUME_RADI = '$record_id'";
                $rsExp = $db->conn->Execute($isqlExp);
                $resultadoJGL .= "CONSULTA: $isqlExp ";


                if ( $rsExp && !$rsExp->EOF ) {

                    $expNumero = $rsExp->fields[0];

                    if ($expNumero== ''){
                        $expNumero = $rsExp->fields['NUMEXPEDIENTE'];
                    }

                    if ( $expNumero =='' || $expNumero == null )
                    {
                        $setFiltroSinEXP .= $record_id ;
                        if($jglCounter<=($num))
                        {
                            $setFiltroSinEXP .= ",";
                        }
                        break;
                    }

                    $rsExp->MoveNext();
                }else {
                    $setFiltroSinEXP .= $record_id ;
                    if($jglCounter<=($num)){
                        $setFiltroSinEXP .= ",";
                    }
                }
                $jglCounter++;
            }
        }break;
        case 16:
        {
            /*
             * Se crea condicion de obligatoriedad clasificacion TRD
             */
            include_once "$ruta_raiz/include/db/ConnectionHandler.php";
            $db = new ConnectionHandler("$ruta_raiz");
            include_once("../include/query/busqueda/busquedaPiloto1.php");
            /*
             * Condicion Radicado Padre
             */
            $anoRad = substr($record_id,0,4);
            $isqlTRDP = "select $radi_nume_radi as RADI_NUME_RADI from SGD_RDF_RETDOCF r where r.RADI_NUME_RADI = '$record_id'";
            if($anoRad == "2005" or $anoRad == "2004" or $anoRad == "2003") $pasaFiltro = "Si";
            $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
            $rsTRDP = $db->conn->Execute($isqlTRDP);
            $radiNumero = $rsTRDP->fields["RADI_NUME_RADI"];

            if( !($anoRad == "2005" or $anoRad == "2004" or $anoRad == "2003")  && strlen (trim($radiNumero)==0))
            {	$pasaFiltro = "No";
            $setFiltroSinTRD .= $record_id ;
            if($i<=($num))
            {  $setFiltroSinTRD .= ",";
            }
            break;
            }
            $pasaFiltro = "Si";

            /*
             * Condicion Anexos Radicados
             */
            $isqlTRDA = "select $radi_nume_salida as RADI_NUME_SALIDA from anexos
                where ANEX_RADI_NUME = '$record_id' and RADI_NUME_SALIDA != 0
                and anex_borrado = 'N'
                and RADI_NUME_SALIDA not in(select RADI_NUME_RADI from SGD_RDF_RETDOCF)";

            $rsTRDA = $db->conn->Execute($isqlTRDA);

            while($rsTRDA && !$rsTRDA->EOF && $pasaFiltro!="No")
            {	$radiNumero = $rsTRDA->fields["RADI_NUME_SALIDA"];
            $anoRadsal=substr($radiNumero,0,4);

            if ($radiNumero !='' && !($anoRadsal == "2005" or $anoRadsal == "2004" or $anoRadsal == "2003"))
            {	$pasaFiltro="No";
            $setFiltroSinTRD .= $record_id ;
            if($i<=($num))
            {
                $setFiltroSinTRD .= ",";
            }break;
            }
            $rsTRDA->MoveNext();
            }
            $i++;

            $whereFiltro.= ' b.radi_nume_radi = '.$record_id.' or';
            $pasaFiltro = "Si";

            /**
             * Modificaciones Febrero de 2007, por SSPD para el DNP
             * Archivar:
             * Se verifica si el radicado se encuentra o no en un expediente,
             * si es negativa la verificacion, ese radicado no se puede archivar
             */
            include_once "$ruta_raiz/include/db/ConnectionHandler.php";
            $db = new ConnectionHandler("$ruta_raiz");

            $isqlExp = "select SGD_EXP_NUMERO as NumExpediente from SGD_EXP_EXPEDIENTE
                where RADI_NUME_RADI = '$record_id'";
            $rsExp = $db->conn->Execute($isqlExp);
            $resultadoJGL .= "CONSULTA: $isqlExp ";
            if ( $rsExp && !$rsExp->EOF )
            {
                $expNumero = $rsExp->fields[0];
                if ($expNumero== ''){
                    $expNumero = $rsExp->fields['NUMEXPEDIENTE'];
                }

                if ( $expNumero =='' || $expNumero == null )
                {
                    $setFiltroSinEXP .= $record_id ;
                    if($jglCounter<=($num))
                    {
                        $setFiltroSinEXP .= ",";
                    }
                    break;
                }

                $rsExp->MoveNext();
            }else {
                $setFiltroSinEXP .= $record_id ;
                if($jglCounter<=($num))
                {
                    $setFiltroSinEXP .= ",";
                }
            }
            $jglCounter++;


        }break;
        default:
        {
            $whereFiltro.= ' b.radi_nume_radi = '.$record_id.' ';
        }break;
        }

        $setFiltroSelect .= "$record_id,";
    }

	if($setFiltroSinTRD and $pasaFiltro=="No")
	{
        //Modificado idrd para aplicar trd
		$mensaje_error = "NO SE PERMITE ESTA OPERACION PARA LOS RADICADOS <BR> < $setFiltroSinTRD > <BR> FALTA CLASIFICACION TRD PARA ESTOS O PARA SUS ANEXOS <BR> FAVOR APLICAR TRD";
	}

    //Se agrega condicional para no de volver al usuario inactivo
	if ($pasaFiltro2 == "No") {
		$mensaje_error = "NO SE PERMITE ESTA OPERACION El USUARIO <BR> < $usuInAct > <BR> SE ENCUENTRA INACTIVO  ";
	}

    // Sin validación de expediente.
	if ( $setFiltroSinEXP && false) {
	    $mensaje_errorEXP = "<br>NO SE PERMITE ESTA OPERACION PARA LOS RADICADOS <BR> < $setFiltroSinEXP > <BR> PORQUE NO SE ENCUENTRAN EN NING&Uacute;N EXPEDIENTE";
	}

    if(substr($whereFiltro,-2)=="or"){
        $whereFiltro = substr($whereFiltro,0,strlen($whereFiltro)-2);
    }

    $whereFiltro =  "and ( $whereFiltro ) ";

} else {
    $mensaje_error="NO HAY REGISTROS SELECCIONADOS";
}

if ($mensaje_errorEXP || $mensaje_error ){
if ($_SESSION["USUA_JEFE_DE_GRUPO"]==false and $_SESSION["USUA_TRAMITADOR"]==false){
    die("
     <div class='well well-sm well-light' style='opacity: 1; width=80%;'>
        <div class='alert alert-warning'>
            <h4 class='alert-heading'>
            No se puede continuar:
            </h4>
            $mensaje_errorEXP
            $mensaje_error
        </div>
     </div>");
}else{
	if ($no_entrada){
    die("
     <div class='well well-sm well-light' style='opacity: 1; width=80%;'>
        <div class='alert alert-warning'>
            <h4 class='alert-heading'>
            No se puede continuar:
            </h4>
            $mensaje_errorEXP
            $mensaje_error
        </div>
     </div>");
	}
}
}

?>
<body topmargin="0"  >


<script>

    function setSel(start,end) {
        document.realizarTx.observa.focus();
        var t=document.realizarTx.observa;
        if(t.setSelectionRange){
            t.setSelectionRange(start,end);
            t.focus();
        }
        else notSupported();
    }

    function valMaxChars(maxchars){
        document.realizarTx.observa.focus();
        if(document.realizarTx.observa.value.length > maxchars){
            alert('Demasiados caracteres en el texto, solo se permiten '+ maxchars);
            setSel(maxchars,document.realizarTx.observa.value.length);
            return false;
        }
        return true;
    }

    /*
     * OPERACIONES EN JAVASCRIPT
     * @marcados Esta variable almacena el numeo de chaeck seleccionados.
     * @document.realizarTx  Este subNombre de variable me indica el formulario principal del listado generado.
     * @tipoAnulacion Define si es una solicitud de anulacion  o la Anulacion Final del Radicado.
     *
     * Funciones o Metodos EN JAVA SCRIPT
     * Anular()  Anula o solicita esta dependiendo del tipo de anulacin.  Previamente verifica que este seleccionado algun  radicado.
     * markAll() Marca o desmarca los check de la pagina.
     *
     */

    function Anular(tipoAnulacion){
        marcados = 0;
        for(i=0;i<document.realizarTx.elements.length;i++) {
            if(document.realizarTx.elements[i].checked==1 ) {
                marcados++;
            }
        }

        <?=$jaScipt?>

        if(marcados>=1) {
            return 1;
        } else {
            alert("Debe marcar un elemento");
            return 0;
        }
    }

    function markAll(noRad){
        if(document.realizarTx.elements.checkAll.checked || noRad >=1) {
            for(i=7;i<document.realizarTx.elements.length;i++) {
                document.realizarTx.elements[i].checked=1;
            }
        } else {
            for(i=3;i<document.realizarTx.elements.length;i++) {
                document.realizarTx.elements[i].checked=0;
            }
        }
    }

    function okTx() {
        valCheck = Anular(0);
        if(valCheck==0) return 0;
        numCaracteres = document.realizarTx.observa.value.length;
        if(numCaracteres>=6) {
            if (valMaxChars(4024))
                document.realizarTx.submit();
        }else {
            alert("Atención:  Falta la observación, el número de caracteres minimo es de 6 letras, (Digitó :"+numCaracteres+")");
        }
    }

    $( "body" ).on( "click", "#idmensaje", function(){
        $('#observa').val($('#idmensaje :selected').val());
    });

    $( "body" ).on( "click", ".attrtext", function(){
        $('#observa').val($(this).attr('attrtext'));
    });

    $( "body" ).on( "click", "#coddepeinf", function(){
        getUsuarios('usuariosInformar',document.getElementById('coddepeinf').value, 0);
    });
    <?php 
    if($codTx!=18){
    ?>    
    $( "body" ).on( "click", "#usuariosInformar", function(){
        informarUsuario('<?=$radicadosTx?>', document.getElementById('coddepeinf').value, document.getElementById('usuariosInformar').value,document.getElementById('observa').value);
    });
    <?php
    } 
    if($codTx==18){
    ?>  
     $( "body" ).on( "click", "#usuariosInformar", function(){
        tramiteConjunto('<?=$radicadosTx?>', document.getElementById('coddepeinf').value, document.getElementById('usuariosInformar').value,document.getElementById('observa').value);
    });
    <?php
    } 
    ?>

    function getUsuarios(varAccion, dependencia, var2){
        $.post( "../include/tx/json/getInfoUsuariosDep.php", { id: dependencia, accion: "informarUsuario" })
            .done(function( data ) {
                var obj = JSON.parse(data);
                var myObj = JSON.parse(data);
                var txt="";
                var xSel=0;
                document.getElementById("usuariosInformar").length = 0;
                for (x in myObj) {
                    document.getElementById("usuariosInformar").options[x] = new Option(myObj[x].USUA_NOMB, myObj[x].USUA_CODI);
                    if(myObj[x].USUA_CODI==1) xSel=x;
                }
                document.getElementById("usuariosInformar").options[xSel].selected = true;
            });
    }

    function informarUsuario(arrRadicados,dependencia,codigoUsuario,comentario){
        $.post( "../tx/ajaxInformarUs.php", { arrRadicados: arrRadicados, dependencia: dependencia, codigoUsuario:codigoUsuario, comentario:comentario })
            .done(function( data ) {
                listaInformados(arrRadicados);
            });
    }

    function tramiteConjunto(arrRadicados,dependencia,codigoUsuario,comentario){
        $.post( "../tx/ajaxTramiteConjunto.php", { arrRadicados: arrRadicados, dependencia: dependencia, codigoUsuario:codigoUsuario, comentario:comentario })
            .done(function( data ) {
                listaTramiteConjunto(arrRadicados);
            });
    }


    function listaInformados(arrRadicados){
        $.post( "../tx/ajax_informarLista.php", { arrRadicados: arrRadicados, idObjetoHtml: "usuariosInformados" })
            .done(function( data ) {
                if(data != null && data !== undefined && data.length > 0)
                    document.getElementById('usuariosInformados').innerHTML=data;console.log(data);
            });
    }

    function listaTramiteConjunto(arrRadicados){
        $.post( "../tx/ajax_tramiteConjunto.php", { arrRadicados: arrRadicados, idObjetoHtml: "usuariosInformados" })
            .done(function( data ) {
                if(data != null && data !== undefined && data.length > 0)
                    document.getElementById('usuariosInformados').innerHTML=data;console.log(data);
            });
    }

    function borrarInformado(arrRadicados,depeCodiBorrar,usuaCodiBorrar, idObjetoHtml){
        $.post( "../tx/ajaxBorrarInformado.php", { arrRadicados: arrRadicados, idObjetoHtml: "msgBorrar",depeCodiBorrar:depeCodiBorrar ,usuaCodiBorrar:usuaCodiBorrar })
            .done(function( data ) {
                document.getElementById('msgBorrar').innerHTML=data;
                listaInformados(arrRadicados);
            });
    }
    function borrarTramiteConjunto(arrRadicados,depeCodiBorrar,usuaCodiBorrar, idObjetoHtml){
        $.post( "../tx/ajaxBorrarTramiteConjunto.php", { arrRadicados: arrRadicados, idObjetoHtml: "msgBorrar",depeCodiBorrar:depeCodiBorrar ,usuaCodiBorrar:usuaCodiBorrar })
            .done(function( data ) {
                document.getElementById('msgBorrar').innerHTML=data;
                listaTramiteConjunto(arrRadicados);
            });
    }
    <?php 
        if($codTx!=18){
    ?> 
    listaInformados('<?= $radicadosTx ?>', 'usuariosInformar');
    <?php
    }
    ?>

    <?php 
        if($codTx==18){
    ?> 
    listaTramiteConjunto('<?= $radicadosTx ?>', 'usuariosInformar');
    <?php
    }
    ?>

    function informar_link() {
        $('#alert').css('visibility','hidden');
        depuser = $("#usCodSelect option:selected" ).val();
        $.post('formEnvio.php', {'depuser':depuser,'radicados':'<?=$_GET["verrad"]?>'}, function(data, status, xhr){
            $('#alert').css('visibility','visible');
            $('#alert').html('correo enviado');
        },"json");
    }
</script>
<div id="content" style="opacity: 1; width=80%;">
<div class="well well-sm well-light" style="opacity: 1; width=80%;">
<div class="widget-body" style="opacity: 1; width=80%;">
<div id="wid-id-0" class="jarviswidget jarviswidget-color-orange jarviswidget-sortable" data-widget-editbutton="false" role="widget" style="opacity: 1; width=80%;">
<header role="heading" style="opacity: 1; width=80%;">

<span class="widget-icon">
<h2>Transacciones de Documentos de Documentos </h2>
<span class="jarviswidget-loader">
</header>
<?php if (true) { ?>
<table width="80%" cellpadding="0" cellspacing="0" ALIGN=CENTER CLASS='form-contol input-sm'>
<tr>
	<td width="100%" align="center">
	<br>
	<form action='realizarTx.php?<?=$encabezado?>' method='POST'  name="realizarTx" class="smart-form" >
	<input type='hidden' name=depsel8 value="<?=implode($depsel8,',')?>">
	<input type='hidden' name=codTx value='<?=$codTx?>'>
	<input type='hidden' name=EnviaraV value='<?=$EnviaraV?>'>
	<input type='hidden' name=fechaAgenda value='<?=$fechaAgenda?>'>
    <table width="98%"
        class='smart-form table table-striped table-bordered table-hover dataTable'>
	<TR>
	<td class="titulos4" width="14%" >
<?

    switch ($codTx) {

    case 7:
        print "Borrar Informados </td><td>";
        echo "<input type='hidden' name='info_doc' value='".$tmp_arr_id."'>";
        break;

    case 8:	$usDefault = 1;
        $cad = $db->conn->Concat("CAST(u.depe_codi as char(10))","'-'","cast(u.usua_codi as char(10))");
        $cad2 = $db->conn->Concat($db->conn->IfNull("d.DEP_SIGLA", "'N.N.'"),"'-'","RTRIM(u.usua_nomb)");
        $usuario = $codUsuario;
        $textoInformados = 'Informados';
        print "Informados</td><td>";
        break;

    case 18: $usDefault = 1;
        $cad = $db->conn->Concat("CAST(u.depe_codi as char(10))","'-'","cast(u.usua_codi as char(10))");
        $cad2 = $db->conn->Concat($db->conn->IfNull("d.DEP_SIGLA", "'N.N.'"),"'-'","RTRIM(u.usua_nomb)");
        $usuario = $codUsuario;
        $textoInformados = 'Enviado a';
        print "Tramite Conjunto</td><td>";
        break;

    case 9:	$whereDep = "and u.depe_codi=$depsel ";
        if($dependencia==$depsel)
        {	$usDefault = $codusuario;	}
        // Esta seccion selecciona las dependencias que se deben visualizar a partir de otras
        // $sql = "SELECT DEPENDENCIA_OBSERVA, DEPENDENCIA_VISIBLE FROM DEPENDENCIA_VISIBILIDAD WHERE DEPENDENCIA_OBSERVA=$dependencia and DEPENDENCIA_VISIBLE = " . $depsel;
        //$rs1 = $db->conn->Execute($sql);

        $usuario_publico =  " and u.id in (select distinct autu_id from autp_permisos ap, autr_restric_grupo ar,autm_membresias am
                where am.autg_id=ar.autg_id and ar.autp_id=ap.id and upper(ap.nombre)='USUARIO_PUBLICO') ";

        if((($esjefe || $reasAjefes || $reasAjefes || $usuario_reasignacion==1) && $dependencia != $depsel ) || ($dependencia==$depsel && (!$esjefe || !$reasAjefes || $usuario_reasignacion !=1)&& $EnviaraV=="VoBo"))
        {
            $iSqlEnrutadores = "select u.usua_codi, u.usua_login, m.autu_id,r.autg_id,r.autp_id, u.depe_codi 
                from autr_restric_grupo r 
                join autm_membresias m on (r.autg_id=m.autg_id)
                join usuario u on (u.id=m.autu_id)
                where autp_id=59 and depe_codi=$depsel";

            $rsEnrutadores = $db->conn->query($iSqlEnrutadores);
            $enrutadores = $rsEnrutadores->fields["USUA_CODI"];
            if(!$enrutadores) $enrutadores = "0";
            $soloJefesSelect = ", s.autg_id as AUTG_ID";
            $soloJefesJoin = "left join autm_membresias s on (u.id = s.autu_id and s.autg_id = 2)";
            $usDefault = 1;

        }

        if ($usuarioreasignatodos>=1){
            $whereReasignar = "";
        }
        
        if(($esjefe || $reasAjefes || $usuario_reasignacion == 1)&& $dependencia==$depsel && $EnviaraV=="VoBo" ){
            if ($objDep->Dependencia_codigo($dependencia)){
                $depPadre=$objDep->getDepe_codi_territorial();
            }
            print ("La dependencia padre ...($depPadre)");
            $whereDep =  " and u.depe_codi=$depPadre";
            $depsel=$depPadre;
        }

        if($EnviaraV=="VoBo") {
            $proccarp = "Visto Bueno";
            $usuario_publico = "";
        }

        $cad = $db->conn->Concat("cast(depe_codi as char(10))","'-'","cast(u.usua_codi as char(10))");
        $sql = "select
            u.USUA_NOMB
            , $cad as USUA_COD
            ,u.DEPE_CODI
            ,u.USUA_CODI
            $soloJefesSelect
            from usuario u
            $soloJefesJoin
            where
            u.USUA_ESTA='1'
            $whereReasignar
            $whereDep
            $usuario_publico
            ORDER BY USUA_NOMB";
        $rs = $db->conn->Execute($sql);
        $usuario = $codUsuario;
        echo  "Reasignar a $proccarp </td><td>";
?>

                <select id="usCodSelect" name=usCodSelect class="form-control input-sm">
                    <option value="-1">-- Seleccione un funcionario --</option>
<?
        $setDefault = true;
        while(!$rs->EOF) {

            $depCodiP = $rs->fields["DEPE_CODI"];
            $usuNombP = $rs->fields["USUA_NOMB"];
            $usuCodiP = $rs->fields["USUA_COD"];
            $usuCodi  = $rs->fields["USUA_CODI"];
            $autGId   = isset($rs->fields["AUTG_ID"]) ? $rs->fields["AUTG_ID"] : null;

            $valOptionP = "";
            $valOptionP =$usuNombP;
            $class = "";
            echo "<hr> $usuCodi==1  || $usuCodi==$enrutadores<hr>";

            if(($usuCodi==1  || $usuCodi==$enrutadores) && $setDefault == true)
            {
                $defaultUs = "selected";
            }
            else if($EnviaraV=="VoBo" && $autGId==2)
            {
                $defaultUs = "selected";
                $setDefault = false;
            }  
            else {
                $defaultUs = "";
            }

            if($depCodiP!=$dependencia)
            {
                $sql = "select DEPE_NOMB from dependencia where depe_codi=$depCodiP";
                $rs2 = $db->conn->Execute($sql);
                $depNombP = $rs2->fields["DEPE_NOMB"];
                $valOptionP .= " [ ".$depNombP."] ";
                $class = " class='leidos'";
            }

?>
                <option <?=$class?>  value='<?=$usuCodiP?>' <?=$defaultUs?>><?=$valOptionP?></option>
<?
            $rs->MoveNext();
        }
?>
                </select>
<?

        break;
    case 10:
        $carpetaTipo = substr($carpSel,1,1);
        $carpetaCodigo = intval(substr($carpSel,-3));
        if($carpetaTipo==1)
        {
            $sql = "select NOMB_CARP as carp_desc from CARPETA_PER
                where
                codi_carp=$carpetaCodigo
                and usua_codi=$codusuario
                and depe_codi=$dependencia";
        } else {
            $sql = "select carp_desc from carpeta where carp_codi=$carpetaCodigo";
        }
        $rs = $db->conn->Execute($sql); # Ejecuta la busqueda y obtiene el recordset vacio
        $carpetaNombre = $rs->fields['carp_desc'];
        print "Movimiento a Carpeta <b>$carpetaNombre</b>
            <input type=hidden name='carpetaCodigo' value=$carpetaCodigo>
            <input type=hidden name='carpetaTipo' value=$carpetaTipo>
            <input type=hidden name='carpetaNombre' value=$carpetaNombre>
";
            break;
    case 12:
        print "Devolver documentos a Usuario Anterior ";
        break;
    case 13:
        $reasigna_requiere_exp = false;
        print "Finalizar tramite. <br> Envio de documento a Area de Archivo Virtual.";
        break;
    }
		?>
		<BR>
		</td>
		<td width='5' class="grisCCCCCC" align="center">
		<?php
		 if($codTx!=8 ||  $codTx!=18){
		?>
			<input type=button value="REALIZAR"
			onClick="okTx();" name=enviardoc align=bottom class="btn btn-sm btn-primary" id=REALIZAR>
	<?php
	}
	?>
		</td>
	</TR>
	<tr align="center">
	<td colspan="4" class="celdaGris" align=center>
        <?
		if(($esjefe || $reasAjefes || ($usuario_reasignacion == 1)) && ($codTx!=13 && $codTx!=8 && $codTx!=18))
		{
		?><label class="checkbox alert alert-info" align=left>
        <input type=checkbox name=chkNivel checked >
        <i></i>
        El documento tomara el nivel del usuario destino
      </label>
			<?
		}elseif($codTx==13){
			?>
			<input type="hidden" name="usCodSelect">
			<input type="hidden" name=chkNivel>
			<span class="info">El documento conservar&aacute; el nivel del usuario que archiva.</span><br>
			<?php
		}
		?>
		<tr bgcolor="White">
            <td>
                <ul class="demo-btns">
                    <li>
                        <div class="btn-group-vertical"><?=$buttacc1?></div>
                    </li>
                    <li>
                        <div class="btn-group-vertical"><?=$buttacc2?></div>
                    </li>
                </ul>
            </td>
            <td colspan=3>

                <label class="select">
                    <?=$select?>
                    <i></i>
                </label>

                <br />
                <label class="textarea">
                    <i class="icon-append fa fa-comment"></i>
                    <textarea name="observa" id="observa" placeholder="Escriba un Comentario" rows="3"></textarea>
                </label>
                <?php
                // verificar si es un usuario que asigna radicados de correo, habilitar informar de link
                if (substr($_GET['verrad'],-1)=='2' && file_exists("$ruta_raiz/cron/config.php")) {// si es entrada
                    include "$ruta_raiz/cron/config.php";
                    $link = false;
                    foreach ($conf_mail['carpetas'] as $usrs) {
                        if (in_array($_SESSION['krd'], $usrs))
                            $link = true;
                    }
                    if ($link) {
                    ?>
                    <button type="button" onclick="informar_link()" class="btn btn-default">Informar enlace</button>
                    <span class="alert alert-info" role="alert" id="alert" style="visibility:hidden"></span>
                <?php
                    }
                }
                ?>
			</td>
        </tr>
		<input type=hidden name=enviar value=enviarsi>
		<input type=hidden name=enviara value='9'>
		<input type=hidden name=carpeta value=12>
		<input type=hidden name=carpper value=10001>
	</td>
	</tr>
  <?php
		if ($codTx=="9" || $codTx=="8" || $codTx=="18" || $informarTerminacionNotificacion){
			if($varTramiteConjunto==1){  ?>
        <tr>
          <td  colspan=4>
            <input type=checkbox name=chkConjunto Value="Si" id="chkConjunto" >
            <span class="info">Tramite Conjunto  </span>
          </td>
        </tr>
	    <? }else{ ?>
					<input type='hidden' name="chkConjunto" id="chkConjunto" disabled >
     <? } ?>
			<tr>
        <td ><font face="Verdana" size="1"><b>
        <?=$textoInformados?>:</td><td >
					<label class='select select-multiple'>
                <?php
				$query ="SELECT DEPE_CODI||' - '||DEPE_NOMB as DEPE_NOMB, DEPE_CODI
					from DEPENDENCIA
					where depe_estado=1
					ORDER BY 1";
					$rs=$db->conn->Execute($query);
					$varQuery = $query;
					echo $rs->GetMenu2("coddepeinf", $coddepeinf, false, true,5," class='custom-scroll' multiple='' id='coddepeinf' ");
        ?>
     </label>
            </td>
        <td align=center width="36%">
        <label class='select select-multiple'>
            <select name="usuariosInformar" id="usuariosInformar" size="5" width=450
            class="custom-scroll"   align="LEFT" >
            </select>
        </label>

	</td>
        </tr>
            <tr >
            <td class="ecajasfecha"><font face="Verdana" size="1"><b>
            Agendar </b></font></td><td class="ecajasfecha">
            <input type="text" id="calendar" name="fechaAgenda"/>
            <button id="trigger"><img src="../include/zpcal/calendar2.ico" width="25" height="25"></button>
            <script type="text/javascript">//<![CDATA[
            Zapatec.Calendar.setup({
                firstDay          : 1,
                weekNumbers       : true,
                showOthers        : false,
                showsTime         : false,
                timeFormat        : "24",
                step              : 2,
                range             : [1900.01, 2999.12],
                electric          : false,
                singleClick       : true,
                inputField        : "calendar",
                button            : "trigger",
                ifFormat          : "%Y-%m-%d",
                daFormat          : "%Y/%m/%d",
                align             : "Br"
            });
            //]]></script>
            </td>
            </tr>
            <?php
        }
        ?>
            <tr><td colspan=6>
        <div id="usuariosInformados"> No Existen Usuarios Informados</div>
        <div id="msgBorrar"></div>
     </td></tr>
</TABLE>
<?php
	/*  GENERACION LISTADO DE RADICADOS
	 *  Aqui utilizamos la clase adodb para generar el listado de los radicados
	 *  Esta clase cuenta con una adaptacion a las clases utiilzadas de orfeo.
	 *  el archivo original es adodb-pager.inc.php la modificada es adodb-paginacion.inc.php
	 */
	if(!$orderNo)  $orderNo=0;
	$order = $orderNo + 1;
	$_rads_=implode(",",array_keys($_REQUEST["checkValue"]));
	if (strlen($whereFiltro)==9){
		$whereFiltro="and (radi_nume_radi in ($_rads_))";
	}
	$sqlFecha = $db->conn->SQLDate("d-m-Y H:i A","b.RADI_FECH_RADI");
	include_once "../include/query/tx/queryFormEnvio.php";

    if($codTx){
        $isql = str_replace("Enviado Por" ,"Devolver a",$isql);
	}

	$pager = new ADODB_Pager($db,$isql,'adodb', true,$orderNo,$orderTipo);
	$pager->toRefLinks = $linkPagina;
	$pager->toRefVars = $encabezado;
	$pager->checkAll = true;
	$pager->checkTitulo = true;
	$pager->Render($rows_per_page=2000,$linkPagina,$checkbox=chkAnulados);
?>
<input type="hidden" name=depsel value="<?=$depsel;?>">
</form>

<?php
}
?>
</div>
</div>
</div>
</body>
</html>
