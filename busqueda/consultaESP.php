<?
/**
 * Programa que despliega el formulario de consulta de ESP
 * @author  YULLIE QUICANO
 * @mail    yquicano@cra.gov.co
 * @author  modify by Aquiles Canto
 * @mail    xoroastro@yahoo.com    
 * @version     1.0
 */
$ruta_raiz = "../";
session_start();
error_reporting(0);
require_once($ruta_raiz."include/db/ConnectionHandler.php");

if (!$db)	$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$nivelus     = $_SESSION["nivelus"];

//En caso de no llegar la dependencia recupera la sesi�n
if(empty($_SESSION)) include $ruta_raiz."rec_session.php";

include ("common.php");
$fechah = date("ymd") . "_" . time("hms");
$radicado=(isset($_POST['radicado']))?$_POST['radicado']:"";
$nombre=(isset($_POST['nombre']))?$_POST['nombre']:"";

?>
<html>
<head>
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
<title>Consulta archivo Historico</title>
<link rel="stylesheet" href="<?php echo $ruta_raiz?>estilos/orfeo.css">

<script language="JavaScript" type="text/JavaScript">
/**
* Env�a el formulario de acuerdo a la opci�n seleccionada, que puede ser ver CSV o consultar
*/
function enviar(argumento)
{	document.formSeleccion.action=argumento+"&"+document.formSeleccion.params.value;
	document.formSeleccion.submit();
}


