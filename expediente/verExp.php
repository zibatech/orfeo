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
include_once "$ruta_raiz/expediente/expediente.class.php";
$expClass = new expediente($ruta_raiz);

$dataExp = $expClass->consultarExp($exp);

//print_r($dataExp);
$dependenciaExp=$dataExp['DEPE_CODI'];
$paramsExp = $expClass->parametrosEXP($dataExp['DEPE_CODI']);
if(strlen($exp)==18)
$trd=$dataExp['SGD_SEXP_PAREXP9'].' / '.$dataExp['SGD_SEXP_PAREXP10'];
else{
$trd = $expClass->ConsultarTRD($dataExp['SGD_SRD_CODIGO'], $dataExp['SGD_SBRD_CODIGO']); 
}//, $dataExp['TRD_VERSION_ID']
$queryDep2 = "select depe_codi||' - '||depe_nomb, d.depe_codi from dependencia d where
d.depe_estado=1 order by depe_codi  ";
$rsD2 = $db->conn->Execute($queryDep2);
$optionDep = $rsD2->GetMenu2("dependenciaExp3", $dependenciaExp, "0:-- Todas las dependencias --", false, "", "id='dependenciaExp3' class='custom-select required text-uppercase' onchange='usuario4()'");
$rsD3 = $db->conn->Execute($queryDep2);
$optionDepResp = $rsD3->GetMenu2("dependenciaResp", $dependenciaExp, "0:-- Todas las dependencias --", false, "", "id='dependenciaResp' class='custom-select required text-uppercase' onchange='usuario5()'");

