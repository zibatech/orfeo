<?php
	session_start();
	define('ADODB_ASSOC_CASE', 1);
	$ruta_raiz = "..";
	$ADODB_COUNTRECS = false;
	
	include_once("$ruta_raiz/processConfig.php");
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
    include_once("$ruta_raiz/formularioWeb/solicitudes_sql.php");
	
	$db = new ConnectionHandler($ruta_raiz);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

	//departamentos($db) de solicitudes_sql.php
	$paises = paises($db);
	$departamentos = departamentos($db);
	$municipios = ciudades_tx_all($db);
	$tipos_entidades = tipos_entidades($db);
	$tipos_documentos = tipos_documentos($db);
	$poblaciones_especiales = [
		'Desplazado',
		'Habitante de calle',
		'Persona con discapacidad',
		'Población carcelaria (Presos)',
		'Trabajador (a) sexual',
		'Violencia de género',
		'Violencia conflicto armado',
		'No Aplica'
	];

	$grupos_etnicos = [
		'Afrocolombiano o Afrodescendiente',
		'Indígena',
		'Mulato',
		'Negro',
		'Palanquero (De San Basilio)',
		'Raizal (Del Archipiélago de San Andrés y Providencia)',
		'ROM- Gitano',
		'No Aplica',
	];
?>

