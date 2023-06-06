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
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once("$ruta_raiz/formularioWeb/solicitudes_sql.php");

$db = new ConnectionHandler($ruta_raiz);
$tipos_documentos = tipos_documentos($db);
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

define('ADODB_ASSOC_CASE', 1);
$url = "http://10.161.91.191/SuperCor-WS/SuperCorWS?wsdl";

$ADODB_COUNTRECS = false;
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
$_SESSION["depeRadicaFormularioWeb"]=$depeRadicaFormularioWeb;  // Es radicado en la Dependencia 900
$_SESSION["usuaRecibeWeb"]=$usuaRecibeWeb; // Usuario que Recibe los Documentos Web
$_SESSION["secRadicaFormularioWeb"]=$secRadicaFormularioWeb; // Osea que usa la Secuencia sec_tp2_900
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//Revisar si se envio el formulario
if(isset($numeroRadicado)){
	$fechah = date("dmy") . "_" . time("hms");
	$usua_nuevo=3;
	if ($numeroRadicado)
	{
		$numeroRadicado = str_replace("_","",$numeroRadicado);
		$numeroRadicado = str_replace(".","",$numeroRadicado);
		$numeroRadicado = str_replace(",","",$numeroRadicado);
		$numeroRadicado = str_replace(" ","",$numeroRadicado);
		include "$ruta_raiz/include/tx/ConsultaRad.php";
		$ConsultaRad = new ConsultaRad($db);
        $idWeb = $ConsultaRad->idRadicadoConCodigoVerificacion($numeroRadicado, $codigoverificacion);

        $superargo = false;
        $supercor = false;
        $pqr = false;

		if($numeroRadicado==$idWeb and substr($numeroRadicado,-1)=='5' && (strcasecmp ($captcha ,$_SESSION['captcha_consulta']['code'] ) == 0))
		{
			$ValidacionWeb="Si";
            $idRadicado = $idWeb;
            $superargo = true;
        }
 /*       
        if(!$superargo)
        {
            $db = ADONewConnection('mssqlnative');
            $db->connect('CYDCL-P-PQR.supersalud.local','steven.hernandez','Super.2019','GestionPqrd');
            //var_dump($db); exit;
            $numeroRadicado = trim(strtoupper($numeroRadicado));
            $data = $db->getAll("SELECT * FROM PQRSNS.PQR INNER JOIN
                                    PQRSNS.PQRDatosAfectado ON PQR.IdPQR = PQRDatosAfectado.IdPQR
                                WHERE (NroRadicacionSistemaExterno = '$numeroRadicado' OR NroRadicacionInicial = '$numeroRadicado' OR 'NroRadicacion' = '$numeroRadicado') AND PQRDatosAfectado.Identificacion = '$id'");
            $pqr = !!$data;
        }
*/
/*
        if(!$superargo && !$pqr)
        {
            $client = new SoapClient($url, array("trace" => 1, "exception" => 0));
            $result = null;
            $t = substr($numeroRadicado, 0, 1);
            $tipo = $t == '2' ? 'ConsultaNumeroRadSalida' : 'ConsultaNumeroRadicacion';

            $p = [
                'validador' => "BPM",
                'operador' => "9999",
                'rad_Numero' => $numeroRadicado,
                'dependencia' => ''
            ];

            $result = $client->__soapCall($tipo, ['parameters' => $p]);

            if($result->return->codigoAccion === ' ') {
                $supercor = true;
            }
        }
*/		
 /*       
        if(!$superargo && !$supercor && !$pqr) 
        {
			$ValidacionWeb="No";
			$mensaje = "El numero de radicado digitado no existe, el codigo de verificacion no corresponde o esta mal escrito o la imagen de verificacion no fue bien digitada.  Por favor corrijalo e intente de nuevo.";
			echo "<center><font color=red class=tpar><font color=red size=3>$mensaje</font></font>";
			echo "<script>alert('$mensaje');</script>";
		}
*/		
    }

    if($superargo)
    {
        $krd = "usWeb";
        $datosEnvio = "$fechah&".session_name()."=".trim(session_id())."&ard=$krd";
        $ulrPrincipal = "Location: principal.php?fechah=$datosEnvio&pasar=no&verdatos=no&idRadicado=$numeroRadicado&estadosTot=".md5(date('Ymd'));
        header($ulrPrincipal);
    }

    if($supercor)
    {
        $ulrSupercor = "Location: supercor.php?fechah=$datosEnvio&pasar=no&verdatos=no&idRadicado=$numeroRadicado&estadosTot=".md5(date('Ymd'));
        header($ulrSupercor);
    }

    if($pqr)
    {
        $ulrPqr = "Location: pqr.php?fechah=$datosEnvio&pasar=no&verdatos=no&idRadicado=$numeroRadicado&id=$id&tipo=$tipo_identificacion&estadosTot=".md5(date('Ymd'));
        header($ulrPqr);
    }

	return ;
}

