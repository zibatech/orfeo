<?php
define('ADODB_ASSOC_CASE', 1);
/**
 * @author Jairo Losada   <jlosada@gmail.com>
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright

 SIIM2 Models are the data definition of SIIM2 Information System
 Copyright (C) 2013 Infometrika Ltda.

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
session_start();

$ruta_raiz = "..";
if (!$_SESSION['dependencia'])
header ("Location: $ruta_raiz/cerrar_session.php");

$krd = $_SESSION["krd"];
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$nomcarpeta = isset($_GET["carpeta"]) ? $_GET["carpeta"] : '';
$tipo_carpt = isset($_GET["tipo_carpt"]) ? $_GET["tipo_carpt"] : '';
$orderNo = isset($_GET["orderNo"]) ? $_GET["orderNo"] : '';
$orderTipo = isset($_GET["orderTipo"]) ? $_GET["orderTipo"] : '';
$tipoEstadistica = isset($_REQUEST["tipoEstadistica"]) ? $_REQUEST["tipoEstadistica"] : '';
$genDetalle = isset($_GET["genDetalle"]) ? $_GET["genDetalle"] : '';
$dependencia_busq = isset($_GET["dependencia_busq"]) ? $_GET["dependencia_busq"] : '';
$fecha_ini = isset($_GET["fecha_ini"]) ? $_GET["fecha_ini"] : '';
$fecha_fin = isset( $_GET["fecha_fin"]) ? $_GET["fecha_fin"] : '';
$codus = isset($_GET["codus"]) ? $_GET["codus"] : '';
$tipoRadicado = isset($_GET["tipoRadicado"]) ? $_GET["tipoRadicado"] : '';

$codUs = isset($_GET["codUs"]) ? $_GET["codUs"] : '';
$fecSel = isset($_GET["fecSel"]) ? $_GET["fecSel"] : '';
$genDetalle = isset($_GET["genDetalle"]) ? $_GET["genDetalle"] : '';
$generarOrfeo = isset($_GET["generarOrfeo"]) ? $_GET["generarOrfeo"] : '';
$dependencia_busqOri = isset($_GET["dependencia_busqOri"]) ? $_GET["dependencia_busqOri"] : '';

$dependencia           = $_SESSION["dependencia"];
$usua_doc              = $_SESSION["usua_doc"];
$codusuario            = $_SESSION["codusuario"];
$tip3Nombre            = $_SESSION["tip3Nombre"];
$tip3desc              = $_SESSION["tip3desc"];
$tip3img               = $_SESSION["tip3img"];
$usua_perm_estadistica = $_SESSION["usua_perm_estadistica"];


if(!$tipoEstadistica) $tipoEstadistica   = 1;
if(!$dependencia_busq) $dependencia_busq = $dependencia;
if(!$dependencia_busqOri) $dependencia_busqOri = $dependencia;
?>
<script>
	function list(datos)
	{
		window.location.assign("<?=$ruta_raiz;?>/estadisticas/listado.php?"+datos);
	}
</script>
<?

/** DEFINICION DE VARIABLES ESTADISTICA
 *	var $tituloE String array  Almacena el titulo de la Estadistica Actual
 * var $subtituloE String array  Contiene el subtitulo de la estadistica
 * var $helpE String Almacena array Almacena la descripcion de la Estadistica.
 */
