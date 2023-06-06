<?php

/**
 * Esta clase inicia la conexión a la bd seleccionada
 * Class ConnectionHandler
 */

class ConnectionHandler
{

    var $Error;
    var $driver;
    var $rutaRaiz;
    var $conn;
    var $entidad;
    var $entidad_largo;
    var $entidad_tel;
    var $entidad_dir;
    var $querySql;
    var $limitPsql;
    var $limitOci8;
    var $limitMsql;

	/**
	 * ConnectionHandler constructor.
	 * @param $ruta_raiz
	 */
    function __construct($ruta_raiz)
    {
        if (isset($_SESSION['dependencia']) && !empty($_SESSION['ABSOL_PATH']) &&
            strpos(__FILE__,$_SESSION['ABSOL_PATH'])===FALSE) {
            unset($_SESSION['dependencia']);
            unset($_SESSION['ABSOL_PATH']);
            header("Location: $ruta_raiz/cerrar_session.php");
            exit;
        }

		include("$ruta_raiz/dbconfig.php");
		if (!defined('ADODB_ASSOC_CASE')) define('ADODB_ASSOC_CASE', 1);
		if (!defined('ADODB_FETCH_ASSOC')) define('ADODB_FETCH_ASSOC', 2);

		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		include("$ruta_raiz/adodb/adodb.inc.php");
		include_once("$ruta_raiz/adodb/adodb-paginacion.inc.php");
		include_once("$ruta_raiz/adodb/tohtml.inc.php");
		include_once $ruta_raiz . '/include/tx/classSanitize.php';
		$this->sanar = new classSanitize();
		$this->driver = $driver;

		$this->conn = NewADOConnection("$driver");
		$this->conn->charSet = 'utf8';
		$this->rutaRaiz = $ruta_raiz;

		if ($this->conn->Connect($servidor, $usuario, $contrasena, $servicio) === false)
			die("0 Error de conexi&oacute;n a la B.D.");
		$this->conn->SetFetchMode(ADODB_FETCH_ASSOC);

        $this->entidad = '';
        if (isset($entidad)) {
            $this->entidad = $entidad;
        }

        $this->entidad_largo = '';
        if (isset($entidad_largo)) {
            $this->entidad_largo = $entidad_largo;
        }

        $this->entidad_tel = '';
        if (isset($entidad_tel)) {
            $this->entidad_tel = $entidad_tel;
        }

        $this->entidad_dir = '';
        if (isset($entidad_dir)) {
            $this->entidad_dir = $entidad_dir;
        }
    }


    function imagen()
    {
        switch ($this->entidad) {
            case "CRA":
                $imagen = "png/logoCRA.gif";
                break;
            case "DNP":
                $imagen = "png/logoDNP.gif";
                break;
            case "SSPD":
                $imagen = "png/escudoColombia.jpg";
                break;
            default:
                $imagen = "";
                break;
        }
        return ($imagen);
    }

	/**
	 * Retorna False en caso de ocurrir error;
	 * @param $sql
	 * @return mixed
	 */
    function query($sql)
    {
        $cursor = $this->conn->Execute($sql);

        if (!$cursor) {
            $this->log_error("include/db/ConectionHandler - query linea: 69 ", "No se pudo realizar la consulta \n $mensaje_error ", $sql, 2);
        }

        return $cursor;
    }

	/**
	 * Retorna la fecha actual segun la BD del driver;
	 * @return string
	 */
    function sysdate()
    {
        if ($this->driver == "postgres") return "now()";
        if ($this->driver == "oci8") return "sysdate";
        if ($this->driver == "mssql") return "GETDATE()";
    }

	/**
	 * Limita el numero de regitros a mostrar en la consulta
	 * @param $numRows
	 */
    function limit($numRows)
    {
        $this->limitOci8 = "";
        $this->limitMsql = "";
        $this->limitPsql = "";
        if ($this->driver == "postgres") $this->limitPsql = "limit $numRows";
        if ($this->driver == "oci8") $this->limitOci8 = " and ROWNUM <= $numRows";
        if ($this->driver == "mssql") $this->limitMsql = " top $numRows ";
    }

	/**
	 * Retorna el controlador de la base de datos
	 */
    function getDriver()
    {
        if ($this->driver == "postgres") $this->Driver = "postgres";
        if ($this->driver == "oci8") $this->Driver = "oci8";
        if ($this->driver == "mssql") $this->Driver = "mssql";
    }


