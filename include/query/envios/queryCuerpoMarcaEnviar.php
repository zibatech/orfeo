<?
  session_start();	

  if ($_SESSION["entidad"] != 'CRA'){
	 //$anexo_secundario = " and a.sgd_dir_tipo =dir.sgd_dir_tipo ";
	}

	/**
	  * CONSULTA VERIFICACION PREVIA A LA RADICACION
	  */

switch ($_GET["orno"])
 {
       case '98':
             $where_impresion = ' and a.sgd_fech_impres is not null ';
             break;
       case '99':
             $where_impresion = ' and a.sgd_fech_impres is null ';
             break;
       default: 
             $where_impresion = '';
       break;
  }
	switch($db->driver)
	{
	case 'mssql':
		$isql = 'select 
             a.anex_estado CHU_ESTADO
		 	,a.sgd_deve_codigo HID_DEVE_CODIGO
			,a.sgd_deve_fech AS "HID_SGD_DEVE_FECH" 
			,convert(char(20),a.radi_nume_salida) AS "IMG_RADICADO_SALIDA"
			,c.RADI_PATH "HID_RADI_PATH"
            ,'.$db->conn->substr.'(convert(char(3),a.sgd_dir_tipo),2,3) AS "COPIA"
			,convert(char(20),a.anex_radi_nume) AS RADICADO_PADRE
			,c.radi_fech_radi AS FECHA_RADICADO
			,a.anex_desc AS DESCRIPCION
			,a.sgd_fech_impres AS FECHA_IMPRESION
			,a.anex_creador AS GENERADO_POR
	        ,convert(char(20), a.radi_nume_salida) AS "CHK_RADI_NUME_SALIDA" 
			,a.sgd_deve_codigo HID_DEVE_CODIGO1
			,a.anex_estado HID_ANEX_ESTADO1
	        ,a.anex_nomb_archivo AS "HID_ANEX_NOMB_ARCHIVO" 
	        ,a.anex_tamano AS "HID_ANEX_TAMANO"
			,a.anex_radi_fech AS "HID_ANEX_RADI_FECH" 
			,' . "'WWW'" . ' AS "HID_WWW" 
			,' . "'9999'" . ' AS "HID_9999"     
			,a.anex_tipo AS "HID_ANEX_TIPO" 
			,a.anex_radi_nume AS "HID_ANEX_RADI_NUME" 
			,a.sgd_dir_tipo AS "HID_SGD_DIR_TIPO"
			,a.sgd_deve_codigo AS "HID_SGD_DEVE_CODIGO" 
		from anexos a,usuario b, radicado c
	    where ANEX_ESTADO>=' .$estado_sal. ' '.
				$dependencia_busq2 . '
				and a.radi_nume_salida=c.radi_nume_radi
				and a.anex_creador=b.usua_login ' . 
			        $where_impresion . '
				and a.anex_borrado= ' . "'N'" . '
				AND
				((c.SGD_EANU_CODIGO <> 2
				AND c.SGD_EANU_CODIGO <> 1) 
				or c.SGD_EANU_CODIGO IS NULL)
                                order by a.radi_nume_salida' ;
		break;
	
	default:	
		
		//if($usuaCodiEnvio!='9999') $whereUsuaCodiEnvio = " AND b.usua_codi=$usuaCodiEnvio"; else $whereUsuaCodiEnvio = "";
		$whereUsuaCodiEnvio = "";
		$isql = 'select 
			a.anex_estado AS "CHU_ESTADO"
		 	,a.sgd_deve_codigo AS "HID_DEVE_CODIGO"
			,a.sgd_deve_fech AS "HID_SGD_DEVE_FECH" 
		  	,CAST(a.radi_nume_salida as varchar(20)) AS "IMG_RADICADO_SALIDA"
			,c.RADI_PATH AS "HID_RADI_PATH"						
			,e.ID AS "HID_ID_ENVIO"						
      		,substr(trim( CAST( a.sgd_dir_tipo AS VARCHAR(5) ) ),2,5) AS COPIA
			,CAST(a.anex_radi_nume as varchar(20)) AS RADICADO_PADRE
			,e.tipo AS TIPO_ENVIO
			,c.radi_fech_radi AS FECHA_RADICADO
			,dir.sgd_dir_nomremdes||'."'/'".'||dir.sgd_dir_nombre||'."'<br>'".'||dir.sgd_dir_direccion AS DESCRIPCION
			,a.sgd_fech_impres AS FECHA_IMPRESION
			,a.anex_creador AS GENERADO_POR
	    	,a.radi_nume_salida AS "CHK_RADI_NUME_SALIDA" 
			,a.sgd_deve_codigo AS HID_DEVE_CODIGO1
			,a.anex_estado  AS HID_ANEX_ESTADO1
			,dir.sgd_dir_tipo
		  	,a.anex_nomb_archivo AS "HID_ANEX_NOMB_ARCHIVO" 
		  	,a.anex_tamano AS "HID_ANEX_TAMANO"
			,a.ANEX_RADI_FECH AS "HID_ANEX_RADI_FECH" 
			,' . "'WWW'" . ' AS "HID_WWW" 
			,' . "'9999'" . ' AS "HID_9999"     
			,a.anex_tipo AS "HID_ANEX_TIPO" 
			,a.anex_radi_nume AS "HID_ANEX_RADI_NUME" 
			,a.sgd_dir_tipo AS "HID_SGD_DIR_TIPO"    
			,a.sgd_deve_codigo AS "HID_SGD_DEVE_CODIGO"
		from 
			((((((
				sgd_rad_envios e 
				JOIN anexos a ON e.id_anexo = a.id)
				JOIN usuario b ON a.anex_creador = b.usua_login) 
				JOIN radicado c ON a.radi_nume_salida = c.radi_nume_radi) 
				JOIN sgd_dir_drecciones dir ON e.id_direccion=dir.id)
				LEFT JOIN sgd_exp_expediente exp ON a.radi_nume_salida=exp.radi_nume_radi)
				LEFT JOIN sgd_rdf_retdocf trd ON a.radi_nume_salida=trd.radi_nume_radi)
		where 
				a.ANEX_ESTADO>=' .$estado_sal. ' '.
				$dependencia_busq2 .
				$whereUsuaCodiEnvio . '
				and a.ANEX_ESTADO <= ' . $estado_sal_max . '
				'.$anexo_secundario .'
				'.$where_impresion. '
				and a.anex_borrado= ' . "'N'" . '
				and (a.sgd_deve_codigo >= 0 or a.sgd_deve_codigo =0 or a.sgd_deve_codigo is null)
				and c.sgd_trad_codigo <> 3
				AND
				((c.SGD_EANU_CODIGO != 2
				AND c.SGD_EANU_CODIGO != 1) 
				or c.SGD_EANU_CODIGO IS NULL)
					 order by c.radi_fech_radi desc';

	}
?>
