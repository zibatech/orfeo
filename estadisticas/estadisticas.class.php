<?php
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
class estadisiticas
{
    private $link;
    private $depart;
    private $munici;
    private $mesesN = array('01' => "Enero", '02' => "Febrero", '03' => "Marzo", '04' => "Abril", '05' => "Mayo", '06' => "Junio", '07' => "Julio", '08' => "Agosto", '09' => "Septiembre", '10' => "Octubre", '11' => "Noviembre", '12' => "Diciembre");
    private $bsq = array('&aacute;','&AACUTE;','&aACUTE;','&Aacute;','&eacute;','&EACUTE;','&eACUTE;','&Eacute;','&iacute;','&IACUTE;','&iACUTE;','&Iacute;','&oacute;','&OACUTE;','&oACUTE;','&Oacute;','&uacute;','&UACUTE;','&uacute;','&Uacute;','&ntilde;','&Ntilde','&nTILDE;','&NTILDE;');
    private $strcambio = array('á','Á','á','Á','é','É','é','É','í','Í','í','Í','ó','Ó','ó','Ó','ú','Ú','ú','Ú','ñ','Ñ','ñ','Ñ');
    private $medioRecp;
    private $depeNombre;

    
    public function __construct($ruta_raiz)
    {
        // parent::__construct();
        $this->link = new ConnectionHandler($ruta_raiz);
        $this->medioRecp = $this->medioRecp();
        $this->depeNombre =$this->dependecias();
        $this->depart =$this->departamentosFN();
        $this->munici =$this->municipiosFN();
      // $this->link->conn->debug =true;
    }

    public function rp1($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpRad)
    {

        if ($tpAds == 1) {
            $depe = $this->depahijas($depe);
        }
        $whereADD = $where2 = '';
        $where = '';
        $where2TB = '';
        $resp['depe'] = $depe;
        if ($depe != 99999) {
            $where = " b.depe_codi  in ($depe) and   ";
        }
        if ($tpdoc != 0) {
            $whereADD .= " and r.TDOC_CODI=$tpdoc ";
        }

        $whereU = $whereI = $ddsubserie = '';
        if ($subserie && $subserie != 0) {
            $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
        }

        if ($serie != 0) {
            $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
              INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
        }

        //     echo $whereI;

        if ($usu && $usu != 0) {
            $whereU = " and r.radi_usua_actu=$usu ";
            $whereUH = " and h.usua_codi=$usu ";
        }
        $whereTipoRadicado='';
        if($tpRad){
            $whereTipoRadicado = $tpRad ? " and r.sgd_trad_codigo = $tpRad " : '';
        }
         $iSql = "SELECT b.USUA_NOMB as USUARIO, count(DISTINCT r.radi_nume_radi)as RADICADOS, MIN(b.USUA_CODI) as COD_USU, MIN(b.depe_codi) as DEPE_USUA,b.usua_doc
                FROM  radicado r
                INNER JOIN
                    (	select h1.radi_nume_radi as radi_nume_radi, h1.id, h1.sgd_ttr_codigo as sgd_ttr_codigo, h1.usua_doc as usua_doc, h1.depe_codi as depe_codi from hist_eventos h1
                        INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi, min(hist_eventos.id) as id  from hist_eventos where hist_eventos.sgd_ttr_codigo = 2 $where GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id  ) htev
                    ON
                        r.radi_nume_radi = htev.radi_nume_radi
                LEFT JOIN  usuario b  ON   htev.usua_doc = b.usua_doc
        where  r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59')  and (r.sgd_eanu_codigo not in (1,2) or   r.sgd_eanu_codigo is null)  $whereTipoRadicado
        GROUP BY b.USUA_NOMB,b.usua_doc  ";
            $iSql="
            select b.USUA_NOMB as USUARIO, count(DISTINCT r.radi_nume_radi)as RADICADOS, MIN(b.USUA_CODI) as COD_USU, MIN(b.depe_codi) as DEPE_USUA, b.usua_doc  from usuario as b ,radicado r ,( select h1.radi_nume_radi as radi_nume_radi, h1.id, h1.sgd_ttr_codigo as sgd_ttr_codigo, h1.usua_doc as usua_doc, h1.depe_codi as depe_codi from hist_eventos h1 
                        INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi, min(hist_eventos.id) as id from hist_eventos where hist_eventos.sgd_ttr_codigo = 2  GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id ) htev
                        
            where $where  b.usua_esta='1' and r.radi_nume_radi = htev.radi_nume_radi and htev.usua_doc=b.usua_doc 
            and  r.radi_fech_radi between  ('$fini 00:00:00') and ('$ffin 23:59:59') and (r.sgd_eanu_codigo not in (1, 2) or r.sgd_eanu_codigo is null) $whereTipoRadicado  GROUP BY b.USUA_NOMB, b.usua_doc;

            ";
          
        $rs = $this->link->conn->query($iSql);

        if (!$rs->EOF) {
            $i = 1;
            while (!$rs->EOF) {
                $dd['NUM'] = $i;
                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        $dd[strtoupper($key)] = $value;
                    }
                }
                $datos[] = $dd;
                $i++;
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }

        return $datos;
    }

    public function rp2($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpRad)
    {

        if ($tpAds == 1) {
            $depe = $this->depahijas($depe);
        }

        $whereTipoRadicado = $tpRad ? " and r.sgd_trad_codigo = $tpRad " : '';
        $whereADD = $where2 = '';
        $where = '';
        $where2TB = '';
        $resp['depe'] = $depe;
        if ($depe != 99999) {
            $where = "and r.radi_depe_radi in ($depe) ";
        }
        if ($tpdoc != 0) {
            $whereADD .= " and r.TDOC_CODI=$tpdoc ";
        }

        $whereU = $whereI = $ddsubserie = '';
        if ($subserie && $subserie != 0) {
            $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
        }

        if ($serie != 0) {
            $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
              INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
        }

        //     echo $whereI;

        if ($usu && $usu != 0) {
            $whereU = " and r.radi_usua_actu=$usu ";
            $whereUH = " and h.usua_codi=$usu ";
        }

        $iSql = "SELECT c.mrec_desc AS MEDIO_RECEPCION, count(DISTINCT r.radi_nume_radi)as RADICADOS, max(c.MREC_CODI) AS CODI
                    FROM
                        MEDIO_RECEPCION c, radicado r
                        INNER JOIN
                        (select h1.radi_nume_radi as radi_nume_radi, h1.id, h1.sgd_ttr_codigo as sgd_ttr_codigo, h1.usua_doc as usua_doc, h1.depe_codi as depe_codi from hist_eventos h1
                        INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi, min(hist_eventos.id) as id  from hist_eventos where hist_eventos.sgd_ttr_codigo = 2
                        GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id  ) htev ON  r.radi_nume_radi = htev.radi_nume_radi
                        LEFT JOIN  usuario  ON   htev.usua_doc = usuario.usua_doc
                    WHERE r.radi_fech_radi BETWEEN ('$fini 00:00:00') and ('$ffin 23:59:59')  AND r.mrec_codi=c.mrec_codi
                        $whereTipoRadicado $where
                        GROUP BY c.mrec_desc ";

        $rs = $this->link->conn->query($iSql);

        if (!$rs->EOF) {
            $i = 1;
            while (!$rs->EOF) {
                $dd['NUM'] = $i;
                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        $dd[strtoupper($key)] = $value;
                    }
                }
                $datos[] = $dd;
                $i++;
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }

        return $datos;
    }
/***reporte 3 envios */
    public function rp3($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpRad)
    {

        if ($tpAds == 1) {
            $depe = $this->depahijas($depe);
        }

        $whereTipoRadicado = $tpRad ? " and r.sgd_trad_codigo = $tpRad " : '';
        $whereADD = $where2 = '';
        $where = '';
        $where2TB = '';
        $resp['depe'] = $depe;
        if ($depe != 99999) {
            $where = "and r.radi_depe_radi in ($depe) ";
        }
        if ($tpdoc != 0) {
            $whereADD .= " and r.TDOC_CODI=$tpdoc ";
        }

        $whereU = $whereI = $ddsubserie = '';
        if ($subserie && $subserie != 0) {
            $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
        }

        if ($serie != 0) {
            $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
          INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
        }

        //     echo $whereI;

        if ($usu && $usu != 0) {
            $whereU = " and r.radi_usua_actu=$usu ";
            $whereUH = " and h.usua_codi=$usu ";
        }

        $iSql = "SELECT c.mrec_desc AS MEDIO_RECEPCION, count(DISTINCT r.radi_nume_radi)as RADICADOS, max(c.MREC_CODI) AS CODI
                FROM
                    MEDIO_RECEPCION c, radicado r
                    INNER JOIN
                    (select h1.radi_nume_radi as radi_nume_radi, h1.id, h1.sgd_ttr_codigo as sgd_ttr_codigo, h1.usua_doc as usua_doc, h1.depe_codi as depe_codi from hist_eventos h1
                    INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi, min(hist_eventos.id) as id  from hist_eventos where hist_eventos.sgd_ttr_codigo = 2
                    GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id  ) htev ON  r.radi_nume_radi = htev.radi_nume_radi
                    LEFT JOIN  usuario  ON   htev.usua_doc = usuario.usua_doc
                WHERE r.radi_fech_radi BETWEEN ('$fini 00:00:00') and ('$ffin 23:59:59')  AND r.mrec_codi=c.mrec_codi
                    $whereTipoRadicado $where
                    GROUP BY c.mrec_desc ";
        $iSql = "   select sgd_fenv_descrip nomb,m.sgd_fenv_codigo cod , count(2) num
    from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b
    where b.sgd_fenv_codigo=m.sgd_fenv_codigo and b.sgd_renv_fech BETWEEN ('$fini 00:00:00') and ('$ffin 23:59:59')
    AND	(b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null)
    and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) and m.sgd_fenv_codigo<>106
    group by sgd_fenv_descrip,m.sgd_fenv_codigo
    UNION
    select sgd_fenv_descrip nomb,m.sgd_fenv_codigo cod , count(2) num
    from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b
    where b.sgd_fenv_codigo=m.sgd_fenv_codigo and b.sgd_renv_fech BETWEEN ('$fini 00:00:00') and ('$ffin 23:59:59')
    AND	(b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null)
    and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) and m.sgd_fenv_codigo=106 and radi_nume_grupo is null
    group by sgd_fenv_descrip,m.sgd_fenv_codigo ";
      //   $this->link->conn->debug =true;
        $rs = $this->link->conn->query($iSql);

        if (!$rs->EOF) {
            $i = 1;
            while (!$rs->EOF) {
                foreach ($rs->fields as $key => $value) {
                    $dd[$rs->fields['COD']][strtoupper($key)] = $value;
                }
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }
/*  */

        $Sql2 = "select m.sgd_fenv_codigo cod , count(2) dev
        from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b,hist_eventos h
        where b.sgd_fenv_codigo=m.sgd_fenv_codigo and b.sgd_renv_fech BETWEEN  ('$fini 00:00:00') and ('$ffin 23:59:59')
        AND	(b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null) and h.radi_nume_radi=b.radi_nume_sal and sgd_ttr_codigo=28
        and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) and m.sgd_fenv_codigo<>106
        group by m.sgd_fenv_codigo UNION
 select m.sgd_fenv_codigo cod, count(2) dev
 from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b,hist_eventos h
 where b.sgd_fenv_codigo=m.sgd_fenv_codigo and b.sgd_renv_fech BETWEEN  ('$fini 00:00:00') and ('$ffin 23:59:59')
 AND	(b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null) and h.radi_nume_radi=b.radi_nume_sal and sgd_ttr_codigo=28
 and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) and m.sgd_fenv_codigo=106 and radi_nume_grupo is null and h.hist_obse='Devolución E-mail (DOCUMENTO MAL RADICADO). Devolución por gestión de Rebote'
 group by m.sgd_fenv_codigo ";
        $rs = $this->link->conn->query($Sql2);
        if (!$rs->EOF) {
            while (!$rs->EOF) {
                foreach ($rs->fields as $key => $value) {
                    $dd[$rs->fields['COD']][strtoupper($key)] = $value;
                }
                $rs->MoveNext();
            }

            //  $resp['titulo'] = $campot;
        }
        $i = 1;
        foreach ($dd as $key => $value) {
            $datos[] = $value;
            // $datos[$i+1]['NUb'] = $i;
            $i++;
        }
        return $datos;
    }
/**
 * rp4 tra los radicado sin digitalizar
 */
    public function rp4($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpRad)
    {
        $whereTipoRadicado = $tpRad ? " and r.sgd_trad_codigo = $tpRad " : '';
        if ($tpAds == 1) {
            $depe = $this->depahijas($depe);
        }
        $whereADD = $where2 = '';
        $where = '';
        $where2TB = '';
        $resp['depe'] = $depe;
        if ($depe != 99999) {
            //$where = "and r.radi_depe_radi in ($depe) ";
            $condicionE = "	AND h.DEPE_CODI=$depe AND b.depe_codi = $depe";
        }
        if ($tpdoc != 0) {
            $whereADD .= " and r.TDOC_CODI=$tpdoc ";
        }

        $whereU = $whereI = $ddsubserie = '';
        if ($subserie && $subserie != 0) {
            $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
        }

        if ($serie != 0) {
            $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
              INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
        }

        //     echo $whereI;

        if ($usu && $usu != 0) {
            $whereU = " and r.radi_usua_actu=$usu ";
            $whereUH = " and h.usua_codi=$usu ";
        }
        //   $this->link->conn->debug =true;
        $iSql = "SELECT b.USUA_NOMB AS USUARIO, count(1) AS RADICADOS, SUM(r.RADI_NUME_FOLIO) AS HOJAS_DIGITALIZADAS, MIN(b.USUA_CODI) AS COD_U, MIN(b.DEPE_CODI) AS DEPE
                FROM RADICADO r, USUARIO b, HIST_EVENTOS h
                WHERE  h.USUA_CODI=b.usua_CODI  AND b.depe_codi = h.depe_codi
        $condicionE
        AND h.RADI_NUME_RADI=r.RADI_NUME_RADI  AND h.SGD_TTR_CODIGO IN(22,42)
        AND r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') 
        $whereTipoRadicado 
            GROUP BY b.USUA_NOMB
            ORDER BY 1 ";
        $rs = $this->link->conn->query($iSql);

        if (!$rs->EOF) {
            $i = 1;
            while (!$rs->EOF) {
                $dd['NUM'] = $i;
                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        $dd[strtoupper($key)] = $value;
                    }
                }
                $datos[] = $dd;
                $i++;
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }

        return $datos;
    }

    public function rp6($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpRad)
    {
        $whereTipoRadicado = $tpRad ? " and r.sgd_trad_codigo = $tpRad " : '';
        $whereDependencia = $depe != '99999' ? " and r.radi_depe_actu = $depe " : '';
	$queryE = "SELECT
	    da.DEPE_CODI || ' - ' || da.DEPE_NOMB as DEPENDENCIA_ACTUAL,
	    count(DISTINCT r.radi_nume_radi)as RADICADOS,
	    da.DEPE_CODI as CODI_DEPE_ACTUAL
	    FROM
	    radicado r 
		LEFT JOIN hist_eventos htev ON r.radi_nume_radi = htev.radi_nume_radi AND htev.sgd_ttr_codigo = 2
		LEFT JOIN dependencia da ON r.radi_depe_actu = da.depe_codi
		WHERE
		r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') 
		$whereTipoRadicado
		$whereDependencia
		GROUP BY CODI_DEPE_ACTUAL
		ORDER BY 1";

        $rs = $this->link->conn->query($queryE);

        if (!$rs->EOF) {
            $i = 1;
            while (!$rs->EOF) {
                $dd['NUM'] = $i;
                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        $dd[strtoupper($key)] = $value;
                    }
                }
                $datos[] = $dd;
                $i++;
                $rs->MoveNext();
            }
        }

        return $datos;
    }
    /**
 * rp7 tra 
 */