function activa_chk(forma)
{	//alert(forma.tbusqueda.value);
	//var obj = document.getElementById(chk_desact);
	if (forma.slc_tb.value == 0)
		forma.chk_desact.disabled = false;
	else
		forma.chk_desact.disabled = true;
}

	function pasar_datos(fecha)
   {
    <?
	 echo " opener.document.VincDocu.numRadi.value = fecha\n";
	echo "opener.focus(); window.close();\n";
	?>
}
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</table> 
</head>
<body onLoad="crea_var_idlugar_defa(<?php echo "'".($_SESSION['cod_local'])."'"; ?>);">
<?
$params = session_name()."=".session_id()."&krd=$krd";
?>
<div id="spiffycalendar" class="text"></div>
<form action="consultaESP.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formSeleccion" id="formSeleccion" >
<input type="hidden" name="selected0" value="<?=$selected0?>">
<input type="hidden" name="selected1" value="<?=$selected1?>">
<input type="hidden" name="selected2" value="<?=$selected2?>">
<input type="hidden" name="selectedctt0" value="<?=$selectedctt0?>">
<input type="hidden" name="selectedctt1" value="<?=$selectedctt1?>">
<input type="hidden" name="nombre1" value="<?=$nombre?>">
<input type="hidden" name="tipo_masiva" value="<?=$_POST['masiva']?>">  <!-- Este valor viene cuando se invoca este archivo en selecConsultaESP.php -->
<table width="57%" border="0" cellspacing="5" cellpadding="0" align="center" class='borde_tab'>
	<tr align="center">
		<td height="25" colspan="4" class='titulos4'>
			CONSULTA ARCHIVO HISTORICO
        	  <input name="accion" type="hidden" id="accion">
        	<input type="hidden" name="params" value="<?=$params?>">
      </td>
    </tr>
    <tr>
		<td width="31%" class='titulos2' align="center">RADICADO</td>
		<td width="69%" height="30" class='listado2' align="left">
			<input name="radicado" id="radicado" type="input" size="50" class="tex_area" value="<?php echo $radicado?>" />
		</td>
	</tr>

	<tr align="center" colspan="2">
		<td width="31%" class='titulos2'>SERIE</td>
		<td width="69%" height="30" class='listado2' align="left">
		
		 <?
		$sql ="SELECT DISTINCT SERIE FROM ARCHIVO_HISTORICO ORDER BY SERIE";

	$rss = $db->conn->Execute($sql);
	print $rss->GetMenu2("serie", $serie, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );


	?>
	</td>
		</tr>	
		
	<tr align="center" colspan="2">
		<td width="31%" class='titulos2'>TIPO</td>
		<td width="69%" height="30" class='listado2' align="left">
		
		 <?
		$sql ="SELECT DISTINCT TIPO FROM ARCHIVO_HISTORICO WHERE SERIE='$serie' ORDER BY TIPO";

	$rst = $db->conn->Execute($sql);
	print $rst->GetMenu2("tipo", $tipo, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );


	?>
	</td>
		</tr>
		
			<tr align="center" colspan="2">
		<td width="31%" class='titulos2'>NOMBRE</td>
		<td width="69%" height="30" class='listado2' align="left">
			<input name="nombre" id="nombre" type="input" size="50" class="tex_area" value="<?php echo $nombre?>" />
		</td>
		</tr>
		
		<tr align="center" colspan="2">
		<td width="31%" class='titulos2'>CARPETA</td>
		<td width="69%" height="30" class='listado2' align="left">
			<input name="carpeta" id="carpeta" type="input" size="50" class="tex_area" value="<?php echo $carpeta?>" />
		</td>
		</tr>
		 <tr>
      <td class="titulos2" align="center">Desde Fecha (dd/mm/yyyy)</td>
      <td class="listado5">
        <select class="select" name="s_desde_dia" >
          <?
  for($i = 1; $i <= 31; $i++)
  {
    if($i == $s_desde_dia) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
    else $option="<option value=\"" . $i . "\">" . $i . "</option>";
    echo $option;
  }
  ?>
        </select>
        <select class="select" name="s_desde_mes">
          <?
  for($i = 1; $i <= 12; $i++)
  {
    if($i == $s_desde_mes) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
    else $option="<option value=\"" . $i . "\">" . $i . "</option>";
    echo $option;
  }
  ?>
        </select>
        <select class="select" name="s_desde_ano">
          <?
  $agnoactual=Date('Y');
  for($i = 1990; $i <= $agnoactual; $i++)
  {
    if($i == $s_desde_ano) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
    else $option="<option value=\"" . $i . "\">" . $i . "</option>";
    echo $option;
  }
  ?>
        </select>
      </td>
    </tr>
    <tr>
      <td class="titulos2" align="center">Hasta Fecha (dd/mm/yyyy)</td>
      <td class="listado5">
        <select class="select" name="s_hasta_dia">
          <?
  for($i = 1; $i <= 31; $i++)
  {
    if($i == $s_hasta_dia) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
    else $option="<option value=\"" . $i . "\">" . $i . "</option>";
    echo $option;
  }
  ?>
        </select>
        <select class="select" name="s_hasta_mes">
          <?
  for($i = 1; $i <= 12; $i++)
  {
    if($i == $s_hasta_mes) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
    else $option="<option value=\"" . $i . "\">" . $i . "</option>";
    echo $option;
  }
  ?>
        </select>
        <select class="select" name="s_hasta_ano">
          <?
  for($i = 1990; $i <= $agnoactual; $i++)
  {
    if($i == $s_hasta_ano) $option="<option SELECTED value=\"" . $i . "\">" . $i . "</option>";
    else $option="<option value=\"" . $i . "\">" . $i . "</option>";
    echo $option;
  }
  ?>
        </select>
      </td>
    </tr>
    
		
	<tr bgcolor="#FFFFFF">
		<td class="titulos1" colspan="4">
			<font style="font-family:verdana; font-size:x-small"><b>Nota:
			<font color="Gray">
			Aqu&iacute; puede consultar los radicados anteriores al 14 de Marzo de 2007, fecha en la cual se comenzaron a utilizar las tablas de retenci&oacute;n documental, lo que parti&oacute; en dos la manera de archivar los documentos.
			</b></font></font>		</td>
	</tr>
	<tr align="center">
		<td height="30" colspan="4" class='listado2'>
		
	
		<center>
			<input name="Consultar" type="submit"  class="botones" id="envia22"   value="Consultar">&nbsp;&nbsp;

		</center>
		
		</td>
	</tr>
</table>
</form>
<?php 
function pintarResultados($fila,$i,$n){
		global $ruta_raiz;
		switch($n){
			case 0:
				if($fila['PATH']!=NULL)
					$salida="<a href=\"{$ruta_raiz}bodega/historico".$fila['PATH']."\">".$fila['RADI_NUME_RADI']."</a>";
				else
					$salida=$fila['RADI_NUME_RADI'];
 				break;
			case 1:
				$salida=$fila['RADI_FECH_RADI'];
				break;
			case 2:
				$salida=$fila['NOMBRE'];
				break;
			case 3:
				$salida=$fila['SERIE'];
				break;
			case 4:
				
				$salida=$fila['TIPO'];
				break;
			case 5:
				$salida=$fila['CARPETA'];
				break;
				
			case 6:
				$salida=$fila['CAJA'];
				break;
					
			case 7:
                   $salida=$fila['FECHA_INICIAL'];
				breaK;
			case 8:
					$salida=$fila['FECHA_FINAL'];
				breaK;
			default:$salida="ERROr";
		}
		return $salida;	
	}

