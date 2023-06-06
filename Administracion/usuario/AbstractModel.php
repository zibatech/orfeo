<?php
/**
*@author Cesar Buelvas
*@mail cejebuto@gmail.com
*@date 01/02/2017
    Modelo abstracto que incluirá los metodos principales de los modulos, estos metodos influyen en la mayoria de los modulos

*/
class AbstractModel
{
    protected $db;
    public $titulos;

 
    public function __construct($db){
        $this->db=$db;
        $this->db->conn->debug=false;
        $this->columnName = $this->getFields();
    }


    /**
    * Se setea la tabla a utilizar
    */
    public function setTable($table){
        $this->table = $table;
    }

    /**
    * Se setea la tabla a utilizar
    */
    public function getTable(){
        return $this->table;
    }

    /**
    * Se setea los campos
    */
    public function setColumnName($columnName){
        $this->columnName = $columnName;
    }

    /**
    *getAll:Metodo que se utiliza para obtener todos los registros, NO SE RECOMIENDA PARA TABLAS GRANDES, favor usar el arayField
    *@author Cesar Buelvas
    *@param $arrayField : Campos o columnas a mostrar
    *@param $arrayWhere : estado de la consulta
    */
    function getAll($arrayField = null,$arrayWhere=null, $order = "" ,$table = "",$getNull = false, $notDebug = false){

        #Comprobamos si se espeficica otra tabla
        if($table != ""){
            $this->table = $table;
        }

        /*Comprobamos que exista una tabla*/
        if ($this->table != NULL){ $table = $this->table; }else{return "Especifique una tabla getAll no se pudo ejecutar";}

        /*Comprobar si existe por parametros, los campos a obtener*/
        if ($arrayField == NULL){

            /*Comprobamos que las columnas existan*/
            if( (is_array($this->columnName)) and ($this->columnName != NULL) ){$columnName = $this->columnName;}else{return "No existen Columnas";}

        }else{
            /*Set Nombre de las columnas*/
            $columnName = $arrayField;
        }

        /*Seteamos los titulos*/
        $this->setTitulos($columnName);

        $retorno = $this->db->getSelectAll($table,$columnName,$arrayWhere,$order,$notDebug);    

            if ($retorno != NULL){
                return $retorno;
            }else{
                if ($getNull === true) {
                    return "";
                }else{
                    return "No se obtuvo ningun resultado";    
                }
                
            }
    }

    /**
    *getFields::Funcion que nos permite actualizar un registro apartir de un array set y un array where
    *@author Cesar Buelvas
    */
    public function update($arraySet,$arrayWhere,$table =""){

        #Comprobamos si se espeficica otra tabla
        if($table != ""){
            $this->table = $table;
        }

        if (!is_array($arraySet) or is_null($arraySet)){
            return "No es valido el primer parametro, debe ser un array no nulo";
        }

        if (!is_array($arrayWhere) or is_null($arrayWhere)){
            return "No es valido el segundo parametro parametro, debe ser un array no nulo";
        }

        if ($this->table != NULL){
            $update =  $this->db->update((string)$this->table,$arraySet,$arrayWhere);    
            if ($update){
                return $update;
            }else{
                return "NO SE PUDO REALIZAR LA TRANSACCION";
            }
        }else{
            return "Especifique una tabla";
        }


    }

    public function insert($arrayValue,$table = ""){

        #Comprobamos si se espeficica otra tabla
        if($table != ""){
            $this->table = $table;
        }

        if (!is_array($arrayValue) or is_null($arrayValue)){
            return "No es valido el parametro, debe ser un array no nulo";
        }

        if ($this->table != NULL){
            $insert =  $this->db->insert((string)$this->table,$arrayValue);    
            if ($insert){
                return $insert;
            }else{
                return "NO SE PUDO REALIZAR LA INSERCION";
            }
        }else{
            return "Especifique una tabla";
        }

        
    }

    /**
    *getFields::Obtiene el solo el nombre de las columnas de una tabla en us array
    *@author Cesar Buelvas
    */
    public function getFields()
    {
        if(isset($this->table))
        {
            $nombreColumnas = null;

            if ($this->table != NULL){
                $todos =  $this->db->getcolname((string)$this->table);    

                if ($todos != NULL){
                    foreach ($todos as $key => $value) {
                        $nombreColumnas[]=$value['column_name'];
                    }
                return $nombreColumnas;
                }
                return "NO SE OBTUVO NINGUNA COLUMNA";
            }else{
                return "Especifique una tabla";
            }
        }
        return "Especifique una tabla";
    }

