<?php

/**
 * M?dulo de listado de radicaci?n R?pida.
 * Desarollo: Grupo Iyunxi Ltda.
 * Autor:IYU.
 * Julio de 2010.
 * 
 * La generacion de este Reporte requiere implementar
 * el proyecto (GPL) Pdftk.
 */

//Seguridad
session_start();
//if (!$_SESSION["USUA_PRAD_REPRAD"]) die("Error accesando la p&aacute;gina. No tiene Privilegios.");

$ruta_raiz="..";
include "$ruta_raiz/processConfig.php";		// incluir configuracion.
define('ADODB_ASSOC_CASE', 1);
include "$ADODB_PATH/adodb.inc.php";	// $ADODB_PATH configurada en config.php
include "$ruta_raiz/include/db/ConnectionHandler.php";
$ADODB_COUNTRECS = false;

$fecha1 = isset($_POST['fecha1'])?$_POST['fecha1']:'';
$fecha2 = isset($_POST['fecha2'])?$_POST['fecha2']:'';

$hora_ini = $_POST['hora_ini'];
$minu_ini = $_POST['minu_ini'];
$seg_ini = $_POST['seg_ini'];

$hora_fin = $_POST['hora_fin'];
$minu_fin = $_POST['minu_fin'];
$seg_fin = $_POST['seg_fin'];

