<?
/**
 
 * @author  JUAN CARLOS VILLALBA CARDENAS
 * @mail   jvillalba@cra.gov.co
 */
$ruta_raiz = "../";
session_start();
error_reporting(7);
require_once($ruta_raiz."include/db/ConnectionHandler.php");

if (!$db)	$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

//En caso de no llegar la dependencia recupera la sesi�n
if(empty($_SESSION)) include $ruta_raiz."rec_session.php";

include ("common.php");
$fechah = date("ymd") . "_" . time("hms");
$dep=$_POST['dep'];
$fecha_ini=$_POST['fecha_ini'];
$fecha_fin=$_POST['fecha_fin'];
?>
<html>
<head>
<title>Consulta Inventario Documental 2012</title>
<link rel="stylesheet" href="<?php echo $ruta_raiz?>estilos/orfeo.css">

<script language="JavaScript" type="text/JavaScript">
/**
* Env�a el formulario de acuerdo a la opci�n seleccionada, que puede ser ver CSV o consultar
*/
function enviar(argumento)
{	document.formSeleccion.action=argumento+"&"+document.formSeleccion.params.value;
	document.formSeleccion.submit();
}


</script>
	 
<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
			<script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
			<script language="JavaScript" type="text/JavaScript">  
				setRutaRaiz('<?php echo $ruta_raiz; ?>')
		 <!--
			<?
				$ano_ini = date("Y");
				$mes_ini = substr("00".(date("m")-1),-2);
				if ($mes_ini==0) {$ano_ini==$ano_ini-1; $mes_ini="12";}
				$dia_ini = date("d");
				if(!$fecha_ini) $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
					$fecha_busq = date("Y/m/d") ;
				if(!$fecha_fin) $fecha_fin = $fecha_busq;
			?>
   var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "formSeleccion", "fecha_ini","btnDate1","<?=$fecha_ini?>",scBTNMODE_CUSTOMBLUE);
   var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "formSeleccion", "fecha_fin","btnDate2","<?=$fecha_fin?>",scBTNMODE_CUSTOMBLUE);

--></script>


<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<div id="spiffycalendar" class="text"></div>
<?
$params = session_name()."=".session_id()."&krd=$krd";
?>

<form action="inventariod.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formSeleccion" id="formSeleccion" >

<table width="57%" border="0" cellspacing="5" cellpadding="0" align="center" class='borde_tab'>
	<tr align="center">
		<td height="25" colspan="3" class='titulos2'>
			CONSULTA DE INVENTARIO DOCUMENTAL 2012
        	  <input name="accion" type="hidden" id="accion">
        	<input type="hidden" name="params" value="<?=$params?>">
      </td>
    </tr>
    </tr>
    <tr align="center" colspan="2">
		<td width="31%" class='titulos2'>DEPENDENCIA</td>
		<td width="69%" height="30" class='listado2' align="left">
		
		 <?
   $sql = "SELECT 'Todas las dependencias' as DEPE_NOMB, 0 AS DEPE_CODI FROM DEPENDENCIA
   				 UNION SELECT 'SUBDIRECCION ADMINISTRATIVA CONSOLIDADO' AS DEPE_NOMB, 3 AS DEPE_CODI FROM DEPENDENCIA
                 UNION  SELECT DEPE_NOMB, DEPE_CODI AS DEPE_CODI FROM DEPENDENCIA
                 WHERE DEPE_CODI NOT IN (900,905,999,910,1,321,210)
				 order by DEPE_NOMB DESC";
	$rsDep = $db->conn->Execute($sql);
	if(!$s_DEPE_CODI) $s_DEPE_CODI= 0;
	print $rsDep->GetMenu2("dep","$dep",false, false, 0," class='select'");
	
	?>
	</td>
</tr> 
    
<tr>
    <td align="center" width="30%" class="titulos2">Desde  fecha (aaaa/mm/dd) </td>
    <td class="listado2">
	<script language="javascript">
	dateAvailable.writeControl();
	dateAvailable.dateFormat="yyyy/MM/dd";
	</script>&nbsp;</td>
  </tr>
  
