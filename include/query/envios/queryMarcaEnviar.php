<?
	switch($db->driver)
	{
	case 'mssql':
		$isql = "SELECT 
	        convert(varchar(20),b.RADI_NUME_RADI) as RADI_NUME_RADI,a.ANEX_NOMB_ARCHIVO,a.ANEX_DESC,a.SGD_REM_DESTINO,a.SGD_DIR_TIPO
  		   ,convert(varchar(20),a.ANEX_RADI_NUME) as ANEX_RADI_NUME, convert(varchar(20),a.RADI_NUME_SALIDA) as RADI_NUME_SALIDA
		 FROM ANEXOS a,RADICADO b
		 WHERE a.radi_nume_salida=b.radi_nume_radi
			and a.RADI_NUME_SALIDA  in(".$setFiltroSelect.")
			and a.sgd_dir_tipo <>7 and anex_estado=3";
		break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	$isql = "SELECT 
	        b.RADI_NUME_RADI as RADI_NUME_RADI,a.ANEX_NOMB_ARCHIVO,a.ANEX_DESC,a.SGD_REM_DESTINO,a.SGD_DIR_TIPO
  		   ,a.ANEX_RADI_NUME as ANEX_RADI_NUME, a.RADI_NUME_SALIDA as RADI_NUME_SALIDA
		 FROM ANEXOS a,RADICADO b
		 WHERE a.radi_nume_salida=b.radi_nume_radi
			and cast(a.RADI_NUME_SALIDA as varchar(20)) in(".$setFiltroSelect.")
			and anex_estado=2";		
		break;
	default:
		
		$isql = "SELECT 
				b.RADI_NUME_RADI as RADI_NUME_RADI
				,a.ANEX_NOMB_ARCHIVO ,a.ANEX_DESC 
				,a.SGD_REM_DESTINO ,dir.SGD_DIR_TIPO
				,a.ANEX_RADI_NUME as ANEX_RADI_NUME
				,a.RADI_NUME_SALIDA as RADI_NUME_SALIDA
				, dir.DPTO_CODI,  dir.MUNI_CODI, dir.SGD_DIR_DIRECCION
				, dir.sgd_dir_nomremdes, dir.id_pais, dir.id_pais, dir.id_cont, dir.sgd_dir_tipo
				, dir.sgd_dir_mail
				, dir.sgd_dir_telefono
		 	FROM ANEXOS a,RADICADO b, sgd_dir_drecciones dir
		 	WHERE a.radi_nume_salida=b.radi_nume_radi
		 	  and dir.radi_nume_radi=b.radi_nume_radi
				and cast(a.RADI_NUME_SALIDA as varchar(20)) in(".$setFiltroSelect.")
				and (anex_estado=2 or anex_estado=3)";
		break;
	}
?>
