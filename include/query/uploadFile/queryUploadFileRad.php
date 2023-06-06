<?

	/**
	  * CONSULTA DE UPLOAD FILE 
	  * @author JAIRO LOSADA DNP - SSPD 2006/03/01
	  * @version 3.5.1
	  * 
	  * @param $query String Almacena Consulta que se enviara
	  * @param $sqlFecha String  Almacena fecha en  formato Y-m-d que devuelve ADODB para la base de datos escogida
	  */
	$sqlFecha = $db->conn->SQLDate("Y-m-d H:i A","r.RADI_FECH_RADI");
	if($usuarioDependencia != '') {
		$selectorUsuariosDependencia = '8230';
		$joinDeUsuario = "JOIN hist_eventos h1 ON r.radi_nume_radi = h1.radi_nume_radi AND h1.depe_codi = '".$selectorUsuariosDependencia."' AND h1.sgd_ttr_codigo = '2' AND h1.usua_codi = '".$usuarioDependencia."'";
	} else {
		$joinDeUsuario = "";
	}

	if($busqRadicados == '')
	{		
		$busq_radicados_tmp = '1 = 1 AND r.RADI_PATH IS NULL';
	}

	switch($db->driver)
	{
	case 'mssql':
		$query = "SELECT 
			convert(char(15), RADI_NUME_RADI) as IDT_Numero_Radicado,
			RADI_PATH as HID_RADI_PATH,		
			$sqlFecha as DAT_Fecha_Radicado,
			RADI_NUME_DERI RADICADO_PADRE,
			convert(char(14), RADI_NUME_RADI) as HID_RADI_NUME_RADI,
			RA_ASUN ASUNTO,
			convert(varchar(15), radi_nume_radi) CHR_DATO
			FROM RADICADO
			 WHERE
			 $busq_radicados_tmp
			 AND r.SGD_TRAD_CODIGO = 2 
			 ORDER BY RADI_FECH_RADI DESC";
		$query2 = "SELECT 
			convert(char(15), RADI_NUME_RADI) as IDT_Numero_Radicado,
			RADI_PATH as HID_RADI_PATH,		
			$sqlFecha as DAT_Fecha_Radicado,
			RADI_NUME_DERI RADICADO_PADRE,
			convert(char(14), RADI_NUME_RADI) as HID_RADI_NUME_RADI,
			RA_ASUN ASUNTO
			convert(varchar(15), radi_nume_radi) CHR_DATO
			FROM RADICADO
			 WHERE
			 	 $busq_radicados_tmp 
				AND r.SGD_TRAD_CODIGO = 2 
				ORDER BY RADI_FECH_RADI DESC
			 ";
	break;
//	case 'oracle':
//	case 'oci8':
//	case 'oci805':	
//	Modificado skina para postgres
	default:	
	$query = 'SELECT 
			r.RADI_NUME_RADI as "IDT_Numero_Radicado",
			r.RADI_PATH as "HID_RADI_PATH",
			'.$sqlFecha.' as "DAT_Fecha_Radicado",
			r.RADI_NUME_DERI as "RADICADO_PADRE",
			r.RADI_NUME_RADI as "HID_RADI_NUME_RADI",
			r.RA_ASUN as "ASUNTO",
			r.RADI_NUME_RADI as "CHR_DATO"
			FROM RADICADO r
			'.$joinDeUsuario.'
			 WHERE
			 '.$busq_radicados_tmp.'
			 AND r.IS_BORRADOR = false 
			 AND cast(r.RADI_NUME_RADI as varchar(20)) not like \'3000%\'  
			 ORDER BY r.RADI_FECH_RADI DESC';
	$query2 = 'SELECT 
			r.RADI_NUME_RADI as "IDT_Numero_Radicado",
			r.RADI_PATH as "HID_RADI_PATH",
			'.$sqlFecha.' as "DAT_Fecha_Radicado",
			r.RADI_NUME_DERI as "RADICADO_PADRE",
			r.RADI_NUME_RADI as "HID_RADI_NUME_RADI",
			r.RA_ASUN as "ASUNTO",
			r.RADI_NUME_RADI as "CHR_DATO"
			FROM RADICADO r
			 WHERE
			 '.$busq_radicados_tmp.'
			 AND r.SGD_TRAD_CODIGO = 2
			 AND r.RADI_PATH IS NULL
			 AND r.IS_BORRADOR = false 
			 AND cast(r.RADI_NUME_RADI as varchar(20)) not like \'3000%\'  
			 ORDER BY r.RADI_FECH_RADI DESC';
	//break;
	}
?>