$permisos = $expClass->permisos($exp, $_SESSION['dependencia'], $_SESSION['codusuario'], $dataExp['USUA_DOC_RESPONSABLE']);
//print_r($permisos);
$tpdocselect='';
foreach ($expClass->ConsultarTdoc($dataExp['SGD_SRD_CODIGO'], $dataExp['SGD_SBRD_CODIGO'],$dataExp['DEPE_CODI']) as $key => $value) {
   $tpdocselect.="<option value='{$value['CODI']}'>{$value['NOM']}</option>";
}
//$permisos= $data['permisos'];
$btnabrir='';
switch ($dataExp['SGD_SEXP_ESTADO']) {
    case 0:
        $stylebnaner = '';
        $mostrar = 'ok';
        $estadoTXT='';
        $btncerrar = "<button class='btn  btn-sm btn-danger float-right'  data-toggle='modal' data-toggle2='tooltip' data-target='#ModalCierreExp' data-placement='bottom' title='' onclick='validarEstadoRad()'  data-original-title='Cerrar Expediente'><i class='fa fa-times-circle' ></i></button>";
        
        break;
    case 1:
        $stylebnaner = 'bg-warning';
        $mostrar = 'no';
        $estadoTXT='';
        $estadoTXT='Cerrado';
        $btnabrir = "<button class='btn  btn-sm btn-danger float-right' data-toggle='modal' data-toggle2='tooltip' data-target='#ModalCierreExp'  data-placement='bottom' title='Re-abrir Expediente' onclick='ReabrirExpRad()'><i class='fa fa-folder-o'  ></i></button>";
        break;
    case 2:
        $stylebnaner = 'bg-danger';
        $mostrar = 'no';
        $estadoTXT='Anulado';
        $btnabrir = "<button class='btn  btn-sm btn-warning float-right' data-toggle='modal' data-toggle2='tooltip' data-target='#ModalCierreExp'  data-placement='bottom' title='Desanular Expediente' onclick='ReabrirExpRad(\"A\")'><i class='fa fa-folder-o'  ></i></button>";
        break;
    case 3:
        $stylebnaner = 'bg-danger';
        $estadoTXT='Cerrado';
        $mostrar = 'no';
        break;
    case 4:
        $stylebnaner = 'bg-success';
        $estadoTXT='Cerrado Con Acta';
        $mostrar = 'no';
}
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


    <div class="card card-andje  ">
        <div class="card-header modal-header-andje  text-white <?=$stylebnaner?> " style="background-color: #404040  ;">
            EXPEDIENTE
            <?php echo $exp .'   '. $estadoTXT; ?>
            <input type="hidden" name="numexp" id="numexp"  value='<?=$exp?>'>
            <input type="hidden" name="premseg" id="premseg"  value='<?=$permisos['segperm']?>'>
            
            <input type="hidden" name="UCRESPONSABLE" id="UCRESPONSABLE"  value='<?=$dataExp['USUA_DOC_RESPONSABLE']?>'>
            <?php if($permisos['admExp']==1){?>
                <?php 
                echo $btncerrar . $btnabrir;
                if ($mostrar == 'ok') {
                    ?>
          <!--
                 <button class="btn  btn-sm btn-outline-warning float-right btn-op-setting"  data-toggle="modal" data-target="#Modaladm" data-toggle2="tooltip" data-placement="bottom" title=""
                data-original-title="Configurar"><i class="fa fa-cogs"></i></button>
                <button class="btn  btn-sm btn-outline-warning float-right"  data-toggle="modal" data-target="#Modaladm" data-toggle2="tooltip" data-placement="bottom" title=""
                data-original-title="Configurar"><i class="fa fa-download"></i></button>-->
                
                                    <button class="btn  btn-sm btn-outline-warning float-right" data-toggle="modal" data-target="#Modaladm" data-toggle2='tooltip'  data-placement='bottom' title='Configurar' ><i class="fa fa-cogs"></i></button>
                                    <?php }                                 
            } ?>
            <?php if ($dataExp['SGD_SEXP_ESTADO'] == 4) {?><a class="btn btn-primary btn-xs float-right "  data-toggle2='tooltip' data-placement='bottom' title='Acta de cierre' target="_blank" href='../old/bodega/<?=$expdata['EXP_PATH_ACTA'];?>'><i class='fa fa-eye'></i></a><?php }?>
        </div>

        <div class="card-body p-0">
            <div class="input-group  ">
                <div class="col-6 p-0 ">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-uppercase font-weight-bold " style="width: 180px"><?=$paramsExp[1] ? $paramsExp[1] : "Nombre Expediente";?></span>
                        </div>
                        <div class="form-control "><span id="txt-title"><?php echo $dataExp['SGD_SEXP_PAREXP1']; ?></span>
                        </div>

                    </div>
                    <div class="input-group">

                        <div class="input-group-prepend ">
                            <span class="input-group-text text-uppercase font-weight-bold" style="width: 180px">Responsable</span>
                             </div>
                            <div class="form-control "><span id="txt-Responsa"><?=$dataExp['RESPONSABLE']?></span></div>
                        </div>

                    <div class="input-group">

                        <div class="input-group-prepend ">
                            <span class="input-group-text text-uppercase font-weight-bold"style="width: 180px">TRD</span>
                        </div>
                        <div class="form-control "><span id="txt-anex"><?=$trd?></span></div>

                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                             <span class="input-group-text text-uppercase font-weight-bold" style="width: 180px">Fecha de Inicio</span>
                        </div>
                          <div class="form-control " ><span id="txt-fech"><?=$dataExp['SGD_SEXP_FECH']?></span></div>
                          <div class="input-group-prepend">
                            <span class="input-group-text  text-uppercase font-weight-bold" style="width: 180px">Estado</span>
                    </div>
                    <div class="form-control " ><span
                            id="txt-seguridad"><?=$dataExp['SGD_SEXP_ESTADO'] ? '' : 'Abierto';?></span></div>
                        </div>

                </div>
                <div class="col-6  p-0 ">

            <div class="input-group  ">

                <div class="input-group-prepend">
                    <span class="input-group-text  text-uppercase font-weight-bold" style="width: 130px"><?=$paramsExp[2] ? $paramsExp[2] : "Descriptor 1";?></span>
                </div>
                <div class="form-control "><span id="txt-param2"><?=$dataExp['SGD_SEXP_PAREXP2']?> </span></div>
                </div>
                <div class="input-group  ">

                <div class="input-group-prepend">
                    <span class="input-group-text  text-uppercase font-weight-bold" style="width: 130px"><?=$paramsExp[3] ? $paramsExp[3] : "Descriptor 2";?> </span>
                </div>
                <div class="form-control "><span id="txt-param3"><?=$dataExp['SGD_SEXP_PAREXP3']?></span> </div>
                </div>
                <div class="input-group  ">

                <div class="input-group-prepend">
                    <span class="input-group-text  text-uppercase font-weight-bold" style="width: 130px"><?=$paramsExp[4] ? $paramsExp[4] : "Descriptor 3";?></span>
                </div>
                <div class="form-control "><span id="txt-param4"><?=$dataExp['SGD_SEXP_PAREXP4']?></span></div>
            </div>
            <div class="input-group  ">

                <div class="input-group-prepend">
                    <span class="input-group-text  text-uppercase font-weight-bold" style="width: 130px"><?=$paramsExp[5] ? $paramsExp[5] : "Descriptor 4";?></span>
                </div>
                <div class="form-control "><span id="txt-param5"><?=$dataExp['SGD_SEXP_PAREXP5']?></span></div>

            </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header  " style="padding: 0.2rem 1.25rem 0.75rem;">
            <?php if ($permisos['list'] == 1) {?>
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="" style="padding: 0px 10px;margin-top: 8px; align-items: center">
                        VER
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active show" data-toggle="tab" onclick="cargartabla('S');"
                            href="#listado">Basica</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" onclick="cargartabla('A');" href="#listado">Anexos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" onclick="cargartabla('R');" href="#listado">Radicado</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" onclick="cargartablaHisto();"
                            href="#Historico">Historico</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" onclick="cargartabla('E');" href="#listado">Excluidos</a>
                    </li>
                    <li class="">
                    </li>
                    <?php if ($permisos['segAdm'] == 1 && $mostrar == 'ok') {?>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" onclick="SeguridadList();" href="#Seguridad">Seguridad</a>
                    </li>
                    <?php }?>
                    <li class="nav-item">
                        <a class="nav-link " data-toggle="tab" onclick="window.open('chequeo.php?exp=<?=$_GET['exp']?>');" href="#">Lista de Chequeo</a>
                    </li>

                    <li class="">
                    </li>
                </ul>
            </div>
            <?php }?>
            <div class="card-body p-0">
                <div class="card-body " style="padding: 0px">
                    <div class="tab-content">

                        <div id="listado" class="container tab-pane active" style="padding: 0px ;   max-width: 100%;">
                            <img src="../imagenes/loadingAnimation.gif" id="imageproceso" name="imageproceso"
                                style="display: none;">
                            <div class="input-group float-rigth" id="btnSearchTb" style="display: block;">
                                <div class="input-group float-right ">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text ">Buscar</span>

                                    </div>
                                    <input class="form-control " id="mysearch" name="mysearch" type="text"
                                        placeholder="">
                                    <!-- <input class="form-control " id="mysearch2"  name="mysearch2"  type="text" placeholder="Número de radicado o anexo exp completo">-->

                                    <div class="input-group-append">
                                        <button class="btn  btn-xs btn-outline-primary " id="btnmysearch2"
                                            onclick="cargartabla('S','ORrad','desc',1);"
                                            style="display: none;"><i class="fa fa-search"></i></button>
                                        <button class="btn  btn-xs btn-outline-success " id="btnrefesh"
                                            onclick="cargartabla('S');" style="display: block;"><i
                                                class="fa fa-recycle"></i></button>

                                    </div>

                                    <div class="input-group-prepend" id="txtselPage" style="display: block;">
                                        <span class="input-group-text ">Pag.</span>
                                    </div> <select name="selPage" id="selPage" onchange="cargartabla('S');"
                                        style="display: none;">
                                        <option value="1">1</option>
                                    </select>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text ">Radicados</span>
                                    </div> <input class="form-control " style="max-width: 60px;" name="radnume"
                                        id="radnume" type="text" placeholder="">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text ">Anexos Exp</span>
                                    </div> <input class="form-control " style="max-width: 60px;" name="anexnume"
                                        id="anexnume" type="text" placeholder="">
                                        <?php  if ($mostrar == 'ok') {?>
                                    <div class="input-group-append">
                                        <!--<button class="btn  btn-sm btn-outline-secondary"><i class="fa fa-sort"></i> <span class="hidden-md">Ordenar</span></button>-->
                                        <?php if ($permisos['fisico'] == 1) {?>
                                        <button class="btn  btn-xs btn-outline-primary" onclick="opertx('fisico')" data-toggle="modal" data-target="#ModalOperMas">
                                            <i class="fa fa-file-archive-o"></i>  <span class="hidden-md">Fisico</span>
                                        </button>
                                        <?php }  if ($permisos['subexp'] == 1) {?>
                                        <button class="btn  btn-xs btn-outline-primary" onclick="opertx('subexp')" data-toggle="modal" data-target="#ModalOperMas">
                                            <i class="fa fa-folder-open-o"></i> <span class="hidden-md">Subexp</span>
                                        </button>
                                        <?php }  if ($permisos['carpeta'] == 1) {?>
                                         <button class="btn  btn-xs btn-outline-primary" onclick="opertx('carpeta')" data-toggle="modal" data-target="#ModalOperMas">
                                             <i class="fa fa-folder-o"></i><span class="hidden-md">Carpeta</span>
                                        </button>
                                        <?php } if ($permisos['excluir'] == 1) {?>
                                         <button class="btn  btn-xs btn-danger" onclick="opertx('Excluir')"  data-toggle="modal" data-target="#ModalOperMas">
                                             <i class="fa fa-eraser"></i> <span class="hidden-md">Excluir</span>
                                        </button>
                                        <?php } if ($permisos['anexa'] == 1) {?>
                                        <button class="btn  btn-xs btn-outline-warning" onclick="$('#subirAnexoX').attr('src', '../expediente/crearAnexoExpediente.php?numeroExpediente=<?=$exp;?>');"  data-toggle="modal" data-target="#ModaladdAnex">
                                            <i class="fa fa-plus-square"></i> <span class="hidden-md">Anexar</span>
                                        </button>
                                        <?php }
                                    }?>
                                        <!--   <button class="btn  btn-xs btn-warning" onclick="opertx('incExp')" data-toggle="modal" data-target="#ModalOperMas"><i class="fa fa-folder-open"></i>  <span class="hidden-md">Incluir a otro Exp</span></button>  -->

                                    </div>
                                </div>
                            </div>
                            <br>
                            <table class="table table-striped table-responsive-sm table-sm " id="tb_listaexp"
                                name="tb_listaexp" style="width: 100%">
                                <thead class="text-center">
                                    <tr class="text-capitalize" style="font-size: 12px;">
                                        <th style=" width:75px"></th>
                                        <!--<th id="tb_th_ORorden" onclick="cargartabla('S', 'ORorden', 'desc')">Orden<i id="tb_th_ORradIcon" class="fa fa-sort-desc"></i></th>-->
                                        <th style=" width:75px"></th>
                                        <th id="tb_th_ORrad"
                                            onclick="cargartabla('S','ORrad','asc')">
                                            Radicado <i id="tb_th_ORradIcon" class="fa fa-sort-desc"></i></th>
                                        <th id="tb_th_ORfech"
                                            onclick="cargartabla('S', 'ORfech', 'desc')">
                                            Fecha <i class="fa fa-sort"></i></th>
                                        <th>Serie Subserie</th>
                                        <th id="tb_th_ORtp"
                                            onclick="cargartabla('S', 'ORtp', 'desc')">
                                            tipo Doc. <i class="fa fa-sort"></i></th>
                                        <th id="tb_th_ORasunto"
                                            onclick="cargartabla('S', 'ORasunto', 'desc')">
                                            Asunto <i class="fa fa-sort"></i></th>
                                        <!--  <th id="tb_th_ORfisico" onclick="cargartabla('S', 'ORfisico', 'desc')">Físico <i class="fa fa-sort"></i></th>-->
                                        <th >
                                            Remitente <i class="fa fa-sort"></i></th>
                                        <th >
                                            Carpeta <i class="fa fa-sort"></i></th>
                                        <th id="tb_th_ORsub"
                                            onclick="cargartabla('S', 'ORsub', 'desc')">
                                            SubExp <i class="fa fa-sort"></i>
                                            <th >
                                            Fisico <i class="fa fa-sort"></i></th>
                                    </tr>
                                </thead>
                                <tbody class="text-uppercase" ></tbody>
                            </table>

                        </div>
                        <div id="Historico" class="container tab-pane fade"
                            style="padding: 0px; max-width: 100%; display: none;">
                            <img src="../imagenes/loadingAnimation.gif" id="imageprocesohist" name="imageprocesoSeg"
                                style="display:none">
                            <img src="../imagenes/loadingAnimation.gif" id="imageproceso" name="imageproceso"
                                style="display:none">
                            <div class="input-group" id="btnSearchTb float-rigth" style="display: block">
                                <div class="input-group float-right ">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text ">Ver Historico por:</span>
                                    </div>
                                    <button class="btn  btn-sm btn-outline-primary"
                                        onclick="cargartablaHisto('tb_listaHistoexp');"><i
                                            class="fa fa-file-archive-o"></i> <span class="hidden-md">Expediente
                                            (transacional)</span></button>
                                    <!--    <button class="btn  btn-sm btn-outline-primary" onclick="cargartablaHisto('tb_listaHistoexpArch');"><i class="fa fa-folder-open-o"></i>  <span class="hidden-md">Archivo</span></button>-->
                                    <button class="btn  btn-sm btn-outline-primary"
                                        onclick="cargartablaHisto('tb_listaHistoexpCons');"><i
                                            class="fa fa-folder-o"></i> <span class="hidden-md">Consulta</span></button>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text ">Buscar</span>

                                    </div>
                                    <input class="form-control " id="mysearchhisto" name="mysearchhisto" type="text"
                                        placeholder="">
                                    <input class="" id="tipohistorico" name="tipohistorico" type="hidden"
                                        value="tb_listaHistoexp">
                                </div>
                            </div>

                            <table class="table table-striped table-responsive-sm table-sm " id="tb_listaHistoexp"
                                name="tb_listaHistoexp">
                                <thead class="text-center">
                                    <tr class="text-capitalize" style="font-size: 12px;">
                                        <th colspan="7">Expediente (transacional)</th>
                                    </tr>
                                    <tr class="text-capitalize" style="font-size: 12px;">
                                        <th>Dependencia</th>
                                        <th>Fecha</th>
                                        <th>Transacción</th>
                                        <th>Usuario</th>
                                        <th>Radicado</th>
                                        <th>Comentario</th>
                                    </tr>
                                </thead>
                                <tbody class="text-uppercase "> </tbody>
                            </table>
                            <table class="table table-striped table-responsive-sm table-sm " id="tb_listaHistoexpCons"
                                name="tb_listaHistoexpCons" style="width: 100%;display:none">
                                <thead class="text-center">
                                    <tr class="text-capitalize" style="font-size: 12px;">
                                        <th colspan="7">Historico de Consulta de Expediente (Usuarios que consultan el
                                            expediente)</th>
                                    </tr>
                                    <tr class="text-capitalize" style="font-size: 12px;">
                                        <th>Dependecia</th>
                                        <th>Fecha de Consulta</th>
                                        <th>Usuario</th>
                                        <th>Nivel de vista</th>
                                        <th>Comentario</th>
                                    </tr>
                                </thead>
                                <tbody class="text-uppercase "> </tbody>
                            </table>
                            <table class="table table-striped table-responsive-sm table-sm " id="tb_listaHistoexpArch"
                                name="tb_listaHistoexpArch" style="width: 100%;display:none">
                                <thead class="text-center">
                                    <tr class="text-capitalize" style="font-size: 12px;">
                                        <th colspan="7">Historico de Operaciones de Archivo (Usuarios que ajustan el
                                            expediente fisico)</th>
                                    </tr>
                                    <tr class="text-capitalize" style="font-size: 12px;">
                                        <th>Dependecia</th>
                                        <th>Fecha</th>
                                        <th>Transacción</th>
                                        <th>Usuario</th>
                                        <th>Radicado</th>
                                        <th>Comentario</th>
                                    </tr>
                                </thead>
                                <tbody class="text-uppercase "> </tbody>
                            </table>
                        </div>
                        <div id="Seguridad" class="container tab-pane fade" style="padding: 0px ;   max-width: 100%;">
                            <img src="../imagenes/loadingAnimation.gif" id="imageproceso" name="imageproceso"
                                style="display:none">
                            <div class="row">
                                <div class="input-group col-12" id="btnSearchTb float-rigth" style="display: block">
                                    <div class="input-group float-right ">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text ">Dependencia</span>

                                        </div>
                                            <?php echo $optionDep; ?><!-- usuariosearch()--->

                                        <div class="input-group-prepend">
                                            <span class="input-group-text ">Usuario</span>
                                        </div>
                                        <select class="custom-select text-uppercase" id="selUsuario" name="selUsuario"></select>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text ">Permiso</span>
                                        </div>
                                        <select class="custom-select" id="seltiposeg" name="seltiposeg">
                                            <option value="0"> Denegar </option>
                                            <option value="1"> Listar </option>
                                            <option value="2"> Listar y Ver Documentos</option>
                                            <option value="3"> Administrar </option>
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn  btn-sm btn-outline-secondary" id="btnSegAsig"
                                                name="btnSegAsig" onclick="saveSeg()" style="display: block"><i
                                                    class="fa fa-floppy-o"></i> <span
                                                    class="hidden-md">Asignar</span></button>
                                        </div>
                                        <input type="hidden" id="idsegrel" name="idsegrel">
                                    </div>
                                </div>
                                <div class="alert alert-warning col-12" id="diverrorsave"></div>
                            </div>

                            <table class="table table-striped table-responsive-sm table-sm " id="tb_listaSegExp"
                                name="tb_listaSegExp" style="width: 100%">
                                <thead class="text-center">
                                    <tr class="text-capitalize" style="font-size: 12px;">
                                        <th id="" onclick="">Usuario</th>
                                        <th id="" onclick="">Dependencia</th>
                                        <th id="" onclick="">Permiso Expediente</th>
                                        <!-- <th id='' onclick=''>Acciones Imagenes</th>-->
                                        <th style=" width:65px"></th>
                                    </tr>
                                </thead>
                                <tbody class="text-uppercase font-weight-bold">
                                    <tr style="font-size:11px">
                                        <td style="vertical-align: middle;">Administrador</td>
                                        <td style="vertical-align: middle;">Administradora del Sistema</td>
                                        <td>
                                            <select class="custom-select" id="sele36563" name="sele36563"
                                                onchange="modSeg(36563,'sele36563')">
                                                <option value="0"> Denegar </option>
                                                <option value="1"> ver listado </option>
                                                <option value="2"> Ver Contenido (Ver Documentos)</option>
                                                <option value="3" selected="selected"> Full Permisos</option>
                                            </select>
                                        </td>
                                        <!--<td style="vertical-align: middle;">Ver</td>-->
                                        <td><span id="divsele36563" name="sele36563"></span></td>
                                    </tr>
                                    <tr style="font-size:11px">
                                        <td style="vertical-align: middle;">Todos</td>
                                        <td style="vertical-align: middle;">undefined</td>
                                        <td> <select class="custom-select" id="seleundefined" name="seleundefined"
                                                onchange="modSeg(undefined,'seleundefined')">
                                                <option value="0" selected="selected"> Denegar </option>
                                                <option value="1"> ver listado </option>
                                                <option value="2"> Ver Contenido (Ver Documentos)</option>
                                                <option value="3"> Full Permisos</option>
                                            </select></td>
                                        <!--<td style="vertical-align: middle;">Ver</td>-->
                                        <td><span id="divseleundefined" name="seleundefined"></span></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                    <div id="Ordenar" class="container tab-pane fade">ordenar</div>
                </div>

                <!-- Modal -->
                <div class="modal fade " id="ModalOperDoc" role="dialog" aria-labelledby="ModalModAnexLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document" style=" width: 100% !important;margin-top: 10px">
                        <div class="modal-content modal-lg"
                            style='    max-width: 90%;  max-height: 90%;margin: 0px auto;'>
                            <div class="modal-header" style="padding: 2px">
                                <span class="modal-title" id="tituloOperDoc"><strong>Info Radicado anexo</strong></span>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" value='' id='tipodoc' name='tipooc'>
                                <input type="hidden" value='' id='numerpDoc' name='numerpDoc'>
                                <input type="hidden" value='' id='numerAexp' name='numerAexp'>
                                <div class="input-group " id="tpdocAnexomod">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text ">Tipo Documento</span>
                                    </div>
                                    <select id="tpdocMD" class="custom-select" name="tpdocMD" s>
                                        <option value="--">-- Selecione --</option> <?php echo $tpdocoption; ?>
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn  btn-sm btn-outline-warning"
                                            onclick="savetxExp('i', 'tpdocMD')" id='btnTPsave'><i
                                                class="fa fa-save"></i></button>
                                    </div>
                                    <!--savemoditem("tpdocMD", "btnTPsave");-->
                                </div>
                                <div class="input-group " id="tpdocAsuntomod">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text " style="width: 117px;">Asunto</span>
                                    </div>
                                    <textarea name="aexpasunto" class="form-control" id="aexpasunto" maxlength="250"
                                        size="5"> </textarea>
                                    <div class="input-group-append">
                                        <button class="btn  btn-sm btn-outline-warning"
                                            onclick="savetxExp('i', 'asuntoaexp')"><i class="fa fa-save"></i></button>
                                    </div>
                                </div>
                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text " style="width: 150px;">Fisico</span>
                                    </div>
                                    <select class="custom-select" name="operFisicoMasi" id="operFisicoMasi">
                                        <option value="VIRTUAL">VIRTUAL</option>
                                        <option value="FISICO">FISICO</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn  btn-sm btn-outline-warning"
                                            onclick="addoption('fisico')"><i class="fa fa-save"></i></button>
                                    </div>
                                </div>
                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text " style="width: 150px;">Carpeta</span>
                                    </div>
                                    <input type="text" name="operCarpi" class='form-control' id="operCarpi"
                                        name="operCarpi" value="">
                                    <div class="input-group-append">
                                        <button class="btn  btn-sm btn-outline-warning"
                                            onclick="addoption('carpeta')"><i class="fa fa-save"></i></button>
                                    </div>
                                </div>
                                <div class="input-group ">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text " style="width: 150px;">Subexpediente</span>
                                    </div>
                                    <input type="number" name="anexhdd" class='form-control' id="operaddsubei"
                                        name="operaddsubei" value="">
                                    <div class="input-group-append">
                                        <button class="btn  btn-sm btn-outline-warning"
                                            onclick="addoption( 'subexp')"><i class="fa fa-save"></i></button>
                                    </div>
                                </div>
                                <div class=" " id="txaccI" name="txaccI">

                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick=""
                                    id="salirexp">Salir</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuracion -->

                <div class="modal show " id="Modaladm" role="dialog" aria-labelledby="ModalModmasLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document" style=" width: 100% !important;margin-top: 10px">
                        <div class="modal-content modal-lg"
                            style='    max-width: 90%;  max-height: 90%;margin: 0px auto;'>
                            <div class="modal-header" style="padding: 2px">
                                <span class="modal-title" id=""><strong>Administrar Expediente</strong></span>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" value='<?=$expdata['SGD_EXP_NUMERO'];?>' id='expAne' name='expAne'>

                                <div id='tpDoc-error' class='float-right'></div>
                                <div class="input-group " id="tpmasObserva">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text  text-uppercase font-weight-bold " style="width: 180px;">Dependencia Resp.</span>
                                    </div>
                                    <?=$optionDepResp;?>
                                </div>
                                <div class="input-group " id="tpmasObserva">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text  text-uppercase font-weight-bold " style="width: 180px;">Responsable</span>
                                    </div>
                                    <select class="custom-select text-capitalize" id="usuaDocExp" name="usuaDocExp"></select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn  btn-sm btn-outline-warning"
                                            onclick="addoption('responsable')"><i class="fa fa-save"></i> </button>
                                    </div>
                                </div>
                                <div id='usuaDocExp-error' class='float-right' style="color:red"></div>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text  text-uppercase font-weight-bold " id="inputGroupFileAddon01"
                                            style="width: 180px;">Nombre de expediente</span>
                                    </div>

                                    <input type="text" class="form-control" name="SGD_SEXP_PAREXP1" id="SGD_SEXP_PAREXP1" value='<?=$dataExp['SGD_SEXP_PAREXP1'];?>'>
                                    <input type="hidden" name="opertituloOld" id="opertituloOld"
                                        value='<?=$expdata['SGD_SEXP_PAREXP1'];?>'>
                                    <div class="input-group-append">
                                        <button type="button" class="btn  btn-sm btn-outline-warning"
                                            onclick="addoption('SGD_SEXP_PAREXP1')"><i class="fa fa-save"></i> </button>
                                    </div>
                                </div>
                                <div class="input-group  ">

                            <div class="input-group-prepend">
                                <span class="input-group-text  text-uppercase font-weight-bold" style="width: 180px"><?=$paramsExp[2] ? $paramsExp[2] : "Descriptor 1";?></span>
                            </div>
                            <input type="text" class="form-control" name="SGD_SEXP_PAREXP2" id="SGD_SEXP_PAREXP2" value='<?=$expdata['SGD_SEXP_PAREXP2'];?>'>
                            <div class="input-group-append">
                                        <button type="button" class="btn  btn-sm btn-outline-warning"
                                            onclick="addoption('SGD_SEXP_PAREXP2')"><i class="fa fa-save"></i> </button>
                                    </div>
                            </div>

                            <div class="input-group  ">

                            <div class="input-group-prepend">
                                <span class="input-group-text  text-uppercase font-weight-bold" style="width: 180px"><?=$paramsExp[3] ? $paramsExp[3] : "Descriptor 2";?> </span>
                            </div>
                            <input type="text" class="form-control" name="SGD_SEXP_PAREXP3" id="SGD_SEXP_PAREXP3" value='<?=$expdata['SGD_SEXP_PAREXP3'];?>'>
                            <div class="input-group-append">
                                        <button type="button" class="btn  btn-sm btn-outline-warning"
                                            onclick="addoption('SGD_SEXP_PAREXP3')"><i class="fa fa-save"></i> </button>
                                    </div>
                            </div>

                            <div class="input-group  ">

                            <div class="input-group-prepend">
                                <span class="input-group-text  text-uppercase font-weight-bold" style="width: 180px"><?=$paramsExp[4] ? $paramsExp[4] : "Descriptor 3";?></span>
                            </div>
                            <input type="text" class="form-control" name="SGD_SEXP_PAREXP4" id="SGD_SEXP_PAREXP4" value='<?=$dataExp['SGD_SEXP_PAREXP4'];?>'> </span>
                            <div class="input-group-append">
                                        <button type="button" class="btn  btn-sm btn-outline-warning"
                                            onclick="addoption('SGD_SEXP_PAREXP4')"><i class="fa fa-save"></i> </button>
                                    </div>
                            </div>

                            <div class="input-group  ">

                            <div class="input-group-prepend">
                                <span class="input-group-text  text-uppercase font-weight-bold" style="width: 180px"><?=$paramsExp[5] ? $paramsExp[5] : "Descriptor 3";?></span>
                            </div>
                            <input type="text" class="form-control" name="SGD_SEXP_PAREXP5" id="SGD_SEXP_PAREXP5" value='<?=$expdata['SGD_SEXP_PAREXP5'];?>'>
                            <div class="input-group-append">
                                        <button type="button" class="btn  btn-sm btn-outline-warning"
                                            onclick="addoption('SGD_SEXP_PAREXP5')"><i class="fa fa-save"></i> </button>
                                    </div>
                            </div>

                                <!-- <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text  " id="inputGroupFileAddon01" style="width: 160px;">Seguridad</span>
                    </div>
                    <select class="custom-select" id="ExpSeguridad" name="ExpSeguridad">
                    <?php
