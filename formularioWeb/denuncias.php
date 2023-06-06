<?php
	session_start();
	define('ADODB_ASSOC_CASE', 1);
	$ruta_raiz = "..";
	$ADODB_COUNTRECS = false;
	
	include_once("$ruta_raiz/processConfig.php");
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	include_once("./solicitudes_sql.php");
	
	$db = new ConnectionHandler($ruta_raiz);
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

	//departamentos($db) de solicitudes_sql.php
	
	$paises = paises($db);
	$departamentos = departamentos($db);
	$tipos_documentos = tipos_documentos($db);
?>
<?php include ('header.php') ?>
	<div class="container">
		<div class="row justify-content-between" style="margin-top:10px;">
			<div class="col-4" style="text-align:left;">
				<a href="https://www.crautonoma.gov.co/" target="_blank">
					<img src="./images/CRA logo.png" height=100 style="margin:0;" align="center">
				</a>
			</div>
			<div class="col-4" style="text-align:right;">
				<a href="https://id.presidencia.gov.co/" target="_blank">
					<img src="./images/Gobierno logo.png" height=57 style="margin-top:28px;" align="center">
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
					Si tiene sugerencias o quejas para el mejoramiento de nuestra empresa, permítanos conocerlas diligenciando este formato. Asimismo, si desea solicitar documentos que ostente la calidad de público y que no tenga reserva legal puede hacerlo por este medio.
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
		<form action="solicitudes_denuncias.php" id="form-solicitud" enctype="multipart/form-data" method="post" autocomplete="nope">
			<div class="row">
				<div class="col-sm">
					<h5 class="text-success section-h">Información personal del remitente</h5>
				</div>
			</div>
      
      <div class="form-row">
				<div class="col-md-6 form-group">
					<label for="tipo">* Tipo de PQRS:</label><br>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="opcion_ts" id="tipo1" value="753" data-required="true">
						<label class="form-check-label" for="tipo1">Petición</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="opcion_ts" id="tipo1" value="97" data-required="true">
						<label class="form-check-label" for="tipo1">Queja</label>
					</div>
          <div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="opcion_ts" id="tipo1" value="754" data-required="true">
						<label class="form-check-label" for="tipo1">Reclamo</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="opcion_ts" id="tipo1" value="755" data-required="true">
						<label class="form-check-label" for="tipo1">Sugerencias</label>
					</div>
				</div>
      </div>
			<div class="form-row">
				<div class="col-md-6 form-group">
					<label for="tipo">* Tipo de Solicitud:</label><br>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="tipo" id="tipo1" value="1" data-required="true">
						<label class="form-check-label" for="tipo1">Natural</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="tipo" id="tipo2" value="2" data-required="true">
						<label class="form-check-label" for="tipo2">Jurídica</label>
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
					<select name="tipo_identificacion" id="tipo_identificacion" title="Seleccionar" data-live-search="true" data-size="5" class="form-control" data-required="true">
						<?php foreach ($tipos_documentos as $tipo) { ?>
							<option value="<?=$tipo['TDID_CODI']?>"><?=$tipo['TDID_DESC']?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-md-3 form-group" data-natural data-juridico>
					<label for="id">* Número de identificación <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite el número de identificación"></i></label>
					<input type="text" class="form-control" id="id" name="id" autocomplete="no" data-required="true">
				</div>
			</div>
			<div class="row" data-natural data-juridico>
				<div class="col-sm">
					<hr>
				</div>
			</div>
			<div class="form-row">
				<div class="col-md-3 form-group" data-natural>
					<label for="nombre_afectado_1">* Primer nombre <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su primer nombre"></i></label>
					<input type="text" class="form-control alpha-only" id="nombre_afectado_1" name="nombre_afectado_1" autocomplete="no" data-required="true">
				</div>
				<div class="col-md-3 form-group" data-natural>
					<label for="nombre_afectado_2">Segundo nombre <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su segundo nombre"></i></label>
					<input type="text" class="form-control alpha-only" id="nombre_afectado_2" name="nombre_afectado_2" autocomplete="no">
				</div>
				<div class="col-md-3 form-group" data-natural>
					<label for="apellidos_afectado_1">* Primer apellido <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su primer apellido"></i></label>
					<input type="text" class="form-control alpha-only" id="apellidos_afectado_1" name="apellidos_afectado_1" autocomplete="no" data-required="true">
				</div>
				<div class="col-md-3 form-group" data-natural>
					<label for="apellidos_afectado_2">Segundo apellido <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su segundo apellido"></i></label>
					<input type="text" class="form-control alpha-only" id="apellidos_afectado_2" name="apellidos_afectado_2" autocomplete="no">
				</div>
				<div class="col-md-12 form-group" style="display: none;" data-juridico>
					<label for="rs">* Razón social <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite la razón social"></i></label>
					<input type="text" class="form-control alpha-only" id="rs" name="rs" autocomplete="no" data-required="true">
				</div>
      
      </div>
        
			<div class="form-row">
				<div class="col-md-3 form-group" data-natural data-juridico>
					<label for="pais_afectado">* País <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Seleccione el país en el que reside"></i></label>
					<select name="pais_afectado" id="pais_afectado" title="Seleccionar" data-live-search="true" data-size="5" class="form-control" data-required="true">
						<?php foreach ($paises as $pais) { ?>
							<option value="<?=$pais['NOMBRE']?>"><?=$pais['NOMBRE']?></option>
						<?php } ?>
					</select>
				</div> 
				<div class="col-md-3 form-group" data-natural data-juridico>
					<label for="departamento_afectado">* Departamento <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Seleccione el departamento en el que reside"></i></label>
					<select name="departamento_afectado" id="departamento_afectado" title="Seleccionar" data-live-search="true" data-size="5" class="form-control" data-required="true">
						<?php foreach ($departamentos as $departamento) { ?>
							<option value="<?=$departamento['DPTO_CODI']?>"><?=$departamento['DPTO_NOMB']?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-md-3 form-group" data-natural data-juridico>
					<label for="ciudad_afectado">* Municipio <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Seleccione el municipio en el que reside"></i></label>
					<select name="ciudad_afectado" id="ciudad_afectado" title="Seleccionar" data-live-search="true" data-size="5" class="form-control" data-required="true"></select>
					<div id="ciudad_bar" class="progress" style="display:none; height:5px;">
						<div class="progress-bar progress-bar-striped progress-bar-animated"  role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
					</div>
					<small id="emailHelp" class="form-text text-muted">*ANM Areas no municipalizadas</small>
				</div>
				<div class="col-md-6 form-group" data-natural data-juridico style="display:none;">
						<label for="provincia_afectado">* Ciudad / Estado / Provincia</label>
						<input type="text" class="form-control" id="provincia_afectado" name="provincia_afectado" autocomplete="no" data-required="true">
					</div>
				<div class="col-md-3 form-group" data-natural data-juridico>
					<label for="direccion">* Dirección <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite la dirección de su residencia"></i></label>
					<input type="text" class="form-control" id="direccion" name="direccion" autocomplete="no" data-required="true">
				</div>
			</div>
			<div class="form-row">
				<div class="col-md-6 form-group" data-natural data-juridico>
					<label for="correo">* Correo electrónico <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su correo electrónico"></i></label>
					<input type="text" class="form-control" id="correo" name="correo" autocomplete="no" data-required="true">
				</div>
				<div class="col-md-3 form-group" data-natural data-juridico>
					<label for="celular"> Teléfono <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su número de teléfono"></i></label>
					<input type="number" class="form-control" id="telefono" name="telefono" autocomplete="no">
				</div>
				<div class="col-md-3 form-group" data-natural data-juridico>
					<label for="telefono"> * Celular <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su número de celular"></i></label>
					<input type="number" class="form-control" id="celular" name="celular" autocomplete="no" data-required="true">
				</div>
			</div>
			<div class="hide" data-juridico>
				<div class="row">
					<div class="col-sm">
						<h4 class="text-success section-h">Denunciante o Quejoso</h4>
					</div>
				</div>
        
				<div class="form-row">
					<div class="col-md-3 form-group">
						<label for="id">* Tipo de identificación <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Seleccione el tipo de identificación"></i></label>
						<select name="representante_tipo_identificacion" id="representante_tipo_identificacion" title="Seleccionar" data-live-search="true" data-size="5" class="form-control" data-required="true">
							<?php foreach ($tipos_documentos as $tipo) { ?>
								<option value="<?=$tipo['TDID_CODI']?>"><?=$tipo['TDID_DESC']?></option>
							<?php } ?>
						</select>
					</div>
				
        	<div class="col-md-3 form-group">
						<label for="id">* Número de identificación <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite el número del documento de identificación del paciente o afectado. En caso de que sea un menor de edad que no cuente con identificación, digite el número de documento del tutor o de la persona a cargo. Recuerde que si selecciona pasaporte debe ingresar números y letras de lo contrario sólo números"></i></label>
						<input type="text" class="form-control" id="representante_id" name="representante_id" autocomplete="no" data-required="true">
					</div>
				</div>
			
      	<div class="form-row">
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
					<div class="col-md-3 form-group" data-natural data-juridico>
						<label for="pais_representante">País <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Seleccione el país en el que reside "></i></label>
						<select name="pais_representante" id="pais_representante" title="Seleccionar" data-live-search="true" data-size="5" class="form-control">
							<?php foreach ($paises as $pais) { ?>
								<option value="<?=$pais['NOMBRE']?>"><?=$pais['NOMBRE']?></option>
							<?php } ?>
						</select>
					</div> 
			
      		<div class="col-md-3 form-group" data-natural data-juridico>
						<label for="">Departamento <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Seleccione el departamento en el que reside"></i></label>
						<select name="departamento_representante" id="departamento_representante" title="Seleccionar" data-live-search="true" data-size="5" class="form-control">
							<?php foreach ($departamentos as $departamento) { ?>
								<option value="<?=$departamento['DPTO_CODI']?>"><?=$departamento['DPTO_NOMB']?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-md-3 form-group" data-natural data-juridico>
						<label for="">Municipio <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Seleccione el municipio en el que reside"></i></label>
						<select name="ciudad_representante" id="ciudad_representante" title="Seleccionar" data-live-search="true" data-size="5" class="form-control"></select>
						<div id="representante_ciudad_bar" class="progress" style="display:none; height:5px;">
							<div class="progress-bar progress-bar-striped progress-bar-animated"  role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
						</div>
						<small id="emailHelp" class="form-text text-muted">*ANM Areas no municipalizadas</small>
					</div>
					<div class="col-md-6 form-group" data-natural data-juridico style="display:none;">
						<label for="provincia_representante">* Ciudad / Estado / Provincia</label>
						<input type="text" class="form-control" id="provincia_representante" name="provincia_representante" autocomplete="no">
					</div>
					<div class="col-md-3 form-group" data-natural data-juridico>
						<label for="representante_direccion">Dirección <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite la dirección de su residencia"></i></label>
						<input type="text" class="form-control" id="representante_direccion" name="representante_direccion" autocomplete="no">
					</div>
				</div>
        
				<div class="form-row">
					<div class="col-md-6 form-group" data-natural data-juridico data-anonimo>
						<label for="representante_correo">* Correo electrónico <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su correo electrónico"></i></label>
						<input type="text" class="form-control" id="representante_correo" name="representante_correo" autocomplete="no" data-required="true">
					</div>
					<div class="col-md-3 form-group" data-natural data-juridico>
						<label for="representante_celular">* Celular <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su número de celular"></i></label>
						<input type="number" class="form-control" id="representante_celular" name="representante_celular" autocomplete="no" data-required="true">
					</div>
					<div class="col-md-3 form-group" data-natural data-juridico>
						<label for="representante_telefono">* Teléfono <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su número de teléfono"></i></label>
						<input type="number" class="form-control" id="representante_telefono" name="representante_telefono" autocomplete="no" data-required="true">
					</div>
				</div>
			</div>
      

        
			<div class="row">
				<div class="col-sm">
					<h5 class="text-success section-h">Detalle de la Queja o Denuncia</h5>
				</div>
			</div>
      
			<div class="row">
				<div class="col-sm">
					<div class="alert alert-light" role="alert">
						<strong>Nota:</strong> Describa los detalles del caso y si conoce el nombre del funcionario por favor indíquelo.
					</div>
				</div>
			</div>
			<div class="form-row">
				<div class="col-md-12 form-group limited-textarea">
					<label for="asunto">* Describa brevemente los hechos referentes a su petición (máximo 500 caracteres): </label>
					<textarea id="asunto" name="asunto_d" class="form-control" rows="5" autocomplete="no" data-required="true" maxlength="500"></textarea>
					
				</div>
			</div>
      
      <div class="form-row">
				<div class="col-md-12 form-group limited-textarea">
					<label for="comentarios">* Describa su solicitud: </label>
					<textarea id="comentarios" name="comentarios" class="form-control" rows="5" autocomplete="no" data-required="true" maxlength="5000"></textarea>
					<span class="size" data-max="5000">0/5000</span>
				</div>
			</div>
      
			<div class="form-row">
				<div class="col-md-12 form-group" id="li_upload">
					<label for=""><i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Adjunte los soportes que considere pueden servir para su queja, si estos son muy pesados los puede comprimir. Adjuntar archivo máximo disponible hasta 5 Adjuntos - por cada uno 2 M.B. Formatos válidos: (tif, tiff, jpeg, pdf, docx, txt, jpg, gif, xls, xlsx, doc, png, msg, Zip, m4a, mp3, mp4.)"></i></label>
					<div id="filelimit-fine-uploader" tabindex="17"></div>
					<div id="availabeForUpload"></div>
				</div>
			</div>
			<div class="row" data-natural>
				<div class="col-sm">
					<hr>
				</div>
			</div>
			<div class="form-row" data-natural data-juridico>
				<div class="col-sm">
					* Autorizo el envío de información a través de: &nbsp;
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
			<div class="row">
				<br>
			</div>
			<div class="form-row">
				<div class="col-md-12 form-group" style="text-align: justify">
Corporación Autónoma Regional del Atlántico solicita su autorización para dar tratamiento a los datos personales de contacto que suministra a través de la presente plataforma. Estos datos serán utilizados con la finalidad de mantener una comunicación efectiva orientada a solicitar mayor información sobre la petición presentada. Nuestra Política de Tratamiento de Información Personal puede ser consultada en el sitio web y sus derechos como titular (acceso, rectificación y cancelación) de datos personales podrán ser ejercidos a través del correo electrónico info@crautonoma.gov.co
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<!--<div class="g-recaptcha" style="float:left" data-sitekey="6LcRhc4ZAAAAAI6Pb3nMgNDZiNKA2Apww8axr42X"></div>-->
				</div>
			</div>
			<div class="form-row">
				<div class="col-md-12">
					<br>
					<input type="hidden" name="tipoSolicitud" value="Queja">
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
	
	<script	type="text/javascript" src="scripts/jquery.fineuploader-3.0.js"></script>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<script type="text/javascript">
		$(function()
		{		
			$('[data-toggle="tooltip"]').tooltip();

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
				});
			});

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

			var tipo_identificacion_peticionario_options = $('select[name="tipo_identificacion"]').html();

			$('input[name="tipo"]').on('change', function(e) 
			{
				var options_html = $('<div></div>').html(tipo_identificacion_peticionario_options);
				options_html.find('.bs-title-option').remove();
				var tipo = $(this).val();
				
				$('label[for="pais_afectado"] i').attr('data-original-title', 'Seleccione el país en el que reside');
				$('label[for="departamento_afectado"] i').attr('data-original-title', 'Seleccione el departamento en el que reside');
				$('label[for="ciudad_afectado"] i').attr('data-original-title', 'Seleccione el municipio en el que reside');
				$('label[for="direccion"] i').attr('data-original-title', 'Digite la dirección de su residencia');

				var label_correo = '* Correo electrónico <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su correo electrónico"></i>';
				switch(tipo){
					case '1':
						$('div[data-juridico]').hide();
						$('div[data-anonimo]').hide();
						$('div[data-natural]').show();
						$('select[name="tipo_identificacion"]').html(options_html.html());
						$('select[name="tipo_identificacion"]').selectpicker('refresh');
						$('select[name="pais_afectado"]').trigger('change');
						$('select[name="pais_representante"]').trigger('change');
						$('div[data-anonimo]').removeClass('col-md-12').addClass('col-md-3');
						$('#correo').attr('data-required', 'true');
					break;
					case '2':
						$('div[data-natural]').hide();
						$('div[data-anonimo]').hide();
						$('div[data-juridico]').show();
						options_html.html('<option value="4">Nit</option>');
						$('select[name="tipo_identificacion"]').html(options_html.html());
						$('select[name="tipo_identificacion"]').selectpicker('refresh');
						$('select[name="pais_afectado"]').trigger('change');
						$('select[name="pais_representante"]').trigger('change');
						$('div[data-anonimo]').removeClass('col-md-12').addClass('col-md-3');

						$('label[for="pais_afectado"] i').attr('data-original-title', 'Seleccione el país de su domicilio');
						$('label[for="departamento_afectado"] i').attr('data-original-title', 'Seleccione el departamento de su domicilio	');
						$('label[for="ciudad_afectado"] i').attr('data-original-title', 'Seleccione el municipio de su domicilio');
						$('label[for="direccion"] i').attr('data-original-title', 'Digite la dirección de su domicilio');
						$('#correo').attr('data-required', 'true');
					break;
					case '3':
						$('div[data-natural]').hide();
						$('div[data-juridico]').hide();
						$('div[data-anonimo]').show();
						label_correo = 'Correo electrónico <i class="bi bi-question-circle" data-toggle="tooltip" data-placement="top" title="Digite su correo electrónico"></i>';

						$('div[data-anonimo]').removeClass('col-md-3').addClass('col-md-12');
						$('#correo').removeAttr('data-required');
					break;
				}

				$('label[for="correo"]').html(label_correo);
				$('[data-toggle="tooltip"]').tooltip();
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

			$('#departamento_afectado').on('change', function(e) {
				$('#ciudad_bar').show();
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
						$('#ciudad_bar').hide();
					}
				})
			});
			
			$('#departamento_representante').on('change', function(e) {
				$('#representante_ciudad_bar').show();
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

						$('#ciudad_representante').html(options);
						$('#ciudad_representante').selectpicker('refresh');
						$('#representante_ciudad_bar').hide();
					}
				})
			});

			
			$('#tipo_identificacion').on('change', function(e) {
				var tipo_identificacion = $(this).val();
				$('#id').val('');

				if(tipo_identificacion == 3)
				{
					$('#id').attr('type', 'text');
				} else {
					$('#id').attr('type', 'number');
				}
			});

			
			$('#representante_tipo_identificacion').on('change', function(e) {
				var representante_tipo_identificacion = $(this).val();
				$('#representante_id').val('');

				if(representante_tipo_identificacion == 3)
				{
					$('#representante_id').attr('type', 'text');
				} else {
					$('#representante_id').attr('type', 'number');
				}
			});

			$('#tipo_entidad').on('change', function(e) {
				var request = $.post(
					'solicitudes_ajax.php',
					{
						servicio: 'entidades',
						id_tipo: $(this).val()
					},
					'json'
				);

				request.done(function(res) {
					var options = '';

					if(res) {
						$.each(res, function(i, e) {
							options += '<option value="'+e.ENT_ID+'">'+e.ENT_NOMB+'</option>'
						});

						$('#entidad').html(options);
						$('#entidad').selectpicker('refresh');
					}
				});
			});

			$('#form-solicitud').on('submit', function(e) {
				var errors = 0;
				console.log('submit');

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

				if(!isEmail($('#correo').val()) && $('#correo').is(':visible') && $('#correo').attr('data-required'))
				{
					$('#correo').removeClass('is-valid');
					$('#correo').addClass('is-invalid');
					errors ++;
				} else {
					$('#correo').removeClass('is-invalid');
					$('#correo').addClass('is-valid');
				}

				if(!isEmail($('#representante_correo').val()) && $('#representante_correo').is(':visible') && $('#correo').attr('data-required'))
				{
					$('#representante_correo').removeClass('is-valid');
					$('#representante_correo').addClass('is-invalid');
					errors ++;
				} else {
					$('#representante_correo').removeClass('is-invalid');
					$('#representante_correo').addClass('is-valid');
				}

				$('#adjuntosSubidos').val(JSON.stringify(fileNamesTmpDir));

				if(grecaptcha.getResponse() == '')
				{
					$('.g-recaptcha').css('border', '1px solid #f00');
				} else {
					$('.g-recaptcha').css('border', 'none');
				}

				console.log(errors);

				if(errors > 0 || grecaptcha.getResponse() == '')
					e.preventDefault();
			});

			function createUploader() {
				uploader = new qq.FineUploader({
					element: document.getElementById('filelimit-fine-uploader'),
					request: {
						endpoint: 'qqUploadedFileXhr.class.php',
					},
					multiple: true,
					validation: {
						sizeLimit: 5*1024*1024// 5.0MB = 5 * 1024 kb * 1024 bytes
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