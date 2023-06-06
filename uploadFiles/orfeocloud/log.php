<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of log
 * 
 * Esta clase es la encargada de hacer funcionar el modelo de datos del log con la vista 
 *
 * @author derodriguez
 */
if (! $ruta_raiz)
	$ruta_raiz = '../..';
//include_once 'config-inc.php';
include_once "$ruta_raiz/processConfig.php";
include_once "modeloLog.php";

class log {
    //Esta variable el c&oacute;digo unico de usuario
    private $UsuaCodi;
    //Esta es la descripci&oacute;n exacta de la operaci&oacute;n
    private $Opera;
    //Este es el c&oacute;digo de la dependencia del usuario que realiza la operaci&oacute;n
    private $DepeCodi;
    //Este es el c&oacute;digo de ID rol el cual tiene el usuario
    private $RolId;
    //Denominaci&oacute;n se refiere al nombre documental (Radicado o Expediente) afectado 
    private $DenomDoc;
    //Este es n&uacute;mero de referencia del radicado
    private $NumDocu;
    //Esta es la direccion de la maquina donde se realiza la operaci&oacute;n
    private $AddrC;
    //Este es el proxy por cual se conecta el usuario
    private $ProxyAd;
    private $RutaDB;
    private $DataDepe;
    private $DataUsua;
    private $DataRol;
    private $DataLog;
    private $modelo;
/**
 *function __construct es el que interactua con las funciones que interactuaan con el modelo de datos
 *
 *@param string $ruta_raiz La ruta raiz del aplicativo
 *
 *
 */   
    function __construct($ruta_raiz) {
        $this->modelo = new modeloLog($ruta_raiz);
    }
/* function getUsuaCodi Devuelve el valor del codigo de usuario
 *
 *@return integer UsuaCodi
 *
 *
 */

    public function getUsuaCodi() {
        return $this->UsuaCodi;
    }
/* function setUsuaCodi Obtiene el valor del codigo de usuario
 *
 *@param integer UsuaCodi
 *
 *
 */

    public function setUsuaCodi($UsuaCodi) {
        $this->UsuaCodi = $UsuaCodi;
    }
/* function getOpera Devuelve la descripcion de la operacion de log
 *
 *@return string getOpera Operacion hecha por usuario
 *
 *
 */

    public function getOpera() {
        return $this->Opera;
    }
/** function setOpera Obtiene la descripcion de la operacion de log
 *
 *@param string setOpera Operacion hecha por usuario
 *
 */
    public function setOpera($Opera) {
        $this->Opera = $Opera;
    }
/** function getDepeCodi Devuelve el numero de la dependencia del usuario
 *
 *@return integer getDepeCodi El n&uacute;mero de la dependencia de usuario
 *
 *
 */
    public function getDepeCodi() {
        return $this->DepeCodi;
    }
/** function setDepeCodi Obtiene el numero de la dependencia del usuario
 *
 *@param integer getDepeCodi El n&uacute;mero de la dependencia de usuario
 *
 *
 */
    public function setDepeCodi($DepeCodi) {
        $this->DepeCodi = $DepeCodi;
    }
/** function getRolId Devuelve el numero del rol de usuario
 *
 *@return integer getRolId Obtiene el valor de RolId
 *
 *@return RolId
 */