$SegOption = '';
$SegOptionA = 'selected';
if ($expdata['SGD_EXP_PRIVADO'] == 0) {
    $SegOption = 'selected';
    $SegOptionA = '';
}?></span>
                    <option value='1' <?php echo $SegOptionA; ?>>Privada</option><option value='0' <?php echo $SegOption; ?>>Pública</option></select>
                    <div class="input-group-append">
                        <button type="button" class="btn  btn-sm btn-outline-warning" onclick="addoption('Seguridad')" ><i class="fa fa-save"></i> </button>
                    </div>
                </div>-->
                                <div id='opertitulo-error' class='float-right' style="color:red"></div>
                                <div class=" " id="txaccAdm" name="txaccAdm">

                                </div>
                            </div>
                            <div class="modal-footer">
                                <!--<button type="button" class="btn btn-sm btn-outline-warning" onclick="addAnexExp();" ><i class="fa fa-save"></i> Anexar</button>-->
                                <button type="button" class="btn  btn-sm btn-danger" data-dismiss="modal" onclick=""
                                    id="salirexp"><i class="fa fa-close"></i> Salir</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php /* ventana de anexar     <iframe name="subirAnexoX" id="subirAnexoX" src="../core/Modulos/Expediente/vista/file.php?exp=<?= $expdata['SGD_EXP_NUMERO']; ?>" style="border: 0;width: 100%;height: 250px"></iframe>  */?>
