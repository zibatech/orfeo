<?php
session_start();
/*
 * Lista Subseries documentales
 * @autor Jairo Losada SuperSOlidaria
 * @fecha 2009/06 Modificacion Variables Globales.
 */
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$ruta_raiz = "..";
if (!$nurad) $nurad= $rad;
if($nurad)
{
	$ent = substr($nurad,-1);
}
$yy = 20;
$date_r = Date('Y-m-d');
$date_r = getdate(strtotime($date_r));
$date_result = date("Y-m-d", mktime(($date_r["hours"]+0),($date_r["minutes"]+0),($date_r["seconds"]+0),($date_r["mon"]+0),($date_r["mday"]+0),($date_r["year"]+$yy)));
if (!$fecha_busq)  $fecha_busq=Date('Y-m-d');
if (!$fecha_busq2)  $fecha_busq2=$date_result;
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
define('ADODB_FETCH_ASSOC',2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$encabezadol = "$PHP_SELF?".session_name()."=".session_id()."&nurad=$nurad&fecha_busq=$fecha_busq&fecha_busq2=$fecha_busq2&codserieI=$codserieI&detaserie=$detaserie&codusua=$codusua&depende=$depende&ent=$ent";
?>
<html>
<head>
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
<!--<link rel="stylesheet" href="../estilos/orfeo.css">-->
<script>
function regresar(){
	document.adm_serie.submit();
}

function val_datos()
{
	bandera = true;
	err_msg = '';
	if(!isNonnegativeInteger(document.getElementById('codserieI').value,false))
	{
		err_msg = err_msg+'Digite numeros Codigo.\n';
		bandera = false;
	}
	if(isWhitespace(document.getElementById('detaserie').value))
	{
		err_msg = err_msg+'Digite Descripcion.\n';
		bandera = false;
	}
	if (dateAvailable.getSelectedDate() > dateAvailable2.getSelectedDate())
	if(isWhitespace(document.getElementById('detaserie').value))
	{
		err_msg = err_msg+'Escoja correctamente las fechas.\n';
		bandera = false;
	}
	if (!bandera) alert(err_msg);
	return bandera;
}
</script>
</head>
<body bgcolor="#FFFFFF">
 <div id="spiffycalendar" class="text"></div>
<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="JavaScript" src="../js/formchek.js"></script>
<script language="javascript">
<!--
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "adm_serie", "fecha_busq","btnDate1","<?=$fecha_busq?>",scBTNMODE_CUSTOMBLUE);
	var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "adm_serie", "fecha_busq2","btnDate1","<?=$fecha_busq2?>",scBTNMODE_CUSTOMBLUE);
//-->
</script>
<table class="table table-striped"  width='100%' cellspacing="5"><tr><td ><center>SERIES DOCUMENTALES</center></td></tr></table>
<form method="post" action="<?=$encabezadol?>" name="adm_serie">
<center>
<TABLE width="550" class="borde_tab" cellspacing="5">
<TR>
	<TD width="125" height="21"  > Id</td>
	<TD valign="top" align="left" >
	<label id="idSerieGrbLabel" name="idSerieGrbLabel" value='<?=$idSerieGrb?>' ></label>
	<input type="hidden" id="idSerieGrb" name="idSerieGrb" value='<?=$idSerieGrb?>'  class='tex_area' size=11 maxlength="4" ></td>
</tr>
<TR>
	<TD width="125" height="21"  > C&oacute;digo</td>
	<TD valign="top" align="left" ><input type=text id="codserieI" name="codserieI" value='<?=$codserieI?>' class='tex_area' size=11 maxlength="4" ></td>
</tr>
<tr>
	<TD height="26" > Descripci&oacute;n</td>
	<TD valign="top" align="left" ><input type=text id="detaserie" name="detaserie" value='<?=$detaserie?>' class='tex_area' size=75 maxlength="75" ></td>
</tr>
<tr>
	<TD height="26" >Fecha desde<br></td>
	<TD width="225" align="right" valign="top" >
		<script language="javascript">
		dateAvailable.date = "<?=date('Y-m-d');?>";
		dateAvailable.writeControl();
		dateAvailable.dateFormat="yyyy-MM-dd";
		</script>
	</TD>