$error = 0;
$dsn = $driver."://".$usuario.":".$contrasena."@".$servidor."/".$db;
$conn = NewADOConnection($dsn);
if ($conn)
{	
	$conn->SetFetchMode(ADODB_FETCH_ASSOC);
//$conn->debug=true;
	$sqlfn = $conn->SQLDate('Y-m-d-H-i-s',$conn->sysTimeStamp);
	$sql = "SELECT $sqlfn as fecha_hoy FROM usuario";
	if ($var_date == $conn->GetOne($sql))
	{
		$var = split('-',$var_date);
		$txt_seg = $var[5];
		$txt_min = $var[4];
		$txt_hor = $var[3];
		$txt_dia = $var[2];
		$txt_mes = $var[1];
		$txt_ano = $var[0];
	}
	
	//Capturamos variable que nos indica si se desea generar reporte estadistico
	$generar_reporte_estadistico = (isset($_POST['generar_reporte_estadistico']))?$_POST['generar_reporte_estadistico']:0;
	
	//Validamos indicando si se hizo click en generar el reporte e identificamos si se trata de solo datos estadisticos	
	if (isset($_POST['btn_enviar']) and (int)$generar_reporte_estadistico == 0)
	{	
		//require_once("$ruta_raiz/fpdf/express1_fpdf.php");

		//include("$ADODB_PATH/tohtml.inc.php");
	 	$fi = $_POST['fecha1']." ".str_pad($hora_ini,2,0,STR_PAD_LEFT).":".str_pad($minu_ini,2,0,STR_PAD_LEFT).":".str_pad($seg_ini,2,0,STR_PAD_LEFT);
		$ff = $_POST['fecha2']." ".str_pad($hora_fin,2,0,STR_PAD_LEFT).":".str_pad($minu_fin,2,0,STR_PAD_LEFT).":".str_pad($seg_fin,2,0,STR_PAD_LEFT); 
		$dep = ($_POST['slc_dep']==0) ? ">0" : "=". $_POST['slc_dep'];
		switch ($conn->databaseType)
		{
			case 'postgres':
			{
                            $radEntrada=$conn->substr."(cast(r.radi_nume_radi as varchar),14,1)='2' ";
			}break;
			case 'oci8po':
                        case 'oci8':
			{
                            $radEntrada=" r.radi_nume_radi like '%2' ";
			}break;
			default:
			{
                            $radEntrada=$conn->substr."(cast(r.radi_nume_radi as varchar),14,1)='2' ";
			}break;
		}
		$sqlfn = $conn->SQLDate('Y-m-d H:i:s','his.hist_fech');
		$sqlw = "WHERE ";
		$sqlw.= $sqlfn." BETWEEN '$fi' AND '$ff'";
		$sqlw.= " $ugt AND r.radi_depe_radi $dep AND r.radi_usua_actu !=0 AND ".$radEntrada;
		
		$sql_dep = "SELECT d.depe_nomb as depDest, r.radi_nume_radi, r.radi_nume_hoja, 
                                   r.radi_desc_anex,r.radi_cuentai,
                                   s.sgd_dir_nomremdes, s.sgd_dir_nombre ,his.hist_fech,
                                   di.depe_nomb as depInf
                            FROM RADICADO r
                            join (select max(hist_fech) as hist_fech,radi_nume_radi from hist_eventos where sgd_ttr_codigo in (60,11,2) and depe_codi_dest $dep group by radi_nume_radi) fecHis on fecHis.radi_nume_radi=r.radi_nume_radi
                            join hist_eventos his on his.radi_nume_radi=r.radi_nume_radi and his.hist_fech=fecHis.hist_fech and his.sgd_ttr_codigo in (60,11,2)
                            join dependencia d on r.radi_depe_radi=d.depe_codi
                            join sgd_dir_drecciones s on r.radi_nume_radi=s.radi_nume_radi
                            left join informados i on  r.radi_nume_radi=i.radi_nume_radi and i.usua_codi=1 and r.radi_depe_radi<>i.depe_codi
                            left join dependencia di on  di.depe_codi=i.depe_codi
                            $sqlw
                            order by d.depe_nomb,r.radi_nume_radi";
		$ADODB_COUNTRECS = false;

		$rs = $conn->Execute($sql_dep);
		//$algo = $rs->GetAssoc();
		$i=-1;
		$k=0;
		$band=true;
		$radicado=$rs->fields['RADI_NUME_RADI'];
		
		if($_POST['fecha1'] != "--" && $_POST['fecha2'] != "--")
		{
			 $permitir_busq = 1;
		}
		
		if($permitir_busq > 0)
		{
		  if($rs && !$rs->EOF)
		  {
			while(!$rs->EOF)
			{
				if($band)
				{
					$i++;
				    $concatenar1="(nombre_de_la_empresa||' '||sigla_de_la_empresa||' '||nombre_rep_legal)";
					$concatenar2="(nombre_de_la_empresa||' '||nombre_rep_legal)";
					$arreglo_remitente="SELECT nombre_de_la_empresa FROM bodega_empresas WHERE $concatenar1 ='".$rs->fields['SGD_DIR_NOMREMDES']."' or $concatenar2='".$rs->fields['SGD_DIR_NOMREMDES']."'";
					$rsarreglo = $conn->Execute($arreglo_remitente);
					
					if($rsarreglo && !$rsarreglo->EOF)
					{
						$remitente_def= $rsarreglo->fields['NOMBRE_DE_LA_EMPRESA'];
					}
					else 
					{
						$remitente_def=$rs->fields['SGD_DIR_NOMREMDES'];
					}
					$depDest[$i]=array('DEPDESTINO'=>$rs->fields['DEPDEST'],
                                                            'RADICADO'=>$rs->fields['RADI_NUME_RADI'],
                                                            'NHOJA'=>$rs->fields['RADI_NUME_HOJA'],
                                                            'ANEXOS'=>$rs->fields['RADI_DESC_ANEX'],
                                                            'URGENTE'=>$rs->fields['SGD_ID_URGENTE'],
                                                            'NOFICIO'=>$rs->fields['RADI_CUENTAI'],
                                                            'REMITENTE'=>$remitente_def,
                                                            'DIGNATARIO'=>$rs->fields['SGD_DIR_NOMBRE'],
                                                            'FECHAMOD'=>$rs->fields['HIST_FECH'],
                                                            'DEPINF'=>$rs->fields['DEPINF'],
                                                            'TIPO'=>0);
					if($rs->fields['DEPINF'])
					{       $i++;
						$depDest[$i]=array('DEPDESTINO'=>$rs->fields['DEPINF'],
										'RADICADO'=>$rs->fields['RADI_NUME_RADI'],
										'NHOJA'=>$rs->fields['RADI_NUME_HOJA'],
										'ANEXOS'=>$rs->fields['RADI_DESC_ANEX'],
										'URGENTE'=>$rs->fields['SGD_ID_URGENTE'],
										'NOFICIO'=>$rs->fields['RADI_CUENTAI'],
										'REMITENTE'=>$remitente_def,
										'DIGNATARIO'=>$rs->fields['SGD_DIR_NOMBRE'],
										'FECHAMOD'=>$rs->fields['HIST_FECH'],
										'DEPINF'=>$rs->fields['DEPINF'],
										'TIPO'=>1);
						
					}
				}
				else if($rs->fields['DEPINF'])
				{
                                    $i++;
                                    $depDest[$i]=array('DEPDESTINO'=>$rs->fields['DEPINF'],
                                                        'RADICADO'=>$rs->fields['RADI_NUME_RADI'],
                                                        'NHOJA'=>$rs->fields['RADI_NUME_HOJA'],
                                                        'ANEXOS'=>$rs->fields['RADI_DESC_ANEX'],
                                                        'URGENTE'=>$rs->fields['SGD_ID_URGENTE'],
                                                        'NOFICIO'=>$rs->fields['RADI_CUENTAI'],
                                                        'REMITENTE'=>$remitente_def,
                                                        'DIGNATARIO'=>$rs->fields['SGD_DIR_NOMBRE'],
                                                        'FECHAMOD'=>$rs->fields['HIST_FECH'],
                                                        'DEPINF'=>$rs->fields['DEPINF'],
                                                        'TIPO'=>1);

				}
				$radicado=$rs->fields['RADI_NUME_RADI'];
				
				$rs->MoveNext();
				if($radicado==$rs->fields['RADI_NUME_RADI'])$band=false;
				else $band=true;
			}
            require_once($ruta_raiz."/include/class/ReportExpressPDF2.php");
            $objReport= new ReportExpressPDF();
            $objReport->fechaIni=$fi;
            $objReport->fechaFin=$ff;
            $objReport->titulo='listado radicacion';
            $objReport->fechaHoy=date('Y-m-d H:i:s');
            $objReport->Entidad=$entidad_largo;
            $objReport->creaFormato($depDest);
            $msg = "<a href='".$objReport->enlacePDF()."' target='".date("dmYh").time("his")."' class='vinculos'>Abrir Archivo Pdf</a>";
			//$msg = "<a href='../$carpetaBodega/".$objReport->enlacePDF()."' target='".date("dmYh").time("his")."' class='vinculos'>Abrir Archivo Pdf</a>";
		   }
		}
		else 
		{	
			$msg = "El criterio de b&uacute;squeda no arroja registros.";	
		}	
	}
	elseif (isset($_POST['btn_enviar']) and (int)$generar_reporte_estadistico == 1)
	{	
		//Inicializamos valores de fecha
		$fi = $_POST['fecha1']." ".str_pad($hora_ini,2,0,STR_PAD_LEFT).":".str_pad($minu_ini,2,0,STR_PAD_LEFT).":".str_pad($seg_ini,2,0,STR_PAD_LEFT);
		$ff = $_POST['fecha2']." ".str_pad($hora_fin,2,0,STR_PAD_LEFT).":".str_pad($minu_fin,2,0,STR_PAD_LEFT).":".str_pad($seg_fin,2,0,STR_PAD_LEFT);
		
		$fdiario = date('Y-m-d',strtotime($_POST['fecha2'])-86400);
		$fi_diario = date('Y-m-d',strtotime($_POST['fecha2'])-86400).' 00:00:00';
		$ff_diario = $_POST['fecha2'].' 00:00:00';		
	
		//Creamos filtro de fechas
		$sqlfn = $conn->SQLDate('Y-m-d H:i:s','radi_fech_radi');
		$sqlw  = $sqlfn." BETWEEN '$fi' AND '$ff'";
		$sqlw_diario  = $sqlfn." BETWEEN '$fi_diario' AND '$ff_diario'";
		
		//Construimos sentencia que obtiene consolidado nacional de las dependencias de radicacion
		//y enviado a la dependencia 213
		$sql_consolidado_nacional = "SELECT COUNT(radi_nume_radi) AS CONSOLIDADO_NACIONAL FROM radicado WHERE 
		(radi_nume_radi like '%2013500%' or radi_nume_radi like '%2013901%' or radi_nume_radi like '%2013902%' or 
		radi_nume_radi like '%2013903%' or radi_nume_radi like '%2013904%' or radi_nume_radi like '%2013905%' or
		radi_nume_radi like '%2013906%' or radi_nume_radi like '%2013907%' or radi_nume_radi like '%2013908%') and 
		$sqlw and radi_depe_radi = 214";
		
		//Construimos sentencia que obtiene consolidado diario (fecha final) de las dependencias de radicacion
		//y enviado a la dependencia 213
		$sql_consolidado_diario = "SELECT COUNT(radi_nume_radi) AS DIARIO_NACIONAL FROM radicado WHERE 
		(radi_nume_radi like '%2013500%' or radi_nume_radi like '%2013901%' or radi_nume_radi like '%2013902%' or 
		radi_nume_radi like '%2013903%' or radi_nume_radi like '%2013904%' or radi_nume_radi like '%2013905%' or
		radi_nume_radi like '%2013906%' or radi_nume_radi like '%2013907%' or radi_nume_radi like '%2013908%') and 
		$sqlw_diario and radi_depe_radi = 214";
		
		$ciudades = array('BOGOTA','IBAGUE','MEDELLIN','NOBSA','BUCARAMANGA','CALI','VALLEDUPAR','CUCUTA','PASTO');
		
		$sql_consolidado_ciudades = array();
		$sql_diario_ciudades = array();
		
		//Creamos consolidado de cada ciudad
		for ($i=0; $i<=8; $i++)
		{
			//definimos like a hacer
			$like = ($i==0)?" like '%2013500%' ":"like '%201390$i%'";
			
			//Construimos sentencia consolidado ciudades
			$sql_consolidado_ciudades[] = "SELECT COUNT(radi_nume_radi) AS CONSOLIDADO_".$ciudades[$i]." FROM radicado WHERE 
			(radi_nume_radi $like ) and $sqlw and radi_depe_radi = 214"; 
			
			//Construimos sentencia diario de ciudades
			$sql_diario_ciudades[] = "SELECT COUNT(radi_nume_radi) AS DIARIO_".$ciudades[$i]." FROM radicado WHERE 
			(radi_nume_radi $like ) and $sqlw_diario and radi_depe_radi = 214";
		}
		
		//Ejecutamos
		$ADODB_COUNTRECS = false;
		
		//Nacional
		$rs_cn = $conn->Execute($sql_consolidado_nacional);		
		$rs_dn = $conn->Execute($sql_consolidado_diario);		
		
		//Bogota
		$rs_cb = $conn->Execute($sql_consolidado_ciudades[0]);		
		$rs_db = $conn->Execute($sql_diario_ciudades[0]);		
		
		//Ibague
		$rs_ci = $conn->Execute($sql_consolidado_ciudades[1]);		
		$rs_di = $conn->Execute($sql_diario_ciudades[1]);		
		
		//Medellin
		$rs_cm = $conn->Execute($sql_consolidado_ciudades[2]);		
		$rs_dm = $conn->Execute($sql_diario_ciudades[2]);		
		
		//Nobsa
		$rs_cno = $conn->Execute($sql_consolidado_ciudades[3]);		
		$rs_dno = $conn->Execute($sql_diario_ciudades[3]);		
		
		//Bucaramanga
		$rs_cbu = $conn->Execute($sql_consolidado_ciudades[4]);		
		$rs_dbu = $conn->Execute($sql_diario_ciudades[4]);		
		
		//Cali
		$rs_cc = $conn->Execute($sql_consolidado_ciudades[5]);		
		$rs_dc = $conn->Execute($sql_diario_ciudades[5]);		
		
		//Valledupar
		$rs_cv = $conn->Execute($sql_consolidado_ciudades[6]);		
		$rs_dv = $conn->Execute($sql_diario_ciudades[6]);

		//Cucuta
		$rs_ccu = $conn->Execute($sql_consolidado_ciudades[7]);		
		$rs_dcu = $conn->Execute($sql_diario_ciudades[7]);	

		//Pasto
		$rs_cp = $conn->Execute($sql_consolidado_ciudades[8]);		
		$rs_dp = $conn->Execute($sql_diario_ciudades[8]);			
		
		//Extraemos dato
		$datos_estadisticos = array();
		$datos_estadisticos['Nacional'][] = $rs_cn->fields['CONSOLIDADO_NACIONAL'];		
		$datos_estadisticos['Nacional'][] = $rs_dn->fields['DIARIO_NACIONAL'];
		$datos_estadisticos['Bogota'][] = $rs_cb->fields['CONSOLIDADO_BOGOTA'];
		$datos_estadisticos['Bogota'][] = $rs_db->fields['DIARIO_BOGOTA'];
		$datos_estadisticos['Ibague'][] = $rs_ci->fields['CONSOLIDADO_IBAGUE'];
		$datos_estadisticos['Ibague'][] = $rs_di->fields['DIARIO_IBAGUE'];
		$datos_estadisticos['Medellin'][] = $rs_cm->fields['CONSOLIDADO_MEDELLIN'];
		$datos_estadisticos['Medellin'][] = $rs_dm->fields['DIARIO_MEDELLIN'];
		$datos_estadisticos['Nobsa'][] = $rs_cno->fields['CONSOLIDADO_NOBSA'];
		$datos_estadisticos['Nobsa'][] = $rs_dno->fields['DIARIO_NOBSA'];
		$datos_estadisticos['Bucaramanga'][] = $rs_cbu->fields['CONSOLIDADO_BUCARAMANGA'];
		$datos_estadisticos['Bucaramanga'][] = $rs_dbu->fields['DIARIO_BUCARAMANGA'];
		$datos_estadisticos['Cali'][] = $rs_cc->fields['CONSOLIDADO_CALI'];
		$datos_estadisticos['Cali'][] = $rs_dc->fields['DIARIO_CALI'];
		$datos_estadisticos['Valledupar'][] = $rs_cv->fields['CONSOLIDADO_VALLEDUPAR'];
		$datos_estadisticos['Valledupar'][] = $rs_dv->fields['DIARIO_VALLEDUPAR'];
		$datos_estadisticos['Cucuta'][] = $rs_ccu->fields['CONSOLIDADO_CUCUTA'];
		$datos_estadisticos['Cucuta'][] = $rs_dcu->fields['DIARIO_CUCUTA'];
		$datos_estadisticos['Pasto'][] = $rs_cp->fields['CONSOLIDADO_PASTO'];
		$datos_estadisticos['Pasto'][] = $rs_dp->fields['DIARIO_PASTO'];
		
		//Creamos tabla
		$tabla = '<table style="width:900px"><tr><th style="text-align:center;width:200px;" colspan="3">DATOS ESTADISTICOS</th></tr>';
		$tabla.= '<tr><th style="text-align:left;width:200px;">DESCRIPCION</th><th>CONSOLIDADO</th><th>'.$fdiario.'</th></tr>';

		foreach ($datos_estadisticos as $descripcion => $item)
		{
			$tabla.= '<tr><td>'.$descripcion.'</td><td style="text-align:left;">'.$item[0].'</td><td style="text-align:left;">'.$item[1].'</td></tr>';
		}		
		$tabla.= '</table>';
		
		$msg = $tabla;	
	}
	
	//ciclo for para generar las opciones en los combos de horas,minutos y segundos
	for ($h=0; $h<24; $h++ )
	{	
		$ih .="<option value='$h'>$h</option>";
		$tmp = ($h==$txt_hor)? 'selected' : '';
		$fh .="<option value='$h' $tmp>$h</option>";
	}
	for ($m=0; $m<60; $m++ )
	{	
		$im .="<option value='$m'>$m</option>";
		$tmp = ($m==$txt_min)? 'selected' : '';
		$fm .="<option value='$m' $tmp>$m</option>";
	}
	for ($s=0; $s<60; $s++ )
	{	
		$is .="<option value='$s'>$s</option>";
		$tmp = ($s==$txt_seg)? 'selected' : '';
		$fs .="<option value='$s' $tmp>$s</option>";
	}	
	//Combo de dependencias.
	$cat_depe = $conn->concat('depe_codi',"' - '",'depe_nomb');
	$sql = "select $cat_depe, depe_codi from dependencia where depe_estado=1 order by 1 ";
	//$sql = "select depe_nomb,depe_codi from dependencia where depe_estado=1 order by 1 ";
	$rs = $conn->Execute($sql);
	$slc_dep1 = $rs->GetMenu2('slc_dep',$slc_dep,'0:&lt;&lt Todas las dependencias &gt;&gt;',false,false,'Class="select"');
}
?>
<html>
<head>
<? include ('../htmlheader.inc.php');?>
<title>Generaci&oacute;n de Listado Correspondencia R&aacute;pida</title>
<script language="JavaScript" src="<?=$ruta_raiz?>/js/formchek.js"></script>
<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css" type="text/css">
<script language="JavaScript">
function validar()
{
	if (date01.date > date02.date)
	{
		alert("La fecha inicial no debe ser mayor a la final.");
		return false;
	}
	if ((date01.date==date02.date) && (parseInt(hora_ini.value)==parseInt(hora_fin.value)) && (parseInt(minu_ini.value) > parseInt(minu_fin.value)))
	{
		alert("Los minutos iniciales no deben ser mayor a los minutos finales.");
		return false;
	}
}
</script>
</head>
<body>
<div id="spiffycalendar" class="text"></div>
<link rel="stylesheet" type="text/css" href="<?=$ruta_raiz?>/js/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="<?=$ruta_raiz?>/js/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="JavaScript">
	var date01 = new ctlSpiffyCalendarBox("date01", "frm_listexpress", "fecha1","btnDate1","<?= $fecha1;?>",scBTNMODE_CUSTOMBLUE);
	var date02 = new ctlSpiffyCalendarBox("date02", "frm_listexpress", "fecha2","btnDate2","<?= $fecha2;?>",scBTNMODE_CUSTOMBLUE);
