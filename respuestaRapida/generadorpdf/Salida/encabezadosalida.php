<?php


if($Button == 'Radicar') {
	$radSAuxSalida = $nurad;
	$fradAuxSalida = $fecha_rad_salida;	
	$depenNomAuxSalida = $depnombAux;	
	$expedienteAuxSalida = $expAnexo;
} else {
	$radSAuxSalida = 'RAD_S';
	$fradAuxSalida = 'F_RAD_S';
	$depenNomAuxSalida = 'DEPENDENCIA';
	$expedienteAuxSalida = 'EXPEDIENTE';
}

$encabezadoSalida = '<div style="text-align:right">
<table align="right" border="1" cellspacing="0">
	<tbody>
		<tr>
			<td colspan="2" style="text-align:center"><span style="font-size:9px"><span style="font-family:Arial,Helvetica,sans-serif"><strong>Corporación Autónoma Regional del Atlántico</strong></span></span></td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:left"><span style="font-size:9px"><span style="font-family:Arial,Helvetica,sans-serif"><strong>Para responder este documento favor citar este n&uacute;mero:</strong></span></span></td>
		</tr>
		<tr>
			<td style="text-align:left"><span style="font-size:9px"><span style="font-family:Arial,Helvetica,sans-serif"><strong>Rad No: </strong></span></span></td>
			<td style="text-align:left"><span style="font-size:9px"><span style="font-family:Arial,Helvetica,sans-serif"><strong>' .  $radSAuxSalida  . '</strong></span></span></td>
		</tr>
		<tr>
			<td style="text-align:left"><span style="font-size:9px"><span style="font-family:Arial,Helvetica,sans-serif">Fecha: </span></span></td>
			<td style="text-align:left"><span style="font-size:9px"><span style="font-family:Arial,Helvetica,sans-serif">' . $fradAuxSalida . '</span></span></td>
		</tr>
		<tr>
			<td style="text-align:left"><span style="font-size:9px"><span style="font-family:Arial,Helvetica,sans-serif">Dependencia: </span></span></td>
			<td style="text-align:left"><span style="font-size:9px"><span style="font-family:Arial,Helvetica,sans-serif">' . $depenNomAuxSalida . '</span></span></td>
		</tr>

		<tr>
			<td style="text-align:left"><span style="font-size:9px"><span style="font-family:Arial,Helvetica,sans-serif">Expediente:</span></span></td>
			<td style="text-align:left"><span style="font-size:9px"><span style="font-family:Arial,Helvetica,sans-serif">' . $expedienteAuxSalida . '</span></span></td>
		</tr>
	</tbody>
</table>
</div>

<p>&nbsp;</p>';


?>

