<?php
session_start();
define('ADODB_ASSOC_CASE', 2);
/**
  * Se añadio compatibilidad con variables globales en Off
  * @autor Infometrika 2009-05
  * @licencia GNU/GPl V3
  */
$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
 
if($_GET["borrcarpeta"]) $borrcarpeta=$_GET["borrcarpeta"];
if($_GET["BorrarCarp"]) $BorrarCarp=$_GET["BorrarCarp"];

$ruta_raiz = ".";

if(!$_SESSION['dependencia']) include "$ruta_raiz/rec_session.php";
$verrad = "";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);

$descripcionSql = $db->conn->concat('NOMB_CARP',"' - '", 'DESC_CARP');
$isql ="select $descripcionSql , CODI_CARP from carpeta_per 
		where usua_codi=$codusuario and depe_codi=$dependencia order by codi_carp  ";
$rs = $db->conn->Execute($isql);
?>
<html>
<head>
<title>Eliminar Carpetas</title>
<?php
 include_once "htmlheader.inc.php"; 

?>
</head>
<body>
<form name='form1' class="form-control" method='GET' action='eliminar_carpeta.php?=<?=session_name."=".session_id?>'>
<div class="panel panel-primary">
  <div class="panel-heading">
	  ELIMINAR CARPETAS PERSONALES
	</div>
<BR>

<?php

if($BorrarCarp)
{
	$isql=	"select count(*) AS Num from radicado
			where carp_per=1 and carp_codi=$borrcarpeta and
			 	  radi_depe_actu=$dependencia and radi_usua_actu=$codusuario ";
			 	  //$db->conn->debug = true;
	$rs=$db->conn->Execute($isql);
	
	$numerot = $rs->fields(0);
	
	if($numerot==0){
		$isql = "delete from  carpeta_per where depe_codi=$dependencia and usua_codi=$codusuario and codi_carp=$borrcarpeta ";
		$rs=$db->conn->query($isql);
		if($rs==-1) die("<div class='alert alert-danger'>No se ha podido Borrar la carpeta</div>");
		?>
		<div class="alert alert-success">Se ha borrado la Carpeta con &eacute;xito</div>
	<?
	}else{
	 
	 die( "<div class='alert alert-danger'>La carpeta no se ha podido borrar por que contiene (<?=$numerot?>) documentos,<br> La carpeta debe estar vacia para poder ser borrada </div> ");
}	 
}
?>
		<div class="alert alert-warning">
		  Solo se pueden eliminar las carpertas que se encuentren vacias
		</div> 
		 <div class='panel-body'>
		  Usted tiene estas carpetas vacias:
<?
$descCarp = "c.nomb_carp||' ('||c.desc_carp||')'";
$isql ="  SELECT $descCarp , c.CODI_CARP from carpeta_per c
  where id not in 
   (SELECT c.id from carpeta_per c 
    JOIN radicado r ON (c.depe_codi=r.radi_depe_actu and c.usua_codi=r.radi_usua_actu and c.codi_carp=r.carp_codi and r.carp_per=1)
    WHERE c.usua_codi=$codusuario and c.depe_codi=$dependencia 
		GROUP BY c.id
	)
	AND c.usua_codi=$codusuario and c.depe_codi=$dependencia 
	ORDER by codi_carp
	";
	//$db->conn->debug = true;
	$rs = $db->conn->Execute($isql);
	print $rs->GetMenu2("borrcarpeta", "$borrcarpeta", "0:-- Seleccione la carpeta Personal--", false,"","onChange='procEst(formulario,18,$i )' class='form-control'"); 
	$row = array();
?>
  </div>
	<div class="panel-footer"><input class="btn btn-danger" type=submit name='BorrarCarp' Value= 'Borrar Carpeta' class='botones'></div>
</div>	


</div>
</form>
</body>
</html>
