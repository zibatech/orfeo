<?php
/**
* @module crearUsuario
*
* @author Jairo Losada   <jlosada@gmail.com>
* @author Cesar Gonzalez <aurigadl@gmail.com>
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
* @copyright

SIIM2 Models are the data definition of SIIM2 Information System
Copyright (C) 2013 Infometrika Ltda.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
session_start();

    $ruta_raiz = "../..";
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");

$ADODB_COUNTRECS = false;
include_once($ruta_raiz.'/processConfig.php'); 			// incluir configuracion.
include_once($ruta_raiz."/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
//$db->conn->debug=true;
if ($db)
{	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	if (isset($_POST['btn_accion']))
    {
        $codep_us1               = $_POST['codep_us1'];
        $dpto_tmp                = explode("-",$codep_us1);
        $dpto_tmp                = $dpto_tmp[1];
        $cod_mpio                = $_POST['muni_us1'];
        $muni_ini                = explode("-", $cod_mpio);

        $muni_ini                =   $muni_ini[2];
        $record                  = array();
        $record['DPTO_CODI']     = $dpto_tmp;
        $record['MUNI_CODI']     = $_POST['txtIdMcpio'];
        $record['ID_PAIS']       = $_POST['idpais1'];
        $dptoCodi                = $dpto_tmp;
        $muniCodi                = $_POST['txtIdMcpio'];
        $paisCodi                = $_POST['idpais1'];
        $record['ID_CONT']       = $_POST['idcont1'];
        $record['MUNI_NOMB']     = $_POST['txtModelo'];
        $record['HOMOLOGA_MUNI'] = $_POST['Slc_defa'];
		if ($_POST['Slc_defa'])
		{	$record['HOMOLOGA_IDMUNI'] = $_POST['idcont2'].'-'.$_POST['muni_us2'];	}
		else
		{	if (!defined('ADODB_FORCE_NULLS')) define('ADODB_FORCE_NULLS',1);
			$ADODB_FORCE_TYPE = ADODB_FORCE_NULL;
			$record['HOMOLOGA_IDMUNI'] = 'null';
		}
		switch($_POST['btn_accion'])
		{	Case 'Agregar':{ 

		$muniNomb =strtoupper($_POST['txtModelo']);
		$idCont = $_POST['idcont1'];
		$idPais = $_POST['idpais1'];
		$homologa_muni = $_POST['Slc_defa'];

		$isql ="INSERT INTO municipio
		(muni_codi, muni_nomb, dpto_codi,id_cont,id_pais,activa,homologa_muni)
		VALUES
		($muniCodi,'$muniNomb',$dptoCodi,$idCont,$idPais,1,$homologa_muni )";

		 if (!($db->conn->Execute($isql))){
                        $error = 5;
                      }else{
						$error = 2 ;		  
					  }
			}break;
			Case 'Modificar':
				{
				  if ($muni_ini <>  $record['MUNI_CODI'])
                                 {
                                   $error = 6;
                                  }
                                  else
                                      {
					$ok = $db->conn->Replace('MUNICIPIO',$record,array('DPTO_CODI','MUNI_CODI','ID_PAIS','ID_CONT'),$autoquote = true);
					$ok ? $error = $ok : $error = 4;}
				}break;
			Case 'Eliminar':
				{	$ADODB_COUNTRECS = true;
					$record = array_slice($record, 0, 3);
					/**
					 * mod JAIRO LOSADA
					 * QUITO LA Instruccion de eliminar que coloco hollman por una Manual   ...., Dejo comentada
					 * en la SSPD no esta funcionado el BIND, pero seria bueno ya que mejora rendimiento.
					 */
					//$rs = $db->conn->Execute('SELECT * FROM SGD_DIR_DRECCIONES WHERE DPTO_CODI=? AND MUNI_CODI=? AND ID_PAIS=?',$record);
					$rs = $db->conn->Execute("SELECT * FROM SGD_DIR_DRECCIONES WHERE DPTO_CODI=$dptoCodi AND MUNI_CODI=$muniCodi AND ID_PAIS=$paisCodi");
					$ADODB_COUNTRECS = false;
					if ($rs->RecordCount() > 0)
					{	$error = 5;	}
					else
					{	if (!($db->conn->Execute("DELETE FROM MUNICIPIO WHERE DPTO_CODI=$dptoCodi AND MUNI_CODI=$muniCodi AND ID_PAIS=$paisCodi")))
							$error = 5;
					 }
				}break;
		}
		unset($record);
	}
	include "../../radicacion/crea_combos_universales.php";
}
else
{
	$error = 3;
}
?>
<html>
<head>
<title>Orfeo- Admor de Municipios.</title>
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
<script language="JavaScript" src="../../js/crea_combos_2.js"></script>
<script language="JavaScript">
<!--
function Actual()
{
var Obj = document.getElementById('muni_us1');
var i = Obj.selectedIndex;
var x = 0;
var y = 0;
var found = true;
var str = "";
while(found)
{	if (vm[x]['ID1'] == Obj.options[i].value)	break;
	x += 1;
}
str = vm[x]['ID1'];
str = str.split('-');
document.getElementById('txtModelo').value = vm[x]['NOMBRE'];
document.getElementById('txtIdMcpio').value = str[2]
document.getElementById('Slc_defa').value = vm[x]['HOMO_MCPIO'];
if (vm[x]['HOMO_MCPIO'] == 1)
{	str = vm[x]['HOMO_IDMCPIO'];
	str = str.split('-');

	document.form1.idcont2.value = str[0];
	cambia(form1,'idpais2','idcont2');
	document.form1.idpais2.value = str[1];
	cambia(form1,'codep_us2','idpais2');
	document.form1.codep_us2.value = str[1]+'-'+str[2];
	cambia(form1,'muni_us2','codep_us2');
	document.form1.muni_us2.value = str[1]+'-'+str[2]+'-'+str[3];
}
else
{
	borra_combo(form1, 9);
	borra_combo(form1, 10);
	borra_combo(form1, 11);
}
}

