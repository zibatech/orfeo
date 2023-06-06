<?php
 
 //include_once    ("$ruta_raiz/processConfig.php");

 
 
 include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");

 //if (!$db){
	$db = new ConnectionHandler($ruta_raiz);
$db2=$db;
 //} 
//require_once    ("$ruta_raiz/class_control/Mensaje.php");
require_once($ruta_raiz."/include/PHPMailer_v5.1/class.phpmailer.php");
/** 
include ($ruta_raiz."/include/tx/TipoDocumental.php");
    $tipoD = new TipoDocumental($db);
    $radicados = array(20189000003515,20186000003985,20186000003975,20186000003965,20186000003955,20186000003945,20186000003935,20186000003925,20186000003915,20186000003905,20186000003895,20186000003885,20186000003875,20186000003865,20186000003855,20186000003845,20186000003835,20186000003825,20186000003815,20186000003805,20186000003795,20186000003785,20186000003775);
    $radicados = array("no hay ");
    foreach($radicados as $value){
    $genAlerta = $tipoD->setFechAlerta($value,5, false);
    echo "prueba alerta para $genAlerta <br>";
    }


    die("prueba alerta para $genAlerta");
//*/

//Numero de dias habiles para el calculo.
$numero_dias = 2;

//CALCULO DE FECHAS
$hoy = date('Y-m-d');
$anio = (date("Y")-2)."-01-01";

//BUSCO LOS DIAS HABILES Y COLOCO UN ARRAY
  
$_isql = "select NOH_FECHA from sgd_noh_nohabiles where NOH_FECHA >= '$anio'";
$_rs=$db->conn->query($_isql);
$j = 0;



//OBTENGO LOS RADICADOS QUE ESTAN POR VENCER EN 2 DIAS SI TIENE EL CORREO SU PROPIETARIO.
$isql = "SELECT sgd_trad_codigo, sgd_trad_descr, sgd_trad_icono, sgd_trad_genradsal, 
    sgd_trad_diasbloqueo, sgd_trad_alerta, sgd_trad_tiempo_alerta
  FROM sgd_trad_tiporad
    where sgd_trad_alerta>=1;";
  //$db->conn->debug = true;
  $rsTRad=$db->conn->query($isql);

unset($arrAlertas);
 while (!$rsTRad->EOF) {
  $tipoRadicado = $rsTRad->fields["SGD_TRAD_CODIGO"];
  $tiempoAlerta = $rsTRad->fields["SGD_TRAD_TIEMPO_ALERTA"];
  $arrAlertas[$tipoRadicado]=$tiempoAlerta; 
  $rsTRad->MoveNext();
 }




//OBTENGO LOS RADICADOS QUE ESTAN POR VENCER EN 2 DIAS SI TIENE EL CORREO SU PROPIETARIO.

$tipoRadicado = 5;
$tiempoAlerta = $arrAlertas[$tipoRadicado];

$iSql = "select r.radi_nume_radi, r.RA_ASUN, u.USUA_EMAIL from radicado r
    INNER JOIN usuario u
    ON (r.RADI_USUA_ACTU=u.usua_codi and r.RADI_DEPE_ACTU = u.DEPE_CODI)
  WHERE (now()-r.radi_fech_radi)<='$tiempoAlerta Days' LIMIT 100";

/*$whereJefe="";
if($codusuario!=1 and (empty($_SESSION["USUA_PERM_ENRUTADOR"]) || $_SESSION["USUA_PERM_ENRUTADOR"]==0)) {
   $whereJefe = " and r.radi_usua_radi=$codusuario ";

}*/

include "$ruta_raiz/include/tx/Radicacion.php";
$enrutador = new Radicacion($db);


