<?php

$coltp3Esp = '"'.$tip3Nombre[3][2].'"'; 
if(!$orno) $orno=2;
 /**
   * $db-driver Variable que trae el driver seleccionado en la conexion
   * @var string
   * @access public
   */
 /**
   * $fecha_ini Variable que trae la fecha de Inicio Seleccionada  viene en formato Y-m-d
   * @var string
   * @access public
   */
/**
   * $fecha_fin Variable que trae la fecha de Fin Seleccionada
   * @var string
   * @access public
   */
/**
   * $mrecCodi Variable que trae el medio de recepcion por el cual va a sacar el detalle de la Consulta.
   * @var string
   * @access public
   */
$whereTipoRadicado  = str_replace("A.","r.",$whereTipoRadicado);
$whereTipoRadicado  = str_replace("a.","r.",$whereTipoRadicado);
switch($db->driver)
{ 
  case 'postgres':
  case 'oracle':
  case 'oci8':
  case 'oci805':
  case 'ocipo':
    { if ( $dependencia_busq != 99999)
      { /*$condicionE = "   AND SUBSTR( ra.radi_nume_radi, 5, 3 ) = $dependencia_busq  "; */
	  $condicionE = "   AND ra.radi_depe_actu = $dependencia_busq  "; 
	  }

      /*echo "<hr>";
      print_r($_GET);
      echo "<hr>";
/*ESTE COMANDO PARA QUE ME MUESTRE EL PANTALLAZO EN EL EXPLORER*/ 

$queryE = "
			SELECT
        ra.sgd_trad_codigo as TIPO_RADICADO,
        r. RADI_NUME_RADI AS RADICADO,
        ra.radi_fech_radi AS FECHA_RADICADO,
        ra.radi_nume_deri AS RADICADO_ASOCIADO,
        s.SGD_SRD_CODIGO AS SERIE_NUMERO,
        s.SGD_SRD_DESCRIP AS DESCRIPCION_SERIE,
        su.SGD_SBRD_CODIGO AS SUBSERIE_NUMERO,
        su.SGD_SBRD_DESCRIP AS SUBDESCRIPCION_SERIE,
        t.SGD_TPR_CODIGO AS TIPO_DOCUMENTAL_NUMERO,
        t.SGD_TPR_DESCRIP AS DESCRIPCION_TIPO_DOCUMENTAL,
        ra.ra_asun AS ASUNTO,$whereTipoRadicado
        dir.sgd_dir_nomremdes AS NOMBRE_ENTIDAD,
        dir.sgd_dir_nombre AS PETICIONARIO,
        ra.radi_usua_actu AS CODIGO_FUNCIONARIO,
        b.usua_nomb AS NOMBRE_FUNCIONARIO,
        ra.radi_depe_actu AS CODIGO_DEPENDENCIA,
        de.depe_nomb AS NOMBRE_DEPENDENCIA, 
        ra.radi_usu_ante AS CODIGO_FUNCIONARIO_ANTERIOR,
        us.usua_nomb AS NOMBRE_FUNCIONARIO_ANTERIOR,
        dir.sgd_dir_direccion AS DIRECCION,
        dir.sgd_dir_mail AS CORREO_ELECTRONICO,
        dep.dpto_nomb AS DEPARTAMENTO,
        mun.muni_nomb AS CIUDAD_MUNICIPIO,
        (".$db->sysdate()."-ra.RADI_FECH_RADI) AS DIAS_RAD      
      FROM radicado ra      
      left join usuario b on ra.radi_usua_actu=b.usua_codi and ra.radi_depe_actu=b.depe_codi
            left join usuario us on ra.radi_usu_ante=us.usua_doc
            left outer join SGD_DIR_DRECCIONES dir on dir.radi_nume_radi=ra.radi_nume_radi
            left join departamento dep on dir.dpto_codi=dep.dpto_codi and dir.id_cont=dep.id_cont and dir.id_pais=dep.id_pais
            left join  municipio mun on dir.muni_codi=mun.muni_codi and dir.id_cont=mun.id_cont and dir.id_pais=mun.id_pais and dir.dpto_codi=mun.dpto_codi,
        sgd_rdf_retdocf r,      
        sgd_mrd_matrird m,
        sgd_srd_seriesrd s,
        sgd_sbrd_subserierd su,
        sgd_tpr_tpdcumento t,
        dependencia de  
      WHERE ra.radi_nume_radi=r.radi_nume_radi
      $condicionE 
      AND ra.sgd_trad_codigo = 2
        AND r.sgd_mrd_codigo = m.sgd_mrd_codigo 
        AND  s.sgd_srd_codigo = m.sgd_srd_codigo 
        AND  su.sgd_srd_codigo = m.sgd_srd_codigo 
        AND su.sgd_sbrd_codigo = m.sgd_sbrd_codigo 
        AND t.sgd_tpr_codigo = m.sgd_tpr_codigo
        AND ra.radi_depe_actu=DE.DEPE_CODI
        AND (t.sgd_TPR_descrip ilike '%DERECHO%PET%' or s.sgd_srd_descrip ilike '%DERECHO%PET%') 
        AND TO_CHAR(ra.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin'";

            	      
  
  $queryE = "  SELECT r.radi_nume_radi RADICADO_ENTRADA, r.radi_FECH_RADI FECHA_RADICADO_ENTRADA, srd.sgd_srd_descrip SERIE,sbrd.sgd_sbrd_descrip SUBSERIE,tpr.sgd_tpr_descrip TIPO_DOCUMENTO 
, replace(replace(r.ra_asun, ',', '.'),'\r',' ') ASUNTO,
 (select concat(d.depe_nomb, ' ,',u.usua_nomb) from hist_eventos h, dependencia d, usuario u where h.hist_fech  < '$fecha_fin' and h.radi_nume_radi=r.radi_nume_radi and h.sgd_ttr_codigo not in (2,22,23,24,11,8) and h.depe_codi=d.depe_codi and h.usua_doc=u.usua_doc and h.depe_codi not in (900) order by hist_fech limit 1) PRIMER_DEPENDENCIA_Y_USUARIO,
 (select concat(d.depe_nomb, ' ,',u.usua_nomb) from hist_eventos h, dependencia d, usuario u where h.hist_fech  < '$fecha_fin' and h.radi_nume_radi=r.radi_nume_radi and h.sgd_ttr_codigo=13 and h.depe_codi=d.depe_codi and h.usua_doc=u.usua_doc and h.depe_codi_dest not in (900) order by hist_fech limit 1) FINALIZA_DEPENDENCIA_Y_USUARIO,
 d.depe_nomb DEPENDENCIA_ACTUAL, u.usua_nomb USUARIO_ACTUAL, dant.depe_nomb Dependencia_Anterior, uant.usua_nomb Usuario_anterior,
 (select a.radi_nume_salida from anexos a where a.anex_radi_nume=r.radi_nume_radi   and a.anex_radi_fech  < '$fecha_fin' order by anex_fech_anex desc limit 1 ) RADICADO_SALIDA
 ,(select a.anex_radi_fech from anexos a where a.anex_radi_nume=r.radi_nume_radi    and a.anex_radi_fech  < '$fecha_fin'order by anex_fech_anex desc limit 1 ) RADICADO_SALIDA_fecha_envio
 ,(select a.anex_fech_envio from anexos a where a.anex_radi_nume=r.radi_nume_radi   and a.anex_fech_envio < '$fecha_fin' order by anex_fech_anex desc limit 1 ) RADICADO_SALIDA_fecha_radicacion
 ,(select a.anex_nomb_archivo from anexos a where a.anex_radi_nume=r.radi_nume_radi and a.anex_fech_anex  < '$fecha_fin' order by anex_fech_anex desc limit 1 ) RADICADO_SALIDA_nomb_archivo
 ,(select a.anex_fech_anex from anexos a    where a.anex_radi_nume=r.radi_nume_radi and a.anex_fech_anex  < '$fecha_fin' order by anex_fech_anex desc limit 1 ) FECHA_ANEXO
 ,(select replace(replace(a.anex_desc, ',', '.'),'\r',' ') from anexos a where a.anex_radi_nume=r.radi_nume_radi and a.anex_fech_anex  < '$fecha_fin'   order by anex_fech_anex desc limit 1 ) DESCRIPCION_ANEXO
 ,extract(days from ((select a.anex_fech_anex from anexos a  where a.anex_radi_nume=r.radi_nume_radi and a.anex_fech_anex  < '$fecha_fin' order by anex_fech_anex desc limit 1 )-r.RADI_FECH_RADI)) Dias_Calendario_Anexo_documetno
 ,extract(days from ((select a.anex_radi_fech from anexos a  where a.anex_radi_nume=r.radi_nume_radi and a.anex_radi_fech  < '$fecha_fin' order by anex_fech_anex desc limit 1 )-r.RADI_FECH_RADI)) Dias_Calendario_Hasta_radicacion
 ,extract(days from ((select a.anex_fech_envio from anexos a where a.anex_radi_nume=r.radi_nume_radi and a.anex_fech_envio < '$fecha_fin' order by anex_fech_anex desc limit 1 )-R.RADI_FECH_RADI)) Dias_Calendario_Envio_Respuesta 
 FROM  radicado r 
  left outer join (select radi_nume_radi, sgd_mrd_codigo, depe_codi from sgd_rdf_retdocf order by radi_nume_radi desc) rdf on (r.radi_nume_radi=rdf.radi_nume_radi)
  left outer join sgd_mrd_matrird mrd on (rdf.sgd_mrd_codigo=mrd.sgd_mrd_codigo)
  left outer join sgd_srd_seriesrd srd on  (mrd.sgd_srd_codigo=srd.sgd_srd_codigo)
  left outer join sgd_sbrd_subserierd sbrd on  (mrd.sgd_srd_codigo=sbrd.sgd_srd_codigo and mrd.sgd_sbrd_codigo=sbrd.sgd_sbrd_codigo)
  left outer join sgd_tpr_tpdcumento tpr on  (mrd.sgd_tpr_codigo=tpr.sgd_tpr_codigo)
  left outer join dependencia d on (d.depe_codi=r.radi_depe_actu)
  left outer join usuario u on (u.usua_codi=r.radi_usua_actu and u.depe_codi=r.radi_depe_actu)
  left outer join usuario     uant on (uant.usua_login=r.radi_usu_ante)
  left outer join dependencia dant on (dant.depe_codi =uant.depe_codi)
WHERE  r.SGD_TRAD_CODIGO =2 and r.radi_fech_radi >= '$fecha_ini'  and r.radi_fech_radi < '$fecha_fin' AND (srd.sgd_srd_codigo in (24,17) or tpr.sgd_tpr_codigo is null)";
//-- and (select concat(radi_nume_salida,' ||', a.anex_nomb_archivo,' ||', a.anex_fech_anex) from anexos a where a.anex_radi_nume=r.radi_nume_radi order by anex_fech_anex desc limit 1 ) is not null";

 

   if($codus) $queryE .= " AND u.USUA_CODI=$codus
                           AND u.depe_codi = $dependencia_busq "; 
                           
   if($codserie<>0){
      if($codserie==24){
             $queryE .=  " AND (srd.sgd_srd_codigo in (24,17) or tpr.sgd_tpr_codigo is null) ";
        }else{
          $queryE .=  " AND srd.sgd_srd_codigo=$codserie ";
          
      }    
       
       
       
   }    

    $queryETodosDetalle = $queryEDetalle . $orderE;
    $queryEDetalle .= $condicionUS . $orderE;
//	echo "<pre>$queryE</pre>";
    }break;
}
if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1)
    $titulos=array();