function borra_datos()
{
	document.getElementById('txtIdMcpio').value = "";
	document.getElementById('txtModelo').value = "";
	document.getElementById('Slc_defa').value = "";
	document.getElementById('idcont2').value = 0;
	borra_combo(form1, 9);
	borra_combo(form1, 10);
	borra_combo(form1, 11);
	document.getElementById('idpais2').value = "";
	document.getElementById('codep_us2').value = "";
	document.getElementById('muni_us2').value = "";
}

function ver_listado()
{
    conti=document.getElementById('idcont1').value;
    pais=document.getElementById('idpais1').value;
    dept=document.getElementById('codep_us1').value;
    window.open('listados.php?<?=session_name()."=".session_id()?>&conti='+conti+'&pais='+pais+'&dept='+dept,'','scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');
}
<?php
// Convertimos los vectores de los paises, dptos y municipios creados en crea_combos_universales.php a vectores en JavaScript.
echo arrayToJsArray($vpaisesv, 'vp');
echo arrayToJsArray($vdptosv, 'vd');
echo arrayToJsArray($vmcposv, 'vm');
?>
//-->
</script>
</head>
<body>
<form name="form1" method="post" id="form1" action="<?= $_SERVER['PHP_SELF']?>">
<input type="hidden" name="hdBandera" value="">

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
                Administrador de municipios<br>
                <small><?=$tituloCrear ?></small>
              </h2>
            </header>

            <!-- widget div-->
            <div>
              <!-- widget content -->
              <div class="widget-body no-padding">

                <table class="table table-bordered table-striped">
                  <tr>
                    <td width="3%" align="center" class="titulos2"><b>1.</b></td>
                    <td width="25%" align="left" class="titulos2"><b>&nbsp;Seleccione Continente</b></td>
                    <td width="72%" class="listado2">
                    <?	// Listamos los continentes.
                        echo $Rs_Cont->GetMenu2('idcont1',0,"0:&lt;&lt; SELECCIONE &gt;&gt;",false,0,"id=\"idcont1\" class=\"select\" onchange=\"borra_datos();cambia(this.form,'idpais1','idcont1')\"");
                        $Rs_Cont->Move(0);
                    ?>	</td>
                  </tr>
                  <tr>
                    <td align="center" class="titulos2"><b>2.</b></td>
                    <td align="left" class="titulos2"><b>&nbsp;Seleccione Pa&iacute;s</b></td>
                      <td align="left" class="listado2">
                      <select name="idpais1" id="idpais1" class="select" onChange="borra_datos();cambia(this.form, 'codep_us1', 'idpais1')">
                        <option value="0" selected>&lt;&lt; Seleccione Continente &gt;&gt;</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" class="titulos2"><b>3.</b></td>
                    <td align="left" class="titulos2"><b>&nbsp;Seleccione Dpto.</b></td>
                    <td align="left" class="listado2">
                      <select name='codep_us1' id ="codep_us1" class='select' onChange="borra_datos();cambia(this.form, 'muni_us1', 'codep_us1')" ><option value='0' selected>&lt;&lt; Seleccione Pa&iacute;s &gt;&gt;</option></select>
                    </td>
                  </tr>
                  <tr>
                    <td align="center" class="titulos2"><b>4.</b></td>
                    <td align="left" class="titulos2"><b>&nbsp;Seleccione Municipio.</b></td>
                    <td align="left" class="listado2">
                      <select name='muni_us1' id="muni_us1" class='select' onchange="borra_datos();Actual()" ><option value='0' selected>&lt;&lt; Seleccione Dpto &gt;&gt;</option></select>
                    </td>
                  </tr>

                  <tr>
                    <td rowspan="4" align="center" class="titulos2"><b>5.</b></td>
                    <td align="left" class="titulos2"><b>&nbsp;Ingrese c&oacute;digo del Municipio.</b></td>
                    <td class="listado2"><input name="txtIdMcpio" id="txtIdMcpio" type="text" size="10" maxlength="3"></td>
                  </tr>
                  <tr>
                    <td align="left" class="titulos2"><b>&nbsp;Ingrese nombre del Municipio.</b></td>
                    <td class="listado2"><input name="txtModelo" id="txtModelo" type="text" size="50" maxlength="70"></td>
                  </tr>
                  <tr>
                    <td align="left" class="titulos2"><b>&nbsp;Homologa precios de envio local con otra ciudad?</b></td>
                    <td class="listado2">
                        <select name="Slc_defa" class="select" id="Slc_defa">
                        <option value="" selected>&lt; seleccione &gt;</option>
                        <option value="0"> N O </option>
                        <option value="1"> S I </option>
                      </select>	</td>
                  </tr>
                  <tr>
                    <td align="left" class="titulos2"><b>&nbsp;Seleccione &eacute;sta...</b></td>
                    <td class="listado2">
                    <?php
                        echo $Rs_Cont->GetMenu2('idcont2',0,"0:&lt;&lt; SELECCIONE &gt;&gt;",false,0,"id=\"idcont2\" class=\"select\" onchange=\"cambia(this.form,'idpais2','idcont2')\"");
                        $Rs_Cont->Move(0);
                    ?>
                      <select name="idpais2" class="select" id="idpais2" onChange="cambia(this.form, 'codep_us2', 'idpais2')"></select>
                      <select name="codep_us2" class="select" id="codep_us2" onChange="cambia(this.form, 'muni_us2', 'codep_us2')"></select>
                      <select name="muni_us2" class="select" id="muni_us2"></select>
                    </td>
                  </tr>
                  <?php
                  if ($error)
                  {	echo '<tr bordercolor="#FFFFFF">
                        <td width="3%" align="center" class="titulosError" colspan="3" bgcolor="#FFFFFF">';
                    switch ($error)
                    {	case 1:	echo "Informaci&oacute;n actualizada!!";break;													//ACUTALIZACION REALIZADA
                      case 2:	echo "Municipio creado satisfactoriamente!!";break;										//INSERCION REALIZADA
                      case 3:	echo "Error al conectar a BD, comun&iacute;quese con el Administrador de sistema !!";break;	//NO CONECCION A BD
                      case 4:	echo "Error al gestionar datos, comun&iacute;quese con el Administrador de sistema !!";break;	//ERROR EJECUCCI�N SQL
                      case 5:	echo "No se puede eliminar municipio, se encuentra ligado a direcciones.";break;		//IMPOSIBILIDAD DE ELIMINAR PAIS, EST� LIGADO CON DIRECCIONES
                      case 6:	echo "No se puede Modificar el Codigo del Municipio.";break;		                      //IMPOSIBILIDAD DE MODIFICAR MPIO
                    }
                    echo '</td></tr>';
                  }
                  ?>
                  </table>

                  <table class="table table-bordered table-striped">
                    <tr>
                      <td width="10%">&nbsp;</td>
                        <td width="20%" align="center"><input name="btn_accion" type="button" class="botones" id="btn_accion" value="Listado" onClick="ver_listado();" accesskey="L" alt="Alt + L"></td>
                      <td width="20%" align="center"><input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Agregar" onClick="document.form1.hdBandera.value='A'; return ValidarInformacion();" accesskey="A"></td>
                      <td width="20%" align="center"><input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Modificar" onClick="document.form1.hdBandera.value='M'; return ValidarInformacion();" accesskey="M"></td>
                      <td width="20%" align="center"><input name="btn_accion" type="submit" class="botones" id="btn_accion" value="Eliminar" onClick="document.form1.hdBandera.value='E'; return ValidarInformacion();" accesskey="E"></td>
                      <td width="10%">&nbsp;</td>
                    </tr>
                  </table>
              </div>
            </div>
          </div>
        </article>
      </div>
    </section>
  </div>
