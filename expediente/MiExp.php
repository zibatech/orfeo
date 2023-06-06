<?php
/** */
session_start();
/*ini_set('display_errors', '7');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/
$ruta_raiz = "..";
if (!$_SESSION['dependencia']) {
    $fallo['session'] = 'off';
    json_encode($fallo);
    die(); //prueba
}

$krd = $_SESSION["krd"];
foreach ($_GET as $key => $valor) {
    ${$key} = $valor;
}

foreach ($_POST as $key => $valor) {
    ${$key} = $valor;
}
// echo $index." = ".date('Y')."; $index  2012; $index--";
for ($index = date('Y'); $index >= 2020; $index--) {
    $select = '';
    if ($index == date('Y'))
        $select = 'selected';
    $yearselect .= "<option $select value='$index'>$index</option>";
}
//print_r($_SESSION);
$contab=$consul==1 ? 1:0;
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$usua_id = $_SESSION["usua_id"];
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$perm_crea_exp_todasdependencias = 'no';
$db = new ConnectionHandler($ruta_raiz);
//$db->conn->debug =true;
if (!$dependenciaExp) {
    $dependenciaExp = $dependencia;
}

if ($perm_crea_exp_todasdependencias == 'no') {$_condicion_dependencia = " and d.depe_codi = $dependenciaExp";}
;
 $queryDep = "select depe_codi||' - '||depe_nomb, d.depe_codi from dependencia d where
                d.depe_estado=1 $_condicion_dependencia order by depe_codi  ";
$rsD = $db->conn->Execute($queryDep);
$comentarioDev = "Muestra las Series Docuementales";
//    include "$ruta_raiz/include/tx/ComentarioTx.php";
if (!$dependenciaExp) {
    $dependenciaExp = $dependencia;
}

$optionDep = $rsD->GetMenu2("dependenciaExp", $dependenciaExp, "0:-- Seleccione --", false, "", "id='dependenciaExp' class='custom-select required text-uppercase'");
 $queryDep2 = "select depe_codi||' - '||depe_nomb, d.depe_codi from dependencia d where
                d.depe_estado=1   and (depe_codi< 1000 or depe_codi> 9999) order by depe_codi  ";
$rsD2 = $db->conn->Execute($queryDep2);
$optionDep2 = $rsD2->GetMenu2("bsq_dep", 0, "0:-- Seleccione --", false, "", "id='bsq_dep' class='custom-select required text-uppercase'");
include_once "$ruta_raiz/expediente/expediente.class.php";
 $queryDep3 = "select depe_codi||' - '||depe_nomb, d.depe_codi from dependencia d where
                d.depe_estado=1 and (depe_codi< 1000 or depe_codi> 9999) order by depe_codi  ";
$rsD3 = $db->conn->Execute($queryDep3);
$optionDep3=$rsD3->GetMenu2("dependenciaExp0", 0, "0:-- Seleccione --", false, "", "id='dependenciaExpO' class='custom-select required text-uppercase'");
$expClass = new expediente($ruta_raiz);
$paramsExp = $expClass->parametrosEXP($dependencia);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Expediente</title>
    <link rel="stylesheet" type="text/css" media="screen" href="../bodega/sys_img/favicon.png">
    <!-- Bootstrap core CSS-->
    <!--<link rel="stylesheet" type="text/css" media="screen" href="../estilos/smartadmin-production.css">-->
    <!--<link rel="stylesheet" type="text/css" media="screen" href="../estilos/smartadmin-skins.css"> -->
    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/bootstrap-select.min.css">
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="../include/DataTables/datatables.css">

    <link rel="stylesheet" type="text/css" media="screen" href="<?=$ruta_raiz?>/estilos/bootstrap4.min.css">

    <link rel="stylesheet" type="text/css" media="screen" href="../estilos/argo.css">
    <style>
.card-header-argo {
    border-bottom: 1px solid #6c757d;
    background-color: #404040;
    color: #fff;
    font-weight: 700;
    padding: 0px 1.25rem;
}
.btn-nav-cel{
    color: white;
}
.active {
    color: black;
    background-color: #fff;
}


    </style>
</head>
<body>
<noscript>
        <span class="warningjs">Aviso: La ejecución de JavaScript está deshabilitada en su navegador. Es posible que no
            pueda responder todas las preguntas de la encuesta. Por favor, verifique la configuración de su
            navegador.</span>
    </noscript>
                    <header class="navbar navbar-expand-lg sticky-top " style="background-color: #404040; color:#fff; /*#006699*/;">