    public function getRolId() {
        return $this->RolId;
    }
    /*function setRolId Asigna el valor del RolId
     * 
     * @param integer RolId El rol de usuario
     */
    public function setRolId($RolId) {
        $this->RolId = $RolId;
    }
    /*function getDenomDoc Funci&oacute;n que obtiene el nombre del documento involucrado en operaci&oacute;n 
     * 
     * @return string La denominacion del documento manipulado(expediente,radicado)
     * 
     */
    public function getDenomDoc() {
        return $this->DenomDoc;
    }
    /*function setDenomDoc Funci&oacute;n que asigna el nombre del documento involucrado en operaci&oacute;n 
     * 
     * @param string DenomDoc  La denominacion del documento manipulado(expediente,radicado)
     */
    public function setDenomDoc($DenomDoc) {
        $this->DenomDoc = $DenomDoc;
    }
    /*function getNumDocu Obtiene el n&uacute;mero del que involucra la operaci&o;acuten
     * 
     * @return NumDocu N&uacute;mero del documento involucrado en la operacio&acute;n
     */
    public function getNumDocu() {
        return $this->NumDocu;
    }
    /*function setNumDocu Asigna el n&uacute;mero del que involucra la operaci&o;acuten
     * 
     * @param NumDocu N&uacute;mero del documento involucrado en la operacio&acute;n
     * 
     */
    public function setNumDocu($NumDocu) {
        $this->NumDocu = $NumDocu;
    }
    /*function getAddrC Obtiene la ip del equipo que realiza la operaci&oacute;n
     * 
     * @return AddrC IP del cliente que hace la operaci&oacute;n
     */
    public function getAddrC() {
        return $this->AddrC;
    }
    /*function getAddrC Asigna el valor de la ip del equipo que realiza la operaci&oacute;n
     * 
     * @param AddrC IP del cliente que hace la operaci&oacute;n
     */
    public function setAddrC($AddrC) {
        $this->AddrC = $AddrC;
    }
    /*function getProxyAd Obtiene el proxy desde donde esta equipo que realiza la operaci&oacute;n
     * 
     * @return ProxyAd IP del proxy de red del cliente que hace la operaci&oacute;n
     */
    public function getProxyAd() {
        return $this->ProxyAd;
    }
    /*function setProxyAd Asigna el proxy desde donde esta equipo que realiza la operaci&oacute;n
     * 
     * @return AddrC IP del cliente que hace la operaci&oacute;n
     */
    public function setProxyAd($ProxyAd) {
        $this->ProxyAd = $ProxyAd;
    }
    /*function getRutaDB
     * 
     */
    public function getRutaDB() {
        return $this->RutaDB;
    }
    /*function setRutaDB
     * 
     */
    public function setRutaDB($RutaDB) {
        $this->RutaDB = $RutaDB;
    }

    /*
     *
     */
     public function getDataDepe(){
	return $this->DataDepe;
     }
    /*
     *
     */
     public function setDataDepe($DataDepe){
        $this->DataDepe=$DataDepe;
     }

    /*
     *
     */
     public function getDataUsua(){
        return $this->DataUsua;
     }
    /*
     *
     */
     public function setDataUsua($DataUsua){
        $this->DataUsua=$DataUsua;
     }

     /*
     *
     */
     public function getDataRol(){
        return $this->DataRol;
     }
    /*
     *
     */
     public function setDataRol($DataRol){
        $this->DataRol=$DataRol;
     }

    /*
     *
     */
     public function getDataLog(){
        return $this->DataLog;
     }
    /*
     *
     */
     public function setDataLog($DataLog){
        $this->DataLog=$DataLog;
     }


    /*function registroEvento Operacion que llama al modelo para registrar la operaci&oacute;n
     * 
     */
    function registroEvento(){
        $this->modelo->registroEvento($this->UsuaCodi,  $this->DepeCodi, $this->RolId,  $this->DenomDoc, $this->NumDocu, $this->AddrC,  $this->ProxyAd, $this->Opera);
    }
    /*function cargaData Consulta y devuelve los datos guardados en el modelo
     * 
     * @return array Datos devueltos por el modelo
     */
    
    function cargaData(){
        return $this->modelo->cargaData($this->RutaDB,  $this->DepeCodi, $this->UsuaCodi);
    }
    /* function cargasinDep carga datos de usuarios sin dependencia
     * 
     * 
     */

