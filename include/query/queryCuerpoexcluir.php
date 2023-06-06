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
                                                    
    $camposConcatenar2 = "(" . $db->conn->Concat("z.sgd_sexp_parexp1",
                                                    "z.sgd_sexp_parexp2",
                                                    "z.sgd_sexp_parexp3",
                                                    "z.sgd_sexp_parexp4",
                                                    "z.sgd_sexp_parexp5") . ")";
//
$radi_nume_radi = "to_char(R.RADI_NUME_RADI)";
$tmp_cad1 = "to_char(".$radi_nume_radi.")";
$expediente="to_char(s.sgd_exp_numero)";
	
 $isql= 'select distinct( r.radi_nume_radi) as Radicado, 
 					'.$sqlFecha.'  "DAT_Fecha Radicado",
 					'.$radi_nume_radi.' "HID_RADI_NUME_RADI", 
 					e.sgd_exp_fech_arch as Archivado, 
 					s.sgd_exp_numero as Excl_Expediente,
 					'.$sqlFechae.'  "Fecha_Excl",
 					substr(t.hist_obse,33,17) "HID_expexcl", 
 					substr(v.hist_obse,33,17) "HID_expincl",
 					'.$camposConcatenar.' as Etiqueta_Ex, 
                    h.sgd_exp_numero as Incl_Expediente, 
                    h.sgd_exp_fech as Fecha_Incl,
                     '.$camposConcatenar2.' as Etiqueta_Incl,
                     U.usua_login as Usuario,
                    '.$tmp_cad1.' "CHK_checkValue"
            from RADICADO R,
            		sgd_exp_expediente E,
            		sgd_exp_expediente H,
            		SGD_SEXP_SECEXPEDIENTES S,
            		SGD_SEXP_SECEXPEDIENTES Z,
					HIST_EVENTOS T,
					HIST_EVENTOS V,
					USUARIO U					
                    where e.sgd_exp_estado=2 AND 
                    T.RADI_NUME_RADI = R.RADI_NUME_RADI and
                    E.RADI_NUME_RADI = R.RADI_NUME_RADI and
                    H.RADI_NUME_RADI = R.RADI_NUME_RADI and
                    E.sgd_exp_numero = S.sgd_exp_numero and
                    H.sgd_exp_numero = Z.sgd_exp_numero and
                    Z.sgd_exp_numero <> S.sgd_exp_numero and
                    h.sgd_exp_estado in (0,1) and
                    s.sgd_exp_numero=substr(t.hist_obse,33,17) and 
                    h.sgd_exp_numero=substr(v.hist_obse,33,17) and
                    R.radi_nume_radi not in (select radi_nume_radi from SGD_EXP_EXPEDIENTE where radi_nume_radi like '."'%3'".') and
                    s.depe_codi not in (900,905,910,999) and
                    t.hist_fech >= e.sgd_exp_fech_arch and
                    T.sgd_ttr_codigo=52 and
                    v.sgd_ttr_codigo=53 and
                    t.usua_doc=u.usua_doc
                     '.$where_filtro.'
                    order by '.$order.' '.$orderTipo;
               
 //$db->conn->debug = true;
 //print $radi_nume_radi;
 	}break;		
}
 	?>

