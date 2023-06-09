<?
session_start();

if (!$ruta_raiz) $ruta_raiz = "..";
    if (!$_SESSION['dependencia']) header ("Location: $ruta_raiz/cerrar_session.php");

/**
* Pagina Archivo.php que muestra el contenido de las Carpetas
* Modificado por Correlibre.org en el año 2012
* Se añadio compatibilidad con variables globales en Off
* @autor Jairo Losada 2012-05
* @licencia GNU/GPL V 3
*/

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

define('ADODB_ASSOC_CASE', 2);

$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
$usua_perm_archi = $_SESSION["usua_admin_archivo"];
  include_once "$ruta_raiz/processConfig.php";
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$encabezadol = "$PHP_SELF?".session_name()."=".session_id()."&num_exp=$num_exp";
?>
<html height=50,width=150>
<head>

  <title>Sistema de informaci&oacute;n <?=$entidad_largo?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap core CSS -->
  <?php include_once "../htmlheader.inc.php"; ?>
</head>
<body class="smart-form">
<div id="spiffycalendar" class="text"></div>
 <link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
 <script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js">
 </script>
<? if (file_exists($nombre_fichero_css)) { ?>
        <link href="<?=$ruta_raiz?>/estilos/<?=$entidad?>.bootstrap.min.css" rel="stylesheet">
	<? }else{ ?>
	<link href="<?=$ruta_raiz?>/estilos/correlibre.bootstrap.min.css" rel="stylesheet">
	<? } ?>
 <form class="smart-form" name=from1 action="<?=$encabezadol?>" method='post' action='archivo.php?<?=session_name()?>=<?=trim(session_id())?>&krd=<?=$krd?>&<?="&num_exp=$num_exp"?>'>
<br>

<table  class="table table-bordered">
<TD class=titulos2 align="center" >
		Menu de Archivo
</TD>
<tr>
<td class=listado2>
<?
	$phpsession = session_name()."=".session_id();
        if($usua_perm_archi!=3 and $usua_perm_archi!=4){
	?>
<span class="leidos2"><a href='../expediente/cuerpo_exp.php?<?=$phpsession?>&<?="fechaf=$fechah&carpeta=8&nomcarpeta=Expedientes&orno=1&adodb_next_page=1"; ?>' target='mainFrame' class="menu_princ"><b>1. Busqueda Basica </a></span>
	</td>
	</tr>
<tr>
  <td class="listado2">
<span class="leidos2"><a href='busqueda_archivo.php?<?=session_name()."=".session_id()."&dep_sel=$dep_sel&fechah=$fechah&$orno&adodb_next_page&nomcarpeta&tipo_archivo=$tipo_archivo&carpeta'" ?>" target='mainFrame' class="menu_princ"><b>2. Busqueda Avanzada </a>
</td>
</tr>
<? }?>
    <tr>
    <td class=listado2>
<span class="leidos2"><a  href='reporte_archivo.php?<?=session_name()."=".session_id()."&adodb_next_page&nomcarpeta&fechah=$fechah&$orno&carpeta&tipo=1'" ?>' target='mainFrame' class="menu_princ"><b>3. Reporte por Radicados Archivados</a>
</td>
</tr>
<?
if($usua_perm_archi!=3 and $usua_perm_archi!=4){
	?>
<tr>
<td class=listado2>

<span class="leidos2"><a href='inventario.php?<?=session_name()."=".session_id()."&fechah=$fechah&$orno&adodb_next_page&nomcarpeta&carpeta&tipo=2'" ?>' target='mainFrame' class="menu_princ"><b> 4.Cambio de Coleccion</a>
</td>
</tr>
<tr>
<td class=listado2>

<span class="leidos2"><a href='inventario.php?<?=session_name()."=".session_id()."&fechah=$fechah&$orno&nomcarpeta&carpeta&tipo=1'" ?>' target='mainFrame' class="menu_princ"><b>5.Inventario Consolidado Capacidad</a>	  </td>
</tr>
<tr>
<td class=listado2>
<span class="leidos2"><a href='<?php print $ruta_raiz; ?>/archivo/formatoUnico.php?<?=session_name()."=".session_id()."&fechah=$fechah&$orno&adodb_next_page" ?>' target='mainFrame' class="menu_princ"><b>6.Formato Unico De Inventario Documental    </a></td>
</tr>
<tr>
<td class=listado2>
<span class="leidos2"><a href='sinexp.php?<?=session_name()."=".session_id()."&fechah=$fechah&$orno&adodb_next_page&nomcarpeta&carpeta&tipo=3'" ?>' target='mainFrame' class="menu_princ"><b>7.Radicados Archivados Sin Expediente</a>	</td>
</tr>
<tr>
	<td class=listado2>
<span class="leidos2"><a href='alerta.php?<?=session_name()."=".session_id()."&fechah=$fechah&$orno&adodb_next_page" ?>' target='mainFrame' class="menu_princ"><b>8.Alerta Expedientes</a>	</td>
	</tr>
        <? 
        }
        ?>
        <tr>
  <td class="listado2">
    <span class="leidos2"><a href='<?php print $ruta_raiz; ?>/archivo/busqueda_central.php?<?=session_name()."=".session_id()."&krd=$krd&fechah=$fechah&$orno&adodb_next_page" ?>' target='mainFrame' class="menu_princ"><b>9.Busqueda Archivo Central</a>  </td>
</tr>
<tr>
  <td class="listado2">
    <span class="leidos2"><a href='<?php print $ruta_raiz; ?>/archivo/busqueda_Fondo_Gestion.php?<?=session_name()."=".session_id()."&krd=$krd&fechah=$fechah&$orno&adodb_next_page" ?>' target='mainFrame' class="menu_princ"><b>10.Busqueda Archivo Fondo Gestion</a>  </td>
</tr>
        <?
if($usua_perm_archi==3 or $usua_perm_archi==5){
?>
<tr>
  <td class="listado2">
    <span class="leidos2"><a href='insertar_central.php?<?=session_name()."=".session_id()."&krd=$krd&fechah=$fechah&$orno&adodb_next_page" ?>' target='mainFrame' class="menu_princ"><b>11.Insertar Archivo Central</a>  </td>
</tr>
        <? }
if($usua_perm_archi>=4){
?>
<tr>
  <td class="listado2">
    <span class="leidos2"><a href='insertar_Fondo_Gestion.php?<?=session_name()."=".session_id()."&krd=$krd&fechah=$fechah&$orno&adodb_next_page" ?>' target='mainFrame' class="menu_princ"><b>12.Insertar Archivo Fondo Gestion</a>  </td>
</tr>
<? }
?>
<!--
    /**
      * Modificado Supersolidaria 05-Dic-2006
      * Se agreg� la opci�n Administarci�n de Edificios.
      */
-->
<?
if($usua_perm_archi==2 or $usua_perm_archi==5){
?>
<tr>
  <td class="listado2">
    <span class="leidos2"><a href='adminEdificio.php?<?=session_name()."=".session_id()."&krd=$krd&fechah=$fechah&$orno&adodb_next_page" ?>' target='mainFrame' class="menu_princ"><b>13.Administraci&oacute;n de Edificios</a>  </td>
</tr>
<td class="listado2">
    <span class="leidos2"><a href='adminDepe.php?<?=session_name()."=".session_id()."&krd=$krd&fechah=$fechah&$orno&adodb_next_page" ?>' target='mainFrame' class="menu_princ"><b>14.Administracion de Relaci&oacute;n Dependencia-Edificios</a>  </td>
</tr>
<? }?>
</table>
</form>
</CENTER>
</html>