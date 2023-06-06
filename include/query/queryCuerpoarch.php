<?
switch($db->driver)
{	case 'oracle':
	case 'oci8':
	{	

	$camposConcatenar = "(" . $db->conn->Concat("s.sgd_sexp_parexp1",
                                                    "s.sgd_sexp_parexp2",
                                                    "s.sgd_sexp_parexp3",
                                                    "s.sgd_sexp_parexp4",
                                                    "s.sgd_sexp_parexp5") . ")";
//
$radi_nume_radi = "to_char(e.RADI_NUME_RADI)";
$tmp_cad1 = "to_char(".$radi_nume_radi.")";
$expediente="to_char(s.sgd_exp_numero)";
	
 $isql= "select distinct( e.radi_nume_radi) as Radicado, r.radi_fech_radi as Fecha_Radicado, s.sgd_exp_numero as Expediente, 
                    $camposConcatenar as Etiqueta, 
                    E.SGD_EXP_FECH as Fecha_Incluido,
                    d.depe_nomb as Dependencia,
                    (se.sgd_srd_descrip||' - '||su.sgd_sbrd_descrip) AS Serie_Subserie,
                    $tmp_cad1 AS CHK_checkValue
            from sgd_exp_expediente E
            		INNER JOIN SGD_SEXP_SECEXPEDIENTES S ON E.sgd_exp_numero = S.sgd_exp_numero
            		INNER JOIN 	RADICADO R ON E.RADI_NUME_RADI = R.RADI_NUME_RADI,
            		dependencia d,
                    sgd_srd_seriesrd se,
                    sgd_sbrd_subserierd su
                    where e.sgd_exp_estado=0 AND 
                    e.radi_nume_radi not in (select radi_nume_radi from SGD_EXP_EXPEDIENTE where radi_nume_radi like '%3') and
                    s.sgd_srd_codigo = se.sgd_srd_codigo and
                    s.sgd_sbrd_codigo = su.sgd_sbrd_codigo and
                    s.sgd_srd_codigo  = su.sgd_srd_codigo and
                    s.depe_codi not in (900,905,910,999) and
                    s.depe_codi = d.depe_codi
                    $where_filtro
                    order by $order $orderTipo";	
 //$db->conn->debug = true;
 //print $radi_nume_radi;
 	}break;		
}
 	?>

