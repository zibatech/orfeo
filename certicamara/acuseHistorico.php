<?php
$vendorDir = dirname(__FILE__);
$baseDir = ($vendorDir);
require_once  $baseDir ."/HttpClient.php";
require_once('connection.php');


$sql_enviados="select distinct customertrackingid radicado from records where date_ like '%".$_GET['f']."%' and customertrackingid not in (select radi_nume_radi from tmp_acu_cert_hist)";


$rs_enviados=$db->query($sql_enviados);


while(!$rs_enviados->EOF)
{
$codigo_de_referencia=$rs_enviados->fields['RADICADO'];
$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.certicamara.com.co/">
<soapenv:Header/>
<soapenv:Body>
   <ws:buscarAcuse>
      <!--Optional:-->
      <idCliente>1030573156</idCliente>
      <!--Optional:-->
      <passwordCliente>Super2021</passwordCliente>
      <!--Optional:-->
      <codigoDeReferencia>'.$codigo_de_referencia.'</codigoDeReferencia>
      <!--Optional:-->
      <idAcuse>2</idAcuse>
   </ws:buscarAcuse>
</soapenv:Body>
</soapenv:Envelope>';

$CLIENT = new HttpClient();
$CLIENT->post(
    "https://cme-supersalud.certicamara.com:443/CertiMailEvidence-war/CertiMailEvidenceService",
    $data,
    array('SOAPAction:""', 'Content-Type: text/xml; charset=utf-8',),
    array(),
    true,
    false
);
$result = $CLIENT->find_pattern("/\<pathAcuseRecibo\>(.*)<\/pathAcuseRecibo\>/");

$destinos= $CLIENT->find_pattern("/\<to\>(.*)<\/to\>/");
$fecha= $CLIENT->find_pattern("/\<horaAcuseDeEnvio\>(.*)<\/horaAcuseDeEnvio\>/");
$fecha= str_replace("T", " ", $fecha[1][0]);
$fecha= str_replace("Z", " ", $fecha);


$decoded = base64_decode($result[1][0]);
$baseDirb='../bodega/'.substr($codigo_de_referencia, 0,4).'/'.substr($codigo_de_referencia, 4,4);
$baseDirs='../bodega/'.substr($codigo_de_referencia, 0,4).'/'.substr($codigo_de_referencia, 4,4);

$file = $baseDirs.'/acuse_'.$codigo_de_referencia.'.pdf';
$fileb = $baseDirb.'/acuse_'.$codigo_de_referencia.'.pdf';

if($destinos)
   file_put_contents($file, $decoded);

$destinosv=explode(",",$destinos[1][0]);
foreach ($destinosv as $value) 
{

   $sql_estado="
   select
   d.delivery_status estado
from
   records r ,
   records_recipients_details d
where
   r.id = d.fk_record
   and r.customertrackingid = ".$codigo_de_referencia."
   and d.address_ = '".$value."'
   ";
if($destinos)
   $rs_estado=$db->query($sql_estado);


   $deliveryStatust=$rs_estado->fields['ESTADO'];        

                  $pos=strpos($deliveryStatust,"Delivery Failed");
                    
                    if($pos !== false)
                        $deliveryStatust="Entrega fallida";

                   $pos=strpos($deliveryStatust,"Delivered to Mailbox" );
                    
                    if($pos !== false)
                        $deliveryStatust="Entregado al buzon";

                  $pos=strpos($deliveryStatust, "Delivered to Mail Server");
                    
                    if($pos !== false)
                        $deliveryStatust="Entregado al servidor de correo";
                    
                  $pos=strpos($deliveryStatust,"Delivered and Opened" );
                    
                    if($pos !== false)
                        $deliveryStatust="Entregado y Abierto";  



   $handle ="
      INSERT INTO SGD_RENV_REGENVIO(
         id,
         sgd_renv_pais,
         sgd_renv_cantidad,
         sgd_renv_depto,
         sgd_renv_mpio,
         sgd_renv_dir,
         sgd_dir_tipo,
         sgd_renv_mail,
         sgd_renv_codigo,
         sgd_renv_fech,
         radi_nume_sal,
         sgd_fenv_codigo,
         sgd_renv_nombre,
         sgd_renv_observa
      )VALUES(
         (select max(id) + 1 from sgd_renv_regenvio),
         'COLOMBIA',
         1,
         'D.C.',
         'BOGOTÁ',
         '<a href=".$fileb.">Descargar certificado de entrega</a>',
         1,
         '".$value."',
         (select max(sgd_renv_codigo) + 1 from sgd_renv_regenvio),
         '".$fecha."',
         ".$codigo_de_referencia.",
         106, 
         '".$value."',
         '".$deliveryStatust."')";

if($destinos)
{
   $db->query($handle);
   $db->query("insert into tmp_acu_cert_hist values(".$codigo_de_referencia.",'".$fecha."')");
}

sleep(5);

}
$rs_enviados->MoveNext();
}
?>