	/**
	 * Realiza una consulta a la base de datos y devuelve un record set
	 * @param $sql
	 * @return int
	 */
    function getResult($sql)
    {
        if ($sql == "") {
            $this->log_error("ConectionHandler-getResult", "No se ha especificado una consulta SQL", $sql, 2);
            $this->Error = "No ha especificado una consulta SQL";
            print($this->Error);
            return 0;
        }
        return ($this->query($sql));
    }



	/**
	 * Funcion miembro que ejecuta una instruccion sql a la base de datos.
	 * @param $numero
	 * @param $texto
	 * @param $data
	 * @param $tipo
	 */
    function log_error($numero, $texto, $data, $tipo)
    {
    	$data_show = '';
        if ($tipo == 1) {
            $array = $data;
            foreach ($array as $k => $valor) {
                $data_show .= "[$k] => $valor \n";
            }
        } else {
            $data_show = "$data";
        }

        $ruta_absoluta = $_SESSION['RUTA_ABSOLUTA'];
        $ru_dt = "$ruta_absoluta/tmp/error.log";

        if (file_exists($ru_dt)) {
            $ddf = fopen($ru_dt, 'a');
            fwrite($ddf, "[" . date("r") . "] --> $numero: $texto \n  \n $data_show");
            fclose($ddf);
        }
    }

	/**
	 * Crea un registro en la tabla especificada con los datos suministrados en el arreglo
	 * @param $table
	 * @param $record
	 * @return mixed
	 */
    function insert($table, $record)
    {
        $temp = array();
        $fieldsnames = array();
        foreach ($record as $fieldName => $field) {
            $fieldsnames[] = $fieldName;
            $temp[] = $field;
        }

        $sql = "insert into " . $table . "(" . join(",", $fieldsnames) . ") values (" . join(",", $temp) . ")";
        if ($this->conn->debug == true) {
            echo "<hr>(" . $this->driver . ") $sql<hr>";
        }
        $this->querySql = $sql;

        $res = $this->conn->Execute($sql);

        if ($res == false) {
            $this->log_error("ConectionHandler-Insert", "No se pudo insertar la consulta", $sql, 2);
        }
        return ($res);
    }

	/**
	 * Recibe como parametros: nombre de la tabla, un array
	 * con los nombres de los campos, un array con los
	 * valores, un array con los nombres de los campo id y
	 * un array con los valores de los campos id respectivamente
	 * @param $table
	 * @param $record
	 * @param $recordWhere
	 * @return mixed
	 */
    function update($table, $record, $recordWhere)
    {

        $tmpSet = array();
        $tmpWhere = array();
        foreach ($record as $fieldName => $field) {
            $tmpSet[] = $fieldName . "=" . $field;
        }

        foreach ($recordWhere as $fieldName => $field) {
            $tmpWhere[] = " " . $fieldName . " = " . $field . " ";
        }
        $sql = "update " . $table . " set " . join(",", $tmpSet) . "    where " . join(" and ", $tmpWhere);
        if ($this->conn->debug == true) {
            echo "<hr>(" . $this->driver . ") $sql<hr>";
        }
        $res = $this->conn->Execute($sql);
        if (!$res) {
            $this->log_error("ConectionHandler", "No se pudo Actualizar la consulta", $sql, 2);
        }

        return ($res);
    }


	/**
	 * Recibe como parametros: nombre de la tabla, un array con los
	 * nombres de los campos id, y un array con los valores de los id.
	 * @param $table
	 * @param $record
	 * @return mixed
	 */
    function delete($table, $record)
    {

        $temp = array();

        foreach ($record as $fieldName => $field) {
            $tmpWhere[] = "  " . $fieldName . "=" . $field;
        }
        $sql = "delete from " . $table . " where " . join(" and ", $tmpWhere);

        //print("*** $sql ****");
        if ($this->conn->debug == true) {
            echo "<hr>(" . $this->driver . ") $sql<hr>";
        }
        return ($this->query($sql));

    }

	/**
	 * Permite consultar el siguiente id de las secuencia suministrada.
	 * @param $secName
	 * @return int
	 */
    function nextId($secName)
    {
        if ($this->conn->hasGenID){
            return $this->conn->GenID($secName);
        } else {
            $retorno = -1;

            if ($this->driver == "oracle") {
                $q = "select $secName.nextval as SEC from dual";
                $this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
                $rs = $this->query($q);
                //$rs!=false &&
                if (!$rs->EOF) {
                    $retorno = $rs->fields['SEC'];
                    //print ("Retorna en la funcion de secuencia($retorno)");
                }
            }
            return $retorno;
        }
    }

