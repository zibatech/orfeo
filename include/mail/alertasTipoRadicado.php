<?php
 $ruta_raiz = "/var/www/html/orfeo47";
//include_once    ("$ruta_raiz/processConfig.php");

 include_once    ("$ruta_raiz/include/db/ConnectionHandler.php");

 //if (!$db){
	$db = new ConnectionHandler($ruta_raiz);
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
  $db->conn->debug = true;
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

$iSql = "select r.radi_nume_radi, r.RA_ASUN, u.USUA_EMAIL, (now()-fech_alertatrad) dias_vencido from radicado r
    INNER JOIN usuario u
    ON (r.RADI_USUA_ACTU=u.usua_codi and r.RADI_DEPE_ACTU = u.DEPE_CODI)
  WHERE (now()-fech_alertatrad)>='0 Days' LIMIT 100";


  $rs=$db->conn->query($iSql);
 //RECORRO DATO POR DATO, Y POR CADA UNO, ENVIO UN CORREO ELECTRONICO. 
 while (!$rs->EOF) {//ESTE ES EL WHILE
   $db = new ConnectionHandler($ruta_raiz);
   $radi_nume_radi = $rs->fields['RADI_NUME_RADI'];
   $radicadosSelText=$radi_nume_radi.",";
   $usua_email = $rs->fields['USUA_EMAIL'];
   $usua_email = "ybetancur@cnsc.gov.co";
   $asu = $rs->fields['RA_ASUN'];

   $mailDestino = $usua_email;
//$mailDestino = "cejebuto@gmail.com";
   echo "<hr> $radi_nume_radi";

try {
    $codTx=10;
    $asuntoMailRadicado = "XX Se ha generado una Respuesta con No. $nurad";
    include "$ruta_raiz/include/mail/mailInformar.php";
    
    if($envioOk == "ok"){
     echo "Envio correo Ok.";
    }
$time = date("G:i:s");
$dia = date('Y-m-d');

 if($mail->Send()){
   $entry = "Correo electronico enviado a $mailDestino del No Rad : $radi_nume_radi el dia $dia  a las $time.\n";
  }else{
   $entry = "** NO se pudo enviar el correo a $mailDestino del No Rad : $radi_nume_radi el dia $dia  a las $time.** \n";
  } 

//$entry = "TEST ->  $mailDestino del No Rad : $radi_nume_radi el dia $dia  a las $time.\n";
$file = $ruta_raiz."/bodega/tmp/mail.cron.txt";
$open = fopen($file,"a");
 
if ( $open ) {
    fwrite($open,$entry);
    fclose($open);
} 

} catch (phpmailerException $e) {
  echo $error = $error . $e->errorMessage() . " " .$mailDestino; //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $error = $error . $e->getMessage() . " " .$mailDestino; //Boring error messages from anything else!
}

   $rs->MoveNext();
}//FIN DEL WHILE

$time = date("G:i:s");
$dia = date('Y-m-d');
if ($error==""){$error = "Correctamente";}
$entry = "************* Se ejecuto el cron el dia $dia  a las $time./ $error *********************\n";
$file = "/var/www/mail.cron.txt";
$open = fopen($file,"a");
if ( $open ) {
    fwrite($open,$entry);
    fclose($open);
} 

?>
