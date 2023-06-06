<?php
/*  Administrador de Esp/Entidades (Bodega_Empresas).
*	Creado por: Ing. Mario Manotas Duran.
*	Permite la administraci?n de esp. La inserci?n y modificaci?n hace uso de la funcion	
*/
	$ruta_raiz = "../..";
	session_start();
	if(!$dependencia or !$tpDepeRad) include "$ruta_raiz/rec_session.php";
	if($_SESSION['usua_admin_sistema'] !=1 ) die(include "$ruta_raiz/errorAcceso.php");
	$phpsession = session_name()."=".session_id();
?>

<html>
<head>
<title>Documento sin t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../../estilos/orfeo.css">
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
</head>
<body>
<form name='frmMnuEsp' action='../formAdministracion.php?<?=session_name()."=".session_id()?>&krd=<?=$krd?>' method="post">

<div class="col-sm-12">
     <!-- widget grid -->
     <section id="widget-grid">
       <!-- row -->
       <div class="row">
         <!-- NEW WIDGET START -->
         <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
           <!-- Widget ID (each widget will need unique ID)-->
           <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">
 
             <header>
               <h2>
                 Administrador de tablas sencillas
               </h2>
             </header>
 
             <!-- widget div-->
             <div>
               <!-- widget content -->
               <div class="widget-body no-padding">


  <table class='table table-bordered table-striped' width="32%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
  <tr bordercolor="#FFFFFF">
    <td align="center" class="listado2" width="98%"><a href='crearEsp.php?usModo=1&<?=$phpsession ?>&krd=<?=$krd?>' class="vinculos" target='mainFrame'>1. Crear Empresa E.S.P.</a></td>
  </tr>
  <tr bordercolor="#FFFFFF">
    <td align="center" class="listado2" width="98%"><a href='editar.php?usModo=2&<?=$phpsession ?>&krd=<?=$krd?>' class="vinculos" target='mainFrame'>2. Editar Empresa E.S.P.</a></td>
  </tr>
  <tr bordercolor="#FFFFFF">
    <td align="center" class="listado2" width="98%"><a href='consultarEsp.php?usModo=3&<?=$phpsession ?>&krd=<?=$krd?>' class="vinculos" target='mainFrame'>3. Consultar Empresa E.S.P.</a></td>
  </tr>
  <tr bordercolor="#FFFFFF">
  	<td align="center" class="listado2">
	<center><input align="middle" class="botones" type="submit" name="Submit" value="Cerrar"></center>
	</td> </tr>
</table>

		 </div>
             </div>
           </div>
         </article>
       </div>
     </section>
   </div>



</form>
</body>
</html>
