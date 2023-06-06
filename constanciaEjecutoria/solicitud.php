<?php

if (!$ruta_raiz)
	$ruta_raiz = "..";

session_start();

if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

?>

<html>
<head>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>

<style>

.scrollX {
  overflow-y: scroll;
  overflow-x: scroll;
}

.circleBlue {
  height: 15px;
  width: 15px;
  background-color: #0000FF;
  border-radius: 50%;
  display: inline-block;
}

.circleGreen {
  height: 15px;
  width: 15px;
  background-color: #008000;
  border-radius: 50%;
  display: inline-block;
}

.borderNav{
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    padding: 10px;   
}

</style> 

</head>
<body>
    <h1 class="mt-2">Constancia ejecutoria</h1>
    <ul class="nav  nav-tabs mb-3" id="proyecor-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Nueva Solicitud</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Devoluciones</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Constancias finalizadas</a>
    </li>
    </ul>
    <div class="tab-content borderNav" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <!-- TAB DE SOLICITUD -->

            </br>
            <form id = "fmBusqueda">
            <!-- INICIO FORMULARIO DE BUSQUEDA -->    
                <div class="container">

                    <!-- FILA 1 -->
                    <div class="row">
                        <div class="col">
                            
                            <div class="row">
                                <label for="inResolucion" class="col-sm-4 col-form-label  col-form-label-sm">No Resolución:</label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control form-control-sm" id="inResolucion">
                                </div>
                            </div>

                        </div>
                        <div class="col">
                        
                            <div class="row">
                                <button type="button" class="btn btn-primary btn-sm" id="btBuscar">Buscar</button>
                            </div>

                        </div>
                    </div>
                </div>
            <!-- FIN FORMULARIO DE BUSQUEDA -->    
            </form>
            <div class="alert alert-warning" role="alert" id="dvBusqueda" style="display: none"></div>
            </br>

            <form id="fmGeneral">
            <!-- INICIO FORMULARIO GENERAL -->
            <input  name="inFuncion" id="inFuncion" type="hidden" value="2">        
            <div class="container">
                <!-- FILA 1 -->
                <div class="row">
                    <div class="col">
                        
                        <div class="row">
                            <label for="inIdple" class="col-sm-4 col-form-label form-control-sm">No. ID/PLE*</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control form-control-sm" id="inIdple" name="inIdple">
                            </div>
                        </div>

                    </div>
                    <div class="col">
                      
                        <div class="row">
                            <label for="inResolucionReposicion" class="col-sm-4 col-form-label form-control-sm">No. Resolución por la cual resuelve recurso de reposición</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control form-control-sm" id="inResolucionReposicion" name="inResolucionReposicion" disabled>
                            </div>
                        </div>

                    </div>
                </div>

                 <!-- FILA 2 -->
                <div class="row">
                    <div class="col">
                        
                        <div class="row">
                            <label for="inResolucionInicial" class="col-sm-4 col-form-label form-control-sm">No. Resolución*</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control form-control-sm" id="inResolucionInicial" name="inResolucionInicial">
                            </div>
                        </div>

                    </div>
                    <div class="col">
                       
                        <div class="row">
                            <label for="inFechaReposicion" class="col-sm-4 col-form-label form-control-sm">Fecha reposición</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control form-control-sm" id="inFechaReposicion" name="inFechaReposicion" disabled>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- FILA 3 -->
                <div class="row">
                    <div class="col">
                        
                        <div class="row">
                            <label for="inFechaActo" class="col-sm-4 col-form-label form-control-sm">Fecha de Firma*</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control form-control-sm" id="inFechaActo" name="inFechaActo">
                            </div>
                        </div>

                    </div>
                    <div class="col">
                       
                        <div class="row">
                                <label for="seRecursoQueja" class="col-sm-4 col-form-label form-control-sm">Presenta recursos de queja revocatoria directa*</label>
                                <div class="col-sm-8">
                                <select class="form-select form-select-sm" id="seRecursoQueja" name="seRecursoQueja">
                                    <option value="0" selected>Seleccione una opción</option>
                                    <option value="Si">Si</option>
                                    <option value="No">No</option>
                                </select>
                                </div>
                        </div>
                        
                    </div>
                </div>

                <!-- FILA 4 -->
                <div class="row">
                    <div class="col">
                        
                        <div class="row">
                            <label for="inNitCC" class="col-sm-4 col-form-label form-control-sm">Nit o C.C*</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="inNitCC" name="inNitCC">
                            </div>
                        </div>

                    </div>
                    <div class="col">
                       
                        <div class="row">
                            <label for="inResolucionRecurosQueja" class="col-sm-4 col-form-label form-control-sm">No. Resolución por la cual resuelve queja o revocatoria directa</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control form-control-sm" id="inResolucionRecurosQueja" name="inResolucionRecurosQueja" disabled>
                            </div>
                        </div>
                        
                    </div>
                </div>   

                <!-- FILA 5 -->
                <div class="row">
                    <div class="col">
                        
                        <div class="row">
                            <label for="seRazonSocial" class="col-sm-4 col-form-label form-control-sm">Razón Social*</label>
                            <div class="col-sm-8">
                            <select class="form-select form-select-sm" id="seRazonSocial" name="seRazonSocial">
                                <option value="0" selected>Seleccione una opción</option>
                                <option value="Persona Natural">Persona Natural</option>
                                <option value="Persona Jurídica">Persona Jurídica</option>
                            </select>
                            </div>
                        </div>

                    </div>
                    <div class="col">
                       
                        <div class="row">
                            <label for="seNotificacionFinal" class="col-sm-4 col-form-label form-control-sm">Tipo de notificación final</label>
                            <div class="col-sm-8">
                            <select class="form-select form-select-sm" id="seNotificacionFinal" name="seNotificacionFinal" disabled>
                                <option value="0" selected>Seleccione una opción</option>
                                <option value="Notificación personal">Notificación personal</option>
                                <option value="Notificación electrónica">Notificación electrónica</option>
                                <option value="Notificación por aviso">Notificación por aviso</option>
                                <option value="Notificación por edicto">Notificación por edicto</option>
                            </select>
                            </div>
                        </div>
                        
                    </div>
                </div>   
                
                <!-- FILA 6 -->
                <div class="row">
                    <div class="col">
                        
                        <div class="row">
                            <label for="seTipoNotifiacion" class="col-sm-4 col-form-label form-control-sm">Tipo notificación del acto*</label>
                            <div class="col-sm-8">
                            <select class="form-select form-select-sm" id="seTipoNotifiacion" name="seTipoNotifiacion">
                                <option value="0" selected>Seleccione una opción</option>
                                <option value="Notificación personal">Notificación personal</option>
                                <option value="Notificación electrónica">Notificación electrónica</option>
                                <option value="Notificación por aviso">Notificación por aviso</option>
                                <option value="Notificación por edicto">Notificación por edicto</option>
                            </select>
                            </div>
                        </div>

                    </div>
                    <div class="col">
                       
                        <div class="row">
                            <label for="inFechaRecursoQueja" class="col-sm-4 col-form-label form-control-sm" data-bs-toggle="tooltip" data-bs-placement="left" title="La fecha debe ser tomada del acuse de recibo efectivo o del acta de notificación personal">Fecha de recursos</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control form-control-sm" id="inFechaRecursoQueja" name="inFechaRecursoQueja" disabled>
                            </div>
                        </div>
                        
                    </div>
                </div>  
                
                <!-- FILA 7 -->
                <div class="row">
                    <div class="col">
                        
                        <div class="row">
                            <label for="inFechaAcuse" class="col-sm-4 col-form-label form-control-sm" data-bs-toggle="tooltip" data-bs-placement="left" title="La fecha debe ser tomada del acuse de recibo efectivo o del acta de notificación personal">Fecha acuse y/o notificación*</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control form-control-sm" id="inFechaAcuse" name="inFechaAcuse">
                            </div>
                        </div>

                    </div>
                    <div class="col">
                       
                        <div class="row">
                            <label for="inExpediente" class="col-sm-4 col-form-label form-control-sm" data-bs-toggle="tooltip" data-bs-placement="left" title="Ingrese el expediente o número de expedientes separados por coma ','">Expediente*</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm" id="inExpediente" name="inExpediente" >
                            </div>
                        </div>
                        
                    </div>
                </div>     
                
                <!-- FILA 8 -->
                <div class="row">
                    <div class="col">
                        
                        <div class="row">
                            <label for="sePresentaRecurso" class="col-sm-4 col-form-label form-control-sm">Presenta recursos*</label>
                            <div class="col-sm-8">
                                <select class="form-select form-select-sm" id="sePresentaRecurso" name="sePresentaRecurso">
                                    <option value="0" selected>Seleccione una opción</option>
                                    <option value="Apelación">Apelación</option>
                                    <option value="Reposición">Reposición</option>
                                    <option value="Ambos">Ambos</option>
                                    <option value="Ninguno">Ninguno</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="col">
                       
                        <div class="row" style="visibility: hidden">
                            <label for="inFechaUltimoActo" class="col-sm-4 col-form-label form-control-sm">Fecha notificación último acto</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control form-control-sm" id="inFechaUltimoActo" name="inFechaUltimoActo">
                            </div>
                        </div>
                        
                    </div>
                </div>  
                
                 <!-- FILA 9 -->
                 <div class="row">
                    <div class="col">
                        
                        <div class="row">
                            <label for="inResolucionApleacion" class="col-sm-4 col-form-label form-control-sm">No. Resolución por la cual resuelve recurso de apelación</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control form-control-sm" id="inResolucionApleacion" name="inResolucionApleacion" disabled>
                            </div>
                        </div>

                    </div>
                    <div class="col">
                       
                        <div class="row" style="visibility: hidden">
                            <label for="inFechaEjecutoria" class="col-sm-4 col-form-label form-control-sm">Fecha ejecutoria</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control form-control-sm" id="inFechaEjecutoria" name="inFechaEjecutoria">
                            </div>
                        </div>

                    </div>
                </div>     
                
                <!-- FILA 10 -->
                <div class="row">
                    <div class="col">
                        
                        <div class="row" >
                            <label for="inFechaApelacion" class="col-sm-4 col-form-label form-control-sm">Fecha apelación</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control form-control-sm" id="inFechaApelacion" name="inFechaApelacion" disabled>
                            </div>
                        </div>

                    </div>
                    <div class="col">
                       
                       <!-- NO DATA -->
                        
                    </div>
                </div>                 
                

            </div>
            </br>
            <div class="alert alert-warning" role="alert" id="dvForm" style="display: none"></div>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-secondary" id="btBorrarForm">Borrar formulario</button>    
                <button type="button" class="btn btn-primary" id="btAgregar">Agregar</button>
            </div>
            </form>
            <!-- FIN INICIO FORMULARIO GENERAL -->  
            <!--INICIO FORMULARIO CARAG MASIVA -->
            <form method="post" action="" enctype="multipart/form-data" id="fmMasivo">
                    <div class="justify-content-center">    
                            <label for="btSolicitudMasiva" class="form-label">Carga masiva</label>  
                            <input class="form-control form-control-sm" type="file" name ="btSolicitudMasiva" id="btSolicitudMasiva" 
                                onchange='getFile(this)' accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">     
                    </div>
                    <center>
                        <div class="spinner-grow text-info" role="status" id="spLoading" style="visibility: hidden">
                                                <span class="visually-hidden">Loading...</span>
                        </div>  
                    </center> 
            </form> 
            <!-- FIN FORMULARIO MASIVO -->

            <!--INICIO TABLA SOLICITUDES POR  ENVIAR -->
            <div class="scrollX" style="height:300px;" id="dvTableHorizontal" >
            <h3>Solicitudes por enviar.</h3>
            <table id="tbHorizontal" class="table table-striped table-bordered table-sm align-middle"  cellspacing="0"
                width="100%">
                <thead>
                    <tr class="align-middle">
                    <th scope="col">Fecha solicitud</th>
                    <th scope="col">ID Solicitud</th>
                    <th scope="col">Item</th>
                    <th scope="col">No. Id/Pe</th>
                    <th scope="col">No. Resolución</th>
                    <th scope="col">Fecha de firma</th>
                    <th scope="col">Nit O CC</th>
                    <th scope="col">Razón social</th>
                    <th scope="col">Tipo de notificación del acto</th>
                    <th scope="col">Fecha acuse y/o notificación</th>
                    <th scope="col">Presenta recursos</th>
                    <th scope="col">Resolución por la cual se resuelve recurso de apelación</th>
                    <th scope="col">Fecha del acto de apelación</th>
                    <th scope="col">Resolución por la cual se resuelve recurso de reposición</th>
                    <th scope="col">Fecha del acto de reposición</th>
                    <th scope="col">Presenta recursos de queja o revocatoria directa</th>
                    <th scope="col">Resolución por la cual se resuelve queja o revocatoria directa</th>
                    <th scope="col">Tipo de notificación del acto final</th>
                    <th scope="col">Fecha de recurso</th>
                    <th scope="col">Expediente</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>  
            </div>
            </br>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-success" id="btEnviar">Enviar</button> 
                <button type="button" class="btn btn-danger" id="btBorrarPen">Borrar</button>    
            </div>

            <!-- FIN TABLA SOLICITUDES POR  ENVIAR -->
            </br></br></br></br></br></br>      
            <!-- FINTAB DE SOLICITUD -->
        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">


            <!--INICIO TABLA DEVOLUCIONES -->
            <div class="alert alert-warning" role="alert" id="dvTbDevolucion" style="display: none"></div>
            <div class="scrollX" style="height:300px;" id="dvTableDevolucion" >
            <h3>Solicitudes por enviar.</h3>
            <table id="tbDevolucion" class="table table-striped table-bordered table-sm align-middle"  cellspacing="0"
                width="100%">
                <thead>
                    <tr class="align-middle">
                    <th scope="col"><input class="form-check-input" type="checkbox" value="" id="cbSolicitudGen"></th>
                    <th scope="col">Alerta</th>
                    <th scope="col">Fecha solicitud</th>
                    <th scope="col">ID Solicitud</th>
                    <th scope="col">Item</th>
                    <th scope="col">No. Id/Pe</th>
                    <th scope="col">No. Resolución</th>
                    <th scope="col">Fecha de firma</th>
                    <th scope="col">Nit O CC</th>
                    <th scope="col">Razón social</th>
                    <th scope="col">Tipo de notificación del acto</th>
                    <th scope="col">Fecha acuse y/o notificación</th>
                    <th scope="col">Presenta recursos</th>
                    <th scope="col">Resolución por la cual se resuelve recurso de apelación</th>
                    <th scope="col">Fecha del acto de apelación</th>
                    <th scope="col">Resolución por la cual se resuelve recurso de reposición</th>
                    <th scope="col">Fecha del acto de reposición</th>
                    <th scope="col">Presenta recursos de queja o revocatoria directa</th>
                    <th scope="col">Resolución por la cual se resuelve queja o revocatoria directa</th>
                    <th scope="col">Tipo de notificación del acto final</th>
                    <th scope="col">Fecha de recurso</th>
                    <th scope="col">Expediente</th>
                    <th scope="col">Comentario</th>
                    <th scope="col">Fecha notificación último acto</th>
                    <th scope="col">Fecha ejecutoria</th>
                    <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>  
            </div>
            </br>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-success" id="btRetonarDevolucion">Enviar</button>    
            </div>

            <!-- FIN TABLA DEVOLUCIONES -->       
            
            <!--MODAL EDICION -->

            <div class="modal" tabindex="-1" id="mEdicion">
                <div class="modal-dialog modal-fullscreen">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Solicitud constancia (Edición)</h5>
                        <button id="bmEdicionClose" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">


                    <form id="fmGeneral2">
                        <!-- INICIO FORMULARIO EDICION -->
                        <input  name="inFuncion" id="inFuncion2" type="hidden" value="9">        
                        <input  name="idSolicitudEdit" id="idSolicitudEdit" type="hidden" value="-1">        
                        <div class="container">
                            <!-- FILA 1 -->
                            <div class="row">
                                <div class="col">
                                    
                                    <div class="row">
                                        <label for="inIdple2" class="col-sm-4 col-form-label form-control-sm">No. ID/PLE*</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control form-control-sm" id="inIdple2" name="inIdple">
                                        </div>
                                    </div>

                                </div>
                                <div class="col">
                                
                                    <div class="row">
                                        <label for="inResolucionReposicion2" class="col-sm-4 col-form-label form-control-sm">No. Resolución por la cual resuelve recurso de reposición</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control form-control-sm" id="inResolucionReposicion2" name="inResolucionReposicion" disabled>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- FILA 2 -->
                            <div class="row">
                                <div class="col">
                                    
                                    <div class="row">
                                        <label for="inResolucionInicial2" class="col-sm-4 col-form-label form-control-sm">No. Resolución*</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control form-control-sm" id="inResolucionInicial2" name="inResolucionInicial">
                                        </div>
                                    </div>

                                </div>
                                <div class="col">
                                
                                    <div class="row">
                                        <label for="inFechaReposicion2" class="col-sm-4 col-form-label form-control-sm">Fecha reposición</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control form-control-sm" id="inFechaReposicion2" name="inFechaReposicion" disabled>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- FILA 3 -->
                            <div class="row">
                                <div class="col">
                                    
                                    <div class="row">
                                        <label for="inFechaActo2" class="col-sm-4 col-form-label form-control-sm">Fecha de Firma*</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control form-control-sm" id="inFechaActo2" name="inFechaActo">
                                        </div>
                                    </div>

                                </div>
                                <div class="col">
                                
                                    <div class="row">
                                            <label for="seRecursoQueja2" class="col-sm-4 col-form-label form-control-sm">Presenta recursos de queja revocatoria directa*</label>
                                            <div class="col-sm-8">
                                            <select class="form-select form-select-sm" id="seRecursoQueja2" name="seRecursoQueja">
                                                <option value="0" selected>Seleccione una opción</option>
                                                <option value="Si">Si</option>
                                                <option value="No">No</option>
                                            </select>
                                            </div>
                                    </div>
                                    
                                </div>
                            </div>

                            <!-- FILA 4 -->
                            <div class="row">
                                <div class="col">
                                    
                                    <div class="row">
                                        <label for="inNitCC2" class="col-sm-4 col-form-label form-control-sm">Nit o C.C*</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="inNitCC2" name="inNitCC">
                                        </div>
                                    </div>

                                </div>
                                <div class="col">
                                
                                    <div class="row">
                                        <label for="inResolucionRecurosQueja2" class="col-sm-4 col-form-label form-control-sm">No. Resolución por la cual resuelve queja o revocatoria directa</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control form-control-sm" id="inResolucionRecurosQueja2" name="inResolucionRecurosQueja" disabled>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>   

                            <!-- FILA 5 -->
                            <div class="row">
                                <div class="col">
                                    
                                    <div class="row">
                                        <label for="seRazonSocial2" class="col-sm-4 col-form-label form-control-sm">Razón Social*</label>
                                        <div class="col-sm-8">
                                        <select class="form-select form-select-sm" id="seRazonSocial2" name="seRazonSocial">
                                            <option value="0" selected>Seleccione una opción</option>
                                            <option value="Persona Natural">Persona Natural</option>
                                            <option value="Persona Jurídica">Persona Jurídica</option>
                                        </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="col">
                                
                                    <div class="row">
                                        <label for="seNotificacionFinal2" class="col-sm-4 col-form-label form-control-sm">Tipo de notificación final</label>
                                        <div class="col-sm-8">
                                        <select class="form-select form-select-sm" id="seNotificacionFinal2" name="seNotificacionFinal" disabled>
                                            <option value="0" selected>Seleccione una opción</option>
                                            <option value="Notificación personal">Notificación personal</option>
                                            <option value="Notificación electrónica">Notificación electrónica</option>
                                            <option value="Notificación por aviso">Notificación por aviso</option>
                                            <option value="Notificación por edicto">Notificación por edicto</option>
                                        </select>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>   
                            
                            <!-- FILA 6 -->
                            <div class="row">
                                <div class="col">
                                    
                                    <div class="row">
                                        <label for="seTipoNotifiacion2" class="col-sm-4 col-form-label form-control-sm">Tipo notificación del acto*</label>
                                        <div class="col-sm-8">
                                        <select class="form-select form-select-sm" id="seTipoNotifiacion2" name="seTipoNotifiacion">
                                            <option value="0" selected>Seleccione una opción</option>
                                            <option value="Notificación personal">Notificación personal</option>
                                            <option value="Notificación electrónica">Notificación electrónica</option>
                                            <option value="Notificación por aviso">Notificación por aviso</option>
                                            <option value="Notificación por edicto">Notificación por edicto</option>
                                        </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="col">
                                
                                    <div class="row">
                                        <label for="inFechaRecursoQueja2" class="col-sm-4 col-form-label form-control-sm" data-bs-toggle="tooltip" data-bs-placement="left" title="La fecha debe ser tomada del acuse de recibo efectivo o del acta de notificación personal">Fecha de recursos</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control form-control-sm" id="inFechaRecursoQueja2" name="inFechaRecursoQueja" disabled>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>  
                            
                            <!-- FILA 7 -->
                            <div class="row">
                                <div class="col">
                                    
                                    <div class="row">
                                        <label for="inFechaAcuse2" class="col-sm-4 col-form-label form-control-sm" data-bs-toggle="tooltip" data-bs-placement="left" title="La fecha debe ser tomada del acuse de recibo efectivo o del acta de notificación personal">Fecha acuse y/o notificación*</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control form-control-sm" id="inFechaAcuse2" name="inFechaAcuse">
                                        </div>
                                    </div>

                                </div>
                                <div class="col">
                                
                                    <div class="row">
                                        <label for="inExpediente2" class="col-sm-4 col-form-label form-control-sm" data-bs-toggle="tooltip" data-bs-placement="left" title="Ingrese el expediente o número de expedientes separados por coma ','">Expediente*</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control form-control-sm" id="inExpediente2" name="inExpediente" >
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>     
                            
                            <!-- FILA 8 -->
                            <div class="row">
                                <div class="col">
                                    
                                    <div class="row">
                                        <label for="sePresentaRecurso2" class="col-sm-4 col-form-label form-control-sm">Presenta recursos*</label>
                                        <div class="col-sm-8">
                                            <select class="form-select form-select-sm" id="sePresentaRecurso2" name="sePresentaRecurso">
                                                <option value="0" selected>Seleccione una opción</option>
                                                <option value="Apelación">Apelación</option>
                                                <option value="Reposición">Reposición</option>
                                                <option value="Ambos">Ambos</option>
                                                <option value="Ninguno">Ninguno</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="col">
                                
                                    <div class="row" style="visibility: hidden">
                                        <label for="inFechaUltimoActo2" class="col-sm-4 col-form-label form-control-sm">Fecha notificación último acto</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control form-control-sm" id="inFechaUltimoActo2" name="inFechaUltimoActo">
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>  
                            
                            <!-- FILA 9 -->
                            <div class="row">
                                <div class="col">
                                    
                                    <div class="row">
                                        <label for="inResolucionApleacion2" class="col-sm-4 col-form-label form-control-sm">No. Resolución por la cual resuelve recurso de apelación</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control form-control-sm" id="inResolucionApleacion2" name="inResolucionApleacion" disabled>
                                        </div>
                                    </div>

                                </div>
                                <div class="col">
                                
                                    <div class="row" style="visibility: hidden">
                                        <label for="inFechaEjecutoria2" class="col-sm-4 col-form-label form-control-sm">Fecha ejecutoria</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control form-control-sm" id="inFechaEjecutoria2" name="inFechaEjecutoria">
                                        </div>
                                    </div>

                                </div>
                            </div>     
                            
                            <!-- FILA 10 -->
                            <div class="row">
                                <div class="col">
                                    
                                    <div class="row" >
                                        <label for="inFechaApelacion2" class="col-sm-4 col-form-label form-control-sm">Fecha apelación</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control form-control-sm" id="inFechaApelacion2" name="inFechaApelacion" disabled>
                                        </div>
                                    </div>

                                </div>
                                <div class="col">
                                
                                <!-- NO DATA -->
                                    
                                </div>
                            </div>                 
                        </div>
                        <div class="alert alert-warning" role="alert" id="dvForm2" style="display: none"></div>
                        </form>
                        <!-- FIN FORMULARIO EDICION -->  
                        


                    </div>
                    <div class="modal-footer">
                        <button id="bmEdicionClose2" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btEditar">Editar</button>
                    </div>
                    </div>
                </br></br></br>    
                </div>
            </div>
            <!--FIN MODAL EDICION -->

        </div>
        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">


            <!--INICIO TABLA CONSTANCIA GENERAL -->
            <div class="alert alert-warning" role="alert" id="dvTbConstanciaGen" style="display: none"></div>
            <form id="fmBusquedaConsta">
                <div class="input-group input-group-sm mb-3">
                    <input  id="inFuncion" name="inFuncion" type="hidden" value="32"> 
                    <input type="date" class="form-control form-control-sm" id="inFechaInicio" name="inFechaInicio">
                    <input type="date" class="form-control form-control-sm" id="inFechaFinal" name="inFechaFinal">
                         
                    <button type="button" class="btn btn-info" id="btBuscarConstan">Buscar</button>
                    <div class="spinner-grow text-info" role="status" id="spLoading2" style="display: none">
                                <span class="visually-hidden">Loading...</span>
                    </div>  
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
            </form>    


            <div class="scrollX" style="height:300px;" id="dvTableConstanciaGen" >
            <h3>Solicitudes por enviar.</h3>
            <table id="tbConstancia" class="table table-striped table-bordered table-sm align-middle"  cellspacing="0"
                width="100%">
                <thead>
                    <tr class="align-middle">
                    <th scope="col">Fecha solicitud</th>
                    <th scope="col">ID Solicitud</th>
                    <th scope="col">Item</th>
                    <th scope="col">No. Id/Pe</th>
                    <th scope="col">No. Resolución</th>
                    <th scope="col">Fecha de firma</th>
                    <th scope="col">Nit O CC</th>
                    <th scope="col">Razón social</th>
                    <th scope="col">Tipo de notificación del acto</th>
                    <th scope="col">Fecha acuse y/o notificación</th>
                    <th scope="col">Presenta recursos</th>
                    <th scope="col">Resolución por la cual se resuelve recurso de apelación</th>
                    <th scope="col">Fecha del acto de apelación</th>
                    <th scope="col">Resolución por la cual se resuelve recurso de reposición</th>
                    <th scope="col">Fecha del acto de reposición</th>
                    <th scope="col">Presenta recursos de queja o revocatoria directa</th>
                    <th scope="col">Resolución por la cual se resuelve queja o revocatoria directa</th>
                    <th scope="col">Tipo de notificación del acto final</th>
                    <th scope="col">Fecha de recurso</th>
                    <th scope="col">Expediente</th>
                    <th scope="col">Fecha notificación último acto</th>
                    <th scope="col">Fecha ejecutoria</th>
                    <th scope="col">Fecha respuesta</th>
                    <th scope="col">Constancia</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>  
            </div>
            </br>

            <!-- FIN TABLA DEVOLUCIONES --> 


        </div>
    </div>

    <div id="dvAbriVisor" style='display:none;
                position:fixed;
                padding:26px 30px 30px;
                top:0;
                left:0;
                right:0;
                bottom:0;
                z-index:2'>
                <button id="btCerrarVisor" type='button' style='float:right; background-color:red;'><b>x</b></button>
    </div>
    

 <!-- SCRIPT LOGICA -->  

 <script type="text/javascript">

    $(document).ready(function(){
        $("#dvTableConstanciaGen").css("display", "none");
        cargarSolicitudPorEnviar();
    });

    //Inicio Logica Nueva solicitud
    $( "#btBorrarForm" ).click(function() {
        resetFormulario();
    });

    $( "#btBuscar" ).click(function() {
        $("#dvBusqueda").css("display", "none");
        $("#dvForm").css("display", "none");
        $("#dvBusqueda").empty();   
        $("#dvForm").empty();  
        let noResolucion = $('#inResolucion').val();
        if(noResolucion == "") {
            $("#dvBusqueda").append("Indique un número de resolución");
            $("#dvBusqueda").css("display", "block");
        } else {
            $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: { inFuncion : '1', noResolucion : noResolucion},
						success: function(result){
                            data = jQuery.parseJSON( result );
                            if(data.length == 0){
                                document.getElementById("fmGeneral").reset();
                                document.getElementById("fmBusqueda").reset();
                                $("#dvBusqueda").append("Resolución no encontrada");
                                $("#dvBusqueda").css("display", "block");
                            } else {
                                document.getElementById("fmGeneral").reset();
                                document.getElementById("fmBusqueda").reset();
                                $("#dvBusqueda").css("display", "none");   
                                $("#dvBusqueda").empty();   
                                $('#inResolucionInicial').val(noResolucion);                       
                                $('#inResolucion').val(noResolucion);
                                $('#inFechaActo').val(data[0]);
                                $('#inNitCC').val(data[2]);
                                if(data[1] == 4)
                                    $('#seRazonSocial').val('Persona Natural');
                                else    
                                    $('#seRazonSocial').val('Persona Jurídica');
                            }
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
			});		
        }
    });    

    $( "#sePresentaRecurso" ).change(function() {
        let sePresentaRecurso = $('#sePresentaRecurso').val();
        if(sePresentaRecurso == 'Apelación') {
            $('#inResolucionApleacion').removeAttr("disabled");
            $('#inFechaApelacion').removeAttr("disabled");
            $("#inResolucionReposicion").attr("disabled", true);
            $("#inFechaReposicion").attr("disabled", true);
        } else if (sePresentaRecurso == 'Reposición'){
            $('#inResolucionReposicion').removeAttr("disabled");
            $('#inFechaReposicion').removeAttr("disabled");
            $("#inResolucionApleacion").attr("disabled", true);
            $("#inFechaApelacion").attr("disabled", true);
        }  else if (sePresentaRecurso == 'Ambos'){
            $('#inResolucionApleacion').removeAttr("disabled");
            $('#inFechaApelacion').removeAttr("disabled");
            $('#inResolucionReposicion').removeAttr("disabled");
            $('#inFechaReposicion').removeAttr("disabled");
        } else {
            $("#inResolucionApleacion").attr("disabled", true);
            $("#inFechaApelacion").attr("disabled", true);
            $("#inResolucionReposicion").attr("disabled", true);
            $("#inFechaReposicion").attr("disabled", true);
        }
    });

    $( "#seRecursoQueja" ).change(function() {
        let seRecursoQueja = $('#seRecursoQueja').val();
        if(seRecursoQueja == 'Si') {
            $('#inResolucionRecurosQueja').removeAttr("disabled");
            $('#seNotificacionFinal').removeAttr("disabled");
            $('#inFechaRecursoQueja').removeAttr("disabled");
        } else {
            $("#inResolucionRecurosQueja").attr("disabled", true);
            $("#seNotificacionFinal").attr("disabled", true);
            $("#inFechaRecursoQueja").attr("disabled", true);
        }
    });   

    $( "#btAgregar" ).click(function() {
        $("#dvBusqueda").css("display", "none");
        $("#dvForm").css("display", "none");
        $("#dvBusqueda").empty();   
        $("#dvForm").empty(); 
        
        if(validarFormulario()){
            $("#inFuncion").val() == 2;
            $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: $("#fmGeneral").serialize(),
						success: function(result){
                            if(result == "200") {
                                alert("Registrado correctamente");
                                resetFormulario();
                                cargarSolicitudPorEnviar();
                            } else {
                                alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                            }
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
			});		
        } 
    });  

    $( "#btEnviar" ).click(function() {
        $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: { inFuncion : '5'},
						success: function(result){
                            if(result == "200") {
                                alert("Solicitudes enviadas correctamente");
                                resetFormulario();
                                cargarSolicitudPorEnviar();
                            } else {
                                alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                            }
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
		});		
    });

    $( "#btBorrarPen" ).click(function() {
        if (confirm('Esta seguro que desea eliminar las peticiones pendientes por enviar?')) {
            $.ajax({url: "constanciaController.php", 
                            type: "POST",
                            data: { inFuncion : '39'},
                            success: function(result){
                                if(result == "200") {
                                    alert("Solicitudes eliminadas correctamente");
                                    resetFormulario();
                                    cargarSolicitudPorEnviar();
                                } else {
                                    alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                                }
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                            }
            });		
        }
    });


    //Funcion para enviar excel y procesalor
    function getFile(elm){
        var fd = new FormData();
        var files = $('#btSolicitudMasiva')[0].files;
        if(files.length > 0 ){
            $("#spLoading").css("visibility", "visible");
            fd.append('btSolicitudMasiva',files[0]);
            fd.append('inFuncion',3);
            $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: fd,
                        contentType: false,
                        processData: false,
						success: function(result){
                            resetFormulario();
                            $("#spLoading").css("visibility", "hidden");
                            if(result == "200") {
                                alert("Registrado correctamente");
                            } else {
                                $("#dvForm").empty();   
                                $("#dvForm").append(result);
                                $("#dvForm").css("display", "block");
                            }
                          
                            cargarSolicitudPorEnviar();
                            
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) { 
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                            resetFormulario();
				  		}
			});		
        }
    }

    function resetFormulario() {
        document.getElementById("fmGeneral").reset();
        document.getElementById("fmBusqueda").reset();
        document.getElementById("fmMasivo").reset();
        $("#dvBusqueda").css("display", "none");
        $("#dvForm").css("display", "none");
        $("#spLoading").css("visibility", "hidden");
        $("#dvBusqueda").empty();   
        $("#dvForm").empty();   
    }

    function validarFormulario(){
       
        if($("#inIdple").val() == "") {
            $("#dvForm").append("No. ID/PLE es requerido");
            $("#dvForm").css("display", "block");
            return false;
        }
        if($("#inResolucionInicial").val() == "") {
            $("#dvForm").append("No. Resolución es requerido");
            $("#dvForm").css("display", "block");
            return false;
        }  
        if($("#inFechaActo").val() == "") {
            $("#dvForm").append("Fecha de firma es requerido");
            $("#dvForm").css("display", "block");
            return false;
        }  
        if($("#inNitCC").val() == "") {
            $("#dvForm").append("Nit o C.C es requerido");
            $("#dvForm").css("display", "block");
            return false;
        } 
        if($("#seRazonSocial").val() == 0) {
            $("#dvForm").append("Razón Social es requerido");
            $("#dvForm").css("display", "block");
            return false;
        }
        if($("#seTipoNotifiacion").val() == 0) {
            $("#dvForm").append("Tipo notificación del acto es requerido");
            $("#dvForm").css("display", "block");
            return false;
        }
        if($("#inFechaAcuse").val() == "") {
            $("#dvForm").append("Fecha acuse y/o notificación es requerido");
            $("#dvForm").css("display", "block");
            return false;
        }  
        if($("#sePresentaRecurso").val() == 0) {
            $("#dvForm").append("Presenta recursos es requerido");
            $("#dvForm").css("display", "block");
            return false;
        }     
        if($("#sePresentaRecurso").val() == 'Apelación' || $("#sePresentaRecurso").val() == 'Ambos') {
            if($("#inResolucionApleacion").val() == "") {
                $("#dvForm").append("No. Resolución por la cual resuelve recurso de apelación es requerido");
                $("#dvForm").css("display", "block");
                return false;
            }            
            if($("#inFechaApelacion").val() == "") {
                $("#dvForm").append("Fecha apelación es requerido");
                $("#dvForm").css("display", "block");
                return false;
            }          
        }     
        if($("#sePresentaRecurso").val() == 'Reposición' || $("#sePresentaRecurso").val() == 'Ambos') {
            if($("#inResolucionReposicion").val() == "") {
                $("#dvForm").append("No. Resolución por la cual resuelve recurso de reposición es requerido");
                $("#dvForm").css("display", "block");
                return false;
            }            
            if($("#inFechaReposicion").val() == "") {
                $("#dvForm").append("Fecha reposición es requerido");
                $("#dvForm").css("display", "block");
                return false;
            }          
        }  
        if($("#seRecursoQueja").val() == 0) {
            $("#dvForm").append("Presenta recursos de queja revocatoria directa es requerido");
            $("#dvForm").css("display", "block");
            return false;
        } else {
            if($("#seRecursoQueja").val() == 'Si') {
                if($("#inResolucionRecurosQueja").val() == "") {
                    $("#dvForm").append("No. Resolución por la cual resuelve queja o revocatoria directa es requerido");
                    $("#dvForm").css("display", "block");
                    return false;
                }            
                if($("#seNotificacionFinal").val() == 0) {
                    $("#dvForm").append("Tipo de notificación final es requerido");
                    $("#dvForm").css("display", "block");
                    return false;
                }  
                if($("#inFechaRecursoQueja").val() == "") {
                    $("#dvForm").append("Fecha de recursos es requerido");
                    $("#dvForm").css("display", "block");
                    return false;
                }                 
            }
        }  
        if($("#inExpediente").val() == "") {
            $("#dvForm").append("Expediente es requerido");
            $("#dvForm").css("display", "block");
            return false;
        } else {
            let strUrl = false;
            $.ajax({url: "constanciaController.php", 
					    type: "POST",
                        async: false,
						data: {inFuncion : '37', resolucion : $("#inResolucionInicial").val(), inExpediente : $("#inExpediente").val()},
						success: function(result){
                            var data = jQuery.parseJSON(result);
                            $.each(data, function(index, value) {
                                if(index == 'ok') {
                                    strUrl = true;
                                } else {
                                    $("#dvForm").append(value);
                                    $("#dvForm").css("display", "block");
                                }
                            });
                            
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) { 
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
			});	
            return strUrl;

        }   

        return true;
    }


    function cargarSolicitudPorEnviar() {
        $("#tbHorizontal").find("tr:gt(0)").remove();
        $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: { inFuncion : '4'},
						success: function(result){
                            data = jQuery.parseJSON( result );
                            if(data.length > 0) {
                                $("#dvTableHorizontal").css("display", "block");
                                $("#btEnviar").css("display", "block");
                                $("#btBorrarPen").css("display", "block");
                                for(let i=0; i<data.length; i++) {
                                    let infoRow = '<tr>';
                                    for(let j=0; j<data[i].length; j++) {
                                            infoRow += '<td>' + data[i][j] + '</td>';
                                    }
                                    infoRow += '</tr>';    
                                    $('#tbHorizontal tr:last').after(infoRow);
                                }
                            } else {
                                $("#dvTableHorizontal").css("display", "none");
                                $("#btEnviar").css("display", "none");
                                $("#btBorrarPen").css("display", "none");
                            }
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
		});	  
       
    }

   

    function resetFormulario2() {
        document.getElementById("fmGeneral2").reset();
        $("#dvForm2").css("display", "none");
        $("#dvForm2").empty();   
    }

    $( "#btEditar" ).click(function() {
        $("#dvForm2").css("display", "none");  
        $("#dvForm2").empty(); 
        
        if(validarFormulario2()){
            $("#inFuncion2").val() == 9;
            $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: $("#fmGeneral2").serialize(),
						success: function(result){
                            if(result == "200") {
                                alert("Registro editado correctamente");
                                resetFormulario2();
                                cargarSolicitudDevuelta();
                                $('#mEdicion').modal('hide');
                            } else {
                                alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                            }
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
			});		

        }
    });    

    function validarFormulario2(){
       
       if($("#inIdple2").val() == "") {
           $("#dvForm2").append("No. ID/PLE es requerido");
           $("#dvForm2").css("display", "block");
           return false;
       }
       if($("#inResolucionInicial2").val() == "") {
           $("#dvForm2").append("No. Resolución es requerido");
           $("#dvForm2").css("display", "block");
           return false;
       }  
       if($("#inFechaActo2").val() == "") {
           $("#dvForm2").append("Fecha de firma es requerido");
           $("#dvForm2").css("display", "block");
           return false;
       }  
       if($("#inNitCC2").val() == "") {
           $("#dvForm2").append("Nit o C.C es requerido");
           $("#dvForm2").css("display", "block");
           return false;
       } 
       if($("#seRazonSocial2").val() == 0) {
           $("#dvForm2").append("Razón Social es requerido");
           $("#dvForm2").css("display", "block");
           return false;
       }
       if($("#seTipoNotifiacion2").val() == 0) {
           $("#dvForm2").append("Tipo notificación del acto es requerido");
           $("#dvForm2").css("display", "block");
           return false;
       }
       if($("#inFechaAcuse2").val() == "") {
           $("#dvForm2").append("Fecha acuse y/o notificación es requerido");
           $("#dvForm2").css("display", "block");
           return false;
       }  
       if($("#sePresentaRecurso2").val() == 0) {
           $("#dvForm2").append("Presenta recursos es requerido");
           $("#dvForm2").css("display", "block");
           return false;
       }     
       if($("#sePresentaRecurso2").val() == 'Apelación' || $("#sePresentaRecurso2").val() == 'Ambos') {
           if($("#inResolucionApleacion2").val() == "") {
               $("#dvForm2").append("No. Resolución por la cual resuelve recurso de apelación es requerido");
               $("#dvForm2").css("display", "block");
               return false;
           }            
           if($("#inFechaApelacion2").val() == "") {
               $("#dvForm2").append("Fecha apelación es requerido");
               $("#dvForm2").css("display", "block");
               return false;
           }          
       }     
       if($("#sePresentaRecurso2").val() == 'Reposición' || $("#sePresentaRecurso2").val() == 'Ambos') {
           if($("#inResolucionReposicion2").val() == "") {
               $("#dvForm2").append("No. Resolución por la cual resuelve recurso de reposición es requerido");
               $("#dvForm2").css("display", "block");
               return false;
           }            
           if($("#inFechaReposicion2").val() == "") {
               $("#dvForm2").append("Fecha reposición es requerido");
               $("#dvForm2").css("display", "block");
               return false;
           }          
       }  
       if($("#seRecursoQueja2").val() == 0) {
           $("#dvForm2").append("Presenta recursos de queja revocatoria directa es requerido");
           $("#dvForm2").css("display", "block");
           return false;
       } else {
           if($("#seRecursoQueja2").val() == 'Si') {
               if($("#inResolucionRecurosQueja2").val() == "") {
                   $("#dvForm2").append("No. Resolución por la cual resuelve queja o revocatoria directa es requerido");
                   $("#dvForm2").css("display", "block");
                   return false;
               }            
               if($("#seNotificacionFinal2").val() == 0) {
                   $("#dvForm2").append("Tipo de notificación final es requerido");
                   $("#dvForm2").css("display", "block");
                   return false;
               }  
               if($("#inFechaRecursoQueja2").val() == "") {
                   $("#dvForm2").append("Fecha de recursos es requerido");
                   $("#dvForm2").css("display", "block");
                   return false;
               }                 
           }
       }  
       if($("#inExpediente2").val() == "") {
           $("#dvForm2").append("Expediente es requerido");
           $("#dvForm2").css("display", "block");
           return false;
       }           
       return true;
   }    

   $( "#sePresentaRecurso2" ).change(function() {
        PresentaRecurso2();
    });

    $( "#seRecursoQueja2" ).change(function() {
        changeRecursoQueja2();
    }); 

    function PresentaRecurso2() {
        let sePresentaRecurso = $('#sePresentaRecurso2').val();
        if(sePresentaRecurso == 'Apelación') {
            $('#inResolucionApleacion2').removeAttr("disabled");
            $('#inFechaApelacion2').removeAttr("disabled");
            $("#inResolucionReposicion2").attr("disabled", true);
            $("#inFechaReposicion2").attr("disabled", true);
        } else if (sePresentaRecurso == 'Reposición'){
            $('#inResolucionReposicion2').removeAttr("disabled");
            $('#inFechaReposicion2').removeAttr("disabled");
            $("#inResolucionApleacion2").attr("disabled", true);
            $("#inFechaApelacion2").attr("disabled", true);
        }  else if (sePresentaRecurso == 'Ambos'){
            $('#inResolucionApleacion2').removeAttr("disabled");
            $('#inFechaApelacion2').removeAttr("disabled");
            $('#inResolucionReposicion2').removeAttr("disabled");
            $('#inFechaReposicion2').removeAttr("disabled");
        } else {
            $("#inResolucionApleacion2").attr("disabled", true);
            $("#inFechaApelacion2").attr("disabled", true);
            $("#inResolucionReposicion2").attr("disabled", true);
            $("#inFechaReposicion2").attr("disabled", true);
        }
    }   
 
    function changeRecursoQueja2(){
        let seRecursoQueja = $('#seRecursoQueja2').val();
        if(seRecursoQueja == 'Si') {
            $('#inResolucionRecurosQueja2').removeAttr("disabled");
            $('#seNotificacionFinal2').removeAttr("disabled");
            $('#inFechaRecursoQueja2').removeAttr("disabled");
        } else {
            $("#inResolucionRecurosQueja2").attr("disabled", true);
            $("#seNotificacionFinal2").attr("disabled", true);
            $("#inFechaRecursoQueja2").attr("disabled", true);
        }
    }


    $( "#pills-profile-tab" ).click(function() {
        cargarSolicitudDevuelta();
    }); 

    $( "#pills-contact-tab" ).click(function() {
        //cargarConstanciaFinalizada();
    });     

    $(document).on('click', '.cbEdit', function() {
        resetFormulario2();
        let idSolicitud = this.id.replace("cb", "");
        $('#idSolicitudEdit').val(idSolicitud);
        $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: {inFuncion : '8', idSolicitud : idSolicitud},
						success: function(result){
                            data = jQuery.parseJSON( result );
                            if(data.length > 0){

                                $('#inIdple2').val(data[0]);        
                                $('#inResolucionInicial2').val(data[1]);

                                if(data[2] != "") {
                                    const inFechaActo2 = data[2].split("/");
                                    document.getElementById("inFechaActo2").value = inFechaActo2[2] + '-' + inFechaActo2[1] + '-' + inFechaActo2[0];
                                }
                                if(data[6] != "") {
                                    const inFechaAcuse2 = data[6].split("/");
                                    document.getElementById("inFechaAcuse2").value = inFechaAcuse2[2] + '-' + inFechaAcuse2[1] + '-' + inFechaAcuse2[0];
                                } 
                                if(data[9] != "") {
                                    const inFechaApelacion2 = data[9].split("/");
                                    document.getElementById("inFechaApelacion2").value = inFechaApelacion2[2] + '-' + inFechaApelacion2[1] + '-' + inFechaApelacion2[0];
                                }      
                                if(data[11] != "") {
                                    const inFechaReposicion2 = data[11].split("/");
                                    document.getElementById("inFechaReposicion2").value = inFechaReposicion2[2] + '-' + inFechaReposicion2[1] + '-' + inFechaReposicion2[0];
                                }   
                                if(data[15] != "") {
                                    const inFechaRecursoQueja2 = data[15].split("/");
                                    document.getElementById("inFechaRecursoQueja2").value = inFechaRecursoQueja2[2] + '-' + inFechaRecursoQueja2[1] + '-' + inFechaRecursoQueja2[0];
                                }                                                                                                                            
                                
                                $('#inNitCC2').val(data[3]);
                                $('#seRazonSocial2').val(data[4]);
                                $('#seTipoNotifiacion2').val(data[5]);
                                $('#sePresentaRecurso2').val(data[7]);
                                $('#inResolucionApleacion2').val(data[8]);
                                $('#inResolucionReposicion2').val(data[10]);
                                $('#seRecursoQueja2').val(data[12]);
                                $('#inResolucionRecurosQueja2').val(data[13]);
                                $('#seNotificacionFinal2').val(data[14]);
                                $('#inExpediente2').val(data[16]);
                                PresentaRecurso2();
                                changeRecursoQueja2();
                                $('#mEdicion').modal('show');
                            } else {
                                alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                            }
                            
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) { 
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
			});	
    }); 

    $( "#bmEdicionClose" ).click(function() {
        cargarSolicitudDevuelta();
        $('#mEdicion').modal('hide');
    }); 

    $( "#bmEdicionClose2" ).click(function() {
        cargarSolicitudDevuelta();
        $('#mEdicion').modal('hide');
    }); 
    

    $(document).on('click', '.cbDelete', function() {
        let idSolicitud = this.id.replace("cb", "");
        if (confirm('Esta seguro que desea eliminar esta solicitud ?')) {
            $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: {inFuncion : '7', idSolicitud : idSolicitud},
						success: function(result){
                            if(result == "200") {
                                alert("Solicitud eliminada correctamnte.");
                                cargarSolicitudDevuelta();
                            } else {
                                alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                            }
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) { 
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
			});		
        }
    }); 

    function cargarSolicitudDevuelta(){
        $("#dvTbDevolucion").empty();  
        $("#dvTbDevolucion").append("");
        $("#dvTbDevolucion").css("display", "none");
        $("#tbDevolucion").find("tr:gt(0)").remove();
        $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: { inFuncion : '6'},
						success: function(result){
                            data = jQuery.parseJSON( result );
                            if(data.length > 0) {
                                $("#dvTableDevolucion").css("display", "block");
                                $("#btRetonarDevolucion").css("display", "block");
                                for(let i=0; i<data.length; i++) {
                                    let infoRow = '<tr>';
                                    let idRow;
                                    for(let j=0; j<data[i].length; j++) {

                                            if(j == 0) {
                                                idRow = data[i][j];
                                                infoRow += '<td><input class="form-check-input" type="checkbox" name="cbSolicitud" value="'+ idRow +'"></td>';
                                            }else if(j == 1) {
                                                if(data[i][j] == 'f')
                                                    infoRow += '<td><span class="circleGreen"></span></td>';
                                                else
                                                    infoRow += '<td><span class="circleBlue"></span></td>';   
                                            } else 
                                                infoRow += '<td>' + data[i][j] + '</td>';
                                    }
                                    infoRow += '<td>';
                                    infoRow += '<i id="cb' + idRow + '" name="cb' + idRow + '" class="bi-pencil cbEdit" style="font-size: 1.5rem; color: cornflowerblue; cursor: pointer;"></i>&nbsp;&nbsp;&nbsp;';
                                    infoRow += '<i id="cb' + idRow + '" name="cb' + idRow + '" class="bi-trash cbDelete" style="font-size: 1.5rem; color: cornflowerblue; cursor: pointer;"></i>';
                                    infoRow += '</td>';
                                    infoRow += '</tr>';    
                                    $('#tbDevolucion tr:last').after(infoRow);
                                }
                            } else {
                                $("#dvTableDevolucion").css("display", "none");
                                $("#btRetonarDevolucion").css("display", "none");
                                $("#dvTbDevolucion").append("No se registran devoluciones");
                                $("#dvTbDevolucion").css("display", "block");
                            }
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
		});	 
    }


    $( "#cbSolicitudGen" ).click(function() {
        if($('#cbSolicitudGen').is(':checked')){
            $("input:checkbox[name=cbSolicitud]").each(function(){
                $(this).prop('checked', true);
            });
        } else {
            $("input:checkbox[name=cbSolicitud]").each(function(){
                $(this).prop('checked', false);
            });  
        }
    }); 
    
 
    $( "#btRetonarDevolucion" ).click(function() {
        let cantidadSeleccionada = 0;
        let arraySolicitud = [];
        $("input:checkbox[name=cbSolicitud]:checked").each(function(){
            arraySolicitud.push($(this).val());
            cantidadSeleccionada++;
        });

        if(cantidadSeleccionada == 0) {
            alert("Debe seleccionar al menos una solicitud para envair.");
        } else {
            if (confirm('Esta seguro que enviar los solicitudes ?')) {
                $.ajax({url: "constanciaController.php", 
                            type: "POST",
                            data: {inFuncion : '10', arraySolicitud : arraySolicitud},
                            success: function(result){
                                if(result == "200") {
                                    alert("Envio correcto.");
                                    cargarSolicitudDevuelta();
                                } else {
                                    alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                                }
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) { 
                                alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                            }
                });		
            }
        }
    });
    //Fin Logica Tab Devoluciones


    $(document).on('click', '.cbDescarga', function() {
            let idSolicitud = this.id.replace("cbDes", "");
            $.ajax({url: "constanciaController.php", 
                            type: "POST",
                            data: {inFuncion : '19', idSolicitud : idSolicitud},
                            success: function(result){
                                $('#dvAbriVisor').empty();
                                $("#dvAbriVisor").append("<button id='btCerrarVisor' type='button' style='float:right; background-color:red;'><b>x</b></button><iframe style='width:100%; height:89vh; z-index:-2;' src='.." + result + "'></iframe>");
                                $("#dvAbriVisor").css("display", "block");
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                            }
            });    
    });

    $(document).on('click', '#btCerrarVisor', function() {
        $("#dvAbriVisor").css("display", "none");
    });


    $( "#btBuscarConstan" ).click(function() {

        $("#dvTbConstanciaGen").empty();  
        $("#dvTbConstanciaGen").append("");
        $("#dvTbConstanciaGen").css("display", "none");

        if($("#inFechaInicio").val() != "" && $("#inFechaFinal").val() !="") {
            let parts = $("#inFechaInicio").val().split('-');
            let dtFechaInicio = new Date(parts[0], parts[1] - 1, parts[2]);
            parts = $("#inFechaFinal").val().split('-');
            let dtFechaFinal = new Date(parts[0], parts[1] - 1, parts[2]);
            if(dtFechaInicio.getTime() > dtFechaFinal.getTime()) {
                $("#dvTbConstanciaGen").append("Fecha de inicio debe ser menor o igual a la fecha final.");
                $("#dvTbConstanciaGen").css("display", "block");
                return;
            } else {
                cargarConstanciaFinalizada();
            }
        } else {
                $("#dvTbConstanciaGen").append("Debe ingresar fecha de inicio y fecha final.");
                $("#dvTbConstanciaGen").css("display", "block");
                return;
        }

    });

    function cargarConstanciaFinalizada(){
        $("#spLoading2").css("visibility", "visible");
        $("#tbConstancia").find("tr:gt(0)").remove();
        $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: $("#fmBusquedaConsta").serialize(),
						success: function(result){
                            data = jQuery.parseJSON( result );
                            if(data.length > 0) {
                                $("#dvTableConstanciaGen").css("display", "block");
                                let idRow;
                                for(let i=0; i<data.length; i++) {
                                    let infoRow = '<tr>';
                                    for(let j=0; j<data[i].length; j++) {
                                            
                                            if(j == 0) {
                                                idRow = data[i][j];
                                            }else if(j == 24) {
                                                if(data[i][j] != "") {
                                                    infoRow += '<td><i id="cbDes' + idRow + '"  class="bi-file-earmark-pdf-fill cbDescarga" style="font-size: 1.5rem; color: FF0000; cursor: pointer;"></i></td>';
                                                } else {
                                                    infoRow += '<td></td>';
                                                }
                                            } else
                                                infoRow += '<td>' + data[i][j] + '</td>';
                                    }
                                    infoRow += '</tr>';    
                                    $('#tbConstancia tr:last').after(infoRow);
                                }
                            } else {
                                $("#dvTableConstanciaGen").css("display", "none");
                                $("#dvTbConstanciaGen").append("No se registran constancias");
                                $("#dvTbConstanciaGen").css("display", "block");
                            }
                            $("#spLoading2").css("visibility", "hidden");
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                            $("#spLoading2").css("visibility", "hidden");
				  		}
		});	 
    }    

    
</script>    

 <!-- FIN SCRIPT LOGICA -->  

</body>
</html>   