<?php

error_reporting(0);

/**
 * Clase que administra o permite ralizar envios por empresa de mesajeria u otro
 * como email, etc...
 *
 * @access public
 * @author Correlibre - Jairo Losada
 * @version v 1.0
 */
class Envio
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---
   
    /**
     * Short description of attribute documento
     *
     * @access public
     * @var Integer
     * @var $usuaDoc integer  Documento de quein realiza la operacion.
     * @var $destino string Variable que indica el destino que puede ser una direccion física o electrónica. 
     * @var $db Objeto Base de datos instanciada.
     */
   
    var $destino; // @var $destino string Variable que indica el nombre de la ciudad destino que de un envio. 
    var $telefono; // 
    var $db;
    var $rutaRaiz;
    
    function __construct($db){
     $this->db = $db; 
     $this->rutaRaiz = $this->db->rutaRaiz;
    }
    
   
    public $documento = null;

    // --- OPERATIONS ---
    
    public function getUsuDoc(){
      
      return $valor;  
    }
    public function setUsuaDoc($valor){
      $this->usuaDoc = $valor;  
    }
    public function set($valor){
      $this->usuaDoc = $valor;  
    }
    public function setFormaEnvio($valor){
      $this->formaEnvio = $valor;  
    }
    public function setRadicadoAEnviar($valor){
      $this->radicadoAEnviar = $valor;  
    }    
    /** Metodo setDestino
      * @var $valor string Variable que indica el nombre de la ciudad  destino. 
      * 
      */
    public function setCiudadDestino($valor=''){
      $this->ciudadDestino = $valor;  
    }
    
    public function setTelefono($valor=''){
      $this->telefono = $valor;  
    }
    
    public function setMail($valor=''){
      $this->mail = $valor;  
    }
    
    public function setPeso($valor=0){
      $this->peso = $valor;  
    }    
    
    public function setValorUnitario($valor=0){
      $this->valorUnitario = $valor;  
    }    

    public function setNombre($valor=0){
      $this->nombre = $valor;  
    }

    /** Metodo setDirCodigo
      * Almacena el nombre de la ciudad destino del envio
      *
      * @var $valor integer codigo de la direccion en la tabla sgd_dir_drecciones. 
      * 
      */    
    public function setCodigoDir($valor){
      $this->codigoDir = $valor;  
    }

    /** Metodo setDirTipo
      * Almacena el codigo del tipo de direccion este indica el orden del remitente/destino de cada radicado.
      *
      * @var $valor string codigo orden de los destinatarios. 
      * 
      */    
    
    public function setDirTipo($valor){
      $this->dirTipo = $valor;  
    } 
    
    public function setCodigoDependencia($valor){
      $this->codigoDependencia = $valor;  
    }     

    /** Metodo setDirTipo
      * Almacena el número del primer radicado en el caso que el envío sea de una Radicación Masiva.
      *
      * @var $valor string Numero radicado de grupo de una masiva. 
      * 
      */    
    
    public function setRadicadoGrupoMasiva($valor=null){
      $this->RadicadoGrupoMasiva = $valor;  
    }     
    
    /** Metodo setNumeroPlanilla
      * Almacena el número de la planilla de envio en el cual se realiza la operación.  Se usa o no segun el tipo de envio.
      *
      * @var $valor string Numero de planilla de un envio. 
      * 
      */    
    
    public function setNumeroPlanilla($valor){
      $this->numeroPlanilla = $valor;  
    }
 
    public function setDireccion($valor){
      $this->direccion = $valor;  
    }       
    public function setNombreDepartamento($valor){
      $this->nombreDepartamento = $valor;  
    }       
    
    public function setNombreMunicipio($valor){
      $this->nombreMunicipio = $valor;  
    }           
    public function setNombrePais($valor){
      $this->nombrePais = $valor;  
    }         

    /** Metodo setObservacinoes
      * Almacena las observacinoes que se colocan al envio a realizar.
      *
      * @var $valor string Observacion. 
      * 
      */        
    public function setObservaciones($valor){
      $this->Observaciones = $valor;  
    }    


    /** getRestriccionEnvioTRad
     * 
     * Metodo que indica si el Tipo de radicado posee alguna restriccion de envio.     
     * @access public
     * @author Yenny Betencur, Jairo loasda
     * @var $tRadCodigo
     * @return mixed
     */             
    public function getRestriccionEnvioTRad($tRadCodigo)
    {
         /* Reglas para el campo "sgd_envr_requiereexp"
            0. No se exige clasificación a los documentos del “sgd_trad_codigo” indicado.
            1. Los docuementos con esa Tipificación Específica aplican para esta restricción.
            2. Se exige clasificación a todos los documentos del “sgd_trad_codigo” indicado.
          **/ 
         $iSql = "SELECT id, sgd_trad_codigo, sgd_envr_requiereexp, sgd_mrd_all, sgd_mrd_codigo, 
          depe_codi_envio
          FROM sgd_envr_enviosreglas
          WHERE sgd_trad_codigo=$tRadCodigo";
         // $this->db->conn->debug = true;
         $rs = $this->db->conn->query($iSql);
         
         while(!$rs->EOF){
            $restriccionExp = $rs->fields["SGD_ENVR_REQUIEREEXP"]; 
         }
       if($restriccionExp==1) $res=true; else $res=false;
       return  $res;

    } // Fin restriccion envio por TipoRadicado tradCodigo    


    /** getRestriccionEnvio
     * 
     * Metodo que indica si el o los radicados tienen alguna restriccion de envio.     
     * @access public
     * @author Yenny Betencur, Jairo loasda
     * @var arrRadicados
     * @return mixed
     */
    public function getRestriccionEnvio($arrRadicados)
    {
       include $this->rutaRaiz."/include/tx/Expediente.php";
       include $this->rutaRaiz."/include/tx/TipoDocumental.php";
       $objExp = new Expediente($this->db);
       $objTRD = new TipoDocumental($this->db);
       $res["restriccion"] = false;
       foreach($arrRadicados as $objRadicado){
         $res["objRadicado"] = $objRadicado;  
         list($radicado, $sgdDirTipo) = explode("_", $objRadicado);
         $tRadCodigo = substr($radicado, -1);

         /* Reglas para el campo "sgd_envr_requiereexp"
            0. No se exige clasificación a los documentos del “sgd_trad_codigo” indicado.
            1. Los docuementos con esa Tipificación Específica aplican para esta restricción.
            2. Se exige clasificación a todos los documentos del “sgd_trad_codigo” indicado.
          **/ 
         $iSql = "SELECT id, sgd_trad_codigo, sgd_envr_requiereexp, sgd_mrd_all, sgd_mrd_codigo, 
          depe_codi_envio
          FROM sgd_envr_enviosreglas
          WHERE sgd_trad_codigo=$tRadCodigo";
         // $this->db->conn->debug = true;
         $rs = $this->db->conn->query($iSql);
         $res["restriccion"] = false;
         
         while(!$rs->EOF){
            $restriccionExp = $rs->fields["SGD_ENVR_REQUIEREEXP"]; 
            $moverRadicadoA = $rs->fields["DEPE_CODI_ENVIO"]; 
            $restriccionMrd = $rs->fields["SGD_MRD_ALL"]; 
            $mrdCodigo = $rs->fields["SGD_MRD_CODIGO"]; 
            /*if($restriccionExp==1 && $res["restriccion"] == false){
               $numExpediente = $objExp->consulta_exp($radicado); 
               if(!$numExpediente){
                   $res["restriccion"] = true;
                   $res["radicado"]    = $radicado;
                   $res["tipoRestriccion"] = "Radicado $radicado, no esta incluido en un expediente!"; 
               }
            }*/

            if($restriccionExp==1 && $res["restriccion"] == false){
               $numExpediente = $objExp->consulta_exp($radicado); 
               $trd= $objTRD->consultaTRDradicado($radicado);
               if(!$numExpediente or $trd==-1){
                   $res["restriccion"] = true;
                   $res["radicado"]    = $radicado;
                   if(!$numExpediente){
                      $res["tipoRestriccion"] = "<strong>Documento no se puede enviar por</strong>Radicado $radicado, no esta incluido en un expediente!<br>"; 
                   }
                   if($trd==-1){
                      $res["tipoRestriccion"] .= "<strong>Documento no se puede enviar por</strong>Radicado $radicado, no esta esta tipificado!<br>"; 
                   }
               }
            }
          $rs->MoveNext();
         }
         

         


       }

       $res["moverRadicadoA"] = $moverRadicadoA;
       //$res["tipoRestriccion"] = "El radicado debe estar en una carpeta o expediente virtual";
       return  $res;

    }


      /**
      * Short description of method generarEnvio
      *
      * @access public
      * @author firstname and lastname of author, <author@example.org>
      * @return mixed
      */
      public function generarEnvio()
      {
        // $this->db->conn->debug = true;
        $sql_sgd_renv_codigo = "select SGD_RENV_CODIGO FROM SGD_RENV_REGENVIO ORDER BY SGD_RENV_CODIGO DESC ";
        $rsRegenvio = $this->db->conn->SelectLimit($sql_sgd_renv_codigo,2);
        $nextval = $rsRegenvio->fields["SGD_RENV_CODIGO"];
        $nextval++;

        /*
        $record["USUA_DOC"] = $this->usuaDoc;
        $record["SGD_RENV_CODIGO"] = $nextval;
        $record["SGD_FENV_CODIGO"] = $this->formaEnvio;
        $record["SGD_RENV_FECH"] = $this->db->sysdate();
        $record["RADI_NUME_SAL"] = $this->radicadoAEnviar;
        $record["SGD_RENV_DESTINO"] = $this->ciudadDestino;
        $record["SGD_RENV_TELEFONO"] = $this->telefono;
        $record["SGDSAL"] = $this->radicadoAEnviar;
        $record["SGD_RENV_DESTINO"] = $this->ciudadDestino;
        $record["SGD_RENV_TELEFONO"] = $this->telefono;
        $record["SGD_RENV_MAIL"] = $this->mail;
        $record["SGD_RENV_PESO"] = $this->peso;
        $record["SGD_RENV_VALOR"] = $this->valorUnitario;
        $record["SGD_RENV_CERTIFICADO"] = 0;
        $record["SGD_RENV_ESTADO"] = 1;
        $record["SGD_RENV_NOMBRE"] = $this->nombre;
        $record["SGD_DIR_CODIGO"] = $this->codigoDir;
        $record["DEPE_CODI"] = $this->codigoDependencia;
        $record["SGD_DIR_TIPO"] = $this->dirTipo;
        if($this->RadicadoGrupoMasiva) $record["RADI_NUME_GRUPO"] = $this->RadicadoGrupoMasiva;
        $record["SGD_RENV_PLANILLA"] = $this->numeroPlanilla;
        $record["SGD_RENV_DIR"] = $this->direccion;
        $record["SGD_RENV_DEPTO"] = $this->nombreDepartamento;
        $record["SGD_RENV_MPIO"] = $this->nombreMunicipio;
        $record["SGD_RENV_PAIS"] = $_RENV_MAIL"] = $this->mail;
        $record["SGD_RENV_PESO"] = $this->peso;
        $record["SGD_RENV_VALOR"] = $this->valorUnitario;
        $record["SGD_RENV_CERTIFICADO"] = 0;
        $record["SGD_RENV_ESTADO"] = 1;
        $record["SGD_RENV_NOMBRE"] = $this->nombre;
        $record["SGD_DIR_CODIGO"] = $this->codigoDir;
        $record["DEPE_CODI"] = $this->codigoDependencia;
        $record["SGD_DIR_TIPO"] = $this->dirTipo;
        if($this->RadicadoGrupoMasiva) $record["RADI_NUME_GRUPO"] = $this->RadicadoGrupoMasiva;
        $record["SGD_RENV_PLANILLA"] = $this->numeroPlanilla;
        $record["SGD_RENV_DIR"] = $this->direccion;
        $record["SGD_RENV_DEPTO"] = $this->nombreDepartamento;
        $record["SGD_RENV_MPIO"] = $this->nombreMunicipio;
        $record["SGD_RENV_PAIS"] = $this->nombrePais;
        $record["SGD_RENV_OBSERVA"] = $this->Observaciones;
        $record["SGD_RENV_CANTIDAD"] = 1;
       
        */
        $rsInsert = $this->db->conn->Replace("SGD_RENV_REGENVIO",$record,'SGD_RENV_CODIGO', $autoquote = true);

    }

} /* end of class Envios */

?>

    
