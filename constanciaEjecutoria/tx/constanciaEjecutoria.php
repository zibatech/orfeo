<?php
/**
 * @author JOHANS GONZALEZ MONTERO 
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright
*/


class ConstanciaEjecutoria{
    
    private $db;

    function __construct($db){
        $this->db = $db;
    }

    /** 
     * Funcion que pregunta si el funcionario tiene el rol proyector para generar una solicitud desde
     * una entidad
    */
    function esRolProyector($depeCodi, $usuaCodi) {
        $sql = "SELECT count(*) FROM public.usuario u
            JOIN public.autm_membresias me on me.autu_id = u.id
            JOIN public.autg_grupos gr on gr.id = me.autg_id
            WHERE gr.nombre = 'Proyector constancia ejecutoria' AND
                u.depe_codi = " . $depeCodi . " AND u.usua_codi = " . $usuaCodi;

        $rs = $this->db->query($sql);
        $count = $rs->fields["COUNT"];

        if($count > 0) 
            return true;
        else    
            return false;
    }

    /** 
     * Funcion que pregunta si el funcionario tiene el rol revisor  en constancia ejecutoria
    */
    function esRolRevisor($depeCodi, $usuaCodi) {
        $sql = "SELECT count(*) FROM public.usuario u
            JOIN public.autm_membresias me on me.autu_id = u.id
            JOIN public.autg_grupos gr on gr.id = me.autg_id
            WHERE gr.nombre = 'Revisor constancia ejecutoria' AND
                u.depe_codi = " . $depeCodi . " AND u.usua_codi = " . $usuaCodi;

        $rs = $this->db->query($sql);
        $count = $rs->fields["COUNT"];

        if($count > 0) 
            return true;
        else    
            return false;
    }

    /** 
     * Funcion que pregunta si el funcionario tiene el rol aprobador  en constancia ejecutoria
    */
    function esRolAprobador($depeCodi, $usuaCodi) {
        $sql = "SELECT count(*) FROM public.usuario u
            JOIN public.autm_membresias me on me.autu_id = u.id
            JOIN public.autg_grupos gr on gr.id = me.autg_id
            WHERE gr.nombre = 'Aprobador constancia ejecutoria' AND
                u.depe_codi = " . $depeCodi . " AND u.usua_codi = " . $usuaCodi;

        $rs = $this->db->query($sql);
        $count = $rs->fields["COUNT"];

        if($count > 0) 
            return true;
        else    
            return false;
    }

    /** 
     * Funcion que pregunta si el funcionario tiene el rol firmante  en constancia ejecutoria
    */
    function esRolFirmante($depeCodi, $usuaCodi) {
        $sql = "SELECT count(*) FROM public.usuario u
            JOIN public.autm_membresias me on me.autu_id = u.id
            JOIN public.autg_grupos gr on gr.id = me.autg_id
            WHERE gr.nombre = 'Firmante constancia ejecutoria' AND
                u.depe_codi = " . $depeCodi . " AND u.usua_codi = " . $usuaCodi;

        $rs = $this->db->query($sql);
        $count = $rs->fields["COUNT"];

        if($count > 0) 
            return true;
        else    
            return false;
    }

    /**
     * Funcion que retorna el nombre del funcionario con rol Revisor
     */
    function nombreRolRevisor(){
        $sql = "SELECT u.usua_nomb FROM public.usuario u
                JOIN public.autm_membresias me on me.autu_id = u.id
                JOIN public.autg_grupos gr on gr.id = me.autg_id
                WHERE gr.nombre = 'Revisor constancia ejecutoria'";
        $rs = $this->db->query($sql);
        $nombre = "";     
        while($rs && !$rs->EOF){
            $nombre = $rs->fields["USUA_NOMB"];
            break;
        }     
        return $nombre;      
    }

    /**
     * Funcion que se encarga de precargar los datos basicos para radicar una solicitud de constancia
     */
    function buscarDataResolucion($resolucion){
        $sql = "SELECT  cast(radi_fech_radi as date) 
            FROM public.radicado where radi_nume_radi = '$resolucion' and cast(radi_nume_radi as text) like '202%'" ;
        $rs = $this->db->query($sql);
        $data = array();
        if($rs && !$rs->EOF){
            $radiFechRadi = $rs->fields["RADI_FECH_RADI"];
            array_push($data, $radiFechRadi);
            $sql = "SELECT sgd_dir_tdoc, sgd_dir_doc
            FROM public.sgd_dir_drecciones where radi_nume_radi = '$resolucion'";
            $rs = $this->db->query($sql);
            array_push($data, $rs->fields["SGD_DIR_TDOC"]);
            array_push($data, $rs->fields["SGD_DIR_DOC"]);            
            return $data;
        } else {
            return $data;
        }
    }

