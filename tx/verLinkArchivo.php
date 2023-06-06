<?php
/**
 * verLinkArchivo es la clase encargada de
 * validar los permisos de acceso a un documento (imagen informacion)
 * @author Liliana Gomez Velasquez
 * @version     1.0
 * @fecha  09 sep 2009
 */
class verLinkArchivo{

    /**
     * Variable que se corresponde con su par
     * @db Objeto conexion
     * @access public
     */
    var $db;

    /**
     * Vector que almacena el resultado de la validacion
     * @var string
     * @access public
     */
    var $vecRads;
    /**
     * Vector que almacena el resultado de la validacion
     * de un Anexo
     * @var string
     * @access public
     */
    var $vecRadsA;

    /**
     * Constructor encargado de obtener la conexion
     * @param	$db	ConnectionHandler es el objeto conexion
     * @return   void
     */

		/**
		 * Muestra información relevante sobre la seguridad del radicado
		 * @var type
		 */
		var $info = array();

    function __construct($db) {
        /**
         * Constructor de la clase
         * @db variable en la cual se recibe el cursor sobre el cual se esta trabajando.
         *
         */
        $this->db = $db;
    }


    function verLinkArchivo($db) {
        /**
         * Constructor de la clase
         * @db variable en la cual se recibe el cursor sobre el cual se esta trabajando.
         *
         */
        $this->db = $db;
    }