<input type="hidden" name="dependencia" id="dependencia"  value='<?=$dependencia?>'>
<input type="hidden" name="usua_codi" id="usua_codi"  value='<?=$codusuario?>'>
<input type="hidden" name="usuaid" id="usuaid"  value='<?=$usua_id?>'>
<input type="hidden" name="usua_doc" id="usua_doc"  value='<?=$usua_doc?>'>
    <a class="navbar-brand mb-0 h1" href="#">Expedientes</a>
    <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-collapse collapse" id="navbarTogglerDemo01" style="">

        <ul class="nav navbar-nav   "  id="myTab" role="tablist">
            <li class="nav-item">

            </li>
            <li class="nav-item">
                <a class="nav-link  btn-nav-cel  <? echo $contab==1?'':'active';?>" href="#" id="btn-mis" onclick="listar('mi');"><i class='fa fa-user'></i> Mis Expedientes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link btn-nav-cel" href="#" id="btn-dep" onclick="listar('dp');"><i class='fa fa-folder-open-o'></i> Dependencia</a>
            </li>

            <li class="nav-item">
                    <a class="nav-link btn-nav-cel" href="#" id="btn-ext" onclick="listar('co');"><i class='fa fa-group'></i> Compartidos</a>
                </li>
             <li class="nav-item">
                 <a class="nav-link btn-nav-cel <? echo $contab==1?'active':'';?>" href="#" id="btn-ccc" onclick="  $('#resulEstdatos').hide();$('#busqueda2').hide();"><i class='fa fa-search'> Consulta</i></a>
             </li>
             <li class="nav-item">
                <a class="nav-link btn-nav-cel" href="#" id="btn-dep" onclick="listar('dpOLD');"><i class='fa fa-folder-open-o'></i> Expediente V1</a>
            </li>
            <?php if($_SESSION["USUA_PERM_ADMEXPV1"]>=1) { ?>
            <li class="nav-item">
                 <a class="nav-link btn-nav-cel " href="#" id="btn-ccc" onclick="  $('#resulEstdatos').hide();$('#busqueda').hide();$('#busqueda2').show();"><i class='fa fa-search'> ADM V1</i></a>
             </li>
             <?php } ?>
            <!-- <li class="nav-item">
                 <a class="nav-link" href="#" id="btn-acc" onclick=" filtrarexpC('A', 0);">Acciones</a>
             </li>-->


        </ul>
        <ul class="navbar-nav flex-row ml-md-auto d-none d-md-flex">
          <li class="nav-item"></li>
            <li class="nav-item">
                <a class="btn btn-outline-warning btn-sm" href="#" onclick="$('#crearExpedienteform').show();cancelarCrearExp()" data-toggle="modal" data-target="#crearExpModal"> <i class="fa fa-plus"></i> Crear</a>
                <!--  <a class="btn btn-warning btn-sm" href="#" onclick="$('#crearExpedienteformP').show();">Crear Externo </a>-->
            </li>
        </ul></div>
