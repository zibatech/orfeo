<?php
session_start();
$ruta_raiz = "..";
define('ADODB_ASSOC_CASE', 1);
if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");
/**
* Paggina generar_envio.php Genera las Planillas de Envio
* Se añadio compatibilidad con variables globales en Off
* @autor Jairo Losada 2011-12
* @licencia GNU/GPL V 3
*/

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;



$verrad         = "";
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
$codigo_envio = $tipo_envio;
include_once  "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("..");
if (!defined('ADODB_FETCH_ASSOC'))	define('ADODB_FETCH_ASSOC',1);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

if(!$fecha_busq) $fecha_busq=date("Y-m-d");

?>
<head>
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
</head>
<script>
function validar(action) {
  if(action!=2) {
    document.new_product.action = "generar_envio.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah"?>&generar_listado_existente= Generar Plantilla existente ";
   }else{
    document.new_product.action = "generar_envio.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah"?>&generar_listado= Generar Nuevo Envio ";
  }
   solonumeros();
}

function rightTrim(sString) {
  while (sString.substring(sString.length-1, sString.length) == ' ')
	{	sString = sString.substring(0,sString.length-1);  }
	return sString;
}

function solonumeros() {
  jh =  document.getElementById('no_planilla');
	if(rightTrim(jh.value) == "" || isNaN(jh.value))
 	{	alert('Solo introduzca numeros.' );
		jh.value = "";
		jh.focus();
 		return false;
	}
	else
	{	document.new_product.submit();	}
}
</script>
<body>
<?

//$db->conn->debug = true;
?>
<div id="spiffycalendar" class="text"></div>
<div class="container-fluid">
  <form name="new_product"  class="smart-form" action='generar_envio.php?<?=session_name()."=".session_id()."&krd=$krd&fecha_h=$fechah"?>' method=post>

      <div class="well">
            <section>
              <h1 class="semi-bold">Generaci&oacute;n Planillas y Guias de Correo</h1>

                <table class='table table-striped  table-hover dataTable no-footer ' >
                  <!--DWLayoutTable-->
                  <TR>
                    <TD width="125" height="21"  class='titulos2'> Fecha<br>
                  <?  echo "(".date("Y-m-d").")"; ?>
                  </TD>
                    <TD>
                  <input id="fecha_busq" name="fecha_busq" type="text" data-provide="datepicker" data-date-format="YYYY/MM/DD" data-date-end-date="0d" value="<?=$fecha_busq;?>">

                </TD>
                </TR>
                <TR>
                  <TD height="26" class='titulos2'> Desde la Hora</TD>
                  <TD valign="top" class='listado2'>
                  <table border=0><tr><td>
                <?
                    if(!$hora_ini) $hora_ini = 01;
                    if(!$hora_fin) $hora_fin = date("H");
                    if(!$minutos_ini) $minutos_ini = 01;
                    if(!$minutos_fin) $minutos_fin = date("i");
                    if(!$segundos_ini) $segundos_ini = 01;
                    if(!$segundos_fin) $segundos_fin = date("s");
                ?>
                  <select name=hora_ini class='select'>
                  <?
                    for($i=0;$i<=23;$i++)
                    {
                    if ($hora_ini==$i){ $datoss = " selected "; }else{ $datoss = " "; }?>
                    <option value='<?=$i?>' '<?=$datoss?>'>
                      <?=$i?>
                    </option>
                      <?
                      }
                      ?>
                </select></td><td>:</td><td><select name=minutos_ini class='select'>
                  <?
                    for($i=0;$i<=59;$i++)
                    {
                    if ($minutos_ini==$i){ $datoss = " selected "; }else{ $datoss = " "; }?>
                    <option value='<?=$i?>' '<?=$datoss?>'>
                    <?=$i?>
                    </option>
                    <?
                    }
                    ?>
                  </select>
                  </td></tr>
                  </table>
                  </TD>
                  </TR>
                  <Tr>
                    <TD height="26" class='titulos2'> Hasta</TD>
                    <TD valign="top" class='listado2'><table border=0><tr><td><select name=hora_fin class=select>
                    <?
                      for($i=0;$i<=23;$i++)
                      {
                      if ($hora_fin==$i){ $datoss = " selected "; }else{ $datoss = " "; }?>
                        <option value='<?=$i?>' '<?=$datoss?>'>
                        <?=$i?>
                        </option >
                        <?
                  }
                  ?></select></td><td>:</td><td><select name=minutos_fin class=select>
                        <?
                  for($i=0;$i<=59;$i++)
                  {
                  if ($minutos_fin==$i){ $datoss = " selected "; }else{ $datoss = " "; }?>
                        <option value='<?=$i?>' '<?=$datoss?>'>
                        <?=$i?>
                        </option>
                    <?
                      }
                      ?>
                      </select>
                      </td></tr>
                      </table>
                      </TD>
                  </TR>
                  <tr>
                    <TD height="26" class='titulos2'>Tipo de Salida</TD>
                    <TD valign="top" align="left" class='listado2'>
                <?
                  $iSql = "select sgd_fenv_descrip,sgd_fenv_codigo from  sgd_fenv_frmenvio order by sgd_fenv_descrip";

                  $rs=$db->conn->query($iSql);
                  print $rs->GetMenu2("tipo_envio", $tipo_envio, "0:-- Seleccione --", false,""," class='select' onChange='submit();'");
                $codigo_envio=$tipo_envio;
                ?>
                </TD>
                  </tr>
                <tr>
                    <TD height="26" class='titulos2'>Numero de Planilla</TD>
                    <TD valign="top" align="left" class='listado2'>
                      <input type="text" name="no_planilla" id="no_planilla" value='<?=$no_planilla?>' class='tex_area' size=11 maxlength="9" >
                <?
                  $fecha_mes = substr($fecha_busq,0,4) ;
                  // conte de el ultimo numero de planilla generado.
                  $sqlChar = $db->conn->SQLDate("Y","SGD_RENV_FECH");
                  //include "$ruta_raiz/include/query/radsalida/queryGenerar_envio.php";
                  $query = "SELECT sgd_renv_planilla, sgd_renv_fech FROM sgd_renv_regenvio
                        WHERE DEPE_CODI=$dependencia AND $sqlChar = '$fecha_mes'
                            AND ".$db->conn->length."(sgd_renv_planilla) > 0
                            AND sgd_fenv_codigo = $tipo_envio ORDER BY cast(SGD_RENV_PLANILLA as numeric(12)) desc";

                  $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
                    $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
                  $rs = $db->conn->query($query);
                  if ($rs) {
                    $planilla_ant = $rs->fields["SGD_RENV_PLANILLA"];
                    $fecha_planilla_ant = $rs->fields["SGD_RENV_FECH"];
                  }

                  if($codigo_envio)
                  {
                    echo "<br><span class=etexto>&Uacute;ltima planilla generada : <b> $planilla_ant </b>  Fec:$fecha_planilla_ant";
                  }	else	{

                  }
                  // Fin conteo planilla generada
                ?>
                  </TD>
                  <tr>
                    <td height="26" colspan="2" valign="top" class='titulos2'> <center>
                    <INPUT TYPE=button name=generar_listado_existente Value=' Generar Plantilla existente ' class='btn btn-primary' onClick="validar(1);">
                    <INPUT TYPE=button name=generar_listado Value=' Generar Nuevo Envio ' class='btn btn-primary' onClick="validar(2);">
                    </center>
                    </td>
                  </tr>
                </TABLE>

            </section>
      </div>
  </form>