public function rp7($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpRad)
{
    $whereTipoRadicado = $tpRad ? " and r.sgd_trad_codigo = $tpRad " : '';
    if ($tpAds == 1) {
        $depe = $this->depahijas($depe);
    }
    $whereADD = $where2 = '';
    $where = '';
    $where2TB = '';
    $resp['depe'] = $depe;
    if ($depe != 99999) {
        //$where = "and r.radi_depe_radi in ($depe) ";
        $whereDependencia = "	r.radi_depe_actu = $depe";
    }
    if ($tpdoc != 0) {
        $whereTipoRadicado .= " and r.TDOC_CODI=$tpdoc ";
    }

    $whereU = $whereI = $ddsubserie = '';
    if ($subserie && $subserie != 0) {
        $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
    }

    if ($serie != 0) {
        $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
          INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
    }

    //     echo $whereI;

    if ($usu && $usu != 0) {
        $whereU = " and r.radi_usua_actu=$usu ";
        $whereUH = " and h.usua_codi=$usu ";
    }
    //   $this->link->conn->debug =true;
		$COD_RADICACION = 2;
		$COD_DIGITALIZACION = 42;
		$queryE = "SELECT 
			u3.usua_nomb as USUARIO,
			count(distinct r.radi_nume_radi) as RADICADOS,
			MIN(u3.depe_codi) as HID_DEPE_USUA,
			MIN(u3.USUA_CODI) as HID_COD_USUARIO,
			MIN(u3.USUA_DOC) as HID_DOC_USUARIO
		FROM dependencia df,dependencia da, RADICADO r
		LEFT OUTER JOIN SGD_TPR_TPDCUMENTO t ON r.tdoc_codi=t.SGD_TPR_CODIGO 
		LEFT OUTER JOIN SGD_DIR_DRECCIONES dir ON r.radi_nume_radi = dir.radi_nume_radi	and dir.sgd_dir_tipo = '1'
		LEFT JOIN hist_eventos he1 ON r.radi_nume_radi = he1.radi_nume_radi AND he1.sgd_ttr_codigo = ".$COD_DIGITALIZACION."
			LEFT JOIN hist_eventos he2 ON r.radi_nume_radi = he2.radi_nume_radi AND he2.sgd_ttr_codigo = ".$COD_RADICACION."
			LEFT JOIN USUARIO u1 ON u1.usua_codi = he1.usua_codi_dest and u1.depe_codi = he1.depe_codi_dest
			LEFT JOIN USUARIO u2 ON u2.usua_codi = he2.usua_codi_dest and u2.depe_codi = he2.depe_codi_dest
			LEFT JOIN USUARIO u3 ON u3.usua_codi = he2.usua_codi and u3.depe_codi = he2.depe_codi
			LEFT JOIN dependencia d3 ON d3.depe_codi = he2.depe_codi
		WHERE 
		r.radi_depe_actu=da.depe_codi AND
		r.RADI_DEPE_RADI=df.DEPE_CODI 
    		AND r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') 
		$whereTipoRadicado $whereDependencia
		AND r.radi_nume_radi::text LIKE '%2'
		group by 
		u3.id,
		u3.usua_codi,
		u3.depe_codi,
		u3.usua_nomb";
    $rs = $this->link->conn->query($queryE);

    if (!$rs->EOF) {
        $i = 1;
        while (!$rs->EOF) {
            $dd['NUM'] = $i;
            foreach ($rs->fields as $key => $value) {
                if ($key != 'RADI') {
                    $dd[strtoupper($key)] = $value;
                }
            }
            $datos[] = $dd;
            $i++;
            $rs->MoveNext();
        }
    }

    return $datos;
}
    public function rp9($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin)
    {

        if ($tpAds == 1) {
            $depe = $this->depahijas($depe);
        }
        $whereADD = $where2 = '';
        $where = '';
        $where2TB = '';
        $resp['depe'] = $depe;
        if ($depe != 99999) {
            $where = "and r.radi_depe_actu in ($depe) ";
            //$where2J = "left join hist_eventos h on h.depe_codi_dest=999 and h.depe_codi  in ($depe) and r.radi_nume_Radi=h.radi_nume_Radi  and h.sgd_ttr_codigo=13";
            $where2Tb = " hist_eventos h, ";
            $where2 = " and h.depe_codi_dest=r.radi_depe_actu and h.depe_codi  in ($depe) and r.radi_nume_Radi=h.radi_nume_Radi  and h.sgd_ttr_codigo  in (13,65)";
        }
        if ($tpdoc != 0) {
            $whereADD .= " and r.TDOC_CODI=$tpdoc ";
        }

        $whereU = $whereI = $ddsubserie = '';
        if ($subserie && $subserie != 0) {
            $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
        }

        if ($serie != 0) {
            $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
              INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
        }

        //     echo $whereI;

        if ($usu && $usu != 0) {
            $whereU = " and r.radi_usua_actu=$usu ";
            $whereUH = " and h.usua_codi=$usu ";
        }
        $iSql = "select
        count(distinct r.radi_nume_radi) num
        from $where2Tb radicado r
        left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO
        left join sgd_dir_drecciones dir on dir.radi_nume_radi = r.radi_nume_radi
        left join municipio m2 on dir.dpto_codi = m2.dpto_codi and dir.muni_codi = m2.muni_codi
        left join departamento d2 on d2.dpto_codi = m2.dpto_codi
        left join anexos a on r.radi_nume_radi = a.anex_radi_nume
        left join dependencia d on d.depe_codi = a.anex_depe_creador
        left join usuario u on u.usua_codi = r.radi_usua_actu and u.depe_codi = r.radi_depe_actu
        left join dependencia du on du.depe_codi = u.depe_codi
        $whereI
        where r.sgd_trad_codigo = 2 and r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59')
        and r.radi_depe_actu = 999  $where2 $whereADD $whereUH ";
        $iSql2 = "select
        count(distinct r.radi_nume_radi) num
        from radicado r
        left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO
        left join sgd_dir_drecciones dir on dir.radi_nume_radi = r.radi_nume_radi
        left join municipio m2 on dir.dpto_codi = m2.dpto_codi and dir.muni_codi = m2.muni_codi
        left join departamento d2 on d2.dpto_codi = m2.dpto_codi
        left join anexos a on r.radi_nume_radi = a.anex_radi_nume 
        left join dependencia d on d.depe_codi = a.anex_depe_creador
        left join usuario u on u.usua_codi = r.radi_usua_actu and u.depe_codi = r.radi_depe_actu
        left join dependencia du on du.depe_codi = u.depe_codi
        $whereI
        where r.sgd_trad_codigo = 2 and r.radi_fech_radi between  ('$fini 00:00:00') and ('$ffin 23:59:59')
        and r.radi_depe_actu <> 999  $where $whereADD $whereU";
        // echo $resp['SQL'] = $iSql2;
        $rs = $this->link->conn->query($iSql);//--and a.anex_salida = 1
        $rs2 = $this->link->conn->query($iSql2);

        $datos['tramitado'] = 0;
        $datos['entramite'] = 0;
        if (!$rs->EOF) {
            $datos['tramitado'] = $rs->fields['NUM'] ? $rs->fields['NUM'] : 0;
        }
        if (!$rs2->EOF) {
            $datos['entramite'] = $rs2->fields['NUM'] ? $rs2->fields['NUM'] : 0;
        }
        //  print_r($datos);
        return $datos;
    }
//detalles reporte 1
    public function dtrp1($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq, $tpRad)
    {

        //    echo "$depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq";
       //    $this->link->conn->debug =true;
        //tpbusq id de usuario
        if ($tpAds == 1 && $tpbusq == 'T') {
            $depe = $this->depahijas($depe);
        }
        $whereADD = $where2 = '';
        $where = '';
        $where2TB = '';
        $resp['depe'] = $depe;
        $where = " and h1.usua_doc='$tpbusq' ";
        if ($tpbusq == 'T') {
            $where = '';
            if ($depe != 99999) {
                $where = "and h1.depe_codi in ($depe) ";
            }

        }
        if ($tpdoc != 0) {
            $whereADD .= " and r.TDOC_CODI=$tpdoc ";
        }

        $whereU = $whereI = $ddsubserie = '';
        if ($subserie && $subserie != 0) {
            $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
        }

        if ($serie != 0) {
            $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
          INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
        }

        //     echo $whereI;

        if ($usu && $usu != 0) {
            $whereU = " and r.radi_usua_actu=$usu ";
            $whereUH = " and h.usua_codi=$usu ";
        }
        
        
            $whereTipoRadicado = $tpRad ? " and r.sgd_trad_codigo = $tpRad " : '';
        

   /*     $iSql = "SELECT *
                FROM  radicado r
                INNER JOIN
                    (	select h1.radi_nume_radi as radi_nume_radi,  h1.id,  h1.sgd_ttr_codigo as sgd_ttr_codigo,  h1.usua_doc as usua_doc,  h1.depe_codi as depe_codi from hist_eventos h1
                        INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi,  min(hist_eventos.id) as id  from hist_eventos where hist_eventos.sgd_ttr_codigo = 2  GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id  ) htev
                    ON
                        r.radi_nume_radi = htev.radi_nume_radi
                LEFT JOIN  usuario b  ON   htev.usua_doc = b.usua_doc
				left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO 
        where  r.radi_fech_radi between ('2021-04-01 00:00:00') and ('2021-04-30 23:59:59')    and r.sgd_trad_codigo = 2 --and b.usua_doc='8235' 
		and b.USUA_CODI=11604 and b.depe_codi=8235";*/
    $iSql=" 
    select  distinct h.radi_nume_radi as radi, r.radi_depe_radi, to_char(r.radi_fech_radi, 'DD-MM-YYYY HH24:MI') rfech, td.sgd_tpr_descrip tpnomb, r.ra_asun asunto, b.USUA_NOMB as USUAR,
    b.depe_codi as DEPEI, r.radi_depe_actu DPA, u.usua_nomb usuaa, 
   radi_nume_folio FOL,  '' dig , mrec_codi mrec,'' FECHADig , '' usud,r.radi_nume_deri ASOCIADO,'' dpto,'' proyecto,'' muni,'' EMAIL,'' REM
    from radicado r
     left join usuario u on u.usua_codi = r.radi_usua_actu and u.depe_codi=r.radi_depe_actu 
          left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO 
    ,hist_eventos h
        LEFT JOIN usuario b ON h.usua_doc = b.usua_doc 
        where h.sgd_ttr_codigo = 2 
        and r.radi_nume_radi=h.radi_nume_radi  and (r.sgd_eanu_codigo not in (1,2) or   r.sgd_eanu_codigo is null)
        and r.radi_fech_radi between  ('$fini 00:00:00') and ('$ffin 23:59:59') $where  $whereTipoRadicado order by rfech desc ";   
        $iSql=" 
        select  distinct r.radi_nume_radi as radi, r.radi_depe_radi, to_char(r.radi_fech_radi, 'DD-MM-YYYY HH24:MI') rfech, td.sgd_tpr_descrip tpnomb, r.ra_asun asunto, b.USUA_NOMB as USUAR,
        b.depe_codi as DEPEI, r.radi_depe_actu DPA, u.usua_nomb usuaa, 
       radi_nume_folio FOL,  '' dig , mrec_codi mrec,'' FECHADig , '' usud,r.radi_cuentai  ||' '||r.radi_nume_deri ||' ' ASOCIADO,'' dpto,'' proyecto,'' muni,'' EMAIL,'' REM
       FROM
       radicado r INNER JOIN ( select h1.radi_nume_radi as radi_nume_radi, h1.id, h1.sgd_ttr_codigo as sgd_ttr_codigo, h1.usua_doc as usua_doc, h1.depe_codi as depe_codi from hist_eventos h1 
      INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi, min(hist_eventos.id) as id from hist_eventos where hist_eventos.sgd_ttr_codigo = 2 GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id $where) htev ON r.radi_nume_radi = htev.radi_nume_radi 
      LEFT JOIN usuario b ON htev.usua_doc = b.usua_doc 
       left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO  left join usuario u on u.usua_codi = r.radi_usua_actu and u.depe_codi=r.radi_depe_actu 
       where  (r.sgd_eanu_codigo not in (1,2) or   r.sgd_eanu_codigo is null)
            and r.radi_fech_radi between  ('$fini 00:00:00') and ('$ffin 23:59:59')   $whereTipoRadicado order by rfech desc ";
      
        //trae info de total
        //trae la informacion de los radicados

      // die($iSql);
        $rs = $this->link->conn->query($iSql);
        $nomdebe = $this->dependecias();

        if (!$rs->EOF) {
            $i = 1;
            $arrayRAD = '';
            $coma = '';
            // echo "hd";
            while (!$rs->EOF) {

                $arrayRAD .= $coma . $rs->fields['RADI'];
                foreach ($rs->fields as $key => $value) {

                    if ($key == 'DEPEI') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $nomdebe[$value];
                    } elseif ($key == 'DPA') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $nomdebe[$value];
                    } elseif ($key == 'MREC') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $this->medioRecp [$value];
                    }elseif($key == 'ASUNTO') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] =str_replace($this->bsq,$this->strcambio,htmlentities( str_replace(array('<','>','','','',"\u23FD"),'', $value)));
                    } else {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                    }

                }
             //   $datoss[$rs->fields['RADI']]['NUM'] = $i;
                //$datoss[$rs->fields['RADI']]['DPAN'] = $nomdebe[$rs->fields['DPA']];
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }

        //trae info de sgd_dir_Dreciones
        $selectB = "select dir.radi_nume_radi radi, dir.SGD_DIR_DIRECCION dir,dir.SGD_DIR_MAIL email,dir.SGD_DIR_NOMREMDES dig,
        dir.SGD_DIR_TELEFONO tel,dir.sgd_dir_mail,dir.sgd_dir_nombre dnom,  dir.sgd_dir_nombre drem,dir.sgd_dir_apellido arem,muni_codi muni,dpto_codi dpto
         from  sgd_dir_drecciones dir
        where  dir.radi_nume_radi  in ($arrayRAD) ";
        $rs = $this->link->conn->query($selectB);

        if (!$rs->EOF) {
            $i = 0;
            //print_r($this->depart);
            while (!$rs->EOF) {

                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                       if($key=='DPTO')
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $this->depart[$value];
                       else if($key=='MUNI')
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $this->munici[$rs->fields['DPTO']][$value];
                        else if($key=='DREM')
                        $datoss[$rs->fields['RADI']]['REM'] = $value.' '.$rs->fields['AREM'];
                                               else
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;

                    }
                }
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }

        }

        //buscar historico fecha de digitalizacion 

        if ($arrayRAD) {

            $selectA = " select to_char( h.hist_fech , 'DD-MM-YYYY') FECHADig,h.radi_nume_radi radi,u.usua_nomb usud
            from  hist_eventos h left join usuario u on u.usua_doc = h.usua_doc 
             where  h.radi_nume_radi in ($arrayRAD) and h.sgd_ttr_codigo in (22,42,23); ";

            $rs = $this->link->conn->query($selectA);

            if (!$rs->EOF) {
                $i = 0;
                while (!$rs->EOF) {
                    foreach ($rs->fields as $key => $value) {
                        if ($key != 'RADI') {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                        }
                    }
                    $i++;
                    $coma = ',';
                    $rs->MoveNext();
                }
                //  $resp['titulo'] = $campot;
            }

     
        $selectA = "select  a.radi_nume_salida radi, anex_creador proyecto
         from  anexos a 
          where   a.radi_nume_salida in ($arrayRAD)  ";
        $rs = $this->link->conn->query($selectA);
    //  echo "$selectA";
        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {

                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {                        
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                    }
                }
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }
      }
        /*
        $selectB = "select distinct  r.radi_nume_deri radi, r.radi_nume_radi ASOCIADO
        from  radicado r where  r.radi_nume_deri in ($arrayRAD) ";
    $rs = $this->link->conn->query($selectB);

    if (!$rs->EOF) {
        $i = 0;
        while (!$rs->EOF) {

            foreach ($rs->fields as $key => $value) {
                if ($key != 'RADI') {
                    $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                }
            }
            $i++;
            $coma = ',';
            $rs->MoveNext();
        }
        //  $resp['titulo'] = $campot;
    }*/