<tr>
    <td align="center" width="30%" class="titulos2">Hasta  fecha (aaaa/mm/dd) </td>
    <td class="listado2">
	<script language="javascript">
	dateAvailable2.writeControl();
	dateAvailable2.dateFormat="yyyy/MM/dd";
	</script>&nbsp;</td>
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

	
if(!empty($_POST['Consultar'])&& ($_POST['Consultar']=="Consultar")){

			
			require_once($ruta_raiz."include/myPaginador.inc.php");
		 			
			
$where=null;

			$where=(!empty($_POST['dep']) && ($_POST['dep'])!="")?"AND s.depe_codi = ".$_POST['dep']:""; 
			
			
			if ($dep==3)
			{
			  $where=" AND s.depe_codi in (301,310,320,341,350) ";	
			}
				
			$where.= " AND TRUNC (S.SGD_SEXP_FECH) BETWEEN (".$db->conn->DBTimeStamp($fecha_ini).") AND (".$db->conn->DBTimeStamp($fecha_fin).")";
			
			$camposConcatenar = "(" . $db->conn->Concat("s.sgd_sexp_parexp1",
                                                    "s.sgd_sexp_parexp2",
                                                    "s.sgd_sexp_parexp3",
                                                    "s.sgd_sexp_parexp4",
                                                    "s.sgd_sexp_parexp5") . ")";
		  				
							
		$con=0;
 			$order=1;      	
			$titulos=array("No.","COD DEP","NUM EXPEDIENTE","NOMBRE DEL EXPEDIENTE","FECHA INICIAL","FECHA FINAL","CAJA","CARPETA","TOMO","OTRO","FOLIOS","SOPORTE","FREC CONSULTA","NOTA");
      	
			
    $isql= "select distinct(s.sgd_exp_numero) AS EXP,
                    s.depe_codi AS DEP, S.SGD_SEXP_FECH AS FECHEXP,
                    $camposConcatenar as PARAMETRO, 
                    (select sum(radi_nume_hoja) as hojas from radicado r, sgd_exp_expediente x where 
                    r.radi_nume_radi=x.radi_nume_radi and x.sgd_exp_numero=s.sgd_exp_numero
                    and x.sgd_exp_numero=s.sgd_exp_numero ) as FOLIOS,  s.sgd_cerrado,
                    round(to_date (max(e.sgd_exp_fech) OVER (PARTITION BY e.sgd_exp_numero))) as fvigencia_ag
            from sgd_sexp_secexpedientes s
            		INNER JOIN SGD_EXP_EXPEDIENTE E ON S.sgd_exp_numero = E.sgd_exp_numero,
            		dependencia d
                    where  
                    s.depe_codi not in (900,905,910,999) and
                    s.depe_codi = d.depe_codi
                    {$where}
                    ORDER BY S.SGD_SEXP_FECH ";
  //$db->conn->debug = true;


  $isqli="select rownum as orden, DEP, exp,  FECHEXP, PARAMETRO, FOLIOS, fvigencia_ag from ($isql) ";
  
  
  $noArchivo = "/pdfs/reporteexp_$fechah.pdf";
 

			$paginador= new myPaginador($db,$isqli,1);
			$paginador->modoPintado(true);
			$paginador->setImagenASC($ruta_raiz."iconos/flechaasc.gif");
			$paginador->setImagenDESC($ruta_raiz."iconos/flechadesc.gif");
			$paginador->setFuncionFilas("pintarResultados");
			
		
error_reporting(7);
	require "../anulacion/class_control_anu.php";
	$btt = new CONTROL_ORFEO($db);
	$campos_align = array("C","C","L","L","C","L","L","C");
	$campos_tabla = array("ORDEN","DEP","EXP","PARAMETRO","FECHEXP","FVIGENCIA_AG","","","","","FOLIOS","","","");
	$campos_width = array (30,30,100,500,80,80,30,30,30,30,60,90,90,100);
	$btt->campos_align = $campos_align;
	$btt->campos_tabla = $campos_tabla; 	
	$btt->campos_vista = $titulos;
	$btt->campos_width = $campos_width;
	
	$btt->tabla_sql($isqli);
	error_reporting(7);
		
	$html= $btt->tabla_html;
	
	
	define(FPDF_FONTPATH,'../fpdf/font/');
	require("../fpdf/planillasinv.php");

	$pdf = new PDF("L","mm","Legal");
	$pdf->AddPage();
	$encabezado = "<td height=100></td></tr>
		</table>";
	$pdf->AliasNbPages();
	$pdf->SetFont('Arial','',8);
	$pdf->WriteHTML($encabezado.$html);
	//save and redirect
	$noArchivo = "../bodega".$noArchivo;
	if($pdf->Output($noArchivo)){}
		?>
		<center><span class="leidos">Imprimir Reporte <a href='<?=$noArchivo?>'>Aqui</a></center>
		<?
	exit;
	
	}


?>

</body>
</html>
