<?php
session_start();
/**
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @author Jairo Losada   <jlosada@gmail.com>
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright

SIIM2 Models are the data definition of SIIM2 Information System
Copyright (C) 2013 Infometrika Ltda.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

define('ADODB_ASSOC_CASE', 1);

$ruta_raiz = "..";
$ADODB_COUNTRECS = false;

include_once("$ruta_raiz/processConfig.php");
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$_SESSION["depeRadicaFormularioWeb"]=$depeRadicaFormularioWeb;  // Es radicado en la Dependencia 900
$_SESSION["usuaRecibeWeb"]=$usuaRecibeWeb; // Usuario que Recibe los Documentos Web
$_SESSION["secRadicaFormularioWeb"]=$secRadicaFormularioWeb; // Osea que usa la Secuencia sec_tp2_900
$_SESSION["idFormulario"] = sha1(microtime(true).mt_rand(10000,90000));
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

include('./funciones.php');
include('./formulario_sql.php');
include('./captcha/simple-php-captcha.php');
$_SESSION['captcha_formulario'] = captcha();

//TamaNo mAximo del todos los archivos en bytes 10MB = 10(MB)*1024(KB)*1024(B) =  10485760 bytes
$max_file_size  = 10485760;

if(!isset($isFacebook)){
	$isFacebook = 0;
}

if($logoEntidad){
  $log = "$ruta_raiz/bodega/$logoEntidad";
}else{
  $log = "$ruta_raiz/img/orfeo.png";
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<!doctype html>
<head>

<title>:: <?=$entidad_largo ?>:: Formulario PQRS</title>

<!-- Meta Tags -->
<meta http-equiv="Content-Type" content="tex<!doctype html>t/html; charset=UTF-8" />

<!--Deshabilitar modo de compatiblidad de Internet Explorer-->
<meta http-equiv="X-UA-Compatible" content="IE=edge"><!-- CSS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<link rel="stylesheet" href="css/structure2.css" type="text/css" />
<link rel="stylesheet" href="css/form.css" type="text/css" />
<link rel="stylesheet" href="css/fineuploader.css" type="text/css" />
<!-- JavaScript --> <script type="text/javascript"
	src="scripts/wufoo.js"></script> <!-- prototype -->
<script type="text/javascript" src="prototype.js"></script> <!-- jQuery -->
<script src="scripts/jquery.js"></script> <!-- FineUploader -->
<script	type="text/javascript" src="scripts/jquery.fineuploader-3.0.js"></script>
<!--funciones-->
<script type="text/javascript" src="ajax.js"></script>
<script>
    window.onload = createUploader;
</script>

</head>

<body id="public">

<div id="container">
<h1>&nbsp;</h1>

<form id="contactoOrfeo" class="wufoo topLabel" autocomplete="on"
	enctype="multipart/form-data" method="post" action="formulariotx.php"
	name="quejas">

<div class="info">
<center><img src='<?=$log?>' height='150' align=center></center>
<p><br> Apreciado ciudadano: </br>
&nbsp <br>Al diligenciar el formulario, tenga en cuenta lo siguiente: </br>

<br>En cualquier caso su requerimiento puede realizarse de manera
anónima o identificada. Si usted opta por presentar su comunicación en
forma anónima, no será posible que reciba de manera directa respuesta.
Los campos con (<font color="#FF0000">*</font>
) son obligatorios. </br>

</p>
</div>

<ul>
<table width="100%">
	<tr>
		<td>
		<li id="li_tipoSolicitud"><label class="desc" id="title_tipoSolicitud" for="tipoSolicitud">Tipo
		de petición <font color="#FF0000">*</font></label>
            <div>
                <select id="tipoSolicitud" name="tipoSolicitud"
                    class="" tabindex="1">
                    <option value="0" selected="selected">Seleccione</option>
                    <?=$tipo; ?>
                    <option value="1">Petici&oacute;n</option>
                    <option value="2">Queja</option>
                    <option value="3">Reclamo</option>
                    <option value="4">Sugerencia</option>
                    <option value="7">Felicitaciones</option>
                    <option value="8">Otros</option>
                </select> &nbsp;
            </div>
		</li>
		</td>
		<td>
		<li id="li_anonimo"><label class="desc" id="title_Anonimo" for="anonimo">¿Desea que su
		petición sea anónima?<font color="#FF0000">*</font></label>
		<div><select id="chkAnonimo" name="anonimo" tabindex="2"
            onChange="if (checkAnonimo()){document.getElementById('campo_asunto').focus();
            alert('Si usted opta por presentar su comunicación en forma anónima,no será posible que reciba de manera directa respuesta.')}else{document.getElementById('tipoDocumento').focus()};"
			>
			<option value=0 selected="selected">No</option>
			<option value=1>Sí</option>
		</select> &nbsp;</div>

		</li>
		</td></tr><tr><td>

<!-- <li id="li_tipoDocumento"><label class="desc" id="title_tipoDocumento"
    for="tipoDocumento">Tipo de documento <font color="#FF0000">*</font></label>
  <div><select id="tipoDocumento" name="tipoDocumento"
    class="field select maximun" tabindex="3">
    <option value="0" selected="selected">Seleccione</option>
    <option value="1">C&eacute;dula de ciudadan&iacute;a</option>
    <?php  //No cambiar el valor 5 de NIT porque se valida  formulariotx.php para guardarlo en empresa ?>
    <option value="5">NIT</option>
    <option value="3">C&eacute;dula extranjer&iacute;a</option>
    <option value="2">Tarjeta de identidad</option>
    <option value="4">Pasaporte</option>
  </select> &nbsp;</div>
  </li> -->
  <input type="hidden" id="tipoDocumento" name="tipoDocumento" value="1" >

  <!--
  <li id="li_numeroDocumento" class="   "><label class="desc" id="lbl_numid"
    for="campo_numid">N&uacute;mero de identificaci&oacute;n (solo
  n&uacute;meros o letras) <font color="#FF0000">*</font></label>
  <div>
  <?php
  //  include "searchAutocomplete3.php";
  ?>
  </div>
  </li> -->

	<li id="li_numeroDocumento" class="   "><label class="desc" id="lbl_numid"
		for="campo_numid">N&uacute;mero de identificaci&oacute;n (solo
	n&uacute;meros o letras) <font color="#FF0000">*</font></label>
	<div><input id="campo_numid" name="numid" type="text"
		class="field" value="" maxlength="11" tabindex="4"
		onkeypress="return alpha(event,numbers+letters)" /> &nbsp;</div>
	</li>
  </td></tr>
  <tr><td>
    <li id="li_nombre"><label class="desc" id="title_Nombre" for="campo_nombre"> Nombre
    del remitente o raz&oacute;n social <font color="#FF0000">*</font> </label>
    <div><input id="campo_nombre" name="nombre_remitente" type="text"
      class="field" value="" size="20" tabindex="5"
      onkeypressS="return alpha(event,letters);" /></div>
    </li>
  </td><td>
    <li id="li_apellido"><label for="campo_apellido" id="lbl_apellido" class="desc">Apellidos o tipo de
    empresa <font color="#FF0000">*</font></label>
    <div><input id="campo_apellido" name="apellidos_remitente" type="text"
      class="field" value="" size="20" tabindex="6"
      onkeypress="return alpha(event,letters);" /></div></span></li>
  </td></tr>
  <tr>
  <td>
    <li id="li_departamento" class="   "><label class="desc" id="lbl_deptop"
    for="label"> Departamento <font color="#FF0000">*</font> </label>
  <div><select id="slc_depto" name="depto" class="field"
    tabindex="8" onchange="trae_municipio()">
    <option value="0" selected="selected">Seleccione</option>
    <?=$depto ?>
  </select> &nbsp;<font color="#FF0000"></font></div>
  </li>
  </td>
  <td>

    <li id="li_pais" class="   "><label class="desc" id="lbl_pais"
      for="label"> País <font color="#FF0000">*</font> </label>
    <div><select id="slc_pais" name="pais" class="field"  tabindex="40"
       onchange="cambia_pais()">
      <?=$pais ?>
    </select> &nbsp;<font color="#FF0000"></font></div>
    </li>

	</td></tr>
	<tr>
	<td>
	<li id="li_municipio" class="   "><label class="desc" id="lbl_municipio"
		for="label2"> Municipio <font color="#FF0000">*</font> </label>
	<div id="div-contenidos"><select id="slc_municipio" name="muni"
		class="field" tabindex="9">
		<option value="0" selected="selected">Seleccione..</option>
	</select> &nbsp;<font color="#FF0000"></font></div>
	</li>
	</td>

	<td>
	<li id="li_grupo"><label class="desc" id="lbl_grupo"
		for="label2"> Dependencia Destino</label>
            <div id="div-contenidos">
                <?=$depselect?>
            </div>
	</li>
	</td>

  </tr>
  <tr>
  <td >
	<li id="li_direccion"> <label class="desc" id="lbl_direccion" for="campo_direccion">Direcci&oacute;n
	</label>
	<div><input id="campo_direccion" name="direccion" type="text" size="45"
		class="field" value="" maxlength="150" tabindex="10"
		onkeypress="return alpha(event,numbers+letters+signs+custom)" />
	&nbsp;</div>
	</li>
  </td><td>
	<li id="li_telefono"><label class="desc" id="lbl_telefono" for="campo_telefono">Tel&eacute;fono
	<font color="#FF0000">*</font></label>
	<div><input id="campo_telefono" name="telefono" type="text" size="35"
		class="field" value="" maxlength="80" tabindex="11"
		onkeypress="return alpha(event,numbers+alpha)" /> &nbsp;</div>
	</li>
  </td></tr>
  <tr><td>
	<li id="li_email"><label class="desc" id="lbl_email" for="campo_email"> E-mail <font
		color="#FF0000">*</font></label>
	<div><input id="campo_email" name="email" type="text" size="45"
		class="field" value="" maxlength="50" tabindex="12"></div>
	</li>
  </td><td>

	<li id="li_tipoPoblacion"> <label class="desc" id="title_tipoPoblacion" for="tipoPoblacion">Tipo
	de poblaci&oacute;n </label>
	<div><select id="tipoPoblacioen" name="tipoPoblacion"
		class="field" tabindex="39">
		<?=$temas;?>
			<option value="0" selected="selected">No aplica</option>
			<option value="1">Poblaci&oacute;n Desplazada</option>
			<option value="2">Mujer Gestante</option>
			<option value="3"class="field text small">Ni&ntilde;os, Ni&ntilde;as, Adolescentes</option>
			<option value="4">Veterano Fueza P&uacute;blica</option>
			<option value="5">Adulto Mayor</option>
	</select> &nbsp;<font color="#FF0000"></font></div>
	</li>
	&nbsp;
  </td></tr>
  <tr>
  <td colspan="2">
	<li id="li_asunto" class="   "><label class="desc" id="lbl_asunto"
		for="campo_asunto">Tema de su petición<font color="#FF0000">*</font></label>
	<div><input id="campo_asunto" name="asunto" type="text" size="85"
		class="field" value="" maxlength="80" tabindex="15" />
	&nbsp;</div>
	</li>
	</td>
    </tr>

    <tr>
	<td colspan="2">
        <li id="li_comentario">
            <label class="desc" id="lbl_comentario"
            for="campo_comentario">Comentario<font color="#FF0000">*</font></label>
            <div class="info">
                <p>Para dar mayor agilidad a su solicitud, por favor realizar la
                descripción de los hechos haciendo referencia al momento, lugar,
                participantes y móviles entre otros elementos que considere que pueden
                despejar cualquier duda sobre las circunstancias.</p>
            </div>
            <div><textarea id="campo_comentario" name="comentario"
                class="field" rows="6" cols="100" tabindex="16"
                onkeyup="countChar(this)" defaultValue="Escriba ac&aacute; ..."></textarea>
            <input type="hidden" id="adjuntosSubidos" name="adjuntosSubidos"
                value="" /> &nbsp;</div>
            <div align="right" id="charNum"></div>
        </li>
    </td>
  </tr>
  <tr>
  <td colspan="2">
	<li id="li_upload">
	<div id="filelimit-fine-uploader" tabindex="17"></div>
	<div id="availabeForUpload"></div>
	&nbsp;
	</li>
	</td></tr>
  <tr>
  <td >
	<li id="li_imagenVerificacion"><label class="desc" id="lbl_captcha" for="campo_captcha">Imagen de
	verificaci&oacute;n  <font color="#FF0000">*</font></label>

	<div>

	<input id="campo_captcha" name="captcha" type="text"
		 value="" maxlength="5" tabindex="20"
		onkeypress="return alpha(event,numbers+letters)"
		alt="Digite las letras y n&uacute;meros de la im&aacute;gen" /> &nbsp;
	<p>
	</td><td>
	<?php
	echo '<img id="imgcaptcha" src="' . $_SESSION['captcha_formulario']['image_src'] . '" alt="CAPTCHA" /><br>';
	echo '<a href="#" onClick="return reloadImg(\'imgcaptcha\');">Cambiar imagen<a>'
  ?></p>
    <input type="hidden" name="pqrsFacebook" value="<?=$isFacebook?>" />
    <input type="hidden" name="idFormulario" value="<?=$_SESSION["idFormulario"]?>" />
	</div>
	</li>
 </td>
	</tr>
 <tr><td colspan="2">
	<li id="li_botones" class="buttons"><center><input id="saveForm" type="submit" value="Enviar"
		onclick="return valida_form();" tabindex="21" />
		<input name="button" type="button"
		id="button" onclick="window.close();" value="Cancelar" tabindex="22" /><center></li>
  </td>
  </tr>
</table>
	</ul>

</form>

</div>
<!--container-->

</body>
</html>