$i=1;
        foreach ($datoss as $key => $value) {
            $value['NUM']=$i;
            $datos[] = $value;
            $i++;
        }
        return $datos;
    }
    //detalles reporte 1
    public function dtrp2($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq, $tpRad)
    {

        //    echo "$depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq";
        //   $this->link->conn->debug =true;
        //tpbusq id de usuario
        if ($tpAds == 1 && $tpbusq == 'T') {
            $depe = $this->depahijas($depe);
        }
        $whereADD = $where2 = '';
        $where = '';
        $where2TB = '';
        $resp['depe'] = $depe;
        if ($tpbusq != 'T') 
        $where = "and c.mrec_codi=$tpbusq  ";
        if ($depe != 99999) {
            $where .= "and r.radi_depe_radi in ($depe) ";
        }

        if ($tpdoc != 0) {
            $whereADD .= " and r.TDOC_CODI=$tpdoc ";
        }

        $whereU = $whereI = $ddsubserie = '';
        if ($subserie && $subserie != 0) {
            $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
        }

        if ($serie != 0) {
            $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
          INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
        }

        //     echo $whereI;

        if ($usu && $usu != 0) {
            $whereU = " and r.radi_usua_actu=$usu ";
            $whereUH = " and h.usua_codi=$usu ";
        }
        
        
            $whereTipoRadicado = $tpRad ? " and r.sgd_trad_codigo = $tpRad " : '';
        

        $iSql = "SELECT distinct r.radi_nume_radi radi, to_char(r.radi_fech_radi, 'DD-MM-YYYY HH24:MI') rfech,r.RA_ASUN 	AS ASUNTO  ,c.MREC_DESC 	AS MEDIO_RECEPCION        ,b.usua_nomb 	AS USUARIO        ,r.RADI_PATH 	AS HID_RADI_PATH
            FROM   MEDIO_RECEPCION c, radicado r 
    INNER JOIN
    (select h1.radi_nume_radi as radi_nume_radi, h1.id, h1.sgd_ttr_codigo as sgd_ttr_codigo, h1.usua_doc as usua_doc, h1.depe_codi as depe_codi from hist_eventos h1 INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi, min(hist_eventos.id) as id  from hist_eventos where hist_eventos.sgd_ttr_codigo = 2  GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id  ) htev
    ON 
        r.radi_nume_radi = htev.radi_nume_radi
    LEFT JOIN
    usuario b
    ON 
        htev.usua_doc = b.usua_doc
    where  r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') AND r.mrec_codi=c.mrec_codi   $where  $whereTipoRadicado ";
		


        //trae info de total
        //trae la informacion de los radicados

   
        $rs = $this->link->conn->query($iSql);
        $nomdebe = $this->dependecias();

        if (!$rs->EOF) {
            $i = 1;
            $arrayRAD = '';
            $coma = '';
            // echo "hd";
            while (!$rs->EOF) {

                $arrayRAD .= $coma . $rs->fields['RADI'];
                foreach ($rs->fields as $key => $value) {

                    if ($key == 'DEPEI') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $nomdebe[$value];
                    } elseif ($key == 'DPA') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $nomdebe[$value];
                    }elseif ($key == 'USUARIO') {
                        $nomss='USUARIO DE ARCHIVO';
                        if($value)$nomss=$value;
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $nomss;
                    } elseif($key == 'ASUNTO') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] =htmlentities( str_replace(array('<','>','','','',"\u23FD"),'', $value));
                    } else {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                    }

                }
                $datoss[$rs->fields['RADI']]['NUM'] = $i;
                //$datoss[$rs->fields['RADI']]['DPAN'] = $nomdebe[$rs->fields['DPA']];
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }

        //trae info de sgd_dir_Dreciones
  /*      $selectB = "select dir.radi_nume_radi radi, dir.SGD_DIR_DIRECCION dir,dir.SGD_DIR_MAIL email,dir.SGD_DIR_NOMREMDES dig,
        dir.SGD_DIR_TELEFONO tel,dir.sgd_dir_mail, dir.sgd_dir_nombre||' '||dir.sgd_dir_apellido rem
         from  sgd_dir_drecciones dir
        where  dir.radi_nume_radi  in ($arrayRAD) ";
        $rs = $this->link->conn->query($selectB);

        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {

                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;

                    }
                }
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }

        }*/

        foreach ($datoss as $key => $value) {
            $datos[] = $value;
        }
        return $datos;
    }

//detalles reporte 3
    public function dtrp3($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq)
    {

        //echo "$depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq";
         //  $this->link->conn->debug =true;
        //tpbusq id de usuario
        if ($tpAds == 1 && $tpbusq == 'T') {
            $depe = $this->depahijas($depe);
        }
        $whereADD = $where2 = '';
        $where = '';
        $where2TB = '';
        $resp['depe'] = $depe;
        $where = "and radi_usua_radi=$tpbusq and r.radi_depe_radi='$depe' ";
        if ($tpbusq == 'T') {
            $where = '';
            if ($depe != 99999) {
                $where = "and r.radi_depe_radi in ($depe) ";
            }

        }
        if ($tpdoc != 0) {
            $whereADD .= " and r.TDOC_CODI=$tpdoc ";
        }

        $whereU = $whereI = $ddsubserie = '';
        if ($subserie && $subserie != 0) {
            $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
        }

        if ($serie != 0) {
            $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
      INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
        }

        //     echo $whereI;

        if ($usu && $usu != 0) {
            $whereU = " and r.radi_usua_actu=$usu ";
            $whereUH = " and h.usua_codi=$usu ";
        }
if($tpbusq=='106'){
   /* $iSql = "select distinct b.radi_nume_sal radi, b.depe_codi depe,b.sgd_renv_fech fech,'0' dev
    from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b
    where b.sgd_fenv_codigo=m.sgd_fenv_codigo  and b.sgd_renv_fech BETWEEN ('$fini 00:00:00') and ('$ffin 23:59:59')
    AND	(b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null)
    and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) and m.sgd_fenv_codigo=106 and radi_nume_grupo is not null";*/
    $iSql = "	select distinct b.radi_nume_sal radi, count(1) env
    from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b
    where b.sgd_fenv_codigo=m.sgd_fenv_codigo  and b.sgd_renv_fech BETWEEN ('$fini 00:00:00') and ('$ffin 23:59:59')
    AND	(b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null)
    and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) and m.sgd_fenv_codigo=106 and radi_nume_grupo is null
    group by b.radi_nume_sal
     ";

}
elseif($tpbusq!='T'){
    $iSql = "   select distinct b.radi_nume_sal radi, count(1) ENV
    from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b
    where b.sgd_fenv_codigo=m.sgd_fenv_codigo and b.sgd_renv_fech BETWEEN ('$fini 00:00:00') and ('$ffin 23:59:59')
    AND    (b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null)
    and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) and m.sgd_fenv_codigo=$tpbusq
    group by b.radi_nume_sal ";
}else{
    $iSql = "   select distinct b.radi_nume_sal radi, count(1) ENV
    from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b
    where b.sgd_fenv_codigo=m.sgd_fenv_codigo and b.sgd_renv_fech BETWEEN ('$fini 00:00:00') and ('$ffin 23:59:59')
    AND    (b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null)
    and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) 
    group by b.radi_nume_sal ";
}
        /*   $iSql = "SELECT distinct r.radi_nume_radi radi, to_char(r.radi_fech_radi, 'DD-MM-YYYY HH24:MI') rfech,td.sgd_tpr_descrip tpnomb,
        r.ra_asun asunto,b.USUA_NOMB as USUAR,b.depe_codi as DEPEI, r.radi_depe_actu DPA,u.usua_nomb usuaa, radi_nume_folio FOL,'' REM,'' dig
        FROM  radicado r
        INNER JOIN
        (    select h1.radi_nume_radi as radi_nume_radi, h1.id, h1.sgd_ttr_codigo as sgd_ttr_codigo, h1.usua_doc as usua_doc, h1.depe_codi as depe_codi from hist_eventos h1
        INNER JOIN (select distinct hist_eventos.radi_nume_radi as radi_nume_radi, min(hist_eventos.id) as id  from hist_eventos where hist_eventos.sgd_ttr_codigo = 2  GROUP BY hist_eventos.radi_nume_radi) h2 on h1.id = h2.id  ) htev
        ON
        r.radi_nume_radi = htev.radi_nume_radi
        LEFT JOIN  usuario b  ON   htev.usua_doc = b.usua_doc
        left join usuario u on u.usua_codi = r.radi_usua_actu   and u.depe_codi=r.radi_depe_actu
        left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO
        where  r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59')  $where";
        
        UNION
        select sgd_fenv_descrip nomb,m.sgd_fenv_codigo cod , count(2) num
        from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b
        where b.sgd_fenv_codigo=m.sgd_fenv_codigo and b.sgd_renv_fech BETWEEN ('$fini 00:00:00') and ('$ffin 23:59:59')
        AND    (b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null)
        and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) and m.sgd_fenv_codigo=106 and radi_nume_grupo is null
        group by sgd_fenv_descrip,m.sgd_fenv_codigo ";*/
