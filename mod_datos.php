<?

session_start();
error_reporting(7); 
$ruta_raiz = ".";
include_once "./include/db/ConnectionHandler.php";


if (!$db)
	$db = new ConnectionHandler(".");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);		

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

#Inicializo el krd
$krd            = $_SESSION["krd"];

#Traigo todos los datos segun el documento el krd
include 'cargodatosusuario.php';


#Recupero todos los datos del POST para Mostrarlos a tiempo
 if ($_POST["usua_doc"]) $usua_doc = $_POST["usua_doc"];
 if ($_POST["usua_dia"]) $usua_dia = $_POST["usua_dia"];
 if ($_POST["usua_mes"]) $usua_mes = $_POST["usua_mes"];
 if ($_POST["usua_ano"]) $usua_ano = $_POST["usua_ano"];
 if ($_POST["usua_email"]) $usua_email = $_POST["usua_email"];
 if ($_POST["usua_piso"]) $usua_piso = $_POST["usua_piso"];
 if ($_POST["usua_ext"]) $usua_ext = $_POST["usua_ext"];
 if ($_POST["usua_at"]) $usua_at = $_POST["usua_at"];


#Debug
#$db->conn->debug = true; 
?>
<?php include_once "htmlheader.inc.php"; ?>
<head>
</head>
<body  onload="SetFocus();">
<form enctype="multipart/form-data"  name=datos_personales action="" class="form-smart" method=post> 
  <table WIDTH=98% align="center"  class="table table-bordered" >
     <tr> <td>
        <table width="90%">
          <tr align="center"> 
            <td ><b>La informaci&oacute;n aqu&iacute; 
              reportada se considera oficial y es indispensable para 
              iniciar el acceso al Sistema de Gesti&oacute;n en la Entidad  <?=$entidad?></b></td>
          </tr>
        </table>
      </td> 
  </tr>
</table>
  <table  WIDTH=90% align="center"  class="table table-bordered">
    <!--DWLayoutTable--> 
    <tr> 
      <td height="50" align="right"  width="24%" >
        Documento C.C:<br>
        (No incluir puntos, comas o caracteres especiales) </td>
      <td  width="17%" > 
	  <?
	    if ($info) $info = "false"; else $info="true";
	  ?>
        <input type=text name=usua_doc value='<?=TRIM($usua_doc)?>'  size=20 maxlength="20" readonly="<?=$info?>"></td>
      <td  align="right"   width="15%">Fecha 
        Nacimiento<br>
        (dd-mm-aaaa)<?=$usua_nacim?> </td>
      <td  width="24%" ><?
		    $ano_fin = date("Y");
			$ano_fin++;
			$ano_fin = $ano_fin - 10;
			$ano_ini = $ano_fin - 80;
		?><select name=usua_dia class="select">
          <option value=0>Dia</option>				
          <?
		    for ($i=1 ; $i<=31 ;$i++)
			{
			   if ($i==$usua_dia) {$datoss=" selected ";} else {$datoss = "";}			
			   echo "<option value=$i  $datoss>$i</option>";
			}
		?>
        </select><select name=usua_mes class="select">
          <option value=0>Mes</option>
		   <? if ($usua_mes==1) {$datoss=" selected ";} else {$datoss = "";}				 ?>
            <option value=1  '<?=$datoss?>'>Ene</option>
		   <? if ($usua_mes==2) {$datoss=" selected ";} else {$datoss = "";}				 ?>	
            <option value=2  '<?=$datoss?>'>Feb</option>
		   <? if ($usua_mes==3) {$datoss=" selected ";} else {$datoss = "";}				 ?>				
            <option value=3  '<?=$datoss?>'>Mar</option>
		   <? if ($usua_mes==4) {$datoss=" selected ";} else {$datoss = "";}				 ?>				
            <option value=4  '<?=$datoss?>'>Abr</option>
		   <? if ($usua_mes==5) {$datoss=" selected ";} else {$datoss = "";}				 ?>				
            <option value=5  '<?=$datoss?>'>May</option>
		   <? if ($usua_mes==6) {$datoss=" selected ";} else {$datoss = "";}				 ?>				
            <option value=6  '<?=$datoss?>'>Jun</option>
		   <? if ($usua_mes==7) {$datoss=" selected ";} else {$datoss = "";}				 ?>				
            <option value=7  '<?=$datoss?>'>Jul</option>
		   <? if ($usua_mes==8) {$datoss=" selected ";} else {$datoss = "";}				 ?>				
            <option value=8  '<?=$datoss?>'>Ago</option>
		   <? if ($usua_mes==9) {$datoss=" selected ";} else {$datoss = "";}				 ?>				
            <option value=9  '<?=$datoss?>'>Sep</option>
		   <? if ($usua_mes==10) {$datoss=" selected ";} else {$datoss = "";}				 ?>				
            <option value=10  '<?=$datoss?>'>Oct</option>
		   <? if ($usua_mes==11) {$datoss=" selected ";} else {$datoss = "";}				 ?>				
            <option value=11  '<?=$datoss?>'>Nov</option>
		   <? if ($usua_mes==12) {$datoss=" selected ";} else {$datoss = "";}				 ?>				
            <option value=12  '<?=$datoss?>'>Dic</option>
        </select>
		<select name=usua_ano class="select">
		
          <option value=0>A&ntilde;o</option>
          <?
		    for ($i=1 ; $i<=80 ;$i++)
			{
			   $ano = ($ano_fin - $i);
			   if ($ano==$usua_ano) {$datoss=" selected ";} else {$datoss = "";}
			   echo "<option value='$ano' $datoss>$ano</option>";
			}
		?>
        </select>
 </td>
      <td   align="right" width="11%" >
        Extension  </td>
      <td  width="9%"> 
        <input type=text name=usua_ext value='<?=$usua_ext?>'  size=5 maxlength="4"> 
      </td>
    </tr>
    <tr> 
      <td align="right"  height="41" width="24%" >Correo 
        Electronico<br>
       </td>
      <td  width="27%" > 
        <input type=text name=usua_email  value='<?=trim($usua_email)?>' maxlength="70"></td>
      <td align="right"  width="15%">Identificacion 
        Equipo<br>
        (ej, at-999)</td>
      <td  width="24%" > <span class="info"></span> 
        <input type=text name=usua_at  value='<?=$usua_at?>'  size=15 maxlength="35">
      </td>
      <td   align="right" width="11%"> 
        Piso</td>
      <td  width="9%"> 
        <input type=text name=usua_piso  value='<?=$usua_piso?>'  size=5 maxlength="2"> 
        &nbsp; </td>
    </tr>
