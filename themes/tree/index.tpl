<html lang="es">
  <head>
  <meta charset="utf-8">
  <title> ..:: <!--{$entidad}--> ::.</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="<?=$entidad?>">
  <!--Si existe un favicon especifico para la entidad su nombre debe de ser asi <entidad>.favicon.png,
       si no existe se toma el favicon por defecto-->
  <!-- Bootstrap core CSS -->
  <!-- font-awesome CSS -->
  <link href="./estilos/font-awesome.css" rel="stylesheet">
  <!-- Bootstrap core CSS -->
  <link href="./themes/tree/tree.css" rel="stylesheet">
  <link href="./estilos/bootstrap.css" rel="stylesheet">
  <style>
   .navbar-inverse {
       background-color: <!--{$colorFondo}-->;
       border-color: <!--{$colorFondo}-->;
   }

   .navbar-inverse .navbar-nav > li > a {
       color: #fff;
   }

  </style>
  <script type="text/javascript" src="./js/jquery.min.js"></script>
  <script type="text/javascript" src="./js/bootstrap.js"></script>
  <script type="text/javascript" src="./themes/tree/tree.js"></script>
  <script>
      function recapagi() {
          location.reload();
      }
  </script>
    </head>

    <body>
      <div id="wrapper">
      <!--{if $ambiente != "PRODUCCION" }-->
      <div style="position:absolute;background:red;left:38%;" align="center" ><b>..:: Ambiente de : <!--{$ambiente}--> ::..</b></div>
      <!--{/if}--> 

        <!-- Sidebar -->
      <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="overflow:auto;">
	  <div class="container-fluid">
              <!-- Brand and toggle get grouped for better mobile display -->
              <div class="navbar-header">              
		  <!--Si existe un logoEntidad especifico para la entidad su nombre debe de ser asi <entidad>.favicon.png, si no existe se toma el favicon por defecto-->
		  <!--<a class="navbar-brand"  onclick="recapagi()" href="#" alt="<?=$entidad_largo?>" title="<?=$entidad_largo?>"><?=$entidad?></a>-->
		  <a class="navbar-brand" align="center" onclick="recapagi()" href="#" alt="<!--{$entidad_largo}-->" title="<!--{$entidad_largo}-->"><img border=0 src="./img/favicon.png" width="55" height="28"></a>
	      </div>

	      <!-- Collect the nav links, forms, and other content for toggling -->
		  <ul class="treeview">
		      <!--{if $menuRadicacion == 1}-->
		      <li class="dropdown">
			  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><!--{$radicacion.nombre}--> <b class="caret"></b></a>
			  <ul class="">
			      <!--{foreach from=$radicacion.menu item=menu}-->
			      <!--{if $menu.subMenu == 0}-->
			      <li>
				  <a href=<!--{$menu.url}--> target='mainFrame' class="menu_princ"><!--{$menu.nombre}--></a>
			      </li>
			      <!--{elseif $menu.subMenu == 1}-->
			      <li class="dropdown-submenu">
				  <a href=<!--{$menu.url}--> class="menu_princ"><!--{$menu.nombre}--></a>
				  <ul class="treeview">
				      <!--{foreach from=$menu.sub item=item}-->
				      <li>
					  <a href=<!--{$item.url}--> target='mainFrame' class="vinculos"><!--{$item.nombre}--></a>
				      </li>
				      <!--{/foreach}-->
				  </ul>
			      </li>
			      <!--{/if}-->
			      <!--{/foreach}-->
			  </ul>
		      </li>
		      <!--{/if}-->
		      <li class="dropdown">
			  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><!--{$bandejas.nombre}--> <b class="caret"></b></a>
			  <ul class="">
			      <!--{foreach from=$bandejas.menu item=menu}-->
			      <!--{if $menu.subMenu == 0}-->
			      <li>
				  <a href=<!--{$menu.url}--> target='mainFrame' class="menu_princ"><!--{$menu.nombre}--></a>
			      </li>
			      <!--{elseif $menu.subMenu == 1}-->
			      <li class="dropdown-submenu">
				  <a href=<!--{$menu.url}--> class="menu_princ"><!--{$menu.nombre}--></a>
				  <ul class="treeview">
				      <!--{foreach from=$menu.sub item=item}-->
				      <li>
					  <a href=<!--{$item.url}--> target='mainFrame' class="vinculos"><!--{$item.nombre}--></a>
				      </li>
				      <!--{/foreach}-->
				  </ul>
			      </li>
			      <!--{/if}-->
			      <!--{/foreach}-->
			  </ul>
		      </li>

		      <!--{if $menuAcciones == 1}-->
		      <li class="dropdown">
			  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><!--{$acciones.nombre}--> <b class="caret"></b></a>
			  <ul class="">
			      <!--{foreach from=$acciones.menu item=menu}-->
			      <!--{if $menu.subMenu == 0}-->
			      <li>
				  <a href=<!--{$menu.url}--> target='mainFrame' class="menu_princ"><!--{$menu.nombre}--></a>
			      </li>
			      <!--{elseif $menu.subMenu == 1}-->
			      <li class="dropdown-submenu">
				  <a href=<!--{$menu.url}--> class="menu_princ"><!--{$menu.nombre}--></a>
				  <ul class="treeview">
				      <!--{foreach from=$menu.sub item=item}-->
				      <li>
					  <a href=<!--{$item.url}--> target='mainFrame' class="vinculos"><!--{$item.nombre}--></a>
				      </li>
				      <!--{/foreach}-->
				  </ul>
			      </li>
			      <!--{/if}-->
			      <!--{/foreach}-->
			  </ul>
		      </li>
		      <!--{/if}-->
		      <!--{if $menuAdministracion == 1}-->
		      <li class="dropdown">
			  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><!--{$administracion.nombre}--> <b class="caret"></b></a>
			  <ul class="treeview">
			      <!--{foreach from=$administracion.menu item=menu}-->
			      <!--{if $menu.subMenu == 0}-->
			      <li>
				  <a href=<!--{$menu.url}--> target='mainFrame' class="menu_princ"><!--{$menu.nombre}--></a>
			      </li>
			      <!--{elseif $menu.subMenu == 1}-->
			      <li class="dropdown-submenu">
				  <a href=<!--{$menu.url}--> class="menu_princ"><!--{$menu.nombre}--></a>
				  <ul class="treeview">
				      <!--{foreach from=$menu.sub item=item}-->
				      <li>
					  <a href=<!--{$item.url}--> target='mainFrame' class="vinculos"><!--{$item.nombre}--></a>
				      </li>
				      <!--{/foreach}-->
				  </ul>
			      </li>
			      <!--{/if}-->
			      <!--{/foreach}-->
			  </ul>
		      </li>
		      <!--{/if}-->
		  </ul>
		  <ul class="treeview">
		    <li class="dropdown">
		      <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <!--{$opciones.nombre}--><b class="caret"></b></a>
		      <ul class="treeview">
			      <!--{foreach from=$opciones.menu item=menu}-->
			      <!--{if $menu.subMenu == 0}-->
			      <li>
				  <a href=<!--{$menu.url}--> target='mainFrame' class="menu_princ"><!--{$menu.nombre}--></a>
			      </li>
			      <!--{elseif $menu.subMenu == 1}-->
			      <li class="dropdown-submenu">
				  <a href=<!--{$menu.url}--> class="menu_princ"><!--{$menu.nombre}--></a>
				  <ul class="treeview">
				      <!--{foreach from=$menu.sub item=item}-->
				      <li>
					  <a href=<!--{$item.url}--> target='mainFrame' class="vinculos"><!--{$item.nombre}--></a>
				      </li>
				      <!--{/foreach}-->
				  </ul>
			      </li>
			      <!--{/if}-->
			      <!--{/foreach}-->
			  </ul>
		    </li>
		    <li class="dropdown">
		      <a href="#" class="dropdown-toggle" data-toggle="dropdown"> <!--{$usuario.nombre}--><b class="caret"></b></a>
		      <ul class="treeview">
			      <!--{foreach from=$usuario.menu item=menu}-->
			      <!--{if $menu.subMenu == 0}-->
			      <li>
				  <a href=<!--{$menu.url}--> target='mainFrame' class="menu_princ"><!--{$menu.nombre}--></a>
			      </li>
			      <!--{elseif $menu.subMenu == 1}-->
			      <li class="dropdown-submenu">
				  <a href=<!--{$menu.url}--> class="menu_princ"><!--{$menu.nombre}--></a>
				  <ul class="treeview">
				      <!--{foreach from=$menu.sub item=item}-->
				      <li>
					  <a href=<!--{$item.url}--> target='mainFrame' class="vinculos"><!--{$item.nombre}--></a>
				      </li>
				      <!--{/foreach}-->
				  </ul>
			      </li>
			      <!--{/if}-->
			      <!--{/foreach}-->
			  </ul>
		    </li>
		  </ul>
	      </div>
      </nav>
       <iframe name='mainFrame' id='mainFrame' frameBorder="0" width="85%" height="100%" src='cuerpo.php?swLog=<?=$swLog?>&fechah=<?=$fechah?>&tipo_alerta=1' scrolling='auto' align='right'/></iframe>
      </div>
      <script>
    function cargarValoresCarpetas(){
      
      url = "<?=$ruta_raiz?>/include/tx/json/getRegistrosCarpetaGen.php?codUsuario=<?=$codusuario?>&depeCodi=<?=$dependencia?>&carpetaPer=0";
      $.get(url, function(data, status){
          var obj = JSON.parse(data);
          if(status="success"){
          $.each( obj, function( key, value ) {
            $('#carpetap_'+key).text(value);
          });
          }
      });
    }
    function cargarValoresCarpetasPersonales(){
      
      url = "<?=$ruta_raiz?>/include/tx/json/getRegistrosCarpetaGen.php?codUsuario=<?=$codusuario?>&depeCodi=<?=$dependencia?>&carpetaPer=1";
      $.get(url, function(data, status){
          var obj = JSON.parse(data);
          if(status="success"){
          $.each( obj, function( key, value ) {
            $('#carpetaPersonal_'+key).text(value);
          });
          }
      });
    }
    </script>
  </body>
</html>