    /**
    *getColumn::Obtiene el nombre de las columnas de una tabla con sus tipos de datos
    *@author Cesar Buelvas
    */
    public function getColumn($table = "")
    {

        #Comprobamos si se espeficica otra tabla
        if($table != ""){
            $this->table = $table;
        }

        if ($this->table != NULL){
            return $this->db->getcolname((string)$this->table);    
        }else{
            return "Especifique una tabla";
        }
    }

    /**
    *setTitulos::De seters , esta funcion nos permite setear un array, que se usa como titulos de las consultas
    *@author Cesar Buelvas
    */
    public function setTitulos($array){
        $this->titulos = $array;
    }

/**
    *setTitulos::De seters , esta funcion nos permite setear un array, que se usa como titulos de las consultas
    *@author Cesar Buelvas
    */
    public function getTitulos(){

        /*Validamos que exista la variable*/
        if( (is_array($this->titulos)) and ($this->titulos != NULL) ){$titulos = $this->titulos;}else{return "No existen Titulos";}

        $newTitulo = array();
        foreach ($titulos as $key => $value) {
            $value = strtoupper($value);
            if (strpos($value,' AS ')) {
                $valueAux = explode(" AS ", $value);
                $value = $valueAux[1];
            } 
            #$newTitulo[]=ucwords(strtolower($value));
            $newTitulo[]=strtolower($value);
        }
        return $newTitulo;
    }


    /**
    *Funcion que devuelve true o false si este se encuentra en una tabla
    *@param parametro 1 , nombre del campo 
    *@param parametro 2 valor irrepetible 
    *@param parametro 3 , nombre de la tabla
    */
    public function ifexistKey($column,$value = "",$table = "") {

        #Si no existe el parametro, se establece la tabla de la clase
        if ($table == ""){ $table = (string)$this->table; }

        if ($table != NULL || $column != "" ){
            return $this->db->ifexistKey($table, $column,$value);    
        }else{
            return "Especifique una tabla o columna";
        }
       
    }
    /**
    *Funcion que devuelve true o false si este se encuentra en una tabla
    *@param parametro 1 , Array de los campos que se desean consultar
    *@param parametro 2 , nombre de la tabla
    */
    public function ifexistkeyArray ($arraySelect , $table = ""){

        #Si no existe el parametro, se establece la tabla de la clase
        if ($table == ""){ $table = (string)$this->table; }

        if ($table != NULL || $arraySelect != NULL ){
            return $this->db->ifexistkeyArray($table, $arraySelect);    
        }else{
            return "Especifique una tabla o array selectores";
        }

        
    }


    /**
    *Funcion que ejecuta una consulta, en caso de falla, realiza debug a dicha consulta
    *@param query
    *@return ResultSet
    */
    public function execute($query="",$tprint=0) {

        if($tprint==1){
            $this->tprint($query);
            die();
        }

        if ($query == "") {
            return "NO SE PUEDE EJECUTAR, NO EXISTE UNA CONSULTA";
        }
        #Se executa la consulta
        $rs = $this->db->conn->query($query);    

        if(!$rs){
            $this->db->conn->debug = true;
            $this->db->conn->query($query);
            die();
        }

        return $rs;
    }

    /**
    *Procedimiento que imprime variables
    *@param var
    */
    public function tprint($var){
        if (is_array($var)) {
            echo "<pre>";
            print_r($var);
        }else{
            echo "<pre>".$var."</pre>";
        }

    }

    /**
    *Procedimiento que devuelve un array de una consulta SQL
    *@param var #$arrayOption = ["M"=>"Masculino","F"=>"Femenino","AI"=>"Inteligencia Artificial"];
    */
    public function getArrayKeyValue($sql,$campoKey="",$campoValue=""){

        if($sql == ""){
            return null;
        }else{
            #Si lleha el campoKey y el Value, se debe cambiar el selector
            return $this->db->getSqlKeyValue($sql);   
        }
    }

        /**
    *Procedimiento que devuelve un array de una consulta SQL
    *@param var
    */
    public function getSqlArray($sql){

        if($sql == ""){
            return null;
        }else{
            return $this->db->getSqlArray($sql);   
        }
    }