	/**
	 * Cambia los caracteres que tengan acento a mayúsculas
	 * @param $string
	 * @return string
	 */
    static function fullUpper($string)
    {
        return strtr(strtoupper($string), array(
            "à" => "À",
            "è" => "È",
            "ì" => "Ì",
            "ò" => "Ò",
            "ù" => "Ù",
            "á" => "Á",
            "é" => "É",
            "í" => "Í",
            "ó" => "Ó",
            "ú" => "Ú",
            "â" => "Â",
            "ê" => "Ê",
            "î" => "Î",
            "ô" => "Ô",
            "û" => "Û",
            "ç" => "Ç",
            "ñ" => "Ñ",
        ));
    }

	/**
	 * Convertir caracteres especiales a minuscula para busquedas
	 * @param $string
	 * @return string
	 */
    static function fullLower($string)
    {
        return strtr(strtolower($string), array(
            "À" => "à",
            "È" => "è",
            "Ì" => "ì",
            "Ò" => "ò",
            "Ù" => "ù",
            "Á" => "á",
            "É" => "é",
            "Í" => "í",
            "Ó" => "ó",
            "Ú" => "ú",
            "Â" => "â",
            "Ê" => "ê",
            "Î" => "î",
            "Ô" => "ô",
            "Û" => "û",
            "Ç" => "ç",
            "Ñ" => "ñ",
        ));
    }

	/**
	 * Cuenta los registros retornados -- No usar este metodo.
	 * Se recomienda usar una consulta que haga este trabajo directamente.
	 * @param $sql
	 * @return int
	 */
    function recordCountSql($sql)
    {

        $rs = $this->conn->execute($sql);

        if (!$rs) {
            $this->conn->debug = true;
            $this->conn->Execute($sql);
            die();
        }

        $a = 0;

        while (!$rs->EOF) {
            ++$a;
            $rs->MoveNext();
        }

        return $a;
    }


	/**
	 * Función que obtiene todos los registros de una tabla, EVITAR usar esta funcion para consultas pesadas
	 * @param $table
	 * @param $array
	 * @param null $arrayWhere
	 * @param null $orderBy
	 * @return string
	 */
    function getSelectStringSql($table, $array, $arrayWhere = null, $orderBy = null)
    {
        $where = "";
        $orderby = "";

        if ($orderBy != null) {
            $orderby = "ORDER BY " . $orderBy;
        }

        if ($arrayWhere != null) {
            $tmpWhere = array();
            foreach ($arrayWhere as $fieldName => $field) {
                $tmpWhere[] = " " . $fieldName . " = " . $field . " ";
            }
            $where = " where " . join(" and ", $tmpWhere);
        }

        $fieldsnames = array();

        foreach ($array as $fieldName => $field) {
            $fieldsnames[] = $field;
        }
        $fieldsnames = array_reverse($fieldsnames);
        $sql = "select " . join(",", $fieldsnames) . " from " . $table . " " . $where . " " . $orderby;

        return $sql;
    }

	/**
	 * Une dos nombres de columna
	 * @param $string1
	 * @param $string2
	 * @return string
	 */
    function concat($string1, $string2)
    {
        if ($this->driver == "postgres") {
            $retorno = $string1 . ' || ' . $string2;
            return $retorno;
        }
        if ($this->driver == "oci8") {
            $retorno = $string1 . ' || ' . $string2;
            return $retorno;
        }
        if ($this->driver == "mssql") {
            $retorno = $string1 . ' || ' . $string2;
            return $retorno;
        }
    }


    /* Sanitizar los string */
    function satinize($string)
    {
        $sanar = $this->sanar;
        $string = $sanar->noSql($string);
        return $string;
    }

    function upperCase($string)
    {
        if ($this->driver == "postgres") {
            $retorno = " upper (" . $string . ") ";
            return $retorno;
        }
        if ($this->driver == "oci8") {
            $retorno = $string;
            return $retorno;
        }
        if ($this->driver == "mssql") {
            $retorno = $string;
            return $retorno;
        }
    }

	/**
	 * Comvierte el parametro a texto dependiendo de la base de datos
	 * @param $string
	 * @return string
	 */
    function castText($string)
    {
        if ($this->driver == "postgres") {
            $retorno = $string . "::text";
            return $retorno;
        }
        if ($this->driver == "oci8" or $this->driver == "mssql") {
            $retorno = $string;
            return $retorno;
        }
    }

}
