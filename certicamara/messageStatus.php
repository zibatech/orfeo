<?php 

require_once('connection.php');
require_once('restclient.php');

$rest = new Restclient();

$params = array(
    'grant_type' => "password",
    'username' => "info@crautonoma.gov.co",
    'password' => "pass"
);

$token = $rest->login($params);

$fecha=date('Y')."-".date('m')."-".date('d');

if(isset($token)) {
    $response = $rest->messageStatus($token,$fecha.'T00:00:01',$fecha.'T23:59:59');


   if(isset($response)) {
        foreach ($response['ResultContent'] as $row) {

            $trackingId = $row['TrackingId'];
            $customerTrackingId = $row['CustomerTrackingId'];
            $senderName = $row['SenderName'];
            $senderAddress = $row['SenderAddress'];
            $date = $row['Date'];
            $status = $row['Status'];
            


            $sql = "INSERT INTO records(trackingid, customertrackingid, sendername, senderaddress, date_, status_) VALUES ('".$trackingId."',".$customerTrackingId.",'".$senderName."','".$senderAddress."', '".$date."', '".$status."')";

            $db->query($sql);

   
                
                if(isset($row['Recipients'])) {
                    foreach($row['Recipients'] as $recipientTemp) {
    
                        $address = $recipientTemp['Address'];
                        $deliveryStatus = $recipientTemp['DeliveryStatus'];
                        $deliveryDetail = $recipientTemp['DeliveryDetail'];
                        $deliveredDate = $recipientTemp['DeliveredDate'];
                        $openedDate = $recipientTemp['OpenedDate'];
    
         
                        $sql1 = "INSERT INTO records_recipients_details(address_, delivery_status, delivery_detail, delivered_date, opened_date, fk_record) VALUES ('".$address ."', '".$deliveryStatus."', '".$deliveryDetail."', '".$deliveredDate."', '".$openedDate."', (select max(id) from records))";
                        $db->query($sql1);

            $link="<a  href=\"certicamara/trackingId.php?t=".$trackingId."\">Descargar certificado de entrega</a>";
            $email=$address;
            $dateF=str_replace('T',' ',$deliveredDate);

            $sql_un="SELECT count(*) k FROM sgd_renv_regenvio WHERE radi_nume_sal=".$customerTrackingId." AND sgd_renv_nombre='".$address."'";


            $rs_un=$db->query($sql_un);


            if($rs_un->fields['K']==0)        
            {
                    
                    $deliveryStatust=$deliveryStatus;     
                                   
                    $pos=strpos($deliveryStatus,"Delivery Failed");
                    
                    if($pos !== false)
                        $deliveryStatust="Entrega fallida";

                   $pos=strpos($deliveryStatus,"Delivered to Mailbox" );
                    
                    if($pos !== false)
                        $deliveryStatust="Entregado al buzon";

                  $pos=strpos($deliveryStatus, "Delivered to Mail Server");
                    
                    if($pos !== false)
                        $deliveryStatust="Entregado al servidor de correo";
                    
                  $pos=strpos($deliveryStatus,"Delivered and Opened" );
                    
                    if($pos !== false)
                        $deliveryStatust="Entregado y Abierto";
                    

                    $isql = "INSERT INTO SGD_RENV_REGENVIO(
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
                        '$link',
                        1,
                        '$email',
                        (select max(sgd_renv_codigo) + 1 from sgd_renv_regenvio),
                        '$dateF',
                        '$customerTrackingId',
                        106,
                        '$address',
                        '$deliveryStatust'
                    )";
              
                    $db->conn->query($isql);

 //acuse en PDF
                    $sqlPdf="
                    INSERT INTO SGD_RENV_REGENVIO(sgd_renv_pais,
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
                        sgd_renv_observa)
                        values('COLOMBIA',
                        1,
                        'D.C',
                        'BOGOTA',
                        '<a href=\"certicamara/acusePDF.php?id=$trackingId\" target=\"_blank\">Ver certificado de entrega en PDF</a>',
                        1,
                        '',
                        (
                        select
                        max(sgd_renv_codigo) + 1
                        from
                        sgd_renv_regenvio),
                        '$dateF',
                        $customerTrackingId,
                        106,
                        '',
                        '$deliveryStatust')";

                        $db->conn->query($sqlPdf);     


            }
                    }
                }
            
        }


        //$response1 = $rest->trackingId($token,$trackingId1);
    }
}

?>