    /**
    *getStringSQL:Procedimiento que nos devuelve el sql sin ejecutarlo  
    *@author Cesar Buelvas
    *@param $arrayField : Campos o columnas a mostrar
    *@param $arrayWhere : estado de la consulta
    */
    function getStringSQL($arrayField = null,$arrayWhere=null, $order = "" ,$table = "",$getNull = false){

        #Comprobamos si se espeficica otra tabla 
        if($table != ""){
            $this->table = $table;
        }

        /*Comprobamos que exista una tabla*/
        if ($this->table != NULL){ $table = $this->table; }else{return "Especifique una tabla getStringSQL no se pudo ejecutar";}

        /*Comprobar si existe por parametros, los campos a obtener*/
        if ($arrayField == NULL){

            /*Comprobamos que las columnas existan*/
            if( (is_array($this->columnName)) and ($this->columnName != NULL) ){$columnName = $this->columnName;}else{return "No existen Columnas";}

        }else{
            /*Set Nombre de las columnas*/
            $columnName = $arrayField;
        }

        /*Seteamos los titulos*/ 
        $this->setTitulos($columnName); 

        $retorno = $this->db->getSelectStringSql($table,$columnName,$arrayWhere,$order);    

            if ($retorno != NULL){
                return $retorno;
            }else{
                if ($getNull === true) {
                    return "";
                }else{
                    return "No se obtuvo ningun resultado";    
                }
                
            }
    }

  /**
  * Procedimiento que permite crear una secuencia
  */
  public function createSequence ($name,$start=1){

    if ($name== ""){
        return "La secuencia Necesita un Nombre para poder crearse";
    }
    return $this->db->createSequence($name,$start);
  }

    /**
    * Funcion que retorna 1 solo valor
    */
    public function getOne($value,$where='',$orderBy= '',$table = ""){

        #Si no existe el parametro, se establece la tabla de la clase
        if ($table == ""){ $table = (string)$this->table; }

        if($value == ""){
            return "No se puede obtener un valor nulo";
        }
        $valueAux[]=$value;

        $auxVar = self::getAll($valueAux,$where,$order,$table,true);

        if(is_array($auxVar) and $auxVar != NULL){
            $auxVar = $auxVar[0][$value];
        }else{
            $auxVar = (string)$auxVar;
        }

        return $auxVar;
    }

    public function rowCount($where='',$table = ""){

        if(is_null($where)){
            $where = ["1"=>"1"];
        }
        
        if (!is_array($where)) {
            return "El parametro debe ser un tipo array";
        }        

        #Si no existe el parametro, se establece la tabla de la clase
        if ($table == ""){ $table = (string)$this->table; }

        $count = $this->db->recordCount($where,$table);

        return $count;

    }


    public function rowCountSql($sql=''){

        #Si no existe el parametro, se establece la tabla de la clase  
        if ($sql == ""){ return "El parametro sql no debe ser tipo nulo"; }

        $count = $this->db->recordCountSql($sql);

        return $count;

    }

    public function encr($string, $key) {
       $result = '';
       for($i=0; $i<strlen($string); $i++) {
          $char = substr($string, $i, 1);
          $keychar = substr($key, ($i % strlen($key))-1, 1);
          $char = chr(ord($char)+ord($keychar));
          $result.=$char;
       }
       return base64_encode($result);
    }

    public function dscr($string, $key) {
       $result = '';
       $string = base64_decode($string);
       for($i=0; $i<strlen($string); $i++) {
          $char = substr($string, $i, 1);
          $keychar = substr($key, ($i % strlen($key))-1, 1);
          $char = chr(ord($char)-ord($keychar));
          $result.=$char;
       }
       return $result;
    }


    /* Obtenemos un result set modificado desde el ADODB*/  
    public function SelectLimitArray($query,$SizePage=10,$StartPage=1,$debug = false){

        if ($query == null){
            die("No se puede procesar sin una consulta SQL "); 
        }
 
        /**incluimos conn para poder llamarlo desde adodb  */  
        $this->db->conn->debug = $debug;
        $rs = $this->db->conn->SelectLimitArray($query,$SizePage,$StartPage); 
        $this->db->conn->debug = false;
        return $rs;  

    }

    public function upperCase($string){
       return  $this->db->upperCase($string); 
    }

    public function castText($string){
       return  $this->db->castText($string); 
    }
    
    /*Funcion que permite desfraqmentar y obtener los alias de los campos */ 
    public function getAliasFields($campos){
        
        if (!is_array($campos)) {
            return "El parametro debe ser un tipo array";
        } 
        $i = 0;
        foreach ($campos as $key ) {
            
            $name = explode(" as ", $key);

            if (array_key_exists(1, $name)) {
                $newCampos[$i]=$name[1];
                $i++;
            }else{
                $newCampos[$i]=$name[0];
                $i++;
            }
        }
        return $newCampos;

    }


    /*Funcion que permite desfraqmentar y obtener los alias de los campos */ 
    public function getNameFields($campos){
        
        if (!is_array($campos)) {
            return "El parametro debe ser un tipo array";
        } 
        $i = 0;
        foreach ($campos as $key ) {
            
            $name = explode(" as ", $key);

            if (array_key_exists(0, $name)) {
                $newCampos[$i]=$name[0];
                $i++;
            }
        }
        return $newCampos;

    }

}