</header>
<input type="hidden" value="mi" id="tpacc" name="tpacc" >
<div class="col-12 mt-2" id='resulEstdatos' style='display:block'>
        <section id="widget-grid">
            <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">
                <header class='pl-2'>
                    <h2 id="nomListado">Mis Expediente </h2>
                </header>
                <div class="input-group p-0 pb-2 ">
                <div class="col-md-6 "></div>
                            <div class="col-md-6 float-right">
                                    <div class="input-group  ">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text ">Buscar</span>

                                    </div>
                                    <input class="form-control " id="mysearch" name="mysearch" type="text" placeholder="" >
                                        <div class="input-group-prepend"  data-toggle="tooltip" data-placement="bottom" title="Número de expediente Completo" >
                                            <span class="input-group-text" style="width: 75px">Año</span>
                                        </div>
                                        <select class='custom-select'  id="anoDep"><option value='all'>Todos</option><?php echo $yearselect; ?></select>
                                        <!--<div class="input-group-prepend"  data-toggle="tooltip" data-placement="bottom" title="Número de expediente Completo" >
                                            <span class="input-group-text" style="width: 25px">Estado</span>
                                        </div>
                                        <select class='custom-select' id="estadoDep"><option value="0">Abierto</option><option value="1">Cerrado</option><option value="2">Anulado</option></select>
