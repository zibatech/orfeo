<?php

session_start();
if (!$ruta_raiz)
	$ruta_raiz = "..";


if (!$_SESSION['dependencia']){
    header ("Location: $ruta_raiz/cerrar_session.php");
}

include_once("$ruta_raiz/processConfig.php");
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
if (!$db)
		$db = new ConnectionHandler($ruta_raiz);


//Se obtienen los parámetros por item según se categoria
$sqlObtenerParametros  = "select * from sgd_notif_parametrizacion";
$extsqlObt = $db->conn->Execute($sqlObtenerParametros);

while(!$extsqlObt->EOF){
	$jsonCitacion =  json_decode($extsqlObt->fields["SGD_NOTIF_PAR_CITACION"]);
    $jsonNotificacion =  json_decode($extsqlObt->fields["SGD_NOTIF_PAR_NOTIFICACION"]);
	$jsonComunicacion =  json_decode($extsqlObt->fields["SGD_NOTIF_PAR_COMUNICACION"]);
	$jsonPublicacion =  json_decode($extsqlObt->fields["SGD_NOTIF_PAR_PUBLICACION"]);
	$jsonDevolucion =  json_decode($extsqlObt->fields["SGD_NOTIF_PAR_DEVOLUCION"]);
	$jsonPriorizacion =  json_decode($extsqlObt->fields["SGD_NOTIF_PAR_PRIORIZACION"]);
	//echo $json->vencido->color;
	$extsqlObt->MoveNext();
	break;
}

$arrayEtapa = array($jsonCitacion, $jsonNotificacion, $jsonComunicacion, $jsonPublicacion,$jsonDevolucion, $jsonPriorizacion);

//echo $array[0]->etapa;

?>

<html>
<head>
<title>Orfeo - Parametrización de tiempos.</title>
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>

<script type="text/javascript">
	
	function editarTiempo(etapa, fase, tiempo) {
		
		var numero = window.prompt("Dígite el nuevo parámetro", "Tiempo");
		if (isNaN(numero)) {
			alert("Dígite solo números");
		} else {			
			if(numero <= 0) {
				alert("El número debe ser mayor a 0");
			} else {
				$.ajax({url: "notificacionBackend.php", 
					    type: "POST",
						data: { funcion : '1', etapa : etapa, fase : fase, tiempo: numero},
						success: function(result){		
				    		if(result == '200') {
				    			var generadorID = '#jg' + etapa + fase;
								$(generadorID).text(numero);
				    		} else {
				    			alert(result);
				    		}
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		 }
				 });				
			}

		}
	}


	$(document).ready(function(){
  	

	});

</script>

</head>
<body>
<form name="form1" method="post" action="<?= $_SERVER['PHP_SELF']?>">
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
                Parametrización de tiempos
              </h2>
            </header>

            <div>
              <!-- widget content -->
              <div class="widget-body no-padding">

				  <?php 
				  	foreach ($arrayEtapa as $etapaConf) {
				   ?>				  	
					<table class="table table-bordered table-striped">
						<tr bordercolor="#FFFFFF">
						  <td rowspan="3" align="center" width="20%"><?php echo $etapaConf->etapa; ?>
							</td>
								<td rowspan="3" align="center">
									
									<a style="cursor:alias;color:<?=$etapaConf->color?>" title="<?=$etapaConf->etapa?>">
										<div class='fa fa-user'>
										</div>
									</a>

								</td>
								<td align="center">En tiempo</td>
								<td align="center">

									<a style="cursor:alias;color:<?=$etapaConf->enTiempo->color?>" title="<?=$etapaConf->enTiempo->duracion?>">
										<div class='fa fa-flag'>
										</div>
									</a>
									
								</td>
								<td align="center">
								        <?php
								        	echo '<a id="jg' . $etapaConf->etapa . '1' . '" style="cursor:context-menu;color:#000000;text-decoration: none">	      
								        			'. $etapaConf->enTiempo->duracion .'
								        </a>';
								        ?>
								        &nbsp;								        
										<a style="cursor:pointer;color:#000000" title="Editar" onclick="editarTiempo('<?=$etapaConf->etapa?>',1,'<?=$etapaConf->enTiempo->duracion?>');">
											<div class='fa fa-pencil'>
											</div>
										</a>
								</td>
						</tr>	
						<tr bordercolor="#FFFFFF">
								
								<td align="center">Próximo a vencer</td>
								<td align="center">

									<a style="cursor:alias;color:<?=$etapaConf->proximoVencer->color?>" title="<?=$etapaConf->proximoVencer->duracion?>">
										<div class='fa fa-flag'>
										</div>
									</a>
									
								</td>
								<td align="center">
								        <?php
								        	echo '<a id="jg' . $etapaConf->etapa . '2' . '" style="cursor:context-menu;color:#000000;text-decoration: none">	      
								        			'. $etapaConf->proximoVencer->duracion .'
								        </a>';
								        ?>								
								        &nbsp;
										<a style="cursor:pointer;color:#000000" title="Editar" onclick="editarTiempo('<?=$etapaConf->etapa?>',2,'<?=$etapaConf->proximoVencer->duracion?>');">
											<div class='fa fa-pencil'>
											</div>
										</a>
								</td>			
						</tr>	
						<tr bordercolor="#FFFFFF">
								<td align="center">Vencido</td>
								<td align="center">

									<a style="cursor:alias;color:<?=$etapaConf->vencido->color?>" title="<?=$etapaConf->vencido->duracion?>">
										<div class='fa fa-flag'>
										</div>
									</a>
									
								</td>
								<td align="center">
								        <?php
								        	echo '<a id="jg' . $etapaConf->etapa . '3' . '" style="cursor:context-menu;color:#000000;text-decoration: none">	      
								        			'. $etapaConf->vencido->duracion .'
								        </a>';
								        ?>								
								        &nbsp;
										<a style="cursor:pointer;color:#000000" title="Editar" onclick="editarTiempo('<?=$etapaConf->etapa?>',3,'<?=$etapaConf->vencido->duracion?>');">
											<div class='fa fa-pencil'>
											</div>
										</a>
								</td>
						</tr>
              		</table>
              		<br>
              		<?php
              			}              			
              		?>	
              </div>
             </div>	
          </div>    
        </article>
      </div>
    </section>
   </div>
 </form>