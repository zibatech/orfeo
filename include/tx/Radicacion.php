<?php
class Radicacion
{
    /*** Attributes: ***/
    /**
     * Clase que maneja los Historicos de los documentos
     *
     * @param int Dependencia Dependencia de Territorial que Anula
     * @db Objeto conexion
     * @access public
     */


    var $db;
    var $radiTipoDeri;
    var $nivelRad;
    var $radiCuentai;
    var $eespCodi;
    var $mrecCodi;
    var $radiFechOfic;
    var $radiNumeDeri;
    var $tdidCodi;
    var $descAnex;
    var $radigplis;
    var $raAsun;
    var $radiDepeRadi;
    var $radiUsuaActu;
    var $radiDepeActu;
    var $carpCodi=0;
    var $carpPer=0;
    var $radiNumeRadi;
    var $sgdSpubCodigo;
    var $trteCodi;
    var $sgd_apli_codi;
    var $tdocCodi;
    var $rutaRaiz=".";

    var $nofolios;
    var $noanexos;
    var $guia;
    var $empTrans;
    var $radi_dato_001;
    var $radi_dato_002;

    var $radigplth;
    var $trdCodigo=0;

    /*
     * Código de verificación para consultar radicado vía consultaWeb
     * @author Sebastian Ortiz GPLV3+ Ministerio de la Protección Social 2012
     */

    var $codigoverificacion;

    /**
     *  VARIABLES DEL USUARIO ACTUAL
     */
    var $dependencia;
    var $dependenciaRadicacion;
    var $usuaDoc;
    var $usuaLogin;
    var $usuaCodi;
    var $codiNivel=1;
    var $noDigitosRad;
    var $noDigitosDep=3;
    var $usuaFirma;
    var $depeFirma;

    function __construct($db){
        $this->Radicacion($db);
    }

    function Radicacion($db){
        /**
         * Constructor de la clase Historico
         * @db variable en la cual se recibe el cursor sobre el cual se esta trabajando.
         *
         */
        global $HTTP_SERVER_VARS,$PHP_SELF,$HTTP_SESSION_VARS,$HTTP_GET_VARS,$krd;
        //global $HTTP_GET_VARS;
        $this->db=$db;
        $this->rutaRaiz=$db->rutaRaiz;
        $this->noDigitosRad = isset($_SESSION['digitosSecRad'])? $_SESSION['digitosSecRad']: 6;
        if(isset($_SESSION)){
            $this->dependenciaRadicacion = $_SESSION["dependencia"];
            $this->dependencia= isset($_SESSION['dependencia'])? $_SESSION['dependencia'] : null;
            $this->usuaDoc    = isset($_SESSION['usua_doc'])? $_SESSION['usua_doc']: null;
            $this->noDigitosDep = isset($_SESSION['digitosDependencia'])? $_SESSION['digitosDependencia']: null;
            $this->usuaCodi     = isset($_SESSION['codusuario'])? $_SESSION['codusuario']: null;
            $this->codiNivel    = isset($_SESSION['nivelus'])? $_SESSION['nivelus']: null;
        }
        $this->usuaLogin  = $krd;
        $this->codiNivel = isset($_GET['nivelus']) ? $_GET['nivelus'] : $this->codiNivel;
    }

    function getUsuaEnrutador($depeCodi){
        $iSql = "select u.usua_login, usua_codi, m.autu_id,r.autg_id,r.autp_id from autr_restric_grupo r
				  join autm_membresias m on (r.autg_id=m.autg_id)
				  join usuario u on (u.id=m.autu_id)
				where autp_id=59 and depe_codi=$depeCodi";
        $rs = $this->db->query($iSql);
        $usuaCodiEnrutador = $rs->fields["USUA_CODI"];

        if(!$usuaCodiEnrutador) $usuaCodiEnrutador=1;
        return $usuaCodiEnrutador;
    }

    function getEsEnrutador($depeCodi,$usuaCodi){
        $iSql = "select u.usua_login, usua_codi, m.autu_id,r.autg_id,r.autp_id from autr_restric_grupo r
				  join autm_membresias m on (r.autg_id=m.autg_id)
				  join usuario u on (u.id=m.autu_id)
				where autp_id=59 and depe_codi=$depeCodi and u.usua_codi=$usuaCodi";
        $rs = $this->db->query($iSql);
        $usuaCodiEnrutador = $rs->fields["USUA_CODI"];

        if(!$usuaCodiEnrutador) $usuaCodiEnrutador=1;
        return $usuaCodiEnrutador;
    }


    function newRadicado($tpRad, $tpDepeRad=null){

        $query = "SET TIMEZONE='America/Bogota'";
        $this->db->conn->Execute($query);

        $whereNivel = "";

        $sql = "SELECT CODI_NIVEL FROM USUARIO WHERE USUA_CODI = ".$this->radiUsuaActu." AND DEPE_CODI=".$this->radiDepeActu;
        # Busca el usuairo Origen para luego traer sus datos.
        //return "2011 $sql";
        $rs = $this->db->conn->Execute($sql); # Ejecuta la busqueda
        $usNivel = $rs->fields["CODI_NIVEL"];
        # Busca el usuairo Origen para luego traer sus datos.

        if(!$tpDepeRad){
            if($this->radiDepeRadi){
                $this->dependenciaRadicacion = $this->radiDepeRadi;
            }
            $tpDepeRad = $this->getDependenciaSecuencia($tpRad);
        } else {
            $this->dependencia = $tpDepeRad;
        }

        //Si no se tiene los campos de depe_rad_tp1| depe_rad_tp2| depe_rad_tp3
        //con numero de alguna dependencia traemos la secuencia de la dependencia
        //padre

        $secDepeRad = $this->getDependenciaSecuenciaPadre();

        $SecName = "SECR_TP".$tpRad."_".$secDepeRad;

        /** Se verifica si el usuario tiene permiso de Enrutador
         * Si contiene este permiso los radicados de entrada que se generen iran al usuario con dicho permiso.
         * El permiso es "USUA_PERM_ENRUTADOR"
         *
         * @author jlosada - correlibre 2016-03
         */

        if($tpRad==2 && !isset($this->radiMail)){
            // Consulta enrutadores de la Dependencia.
            $this->radiUsuaActu =  $this->getUsuaEnrutador($this->radiDepeActu);
        }
        $secNew=$this->db->conn->nextId($SecName);

        if($secNew==0){
            $secNew=$this->db->conn->nextId($SecName);
            if($secNew==0) die("<hr><b><font color=red><center>Error no genero un Numero de Secuencia<br>SQL: $secNew</center></font></b><hr>");
        }
        $newRadicado = date("Y") . str_pad($this->dependencia,$this->noDigitosDep,"0",STR_PAD_LEFT) . str_pad($secNew,$this->noDigitosRad,"0", STR_PAD_LEFT) . $tpRad;

        //echo 'SecName: '.$SecName.' secNew: '.$secNew.' radiUsuaActu: '.$this->radiUsuaActu.' radiDepeActu: '.$this->radiDepeActu.' newRadicado: '.$newRadicado; exit;

        if(!$this->radiTipoDeri){
            $recordR["radi_tipo_deri"]= "0";
        }else{
            $recordR["radi_tipo_deri"]= $this->radiTipoDeri;
        }

        if(!$this->carpCodi) $this->carpCodi = 0;
        if(!$this->carpPer)  $this->carpPer = 0;
        if(!$this->radiNumeDeri) $this->radiNumeDeri = 0;
        if(!$this->nivelRad) $this->nivelRad=0;
        if(!$this->mrecCodi) $this->mrecCodi=0;
        if(!$this->idPais) $this->idPais=170;

        $recordR["SGD_SPUB_CODIGO"] = $this->sgdSpubCodigo ? $this->sgdSpubCodigo : 0;

        $this->radiCuentai          = "'".str_replace("'"," ",$this->radiCuentai)."'";
        $recordR["RADI_CUENTAI"]    = $this->radiCuentai;
        $recordR["EESP_CODI"]       = $this->eespCodi?$this->eespCodi:0;
        $recordR["MREC_CODI"]       = $this->mrecCodi;

        $fechofic = $this->radiFechOfic;

        if(!empty($fechofic)){
            switch ($this->db->driver){
                case 'postgres':
                    $recordR["radi_fech_ofic"]= "'".$fechofic."'";
                    break;
                default:
                    $recordR["radi_fech_ofic"]= $this->db->conn->DBDate($this->radiFechOfic);
            }
        }

        $recordR["RADI_NUME_DERI"] = $this->radiNumeDeri;
        $recordR["RADI_USUA_RADI"] = $this->usuaCodi;
        $this->raAsun              = str_replace("'"," ",$this->raAsun);
        $recordR["RA_ASUN"]        = "'".$this->raAsun."'";
        $this->descAnex            = str_replace("'"," ",$this->descAnex);
        $recordR["radi_desc_anex"] = "'".$this->descAnex."'";
        $radi_depe_radi_           = $this->radiDepeRadi;
        $recordR["RADI_DEPE_RADI"] = $this->radiDepeRadi;
        $recordR["RADI_USUA_ACTU"] = $this->radiUsuaActu;
        $recordR["carp_codi"]      = $this->carpCodi;
        $recordR["CARP_PER"]       = $this->carpPer;
        $recordR["RADI_NUME_RADI"] = $newRadicado;
        $recordR["RADI_FECH_RADI"] = $this->db->sysdate();
        $recordR["RADI_DEPE_ACTU"] = $this->radiDepeActu;
        $recordR["ID_PAIS"]        = $this->idPais;
        $recordR["RADI_NUME_GUIA"] = "'$this->guia'";
        $recordR["RADI_DATO_001"]  = "'$this->radi_dato_001'";
        $recordR["RADI_DATO_002"]  = "'$this->radi_dato_002'";

        if($this->usuaFirma != NULL) {
            $recordR["RADI_USUA_FIRMA"] = $this->usuaFirma;
            $recordR["RADI_DEPE_FIRMA"] = $this->depeFirma;        
        }

        //validaciones para campos nuevos.
        if($this->empTrans)
            $recordR["EMP_TRANSPORTADORA"] = $this->empTrans;

        if(trim($this->radigplis)) $recordR["RADI_PAIS"]       = "'".$this->radigplis."'";
        if($this->tdocCodi=="null"){
            $this->tdocCodi = 0;
            $recordR["TDOC_CODI"] = $this->tdocCodi;
        }else{
            if ($this->tdocCodi != 'noactualizar'){
                $recordR["TDOC_CODI"]       = $this->tdocCodi;
            }
        }
        if(!$this->tdidCodi) $this->tdidCodi = "0";
        $recordR["TDID_CODI"] = $this->tdidCodi;
        if(!trim($this->nofolios)) $this->nofolios=0;
        if(trim($this->nofolios) ){
            $recordR["RADI_NUME_FOLIO"] = $this->nofolios;
        }

        if(!empty($this->noanexos)){
            $recordR["RADI_NUME_ANEXO"] = $this->noanexos;
        }

        $recordR["depe_codi"]       = $this->dependencia;
        $recordR["sgd_trad_codigo"] = $tpRad;

        if(!$usNivel) $usNivel=1;
        $recordR["CODI_NIVEL"]=$usNivel;
        if($this->radigplth)  $recordR["RADI_PATH"] = "'".$this->radiPath."'";

        /*
         * Codigo de verificación
         */
        $recordR["SGD_RAD_CODIGOVERIFICACION"] = "'" . substr(sha1(microtime()), 0 , 5) . "'";
        $this->codigoverificacion = str_replace("'","",$recordR["SGD_RAD_CODIGOVERIFICACION"]);

        // Calcula la fecha de Alerta si el Tipo de Radicado lo exige.
        include_once ($this->rutaRaiz."/include/tx/TipoDocumental.php");

        $tipoD = new TipoDocumental($this->db);

        /**
        $genAlerta = $tipoD->setFechAlerta("No existe todavia",$tpRad, false);
        if($genAlerta){
            $recordR["FECH_ALERTATRAD"] = "'$genAlerta'";
        }
        **/


        //Si el numero el radicado esta en fisico
        if (isset($_SESSION) && $_SESSION["varEstaenfisico"] == 1){
            if($this->esta_fisico) $recordR["esta_fisico"] = $this->esta_fisico;
        }

        $whereNivel = "";

        $insertSQL = $this->db->insert("RADICADO", $recordR, "false");

        if (!$insertSQL) {
            // Hay ocasiones en las que la secuencia se repite, lo que resulta en un 
            // radicado duplicado y la insercion en la BBDD falla.
            if ($this->newRadicadoRepetido($newRadicado)) {
                $newRadicado = $this->generateNewRadicado($SecName, $tpRad);
                $recordR["RADI_NUME_RADI"] = $newRadicado;
                $insertSQL = $this->db->insert("RADICADO", $recordR, "false");
                if (!$insertSQL) {
                    return "-1";
                } else {
                    return $newRadicado;
                }
            } else {
                return "-1";
            }
        } else {
            return $newRadicado;
        }
    }