-->
                                        <div class="input-group-append">
                                        <input type="button" onclick="filtrobtn(0)" name="filtrar" value="Filtrar" class="btn btn-xs btn-warning">
                                      
                                        <button type="button"  onclick="filtrobtn(1)" class="btn btn-primary  float-right" ><i class='fa fa-download'></i></button>
                                        </div>
                                    </div>
                                </div>
                 </div>
               
                <!-- widget content -->
                <div class="widget-body p-0 m-0">
                <table class="table table-striped table-responsive-sm table-sm text-uppercase" id="tb_listaexp" name="tb_listaexp" style="width: 100%;">
            <thead><tr class="display-6 text-center" id="tb_titulo"><th></th><th scope="col">Expediente </th><th scope="col">Fecha Creaci&oacute;n</th>
            <th scope="col">Titulo</th><th scope="col">Responsable</th><th scope="col"> Creador</th><th scope="col">Estado</th>
            </tr> </thead>
            <tbody><tr class="text-uppercase display-5">
            <!--<td><a class="btn btn-xs btn-info" href="#" onclick="abrirExp('2021900430100004E');"><i class="fa fa-folder-open" aria-hidden="true"></i></a></td><td scope="row">2021900430100004E</td><td>2021/05/21 10:57</td><td>prueba 21021</td>
                     <td>PRUEBA orfeo</td><td>PRUEBA orfeo</td><td class="text-success">Abierto</td></tr>--></tbody>
            </table>


                </div>

            </div>
        </section>
    </div>

    
    <div class="card " id="busqueda" style="display:none">
        <div class='card-header card-header-argo'>
                            <span id="nomListado">Consulta Expediente </span>
        </div>
        <div class="card-body p-2">
            <div class="input-group p-0 pb-2 ">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label data-toggle="tooltip" data-placement="bottom" title="Número de expediente Completo">
                                Nº Expediente <span class="badge badge-primary">Zidoc</span>
                            </label>
                            <input class="form-control" type="text" name="bsq_nume_expe" id='bsq_nume_expe' value="" > 
                        </div>
                        <div class="col-md-3 form-group">
                            <label data-toggle="tooltip" data-placement="bottom" title="Nombre o titulo u otro atributo">
                                Nombre Expediente <span class="badge badge-primary">Zidoc</span>
                            </label>
                            <input class="form-control" type="text" name="bsq_nomexpe" id="bsq_nomexpe" maxlength="4000" value="">
                        </div>
                        <div class="col-md-3 form-group">
                            <label data-toggle="tooltip" data-placement="bottom" title="Dependencia Responsable">
                                Dependencia
                            </label>
                            <?php echo $optionDep2; ?> 
                        </div>
                        <div class="col-md-3 form-group">
                            <label data-toggle="tooltip" data-placement="bottom" title="Usuario Responsable">
                                Usuario 
                            </label>
                            <select aria-selected="true" name="bsq_usuaDoc" id="bsq_usuaDoc" class="custom-select" >
                                <option value="0">Todos los Usuarios</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label data-toggle="tooltip" data-placement="bottom" title="Radicado perteneciente el radicado">
                                Referencia <span class="badge badge-primary">Zidoc</span>
                            </label>
                            <input class="form-control" type="text" name="bsq_referencia" id="bsq_referencia" value="">
                        </div>
                        <div class="col-md-3 form-group">
                            <label data-toggle="tooltip" data-placement="bottom" title="Consecutivo">
                                Consecutivo <span class="badge badge-primary">Zidoc</span>
                            </label>
                            <input class="form-control" type="text" name="bsq_consecutivo" id="bsq_consecutivo" value="">
                        </div>
                        <div class="col-md-3 form-group">
                            <label data-toggle="tooltip" data-placement="bottom" title="Radicado perteneciente el radicado">
                                Radicado
                            </label>
                            <input class="form-control" type="text" name="bsq_nume_radi" id="bsq_nume_radi" maxlength="17" value="" size="25">
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label><br>
                            <button type="button" class="btn btn-default btn-o-limpia" >Limpiar</button>
                            <button type="button" class="btn btn-primary btn-o-bsq" >Busqueda</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="card " id="busqueda2" style="display:none">
        <div class='card-header card-header-argo'>
                            <span id="nomListado">Consulta Expediente v1 </span>
        </div>
        <div class="card-body p-0" >
               
                      <div class="input-group p-0 pb-2 ">
                            <div class="col-md-6">

                                    <div class="input-group  ">
                                        <div class="input-group-prepend"  data-toggle="tooltip" data-placement="bottom" title="Número de expediente Completo" >
                                            <span class="input-group-text" style="width: 175px">Expediente</span>
                                        </div>
                                        <input class="form-control" type="text" name="bsq_nume_expe2" id='bsq_nume_expe2' value="" >
                                      
                                        </div>  </div>
                                    <div class="col-md-6">


                                    <div class="input-group  ">
                                        <div class="input-group-prepend" >
                                            <span class="input-group-text" style="width: 175px" data-toggle="tooltip" data-placement="bottom" title="Nombre o titulo u otro atributo">Nombre Expediente</span>
                                        </div>
                                        <input class="form-control" type="text" name="bsq_nomexpe2" id="bsq_nomexpe2" maxlength="4000" value="">
                                       
                                    </div>

                                   
                            </div>
                            <div class="col-md-12">
                            <div class="form-group">
                                <button type="button" class="btn btn-default btn-o-limpia2 float-right" >Limpiar</button>
                                <button type="button" class="btn btn-primary btn-o-bsqV1 float-right" >Busqueda</button>
                            </div>
                            </div>
                      </div>
                    
              </div>
    </div>
    <div class="row">
                          
                          </div>
    <div class="card" id='resulEstdatos2' style='display:none'>
        <div class="card-header card-header-argo" >
            <span id="nomListado">Resultado </span>  <!--<button type="button"  data-xls='tb_bsq_listaexp' class="btn btn-outline-warning float-right btn-o-descarga" ><i class="fa fa-download"></i></button>-->
        </div>
                <!-- widget content -->
        <div class="card-body p-0 m-0">
            <table class="table table-striped table-responsive-sm table-sm text-uppercase" id="tb_bsq_listaexp" name="tb_bsq_listaexp" style="width: 100%;">
                <thead>
                    <tr class="display-6 text-center" id="tb_titulo">
                        <th></th>
                        <th scope="col">Expediente</th>
                        <th scope="col">Fecha creación</th>
                        <th scope="col">Titulo</th>
                        <th scope="col">Responsable</th>
                        <th scope="col">Creador</th>
                        <th scope="col">Estado</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>               
        </div>
    </div>
    <div class="card" id='resulEstdatos3' style='display:none'>
        <div class="card-header card-header-argo" >
            <span id="nomListado">Zidoc </span>  <!--<button type="button"  data-xls='tb_bsq_listaexp' class="btn btn-outline-warning float-right btn-o-descarga" ><i class="fa fa-download"></i></button>-->
        </div>
                <!-- widget content -->
        <div class="card-body p-0 m-0">
            <table class="table table-striped table-responsive-sm table-sm text-uppercase" id="tb_bsq_zidoc" name="tb_bsq_listaexp" style="width: 100%;">
                <thead>
                    <tr class="display-6 text-center" id="tb_titulo">
                        <th></th>
                        <th scope="col">Expediente</th>
                        <th scope="col">Dependencia</th>
                        <th scope="col">Fecha</th>
                        <th scope="col">Referencia</th>
                        <th scope="col">Creador</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>               
        </div>
    </div>

    </div>
    

