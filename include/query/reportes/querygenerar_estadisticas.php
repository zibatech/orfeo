<?
	/**
	  * CONSULTA VERIFICACION PREVIA A LA RADICACION
	  */
	switch($db->driver)
	{  
	 case 'mssql':
	 $radi_nume_sal = "convert(varchar(14), RADI_NUME_SAL)";
	 $query = "select  
		d.sgd_fenv_descrip,
		c.depe_nomb,
		$radi_nume_sal as radi_nume_sal,
		a.sgd_renv_nombre,
		a.sgd_renv_dir,
		a.sgd_renv_mpio,
		a.sgd_renv_depto,
		a.sgd_renv_fech,
		a.sgd_deve_fech,
		b.sgd_deve_desc,
		'-' firma,
		a.sgd_renv_planilla,
		a.sgd_renv_cantidad,
		a.sgd_renv_valor
		from SGD_RENV_REGENVIO a, 
			 sgd_deve_dev_envio  b,
			 dependencia c,
			 SGD_FENV_FRMENVIO d ";
		$fecha_mes = substr($fecha_ini,0,7);
	 	$where_isql = ' WHERE a.sgd_deve_fech BETWEEN
   	                  '.$db->conn->DBTimeStamp($fecha_ini).' and '.$db->conn->DBTimeStamp($fecha_fin).'
	                  and a.sgd_deve_codigo=b.sgd_deve_codigo
	                  and a.sgd_fenv_codigo=d.sgd_fenv_codigo
	                  and '.$db->conn->substr.'('.$radi_nume_sal.', 5, 3)=c.depe_codi
	                  and a.sgd_deve_codigo is not null
	                  ';
	break;		
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'postgres':
	
		$query = "
				SELECT 
				a.anex_tipo_envio as tipo_envio,
				u.usua_nomb as usuario,
				d.depe_nomb as dependencia,
				r.radi_nume_radi as radicado,
				he.hist_obse as observaciones,
				DATE(he.hist_fech) as fecha
			FROM
				hist_eventos he 
				LEFT JOIN usuario u ON u.usua_codi = he.usua_codi AND u.depe_codi = he.depe_codi
				LEFT JOIN anexos a ON he.radi_nume_radi = a.radi_nume_salida
				LEFT JOIN radicado r ON r.radi_nume_radi = he.radi_nume_radi
				LEFT JOIN dependencia d ON r.radi_depe_actu = d.depe_codi
			WHERE 
				he.sgd_ttr_codigo = 28";
			$where_isql = ' AND he.hist_fech BETWEEN
				'.$db->conn->DBTimeStamp($fecha_ini).' AND '.$db->conn->DBTimeStamp($fecha_fin);
	break;		
		
	//Modificado skina
	default:
			/*$query = "select  
					d.sgd_fenv_descrip,
					c.depe_nomb,
					a.radi_nume_sal,
					a.sgd_renv_nombre,
					a.sgd_renv_dir,
					a.sgd_renv_mpio,
					a.sgd_renv_depto,
					a.sgd_renv_fech,
					a.sgd_deve_fech,
					b.sgd_deve_desc,
					'-'  as firma,
					a.sgd_renv_planilla,
					a.sgd_renv_cantidad,
					a.sgd_renv_valor
					from SGD_RENV_REGENVIO a, 
						sgd_deve_dev_envio  b,
						dependencia c,
						SGD_FENV_FRMENVIO d ";
			$fecha_mes = substr($fecha_ini,0,7);
			$where_isql = ' WHERE a.sgd_deve_fech BETWEEN
					'.$db->conn->DBTimeStamp($fecha_ini).' and '.$db->conn->DBTimeStamp($fecha_fin).'
					and a.sgd_deve_codigo=b.sgd_deve_codigo
					and a.sgd_fenv_codigo=d.sgd_fenv_codigo
					and '.$db->conn->substr.'(a.radi_nume_sal, 5, 3)=c.depe_codi
					and a.sgd_deve_codigo is not null
			';*/

			$query = "
				SELECT 
					a.anex_tipo_envio,
					d.depe_nomb,
					r.radi_nume_radi,
					he.hist_obse,
					DATE(he.hist_fech)
				FROM
					hist_eventos he 
					LEFT JOIN anexos a ON he.radi_nume_radi = a.radi_nume_salida
					LEFT JOIN radicado r ON r.radi_nume_radi = he.radi_nume_radi
					LEFT JOIN dependencia d ON r.radi_depe_actu = d.depe_codi
				WHERE 
					he.sgd_ttr_codigo = 28";
				$where_isql = ' AND he.hist_fech BETWEEN
					'.$db->conn->DBTimeStamp($fecha_ini).' AND '.$db->conn->DBTimeStamp($fecha_fin);
	}
?>