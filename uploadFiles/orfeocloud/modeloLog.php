<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Este el modelo de datos encargado de ingresar y mostrar la informaci&oacute;n contenida dentro del log de usuario
 *
 * @author derodriguez
 */
if (!$ruta_raiz)
    $ruta_raiz = '../..';
include_once "$ruta_raiz/processConfig.php";

/*class modeloLog
 * 
 * Clase que carga el modelo de la base de datos que guarda, crea y muestra informaci&oacute;n detallada
 * sobre cada una de las operaciones de usuario dentro del sistema. Hereda datos de los datos que carga
 * PHP para funciones con SQLite.
 */

class modeloLog extends SQLite3{
    //Es la variable que se usa para hacer el enlace de conexi&on a la base de datos
    private $link;
    //Esta es la ruta donde se almacena la informaci&oacute;n de la base de datos
    private $ruta_log_db;
    public $ruta_raiz; 
    /*function __construct
     * 
     * Esta es la funci&on que recrea la funcionalidad de conexion a la base de datos
     * 
     */
    function __construct() {
        try{
            if(!isset($this->ruta_log_db)){
                if(!file_exists($this->ruta_log_db=$this->obDir())){
                    if(!$this->crearRuta())
                        throw new Exception('No hay permisos en crear archivos en la bodega');
                }
            }
            $db = new SQLite3 ($this->ruta_log_db);
            $this->link=$db;
            
        }
        catch(Exception $e){
            return false;
        }
    }
    /*function obDir 
     * Funci&oacute;n que obtiene el dato de la ruta en donde guarda las multiples copias de
     * bases de datos, construida a raiz de la fecha.
     * 
     */
    protected function obDir(){
        date_default_timezone_set("America/Bogota");
        $dir=date('Y');
        $dbfile=$dir.'-'.date('m').'.db';
        $ruta_final='log/'.$dir.'/'.$dbfile;
        return $ruta_final;
    }
    /*function crearRuta Crea los directorios y archivos donde guarda la informaci&oacute;n de las bases de datos de operaciones realizadas dentro del
     *                   sistema.
     * 
     */
    protected function crearRuta(){
        date_default_timezone_set("America/Bogota");
        $dir=date('Y');
        $dbfile=$dir.'-'.date('m').'.db';
        $ruta_log_dir='../../bodega/tmp/log/'.$dir;
        $ruta_log_db ='../../bodega/tmp/log/'.$dir.'/'.$dbfile;
        if(!file_exists($ruta_log_dir)){
           if(mkdir($ruta_log_dir)){
                $status=true;
                touch($ruta_log_db);
                chmod($ruta_log_db,0777);
           }else
              $status=false;
        }elseif(!file_exists($ruta_log_db)){
           if(@touch($ruta_log_db)){
                $status=true;
                chmod($ruta_log_db,0777);
           }  else {
               $status=false;
           }
        }
        return $status;
    }
    /*function registroEvento Es la funcion encargada de registrar y validar la tabla que registra las operaciones hechas por usuario
     * 
     */
    function registroEvento($codus,$depus,$rolus,$denomdoc,$numdoc,$addr,$proaddr,$opus){
      return true;
        //$fecha_hoy =  $this->link->conn->OffsetDate(0, $this->link->conn->sysTimeStamp);
        $csql="CREATE TABLE if not exists sgd_log_evento(usua_codi int not null, rol_id int not null, depe_codi numeric(5,0),
            fecha datetime, denominacion varying character (20), referencia_denominacion varying character(20),
            ip_address varying character(16),ip_proxy varying character(16),sgd_log_operacion varying character(50))";

        $this->link->query($csql);
        $rolus = 1;
        if(!is_array($numdoc)){
        
            $isql="insert into sgd_log_evento values($codus,$rolus,$depus,('now','localtime'),'$denomdoc','$numdoc','$addr','$proaddr','$opus')";
            //echo $isql;
            //$this->link->query($isql);
        }else{
            for($i=0;$i<count($numdoc);$i++){
                $tmpndoc=$numdoc[$i];
                $isql="insert into sgd_log_evento values($codus,$rolus,$depus,datetime('now','localtime') ,'$denomdoc','$tmpndoc','$addr','$proaddr','$opus')";
                //$this->link->query($isql);
            }
        }
        
    }
    /*function cargaData Funci&oacute; que devuelve informaci&oacute;n guardada en la base de datos del log
     * 
     * @return array $resul conjunto de datos devueltos dentro de la base de datos
     */
    function cargaData($ruta_db,$depsel,$codus){
        $this->link->close();
        $this->link->open($ruta_db);
        if($codus==0){
            if($depsel==0){
                $where="";
            }else{
                $where="where depe_codi=$depsel";
            }
        }else{
            $where="where usua_codi=$codus";
        }
        $ssql="select usua_codi, rol_id, depe_codi, fecha, denominacion, referencia_denominacion, ip_address, ip_proxy,sgd_log_operacion 
                from sgd_log_evento $where";
        $query=$this->link->query($ssql);
        $resul=array();
        $i=0;
        while ($row=$query->fetchArray(SQLITE3_ASSOC)){
            if(!isset($row['usua_codi']))continue;
            $resul[$i]['usua_codi']=$row['usua_codi'];
            $resul[$i]['rol_id']=$row['rol_id'];
            $resul[$i]['depe_codi']=$row['depe_codi'];
            $resul[$i]['fecha']=$row['fecha'];
            $resul[$i]['denominacion']=$row['denominacion'];
            $resul[$i]['referencia']=$row['referencia_denominacion'];
            $resul[$i]['ip_address']=$row['ip_address'];
            $resul[$i]['ip_proxy']=$row['ip_proxy'];
            $resul[$i]['operacion']=$row['sgd_log_operacion'];
            $i++;
        }
        return $resul;
    }
    /*
     * 
     */
    function cargasinDep($ruta_db,$codus){
        $this->link->close();
        $this->link->open($ruta_db);
        $patron=  implode(',', $codus);
        $ssql="select usua_codi, rol_id, depe_codi, fecha, denominacion, referencia_denominacion, ip_address, ip_proxy,sgd_log_operacion 
                from sgd_log_evento where usua_codi in ($patron)";
        $query=$this->link->query($ssql);
        $resul=array();
        $i=0;
        while ($row=$query->fetchArray(SQLITE3_ASSOC)){
            if(!isset($row['usua_codi']))continue;
            $resul[$i]['usua_codi']=$row['usua_codi'];
            $resul[$i]['rol_id']=$row['rol_id'];
            $resul[$i]['depe_codi']=$row['depe_codi'];
            $resul[$i]['fecha']=$row['fecha'];
            $resul[$i]['denominacion']=$row['denominacion'];
            $resul[$i]['referencia']=$row['referencia_denominacion'];
            $resul[$i]['ip_address']=$row['ip_address'];
            $resul[$i]['ip_proxy']=$row['ip_proxy'];
            $resul[$i]['operacion']=$row['sgd_log_operacion'];
            $i++;
        }
        return $resul;
    }
    /*Cierra todas las conexiones
     * 
     * 
     */
    public function __destruct() {
       // $this->link->close();
    }

}

?>
