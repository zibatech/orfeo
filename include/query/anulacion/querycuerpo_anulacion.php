<?php
/**
  * CONSULTA VERIFICACION PREVIA A LA RADICACION
  */
switch($db->driver){
	case 'mssql':
		$isql = 'select
				convert(varchar(14), b.RADI_NUME_RADI) "IMG_Numero Radicado",
				b.RADI_PATH "HID_RADI_PATH",
				convert(varchar(14),b.RADI_NUME_DERI) "Radicado Padre",
				b.RADI_FECH_RADI "HOR_RAD_FECH_RADI",'.
				$sqlFecha.' "Fecha Radicado",
				b.RA_ASUN "Descripcion",
				c.SGD_TPR_DESCRIP "Tipo Documento",
				convert(varchar(14),b.RADI_NUME_RADI) "CHK_CHKANULAR"
			from
				radicado b, SGD_TPR_TPDCUMENTO c
			where
				b.radi_nume_radi is not null
				and b.depe_codi='.$dep_sel.'
				and b.SGD_TRAD_CODIGO <> 2
				and b.tdoc_codi=c.sgd_tpr_codigo
				and sgd_eanu_codigo is null '.
				$whereTpAnulacion.' '.$whereFiltro.'
			order by '.$order .' ' .$orderTipo;
		break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
		
	    $dateMin = $db->sysdate()."-90 ";
		$whereFiltro = str_replace("radi_nume_radi","b.radi_nume_radi", $whereFiltro);
		$isql = 'select
				to_char(b.RADI_NUME_RADI) "IMG_Numero Radicado",
				s.SGD_DIR_NOMREMDES "REMITENTE",
				b.RADI_PATH "HID_RADI_PATH",
				to_char(b.RADI_NUME_DERI) "Radicado Padre",
				b.RADI_FECH_RADI "HOR_RAD_FECH_RADI",
				b.RADI_FECH_RADI "Fecha Radicado",
				b.RA_ASUN "Descripcion",
				c.SGD_TPR_DESCRIP "Tipo Documento",
				b.RADI_NUME_RADI "CHK_CHKANULAR"
			from
				radicado b, SGD_TPR_TPDCUMENTO c, SGD_DIR_DRECCIONES s
			where
				b.radi_nume_radi is not null
				and b.radi_nume_radi = s.radi_nume_radi
				and b.radi_depe_actu='.$dep_sel.'
				and b.SGD_TRAD_CODIGO <> 2
				and b.tdoc_codi=c.sgd_tpr_codigo
				and b.radi_fech_radi >='.$dateMin.'
				and sgd_eanu_codigo is null'.
				$whereTpAnulacion.' '.$whereFiltro.'
			order by '.$order .' ' .$orderTipo;
//			$db->conn->debug = true;
		break;
	default:
		$isql = 'select
				b.RADI_NUME_RADI as "IMG_Numero Radicado",
				b.RADI_PATH as "HID_RADI_PATH",
				b.RADI_NUME_DERI as "Radicado Padre",'.
				$db->conn->SQLDate('Y-m-d H:i:s', 'b.RADI_FECH_RADI').' as "HOR_RAD_FECH_RADI",'.
				$db->conn->SQLDate('Y-m-d H:i:s', 'b.RADI_FECH_RADI').' as "Fecha Radicado",
				b.RA_ASUN as "Descripcion",
				b.RADI_NUME_RADI as "CHK_CHKANULAR"
			from
				radicado b
				left join anexos a on  b.RADI_NUME_RADI=a.RADI_NUME_SALIDA
				left join sgd_renv_regenvio env on b.RADI_NUME_RADI= env.radi_nume_sal
			where
				(a.anex_estado is null or a.anex_estado < 4)
				and b.SGD_TRAD_CODIGO <> 2
				and (env.sgd_renv_planilla = \'00\' or env.sgd_renv_planilla is null) 
				and is_borrador = false and ( cast(b.radi_nume_radi as varchar(20)) not like \'3000%\' ) '. $whereTpAnulacion.' '.$whereFiltro;
}
?>
