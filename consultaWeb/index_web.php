<?php
session_start();
/**
 * Modulo de consulta Web para atencion a Ciudadanos.
 * @autor Sebastian Ortiz
 * @fecha 2012/06
 *
 */
$ruta_raiz = "..";
include ("$ruta_raiz/processConfig.php");
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

define('ADODB_ASSOC_CASE', 1);

$ADODB_COUNTRECS = false;
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
$_SESSION["depeRadicaFormularioWeb"]=$depeRadicaFormularioWeb;  // Es radicado en la Dependencia 900
$_SESSION["usuaRecibeWeb"]=$usuaRecibeWeb; // Usuario que Recibe los Documentos Web
$_SESSION["secRadicaFormularioWeb"]=$secRadicaFormularioWeb; // Osea que usa la Secuencia sec_tp2_900
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//Revisar si se envio el formulario
if(isset($numeroRadicado) && isset($codigoverificacion)){
	$fechah = date("dmy") . "_" . time("hms");
	$usua_nuevo=3;
	if ($numeroRadicado)
	{
		$numeroRadicado = str_replace("-","",$numeroRadicado);
		$numeroRadicado = str_replace("_","",$numeroRadicado);
		$numeroRadicado = str_replace(".","",$numeroRadicado);
		$numeroRadicado = str_replace(",","",$numeroRadicado);
		$numeroRadicado = str_replace(" ","",$numeroRadicado);
		include "$ruta_raiz/include/tx/ConsultaRad.php";
		$ConsultaRad = new ConsultaRad($db);
		$idWeb = $ConsultaRad->idRadicadoConCodigoVerificacion($numeroRadicado, $codigoverificacion);
		if($numeroRadicado==$idWeb and substr($numeroRadicado,-1)=='2' && (strcasecmp ($captcha ,$_SESSION['captcha_consulta']['code'] ) == 0))
		{
			$ValidacionWeb="Si";
			$idRadicado = $idWeb;
		}
		else
		{
			$ValidacionWeb="No";
			$mensaje = "El numero de radicado digitado no existe, el codigo de verificacion no corresponde o esta mal escrito o la imagen de verificacion no fue bien digitada.  Por favor corrijalo e intente de nuevo.";
			echo "<center><font color=red class=tpar><font color=red size=3>$mensaje</font></font>";
			echo "<script>alert('$mensaje');</script>";
		}
	}
	$krd = "usWeb";
	$datosEnvio = "$fechah&".session_name()."=".trim(session_id())."&ard=$krd";
	$ulrPrincipal = "Location: principal.php?fechah=$datosEnvio&pasar=no&verdatos=no&idRadicado=$numeroRadicado&estadosTot=".md5(date('Ymd'));
	header($ulrPrincipal);
	return ;
}


?>

<!DOCTYPE html>
<!--[if IE 8]> <html class="no-js lt-ie9" lang="en" > <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"><!--<![endif]--><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="SIIM2">
<meta name="keywords" content="">
<link rel="shortcut icon" href="../img/favicon.png">

<title>..:: <?=$entidad?>  ::..</title>
<!-- Bootstrap core CSS -->
<link href="../estilos/bootstrap.min.css" rel="stylesheet">
<!-- CSS -->
<link rel="stylesheet" href="css/structure2.css" type="text/css" />
<link rel="stylesheet" href="css/form.css" type="text/css" />

<!-- Custom styles for this template -->
<link href="../estilos/login.css" rel="stylesheet">
<!-- JavaScript -->
<script type="text/javascript" src="js/wufoo.js"></script>
<!-- prototype -->
<!--funciones-->
<script type="text/javascript" src="js/orfeo.js"></script>

</head>
<body>
<!-- start Login box -->
<div class="container" id="login-block">
<div class="row">
<div class="col-sm-6 col-md-4 col-lg-12">
<div class="login-box clearfix animated flipInY">
<h3 class="animated bounceInDown">CONSULTA DE RADICADOS</h3>
<hr>
<div class="login-form">

<div class="alert alert-error hide">
<button type="button" class="close" data-dismiss="alert">×</button>
<h4>Error!</h4>
Los datos suministrados no son correctos.
</div>

<form id="consultaweb" action= "<?=$_SERVER['PHP_SELF']?>" name="consultaweb" enctype="multipart/form-data" method="post" autocomplete="on">
<input id="numeroRadicado" placeholder="N&uacute;mero de Radicado (s&oacute;lo n&uacute;meros)" required="" name="numeroRadicado" type="text" class="field text small" value="" maxlength="15" tabindex="1" onkeypress="return alpha(event,numbers)" />
<input id="codigoverificacion"  placeholder="C&oacute;digo verificaci&oacute;n radicado" required="" name="codigoverificacion" type="text" class="field text small" value="" maxlength="5"	tabindex="2" onkeypress="return alpha(event,numbers+letters)" />
<div style="height:20px;"></div>
<div class="g-recaptcha" data-sitekey="6LcRhc4ZAAAAAI6Pb3nMgNDZiNKA2Apww8axr42X"></div>

<button id="saveForm" type="submit" class="btn btn-login" onclick="return validar_formulario();">Entrar</button>
</form>

</div>
</div>
</div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
	$(function()
	{
		$('#consultaweb').on('submit', function(e) {
			if(grecaptcha.getResponse() == '')
				e.preventDefault();
		});
	});
</script>
</body>
</html>
