<-{assign var="gruposHtml" value='
		<tr>

			<td class="toogletd">

				<a href="javascript:void(0);" class="button-icon" data-tipo="grupos" >
					<i class="fa fa-minus"></i>
				</a>

				<a href="javascript:void(0);" class="button-icon" data-tipo="grupos">
					<i class="fa fa-save"></i>
				</a>

			</td>

			<td class="hasinput">
				<label class="input">
					<input type="text" name="nombre" value="" required>
				</label>
			</td>

			<td class="hasinput">
				<label name="" class="input">
					<input type="text" name="descripcion" value="" required>
				</label>
			</td>

		</tr>
'}->



<!DOCTYPE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link href="../../dist/css/select2.min.css" rel="stylesheet" />
<script src="../../dist/js/select2.min.js"></script>
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title>Administración de usuarios</title>

</head>

<body>
<br>
<br>

<article class="col-sm-12 col-md-12 col-lg-12">
	<!-- widget content -->

<div class="well">
	<button class="close" data-dismiss="alert">
		×
	</button>
	<h1 class="semi-bold"> Administración de usuarios y permisos </h1>
	<div class="well">
	<div class="widget-body no-padding   smart-form">
		<FORM action="index.php" method="POST" >
				<label class="select">
					<select data-tipo="dependencias"  id="grupo_dependencias" name="grupo_adm"
									class="custom-scroll" onchange="submit();">
						<option value="">-- Seleccione una Opción --</option>
						<-{foreach item=i from=$dependencias}->
							<option value="<-{$i.DEPE_CODI}->" <-{if $grupo_adm == $i.DEPE_CODI}->selected<-{/if}-> > <-{$i.DEPE_NOMB}-> </option>
						<-{/foreach}->
					</select>

				</label>
		</FORM>
		<div class="widget-body-toolbar"></div>
</div>
	<p>ss
		- El siguiente panel nos muestra las distintas opciones para realizar las relaciones entre usuarios y
		permisos mediante la utilización de roles.
	</p>

	<p>
		Un rol de seguridad es una función del trabajo que identifica las tareas que puede realizar el
		usuario y los recursos a los que tiene acceso. Todas las autorizaciones en se realizan en estos roles.
		La navegación de la interfaz web se filtra en función del rol de seguridad asignado del usuario.
	</p>

	<p>
		Los roles de seguridad son una función del control de accesos que siempre está habilitada.
		Se debe asignar un usuario a un rol de seguridad con el permiso básico para iniciar la sesión.
	</p>

	<p>
		Los administradores del sistema tienen acceso a toda la interfaz web. Configuran las nuevas
		cuentas de usuario y pueden asignar controles de accesos en el perfil de cuenta de usuario.
		La información de cuenta de cada usuario debe incluir información de contacto, un nombre de
		usuario y contraseña, un grupo de acceso predeterminado y un rol de seguridad asignado.
	</p>

	<p>
		Además de asignar roles de seguridad a un usuario determinado, también se le puede establecer
		como superusuario. Un superusuario es alguien que no tiene ningún control de accesos y que
		puede ver todos los recursos y realizar acciones en cualquier elemento del modelo de datos.
	</p>

</div>


<div class="alert alert-success fade in alertFixed" style="display: none;">
	<i class="fa-fw fa fa-check"></i>
	<strong id="alert-success" >Guardado</strong>
</div>

<div class="alert alert-danger fade in alertFixed" style="display: none;">
	<i class="fa-fw fa fa-times"></i>
	<strong id="alert-danger" >Error! </strong>
</div>


<div id="accordion"> <!-- ACORDION -->

<-{if $Perm_solo_usuario eq 0}->
<div><!-- Roles -->
	<h4><strong>Roles</strong></h4>

	<div class="padding-10">

		<div class="row">
			<div class="col-sm-12">

				<div class="well">
					<button class="close" data-dismiss="alert">
						×
					</button>
					<p>
						Puede crear y asignar roles de seguridad en función del área de responsabilidad o la
						autoridad de aprobación de cambios  en el sistema. Tiene una  asignación de roles flexibles.
						Cada  rol de seguridad proporciona acceso a un conjunto de
						recursos específico. Puede asignar el rol de administrador del sistema a un usuario para
						proporcionar acceso a todos los recursos, o asignar uno o varios roles de seguridad en
						función de la responsabilidad del trabajo.  Estos roles de seguridad,
						que se pueden personalizar, representan un conjunto de permisos básicos.
					</p>

				</div>


			</div>
		</div>


		<!-- Widget ID (each widget will need unique ID)-->
		<div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">
			<header></header>
			<!-- widget div-->
			<div>

				<!-- widget edit box -->
				<div class="jarviswidget-editbox">
					<!-- This area used as dropdown edit box -->
				</div>
				<!-- end widget edit box -->

				<!-- widget content -->
				<div class="widget-body no-padding">
					<div class="widget-body-toolbar"></div>
					<table id="dt_basic" class="table table-striped table-bordered table-hover smart-form" width="100%">
						<thead>
						<tr>
							<th style="width: 35px;">
								<a href="javascript:void(0);" id="xdt_basic" class="btn btn-sm">
									<i class="fa fa-plus"></i>
								</a>
							</th>
							<th>Nombre</th>
							<th>Descripción</th>
						</tr>
						</thead>
						<tbody id="bdt_basic">
						<-{if count($grupos) eq 0}->
							<-{$gruposHtml}->
						<-{else}->
							<-{foreach item=grupo from=$grupos}->
								<tr>
									<td class="toogletd">

										<a href="#" data-tipo="grupos" data-id="<-{$grupo.ID}->"
										   class="button-icon" >
											<i class="fa fa-minus"></i>
										</a>

										<a href="javascript:void(0);" data-tipo="grupos" data-id="<-{$grupo.ID}->"
										   class="bu$resultado['estado']tton-icon">
											<i class="fa fa-save "></i>
										</a>

									</td>

									<td class="hasinput">
										<label class="input">
											<input type="text" name="nombre" value="<-{$grupo.NOMBRE}->">
										</label>
									</td>

									<td class="hasinput">
										<label name="" class="input">
											<input type="text" name="descripcion" value="<-{$grupo.DESCRIPCION}->">
										</label>
									</td>

								</tr>
							<-{/foreach}->
						<-{/if}->
						</tbody>
					</table>
				</div>
				<!-- end widget content -->
			</div>
			<!-- end widget div -->
		</div>
		<!-- WIDGET END -->
	</div>
</div><!-- Fin Roles -->
<-{/if}->
<-{if $Perm_solo_usuario eq 0}->
<div> <!-- Permisos -->

<h4><strong>Permisos</strong></h4>

<div class="padding-10">
<!-- Widget ID (each widget will need unique ID)-->
<div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">
<header></header>
<!-- widget div-->
<div>

<!-- widget edit box -->
<div class="jarviswidget-editbox">
	<!-- This area used as dropdown edit box -->
</div>
<!-- end widget edit box -->

<!-- widget content -->
<div class="widget-body no-padding smart-form">
	<table id="dt_basic2">
		<thead>
		<tr>
			<th style="width: 35px;">
				<a href="javascript:void(0);" id="xdt_basic2" class="btn btn-sm"><i class="fa
				fa-plus"></i></a>
			</th>
			<th>Nombre</th>
			<th>Descripci&oacute;n</th>
			<th>Crud</th>
			<th>Grupo</th>
		</tr>
		</thead>
		<tbody id="bdt_basic2">
		<-{if count($permisos) eq 0}->
			<tr>

				<td class="toogletd">

					<a href="javascript:void(0);" data-tipo="permisos" data-id="<-{$item.ID}->"
					   class="button-icon">
						<i class="fa fa-save "></i>
					</a>

				</td>

				<td class="hasinput">
					<label class="input">
						<input type="text" name="nombre" value="<-{$item.NOMBRE}->">
					</label>
				</td>

				<td class="hasinput">
					<label name="" class="input">
						<input type="text" name="descripcion" value="<-{$item.DESCRIPCION}->">
					</label>
				</td>

				<td class="hasinput">
					<label class="select">
						<select class="input-sm" name="crud">
							<option value="">-- Seleccione una Opción --</option>
							<-{foreach item=i from=$crud}->
								<option value="<-{$i.ID}->">
									<-{$i.NOMBRE}->
								</option>
							<-{/foreach}->
						</select> <i></i>
					</label>
				</td>

				<td class="hasinput">
					<label class="select select-multiple">
						<select class="custom-scroll" multiple name="grupo">
							<option value="">-- Seleccione una Opción --</option>
							<-{foreach item=i from=$grupos}->
								<-{if $item.AUTG_ID eq $i.ID}->
									<option value="<-{$i.ID}->" selected>
										<-{$i.NOMBRE}->
									</option>
								<-{else}->
									<option value="<-{$i.ID}->">
										<-{$i.NOMBRE}->
									</option>
								<-{/if}->
							<-{/foreach}->
						</select> <i></i>
					</label>
				</td>

			</tr>
		<-{else}->

			<-{foreach item=item from=$permisos}->
				<tr>

					<td class="toogletd">

						<a href="javascript:void(0);" data-tipo="permisos" data-id="<-{$item.ID}->"
						   class="button-icon">
							<i class="fa fa-save "></i>
						</a>

					</td>

					<td class="hasinput">
						<div class="hide"><-{$item.NOMBRE}-></div>
						<label class="input">
							<input type="text" name="nombre" value="<-{$item.NOMBRE}->">
						</label>
					</td>

					<td class="hasinput">
						<div class="hide"><-{$item.DESCRIPCION}-></div>
						<label name="" class="input">
							<input type="text" name="descripcion" value="<-{$item.DESCRIPCION}->">
						</label>
					</td>

					<td class="hasinput">
						<label class="select">
							<select class="input-sm" name="crud">
								<option value="">-- Seleccione una Opción --</option>
								<-{foreach item=i from=$crud}->
									<-{if $item.CRUD eq $i.ID}->
										<option value="<-{$i.ID}->" selected>
											<-{$i.NOMBRE}->
										</option>
									<-{else}->
										<option value="<-{$i.ID}->">
											<-{$i.NOMBRE}->
										</option>
									<-{/if}->
								<-{/foreach}->
							</select> <i></i>
						</label>
					</td>

					<td class="hasinput">
						<label class="select select-multiple">

							<select class="custom-scroll select2" multiple name="grupo">
								<option value="">-- Seleccione una Opción --</option>
								<-{foreach item=i from=$grupos}->
									<option value="<-{$i.ID}->"
										<-{foreach item=j from=$item.AUTG_ID}->
											<-{if $j eq $i.ID}->
												selected
												{break}
											<-{/if}->
										<-{/foreach}->>
										<-{$i.NOMBRE}->
									</option>
								<-{/foreach}->
							</select> <i></i>
						</label>
					</td>

				</tr>
			<-{/foreach}->
		<-{/if}->
		</tbody>

	</table>
	<!-- pager -->
	<div class="pager2">
		<div class="btn btn-sm"><span class="fa fa-fast-backward
						txt-color-blueLight first"></span></div>
		<div class="btn btn-sm"><span class="fa fa-backward prev
						txt-color-blueLight"></span></div>
		<span class="pagedisplay"></span> <!-- this can be any element, including an input -->
		<div class="btn btn-sm"><span class="fa fa-forward next
						txt-color-blueLight"></span></div>
		<div class="btn btn-sm"><span class="fa fa-fast-forward
						txt-color-blueLight last"></span></div>

		<select class="pagesize" title="Select page size">
			<option selected="selected" value="10">10</option>
			<option value="20">20</option>
			<option value="30">30</option>
			<option value="40">40</option>
		</select>

		<select class="gotoPage" title="Select page number"></select>
	</div>

</div>
<!-- end widget content -->

</div>
<!-- end widget div -->

</div>
<!-- WIDGET END -->
</div>
</div> <!-- Fin Permisos -->
<-{/if}->

<div> <!-- Usuarios -->
	<h4><strong>Usuarios</strong></h4>

	<div class="padding-10">
		<!-- Widget ID (each widget will need unique ID)-->
		<div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">
			<header></header>
			<!-- widget div-->
			<div>

				<!-- widget edit box -->
				<div class="jarviswidget-editbox">
					<!-- This area used as dropdown edit box -->
				</div>
				<!-- end widget edit box -->

				<!-- widget content -->
				<div class="widget-body no-padding smart-form">
					<div class="widget-body-toolbar"></div>

					<table id="dt_basic3" class="table table-striped table-bordered table-hover smart-form"
					       width="100%">
						<thead>
						<tr>
							<th style="width: 35px;">
								<-{if $Less_edita_usuario eq 0}->
								<a href="javascript:void(0);" id="xdt_basic3" class="btn btn-sm"><i
											class="fa fa-plus"></i></a>
								<-{/if}->
							</th>
							<th>Usuario</th>
							<th>Nuevo</th>
							<th>Nombres</th>
							<th>Documento</th>
							<th>Dependencias</th>
							<th>Correo</th>
							<th>Estado</th>
							<th>Nivel de seguridad</th>
							<th>LDAP user</th>
							<th>Ultima conexion</th>
						</tr>
						</thead>
						<tbody id="bdt_basic3">
						<-{if count($usuarios) eq 0}->
							<tr>

								<td class="toogletd">
									<a href="javascript:void(0);" class="button-icon" data-tipo="usuarios">
										<i class="fa fa-save"></i>
									</a>
								</td>

								<td class="hasinput">
									<label class="input">
										<input type="text" name="usuarios" value="">
									</label>
								</td>


								<td class="hasinput">
									<label class="select">
										<select class="custom-scrollselectpicker" name="nuevo">
											<option value=""> Seleccione una opción </option>
											<option value="0"> Actual </option>
											<option value="1"> Nuevo </option>
										</select> <i></i>
									</label>
								</td>

								<td class="hasinput">
									<label class="input">
										<input type="text" name="nombres" value="">
									</label>
								</td>

								<td class="hasinput">
									<label class="input">
										<input type="text" name="documento" value="">
									</label>
								</td>

								<td class="hasinput">
									<label class="select">
										<select class="custom-scrollselectpicker" name="dependencia"
										        readonly/>
											<option value="">-- Seleccione una Opción --</option>
											<-{foreach item=i from=$dependencias}->
												<option value="<-{$i.DEPE_CODI}->">
													<-{$i.DEPE_NOMB}->
												</option>
											<-{/foreach}->
										</select> <i></i>
									</label>
								</td>


								<td class="hasinput">
									<label class="input">
										<input type="text" name="correo" value="">
									</label>
								</td>

								<td class="hasinput">
									<label class="select">
										<select class="custom-scrollselectpicker" name="estado">
											<option value="">-- Seleccione una Opción --</option>
											<option value="0"> Inactivo </option>
											<option value="1"> Activo </option>
										</select> <i></i>
									</label>
								</td>

								<td class="hasinput">
									<label class="select">
										<select class="custom-scrollselectpicker" name="nivel">

											<-{for $i=1 to 5}->
												<option value="<-{$i}->"
												<-{if $item.CODIGO_NIVEL eq $i}->
											        selected
												<-{/if}->><-{$i}->
											<-{/for}->


										</select> <i></i>
									</label>
								</td>

								<td class="hasinput">
									<label class="input">
										<select name="ldap_login">
											<option value="null"></option>
											<option value="0"
												<-{if $item.AUTH_LDAP eq '0'}->
													selected
												<-{/if}->> Inactivo
											</option>

											<option value="1"
												<-{if $item.AUTH_LDAP eq '1'}->
													selected
												<-{/if}->> Activo
											</option>
										</select>
										<div class="hide"><-{$item.AUTH_LDAP}-></div>
									</label>

								</td>


								<td class="hasinput">
								
										<input type="text" name="conexion" value="">
								</td>

							</tr>
						<-{else}->
							<-{foreach item=item from=$usuarios}->
								<tr>
									<td class="toogletd">

									<-{if $Less_edita_usuario eq 0}->
										<a href="javascript:void(0);" data-tipo="usuarios" data-id="<-{$item.ID}->"
										   class="button-icon">
											<i class="fa fa-save "></i>
										</a>
									<-{/if}->

										<a href="javascript:void(0);" data-id="<-{$item.ID}->"
										   class="button-icon listusua"> <i class="fa fa-list"></i>
										</a>

									</td>

									

									<td class="hasinput">
										<label class="input">
											<input type="text" name="usuarios" value="<-{$item.USUARIO}->" readonly>
											<div class="hide"><-{$item.USUARIO}-></div>
										</label>
									</td>

									
									<td class="hasinput">
										<label class="select">
											<select class="custom-scrollselectpicker" name="nuevo">
												<option value="">-- Seleccione una Opción --</option>
												<option value="1"
													<-{if $item.NUEVO eq 1}->
														selected
													<-{/if}-> > actual
												</option>

												<option value="0"
													<-{if $item.NUEVO eq 0}->
														selected
													<-{/if}->> Nuevo
											</option>
											</select> <i></i>
										</label>
									</td>

									<td class="hasinput">
										<label class="input">
											<input type="text" name="nombres" value="<-{$item.NOMBRES}->">
											<div class="hide"><-{$item.NOMBRES}-></div>
										</label>
									</td>

									<td class="hasinput">
										<label class="input">
											<input type="text" name="documento" value="<-{$item.DOCUMENTO}->" disabled>
											<div class="hide"><-{$item.DOCUMENTO}-></div>
										</label>
									</td>

									<td class="hasinput">
									   <label class="select">
									      <select class="custom-scroll" name="dependencia" readonly/>
									         <option value="">-- Seleccione una Opción --</option>
									         <-{foreach item=i from=$dependencias}->
												 <-{if $item.DEPECODI eq $i.DEPE_CODI}->
													 <option value="<-{$i.DEPE_CODI}->" selected>
													    <-{$i.DEPE_NOMB}->(Actual)
													 </option>
													 {break}
												 <-{else}->
													 <option value="<-{$i.DEPE_CODI}->">
														<-{$i.DEPE_NOMB}->
													 </option>
												 <-{/if}->
									         <-{/foreach}->
									      </select> <i></i>
									   </label>
									 </td>
									<td class="hasinput">
										<label class="input">
											<input type="text" name="correo" value="<-{$item.CORREO}->">
											<div class="hide"><-{$item.CORREO}-></div>
										</label>
									</td>

									<td class="hasinput">
										<label class="select">
											<select class="custom-scrollselectpicker" name="estado">

												<option value="">-- Seleccione una Opción --</option>
												<option value="0"
													<-{if $item.ESTADO eq 0}->
												        selected
													<-{/if}->> Inactivo
												</option>

												<option value="1"
													<-{if $item.ESTADO eq 1}->
												        selected
													<-{/if}->> Activo
												</option>


											</select> <i></i>
										</label>
									</td>
									<td class="hasinput">
										<label class="select">
											<select class="custom-scrollselectpicker" name="nivel">

												<-{for $i=1 to 5}->
													<option value="<-{$i}->"
													<-{if $item.CODIGO_NIVEL eq $i}->
												        selected
													<-{/if}->><-{$i}->
												<-{/for}->


											</select> <i></i>
										</label>
									</td>
									<td class="hasinput">
										<label class="input">
											<select name="ldap_login">
												<option value="null"></option>
												<option value="0"
													<-{if $item.AUTH_LDAP eq '0'}->
												        selected
													<-{/if}->> Inactivo
												</option>

												<option value="1"
													<-{if $item.AUTH_LDAP eq '1'}->
												        selected
													<-{/if}->> Activo
												</option>
											</select>
											<div class="hide"><-{$item.AUTH_LDAP}-></div>
										</label>
									</td>
									<td class="hasinput">
										<label class="input">
											<-{$item.CONEXION}->
										</label>
									</td>

								</tr>
							<-{/foreach}->
						<-{/if}->
						</tbody>
					</table>

				</div>
				<!-- end widget content -->

			</div>
			<!-- end widget div -->

		</div>
		<!-- WIDGET END -->
	</div>
</div><!-- Fin Usuarios -->

<-{if $Perm_solo_usuario eq 0}->
<div> <!-- Perfiles -->
	<h4><strong>Perfiles</strong></h4>

	<div class="padding-10">
		<!-- Widget ID (each widget will need unique ID)-->
		<div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">
			<header></header>
			<!-- widget div-->
			<div>

				<!-- widget edit box -->
				<div class="jarviswidget-editbox">
					<!-- This area used as dropdown edit box -->
				</div>
				<!-- end widget edit box -->

				<!-- widget content -->
				<div class="widget-body no-padding smart-form">
 					<-{if $Less_edita_profile eq 0}->
									<label class="select">
										<select data-tipo="membresias"  id="grupo_membresias" name="grupo"
										        class="custom-scroll select2">
											<option value="">-- Seleccione una Opción --</option>
											<-{foreach item=i from=$grupos}->
												<option value="<-{$i.ID}->"> <-{$i.NOMBRE}-> </option>
											<-{/foreach}->
										</select>
									</label>
					<-{/if}->
										<table id="datatable"  width="100%">

											<colgroup>
												<col/>
												<col/>
												<col/>
												<col/>
											</colgroup>

											<thead>
												<tr>
													<th>ID</th>
													<th>Roles</th>
													<th>Usuario</th>
													<th>Dependencia</th>
													<th>Nombre</th>
												</tr>
											</thead>
											<tbody id="contentable">
											<-{foreach item=i from=$usuarios}->
												<-{if $i.ESTADO eq "1"}->
													<tr>
														<td> <-{if $Less_edita_profile eq 0}->
															<label class="checkbox">
																<input data-tipo="membresias"  type="checkbox"
																	   name="<-{$i.ID}->" value="<-{$i.ID}->">
																<i></i>
															</label>
														</td> <-{/if}->
														<td><-{$i.ROLES}-></td>
														<td><-{$i.USUARIO}-></td>
														<td><-{$i.DEPENDENCIA}-></td>
														<td><-{$i.NOMBRES}-> <-{if $i.CODIGO_USUARIO eq "1"}-> ( Jefe ) <-{/if}-></td>
													</tr>
												<-{/if}->
											<-{/foreach}->
											</tbody>
										</table>

					<!-- pager -->
					<div class="pager">
						<div class="btn btn-sm"><span class="fa fa-fast-backward
						txt-color-blueLight first"></span></div>
						<div class="btn btn-sm"><span class="fa fa-backward prev
						txt-color-blueLight"></span></div>
						<span class="pagedisplay"></span> <!-- this can be any element, including an input -->
						<div class="btn btn-sm"><span class="fa fa-forward next
						txt-color-blueLight"></span></div>
						<div class="btn btn-sm"><span class="fa fa-fast-forward
						txt-color-blueLight last"></span></div>

						<select class="pagesize" title="Select page size">
							<option selected="selected" value="10">10</option>
							<option value="20">20</option>
							<option value="30">30</option>
							<option value="40">40</option>
						</select>

						<select class="gotoPage" title="Select page number"></select>
					</div>


				</div>
				<!-- end widget content -->

			</div>
			<!-- end widget div -->

		</div>
		<!-- WIDGET END -->
	</div>
</div><!-- Fin Perfiles -->
<-{/if}->

</div> <!-- FIN ACORDION -->

</article>
<a href="#" data-toggle="modal" data-target="#confirm-delete" id="show_toggle"></a>
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Borrar el Registro
            </div>
            <div class="modal-body">
                Seguro que desea borrar este registro ?
                Esto puede afectar a la funcionalidad de los roles en todo el sistema
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="ocultar_modal" data-dismiss="modal">Cancelar</button>
                <a href="javascript:void(0);" id="atribute_confirm_delete" class="btn btn-danger danger">Borrar</a>
            </div>
        </div>
    </div>
</div>


</body>


<!-- Plantilla para la creación de los grupos.
Elemento necesario para duplicar los campos
de inserción para los nuevos registros. -->
<script id="plantillaGrupos" type="text/html">
	<-{$gruposHtml}->
</script>


<!-- Plantilla para la creación de los permisos.
Elemento necesario para duplicar los campos
de inserción para los nuevos registros. -->
<script id="plantillaPermisos" type="text/html">
	<tr>
		<td class="toogletd">
			<a href="javascript:void(0);" data-tipo="permisos" data-id=""
			   class="button-icon">
				<i class="fa fa-save "></i>
			</a>
		</td>

		<td class="hasinput">
			<label class="input">
				<input type="text" name="nombre" value="">
			</label>
		</td>

	 	<td class="hasinput">
			<label name="" class="input">
				<input type="text" name="descripcion" value="">
			</label>
		</td>

		<td class="hasinput">
			<label class="select">
				<select class="input-sm" name="crud">
					<option value="">-- Seleccione una Opción --</option>
					<-{foreach item=i from=$crud}->
						<option value="<-{$i.ID}->">
							<-{$i.NOMBRE}->
						</option>
					<-{/foreach}->
				</select> <i></i>
			</label>
		</td>

		<td class="hasinput">
			<div>
				<label class="select select-multiple">
					<select class="custom-scroll select2"  multiple name="grupo">
						<option value="">-- Seleccione una Opción --</option>
						<-{foreach item=i from=$grupos}->
						<option value="<-{$i.ID}->">
							<-{$i.NOMBRE}->
						</option>
						<-{/foreach}->
					</select> <i></i>
				</label>
			</div>
		</td>

	</tr>
</script>


<!-- Plantilla para la creación de los Usuarios.
Elemento necesario para duplicar los campos
de inserción para los nuevos registros. -->
<script id="plantillaUsuarios" type="text/html">
	<tr>
		<td class="toogletd">

			<a href="javascript:void(0);" class="button-icon" data-tipo="usuarios">
				<i class="fa fa-save"></i>
			</a>

		</td>

		<td class="hasinput">
			<label class="input">
				<input type="text" name="usuarios" value="">
			</label>
		</td>

		<td class="hasinput">
			<label class="select">
				<select class="custom-scrollselectpicker" name="nuevo">
					<option value=""> Seleccione una opción </option>
					<option value="0"> Actual </option>
					<option value="1"> Nuevo </option>
				</select> <i></i>
			</label>
		</td>

		<td class="hasinput">
			<label class="input">
				<input type="text" name="nombres" value="">
			</label>
		</td>

		<td class="hasinput">
			<label class="input">
				<input type="text" name="documento" value="">
			</label>
		</td>


		<td class="hasinput">
			<label class="select">
				<select class="custom-scrollselectpicker" name="dependencia">
					<option value="">-- Seleccione una Opción --</option>
					<-{foreach item=i from=$dependencias}->
						<option value="<-{$i.DEPE_CODI}->">
							<-{$i.DEPE_NOMB}->
						</option>
					<-{/foreach}->
					</select> <i></i>
			</label>
		</td>


		<td class="hasinput">
			<label class="input">
				<input type="text" name="correo" value="">
			</label>
		</td>

		<td class="hasinput">
			<label class="select">
				<select class="custom-scrollselectpicker" name="estado">
					<option value="">-- Seleccione una Opción --</option>
					<option value="0"> Inactivo </option>
					<option value="1"> Activo </option>
				</select> <i></i>
			</label>
		</td>
        <td class="hasinput">
                        <label class="select">
                                <select class="custom-scrollselectpicker" name="nivel">
                                        <option value="">-- Seleccione una Opción --</option>
                                        <option value="1"> 1 </option>
                                        <option value="2"> 2 </option>
                                        <option value="3"> 3 </option>
                                        <option value="4"> 4 </option>
                                        <option value="5"> 5 </option>
                                </select> <i></i>
                        </label>
                </td>
		<td class="hasinput">
		    <label class="input">
		    	<select name="ldap_login">
					<option value="null">-- Seleccione una Opción --</option>
					<option value="0">Inactivo</option>
					<option value="1">Activo</option>
				</select>
		    </label>
		</td>

	</tr>
</script>


<script type="text/javascript">
$( document ).ready(function() {
		// DO NOT REMOVE : GLOBAL FUNCTIONS!
		pageSetUp();

		$('.listusua').on('click',function(event){
			var iduser = $(this).data('id');

			$('<div>').dialog({
				modal: true,
				open: function (){
					$(this).load('dialogpermisos.php?id=' + iduser);
				},
				title: 'Permisos del usuario',
				width: 600,
				position: {my: "top", at: "top", of: window }
			})


		});


		// PAGE RELATED SCRIPTS
		loadDataTableScripts();
		function loadDataTableScripts() {
			loadScript("../../js/plugin/datatables/jquery.dataTables-cust.min.js", dt_2);

			function dt_2() {
				loadScript("../../js/plugin/datatables/DT_bootstrap.js", runDataTables);
			}
		}

		function runDataTables() {
			$('#dt_basic, #dt_basic3').dataTable();
		}

		$("#dt_basic2").tablesorter({
			widgets: ["filter"],
			widgetOptions : {
				filter_reset : '.reset'
			}
		}).tablesorterPager({
			// target the pager markup - see the HTML block below
			container: $(".pager2"),
			// use this url format "http:/mydatabase.com?page={page}&size={size}"
			ajaxUrl: null
		});

		$("#datatable").tablesorter({
			widgets: ["filter"],
			widgetOptions : {
				filter_reset : '.reset'
			}
		}).tablesorterPager({
			// target the pager markup - see the HTML block below
			container: $(".pager"),
			// use this url format "http:/mydatabase.com?page={page}&size={size}"
			ajaxUrl: null
		});







		//Validacion de los campos del formulario
		function validarCampos(datos){
			var res = datos.split('&');
			var test = true;
			$.each(res, function(index, value) {
				var dato = value.split('=')[1];

				if((dato == "" || dato.length == 0 || dato == undefined) && (value.split('=')[0] != 'id')  && (value.split('=')[0] != 'ldap_login')){
					$('.alert-danger').show().delay(3000).fadeOut();
					test = false;
				}
			});
			return test;
		}

		/*
		 * ACCORDION
		 * jquery accordion
		 */
		var accordionIcons = {
			header: "fa fa-plus",    // custom icon class
			activeHeader: "fa fa-arrow-up" // custom icon class
		};

		$("#accordion").accordion({
			autoHeight: false,
			heightStyle: "content",
			collapsible: true,
			animate: 300,
			header: "h4",
			icons: { "header": "fa fa-plus", "activeHeader": "fa fa-arrow-up"},
			active: 3
		});





		//agregar elementos
		$('#xdt_basic2, #xdt_basic, #xdt_basic3, #xdt_basic4').click(function (event) {
			var nomPlus = 'b' + $(this).attr('id').substring(1);
			switch (nomPlus) {
				case 'bdt_basic':
					//Plantillas y clonar elementos Grupos
					var plaGrup = $('#plantillaGrupos').clone();
					$('#bdt_basic').prepend($(plaGrup).html());
					break;

				case 'bdt_basic2':
					//Plantillas y clonar elementos Permisos
					var plaPerm = $('#plantillaPermisos').clone();
					$('#bdt_basic2').prepend($(plaPerm).html());
					break;

				case 'bdt_basic3':
					//Plantillas y clonar elementos Usuarios
					var plaUsua = $('#plantillaUsuarios').clone();
					$('#bdt_basic3').prepend($(plaUsua).html());
					break;
			}

			event.stopPropagation();

		});

		//Eliminar un campo de la selección
		$('body').on('click', '.fa-minus', function (event) {
			var tipo  = $(this).parent().data('tipo');
			var id    = $(this).parent().data('id');
			var datos = 'accion=borrar&tipo=' + tipo + '&id=' + id;


			$('#atribute_confirm_delete').attr('data-id',id);
			$('#atribute_confirm_delete').attr('data-tipo',tipo);
			$('#atribute_confirm_delete').attr('data-datos',datos);

		$($(this).closest('tr')).addClass('deleteme');
		$("#show_toggle").click();
		});

		$('#atribute_confirm_delete').click(function(event) {

			var id = $('#atribute_confirm_delete').attr('data-id');
			var tipo = $('#atribute_confirm_delete').attr('data-tipo');
			var datos = $('#atribute_confirm_delete').attr('data-datos');

			//Valida si existe algun campo en blanco
			if(!validarCampos(datos)){
				return false;
			}

			if (id == undefined) {
				$($(this).closest('tr')).remove();
				return;
			}

			$.post("ajaxPermisos.php", datos).done(function (data) {
				if (data['estado'] == 1) {
					$($('.deleteme').closest('tr')).remove();
					$('.alert-success').show().delay(3000).fadeOut();
				} else {
					$('.alert-danger').show().delay(3000).fadeOut();
				}
			})

			$('#ocultar_modal').click();
			});
		//Agregar o editar
		$('body').on('click', '.fa-save', function (event) {

			var tipo     = $(this).parent().data('tipo');
			var id       = $(this).parent().data('id');
			var datos    = 'accion=guardar&tipo=' + tipo;
			var boton    = $(this);
			var elemnttr = $(this).closest('tr');


			if (id !== undefined) {
				datos += '&id=' + id;
			} else {
				datos += '&id='
			}
			elemnttr.find('input').each(function (index) {

					var inpe = elemnttr.find('input')[index];
					var name = $(inpe).attr('name');
					var valu = $(inpe).val();
				if(name !== undefined && inpe !== undefined && valu !== undefined){
					datos += '&' + name + '=' + valu;
				}
			});
			console.log(datos)

			elemnttr.find('select').each(function (index) {
				var inpe = elemnttr.find('select')[index];
				var name = $(inpe).attr('name');
				var valu = $(inpe).val();
				datos += '&' + name + '=' + valu;
			});
			//Valida si existe algun campo en blanco
			if(!validarCampos(datos)){
				return false;
			}
			$.post("ajaxPermisos.php", datos).done(function (data) {
				if (data['estado'] == 1) {
					boton.closest('td').find('a').each(function (index) {
						$(this).attr("data-id", data['valor']);
					});

					if (id == undefined) {
						switch (tipo) {

							case 'grupos':
								var newdato = new Option(data['nombre'], data['valor']);
								$('#bdt_basic2').find("[name='grupo']").each(
										function(index,value){
											$(value).append(newdato);
										});
								$('#grupo_membresias').append(newdato);
								break;

							case 'usuarios':
								var deptext = $('*[data-id="'+ data['valor'] +'"]').closest( "tr" ).find('[name="dependencia"] ' +
												'option:selected')
										.text().replace(/\s/g, "");
									$('#contentable').append(
										"<tr role='row'>" +
											"<td>" +
												"<label class='checkbox'>" +
													"<input data-tipo='membresias' type='checkbox'" +
													"name='"+data['value']+"' value='" + data['value'] + "'>" +
													"<i></i>" +
												"</label>" +
											"</td>" +
											"<td>" + data['nombre'] +"</td>" +
											"<td>" + deptext + "</td>" +
											"<td>" + data['nombre'] + "</td>" +
										"</tr>");
								break;
						}
					}

					$('.alert-success').show().delay(3000).fadeOut();
				} else {
					$('.alert-danger').show().delay(3000).fadeOut();
				}
			});
		})

		$( "#grupo_dependencias" ).change(function () {
		 //var tableUsu = $('dt_basic3').dataTable();
			$("#dt_basic3_wrapper .form-control").val($("#grupo_dependencias option:selected").text().slice(0,-1)+"(Actual)");
$('#dt_basic3_wrapper .form-control').focus();
		});

		//Selector multiple para las membresias Grupos
	    //Cuando se cambia un grupo debe buscar los usuarios existente
		$("body").delegate( "#grupo_membresias","change",function () {
			var tipo  = $(this).data('tipo');
			var datos = 'accion=buscarUsuariosDelGrupo&' + $(this).attr('name') + '=' + $(this).val();
			console.log("delegate")
			$( ".form-control" ).each(function(index, value){$(value).val('');});
			$( "*:checkbox" ).each(function(index,value){$(value).prop('checked', false);})

			//Valida si existe algun campo en blanco
			if(!validarCampos(datos)){
				return false;
			}

			$.post("ajaxPermisos.php", datos).done(function (data) {

				if (data['estado'] == 1) {
					if(data['valor'].length > 0){
						$(data['valor']).each(function (index, value) {
							$(`input[name=${value}]`).prop('checked', true);
							console.log(`input[name=${value}]`);
						});
					}
				}
			});
		});

		//Selector multiple para las Membresias Usuarios
	    //Si se selecciona un usuario o se desmarca uno
		$( "input:checkbox" ).change(function () {

			var tipo   = $(this).data('tipo');
			var grupo  = $('#grupo_membresias').val();
			var idusua = $(this).val();
			var esusua = $(this).is(':checked');
			var datos  = 'accion=grabarUsuariosDelGrupo&usuario='
						+ idusua + '&estado='  + esusua + '&grupo=' + grupo;

			$.post("ajaxPermisos.php", datos).done(function (data) {
				if (data['estado'] == 1) {
					$(data['valor']).each(function (index) {
						$('#usuarios_membresias option[value=' + data['valor'][index] + ']').attr('selected', true);
					});
					$('#alert-success').html(data['valor']);
					$('.alert-success').show().delay(3000).fadeOut();
				}else{
					$('#alert-danger').html(data['valor']);
					$('.alert-danger').show().delay(3000).fadeOut();
				}
			});

		});

});
</script>
</html>