$tituloE[1] = "RADICACION - CONSULTA DE RADICADOS POR USUARIO";
$tituloE[2] = "RADICACION - ESTADISTICAS POR MEDIO DE RECEPCION";
$tituloE[3] = "RADICACION - ESTADISTICAS DE MEDIO ENVIO FINAL DE DOCUMENTOS";
$tituloE[4] = "RADICACION - ESTADISTICAS DE DIGITALIZACION DE DOCUMENTOS"; //// **************
$tituloE[5] = "RADICADOS DE ENTRADA RECIBIDOS DEL AREA DE CORRESPONDENCIA";
$tituloE[6] = "RADICADOS ACTUALES EN LA DEPENDENCIA";
$tituloE[7] = "CONTROL ENTREGA DE CORRESPONDENCIA RECIBIDA";
//$tituloE[11] = "ESTADISTICA DE DIGITALIZACION";
$tituloE[17] = "ESTADISTICA POR RADICADOS Y SUS RESPUESTAS ";
//$tituloE[18] = "ESTADISTICA POR select * from sgd_srd_seriesrd where sgd_srd_descrip ilike '%DERECHO%PET%'RADICADOS CON SERIE";
$tituloE[19] = "ESTADISTICA PARA INFORME PQR";
//$tituloE[20] = "RADICADOS CON 4 VISTOS BUENOS";
//$tituloE[21] = "CANTIDAD DE RADICADOS EN BANDEJA JEFES";
//$tituloE[22] = "CANTIDAD DE RADICADOS EN BANDEJA POR DEPENDENCIA";
$tituloE[23] = "CANTIDAD DE RADICADOS EN BANDEJA JEFES";
$tituloE[24] = "CANTIDAD DE RADICADOS EN BANDEJA POR DEPENDENCIA/AREA";
$tituloE[25] = "CANTIDAD DE RADICADOS QUE TRANSITAN POR DEPENDENCIA/AREA";
$tituloE[26] = "RADICADOS POR TIPO";
$tituloE[27] = "RADICADOS POR USUARIO - (IMAGEN Y ANEXOS)";
//$tituloE[28] = "RADICADOS SIN ANEXOS";
$tituloE[29] = "RADICADOS POR USUARIO - (DATOS DE ENVIO)";

$subtituloE[1] = "ORFEO - Generada el: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[2] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[3] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[4] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[5] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[6] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[8] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[17] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[18] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[19] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";	
$subtituloE[20] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";	
//$subtituloE[21] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
//$subtituloE[22] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
$subtituloE[23] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n ";
$subtituloE[24] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n ";
$subtituloE[25] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n ";
$subtituloE[26] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n ";
$subtituloE[27] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n ";
//$subtituloE[28] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n ";
$subtituloE[29] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n "; 


$helpE[1] = "Este reporte genera la cantidad de radicados por usuario. Se puede discriminar por tipo de radicaci&oacute;n. " ;
$helpE[2] = "Este reporte genera la cantidad de radicados de acuerdo al medio de recepci&oacute;n, realizado al momento de la radicaci&oacute;n. " ;
$helpE[3] = "Este reporte genera la cantidad de radicados enviados a su destino final por el &aacute;rea.  " ;
$helpE[4] = "Este reporte genera la cantidad de radicados digitalizados por usuario y el total de hojas digitalizadas. Se puede seleccionar el tipo de radicaci&oacute;n." ;
$helpE[5] = "Este reporte genera la cantidad de documentos de entrada radicados del &aacute;rea de correspondencia a una dependencia. " ;
$helpE[6] = "Esta estadistica trae la cantidad de radicados \n generados por usuario, se puede discriminar por tipo de Radicacion. " ;
$helpE[7] = "Este reporte trae la cantidad de radicados \n generados por correspondencia. ";
$helpE[8] = "Este reporte genera la cantidad de radicados de entrada cuyo vencimiento esta dentro de las fechas seleccionadas. " ;
$helpE[9] = "Este reporte muestra el proceso que han tenido los radicados tipo 2 que ingresaron durante las fechas seleccionadas. ";
$helpE[10] = "Este reporte muestra cuantos radicados de entrada han sido asignados a cada dependencia. ";
//$helpE[11] = "Muestra la cantidad de radicados digitalizados por usuario y el total de hojas digitalizadas. Se puede seleccionar el tipo de radicaci&oacute;n y la fecha de digitalizaci&oacute;n." ;
$helpE[12] = "Muestra los radicados que ten&iacute;an asignados un tipo documental(TRD) y han sido modificados";
$helpE[13] = "Muestra todos los expedientes agrupados por dependencia que con el n&uacute;mero de radicados totales";
$helpE[14] = "Muestra el total de radicados que tiene un usuario y
el detalle del radicado con respecto al Remitente(Detalle),
   Predio(Detalle), ESP(Detalle) ";