</div>
<div style="overflow-x:auto;">
<?php

if(!$fecha_busq) $fecha_busq = date("Y-m-d");
if($generar_listado or $generar_listado_existente)
{
$no_planilla_Inicial = $no_planilla;
	if($tipo_envio!=111111)
	{
		error_reporting(7);
		if($generar_listado_existente)
		{
			$generar_listado = "Genzzz";
			echo "<table  width='100%'><tr><td class=listado2><CENTER>Generar Listado Existente</td></tr></table>";
		}
		include "./listado_planillas.php";
		echo "<table  width='100%'><tr><td class=titulos2><CENTER>FECHA DE BUSQUEDA $fecha_busq</cebter></td></tr></table>";


	}
	if($tipo_envio==2222)
	{

		include "./listado_guias.php";
	 	echo "<table  width='100%'><tr><td class=listado2><CENTER>FECHA DE BUSQUEDA $fecha_busq </center></td></tr></table>";
	 }
	if($tipo_envio==1108)
	{

		echo "<table  width='100%'><tr><td class=titulos2><CENTER>PLANILLA NORMAL</center></td></tr></table>";
		if($generar_listado_existente)  $generar_listado = "Genzzz";
		include "./listado_planillas_normal.php";
		echo "<table  width='100%'><tr><td class=titulos2><CENTER>FECHA DE BUSQUEDA $fecha_busq </center></td></tr></table>";
	}
	if($tipo_envio==1109)
	{

		echo "<table  width='100%'><tr><td class=titulos2><CENTER>PLANILLA ACUSE DE RECIBO</center></td></tr></table>";
		if($generar_listado_existente)  $generar_listado = "Genzzz";
		include "./lPlanillaAcuseR.php";
		echo "<table  width='100%'><tr><td class=listado2><CENTER>FECHA DE BUSQUEDA $fecha_busq </td></tr></table>";
	}
  include "./generar_planos.php";
}
?>
</div>
<script>
<?php
 if(!$fecha_busq) $fecha_busq=date("Y-m-d");

?>
$('#fecha_busq').datepicker({
   dateFormat: 'yy-mm-dd',
    format: {
        /*
         * Say our UI should display a week ahead,
         * but textbox should store the actual date.
         * This is useful if we need UI to select local dates,
         * but store in UTC
         */
        toDisplay: function (date, format, language) {
            var d = new Date('<?=$fecha_busq?>');
            d.setDate(d.getDate() - 7);
            return d.toISOString();
        },
        toValue: function (date, format, language) {
            var d = new Date('<?=$fecha_busq?>');
            d.setDate(d.getDate() + 7);
            return new Date(d);
        }
    },
    autoclose: true
});
$("#fecha_busq").datepicker( "setDate" , "<?=$fecha_busq?>" );
</script>