    /**
     * Retorna el valor correspondiente al
     * resultado de la validacion
     * @numrad  Numero del Radicado a validar
     * @return   array  $vecRads resultado de la operacion de validacion
     */
    function valPermisoRadi($numradi){
        // Busca el Documento del usuario Origen
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $verImg = "NO";
        $isql = "select
                    r.RADI_PATH,
                    r.SGD_SPUB_CODIGO,
                    r.CODI_NIVEL,
                    u.USUA_NOMB,
					u.USUA_DOC,
                    r.RADI_DEPE_RADI,
                    r.RADI_USUA_RADI,
                    r.RADI_USU_ANTE,
                    r.RADI_DEPE_ACTU
                from
                    RADICADO r left outer join USUARIO u on
                        r.RADI_USUA_ACTU= u.USUA_CODI
                        and r.RADI_DEPE_ACTU= u.DEPE_CODI
                where
                    r.RADI_NUME_RADI='{$numradi}'";

        $rs=$this->db->conn->query($isql);


        //Start::Validar si el memorando tienen al usuarrio
        $iSqlMemorandoMultiple= "SELECT count(*) EXISTE FROM SGD_DIR_DRECCIONES WHERE radi_nume_radi='{$numradi}' AND  SGD_DIR_DOC = '{$_SESSION["usua_doc"]}' and radi_nume_radi::text like '%3'";
        $rsMemorandoMultiple = $this->db->conn->query($iSqlMemorandoMultiple);
        $tieneAsignacion = 0;
        if ($rsMemorandoMultiple) {
            if($rsMemorandoMultiple->fields["EXISTE"] > 0){
                $tieneAsignacion = true;
            }
        }
        //End::Validar si el memorando tienen al usuarrio

        //Start::publico
        if ($_SESSION["usua_perm_root_email"]=="t"){
            $tieneAsignacion = true;
        }
        //End::publico



        $consultaExpediente = "SELECT
                                exp.SGD_EXP_NUMERO,
                                sexp.SGD_EXP_PRIVADO
                                FROM
                                SGD_EXP_EXPEDIENTE exp,
                                SGD_SEXP_SECEXPEDIENTES sexp
                            WHERE
                                exp.sgd_exp_numero=sexp.sgd_exp_numero AND
                                exp.radi_nume_radi=$numradi
                                AND exp.sgd_exp_estado<>2
                                ORDER BY concat(sexp.SGD_EXP_PRIVADO ,'0') desc ";

        $rsE=$this->db->conn->query($consultaExpediente);

        if (!$rsE->EOF){
            $fldsSGD_EXP_SUBEXPEDIENTE=$rsE->fields["SGD_EXP_NUMERO"];
            $privadoExp = $rsE->fields["SGD_EXP_PRIVADO"];
        }else{
            $fldsSGD_EXP_SUBEXPEDIENTE= "";
        }
        //Consulta Informados
        $usuaInformado= "";
        $isqlI = "select USUA_DOC
            from INFORMADOS
            where RADI_NUME_RADI='$numradi'
            and USUA_DOC= '".$_SESSION['usua_doc']."'";

        $rsI=$this->db->conn->query($isqlI);
        if (!$rsI->EOF){
            $usuaInformado=$rsI->fields["USUA_DOC"];
        }

        //Star::Consulta Tramite conjunto
        $usuaInformado= "";
        $isqlI = "select USUA_DOC
            from TRAMITECONJUNTO
            where RADI_NUME_RADI='$numradi'
            and USUA_DOC= '".$_SESSION['usua_doc']."'";

        $rsI=$this->db->conn->query($isqlI);
        if (!$rsI->EOF){
            $usuaInformado=$rsI->fields["USUA_DOC"];
        }
        //End::Consulta Tramite conjunto

        if (!$rs->EOF){
            $seguridadRadicado=$rs->fields["SGD_SPUB_CODIGO"];
            $_SESSION['seguridadradicado'] = $seguridadRadicado;
            $nivelRadicado = $rs->fields["CODI_NIVEL"];
            $USUA_ACTU_R   = $rs->fields["USUA_DOC"];
            $USUA_ANTE     = $rs->fields["RADI_USU_ANTE"];
            $DEPE_ACTU_R   = $rs->fields["RADI_DEPE_ACTU"];
            $pathImagen    = $rs->fields['RADI_PATH'];
            $USUA_CODI_PROYECTO    = $rs->fields['RADI_USUA_RADI'];
            $DEPE_CODI_PROYECTO    = $rs->fields['RADI_DEPE_RADI'];

						$this->info['Nivel Usuario'] = $_SESSION["nivelus"];
						$this->info['Nivel del radicado'] = $nivelRadicado;
						$this->info['Seguridad de radicado'] = $seguridadRadicado;
						$this->info['Seguridad de Expediente'] = $privadoExp;
						$this->info['Usuario es Root'] = $_SESSION["usua_perm_root"];
						$this->info['Usuario actual'] = $USUA_ACTU_R;
						$this->info['Usuario informado'] = $usuaInformado;


            if($seguridadRadicado==0 && $privadoExp==0){
                $verImg = "SI";
            }

            if($_SESSION["dependencia"] == $DEPE_ACTU_R &&
                $seguridadRadicado == 1 && $privadoExp==0){
                $verImg = "SI";
            }

            if($seguridadRadicado == 2 && $privadoExp==0                
                &&  (($_SESSION["dependencia"] == $DEPE_ACTU_R && 
                ($_SESSION["USUA_JEFE_DE_GRUPO"] == true  || $USUA_ACTU_R == $_SESSION["usua_doc"])) || ($_SESSION["dependencia"] == $DEPE_CODI_PROYECTO && $_SESSION["codusuario"] ==     $USUA_CODI_PROYECTO))) {
                $verImg = "SI";
            }             

            /*if ($_SESSION["usua_perm_root"]=="t"){
                $verImg = "SI";
            }*/

            if($USUA_ACTU_R == $_SESSION["usua_doc"]){
                $verImg = "SI";
            }elseif($DEPE_ACTU_R == '999'){
                if ($seguridadRadicado==0){
                    $verImg = "SI";
                }else{
                    $verImg = "NO";
                }
            } elseif(isset( $fldsSGD_EXP_SUBEXPEDIENTE ) ){

                //Consultamos el documento del usuario responsable del expediente
                $consultaDuenoExp="SELECT USUA_DOC_RESPONSABLE	FROM SGD_SEXP_SECEXPEDIENTES
                    WHERE SGD_EXP_NUMERO = '$fldsSGD_EXP_SUBEXPEDIENTE'";
                $rsExpDueno=$this->db->conn->query($consultaDuenoExp);
                $duenoExpediente=$rsExpDueno->fields["USUA_DOC_RESPONSABLE"];

								$this->info['Dueño del expediente'] = $duenoExpediente;

                if (  $duenoExpediente != $_SESSION[ 'usua_doc' ]) {
                    // Entra a este condicion siempre y cuando el usuario de las session no es el dueño del expediente.
                    $sqlExpR = "SELECT
                        SEXP.DEPE_CODI AS DEPENDENCIA, SEXP.SGD_EXP_PRIVADO AS PRIVEXP, USUA_DOC_RESPONSABLE AS RESPONSABLE
                        FROM
                        RADICADO R, SGD_SEXP_SECEXPEDIENTES SEXP, SGD_EXP_EXPEDIENTE EXP
                        WHERE
                        R.RADI_NUME_RADI=$numradi
                        AND R.RADI_NUME_RADI = EXP.RADI_NUME_RADI
                        AND EXP.SGD_EXP_NUMERO = SEXP.SGD_EXP_NUMERO
                        AND EXP.sgd_exp_estado<>2
                        AND SEXP.USUA_DOC_RESPONSABLE = "."'".$duenoExpediente."'" ;

                    $rsER = $this->db->conn->query( $sqlExpR );
                    if (!$rsER->EOF){
                        $responsableExp = $rsER->fields["RESPONSABLE"];
                        $privadoExp = $rsER->fields["PRIVEXP"];
                        $dependenciaExp = $rsER->fields["DEPENDENCIA"];
                    }
                }else{
                    // Si el usuario es el Dueño";
                    $privadoExp = 0;
                }


                //Si el usuario que consulta es: usuario actual o responsable del expediente puede ver el Radicado
                $depeActu =(string) $_SESSION['dependencia'];

                if ($privadoExp == 0 || !$privadoExp){

										if($seguridadRadicado==0 && $_SESSION["nivelus"] >= $nivelRadicado){
											$verImg = "SI";//echo "nivel 0";
										}

                }elseif ( $privadoExp == 1 && ($dependenciaExp == $depeActu || 1 == $_SESSION[ 'codusuario' ])) {

                    $verImg = "SI";//echo "nivel 1";
                }elseif ($privadoExp == 2 && ($responsableExp == $_SESSION['usua_doc'] ||($dependenciaExp == $depeActu && $_SESSION['USUA_JEFE_DE_GRUPO'] ))){

                    $verImg = "SI";//echo "nivel 2";
                }elseif ($seguridadRadicado>=1){

                    if ($DEPE_ACTU_R == '999' && $USUA_ANTE ==  $_SESSION[ 'krd' ] ){
                        $verImg = "SI";
                    }
                }
                if($tieneAsignacion)
                    $verImg = "SI";

            }elseif($seguridadRadicado>=1){
                if ($DEPE_ACTU_R == '999' && $USUA_ANTE ==  $_SESSION[ 'krd' ] ){
                    $verImg = "SI";
                }
            }elseif(!isset($privadoExp) && $_SESSION["nivelus"] >= $nivelRadicado){
                $verImg = "SI";
            }

        }else{
            $verImg = "NO SE ENCONTRO INFORMACION DEL RADICADO";
        }
        $vecRadsD['verImg'] = $verImg;
        $vecRadsD['pathImagen']= $pathImagen;
        $vecRadsD['numExpe']= $fldsSGD_EXP_SUBEXPEDIENTE;
		$vecRadsD['info']= $this->info;

        return $vecRadsD;
    }