    function newRadicadoBorrador($tpRad, $tpDepeRad = null) { 

        $query = "SET TIMEZONE='America/Bogota'";
        $this->db->conn->Execute($query);

        $whereNivel = "";

        $sql = "SELECT CODI_NIVEL FROM USUARIO WHERE USUA_CODI = ".$this->radiUsuaActu." AND DEPE_CODI=".$this->radiDepeActu;
        # Busca el usuairo Origen para luego traer sus datos.
        //return "2011 $sql";
        $rs = $this->db->conn->Execute($sql); # Ejecuta la busqueda
        $usNivel = $rs->fields["CODI_NIVEL"];
        # Busca el usuairo Origen para luego traer sus datos.

        if(!$tpDepeRad){
            if($this->radiDepeRadi){
                $this->dependenciaRadicacion = $this->radiDepeRadi;
            }
            $tpDepeRad = $this->getDependenciaSecuencia($tpRad);
        } else {
            $this->dependencia = $tpDepeRad;
        }

        //Si no se tiene los campos de depe_rad_tp1| depe_rad_tp2| depe_rad_tp3
        //con numero de alguna dependencia traemos la secuencia de la dependencia
        //padre

        $secDepeRad = $this->getDependenciaSecuenciaPadre();

        /** Se verifica si el usuario tiene permiso de Enrutador
         * Si contiene este permiso los radicados de entrada que se generen iran al usuario con dicho permiso.
         * El permiso es "USUA_PERM_ENRUTADOR"
         *
         * @author jlosada - correlibre 2016-03
         */

        if($tpRad==2 && !isset($this->radiMail)){
            // Consulta enrutadores de la Dependencia.
            $this->radiUsuaActu =  $this->getUsuaEnrutador($this->radiDepeActu);
        }
        
        if ($tpRad == 1)
            $secNew = $this->db->conn->nextId("borrador_salida_seq");
        elseif ($tpRad == 3)
            $secNew = $this->db->conn->nextId("borrador_memorando_seq");
        elseif ($tpRad == 4)
            $secNew = $this->db->conn->nextId("borrador_cir_inter_seq");
        elseif ($tpRad == 5)
            $secNew = $this->db->conn->nextId("borrador_cir_exter_seq");
        elseif ($tpRad == 6)
            $secNew = $this->db->conn->nextId("borrador_resolu_seq");
        elseif ($tpRad == 7) 
            $secNew = $this->db->conn->nextId("borrador_auto_seq");   
        
        $newRadicado = (date("Y") + 1000) . str_pad($this->dependencia,$this->noDigitosDep,"0",STR_PAD_LEFT) . str_pad($secNew,$this->noDigitosRad,"0", STR_PAD_LEFT) . $tpRad;

        //echo 'SecName: '.$SecName.' secNew: '.$secNew.' radiUsuaActu: '.$this->radiUsuaActu.' radiDepeActu: '.$this->radiDepeActu.' newRadicado: '.$newRadicado; exit;

        if(!$this->radiTipoDeri){
            $recordR["radi_tipo_deri"]= "0";
        }else{
            $recordR["radi_tipo_deri"]= $this->radiTipoDeri;
        }

        if(!$this->carpCodi) $this->carpCodi = 0;
        if(!$this->carpPer)  $this->carpPer = 0;
        if(!$this->radiNumeDeri) $this->radiNumeDeri = 0;
        if(!$this->nivelRad) $this->nivelRad=0;
        if(!$this->mrecCodi) $this->mrecCodi=0;
        if(!$this->idPais) $this->idPais=170;

        $recordR["SGD_SPUB_CODIGO"] = $this->sgdSpubCodigo ? $this->sgdSpubCodigo : 0;

        $this->radiCuentai          = "'".str_replace("'"," ",$this->radiCuentai)."'";
        $recordR["RADI_CUENTAI"]    = $this->radiCuentai;
        $recordR["EESP_CODI"]       = $this->eespCodi?$this->eespCodi:0;
        $recordR["MREC_CODI"]       = $this->mrecCodi;

        $fechofic = $this->radiFechOfic;

        if(!empty($fechofic)){
            switch ($this->db->driver){
                case 'postgres':
                    $recordR["radi_fech_ofic"]= "'".$fechofic."'";
                    break;
                default:
                    $recordR["radi_fech_ofic"]= $this->db->conn->DBDate($this->radiFechOfic);
            }
        }

        $recordR["RADI_NUME_DERI"] = $this->radiNumeDeri;
        $recordR["RADI_USUA_RADI"] = $this->radiUsuaActu;
        $this->raAsun              = str_replace("'"," ",$this->raAsun);
        $recordR["RA_ASUN"]        = "'".$this->raAsun."'";
        $this->descAnex            = str_replace("'"," ",$this->descAnex);
        $recordR["radi_desc_anex"] = "'".$this->descAnex."'";
        $radi_depe_radi_           = $this->radiDepeRadi;
        $recordR["RADI_DEPE_RADI"] = $this->radiDepeActu;
        $recordR["RADI_USUA_ACTU"] = $this->radiUsuaActu;
        $recordR["carp_codi"]      = $this->carpCodi;
        $recordR["CARP_PER"]       = $this->carpPer;
        $recordR["RADI_NUME_RADI"] = $newRadicado;
        $recordR["RADI_NUME_BORRADOR"] = $newRadicado;
        $recordR["RADI_FECH_RADI"] = $this->db->sysdate();
        $recordR["RADI_DEPE_ACTU"] = $this->radiDepeActu;
        $recordR["ID_PAIS"]        = $this->idPais;
        $recordR["RADI_NUME_GUIA"] = "'$this->guia'";
        $recordR["RADI_DATO_001"]  = "'$this->radi_dato_001'";
        $recordR["RADI_DATO_002"]  = "'$this->radi_dato_002'";

        if($this->usuaFirma != NULL) {
            $recordR["RADI_USUA_FIRMA"] = $this->usuaFirma;
            $recordR["RADI_DEPE_FIRMA"] = $this->depeFirma;        
        }
        //validaciones para campos nuevos.
        if($this->empTrans)
            $recordR["EMP_TRANSPORTADORA"] = $this->empTrans;

        if(trim($this->radigplis)) $recordR["RADI_PAIS"]       = "'".$this->radigplis."'";
        if($this->tdocCodi=="null"){
            $this->tdocCodi = 0;
            $recordR["TDOC_CODI"] = $this->tdocCodi;
        }else{
            if ($this->tdocCodi != 'noactualizar'){
                $recordR["TDOC_CODI"]       = $this->tdocCodi;
            }
        }
        if(!$this->tdidCodi) $this->tdidCodi = "0";
        $recordR["TDID_CODI"] = $this->tdidCodi;
        if(!trim($this->nofolios)) $this->nofolios=0;
        if(trim($this->nofolios) ){
            $recordR["RADI_NUME_FOLIO"] = $this->nofolios;
        }

        if(!empty($this->noanexos)){
            $recordR["RADI_NUME_ANEXO"] = $this->noanexos;
        }

        $recordR["depe_codi"]       = $this->dependencia;
        $recordR["sgd_trad_codigo"] = $tpRad;

        if(!$usNivel) $usNivel=1;
        $recordR["CODI_NIVEL"]=$usNivel;
        if($this->radigplth)  $recordR["RADI_PATH"] = "'".$this->radiPath."'";

        /*
         * Codigo de verificación
         */
        $recordR["SGD_RAD_CODIGOVERIFICACION"] = "'" . substr(sha1(microtime()), 0 , 5) . "'";
        $this->codigoverificacion = str_replace("'","",$recordR["SGD_RAD_CODIGOVERIFICACION"]);

        // Calcula la fecha de Alerta si el Tipo de Radicado lo exige.
        include_once ($this->rutaRaiz."/include/tx/TipoDocumental.php");

        $tipoD = new TipoDocumental($this->db);

        /**
        $genAlerta = $tipoD->setFechAlerta("No existe todavia",$tpRad, false);
        if($genAlerta){
            $recordR["FECH_ALERTATRAD"] = "'$genAlerta'";
        }
        **/


        //Si el numero el radicado esta en fisico
        if (isset($_SESSION) && $_SESSION["varEstaenfisico"] == 1){
            if($this->esta_fisico) $recordR["esta_fisico"] = $this->esta_fisico;
        }

        $whereNivel = "";

        $insertSQL = $this->db->insert("RADICADO", $recordR, "false");

        $sqlAgregarBanderaBorrador = "update radicado set is_borrador = true where 
                radi_nume_radi = " . $newRadicado;   
        $this->db->conn->execute($sqlAgregarBanderaBorrador);  

        return $newRadicado;
    }

