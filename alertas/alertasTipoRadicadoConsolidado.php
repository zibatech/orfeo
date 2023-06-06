<?php
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
if (!$_SESSION['dependencia']) header ("Location: $ruta_raiz/cerrar_session.php");

 
 $ruta_raiz = $ABSOL_PATH;
//include_once    ("$ruta_raiz/processConfig.php");

 include_once    ($ruta_raiz."include/db/ConnectionHandler.php");

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

$whereJefe="";
if($codusuario!=1 and (empty($_SESSION["USUA_PERM_ENRUTADOR"]) || $_SESSION["USUA_PERM_ENRUTADOR"]==0)) {
   $whereJefe = " and r.radi_usua_radi=$codusuario ";

}
/*$iSql = "SELECT (now()-fech_alertatrad) Dias,r.radi_nume_radi, r.RA_ASUN, u.USUA_EMAIL, r.radi_usua_actu, r.radi_depe_actu,a.anex_radi_nume, a.anex_salida, a.anex_estado
           ,to_char(r.radi_fech_radi , 'YYYY-mm-dd') RADI_FECH, u.usua_login
         FROM radicado r INNER JOIN usuario u ON (r.RADI_USUA_RADI=u.usua_codi and r.RADI_DEPE_RADI = u.DEPE_CODI) 
         LEFT OUTER join anexos a on (r.radi_nume_radi=a.anex_radi_nume  and anex_estado<=3 )
         WHERE fech_alertatrad is not null 
           and  r.DEPE_CODI=$dependencia $whereJefe
        ORDER BY fech_alertatrad ";*/
        
 $iSql = "SELECT (now()-fech_alertatrad) Dias,r.radi_nume_radi, r.RA_ASUN, u.USUA_EMAIL, r.radi_usua_actu, r.radi_depe_actu,a.anex_radi_nume, a.anex_salida, a.anex_estado ,
to_char(r.radi_fech_radi , 'YYYY-mm-dd') RADI_FECH, u.usua_login 

FROM radicado r INNER JOIN usuario u ON (r.RADI_USUA_RADI=u.usua_codi and r.RADI_DEPE_RADI = u.DEPE_CODI) LEFT OUTER join anexos a on (r.radi_nume_radi=a.anex_radi_nume) 
            left outer join sgd_renv_regenvio renv on (renv.radi_nume_sal =r.radi_nume_radi)
         WHERE (r.sgd_eanu_codigo not in (1,2) or r.sgd_eanu_codigo is null) and (renv.id is null or renv.sgd_deve_codigo is not null or renv.sgd_deve_codigo <>0 or renv.sgd_renv_planilla='00' ) and  fech_alertatrad is not null 
           and  r.DEPE_CODI=$dependencia $whereJefe
        ORDER BY fech_alertatrad ";       
        
        //echo "****$iSql*****";
  $rs=$db->conn->query($iSql);
 //RECORRO DATO POR DATO, Y POR CADA UNO, ENVIO UN CORREO ELECTRONICO. 
   $countAlerta1=0;
   $countAlerta2=0;
   $countAlerta3=0;
 while (!$rs->EOF) {//ESTE ES EL WHILE
   $db = new ConnectionHandler($ruta_raiz);
   $radi_nume_radi = $rs->fields['RADI_NUME_RADI'];
   $dias           = $rs->fields['DIAS'];
   $anexEstado     = $rs->fields['ANEX_ESTADO'];
   $radiFech       = $rs->fields['RADI_FECH'];
   $usuaLogin      = $rs->fields['USUA_LOGIN'];
   $diasSolo = trim(substr(str_replace("days", " ", $dias), 0,4));
   $diasSolo = intval(str_replace(":", " ", $diasSolo));
   $radicadosSelText=$radi_nume_radi.",";
   $usua_email = $rs->fields['USUA_EMAIL'];
   $usua_email = "ybetancur@cnsc.gov.co";
   $asu = $rs->fields['RA_ASUN'];

   $mailDestino = $usua_email;
//$mailDestino = "cejebuto@gmail.com";

  if(intval(trim($diasSolo)) <= 0){
      $arrAlerta3[$radi_nume_radi]["anexEstado"] = $anexEstado;
      $arrAlerta3[$radi_nume_radi]["dias"]       = $diasSolo;
      $arrAlerta3[$radi_nume_radi]["radiFech"]   = $radiFech;
      $arrAlerta3[$radi_nume_radi]["usuaLogin"]  = $usuaLogin;
      $countAlerta3++;
       }

   if(intval(trim($diasSolo)) ==1){
      $arrAlerta1[$radi_nume_radi]["anexEstado"] = $anexEstado;
      $arrAlerta1[$radi_nume_radi]["dias"]       = $diasSolo;
      $arrAlerta1[$radi_nume_radi]["radiFech"]   = $radiFech;
      $arrAlerta1[$radi_nume_radi]["usuaLogin"]  = $usuaLogin;
      $countAlerta1++;
      
   }
   if(intval(trim($diasSolo)) >=2 ){
      $arrAlerta2[$radi_nume_radi]["anexEstado"]= $anexEstado;
      $arrAlerta2[$radi_nume_radi]["dias"]      = $diasSolo;
      $arrAlerta2[$radi_nume_radi]["radiFech"]  = $radiFech;
      $arrAlerta2[$radi_nume_radi]["usuaLogin"] = $usuaLogin;
      $countAlerta2++;
   }
   
   $rs->MoveNext();
}//FIN DEL WHILE

$time = date("G:i:s");
$dia = date('Y-m-d');
if ($error==""){$error = "Correctamente";}

?>