</script>
<form name="frm_listexpress" id="frm_listexpress" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="table table-bordered table-striped mart-form" width='80%' cellspacing="5" align="center">
    <tr><td class=titulos2 colspan="4"><center>REPORTE DE RADICACI&Oacute;N DE CORRESPONDENCIA ENTRANTE</center></td></tr>
	<tr>
    	<td width="15%" height="21"  class='titulos2'>Fecha Inicial</td>
    	<td width="35%" align="left" valign="top" class='listado2'>
			<script language="JavaScript">
		        date01.date = "<?=$txt_ano."-".$txt_mes."-".$txt_dia?>";
			    date01.writeControl();
				date01.dateFormat="yyyy-MM-dd";
			</script>
		</td>
		<td width="15%" height="21"  class='titulos2'>Fecha Final</td>
    	<td width="35%" align="left" valign="top" class='listado2'>
			<script language="JavaScript">
                            date02.date = "<?=$txt_ano."-".$txt_mes."-".$txt_dia?>";
                            date02.writeControl();
                            date02.dateFormat="yyyy-MM-dd";
			</script>
		</td>
	</tr>
	<tr>
    	<td height="15%" class='titulos2'>Desde la Hora</td>
    	<td valign="35%" class='listado2'>
			<select name="hora_ini" id="hora_ini" class='select'><?php echo $ih; ?></select>:
			<select name="minu_ini" id="minu_ini" class='select'><?php echo $im; ?></select>:
			<select name="seg_ini" id="seg_ini" class='select'><?php echo $is; ?></select>
		</td>
    	<td height="15%" class='titulos2'>Hasta la Hora</td>
    	<td valign="35%" class='listado2'>
			<select name="hora_fin" id="hora_fin" class="select"><?php echo $fh; ?></select>:
			<select name="minu_fin" id="minu_fin" class="select"><?php echo $fm; ?></select>:
			<select name="seg_fin"  id="seg_fin"  class="select"><?php echo $fs; ?></select>
		</td>
	</tr>
	<tr>
    	<td height="26" class='titulos2'>Dependencia</td>
    	<td valign="top" class='listado2' colspan="3">
			<?php echo $slc_dep1; ?>
		</td>
	<!--<tr>
		<td height="26" class='titulos2'>Tipo de informe</td>
		<td valign="top" class='listado2' colspan="3">
			<input type="radio" name="rbt_ugt" value="0" checked>No Urgentes</input>
			<input type="radio" name="rbt_ugt" value="1">Urgentes</input>
			<input type="radio" name="rbt_ugt" value="2">D. Petici&oacute;n</input>
		</td>
	</tr>-->
	<tr>
    	<td height="26" colspan="4" valign="middle" align="left" class='titulos2'>
    		<div style="font-size:12px; font-weight:normal; padding:10px 5px 5px;">
