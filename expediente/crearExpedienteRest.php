<?php
if($_POST['usuario']=='orfeo' && $_POST['password']=='pfZEqMgzdvFt2s6j'&& $_POST['dependencia'] <> NULL)
{
    header("Content-Type:application/json");
    $ruta_raiz = "../";

    define('RUTA_RAIZ','../');

    include_once RUTA_RAIZ."include/db/ConnectionHandler.php";

    	$db = new ConnectionHandler($ruta_raiz);
    	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);


        /*consulta si existe un expediente*/

        $sql_exp="SELECT sgd_exp_numero FROM sgd_sexp_secexpedientes WHERE depe_codi=".$_POST['dependencia']." AND sgd_srd_codigo=19 AND sgd_sbrd_codigo=1 AND sgd_sexp_fech BETWEEN '".date('Y-m')."-01 00:00:01' AND '".date('Y-m-t')." 23:59:59'";


        $rs_exp=$db->conn->query($sql_exp);
        $expediente=$rs_exp->fields["SGD_EXP_NUMERO"];

        if($expediente<> NULL)
            $response['expediente'] = $expediente;
        else
        {
            /*crea expediente si no existe*/
         
            include_once "expediente.class.php";

            $expClass = new expediente($ruta_raiz);

            $dependencia=$_POST['dependencia'];

            $codiSRD=19;
            $codiSBRD=1; 
            $anoExp=date('Y');            

            $numExp = $expClass->numExp($dependencia, $codiSRD, $codiSBRD, $anoExp);
            $secExp = substr($numExp, 13, 6);


            if($_POST['dependencia']=='20000')
            {
                $respo='11';
                $codusuario='11';
            }
            else
            {
                $respo='82365';
                $codusuario='82365';
            }


            $exptilulo='PQRS '.date('m').'-'.date('Y');
            $usua_doc='11';
            $fechaExp=date('Y-m-d h:i:s');


            $numExp2 = $expClass->crearExpediente($numExp, $respo, $dependencia, $codiSRD, $codiSBRD, $secExp, date('Y'), $dependencia, $codusuario, $exptilulo, 0, $usua_doc, $fechaExp, $arrParametro);
            $expClass->addAclExp($numExp2 , $dependencia,0, 3);

             $response['expediente'] = $numExp2;
        }

    	$json_response = json_encode($response);
}  
else
{
        $response['expediente'] ="Parametros incompletos";
        $json_response = json_encode($response);
}

    echo $json_response;
?>