    function generateNewRadicadoNotificacion($tpRad, $tpDepeRad = null) {

       if(!$tpDepeRad){
            if($this->radiDepeRadi){
                $this->dependenciaRadicacion = $this->radiDepeRadi;
            }
            $tpDepeRad = $this->getDependenciaSecuencia($tpRad);
        } else {
            $this->dependencia = $tpDepeRad;
        }

        //Si no se tiene los campos de depe_rad_tp1| depe_rad_tp2| depe_rad_tp3
        //con numero de alguna dependencia traemos la secuencia de la dependencia
        //padre

        $secDepeRad = $this->getDependenciaSecuenciaPadre();        
        $SecName = "SECR_TP".$tpRad."_".$secDepeRad;

        $secNew=$this->db->conn->nextId($SecName);

        if($secNew==0){
            $secNew=$this->db->conn->nextId($SecName);
            if($secNew==0) die("<hr><b><font color=red><center>Error no genero un Numero de Secuencia<br>SQL: $secNew</center></font></b><hr>");
        }
        $newRadicado = date("Y") . str_pad($this->dependencia,$this->noDigitosDep,"0",STR_PAD_LEFT) . str_pad($secNew,$this->noDigitosRad,"0", STR_PAD_LEFT) . $tpRad;

        return  $newRadicado;
    }

    function borradorArradicado($numRadicadoPadreAnt, $numRadicadoPadre, $rutaRaiz){


        $radTemporal =  rand(-99999, -90000);
        $sqlCrerRadTemporal = "INSERT INTO radicado(
                id, radi_nume_radi, radi_fech_radi, tdoc_codi)
                    VALUES ( $radTemporal,  $radTemporal, '1999-11-21 15:09:04.619651', 0)";

        $this->db->conn->execute($sqlCrerRadTemporal);                    

        $sql = "SELECT sgd_notif_codigo
                    FROM sgd_notif_notificaciones   
                    where radi_nume_radi = $numRadicadoPadreAnt";

        $rs = $this->db->conn->query($sql);
        $arraySgdNotifCodigo = array();
        while(!$rs->EOF ){

            $sgdNotifCodigo = $rs->fields["SGD_NOTIF_CODIGO"];
            $sqlUpdateNotifNotifica = "update sgd_notif_notificaciones set radi_nume_radi = 
                $radTemporal where sgd_notif_codigo = $sgdNotifCodigo";

            $this->db->conn->execute($sqlUpdateNotifNotifica);
            array_push($arraySgdNotifCodigo, $sgdNotifCodigo);
            $rs->MoveNext();
        }   

        $sqlSgdRdfRetDocf = "SELECT id from sgd_rdf_retdocf where 
                radi_nume_radi = $numRadicadoPadreAnt";
        $rsSgdRdfRetDocf = $this->db->conn->query($sqlSgdRdfRetDocf);
        $arraySgdRdfRetDocf = array();
        while(!$rsSgdRdfRetDocf->EOF ){
            $sgdRdId = $rsSgdRdfRetDocf->fields["ID"];

            $sqlUpdateSgdRdfRetDocf = "update sgd_rdf_retdocf set radi_nume_radi = $radTemporal  where id = $sgdRdId";
             $this->db->conn->execute($sqlUpdateSgdRdfRetDocf);             
            
            array_push($arraySgdRdfRetDocf, $sgdRdId);
            $rsSgdRdfRetDocf->MoveNext();
        }

        $sqlSgdExpediente = "SELECT id, sgd_exp_numero 
            FROM sgd_exp_expediente where radi_nume_radi = $numRadicadoPadreAnt";
        $rsSqlSgdExpediente = $this->db->conn->query($sqlSgdExpediente);
        $arraySgdExpediente = array();
        while(!$rsSqlSgdExpediente->EOF ){

            $sgdExpedienteId = $rsSqlSgdExpediente->fields["ID"];
            $sqlUpdateSgdExp = "update sgd_exp_expediente set radi_nume_radi = $radTemporal where id = $sgdExpedienteId";
             $this->db->conn->execute($sqlUpdateSgdExp);               

            array_push($arraySgdExpediente, $sgdExpedienteId);
            $rsSqlSgdExpediente->MoveNext();
        }

        $sqlSgdCausCausales = "select sgd_caux_codigo FROM sgd_caux_causales where radi_nume_radi = $numRadicadoPadreAnt";
        $rsSqlSgdCausCausales = $this->db->conn->query($sqlSgdCausCausales);
        $arraySgdCausCausales = array();
        while(!$rsSqlSgdCausCausales->EOF ){

            $sgdCauxCodigo = $rsSqlSgdCausCausales->fields["SGD_CAUX_CODIGO"];
            $sqlUpdateSgdCausCausales = "update sgd_caux_causales set radi_nume_radi = 
                $radTemporal where sgd_caux_codigo = $sgdCauxCodigo";
             $this->db->conn->execute($sqlUpdateSgdCausCausales);               

            array_push($arraySgdCausCausales, $sgdCauxCodigo);
            $rsSqlSgdCausCausales->MoveNext();
        }        

