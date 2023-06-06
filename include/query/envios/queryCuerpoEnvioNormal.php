<?
session_start();

if ($_SESSION["entidad"] != 'CRA'){
 //$anexo_secundario = " and a.sgd_dir_tipo   = dir.sgd_dir_tipo ";
 }

if ($dep_sel == '9999')
$dependencia_busq2= '';


//MODIFICADO POR SKINA PARA POSTGRES
switch($db->driver)
{
	case 'mssql':
		$isql = 'select 
		,a.anex_estado CHU_ESTADO
		,a.sgd_deve_codigo HID_DEVE_CODIGO
		,a.sgd_deve_fech AS "HID_SGD_DEVE_FECH" 
		,convert(varchar(15),a.radi_nume_salida) AS "IMG_Radicado Salida"
		,'.$radiPath.' as "HID_RADI_PATH"
		,'.$db->conn->substr.'(convert(char(1),a.sgd_dir_tipo),2,3) AS "Copia"
		,convert(varchar(15),a.anex_radi_nume) AS "Radicado Padre"
		,c.radi_fech_radi AS "Fecha Radicado"
		,a.anex_desc AS "Descripcion"
		,a.sgd_fech_impres AS "Fecha Impresion"
		,a.anex_creador AS "Generado Por"
		,convert(varchar(20),a.anex_codigo) AS "CHK_RADI_NUME_SALIDA" 
		,a.sgd_deve_codigo HID_DEVE_CODIGO1
		,a.anex_estado HID_ANEX_ESTADO1
		,a.anex_nomb_archivo AS "HID_ANEX_NOMB_ARCHIVO" 
		,a.anex_tamano AS "HID_ANEX_TAMANO"
		,a.ANEX_RADI_FECH AS "HID_ANEX_RADI_FECH" 
		,' . "'WWW'" . ' AS "HID_WWW" 
		,' . "'9999'" . ' AS "HID_9999"     
		,a.anex_tipo AS "HID_ANEX_TIPO" 
		,a.anex_radi_nume AS "HID_ANEX_RADI_NUME" 
		,a.sgd_dir_tipo AS "HID_SGD_DIR_TIPO"
		,a.sgd_deve_codigo AS "HID_SGD_DEVE_CODIGO" 
		FROM anexos a,usuario b, radicado c
		WHERE  ANEX_ESTADO>=' .$estado_sal. ' '.
		$dependencia_busq2 . '
		and a.ANEX_ESTADO <= ' . $estado_sal_max . '
		and a.radi_nume_salida=c.radi_nume_radi
		and a.anex_creador=b.usua_login
		and a.anex_borrado= ' . "'N'" . '
		and a.sgd_dir_tipo != 7
		and ((a.SGD_DEVE_CODIGO <=0 
		and a.SGD_DEVE_CODIGO <=99)
		OR a.SGD_DEVE_CODIGO IS NULL)
		AND
		((c.SGD_EANU_CODIGO <> 2
		AND c.SGD_EANU_CODIGO <> 1) 
		or c.SGD_EANU_CODIGO IS NULL)
		order by '.$order .' ' .$orderTipo;
	break;
	default:
		$isql = 'select 
			e.id as "HID_ID_EVENTO"
			,a.anex_estado as "CHU_ESTADO"
		 	,a.sgd_deve_codigo as "HID_DEVE_CODIGO"
			,a.sgd_deve_fech as "HID_SGD_DEVE_FECH" 
			,a.radi_nume_salida AS "IMG_Radicado Salida"
			,'.$radiPath.' as "HID_RADI_PATH"
      		,CASE WHEN cast(dir.sgd_dir_tipo as varchar)=\'1\' THEN \'\' ELSE cast((dir.sgd_dir_tipo-1) as varchar) END  AS "Copia"
			,a.anex_radi_nume AS "Radicado Padre"
			,c.radi_fech_radi AS "Fecha Radicado"
			,dir.sgd_dir_nomremdes||'."'/'".'||dir.sgd_dir_nombre||'."'<br>'".'||dir.sgd_dir_direccion AS "Descripcion"
			,a.sgd_fech_impres AS "Fecha Impresion"
			,concat(c.radi_nume_folio,\' / \',c.radi_nume_hoja) AS "FOLIOS"
			,c.radi_nume_anexo AS "Anexos"
			,a.anex_creador AS "Generado Por"
		  	,dir.radi_nume_radi||'."'_'".'||dir.sgd_dir_tipo||'."'_'".'||e.id AS "CHK_RADI_NUME_SALIDA" 
			,a.sgd_deve_codigo as "HID_DEVE_CODIGO1"
			,a.anex_estado as "HID_ANEX_ESTADO1"
		  ,a.anex_nomb_archivo AS "HID_ANEX_NOMB_ARCHIVO" 
	    ,a.anex_tamano AS "HID_ANEX_TAMANO"
			,a.ANEX_RADI_FECH AS "HID_ANEX_RADI_FECH" 
			,' . "'WWW'" . ' AS "HID_WWW" 
			,' . "'9999'" . ' AS "HID_9999"     
			,a.anex_tipo AS "HID_ANEX_TIPO" 
			,a.anex_radi_nume AS "HID_ANEX_RADI_NUME" 
			,a.sgd_dir_tipo AS "HID_SGD_DIR_TIPO"
			,a.sgd_deve_codigo AS "HID_SGD_DEVE_CODIGO" 
			from sgd_rad_envios e, anexos a,usuario b, radicado c, sgd_dir_drecciones dir
			where e.id_anexo = a.id
			and e.estado = 1  
			and e.tipo = ' . "'Físico'" . ' and ';
			if($busqRadicados!=''){
				$isql = $isql."(a.radi_nume_salida in ($busqRadicados) or a.anex_radi_nume in ($busqRadicados)) and ";
			}	
			$isql = $isql.'	a.ANEX_ESTADO>=' .$estado_sal. ' '.
			$dependencia_busq2 . '
			and a.ANEX_ESTADO <= 4
			and a.radi_nume_salida=c.radi_nume_radi
			AND e.id_direccion = dir.id 
			'.$anexo_secundario .'
			and a.anex_creador=b.usua_login
			and a.anex_borrado= ' . "'N'" . '
			-- and (dir.sgd_dir_enviado=0 or dir.sgd_dir_enviado is null)
			and ((a.SGD_DEVE_CODIGO >=0 and a.SGD_DEVE_CODIGO <=99) OR a.SGD_DEVE_CODIGO IS NULL)
			AND	((c.SGD_EANU_CODIGO != 2 AND c.SGD_EANU_CODIGO != 1) or c.SGD_EANU_CODIGO IS NULL)
			and substring(cast(c.radi_nume_radi as varchar),17,1)=\'1\'
			order by '.$order .' ' .$orderTipo. ", dir.sgd_dir_tipo ";
		break; 
		
	}
?>