<div class="modal  static fade" data-backdrop="static" id="processing-modal" aria-modal="true" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body" style='min-height:300px' >
                    <div class="text-center">
                            <h5><span class="modal-text">Procesando, Espere por favor... </span></h5>
                            <div id="imageLoad"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
/*
 * Operaciones de expedientes como  crear, incluir, se utiliza la function parent para pode r  ejecutarlo.
 */
/* * Creación de expediente* */
?>
<div  id="crearExpModal" class="modal fade"   aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg"  >
        <div class="modal-content cajabase" >
            <div class="modal-header  bg-primary" style='color:#fff   ;  padding: 10px 5px 0px 5px;' id='tituloExpmodaltt' name='tituloExpmodaltt'>
                <span id='tituloExpmodal'> CREACION EXPEDIENTE VIRTUAL </span>
                <?php if ($_SESSION['CREATEEXPEXT'] == 1) {?>
                    <div class="switchToggle" id='switchdato'>
                        <input type="checkbox" id="switch" name='switch' onchange='tpexpcrea()'>
                        <label for="switch">Toggle</label>
                    </div>
                <?php }?>
            </div>
            <div class="modal-body">
                <div name='dataformCrearExp' id='dataformCrearExp'>
                    <form action="#" id='formModRadanex'   class="form-group " name="formExpinicial" style='margin: 0px' method ="post" enctype="multipart/form-data" >
                        <div class='form-group'>
                            <div class="text-primary " >
                                APLICAR DE LA TRD DEL EXPEDIENTE
                            </div>
                            <div class="input-group  ">
                                <div class="input-group-prepend" >
                                    <span class="input-group-text" style="width: 175px">Dependencia</span>
                                </div>
                                <?php echo $optionDep; ?>

                                <div class='with-errors text-danger pull-right' id='error-coddepe'></div>
                            </div>
                            <div class="input-group  ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"  style="width: 175px">Serie</span>
                                </div>

                                <select name="selSerie" id="selSerie" onChange='' class='custom-select required text-uppercase'>
                                    <option value="0">-- Seleccione --</option>
                                    <?php echo $optionSSd; ?>
                                </select>


                            </div>

                            <div class="input-group  ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"  style="width: 175px">Subserie</span>
                                </div>
                                <select  id="selSubSerie" name='selSubSerie' onchange=''  class='custom-select required text-uppercase'  >
                                    <option value='0' data-cla='' id='cla0'>--- Seleccione ---</option>
                                </select>

                            </div>
                          <!--  <div class="input-group  ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"  style="width: 175px">Clasificación</span>
                                </div>
                                <div id="stcladifica" class='form-control' name="stcladifica"></div>

                            </div>-->
                            <div class="input-group  ">
                            <div class="input-group-prepend">
                                    <span class="input-group-text"  style="width: 175px">Seguridad Inicial</span>
                                </div>
                                <select name="idseguridad" id="idseguridad" class='custom-select required text-uppercase' >
                                    <option value="0" class='text-success'>Público</option>
                                    <option value="1">Privado dependencia (Solo usuarios área y usuarios jefes )</option>
                                    <option value="2">Privado (Solo Usuario Responsable Y Jefe) </option>
                                </select>
                                </div>


                        </div>
                        <div class="form-group ">

                            <div class="text-primary ">
                                DATOS DEL EXPEDIENTE
                            </div>
                            <div class="input-group">

                                <select name='anoExp' id='anoExp' class='custom-select' onchange='fn_numExp()'  >
                                    <?php