</form>
</body>
</html>

<script ID="clientEventHandlersJS" LANGUAGE="JavaScript">
<!--
function ValidarInformacion()
{	var strMensaje = "Por favor ingrese las datos.";

	if(document.form1.idcont1.value == "0")
	{	alert("Debe seleccionar el continente.\n" + strMensaje);
		document.form1.idcont1.focus();
		return false;
	}

	if(document.form1.idpais1.value == "0")
	{	alert("Debe seleccionar el pais.\n" + strMensaje);
		document.form1.idpais1.focus();
		return false;
	}

	if(document.form1.codep_us1.value == "0")
	{	alert("Debe seleccionar el departamento.\n" + strMensaje);
		document.form1.codep_us1.focus();
		return false;
	}

	if(document.form1.txtIdMcpio.value <= "0")
	{	alert("Debe ingresar el Codigo del Municipio.\n" + strMensaje);
		document.form1.txtIdMcpio.focus();
		return false;
	}
	else if(isNaN(document.form1.txtIdMcpio.value))
	{	alert("El Codigo del Municipio debe ser numerico.\n" + strMensaje);
		document.form1.txtIdMcpio.select();
		document.form1.txtIdMcpio.focus();
		return false;
	}

	if(document.form1.hdBandera.value == "A" || document.form1.hdBandera.value == "M")
	{	if(document.form1.txtModelo.value == "")
		{	alert("Debe ingresar nombre del Municipio.\n" + strMensaje);
			document.form1.txtModelo.focus();
			return false;
		}
		if(!isNaN(document.form1.txtModelo.value))
		{	alert("El nombre del Municipio no debe ser numerico.\n" + strMensaje);
			document.form1.txtModelo.select();
			document.form1.txtModelo.focus();
			return false;
		}
		if ((document.form1.Slc_defa.value == "") || (document.form1.Slc_defa.value == "1" && document.form1.muni_us2.value == "0"))
		{	alert("Debe seleccionar si tiene (y la) ciudad homl�loga para envios locales.\n" + strMensaje);
			document.form1.Slc_defa.focus();
			return false;
		}
	}
	if(document.form1.hdBandera.value == "E")
	{	if(confirm("Esta seguro de borrar el registro ?"))
		{	document.form1.submit();	}
		else
		{	return false;	}
	}
	document.form1.submit();
}
//-->
</script>
