<?php
$fechaYmdJ = date("Ymdhms"); 
?>

<script type="text/javascript">
    function popUpBorrador(radAnterior, radNuevo, tipoRadicadp) {
        window.open('borradorPopUp.php?borrador=' + radAnterior + '&radicado=' + radNuevo , 'Mensaje Borrador', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,width=750,height=460,left = 390,top = 50');
    
    if (tipoRadicadp == 1) {
  		 document.getElementById('carpetaCarpSalida').click();      
  	} else if (tipoRadicadp == 3) {
  		 document.getElementById('carpetaCarpMemorando').click();      
  	}  else if (tipoRadicadp == 4) {
  		 document.getElementById('carpetaCarpInterna').click();
	} else if (tipoRadicadp == 5) {
	   	document.getElementById('carpetaCarpExterna').click();
	} else if (tipoRadicadp == 6) {
	   	document.getElementById('carpetaCarpResolucion').click();
	} else if (tipoRadicadp == 7) {
	   	document.getElementById('carpetaCarpAuto').click();
	}
	window.parent.close();  
}

</script>

<a href="../cuerpo.php?c=<?=$fechaYmdJ?>&<?=$fechaYmdJ?>&nomcarpeta=Salida&carpeta=1&tipo_carpt=0&order=14" target="mainFrame" class="menu_princ" id="carpetaCarpSalida" />

<a href="../cuerpo.php?c=<?=$fechaYmdJ?>&<?=$fechaYmdJ?>&nomcarpeta=Memorandos&carpeta=3&tipo_carpt=0&order=14" target="mainFrame" class="menu_princ"  
	id="carpetaCarpMemorando" />

<a href="../cuerpo.php?c=<?=$fechaYmdJ?>&<?=$fechaYmdJ?>&nomcarpeta=Circular%20Interna&carpeta=4&tipo_carpt=0&order=14" target="mainFrame" class="menu_princ" id="carpetaCarpInterna" />

<a href="../cuerpo.php?c=<?=$fechaYmdJ?>&<?=$fechaYmdJ?>&nomcarpeta=Circular%20Externa&carpeta=5&tipo_carpt=0&order=14" target="mainFrame" class="menu_princ" id="carpetaCarpExterna" />

<a href="../cuerpo.php?c=<?=$fechaYmdJ?>&<?=$fechaYmdJ?>&nomcarpeta=Resoluciones&carpeta=6&tipo_carpt=0&order=14" target="mainFrame" class="menu_princ" id="carpetaCarpResolucion" />

<a href="../cuerpo.php?c=<?=$fechaYmdJ?>&<?=$fechaYmdJ?>&nomcarpeta=Autos&carpeta=7&tipo_carpt=0&order=14" target="mainFrame" class="menu_princ" id="carpetaCarpAuto" />

<?php
/**
* @author Cesar Augusto <aurigadl@gmail.com>
* @author Jairo Losada  <jlosada@gmail.com>
* @author Correlibre.org // Tomado de version orginal realizada por JL en SSPD, modificado.
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
*
* @copyleft

OrfeoGpl Models are the data definition of OrfeoGpl Information System
Copyright (C) 2020 

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Fou@copyrightndation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

session_start();
define('ARCHIVO_PDF', 7);
define('NO_DEFINIDO', 0);
define('ADODB_ASSOC_CASE', 0);
define('NO_SELECCIONO', 0);

foreach ($_POST as $key => $valor)
	${$key} = $valor;

if ($_SESSION["krd"])
	$krd = $_SESSION["krd"];

$ruta_raiz = dirname(__DIR__, 1);
if (!$_SESSION['dependencia'])
	header("Location: $ruta_raiz/cerrar_session.php");

$encabe = session_name() . "=" . session_id() . "&krd=$krd";
$enviar_error = $encabe .
		'&radicado=' . $radPadre .
		'&nurad=' . $radPadre .
		'&asunto=' . $asunto .
		'&error_radicacion=1';

$selecciono_rad = $tipo_radicado != NO_SELECCIONO;

// Si no Selecciono tipo de radicado enviarlo a formulario inicial
if ($selecciono_rad) {
	$tipo_radicado = $_POST['tipo_radicado'];
} else {
	$redireccionar = 'Location: index.php?' . $enviar_error;
	header($redireccionar);
	exit();
}

// envio de respuesta via email
// Obtiene los datos de la respuesta rapida.
$ruta_libs = $ruta_raiz . "/respuestaRapida/";
$fecharad = date("Y-m-d h:i");

//formato para fecha en documentos
if(!function_exists("fechaFormateada")){
	function fechaFormateada($FechaStamp) {
		$ano = date('Y', $FechaStamp); //<-- Ano
		$mes = date('m', $FechaStamp); //<-- número de mes (01-31)
		$dia = date('d', $FechaStamp); //<-- Día del mes (1-31)
		$dialetra = date('w', $FechaStamp); //Día de la semana(0-7)

		$arreglo_dias = array();
		$arreglo_dias[] = 'domingo';
		$arreglo_dias[] = 'lunes';
		$arreglo_dias[] = 'martes';
		$arreglo_dias[] = 'miercoles';
		$arreglo_dias[] = 'jueves';
		$arreglo_dias[] = 'viernes';
		$arreglo_dias[] = 'sabado';

		$dialetra = (isset($arreglo_dias[$dialetra])) ? $arreglo_dias[$dialetra] : null;

		$arreglo_meses['01'] = 'enero';
		$arreglo_meses['02'] = 'febrero';
		$arreglo_meses['03'] = 'marzo';
		$arreglo_meses['04'] = 'abril';
		$arreglo_meses['05'] = 'mayo';
		$arreglo_meses['06'] = 'junio';
		$arreglo_meses['07'] = 'julio';
		$arreglo_meses['08'] = 'agosto';
		$arreglo_meses['09'] = 'septiembre';
		$arreglo_meses['10'] = 'octubre';
		$arreglo_meses['11'] = 'noviembre';
		$arreglo_meses['12'] = 'diciembre';

		$mesletra = (isset($arreglo_meses[$mes])) ? $arreglo_meses[$mes] : null;

		return htmlentities("$dialetra, $dia de $mesletra de $ano");
	}
}
$pos = strpos('salidaRespuesta', $_SERVER['HTTP_REFERER']);

if ($pos !== false) {
	header("Location: index.php?$encabe?rad_salida=$rad_salida&fecha_rad_salida=$fecha_rad_salida");
}


// Si no es nuevo radique el anexo
if ($editar == false) {
	require __DIR__.'/radicar_respuesta.php';
} else {
	require __DIR__.'/radicar_anexo.php';
}

#Logica Notificacion - Se hace reasignacion a quien lo creo
if(isset($numRadicadoPadreAnt) && $esNotificacion == true) {

	#Se valida si hay alguien con el rol Pre Gestor Notificacion para asigarselo, En caso de que no este se envia a quien lo creo.

	$sqlInfoAdicionalReasignar = "select radi_depe_radi, radi_usua_radi from radicado where radi_nume_radi = " . $radicadosSel[0];
	$rsInfoAdicionalReasignar = $db->conn->Execute($sqlInfoAdicionalReasignar);
	while(!$rsInfoAdicionalReasignar->EOF){
		$depeDestino = $rsInfoAdicionalReasignar->fields["RADI_DEPE_RADI"];
		$usuDestino = $rsInfoAdicionalReasignar->fields["RADI_USUA_RADI"];
		$rsInfoAdicionalReasignar->MoveNext();
	}	


	$sqlPreGestor = "SELECT u.usua_codi, u.depe_codi FROM usuario u
      		JOIN autm_membresias me on me.autu_id = u.id
      		JOIN autg_grupos gr on gr.id = me.autg_id
      		WHERE gr.nombre = 'Pre Gestor Notificación' AND
				  u.depe_codi = " . $depeDestino . " AND
				  gr.id != 2;	";
	$rsSqlPreGestor = $db->conn->Execute($sqlPreGestor);
	while(!$rsSqlPreGestor->EOF){
		$depeDestino = $rsSqlPreGestor->fields["DEPE_CODI"];
		$usuDestino = $rsSqlPreGestor->fields["USUA_CODI"];	
		$rsSqlPreGestor->MoveNext();
	}


	$usCodDestino = $Tx ->reasignar( $radicadosSel, $krd, $depeDestino, $coddepe, $usuDestino, $usua_actu, "si", "Para agregar expediente y enviar a Notificaciones", 9, 0);


   echo '<script type="text/javascript">
        popUpBorrador("' . $numRadicadoPadreAnt . '", "' . $numRadicadoPadre . '", ' . $tipo_radicado . ');
      </script>';	
#Memorando y salida al pasar de borrador a radicado se acutaliza la informacion
} elseif(isset($numRadicadoPadreAnt) && $esNotificacion == false) {
   		
   		echo '<script type="text/javascript">
        	popUpBorrador("' . $numRadicadoPadreAnt . '", "' . $numRadicadoPadre . '", ' . 
        		$tipo_radicado . '); </script>';		
} else {

		header("Location: salidaRespuesta.php?$encabe&nurad=$nurad&rad_salida=$rad_salida&fecha_rad_salida=$fecha_rad_salida".$errores);	
}