$seleted = "selected='selected'";
for ($index = date('Y'); $index >= 2020; $index--) {
    echo "<option value='$index'  $seleted >$index</option>";
    $seleted = '';
}
?>
                         </select>
                                <input type='text' name='depExp' id='depExp' readonly="readonly" value='<?php echo $dependenciaC; ?>'   maxlength="3" size="3" class='form-control text-center'>
                                <input type='text' name='numsrb'  readonly="readonly"  id='numsrb' value='00000'  maxlength="4" size="4"   class='form-control text-center' >
                                <input type='text' name='consecutivoExp'  readonly="readonly"  id='consecutivoExp' value='000001'  maxlength="6"  size=6 class='form-control text-center'>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">E</span>
                                </div>
                            </div>

                        </div>

                        <div class="form-group ">
                            <div class="col-sm-12 h6">
                                <div class='alert alert-warning'>El consecutivo "000X" temporal y puede cambiar al momento de crear el expediente.  <strong id='dt_num_exp'>20180000100001E</strong> </div>
                            </div>

                            <div class="input-group  ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"  style="width: 175px"><?=$paramsExp[1] ? $paramsExp[1] : "Nombre Expediente";?></span>
                                </div>
                                <input type="text" id='exptilulo' name='exptilulo' class='form-control'>
                            </div>
                            <div id='optionextatrib' name='optionextatrib'  style='display:block'>

                                <div class="input-group  ">
                                    <div class="input-group-prepend" <?=$paramsExp[2] ? '' : "style='display:none'";?>>
                                        <span class="input-group-text"  style="width: 175px"><?=$paramsExp[2] ? $paramsExp[2] : "Descriptor 1";?></span>
                                    </div>
                                    <input type="text" id='param2' name='param2' class='form-control'>
                                </div>
                                <div class="input-group  " <?=$paramsExp[3] ? '' : "style='display:none'";?>>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"  style="width: 175px"><?=$paramsExp[3] ? $paramsExp[3] : "Descriptor 2";?></span>
                                    </div>
                                    <input type="text" id='param3' name='param3' class='form-control'>
                                </div>
                                <div class="input-group  " <?=$paramsExp[4] ? '' : "style='display:none'";?>>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"  style="width: 175px"><?=$paramsExp[4] ? $paramsExp[4] : "Descriptor 3";?></span>
                                    </div>
                                    <input type="text" id='param3' name='param3' class='form-control'>
                                </div>
                                <div class="input-group  " <?=$paramsExp[7] ? '' : "style='display:none'";?>>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"  style="width: 175px"><?=$paramsExp[5] ? $paramsExp[5] : "Descriptor 4";?></span>
                                    </div>
                                    <input type="text" id='param4' name='param4' class='form-control'>
                                </div>
                            </div>
                            <div class="input-group  ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"  style="width: 175px">Fecha de Inicio</span>
                                </div>
                                <input type='date' value='<?php echo date("Y-m-d"); ?>' name='fechaExp' id='fechaExp' class='form-control' style='width: 200px '>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" ><?php echo " < " . date("d/m/Y"); ?></span>
                                </div>

                            </div>
                            <div class="input-group  ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"  style="width: 175px">Responsable</span>
                                </div>
                                <select name="selUsuario" id="selUsuario" class='custom-select required text-uppercase' >
                                    <option value="0">-- Seleccione --</option>
                                </select>

                            </div>
                            <div  class='with-errors text-danger pull-right' id='error-crearexp'></div>

                        </div>
                    </form>
                </div>
                <input type="hidden" name='accion' value='crearConfirmar'>
                <div id ="numExpL" style='display:none'>

                </div>

                <div class='row'>

                </div>

                <div name='confCrea' id='confCrea' style='display:none'>
                    <div class="form-group" >
                        <div class="col-sm-12">

                            <div class="alert alert-warning">
                                <strong>ESTA SEGURO DE CREAR EL EXPEDIENTE </strong>  <span id='titleexp'></span> ?
                                <br>
                                con la siguiente información:
                            </div>
                            <table border="0" width="100%" align="center" class="listadoC" cellspacing="1" cellpadding="0">

                                <tbody>

                                    <tr align="center" class="titulos2">
                                        <th height="25" class="titulos2" colspan="2">APLICACION DE LA TRD EL EXPEDIENTE</th>
                                    </tr>
                                    <tr>
                                        <td><label>TITULO</label></td><td id="txt-titulo" class="form-control" >  </td>
                                    </tr>
                                    <tr>
                                        <td><label>SERIE</label></td> <td id="txt-serie" class="form-control"> </td>
                                    </tr>
                                    <tr>
                                        <td><label>SUBSERIE</label></td><td id="txt-subserie" class="form-control"></td>
                                    </tr>
                                    <tr id="tr-extEntidad">
                                        <td><label>ENTIDAD</label></td><td id="txt-extEntidad" class="form-control"></td>
                                    </tr>
                                    <tr id="tr-extasunto">
                                        <td><label>ASUNTO</label></td><td id="txt-extasunto" class="form-control"></td>
                                    </tr>
                                    <tr id="tr-extobservacion">
                                        <td><label>OBSERVACION</label></td><td id="txt-extobservacion" class="form-control"></td>
                                    </tr>
                                    <tr>
                                        <td><label>FECHA DE INICIO</label></td><td class="form-control" id="txt-fechini"></td>
                                    </tr>
                                    <tr>
                                        <td><label>SEGURIDAD</label></td><td class="form-control" id="txt-seguridad"></td>
                                    </tr>

                                    <tr>
                                        <td><label>RESPONSABLE</label></td><td class="form-control" id="txt-resp"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-sm-12">
                            <div class="alert alert-danger">
                                <strong>Nota: </strong> No se podrá modificar el número del expediente una vez haya sido creado.

                            </div>
                        </div>
                        <div class='row'>

                        </div>

                    </div>
                </div>
                <div name='creacionconfi' id='creacionconfi' style='display:none'>
                    <div class="form-group" >

                    </div>
                    <div class='row'>

                    </div>

                </div>


            </div>

            <div class="modal-footer">
                <input name="btnConfCrea" type="button" onclick='crearEA();' class="btn btn-warning "  style='display: none' id='btnConfCrea' value=" Confirmación Creación Expediente ">
                <input type="button"  value="Crear Expediente" id='btnRcera' class='btn btn-warning btn-crearExp' >
             <!--   <input type="submit"  value="Radicar" id='btnRadicar' class='btn btn-success'>-->
                <button type="button" class="btn btn-danger " data-dismiss="modal" style='display: none' id='btnradcerrartx' onclick="regresar()">Cerrar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick='cancelarCrearExp()' id='btnRadcancelartx'>Cancelar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick='location.reload()' style='display: none' id='btnRadcancelartxslir'>Salir</button>
            </div>

        </div>
    </div>
