<?php
session_start();
if(empty($_SESSION['dependencia']))
    include "$ruta_raiz/rec_session.php";

set_time_limit(0);


foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}

$usua_doc    = $_SESSION["usua_doc"];
$krd    	 = $_SESSION["krd"];
$codusuario  = $_SESSION["codusuario"];
$ruta_raiz = "../..";

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
require_once("$ruta_raiz/class_control/CombinaError.php");
include_once "$ruta_raiz/processConfig.php";
include_once "$ruta_raiz/include/tx/Envio.php";

$db = new ConnectionHandler($ruta_raiz);

$hora  = date("H")."_".date("i")."_".date("s");
// var que almacena el dia de la fecha
$ddate = date('d');
// var que almacena el mes de la fecha
$mdate = date('m');
// var que almacena el ano de la fecha
$adate = date('Y');
// var que almacena  la fecha formateada
$fecha  = $adate."_".$mdate."_".$ddate;
$dir = $usua_doc."_".$fecha."_".$hora;
$arcCsv = $dir.".xls";
$arcAnex = $dir.".zip";
//var que almacena el path hacia el PDF final
$arcPDF      = "$ruta_raiz/bodega/masiva/"."tmp_".$dir.".pdf";


$phpsession  = session_name()."=".session_id();

$dependencia = $_SESSION['dependencia'];

//var que almacena los parametros de sesion
$params=$phpsession."&dependencia=$dependencia&
    codiTRD=$codiTRD&depe_codi_territorial=$depe_codi_territorial&
    usua_nomb=$usua_nomb&tipo=$tipo&depe_nomb=$depe_nomb&
	usua_doc=$usua_doc&codusuario=$codusuario";

//Función que calcula el tiempo transcurrido
function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

?>

<html>
    <head>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>

    <style type="text/css">
   	 /* [FULL SCREEN SPINNER] */
	    #spinner-back, #spinner-front {
	      position: fixed;
	      width: 100vw;
	      transition: all 1s;
	      visibility: hidden;
	      opacity: 0;
	    }
	    #spinner-back {
	      z-index: 998;
	      height: 100vh;
	      background: rgba(0, 0, 0, 0.7);
	    }
	    #spinner-front {
	      z-index: 999;
	      color: #fff;
	      text-align: center;
	      margin-top: 50vh;
	      transform: translateY(-50%);
	    }
	    #spinner-back.show, #spinner-front.show {
	      visibility: visible;
	      opacity: 1;
	    }

	    #spinner-back.hide, #spinner-front.hide {
	      visibility: hidden;
	      opacity: 0;
	    }
	    
	  </style>

    <script>

        function abrirArchivoaux(url){
            nombreventana='Documento';
            window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');
            return;
        }

      	function cargando(){
	      document.getElementById("spinner-back").classList.add("show");
	      document.getElementById("spinner-front").classList.add("show");
	    }

	    function terminado(){
	      document.getElementById("spinner-back").classList.add("hide");
	      document.getElementById("spinner-front").classList.add("hide");
	    }
										
	    
     
    </script>
    </head>