    /**
     * Retorna el valor correspondiente al
     * resultado de la validacion
     * @numrad  Numero del Anexo a validar
     * @return   array  $vecRadsA resultado de la operacion de validacion
     */
    function valPermisoAnex($numAnex){

        /// Busca el Documento del usuario Origen
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $verImg     = "SI";
        $pathImagen = "";
        $isqlAnex   = "select ANEX_NOMB_ARCHIVO, ANEX_RADI_NUME
            from ANEXOS
            where ANEX_CODIGO = '$numAnex'";
        //echo $isqlAnex;
        $rsAnex=$this->db->conn->query($isqlAnex);
        if (!$rsAnex->EOF){
            //$this->db->conn->debug = true;
            $pathImagen = trim($rsAnex->fields["ANEX_NOMB_ARCHIVO"]);
            $numeradi = trim($rsAnex->fields["RADI_NUME_SALIDA"]);
            $radiNumePadre = trim($rsAnex->fields["ANEX_RADI_NUME"]);
            $rsValPermisosPadre = $this->valPermisoRadi($radiNumePadre);

            $verImg = $rsValPermisosPadre["verImg"];
        }else{
            $verImg = "NO SE ENCONTRO INFORMACION DEL RADICADO";
        }
        $vecRadsA['verImg']     = $verImg;
        $vecRadsA['pathImagen'] = $pathImagen;

        return $vecRadsA;
    }
}

?>
