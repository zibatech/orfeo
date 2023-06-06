<?php
/**
 * @author Raul Vera P. <raul.vera@supersalud.gov.co>
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright

OrfeoGpl- ArgoGpl  Models are the data definition of SIIM2 Information System
Copyright (C) 2013 Infometrika Ltda - Correlibre.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
#require '/var/www/html/orfeo/processConfig.php);';

class Notificacion {
    /*** Attributes:
     * Clase que maneja los usuarios
     *
     * $dirMail        array Variable que almacena el correo electronico del tercero de un radicado.
     * $dirNombre      array Almacena los nombres de terceros relacionados con un radicado.
     * $dirNomRemDes   array Almacena los nombres completos de los terceros de un radicado.
     */

    var $db;
    var $result;

    function __construct($db){
        $this->db = $db;
    }

    /**
     * Cargar informacion de notificacion para copiar o modificar
     * @param  string numero de radicado
     * @return array informacion de notificacion
     */
    public function cargarNotificacionAntigua($nurad){
        $notificacion = array();
        $query = "SELECT *
                FROM
                SGD_NOTIF_NOTIFICACIONES
                WHERE
                RADI_NUME_RADI = $nurad";

        //$this->db->conn->debug = true;
        $rs = $this->db->conn->query($query);
        //$this->db->conn->debug = false;

        if($rs && !$rs->EOF){
          $notificacion["notifica_codi"] = $rs->fields["SGD_NOTIF_CODIGO"];
          $notificacion["med_public"] = $rs->fields["MPUB_CODI"];
          $notificacion["caracter_adtvo"] = $rs->fields["CADTVO_CODI"];
          $notificacion["siad"] = $rs->fields["SIAD"];
          $notificacion["prioridad"] = $rs->fields["PRIORIDAD"];
        }else{
            return false;
        }

        return $notificacion;
    }

    /**
     * Cargar informacion de las ordenes de la notificacion para copiar o modificar
     * @param  string numero de radicado
     * @return array informacion de notificacion
     */
    public function cargarOrdenesDeNotificacion($radicado){
        $notificacion = array();
        $sql = "SELECT 
                    od.orden_codi as orden_codigo, 
                    sdd.sgd_dir_codigo as dir_codigo
                FROM  
                    ordenacto_dirdreccion od
                INNER JOIN 
                    sgd_dir_drecciones sdd
                ON 
                    od.sgd_dir_codigo = sdd.sgd_dir_codigo
                WHERE
                    sdd.radi_nume_radi = $radicado
                GROUP BY 
                    sdd.sgd_dir_codigo, 
                    od.orden_codi
                ORDER BY 
                    od.orden_codi";

        //$this->db->conn->debug = true;
        $rs = $this->db->conn->query($sql);
        //$this->db->conn->debug = false;

        while(!$rs->EOF){
            $ordenes[$rs->fields["DIR_CODIGO"]][] = $rs->fields["ORDEN_CODIGO"];
            $rs->MoveNext();
        }

        return $ordenes;
    }
    
    public function cargarCamposFormulario($ent, $med_public = null, $caracter_adtvo = null) {
        $tipoDoc = array(
            "4" => "Circular Interna",
            "5" => "Circular Externa",
            "6" => "Resolución",
            "7" => "Auto"
        );

        $query = "SELECT SGD_TPR_CODIGO
                FROM SGD_TPR_TPDCUMENTO
                WHERE SGD_TPR_DESCRIP = '$tipoDoc[$ent]'";

        $rs = $this->db->conn->query($query);

        if($rs && !$rs->EOF){
          $tdoc_codi = $rs->fields["SGD_TPR_CODIGO"];
        }

        $query = "SELECT
                MPUB_DESC, MPUB_CODI
                FROM MEDIO_PUBLICACION
                WHERE MPUB_CODI <> 0
                ORDER BY MPUB_CODI";

        $rs = $this->db->conn->query($query);

        $medioPub = $rs->GetMenu2( "med_public",
                                    $med_public,
                                    '',
                                    false,
                                    "",
                                    "required class='form-control'");

        $query = "SELECT
                CADTVO_DESC, CADTVO_CODI
                FROM CARACTER_ADMINISTRATIVO
                WHERE CADTVO_CODI <> 0
                ORDER BY CADTVO_CODI";

        $rs = $this->db->conn->query($query);

        $caracterAdtvo = $rs->GetMenu2("caracter_adtvo",
                                        $caracter_adtvo,
                                        '',
                                        false,
                                        "",
                                        "required class='form-control'");
        
        return array(
            "tdoc"          => $tdoc_codi, 
            "medioPub"      => $medioPub, 
            "caracterAdtvo" => $caracterAdtvo
        );
    }

    /**
     * Asocia un destinatario con una circular
     * @param datos array Informacion del destinatario
     * @return sgd_notif_codigo int
     */
    public function guardarDestinatarioRadicadoCirculares($datos, $sgd_notif_codigo, $modificar = false) {
        $result = array("status" => null, 
                        "sgd_notif_circ_codi" => null, 
                        "message" => null);
        $esUpdate = "";

        //Modificar o Crea un destinatario
        $registroDestinatario = $this->creaEditaDestinatario($datos, $modificar);

        if(!$registroDestinatario["status"]) {
            $result["status"] = false;
            $result["message"] = $registroDestinatario["message"];
            return $result;
        };

        if (empty($registroDestinatario['sgd_notif_circ_codi'])) {
            //Cuando el formulario de pre-radicacion se edita inmediatamente despues de 
            //haberlo creado, es decir, sin ingresar al formulario desde la bandeja de 
            //notificaciones invocando a destinatariosPorRadicado, el campo notifica_codi  
            //llega vacio por lo que hay que traerlo para no crear un nuevo registro.
            if ($modificar) {
                $query_ = " SELECT sgd_notif_circ_codi 
                            FROM sgd_notif_circulares  
                            WHERE sgd_notif_codigo = ".$sgd_notif_codigo."
                            AND sgd_notif_circ_dest_codi = ".$registroDestinatario['sgd_notif_circ_dest_codi'];

                $rs = $this->db->conn->query($query_);

                if($rs && !$rs->EOF){
                    $nextval = $rs->fields["sgd_notif_circ_codi"];
                    $esUpdate = array("sgd_notif_circ_dest_codi","sgd_notif_codigo","sgd_notif_circ_dest_codi");
                }    
            }
            if (empty($nextval)) {
                $nextval = $this->db->conn->nextId("sgd_notif_circulares_seq");
            }
        } else {
            $nextval = $registroDestinatario['sgd_notif_circ_codi'];
            $esUpdate = array("sgd_notif_circ_dest_codi","sgd_notif_codigo","sgd_notif_circ_dest_codi");
        }

        if ($nextval==-1){
            $result["status"] = false;
            $result["message"] = 'No se encontr&oacute; la secuencia sgd_notif_circulares_seq';
            return $result;
        }

        $datos["orden_iniciada"]    = empty($datos['orden_iniciada'])   ? 'false' : 'true';
        $datos["orden_finalizada"]  = empty($datos['orden_finalizada']) ? 'false' : 'true';

        $record = array();
        $record["sgd_notif_circ_codi"]      = $nextval;
        $record["sgd_notif_codigo"]         = $sgd_notif_codigo;
        $record["sgd_notif_circ_dest_codi"] = $registroDestinatario['sgd_notif_circ_dest_codi'];
        $record["orden_iniciada"]           = $datos['orden_iniciada'];
        $record["orden_finalizada"]         = $datos['orden_finalizada'];

        //Regresa 0 si falla, 1 si efectuo el update y 2 si no se
        //encontro el registro y el insert fue con exito
        $insertSQL = $this->db->conn->Replace(
            "sgd_notif_circulares", 
            $record, 
            $esUpdate, 
            false
        );
        
        if($insertSQL){
            $result["status"] = true; 
            $result["sgd_notif_circ_codi"] = $nextval;
            return $result;
        }else{
            $result["status"] = false;
            $result["message"] = "Error al escribir en sgd_notif_circulares";
            return $result;
        }
    }

    /**
     * Crea o modifica la informaci'on del destinatario de una circular.
     * @param  array todos los campos que se guardar'an en la tabla de notificaciones
     * @return sgd_notif_codigo
     */
    public function creaEditaDestinatario($destinatario, $modificar = false){
        $result = array("status" => null, 
                        "sgd_notif_circ_dest_codi" => null, 
                        "sgd_notif_circ_codi" => null,
                        "message" => null);
        $esUpdate = "";

        list($destinatarios_codi, $circular_codi) = explode("_", $destinatario['destinatarios_codi']);

        if (empty($destinatarios_codi)) {
            //Cuando el formulario de pre-radicacion se edita inmediatamente despues de 
            //haberlo creado, es decir, sin ingresar al formulario desde la bandeja de 
            //notificaciones invocando a destinatariosPorRadicado, el campo destinatario_codi 
            //llega vacio por lo que hay que traerlo para no crear un nuevo registro.
            if ($modificar) {
                $query_ = " SELECT sgd_notif_circ_dest_codi 
                            FROM sgd_notif_circular_destinatario  
                            WHERE sgd_notif_circ_dest_desc = ".$destinatario['destinatarios']." 
                            AND sgd_trad_codigo = ".$destinatario['tpRad'];

                $rs = $this->db->conn->query($query_);

                if($rs && !$rs->EOF){
                    $nextval = $rs->fields["sgd_notif_circ_dest_codi"];
                    $esUpdate = array("sgd_notif_circ_dest_codi","sgd_trad_codigo");
                }    
            }
            if (empty($nextval)) {
                $nextval = $this->db->conn->nextId("sgd_notif_circ_dest_seq");
            }
        } else {
            $nextval = $destinatarios_codi;
            $esUpdate = array("sgd_notif_circ_dest_codi","sgd_trad_codigo");
        }

        if ($nextval==-1){
            $result["status"] = false;
            $result["message"] = 'No se encontr&oacute; la secuencia sgd_notif_circ_dest_seq';
            return $result;
        }

        $record = array();
        $record["sgd_notif_circ_dest_codi"] = $nextval;
        $record["sgd_notif_circ_dest_desc"] = strtoupper(trim($destinatario['destinatarios']));
        $record["sgd_trad_codigo"]          = $destinatario['tpRad'];

        //Regresa 0 si falla, 1 si efectuo el update y 2 si no se
        //encontro el registro y el insert fue con exito
        $insertSQL = $this->db->conn->Replace(
            "sgd_notif_circular_destinatario", 
            $record, 
            $esUpdate, 
            true
        );
        
        if($insertSQL){
            $result["status"] = true; 
            $result["sgd_notif_circ_dest_codi"] = $nextval;
            $result["sgd_notif_circ_codi"] = $circular_codi;
            return $result;
        }else{
            $result["status"] = false;
            $result["message"] = "Error al escribir en sgd_notif_circular_destinatario";
            return $result;
        }
    }

    /**
     * Crea o modifica el registro de la informaci'on de una notificacion.
     * @param  array todos los campos que se guardar'an en la tabla de notificaciones
     * @return sgd_notif_codigo
     */
    public function creaEditaNotificacion($datos, $modificar = false){
        $result = array("status" => null, 
                        "sgd_notif_codigo" => null, 
                        "message" => null);
        $esUpdate = "";

        if (empty($datos['notifica_codi'])) {
            //Cuando el formulario de pre-radicacion se edita inmediatamente despues de 
            //haberlo creado, es decir, sin ingresar al formulario desde la bandeja de 
            //notificaciones llamando usuario->usuarioPorRadicado, el campo notifica_codi  
            //llega vacio por lo que hay que traerlo para no crear un nuevo registro.
            if ($modificar) {
                $query_ = " SELECT sgd_notif_codigo 
                        FROM sgd_notif_notificaciones  
                        WHERE radi_nume_radi = ".$datos['radicado'];

                $rs = $this->db->conn->query($query_);

                if($rs && !$rs->EOF){
                    $nextval = $rs->fields["SGD_NOTIF_CODIGO"];
                    $esUpdate = array("sgd_notif_codigo","radi_nume_radi");
                }    
            }
            if (empty($nextval)) {
                $nextval = $this->db->conn->nextId("sgd_notif_notificaciones_seq");
            }
        } else {
            $nextval = $datos['notifica_codi'];
            $esUpdate = array("sgd_notif_codigo","radi_nume_radi");
        }

        if ($nextval==-1){
            $result["error"] = 'No se encontr&oacute; la secuencia sgd_notif_notificaciones_seq';
            return false;
        }

        $record = array();
        $record["sgd_notif_codigo"] = $nextval;
        $record["radi_nume_radi"]   = $datos['radicado'];
        $record["mpub_codi"]        = $datos['med_public'];
        $record["cadtvo_codi"]      = $datos['caracter_adtvo'];
        $record["prioridad"]        = empty($datos['prioridad'])? 'false' : 'true';

        if (!empty($datos['siad'])) {
            $record["siad"] = $datos['siad'];    
        }

        //Regresa 0 si falla, 1 si efectuo el update y 2 si no se
        //encontro el registro y el insert fue con exito
        $insertSQL = $this->db->conn->Replace(
            "SGD_NOTIF_NOTIFICACIONES", 
            $record, 
            $esUpdate, 
            false
        );
        
        if($insertSQL){
            $result["status"] = true; 
            $result["sgd_notif_codigo"] = $nextval;
            return $result;
        }else{
            $result["status"] = false;
            $result["message"] = "Error al escribir en sgd_notif_notificaciones";
            return $result;
        }
    }

    /**
     * Crea o modifica las ordenes del acto administrativo de una notificacion.
     * @param  array sgd_dir_codigo y orden_codi
     * @return status true si el almacenamiento fue exitoso, false si no exitoso.
     */
    public function creaEditaOrdenesNotificacion($datos, $modificar = false){
        $result = array("status" => null, 
                        "message" => "");
        $esUpdate = "";

        $datos["orden_iniciada"]    = empty($datos['orden_iniciada'])   ? 'false' : 'true';
        $datos["orden_finalizada"]  = empty($datos['orden_finalizada']) ? 'false' : 'true';

        if ($modificar) {
            $esUpdate = array("sgd_dir_codigo", "orden_codi");
        }
        
        //Regresa 0 si falla, 1 si efectuo el update y 2 si no se
        //encontro el registro y el insert fue con exito
        $insertSQL = $this->db->conn->Replace(
            "ORDENACTO_DIRDRECCION", 
            $datos, 
            $esUpdate, 
            false
        );
        
        if($insertSQL){
            $result["status"] = true; 
            return $result;
        }else{
            $result["status"] = false;
            $result["message"] = "Error al escribir en ordenacto_dirdreccion";
            return $result;
        }
    }

    /**
     * Borra las ordenes de una acto administrativo en caso de que el funcionario las
     * hayan desmarcado al momento de modificar el formulario de pre-radicacion.
     * @param  int sgd_dir_codigo
     * @return status 
     */
    public function borrarOrdenesNotificacion($sgd_dir_drecciones_id){
        $result = array("status" => null, 
                        "message" => "");
        $isql = "DELETE FROM ORDENACTO_DIRDRECCION WHERE SGD_DIR_CODIGO = $sgd_dir_drecciones_id";
        $rs = $this->db->conn->query($isql);
        if($rs==-1) {
            $result["status"] = false;
            $result["message"] = "Error al borrar de ordenacto_dirdreccion";
        }
        return $result;
    }

    /**
     * Obtener la descripcion del caracter administrativo a traves del codigo
     * @param  numeric codigo de caracter administrativo
     * @return string descripcion del caracter administrativo
     */
    public function obtenerCaracterAdministrativo($codigo){
        $sql = "SELECT 
                    CADTVO_DESC
                FROM
                    CARACTER_ADMINISTRATIVO
                WHERE
                    CADTVO_CODI = $codigo";

        //$this->db->conn->debug = true;
        $rs = $this->db->conn->query($sql);
        //$this->db->conn->debug = false;

        if($rs && !$rs->EOF){
          return $rs->fields["CADTVO_DESC"];
        }else {
            return false;
        }
    }

    /**
     * Obtener la descripcion del medio de publicacion a traves del codigo
     * @param  numeric codigo de medio de publicacion
     * @return string descripcion del medio de publicacion
     */
    public function obtenerMedioPublicacion($codigo){
        $sql = "SELECT 
                    MPUB_DESC
                FROM
                    MEDIO_PUBLICACION
                WHERE
                    MPUB_CODI = $codigo";

        //$this->db->conn->debug = true;
        $rs = $this->db->conn->query($sql);
        //$this->db->conn->debug = false;

        if($rs && !$rs->EOF){
          return $rs->fields["MPUB_DESC"];
        }else{
            return false;
        }
    }

    /**
     * Obtener la descripcion del medio de envio a traves del codigo
     * @param  numeric codigo de medio de envio
     * @return string descripcion del medio de envio
     */
    public function obtenerMedioEnvio($codigo){
        $sql = "SELECT 
                    MENVIO_DESC
                FROM
                    MEDIO_ENVIO
                WHERE
                    MENVIO_CODI = $codigo";

        //$this->db->conn->debug = true;
        $rs = $this->db->conn->query($sql);
        //$this->db->conn->debug = false;

        if($rs && !$rs->EOF){
          return $rs->fields["MENVIO_DESC"];
        }else{
            return false;
        }
    }

    /**
     * Retorna un html que se integra con el codigo javascript escrito
     * en el modulo en que se implemente. Inicialmente esta funcion
     * esta hecha para radicacion incluida en New.php.
     *
     * @return string html 
     *
     */
    public function agregarDestinatarios($data, $nuevo = false){
        if (is_array($data)) {
            $destinatarios = $data;
        } else {
            $destinatarios = json_decode($data, true);
        }

        foreach ($destinatarios as $k => $result){
            
            $tipo = intval($result["TIPO_CIRCULAR"]);
            $codigo_destinatarios = !empty($result["CODIGO_DESTINATARIOS"]) ? $result["CODIGO_DESTINATARIOS"] : '';

            if (empty($result["CODIGO_CIRCULAR"]) || $nuevo) {
                $codigo_circular = '';
            } else {
                $codigo_circular = $result["CODIGO_CIRCULAR"];
            }

            /**
             * Identificador para realizar transaccion y eventos desde
             * la pagina de radicacion, el identificador se compone por:
             * @tipo tipo de circular (interna  externa)
             * @codigo_destinatarios codigo grabado en la tabla sgd_notif_circular_destinatario 
             * @codigo_circular codigo grabado en la tabla sgd_notif_circulares
             * si esta vacio se grabara como nuevo.
             */

            //Si es un registro nuevo mostramos los campos para editar
            $idtx = $codigo_destinatarios.'_'.$codigo_circular;

            $html = '<table style="width:100%;">';
                $html .= '</tr>';
                $html .= '<td class="search-table-icon">
                            <div class="row-fluid">
                                <span class="inline widget-icon txt-color-red"
                                rel="tooltip"
                                data-placement="right"
                                data-original-title="Eliminar Usuario">
                                <i class="fa fa-minus"></i></span>
                            </div>
                            <input type="hidden" class="hide" name="destinatarios_codi" value="'.$idtx.'">
                        </td>';

                $html .= '<td colspan="4">
                            <label class="input">
                                Destinatarios
                                <br>
                                <textarea id="id_destinatario" name="destinatarios" style="width:100%;" data-rel="solo-text">'.$result["DESTINATARIOS"].'</textarea>
                            </label>
                            </td>';
                $html .= '</tr>';
            $html .= '</table>';

            $htmltotal .= '<tr class="item_usuario" name="item_usuario"><td>'.$html.'</td></tr>';
        }
        
        return $htmltotal;
    }


    public function buscarDestinatarios($search) {
        $tipo   = (is_array($search['tdoc']))? $search['tdoc']['value'] : $search['tdoc'];
        $name   = trim((is_array($search['name']))? $search['name']['value'] : $search['name']);
        $codi   = (is_array($search['codi']))? $search['codi']['value'] : $search['codi'];

        $name = str_replace(' ', '%', trim($name));
        $where = "(SGD_NOTIF_CIRC_DEST_DESC LIKE '%". strtoupper($name) ."%')" ;

        if(!empty($codi)){
            $sub    = " SGD_NOTIF_CIRC_DEST_CODI LIKE '%$codi%'";
            $where .= (empty($where))? $sub : ' and '. strtoupper($sub);
        }

        $isql = "
        SELECT 
            SGD_NOTIF_CIRC_DEST_CODI as codigo_destinatarios,
            SGD_NOTIF_CIRC_DEST_DESC as destinatarios,
            SGD_TRAD_CODIGO as tipo_circular
        FROM 
            SGD_NOTIF_CIRCULAR_DESTINATARIO
        WHERE
            $where
            and SGD_TRAD_CODIGO =  $tipo";

        //$this->db->conn->debug = true;
        $rs = $this->db->conn->query($isql);

        while(!$rs->EOF ){
            $result[] = $rs->fields;
            $rs->MoveNext();
        }

        return $result;

    }

    public function destinatariosPorRadicado($radicado) {
        $isql = "
        SELECT 
            sd.SGD_NOTIF_CIRC_DEST_CODI as codigo_destinatarios,
            sd.SGD_NOTIF_CIRC_DEST_DESC as destinatarios,
            sd.SGD_TRAD_CODIGO as tipo_circular,
            sc.SGD_NOTIF_CIRC_CODI as codigo_circular
        FROM 
            SGD_NOTIF_CIRCULAR_DESTINATARIO as sd
        INNER JOIN 
            SGD_NOTIF_CIRCULARES AS sc
        ON sd.SGD_NOTIF_CIRC_DEST_CODI = sc.SGD_NOTIF_CIRC_DEST_CODI
        INNER JOIN
            SGD_NOTIF_NOTIFICACIONES AS sn
        ON sc.SGD_NOTIF_CODIGO = sn.SGD_NOTIF_CODIGO
        WHERE
            sn.RADI_NUME_RADI =  $radicado";

        //$this->db->conn->debug = true;
        $rs = $this->db->conn->query($isql);
        return array($rs->fields);
    }

}