</div>

<div  id="AddCrearExpModal" class="modal fade"   aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg"  >
        <div class="modal-content cajabase" >
            <div class="modal-header  bg-primary" style='color:#fff   ;  padding: 10px 5px 0px 5px;' id='tituloExpmodaltt' name='tituloExpmodaltt'>
                <span id='tituloExpmodal'> CREACION EXPEDIENTE VIRTUAL </span>
               
            </div>
            <div class="modal-body">
            <div class="input-group  ">
                                <div class="input-group-prepend" >
                                    <span class="input-group-text" style="width: 175px">Expediente</span>
                                </div>
                                
                                  <input type="text"  name="expOld" id="expOld" class='form-control ' readonly> 

                                <div class='with-errors text-danger pull-right' id='error-coddepe'></div>
                            </div>
                              <div class="input-group  ">
                                <div class="input-group-prepend" >
                                    <span class="input-group-text" style="width: 175px">Dependencia</span>
                                </div>
                                <?php echo $optionDep3; ?>
                                 

                                <div class='with-errors text-danger pull-right' id='error-coddepe'></div>
                            </div>
                            <div class="input-group  ">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"  style="width: 175px">Responsable</span>
                                </div>
                                <select name="selUsuario2" id="selUsuario2" class='custom-select required text-uppercase' >
                                    <option value="0">-- Seleccione --</option>
                                </select>

                            </div>
                            <div id='respodatox'></div>

            </div>
            
            <div class="modal-footer">
                <input type="button"  value="Asociar responsable" id='btnRcera' class='btn btn-warning btn-crearExpOLD' onclick='crearExpOLD () '>   
                <button type="button" class="btn btn-danger" data-dismiss="modal" id='btnRadcancelartxslir'>Salir</button>
            </div>

        </div>
    </div>