//radicados a evaluar
     
        //trae info de total
        //trae la informacion de los radicados

        //   die();
        $rs = $this->link->conn->query($iSql);

        //$nomdebe = $this->dependecias();
        $arrayRAD = $dd= $coma='';
        if (!$rs->EOF) {
            $i = 1;
            // echo "hd";
            while (!$rs->EOF) {
                $arrayRAD .= $coma . $rs->fields['RADI'];
                $datoss[$rs->fields['RADI']]['DEV']=0;
                $datoss[$rs->fields['RADI']]['FECH']='';
                foreach ($rs->fields as $key => $value) {

                    $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                }
                $dd = $rs->fields['ENV'] + $dd;
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }
        //para la 106
        if($tpbusq=='106'){
        //trae info de envio
        /*$selectB = " select distinct b.radi_nume_sal radi, b.depe_codi depe
            from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b
            where b.sgd_fenv_codigo=m.sgd_fenv_codigo  and b.sgd_renv_fech BETWEEN ('$fini 00:00:00') and ('$ffin 23:59:59')
            AND	(b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null) and b.radi_nume_sal  in ($arrayRAD)
            and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) and m.sgd_fenv_codigo=106 and radi_nume_grupo is not null
         ";*/
         $selectB = "	select distinct b.radi_nume_sal radi ,to_char( b.sgd_renv_fech, 'DD-MM-YYYY HH24:MI') fech,'0' dev
 from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b
 where b.sgd_fenv_codigo=m.sgd_fenv_codigo  and b.sgd_renv_fech BETWEEN ('$fini 00:00:00') and ('$ffin 23:59:59')
 AND	(b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null) and b.radi_nume_sal  in ($arrayRAD)
 and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) and m.sgd_fenv_codigo=106 and radi_nume_grupo is null
 ";
	}elseif($tpbusq!='T'){
    $selectB = "  select distinct b.radi_nume_sal radi ,to_char( b.sgd_renv_fech, 'DD-MM-YYYY HH24:MI') fech,'0' dev
    from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b
    where b.sgd_fenv_codigo=m.sgd_fenv_codigo and b.sgd_renv_fech BETWEEN ('$fini 00:00:00') and ('$ffin 23:59:59')
    AND    (b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null) and b.radi_nume_sal  in ($arrayRAD)
    and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) and m.sgd_fenv_codigo=$tpbusq";

	}else{
    $selectB = "  select distinct b.radi_nume_sal radi ,to_char( b.sgd_renv_fech, 'DD-MM-YYYY HH24:MI') fech,'0' dev
    from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b
    where b.sgd_fenv_codigo=m.sgd_fenv_codigo and b.sgd_renv_fech BETWEEN ('$fini 00:00:00') and ('$ffin 23:59:59')
    AND    (b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null) and b.radi_nume_sal  in ($arrayRAD)
    and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null)";
  }
        $rs = $this->link->conn->query($selectB);

        if (!$rs->EOF) {
            $i = 0;
            //$dd = 0;
            while (!$rs->EOF) {

                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                    }
                   
                }
              //  $dd = $rs->fields['ENV'] + $dd;
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
        }
    
            //trae info de devolucion
            
            if($tpbusq=='106'){
            $selectB = "	select b.radi_nume_sal radi, count(1) dev
        from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b,hist_eventos h
        where b.sgd_fenv_codigo=m.sgd_fenv_codigo and b.sgd_renv_fech BETWEEN  ('$fini 00:00:00') and ('$ffin 23:59:59')
        AND	(b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null) and h.radi_nume_radi=b.radi_nume_sal and sgd_ttr_codigo=28  and b.radi_nume_sal  in  ($arrayRAD)
        and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) and m.sgd_fenv_codigo=$tpbusq and radi_nume_grupo is null and h.hist_obse='Devolución E-mail (DOCUMENTO MAL RADICADO). Devolución por gestión de Rebote'
        group by b.radi_nume_sal    ";}
	elseif($tpbusq!='T'){
         $selectB =  "select b.radi_nume_sal radi, count(1) dev
        from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b,hist_eventos h
        where b.sgd_fenv_codigo=m.sgd_fenv_codigo and b.sgd_renv_fech BETWEEN  ('$fini 00:00:00') and ('$ffin 23:59:59')
        AND	(b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null) and h.radi_nume_radi=b.radi_nume_sal and sgd_ttr_codigo=28   and b.radi_nume_sal  in  ($arrayRAD)
        and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null) and m.sgd_fenv_codigo=$tpbusq
        group by  b.radi_nume_sal ";}
	else{
         $selectB =  "select b.radi_nume_sal radi, count(1) dev
        from SGD_FENV_FRMENVIO m, SGD_RENV_REGENVIO b,hist_eventos h
        where b.sgd_fenv_codigo=m.sgd_fenv_codigo and b.sgd_renv_fech BETWEEN  ('$fini 00:00:00') and ('$ffin 23:59:59')
        AND	(b.sgd_renv_planilla != '00' or b.sgd_renv_planilla is null) and h.radi_nume_radi=b.radi_nume_sal and sgd_ttr_codigo=28   and b.radi_nume_sal  in  ($arrayRAD)
        and (b.sgd_renv_observa not like 'Masiva%' or  b.sgd_renv_observa is null)
        group by  b.radi_nume_sal ";}

      //   echo $selectB;
            $rs = $this->link->conn->query($selectB);

            if (!$rs->EOF) {
                $i = 0;
                $dd2 = 0;
                while (!$rs->EOF) {

                    foreach ($rs->fields as $key => $value) {
                        if ($key != 'RADI') {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;

                        }
                    }
                    $dd2 = $rs->fields['DEV'] + $dd2;
                    $i++;
                    $rs->MoveNext();
                }

            }
        
        $i = 1;
        foreach ($datoss as $key => $value) {
            if($value['ENV'] )
            $datoss[$key]['NUM'] = $i;
            $i++;

        }
        foreach ($datoss as $key => $value) {
            if ($value['ENV']) {
                $dataw[] = $value;
            }

        }
        $datos['ENVIADOS'] = $dd;
        $datos['DEVUELTOS'] = $dd2?$dd2:0;
        $datos['datos'] = $dataw;
        return $datos;
    }


    //detalles reporte 4
    public function dtrp4($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq, $tpRad)
    {

         //   echo "$depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq";
           //$this->link->conn->debug =true;
        //tpbusq id de usuario
        $whereTipoRadicadoD='';
        if ($tpAds == 1 && $tpbusq == 'T') {
            $depe = $this->depahijas($depe);
        }
        $whereADD = $where2 = '';
        $where = '';
        $where2TB = '';
        $resp['depe'] = $depe;
            $where = '';
            if ($depe != 99999) {
                $where = "	AND h.DEPE_CODI=$depe AND b.depe_codi = $depe ";	
            }

        
        if ($tpdoc != 0) {
            $whereADD .= " and r.TDOC_CODI=$tpdoc ";
        }

        $whereU = $whereI = $ddsubserie = '';
        if ($subserie && $subserie != 0) {
            $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
        }

        if ($serie != 0) {
            $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
          INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
        }

        if ($usu && $usu != 0) {
            $whereU = " and r.radi_usua_actu=$usu ";
            $whereUH = " and h.usua_codi=$usu ";
        }       
         $whereTipoRadicado = $tpRad ? " and r.sgd_trad_codigo = $tpRad " : '';

    		$iSql = "SELECT 
            r.radi_nume_radi AS RADI
            , b.USUA_NOMB AS USUARIO_DIGITALIZADOR
            , h.HIST_OBSE AS OBSERVACIONES,
            to_char(r.radi_fech_radi, 'DD-MM-YYYY HH24:MI')  AS FECHA_RADICACION, 
            to_char(h.HIST_FECH, 'DD-MM-YYYY HH24:MI')  AS FECHA_DIGITALIZACION
            , mr.mrec_desc AS MEDIO_RECEPCION
            , t.sgd_tpr_descrip AS TIPO_DE_DOCUMENTO
            FROM RADICADO r, USUARIO b, HIST_EVENTOS h, sgd_tpr_tpdcumento t , MEDIO_RECEPCION mr
        WHERE 
            h.USUA_CODI=b.usua_CODI 
            AND b.depe_codi = h.depe_codi
            AND r.tdoc_codi = t.sgd_tpr_codigo
            $where
            AND h.RADI_NUME_RADI=r.RADI_NUME_RADI
            AND r.MREC_CODI=mr.MREC_CODI
            AND h.SGD_TTR_CODIGO IN(22,42)
            AND  r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') 
            $whereTipoRadicadoD 
                ";
                  

        //trae info de total
        //trae la informacion de los radicados

        //   die();
        $rs = $this->link->conn->query($iSql);
        $nomdebe = $this->dependecias();

        if (!$rs->EOF) {
            $i = 1;
            $arrayRAD = '';
            $coma = '';
            // echo "hd";
            while (!$rs->EOF) {

                $arrayRAD .= $coma . $rs->fields['RADI'];
                foreach ($rs->fields as $key => $value) {

                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                 
                }
                $datoss[$rs->fields['RADI']]['NUM'] = $i;
                //$datoss[$rs->fields['RADI']]['DPAN'] = $nomdebe[$rs->fields['DPA']];
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
          //  print_r( $datoss);
        }
/*
        //trae info de sgd_dir_Dreciones
        $selectB = "select dir.radi_nume_radi radi, dir.SGD_DIR_DIRECCION dir,dir.SGD_DIR_MAIL email,dir.SGD_DIR_NOMREMDES dig,
        dir.SGD_DIR_TELEFONO tel,dir.sgd_dir_mail, dir.sgd_dir_nombre||' '||dir.sgd_dir_apellido rem
         from  sgd_dir_drecciones dir
        where  dir.radi_nume_radi  in ($arrayRAD) ";
        $rs = $this->link->conn->query($selectB);

        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {

                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;

                    }
                }
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }

        }*/

        foreach ($datoss as $key => $value) {
            $datos[] = $value;
        }
        return $datos;
    }
    //detalles reporte 6
    public function dtrp6($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq, $tpRad)
    {
	    
        $whereTipoRadicado = $tpRad ? " and r.sgd_trad_codigo = $tpRad " : '';
        $whereDependencia = $depe != '99999' ? " and r.radi_depe_actu = $depe " : '';
	$redondeo="date_part('days', r.radi_fech_radi-".$this->link->conn->sysTimeStamp.")+floor(t.sgd_tpr_termino * 7/5)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between r.radi_fech_radi and ".$this->link->conn->sysTimeStamp.")";

        if ($tpAds == 1 && $tpbusq == 'T') {
            $depe = $this->depahijas($depe);
        }

	$queryEDetalle = "SELECT
	    DISTINCT cast(r.radi_nume_radi as varchar(20)) as RADICADO ,
	    r.RADI_FECH_RADI as FECHA_RADICADO,
	    r.RA_ASUN as ASUNTO ,
	    t.SGD_TPR_DESCRIP as TIPO_DE_DOCUMENTO,
	    da.DEPE_CODI as CODIGO_DEPENDENCIA_ACTUAL ,
	    da.DEPE_NOMB as DEPENDENCIA_ACTUAL ,
	    c.usua_nomb AS USUARIO_ACTUAL,
	    b.usua_nomb as Usuario ,
	    dir.SGD_DIR_NOMBRE || ' ' || dir.SGD_DIR_APELLIDO as REMITENTE ,
	    dir.SGD_DIR_NOMREMDES as DIGNATARIO,
	    df.DEPE_NOMB as DEPENDENCIA_INICIAL ,
	    r.RADI_NUME_FOLIO  AS NUMERO_FOLIOS,
	    r.RADI_NUME_ANEXO  AS NUMERO_ANEXO,
	    r.RADI_DESC_ANEX   AS DESCRIPCION_ANEXO,
	    $redondeo		as DIAS_RESTANTES
	    FROM
	    radicado r

		LEFT JOIN dependencia da ON r.radi_depe_actu=da.depe_codi
		LEFT JOIN dependencia df ON r.RADI_DEPE_RADI=df.DEPE_CODI
		LEFT JOIN hist_eventos htev ON r.radi_nume_radi = htev.radi_nume_radi AND htev.sgd_ttr_codigo = 2
		LEFT JOIN usuario b ON htev.usua_doc = b.usua_doc
		LEFT OUTER JOIN SGD_TPR_TPDCUMENTO t ON r.tdoc_codi=t.SGD_TPR_CODIGO
		LEFT OUTER JOIN SGD_DIR_DRECCIONES dir ON r.radi_nume_radi = dir.radi_nume_radi and dir.sgd_dir_tipo = '1'
		LEFT JOIN USUARIO c ON r.radi_usua_actu=c.usua_CODI AND r.radi_depe_actu=c.depe_codi

		WHERE
		r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') 
		$whereTipoRadicado
		$whereDependencia ";
        $rs = $this->link->conn->query($queryEDetalle);

        if (!$rs->EOF) {
            $i = 1;
            $arrayRAD = '';
            $coma = '';
            while (!$rs->EOF) {
                foreach ($rs->fields as $key => $value) {
                        $datoss[$rs->fields['RADICADO']][strtoupper($key)] = $value;
                }
                $datoss[$rs->fields['RADICADO']]['NUM'] = $i;
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
        }
        foreach ($datoss as $key => $value) {
            $datos[] = $value;
        }
        return $datos;
    }

    //detalles reporte 7
    public function dtrp7($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq, $tpRad)
    {
	    
	    if($tpbusq!='T')
		$whereUsu=" AND u3.usua_doc='$tpbusq'";
	    else
		$whereUsu="";
        $whereTipoRadicado = $tpRad ? " and r.sgd_trad_codigo = $tpRad " : '';
        $whereDependencia = $depe != '99999' ? " and r.radi_depe_actu = $depe " : '';
	$redondeo="date_part('days', r.radi_fech_radi-".$this->link->conn->sysTimeStamp.")+floor(t.sgd_tpr_termino * 7/5)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between r.radi_fech_radi and ".$this->link->conn->sysTimeStamp.")";

        if ($tpAds == 1 && $tpbusq == 'T') {
            $depe = $this->depahijas($depe);
        }

		$COD_RADICACION = 2;
		$COD_DIGITALIZACION = 42;
		$queryEDetalle = "SELECT DISTINCT ON (r.radi_nume_radi) r.radi_nume_radi as RADICADO
			,r.RADI_FECH_RADI as FECHA_RADICADO
			,t.SGD_TPR_DESCRIP as TIPO_DE_DOCUMENTO
			,r.RA_ASUN as ASUNTO 
			,r.RADI_DESC_ANEX 
			,r.RADI_NUME_HOJA 
			,r.RADI_NUME_FOLIO
			,r.RADI_PATH as HID_RADI_PATH
			,dir.SGD_DIR_NOMBRE || ' ' || dir.SGD_DIR_APELLIDO as REMITENTE
			,df.DEPE_NOMB as DEPE_NOMB
			,da.DEPE_NOMB as DEPE_NOMB_ACTUAL
			,da.DEPE_CODI as DEPE_CODI_ACTUAL
			,r.RADI_USU_ANTE
			,u2.usua_nomb AS USUA_NOMB_ACTUAL
			,r.radi_nume_anexo as NUM_ANEXOS
			,he1.hist_fech as FECHA_DIGITALIZACION
			,u1.usua_nomb as DIGITALIZADOR
			,u2.usua_nomb as USUARIO
			,u3.usua_nomb as RADICADOR
			,d3.DEPE_NOMB as DEPENDENCIA_RADICADOR
			,r.RADI_DEPE_RADI as COD_DEPE
			,mr.mrec_desc as MEDIO
			FROM dependencia df, dependencia da, RADICADO r
			LEFT OUTER JOIN SGD_TPR_TPDCUMENTO t ON r.tdoc_codi=t.SGD_TPR_CODIGO 
			LEFT OUTER JOIN SGD_DIR_DRECCIONES dir ON r.radi_nume_radi = dir.radi_nume_radi	and dir.sgd_dir_tipo = '1'
			LEFT JOIN medio_recepcion mr ON r.mrec_codi = mr.mrec_codi
			LEFT  JOIN hist_eventos he1 ON r.radi_nume_radi = he1.radi_nume_radi AND he1.sgd_ttr_codigo = ".$COD_DIGITALIZACION."
			LEFT JOIN hist_eventos he2 ON r.radi_nume_radi = he2.radi_nume_radi AND he2.sgd_ttr_codigo = ".$COD_RADICACION."
			LEFT JOIN USUARIO u1 ON u1.usua_codi = he1.usua_codi_dest and u1.depe_codi = he1.depe_codi_dest
			LEFT JOIN USUARIO u2 ON u2.usua_codi = he2.usua_codi_dest and u2.depe_codi = he2.depe_codi_dest
			LEFT JOIN USUARIO u3 ON u3.usua_codi = he2.usua_codi and u3.depe_codi = he2.depe_codi
			LEFT JOIN dependencia d3 ON d3.depe_codi = he2.depe_codi
			WHERE 
			r.radi_depe_actu=da.depe_codi AND
			r.RADI_DEPE_RADI=df.DEPE_CODI AND
			r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') 
			$whereTipoRadicado $whereDependencia $whereUsu
			AND r.radi_nume_radi::text LIKE '%2'";
        $rs = $this->link->conn->query($queryEDetalle);

        if (!$rs->EOF) {
            $i = 1;
            $arrayRAD = '';
            $coma = '';
            while (!$rs->EOF) {
                foreach ($rs->fields as $key => $value) {
                        $datoss[$rs->fields['RADICADO']][strtoupper($key)] = $value;
                }
                $datoss[$rs->fields['RADICADO']]['NUM'] = $i;
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
        }
        foreach ($datoss as $key => $value) {
            $datos[] = $value;
        }
        return $datos;
    }
    //detalles reporte  9
    public function dtrp9($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq)
    {

        $where=$btns = '';
        $ddcamp=$ddcamp2 = '';
        if ($tpdoc != 0) {
            $where .= " and r.TDOC_CODI=$tpdoc ";
        }

        $campos = " ,TO_CHAR(r.radi_fech_radi, 'MM') MES,'' DIFFECH ,r.mrec_codi MERC, to_char(r.radi_fech_radi, 'DD-MM-YYYY HH24:MI')  FECHASIG,'' FECHR,'' NUMREXT,'' NOBRENT,'' proy,'' envio";

        if ($depe != 99999) {

            if ($tpAds == 1) {
                $depe = $this->depahijas($depe);
            }

            if ($tpbusq == '2' || $tpbusq == 'T') {
                $where2 = " and r.radi_depe_actu!=999 and r.radi_depe_actu in ($depe) ";
                $hcamp2 =', r.radi_depe_actu dpclose';
            }

            if ($tpbusq == '1' || $tpbusq == 'T') {
                $tbwh = " hist_eventos h ";
                $whereA = " and r.radi_depe_actu=999 and h.depe_codi_dest=999 and h.depe_codi  in ($depe) and r.radi_nume_Radi=h.radi_nume_Radi and h.sgd_ttr_codigo  in (13,65) ";
                $hcamp = $ddcamp.',h.depe_codi dpclose';
              //  $hcamp2 = $ddcamp2;
                $whereJ = " left join dependencia dh on  dh.depe_codi = h.depe_codi  and dh.depe_codi is not null
                            left join usuario uh on uh.usua_codi = h.usua_codi ,";
            }
            $whereI = '';
            if ($subserie && $subserie != 0) {
                $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
            }

            if ($serie != 0) {
                $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
                  INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
            }

            if ($usu && $usu != 0) {
                $whereU = " and r.radi_usua_actu=$usu ";
                $whereUH = " and h.usua_codi=$usu ";
            }

            $iSql1 = "select distinct r.radi_nume_radi radi, to_char(r.radi_fech_radi, 'DD-MM-YYYY ') rfech,td.sgd_tpr_descrip tpnomb,
            trim(r.ra_asun) asunto, date_part('days', r.radi_fech_radi-CURRENT_TIMESTAMP)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between r.radi_fech_radi and CURRENT_TIMESTAMP) as FVCMTO,
            '' est,upper(du.depe_nomb) depeac,u.usua_nomb usuaa,'' DIR,'' tel,'' muni,'' depto,'' email,'' resp,'' estresp,r.radi_depe_actu ges ,r.radi_depe_actu dpa,'' rem $campos $hcamp
            from $tbwh $whereJ radicado r
            left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO
            left join dependencia du on  du.depe_codi = r.radi_depe_actu  and du.depe_codi is not null
            left join usuario u on u.usua_codi = 15  and u.depe_codi=r.radi_depe_actu
            $whereI
            where r.sgd_trad_codigo = 2   and r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') and (r.sgd_eanu_codigo is null or  r.sgd_eanu_codigo not in (1,2)) $whereA  $where $whereUH";
            $iSql2 = "select distinct r.radi_nume_radi radi, to_char(r.radi_fech_radi, 'DD-MM-YYYY HH24:MI') rfech,td.sgd_tpr_descrip tpnomb,
            trim(r.ra_asun)  asunto, date_part('days', r.radi_fech_radi-CURRENT_TIMESTAMP)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between r.radi_fech_radi and CURRENT_TIMESTAMP) as FVCMTO,
            '' est,upper(du.depe_nomb) depeac,u.usua_nomb usuaa,'' DIR,'' tel,'' muni,'' depto,'' email,'' resp,'' estresp,r.radi_depe_actu ges, r.radi_depe_actu dpa,'' rem  $campos $hcamp2
            from radicado r
            left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO
            left join usuario u on u.usua_codi = r.radi_usua_actu and u.depe_codi= r.radi_depe_actu
            left join dependencia du on  du.depe_codi = r.radi_depe_actu  and du.depe_codi is not null
            $whereI
            where r.sgd_trad_codigo = 2  $where2 and r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') and (r.sgd_eanu_codigo is null or  r.sgd_eanu_codigo not in (1,2))  $where  $whereU";
            if ($tpbusq == '1') {
                $iSql = $iSql1 . 'order by 2';
            }

            if ($tpbusq == '2') {
                $iSql = $iSql2 . 'order by 2';
            }

            if ($tpbusq == 'T') {
                $iSql = $iSql2 . ' UNION ' . $iSql1 . 'order by 2';
            }

           //  echo $iSql;
        } else {

            if ($tpbusq == 'T') {
                $where2 = '';
            }
            if ($tpbusq == '1') {
                $where2 = " and r.radi_depe_actu=999 ";
            }

            if ($tpbusq == '2') {
                $where2 = " and r.radi_depe_actu!=999 ";

            }

            $whereI = '';
            if ($subserie && $subserie != 0) {
                $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
            }

            if ($serie != 0) {
                $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
                  INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
            }

            $iSql = "select distinct r.radi_nume_radi radi, to_char(r.radi_fech_radi, 'DD-MM-YYYY') rfech,td.sgd_tpr_descrip tpnomb,
            trim(r.ra_asun) asunto, date_part('days', r.radi_fech_radi-CURRENT_TIMESTAMP)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between r.radi_fech_radi and CURRENT_TIMESTAMP) as FVCMTO,
            '' est,upper(du.depe_nomb) depeac,u.usua_nomb usuaa,'' DIR,'' tel,'' muni,'' depto,'' email,'' resp,'' estresp,r.radi_depe_actu ges ,r.radi_depe_actu dpa,'' rem,r.radi_depe_actu dpclose $campos $ddcamp2
            from radicado r
            left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO
            left join usuario u on u.usua_codi = r.radi_usua_actu and u.depe_codi= r.radi_depe_actu
            left join dependencia du on  du.depe_codi = r.radi_depe_actu  and du.depe_codi is not null
            $whereI
            where r.sgd_trad_codigo = 2  $where2 $where and r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') and (r.sgd_eanu_codigo is null or  r.sgd_eanu_codigo not in (1,2)) order by 2";
        }

        //trae info de total
        //trae la informacion de los radicados
        $nomdebe = $this->depeNombre;
        $arrayRADF='';
        $rs = $this->link->conn->query($iSql);
        if (!$rs->EOF) {
            $i = 0;
            $arrayRAD = '';
            $coma = '';
            // echo "hd";
            $arrayRADa='';
            while (!$rs->EOF) {

                $arrayRAD .= $coma . $rs->fields['RADI'];
                if ($rs->fields['DPCLOSE'] == 999) {
                    $arrayRADFa[$rs->fields['RADI']] = $rs->fields['RADI'];

                } 
                foreach ($rs->fields as $key => $value) {
                    if ($key == 'MES') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $this->mesesN[$value];
                    } elseif ($key == 'MERC') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $this->medioRecp[$value];
                    } elseif ($key == 'DPCLOSE') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = trim($value);
                        $datoss[$rs->fields['RADI']]['DPCLOSEN'] = $this->depeNombre[$value];
                    } else {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = trim($value); 
                       // $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                    }

                }

                $datoss[$rs->fields['RADI']]['DPAN'] = $nomdebe[$rs->fields['DPA']];
                if ($rs->fields['DPA'] == '999') {
                    $datoss[$rs->fields['RADI']]['EST'] = 'Finalizado';
                } else {
                    $datoss[$rs->fields['RADI']]['EST'] = 'En Trámite';
                }

                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }


        //buscar historico fecha de asignacion quien da respuesta
        // trae datos de finalizados
        if ($arrayRADF) {
            $selectA = "select  h.radi_nume_radi radi ,to_char(h.hist_fech, 'DD-MM-YYYY HH24:MI') FTX, hist_obse comen,uh.usua_nomb usuafin,dh.depe_nomb DEPEFIN
        from  hist_eventos h
        left join dependencia dh on  dh.depe_codi = h.depe_codi  and dh.depe_codi is not null
        left join usuario uh on uh.usua_doc = h.usua_doc
        where  h.radi_nume_radi in ($arrayRADF) and h.sgd_ttr_codigo in (13,65)  "; //left join usuario uh on uh.usua_codi = h.usua_codi and h.depe_codi=uh.depe_codi

            $rs = $this->link->conn->query($selectA);

            if (!$rs->EOF) {
                $i = 0;
                while (!$rs->EOF) {
                    foreach ($rs->fields as $key => $value) {
                        if ($key != 'RADI') {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                        }
                    }
                    $i++;
                    $coma = ',';
                    $rs->MoveNext();
                }
                //  $resp['titulo'] = $campot;
            }

        }
        //trae la info de los anexos --     left join sgd_dir_drecciones dir on dir.radi_nume_radi=a.radi_nume_salida
        $selectA = "select  a.anex_radi_nume radi ,d.depe_nomb depeaa,a.radi_nume_salida as resp,anex_creador proy,anex_estado estresp,
        to_char(anex_fech_envio, 'DD-MM-YYYY ') envio ,dir.SGD_DIR_NOMREMDES NOBRENT,dir.sgd_ciu_codigo cui2, dir.sgd_oem_codigo oem2
             from  anexos a
             left join sgd_dir_drecciones dir on dir.radi_nume_radi=a.radi_nume_salida
             ,dependencia d
             where  a.anex_radi_nume in ($arrayRAD) and d.depe_codi = a.anex_depe_creador and a.anex_salida = 1 and a.radi_nume_salida is not null ";
        $rs = $this->link->conn->query($selectA);
        $estados = array(1 => 'Anexado', 2 => 'Radicado', 3 => 'Firmado', 4 => 'Enviado');
        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {

                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        if ($key == 'ENVIO') {
                            $date2 = new DateTime($value);
                            $date1 = new DateTime($datoss[$rs->fields['RADI']]['RFECH']);
                            $diff = $date1->diff($date2);
                            // will output 2 days
                            //echo $diff->days . ' days ';
                            $datoss[$rs->fields['RADI']]['DIFFECH'] = $diff->days . ' dias';

                            //   $datoss[$rs->fields['RADI']]['DIFFECH'] = $value-$datoss[$rs->fields['RADI']]['FECHASIG'];
                        }
                        if ($key == 'ESTRESP') {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $estados[$value];
                        } else {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                            //trim(preg_replace("[\n|\r|\n\r|\t|\0|\x0B]", "",$value))
                        }

                    }
                }
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }
        //trae info de sgd_dir_Dreciones
        $selectB = "select dir.radi_nume_radi radi,dir.sgd_ciu_codigo cui, dir.sgd_oem_codigo oem, dir.SGD_DIR_DIRECCION dir,dir.SGD_DIR_MAIL email,dir.sgd_dir_nombre||' '||dir.sgd_dir_apellido rem,dir.SGD_DIR_NOMREMDES dig,
         trim(dir.SGD_DIR_TELEFONO) tel,dir.sgd_dir_mail, m2.muni_nomb muni,d2.dpto_nomb depto
             from  sgd_dir_drecciones dir
            left join municipio m2 on dir.dpto_codi = m2.dpto_codi and dir.muni_codi = m2.muni_codi
            left join departamento d2 on d2.dpto_codi = m2.dpto_codi
            where  dir.radi_nume_radi  in ($arrayRAD) and dir.sgd_dir_nombre is not null ";
        $rs = $this->link->conn->query($selectB);

        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {

                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        if ($key == 'CUI' && $datoss[$rs->fields['RADI']]['RESP'] && $value != 0) {
                            if (trim($value) != trim($datoss[$rs->fields['RADI']]['CUI2'])) {
                                //$datoss[$rs->fields['RADI']]['NOBRENT'] = '¡'.trim($value).'¡'.trim($datoss[$rs->fields['RADI']]['CUI2']).'¡';
                                $datoss[$rs->fields['RADI']]['NUMREXT'] = $datoss[$rs->fields['RADI']]['RESP'];
                                $datoss[$rs->fields['RADI']]['RESP'] = '';
                                //  $datoss[$rs->fields['RADI']]['NOBRENT'] = '';
                            } else {
                                $datoss[$rs->fields['RADI']]['NOBRENT'] = '';
                            }

                            //$datoss[$rs->fields['RADI']]['NOBRENT'] = '¡'.trim($value).'¡'.trim($datoss[$rs->fields['RADI']]['OEM2']).'¡';
                        } elseif ($key == 'OEM' && $datoss[$rs->fields['RADI']]['RESP'] && $value != 0) {
                            if (trim($value) == trim($datoss[$rs->fields['RADI']]['OEM2'])) {
                                $datoss[$rs->fields['RADI']]['NUMREXT'] = $datoss[$rs->fields['RADI']]['RESP'];
                                $datoss[$rs->fields['RADI']]['RESP'] = '';

                            }
                            //    $datoss[$rs->fields['RADI']]['NOBRENT'] = '¡'.trim($value).'¡'.trim($datoss[$rs->fields['RADI']]['OEM2']).'¡';
                            //else
                            // $datoss[$rs->fields['RADI']]['NOBRENT'] = '';
                        } elseif ($key == 'REM' && !$value) {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $rs->fields['DIG'];

                        } else {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] =  preg_replace('([^A-Za-z0-9 ])', '', $value); 
                        }

                    }
                }
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }

        }
        //buscar historicofecha de asignacion ultima
    $arrayRADF = implode(',', $arrayRADFa);
    //consulta de ultimo historico asignacion
    if ($arrayRADF) {
        $selectA = " select radi_nume_radi radi, hist_fech fechn,depe_codi dpclose from hist_eventos where 
        radi_nume_radi in  ($arrayRADF) and sgd_ttr_codigo in (13,65) order by 1,2 asc";

        $rs = $this->link->conn->query($selectA);

        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {
                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        
           
                         
                        $datoss[$rs->fields['RADI']][strtoupper($key).'N'] = $this->depeNombre[$value];
                        $datoss[$rs->fields['RADI']]['DPCLOSE'] = trim($value);
                    }
                }
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }

    }
        //busca los asociados
        if ($btns == '3') {
            $selectB = "select distinct  r.radi_nume_deri radi, r.radi_nume_radi ASOCIADO
                from  radicado r where  r.radi_nume_deri in ($arrayRAD) ";
            $rs = $this->link->conn->query($selectB);

            if (!$rs->EOF) {
                $i = 0;
                while (!$rs->EOF) {

                    foreach ($rs->fields as $key => $value) {
                        if ($key != 'RADI') {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                        }
                    }
                    $i++;
                    $coma = ',';
                    $rs->MoveNext();
                }
                //  $resp['titulo'] = $campot;
            }
            //   $resp['SQL'] = $selectB;
        }
        foreach ($datoss as $key => $value) {
            $datos[] = $value;
        }
        return $datos;
    }

    public function dtrp10($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq)
    {
        $where = '';
        $ddcamp = '';
        if ($tpdoc != 0) {
            $where .= " and r.TDOC_CODI=$tpdoc ";
        }
        $whereU = " ";
        $whereUH = " ";
        $campos = " ,'' ENVIO,'' FIRMA, '' ASOCIADO , '' PROYi ,'' ESTU,'' ftx,r.radi_depe_actu depeAcod,'' FECHRESP,r.mrec_codi MERC,'' comen,'' usuafin,'' depefin";
        $ddcamp = ",to_char(h.hist_fech, 'DD-MM-YYYY HH24:MI') fechf, hist_obse comen,uh.usua_nomb usuafin,dh.depe_nomb DEPEFIN";
        $ddcamp2 = ",'' fechf, '' comen,'' usuafin,'' DEPEFIN ";

        if ($depe != 99999) {

            if ($tpAds == 1) {
                $depe = $this->depahijas($depe);
            }

            /*if ($depe != 999 ) {
            $where = "and r.radi_depe_actu in ($depe) ";
            }*/
            if ($tpbusq == '2' || $tpbusq == 'T') {
                $where2 = " and r.radi_depe_actu!=999 and r.radi_depe_actu in ($depe) ";
            }

            if ($tpbusq == '1' || $tpbusq == 'T') {
                $tbwh = " hist_eventos h ";
                $whereA = " and r.radi_depe_actu=999 and h.depe_codi_dest=999 and h.depe_codi  in ($depe) and r.radi_nume_Radi=h.radi_nume_Radi and h.sgd_ttr_codigo  in (13,65) ";
                $hcamp = $ddcamp;
                $hcamp2 = $ddcamp2;
                $whereJ = " left join dependencia dh on  dh.depe_codi = h.depe_codi  and dh.depe_codi is not null
                         left join usuario uh on uh.usua_codi = h.usua_codi and uh.depe_codi=h.depe_codi ,";
                //$whereA = " and r.radi_depe_actu=999";
            }
            $whereI = '';
            if ($subserie && $subserie != 0) {
                $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
            }

            if ($serie != 0) {
                $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
               INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
            }

            if ($usu && $usu != 0) {
                $whereU = " and r.radi_usua_actu=$usu ";
                $whereUH = " and h.usua_codi=$usu ";
            }

            $iSql1 = "select distinct r.radi_nume_radi radi, to_char(r.radi_fech_radi, 'DD-MM-YYYY HH24:MI') rfech,td.sgd_tpr_descrip tpnomb,
         r.ra_asun asunto, date_part('days', r.radi_fech_radi-CURRENT_TIMESTAMP)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between r.radi_fech_radi and CURRENT_TIMESTAMP) as FVCMTO,
         '' est,upper(du.depe_nomb) depeac,u.usua_nomb usuaa,'' DIR,'' tel,'' muni,'' depto,'' email,'' resp,'' estresp,r.radi_depe_actu ges ,r.radi_depe_actu dpa,'' rem $campos $hcamp
         from $tbwh $whereJ radicado r
         left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO
         left join dependencia du on  du.depe_codi = r.radi_depe_actu  and du.depe_codi is not null
         left join usuario u on u.usua_codi = 15  and u.depe_codi=r.radi_depe_actu
         $whereI
         where r.sgd_trad_codigo = 2   and r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') and (r.sgd_eanu_codigo is null or  r.sgd_eanu_codigo not in (1,2)) $whereA  $where $whereUH";
            $iSql2 = "select distinct r.radi_nume_radi radi, to_char(r.radi_fech_radi, 'DD-MM-YYYY HH24:MI') rfech,td.sgd_tpr_descrip tpnomb,
         r.ra_asun asunto, date_part('days', r.radi_fech_radi-CURRENT_TIMESTAMP)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between r.radi_fech_radi and CURRENT_TIMESTAMP) as FVCMTO,
         '' est,upper(du.depe_nomb) depeac,u.usua_nomb usuaa,'' DIR,'' tel,'' muni,'' depto,'' email,'' resp,'' estresp,r.radi_depe_actu ges, r.radi_depe_actu dpa,'' rem  $campos $hcamp2
         from radicado r
         left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO
         left join usuario u on u.usua_codi = r.radi_usua_actu and u.depe_codi= r.radi_depe_actu
         left join dependencia du on  du.depe_codi = r.radi_depe_actu  and du.depe_codi is not null
         $whereI
         where r.sgd_trad_codigo = 2  $where2 and r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') and (r.sgd_eanu_codigo is null or  r.sgd_eanu_codigo not in (1,2))  $where  $whereU";
            if ($tpbusq == '1') {
                $iSql = $iSql1 . 'order by 2';
            }

            if ($tpbusq == '2') {
                $iSql = $iSql2 . 'order by 2';
            }

            if ($tpbusq == 'T') {
                $iSql = $iSql2 . ' UNION ' . $iSql1 . 'order by 1,2';
            }

            //  echo $iSql;
        } else {
            $where2 = '';
            if ($tpbusq == '1') {
                $where2 = " and r.radi_depe_actu=999 ";
            }
            if ($tpbusq == '2') {
                $where2 = " and r.radi_depe_actu!=999 ";
            }
            $whereI = '';
            if ($subserie && $subserie != 0) {
                $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
            }

            if ($serie != 0) {
                $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
               INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
            }

            $iSql = "select distinct r.radi_nume_radi radi, to_char(r.radi_fech_radi, 'DD-MM-YYYY HH24:MI') rfech,td.sgd_tpr_descrip tpnomb,
         r.ra_asun asunto, date_part('days', r.radi_fech_radi-CURRENT_TIMESTAMP)+(select count(1) from sgd_noh_nohabiles where NOH_FECHA between r.radi_fech_radi and CURRENT_TIMESTAMP) as FVCMTO,
         '' est,upper(du.depe_nomb) depeac,u.usua_nomb usuaa,'' DIR,'' tel,'' muni,'' depto,'' email,'' resp,'' estresp,r.radi_depe_actu ges ,r.radi_depe_actu dpa,'' rem $campos $ddcamp2
         from radicado r
         left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO
         left join usuario u on u.usua_codi = r.radi_usua_actu and u.depe_codi= r.radi_depe_actu
         left join dependencia du on  du.depe_codi = r.radi_depe_actu  and du.depe_codi is not null
         $whereI
         where r.sgd_trad_codigo = 2  $where2 $where and r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') and (r.sgd_eanu_codigo is null or  r.sgd_eanu_codigo not in (1,2)) order by 2 ";
        }

        //trae info de total
        //trae la informacion de los radicados

        $rs = $this->link->conn->query($iSql);
     //echo   $resp['SQL2'] = $iSql;
  //   die();
        if (!$rs->EOF) {
            $i = 0;
            $arrayRADa=array();
            $arrayRAD = '';
            $arrayRAD1 = '';
            $arrayRADF = '';
            $arrayRADNF = '';

            // echo "hd";
            while (!$rs->EOF) {

                $arrayRADa[$rs->fields['RADI']] = $rs->fields['RADI'];
                if ($rs->fields['DEPEACOD'] == 999) {
                    $arrayRADFa[$rs->fields['RADI']] = $rs->fields['RADI'];

                } /*else {
                $arrayRADNFa[$rs->fields['RADI']]= $rs->fields['RADI'];
                }*/

                foreach ($rs->fields as $key => $value) {

                    if ($key == 'MES') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $this->mesesN[$value];
                    } elseif ($key == 'MERC') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $this->medioRecp[$value];
                    }elseif ($key == 'USUAA') {
                        $datom=$value;
                        if(!$value && $rs->fields['DPA']==999 )
                           $datom='ARCHIVO NRR';
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $datom;
                    } elseif($key == 'ASUNTO') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] =htmlentities( str_replace(array('<','>','','','',"\u23FD"),'', $value));
                    } else {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                      
                    }

                }

                if ($rs->fields['DPA'] == '999') {
                    $datoss[$rs->fields['RADI']]['EST'] = 'Finalizado';
                } else {
                    $datoss[$rs->fields['RADI']]['EST'] = 'En Trámite';
                }

                $i++;
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }
        //  $datos['SQL'] =$arrayRADa;
        //$a=explode(',',$arrayRAD);
        $arrayRAD = implode(',', $arrayRADa);
