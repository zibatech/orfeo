<?php

session_start();
//ini_set('display_errors', '7');
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

$ruta_raiz = "..";
if (!$_SESSION['dependencia']) {
    header("Location: $ruta_raiz/cerrar_session.php");
}

$krd = $_SESSION["krd"];
foreach ($_GET as $key => $valor) {
    ${$key} = $valor;
}

foreach ($_POST as $key => $valor) {
    ${$key} = $valor;
}
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);

$arreglo_insert=$_POST['tipos'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/bootstrap-select.min.css">
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" media="screen" href="../include/DataTables/datatables.css">

    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/argo.css">
    <!--<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />-->
    <title>Expediente <?php echo $exp;?></title>
</head>
<body>
    <div class="container">
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <h2>Tipos documentales para este expediente</h2>
                </div>
                <div class="col-md-12">
                    <br>
                </div>
                <div class="col-md-12">
                    <div class="alert alert-primary" role="alert">
                        Lista de chequeo para el expediente <?php echo $exp;?>
                    </div>
                </div>
            </div>
            <form method="post" action="">
                <div class="row">
                    <?php
                        //$sql_con="SELECT COUNT(*) k FROM lista_chequeo WHERE sgd_exp_numero='".$_GET['exp']."'";
                        //$rs_con=$db->conn->Execute($sql_con);
                        $dependencia = intval(substr($_GET['exp'],4,5), 10);
                        $serie = substr($_GET['exp'],9,2);
                        $subserie = substr($_GET['exp'],11,2);

                        $sql="
                        select
                            t.sgd_tpr_descrip nombre,t.sgd_tpr_codigo codigo
                        from
                            sgd_tpr_tpdcumento t,
                            sgd_mrd_matrird m
                        where
                            t.sgd_tpr_codigo = m.sgd_tpr_codigo
                            and m.sgd_srd_codigo = ".$serie."
                            and m.sgd_sbrd_codigo = ".$subserie."
                            and m.depe_codi = ".$dependencia;

                        $rs=$db->conn->Execute($sql);
                        $clase="text-dark";
                        while(!$rs->EOF)
                        {
                            $sql_val="SELECT COUNT(*) k FROM sgd_exp_anexos where exp_tpdoc=".$rs->fields['CODIGO']." and exp_numero='".$_GET['exp']."'";
                            $rs_val=$db->conn->Execute($sql_val);
                            if($rs_val->fields['K']>0)
                                $clase="text-success";
                            else
                                $clase="text-danger";

                    ?>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?=$rs->fields['CODIGO']?>" id="ck<?=$rs->fields['CODIGO']?>" disabled <?= $clase == 'text-success' ? 'checked' : ''?>>
                            <label class="form-check-label <?=$clase?>" for="ck<?=$rs->fields['CODIGO']?>">
                                <?=$rs->fields['NOMBRE']?>
                            </label>
                        </div>
                    </div>
                    <?php
                        $rs->MoveNext();    
                        }
                    ?>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <br>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