</div>
<!-- Moda
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Understood</button>
      </div>
    </div>
  </div>
</div>
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#AddCrearExpModal">+</button>l -->

<script type="text/javascript" src="<?=$ruta_raiz?>/js/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="<?=$ruta_raiz?>/js/jquery.table2excel.js"></script>
<script type="text/javascript" src="../js/JsApp/comp.js?<?=uniqid('h');?>"></script>
<script type="text/javascript" src="../js/bootstrap/popper.min.js"></script>
<script type="text/javascript" src="<?=$ruta_raiz?>/js/bootstrap/bootstrap4.min.js?2"></script>
<script type="text/javascript" src="../js/axios.min.js"></script>
<script type="text/javascript" src="../js/JsApp/exp.js?<?=uniqid('h');?>"></script>
<script type="text/javascript" src="<?=$ruta_raiz?>/contratistas/js/modal.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    
    <script>
     <? echo $contab==1?"  $('#resulEstdatos').hide(); $('#busqueda').show(); ":" listar('mi');";?>
        $("#depExp").val($("#dependenciaExp").val());
        series();
        $("#selSubSerie").empty();
        usuario();</script>
    <script>
        $(function() {
            console.log(moment())
            var ruta_raiz = '<?= $ruta_raiz ?>';
            $('.btn-o-bsq').on('click', function(e) {
                $('#resulEstdatos3').hide();
                $('#tb_bsq_zidoc tbody').html('');
                var FUID = [];
                var request = $.get(ruta_raiz+'/zidoc/zidoc_exp_search.php?exp='+$('#bsq_nume_expe').val()+'&nom='+$('#bsq_nomexpe').val()+'&ref='+$('#bsq_referencia').val()+'&con='+$('#bsq_consecutivo').val());
                request.done(function(data) {
                    const grupos = data;
                    grupos.forEach(function(grupo, indice) {
                        const resultados = grupo.data;
                        if(resultados)
                        {
                            $('#resulEstdatos3').show();
                            resultados.forEach(function(registro, indice) {
                                if (FUID.indexOf(registro.id) == -1)
                                {
                                    FUID.push(registro.id);
                                    $('#tb_bsq_zidoc tbody').append(`
                                        <tr>
                                            <td>
                                                <button data-id="${registro.id}" class="documents btn btn-sm btn-primary">
                                                    <i class="fa fa-file-o"></i>
                                                </button>
                                            </td>
                                            <td>${registro.NumeroExpediente}</td>
                                            <td>${registro.Dependencia}</td>
                                            <td>${moment(registro.Fecha).format('YYYY/MM/DD')}</td>
                                            <td>${registro.ReferenciaDoc}</td>
                                            <td>${registro.Usuario}</td>
                                        </tr>
                                    `);
                                }
                            })
                        }
                    });
                });
            });

            $('body').delegate('.documents', 'click', function(e) {
                var id_expediente = $(this).data('id');
                var recargar_al_cierre = false;

                modal(ruta_raiz+'/zidoc/zidoc_doc_results.php?id_exp='+id_expediente, 'height=900,width=1400,scrollbars=yes,status=no', recargar_al_cierre)
            });

            $('body').delegate('.zidoc', 'click', function(e) {
                var expediente = $(this).data('expediente');
                var recargar_al_cierre = false;

                modal(ruta_raiz+'/zidoc/zidoc_results.php?exp='+expediente, 'location=no,height=900,width=600,scrollbars=no,status=no', recargar_al_cierre);
            });
        })
    </script>
</body>
</html>