/*$iSql = "SELECT (now()-fech_alertatrad) Dias,r.radi_nume_radi, r.RA_ASUN, uJ.USUA_EMAIL USUA_EMAIL_JEFE, Uj.USUA_LOGIN USUA_LOGIN_JEFE, r.radi_usua_actu, r.radi_depe_actu,a.anex_radi_nume, a.anex_salida, a.anex_estado
           ,to_char(r.radi_fech_radi , 'YYYY-mm-dd') RADI_FECH, u.usua_login, r.DEPE_CODI
         FROM radicado r 
            INNER JOIN usuario u ON     (r.RADI_USUA_RADI=u.usua_codi and r.RADI_DEPE_RADI = u.DEPE_CODI) 
            INNER JOIN usuario uJ ON (uJ.usua_codi=1 and r.RADI_DEPE_RADI = uJ.DEPE_CODI) 
            LEFT OUTER join anexos a on (r.radi_nume_radi=a.anex_radi_nume  and anex_estado<=3 )
         WHERE fech_alertatrad is not null  and (now()-fech_alertatrad)>='1 days'
        ORDER BY r.depe_codi, fech_alertatrad ";*/
        
$iSql = "SELECT (now()-fech_alertatrad) Dias,r.radi_nume_radi, r.RA_ASUN, uJ.USUA_EMAIL USUA_EMAIL_JEFE, Uj.USUA_LOGIN USUA_LOGIN_JEFE, r.radi_usua_actu, r.radi_depe_actu,a.anex_radi_nume, a.anex_salida, a.anex_estado
           ,to_char(r.radi_fech_radi , 'YYYY-mm-dd') RADI_FECH, u.usua_login, r.DEPE_CODI
         FROM radicado r 
            INNER JOIN usuario u ON     (r.RADI_USUA_RADI=u.usua_codi and r.RADI_DEPE_RADI = u.DEPE_CODI) 
            INNER JOIN usuario uJ ON (uJ.usua_codi=1 and r.RADI_DEPE_RADI = uJ.DEPE_CODI) 
            LEFT OUTER join anexos a on (r.radi_nume_radi=a.radi_nume_salida )
            left outer join sgd_renv_regenvio renv on (renv.radi_nume_sal =r.radi_nume_radi)
		
         WHERE  (r.sgd_eanu_codigo not in (1,2) or r.sgd_eanu_codigo is null) and (renv.id is null or renv.sgd_deve_codigo is not null or renv.sgd_deve_codigo <>0 or renv.sgd_renv_planilla='00' ) 
and fech_alertatrad is not null  and (now()-fech_alertatrad)>='1 days'
        ORDER BY r.depe_codi, fech_alertatrad " ;
  $rs=$db->conn->query($iSql);
 //RECORRO DATO POR DATO, Y POR CADA UNO, ENVIO UN CORREO ELECTRONICO. 
 while (!$rs->EOF) {//ESTE ES EL WHILE
   $db = new ConnectionHandler($ruta_raiz);
   $radi_nume_radi = $rs->fields['RADI_NUME_RADI'];
   $depeCodi       = $rs->fields['DEPE_CODI'];
   $dias           = $rs->fields['DIAS'];
   $usuaEMail      = $rs->fields['USUA_EMAIL_JEFE'];
   $anexEstado     = $rs->fields['ANEX_ESTADO'];
   $radiFech       = $rs->fields['RADI_FECH'];
   $usuaLogin      = $rs->fields['USUA_LOGIN'];
   $usuaLoginJefe  = $rs->fields['USUA_LOGIN_JEFE'];
   $diasSolo = trim(substr(str_replace("days", " ", $dias), 0,4));
   $diasSolo = intval(str_replace(":", " ", $diasSolo));
   //$radicadosSelText=$radi_nume_radi.",";
   $usua_email = $rs->fields['USUA_EMAIL'];
   $asu = $rs->fields['RA_ASUN'];
   $mailDestino = $usua_email;

//$mailDestino = "cejebuto@gmail.com";
   $alertasXdependencia[$depeCodi]["USUA_EMAIL"]=$usuaEMail;
   $alertasXdependencia[$depeCodi]["USUA_ENRUTADOR"]=$correoEnr;
   $alertasXdependencia[$depeCodi]["USUA_LOGIN_JEFE"]=$usuaLoginJefe;  // Login del Jefe de quien genera el radicado.
   $alertasXdependencia[$depeCodi]["RADICADOS"].=$radi_nume_radi.",";
   $alertasXdependencia[$depeCodi][$radi_nume_radi]["ASUNTO"]=$asu;
   $alertasXdependencia[$depeCodi][$radi_nume_radi]["RADI_FECH"]=$radiFech;
   $alertasXdependencia[$depeCodi][$radi_nume_radi]["USUA_LOGIN"]=$usuaLogin;  // Login de Quien genera el radicado
   $alertasXdependencia[$depeCodi][$radi_nume_radi]["DIAS_VENCIDO"]=$diasSolo;
   
   $rs->MoveNext();
}//FIN DEL WHILE


