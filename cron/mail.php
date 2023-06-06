<?php
set_time_limit(3600);

$socket = @stream_socket_server("tcp://0.0.0.0:7700", $errno, $errstr);
if (!$socket) {
    echo "already in use\n";
    exit;
}

$ruta_raiz = "..";
include "$ruta_raiz/include/tx/sanitize.php";
include "$ruta_raiz/cron/config.php";

include_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db     = new ConnectionHandler("$ruta_raiz");
$ADODB_COUNTRECS  = true;
$ADODB_FORCE_TYPE = ADODB_FORCE_NULL;

include("$ruta_raiz/include/tx/Tx.php");
include("$ruta_raiz/include/tx/Radicacion.php");
include("$ruta_raiz/include/tx/usuario.php");
include("$ruta_raiz/include/tx/roles.php");
include("$ruta_raiz/class_control/Municipio.php");
include("$ruta_raiz/processConfig.php");
require "$ruta_raiz/vendor/autoload.php";

@mkdir("$ruta_raiz/bodega/mail");

function convert($size)
{
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

function circular(&$array) {
    if(($result = current($array)) === false) {
        $result = reset($array);
    }
    next($array);

    return $result;
}

function logg($m) {
    global $ruta_raiz;
    error_log(date(DATE_ATOM)." $m\n",3,"$ruta_raiz/bodega/mailrad.log");
}

$hist      = new Historico($db);

$mailbox = new PhpImap\Mailbox(
    '{outlook.office365.com:993/imap/ssl}INBOX',
    $conf_mail['cuenta'][0],
    $conf_mail['cuenta'][1],
);

$k = 1;
$s = unserialize(@file_get_contents('/tmp/radimail'));
foreach ($conf_mail['carpetas'] as $carpeta => $usuario) {
    $usuarios = array_map('info_usuario',$usuario);
    $mailbox->switchMailbox($carpeta);
    $mailsIds = $mailbox->searchMailbox('ALL');
    $total = count($mailsIds);
    logg("$carpeta: $total");
    for ($i=0;$i<count($usuarios);$i++) {
        if (circular($usuarios)['USUA_LOGIN'] == $s[$carpeta]) break;
    }
    foreach ($mailsIds as $mailId) {
        $usua_radi = circular($usuarios);
        $c[$carpeta] = $usua_radi['USUA_LOGIN'];

        $omail = $mailbox->getMail($mailId);
        $mail_dir = "$ruta_raiz/bodega/tmp/radimail/{$omail->messageId}";
        @mkdir($mail_dir, 0755, true);
        $attachments = $omail->getAttachments();
        foreach($attachments as $attachment) {
            $dst = "$mail_dir/{$attachment->id}";
            $attachment->setFilePath($dst);
            if (!$attachment->saveToDisk()) {
                logg("\terror attachment $numrad {$attachment->name}");
            }
        }

        $subject = $omail->subject;
        $name = (isset($omail->fromName)) ? $omail->fromName : $omail->fromAddress;
        $email = $omail->fromAddress;
        $body = $omail->textHtml ?: "<html><body><pre>{$omail->textPlain}</pre></body></html>";
        $date = $omail->date;
        $attachments = $omail->getAttachments();

        if (!$name) {
            logg("error al leer el correo: $e");
            $k++;
            continue;
        }

        try {
            $mailbox->moveMail($mailId,'R_'.$carpeta);
        }
        catch (exception $e) {
            logg("error al mover el correo: $e");
            $k++;
            continue;
        }

        //$dependencia = str_pad($usua_radi['DEPE_CODI'], $digitosDependencia, "0", STR_PAD_LEFT);
        //$rads = radicar($message->getSubject(), 7050, $usua_radi);
        try {
            $rads = radicar($subject, '$dependencia', $usua_radi, $omail);
        }
        catch (exception $e) {
            logg("error al radicar: $email ($subject) {$omail->messageId}");
        }
        if (!$rads[0]['answer']) {
            logg("error al radicar ({$rads[0]['error']}): $email ($subject) {$omail->messageId}");
        }
        $numrad = $rads[0]['answer'];
        logg("$k rad:$numrad {$usua_radi['USUA_LOGIN']} adjuntos:".count($attachments));
        $ano = substr($numrad,0,4);
        //$dependencia = ltrim(substr($usua_radi['DEPE_CODI'],4,$digitosDependencia),'0');
        $dependencia = $usua_radi['DEPE_CODI'];
        $radi_path = "/$ano/$dependencia/$numrad.html";

        $nurad = $numrad;

        $listaAdjuntos = '';
        $i = 0;
        foreach($attachments as $attachment) {
            $anex=fileAdttachments($db,$numrad,$usua_radi['USUA_LOGIN'],$attachment->name,++$i,$dependencia);
            $dst = "$ruta_raiz/bodega/$ano/{$usua_radi['DEPE_CODI']}/docs/{$anex['name']}";
            $src = "$mail_dir/{$attachment->id}";
            rename($src, $dst);
            logg("\t$i,{$attachment->name}");

            $ext = pathinfo($dst,PATHINFO_EXTENSION);
            if (strtolower($ext) == 'pdf')
                $listaAdjuntos.= "<a href='javascript:void(0)' class='abrirVisor' link='bodega/$ano/{$usua_radi['DEPE_CODI']}/docs/{$anex['name']}'>".$attachment->name."</a><br>";
            else
                $listaAdjuntos.= "<a href='javascript:void(0)' onclick='funlinkArchivo(\"".$anex['code']."\",\"./\")'>".$attachment->name."</a><br>";
        }
        rmdir($mail_dir);

        $email_path = "bodega/$ano/$dependencia/$numrad.email.html";
        file_put_contents("$ruta_raiz/$email_path",$body);
        $email_para = htmlentities($omail->headers->toaddress);
        $email_cc  = htmlentities($omail->headers->ccaddress);
        ob_start();
        include "$ruta_raiz/radiMail/mensaje.php";
        //$data = ob_get_flush();
        $data = ob_get_contents();
        ob_end_clean();
        file_put_contents("$ruta_raiz/bodega$radi_path",$data);
        $isqlRadicado = "update radicado set RADI_PATH = '$radi_path' where radi_nume_radi = $numrad";
        $rs=$db->conn->query($isqlRadicado);
        if (!$rs)//Si actualizo BD correctamente
        {
            logg("\terror Fallo la Actualizacion del Path en radicado < $isqlRadicado >");
        }else{
            $observa = "Radicaci&oacute;n e-mail, se anexa (".count($attachments).") adjunto(s).";
            $codusuario = 1; 
            $codTx = 42;
            //$hist->insertarHistorico(array($numrad),  $dependencia , $codusuario, $dependencia, $codusuario, $observa, $codTx);
            $hist->insertarHistorico(array($numrad),  $dependencia , $usua_radi['USUA_CODI'], $dependencia, $usua_radi['USUA_CODI'], $observa, $codTx);
            //include "enviarMail.php";
        }

        file_put_contents('/tmp/radimail', serialize($c));
        $k++;
        //if ($k>20) break;
    }
}

function fileAdttachments($db,$nurad,$user,$filename,$attachNumber,$dependence){
    $ext=strtolower(array_pop(explode(".",$filename)));
    //$ext=array_pop(explode(".",$filename));
    $type = "SELECT ANEX_TIPO_CODI FROM ANEXOS_TIPO WHERE ANEX_TIPO_EXT = '$ext'";
    $type = $db->conn->query($type);
    $type = $type->fields["ANEX_TIPO_CODI"];
    if(!$type) $type = 99;
    $attachNumber=str_pad($attachNumber, 5, "0", STR_PAD_LEFT);
    //$code = $nurad."0000".$attachNumber;
    $code = "$nurad$attachNumber";
    $anexName = $nurad."_$attachNumber.$ext";
    $record["ANEX_RADI_NUME"]    = $nurad;
    $record["ANEX_CODIGO"]       = "'$code'";
    $record["ANEX_SOLO_LECT"]    = "'S'";
    $record["ANEX_CREADOR"]      = "'$user'";
    $record["ANEX_DESC"]         = "' Archivo:.". $filename."'";
    $record["ANEX_NUMERO"]       = $attachNumber;
    $record["ANEX_NOMB_ARCHIVO"] = "'$anexName'";
    $record["ANEX_BORRADO"]      = "'N'";
    $record["ANEX_DEPE_CREADOR"] = $dependence;
    $record["SGD_TPR_CODIGO"]    = '0';
    $record["ANEX_TIPO"]         = $type;
    $sqlDate=$db->conn->DBDate(Date("Y-m-d"));
    $record["ANEX_FECH_ANEX"]    = $sqlDate;
    $anex['name']=$anexName;
    $anex['code']=$code;
    if ($db->insert("anexos", $record, "true")){
        return $anex;
    }
    return false;
}

function info_usuario($login) {
    global $db;
    $query = "select * from usuario where usua_login = '$login'";
    $rs = $db->conn->Execute($query);
    return $rs->fetchRow();
}

function radicar($asunto, $depe_actu, $usua_radi, $omail) {
    global $db;
    $hist      = new Historico($db);
    $classusua = new Usuario($db);
    $Tx        = new Tx($db);

    $adate=date('Y');

    //$tpRadicado    = empty($_POST['datorad'])? 0 : $_POST['datorad'];
    $tpRadicado    = 0;//trim($tpRadicado ,";");

    //$tpRadicado    = empty($_POST['radicado_tipo'])? 0 : $_POST['radicado_tipo'];
    $empTrans      = $_POST['empTrans'];
    $fecha_gen_doc = '04-10-2020';
    $med           = 4;

    $nofolios      = '';
    $noanexos      = '';
    $sgdSpubCodigo = 0;

    $ane           = '';
    $coddepe       = /*$depe_actu;*/ $usua_radi['DEPE_CODI'];//?
    $tdoc          = 0;

    $ent           = 2;
    $radicadopadre = '';
    if(!$radicadopadre){  $radicadopadre = null; }


    //Enviados solo si es para modificar
    //$modificar     = $_POST['modificar'];
    //$nurad         = $_POST['nurad'];
    //$radi_dato_001 = $_POST['radi_dato_001'];
    //$radi_dato_002 = $_POST['radi_dato_002'];

    /**************************************************/
    /*********** RADICAR DOCUMENTO  *******************/
    /**************************************************/
    $rad               = new Radicacion($db);

    global $digitosDependencia, $digitosSecRad;
    $rad->noDigitosDep = $digitosDependencia;
    $rad->noDigitosRad = $digitosSecRad;
    $rad->dependencia= $usua_radi['DEPE_CODI'];
    $rad->usuaDoc    = $usua_radi['USUA_DOC'];
    //$this->noDigitosDep = $_SESSION['digitosDependencia'];
    $rad->usuaLogin  = $usua_radi['USUA_LOGIN'];
    $rad->usuaCodi   = $usua_radi['USUA_CODI'];

    //Si el radicado que se esta realizando es un memorando
    //este debe quedar guardado en la bandeja del usuario que
    //realiza el radicado por esta razon guardamos el radicado
    //con el codigo del usuario que realiza la accion.
    /*if($ent == 2){
        $carp_codi         = 0;

        $rad->radiDepeActu = $coddepe;

        $rol  = new Roles($db);
        //El grupo numero 2 corresponde a Jefe de grupo
        //en el listado predefinido de perfiles
        if($rol->buscarUsuariosGrupoDepen(2, $coddepe)){
            $rad->radiUsuaActu = $rol->users[0];
        }
    }else{*/
    //$carp_codi         = $ent;
    $carp_codi         = 0;
    $rad->radiUsuaActu = $usua_radi['USUA_CODI'];
    $rad->radiDepeActu = $usua_radi['DEPE_CODI'];
    //}

    $rad->radiTipoDeri = $tpRadicado;
    $rad->radiCuentai  = '';
    $rad->guia         = '';//trim(substr($guia,0 ,20));
    $rad->empTrans     = $empTrans;
    $rad->eespCodi     = $documento_us3;
    $rad->mrecCodi     = $med;// "dd/mm/aaaa"
    $rad->radiFechOfic =       substr($fecha_gen_doc,6 ,4)
        ."-". substr($fecha_gen_doc,3 ,2)
        ."-". substr($fecha_gen_doc,0 ,2);

    if(!$radicadopadre){
        $radicadopadre = null;
    }

    if(!$ent){
        $radicadopadre = null;
    }

    $rad->radiNumeDeri = trim($radicadopadre);
    $rad->descAnex     = substr($ane, 0, 99);
    $rad->radiDepeRadi = "'$coddepe'";
    $rad->trteCodi     = $tip_rem;
    $rad->nofolios     = $nofolios;
    $rad->noanexos     = $noanexos;
    $rad->sgdSpubCodigo = $sgdSpubCodigo;
    $rad->carpCodi     = $carp_codi;
    $rad->carPer       = $carp_per;
    $rad->trteCodi     = $tip_rem;
    $rad->raAsun       = substr(htmlspecialchars(stripcslashes($asunto)),0,349);
    $rad->radi_dato_001 = $radi_dato_001;
    $rad->radi_dato_002 = $radi_dato_002;
    if(strlen(trim($aplintegra)) == 0){
        $aplintegra = "0";
    }

    $rad->sgd_apli_codi = $aplintegra;

    if($nurad){
        if ($modificar==true){
            $rad->tdocCodi = "noactualizar";
        }else{
            $rad->tdocCodi     = $tdoc;
        }
        if(!$rad->updateRadicado($nurad)){
            $data[] = array( "error"   => 'No se actualiz&oacute; el radicado');
        }
    }else{
        $rad->tdocCodi     = $tdoc;
        //$nurad = $rad->newRadicado($ent,8230);//, $tpDepeRad[$ent]);
        $rad->radiMail=true;
        $nurad = $rad->newRadicado($ent);//, $tpDepeRad[$ent]);
    }

    if ($nurad=="-1"){
        $data[] = array( "error"   => 'No se genero un numero de radicado');
    }else{
        $data[] = array( "answer"  => $nurad);
    }
    $radicadosSel[0] = $nurad;

    if (isset($_POST['modificar'])){$_tipo_tx = 21;}else{$_tipo_tx = 2;}

    $hist->insertarHistorico( $radicadosSel,
        $usua_radi['DEPE_CODI'] ,
        $usua_radi['USUA_CODI'],
        $coddepe,
        $rad->radiUsuaActu,
        " ",
        $_tipo_tx);

    //Borramos todos los usuarios existentes en sgd_dir_drecciones y los
    //grabamos nuevamente con los datos suministrados.
    $select = "delete from sgd_dir_drecciones where radi_nume_radi = $nurad";
    $db->conn->query($select);

    /**********************************************************************
     *********** GRABAR DIRECCIONES ***************************************
     **********************************************************************
     * Existen tres tipos distintos de datos de direccion
     *
     * Descripcion
     * (0_0_XX_XX2)
     * primer campo : consecutivo de los usuarios asignados a un radicado
     *                si es nuevo puede ser 0, o si es el primer registro
     *                de los usuarios asignados al radicado.
     *
     * segundo campo: tipo de usuario {usuario: 0, empresa :2, funcionario: 6}
     *
     * tercer campo: codigo con el cual esta grabado en la tabla SGD_DIR_DRECCIONES
     *
     * Cuarto campo; el codigo de identificacion del usuario de la tabla origen.
     *               esta tabla puede ser la de SGD_OEM_CODIGO, SGD_CIU_CODIGO
     *               o USUARIOS
     *
     *
     * 1) Un usuario nuevo (0_0_XX_XX2)(0_0_XX_XX3)....
     *    El usuario nuevo se puede identificar cuando en los datos
     *    de usuario se muestra el siguiente string (0_0_XX_XX2) el
     *    cual denota que no existe un codigo de almacenamiento unido a un
     *    radicado que son las dos primeras equis, las siguietnes son el
     *    codigo que le corresponde al usuario almacenado en la base de datos
     *    ya sea un usuario, funcionario o entidad y esta representado por
     *    las ultimas equis. Como se pueden crear mas de un usuario nuevo se
     *    genera un cosecutivo que es el ultimo digito
     *    ejemplo: (0_0_XX_XX2) las dos xx significan que no esta asociado
     *              a ningun radicado, las ultimas muestran que es un
     *              usuario nuevo y el 2 que es el segundo registro generado

     * 2) Un usuario existente en el sistema, NO asociado a un radicado (0_0_XX_12)
     *    (0_0_XX_16)...
     *
     *
     * 3) Un usuario existen (0_0_123_17) (0_0_327_123)
     *    Al momento de genear un radicado podemos traer usuario del sistema y a su ves
     *    cambiar la informacion que hace parte de este.
     */

    $name = (isset($omail->fromName)) ? $omail->fromName : $omail->fromAddress;
    $date = $omail->date;
    $emailf = $omail->fromAddress;
    $usuarios = array(
        "cedula"         => '',
        "nombre"         => $name,
        "apellido"       => '',
        "dignatario"     => '',
        "telef"          => '',
        "direccion"      => '',
        "email"          => $emailf,
        "muni"           => 'BOGOTÁ, D.C.',
        "muni_tmp"       => '1',
        "dep"            => 'BOGOTÁ, D.C.',
        "dpto_tmp"       => '11',
        "pais"           => 'COLOMBIA',
        "pais_tmp"       => '170',
        "cont_tmp"       => '1',
        "tdid_codi"      => '0',
        "sgdTrd"         => 0,
        "id_sgd_dir_dre" => 'XX',
        //"id_table"       => 'XX0',
        "id_table"       => '0',
        "sgdDirTipo"     => 1
    );

    if($ent == 2){
        $query = "select u.USUA_EMAIL
            from usuario u
            where u.USUA_CODI = 1 and  u.depe_codi='$coddepe'";
        $rsM=$db->conn->query($query);
        $mailDestino_frm = $rsM->fields["USUA_EMAIL"];
    }
    $respons = $classusua->guardarUsuarioRadicado($usuarios, $nurad);

    if($respons!=1){
        $data[] = array( "error"   => 'No se Agregó correctamente el destinatario, compruebe datos');
    }

    //ENVIAR UN CORREO ELECTRONICO AL DESTINATARIO AL MOMENTO DE RADICAR.
    global $sendEmail,$ruta_raiz,$conf_mail;
    include("$ruta_raiz/dbconfig.php");
    if($conf_mail['enviar'] && !in_array($omail->fromAddress, $conf_mail['excepciones'])){
        $codTx=99;
        $email = $omail->fromAddress;
        $texto = "Estimado usuario, la Corporación Autónoma Regional del Atlántico le informa que ha recibido su correo electr&oacute;nico, el cual qued&oacute; radicado con radicado *RAD_S* Asunto: ".htmlentities($rad->raAsun);
        $asuntoMailRespuestaRapida='answer';
        $radicadosSelText = $nurad;
        if ($_emailUser == ""){$_emailUser="no-reply@test.com";}

        $nombre_fichero = "GENERAL.mailInformar.php";
        $ruta_fichero = $ruta_raiz.'/include/mail/'.$nombre_fichero;
        require("$ruta_raiz/include/mail/GENERAL.mailInformar.php");
        ob_end_clean();
    }//FIN enviar email


    return $data;
}