        $sqlAnexos = "select anex_codigo from anexos where  anex_radi_nume = 
            $numRadicadoPadreAnt";
        $rsSqlAnexos = $this->db->conn->query($sqlAnexos);
        $arrayAnexos = array();
        while(!$rsSqlAnexos->EOF ){

            $sgdAnexCodigo = $rsSqlAnexos->fields["ANEX_CODIGO"];
            $sqlUpdateAnexos = "update anexos set anex_radi_nume = $radTemporal where anex_codigo = '$sgdAnexCodigo'";
             $this->db->conn->execute($sqlUpdateAnexos);               

            array_push($arrayAnexos, $sgdAnexCodigo);
            $rsSqlAnexos->MoveNext();
        }           

        $sqlFirRad = "SELECT sgd_firrad_id FROM sgd_firrad_firmarads where radi_nume_radi =
                $numRadicadoPadreAnt";
        $rsSqlFirRad = $this->db->conn->query($sqlFirRad);
        $arrayFirRad = array();
        while(!$rsSqlFirRad->EOF ){

            $sgdFirradId = $rsSqlFirRad->fields["SGD_FIRRAD_ID"];
            $sqlUpdateFirrad = "update sgd_firrad_firmarads set radi_nume_radi = $radTemporal  where sgd_firrad_id = $sgdFirradId";
             $this->db->conn->execute($sqlUpdateFirrad);               

            array_push($arrayFirRad, $sgdFirradId);
            $rsSqlFirRad->MoveNext();
        }    

        $sqlHmtd = "SELECT sgd_hmtd_codigo from sgd_hmtd_hismatdoc where radi_nume_radi = 
            $numRadicadoPadreAnt";
        $rsSqlHmtd = $this->db->conn->query($sqlHmtd);
        $arrayHmtd = array();
        while(!$rsSqlHmtd->EOF ){

            $sgdHmtdCodigo = $rsSqlHmtd->fields["SGD_HMTD_CODIGO"];
            $sqlUpdateHmtd = "update sgd_hmtd_hismatdoc set radi_nume_radi = $radTemporal  where sgd_hmtd_codigo = $sgdHmtdCodigo";
             $this->db->conn->execute($sqlUpdateHmtd);               

            array_push($arrayHmtd, $sgdHmtdCodigo);
            $rsSqlHmtd->MoveNext();
        }      

        $sqlNtrdNoti = "SELECT radi_nume_radi from sgd_ntrd_notifrad where radi_nume_radi =  
            $numRadicadoPadreAnt";
        $rsSqlNtrdNoti = $this->db->conn->query($sqlNtrdNoti);
        $arrayNtrdNoti = array();
        while(!$rsSqlNtrdNoti->EOF ){

            $sgdRadiNumeRadi = $rsSqlNtrdNoti->fields["RADI_NUME_RADI"];
            $sqlUpdateNtrdNotfi = "update sgd_ntrd_notifrad set radi_nume_radi = $radTemporal  where radi_nume_radi = $sgdRadiNumeRadi";
             $this->db->conn->execute($sqlUpdateNtrdNotfi);               

            array_push($arrayNtrdNoti, $sgdRadiNumeRadi);
            $rsSqlNtrdNoti->MoveNext();
        }     

        $sqlAgenAgendados = "SELECT id FROM sgd_agen_agendados where radi_nume_radi = 
            $numRadicadoPadreAnt";
        $rsSqlAgenAgendados = $this->db->conn->query($sqlAgenAgendados);
        $arrayAgenAgendados = array();
        while(!$rsSqlAgenAgendados->EOF ){

            $sgdAgenId = $rsSqlAgenAgendados->fields["ID"];
            $sqlUpdateAgen = "update sgd_agen_agendados set radi_nume_radi = $radTemporal 
              where id = $sgdAgenId";
             $this->db->conn->execute($sqlUpdateAgen);               

            array_push($arrayAgenAgendados, $sgdAgenId);
            $rsSqlAgenAgendados->MoveNext();
        }    

        $sqlFijacion = "SELECT  radi_nume_radi from sgd_nfn_notifijacion where radi_nume_radi = $numRadicadoPadreAnt";
        $rsSqlFijacion = $this->db->conn->query($sqlFijacion);
        $arrayFijacion = array();
        while(!$rsSqlFijacion->EOF ){

            $sgdFijacionId = $rsSqlFijacion->fields["RADI_NUME_RADI"];
            $sqlUpdateFijacion = "update sgd_nfn_notifijacion set radi_nume_radi = $radTemporal
                 where radi_nume_radi = $sgdFijacionId";
             $this->db->conn->execute($sqlUpdateFijacion);               

            array_push($arrayFijacion, $sgdFijacionId);
            $rsSqlFijacion->MoveNext();
        }                                


        $sqlUpdateRadicado = "update radicado set radi_nume_radi = $numRadicadoPadre, 
            radi_fech_radi = (select now()) where radi_nume_radi = $numRadicadoPadreAnt";   
        $this->db->conn->execute($sqlUpdateRadicado);     

        foreach ($arraySgdNotifCodigo as &$valor) {
            $sqlUpdateNotifNotifica = "update sgd_notif_notificaciones 
                set radi_nume_radi = $numRadicadoPadre where sgd_notif_codigo = $valor";
            $this->db->conn->execute($sqlUpdateNotifNotifica);  
        }  

        foreach ($arraySgdRdfRetDocf as &$valor) {
            $sqlUpdateSgdRdfRetDocf = "update sgd_rdf_retdocf set radi_nume_radi = $numRadicadoPadre where  id = $valor";
            $this->db->conn->execute($sqlUpdateSgdRdfRetDocf);                          
        }

        foreach ($arraySgdExpediente as &$valor) {
            $sqlUpdateSgdExp = "update sgd_exp_expediente set radi_nume_radi = $numRadicadoPadre  where id = $valor";
            $this->db->conn->execute($sqlUpdateSgdExp);
        }

        foreach ($arraySgdCausCausales as &$valor) {
            $sqlUpdateSgdCausCausales = "update sgd_caux_causales set radi_nume_radi = 
                $numRadicadoPadre where sgd_caux_codigo = $valor";
            $this->db->conn->execute($sqlUpdateSgdCausCausales);
        }        

        foreach ($arrayAnexos as &$valor) {
            $sqlUpdateAnexos = "update anexos set anex_radi_nume = $numRadicadoPadre where anex_codigo = '$valor'";
             $this->db->conn->execute($sqlUpdateAnexos);               
        }    

        foreach ($arrayFirRad as &$valor) {
            $sqlUpdateFirrad = "update sgd_firrad_firmarads set radi_nume_radi = 
                $numRadicadoPadre where sgd_firrad_id = $valor";
             $this->db->conn->execute($sqlUpdateFirrad);               
        }        

        foreach ($arrayHmtd as &$valor) {
            $sqlUpdateHmtd = "update sgd_hmtd_hismatdoc set radi_nume_radi = $numRadicadoPadre where sgd_hmtd_codigo = $valor";
             $this->db->conn->execute($sqlUpdateHmtd);               
        }      

        foreach ($arrayNtrdNoti as &$valor) {
            $sqlUpdateNtrdNotfi = "update sgd_ntrd_notifrad set radi_nume_radi = 
                $numRadicadoPadre where radi_nume_radi = $valor";
             $this->db->conn->execute($sqlUpdateNtrdNotfi);               
        }                       

        foreach ($arrayAgenAgendados as &$valor) {
            $sqlUpdateAgen = "update sgd_agen_agendados set radi_nume_radi = $numRadicadoPadre   where id = $valor";
             $this->db->conn->execute($sqlUpdateAgen);                                        
        }   

        foreach ($arrayFijacion as &$valor) {
            $sqlUpdateFijacion = "update sgd_nfn_notifijacion set radi_nume_radi = 
                $numRadicadoPadre where radi_nume_radi = $valor";
             $this->db->conn->execute($sqlUpdateFijacion);               
        }                             


        $sqlUpdateHisEvento = "update hist_eventos set radi_nume_radi = $numRadicadoPadre 
                where radi_nume_radi = $numRadicadoPadreAnt"; 
        $this->db->conn->execute($sqlUpdateHisEvento);    
        
        $sqlSgdDirDirecciones = "update sgd_dir_drecciones set radi_nume_radi = $numRadicadoPadre  where radi_nume_radi =  $numRadicadoPadreAnt";   
        $this->db->conn->execute($sqlSgdDirDirecciones);