    /**
     *  Funcion que se encarga de guardar una o varias solicitudessolicitud
     */
    function guardarSolicitud($data, $depeCodi, $usuaCodi){
        
        $sql = "select nextval('public.secr_sgd_ce_grupo'::regclass)";
        $rs = $this->db->query($sql);
        $count = $rs->fields["NEXTVAL"];    

        for ($i = 0; $i < count($data); $i++) {
            $sql = "INSERT INTO public.sgd_ce_constancia(
                        grupo, item, no_id_ple, resolucion_inicial, fecha_acto, identificacion, razon_social, tipo_notificacion, fecha_acuse, 
                        presenta_recuro, reso_apelacion, fecha_apelacion, reso_reposicion, fecha_reposicion, recurso_queja_revoc, 
                        reso_queja_revoc, tipo_notificacion_final, fecha_notificacion_final, expediente, fecha_notificacion_ultimo, fecha_ejecutoria,
                         depe_codi, usua_codi, comentario, fecha_noti_resp, ubicacion, depe_codi_pro, usua_codi_pro, numero_constancia, id_estado,fecha_solicitud, 
                         depe_codi_fir, usua_codi_fir, depe_codi_rev, usua_codi_rev)
                        VALUES ($count, ($i + 1), '" .$data[$i][0] . "', '" .$data[$i][1] . "', 
                            '" .$data[$i][2] . "', '" .$data[$i][3] . "', '" .$data[$i][4] . "',
                             '" .$data[$i][5] . "', '" .$data[$i][6] . "', '" .$data[$i][7] . "', 
                             '" .$data[$i][8] . "', '" .$data[$i][9] . "', '" .$data[$i][10] . "', 
                             '" .$data[$i][11] . "', '" .$data[$i][12] . "', '" .$data[$i][13] . "', 
                             '" .$data[$i][14] . "', '" .$data[$i][15] . "', '" .$data[$i][16] . "', 
                             '', '', '" .$data[$i][17] . "', '" .$data[$i][18] . "', '', '', '', 0, 0, 0, 1, (select now()),0,0,0,0);";
        
            $this->db->conn->execute($sql); 
            $this->agergarHistorialCom($count, ($i + 1), $data[$i][1], 
                    $depeCodi, $usuaCodi, 'Se registra la solicitud');
        }
    }  

    /**
     * Lista las solicitudes de un funcionario listas para enviar
     */
    function verSolicitudPorEnviar($depeCodi, $usuaCodi){
        $sql = "select * from public.sgd_ce_constancia 
                where depe_codi = " . $depeCodi . " and usua_codi = " . $usuaCodi . " 
                and id_estado = 1 order by id ";

        $rs = $this->db->query($sql);    
        return  $rs;  
    }

    /**
     * Enviar grupo de peticiones del proyector a revision 
     */
    function proyectorARevision($depeCodi, $usuaCodi){

        $sql = "SELECT grupo, item, resolucion_inicial
                    FROM public.sgd_ce_constancia where depe_codi = " . $depeCodi . " and usua_codi = " . $usuaCodi . " and id_estado = 1";
        $rs = $this->db->query($sql);
        
        while($rs && !$rs->EOF){
            $grupo = $rs->fields["GRUPO"];
            $item  = $rs->fields["ITEM"];
            $resolucionInicial = $rs->fields["RESOLUCION_INICIAL"];
            $this->agergarHistorialCom($grupo, $item, $resolucionInicial, 
                $depeCodi, $usuaCodi, 'Se envía a Revisión');
            $rs->MoveNext();
        }

        $sql = "update public.sgd_ce_constancia set id_estado = 2, comentario = '' 
            where depe_codi = " . $depeCodi . " and usua_codi = " . $usuaCodi . " and id_estado = 1";
        $this->db->conn->execute($sql); 
    } 

    /**
     * Borrar solicitudes sin haberlas enviando a Notificaciones
     */
    function borrarSolicitudPri($depeCodi, $usuaCodi){

        $sql = "SELECT grupo, item, resolucion_inicial
                    FROM public.sgd_ce_constancia where depe_codi = " . $depeCodi . " and usua_codi = " . $usuaCodi . " and id_estado = 1";
        $rs = $this->db->query($sql);
        
        while($rs && !$rs->EOF){
            $grupo = $rs->fields["GRUPO"];
            $item  = $rs->fields["ITEM"];
            $resolucionInicial = $rs->fields["RESOLUCION_INICIAL"];
            $this->agergarHistorialCom($grupo, $item, $resolucionInicial, 
                $depeCodi, $usuaCodi, 'Se elimina la solicitud');
            $rs->MoveNext();
        }

        $sql = "update public.sgd_ce_constancia set id_estado = 0 
            where depe_codi = " . $depeCodi . " and usua_codi = " . $usuaCodi . " and id_estado = 1";
        $this->db->conn->execute($sql); 
    }     
    
    /**
     * Lista las solicitudes que han sido devueltas
     */
    function verSolicitudDevuelta($depeCodi, $usuaCodi){
        $sql = "select * from public.sgd_ce_constancia 
                where depe_codi = " . $depeCodi . " and usua_codi = " . $usuaCodi . " 
                and id_estado = 6 order by id ";

        $rs = $this->db->query($sql);    
        return  $rs;  
    }

    /**
     * Elimina una solicitud por parte del funcionario solicitante
     */
    function eliminarSolicitud($idSolicitud, $depeCodi, $usuaCodi){
        $sql = "update public.sgd_ce_constancia set id_estado = 0 where id = " . $idSolicitud;
        $this->db->conn->execute($sql); 
        $this->agergarHistorial($idSolicitud, $depeCodi, $usuaCodi, 'Se ha eliminado la solicitud');
    }

     /**
     * Cambia una solicitud a estado leido
     */
    function cambiarEstadoLeido($idSolicitud){
        $sql = "update public.sgd_ce_constancia set alerta = true where id = " . $idSolicitud;
        $this->db->conn->execute($sql); 
    }   

     /**
     * Cambia una solicitud a estado no leido
     */
    function cambiarEstadoNoLeido($idSolicitud){
        $sql = "update public.sgd_ce_constancia set alerta = false where id = " . $idSolicitud;
        $this->db->conn->execute($sql); 
    }     

    /**
     * Lista solciitud por ID
     */
    function verSolicitudPorId($idSolicitud){
        $sql = "select * from public.sgd_ce_constancia 
                where id = " . $idSolicitud;

        $rs = $this->db->query($sql);    
        $data = array();
        if($rs && !$rs->EOF){
            array_push($data, $rs->fields["NO_ID_PLE"], $rs->fields["RESOLUCION_INICIAL"] ,$rs->fields["FECHA_ACTO"],
            $rs->fields["IDENTIFICACION"], $rs->fields["RAZON_SOCIAL"], $rs->fields["TIPO_NOTIFICACION"], $rs->fields["FECHA_ACUSE"],
            $rs->fields["PRESENTA_RECURO"], $rs->fields["RESO_APELACION"], $rs->fields["FECHA_APELACION"], $rs->fields["RESO_REPOSICION"],
            $rs->fields["FECHA_REPOSICION"], $rs->fields["RECURSO_QUEJA_REVOC"], $rs->fields["RESO_QUEJA_REVOC"], $rs->fields["TIPO_NOTIFICACION_FINAL"],
            $rs->fields["FECHA_NOTIFICACION_FINAL"], $rs->fields["EXPEDIENTE"], $rs->fields["FECHA_NOTIFICACION_ULTIMO"], $rs->fields["FECHA_EJECUTORIA"],
            $rs->fields["DEPE_CODI"], $rs->fields["USUA_CODI"], $rs->fields["COMENTARIO"], $rs->fields["FECHA_NOTI_RESP"],
            $rs->fields["UBICACION"], $rs->fields["DEPE_CODI_PRO"], $rs->fields["USUA_CODI_PRO"], $rs->fields["NUMERO_CONSTANCIA"],
            $rs->fields["FECHA_SOLICITUD"] );
        }
        return $data;
    }    

    function editarSolicitud($data){

        $sql = "UPDATE public.sgd_ce_constancia
                SET no_id_ple='" . $data[1] . "', resolucion_inicial='" . $data[2] . "', fecha_acto='" . $data[3] . "', 
                identificacion='" . $data[4] . "', razon_social='" . $data[5] . "', tipo_notificacion='" . $data[6] . "', 
                fecha_acuse='" . $data[7] . "', presenta_recuro='" . $data[8] . "', reso_apelacion='" . $data[9] . "', 
                fecha_apelacion='" . $data[10] . "', reso_reposicion='" . $data[11] . "', fecha_reposicion='" . $data[12] . "', 
                recurso_queja_revoc='" . $data[13] . "', reso_queja_revoc='" . $data[14] . "', tipo_notificacion_final='" . $data[15] . "', 
                fecha_notificacion_final='" . $data[16] . "', expediente='" . $data[17] . "'
                    WHERE id = " . $data[0];
    
        $this->db->conn->execute($sql);    
    }

    /**
     * Enviar grupo de peticiones por Id  proyector a revision despues de corregir
     */
    function proyectorARevisionEditado($idSolicitud, $depeCodi, $usuaCodi){
        $sql = "update public.sgd_ce_constancia set id_estado = 2, comentario = '', 
                    alerta = false where id = " . $idSolicitud;
        $this->db->conn->execute($sql); 
        $this->agergarHistorial($idSolicitud, $depeCodi, $usuaCodi, 'Se envía a revisión despues de edición');
    } 

    /**
     * Obtiene las dependencias para filtar por estado
     */
    function obtenerDepenenciaPorEstado($estado){
        $sql = "";
        if($estado > 0) {
            if($estado == 2) {
                $sql = "select distinct(depe_codi) from 
                public.sgd_ce_constancia where id_estado = " . $estado . " or id_estado = 7 order by depe_codi";
            } elseif($estado == 3){
                $sql = "select distinct(depe_codi) from 
                public.sgd_ce_constancia where id_estado = " . $estado . " or id_estado = 8 order by depe_codi";
            }else {
                $sql = "select distinct(depe_codi) from 
                public.sgd_ce_constancia where id_estado = " . $estado . " order by depe_codi";
            }
        } else{
            $sql = "select distinct(depe_codi) from 
                    public.sgd_ce_constancia order by depe_codi";            
        }
        $rs = $this->db->query($sql);    
        $data = array();
        while($rs && !$rs->EOF){
            $nomDep = $this->obtenerNombreDependencia($rs->fields["DEPE_CODI"]);
            array_push($data, $rs->fields["DEPE_CODI"], $nomDep);
            $rs->MoveNext();
        }
        return $data;
    }

    /**
     * Obtiene los grupos para filtar por estado
     */
    function obtenerGrupoPorEstado($estado){
        $sql = "";
        if($estado > 0) {
            if($estado == 2) {
                $sql = "select distinct(grupo) from 
                public.sgd_ce_constancia where id_estado = " . $estado . " or id_estado = 7 order by grupo";  
            } elseif($estado == 3){
                $sql = "select distinct(grupo) from 
                public.sgd_ce_constancia where id_estado = " . $estado . " or id_estado = 8 order by grupo";  
            } else {
                $sql = "select distinct(grupo) from 
                public.sgd_ce_constancia where id_estado = " . $estado . " order by grupo";        
            }
        } else {
            $sql = "select distinct(grupo) from 
                     public.sgd_ce_constancia order by grupo";         
        }
        $rs = $this->db->query($sql);    
        $data = array();
        while($rs && !$rs->EOF){
            array_push($data, $rs->fields["GRUPO"]);
            $rs->MoveNext();
        }
        return $data;
    } 
    
    /**
     * Obtiene las solicitudes para el revisor con filtro
     */
    function verSolicitudRolRevisor($inFechaInicio, $inFechaFinal, $seDependencia, $seGrupo){

        $sql = "select * from public.sgd_ce_constancia 
                    where id_estado = 2 ";
        if($inFechaInicio != "")
            $sql .= " and fecha_solicitud >= '" . $inFechaInicio . "'"; 
        if($inFechaFinal != "")
            $sql .= " and (fecha_solicitud - INTERVAL '1 DAY') <= '" . $inFechaFinal . "'";  
        if($seDependencia != 0)
            $sql .= " and depe_codi = " . $seDependencia;          
        if($seGrupo != 0)
            $sql .= " and grupo = " . $seGrupo;          
        $sql .= " order by id";                
        $rs = $this->db->query($sql);    
        return  $rs;      
    }

    /**
     * Obtiene las solicitudes para el revisor devuelta con filtro
     */
    function verSolicitudRolRevisorDevuelta($inFechaInicio, $inFechaFinal, $seDependencia, $seGrupo){

        $sql = "select * from public.sgd_ce_constancia 
                    where id_estado = 7 ";
        if($inFechaInicio != "")
            $sql .= " and fecha_solicitud >= '" . $inFechaInicio . "'"; 
        if($inFechaFinal != "")
            $sql .= " and (fecha_solicitud - INTERVAL '1 DAY') <= '" . $inFechaFinal . "'";  
        if($seDependencia != 0)
            $sql .= " and depe_codi = " . $seDependencia;          
        if($seGrupo != 0)
            $sql .= " and grupo = " . $seGrupo;          
        $sql .= " order by id";                
        $rs = $this->db->query($sql);    
        return  $rs;      
    } 

    /**
     * Obtiene las solicitudes para el aprobador con filtro
     */
    function verSolicitudRolAprobador($inFechaInicio, $inFechaFinal, $seDependencia, $seGrupo){

        $sql = "select * from public.sgd_ce_constancia 
                    where id_estado = 3 ";
        if($inFechaInicio != "")
            $sql .= " and fecha_solicitud >= '" . $inFechaInicio . "'"; 
        if($inFechaFinal != "")
            $sql .= " and (fecha_solicitud - INTERVAL '1 DAY') <= '" . $inFechaFinal . "'";  
        if($seDependencia != 0)
            $sql .= " and depe_codi = " . $seDependencia;          
        if($seGrupo != 0)
            $sql .= " and grupo = " . $seGrupo;          
        $sql .= " order by id";                
        $rs = $this->db->query($sql);    
        return  $rs;      
    }    

    /**
     * Obtiene las solicitudes para el aprobador devuelta con filtro
     */
    function verSolicitudRolAprobadorDevuelta($inFechaInicio, $inFechaFinal, $seDependencia, $seGrupo){

        $sql = "select * from public.sgd_ce_constancia 
                    where id_estado = 8 ";
        if($inFechaInicio != "")
            $sql .= " and fecha_solicitud >= '" . $inFechaInicio . "'"; 
        if($inFechaFinal != "")
            $sql .= " and (fecha_solicitud - INTERVAL '1 DAY') <= '" . $inFechaFinal . "'";  
        if($seDependencia != 0)
            $sql .= " and depe_codi = " . $seDependencia;          
        if($seGrupo != 0)
            $sql .= " and grupo = " . $seGrupo;          
        $sql .= " order by id";                
        $rs = $this->db->query($sql);    
        return  $rs;      
    }        

    function editarSolicitudFunNoti($data){

        $sql = "UPDATE public.sgd_ce_constancia
                SET no_id_ple='" . $data[1] . "', resolucion_inicial='" . $data[2] . "', fecha_acto='" . $data[3] . "', 
                identificacion='" . $data[4] . "', razon_social='" . $data[5] . "', tipo_notificacion='" . $data[6] . "', 
                fecha_acuse='" . $data[7] . "', presenta_recuro='" . $data[8] . "', reso_apelacion='" . $data[9] . "', 
                fecha_apelacion='" . $data[10] . "', reso_reposicion='" . $data[11] . "', fecha_reposicion='" . $data[12] . "', 
                recurso_queja_revoc='" . $data[13] . "', reso_queja_revoc='" . $data[14] . "', tipo_notificacion_final='" . $data[15] . "', 
                fecha_notificacion_final='" . $data[16] . "', expediente='" . $data[17] . "' , fecha_notificacion_ultimo='" . $data[18] . "' , fecha_ejecutoria='" . $data[19] . "' 
                WHERE id = " . $data[0];
    
        $this->db->conn->execute($sql);    
    }    

    /**
     * Obtiene el nombre de una dependencia por el depe_codi
     */
    function obtenerNombreDependencia($depeCodi) {
        $sql = "select depe_nomb from public.dependencia where depe_codi= " . $depeCodi;
        $rs = $this->db->query($sql);    
        if($rs && !$rs->EOF){
            return $rs->fields["DEPE_NOMB"];
        } else {
            return "";
        }
    }

    /**
     * Obtiene el nombre de usuario por su usuaCodi y Depecodi
     */
    function obtenerNombreUsuario($usuaCodi, $depeCodi) {
        $sql = "select usua_nomb from public.usuario where usua_codi= " . $usuaCodi . " and
                    depe_codi=" . $depeCodi;
        $rs = $this->db->query($sql);    
        if($rs && !$rs->EOF){
            return $rs->fields["USUA_NOMB"];
        } else {
            return "";
        }
    } 
    
    /**
     * Obtiene el nombre del estado por id
     */
    function obtenerNombreEstado($idEstado){
        $sql = "SELECT estado FROM public.sgd_ce_estado where id = " . $idEstado;
        $rs = $this->db->query($sql);    
        if($rs && !$rs->EOF){
            return $rs->fields["ESTADO"];
        } else {
            return "";
        }
    }

    /**
     * Funcion que revisa si las solicitudes pertenecen al mismo grupo
     */
    function validarMismoGrupo($idSolicitudArray){
        $numGrupo = -1;
        $depeCodi = 0;
        $usuaCodi = 0;
        $item = " Item ";
        for ($i = 0; $i < count($idSolicitudArray); $i++) {
            $sql = "select grupo, item, depe_codi, usua_codi, fecha_notificacion_ultimo, fecha_ejecutoria, id_estado, ubicacion from public.sgd_ce_constancia where id= " . $idSolicitudArray[$i];
            $rs = $this->db->query($sql);   

                if($rs->fields["FECHA_NOTIFICACION_ULTIMO"] == "" || $rs->fields["FECHA_EJECUTORIA"] == "") {
                    return 'f2';
                }

                if($rs->fields["UBICACION"] == "") {
                    return 'f4';
                }                

            if($numGrupo == -1) {
                $numGrupo = $rs->fields["GRUPO"];
                $depeCodi = $rs->fields["DEPE_CODI"];
                $usuaCodi = $rs->fields["USUA_CODI"];
                $item     = $item . $rs->fields["ITEM"] . ",";
            }
            else {
                $item     = $item . $rs->fields["ITEM"]  . ",";
                if($numGrupo != $rs->fields["GRUPO"])
                    return 'f';
            }    
        }
        $nombreDependecia = $this->obtenerNombreDependencia($depeCodi);
        $nombreFuncionario = $this->obtenerNombreUsuario($usuaCodi, $depeCodi);
        return $numGrupo . $item . "-" .$nombreDependecia . "-" . $nombreFuncionario;
    }

    /**
     * Funcion que revisa si las solicitudes pertenecen al mismo grupo para devolver
     */
    function validarMismoGrupoRetornar($idSolicitudArray){
        $numGrupo = -1;
        $depeCodi = 0;
        $usuaCodi = 0;
        $item = " Item ";
        for ($i = 0; $i < count($idSolicitudArray); $i++) {
            $sql = "select grupo, item, depe_codi, usua_codi, fecha_notificacion_ultimo, fecha_ejecutoria, id_estado, ubicacion from public.sgd_ce_constancia where id= " . $idSolicitudArray[$i];
            $rs = $this->db->query($sql);   

            if($numGrupo == -1) {
                $numGrupo = $rs->fields["GRUPO"];
                $depeCodi = $rs->fields["DEPE_CODI"];
                $usuaCodi = $rs->fields["USUA_CODI"];
                $item     = $item . $rs->fields["ITEM"] . ",";
            }
            else {
                $item     = $item . $rs->fields["ITEM"]  . ",";
                if($numGrupo != $rs->fields["GRUPO"])
                    return 'f';
            }    
        }
        $nombreDependecia = $this->obtenerNombreDependencia($depeCodi);
        $nombreFuncionario = $this->obtenerNombreUsuario($usuaCodi, $depeCodi);
        return $numGrupo . $item . "-" .$nombreDependecia . "-" . $nombreFuncionario;
    }    

    /**
     * Funcion que retorna un grupo de solicitudes
     */
    function retonarSolicitud($idSolicitudArray, $comentario, $depeCodi, $usuaCodi){
        for ($i = 0; $i < count($idSolicitudArray); $i++) {
            $sql = "update public.sgd_ce_constancia	set aprobacion = false, alerta = false, id_estado = 6, depe_codi_rev = 0, usua_codi_rev = 0, depe_codi_pro = 0, usua_codi_pro = 0, 
                        ubicacion = '', comentario = '" . $comentario . "' 
                        where id = " . $idSolicitudArray[$i];
            $this->db->conn->execute($sql);              
            $this->agergarHistorial($idSolicitudArray[$i], $depeCodi, $usuaCodi, 'Se retorna a funcionario con el comentario: ' . $comentario);
        }
    }

    /**
     * Funcion que retorna un grupo de solicitudes a un Revisor
     */
    function retonarSolicitudaRevisor($idSolicitudArray, $comentario, $depeCodi, $usuaCodi){
        for ($i = 0; $i < count($idSolicitudArray); $i++) {
            $sql = "update public.sgd_ce_constancia	set aprobacion = false, alerta = false, id_estado = 7, ubicacion = '', depe_codi_rev = 0, usua_codi_rev = 0, depe_codi_pro = 0, usua_codi_pro = 0, 
            comentario = '" . $comentario . "' 
                        where id = " . $idSolicitudArray[$i];
            $this->db->conn->execute($sql);  
            $this->agergarHistorial($idSolicitudArray[$i], $depeCodi, $usuaCodi, 'Se retorna a revisor con el comentario: ' . $comentario);            
        }
    }    

    /**
     * Funcion que retorna un grupo de solicitudes al aprobador
     */
    function retonarSolicitudaProbador($idSolicitudArray, $comentario, $depeCodi, $usuaCodi){
        for ($i = 0; $i < count($idSolicitudArray); $i++) {
            $sql = "update public.sgd_ce_constancia	set aprobacion = false, alerta = false, id_estado = 8, ubicacion = '', depe_codi_pro = 0, usua_codi_pro = 0, 
            comentario = '" . $comentario . "' 
                        where id = " . $idSolicitudArray[$i];
            $this->db->conn->execute($sql);   
            $this->agergarHistorial($idSolicitudArray[$i], $depeCodi, $usuaCodi, 'Se retorna a aprobador con el comentario: ' . $comentario);           
        }
    }      

    /**
     * Enviar de solicitudes entre roles del grupo de Notifcaciones
     */
    function envioRolesNoti($idSolicitud, $estado, $depeCodi, $usuaCodi){
        $comentario = '';
        if($estado == 3) {
            $sql = "update public.sgd_ce_constancia set id_estado = " . $estado . ", comentario = '', 
            alerta = false, depe_codi_rev = '" . $depeCodi . "', usua_codi_rev = '" . $usuaCodi . "' where id = " . $idSolicitud;
            $comentario = 'Envío de Revisor a Aprobador';
        } elseif($estado == 4) {

            $sqlAux = "SELECT id_estado from public.sgd_ce_constancia where  id = " . $idSolicitud;
            $rs = $this->db->query($sqlAux);
            $estadoAnte = $rs->fields["ID_ESTADO"];
            if($estadoAnte == 2 || $estadoAnte == 7){
                $sql = "update public.sgd_ce_constancia set id_estado = " . $estado . ", comentario = '', ubicacion = '',  
                alerta = false, depe_codi_rev = '" . $depeCodi . "', usua_codi_rev = '" . $usuaCodi . "' where id = " . $idSolicitud;
                $comentario = 'Envío de Revisor a Firmante';
            } else {
                $sql = "update public.sgd_ce_constancia set id_estado = " . $estado . ", comentario = '', ubicacion = '', 
                alerta = false where id = " . $idSolicitud;
                $comentario = 'Envío de Aprobador a Firmante';
            }
        } elseif($estado == 5){
            $sql = "update public.sgd_ce_constancia set id_estado = " . $estado . ", comentario = '', 
            alerta = false, depe_codi_fir = '" . $depeCodi . "', usua_codi_fir = '" . $usuaCodi . "' where id = " . $idSolicitud;
            $comentario = 'Se realiza firma del documento';
        } else {
            $sql = "update public.sgd_ce_constancia set id_estado = " . $estado . ", comentario = '', 
            alerta = false where id = " . $idSolicitud;
        }
        $this->db->conn->execute($sql); 
        $this->agergarHistorial($idSolicitud, $depeCodi, $usuaCodi, $comentario);
    } 

    /**
     * Creaar el pdf de la solicitud y lo guarda en Bodega
     */
    function crearPDF($idSolicitud, $pdf, $rutaRaiz, $ABSOL_PATH) {

        $sql = "select numero_constancia from public.sgd_ce_constancia where id = " . $idSolicitud;
        $rs = $this->db->query($sql);
        $numeroConstansia = $rs->fields["NUMERO_CONSTANCIA"];
        $date = date('Y');
        if($numeroConstansia == 0) {
            $sql = "select nextval('public.secr_sgd_ce_num_constancia'::regclass)";
            $rs = $this->db->query($sql);
            $nuevo= $rs->fields["NEXTVAL"];
            $numeroConstansia = $date . $nuevo;

            $sql = "update public.sgd_ce_constancia set numero_constancia = " . intval($numeroConstansia) . " 
                        where id = " . $idSolicitud;
            $this->db->conn->execute($sql); 
        } 

        $sql = "select grupo, item, resolucion_inicial, fecha_acto, razon_social, presenta_recuro, 
                    fecha_notificacion_ultimo, fecha_ejecutoria, depe_codi, usua_codi,
                    depe_codi_pro, usua_codi_pro, depe_codi_rev, usua_codi_rev 
                    from public.sgd_ce_constancia where id = " . $idSolicitud;
        $rs = $this->db->query($sql);                    

        $pdf->SetFont ('helvetica', '', 11 , '', 'default', true );
        $pdf->SetMargins(22, 51, 22);
        $pdf->AddPage();     
        $resoInicial = $rs->fields["RESOLUCION_INICIAL"];
        $fechaActo = $rs->fields["FECHA_ACTO"];
        $razonSocial = $rs->fields["RAZON_SOCIAL"];

        if($rs->fields["PRESENTA_RECURO"] == "Ninguno"){
            $interpuso = "No";
        } else {
            $interpuso = "Si";
        }
        $ultimoActo = $rs->fields["FECHA_NOTIFICACION_ULTIMO"];
        $fechaEjecutoria = $rs->fields["FECHA_EJECUTORIA"];

        $fechaGlob = explode(" ", date("d F Y")); 
        $_mes = array(
            "January"   => "Enero",
            "February"  => "Febrero",
            "March"     => "Marzo",
            "April"     => "Abril",
            "May"       => "Mayo",
            "June"      => "Junio",
            "July"      => "Julio",
            "August"    => "Agosto",
            "September" => "Septiembre",
            "October"   => "Octubre",
            "November"  => "Noviembre",
            "December"  => "Diciembre"
        );   
        $dia = $fechaGlob[0];
        $mes = $_mes[$fechaGlob[1]];
        $anho = $fechaGlob[2];

        if($dia == 1)
            $fecha= "Dada en Bogot&aacute; D.C., el " . $dia . " d&iacute;a del mes " . $mes . " de " . $anho . ".";
        else
            $fecha= "Dada en Bogot&aacute; D.C., a los " . $dia . " d&iacute;as del mes " . $mes . " de " . $anho . ".";
        $proyecto = $this->obtenerNombreUsuario($rs->fields["USUA_CODI"], $rs->fields["DEPE_CODI"]);
        //$reviso = $this->nombreRolRevisor();
        $reviso = $this->obtenerNombreUsuario($rs->fields["USUA_CODI_REV"], $rs->fields["DEPE_CODI_REV"]);
        $aprobo = $this->obtenerNombreUsuario($rs->fields["USUA_CODI_PRO"], $rs->fields["DEPE_CODI_PRO"]);;
        $radicado = $rs->fields["GRUPO"] . "-" . $rs->fields["ITEM"];

        $directorio = $ABSOL_PATH . "/bodega/" . date('Y') . "/" . $rs->fields["DEPE_CODI"] ."/constancia";
        if(!is_dir($directorio)){    
            mkdir($directorio, 0755, true);
        }

        $pdf->writeHTML($this->plantilla($resoInicial,$fechaActo, $razonSocial, $interpuso, $ultimoActo, 
            $fechaEjecutoria, $fecha, $proyecto, $reviso, $aprobo, $radicado), true, false, true, false, '');
        $pdf_result = $pdf->Output($rutaRaiz ."/bodega/" . date('Y') . "/" . $rs->fields["DEPE_CODI"] . "/constancia/". $radicado . ".pdf", 'F');

        $sql = "update public.sgd_ce_constancia set ubicacion = '/bodega/" .  date('Y') . "/" . $rs->fields["DEPE_CODI"] .  "/constancia/" . $radicado . ".pdf' 
                    where id = " . $idSolicitud;
        $this->db->conn->execute($sql); 
    }

    /**
     * Retorna la ubicacion de la constancia por id
     */
    function obtenerUbicacionConstancia($idSolicitud){
        $sql = "select ubicacion from public.sgd_ce_constancia where id = " . $idSolicitud;
        $rs = $this->db->query($sql);
        return $rs->fields["UBICACION"];
    }

    /**
     * Lista las solicitudes que han sido devueltas general por filtro
     */
    function verSolicitudDevueltaGeneral($inFechaInicio, $inFechaFinal, $seDependencia, $seGrupo){
        $sql = "select * from public.sgd_ce_constancia 
                where id_estado = 6 ";
        if($inFechaInicio != "")
            $sql .= " and fecha_solicitud >= '" . $inFechaInicio . "'"; 
        if($inFechaFinal != "")
            $sql .= " and (fecha_solicitud - INTERVAL '1 DAY') <= '" . $inFechaFinal . "'";  
        if($seDependencia != 0)
            $sql .= " and depe_codi = " . $seDependencia;          
        if($seGrupo != 0)
            $sql .= " and grupo = " . $seGrupo;          
        $sql .= " order by id";                
        $rs = $this->db->query($sql);   
        
        $data = array();
        $i =0;
        while($rs && !$rs->EOF){

            $nombreDependecia = $this->obtenerNombreDependencia($rs->fields["DEPE_CODI"]);
            $nombreFuncionario = $this->obtenerNombreUsuario($rs->fields["USUA_CODI"], $rs->fields["DEPE_CODI"]);
            //$nombreEstado = $this->obtenerNombreEstado($rs->fields["ID_ESTADO"]);

            $data[$i] = array($rs->fields["FECHA_SOLICITUD"], $rs->fields["GRUPO"], $rs->fields["ITEM"], $nombreDependecia, $nombreFuncionario,
            $rs->fields["NO_ID_PLE"], $rs->fields["RESOLUCION_INICIAL"] ,$rs->fields["FECHA_ACTO"],
            $rs->fields["IDENTIFICACION"], $rs->fields["RAZON_SOCIAL"], $rs->fields["TIPO_NOTIFICACION"], $rs->fields["FECHA_ACUSE"],
            $rs->fields["PRESENTA_RECURO"], $rs->fields["RESO_APELACION"], $rs->fields["FECHA_APELACION"], $rs->fields["RESO_REPOSICION"],
            $rs->fields["FECHA_REPOSICION"], $rs->fields["RECURSO_QUEJA_REVOC"], $rs->fields["RESO_QUEJA_REVOC"], $rs->fields["TIPO_NOTIFICACION_FINAL"],
            $rs->fields["FECHA_NOTIFICACION_FINAL"], $rs->fields["EXPEDIENTE"], $rs->fields["FECHA_NOTIFICACION_ULTIMO"], $rs->fields["FECHA_EJECUTORIA"],
            $rs->fields["COMENTARIO"]);
            $i++;
            $rs->MoveNext();
        }
        return $data;                  
    }

    /**
     * Lista las solicitudes a nivel general por filtro para grupo de notificaciones
     */
    function verConstanciasGeneralNoti($inFechaInicio, $inFechaFinal, $seDependencia, $seGrupo, $estadoDife){
        $sql = "select * from public.sgd_ce_constancia where id_estado <> " . $estadoDife;
        if($inFechaInicio != "")
            $sql .= " and fecha_solicitud >= '" . $inFechaInicio . "'"; 
        if($inFechaFinal != "")
            $sql .= " and (fecha_solicitud - INTERVAL '1 DAY') <= '" . $inFechaFinal . "'";  
        if($seDependencia != 0)
            $sql .= " and depe_codi = " . $seDependencia;          
        if($seGrupo != 0)
            $sql .= " and grupo = " . $seGrupo;          
        $sql .= " order by id";                
        $rs = $this->db->query($sql);   
        
        $data = array();
        $i =0;
        while($rs && !$rs->EOF){

            $nombreDependecia = $this->obtenerNombreDependencia($rs->fields["DEPE_CODI"]);
            $nombreFuncionario = $this->obtenerNombreUsuario($rs->fields["USUA_CODI"], $rs->fields["DEPE_CODI"]);
            $nombreEstado = $this->obtenerNombreEstado($rs->fields["ID_ESTADO"]);
            $nombreAprobador = "";
            if($rs->fields["DEPE_CODI_PRO"] != 0 && $rs->fields["USUA_CODI_PRO"] != 0)
                $nombreAprobador = $this->obtenerNombreUsuario($rs->fields["USUA_CODI_PRO"], $rs->fields["DEPE_CODI_PRO"]);

            $data[$i] = array($rs->fields["ID"], $rs->fields["FECHA_SOLICITUD"], $rs->fields["GRUPO"], $rs->fields["ITEM"], $nombreDependecia, $nombreFuncionario,
            $rs->fields["NO_ID_PLE"], $rs->fields["RESOLUCION_INICIAL"] ,$rs->fields["FECHA_ACTO"],
            $rs->fields["IDENTIFICACION"], $rs->fields["RAZON_SOCIAL"], $rs->fields["TIPO_NOTIFICACION"], $rs->fields["FECHA_ACUSE"],
            $rs->fields["PRESENTA_RECURO"], $rs->fields["RESO_APELACION"], $rs->fields["FECHA_APELACION"], $rs->fields["RESO_REPOSICION"],
            $rs->fields["FECHA_REPOSICION"], $rs->fields["RECURSO_QUEJA_REVOC"], $rs->fields["RESO_QUEJA_REVOC"], $rs->fields["TIPO_NOTIFICACION_FINAL"],
            $rs->fields["FECHA_NOTIFICACION_FINAL"], $rs->fields["EXPEDIENTE"], $rs->fields["FECHA_NOTIFICACION_ULTIMO"], $rs->fields["FECHA_EJECUTORIA"],
            $rs->fields["COMENTARIO"], $rs->fields["FECHA_NOTI_RESP"], $nombreAprobador, $rs->fields["NUMERO_CONSTANCIA"],$nombreEstado, $rs->fields["UBICACION"]);
            $i++;
            $rs->MoveNext();
        }
        return $data;                 
    }    

    /**
     * Funcion que valida si una solicitud esta lista para aprobar
     */
    function estaListoAprobacion($idSolicitud){
        $sql = "select fecha_notificacion_ultimo, fecha_ejecutoria from public.sgd_ce_constancia
                    where id = " . $idSolicitud;
        $rs = $this->db->query($sql);
        if($rs->fields["FECHA_NOTIFICACION_ULTIMO"] == "" || $rs->fields["FECHA_EJECUTORIA"] == "" 
                || $rs->fields["FECHA_NOTIFICACION_ULTIMO"] == NULL || $rs->fields["FECHA_EJECUTORIA"] == NULL){
            return "f";
        }           
        return "t";
    }

    /**
     * Funcion que aprueba una solicitud o un grupo de solicitudes
     */
    function aprobarSolicitud($idSolicitudArray, $depeCodi, $usuaCodi){
        for ($i = 0; $i < count($idSolicitudArray); $i++) {
            $sql = "update public.sgd_ce_constancia 
                    set aprobacion = true, depe_codi_pro = '" . $depeCodi . "', usua_codi_pro = '" . $usuaCodi . "' where id = " . $idSolicitudArray[$i];
            $this->db->conn->execute($sql);  
            $this->agergarHistorial($idSolicitudArray[$i], $depeCodi, $usuaCodi, 'Solicitud aprobada');            
        }
    }

   /**
     * Funcion que revisa si las solicitudes pertenecen al mismo grupo y estan aprobadas
     */
    function validarMismoGrupoAprobador($idSolicitudArray){
        $numGrupo = -1;
        $depeCodi = 0;
        $usuaCodi = 0;
        $item = " Item ";
        for ($i = 0; $i < count($idSolicitudArray); $i++) {
            $sql = "select grupo, item, depe_codi, usua_codi, fecha_notificacion_ultimo, fecha_ejecutoria, aprobacion, ubicacion from public.sgd_ce_constancia where id= " . $idSolicitudArray[$i];
            $rs = $this->db->query($sql);   

            if($rs->fields["FECHA_NOTIFICACION_ULTIMO"] == "" || $rs->fields["FECHA_EJECUTORIA"] == "") {
                return 'f2';
            }

            if($rs->fields["APROBACION"] == "f") {
                return 'f3';
            }

            if($rs->fields["UBICACION"] == "") {
                return 'f4';
            }            

            if($numGrupo == -1) {
                $numGrupo = $rs->fields["GRUPO"];
                $depeCodi = $rs->fields["DEPE_CODI"];
                $usuaCodi = $rs->fields["USUA_CODI"];
                $item     = $item . $rs->fields["ITEM"] . ",";
            }
            else {
                $item     = $item . $rs->fields["ITEM"]  . ",";
                if($numGrupo != $rs->fields["GRUPO"])
                    return 'f';
            }    
        }
        $nombreDependecia = $this->obtenerNombreDependencia($depeCodi);
        $nombreFuncionario = $this->obtenerNombreUsuario($usuaCodi, $depeCodi);
        return $numGrupo . $item . "-" .$nombreDependecia . "-" . $nombreFuncionario;
    }

    /**
     * Funcion que revisa si las solicitudes pertenecen al mismo grupo
     */
    function validarMismoGrupoGenerar($idSolicitudArray){
        $numGrupo = -1;
        $depeCodi = 0;
        $usuaCodi = 0;
        $item = " Item ";
        for ($i = 0; $i < count($idSolicitudArray); $i++) {
            $sql = "select grupo, item, depe_codi, usua_codi, fecha_notificacion_ultimo, fecha_ejecutoria, id_estado, ubicacion from public.sgd_ce_constancia where id= " . $idSolicitudArray[$i];
            $rs = $this->db->query($sql);   

                if($rs->fields["FECHA_NOTIFICACION_ULTIMO"] == "" || $rs->fields["FECHA_EJECUTORIA"] == "") {
                    return 'f2';
                }

            if($numGrupo == -1) {
                $numGrupo = $rs->fields["GRUPO"];
                $depeCodi = $rs->fields["DEPE_CODI"];
                $usuaCodi = $rs->fields["USUA_CODI"];
                $item     = $item . $rs->fields["ITEM"] . ",";
            }
            else {
                $item     = $item . $rs->fields["ITEM"]  . ",";
                if($numGrupo != $rs->fields["GRUPO"])
                    return 'f';
            }    
        }
        $nombreDependecia = $this->obtenerNombreDependencia($depeCodi);
        $nombreFuncionario = $this->obtenerNombreUsuario($usuaCodi, $depeCodi);
        return $numGrupo . $item . "-" .$nombreDependecia . "-" . $nombreFuncionario;
    }    

    /**
     * Obtiene las solicitudes para el aprobador con filtro
     */
    function verSolicitudRolFirmante($inFechaInicio, $inFechaFinal, $seDependencia, $seGrupo){

        $sql = "select * from public.sgd_ce_constancia 
                    where id_estado = 4 ";
        if($inFechaInicio != "")
            $sql .= " and fecha_solicitud >= '" . $inFechaInicio . "'"; 
        if($inFechaFinal != "")
            $sql .= " and (fecha_solicitud - INTERVAL '1 DAY') <= '" . $inFechaFinal . "'";  
        if($seDependencia != 0)
            $sql .= " and depe_codi = " . $seDependencia;          
        if($seGrupo != 0)
            $sql .= " and grupo = " . $seGrupo;          
        $sql .= " order by id";                
        $rs = $this->db->query($sql);    
        return  $rs;      
    }    

    /**
     * Funcion que se encarga de finalizar el estado de las solicitudes
     */
    function finalizarSolicitud($idSolicitud, $depeCodi, $usuaCodi){
        $sql = "update public.sgd_ce_constancia set comentario = '', depe_codi_fir = " . $depeCodi . ", 
            usua_codi_fir = " . $usuaCodi . ", fecha_noti_resp = (select now()), id_estado = 5 where id = " . $idSolicitud;
        $this->db->conn->execute($sql);    
    }

    /**
     * Funcion que se encarga de firma un documento
     */
    function firmaDigital($idSolicitud, $ABSOL_PATH, $P12_FILE, $clave, $depeCodi, $usuaCodi) {
        $ubicacion = $this->obtenerUbicacionConstancia($idSolicitud);
        $data = explode("/", $ubicacion);

        chdir($ABSOL_PATH . '/' . $data[1] . '/' . $data[2] . '/' . $data[3] . '/' . $data[4]);
        $commandFirmado='java -jar '.$ABSOL_PATH.'/include/jsignpdf-1.6.4/JSignPdf.jar ' . $ABSOL_PATH . $ubicacion . " -kst PKCS12 -ksf " . $P12_FILE . ' -ksp ' . $clave . ' --font-size 7 -r \'Firmado al Radicar en CRA\' -V -v -llx 0 -lly 0 -urx 550 -ury 27';  
    
        $out = null;
        $ret = null;
        $inf = exec($commandFirmado,$out,$ret);  
        if($ret == 0) {
            $data = explode(".", $ubicacion);
            $sql = "update public.sgd_ce_constancia	set ubicacion = '" . $data[0] . "_signed.pdf' where id = " . $idSolicitud;
            $this->db->conn->execute($sql); 
            unlink($ABSOL_PATH . $ubicacion);
            $this->agergarHistorial($idSolicitud, $depeCodi, $usuaCodi, 'Se agrega firma digital');                    
        }  
        
    }    

    /**
     * Crea el anexo al o los expedientes solicitados
     */
    function anexarExpediente($idSolicitud, $ABSOL_PATH, $digitosDep, $dependencia, $codUsuario, $usDoc) {
        
        $sql = "select resolucion_inicial, expediente, depe_codi_fir, usua_codi_fir FROM public.sgd_ce_constancia where id = " . $idSolicitud;
        $resp = $this->db->query($sql);    
        $expediente = $resp->fields['EXPEDIENTE'];
        $resoInicial = $resp->fields['RESOLUCION_INICIAL'];
        $exp = explode(",", $expediente); 
        $login = $this->obtenerNombreUsuario($resp->fields["USUA_CODI_FIR"], $resp->fields["DEPE_CODI_FIR"]);
        for ($i = 0; $i < count($exp); $i++) {

            $sqlCount = "select count(*) from sgd_sexp_secexpedientes where sgd_exp_numero='" .  $exp[$i] . "'";
            $respAux = $this->db->query($sqlCount);   
            if($respAux->fields['COUNT'] > 0) {

                
                    $sql="select count(*) cons from sgd_exp_anexos where  exp_anex_nomb_archivo like '" . $exp[$i] . "%'";
                    $respAux= $this->db->conn->query($sql);
                    $conse = $respAux->fields['CONS']+1;

                    $id=rand(10, 9999);
                    $anextipo = 7;
                    $size = '4645334';
                    $namefile = "CE " . $resoInicial;
                    $path_nomb = $exp[$i] . "_" . $id .".pdf";
                    $hashs = '';
                    $path = '';
                    $fisico = "VIRTUAL";
                    $carpeta = '';
                    $subexp = '';
                    $tpdoc = 0;
            
                    $sqlI = "INSERT INTO public.sgd_exp_anexos(
                        id, exp_anex_tipo, exp_anex_tamano, exp_anex_creador, exp_anex_desc, exp_anex_nomb_archivo, exp_anex_borrado, exp_anex_radi_fech, exp_anex_hash,
                        exp_numero, exp_anex_path, exp_consecutivo, exp_fisico, exp_carpeta, exp_subexp,exp_tpdoc)
                        values ( {$id},{$anextipo}, '{$size}','{$login}', '{$namefile}',  '{$path_nomb}', 'N',  CURRENT_TIMESTAMP , '{$hashs}', '" . $exp[$i] . "','{$path}','{$conse}' ,'{$fisico}','{$carpeta}','{$subexp}',$tpdoc )";
            
                    $this->db->conn->execute($sqlI);

                    $sqlI = "INSERT INTO public.sgd_hfld_histflujodoc(
                        sgd_fexp_codigo, sgd_hfld_fech, sgd_exp_numero, radi_nume_radi, usua_doc, usua_codi, depe_codi, sgd_ttr_codigo, sgd_hfld_observa)
                        VALUES (0, CURRENT_TIMESTAMP, '" . $exp[$i] . "', " . $resoInicial .", '" . $usDoc . "', " . $codUsuario .", " . $dependencia .", 91, 'INCLUIR ANEXO DE EXPEDIENTE')";
                    $this->db->conn->execute($sqlI);

                    $ubicacion = $this->obtenerUbicacionConstancia($idSolicitud);
                    $depe_dir = substr( $exp[$i], 4, $digitosDep);
                    $depe_dir = ltrim($depe_dir, "0");
                    $uploadDir = $ABSOL_PATH . "bodega/" .substr($exp[$i],0,4)."/".$depe_dir."/docs/" . $path_nomb;
                    copy($ABSOL_PATH . $ubicacion, $uploadDir);

                    $this->agergarHistorial($idSolicitud, $dependencia, $codUsuario, 'Se agrega como anexo al expdiente: ' . $exp[$i]);                    
            }
        }
    }

    /**
     * Se ven las contancias finalizadas por usuario
     */
    function verCostanciasPorUsuairo($inFechaInicio, $inFechaFinal, $depeCodi, $usuaCodi){
        $sql = "select * from public.sgd_ce_constancia 
                    where id_estado = 5 and fecha_solicitud >= '" . $inFechaInicio . "' 
                    and (fecha_solicitud - INTERVAL '1 DAY') <= '" . $inFechaFinal . "'  
                    and depe_codi = " . $depeCodi . " and usua_codi = " . $usuaCodi . " order by id";    
        $rs = $this->db->query($sql);    
        return  $rs;    
    }

    /**
     * Enviar la plantilla base
     */
    function plantilla($resoInicial, $fechaActo, $razonSocial, $interpuso, $ultimoActo, 
                            $fechaEjecutoria, $fecha, $proyecto, $reviso, $aprobo, $radicado){
        return '<p style="text-align:center"><b>CONSTANCIA DE EJECUTORIA</b></p>

        <p style="text-align:center"><b>LA COORDINADORA DEL GRUPO GESTI&Oacute;N DE NOTIFICACIONES Y COMUNICACIONES DE LA DIRECCI&Oacute;N ADMINISTRATIVA DE LA SUPERINTENDENCIA NACIONAL DE SALUD</b></p>
        
        <p style="text-align:center">En ejercicio de las funciones consagradas en el numeral 4, art&iacute;culo 11 de la Resoluci&oacute;n 20218000013221-6 del 24 de septiembre de 2021, en concordancia con el art&iacute;culo 1 de la Resoluci&oacute;n 2022910010001120-6 de 2022 o la que lo modifique,</p>
        
        <p style="text-align:center"><b>CERTIFICA QUE:</b></p>
        
        <p style="text-align:justify">Los actos administrativos que se identifican a continuaci&oacute;n quedaron en firme de acuerdo con lo establecido en el art&iacute;culo 87 de la Ley 1437 del 18 de enero de 2011:</p>
        
        <table border="1" cellspacing="0" class="Table" style="border-collapse:collapse; border:solid windowtext 1.0pt; margin-left:3.75pt; width:100%">
            <tbody>
                <tr>
                    <td style="background-color:#c6e0b4">
                    <p style="text-align:center"><span style="font-size:8px"><span style="color:black">N&Uacute;MERO RESOLUCI&Oacute;N INICIAL</span></span></p>
                    </td>
                    <td style="background-color:#c6e0b4">
                    <p style="text-align:center"><span style="font-size:8px"><span style="color:black">FECHA DEL ACTO</span></span></p>
                    </td>
                    <td style="background-color:#c6e0b4">
                    <p style="text-align:center"><span style="font-size:8px"><span style="color:black">RAZ&Oacute;N SOCIAL (PERSONA JUR&Iacute;DICA O NATURAL).</span></span></p>
                    </td>
                    <td style="background-color:#c6e0b4">
                    <p style="text-align:center"><span style="font-size:8px"><span style="color:black">INTERPUSO RECURSO</span></span></p>
                    </td>
                    <td style="background-color:#c6e0b4">
                    <p style="text-align:center"><span style="font-size:8px"><span style="color:black">FECHA NOTIFICACI&Oacute;N &Uacute;LTIMO ACTO </span></span></p>
                    </td>
                    <td style="background-color:#c6e0b4">
                    <p style="text-align:center"><span style="font-size:8px"><span style="color:black">FECHA EJECUTORIA</span></span></p>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:center"><span style="font-size:8px">' . $resoInicial . '</span></td>
                    <td style="text-align:center"><span style="font-size:8px">' . $fechaActo . '</span></td>
                    <td style="text-align:center"><span style="font-size:8px">' . $razonSocial . '</span></td>
                    <td style="text-align:center"><span style="font-size:8px">' . $interpuso . '</span></td>
                    <td style="text-align:center"><span style="font-size:8px">' . $ultimoActo . '</span></td>
                    <td style="text-align:center"><span style="font-size:8px">' . $fechaEjecutoria . '</span></td>
                </tr>
            </tbody>
        </table>
        
        <p style="text-align:justify">Para la determinaci&oacute;n de la fecha de ejecutoria del acto administrativo, se tuvo en cuenta la informaci&oacute;n reportada por el &aacute;rea solicitante, mediante solicitud No. <b>' . $radicado . '</b> correspondientes a los soportes del acto y de notificaci&oacute;n de este, as&iacute; como si existi&oacute; la interposici&oacute;n de recursos o solicitudes de revocatoria directa.&nbsp;</p>
        
        <p style="text-align:justify">' . $fecha . '</p>
        
        <p>&nbsp;</p>
        
        <p>&nbsp;</p>
        
        <p style="text-align:center">Firmado electrónicamente por<br>        
            <b>IVETH MELISA MARQUEZ MAYORGA</b></p>
        
        <p style="text-align:center"><b>Coordinadora Grupo Gesti&oacute;n de Notificaciones y Comunicaciones</b></p>
        
        <p><span style="font-size:8px">Proyect&oacute;: ' . $proyecto . '<br />
        Revis&oacute;: ' . $reviso . '<br />
        Aprob&oacute;: ' . $aprobo . '</span></p>';
    }

    /**
     * Funcion que evalua si un o un grupo de expedientes existe
     */
    function evaluarExpediente($resolucion, $expeData, $fila = -1) {
        if($fila == -1) {
            $fila = 'única';
        }
        $dataExpediente = explode(",", $expeData);
        $countError = 0;
        $respuesta = '';
        for ($i = 0; $i < count($dataExpediente); $i++) {
            $sql = "select sgd_sexp_estado from public.sgd_sexp_secexpedientes 
                    where sgd_exp_numero = '" . $dataExpediente[$i] . "'";
            $rs = $this->db->query($sql);
 
            if($rs && !$rs->EOF){
                $estado = $rs->fields["SGD_SEXP_ESTADO"];
                if($estado != 0) {
                    $countError++;
                    $respuesta = $respuesta . "Expediente " . $dataExpediente[$i] . " esta anulado o cerrado para resolución: " . $resolucion . " Fila " . $fila . "</br>";
                } 
            } else { 
                $countError++;
                $respuesta = $respuesta .  "Expediente " .  $dataExpediente[$i] . " No existe para resolución: " . $resolucion . " Fila " . $fila . "</br>";
            }
        }

        if($countError > 0) {
            $data = array("error" => $respuesta);
        } else {
            $data = array("ok" => "200");
        }
        return $data;
    }

    /**
     * Funcion que agrega el historial de una operacion por idSolicitud
     */
    function agergarHistorial($idSolicitud, $depeCodi, $usuaCodi, $operacion){

        $sql = 'SELECT grupo, item, resolucion_inicial 
                    FROM public.sgd_ce_constancia where id = ' . $idSolicitud;
        $rs = $this->db->query($sql);  

        $grupo = $rs->fields["GRUPO"];
        $item = $rs->fields["ITEM"];
        $resolucionInicial = $rs->fields["RESOLUCION_INICIAL"];

        $sql = "INSERT INTO public.sgd_ce_historial(
                    grupo, item, fecha, resolucion, operacion, depe_codi, usua_codi)
                    VALUES (" . $grupo .", " . $item . ", CURRENT_TIMESTAMP,
                         '" . $resolucionInicial ."', '" . $operacion ."',
                         " . $depeCodi .", " . $usuaCodi .")";
        $this->db->conn->execute($sql);             
    }

    /**
     * Funcion que agrega el historial por grupo e item
     */
    function agergarHistorialCom($grupo, $item, $resolucionInicial, 
                $depeCodi, $usuaCodi, $operacion){

        $sql = "INSERT INTO public.sgd_ce_historial(
                    grupo, item, fecha, resolucion, operacion, depe_codi, usua_codi)
                    VALUES (" . $grupo .", " . $item . ", CURRENT_TIMESTAMP,
                         '" . $resolucionInicial ."', '" . $operacion ."',
                         " . $depeCodi .", " . $usuaCodi .")";
        $this->db->conn->execute($sql);            
    } 

    /**
     * Funcion que obtiene el historial por idSolicitud
     */
    function obtenerHistorial($idSolicitud){
        $sql = "select 	grupo, item FROM public.sgd_ce_constancia where id = " . $idSolicitud;
        $rs = $this->db->query($sql);  
        $grupo = $rs->fields["GRUPO"];
        $item = $rs->fields["ITEM"];

        $sql = "select his.fecha, his.operacion, usu.usua_nomb
                FROM public.sgd_ce_historial his
                JOIN  public.usuario usu on his.depe_codi = usu.depe_codi and his.usua_codi = usu.usua_codi
                WHERE
                    his.grupo = " . $grupo . " and
                    his.item = " . $item . "
                order by his.fecha desc	";
        $rs = $this->db->query($sql);    
        return  $rs;                    
    }

} 

?>
