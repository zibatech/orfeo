<?
/**
 * CONSULTA VERIFICACION PREVIA A LA RADICACION
 */
switch($db->driver)
{	case 'mssql':
	{	$tmp_EspCampo = "";
		$tmp_EspTabla = "";

		$query1 = " SELECT
	  	a.RADI_NUME_HOJA ,
		a.RADI_FECH_RADI ,
		convert(varchar(15), a.radi_nume_radi) as radi_nume_radi,
		a.RA_ASUN ,
		a.RADI_PATH ,
		a.RADI_USU_ANTE ,
		a.RADI_USUA_ACTU ,
		$tmp_EspCampo
		$sqlFecha AS FECHA ,
		b.sgd_tpr_descrip ,
		b.sgd_tpr_codigo ,
		b.sgd_tpr_termino,
		RADI_LEIDO ,
		RADI_TIPO_DERI ,
		convert(varchar(15), radi_nume_deri) as RADI_NUME_DERI ,
		d.SGD_DIR_TIPO,
		d.SGD_DIR_NOMBRE,
		d.SGD_DIR_NOMREMDES,
		a.radi_cuentai,
		g.SGD_EXP_NUMERO
		FROM RADICADO a
			LEFT OUTER JOIN SGD_EXP_EXPEDIENTE g
				ON a.radi_nume_radi  =g.radi_nume_radi,
				SGD_TPR_TPDCUMENTO b,
				$tmp_EspTabla
				SGD_DIR_DRECCIONES d, USUARIO u,
    dependencia depa, dependencia depb
		WHERE
		a.radi_nume_radi = convert(varchar(15), d.radi_nume_radi) AND
    a.radi_depe_actu = depa.depe_codi and
    a.radi_depe_radi = depb.depe_codi and
    u.usua_codi = a.radi_usua_actu and
    u.depe_codi = a.radi_depe_actu and
		a.tdoc_codi=b.sgd_tpr_codigo
		$whereTrd
		$where_ciu
		$where_general";
	}break;
	case 'oracle':
	case 'oci8':
	{$query1 = "
		SELECT
		a.RADI_NUME_HOJA ,
		a.RADI_FECH_RADI  ,
		a.radi_nume_radi ,
		a.RA_ASUN  ,
		a.RADI_PATH ,
		a.RADI_USU_ANTE ,
		a.RADI_USUA_ACTU ,
		'' AS R_RADI_NOMB ,
		$sqlFecha AS FECHA ,
		b.sgd_tpr_descrip ,
		b.sgd_tpr_codigo ,
		b.sgd_tpr_termino,
		RADI_LEIDO ,
		RADI_TIPO_DERI ,
		RADI_NUME_DERI ,
		d.SGD_DIR_NOMREMDES,
		d.SGD_DIR_TIPO,
		d.SGD_DIR_NOMBRE,
		a.radi_cuentai,
		g.SGD_EXP_NUMERO
		FROM RADICADO a,SGD_TPR_TPDCUMENTO b, SGD_DIR_DRECCIONES d, SGD_EXP_EXPEDIENTE g, USUARIO u,
    dependencia depa, dependencia depb
		WHERE
    a.radi_depe_actu = depa.depe_codi and
    a.radi_depe_radi = depb.depe_codi and
		a.radi_nume_radi =d.radi_nume_radi AND
		a.radi_nume_radi  =g.radi_nume_radi (+) AND
    u.usua_codi = a.radi_usua_actu and
    u.depe_codi = a.radi_depe_actu and
		a.tdoc_codi=b.sgd_tpr_codigo
		$whereTrd
		$where_ciu
		$where_general
		and rownum <= 200
		order by radi_fech_radi desc";
	}break;
	case 'postgres':
		$query1 = "
		SELECT
		a.RADI_NUME_HOJA ,
		a.RADI_FECH_RADI  ,
		a.radi_nume_radi ,
		a.RA_ASUN  ,
		a.RADI_PATH ,
		a.RADI_USU_ANTE ,
		a.RADI_USUA_ACTU ,
		depa.DEPE_NOMB as DEPE_ACTUAL,
		depb.DEPE_NOMB as DEPE_RADICA,
		u.USUA_NOMB ,
		'' AS R_RADI_NOMB ,
		$sqlFecha AS FECHA ,
		RADI_LEIDO ,
		RADI_TIPO_DERI ,
		RADI_NUME_DERI ,
		a.radi_cuentai,
		g.SGD_EXP_NUMERO,
		$select_circular_ext
		FROM USUARIO u,
        dependencia depa, dependencia depb,
        $from_circular_ext
		RADICADO a LEFT JOIN SGD_EXP_EXPEDIENTE g ON a.radi_nume_radi =g.radi_nume_radi
		WHERE
        a.radi_depe_actu = depa.depe_codi and
        a.radi_depe_radi = depb.depe_codi and
        u.usua_codi = a.radi_usua_actu and
        u.depe_codi = a.radi_depe_actu and
		$where_circular_ext
		$where_ciu
		$where_general 
		$where_tipo
		order by radi_fech_radi desc
		limit 20";
		break;
}
?>