        $sqlInformados = "update informados set radi_nume_radi = $numRadicadoPadre 
    where radi_nume_radi =  $numRadicadoPadreAnt";    
        $this->db->conn->execute($sqlInformados);          


         $sqlAnexoCamNombre = "select id, anex_codigo, anex_nomb_archivo from anexos 
                where anex_radi_nume = $numRadicadoPadre";
         $rsAnexoCamNombre = $this->db->conn->query($sqlAnexoCamNombre); 
         
         $directorioAno  = substr($numRadicadoPadreAnt, 0, 4);
         $depeRadiPadre = ltrim(substr($numRadicadoPadreAnt, 4, $this->noDigitosDep), '0');

         $directorioAnoNuevo  = substr($numRadicadoPadre, 0, 4);
         $depeRadiPadreNuevo = ltrim(substr($numRadicadoPadre, 4, $this->noDigitosDep), '0');

         while(!$rsAnexoCamNombre->EOF ) { 
             $idAnexoCN = $rsAnexoCamNombre->fields["ID"];
             $anexCodigoCN = $rsAnexoCamNombre->fields["ANEX_CODIGO"];
             $anexCodigoCN = substr($anexCodigoCN, -5);
             $auxNuevoAnexCodigo =  $numRadicadoPadre . $anexCodigoCN;

             $extCodigoCn = explode('.', $rsAnexoCamNombre->fields["ANEX_NOMB_ARCHIVO"]);             
             if($extCodigoCn[1] != 'pdf') {
                $auxNuevoAnexCodigoExt = '1' . $numRadicadoPadre . '_' . $anexCodigoCN . '.' . $extCodigoCn[1];
             } else {
                if (strpos($rsAnexoCamNombre->fields["ANEX_NOMB_ARCHIVO"], '_') !== false) {
                    $auxNuevoAnexCodigoExt = '1' . $numRadicadoPadre . '_' . $anexCodigoCN . '.pdf';
                } else {
                    $auxNuevoAnexCodigoExt = $auxNuevoAnexCodigo . ".pdf";    
                }                
             }

             $sqlAnexoCamNombreUpdate = "update anexos 
                set anex_codigo = '$auxNuevoAnexCodigo', 
                    anex_nomb_archivo = '" . $auxNuevoAnexCodigoExt . "' 
                    where id = $idAnexoCN";


           if($extCodigoCn[1] == 'pdf' && strpos($rsAnexoCamNombre->fields["ANEX_NOMB_ARCHIVO"], '_') !== false) {
                $directorioAnt     = $rutaRaiz . '/bodega/' . $directorioAno . '/' . $depeRadiPadre . '/docs/' . $rsAnexoCamNombre->fields["ANEX_NOMB_ARCHIVO"];
                $directorioNuevo   = $rutaRaiz . '/bodega/' . $directorioAnoNuevo . '/' . $depeRadiPadreNuevo . '/docs/' . $auxNuevoAnexCodigoExt;
        
                rename($directorioAnt, $directorioNuevo);
            } elseif($extCodigoCn[1] == 'pdf' && strpos($rsAnexoCamNombre->fields["ANEX_NOMB_ARCHIVO"], '_') !== true) {
                
                $directorioAnt     = $rutaRaiz . '/bodega/' . $directorioAno . '/' . $depeRadiPadre . '/docs/' . $rsAnexoCamNombre->fields["ANEX_CODIGO"];
                $directorioNuevo   = $rutaRaiz . '/bodega/' . $directorioAnoNuevo . '/' . $depeRadiPadreNuevo . '/docs/' . $auxNuevoAnexCodigo;               

                rename($directorioAnt . '.txt', $directorioNuevo . '.txt'); 
                rename($directorioAnt . '.pdf', $directorioNuevo . '.pdf');

            } else {
                $directorioAnt     = $rutaRaiz . '/bodega/' . $directorioAno . '/' . $depeRadiPadre . '/docs/' . $rsAnexoCamNombre->fields["ANEX_NOMB_ARCHIVO"];
                $directorioNuevo   = $rutaRaiz . '/bodega/' . $directorioAnoNuevo . '/' . $depeRadiPadreNuevo . '/docs/' . $auxNuevoAnexCodigoExt;
                rename($directorioAnt, $directorioNuevo); 
            }                                    

            $this->db->conn->execute($sqlAnexoCamNombreUpdate);                      

             $rsAnexoCamNombre->MoveNext();
         }

        $sqlAgregarBanderaBorrador = "update radicado set is_borrador = false where 
            radi_nume_radi = " . $numRadicadoPadre;   
        $this->db->conn->execute($sqlAgregarBanderaBorrador);          

