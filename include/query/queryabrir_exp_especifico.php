<?
switch($db->driver)
{	case 'oracle':
	case 'oci8':
	{	

	$camposConcatenar = "(" . $db->conn->Concat("sb.sgd_sexp_parexp1",
                                                    "sb.sgd_sexp_parexp2",
                                                    "sb.sgd_sexp_parexp3",
                                                    "sb.sgd_sexp_parexp4",
                                                    "sb.sgd_sexp_parexp5") . ")";
//
$expediente = "sb.sgd_exp_numero";
$tmp_cad1 = "to_char(".$expediente.")";

	$isqla="select  distinct sb.SGD_EXP_NUMERO as Expediente, 
					sb.SGD_sEXP_FECH as Fecha_Creado, 
					$camposConcatenar as Etiqueta, 
					(se.sgd_srd_descrip||' - '||sub.sgd_sbrd_descrip) AS Serie_Subserie, d.depe_nomb as Dependencia,
					round(to_date (max(s.sgd_exp_fech) OVER (PARTITION BY s.sgd_exp_numero)+(sub.sgd_sbrd_tiemag * 365) + 365)) as VENCE,
					 $tmp_cad1 AS CHK_checkValue
					from sgd_sexp_secexpedientes sb inner join sgd_exp_expediente s on sb.sgd_exp_numero=s.sgd_exp_numero, sgd_sbrd_subserierd sub, 
					dependencia d , sgd_srd_seriesrd se
					where 
					se.sgd_srd_codigo=sb.sgd_srd_codigo
					and sb.sgd_srd_codigo=sub.sgd_srd_codigo 
					and sb.sgd_sbrd_codigo=sub.sgd_sbrd_codigo
					and sb.depe_codi not in (905,910,999) 
					and d.dependencia_estado=1
					and sb.sgd_cerrado=1
					and sb.depe_codi = d.depe_codi
					$where_filtro
					";
			//$db->conn->debug = true;
			$rsa=$db->conn->query($isqla);
			
			$isql = "select distinct  Expediente, 
					Fecha_Creado, 
					Etiqueta, 
					Serie_Subserie, Dependencia,
					VENCE,
					CHK_checkValue
					 
					from ($isqla) 
					 
			        order by vence";
				//	$db->conn->debug = true;		
			//$rs=$db->conn->query($isql);

 	}break;		
}
 	?>