<body>
	<div id="spinner-back"></div>
	<div id="spinner-front">
		<img src="https://www.v4software.com/Admin/Tpl/V4admin/Public/image/ajax-loaders/ajax-loader-no-color.gif"/><br>
		Cargando...
	</div>
	<div id="content" style="opacity: 1;">
		<div class="row" >
			<!-- NEW WIDGET START -->
			<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<!-- Widget ID (each widget will need unique ID)-->
				<div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">
					<header> </header>
					<div>
						<form action="adjuntar_defintExcel.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formDefinitivo">
							<input type=hidden name=pNodo value='<?=$pNodo?>'>
							<input type=hidden name=codProceso value='<?=$codProceso?>'>
							<input type=hidden name=tipoRad value='<?=$tipoRad?>'>
							<?php

							$time_start = microtime_float();

							$archivoPlantilla = $_FILES['archivoPlantilla']["tmp_name"];
							$archivoAnexos = $_FILES['archivoAnexos']["tmp_name"];

							if($_FILES['archivoPlantilla']['size'] > 3073741824){


									echo "El tama&ntilde;o de los archivos no es correcto.
											<br><br>
											<table>
													<tr>
															<td>
																	se permiten archivos de 100 Kb m&aacute;ximo.
															</td>
													</tr>
											</table>";
								} else {
								if(!copy( $archivoAnexos, "../../bodega/masiva/".$arcAnex )){
										echo "error al copiar los anexos";
										
										$arcAnex = '';
								}
								if(!copy( $archivoPlantilla, "../../bodega/masiva/".$arcCsv )){
										echo "error al copiar los archivos";
										
								} else {
										echo "<center><span class=etextomenu align=left>";
										echo " 
										<div class=\"widget-body no-padding\">
													<table id=\"dt_basic\" class=\"table table-striped table-bordered table-hover dataTable no-footer smart-form\" width=\"100%\">
													<TR ALIGN=LEFT><TD width=20% class='titulos2' >DEPENDENCIA :</td><td class='listado2'> ".$_SESSION['depe_nomb']."</TD>	<TR ALIGN=LEFT><TD class='titulos2' >USUARIO RESPONSABLE :</td><td class='listado2'>".$_SESSION['usua_nomb']."</TD> <TR ALIGN=LEFT><TD class='titulos2' >FECHA :</td><td class='listado2'>" . date("d-m-Y - h:mi:s") ."</TD></TR></TABLE>
												</div>
											</div>
										</article>";

										echo "
											<article class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12\">
												<!-- Widget ID (each widget will need unique ID)-->
												<div class=\"jarviswidget jarviswidget-color-darken\" id=\"wid-id-0\" data-widget-editbutton=\"false\">
													<header> </header>
													<div>
														<div class=\"widget-body no-padding\">";

															require "$ruta_raiz/jhrtf/jhrtfExcel.php";

															$ano = date("Y") ;
															$ruta_raiz = "../..";
															$definitivo="si";

															// Se crea el objeto de masiva
                              //$db->conn->debug = TRUE;
                              $Envio = new Envio($db);
															$masiva = new jhrtf($arcCsv,$ruta_raiz,$arcPDF,$db,$arcAnex,$dir);
                              /*if($Envio->getRestriccionEnvioTRad($tipoRad)){
                                 $masiva->validarExpediente=true;  
							  }*/
							  
															$masiva->cargar_csv();
															$masiva->validarArchs();
															if ($masiva->hayError()){
																	$masiva->mostrarError();
															}else{
															 $masiva->setTipoDocto($tipo);
															 $_SESSION["masiva"]=$masiva;
															 $usua_nomb=$_SESSION['usua_nomb'];
															 $depe_codi_territorial=$_SESSION["depe_codi_territorial"];
															 $masiva->combinar_csv($dependencia,$codusuario,$usua_doc,$usua_nomb,$depe_codi_territorial,$codiTRD,$tipoRad,$krd,$respuesta);

															 //Retorna el archivo csv con los numeros de radicados.
															 $arcCSVfinal = $masiva->final_csv();

															include("$ruta_raiz/processConfig.php");
															// EstadoTransaccion como 0 si se pudo procesar el documento, -1 de lo
															// contrario
															$estadoTransaccion=-1;

															// Se utiliza el combinador por medio del servlet para los .doc
															// EstadoTransaccion!=0 &&
															echo ("<br>$archInsumo");

															if ( !file_exists("$ruta_raiz/bodega/masiva/$archInsumo.ok")){
																	$objError = new CombinaError (NO_DEFINIDO);
																	echo ($objError->getMessage());
																	die;
															}else {
																echo "
																		<center>
																				<span  class=info>
																						<br>Se llev&oacute; a cabo la radicaci&oacute;n masiva.<br>
																				</span>
																				<span class='info'>
																						<br>
																						<a class='vinculos' href=javascript:abrirArchivoaux('$arcPDF')> Abrir Listado</a>
																				</span>
																				<!--<span class='info'>
																						<br>
																						<a class='vinculos' href=javascript:abrirArchivoaux('$arcCSVfinal')> Archivo CSV para combinar</a>
																				</span>-->
																		<center>";
															}
													}
											}
													//Contabilizamos tiempo final
													$time_end = microtime_float();
													$time = $time_end - $time_start;
													echo "<br><b>Se demor&oacute;: $time segundos la Operaci&oacute;n total.</b>";
											}

											?>
											<input name='archivo'      type='hidden' value='<?=$archivoFinal?>'>
											<input name='arcPDF'       type='hidden' value='<?=$arcPDF ?>'>
											<input name='tipoRad'      type='hidden' value='<?=$tipoRad?>'>
											<input name='pNodo'        type='hidden' value='<?=$pNodo?>'>
											<input name='params'       type='hidden' value="<?=$params?>">
											<input name='archInsumo'   type='hidden' value="<?=$archInsumo?>">
											<input name='extension'    type='hidden' value="<?=$extension?>">
											<input name='arcPlantilla' type='hidden' value='<?=$arcPlantilla?>'>

									</div>
								</div>
							</div>
						</div>
					</artible>

				</form>
      </div>

     
    </body>
</html>