    function cargasinDep(){
        return $this->modelo->cargasinDep($this->RutaDB, $this->UsuaCodi);
    }
    /* function buscaDB busca y abre un base de datos existente del log
     * 
     * 
     */
    function buscaDB(){
        $p=array();
        $p[]='.';
        $p[]='..';
        //Definiendo los meses del an&tilde;o 
        $mes[0]='Enero';
        $mes[1]='Febrero';
        $mes[2]='Marzo';
        $mes[3]='Abril';  
        $mes[4]='Mayo'; 
        $mes[5]='Junio';
        $mes[6]='Julio';
        $mes[7]='Agosto'; 
        $mes[8]='Septiembre';
        $mes[9]='Octubre';
        $mes[10]='Noviembre';
        $mes[11]='Diciembre';
        $tmpfile=RUTA_BODEGA.'log/';
        $i=0;
        if($gestor=opendir($tmpfile)){
            while(false !==($entrada=readdir($gestor))){
                if($entrada!='.' && $entrada!='..'){
                    if(false!==($filename=scandir($tmpfile.$entrada,1))){
                        $resul=array_diff($filename,$p);
                        //print_r($resul);
                        for($j=0;$j<count($resul);$j++){
                            list($part1,$part2)=explode('.', $resul[$j]);
                            list($ano,$mot)=explode('-',$part1);
                            $matrix[$i]['select']=$ano.'-'.$mes[$mot-1];
                            $matrix[$i]['ruta']=$tmpfile.$entrada."/".$resul[$j];
                            $i++;
                        }
                    }
                }
            }
            closedir($gestor);
        }
        return $matrix;
    }

    function CrearCsv(){
	$datalog=$this->DataLog;
        $datadepe=$this->DataDepe;
        $datausua=$this->DataUsua;
	$datarol=$this->DataRol;
	$Csv="USUARIO;ROL;DEPENDENCIA;FECHA DE OPERACION;DIRECCION IP;DOCUMENTO INVOLUCRADO;NUMERO DE DOCUMENTO;OPERACION\n";
	$numelem=count($datalog);
        for($i=0;$i<$numelem;$i++){
	    $j=$datalog[$i]['usua_codi'];
            $h=$datalog[$i]['depe_codi'];
            $k=$datalog[$i]['rol_id'];
	    if(isset($datausua[$j])){
	        $usuanomb=$datausua[$j];
	    }else{
   	        $usuanomb='';
	    }
	    if(isset($datarol[$k])){
    	        $rolnomb=$datarol[$k];
	    } else{
    	        $rolnomb='';
	    }
	    if(isset($datadepe[$h])){
    	        $depenomb=$datadepe[$h];
	    }else{
    	        $depenomb='';
	    }
	    $Csv.=str_replace('\t',' ',str_replace('\n','',trim($usuanomb).';'.trim($rolnomb).';'.trim($depenomb).';'.trim($datalog[$i]['fecha']).';'.trim($datalog[$i]['ip_address']).';'.trim($datalog[$i]['denominacion']).';'.trim($datalog[$i]['referencia']).';'.trim($datalog[$i]['operacion'])))."\n";
	}
	$Csv=iconv('iso-8859-1','utf-8',$Csv);
	date_default_timezone_set("America/Bogota");
        $nomarchivo='LogReporte-'.date('y').'-'.date('m').'-'.date('d').date('h:i:s');
	//Archivo  csv
        $nombre_archivo = "/tmp/$nomarchivo.csv";
        fopen ( $nombre_archivo, 'wra+' );

        // Asegurarse primero de que el archivo existe y puede escribirse sobre el.
        if (is_writable ( $nombre_archivo )) {

                // En nuestro ejemplo estamos abriendo $nombre_archivo en modo de adicion.
                // El apuntador de archivo se encuentra al final del archivo, asi que
                // alli es donde ira $contenido cuando llamemos fwrite().
                if (! $gestor = fopen ( $nombre_archivo, 'a' )) {
                        echo "No se puede abrir el archivo ($nombre_archivo)";
                        exit ();
                }

                // Escribir $contenido a nuestro arcivo abierto.
                if (fwrite ( $gestor, $Csv ) === FALSE) {
                        echo "No se puede escribir al archivo ($nombre_archivo)";
                        exit ();
                }

                //echo "&Eacute;xito, se escribi&oacute; ($contenidoCsv) d al archivo ($nombre_archivo)";


                fclose ( $gestor );

        } else {
                echo "No se puede escribir sobre el archivo $nombre_archivo";
        }
        return $nombre_archivo;
    }

}

?>