        $sqlBorrarTemporal = "delete from radicado where id = $radTemporal and radi_nume_radi = $radTemporal";
        $this->db->conn->execute($sqlBorrarTemporal);          
    }

    function borradorArradicadoAnexo($numRadicadoPadreAnt, $numRadicadoPadre, $rutaRaiz){

        $radTemporal =  rand(-99999, -90000);
        $sqlCrerRadTemporal = "INSERT INTO radicado(
                id, radi_nume_radi, radi_fech_radi, tdoc_codi)
                    VALUES ( $radTemporal,  $radTemporal, '1999-11-21 15:09:04.619651', 0)";

        $this->db->conn->execute($sqlCrerRadTemporal);                    


        $sql = "SELECT sgd_notif_codigo
                    FROM sgd_notif_notificaciones   
                    where radi_nume_radi = $numRadicadoPadreAnt";

        $rs = $this->db->conn->query($sql);
        $arraySgdNotifCodigo = array();
        while(!$rs->EOF ){

            $sgdNotifCodigo = $rs->fields["SGD_NOTIF_CODIGO"];
            $sqlUpdateNotifNotifica = "update sgd_notif_notificaciones set radi_nume_radi = 
                $radTemporal where sgd_notif_codigo = $sgdNotifCodigo";

            $this->db->conn->execute($sqlUpdateNotifNotifica);
            array_push($arraySgdNotifCodigo, $sgdNotifCodigo);
            $rs->MoveNext();
        }   

        $sqlSgdRdfRetDocf = "SELECT id from sgd_rdf_retdocf where 
                radi_nume_radi = $numRadicadoPadreAnt";
        $rsSgdRdfRetDocf = $this->db->conn->query($sqlSgdRdfRetDocf);
        $arraySgdRdfRetDocf = array();
        while(!$rsSgdRdfRetDocf->EOF ){
            $sgdRdId = $rsSgdRdfRetDocf->fields["ID"];

            $sqlUpdateSgdRdfRetDocf = "update sgd_rdf_retdocf set radi_nume_radi = $radTemporal
                where id = $sgdRdId";
             $this->db->conn->execute($sqlUpdateSgdRdfRetDocf);             
            
            array_push($arraySgdRdfRetDocf, $sgdRdId);
            $rsSgdRdfRetDocf->MoveNext();
        }

        $sqlSgdExpediente = "SELECT id, sgd_exp_numero 
            FROM sgd_exp_expediente where radi_nume_radi = $numRadicadoPadreAnt";
        $rsSqlSgdExpediente = $this->db->conn->query($sqlSgdExpediente);
        $arraySgdExpediente = array();
        while(!$rsSqlSgdExpediente->EOF ){

            $sgdExpedienteId = $rsSqlSgdExpediente->fields["ID"];
            $sqlUpdateSgdExp = "update sgd_exp_expediente set radi_nume_radi = $radTemporal 
                where id = $sgdExpedienteId";
             $this->db->conn->execute($sqlUpdateSgdExp);               

            array_push($arraySgdExpediente, $sgdExpedienteId);
            $rsSqlSgdExpediente->MoveNext();
        }

        $sqlSgdCausCausales = "select sgd_caux_codigo FROM sgd_caux_causales where radi_nume_radi = $numRadicadoPadreAnt";
        $rsSqlSgdCausCausales = $this->db->conn->query($sqlSgdCausCausales);
        $arraySgdCausCausales = array();
        while(!$rsSqlSgdCausCausales->EOF ){

            $sgdCauxCodigo = $rsSqlSgdCausCausales->fields["SGD_CAUX_CODIGO"];
            $sqlUpdateSgdCausCausales = "update sgd_caux_causales set radi_nume_radi = 
                $radTemporal where sgd_caux_codigo = $sgdCauxCodigo";
             $this->db->conn->execute($sqlUpdateSgdCausCausales);               

            array_push($arraySgdCausCausales, $sgdCauxCodigo);
            $rsSqlSgdCausCausales->MoveNext();
        }        

        $sqlAnexos = "select anex_codigo from anexos where  anex_radi_nume = 
            $numRadicadoPadreAnt";
        $rsSqlAnexos = $this->db->conn->query($sqlAnexos);
        $arrayAnexos = array();
        while(!$rsSqlAnexos->EOF ){

            $sgdAnexCodigo = $rsSqlAnexos->fields["ANEX_CODIGO"];
            $sqlUpdateAnexos = "update anexos set anex_radi_nume = $radTemporal where 
                anex_codigo = '$sgdAnexCodigo'";
             $this->db->conn->execute($sqlUpdateAnexos);               

            array_push($arrayAnexos, $sgdAnexCodigo);
            $rsSqlAnexos->MoveNext();
        }           

        $sqlFirRad = "SELECT sgd_firrad_id FROM sgd_firrad_firmarads where radi_nume_radi =
                $numRadicadoPadreAnt";
        $rsSqlFirRad = $this->db->conn->query($sqlFirRad);
        $arrayFirRad = array();
        while(!$rsSqlFirRad->EOF ){

            $sgdFirradId = $rsSqlFirRad->fields["SGD_FIRRAD_ID"];
            $sqlUpdateFirrad = "update sgd_firrad_firmarads set radi_nume_radi = $radTemporal  
                where sgd_firrad_id = $sgdFirradId";
             $this->db->conn->execute($sqlUpdateFirrad);               

            array_push($arrayFirRad, $sgdFirradId);
            $rsSqlFirRad->MoveNext();
        }    

        $sqlHmtd = "SELECT sgd_hmtd_codigo from sgd_hmtd_hismatdoc where radi_nume_radi = 
            $numRadicadoPadreAnt";
        $rsSqlHmtd = $this->db->conn->query($sqlHmtd);
        $arrayHmtd = array();
        while(!$rsSqlHmtd->EOF ){

            $sgdHmtdCodigo = $rsSqlHmtd->fields["SGD_HMTD_CODIGO"];
            $sqlUpdateHmtd = "update sgd_hmtd_hismatdoc set radi_nume_radi = $radTemporal 
                where sgd_hmtd_codigo = $sgdHmtdCodigo";
             $this->db->conn->execute($sqlUpdateHmtd);               

            array_push($arrayHmtd, $sgdHmtdCodigo);
            $rsSqlHmtd->MoveNext();
        }      

        $sqlNtrdNoti = "SELECT radi_nume_radi from sgd_ntrd_notifrad where radi_nume_radi =  
            $numRadicadoPadreAnt";
        $rsSqlNtrdNoti = $this->db->conn->query($sqlNtrdNoti);
        $arrayNtrdNoti = array();
        while(!$rsSqlNtrdNoti->EOF ){

            $sgdRadiNumeRadi = $rsSqlNtrdNoti->fields["RADI_NUME_RADI"];
            $sqlUpdateNtrdNotfi = "update sgd_ntrd_notifrad set radi_nume_radi = $radTemporal 
                 where radi_nume_radi = $sgdRadiNumeRadi";
             $this->db->conn->execute($sqlUpdateNtrdNotfi);               

            array_push($arrayNtrdNoti, $sgdRadiNumeRadi);
            $rsSqlNtrdNoti->MoveNext();
        }     

        $sqlAgenAgendados = "SELECT id FROM sgd_agen_agendados where radi_nume_radi = 
            $numRadicadoPadreAnt";
        $rsSqlAgenAgendados = $this->db->conn->query($sqlAgenAgendados);
        $arrayAgenAgendados = array();
        while(!$rsSqlAgenAgendados->EOF ){

            $sgdAgenId = $rsSqlAgenAgendados->fields["ID"];
            $sqlUpdateAgen = "update sgd_agen_agendados set radi_nume_radi = $radTemporal 
                 where id = $sgdAgenId";
             $this->db->conn->execute($sqlUpdateAgen);               

            array_push($arrayAgenAgendados, $sgdAgenId);
            $rsSqlAgenAgendados->MoveNext();
        }    

        $sqlFijacion = "SELECT  radi_nume_radi from sgd_nfn_notifijacion where radi_nume_radi = $numRadicadoPadreAnt";
        $rsSqlFijacion = $this->db->conn->query($sqlFijacion);
        $arrayFijacion = array();
        while(!$rsSqlFijacion->EOF ){

            $sgdFijacionId = $rsSqlFijacion->fields["RADI_NUME_RADI"];
            $sqlUpdateFijacion = "update sgd_nfn_notifijacion set radi_nume_radi = $radTemporal  where radi_nume_radi = $sgdFijacionId";
             $this->db->conn->execute($sqlUpdateFijacion);               

            array_push($arrayFijacion, $sgdFijacionId);
            $rsSqlFijacion->MoveNext();
        }                                

            
        $sqlUpdateRadicado = "update radicado set radi_nume_radi = $numRadicadoPadre, 
            radi_fech_radi = (select now()) where radi_nume_radi = $numRadicadoPadreAnt";   
        $this->db->conn->execute($sqlUpdateRadicado);     

        foreach ($arraySgdNotifCodigo as &$valor) {
            $sqlUpdateNotifNotifica = "update sgd_notif_notificaciones 
                set radi_nume_radi = $numRadicadoPadre where sgd_notif_codigo = $valor";
            $this->db->conn->execute($sqlUpdateNotifNotifica);  
        }  

        foreach ($arraySgdRdfRetDocf as &$valor) {
            $sqlUpdateSgdRdfRetDocf = "update sgd_rdf_retdocf set radi_nume_radi = $numRadicadoPadre where  id = $valor";
             $this->db->conn->execute($sqlUpdateSgdRdfRetDocf);                          
        }

        foreach ($arraySgdExpediente as &$valor) {
            $sqlUpdateSgdExp = "update sgd_exp_expediente set radi_nume_radi = $numRadicadoPadre  where id = $valor";
            $this->db->conn->execute($sqlUpdateSgdExp);
        }    

        foreach ($arraySgdCausCausales as &$valor) {
            $sqlUpdateSgdCausCausales = "update sgd_caux_causales set radi_nume_radi = 
                $numRadicadoPadre where sgd_caux_codigo = $valor";
            $this->db->conn->execute($sqlUpdateSgdCausCausales);
        }        

        foreach ($arrayAnexos as &$valor) {
            $sqlUpdateAnexos = "update anexos set anex_radi_nume = $numRadicadoPadre where anex_codigo = '$valor'";
             $this->db->conn->execute($sqlUpdateAnexos);               
        }    

        foreach ($arrayFirRad as &$valor) {
            $sqlUpdateFirrad = "update sgd_firrad_firmarads set radi_nume_radi = 
                $numRadicadoPadre where sgd_firrad_id = $valor";
             $this->db->conn->execute($sqlUpdateFirrad);               
        }        

        foreach ($arrayHmtd as &$valor) {
            $sqlUpdateHmtd = "update sgd_hmtd_hismatdoc set radi_nume_radi = $numRadicadoPadre where sgd_hmtd_codigo = $valor";
             $this->db->conn->execute($sqlUpdateHmtd);               
        }      

        foreach ($arrayNtrdNoti as &$valor) {
            $sqlUpdateNtrdNotfi = "update sgd_ntrd_notifrad set radi_nume_radi = 
                $numRadicadoPadre where radi_nume_radi = $valor";
             $this->db->conn->execute($sqlUpdateNtrdNotfi);               
        }                       

        foreach ($arrayAgenAgendados as &$valor) {
            $sqlUpdateAgen = "update sgd_agen_agendados set radi_nume_radi = $numRadicadoPadre   where id = $valor";
             $this->db->conn->execute($sqlUpdateAgen);                                        
        }   

        foreach ($arrayFijacion as &$valor) {
            $sqlUpdateFijacion = "update sgd_nfn_notifijacion set radi_nume_radi = 
                $numRadicadoPadre where radi_nume_radi = $valor";
             $this->db->conn->execute($sqlUpdateFijacion);               
        }                             


        $sqlUpdateHisEvento = "update hist_eventos set radi_nume_radi = $numRadicadoPadre 
                where radi_nume_radi = $numRadicadoPadreAnt"; 
        $this->db->conn->execute($sqlUpdateHisEvento);    
        
        $sqlSgdDirDirecciones = "update sgd_dir_drecciones set radi_nume_radi = $numRadicadoPadre  where radi_nume_radi =  $numRadicadoPadreAnt";   
        $this->db->conn->execute($sqlSgdDirDirecciones);

        $sqlInformados = "update informados set radi_nume_radi = $numRadicadoPadre 
    where radi_nume_radi =  $numRadicadoPadreAnt";    
        $this->db->conn->execute($sqlInformados);  
        

         $sqlAnexoCamNombre = "select id, anex_codigo, anex_nomb_archivo from anexos 
                where anex_radi_nume = $numRadicadoPadre";
         $rsAnexoCamNombre = $this->db->conn->query($sqlAnexoCamNombre); 
         
         $directorioAno  = substr($numRadicadoPadreAnt, 0, 4);
         $depeRadiPadre = ltrim(substr($numRadicadoPadreAnt, 4, $this->noDigitosDep), '0');

         $directorioAnoNuevo  = substr($numRadicadoPadre, 0, 4);
         $depeRadiPadreNuevo = ltrim(substr($numRadicadoPadre, 4, $this->noDigitosDep), '0');

         while(!$rsAnexoCamNombre->EOF ) { 
             $idAnexoCN = $rsAnexoCamNombre->fields["ID"];
             $anexCodigoCN = $rsAnexoCamNombre->fields["ANEX_CODIGO"];
             $anexCodigoCN = substr($anexCodigoCN, -5);
             $auxNuevoAnexCodigo =  $numRadicadoPadre . $anexCodigoCN;
             
             $extCodigoCn = explode('.', $rsAnexoCamNombre->fields["ANEX_NOMB_ARCHIVO"]);
             
             if($extCodigoCn[1] == 'pdf') {
                $auxNuevoAnexCodigoExt = $numRadicadoPadre. $anexCodigoCN . '.' . $extCodigoCn[1];
                $auxNuevoAnexCodigoExtTxt = $numRadicadoPadre. $anexCodigoCN . '.txt';
             } else {
                $auxNuevoAnexCodigoExt = '1' .$numRadicadoPadre . '_' . $anexCodigoCN . '.' . $extCodigoCn[1];
            }


             $sqlAnexoCamNombreUpdate = "update anexos 
                set anex_codigo = '$auxNuevoAnexCodigo', 
                    anex_nomb_archivo = '$auxNuevoAnexCodigoExt' 
                     where id = $idAnexoCN";

            $directorioAnt     = $rutaRaiz . '/bodega/' . $directorioAno . '/' . $depeRadiPadre . '/docs/' . 
                    $rsAnexoCamNombre->fields["ANEX_NOMB_ARCHIVO"];
            $directorioNuevo   = $rutaRaiz .  '/bodega/' . $directorioAnoNuevo . '/' .
                                    $depeRadiPadreNuevo . '/docs/' . $auxNuevoAnexCodigoExt;


            rename($directorioAnt, $directorioNuevo);
            if($extCodigoCn[1] == 'pdf') {
                $directorioAntTxt     = $rutaRaiz . '/bodega/' . $directorioAno . '/' . $depeRadiPadre . '/docs/' . $rsAnexoCamNombre->fields["ANEX_CODIGO"] . '.txt';   
                $directorioNueTxt     = $rutaRaiz . '/bodega/' . $directorioAnoNuevo . '/' . $depeRadiPadreNuevo . '/docs/' . $auxNuevoAnexCodigoExtTxt;                
                rename($directorioAntTxt, $directorioNueTxt);
            }                                    

            $this->db->conn->execute($sqlAnexoCamNombreUpdate);                      

             $rsAnexoCamNombre->MoveNext();
         }

        $sqlAgregarBanderaBorrador = "update radicado set is_borrador = false where 
            radi_nume_radi = " . $numRadicadoPadre;   
        $this->db->conn->execute($sqlAgregarBanderaBorrador);  

        $sqlBorrarTemporal = "delete from radicado where id = $radTemporal and radi_nume_radi = $radTemporal";
        $this->db->conn->execute($sqlBorrarTemporal);          


    }


    function newRadicadoRepetido($radicado) {
        $siExiste = false;
        $sql = "SELECT *
                FROM RADICADO
                WHERE RADI_NUME_RADI = ".$radicado;

        $rs = $this->db->conn->query($sql);

        if(!$rs->EOF){
            //$siExiste = $rs->fields['RADI_NUME_RADI'];
            $siExiste = true;
        }

        return $siExiste;
    }

    function generateNewRadicado($SecName, $tpRad) {
        do {
            $secNew=$this->db->conn->nextId($SecName);

            if($secNew==0){
                $secNew=$this->db->conn->nextId($SecName);
                if($secNew==0) die("<hr><b><font color=red><center>Error no genero un Numero de Secuencia<br>SQL: $secNew</center></font></b><hr>");
            }

            $newRadicado = date("Y") . str_pad($this->dependencia,$this->noDigitosDep,"0",STR_PAD_LEFT) . str_pad($secNew, $this->noDigitosRad, "0", STR_PAD_LEFT) . $tpRad;    
        } while ($this->newRadicadoRepetido($newRadicado));

        return $newRadicado;
    }


    function updateRadicado($radicado, $radgplthUpdate = null){

        if(trim($this->radiCuentai)) $recordR["RADI_CUENTAI"]    = "$this->radiCuentai";
        $recordR["EESP_CODI"]       = empty($this->eespCodi)? 0 : $this->eespCodi ;
        if(trim($this->mrecCodi))$recordR["MREC_CODI"]       = $this->mrecCodi;
        if(trim($this->db->conn->DBDate($this->radiFechOfic)))$recordR["RADI_FECH_OFIC"]  = $this->db->conn->DBDate($this->radiFechOfic);
        if(trim($this->radigplis)) $recordR["RADI_PAIS"]       = "'".$this->radigplis."'";
        if(trim($this->raAsun))$recordR["RA_ASUN"]         = "'".$this->raAsun."'";
        if(trim($this->descAnex)) $recordR["RADI_DESC_ANEX"]  = "'".$this->descAnex."'";
        $recordR["RADI_NUME_RADI"]  = $radicado;
        // $recordR["SGD_APLI_CODI"]   = $this->sgd_apli_codi;

        if(!empty($this->nofolios)){
            $recordR["RADI_NUME_FOLIO"] = $this->nofolios;
        }

        if($this->tdocCodi=="null"){
            $this->tdocCodi = 0;
            $recordR["TDOC_CODI"] = $this->tdocCodi;
        }else{
            if ($this->tdocCodi != 'noactualizar'){
                $recordR["TDOC_CODI"]       = $this->tdocCodi;
            }
        }

        if(!empty($this->noanexos)){
            $recordR["RADI_NUME_ANEXO"] = $this->noanexos;
        }else {
            $recordR["RADI_NUME_ANEXO"] = 0;
        }

        if(!empty($this->empTrans))
            $recordR["EMP_TRANSPORTADORA"]  = $this->empTrans;
        else 
            $recordR["EMP_TRANSPORTADORA"] = 0;

        $recordR["RADI_NUME_GUIA"]  = "'$this->guia'";
        $recordR["RADI_DATO_001"]  = "'$this->radi_dato_001'";
        $recordR["RADI_DATO_002"]  = "'$this->radi_dato_002'";

        // Linea para realizar radicacion Web de archivos pdf
        if(!empty($radgplthUpdate) && $radPathUpdate != ""){
            $archivogplth = explode(".", $radPathUpdate);
            // Sacando la extension del archivo
            $extension = array_pop($archivogplth);
            if($extension == "pdf"){
                $recordR["RADI_PATH"] = "'" . $radgplthUpdate . "'";
            }
        }
        //Si el numero el radicado esta en fisico
        if ($_SESSION["varEstaenfisico"] == 1){

            $_esta_fisico = $this->esta_fisico;
            if ($_esta_fisico == ''){
                $_esta_fisico = 'null';
            }
            $recordR["ESTA_FISICO"] =$_esta_fisico;
        }

        if(!empty($this->usuaFirma)) {
            $recordR["RADI_USUA_FIRMA"] = $this->usuaFirma;
            $recordR["RADI_DEPE_FIRMA"] = $this->depeFirma;        
        }  else {
            $recordR["RADI_USUA_FIRMA"] = 'null';
            $recordR["RADI_DEPE_FIRMA"] = 'null';                    
        }

        if($this->radiPath) $recordR["RADI_PATH"] = "'" . $this->radiPath . "'";
        $recordR["RADI_NUME_RADI"]  = $radicado;        

        $insertSQL = $this->db->conn->Replace("RADICADO", $recordR, "RADI_NUME_RADI", $autoquote = true);
        return $insertSQL;

    }




    /** FUNCION ANEXOS IMPRESOS RADICADO
     * Busca los anexos de un radicado que se encuentran impresos.
     * @param $radicado int Contiene el numero de radicado a Buscar
     * @return Arreglo con los anexos impresos
     * Fecha de creaci�n: 10-gplosto-2006
     * Creador: Supersolidaria
     * Fecha de modificaci�n:
     * Modificador:
     */
    function getRadImpresos($radicado)
    {
        $sqlImp = "SELECT A.RADI_NUME_SALIDA
                   FROM ANEXOS A, RADICADO R
                   WHERE A.ANEX_RADI_NUME=R.RADI_NUME_RADI
                   AND ( A.ANEX_ESTADO=3 OR A.ANEX_ESTADO=4 )
                   AND R.RADI_NUME_RADI = ".$radicado;
        // print $sqlImp;
        $rsImp = $this->db->conn->query( $sqlImp );

        if ( $rsImp->EOF )
        {
            $arrAnexos[0] = 0;
        }
        else
        {
            $e = 0;
            while( $rsImp && !$rsImp->EOF )
            {
                $arrAnexos[ $e ] = $rsImp->fields['RADI_NUME_SALIDA'];
                $e++;
                $rsImp->MoveNext();
            }
        }
        return $arrAnexos;
    }


    /** FUNCION DATOS DE UN RADICADO
     * Busca los datos de un radicado.
     * @param $radicado int Contiene el numero de radicado a Buscar
     * @return Arreglo con los datos del radicado
     * Fecha de creaci�n: 29-gplosto-2006
     * Creador: Supersolidaria
     * Fecha de modificaci�n:
     * Modificador:
     */
    function getDatosRad( $radicado )
    {
        $query  = 'SELECT RAD.RADI_FECH_RADI, RAD.RADI_PATH, TPR.SGD_TPR_DESCRIP,';
        $query .= ' RAD.RA_ASUN';
        $query .= ' FROM RADICADO RAD';
        $query .= ' LEFT JOIN SGD_TPR_TPDCUMENTO TPR ON TPR.SGD_TPR_CODIGO = RAD.TDOC_CODI';
        $query .= ' WHERE RAD.RADI_NUME_RADI = '.$radicado;
        // print $query;
        $rs = $this->db->conn->query( $query );

        $arrDatosRad['fechaRadicacion'] = $rs->fields['RADI_FECH_RADI'];
        $arrDatosRad['ruta'] = $rs->fields['RADI_PATH'];
        $arrDatosRad['tipoDocumento'] = $rs->fields['SGD_TPR_DESCRIP'];
        $arrDatosRad['asunto'] = $rs->fields['RA_ASUN'];

        return $arrDatosRad;
    }

    /** Funcion que trae la dependencia de extraccion de la secuencia de la dependencia de la session Activa.
     */
    function getDependenciaSecuencia($tpRadicado){
        $depeCodi = $this->dependenciaRadicacion;
        $campoSecuencia = strtoupper("depe_rad_tp$tpRadicado");
        $iSql = "select $campoSecuencia  from dependencia where depe_codi=$depeCodi";
        $rs = $this->db->conn->query($iSql);

        $dependenciaBaseSec = $rs->fields[$campoSecuencia];
        return $dependenciaBaseSec;
    }

    function getDependenciaSecuenciaPadre(){
        $depeCodi = $this->dependenciaRadicacion;

        $iSql = "select
                    DEPE_CODI_PADRE
                 from dependencia
                 where depe_codi=$depeCodi";

        $rs = $this->db->conn->query($iSql);

        $dependenciaBaseSec = $rs->fields['DEPE_CODI_PADRE'];
        return $dependenciaBaseSec;
    }


    /**
     * Metodo que inserta direcciones de un radicado.
     * Usa la tabla SGD_DIR_DRECCIONES
     * @autor 12/2009 Fundacion Correlibre
     *        07/2009 adaptacion DNP por Jairo Losada
     * @version Orfeo 3.8.0
     * @param $tipoAccion numeric Indica 0--> es un parametro
     * de Radicado Nuevo o 1-> Que es una modificacion a la Existente.
     * @param $idDirecciones Si esta variable llega, se duplica un remitente de la tabla sgd_dir_drecciones segun el identificador unico de la tabla sgd_dir_drecicones "id"
     **/

    function insertDireccion($radiNumeRadi, $dirTipo,$tipoAccion, $idDirecciones=null){
        if($tipoAccion==0) {
            $nextval = $this->db->conn->nextId("sec_dir_drecciones");
            $this->dirCodigo = $nextval;
        }

        if($idDirecciones){
            $iSql = "  select *  from sgd_dir_drecciones where id=$idDirecciones";
            $rsDir= $this->db->query($iSql);
            if(!$rsDir->EOF){
                $this->oemCodigo = $rsDir->fields["SGD_OEM_CODIGO"];
                $this->ciuCodigo = $rsDir->fields["SGD_CIU_CODIGO"];
                $this->espCodigo = $rsDir->fields["SGD_ESP_CODIGO"];
                $this->funCodigo = $rsDir->fields["SGD_FUN_CODIGO"];
                $this->grbNombresUs = $rsDir->fields["SGD_DIR_NOMREMDES"];
                $this->ccDocumento  = $rsDir->fields["SGD_DIR_DOC"];
                $this->muniCodi   =  $rsDir->fields["MUNI_CODI"];
                $this->dpto_tmp1   = $rsDir->fields["DPTO_CODI"];
                $this->idPais   =    $rsDir->fields["ID_PAIS"];
                $this->idCont      = $rsDir->fields["ID_CONT"];
                $this->direccion   = $rsDir->fields["SGD_DIR_DIRECCION"];
                $this->dirTelefono = $rsDir->fields["SGD_DIR_TELEFONO"];
                $this->dirMail   =   $rsDir->fields["SGD_DIR_MAIL"];
                $this->dirTipo = 1;
                $this->dirNombre   = $rsDir->fields["SGD_DIR_NOMBRE"];
            }
        }
        $codFuncionario = $this->funCodigo;
        if($codFuncionario==0 or $codFuncionario==null){$codFuncionario=1;}
        //$this->db->conn->debug = true;
        $this->dirTipo = $dirTipo;
        if(!$this->oemCodigo) $this->oemCodigo = 0;
        $record = array();
        $this->trdCodigo = trim(str_replace("'","", $this->trdCodigo));
        //if($this->trdCodigo or $this->trdCodigo <> "" ) $record['SGD_TRD_CODIGO'] = $this->trdCodigo; else $record['SGD_TRD_CODIGO'] = 0;
        if($this->grbNombresUs) $record['SGD_DIR_NOMREMDES'] = $this->grbNombresUs;
        if($this->ccDocumento) $record['SGD_DIR_DOC']    = $this->ccDocumento;
        if($this->muniCodi) $record['MUNI_CODI']      = $this->muniCodi;
        if($this->dpto_tmp1) $record['DPTO_CODI']      = $this->dpto_tmp1;
        if($this->idgplis || $this->idPais) $record['ID_PAIS']        = $this->idPais;
        if($this->idCont) $record['ID_CONT']        = $this->idCont;
        if($this->funCodigo) $record['SGD_DOC_FUN']    = $codFuncionario;
        if($this->oemCodigo) $record['SGD_OEM_CODIGO'] = $this->oemCodigo;
        if($this->ciuCodigo)$record['SGD_CIU_CODIGO'] = $this->ciuCodigo;
        if($this->espCodigo) $record['SGD_ESP_CODI']   = $this->espCodigo;
        $record['RADI_NUME_RADI'] = $radiNumeRadi;
        //$record['SGD_SEC_CODIGO'] = 0;
        if($this->direccion) $record['SGD_DIR_DIRECCION'] = $this->direccion;
        if($this->dirTelefono) $record['SGD_DIR_TELEFONO'] = $this->dirTelefono;
        if($this->dirMail) $record['SGD_DIR_MAIL']   = $this->dirMail;
        if($this->dirTipo and $tipoAccion==0) $record['SGD_DIR_TIPO']   = $this->dirTipo;
        if($this->dirCodigo) $record['SGD_DIR_CODIGO'] = $this->dirCodigo;
        if($this->dirNombre) $record['SGD_DIR_NOMBRE'] = $this->dirNombre;

        $ADODB_COUNTRECS = true;
        //$insertSQL = $this->db->insert("SGD_DIR_DRECCIONES", $record, "true");
        if($tipoAccion==0){
            $insertSQL = $this->db->conn->Replace("SGD_DIR_DRECCIONES",
                $record,
                array('RADI_NUME_RADI','SGD_DIR_TIPO'),
                $autoquote = true);
            $insertSQL = "ddddddddd ddccccwww ";
        }else{
            $recordWhere['RADI_NUME_RADI'] = $radiNumeRadi;
            $recordWhere['SGD_DIR_TIPO']   = $dirTipo;
            $insertSQL = $this->db->update("SGD_DIR_DRECCIONES",
                $record,
                $recordWhere);
        }

        if(!$insertSQL) {
            $this->errorNewRadicado .= "<hr><b><font color=red>Error no se inserto sobre sgd_dir_drecciones<br>SQL:". $this->db->querySql .">> $insertSQL </font></b><hr>";
            $insertSQL =-1;
        }else{
            $this->errorNewRadicado .= "<hr><b><font color=green>0: Ok </font></b><hr>";
            $insertSQL =1;
        }

        return $insertSQL;
    }

    function borrarBorrador($numeBorrador) {
        $isql = "UPDATE radicado SET radi_depe_actu = 999, radi_usua_actu = 1, carp_codi=12, carp_per=1,SGD_EANU_CODIGO=1 WHERE radi_nume_radi = $numeBorrador";
        $this->db->conn->execute($isql);          
    }


} // Fin de Class Radicacion
?>
