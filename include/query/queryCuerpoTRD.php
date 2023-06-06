<?php
$coltp3Esp = '"'.$tip3Nombre[3][2].'"';
        #LLAMO UNA FUNCIÓN CONNECCIÓN HANDLER LLAMADA LIMIT LA CUAL ME DEVUELVE LA ESTRUCTURA CORRECTA
$db->limit(1500);
$limitMsql = $db->limitMsql;
$limitOci8 = $db->limitOci8;
$limitPsql = $db->limitPsql;
switch($db->driver)
{
	case 'mssql':
        $isql = '2 select '. $limitMsql .'
        	convert(char(20), b.RADI_NUME_RADI) "IDT_Numero Radicado"
        	,b.RADI_PATH as "HID_RADI_PATH"
        	,'.$sqlFecha.' as "DAT_Fecha Radicado"
        	,convert(char(20), b.RADI_NUME_RADI) as "HID_RADI_NUME_RADI"
        	,UPPER(b.RA_ASUN)  as "Asunto"'.
        	$colAgendado.
        	',d.NOMBRE_DE_LA_EMPRESA "'.$tip3Nombre[3][2].'"
        	,c.SGD_TPR_DESCRIP as "Tipo Documento"
        	,b.RADI_USU_ANTE "Enviado Por"
        	,CAST (((radi_fech_radi+(c.sgd_tpr_termino * 7/5))-GETDATE()) AS NUMERIC) as "Dias RestanteS"
        	,convert(char(20),b.RADI_NUME_RADI) "CHK_CHKANULAR"
        	,b.RADI_LEIDO "HID_RADI_LEIDO"
        	,b.RADI_NUME_HOJA "HID_RADI_NUME_HOJA"
        	,b.CARP_PER "HID_CARP_PER"
        	,b.CARP_CODI "HID_CARP_CODI"
        	,b.SGD_EANU_CODIGO "HID_EANU_CODIGO"
        	,b.RADI_NUME_DERI "HID_RADI_NUME_DERI"
        	,b.RADI_TIPO_DERI "HID_RADI_TIPO_DERI"
            from
            radicado b
            left outer join SGD_TPR_TPDCUMENTO c
            on b.tdoc_codi=c.sgd_tpr_codigo
            left outer join BODEGA_EMPRESAS d
            on b.eesp_codi=d.identificador_empresa
            where
            b.radi_nume_radi is not null
            and b.radi_depe_actu='.$dependencia.
            $whereUsuario.$whereFiltro.'
            '.$whereCarpeta.'
            '.$sqlAgendado.'
            order by '.$order .' ' .$orderTipo;
        break;
	case 'postgres':
	case 'oci8':

        //Codigo para la busueda del php por varios radicados
            if ($_POST['radicado_a_buscar']){$radicados_in = 'b.radi_nume_radi in('.$radicados_a_buscar.') and ';$limitOci8 = '';}
            //Start::Busqueda por fecha
            $fecha="";
            if (!empty($_SESSION['fecha_inicial']) && !empty($_SESSION['fecha_final'])  ){
                $fecha = " and  b.radi_fech_radi BETWEEN '".$_SESSION['fecha_inicial']." 0:0' and '".$_SESSION['fecha_final']." 23:59'";
            }

			$medio_recepcion="";
			if(!empty($_SESSION['medio_recepcion'])  && $_SESSION['medio_recepcion'] != 'todos') {
				$medio_recepcion = "and b.mrec_codi = '".$_SESSION['medio_recepcion']."'";
			}

              if(isset($krd) && $krd=="AMERICAS")
                    $paginacion = 1000;
                else
                    $paginacion = 100;
                
            if (!empty($_SESSION['resultados'])){
                $offset = ($_SESSION['resultados'] - 1) * $paginacion;
                $limitPsql = " limit $paginacion offset ".$offset;
            }else{
                 $limitPsql = " limit $paginacion offset 0";
            }

        //End::Busqueda por fecha
	   $whereFiltro = str_replace("b.radi_nume_radi","cast(b.radi_nume_radi as varchar(20))",$whereFiltro);
		//Start::Relacionado multiple ususario
		$sentido ='and d.sgd_dir_tipo=1';
		if($carpeta == 0 || $carpeta == 9999){
			$whereCarpeta2 = "AND b.sgd_trad_codigo = 3";
			$sentido = "";
			$relacionados = 'or(
			'.$radicados_in.'
			b.radi_nume_radi is not null and
			d.SGD_DIR_DOC = \''.$usua_doc.'\' and
			a.ANEX_ESTADO >= 2 and 
			(SELECT count(*) FROM SGD_DIR_DRECCIONES WHERE SGD_DIR_DRECCIONES.radi_nume_radi=b.radi_nume_radi) > 1 
            '.$fecha.'
			'.$whereCarpeta2.')
			'.$sqlAgendado.'
			'.$limitOci8;
	  	}
		//End::Relacionado multiple ususario
		//	$redondeo="date_part('days', radi_fech_radi-".$db->conn->sysTimeStamp.")+floor(c.sgd_tpr_termino * 7/5)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between radi_fech_radi and ".$db->conn->sysTimeStamp.")";
        if($db->driver=="oci8") {
            $fechaT = "((radi_fech_radi-".$db->conn->sysTimeStamp.")+(c.sgd_tpr_termino))";
            $diasHabiles = " fech_vcmto -5 "; // ".$db->conn->sysdate ." ";
    	}else{
            ///$fechaT = "(date_part('days', radi_fech_radi-".$db->conn->sysTimeStamp."))+floor(c.sgd_tpr_termino * 7/5)";
			$fechaT = "(date_part('days', radi_fech_radi-".$db->conn->sysTimeStamp."))";
            $diasHabiles = '- extract(days from date_trunc('."'".'days'."'".', NOW()) - date_trunc('."'".'days'."'".',fech_vcmto))';
    	}
    	//$redondeo="$fechaT+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between radi_fech_radi and ".$db->conn->sysTimeStamp.")";
    	//$redondeo="date_part('days', radi_fech_radi-".$db->conn->sysTimeStamp.")+floor(c.sgd_tpr_termino * 7/5)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between radi_fech_radi and ".$db->conn->sysTimeStamp.")";
		$redondeo="date_part('days', radi_fech_radi-".$db->conn->sysTimeStamp.")+(c.sgd_tpr_termino)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between radi_fech_radi and ".$db->conn->sysTimeStamp.")";
    	

        $selectDocumentoUsuario = ',d.SGD_DIR_DOC "DOCUMENTO_USUARIO"';

        $isql = 'select DISTINCT
            b.RADI_NUME_RADI "IDT_Numero RADICADO"
			,b.RADI_PATH "HID_RADI_PATH"
			,'.$sqlFecha.' "DAT_FECHA RADICADO"
			,'.$sqlFecha.' "HID_RADI_FECH_RADI"
			, b.RADI_NUME_RADI "HID_RADI_NUME_RADI"
			,b.RA_ASUN  "ASUNTO"
			, b.RADI_CUENTAI "REFERENCIA"'.
			$colAgendado.
			',d.SGD_DIR_NOMREMDES "REMITENTE"
			,c.SGD_TPR_DESCRIP "TIPO DOCUMENTO"
                	,'.$redondeo.' "DIAS RESTANTES"
                	,b.fech_vcmto "FECHA_VCMTO"
			,b.RADI_USU_ANTE "ENVIADO POR"
			,b.RADI_NUME_RADI "CHK_CHKANULAR"
			,b.RADI_LEIDO "HID_RADI_LEIDO"
			,m.MREC_DESC "RADI_MREC_DESC"
			,b.RADI_USUA_ACTU "RADI_USUA_ACTU"
			,b.RADI_NUME_HOJA "HID_RADI_NUME_HOJA"
			,b.CARP_PER "HID_CARP_PER"
			,b.CARP_CODI "HID_CARP_CODI"
			,b.SGD_EANU_CODIGO "HID_EANU_CODIGO"
			,b.RADI_NUME_DERI "HID_RADI_NUME_DERI"
			,b.RADI_TIPO_DERI "HID_RADI_TIPO_DERI"
            ,b.SGD_TRAD_CODIGO "TIPO_RAD"
			,a.ANEX_ESTADO "ANEX_ESTADO"
			,a.SGD_DEVE_CODIGO "SGD_DEVE_CODIGO"
			'.$selectDocumentoUsuario.'
			
            from
            radicado b
            left outer join SGD_TPR_TPDCUMENTO c
            on b.tdoc_codi=c.sgd_tpr_codigo

			left join medio_recepcion m
			on b.mrec_codi = m.mrec_codi

            left outer join ANEXOS a
            on b.radi_nume_radi=a.RADI_NUME_SALIDA

            left outer join SGD_DIR_DRECCIONES d
            on (b.radi_nume_radi=d.radi_nume_radi '.$sentido.')';
            /*
              and
              d.sgd_dir_nombre = (select sgd_dir_nombre from sgd_dir_drecciones
            	  order by sgd_dir_codigo asc limit 1) and
              d.sgd_dir_direccion = (select sgd_dir_direccion from sgd_dir_drecciones
            	  order by sgd_dir_codigo asc limit 1)';

            $isql .= '
            )*/

        
        $isqlconteo = 'select COUNT(*) FROM (select b.RADI_NUME_RADI from
            radicado b
            left outer join SGD_TPR_TPDCUMENTO c
            on b.tdoc_codi=c.sgd_tpr_codigo

            left outer join ANEXOS a
            on b.radi_nume_radi=a.RADI_NUME_SALIDA

			left join medio_recepcion m
			on b.mrec_codi = m.mrec_codi

            left outer join SGD_DIR_DRECCIONES d
            on (b.radi_nume_radi=d.radi_nume_radi '.$sentido.') where '.$radicados_in.'
            b.radi_nume_radi is not null
            and b.radi_depe_actu <> 900
            and b.tdoc_codi=0'.            
            $whereFiltro.'
            '.$whereCarpeta.'
            '.$sqlAgendado.'
            '.$fecha.'
            '.$medio_recepcion.') as total';
        

        $isql = $isql . 'where '.$radicados_in.'
        	b.radi_nume_radi is not null
            and b.radi_depe_actu <> 900
        	and b.tdoc_codi=0'.
        	$whereFiltro.'
        	'.$whereCarpeta.'
        	'.$sqlAgendado.'
        	'.$limitOci8 .'
        	'.$fecha.'
			'.$medio_recepcion.'
        	'.$relacionados.'
            order by '.$order . ' ' .$orderTipo. ', b.RADI_NUME_RADI desc'
            . ' ' . $limitPsql . ' ' ;
		//echo $isql;
        //$db->conn->debug = true;
        break;

}
?>