$helpE[17] = "Este reporte genera la cantidad de documentos que han llegado al area o usuarios sin importar su origen. , <br> El reporte muestra los radicados que han pasado por los usuarios y me deja ver sus respectivas Respuestas y tiempos qeu demoro para cada paso." ;
//$helpE[18] = "Este reporte refleja el Tramite que se les ha dado a los Radicados HASTA EL DIA INMEDIATAMENTE ANTERIOR Puede filtrarse por la fecha de REASIGNACION y la dependencia ORIGEN y DESTINO " ;
$helpE[19] = "Este reporte genera la cantidad de documentos para PQR.  Genera todos los radicados de las consultas con sus respuestas, calculo de dias en respuesta.  Siempre y cuando cada radicado tenga un anexo. " ;
$helpE[20] = "Este reporte genera los radicados con  cuatro (4) vistos buenos filtrados por tipo de radicado" ;
$helpE[23] = "Muestra la cantidad de raricados en bandeja de usuarios jefes" ;
$helpE[24] = "Muestra la cantidad de raricados en bandeja totales o por dependencias" ;
$helpE[25] = "Muestra la cantidad de raricados y/o documentos que transitan por una dependencia. <br> En esta consulta se observaran los radicados que llegan a cada usuario de cadad area en los eventos de radicacion, reasignacion, devolucion y envio para visto bueno." ;
$helpE[26] = "Muestra los radicados por tipo que se generaron en determinado periodo";
$helpE[27] = "Muestra los radicados que no cuentan con imagen.";
//$helpE[28] = "Muestra los radicados que no cuentan con anexos.";
$helpE[29] = "Muestra los radicados de salida que no cuentan con datos de envio.";
?>

<html>
<head>
<title>principal</title>

<?php
include_once("$ruta_raiz/htmlheader.inc.php");
include_once("../js/funtionImage.php");
?>

<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
<script>

function adicionarOp (forma,combo,desc,val,posicion){
	o = new Array;
	o[0]=new Option(desc,val );
	eval(forma.elements[combo].options[posicion]=o[0]);
}

function noPermiso(){
	alert ("No tiene permiso para acceder");
}

</script>
<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>

<script language="javascript">
<!--
<?//corrregido fecha inicial que se mostraba mal en enero rperilla
$ano_ini = date("Y");
$mes_ini = substr("00".(date("m")-1),-2);
if ($mes_ini=="00") {
	$ano_ini=$ano_ini-1; $mes_ini="12";
}
$dia_ini = date("d");

if(!$fecha_ini) $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
$fecha_busq = date("Y/m/d") ;
if(!$fecha_fin) $fecha_fin = $fecha_busq;
?>

//--></script>
</head>
<?php
$ruta_raiz = "..";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
include("$ruta_raiz/class_control/usuario.php");
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$objUsuario = new Usuario($db);
?>

<body bgcolor="#ffffff" onLoad="comboUsuarioDependencia(document.formulario,document.formulario.elements['dependencia_busq'].value,'codus');" topmargin="0">
<div id="spiffycalendar" class="text"></div>
<form name="formulario"  method="GET" action='./vistaFormConsulta.php?<?=session_name()."=".trim(session_id())."&fechah=$fechah"?>'>
<div class="col-sm-12">
<!-- widget grid -->
<h2></h2>
<section id="widget-grid">
<!-- row -->
<div class="row">
<!-- NEW WIDGET START -->
<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">