else 

	$titulos=array("#",
		"1#TIPO_RADICADO",
		"2#RADICADO_ENTRADA",
		"3#FECHA_RADICADO_ENTRADA",
		"4#SERIE",
		"5#SUBSERIE",
		"6#TIPO_DOCUMENTO",
		"7#ASUNTO",
		"8#RADICADOR",
		"9#PRIMER_DEPENDENCIA_Y_USUARIO",
		"10#FINALIZA_DEPENDENCIA_Y_USUARIO",
		"11#DEPENDENCIA_ACTUAL",
		"12#RADICADO_SALIDA",
		"13#RADICADO_SALIDA_FECHA_ENVIO",
		"14#RADICADO_SALIDA_FECHA_RADICACION",
		"15#FECHA_ANEXO",
		"16#DESCRIPCION_ANEXO",
		"17#DIAS_CALENDARIO_ANEXO_DOCUMENTO",
		"18#DIAS_CALENDARIO_HASTA_RADICACION_SALIDA",
		"19#DIAS_CALENDARIO_ENVIO_RESPUESTA"
	);	
function pintarEstadistica($fila,$indice,$numColumna)
{
  global $ruta_raiz,$_POST,$_GET,$krd;
  $salida="";
  switch ($numColumna)
  {
    case  0:  $salida=$indice;    break;
	case 1: $salida=$fila['TIPO_RADICADO']; break;
	case 2: $salida=$fila['RADICADO_ENTRADA']; break;
	case 3: $salida=$fila['FECHA_RADICADO_ENTRADA']; break;
	case 4: $salida=$fila['SERIE']; break;
	case 5: $salida=$fila['SUBSERIE']; break;
	case 6: $salida=$fila['TIPO_DOCUMENTO']; break;
	case 7: $salida=$fila['ASUNTO']; break;
	case 8: $salida=$fila['RADICADOR']; break;
	case 9: $salida=$fila['PRIMER_DEPENDENCIA_Y_USUARIO']; break;
	case 10: $salida=$fila['FINALIZA_DEPENDENCIA_Y_USUARIO']; break;
	case 11: $salida=$fila['DEPENDENCIA_ACTUAL']; break;
	case 12: $salida=$fila['RADICADO_SALIDA']; break;
	case 13: $salida=$fila['RADICADO_SALIDA_FECHA_ENVIO']; break;
	case 14: $salida=$fila['RADICADO_SALIDA_FECHA_RADICACION']; break;
	case 15: $salida=$fila['FECHA_ANEXO']; break;
  case 16: $salida=$fila['DESCRIPCION_ANEXO']; break;
  case 17: $salida=$fila['DIAS_CALENDARIO_ANEXO_DOCUMENTO']; break;
  case 18: $salida=$fila['DIAS_CALENDARIO_HASTA_RADICACION']; break;
  case 19: $salida=$fila['DIAS_CALENDARIO_ENVIO_RESPUESTA']; break;
    default: $salida=false;
  }

return $salida;
 }
  //$db->conn->debug = true;
?>

