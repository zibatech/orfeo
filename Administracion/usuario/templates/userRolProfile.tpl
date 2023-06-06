<!DOCTYPE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title>Administración de usuarios</title>
</head>
<script src="loadingoverlay.js"></script>
<link href="jquery.dataTables.css" rel="stylesheet">
	<link href="../../dist/css/pagination.custom.css" rel="stylesheet">

    <script src="../../bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="../../bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>

<style type="text/css">
	.nav-tabs{
		cursor:pointer;
	}
</style>
<body>
<br>
<article class="col-sm-12 col-md-12 col-lg-12">

	<ul class="nav nav-tabs">
	  <li class="active"><a referencia="#tabUser">USUARIOS</a></li>
	  <li><a referencia="#tabPerm">PERMISOS</a></li>
	  <li><a referencia="#tabRol">ROLES</a></li>
	  <li><a referencia="#tabAssig">ASIGNACION DE PERMISOS</a></li>
	</ul>
    <div id="wrapper" >
		<div id="tabUser" class="contenido">
			{* incluimos template para usuarios*} 
			{include 'usuarios.tpl'}
		</div>

		<div id="tabPerm" class="contenido">Permiso</div>

		<div id="tabRol" class="contenido">Rol</div>

		<div id="tabAssig" class="contenido">Asignacion</div>
	</div>
</article>

</body>
{**}
<script src="tableJson/Survey.table.js"></script>
<script src="Paginator.controller.js"></script>

<script type="text/javascript">
	$(document).on('ready',function(){
		$('.contenido').hide();
		$('.contenido:first').show();
		/*Cargar por primera vez los usuarios*/

		$('.nav-tabs li').click(function(){
			$('.nav-tabs li').removeClass('active');
			$(this).addClass('active');

			$('.contenido').hide();

			var contenido_activo = $(this).find('a').attr('referencia');
			$(contenido_activo).fadeIn(); 

		});

		//Aqui abajo todos los scrips necesarios para paginar, guardar y editar.



	});
</script>