<?php include ('header.php') ?>
	<div class="container">
		<div class="row justify-content-between" style="margin-top:10px;">
			<div class="col-4" style="text-align:left;">
				<a href="http://www.supersalud.gov.co/" target="_blank">
					<img alt="Supersalud logo" src="./images/supersalud.png" height=100 style="margin:0;" align="center">
				</a>
			</div>
			<div class="col-4" style="text-align:right;">
				<a href="https://www.minsalud.gov.co/" target="_blank">
					<img alt="MinSalud logo" src="./images/minsalud.png" height=57 style="margin-top:28px;" align="center">
				</a>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<hr>
			</div>
		</div>
		<div class="row justify-content-between">
			<div class="col-sm">
				<p class="fecha">
					<small>Fecha radicación <?= date('d/m/Y H:i') ?></small>
				</p>
			</div>
		</div>
		<div class="row">
			<div class="col-sm">
				<p class="lead" style="text-align: justify;">
					Este formulario es para radicar solicitudes cuando se requiere un trámite o servicio de alguna de las Delegaturas u Oficinas de la Superintendencia Nacional de Salud
				</p>
			</div>
		</div>
		<div class="row">
			<div class="col-sm">
				<div class="alert alert-warning" role="alert">
					Los campos con <strong>*</strong> son de diligenciamiento obligatorio
				</div>
			</div>
		</div>
		<form action="delegatura_back.php" id="form-solicitud" enctype="multipart/form-data" method="post">
			
			<div class="row"  id="formulario_peticionario">
				<div class="col-sm">
					<div class="row">
						<div class="col-sm">
							<h4 class="text-success section-h">Información del peticionario</h4>
						</div>
					</div>
					<div class="form-row">
						<div class="col-md-6 form-group">
							<label for="tipo">* Tipo remitente <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Persona natural se refiere a un individuo, Persona jurídica a una empresa u organización, Anónimo que no quiere dar a conocer su identidad. Defina si la persona que está formulando la queja es una Persona natural, Persona jurídica o Anónimo"></i></label><br>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="tipo" id="tipo1" value="1" data-required="true">
								<label class="form-check-label" for="tipo1">Natural</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="tipo" id="tipo2" value="2" data-required="true">
								<label class="form-check-label" for="tipo2">Jurídica</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="tipo" id="tipo4" value="4" data-required="true">
								<label class="form-check-label" for="tipo4">Niños, niñas y adolescentes</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="tipo" id="tipo3" value="3" data-required="true">
								<label class="form-check-label" for="tipo3">Anónimo</label>
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="col-md-3 form-group" data-natural data-juridico>
							<label for="id">* Tipo de identificación <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Seleccione el tipo de identificación"></i></label>
							<select name="tipo_identificacion_peticionario" id="tipo_identificacion_peticionario" title="Seleccionar" data-live-search="true" data-size="5" class="form-control" data-required="true">
								<?php foreach ($tipos_documentos as $tipo) { ?>
									<option value="<?=$tipo['TDID_CODI']?>"><?=$tipo['TDID_DESC']?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-3 form-group" data-natural data-juridico>
							<label for="id_peticionario">* Número de identificación <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite el número del documento de identificación del peticionario. En caso de que sea un menor de edad que no cuente con identificación, digite el número de documento del tutor o de la persona a cargo. Recuerde que si selecciona pasaporte debe ingresar números y letras de lo contrario sólo números."></i></label>
							<input type="number" class="form-control" id="id_peticionario" name="id_peticionario" autocomplete="no"  data-required="true">
						</div>
					</div>
					<div class="row" data-natural data-juridico>
						<div class="col-sm">
							<hr>
						</div>
					</div>
					<div class="form-row" data-natural>
						<div class="col-md-3 form-group">
							<label for="nombre_peticionario_1">* Primer nombre <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su primer nombre"></i></label>
							<input type="text" class="form-control alpha-only" id="nombre_peticionario_1" name="nombre_peticionario_1" autocomplete="no" data-required="true">
						</div>
						<div class="col-md-3 form-group">
							<label for="nombre_peticionario_2">Segundo nombre <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su segundo nombre"></i></label>
							<input type="text" class="form-control alpha-only" id="nombre_peticionario_2" name="nombre_peticionario_2" autocomplete="no">
						</div>
						<div class="col-md-3 form-group">
							<label for="apellidos_peticionario_1">* Primer apellido <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su primer apellido"></i></label>
							<input type="text" class="form-control alpha-only" id="apellidos_peticionario_1" name="apellidos_peticionario_1" autocomplete="no" data-required="true">
						</div>
						<div class="col-md-3 form-group">
							<label for="apellidos_peticionario_2">Segundo apellido <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su segundo apellido"></i></label>
							<input type="text" class="form-control alpha-only" id="apellidos_peticionario_2" name="apellidos_peticionario_2" autocomplete="no">
						</div>
					</div>
					<div class="form-row">
						<div class="col-md-12 form-group" style="display: none;" data-juridico>
							<label for="rs">* Razón social <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite la razón social"></i></label>
							<input type="text" class="form-control alpha-only" id="rs" name="rs" autocomplete="no">
						</div>
					</div>
					<div class="form-row">
						<div class="col-md-3 form-group" data-natural data-juridico>
							<label for="pais_peticionario">* País <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Seleccione el país en el que reside  "></i></label>
							<select name="pais_peticionario" id="pais_peticionario" title="Seleccionar" data-live-search="true" data-size="5" class="form-control" data-required="true">
								<?php foreach ($paises as $pais) { ?>
									<option value="<?=$pais['NOMBRE']?>"><?=$pais['NOMBRE']?></option>
								<?php } ?>
							</select>
						</div> 
						<div class="col-md-3 form-group" data-natural data-juridico>
							<label for="departamento_peticionario">* Departamento <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Seleccione el departamento en el que reside"></i></label>
							<select name="departamento_peticionario" id="departamento_peticionario" title="Seleccionar" data-live-search="true" data-size="5" class="form-control" data-required="true">
								<?php foreach ($departamentos as $departamento) { ?>
									<option value="<?=$departamento['DPTO_CODI']?>"><?=$departamento['DPTO_NOMB']?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-md-3 form-group" data-natural data-juridico>
							<label for="ciudad_peticionario">* Municipio <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Seleccione el municipio en el que vive"></i></label>
							<select name="ciudad_peticionario" id="ciudad_peticionario" title="Seleccionar" data-live-search="true" data-size="5" class="form-control" data-required="true"></select>
							<div id="ciudad_bar_peticionario" class="progress" style="display:none; height:5px;">
								<div class="progress-bar progress-bar-striped progress-bar-animated"  role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
							</div>
							<small id="emailHelp" class="form-text text-muted">*ANM Areas no municipalizadas</small>
						</div>
						<div class="col-md-6 form-group" data-natural data-juridico style="display:none;">
							<label for="provincia_peticionario">* Ciudad / Estado / Provincia</label>
							<input type="text" class="form-control" id="provincia_peticionario" name="provincia_peticionario" autocomplete="no" data-required="true">
						</div>
						<div class="col-md-3 form-group" data-natural data-juridico>
							<label for="direccion_peticionario">* Dirección <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite la dirección de su residencia"></i></label>
							<input type="text" class="form-control" id="direccion_peticionario" name="direccion_peticionario" autocomplete="no" data-required="true">
						</div>
						<div class="col-md-3 form-group" data-juridico>
							<label for="direccion_peticionario_2">* Dirección comercial <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite la dirección comercial"></i></label>
							<input type="text" class="form-control" id="direccion_peticionario_2" name="direccion_peticionario_2" autocomplete="no" data-required="true">
						</div>
					</div>
					<div class="row" data-natural data-juridico>
						<div class="col-sm">
							<hr>
						</div>
					</div>
					<div class="form-row" data-natural data-juridico>
						<div class="col-md-3 form-group">
							<label for="celular_peticionario">* Celular <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su número de celular, si no cuenta con esta información, seleccione la casilla de selección para deshabilitar el campo"></i></label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<input type="checkbox" data-active="#celular_peticionario" aria-label="Checkbox for following text input" checked>
									</div>
								</div>
								<input type="number" class="form-control" id="celular_peticionario" name="celular_peticionario" autocomplete="no" data-required="true">
							</div>
						</div>
						<div class="col-md-3 form-group" data-natural data-juridico>
							<label for="telefono_peticionario">* Teléfono fijo <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su número de teléfono fijo, si no cuenta con esta información, seleccione la casilla de selección para deshabilitar el campo"></i></label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<div class="input-group-text">
										<input type="checkbox" data-active="#telefono_peticionario" aria-label="Checkbox for following text input" checked>
									</div>
								</div>
								<input type="number" class="form-control" id="telefono_peticionario" name="telefono_peticionario" autocomplete="no" data-required="true">
							</div>
						</div>
						<div class="col-md-6" data-natural data-juridico>
							<div class="form-row">
								<div class="col-md-6 form-group">
									<label for="correo_peticionario">* Correo electrónico <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su correo electrónico, si no cuenta con esta información, seleccione la casilla de selección para deshabilitar el campo"></i></label>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<div class="input-group-text">
												<input type="checkbox" data-active="#correo_peticionario,#dominio_peticionario" aria-label="Checkbox for following text input" checked>
											</div>
										</div>
										<input type="text" class="form-control" id="correo_peticionario" name="correo_peticionario" autocomplete="no" data-required="true">
									</div>
								</div>
								<div class="col-md-6 email-component">
									<label for=""><i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Seleccione el dominio de la lista desplegable si no lo encuentra digítelo"></i>&nbsp;</label>
									<input type="text" class="form-control dominio" id="dominio_peticionario" name="dominio_peticionario" placeholder="dominio" aria-label="dominio" autocomplete="no" data-required="true">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm">
					<h4 class="text-success section-h">Detalle de la petición</h4>
				</div>
			</div>
			<!--
			<div class="form-row">
				<div class="col-md-6 form-group">
					<label for="">* Clase atención</label>
					<select name="tipoSolicitud" id="tipoSolicitud" title="Seleccionar" data-live-search="true" class="form-control">
						<option value="Reclamos">Reclamos</option>
						<option value="Consulta">Consulta</option>
						<option value="Solicitud información">Solicitud información</option>
						<option value="Seguimiento radicación">Seguimiento radicación</option>
						<option value="No Competencia">No Competencia</option>
					</select>
				</div>
				<div class="col-md-6 form-group">
					<label for="">* Tipo usuario</label>
					<select name="tipoUsuario" id="tipoUsuario" title="Seleccionar" data-live-search="true" class="form-control">
						<option value="Pobre NCSD">Pobre NCSD</option>
						<option value="Reg. Subsidiado">Reg. Subsidiado</option>
						<option value="Reg. Contributivo">Reg. Contributivo</option>
						<option value="P.A.S y Prepago">P.A.S y Prepago</option>
						<option value="Víctimas accidente tránsito">Víctimas accidente tránsito</option>
						<option value="Urgencias">Urgencias</option>
						<option value="No Competencia">No Competencia</option>
						<option value="Régimen de excepción">Régimen de excepción</option>
					</select>
				</div>
			</div>
			-->
			<div class="row">
				<div class="col-sm">
					<div class="alert alert-light" role="alert">
						Describa brevemente la solicitud de información
					</div>
				</div>
			</div>
			<div class="form-row">
				<div class="col-md-12 form-group limited-textarea">
					<label for="comentarios">* Escriba aquí la solicitud de información: <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite la inconformidad o insatisfacción frente a la prestación del servicio de salud"></i></label>
					<textarea id="comentarios" name="comentarios" class="form-control" rows="5" autocomplete="no" data-required="true" maxlength="5000"></textarea>
					<span class="size" data-max="5000">0/5000</span>
				</div>
			</div>
			<div class="form-row">
				<div class="col-md-12 form-group" id="li_upload">
					<label for=""><i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Adjunte los soportes que considere pueden servir para su solicitud de información, si estos son muy pesados los puede comprimir. Adjuntar archivo máximo disponible hasta 5 Adjuntos - por cada uno 2 M.B. Formatos válidos: (tif, tiff, jpeg, pdf, docx, txt, jpg, gif, xls, xlsx, doc, png, msg, Zip, m4a, mp3, mp4.)"></i></label>
					<div id="filelimit-fine-uploader" tabindex="17"></div>
					<div id="availabeForUpload"></div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm">
					<hr>
				</div>
			</div>
			<div class="form-row">
				<div class="col-sm" data-natural data-juridico>
					Autorizo el envío de información a través de: &nbsp;
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="medio[]" id="medio_1" value="Correo electrónico" data-required="true" checked>
						<label class="form-check-label" for="medio_1">Correo electrónico</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="medio[]" id="medio_2" value="Dirección de correspondencia" data-required="true">
						<label class="form-check-label" for="medio_2">Dirección de correspondencia</label>
					</div>
				</div>
			</div>
			<div class="row" data-natural data-juridico>
				<br>
			</div>
			<div class="form-row">
				<div class="col-md-12 form-group" style="text-align: justify">
					Al hacer clic en el botón enviar, usted acepta la remisión de la PQRD a la entidad Superintendencia Nacional de Salud. Sus datos serán recolectados y tratados conforme con la <a href="https://www.supersalud.gov.co/es-co/atencion-ciudadano/informacion-de-interes/proteccion-de-datos-personales" target="_blank">Política de Tratamiento de Datos.</a>
					<br><br>
					En caso de que la solicitud de información sea de naturaleza de identidad reservada, deberá efectuar el respectivo trámite ante la Procuraduría General de la Nación, haciendo clic en el siguiente link: <a href="https://www.procuraduria.gov.co/portal/solicitud_informacion_identificacion_reservada.page" target="_blank">https://www.procuraduria.gov.co/portal/solicitud_informacion_identificacion_reservada.page</a>
					<br><br>
					Términos que aplican en la presentación de quejas anónimas, <a href="http://www.suin-juriscol.gov.co/viewDocument.asp?ruta=Leyes/1671809" target="_blank">Ley 962 de 2005 Artículo 81</a>. "Ninguna denuncia o queja anónima podrá promover acción jurisdiccional, penal, disciplinaria, fiscal, o actuación de la autoridad administrativa competente (excepto cuando se acredite, por lo menos sumariamente la veracidad de los hechos denunciados) o cuando se refiera en concreto a hechos o personas claramente identificables."
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="g-recaptcha" style="float:left" data-sitekey="6LcRhc4ZAAAAAI6Pb3nMgNDZiNKA2Apww8axr42X"></div>
				</div>
			</div>
			<div class="form-row">
				<div class="col-md-12">
					<br>
					<input type="hidden" id="tipoSolicitud" name="tipoSolicitud" value="Trámite y/o servicio ante la Supersalud"/>
					<input type="hidden" id="tipoUsuario" name="tipoUsuario" value=""/>
					<input type="hidden" id="adjuntosSubidos" name="adjuntosSubidos" value=""/>
					<input type="hidden" name="pais" value="170">
					<input type="submit" class="btn btn-success" value="Enviar">
					<input type="button" id="borrar" class="btn btn-default" value="Borrar">
					<a href="index.php" class="btn btn-default">Volver</a>
				</div>
			</div>
			<div class="form-row">
				<div class="col-md-12">
					<br><br>
				</div>
			</div>
		</form>
	</div>
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-*.min.js"></script>
	<script src="https://cdn.jsdelivr.net/gh/xcash/bootstrap-autocomplete@v2.3.7/dist/latest/bootstrap-autocomplete.min.js"></script>
	<script	type="text/javascript" src="scripts/jquery.fineuploader-3.0.js"></script>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<script type="text/javascript">
		$(function()
		{
			$('[data-toggle="tooltip"]').tooltip()

			$(".alpha-only").on("keydown", function(event){
				// Allow controls such as backspace, tab etc.
				var arr = [8,9,16,17,20,32,35,36,37,38,39,40,45,46,192];

				// Allow letters
				for(var i = 65; i <= 90; i++){
					arr.push(i);
				}

				// Prevent default if not in array
				if(jQuery.inArray(event.which, arr) === -1){
					event.preventDefault();
				}
			});
			
			$('body').delegate('input[type="number"]', 'keypress', function(event)
			{
				if ((event.which != 8 && event.which != 9) && isNaN(String.fromCharCode(event.which))){
					event.preventDefault(); //stop character from entering input
				}
			});

			$('#borrar').on('click', function(e) {
				$("#form-solicitud")[0].reset();
				$('select').each(function(e) {
					$(this).val('').trigger('change');
					$('#ciudad_bar').hide();
					$('#entidad_bar').hide();
				});
			});

			function validarCheckboxGestante() {
				if($('input[name="edad"]').val() != '' && $('input[name="sexo"]').is(':checked')) {
					console.log($('input[name="sexo"]:checked').val(), $('input[name="edad"]').val());
					if($('input[name="sexo"]:checked').val() == 'Femenino' && $('input[name="edad"]').val() * 1 > 10) {
						$('input[name="gestante"]').prop('disabled', false);
					} else {
						$('input[name="gestante"]').prop('checked', false);
						$('input[name="gestante"]').prop('disabled', true);
					}	
				}
			}
			
			var fileCountSize = 0;
			// Limite para la cantidad de archivos que se pueden subir.
			var fileCountLimit = 100;
			// Cantidad de archivos subidos.
			var addedFiles = 0;
			// Limite de subida de los archivos, en total.
			var fileLimit = 10*1024*1024;
			// Arregloq ue contiene los archivos subidos.
			var fileNamesTmpDir  = new Array();
			var uploader;

			function isEmail(email) {
				var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				return regex.test(email);
			}

			$('select').selectpicker();

			
			var tipo_identificacion_peticionario_options = $('select[name="tipo_identificacion_peticionario"]').html();
			

			$('input[name="tipo"]').on('change', function(e) 
			{
				var options_html = $('<div></div>').html(tipo_identificacion_peticionario_options);
				options_html.find('.bs-title-option').remove();
				var tipo = $(this).val();

				var label_celular_peticionario = '* Celular <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su número de celular, si no cuenta con esta información, seleccione la casilla de selección para deshabilitar el campo"></i>';
				var label_direccion_peticionario = '* Dirección <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite la dirección de su residencia"></i>';
				var label_telefono_peticionario = '* Teléfono fijo<i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su número de teléfono fijo, si no cuenta con esta información, seleccione la casilla de selección para deshabilitar el campo"></i>';
				switch(tipo){
					case '4':
						$('div[data-juridico]').hide();
						$('div[data-anonimo]').hide();
						$('div[data-natural]').show();
						$('select[name="pais_peticionario"]').trigger('change');
						options_html.html(` <option value="6">Menor sin identificación</option>
											<option value="5">Nuip</option>
											<option value="8">Registro civil</option>
											<option value="1">Tarjeta de Identidad</option>
											<option value="3">Pasaporte</option>
											<option value="7">Permiso especial de permanencia</option>`);
						$('select[name="tipo_identificacion_peticionario"]').html(options_html.html());
						$('select[name="tipo_identificacion_peticionario"]').selectpicker('refresh');
					case '1':
						$('div[data-juridico]').hide();
						$('div[data-anonimo]').hide();
						$('div[data-natural]').show();
						$('select[name="pais_peticionario"]').trigger('change');
						$('select[name="tipo_identificacion_peticionario"]').html(options_html.html());
						$('select[name="tipo_identificacion_peticionario"]').selectpicker('refresh');
					break;
					case '2':
						$('div[data-natural]').hide();
						$('div[data-anonimo]').hide();
						$('div[data-juridico]').show();
						$('select[name="pais_peticionario"]').trigger('change');
						options_html.html('<option value="4">Nit</option>');
						$('select[name="tipo_identificacion_peticionario"]').html(options_html.html());
						$('select[name="tipo_identificacion_peticionario"]').selectpicker('refresh');
						label_direccion_peticionario = '* Dirección fiscal <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite la dirección fiscal"></i>';
						label_celular_peticionario = '* Teléfono fiscal <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su número de teléfono fiscal, si no cuenta con esta información, seleccione la casilla de selección para deshabilitar el campo fiscal"></i>';
						label_telefono_peticionario = '* Teléfono comercial <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su número de teléfono comercial, si no cuenta con esta información, seleccione la casilla de selección para deshabilitar el campo comercial"></i>';
					break;
					case '3':
						$('div[data-natural]').hide();
						$('div[data-juridico]').hide();
						$('div[data-anonimo]').show();
					break;
				}

				$('label[for="direccion_peticionario"]').html(label_direccion_peticionario);
				$('label[for="celular_peticionario"]').html(label_celular_peticionario);
				$('label[for="telefono_peticionario"]').html(label_telefono_peticionario);
				$('[data-toggle="tooltip"]').tooltip();
			});

			$('input[name="afectado"]').on('change', function(e) {
				if($(this).val() == 'No')
					$('#formulario_peticionario').show();
				else
					$('#formulario_peticionario').hide();
			});

			$('input[name="sexo"]').on('change', function(e) {
				validarCheckboxGestante();
			});

			$('select[name^="pais_"]').on('change', function(e) {
				var name = $(this).attr("name");
				var persona = name.split('_');

				if($(this).val() !== 'Colombia' && $(this).val() !== '')
				{
					$('select[name="departamento_'+persona[1]+'"]').closest('.form-group').hide();
					$('select[name="ciudad_'+persona[1]+'"]').closest('.form-group').hide();
					$('input[name="provincia_'+persona[1]+'"]').closest('.form-group').show();
				} else {
					$('select[name="departamento_'+persona[1]+'"]').closest('.form-group').show();
					$('select[name="ciudad_'+persona[1]+'"]').closest('.form-group').show();
					$('input[name="provincia_'+persona[1]+'"]').closest('.form-group').hide();
				}
			});

			$('input[data-active]').on('change', function(e) {
				var selector = $(this).data('active');
				if($(this).is(':checked'))
				{
					$(selector).each(function(i, element) {
						$(element).prop('disabled', false);
						$(element).attr('data-required', 'true');
					});
				} else {
					$(selector).each(function(i, element) {
						$(element).prop('disabled', true);
						$(element).removeAttr('data-required');
					});
				}
			});

			
			$('#tipo_identificacion_afectado').on('change', function(e) {
				var tipo_identificacion = $(this).val();
				$('#id_afectado').val('');

				if(tipo_identificacion == 3)
				{
					$('#id_afectado').attr('type', 'text');
				} else {
					$('#id_afectado').attr('type', 'number');
				}
			});

			$('#tipo_identificacion_peticionario').on('change', function(e) {
				var tipo_identificacion = $(this).val();
				$('#id_peticionario').val('');

				if(tipo_identificacion == 3)
				{
					$('#id_peticionario').attr('type', 'text');
				} else {
					$('#id_peticionario').attr('type', 'number');
				}
			});

			$('#departamento_afectado').on('change', function(e) {
				$('#ciudad_bar_afectado').show();
				var request = $.post(
					'solicitudes_ajax.php',
					{
						servicio: 'ciudades',
						id_depto: $(this).val()
					},
					'json'
				);

				request.done(function(res) {
					var options = '';

					if(res) {
						$.each(res, function(i, e) {
							options += '<option value="'+e.MUNI_CODI+'">'+e.MUNI_NOMB+'</option>'
						});

						$('#ciudad_afectado').html(options);
						$('#ciudad_afectado').selectpicker('refresh');
						$('#ciudad_bar_afectado').hide();
					}
				})
			});

			$('#departamento_peticionario').on('change', function(e) {
				$('#ciudad_bar_peticionario').show();
				var request = $.post(
					'solicitudes_ajax.php',
					{
						servicio: 'ciudades',
						id_depto: $(this).val()
					},
					'json'
				);

				request.done(function(res) {
					var options = '';

					if(res) {
						$.each(res, function(i, e) {
							options += '<option value="'+e.MUNI_CODI+'">'+e.MUNI_NOMB+'</option>'
						});

						$('#ciudad_peticionario').html(options);
						$('#ciudad_peticionario').selectpicker('refresh');
						$('#ciudad_bar_peticionario').hide();
					}
				})
			});

			

			$('input[name="edad"]').on('keyup blur change', function(e) {
				var edad = $(this).val() * 1;
				var rango = '';

				if(edad > 62) {
					rango = 'Mayor de 63 años';
				} else if (edad > 49) {
					rango = 'De 50 a 62 años';
				} else if (edad > 37) {
					rango = 'De 38 a 49 años';
				} else if (edad > 29) {
					rango = 'De 30 a 37 años';
				} else if (edad > 24) {
					rango = 'De 25 a 29 años';
				} else if (edad > 17) {
					rango = 'De 18 a 24 años';
				} else if (edad > 12) {
					rango = 'De 13 a 17 años';
				} else if (edad > 5) {
					rango = 'De 6 a 12 años';
				} else if (edad > -1) {
					rango = 'De 0 a 5 años';
				} else {
					rango = '';
				}

				if($(this).val() == '')
				{
					$('input[name="rango_edad"]').val('');	
				} else {
					validarCheckboxGestante();
					$('input[name="rango_edad"]').val(rango);
				}
			});

			$('#form-solicitud').on('submit', function(e) {
				var errors = 0;

				$('input[type="text"], input[type="number"], input[type="radio"], input[type="checkbox"], textarea, select').each(function(e) {
					
					if($(this).is(':visible') && $(this).attr('data-required') == 'true')
					{
						var valor = '';
						var name = $(this).attr('name');
						if($(this).is('input'))
						{
							switch($(this).attr('type'))
							{
								case 'text':
								case 'number':
									valor = $(this).val();
								break;

								case 'radio':
								case 'checkbox':
									valor = $('input[name="'+name+'"]:checked').val();
									if(!valor) valor = '';
								break;
							}
						}

						if($(this).is('textarea'))
						{
							valor = $(this).val();
						}

						if($(this).is('select'))
						{
							valor = $(this).val();
						}

						if(valor == '')
						{
							$(this).removeClass('is-valid');
							$(this).addClass('is-invalid');
							errors ++;
						} else {
							$(this).removeClass('is-invalid');
							$(this).addClass('is-valid');
						}

						if($(this).is('select'))
						{
							$(this).selectpicker('refresh');
						}
						
					} else {
						$(this).removeClass('is-invalid');
					}
				});

				if($('#correo_afectado').attr('data-required') == 'true')
				{
					if($('#correo_afectado').is(':visible') && !isEmail($('#correo_afectado').val()+'@'+$('#dominio_afectado').val()))
					{
						$('#correo_afectado').removeClass('is-valid');
						$('#correo_afectado').addClass('is-invalid');
						$('#dominio_afectado').removeClass('is-valid');
						$('#dominio_afectado').addClass('is-invalid');
						errors ++;
					} else {
						$('#correo_afectado').removeClass('is-invalid');
						$('#correo_afectado').addClass('is-valid');
						$('#dominio_afectado').removeClass('is-invalid');
						$('#dominio_afectado').addClass('is-valid');
					}
				}

				if($('#correo_peticionario').attr('data-required') == 'true')
				{
					if($('#correo_peticionario').is(':visible') && !isEmail($('#correo_peticionario').val()+'@'+$('#dominio_peticionario').val()))
					{
						$('#correo_peticionario').removeClass('is-valid');
						$('#correo_peticionario').addClass('is-invalid');
						$('#dominio_peticionario').removeClass('is-valid');
						$('#dominio_peticionario').addClass('is-invalid');
						errors ++;
					} else {
						$('#correo_peticionario').removeClass('is-invalid');
						$('#correo_peticionario').addClass('is-valid');
						$('#dominio_peticionario').removeClass('is-invalid');
						$('#dominio_peticionario').addClass('is-valid');
					}
				}

				$('#adjuntosSubidos').val(JSON.stringify(fileNamesTmpDir));

				if(grecaptcha.getResponse() == '')
				{
					$('.g-recaptcha').css('border', '1px solid #f00');
				} else {
					$('.g-recaptcha').css('border', 'none');
				}

				if(errors > 0 || grecaptcha.getResponse() == '')
				{
					e.preventDefault();
				} else {
					$("body").append($(`<div id="imageloader" class="loader" style="
						width: 100%;
						position: fixed;
						top: 50%;
						left: 40%;
					"><img id="spinner" width="200" src="https://flevix.com/wp-content/uploads/2019/12/Color-Fill-loading-Image-1.gif" alt="Spinner">
					</div>`));
					$(this).prop("disabled", true);
					$(this).css({
						"opacity": ".2",
						"cursor": "progress"
					});
				}
			});

			function createUploader() {
				uploader = new qq.FineUploader({
					element: document.getElementById('filelimit-fine-uploader'),
					request: {
						endpoint: 'qqUploadedFileXhr.class.php',
					},
					multiple: true,
					validation: {
						sizeLimit: '5'*1024*1024// 5.0MB = 5 * 1024 kb * 1024 bytes
					},
					text: {
						uploadButton: '<i class="icon-upload icon-white"></i> Adjuntar soportes'
					},
					autoUpload : true,
					callbacks: {
						onSubmit: function(id, fileName) {	    
							if((fileCountSize + uploader._handler._files[id].size) > fileLimit) 
							{
								$('.qq-upload-button').hide();
								$('.qq-upload-drop-area').hide();
								alert('El tamaño máximo permitido de subida de todos los archivos es de ' + uploader._formatSize(fileLimit));
								return false;
							}
							fileCountSize += uploader._handler._files[id].size;
						},
						onCancel: function(id, fileName) {
							try {
								if($.isNumeric(uploader._handler._files[id].size)) {
									fileCountSize -= uploader._handler._files[id].size;
								}
							} catch(error) {
								//Debe ser que estamos en explorer
							}
							var index = fileNamesTmpDir.indexOf(fileName);
							if(index>=0) {
								addedFiles--;
								fileNamesTmpDir.splice(index,1);
								//Prevenir sacar el mensaje de archivos en progreso, cuando se hace un cancel manual.
								uploader._filesInProgress++;
							}
							
							if(fileCountSize <= fileLimit) {
								$('.qq-upload-button').show();
							}
							
							$('#availabeForUpload').html('Tamaño máximo por archivo de 5.0MB. Total disponible ' +  uploader._formatSize(fileLimit-fileCountSize) );
						},
						onComplete: function(id, fileName, responseJSON) {
							if (responseJSON.success) {
								fileNamesTmpDir.push(fileName);
								addedFiles ++;
								$('#availabeForUpload').html('Tamaño máximo por archivo de 5.0MB. Total disponible ' +  uploader._formatSize(fileLimit-fileCountSize) );
								if(addedFiles >= fileCountLimit) {
									alert('Has alcanzado la cantidad máxima de archivos a subir, no podrás subir más de ' + fileCountLimit + ' archivos.');
									$('.qq-upload-button').hide();
									$('.qq-upload-drop-area').hide();
								}
							} else {
								alert('Ocurrió un error subiendo el archivo. Por favor valida que no supere los 5.0MB y en total 10.0MB');
							}
						},
						onError: function(id, fileName, errorReason) {
							alert('Ocurrió un error subiendo el archivo.' + errorReason);
						},
					},
					debug: true
				});
			}

			createUploader();
			$('#availabeForUpload').html('Tamaño máximo por archivo 5.0MB, Total disponible ' +  uploader._formatSize(fileLimit-fileCountSize));
			
			var dominios = [
				{ value:'gmail.com', text: 'gmail.com'},
				{ value:'googlemail.com', text: 'googlemail.com'},
				{ value:'hotmail.com', text: 'hotmail.com'},
				{ value:'hotmail.es', text: 'hotmail.es'},
				{ value:'live.com', text: 'live.com'},
				{ value:'mac.com', text: 'mac.com'},
				{ value:'facebook.com', text: 'facebook.com'},
				{ value:'outlook.com', text: 'outlook.com'},
				{ value:'yahoo.com', text: 'yahoo.com'},
				{ value:'comcast.net', text: 'comcast.net'},
				{ value:'aol.com', text: 'aol.com'},
				{ value:'sky.com', text: 'sky.com'},
				{ value:'bellsouth.net', text: 'bellsouth.net'},
				{ value:'yahoo.es', text: 'yahoo.es'},
				{ value:'verizon.net', text: 'verizon.net'},
				{ value:'mail.com', text: 'mail.com'},
				{ value:'me.com', text: 'me.com'},
				{ value:'msn.com', text: 'msn.com'}
			];

			$('.dominio').autoComplete({
				minLength: 0,
				noResultsText: '',
				resolver: 'custom',
				events: {
					search: function (qry, callback) {	
						if(qry=='') {
							callback(dominios);
						} else {
							callback(dominios.filter(function(dominio) {
								return dominio.value.indexOf(qry) != -1;
							}))
						}
					}
				}
			});

			$('.dominio').on('focus', function(e) {
				$(this).trigger('keyup');
			});

			$('#comentarios').on('keyup', function(e) {
				var comentarios = $('#comentarios').val();
				$('.size').text(comentarios.length+'/5000');
			});

			$('#comentarios').bind('copy paste cut',function(e) { 
				e.preventDefault(); //disable cut,copy,paste
				//alert('cut,copy & paste options are disabled !!');
			});
		});
	</script>
<?php include ('footer.php') ?>