//$arrayRADNF=implode(',',$arrayRADNFa);
        $arrayRADF = implode(',', $arrayRADFa);
        // trae datos de finalizados
        if ($arrayRADF) {
            $selectA = "select  h.radi_nume_radi radi ,to_char(h.hist_fech, 'DD-MM-YYYY HH24:MI') FTX, hist_obse comen,uh.usua_nomb usuafin,dh.depe_nomb DEPEFIN
          from  hist_eventos h
          left join dependencia dh on  dh.depe_codi = h.depe_codi  and dh.depe_codi is not null
          left join usuario uh on uh.usua_doc = h.usua_doc
         where  h.radi_nume_radi in ($arrayRADF) and h.sgd_ttr_codigo in (13,65)  "; //left join usuario uh on uh.usua_codi = h.usua_codi and h.depe_codi=uh.depe_codi

            $rs = $this->link->conn->query($selectA);

            if (!$rs->EOF) {
                $i = 0;
                while (!$rs->EOF) {
                    foreach ($rs->fields as $key => $value) {
                        if ($key != 'RADI') {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                        }
                    }
                    $i++;
                    $coma = ',';
                    $rs->MoveNext();
                }
                //  $resp['titulo'] = $campot;
            }

        }
//firmante
        $arrayRADF = implode(',', $arrayRADFa);
        if ($arrayRADF) {
            $selectAs = "select  h.radi_nume_radi radi ,uh.usua_nomb FIRMA
          from  hist_eventos h
          left join dependencia dh on  dh.depe_codi = h.depe_codi  and dh.depe_codi is not null
          left join usuario uh on uh.usua_codi = h.usua_codi and h.depe_codi=uh.depe_codi
         where  h.radi_nume_radi in ($arrayRADF) and h.sgd_ttr_codigo in (40)  ";

            $rs = $this->link->conn->query($selectAs);

            if (!$rs->EOF) {
                $i = 0;
                while (!$rs->EOF) {
                    foreach ($rs->fields as $key => $value) {
                        if ($key != 'RADI') {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                        }
                    }
                    $i++;
                    $coma = ',';
                    $rs->MoveNext();
                }
                //  $resp['titulo'] = $campot;
            }

        }

        //consulta de ultimo historico
        if ($arrayRAD) {
            $selectA = " select  t.sgd_ttr_descrip ESTU,h.radi_nume_radi radi
            from  hist_eventos h,
            (select  radi_nume_radi, max(hist_fech) fechn from  hist_eventos  where  radi_nume_radi in ($arrayRAD) group by radi_nume_radi ) as h2
            ,sgd_ttr_transaccion t
           where  h.radi_nume_radi=h2.radi_nume_radi and  h2.fechn=h.hist_fech and h.sgd_ttr_codigo=t.sgd_ttr_codigo ";

            $rs = $this->link->conn->query($selectA);
            if (!$rs->EOF) {
                $i = 0;
                while (!$rs->EOF) {
                    foreach ($rs->fields as $key => $value) {
                        if ($key != 'RADI') {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] =str_replace(array('<','>'),'', $value);
                        }
                    }
                    $i++;
                    $coma = ',';
                    $rs->MoveNext();
                }
                //  $resp['titulo'] = $campot;
            }

        }

        //trae la info de los anexos
        $selectA = "select  a.anex_radi_nume radi ,d.depe_nomb depeaa,a.radi_nume_salida as resp,anex_creador proyi,anex_estado estresp,to_char( case when (a.anex_fech_envio is not null)  THEN 
        a.anex_fech_envio ELSE e.sgd_Renv_FECH END, 'DD-MM-YYYY HH24:MI') envio  ,r.radi_fech_Radi FECHRESP
         from  anexos a left join sgd_renv_regenvio e on e.radi_nume_sal=a.radi_nume_salida and sgd_renv_estado=1
         ,dependencia d, radicado r
          where  a.anex_radi_nume in ($arrayRAD) and d.depe_codi = a.anex_depe_creador and a.anex_salida = 1 and a.radi_nume_salida is not null and a.radi_nume_salida=r.radi_nume_radi order by  estresp";
        $rs = $this->link->conn->query($selectA);
        $estados = array(1 => 'Anexado', 2 => 'Radicado', 3 => 'Firmado', 4 => 'Enviado');
        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {

                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        if ($key == 'ESTRESP') {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $estados[$value];
                        } else if ($key == 'ENVIO') {
                            $valuex='';
                            if($rs->fields['ESTRESP']==4 ) $valuex=$value;
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $valuex;
                        } else {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                        }

                    }
                }
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }
        //trae info de sgd_dir_Dreciones
        $selectB = "select dir.radi_nume_radi radi, dir.SGD_DIR_DIRECCION dir,dir.SGD_DIR_MAIL email, dir.sgd_dir_nombre||' '||dir.sgd_dir_apellido rem,dir.SGD_DIR_NOMREMDES dig,dir.SGD_DIR_TELEFONO tel,dir.sgd_dir_mail, m2.muni_nomb muni,d2.dpto_nomb depto
          from  sgd_dir_drecciones dir
         left join municipio m2 on dir.dpto_codi = m2.dpto_codi and dir.muni_codi = m2.muni_codi
         left join departamento d2 on d2.dpto_codi = m2.dpto_codi
         where  dir.radi_nume_radi  in ($arrayRAD) ";
        $rs = $this->link->conn->query($selectB);

        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {
                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        // $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                        if ($key == 'REM' && (!$value || $value == ' ' || $value == '')) {
                            if (!$datoss[$rs->fields['RADI']]['REM']) {
                                $datoss[$rs->fields['RADI']][strtoupper($key)] = $rs->fields['DIG'];
                            }

                        } else {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                        }

                    }
                }
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }
        //busca los asociados

        $selectB = "select distinct  r.radi_nume_deri radi, r.radi_nume_radi ASOCIADO
             from  radicado r where  r.radi_nume_deri in ($arrayRAD) ";
        $rs = $this->link->conn->query($selectB);

        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {

                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                    }
                }
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }
        $resp['SQL'] = $selectAs;

        foreach ($datoss as $key => $value) {

            $datos[] = $value;
        }
       // $datos['SQL'] =$resp;
        return $datos;
    }

    public function ConsultaRadi($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpRAD = 1)
    {
        if ($tpAds == 1) {
            $depe = $this->depahijas($depe);
        }
        $whereADD = $where2 = '';
        $where = '';
        $where2TB = '';
        $resp['depe'] = $depe;
        if ($depe != 99999) {
            $where = "and r.radi_depe_actu in ($depe) ";
            //$where2J = "left join hist_eventos h on h.depe_codi_dest=999 and h.depe_codi  in ($depe) and r.radi_nume_Radi=h.radi_nume_Radi  and h.sgd_ttr_codigo=13";
            $where2Tb = " ,hist_eventos h ";
            $where2 = " and h.depe_codi_dest=r.radi_depe_actu and h.depe_codi  in ($depe) and r.radi_nume_Radi=h.radi_nume_Radi  and h.sgd_ttr_codigo in (13,65)";
        }
        if ($tpdoc != 0) {
            $whereADD .= " and r.TDOC_CODI=$tpdoc ";
        }

        $whereU = $whereI = $ddsubserie = '';
        if ($subserie && $subserie != 0) {
            $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
        }

        if ($serie != 0) {
            $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
              INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
        }
        if ($usu && $usu != 0) {
            $whereU = " and r.radi_usua_actu=$usu ";
            $whereUH = " and h.usua_codi=$usu ";
        }

        $iSql = "select count(distinct r.radi_nume_radi) num  from  radicado r     $whereI  $where2Tb
        where r.sgd_trad_codigo = $tpRAD and r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') 
        and r.radi_depe_actu = 999  and (r.sgd_eanu_codigo is null or  r.sgd_eanu_codigo not in (1,2))  and  substring(cast(r.radi_nume_radi as char(18)) from 1 for 1)='2'
         $where2  $whereADD  $whereUH ";
        $iSql2 = "select count(distinct r.radi_nume_radi) num  from  radicado r   $whereI
        where r.sgd_trad_codigo = $tpRAD and r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59')
         and r.radi_depe_actu <> 999   and (r.sgd_eanu_codigo is null or  r.sgd_eanu_codigo not in (1,2))  and  substring(cast(r.radi_nume_radi as char(18)) from 1 for 1)='2'
         $where  $whereADD  $whereU";
        /*  $iSql = "select case when  r.radi_depe_actu='999' THEN 'tramitado' ELSE 'entramite' end nomb ,count(1)  num
        from radicado r  $whereI
        where  r.sgd_trad_codigo = $tpRAD- and r.radi_fech_radi between  ('$fini') and ('$ffin')  $where $whereADD $whereU
        group by 1 ";*/
        $rs = $this->link->conn->query($iSql);
        $rs2 = $this->link->conn->query($iSql2);
       /* $datos['sql'] = $iSql;
        $datos['sql2'] = $iSql2;*/
        $datos['tramitado'] = 0;
        $datos['entramite'] = 0;
        if (!$rs->EOF) {
            $datos['tramitado'] = $rs->fields['NUM'] ? $rs->fields['NUM'] : 0;
        }
        if (!$rs2->EOF) {
            $datos['entramite'] = $rs2->fields['NUM'] ? $rs2->fields['NUM'] : 0;
        }

        return $datos;
    }
    //dtrpCons
    public function dtrpCons($depe, $tpAds, $tpdoc, $serie, $subserie, $usu, $fini, $ffin, $tpbusq, $tpRAD = 1)
    {
        //    $this->link->conn->debug =true;
        $where = '';
        $ddcamp =$whereADD = '';
        if ($tpdoc != 0) {
            $where .= " and r.TDOC_CODI=$tpdoc ";
        }

        $campos = " ,'' ENVIO,''  RESP ,'' MERC,'' ESTU,'' ftx,r.radi_depe_actu depeAcod,'' FECHRESP,'' NENV,'' respsal";
        $ddcamp = ",to_char(h.hist_fech, 'DD-MM-YYYY HH24:MI') fechf, hist_obse comen,uh.usua_nomb usuafin,dh.depe_nomb DEPEFIN";
        $ddcamp2 = ",'' fechf, '' comen,'' usuafin,'' DEPEFIN ";

        if ($depe != 99999) {

            if ($tpAds == 1) {
                $depe = $this->depahijas($depe);
            }

            /*if ($depe != 999 ) {
            $where = "and r.radi_depe_actu in ($depe) ";

            if ($depe != 99999) {
            $where = "and r.radi_depe_actu in ($depe) ";
            //$where2J = "left join hist_eventos h on h.depe_codi_dest=999 and h.depe_codi  in ($depe) and r.radi_nume_Radi=h.radi_nume_Radi  and h.sgd_ttr_codigo=13";
            $where2Tb = " ,hist_eventos h ";
            $where2 = " and h.depe_codi_dest=r.radi_depe_actu and h.depe_codi  in ($depe) and r.radi_nume_Radi=h.radi_nume_Radi  and h.sgd_ttr_codigo=13";
            }
            }*/
            if ($tpbusq == '2' || $tpbusq == 'T') {
                //$where2 = " and r.radi_depe_actu!=999 and r.radi_depe_actu in ($depe) ";
                $where = "and r.radi_depe_actu in ($depe) ";
                $where2Tb = " hist_eventos h ,";
                $where2 = " and h.depe_codi_dest=r.radi_depe_actu and h.depe_codi  in ($depe) and r.radi_nume_Radi=h.radi_nume_Radi  and h.sgd_ttr_codigo in (13,65)";
            }

            if ($tpbusq == '1' || $tpbusq == 'T') {
                $tbwh = " hist_eventos h ,";
                $whereA = " and r.radi_depe_actu=999 and h.depe_codi_dest=999 and h.depe_codi  in ($depe) and r.radi_nume_Radi=h.radi_nume_Radi and h.sgd_ttr_codigo  in (13,65)";
                $hcamp = $ddcamp;
                $hcamp2 = $ddcamp2;
                $whereJ = " left join dependencia dh on  dh.depe_codi = h.depe_codi  and dh.depe_codi is not null
                         left join usuario uh on uh.usua_codi = h.usua_codi ,";
                //$whereA = " and r.radi_depe_actu=999";
            }
            $whereI = '';
            if ($subserie && $subserie != 0) {
                $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
            }

            if ($serie != 0) {
                $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
               INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
            }

            if ($usu && $usu != 0) {
                $whereU = " and r.radi_usua_actu=$usu ";
                $whereUH = " and h.usua_codi=$usu ";
            }
            $iSql1 = "select distinct r.radi_nume_radi radi, to_char(r.radi_fech_radi, 'DD-MM-YYYY HH24:MI') rfech,td.sgd_tpr_descrip tpnomb,
            r.ra_asun asunto, '' est,upper(du.depe_nomb) depeac,u.usua_nomb usuaa ,'' rem,r.radi_nume_deri ASOCIADO,ur.usua_nomb proyi $campos $ddcamp2
            from   $tbwh  radicado r
            left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO
            left join usuario u on u.usua_codi = r.radi_usua_actu and u.depe_codi= r.radi_depe_actu
            left join dependencia du on  du.depe_codi = r.radi_depe_actu  and du.depe_codi is not null
            left join usuario ur on ur.usua_codi = r.radi_usua_radi and ur.depe_codi= r.radi_depe_radi and  substring(cast(r.radi_nume_radi as char(18)) from 1 for 1)='2'

              $whereI
            where r.sgd_trad_codigo = $tpRAD  and  substring(cast(r.radi_nume_radi as char(18)) from 1 for 1)='2' and r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') and r.radi_depe_actu = 999
             and r.radi_depe_radi <> 900  and (r.sgd_eanu_codigo is null or  r.sgd_eanu_codigo not in (1,2)) $where2 $whereA  $whereADD  $whereUH ";
            $iSql2 = "select distinct r.radi_nume_radi radi, to_char(r.radi_fech_radi, 'DD-MM-YYYY HH24:MI') rfech,td.sgd_tpr_descrip tpnomb,
            r.ra_asun asunto,'' est,upper(du.depe_nomb) depeac,u.usua_nomb usuaa,'' rem,r.radi_nume_deri ASOCIADO,ur.usua_nomb proyi $campos $ddcamp2
             from  radicado r
             left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO
             left join usuario u on u.usua_codi = r.radi_usua_actu and u.depe_codi= r.radi_depe_actu
             left join dependencia du on  du.depe_codi = r.radi_depe_actu  and du.depe_codi is not null
             left join usuario ur on ur.usua_codi = r.radi_usua_radi and ur.depe_codi= r.radi_depe_radi
              $whereI
            where r.sgd_trad_codigo = $tpRAD  and  substring(cast(r.radi_nume_radi as char(18)) from 1 for 1)='2' and r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') and r.radi_depe_actu <> 999
              and r.radi_depe_radi <> 900 and (r.sgd_eanu_codigo is null or  r.sgd_eanu_codigo not in (1,2))   $where  $whereADD  $whereU";

            if ($tpbusq == '1') {
                $iSql = $iSql1 . 'order by 2';
            }

            if ($tpbusq == '2') {
                $iSql = $iSql2 . 'order by 2';
            }

            if ($tpbusq == 'T') {
                $iSql = $iSql2 . ' UNION ' . $iSql1 . 'order by 1,2';
            }

            // echo $iSql;
        } else {
            $where2 = '';
            if ($tpbusq == '1') {
                $where2 = " and r.radi_depe_actu=999 ";
            }
            if ($tpbusq == '2') {
                $where2 = " and r.radi_depe_actu!=999 ";
            }
            $whereI = '';
            if ($subserie && $subserie != 0) {
                $ddsubserie = " and mrd.sgd_sbrd_id=$subserie ";
            }

            if ($serie != 0) {
                $whereI .= "INNER JOIN  sgd_rdf_retdocf rdf on rdf.radi_nume_radi=r.radi_nume_radi
               INNER JOIN sgd_mrd_matrird mrd on mrd.sgd_mrd_codigo=rdf.sgd_mrd_codigo and  mrd.sgd_srd_id=$serie $ddsubserie";
            }

            $iSql = "select distinct r.radi_nume_radi radi, to_char(r.radi_fech_radi, 'DD-MM-YYYY HH24:MI') rfech,td.sgd_tpr_descrip tpnomb,
            r.ra_asun asunto, '' est,upper(du.depe_nomb) depeac,u.usua_nomb usuaa ,'' rem,r.radi_nume_deri ASOCIADO,ur.usua_nomb proyi $campos $ddcamp2
         from radicado r
         left join sgd_tpr_tpdcumento td on r.TDOC_CODI=td.SGD_TPR_CODIGO
         left join usuario u on u.usua_codi = r.radi_usua_actu and u.depe_codi= r.radi_depe_actu
         left join dependencia du on  du.depe_codi = r.radi_depe_actu  and du.depe_codi is not null
         left join usuario ur on ur.usua_codi = r.radi_usua_radi and ur.depe_codi= r.radi_depe_radi
         $whereI
         where r.sgd_trad_codigo = $tpRAD  $where2 $where and r.radi_fech_radi between ('$fini 00:00:00') and ('$ffin 23:59:59') 
         and (r.sgd_eanu_codigo is null or  r.sgd_eanu_codigo not in (1,2)) and  substring(cast(r.radi_nume_radi as char(18)) from 1 for 1)='2' order by 2 ";
        }

        //trae info de total
        //trae la informacion de los radicados
        //  return $datos['SQL2'] = $iSql;
        $rs = $this->link->conn->query($iSql);

        if (!$rs->EOF) {
            $i = 0;
            $arrayRADa=array();
            $arrayRAD = '';
            $arrayRAD1 = '';
            $arrayRADF = '';
            $arrayRADNF = '';

            // echo "hd";
            while (!$rs->EOF) {
                if ($rs->fields['RADI']){
                $arrayRADa[$rs->fields['RADI']] = $rs->fields['RADI'];
                if ($rs->fields['DEPEACOD'] == 999) {
                    $arrayRADFa[$rs->fields['RADI']] = $rs->fields['RADI'];

                } /*else {
                $arrayRADNFa[$rs->fields['RADI']]= $rs->fields['RADI'];
                }*/

                foreach ($rs->fields as $key => $value) {

                    /*if ($key == 'MES') {
                    $datoss[$rs->fields['RADI']][strtoupper($key)] = $mesesN[$value];
                    } else*/

                    if ($key == 'ASOCIADO') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $value == '0' ? '' : $value;
                    } else
                    if ($key == 'MERC') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $this->medioRecp[$value];
                    } elseif ($key == 'USUAA') {
                        $datom=$value;
                        if(!$value && $rs->fields['DEPEAC']==999 )
                           $datom='ARCHIVO NRR';
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $datom;
                     } elseif($key == 'ASUNTO') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] =htmlentities( str_replace(array('<','>','','','',"\u23FD"),'', $value));
                    }else {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                    } 

                  
                }

                if ($rs->fields['DPA'] == '999') {
                    $datoss[$rs->fields['RADI']]['EST'] = 'Finalizado';
                } else {
                    $datoss[$rs->fields['RADI']]['EST'] = 'En Trámite';
                }
            }
                $i++;
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }
//$a=explode(',',$arrayRAD);
        $arrayRAD = implode(',', $arrayRADa);
