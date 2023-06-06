<?php
namespace Orpyca\webService;

/**
 * ORFEOConnect - ORFEO creation and transport class.
 *
 * @see https://repo.correlibre.org/argopublico/argogpl
 * The ORFEOConnect OrfeoGPL project
 *
 * @author    cesar.gonzalez@hdsas.co
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

class ORFEOConnect
{
	use ORFEOerrorTrait;

    //=============================================
    // CONSTANT DEFINITIONS
    //=============================================

    const RUTA_RAIZ = '../';
    /**
     * Listado de documentos.
     * @var array
     */

    public $tipoDocumento = array(
        "CC" => array('cod' => 0, 'desc' => 'Cedula'),
        "TI" => array('cod' => 1, 'desc' => 'Tarjeta de identidad'),
        "CE" => array('cod' => 2, 'desc' => 'Cedula de Extrangeria'),
        "PA" => array('cod' => 3, 'desc' => 'Pasaporte'),
        "RC" => array('cod' => 12, 'desc' => ''),
        "NI" => array('cod' => 4, 'desc' => 'Nit'),
        "PJ" => array('cod' => 8, 'desc' => ''),
        "EO" => array('cod' => 9, 'desc' => ''),
        "RM" => array('cod' => 10, 'desc' => ''));

    /**
     * database object.
     * Receives the connection to the database generated
     * by adodb and is distributed to all objects.
     * @var ConnectionHandler
     */
    protected $db;

    /**
     * Objeto de la clase historico permite registrar las acciones
     * en el sistema y llevar la trazabilidad.
     * @var Historico
     */
    protected $hist;

    /**
     * Permite Obtener los elementos de la división politica
     * de ciudad pais municipio.
     * @var Municipio
     */
    protected $tmp_mun;

    /**
     * Generacion de radicados.
     * @var Radicacion
     */
    protected $rad;

    /**
     * Generacion de expedientes.
     * @var Expediente
     */
    protected $expe;

    /**
     * Ruta de los archivos del sistema.
     * bodega de imagenes
     * @var string
     */
    private $contentPath;
    /**
     * Arreglo que retorna la captura de errores.
     * @var array
     */

    /**
     * Variable del sistema que permite
     * utilizar datos de prueba o reales.
     * @var string
     */
    private $ambiente;


    /**
     * Variables de configuración para usar
     * el servicios de gse en la firma
     * de documentos.
     * @var array
     */
    private $gse;

    /**
     * Longitud para los radicados.
     * @var integer
     */
    private $lonRadicados = '14';

    /**
     * ORFEOConnect constructor.
     * @param $objOrfeo Arreglo con los objetos instanciados del historico,
     * expedientes, radicacion, transacciones y based de datos de orfeo.
     * @param bool $debug Oculta o muestra las consultas realizadas y los
     * errores sql que se generan.
     */
    public function __construct($objOrfeo, $debug = false)
    {
        $this->db      = $objOrfeo['dbx'];
        $this->hist    = $objOrfeo['hist'];
        $this->tmp_mun = $objOrfeo['tmp_mun'];
        $this->rad     = $objOrfeo['rad'];
        $this->expe    = $objOrfeo['expe'];

        $this->db->conn->debug = $debug;

    }

    /**
     * Retorna el codigo de la extensiones
     * si no existe retorna false
     *
     * @param $extension
     *
     * @return string | boolean
     */
    private function tipoAnexo($extension)
    {

        $consulta = vsprintf("SELECT
            ANEX_TIPO_CODI
            FROM
            ANEXOS_TIPO
            WHERE
            ANEX_TIPO_EXT = '%s'", array(strtolower($extension)));

        $rs = $this->db->conn->Execute($consulta);

        if ($rs && !$rs->EOF) {
            return $salida = $rs->fields['ANEX_TIPO_CODI'];
        }

        $this->setError('extension_invalida');
        return false;
    }

    /**
     * funcion encargada regenerar un archivo enviado en base64
     * @param string $ruta ruta donde se almacenara el archivo
     * @param string $archivo archivo codificado en base64
     * @param string $nombre nombre del archivo
     * @return boolean retorna si se pudo decodificar el archivo
     */
    private function subirArchivo($ruta, $archivo, $nombre)
    {
        try {
            //direccion donde se quiere guardar los archivos
            $fp = @fopen("{$ruta}{$nombre}", "w");
            $bytes = base64_decode($archivo);
            $salida = true;

            if (is_array($bytes)) {
                foreach ($bytes as $k => $v) {
                    $salida = ($salida && fwrite($fp, $bytes));
                }
            } else {
                $salida = fwrite($fp, $bytes);
            }
            fclose($fp);
        } catch (Exception $e) {
            $this->setError('archivo_escritura');
            return false;
        }
        return $salida;
    }

    /**
     * Valida si la estructura del radicado es correcta teneiendo
     * en cuenta le tamaño predefinido por la aplicación en las
     * variables de configuración
     * @param $rad
     * @return bool
     */
    private function validarRadicado($rad)
    {

        if (empty($rad) || strlen($rad) != $this->lonRadicados) {
            $this->setError('radicado_invalido');
            return false;
        }

        return true;
    }

    /**
     * Verifica el formato del correo electronico
     * @param string $correo correo a verificar
     * @return boolean
     */
    private function verificarCorreo($correo)
    {
        return preg_match("/(^\w+([\.-] ?\w+)*@\w+([\.-]?\w+)*(\.\w+)+)/", $correo);
    }

    /**
     * Cuenta la cantidad de anexos de un radicado, permitiendo
     * saber que numeración se le coloca al siguiente anexo.
     * El numero de radicado permite consultador la tabla asociada
     * de anexos
     * @param $radiNume
     * @return integer
     */
    private function numeroAnexos($radiNume)
    {
        $salida = 0;

        $consulta = vsprintf(
            "SELECT
                        COUNT(1) AS NUM_ANEX
                    FROM ANEXOS
                    WHERE ANEX_RADI_NUME = %u", array($radiNume));


        $rs = $this->db->conn->Execute($consulta);

        if ($rs && !$rs->EOF)
            $salida = $rs->fields['NUM_ANEX'];

        return $salida;
    }

    /**
     * Retorna el numero de anexo con el mayor numero asignado
     * Consulta sobre la tabla de anexos asociada a un radicado
     * @param $radiNume
     * @param $db
     * @return integer
     */
    private function maxRadicados($radiNume)
    {
        $consulta = vsprintf("SELECT
                        max(ANEX_NUMERO) AS NUM_ANEX
                    FROM ANEXOS
                    WHERE ANEX_RADI_NUME = %u", array($radiNume));

        $rs = $this->db->conn->Execute($consulta);

        if ($rs && !$rs->EOF)
            $salida = $rs->fields['NUM_ANEX'];

        return $salida;
    }

    /**
     * Define la ruta de donde estan ubicados los archivos
     * en la bodega de imagenes del sistema permitiendo
     * ubicar los documentos asociados a los radicados, anexos y
     * adjuntos de los expedientes
     * @param string $contentPath
     */
    public function setContentPath($contentPath)
    {
        $this->contentPath = $contentPath;
    }

    /**
     * Define si las variables a usar de la configuración
     * corresponden a datos de prueba o de producción
     * @param string $ambiente
     */
    public function setAmbiente($ambiente)
    {
        $this->ambiente = $ambiente;
    }

    /**
     * Variables de configuración de la firma de gse
     * para firma centralizada
     * @param array $gse
     */
    public function setGseConfig($gse)
    {
        $this->gseConfig = $gse;
    }



    /**
     * Logitud de los radicados definido por el usuario
     * Esta funcion es funcinal si el sistem tiene mas o menos
     * de 14 digitos en el numero de radicado, de lo contrario
     * se utiliza el valor por defecto del sistema
     * @param integer $lonRadicados
     */
    public function setSizeRad($lonRadicados)
    {
        $this->lonRadicados = $lonRadicados;
    }

    /** PNN 1
     * Permite cargar una imagen a un numero de radicado asignado
     * @param file $bytes
     * @param integer $nombreArchivo
     * @return boolean
     */
    private function UploadFile($bytes, $nombreArchivo)
    {
        $path = '';
        $output = true;

        $var = explode(".", $nombreArchivo);
        $path .= $this->contentPath . substr($var[0], 0, 4);
        $path .= "/" . substr($var[0], 4, 3);
        $path .= "/docs/" . $nombreArchivo;

        if (!$fp = fopen("$path", "w")) {
            $this->setError('archivo_ruta');
            return false;
        }

        $bytes = base64_decode($bytes);

        if (is_array($bytes)) {
            foreach ($bytes as $k => $v) {
                $output = ($output && fwrite($fp, $bytes));
            }
        } else {
            $output = fwrite($fp, $bytes);
        }

        fclose($fp);

        if ($output) {
            return true;
        }

        $this->setError('archivo_ruta');
        return false;
    }

    /** PNN 2
     * Relacionar un radicado de entrada con uno de salida para
     * poder posteriormente archivar el documento o tramitarlo y
     * ver cual fue la respuesta que se dio.
     * @param integer $numrad
     * @param integer $radicadoSalida
     * @return boolean
     */
    public function asociarRadicado($numrad, $radicadoSalida)
    {
        if (!$this->validarRadicado($numrad)
            && !$this->validarRadicado($radicadoSalida)) {
            return false;
        }

        $sql21 = "SELECT max(anex_numero) + 1 as SIG
                    from anexos
                    where anex_RADI_NUME = '%u' ";

        $sql21 = vsprintf($sql21, array($numrad));

        $rs_Anex = $this->db->conn->Execute($sql21);

        $next = $rs_Anex->fields['SIG'];
        $next = empty($next) ? "1" : $next;

        $anexo = str_pad($next, 5, "0", STR_PAD_LEFT);
        $numradAnexo = $numrad . "_" . $anexo;
        $rad_sal = $radicadoSalida;

        $texto = "Rad " . $rad_sal . " Anexo automatico Servicios";

        $temp_1 = array($numrad, $numradAnexo, $next, $texto, $rad_sal);

        $sql22 = "  INSERT INTO anexos(
                    anex_radi_nume,
                    anex_codigo,
                    anex_solo_lect,
                    anex_creador,
                    anex_nomb_archivo,
                    anex_borrado,
                    anex_tipo,
                    anex_numero,
                    anex_desc,
                    radi_nume_salida
                    )VALUES(
                     %u
                    ,'%u'
                    ,'S'
                    ,'Servios de conexión'
                    ,''
                    ,'N'
                    , 27
                    , %u
                    ,'%u'
                    ,'%u')";

        $s_anexo = vsprintf($sql22, $temp_1);

        $temp_2 = array($numrad, $rad_sal);

        $sql23 = "UPDATE
                        RADICADO
                      SET
                        RADI_NUME_DERI = %u
                      WHERE
                        RADI_NUME_RADI = %u";

        $sql_upd_e = vsprintf($sql23, $temp_2);

        if ($this->db->conn->Execute($s_anexo)
            and $this->db->conn->Execute($sql_upd_e)) {
            return true;
        }

        $this->setError('insertar');
        return false;
    }

    /** PNN 4
     * Crea un radicado con el numero de usuario del sistema el
     * tipo documento, tipo de radicado, asunto y la referencia
     * al numero de guia o radicado del sistema que radica si
     * existe
     * @param integer $funcionario Documento del funcionario o Correo
     * con el que esta registrado en la aplicación. Cuenta a la cual se le
     * asignara el radicado.
     * @param integer $tipo tipo de radicado 1 salida, 2 entrada
     * , 3 memorando, 4 edictos
     * @param integer $td tipo documental
     * @param string $asunto descripción del contenido del radicado
     * @param string $referencia numero de identificación del
     * documento si tiene consecutivo de otra entidad
     * @param array $ciudadano Datos de la persona que realiza el radicado
     *
     * @return bool|string Numero de radicado
     */
    public function crearRadicado($funcionario,
                                  int $tipo,
                                  int $td,
                                  string $asunto,
                                  string $referencia,
                                  array $ciudadano = [])
    {

        if(empty($tipo * $td) || empty($asunto || ($tipo > 0 && $tipo < 10) )){
            $this->setError('arg_insuficientes');
            return false;
        }

        $usuario   = $this->getUsuarioSelect($funcionario);

        $depe_codi = $usuario['dependencia'];
        $usua_codi = $usuario['codusuario'];
        $usua_doc  = $usuario['documento'];
        $usua_nivel = $usuario['nivel'];

        if (!empty($ciudadano)) {
            $usua_tipo = $ciudadano['tipoDocumento']? $ciudadano['tipoDocumento'] : 0;
            $usua_doc = $ciudadano['documento']? $ciudadano['documento'] : '0';
            $usua_ema = $ciudadano['email']? $ciudadano['email'] : null;
            $usua_nom = $ciudadano['nombre']? $ciudadano['nombre'] : null;
            $usua_apell = $ciudadano['apellido']? $ciudadano['apellido'] : 'anonimo';
            $usua_dir = $ciudadano['direccion']? $ciudadano['direccion'] : 'no registra';
            $usua_tel = $ciudadano['telefono']? $ciudadano['telefono'] : 'no registra' ;
        }else{
            $usua_tipo = 0;
            $usua_doc = '0';
            $usua_ema = null;
            $usua_nom = 'anonimo';
            $usua_apell = 'anonimo';
            $usua_dir = 'no registra';
            $usua_tel = 'no registra';
        }

        $referencia = $referencia ? $referencia : null;

        $rad = $this->rad;

        $rad->dependenciaRadicacion = $depe_codi;
        $rad->dependencia = $depe_codi;
        $rad->usuaDoc    = $usua_doc;
        $rad->noDigitosDep = 3;
        $rad->usuaCodi   = $usua_codi;
        $rad->codiNivel  = $usua_nivel;
        $rad->idPais = 170;

        //Carpeta de entrada por defecto
        $rad->radiUsuaActu = $usua_codi;
        $rad->radiDepeActu = $depe_codi;

        $rad->radiCuentai = trim($referencia);
        $rad->mrecCodi = 10;

        $rad->radiFechOfic = " now() ";

        $rad->radiNumeDeri = null;
        $rad->radiDepeRadi = "'{$depe_codi}'";
        $rad->tdocCodi = $td;
        $rad->carpCodi = $tipo ? $tipo : 0;
        $rad->carpPer = 0;
        $rad->raAsun = substr(htmlspecialchars(stripcslashes($asunto)), 0, 349);
        $nu_radicado = $rad->newRadicado($tipo, $depe_codi);

        if (!$this->validarRadicado($nu_radicado)) {
            return false;
        }

        $this->hist->insertarHistorico(array($nu_radicado)
            , $depe_codi
            , $usua_codi
            , $depe_codi
            , $usua_codi
            , " "
            , 2);

        $temp13 = array(
            $nu_radicado,
            $usua_doc,
            $usua_ema,
            $usua_nom. ' ' .$usua_apell,
            $usua_nom,
            $usua_apell,
            $usua_dir,
            $usua_tel,
            $usua_tipo);

        $s_direcciones = "INSERT INTO sgd_dir_drecciones (
            sgd_dir_codigo
            ,sgd_dir_tipo
            ,radi_nume_radi
            ,sgd_doc_fun
            ,sgd_dir_mail
            ,sgd_dir_nomremdes
            ,sgd_dir_nombre
            ,sgd_dir_apellido               
            ,sgd_dir_direccion
            ,sgd_dir_telefono            
            ,id_cont
            ,id_pais            
            ,muni_codi
            ,dpto_codi
            ,sgd_dir_tdoc
            ,sgd_oem_codigo             
        ) VALUES (
            (select nextval('sec_dir_direcciones'))
            , 1
            , %u
            ,'%s'            
            ,'%s'            
            ,'%s'                        
            ,'%s'            
            ,'%s'            
            ,'%s'            
            ,'%u'
            ,1
            ,170
            ,1
            ,11
            ,2
            ,%u
        )";

        $s_direcciones = vsprintf($s_direcciones, $temp13);

        $this->db->conn->Execute($s_direcciones);

        return $nu_radicado;

    }

    /** PNN 3
     * Retorna un vector con la informacion de un funcionario
     * en particular de Orfeo
     * Anteriormente nombrado darUsuarioMIG
     * @param $data
     * @return array | boolean $usuario
     */
    public function getUsuarioSelect($data)
    {
        if(empty($data)){
            $this->setError('usuario_noexiste');
            return false;
        }

        $email = $this->verificarCorreo($data);

        $usuario = array();

        if ($email) {
            $sql = vsprintf("select
                        u.DEPE_CODI,
                        u.USUA_CODI,
                        u.USUA_DOC,
                        u.USUA_EMAIL,
                        u.USUA_NOMB,                        
                        u.USUA_LOGIN,
                        u.CODI_NIVEL,
                        d.depe_nomb
                    from usuario u
                        inner join dependencia d on (d.depe_codi=u.depe_codi)
                    where
                        u.USUA_ESTA = '1' and
                        UPPER(u.USUA_EMAIL) = UPPER('%s')", array($data));
        } else {
            $sql = "SELECT
                        u.DEPE_CODI,
                        u.USUA_CODI,
                        u.USUA_DOC,
                        u.USUA_EMAIL,
                        u.CODI_NIVEL,
                        u.USUA_NOMB,
                        u.USUA_LOGIN,                        
                        d.depe_nomb
                    from usuario u
                    inner join dependencia d on (d.depe_codi=u.depe_codi)
                    where u.USUA_DOC = '%u' and u.USUA_ESTA = '1'";
            $sql = vsprintf($sql, array($data));
        }

        $rs = $this->db->conn->Execute($sql);

        if(!$rs || $rs->EOF){
            $this->setError('arg_insuficientes');
            return false;
        }

            $usuario['email'] = $rs->fields['USUA_EMAIL'];
            $usuario['login'] = $rs->fields['USUA_LOGIN'];
            $usuario['codusuario'] = $rs->fields['USUA_CODI'];
            $usuario['nivel'] = $rs->fields['CODI_NIVEL'];
            $usuario['dependencia'] = $rs->fields['DEPE_CODI'];
            $usuario['documento'] = $rs->fields['USUA_DOC'];
            $usuario['nombre'] = $rs->fields['USUA_NOMB'];
            $usuario['nombre_dependencia'] = $rs->fields['DEPE_NOMB'];

        return $usuario;

    }

    /** PNN 5
     * funcion que crea un Anexo, y ademas decodifica el anexo enviasdo en base64
     * @param integer $radiNume
     * @param base64 $file archivo codificado en base64
     * @param string $filename nombre original del anexo, con extension
     * @param $usuario
     * @param string $descripcion descripcion del anexo
     * @return string mensaje de error en caso de fallo o el numero del
     * anexo en caso de exito
     */
    public function crearAnexo($radiNume, $file, $filename, $usuario, $descripcion)
    {
        $usuario = $this->verificarCorreo($usuario);

        if (empty($usuario)) {
            $this->setError('correo_novalido');
            return false;
        }

        $usuario = $this->getUsuarioSelect($usuario);

        if (empty($usuario)) {
            $this->setError('arg_insuficientes');
            return false;
        }

        $ruta = $this->contentPath . substr($radiNume, 0, 4)
            . "/" . substr($radiNume, 4, 3) . "/docs/";

        $numAnexos = $this->numeroAnexos($radiNume) + 1;
        $maxAnexos = $this->maxRadicados($radiNume) + 1;
        $extension = substr($filename, strrpos($filename, ".") + 1);
        $numAnexo = ($numAnexos > $maxAnexos) ? $numAnexos : $maxAnexos;
        $nombreAnexo = $radiNume . substr("00000" . $numAnexo, -5);
        $subirArchivo = $this->subirArchivo($ruta, $file, $nombreAnexo . "." . $extension);

        if (!$subirArchivo) {
            $this->setError('archivo_escritura');
            return false;
        }

        $sizeAnexo = $subirArchivo / 1024; //tamano en kilobytes
        $fechaAnexado = date('Y-m-d h:i:s A');

        $tipoAnexo = $this->tipoAnexo($extension);

        $tipoAnexo = ($tipoAnexo) ? $tipoAnexo : "NULL";

        $sqlInsert = "INSERT
            INTO ANEXOS (
                 ANEX_CODIGO
                ,ANEX_RADI_NUME
                ,ANEX_TIPO
                ,ANEX_TAMANO
                ,ANEX_SOLO_LECT
                ,ANEX_CREADOR
                ,ANEX_DESC
                ,ANEX_NUMERO
                ,ANEX_NOMB_ARCHIVO
                ,ANEX_ESTADO
                ,SGD_REM_DESTINO
                ,ANEX_FECH_ANEX
                ,ANEX_BORRADO)
                VALUES(
                    '$nombreAnexo'
                    ,$radiNume
                    ,$tipoAnexo
                    ,$sizeAnexo
                    ,'n'
                    ,'{$usuario['login']}'
                    ,'$descripcion'
                    , $numAnexo
                    ,'$nombreAnexo.$extension'
                    , 0
                    , 1
                    , '{$fechaAnexado}'
                    , 'N')";


        if ($this->db->conn->Execute($sqlInsert)) {
            $consultaVerificacion = vsprintf("
                            SELECT
                                ANEX_CODIGO
                            FROM
                                ANEXOS
                            WHERE ANEX_CODIGO = '%u'", array($nombreAnexo));
            $rs = $this->db->conn->Execute($consultaVerificacion);

            return array(
                'anexCodigo' => $rs->fields['ANEX_CODIGO'],
                'anexRadiNume' => $radiNume,
                'anexTipo' => $tipoAnexo,
                'anexTamano' => $sizeAnexo,
                'anexSoloLect' => 'n',
                'anexCreador' => $usuario['login'],
                'anexDesc' => $descripcion,
                'anexNumero' => $numAnexo,
                'anexNombArchivo' => $nombreAnexo.$extension,
                'anexEstado' => 0,
                'sgdRemDestino' => 1,
                'anexFechAnex' => $fechaAnexado,
                'anexBorrado' => 'N' );

        }

        $this->setError('insertar');
        return false;
    }

    /** PNN 7
     * Retorna un documento firmado
     * @param type $bytes
     * @param type $filename
     * @return string base64
     */
    public function firmaDigital($bytes, $filename)
    {
        $this->UploadFile($bytes, $filename);

        $path = substr($filename, 0, 4) . "/" . substr($filename, 4, 3) . "/docs/" . $filename;

        shell_exec("java -jar /var/www/html/firma/PortableSigner/PortableSigner.jar
            -n -t /var/www/html/bodega/{$path}
            -o {$this->contentPath}" . str_replace(".pdf", "F.pdf", $path) . "
            -s /var/www/html/firma/pnn.p12 -p 5656 2>&1");

        $file = file_get_contents($this->contentPath . str_replace(".pdf", "F.pdf", $path));

        $serialized = base64_encode($file);

        $radicado = substr($filename, 0, 14);
        $this->cambiarImagenRad($radicado, 'pdf', $serialized);

        return $serialized;
    }

    /** PNN 8
     * Remplaza la imagen actual asociada al radicado por la enviada
     * @param type $numRadicado
     * @param type $ext
     * @param type $file
     * @return bool
     */
    public function cambiarImagenRad($numRadicado, $ext, $file)
    {
        $sql = vsprintf("SELECT
            count(1) as radicado
            FROM RADICADO
            WHERE RADI_NUME_RADI = %u", array($numRadicado));

        $rs = $this->db->conn->Execute($sql);

        if (!$rs->EOF) {
            $year = substr($numRadicado, 0, 4);
            $depe = substr($numRadicado, 4, 3);
            $path = "/{$year}/{$depe}/docs/{$numRadicado}.{$ext}";

            $update = vsprintf("UPDATE
                RADICADO SET RADI_PATH='%s'
                where RADI_NUME_RADI='%u'", array($path, (int)$numRadicado));

            if ($this->UploadFile($file, $numRadicado . '.' . $ext)) {
                $this->db->conn->Execute($update);
                return $path;
            } else {
                $this->setError('archivo_escritura');
                return false;
            }
        } else {
            $this->setError('radicado_noexiste');
            return false;
        }
    }

    /** PNN 9
     * Retorna el estado del radicado con los estados:
     * Con respuesta
     * En tramite
     * @param type $radicado
     * @return array
     */
    public function darEstado($radicado)
    {
        $sql1 = "
                SELECT
                    CASE WHEN (SELECT
                                COUNT (1) AS NUM
                                FROM
                                ANEXOS
                                WHERE
                                ANEX_RADI_NUME=RADI_NUME_RADI
                                AND RADI_NUME_SALIDA IS NOT NULL) > 0
                    THEN 'CON RESPUESTA ASIGNADA'
                    ELSE 'EN TRAMITE'
                    END AS ESTADO
                FROM
                    RADICADO
                WHERE
                    RADI_NUME_RADI = %u ";

        $sql_estado = vsprintf($sql1, array($radicado));

        $rs_estado = $this->db->conn->Execute($sql_estado);

        $estado['estado'] = $rs_estado->fields['ESTADO'];

        $sql = "SELECT
                    COUNT(1) K
                FROM
                    ANEXOS
                WHERE
                    ANEX_RADI_NUME = %u";

        $sql_anexos = vsprintf($sql, array($radicado));

        $rs_anexos = $this->db->conn->Execute($sql_anexos);

        $estado['anexos'] = $rs_anexos->fields['K'];

        $sql_creador = vsprintf("SELECT
                                    U.USUA_NOMB
                                   ,D.DEPE_CODI
                                   ,A.ANEX_RADI_FECH
                                   ,D.DEPE_NOMB
                                   ,A.RADI_NUME_SALIDA
                                FROM USUARIO U
                                INNER JOIN ANEXOS A ON (
                                                A.ANEX_CREADOR=U.USUA_LOGIN
                                            AND A.RADI_NUME_SALIDA IS NOT NULL
                                            AND A.ANEX_RADI_NUME = %u)
                        INNER JOIN DEPENDENCIA D ON (U.DEPE_CODI=D.DEPE_CODI)", array($radicado));

        $rs_creador = $this->db->conn->Execute($sql_creador);

        $estado['creador'] = $rs_creador->fields['USUA_NOMB'];
        $estado['dependencia_creador'] = $rs_creador->fields['DEPE_NOMB'];
        $estado['fecha'] = $rs_creador->fields['ANEX_RADI_FECH'];
        $estado['radicado_respuesta'] = $rs_creador->fields['RADI_NUME_SALIDA'];

        return $estado;
    }

    /** PNN 10
     * Consulta un radicado y sus anexos
     * @param $radicado radicado padre o un anexo
     * @return array
     */
    public function consultarAnexos($radicado)
    {
        if (!$this->validarRadicado($radicado)) {
            return false;
        }

        $attach_rad = array();

        $valRad = substr($radicado, 0, $this->lonRadicados);

        $sql = vsprintf("SELECT
                            anex_codigo DOCU ,
                            anex_tipo_ext EXT ,
                            anex_tamano TAMA ,
                            anex_solo_lect RO ,
                            usua_nomb CREA ,
                            anex_desc DESCR ,
                            anex_nomb_archivo NOMBRE ,
                            ANEX_CREADOR ,
                            ANEX_ORIGEN ,
                            ANEX_SALIDA ,
                            ANEX_NUMERO ,                            
                            RADI_NUME_SALIDA AS RADI_NUME_SALIDA ,
                            ANEX_ESTADO ,
                            SGD_PNUFE_CODI ,
                            SGD_DOC_SECUENCIA ,
                            SGD_DIR_TIPO ,
                            SGD_REM_DESTINO,
                            SGD_DOC_PADRE ,
                            SGD_TPR_CODIGO ,
                            SGD_APLI_CODI ,
                            SGD_TRAD_CODIGO ,
                            SGD_TPR_CODIGO ,
                            ANEX_TIPO ,
                            TO_CHAR(sgd_fech_doc,'YYYY-MM-DD HH24:MI:SS AM')   AS FECDOC ,
                            TO_CHAR(anex_fech_anex,'YYYY-MM-DD HH24:MI:SS AM') AS FEANEX ,
                            ANEX_TIPO                                          AS NUMEXTDOC ,
                            ANEX_DEPE_CREADOR
                        FROM
                            anexos,
                            anexos_tipo,
                            usuario
                        WHERE
                                anex_radi_nume= '%u'
                            AND anex_tipo       =anex_tipo_codi
                            AND anex_creador    =usua_login
                            AND anex_borrado    ='N'
                            ORDER BY
                            anex_codigo,
                            radi_nume_salida,
                            sgd_dir_tipo,
                            anex_numero", array($valRad));

        $rs = $this->db->conn->Execute($sql);

        while (!$rs->EOF) {

            $attach_rad[] = array(
                'anexCodigo' => $rs->fields["DOCU"],
                'anexRadiNume' => $valRad,
                'anexTipo' => $rs->fields["ANEX_TIPO"],
                'anexTamano' => $rs->fields["TAMA"],
                'anexSoloLect' => $rs->fields["RO"],
                'anexCreador' => $rs->fields["CREA"],
                'anexDesc' => $rs->fields["DESCR"],
                'anexNumero' => $rs->fields["ANEX_NUMERO"],
                'anexNombArchivo' => $rs->fields["NOMBRE"],
                'anexEstado' => $rs->fields["ANEX_ESTADO"],
                'sgdRemDestino' => $rs->fields["SGD_REM_DESTINO"],
                'anexFechAnex' => $rs->fields["FEANEX"],
                'anexBorrado' => 'N' );

            $rs->MoveNext();
        }

        return $attach_rad;
    }

    /**
     * Regresa en un arreglo la información del radicado solicitado
     * @param $radicado
     * @return array | bool
     */
    public function consultarRadicado($radicado)
    {

        if (!$this->validarRadicado($radicado)) {
            return false;
        }

        $valRad = substr($radicado, 0, $this->lonRadicados);

        $sql = "SELECT "
            . "r.RA_ASUN, "
            . "r.RADI_NUME_RADI, "
            . "r.RADI_NUME_DERI, "
            . "r.RADI_FECH_RADI, "
            . "r.TDOC_CODI, "
            . "r.RADI_DESC_ANEX, "
            . "r.RADI_DEPE_ACTU, "
            . "d.DEPE_NOMB, "
            . "r.RADI_USUA_ACTU, "
            . "r.RADI_PATH, "
            . "r.RADI_CUENTAI, "
            . "u.USUA_NOMB "
            . "FROM RADICADO r, "
            . "USUARIO u, "
            . "DEPENDENCIA d "
            . "WHERE "
            . "u.USUA_CODI = r.RADI_USUA_ACTU "
            . "AND r.RADI_DEPE_ACTU = d.DEPE_CODI "
            . "AND u.DEPE_CODI = d.DEPE_CODI "
            . "AND r.RADI_NUME_RADI = '%u'";

        $sql = vsprintf($sql, $valRad);

        $rs = $this->db->conn->Execute($sql);

        if ($rs && !$rs->EOF) {
            if(empty($rs->fields['RADI_PATH'])){
                $imdata['archivo_b64'] = '';
            }else{
                $filename = $this->contentPath . $rs->fields['RADI_PATH'];
                $im = file_get_contents($filename);
                $serializado = base64_encode($im);
                $imdata['archivo_b64'] = $serializado;
            }
            $imdata['radicado'] = $rs->fields['RADI_NUME_RADI'];
            $imdata['radicadoPadre'] = $rs->fields['RADI_NUME_DERI'];
            $imdata['asunto'] = $rs->fields['RA_ASUN'];
            $imdata['fechaRadicación'] = $rs->fields['RADI_FECH_RADI'];
            $imdata['tipoDocumental'] = $rs->fields['TDOC_CODI'];
            $imdata['cuentaInterna'] = $rs->fields['RADI_CUENTAI'];
            $imdata['codigoDependenciaActual'] = $rs->fields['RADI_DEPE_ACTU'];
            $imdata['dependenciaActual'] = $rs->fields['DEPE_NOMB'];
            $imdata['codigoUsuarioActual'] = $rs->fields['RADI_USUA_ACTU'];
            $imdata['usuarioActual'] = $rs->fields['USUA_NOMB'];

            return $imdata;

        } else {
            $this->setError('radicado_noexiste');
            return false;
        }
    }

    /**
     * Actuliza la trd de un radicado y registra la
     * transaccion en el historico de la aplicación
     *
     * @param $dependencia
     * @param $serie
     * @param $sub_serie
     * @param $tipo
     * @param $radicado
     * @param $cedula
     *
     * @return boolean
     */
    public function actualizarTrd($dependencia, $serie, $sub_serie,
                                  $tipo, $radicado, $cedula)
    {
        $dsql = vsprintf("DELETE
                        FROM
                            SGD_RDF_RETDOCF
                        WHERE
                            RADI_NUME_RADI = '%u'", array($radicado));

        $this->db->conn->Execute($dsql);

        $sqlmrd = vsprintf("SELECT
                                SGD_MRD_CODIGO
                            FROM
                                 SGD_MRD_MATRIRD
                            WHERE
                                DEPE_CODI       = '%u'
                            AND SGD_SRD_ID      = '%u'
                            AND SGD_SBRD_ID     = '%u'
                            AND SGD_TPR_CODIGO  = '%u'"
            , array($dependencia, $serie, $sub_serie, $tipo));

        $rs_sqlmrd = $this->db->conn->Execute($sqlmrd);

        $sqlc = vsprintf("SELECT
                            DEPE_CODI,
                            USUA_CODI
                          FROM
                            USUARIO
                          WHERE USUA_DOC = '%u'", array($cedula));

        $rs_sqlc = $this->db->conn->Execute($sqlc);

        $isql = " INSERT
                    INTO SGD_RDF_RETDOCF (
                        RADI_NUME_RADI
                       ,DEPE_CODI
                       ,USUA_CODI
                       ,USUA_DOC
                       ,SGD_MRD_CODIGO
                       ,SGD_RDF_FECH)
                    VALUES
                        ({$radicado},
                        {$rs_sqlc->fields['DEPE_CODI']},
                        {$rs_sqlc->fields['USUA_CODI']},
                        '{$cedula}',
                        {$rs_sqlmrd->fields['SGD_MRD_CODIGO']},
                        (SYSDATE+0))";

        if (!$this->db->conn->Execute($isql)) {
            $this->setError('insertar');
            return false;
        }

        $this->hist->insertarHistorico(array($radicado)
            , $rs_sqlc->fields['DEPE_CODI']
            , $rs_sqlc->fields['USUA_CODI']
            , $rs_sqlc->fields['DEPE_CODI']
            , $rs_sqlc->fields['USUA_CODI']
            , 'TRD AUTOMATICA BPM'
            , '32');

        return true;
    }

    /**
     * Retorna los tipos documentales asignados a la dependencia
     *
     * @param $dependencia
     * @param $serie
     * @param $subserie
     *
     * @return array
     */
    public function tiposDocumentales($dependencia, $serie, $subserie)
    {
        $tipos = array();

        $sql = vsprintf("
                SELECT
                   DISTINCT
                   s.SGD_TPR_CODIGO
                  ,s.SGD_TPR_DESCRIP texto
                FROM SGD_MRD_MATRIRD m,
                  sgd_tpr_tpdcumento s
                WHERE
                    m.SGD_TPR_CODIGO = s.SGD_TPR_CODIGO
                AND s.SGD_TPR_CODIGO = m.SGD_TPR_CODIGO
                AND m.DEPE_CODI      = %u
                AND m.SGD_SRD_ID     = %u
                AND m.SGD_SBRD_ID    = %u", array($dependencia, $serie, $subserie));

        $rs = $this->db->conn->Execute($sql);

        while (!$rs->EOF) {
            $tipos[] = array($rs->fields['SGD_TPR_CODIGO'] => $rs->fields['TEXTO']);
            $rs->MoveNext();
        }
        return $tipos;
    }

    /**
     * Retorna el listado de subseries que pertenecen a una dependencia
     *
     * @param $dependencia
     * @param $serie
     *
     * @return array
     *
     */
    public function subseries($dependencia, $serie)
    {
        $sub_series = array();
        $sql = vsprintf("SELECT DISTINCT s.SGD_SBRD_ID
                      ||'-'
                      ||s.SGD_SBRD_DESCRIP texto
                    FROM SGD_MRD_MATRIRD m,
                      SGD_SBRD_SUBSERIERD s
                    WHERE
                        m.SGD_SRD_ID  = s.SGD_SRD_ID
                    AND m.SGD_SBRD_ID = s.SGD_SBRD_ID
                    AND m.DEPE_CODI       = '%u'
                    AND m.SGD_SRD_ID      = '%u'", array($dependencia, $serie));

        $rs = $this->db->conn->Execute($sql);

        while (!$rs->EOF) {
            $sub_series[] = $rs->fields['TEXTO'];
            $rs->MoveNext();
        }

        return $sub_series;
    }

    /**
     * Retorna el listados de series
     * @param $dependencia
     * @return array
     */
    public function series($dependencia)
    {
        $series = array();
        $sql = vsprintf("SELECT DISTINCT s.SGD_SRD_ID
                  ||'-'
                  ||s.SGD_SRD_DESCRIP texto
                FROM SGD_MRD_MATRIRD m,
                  sgd_srd_seriesrd s
                WHERE
                    m.SGD_SRD_ID = s.SGD_SRD_ID
                    AND m.DEPE_CODI  = '%u')", array($dependencia));

        $rs = $this->db->conn->Execute($sql);

        while (!$rs->EOF) {
            $series[] = $rs->fields['TEXTO'];
            $rs->MoveNext();
        }

        return $series;
    }

    /**
     * Con un usuario origen destino y un destino envio el radicado de
     * una cuenta a otra.
     *
     * @param $origen
     * @param $destino
     * @param $tipo
     * @param $radicado
     *
     * @return boolean
     *
     */
    public function reasignarRadicado($origen, $destino, $radicado)
    {
        $success = false;

        if (!$this->validarRadicado($radicado)) {
            return false;
        }

        $usu_origen = $this->getUsuarioSelect($origen);
        $usu_destino = $this->getUsuarioSelect($destino);

        if (!$usu_origen || !$usu_destino) {
            return false;
        }

        $data = array($usu_destino['dependencia']
        , $usu_destino['codusuario']
        , $radicado);

        $upd_rad = vsprintf("UPDATE
            RADICADO
            SET RADI_DEPE_ACTU = %u
            RADI_USUA_ACTU = %u
            WHERE
            RADI_NUME_RADI = '%u'",
            array($data));

        if (!$this->db->conn->Execute($upd_rad)) {
            $this->setError('insertar');
            return false;
        }

        $this->hist->insertarHistorico(array($radicado)
            , $usu_origen['dependencia']
            , $usu_origen['codusuario']
            , $usu_destino['dependencia']
            , $usu_destino['codusuario']
            , 'Radicado reasignado desde platinum'
            , 9);

        //variable modificada en General.mailinforma
        if ($success !== true) {
            $this->setError('error_evnioemail');
            return false;
        }

        return true;
    }

    /**
     * Retorna el listado de tipo de documentos habilitados para las
     * radicaciones que no requiren la tabla de retención completa
     * @return array
     */
    public function tipoDocumentalParaRadicar()
    {
        $salida = array();

        $consulta = "   SELECT
                            concat(
                                case when sgd_tpr_tp1 = 1 then 'salida ' else '' end ,
                                case when sgd_tpr_tp2 = 1 then 'entrada ' else '' end ,
                                case when sgd_tpr_tp3 = 1 then 'memorando ' else '' end
                            ) as LISTA_TIPO_RADICADO,
                            SGD_TPR_CODIGO,
                            SGD_TPR_TERMINO,
                            SGD_TPR_DESCRIP
                        FROM
                            SGD_TPR_TPDCUMENTO
                        WHERE
                            (sgd_tpr_tp1 = 1 or
                            sgd_tpr_tp2 = 1 or
                            sgd_tpr_tp3 = 1 ) and
                            SGD_TPR_RADICA='1' and
                            SGD_TPR_ESTADO = 1";

        $rs = $this->db->conn->Execute($consulta);

        while ($rs && !$rs->EOF) {
            $list = json_encode(explode(' ', trim($rs->fields['LISTA_TIPO_RADICADO'])));

            $salida[] = array( 'id'  => $rs->fields['SGD_TPR_CODIGO'],

                               'termino' => $rs->fields['SGD_TPR_TERMINO'],

                               'nombre' => strtoupper($rs->fields['SGD_TPR_DESCRIP']),

                                'listRadicado' => $list
                             );
            $rs->MoveNext();
        }
        return $salida;
    }

    /**
     * Creacion y retorno de un numero de expediente
     *
     * @param $nurad
     * @param $usuario
     * @param $anoExp
     * @param $fechaExp
     * @param $codiSRD
     * @param $codiSBRD
     * @param $codiProc
     * @param $digCheck
     * @param $tmr
     * @param $busquedaTag
     *
     * @return integer
     *
     */
    public function crearExpediente($nurad, $usuario, $anoExp, $fechaExp,
                                    $codiSRD, $codiSBRD, $tmr, $busquedaTag)
    {
        $expediente = $this->expe;

        //Informacion necesaria del usuario para la creacion de expedientes
        $usuario = $this->getUsuarioSelect($usuario);

        if (!$usuario) {
            return false;
        }

        $codusuario = $usuario['codusuario'];
        $dependencia = $usuario['dependencia'];
        $usuaDocExp = $usua_doc = $usuario['documento'];
        $usua_login = $usuario['login'];

        $serchParam = array($codiSRD, $codiSBRD, $busquedaTag);

        //busca si la busquedaTag ya existe
        $sql_buscahe_c = vsprintf("
                            SELECT
                                DISTINCT S.SGD_EXP_NUMERO
                            FROM
                                SGD_SEXP_SECEXPEDIENTES S
                            WHERE
                                S.SGD_SRD_ID = %u
                            AND S.SGD_SBRD_ID  = %u
                            AND S.SGD_SEXP_PAREXP1 LIKE '%%s%'", $serchParam);

        $rs_buscahe_c = $this->db->conn->Execute($sql_buscahe_c);

        if ($rs_buscahe_c->fields[SGD_EXP_NUMERO]) {
            $numeroExpediente = $rs_buscahe_c->fields['SGD_EXP_NUMERO'];
            //Insercion para el TMR
            $sql = "INSERT
                        INTO
                     SGD_RDF_RETDOCF
                        (SGD_MRD_CODIGO,RADI_NUME_RADI,DEPE_CODI,USUA_CODI,USUA_DOC,SGD_RDF_FECH)
                        VALUES ($tmr,$nurad,$dependencia,$codusuario,'$usua_doc',SYSDATE)";

            $this->db->conn->Execute($sql);

            $this->anexarExpediente($nurad, $numeroExpediente, $usua_login, "ANEXADO DESDE SIMCA");

            return $numeroExpediente;

        } else {
            //Insercion para el TMR
            $sql = "INSERT
                INTO SGD_RDF_RETDOCF
                (SGD_MRD_CODIGO,RADI_NUME_RADI,DEPE_CODI,USUA_CODI,USUA_DOC,SGD_RDF_FECH)
                VALUES ($tmr,$nurad,$dependencia,$codusuario,'$usua_doc',SYSDATE)";

            $this->db->conn->Execute($sql);

            $trdExp = substr("00" . $codiSRD, -2) . substr("00" . $codiSBRD, -2);
            $depeCo = substr($dependencia, 0, 3);
            $secExp = $expediente->secExpediente($depeCo, $codiSRD, $codiSBRD, $anoExp);
            $consecutivoExp = substr("00000" . $secExp, -5);

            $numeroExpediente = $anoExp . substr($dependencia, 0, 3) .
                $trdExp . $consecutivoExp . 'E';

            $numeroExpedienteE = $expediente->crearExpediente($numeroExpediente
                , $nurad
                , substr($dependencia, 0, 3)
                , $codusuario
                , $usua_doc
                , $usuaDocExp
                , $codiSRD
                , $codiSBRD
                , 'false'
                , $fechaExp
                , 0);


            $expediente->insertar_expediente($numeroExpediente
                , $nurad
                , substr($dependencia, 0, 3)
                , $codusuario
                , $usua_doc);

            $upd_exp = "UPDATE
                SGD_SEXP_SECEXPEDIENTES
                SET SGD_SEXP_PAREXP1 = '{$busquedaTag}'
                WHERE SGD_EXP_NUMERO = '{$numeroExpedienteE}'";

            $this->db->conn->query($upd_exp);

            return $numeroExpedienteE;
        }
    }

    /**
     * Agrega un radicado a un Expediente retorna false o true
     * si la accion se realizo con exito
     * @param $numRadicado
     * @param $numExpediente
     * @param $usuario cedula o email
     * @param $observa
     * @return bool
     */
    public function anexarExpediente($numRadicado, $numExpediente, $usuario, $observa)
    {
        $usuario = $this->getUsuarioSelect($usuario);

        $tipoTx = 53;
        $fecha = $this->db->conn->OffsetDate(0, $this->db->conn->sysTimeStamp);

        $data = array($numRadicado, $numExpediente);
        $sql = "SELECT
            SGD_EXP_ESTADO
            FROM
            SGD_EXP_EXPEDIENTE
            WHERE
            RADI_NUME_RADI = %u  AND
            SGD_EXP_NUMERO = '%s'";

        $sql = vsprintf($sql, $data);

        $resultado = $this->db->conn->Execute($sql);

        if ($resultado && !$resultado->EOF) {
            $estado = $resultado->fields['SGD_EXP_ESTADO'];
        }

        if ($estado == 0 or $estado == 1) {
            $this->setError('existente');
            return false;
        }

        if ($estado == 2) {
            $sqli = "UPDATE
                SGD_EXP_EXPEDIENTE
                SET SGD_EXP_ESTADO = 0,
                SGD_EXP_FECH = $fecha,
                USUA_CODI    = {$usuario['usua_codi']},
                USUA_DOC     = '{$usuario['usua_doc']}',
                DEPE_CODI    = {$usuario['usua_depe']}
                WHERE
                RADI_NUME_RADI={$numRadicado}
                AND SGD_EXP_NUMERO='$numExpediente'";
        } else {
            $sqli = "INSERT
                INTO SGD_EXP_EXPEDIENTE (SGD_EXP_NUMERO,RADI_NUME_RADI,
                    SGD_EXP_FECH,SGD_EXP_ESTADO,USUA_CODI,USUA_DOC,DEPE_CODI)
                    VALUES ('{$numExpediente}',$numRadicado,$fecha,0,{$usuario['usua_codi']},
                    {$usuario['usua_doc']}, {$usuario['usua_depe']})";

            if ($this->db->query($sqli)) {
                $radicaArr = array($numRadicado);
                $this->hist->insertarHistoricoExp($numExpediente
                    , $radicaArr
                    , $usuario['usua_depe']
                    , $usuario['usua_codi']
                    , $observa
                    , $tipoTx
                    , 0);

                $this->hist->insertarHistorico($radicaArr
                    , $usuario['usua_depe']
                    , $usuario['codusuario']
                    , $usuario['usua_depe']
                    , $usuario['codusuario']
                    , " "
                    , 53);

                return true;
            }
        }

        $this->setError('insertar');
        return false;
    }

    /**
     * Bloquear un expediente para que no se registren mas transacciones
     *
     * @param $num Numero del expediente
     *
     * @return boolean
     */
    public function cerrarExpediente($num)
    {
        $sqlve = vsprintf("SELECT
            COUNT(1) AS K
            FROM
            SGD_EXP_EXPEDIENTE
            WHERE
            SGD_EXP_NUMERO = '%s'", array($num));

        $rsve = $this->db->conn->Execute($sqlve);

        if ($rsve->fields['K']) {

            $sql = vsprintf("UPDATE
                SGD_EXP_EXPEDIENTE
                SET SGD_EXP_ARCHIVO='2',
                SGD_EXP_FECHFIN=CURRENT_DATE
                WHERE
                SGD_EXP_NUMERO = '%s'", array($num));

            $this->db->conn->Execute($sql);

            return true;
        }

        $this->setError('insertar');
        return false;
    }

    /**
     * Anulación de un radicado que ya tiene la solicitud
     * de la anulación, termina el tramite de anulación
     *
     * @param $radiNume
     * @param $descripcion
     * @param $usuario correo o cedula
     * @return bool
     */
    public function anulacionRadicado($radiNume, $descripcion, $usuario)
    {
        //Se traen los datos del usuario que solicita anulacion
        $usuario = $this->getUsuarioSelect($usuario);

        if (empty($usuario)) {
            $this->setError('arg_insuficientes');
            return false;
        }

        $valSolic = $this->verificaSolAnulacion($radiNume, $usuario['login']);

        if ($valSolic) {

            $updataRad = vsprintf("UPDATE
                RADICADO
                SET SGD_EANU_CODIGO = 2
                WHERE
                RADI_NUME_RADI = %u", array($radiNume));

            $rs = $this->db->conn->Execute($updataRad);

            $sqlIns = " INSERT
                INTO
                SGD_ANU_ANULADOS
                (RADI_NUME_RADI, SGD_EANU_CODI, SGD_ANU_SOL_FECH,
                DEPE_CODI , USUA_DOC, SGD_ANU_DESC , USUA_CODI)
                VALUES({$radiNume}, 2, (SYSDATE+0) ,{$usuario['dependencia']},
            {$usuario['documento']}, 'Solicitud Anulacion Servicio Web',
            {$usuario['codusuario']})";

            $rs = $this->db->conn->Execute($sqlIns);

            $sql = "INSERT
                INTO HIST_EVENTOS
                (RADI_NUME_RADI,DEPE_CODI,USUA_CODI,USUA_CODI_DEST,
                DEPE_CODI_DEST,USUA_DOC,HIST_DOC_DEST,
                SGD_TTR_CODIGO,HIST_OBSE,HIST_FECH)
                VALUES ( $radiNume , {$usuario['dependencia']}, {$usuario['codusuario']},
                    1 , 100, {$usuario['documento']}, {$usuario['documento']}, 25,
                    'Anulacion de Radicado desde Webservice', (SYSDATE+0))";

            $rs = $this->db->conn->Execute($sql);

            return true;
        } else {
            $this->setError('solicitud_anulado');
            return false;
        }
    }

    /**
     * Verifica si el radicado fue solicitado como anulado
     * Si esta solicitado permitira en otro paso ser
     * anulado por el jefe de la dependencia
     * @param $radiNume
     * @param $usuaLogin
     * @return bool
     */
    public function verificaSolAnulacion($radiNume, $usuaLogin)
    {
        if (!$this->validarRadicado($radiNume)) {
            return false;
        }

        $consultaPermiso = vsprintf("SELECT
            SGD_PANU_CODI
            FROM
            USUARIO
            WHERE
            USUA_LOGIN = '%s'", array($usuaLogin));

        $rs = $this->db->conn->Execute($consultaPermiso);

        $permAnu = $rs->fields['SGD_PANU_CODI'];

        if (empty($permAnu)) {
            $this->setError('solicitud_anulado');
            return false;
        }

        $sql = vsprintf("SELECT
            R.RADI_NUME_RADI
            FROM
            RADICADO R,
            SGD_TPR_TPDCUMENTO c
            WHERE
            R.RADI_NUME_RADI IS NOT NULL
            AND substr(R.RADI_NUME_RADI, {$this->lonRadicados}, 1) NOT IN ( 2 )
            AND R.TDOC_CODI = C.SGD_TPR_CODIGO
            AND R.SGD_EANU_CODIGO IS NULL
            AND R.SGD_EANU_CODIGO IS NULL
            AND R.RADI_NUME_RADI = %u
            AND ( R.SGD_EANU_CODIGO = 9
            or r.SGD_EANU_CODIGO = 2
            or r.SGD_EANU_CODIGO IS NULL)", array($radiNume));


        $rs = $this->db->conn->Execute($sql);

        $numRadicado = $rs->fields['RADI_NUME_RADI'];

        if (!$numRadicado) {
            $this->setError('solicitud_anulado');
            return false;
        }

        return true;
    }
}