<header>
<h2> Estadisticas</h2>
</header>
<!-- widget content -->
<div class="widget-body">
<div class="table-responsive">
<table class="table table-bordered table-striped smart-form">
<tr>
<td colspan="2" >
POR RADICADOS -
<a  href='vistaFormProc.php?<?=session_name()."=".trim(session_id())."&fechah=$fechah"?>'>POR PROCESOS </a>
<a class='btn btn-default btn-sm pull-right' href='vistaFormConsultaNew.php?<?=session_name()."=".trim(session_id())."&fechah=$fechah"?>'>NUEVA VERSION </a>
</td>
</tr>
<tr>
<td colspan="2" ><span class="cal-TextBox"><?=$helpE[$tipoEstadistica]?></span></td>
</tr>
<tr>
<td width="30%" >Tipo de Consulta / Estadistica</td>
<td class="listado2" align="left">
<label class="select">
<select name=tipoEstadistica  class="select" onChange="formulario.submit();">
<?php
$num = 1;
foreach($tituloE as $key=>$value){
	?> <?php if($tipoEstadistica==$key) $selectE = " selected "; else $selectE = ""; ?>
		<option value=<?=$key?> <?=$selectE?>><?=$num?> - <?=$tituloE[$key]?></option>
	<?
$num++;
}
?>
<?php if (isset($tipoEstadistica) and $tipoEstadistica!=26){?>
<TR>
<TD width="125" height="21"  class='titulos2'>DEPENDENCIA</td>
<td colspan="3"  class="listado5"> 
<label class="select">
<?
$coddepe = isset($_GET['dependencia_busq']) ? $_GET['dependencia_busq'] : '';
include_once "$ruta_raiz/include/query/envios/queryPaencabeza.php";
$sqlConcat = $db->conn->Concat($db->conn->substr."($conversion,1,5) ", "'-'",$db->conn->substr."(depe_nomb,1,30) ");
if ($usua_perm_estadistica>1){
	$sql = "select $sqlConcat ,depe_codi from dependencia 
		order by depe_codi";
	$rsDep = $db->conn->Execute($sql);
	print $rsDep->GetMenu2("dependencia_busq",$coddepe,'99999:-- Todas las Dependencias --', false,""," onChange='formulario.submit();' class='select'");
}else{
	$sql = "select $sqlConcat ,depe_codi from dependencia where DEPE_CODI=$dependencia  
		order by depe_codi";
	$rsDep = $db->conn->Execute($sql);
	print $rsDep->GetMenu2("dependencia_busq","$coddepe",false, false,""," onChange='formulario.submit();' class='select'");

}
if($tipoEstadistica!=19 && $tipoEstadistica!=23 && $tipoEstadistica!=24 && $tipoEstadistica!=25 && $tipoEstadistica!=26 ){
?>
</label>
</td>
</tr>
<?php } ?>
<TR>
<TD width="125" height="21"  class='titulos2'> SERIE </td>
<td colspan="3"  class="listado5"> 
<label class="select">
<?php
include "$ruta_raiz/trd/actu_matritrd.php";  
if(!isset($codserie) || !$codserie) $codserie = 0;
$fechah=date("dmy") . " ". time("h_m_s");
$fecha_hoy = date("d-m-y");
$sqlFechaHoy="'".$fecha_hoy."'";
$check=1;
$fechaf=date("dmy") . "_" . time("hms");
$num_car = 4;
$nomb_varc = "sgd_srd_codigo";
$nomb_varde = "sgd_srd_descrip";
include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
if($coddepe == "" || $coddepe == "99999"){
	$auxsqlser ="";
}else{
	$auxsqlser = "and (cast(m.depe_codi as varchar(10)) = '$coddepe' or cast(m.depe_codi_aplica as varchar(100)) like '%$coddepe%' or cast(m.depe_codi as varchar(10))='$depDireccion')";
}
$querySerie = "select distinct (s.sgd_srd_descrip||'-'||s.sgd_srd_codigo) as detalle, s.sgd_srd_codigo
                from sgd_mrd_matrird m, sgd_srd_seriesrd s
                where
                s.id = m.sgd_srd_id
                $auxsqlser
                and s.sgd_srd_estado  = '1' 
                and " . $db->sysdate() . " between s.sgd_srd_fechini and s.sgd_srd_fechfin
                order by  (s.sgd_srd_descrip||'-'||s.sgd_srd_codigo), s.sgd_srd_codigo";

$rsD=$db->conn->query($querySerie);
$comentarioDev = "Muestra las Series Docuementales";
include "$ruta_raiz/include/tx/ComentarioTx.php";
print $rsD->GetMenu2("codserie", $codserie, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
?>
</label>
<TR>
<TD width="125" height="21"  class='titulos2'> SUBSERIE</td>
<td colspan="3"  class="listado5"> 
<label class="select">
<?
$nomb_varc = "sgd_sbrd_codigo";
$nomb_varde = "sgd_sbrd_descrip";
include "$ruta_raiz/include/query/trd/queryCodiDetalle.php"; 
//Modificado skina 11-02-09
//$sqlFechaHoy=$db->conn->SQLDate('Y-m-d',$fecha_hoy);
$fecha_hoy = date("Y-m-d");
$sqlFechaHoy="'".$fecha_hoy."'";

if($coddepe == "" || $coddepe == "99999"){
	$auxsql ="";
}else{
	$auxsql = "and (cast(m.depe_codi as varchar(10)) = '$coddepe' or cast(m.depe_codi_aplica as varchar(100)) like '%$coddepe%' or cast(m.depe_codi as varchar(10))='$depDireccion')";
}
$querySub = "
select distinct (su.sgd_sbrd_descrip||'-'||su.sgd_sbrd_codigo) as detalle, su.sgd_sbrd_codigo
                from sgd_mrd_matrird m, sgd_sbrd_subserierd su
                where 
				m.sgd_srd_codigo = '$codserie'
				$auxsql
                and su.sgd_srd_codigo = '$codserie'
                and su.id = m.sgd_sbrd_id
                and cast(m.sgd_mrd_esta as numeric(1))       = 1
                and su.sgd_srd_id = m.sgd_srd_id
                and " . $db->sysdate() . " between su.sgd_sbrd_fechini and su.sgd_sbrd_fechfin
                order by detalle
";
$rsSub=$db->conn->query($querySub);
include "$ruta_raiz/include/tx/ComentarioTx.php";
$tsub = isset($tsub) ? $tsub : '';
print $rsSub->GetMenu2("tsub", $tsub, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );

?> 
</label>
</td>


<?php
}
if ($tipoEstadistica==18){?>
	<tr>
		<td width="30%" >Dependencia ORIGEN</td>
		<td class="listado2">
		<label class="select">
		<select name=dependencia_busqOri  class="select"  onChange="formulario.submit();">
		<?php
		$encabezado = '&genDetalle=' . $genDetalle .
		'&tipoEstadistica=' . $tipoEstadistica .
		'&codus=' . $codus .
		'&dependencia_busq=' . $dependencia_busq .
		'&dependencia_busqOri=' . $dependencia_busqOri .
		'&ruta_raiz=' . $ruta_raiz .
		'&fecha_ini=' . $fecha_ini .
		'&fecha_fin=' . $fecha_fin .
		'&tipoRadicado=' . $tipoRadicado .
		'&tipoDocumento=' . $tipoDocumento .
		'&codUs=' . $codUs .
		'&fecSel=' . $fecSel .
		'&';
	if($usua_perm_estadistica>1)  {
		if($dependencia_busqOri==99999)  {
			$datossOri= " selected ";
		}
		?>
			<option value="99999" <?=$datossOri?>>-- Todas las Dependencias --</option>
			<?php
	}
	$whereDepSelectOri=" DEPE_CODI = $dependencia ";
	if ($usua_perm_estadistica==1){
		$whereDepSelectOri="( $whereDepSelectOri or depe_codi_padre = $dependencia )";
	}
	if ($usua_perm_estadistica==2) {
		$isqlusOri = "select a.DEPE_CODI,a.DEPE_NOMB,a.DEPE_CODI_PADRE from DEPENDENCIA a ORDER BY a.DEPE_NOMB";
	}else {
		$isqlusOri = "select a.DEPE_CODI,a.DEPE_NOMB,a.DEPE_CODI_PADRE from DEPENDENCIA a
			where $whereDepSelectOri ";
	}
	$rsO=$db->query($isqlusOri);

	do{
		$codigoOri = $rsO->fields["DEPE_CODI"];
		$vecDeps[]=$codigoOri;
		$depnombreOri = $rsO->fields["DEPE_NOMB"];
		$datossOri="";
		if($dependencia_busqOri==$codigoOri){
			$datossOri = " selected ";
		}
		echo "<option value=$codigoOri  $datossOri>$depnombreOri</option>";
		$rsO->MoveNext();
	}while(!$rsO->EOF);
	?>
		</select>
		</label>
		</td>
		</tr>
		<?}
		// Variable para controlar el la vista de opcion a los usuarios segun el tipo de estadistica
		$mostrar_estadisticas = array(1,2,3,4,5,6,7,11,12,17,18,19);
		$mostrar_usuario = in_array($tipoEstadistica, $mostrar_estadisticas);

		if($mostrar_usuario) {
			?>
				<tr id="cUsuario">
				<td width="30%" >Usuario
				<br />
				<?	$datoss = isset($usActivos) && ($usActivos) ?" checked ":"";	?>
				<label class="checkbox">
				<input name="usActivos" type="checkbox" class="select" <?=$datoss?> onChange="formulario.submit();">
				<i></i>Incluir Usuarios Inactivos
				</label>
				<?  if($tipoEstadistica==17 or $tipoEstadistica==18 or $tipoEstadistica==19 ) { ?>
					<?	$datoss = isset($conSinRep) && ($conSinRep)? " checked " : '';	?>
						<label class="checkbox">
						<input name="conSinRep" type="checkbox" class="select" <?=$datoss?> onChange="formulario.submit();">
						<i></i> Limitar a una respuesta
						</label>
						<?  } ?>
						</td>
						<td class="listado2">
						<label class="select">
						<select name="codus"  class="select"  onChange="formulario.submit();">
						<?php 	if(($tipoEstadistica==5) AND ($usua_perm_estadistica > 0)) {	?>
							<option value=0> -- NO AGRUPAR POR USUARIOS --</option>
								<?php }
			if (($usua_perm_estadistica > 0) AND ($tipoEstadistica!=5)){	?>
				<option value=0> -- AGRUPAR POR TODOS LOS USUARIOS --</option>
					<?php }

			$whereDep = ($dependencia_busq != 99999) ? "  u.DEPE_CODI = " . $dependencia_busq : '';

			$whereUsSelect = (!isset($_GET['usActivos']) )? " u.USUA_ESTA = '1' ":"";
			$whereUsSelect=($usua_perm_estadistica < 1)?
				(($whereUsSelect!="")?$whereUsSelect." AND u.USUA_LOGIN='$krd' ":" u.USUA_LOGIN='$krd' "):$whereUsSelect;	  if($dependencia_busq != 99999)  {

					$whereUsSelect=($whereUsSelect=="")? $whereDep:$whereUsSelect." and  ".$whereDep;
					$isqlus = "select u.USUA_NOMB,u.USUA_CODI,u.USUA_ESTA from USUARIO u
						where  $whereUsSelect
						order by u.USUA_NOMB";
					//if($codusuario!=1) $isqlus .= " and a.usua_codi=$codusuario ";
					echo "<hr>".$isqlus;
					$rs1=$db->conn->query($isqlus);
					while(!$rs1->EOF)  {
						$codigo = $rs1->fields["USUA_CODI"];
						$vecDeps[]=$codigo;
						$usNombre = $rs1->fields["USUA_NOMB"];
						$datoss=($codus==$codigo)?$datoss= " selected ":"";
						echo "<option value=$codigo  $datoss>$usNombre</option>";
						$rs1->MoveNext();
					}
				}
			?>
				</select>
				</label>
				&nbsp;</td>
				</tr>
				<?
		}
$condiRep =  (!isset($_GET['conSinRep']) )? "NO":"SI";

if($tipoEstadistica==1 or $tipoEstadistica==2 or $tipoEstadistica==3 or
		$tipoEstadistica==4 or $tipoEstadistica==6 or $tipoEstadistica==7 or $tipoEstadistica==23 or $tipoEstadistica==24 or $tipoEstadistica==11 or
		$tipoEstadistica==12 or $tipoEstadistica==17  or $tipoEstadistica==18 or $tipoEstadistica==20 or $tipoEstadistica==26 or $tipoEstadistica==27 or $tipoEstadistica==28 )
{
	?>
		<tr>
		<td width="30%" height="40" >Tipo de Radicado </td>
		<td class="listado2">
		<label class="select">
		<?php
		$rs = $db->conn->Execute('select SGD_TRAD_DESCR, SGD_TRAD_CODIGO  from SGD_TRAD_TIPORAD order by 2');
	$nmenu = "tipoRadicado";
	$valor = "";
	$default_str=$tipoRadicado;
	$itemBlanco = " -- Agrupar por Todos los Tipos de Radicado -- ";
	print $rs->GetMenu2($nmenu, $default_str, $blank1stItem = "$valor:$itemBlanco",false,0,'class=select');
	?>&nbsp;
	</label>
		</td>
		</tr>
		<?
}
if($tipoEstadistica==1 or $tipoEstadistica==6 or $tipoEstadistica==7 or $tipoEstadistica==10 or
		$tipoEstadistica==12 or $tipoEstadistica == 14 or $tipoEstadistica==17  or $tipoEstadistica==18  or $tipoEstadistica==27 or $tipoEstadistica==28 or $tipoEstadistica==29 ) {
	?>
		<tr>
		<td width="30%" height="40" >Agrupar por Tipo de Documento </td>
		<td class="listado2">
		<label class="select">
		<select name=tipoDocumento  class="select" >
		<?
		$isqlTD = "SELECT SGD_TPR_DESCRIP, SGD_TPR_CODIGO
		from SGD_TPR_TPDCUMENTO
		WHERE SGD_TPR_CODIGO<>0
		order by  SGD_TPR_DESCRIP";
	//if($codusuario!=1) $isqlus .= " and a.usua_codi=$codusuario ";
	//echo "--->".$isqlus;
	$rs1=$db->query($isqlTD);
	$datoss = "";

	if($tipoDocumento!='9998'){
		$datoss= " selected ";
		$selecUs = " b.USUA_NOMB USUARIO, ";
		$groupUs = " b.USUA_NOMB, ";
	}

	$datoss = "";

	if($tipoDocumento=='9999'){
		$datoss= " selected ";
	}
	?>
		<option value='9999'  <?=$datoss?>>-- No Agrupar Por Tipo de Documento</option>
		<?   $datoss = "";


	if($tipoDocumento=='9997'){
		$datoss= " selected ";
	}
	if($tipoEstadistica==6)
	{
		if($tipoDocumento=='9998'){
			$datoss= " selected ";
		}

		?>
			<option value='9998'  <?=$datoss?>>-- Agrupar Por Tipo de Documento</option>
			<?
	}
	?>

		<option value='9997'  <?=$datoss?>>-- Tipos Documentales No Definidos</option>
		<?
		do{
			$codigo = $rs1->fields["SGD_TPR_CODIGO"];
			$vecDeps[]=$codigo;
			$selNombre = $rs1->fields["SGD_TPR_DESCRIP"];
			$datoss="";
			if($tipoDocumento==$codigo){
				$datoss= " selected ";
			}
			echo "<option value=$codigo  $datoss>$selNombre</option>";
			$rs1->MoveNext();
		}while(!$rs1->EOF);
	?>
		</select>
		</label>
		</td>
		</tr>
		<?
}
if( $tipoEstadistica==1   or $tipoEstadistica==2  or $tipoEstadistica==3  or
		$tipoEstadistica==4   or $tipoEstadistica==5  or $tipoEstadistica==6 or $tipoEstadistica==7  or
		$tipoEstadistica==8   or $tipoEstadistica==9  or $tipoEstadistica==10 or
		$tipoEstadistica==11  or $tipoEstadistica==12 or $tipoEstadistica==14 or
		$tipoEstadistica==17  or $tipoEstadistica==19 or $tipoEstadistica==20 or $tipoEstadistica==21  or $tipoEstadistica==22 or 
		$tipoEstadistica==25  or $tipoEstadistica==27 or $tipoEstadistica==28 or $tipoEstadistica==29 ) { ?>
	<tr>
		<td width="30%" >Desde fecha (aaaa/mm/dd) </td>
		<td class="listado2">
		<label class="input"> <i class="icon-append fa fa-calendar"></i>
		<input type="text" name="fecha_ini" id="startdate1" placeholder="Fecha de busqueda final" value="<?=$fecha_ini?>">
		</label>
		</td>
		</tr>
		<tr>
		<td width="30%" >Hasta  fecha (aaaa/mm/dd) </td>
		<td class="listado2">
		<label class="input"> <i class="icon-append fa fa-calendar"></i>
		<input type="text" name="fecha_fin" id="finishdate1" placeholder="Fecha de busqueda final" value="<?=$fecha_fin?>">
		</label>
		</tr>
		<?
}
if( $tipoEstadistica==26){
?><tr>
		<td width="30%" >Año de secuencia </td>
		<td class="listado2">
		<label class="select">
		 <select class="select" name="ano_secuencia">
                            <?
                    $agnoactual=Date('Y');
                    for($i = 1990; $i <= $agnoactual; $i++)
                    {
                      if(isset($_GET['ano_secuencia']) and $i == $_GET['ano_secuencia']) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
                      elseif (!isset($_GET['ano_secuencia']) and  $agnoactual ==$i) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
		      else  $option="<option value=\"" . $i . "\">" . $i . "</option>";
                      echo $option;
                    }
                    ?>
                </select>
		</label>
		</td>
		</tr>
<?
}
if( $tipoEstadistica==18){
	?>
		<tr>
		<td width="30%" >REASIGNADO Desde fecha (aaaa/mm/dd) </td>
		<td class="listado2">
		<label class="input"> <i class="icon-append fa fa-calendar"></i>
		<input type="text" name="fecha_ini2" id="startdate2" placeholder="Fecha de busqueda inicial" value="<?=$fecha_ini?>">
		</label>
		</td>
		</tr>
		<tr>
		<td width="30%" >REASIGNADO Hasta fecha (aaaa/mm/dd) </td>
		<td class="listado2">
		<label class="input"> <i class="icon-append fa fa-calendar"></i>
		<input type="text" name="fecha_fin2" id="finishdate2" placeholder="Fecha de busqueda final" value="<?=$fecha_fin?>">
		</label>
		</td>
		</tr>
		<?
}
?>
<tr>
<td colspan="2" > 
<center>
<input name="Submit" type="submit" class="btn btn-primary btn-sm" value="Limpiar">
<input type="submit" class="btn btn-primary btn-sm" value="Generar" name="generarOrfeo">
</center>
</td>
</tr>
</table>
</div>
</div>
</div>
</article>
</div>
</section>
</div>
</form>
<script type="text/javascript">
$(document).ready(function() {

		$('#startdate1').datepicker({
dateFormat : 'yy/mm/dd',
onSelect : function(selectedDate) {
$('#startdate1').datepicker('option', 'maxDate', selectedDate);
}
});

		$('#finishdate1').datepicker({
dateFormat : 'yy/mm/dd',
onSelect : function(selectedDate) {
$('#finishdate1').datepicker('option', 'minDate', selectedDate);
}
});

		$('#startdate2').datepicker({
dateFormat : 'yy/mm/dd',
onSelect : function(selectedDate) {
$('#startdate2').datepicker('option', 'maxDate', selectedDate);
}
});

$('#finishdate2').datepicker({
dateFormat : 'yy/mm/dd',
onSelect : function(selectedDate) {
$('#finishdate2').datepicker('option', 'minDate', selectedDate);
}
});
});
</script>
<?php
$tipoDocumento = isset($tipoDocumento) ? $tipoDocumento : '';
$datosaenviar = "fechaf=$fechaf&tipoEstadistica=$tipoEstadistica&codus=$codus&dependencia_busq=$dependencia_busq&dependencia_busqOri=$dependencia_busqOri&ruta_raiz=$ruta_raiz&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&tipoRadicado=$tipoRadicado&tipoDocumento=$tipoDocumento";

if (isset($generarOrfeo) && $tipoEstadistica == 12) {
	global $orderby;
	$orderby = 'ORDER BY NOMBRE';
	$whereDep = ($dependencia_busq != 99999) ? "AND h.DEPE_CODI = " . $dependencia_busq : '';
	//modificado idrd para postgres
	$isqlus = "SELECT u.USUA_NOMB NOMBRE, u.USUA_DOC, d.DEPE_CODI,
		COUNT(r.RADI_NUME_RADI) as TOTAL_MODIFICADOS
			FROM USUARIO u, RADICADO r, HIST_EVENTOS h, DEPENDENCIA d, SGD_TPR_TPDCUMENTO s
			WHERE u.USUA_DOC = h.USUA_DOC
			AND h.SGD_TTR_CODIGO = 32
			AND h.HIST_OBSE LIKE '*Modificado TRD*%'
			AND h.DEPE_CODI = d.DEPE_CODI
			$whereDep
			AND s.SGD_TPR_CODIGO = r.TDOC_CODI
			AND r.RADI_NUME_RADI = h.RADI_NUME_RADI
			AND TO_CHAR(r.RADI_FECH_RADI,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin'
			GROUP BY u.USUA_NOMB, u.USUA_DOC, d.DEPE_CODI $orderby";
	$rs1 = $db->query($isqlus);
	while(!$rs1->EOF)  {
		$usuadoc[] = $rs1->fields["USUA_DOC"];
		$dependencias[] = $rs1->fields["DEPE_CODI"];
		$rs1->MoveNext();
	}
}

if($generarOrfeo) {
	
	$db->conn->debug =false; //true;
	include "genEstadistica.php";
}
?>