//$arrayRADNF=implode(',',$arrayRADNFa);
        $arrayRADF = implode(',', $arrayRADFa);
      if ($arrayRADF) {
            $selectA = "select  h.radi_nume_radi radi ,to_char(h.hist_fech, 'DD-MM-YYYY HH24:MI') FTX, h.hist_obse comen,uh.usua_nomb usuafin,dh.depe_nomb DEPEFIN
          from  hist_eventos h
          left join dependencia dh on  dh.depe_codi = h.depe_codi  and dh.depe_codi is not null
          left join usuario uh on uh.usua_doc = h.usua_doc
         where  h.radi_nume_radi in ($arrayRADF) and h.sgd_ttr_codigo in (13,65)  "; //--left join usuario uh on uh.usua_codi = h.usua_codi and h.depe_codi=uh.depe_codi

            $rs = $this->link->conn->query($selectA);

            if (!$rs->EOF) {
                $i = 0;
                while (!$rs->EOF) {
                    foreach ($rs->fields as $key => $value) {
                        if ($key != 'RADI') {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] =preg_replace('/</i','',str_replace(array('<','>','&lt;','&gt;','','<'),'', $value));
                        }
                    }
                    $i++;
                    $coma = ',';
                    $rs->MoveNext();
                }
                //  $resp['titulo'] = $campot;
            }

        }