if(!empty($_POST['Consultar'])&& ($_POST['Consultar']=="Consultar")){
			
			require_once($ruta_raiz."include/myPaginador.inc.php");
			
			$ps_desde_RADI_FECH_RADI = mktime(0,0,0,$_POST[ "s_desde_mes"],$_POST["s_desde_dia"],$_POST["s_desde_ano"]);
 			$ps_hasta_RADI_FECH_RADI = mktime(23,59,53,$_POST["s_hasta_mes"],$_POST["s_hasta_dia"],$_POST["s_hasta_ano"]);
 			


 			
 			$where=null;
			$where=(!empty($_POST['nombre']) && trim($_POST['nombre'])!="")?"WHERE NOMBRE LIKE '%".strtoupper(trim($_POST['nombre']))."%' ":"";    
 		    
			$where=(!empty($_POST['serie']) && trim($_POST['serie'])!="")?(
							($where!="")? $where." AND SERIE LIKE '%".strtoupper(trim($_POST['serie']))."%'":" WHERE SERIE LIKE '%".strtoupper(trim($_POST['serie'])."%'")) 			
							:$where;
							
			$where=(!empty($_POST['tipo']) && trim($_POST['tipo'])!="")?(
							($where!="")? $where." AND TIPO LIKE '%".strtoupper(trim($_POST['tipo']))."%'":" WHERE TIPO LIKE '%".strtoupper(trim($_POST['tipo'])."%'")) 			
							:$where;
			
			$where=(!empty($_POST['carpeta']) && trim($_POST['carpeta'])!="")?(
							($where!="")? $where." AND CARPETA = '".trim($_POST['carpeta'])."' ":"  WHERE CARPETA = '".trim($_POST['carpeta'])."'")
							 :$where;
									
							
 		    $where=(!empty($_POST['radicado']) && trim($_POST['radicado'])!="")?(
							($where!="")? $where." AND (ano||DEPE_CODI|| LPAD(RADI_NUME_RADI,6,0)||TIPO_RADI) LIKE '%".trim($radicado)."%' ":"  WHERE (ano||DEPE_CODI|| LPAD(RADI_NUME_RADI,6,0)||TIPO_RADI) LIKE '%".trim($radicado)."%'")
							 :$where; 
 			
			$where = (empty($where) && ($where==null))?" WHERE RADI_FECH_RADI>=".$db->conn->DBTimeStamp($ps_desde_RADI_FECH_RADI):$where." AND  RADI_FECH_RADI>=".$db->conn->DBTimeStamp($ps_desde_RADI_FECH_RADI);
 			$where = $where . " AND RADI_FECH_RADI<=".$db->conn->DBTimeStamp($ps_hasta_RADI_FECH_RADI);
 	        
 			$order=1; 
 			$titulos=array("1#RADICADO","2#FECHA RADICACION","3#NOMBRE","4#SERIE","5#TIPO","6#CARPETA","7#CAJA","8#FECHA INICIAL","9#FECHA FINAL");
      	
			$isql = "SELECT  (ano||DEPE_CODI|| LPAD(RADI_NUME_RADI,6,0)||TIPO_RADI) as RADI_NUME_RADI, RADI_FECH_RADI, NOMBRE, CARPETA,CAJA,TIPO,FECHA_INICIAL,FECHA_FINAL,PATH, SERIE
					FROM ARCHIVO_HISTORICO
					{$where} ";
			//$db->conn->debug=true;
		
			$paginador= new myPaginador($db,$isql,1);
			$paginador->modoPintado(true);
			$paginador->setImagenASC($ruta_raiz."iconos/flechaasc.gif");
			$paginador->setImagenDESC($ruta_raiz."iconos/flechadesc.gif");
			$paginador->setFuncionFilas("pintarResultados");
			$paginador->generarPagina($titulos);
	}

?>

</body>
</html>