<?php /*
				Seleccione la opci&oacute;n "Reporte estad&iacute;stico" para generar &uacute;nicamente en 
				pantalla un reporte de la cantidad de radicados hechos por las dependencias de radicac&oacute;n
				y enviados a la dependencia 214, recuerde que est&aacute; herramienta utilizara solamente las fechas seleccionadas, 
				para realizar el reporte, por &uacute;ltimo, presione sobre "generar".
*/ ?>
			</div>
		</td>
	</tr>
	<tr>
    	<td height="26" valign="middle" align="center" class='titulos2'>
    		Reporte estad&iacute;stico
		</td>
		<td height="26" colspan="3" valign="middle" align="left" class='listado2'>
    		<input type="checkbox" value="1" style="cursor:pointer;" name="generar_reporte_estadistico" id="generar_reporte_estadistico" <?php if ($generar_reporte_estadistico) echo 'checked'; ?> />
		</td>
	</tr>
	<tr>
    	<td height="26" colspan="4" valign="middle" align="center" class='titulos2'>
    		<input type="submit" class='botones_largo' value="Generar" name="btn_enviar" id="btn_enviar" onClick="return validar();">
		</td>
	</tr>
	<tr>
		<td colspan="4" class="listado2" align="center"><?php echo $msg; ?>
		</td>
	</tr>
</table>
</form>
</body>
</html>