//medio de envio
        $arrayRADF = implode(',', $arrayRADFa);
        if ($arrayRADF) {
            $selectAs = "select  to_char(a.SGD_RENV_FECH, 'DD-MM-YYYY HH24:MI') envio ,a.RADI_NUME_SAL RADI,c.SGD_FENV_DESCRIP NENV
                        from sgd_renv_regenvio a, sgd_fenv_frmenvio c
		                where
		                a.radi_nume_sal in($arrayRAD)
		                AND a.sgd_fenv_codigo = c.sgd_fenv_codigo order by 1 asc  ";

            $rs = $this->link->conn->query($selectAs);

            if (!$rs->EOF) {
                $i = 0;
                while (!$rs->EOF) {
                    foreach ($rs->fields as $key => $value) {
                        if ($key != 'RADI') {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                        }
                    }
                    $i++;
                    $coma = ',';
                    $rs->MoveNext();
                }
                //  $resp['titulo'] = $campot;
            }

        }
if($tpRAD==1 ){
    //select anex_radi_nume radi,radi_nume_salida respsal from anexos  where anex_radi_nume in  ($arrayRAD) and anex_radi_nume<>radi_nume_salida and anex_estado=4 and  substring(cast(anex_radi_nume as char(18)) from 15 for 1)='1'
 $sqlt="
 select a.anex_radi_nume radi,a.radi_nume_salida respsal,to_char(r.SGD_RENV_FECH, 'DD-MM-YYYY HH24:MI') envio ,c.SGD_FENV_DESCRIP NENV 
 from anexos  a
 ,sgd_renv_regenvio r  
 , sgd_fenv_frmenvio c 
 where anex_radi_nume in  ($arrayRAD) and a.anex_radi_nume<>a.radi_nume_salida and anex_estado=4 and  substring(cast(a.anex_radi_nume as char(18)) from 15 for 1)='1' 
 and r.radi_nume_sal = a.radi_nume_salida and r.sgd_fenv_codigo = c.sgd_fenv_codigo ";
$rs = $this->link->conn->query($sqlt);

            if (!$rs->EOF) {
                $i = 0;
                while (!$rs->EOF) {
                    foreach ($rs->fields as $key => $value) {
                        if ($key != 'RADI') {
                            if ($value) {
                                $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;// ($datoss[$rs->fields['RADI']][strtoupper($key)] ?  $datoss[$rs->fields['RADI']][strtoupper($key)].','.$value: $value);
                            }

                        }
                    }
                    $i++;
                    $coma = ',';
                    $rs->MoveNext();
                }
                //  $resp['titulo'] = $campot;
            }

}

        //consultar proeycto
        if ($arrayRAD) {
            $selectAbb = " select distinct h.radi_nume_radi radi,ur.usua_nomb proyi
            from  hist_eventos h
			left join usuario ur on ur.usua_doc = h.usua_doc
			where   h.radi_nume_radi in  ($arrayRAD) and  h.sgd_ttr_codigo=2";
            //   return $datos['SQL2'] = $selectAbb;
            $rs = $this->link->conn->query($selectAbb);

            if (!$rs->EOF) {
                $i = 0;
                while (!$rs->EOF) {
                    foreach ($rs->fields as $key => $value) {
                        if ($key != 'RADI') {
                            if ($value) {
                                $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                            }

                        }
                    }
                    $i++;
                    $coma = ',';
                    $rs->MoveNext();
                }
                //  $resp['titulo'] = $campot;
            }

        }

        //consulta de ultimo historico
        if ($arrayRAD) {
            $selectA = " select  t.sgd_ttr_descrip ESTU,h.radi_nume_radi radi
            from  hist_eventos h,
            (select  radi_nume_radi, max(hist_fech) fechn from  hist_eventos  where  radi_nume_radi in ($arrayRAD) group by radi_nume_radi ) as h2
            ,sgd_ttr_transaccion t
           where  h.radi_nume_radi=h2.radi_nume_radi and  h2.fechn=h.hist_fech and h.sgd_ttr_codigo=t.sgd_ttr_codigo ";

            $rs = $this->link->conn->query($selectA);

            if (!$rs->EOF) {
                $i = 0;
                while (!$rs->EOF) {
                    foreach ($rs->fields as $key => $value) {
                        if ($key != 'RADI') {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                        }
                    }
                    $i++;
                    $coma = ',';
                    $rs->MoveNext();
                }
                //  $resp['titulo'] = $campot;
            }

        }

        //trae la info de los anexos

        $selectA = "select  a.radi_nume_salida radi ,anex_creador proyi2,anex_estado estresp,to_char(anex_fech_envio, 'DD-MM-YYYY HH24:MI') envio
         from  anexos a,dependencia d
          where  a.radi_nume_salida in ($arrayRAD) and d.depe_codi = a.anex_depe_creador and a.anex_salida = 1 and a.radi_nume_salida is not null ";
          $selectA = "select  a.radi_nume_salida radi ,anex_creador proyi2,anex_estado estresp,to_char( case when (a.anex_fech_envio is not null)  THEN 
          a.anex_fech_envio ELSE e.sgd_Renv_FECH END, 'DD-MM-YYYY HH24:MI') envio  
           from  anexos a left join sgd_renv_regenvio e on e.radi_nume_sal=a.radi_nume_salida and sgd_renv_estado=1
           ,dependencia d
            where  a.radi_nume_salida in ($arrayRAD) and d.depe_codi = a.anex_depe_creador and a.anex_salida = 1 and a.radi_nume_salida is not null  order by  estresp";
   
        if ($tpRAD == 3) {
            $selectA = "select  a.anex_radi_nume radi ,anex_creador proyi2,a.radi_nume_salida RESP
         from  anexos a,dependencia d
          where  a.anex_radi_nume in ($arrayRAD) and d.depe_codi = a.anex_depe_creador and a.anex_salida = 1 and a.radi_nume_salida is not null and a.anex_radi_nume<> a.radi_nume_salida ";
        }

        $rs = $this->link->conn->query($selectA);
        $estados = array(1 => 'Anexado', 2 => 'Radicado', 3 => 'Firmado', 4 => 'Enviado');
        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {

                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        if ($key == 'ESTRESP') {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $estados[$value];
                        } else if ($key == 'ENVIO') { //NENV
                            $valuex='';
                            if($rs->fields['ESTRESP']==4 ){
                                $valuex=$value;
                                if($value)                                
                                  $datoss[$rs->fields['RADI']][strtoupper($key)] = $valuex;
                                  if(!$datoss[$rs->fields['RADI']]['RESPSAL'])
                                    $datoss[$rs->fields['RADI']]['RESPSAL'] =$rs->fields['RADI'];
                            }
                            else{
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $valuex;
                            $datoss[$rs->fields['RADI']]['NENV']='';
                            /*if($datoss[$rs->fields['RADI']]['NENV'] ) {
                               // if(!$rs->fields['RADI']['ENVIO']) $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                                if(!$rs->fields['RADI']['RESPSAL'])$datoss[$rs->fields['RADI']]['RESPSAL'] =$rs->fields['RADI'];
                            } */
                           
                        }
                        }else {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                        }

                    }
                }
              
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }
        //trae info de sgd_dir_Dreciones
        $selectB = "select dir.radi_nume_radi radi, dir.SGD_DIR_DIRECCION dir,dir.SGD_DIR_MAIL email, dir.sgd_dir_nombre DREM,dir.sgd_dir_apellido arem,
        dir.SGD_DIR_NOMREMDES dig,dir.SGD_DIR_TELEFONO tel,dir.sgd_dir_mail email, m2.muni_nomb muni,d2.dpto_nomb depto
          from  sgd_dir_drecciones dir
         left join municipio m2 on dir.dpto_codi = m2.dpto_codi and dir.muni_codi = m2.muni_codi
         left join departamento d2 on d2.dpto_codi = m2.dpto_codi
         where  dir.radi_nume_radi  in ($arrayRAD) ";
        $rs = $this->link->conn->query($selectB);

        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {

                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        if ($key == 'REM' && (!$value || $value == ' ' || $value == '')) {
                            
                            if (!$datoss[$rs->fields['RADI']]['REM']) {
                                $datoss[$rs->fields['RADI']][strtoupper($key)] = $rs->fields['DIG'];
                            }

                        }
                       /* else if($key=='DEPTO')
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $this->depart[$value];  
                        else if($key=='MUNI')
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $this->munici[$rs->fields['DEPTO']][$value];*/
                         if($key=='DREM')
                            $datoss[$rs->fields['RADI']]['REM'] = $value.' '.$rs->fields['AREM'];
                        else {
                            $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                        }

                    }
                }
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }
        //busca los asociados

        $selectB = "select distinct  r.radi_nume_deri radi, r.radi_nume_radi ASOCIADO
        from  radicado r where  r.radi_nume_deri in ($arrayRAD) ";
        $rs = $this->link->conn->query($selectB);

        if (!$rs->EOF) {
            $i = 0;
            while (!$rs->EOF) {

                foreach ($rs->fields as $key => $value) {
                    if ($key != 'RADI') {
                        $datoss[$rs->fields['RADI']][strtoupper($key)] = $value;
                    }
                }
                $i++;
                $coma = ',';
                $rs->MoveNext();
            }
            //  $resp['titulo'] = $campot;
        }
        //$resp['SQL'] = $selectAs;
        //$datos['SQL2'] = $iSql;

        foreach ($datoss as $key => $value) {

		if(!isset($value['EMAIL']))
			$value['EMAIL']='';
		if(!isset($value['MUNI']))
			$value['MUNI']='';
		if(!isset($value['DEPTO']))
			$value['DEPTO']='';

            $datos[] = $value;
        }

        return $datos;
    }

    public function depahijas($depe)
    {

        $selectB = "select depe_codi from dependencia where depe_codi_territorial=$depe ";
        $rs = $this->link->conn->query($selectB);
        $depes = $depe;
        if (!$rs->EOF) {
            $i = 0;
            $coma = ',';
            while (!$rs->EOF) {
                $depes .= $coma . $rs->fields['DEPE_CODI'];
                //$coma = ',';
                $rs->MoveNext();
            }

        }
        $selectB = "select depe_codi from dependencia where depe_codi_territorial in ($depes) ";

        $rs = $this->link->conn->query($selectB);
        $depes2 = $depe;
        if (!$rs->EOF) {
            $i = 0;
            $coma = ',';
            while (!$rs->EOF) {
                $depes2 .= $coma . $rs->fields['DEPE_CODI'];
                //$coma = ',';
                $rs->MoveNext();
            }

        }

        return $depes2;
    }

    public function medioRecp()
    {
        $selectB = 'SELECT mrec_codi codi,mrec_desc nom FROM medio_recepcion';
        $rs = $this->link->conn->query($selectB);
        if (!$rs->EOF) {
            $i = 0;
            $coma = ',';
            while (!$rs->EOF) {
                $mrec[$rs->fields['CODI']] = $rs->fields['NOM'];
                //$coma = ',';
                $rs->MoveNext();
            }

        }
        return $mrec;
    }

    public function dependecias()
    {
        $selectB = 'SELECT depe_codi codi,depe_nomb nom FROM dependencia';
        $rs = $this->link->conn->query($selectB);
        if (!$rs->EOF) {
            $i = 0;
            $coma = ',';
            while (!$rs->EOF) {
                $mrec[$rs->fields['CODI']] = $rs->fields['NOM'];
                //$coma = ',';
                $rs->MoveNext();
            }

        }
        return $mrec;
    }

    public function departamentosFN()
    {
        $selectB = 'select dpto_codi codi,dpto_nomb nom from departamento';
        $rs = $this->link->conn->query($selectB);
        if (!$rs->EOF) {
            $i = 0;
            $coma = ',';
            while (!$rs->EOF) {
                $mrec[$rs->fields['CODI']] = $rs->fields['NOM'];
                //$coma = ',';
                $rs->MoveNext();
            }

        }
        return $mrec;
    }
    public function municipiosFN()
    {
        $selectB = 'select dpto_codi codi,muni_codi mcodi,muni_nomb nom from municipio';
        $rs = $this->link->conn->query($selectB);
        if (!$rs->EOF) {
            $i = 0;
            $coma = ',';
            while (!$rs->EOF) {
                $mrec[$rs->fields['CODI']][$rs->fields['MCODI']] = $rs->fields['NOM'];
                //$coma = ',';
                $rs->MoveNext();
            }

        }
        return $mrec;
    }


    

    public function __destruct()
    {
        $this->link->conn->Disconnect();
        unset($this->link);
    }
}