//var_dump($alertasXdependencia);

foreach($alertasXdependencia as $dependencia => $arrConsolidadoDep ){
unset($rsEnr); 
unset($mensajeMail);

$enrutadorDep= $enrutador->getUsuaEnrutador($dependencia);
if (!$enrutadorDep or $enrutadorDep!="") $enrutadorDep=1;
$iSqlEnr= "SELECT u.usua_email, d.depe_nomb FROM usuario u  , dependencia d
             WHERE
               u.DEPE_CODI=d.DEPE_CODI and 
               u.depe_codi= $dependencia and u.usua_codi= $enrutadorDep ";

$rsEnr=$db2->conn->query($iSqlEnr);
$correoEnr=$rsEnr->fields['USUA_EMAIL'];
$dependenciaNombre=$rsEnr->fields['DEPE_NOMB'];

  echo "Correos para la $dependencia <br>";
  //var_dump($arrRadicados);
  unset($radicados);
  $radicados = explode(",",$arrConsolidadoDep["RADICADOS"]);
  $mensajeMail .= "<tr><th>Radicado </th><th> Dias Vencido </th><th> Fecha Generado </th><th>Asunto </th><th> Usuario Generador </th></tr>"; 
  foreach($radicados as $radicado){
     $asunto           = $arrConsolidadoDep[$radicado]["ASUNTO"];
     $diasVencido      = $arrConsolidadoDep[$radicado]["DIAS_VENCIDO"];
     $fechaRadicacion  = $arrConsolidadoDep[$radicado]["RADI_FECH"];
     $usuarioGenerador = $arrConsolidadoDep[$radicado]["USUA_LOGIN"];
     $usuarioJefe      = $arrConsolidadoDep["USUA_LOGIN_JEFE"];
     $mensajeMail .= "<tr><td>$radicado </td><td> $diasVencido </td><td> $fechaRadicacion </td><td>$asunto </td><td> $usuarioGenerador </td></tr>"; 
  }

  $usua_email = $rs->fields['USUA_EMAIL_JEFE'];

if ($correoEnr and $correoEnr!=$usua_email){
  $usua_email.= ";".$correoEnr;
}else{
  $correoEnr="";
}
  $mailDestino = $usua_email;

echo "*********$mailDestino********";
  $observa = "<br>$usuarioJefe, Actualmente en el área $dependenciaNombre ($dependencia),  se encuentran generados los siguientes radicados, y  el proceso de tramite no se ha completado. <br><br><table border='1'>$mensajeMail</table>";
  try {
      $codTx=10;
      $asuntoMailAlertaTprad = "Radicados generados con tramite inicial vencido.";
      include "$ruta_raiz/include/mail/mailInformar.php";
      
      if($envioOk == "ok"){
      echo "Envio correo Ok.";
      }
  $time = date("G:i:s");
  $dia = date('Y-m-d');

 /**
  if($mail->Send()){
    $entry = "P2  Correo electronico enviado a $mailDestino del No Rad : $radi_nume_radi el dia $dia  a las $time.\n";
    echo $entry."<<<<"; 
    }else{
    $entry = "P2 ** NO se pudo enviar el correo a $mailDestino del No Rad : $radi_nume_radi el dia $dia  a las $time.** \n";
    echo $entry."<<<<";
    } */
   
  }catch (phpmailerException $e) {
  echo $error = $error . $e->errorMessage() . " " .$mailDestino; //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $error = $error . $e->getMessage() . " " .$mailDestino; //Boring error messages from anything else!
}
}
?>