<?
$bodega_firmas=$ruta_raiz.'/bodega/firmas/';
$uriFile1=$bodega_firmas.$usua_doc;
$uriFile2=$bodega_firmas.$usua_doc.'.p12';
?>

    <tr> 
      <!--Subir firma mecanica-->
      <?if (isset($_SESSION["usua_perm_firma"])){?>

      <td align="right"  width="15%">Identificacion Archivo Imagen de Firma</td>
      <td  width="24%" > <span class="info"></span> 
        <input type=file name=file1  >
	<?=file_exists($uriFile1)?'<span style="color: red">Ya existe una imagen de firma cargada en el sistema</span>':'';?>
      </td>
     <!--Fin subir firma mecanica-->

      <!--Subir firma digital-->

      <td   align="right" width="11%"> 
        Archivo de Firma Digital .p12</td>
      <td  width="9%"> 
        <input type=file name=file2  > 
	<?=file_exists($uriFile2)?'<span style="color: red">Ya existe una firma digital cargada en el sistema</span>':'';?>
        &nbsp; </td>
     <?}?>
     <!--Fin subir firma digital-->
    </tr>
   </table>   

    
  <table class="table table-bordered">
    <tr align="center"> 
      <td> 
        <input type=submit name=grabar_datos_per class=botones_largo Value="Grabar Datos Personales">
      </td>
      </tr>
    </table>
    
</form>

<?
$usua_email=$_GET['usua_email']=$_POST["usua_email"];
$usua_at=$GET['usua_email']=$_POST["usua_at"];
$grabar_datos_per = $_POST["grabar_datos_per"];
#echo "$usua_doc and $grabar_datos_per";
if($usua_doc and $grabar_datos_per) 
	{
	#compruebo si el check llega vacio, coloco zero de lo contrario coloco 1
	if (isset($_POST["USUA_PERM_FIRMA"])){
	 #Esta chekeado
	 $record["USUA_PERM_FIRMA"]=1;
		}else{ 
	 #NO esta chekeado
     $record["USUA_PERM_FIRMA"]=0;
	 }
#Obtengo y guardo la fecha
$usua_ano = $_POST["usua_ano"];
$usua_mes = $_POST["usua_mes"];
$usua_dia = $_POST["usua_dia"];

		$fechaNacimiento = "".$usua_ano."-".substr("0$usua_mes",-2)."-".substr("0$usua_dia",-2)."";
		$record["USUA_DOC"]="$usua_doc";
		if(trim($usua_email)) $record["USUA_EMAIL"]="'".$usua_email."'";
		$usua_dia = substr("0$usua_dia",-2);
		
		$record["USUA_NACIM"]=$db->conn->DBDate($fechaNacimiento);
		if(trim($usua_piso)) $record["USUA_PISO"]="'".$usua_piso."'";
		if(trim($usua_ext)) $record["USUA_EXT"]="'".$usua_ext."'";				
		if(trim($usua_at)) $record["USUA_AT"]="'".$usua_at."'";
		$record1["USUA_LOGIN"]="'".$krd."'";
		$db->update("USUARIO",$record, $record1);
	 $db->conn->CommitTrans();
	
	#Ddespues de insertado el registro y con el usua_codi, actualizo en la base de datos la firma digital

#echo "subi firmas"; exit;

	 include 'subirfirmas.php';



		?>
		
		<TABLE BORDER=0 WIDTH=100%>
		<TR><TD class="etextomenu">
		 <center><B>Los datos han sido guardados, Por favor ingrese de modo normal al sistema.</center>
		 </TD></TR>
		 </TABLE>
		<? 
		
	}ELSE
	{
	   ?>
		<TABLE BORDER=0 WIDTH=100%>
		<TR><TD class="listado2">
		 <center><B><span class="alarmas">Todos los datos deben ser grabados correctamente.  De lo contrario no podra seguir navegando por el sistema.</span></center>
		 </TD></TR>
		 </TABLE>
       <?	
	}
?>
</body>