?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">
		<title>Gestión Documental y Trámites</title>
		<style>
			.title {
				text-align: center;
			}

			.fecha {
				text-align: right;
			}

			.section-h {
				padding-bottom: 10px;
				border-bottom:1px solid #ccc;
			}
		</style>
	</head>

	<body>
		<div class="container">
			<div class="row justify-content-between" style="margin-top:10px;">
				<div class="col-4" style="text-align:left;">
					<a href="https://www.crautonoma.gov.co/" target="_blank">
						<img src="./images/CRA logo.png" height=100 style="margin:0;" align="center">
					</a>
				</div>
				<div class="col-4" style="text-align:right;">
					<a href="https://id.presidencia.gov.co/deinteres/index.html" target="_blank">
						<img src="./images/Gobierno logo.png" height=57 style="margin-top:28px;" align="center">
					</a>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<hr>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12" style="height:100px;">
				</div>
			</div>
            <form id="consultaweb" action= "<?=$_SERVER['PHP_SELF']?>" name="consultaweb" enctype="multipart/form-data" method="post" autocomplete="on">
                <div class="row justify-content-md-center">
					<div class="col-md-6" style="text-align: center">
						<p>A través de este servicio usted puede consultar el estado de su PQRS.</p>
					</div>
				</div>
				<div class="row justify-content-md-center">
					<div class="col-md-auto">
						<h3>CONSULTA DE RADICADOS</h3>
					</div>
				</div>
				<div class="row justify-content-md-center">
					<div class="col-md-6">
						<div class="form-group">
							<label for="numeroRadicado" class="form-label">
                            <i id="3" class="fa fa-question-circle-o" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="El radicado se encuentra en el documento que le llegó a su correo electrónico o fue entregado en el momento de la radicación. Ejemplo: 2021 0000 00 00 00000"></i>
                            Nº de radicado <span class="text text-danger">*</span></label>
                            <input class="form-control" id="numeroRadicado" aria-describedby="radicado" name="numeroRadicado" type="text" value="" maxlength="15" tabindex="1" autocomplete="off">
                            <div class="invalid-feedback">
                                Ingrese el número de radicación o NURC
                            </div>
						</div>
					</div>
				</div>
				<div class="row justify-content-md-center">
                    <div class="col-md-6 form-group">
						<label for="tipo_identificacion" class="form-label">
                            <i id="2" class="fa fa-question-circle-o" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Tipo de documento de identificación de la persona afectada."></i>
                            Tipo de identificación <span class="text text-danger">*</span>
                        </label>
						<select name="tipo_identificacion" id="tipo_identificacion" title="Seleccionar" data-live-search="true" data-size="5" class="form-control">
                            <option value="CC">Cédula de ciudadania</option>
                            <option value="CE">Cédula de extranjeria</option>
                            <option value="MS">Menor sin identificación</option>
                            <option value="NIT">NIT</option>
                            <option value="PA">Pasaporte</option>
                            <option value="PE">Permiso especial de permanencia</option>
                            <option value="RC">Registro civil</option>
                            <option value="TI">Tarjeta de identidad</option>
						</select>
					</div>
                </div>
				<div class="row justify-content-md-center">
					<div class="col-md-6 form-group" data-natural data-juridico>
						<label for="id" class="form-label">
                            <i id="3" class="fa fa-question-circle-o" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Si el número de identificación contiene letras, guiones y números debe incluirlo.  Si el tipo de documento es de un menor sin identificación, ingrese el número de documento del tutor."></i>
                            Número de identificación <span class="text text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="id" name="id" autocomplete="off">
                        <div class="invalid-feedback">
                            Ingrese el número de identificación del afectado
                        </div>
					</div>
                </div>
                <div class="row justify-content-md-center">
                    <div class="col-md-6">
                        <small class="text text-mutted">(<span class="text text-danger">*</span>) Campo obligatorio para realizar la consulta.</small>
                    </div>
                </div>
				<div class="row justify-content-md-center">
					<div class="col-md-6">
                        <div class="row">
						    <!--<div style="margin: 15px" class="g-recaptcha" data-sitekey="6LcRhc4ZAAAAAI6Pb3nMgNDZiNKA2Apww8axr42X"></div>-->
                        </div>
					</div>
				</div>
				<div class="row justify-content-md-center">
					<div class="col-md-6">
						<hr>
					</div>
				</div>
				<div class="row justify-content-md-center">
					<div class="col-md-6">
						<button id="saveForm" type="submit" class="btn btn-primary">Entrar</button>
					</div>
				</div>
			</form>
        </div>
		<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script><!-- JavaScript Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
		<!--<script src="https://www.google.com/recaptcha/api.js" async defer></script>-->
		<script>
			$(function()
			{
                $('[data-toggle="tooltip"]').tooltip()

				$('#consultaweb').on('submit', function(e) {
                    var errores = false;
                    $('#numeroRadicado').removeClass('is-invalid');
                    $('#tipo_identificacion').removeClass('is-invalid');
                    $('#tipo_identificacion').closest('.form-control').removeClass('is-invalid');
                    $('#id').removeClass('is-invalid');

                    if($('#numeroRadicado').val() == '')
                    {
                        errores = true;
                        $('#numeroRadicado').addClass('is-invalid');
                    }
                    if($('#tipo_identificacion').val() == '')
                    {
                        errores = true;
                        $('#tipo_identificacion').addClass('is-invalid');
                    }
                    if($('#id').val() == '')
                    {
                        errores = true;
                        $('#id').addClass('is-invalid');
                    }
                    
                    if(grecaptcha.getResponse() == '')
                    {
                        $('.g-recaptcha').css('border', '1px solid #f00');
                    } else {
                        $('.g-recaptcha').css('border', 'none');
                    }
                    
					if(grecaptcha.getResponse() == '' || errores)
						e.preventDefault();
				});
			});
		</script>
	</body>
</html>