<!-- Modal -->
<div class="modal show " id="ModaladdAnex" role="dialog" aria-labelledby="ModalModmasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style=" width: 100% !important;margin-top: 10px">
        <div class="modal-content modal-lg" style='    max-width: 90%;  max-height: 90%;margin: 0px auto;'>
            <div class="modal-header" style="padding: 2px">
                <span class="modal-title" id="" ><strong>Anexar al Expediente</strong></span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"  style='width: 163px;'>Anexo</span>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="archFile" onchange="$('#alkfa').html($('#archFile').val())">
                        <label class="custom-file-label" id='alkfa' for="archFile">Selecione el archivo</label>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Tipo de documento</span>
                    </div>
                    <div class="custom-file">
                        <select class='custom-select' id='tpDocAnex' name="tpDocAnex"><option value=0>No definido</option><?php echo $tpdocselect ?></select>
                    </div>
                </div>
                <div class="input-group mb-3">
                <div class="input-group-prepend">
                        <span class="input-group-text"  style='width: 163px;'>Descripción</span>
                    </div>
                    <input type='text' name='descriop' id ='descriop' value='' class='form-control' placeholder="Escriba descripción del anexo"/>
                </div>

                <div id='operAnexo-error' class='float-right' style="color:red"></div>
                <div id='operAnexo-msg' class='float-right' style="color:green"></div>

             </div>
             <div class="modal-footer">
                                <!--<button type="button" class="btn btn-sm btn-outline-warning" onclick="addAnexExp();" ><i class="fa fa-save"></i> Anexar</button>-->
                                <button class="btn  btn-xs btn-warning float-right" onclick="uploadAnexos()" ><i class="fa fa-floppy"></i> <span >Guardar</span></button>
                                <button class="btn  btn-xs btn-warning float-right " data-dismiss="modal" >             <i class="fa fa-close"></i> Salir</button>
                            </div>

        </div>
    </div>
