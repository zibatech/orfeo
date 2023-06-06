<?php


$depeRadica   = $_GET['depeRadica'];
$usuRadica   = $_GET['usuRadica'];
$depeEnvio   = $_GET['depeEnvio'];
$usuEnvio   = $_GET['usuEnvio'];

$ruta_raiz = "..";
session_start();

if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

?>

<html>
<head>
<script src="jquery.js"></script>
<title>TASA - Masivos.</title>
<script type="text/javascript">

	var contador = 0;

	$(document).ready(function(){  		
  		ejecutarProceso();
	});

	function ejecutarProceso() {
		contador++;			
		var depeRadica = <?php echo $depeRadica; ?>;	
		var usuRadica = <?php echo $usuRadica; ?>;	
		var depeEnvio = <?php echo $depeEnvio; ?>;	
		var usuEnvio = <?php echo $usuEnvio; ?>;	
		$.ajax({url: "masiva.php", 
						    type: "POST",
							data: { depeRadica : depeRadica, usuRadica : usuRadica, depeEnvio : depeEnvio, usuEnvio: usuEnvio},
							success: function(result){							    		
					    		$( "#D_Contenido" ).append( "<p>" + result + "</p>" );					    	
					    		/*if(!result.includes('*FIN*') && !result.includes('Error')) {
					    			setTimeout(function(){ ejecutarProceso(); }, 300);
					    		}*/

					    		if(!result.includes('*FIN*')) {
					    			setTimeout(function(){ ejecutarProceso(); }, 300);
					    		}


					  		},
					  		error: function(XMLHttpRequest, textStatus, errorThrown) {
					  			console.log(XMLHttpRequest);
					  			console.log(textStatus);
					  			console.log(errorThrown);
					  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
					  		 }
					 });	
	}

</script>
</head>
<body>

<p>Proceso Iniciado......</p>
<div id="D_Contenido"></div>
</body>
</html>	