</TR>
<TR>
	<TD height="26" > Fecha Hasta<br></td>
	<TD width="225" align="right" valign="top" >
		<script language="javascript">
		dateAvailable2.date = "<?=date('Y-m-d');?>";
		dateAvailable2.writeControl();
		dateAvailable2.dateFormat="yyyy-MM-dd";
		</script>
	</td>
</TR>
<tr>
	<td height="26" colspan="3" valign="top" >
		<center>
		<input type=submit name=buscar_serie Value='Buscar' class="btn btn-primary" >
		<input type=submit name=insertar_serie Value='Insertar' class="btn btn-success" oonclick="return val_datos();" >
		<input type=submit name=actua_serie Value='Modificar' class="btn btn-info" oonclick="return val_datos();" >
		<input type="reset"  name=aceptar class="btn btn-danger" id=envia22  value='Cancelar'>
		</center>
	</td>
</tr>
</table>
<?PHP
$sqlFechaD=$db->conn->DBDate($fecha_busq);
$sqlFechaH=$db->conn->DBDate($fecha_busq2);
$detaserie = strtoupper(trim($detaserie));
//Busca series que cumplen con el detalle
if($buscar_serie && $detaserie !='')
{
	$whereBusqueda = " where upper(sgd_srd_descrip) like '%".strtoupper($detaserie)."%'";
} else {
	if($buscar_serie && $codserieI > 0)
	{
		$whereBusqueda = " where sgd_srd_codigo = $codserieI";
	}
}
if($insertar_serie && $codserieI !=0 && $detaserie !='')
{
	$isqlB = "select * from sgd_srd_seriesrd where sgd_srd_codigo = $codserieI AND SGD_SRD_FECHFIN>='$fecha_busq' ";
	# Selecciona el registro a actualizar
	$rs = $db->query($isqlB); # Executa la busqueda y obtiene el registro a actualizar.
	$radiNumero = $rs->fields["SGD_SRD_CODIGO"];
	$srdOldFechIni = substr($rs->fields["SGD_SRD_FECHINI"],0,10);
	$srdOldFechFin = substr($rs->fields["SGD_SRD_FECHFIN"],0,10);
	$idSerie = $rs->fields["ID"];
	if ($radiNumero !='')
	{
		$mensaje_err = "<HR><center><B><FONT COLOR=RED>EL CODIGO < $codserieI > YA EXISTE. <BR>  VERIFIQUE LA INFORMACION E INTENTE DE NUEVO</FONT></B></center><HR>";
		$mensaje_err = "<HR><center><B><FONT COLOR=RED>La serie <$detaserie > ya EXISTE y esta activa entre fechas ($srdOldFechIni a $srdOldFechFin), <br>La fecha de la nueva serie es ($fecha_busq a $fecha_busq2). <BR>  VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO</FONT></B></center><HR>";
	}
	else
	{
		$isqlB = "select * from sgd_srd_seriesrd where sgd_srd_descrip = '$detaserie'";
		$rs = $db->query($isqlB); # Executa la busqueda y obtiene el registro a actualizar.
		$radiNumero = $rs->fields["SGD_SRD_DESCRIP"];
	    if ($radiNumero !='')
	    {
	    	$mensaje_err = "<HR><center><B><FONT COLOR=RED>LA SERIE <$detaserie > YA EXISTE. <BR>  VERIFIQUE LA INFORMACION E INTENTE DE NUEVO</FONT></B></center><HR>";
	    }else{
			$query="insert into SGD_SRD_SERIESRD(SGD_SRD_CODIGO   , SGD_SRD_DESCRIP,SGD_SRD_FECHINI,SGD_SRD_FECHFIN )
					VALUES ($codserieI,'$detaserie'    ,".$sqlFechaD.",".$sqlFechaH.")";
			$rsIN = $db->conn->query($query);
			$codserieI = 0 ;
			$detaserie = '';
?>
<script language="javascript">
document.adm_serie.codserieI.value ='';
document.adm_serie.detaserie.value ='';
</script>
<?php
  		}
	}
}
if($actua_serie && $codserieI !=0 && $detaserie !='')
{
	$isqlB = "select * from sgd_srd_seriesrd where id = $idSerieGrb";
	# Selecciona el registro a actualizar
	//$db->conn->debug = true;
	$rs = $db->query($isqlB); # Executa la busqueda y obtiene el registro a actualizar.
	$radiNumero = $rs->fields["SGD_SRD_CODIGO"];
	$srdCodigo = $rs->fields["SGD_SRD_CODIGO"];
	if ($radiNumero =='')
	{
		$mensaje_err = "<HR><center><B><FONT COLOR=RED>EL CODIGO < $codserieI > NO EXISTE. <BR>  VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO</FONT></B></center><HR>";
	}
	else
	{
		$isqlB = "select * from sgd_srd_seriesrd
				  where sgd_srd_descrip = '$detaserie'
				  and sgd_srd_codigo != $codserieI";
		$rs = $db->query($isqlB); # Executa la busqueda y obtiene el registro a actualizar.
		$radiNumero = $rs->fields["SGD_SRD_CODIGO"];
		$idSerieAct = $rs->fields["ID"];
		$srdOldFechIni = $rs->fields["SGD_FECH_INI"];
		$srdOldFechFin = $rs->fields["SGD_FECH_FIN"];

	  if ($radiNumero !='' && $idSerieGrb!=$idSerieAct)
	  {
	    	$mensaje_err = "<HR><center><B><FONT COLOR=RED>LA SERIE <$detaserie > YA EXISTE. <BR>  VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO</FONT></B></center><HR>";
				}else{

		$isqlB = "select * from sgd_srd_seriesrd
				where sgd_srd_codigo = $codserieI
				AND SGD_SRD_FECHFIN>='$fecha_busq'";
		$rs = $db->query($isqlB); # Executa la busqueda y obtiene el registro a actualizar.
		$radiNumero = $rs->fields["SGD_SRD_CODIGO"];
		$idSerieAct = $rs->fields["ID"];
		$srdOldFechIni = substr($rs->fields["SGD_SRD_FECHINI"],0,10);
		$srdOldFechFin = substr($rs->fields["SGD_SRD_FECHFIN"],0,10);
	  if ($radiNumero && $idSerieGrb!=$idSerieAct)
	  {
	    	$mensaje_err = "<HR><center><B><FONT COLOR=RED>La serie <$detaserie > ya EXISTE y esta activa entre fechas ($srdOldFechIni a $srdOldFechFin), <br>La fecha de la nueva serie es ($fecha_busq a $fecha_busq2). <BR>  VERIFIQUE LA INFORMACI&Oacute;N E INTENTE DE NUEVO</FONT></B></center><HR>";
		}else{

			$isqlUp =	"update sgd_srd_seriesrd
						set SGD_SRD_DESCRIP= '$detaserie',
						sgd_srd_codigo = $codserieI,
						SGD_SRD_FECHINI=$sqlFechaD,
						SGD_SRD_FECHFIN =$sqlFechaH
						where id = $idSerieGrb";
			$rsUp= $db->query($isqlUp);

			// Si cambia el codigo de serie este debe cambiarse en la tabla subseries y en la matriz.
			if($srdCodigo!=$codserieI){
			$isqlUp =	"update sgd_sbrd_subserierd
						set sgd_srd_codigo = $codserieI
						where sgd_srd_id = $idSerieGrb";
			$rsUp= $db->query($isqlUp);

			echo "Se modificaron los codigos de las series en las tablas de subseries(sbrd).";

			$isqlUp =	"update sgd_mrd_matrird
						set sgd_srd_codigo = $codserieI
						where sgd_srd_id = $idSerieGrb";
			$rsUp= $db->query($isqlUp);

			echo "Se modificaron los codigos de las series en las tabla de matriz de relacion (mrd)s.";
			}


			$codserieI = 0 ;
			$detaserie = '';
			$mensaje_err ="<HR><center><B><FONT COLOR=RED>SE MODIFIC&Oacute; LA SERIE</FONT></B></center><HR>";
			}


?>
			<script language="javascript">
			document.adm_serie.codserieI.value ='';
			document.adm_serie.detaserie.value ='';
			</script>
<?php
		}
	}
}
include_once "$ruta_raiz/trd/lista_series.php";
?>
</form>
<p>
<?=$mensaje_err?>
</p>
</body>
</html>