</div>
<div id="ModalviewImg2" class="modal fade " role="dialog" style="padding-right: 17px; ">
    <div class="modal-dialog modal-lg" style="min-width: 90%;min-height: 90vh;">

        <!-- Modal content-->
        <div class="modal-content ">
            <div class="modal-header" style="padding: 2px">
                <h4 class="modal-title text-andje-bold" id="tituloimagen">Ver Documento</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body" style="height: 80vh;padding: 0px">
                <iframe name="mainFrameView2" id="mainFrameView2" src="" border="-1" cellpadding="0" style="width: 100%;border-top-left-radius: 10px" cellspacing="0" marginwidth="0" marginheight="0" scrolling="auto" width="100%" height="100%" frameborder="0" framespacing="0" allowtransparency="0"></iframe>
            </div>
            <!--<div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>-->
        </div>

    </div>
</div>
<?php // ventana de operaciones masivas      ?>
<!-- Modal -->
<div class="modal fade " id="ModalOperMas" role="dialog" aria-labelledby="ModalModmasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style=" width: 100% !important;margin-top: 10px">
        <div class="modal-content modal-lg" style='    max-width: 90%;  max-height: 90%;margin: 0px auto;'>
            <div class="modal-header" style="padding: 2px">
                <span class="modal-title" id="tituloOperMas" ><strong>Operación TX</strong></span>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" value='' id='tpopertx' name='tpopertx'>
                <input type="hidden" value='' id='listRad' name='listRad'>
                <input type="hidden" value='' id='listAnex' name='listAnex'>
                <input type="hidden" value='<?=$expdata['SGD_EXP_NUMERO'];?>' id='expdientedd' name='expdientedd'>

                <div class="input-group " id="tpmasObserva">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-andje-bold">Observación</span>
                    </div>
                    <textarea name="operObs" class="form-control" id="operObs" maxlength="250" size="5"> </textarea>
                </div>
                <div class="input-group " id="tipoCarpeta">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-andje-bold" style="width: 117px;">Carpeta</span>
                    </div>
                    <input name="operCarp" class="form-control" id="operCarp"  type='text'/>
                </div>
                <div class="input-group " id="tipoFisico">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-andje-bold" style="width: 117px;">Fisico</span>
                    </div>
                    <select id="operFisicoMas" class="custom-select" name="operFisicoMas" ><option value="VIRTUAL">VIRTUAL</option><option value="FISICO">FISICO</option></select>
                </div>
                <div class="input-group " id="tiposubexp">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-andje-bold" style="width: 117px;">Subexpediente</span>
                    </div>
                    <input name="operaddsube" class="form-control" id="operaddsube"  type='text'/>
                </div>
                <div class="input-group " id="tipoincexp">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-andje-bold" style="width: 117px;">Expediente</span>
                    </div>
                    <input type="text" name="operincexp" class='form-control' id="operincexp" value="">
                    <div class="input-group-append">
                        <button class="btn  btn-sm btn-outline-warning"><i class="fa fa-save"></i></button></div>
                </div>
                <div class=" " id="txacc" name="txacc">

                </div>
                <div class=" " id="listenviar" name="listenviar">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-warning" onclick="savetxExp();" id="salirexp"><i class="fa fa-save"></i> Realizar</button>
                <button type="button" class="btn  btn-sm btn-danger" data-dismiss="modal" onclick="" id="salirexp"><i class="fa fa-close"></i> Salir</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade modal-lg" id="ModalCierreExp" tabindex="-1" role="dialog" aria-labelledby="ModalModAnexLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style=" width: 100% !important;margin-top: 10px">
        <div class="modal-content modal-lg" style="    max-width: 90%;  max-height: 90%;margin: 0px auto;">
            <div class="modal-header" style="padding: 2px">
                <h5 class="modal-title" id="titulocierreExp"> </h5>
            </div>
            <div class="modal-body">
                <div id='msgalert' class="alert alert-warning" role="alert">
                    <strong>Recuerde que para cerrar el expediente sus radicados debe estar archivados y perdera acciones sobre ellos.</strong>
                </div>
                <div id='valradicados' class="alert alert-warning" role="alert" style='display: none'>

                </div>
                <div id='valcontradicados' style=" height: 150px; overflow-y: scroll;" class="alert alert-warning" role="alert" style='display: none'>

                </div>
                <div id='cierreExpdiv' style=" height: 150px; overflow-y: scroll;" class="alert alert-success" role="alert" style='display: none'>

                </div>
            </div>

            <div class="modal-footer">
               
                <button type="button" class="btn btn-danger" id='botonCerrarexp'  onclick='CerrarExpModal1(1);'>Cerrar Expediente</button>
                <button type="button" class="btn btn-danger" id='botonAnularexp'  onclick='CerrarExpModal1(2);'>Anular Expediente</button>
                <button type="button" class="btn btn-danger" id='botonReAbrirexp'  onclick='CerrarExpModal1(0);'>Re-Abrir</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal" onclick="location.reload()">Salir</button>
            </div>
        </div>
    </div>
</div>
<div class="modal  static fade" data-backdrop="static" id="processing-modal" aria-modal="true" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body" style='min-height:300px;' >
                    <div class="text-center">                        
                            <h5><span class="modal-text">Procesando, Espere por favor... </span></h5>
                            <div id="imageLoad"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
</script>
        <input type="hidden" value="<?=$exp;?>" id='numExp' name='numExp'>
        <script type="text/javascript" src="../js/JsApp/comp.js?<?=uniqid('h');?>"></script>
        <script type="text/javascript" src="<?=$ruta_raiz?>/js/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="../js/bootstrap/popper.min.js"></script>
        <script type="text/javascript" src="<?=$ruta_raiz?>/js/bootstrap/bootstrap4.min.js?2"></script>
        <script type="text/javascript" src="../js/axios.min.js"></script>
        <script type="text/javascript" src="../js/JsApp/exp.js?"></script>
        <script>
            <?php if ($permisos['list'] == 1) {?>
             usuario3(<?=$dataExp['DEPE_CODI']?>);
        cargartabla('S');
     //   <?=uniqid('h');?>
     <?php }?>
        </script>
    </div>
</body>

</html>
