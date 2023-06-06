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

    <h3 class="mt-2">Rol Firmante</h3>
    <div class="alert alert-warning" role="alert" id="dvGeneral" style="display: none"></div>
    <form id="fmBusqueda">
        <div class="input-group input-group-sm mb-3">
             <input  id="inFuncion" name="inFuncion" type="hidden" value="29"> 
             <input  id="estadoDife" name="estadoDife" type="hidden" value="4"> 
            <input type="date" class="form-control form-control-sm" id="inFechaInicio" name="inFechaInicio">
            <input type="date" class="form-control form-control-sm" id="inFechaFinal" name="inFechaFinal">
            <select class="form-select form-select-sm" id="seDependencia" name="seDependencia">
                        
            </select>
            <select class="form-select form-select-sm" id="seGrupo" name="seGrupo">
                
            </select>     
            <button type="button" class="btn btn-info" id="btBuscar">Buscar</button>
            <div class="spinner-grow text-info" role="status" id="spLoading" style="display: none">
                        <span class="visually-hidden">Loading...</span>
            </div>  
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
    </form>    

    <ul class="nav  nav-tabs mb-3" id="proyecor-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Solicitudes</a>
    </li>
    </ul>
    <div class="tab-content borderNav" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <!-- TAB SOLICITUDES -->
            &nbsp;&nbsp;           
            <i class="bi-vector-pen" id="cbFirmaGeneral" style="font-size: 1.5rem; color: cornflowerblue; cursor: pointer;"></i>&nbsp;&nbsp;
            <i class="bi-arrow-return-left" id="cbDevolucionGeneral" style="font-size: 1.5rem; color: cornflowerblue; cursor: pointer;"></i>&nbsp;&nbsp;
            <i class="bi-file-earmark-pdf-fill" id="cbGeneracionGeneral" style="font-size: 1.5rem; color: cornflowerblue; cursor: pointer;"></i>&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

            <div class="scrollX" style="height:300px;"  >
                <table id="tbSolicitud" class="table table-striped table-bordered table-sm align-middle"  cellspacing="0"
                    width="100%" style="display: none">
                    <thead>
                        <tr class="align-middle">
                        <th scope="col"><input class="form-check-input" type="checkbox" value="" id="cbSolicitudGen"></th>
                        <th scope="col">Alerta</th>
                        <th scope="col">Fecha solicitud</th>
                        <th scope="col">ID Solicitud</th>
                        <th scope="col">Item</th>
                        <th scope="col">Dependencia</th>
                        <th scope="col">Usuario</th>
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
                        <th scope="col">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acciones&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                        <th scope="col">Constancia</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>  
            </div>    

            </br></br></br>
            <!-- FIN TAB SOLICITUDES -->
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


    <!--MODAL DE EDICION -->

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
            <input  name="inFuncion" id="inFuncion2" type="hidden" value="14">        
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
                    
                        <div class="row">
                            <label for="inFechaUltimoActo2" class="col-sm-4 col-form-label form-control-sm">Fecha notificación último acto*</label>
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
                    
                        <div class="row">
                            <label for="inFechaEjecutoria2" class="col-sm-4 col-form-label form-control-sm">Fecha ejecutoria*</label>
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
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        </div>
    </br></br></br>    
    </div>
</div>
</br></br>

    <!--FIN MODAL DE EDICION -->

    <!--MODAL DE DEVOLUCION -->

    <div class="modal" tabindex="-1" id="mDevolucion">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Devolución constancia ejecutória</h5>
                    <button id="bmDevolucionClose" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                        <div id="dvDevolucionIndo"></div>
                        <div class="mb-3">
                            <label for="taComentarioDev" class="form-label">Comentario:</label>
                            <textarea class="form-control" id="taComentarioDev" rows="3"></textarea>
                        </div>

                </div>
                <div class="modal-footer">
                    <button id="bmDevolucionClose2" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btDevolver">Devolver</button>
                </div>
            </div>

        </div>
    </div>

    <!--FIN MODAL DE DEVOLUCION -->  

    <!--MODAL DE HISTORICO -->

    <div class="modal  fade bd-example-modal-lg" tabindex="-1" id="mHistorico">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Histórico</h5>
                    <button id="bmHistoricoClose" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">


                <div class="scrollX" style="height:300px;" >
                <table id="tbHistorio" class="table table-striped table-bordered table-sm align-middle"  cellspacing="0"
                    width="100%">
                    <thead>
                        <tr class="align-middle">
                            <th scope="col">Fecha</th>
                            <th scope="col">Operación </th>
                            <th scope="col">Usuario</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>  
            </div>                    
                       
                </div>
                <div class="modal-footer">
                    <button id="bmEnvioClose3" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>

        </div>
    </div>

    <!--FIN MODAL DE HISTORICO -->      
       

<!-- SCRIPT LOGICA -->  
<script type="text/javascript">

    var tab;
    var arraySolicitudGen;

    $(document).ready(function(){
        mostrarLoading();
        tab = 1;
        cargarDependenciaFiltro(4);
        cargarGrupoFiltro(4);
        ocultarLoading();
    });

    function mostrarLoading(){
        $("#spLoading").css("display", "block");
    }

    function ocultarLoading(){
        $("#spLoading").css("display", "none");
    }

    $( "#pills-home-tab" ).click(function() { 
        mostrarLoading();
        tab = 1;
        cargarDependenciaFiltro(4);
        cargarGrupoFiltro(4);
        $("#inFuncion").val(13);
        limpiarDivInfo();
        ocultarLoading();
    });

    function limpiarDivInfo(){
        $("#dvGeneral").empty(); 
        $("#dvGeneral").css("display", "none");
    }

    function divInfo(info){
        $("#dvGeneral").empty(); 
        $("#dvGeneral").append(info);
        $("#dvGeneral").css("display", "block");
    }

    $( "#btBuscar" ).click(function() { 
        if(tab == 1)
            cargarSolicitud();
    });   

    function cargarSolicitud(){
        limpiarDivInfo();
        if($("#inFechaInicio").val() != "" && $("#inFechaFinal").val() !="") {
            let parts = $("#inFechaInicio").val().split('-');
            let dtFechaInicio = new Date(parts[0], parts[1] - 1, parts[2]);
            parts = $("#inFechaFinal").val().split('-');
            let dtFechaFinal = new Date(parts[0], parts[1] - 1, parts[2]);
            if(dtFechaInicio.getTime() > dtFechaFinal.getTime()) {
                divInfo("Fecha de inicio debe ser menor o igual a la fecha final.");  
                return;
            }
        }
        mostrarLoading();
        $("#tbSolicitud").find("tr:gt(0)").remove();
        $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: $("#fmBusqueda").serialize(),
						success: function(result){
                            ocultarLoading();
                            data = jQuery.parseJSON(result);
                            if(data.length == 0){
                                $("#tbSolicitud").css("display", "none"); 
                                divInfo("No hay solicitudes para el criterio de búsqueda.");  
                            } else {
                                $("#tbSolicitud").css("display", "block"); 
                                for(let i=0; i<data.length; i++) {
                                    let infoRow = "<tr>";
                                    let idRow;
                                    for(let j=0; j<data[i].length; j++) {
                                            if(j == 27)
                                                break;
                                            
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
                                    infoRow += '<i id="cbHis' + idRow + '" name="cb' + idRow + '" class="bi-clock-history cbHistorial" style="font-size: 1.5rem; color: cornflowerblue; cursor: pointer;"></i>';
                                    infoRow += '<i id="cbEdi' + idRow + '" name="cb' + idRow + '" class="bi-pencil cbEdicion" style="font-size: 1.5rem; color: cornflowerblue; cursor: pointer;"></i>';
                                    infoRow += '<i id="cbFir' + idRow + '" name="cb' + idRow + '" class="bi-vector-pen cbFirma" style="font-size: 1.5rem; color: cornflowerblue; cursor: pointer;"></i>';
                                    
                                    infoRow += '<i id="cbDev' + idRow + '" name="cb' + idRow + '" class="bi-arrow-return-left cbDevolucion" style="font-size: 1.5rem; color: cornflowerblue; cursor: pointer;"></i>';
                                    if(data[i][25] != "" && data[i][26] != "")
                                        infoRow += '<i id="cbGen' + idRow + '" name="cb' + idRow + '" class="bi-file-earmark-pdf-fill cbGeneracion" style="font-size: 1.5rem; color: cornflowerblue; cursor: pointer;"></i>';

                                    infoRow += '</td>';
                                    if(data[i][27] != "") {
                                        infoRow += '<td><i id="cbDes' + idRow + '"  class="bi-file-earmark-pdf-fill cbDescarga" style="font-size: 1.5rem; color: FF0000; cursor: pointer;"></i></td>';
                                    }
                                    else                      
                                        infoRow += '<td></td>';
                                    infoRow += '</tr>';    
                                    $('#tbSolicitud tr:last').after(infoRow);
                                }
                            }
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
                            ocultarLoading();
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
		});	
    }

      

    function cargarDependenciaFiltro(estado){
        $('#seDependencia').empty();
        $('#seDependencia').append('<option value="0" selected>Seleccione una dependencia</option>');
        $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: { inFuncion : '11', estado : estado},
						success: function(result){
                            data = jQuery.parseJSON(result);
                            for(let i=0; i<data.length; i=i+2) {
                                $('#seDependencia').append('<option value="' + data[i] +'">' + data[i+1] +'</option>');
                            }
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
		});	
    }

    function cargarGrupoFiltro(estado){
        $('#seGrupo').empty();
        $('#seGrupo').append('<option value="0" selected>Seleccione un ID</option>');
        $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: { inFuncion : '12', estado : estado},
						success: function(result){
                            data = jQuery.parseJSON(result);
                            for(let i=0; i<data.length; i++) {
                                $('#seGrupo').append('<option value="' + data[i] +'">' + data[i] +'</option>');
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

    $(document).on('click', '.cbEdicion', function() {
        resetFormulario2();
        let idSolicitud = this.id.replace("cbEdi", "");
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
                                if(data[17] != "") {
                                    const inFechaUltimoActo2 = data[17].split("/");
                                    document.getElementById("inFechaUltimoActo2").value = inFechaUltimoActo2[2] + '-' + inFechaUltimoActo2[1] + '-' + inFechaUltimoActo2[0];
                                }
                                if(data[18] != "") {
                                    const inFechaEjecutoria2 = data[18].split("/");
                                    document.getElementById("inFechaEjecutoria2").value = inFechaEjecutoria2[2] + '-' + inFechaEjecutoria2[1] + '-' + inFechaEjecutoria2[0];
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

    $( "#bmEdicionClose" ).click(function() {
        cargarSolicitud();
        $('#mEdicion').modal('hide');
    }); 

    $( "#bmEdicionClose2" ).click(function() {
        cargarSolicitud();
        $('#mEdicion').modal('hide');
    }); 

    $( "#bmDevolucionClose" ).click(function() {
        $('#mDevolucion').modal('hide');
    }); 

    $( "#bmDevolucionClose2" ).click(function() {
        $('#mDevolucion').modal('hide');
    });  



    $( "#btEditar" ).click(function() {
        $("#dvForm2").css("display", "none");  
        $("#dvForm2").empty(); 
        mostrarLoading();
        if(validarFormulario2()){
            $("#inFuncion2").val(14);
            $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: $("#fmGeneral2").serialize(),
						success: function(result){
                            if(result == "200") {

                                $.ajax({url: "constanciaController.php", 
                                    type: "POST",
                                    data: {inFuncion : '18', idSolicitud : $('#idSolicitudEdit').val()},
                                    success: function(result){
                                        if(result == "200") {
                                            alert("Registro editado correctamente");
                                            resetFormulario2();
                                            cargarSolicitud();
                                            $('#mEdicion').modal('hide');
                                        } else {
                                            alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                                        }
                                        ocultarLoading();
                                    },
                                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                                        alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                                        ocultarLoading();
                                    }
                                });	 

                                
                            } else {
                                $("#dvForm2").append(result);
                                $("#dvForm2").css("display", "block");
                                ocultarLoading();
                            }
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                            ocultarLoading();
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
       if($("#inFechaUltimoActo2").val() == "") {
               $("#dvForm2").append("Fecha notificación último acto es requerida");
               $("#dvForm2").css("display", "block");
               return false;
       }
       if($("#inFechaEjecutoria2").val() == "") {
               $("#dvForm2").append("Fecha ejecutoria es requerida");
               $("#dvForm2").css("display", "block");
               return false;
       }                            
       return true;
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


    $( "#cbGeneracionGeneral" ).click(function() {
        limpiarDivInfo();
        let cantidadSeleccionada = 0;
        let arraySolicitud = [];
        $("input:checkbox[name=cbSolicitud]:checked").each(function(){
            arraySolicitud.push($(this).val());
            cantidadSeleccionada++;
        });

        if(cantidadSeleccionada == 0){
            divInfo("Debe seleccionar al menos una soliciutd.");
        } else{
            if(cantidadSeleccionada >27) {
                divInfo("Excede la cantidad máxima.");
            } else {
                generacionGeneral(arraySolicitud);
            }
        }        
    });    

    function generacionGeneral(arraySolicitud){
        limpiarDivInfo();
        $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: {inFuncion : '27', arraySolicitud : arraySolicitud},
						success: function(result){
                            if(result == 'f') {
                                divInfo("No se puede realizar la generación ya que no pertenecen al mismo ID.");
                            } else if(result == 'f2') {
                                divInfo("No se puede puede realizar la generación ya que algunas solicitudes no tienen la Fecha notificación último acto y/o Fecha ejecutoria.");
                            } else {
                                if (confirm('Esta seguro de realizar la generación(es) de la constancia ejecutoria  ?')) {
                                    
                                    mostrarLoading();
                                    $.ajax({url: "constanciaController.php", 
                                                    type: "POST",
                                                    data: {inFuncion : '22', arraySolicitud : arraySolicitud},
                                                    success: function(result){
                                                        if(result == "200") {
                                                            alert("Documento(s) generado correctamente.");
                                                            cargarSolicitud();
                                                        } else {
                                                            alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                                                        }
                                                        ocultarLoading();
                                                    },
                                                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                                                        alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                                                        ocultarLoading();
                                                    }
                                        });	 

                                }
                            }
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                              ocultarLoading();
				  		}
		});	        
    }     


    $( "#cbDevolucionGeneral" ).click(function() {
        limpiarDivInfo();
        let cantidadSeleccionada = 0;
        let arraySolicitud = [];
        $("input:checkbox[name=cbSolicitud]:checked").each(function(){
            arraySolicitud.push($(this).val());
            cantidadSeleccionada++;
        });

        if(cantidadSeleccionada == 0){
            divInfo("Debe seleccionar al menos una soliciutd.");
        } else{
            retonarSolicitud(arraySolicitud);
        }
    });     

    $(document).on('click', '.cbDevolucion', function() {
        let idSolicitud = this.id.replace("cbDev", "");
        let arraySolicitud = [];
        arraySolicitud.push(idSolicitud);
        retonarSolicitud(arraySolicitud);
    });      

    function retonarSolicitud(arraySolicitud){
        arraySolicitudGen = arraySolicitud;
        limpiarDivInfo();
        $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: {inFuncion : '26', arraySolicitud : arraySolicitud},
						success: function(result){
                            if(result == 'f') {
                                divInfo("No se pueden procesar la devoluciones ya que las solicitudes no pertenecen al mismo ID.");
                            } else {
                                let parts = result.split('-');
                                $('#taComentarioDev').val('');
                                $("#dvDevolucionIndo").empty(); 
                                $("#dvDevolucionIndo").append('ID: ' + parts[0] + '</br>');
                                $("#dvDevolucionIndo").append('Dependencia: ' + parts[1] + '</br>');
                                $("#dvDevolucionIndo").append('Usuario: ' + parts[2] + '</br>');
                                $('#mDevolucion').modal('show');
                            }
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
		});	        
    }

    $( "#btDevolver" ).click(function() {
        limpiarDivInfo();
        if($('#taComentarioDev').val() == ""){
            alert("Comentario es requerido.");
        } else if($('#taComentarioDev').val().length > 199) {
            alert("Comentario no debe tener mas de 200 caracteres.");
        } else{
            $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: {inFuncion : '30', arraySolicitud : arraySolicitudGen, comentario : $('#taComentarioDev').val()},
						success: function(result){
                            alert("Devoluciones realizadas correctamente");
                            cargarSolicitud();
                            $('#mDevolucion').modal('hide');
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
		    });	      
        }
    });

    $( "#cbFirmaGeneral" ).click(function() {
        let cantidadSeleccionada = 0;
        let arraySolicitud = [];
        $("input:checkbox[name=cbSolicitud]:checked").each(function(){
            arraySolicitud.push($(this).val());
            cantidadSeleccionada++;
        });
        
        if(cantidadSeleccionada == 0){
            divInfo("Debe seleccionar al menos una soliciutd.");
        } else{
            if(cantidadSeleccionada > 10) {
                divInfo("Excede la cantidad máxima de 10 para firma.");
            } else {
                envairFirma(arraySolicitud);
            }
        }
    });

    $(document).on('click', '.cbFirma', function() {
        let idSolicitud = this.id.replace("cbFir", "");
        let arraySolicitud = [];
        arraySolicitud.push(idSolicitud);
        envairFirma(arraySolicitud);
    });


    function envairFirma(arraySolicitud){
        limpiarDivInfo();
        $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: {inFuncion : '15', arraySolicitud : arraySolicitud},
						success: function(result){
                            if(result == 'f') {
                                divInfo("No se pueden procesar el envio(s) ya que algunas solicitudes no pertenecen al mismo ID.");
                            } else if(result == 'f2'){
                                divInfo("No se pueden procesar el envio(s) ya que algunas solicitudes no tienen la Fecha notificación último acto y/o Fecha ejecutoria.");
                            } else if(result == 'f4'){
                                divInfo("No se pueden procesar el envio(s) ya que algunas solicitudes no se les ha generado la constancia en PDF.");
                            } else {

                                    if (confirm('Esta seguro de realizar la firma de o el ID de constancias ejecutorias  ?')) {
                                        mostrarLoading();
                                        $.ajax({url: "constanciaController.php", 
                                                        type: "POST",
                                                        data: {inFuncion : '31', arraySolicitud : arraySolicitud},
                                                        success: function(result){
                                                        if(result == "200") {
                                                                alert("Documentos firmados y enviados correctamente");
                                                                cargarSolicitud();
                                                        } else{
                                                                alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                                                        }
                                                        ocultarLoading();
                                                        },
                                                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                                                            alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                                                            ocultarLoading();
                                                        }
                                        });	  
                                    }
                            }
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                            ocultarLoading();
				  		}
		});	      
    }

    $(document).on('click', '.cbGeneracion', function() {
        if (confirm('Esta seguro de realizar la generación de la constancia ejecutoria  ?')) {
            mostrarLoading();
            let idSolicitud = this.id.replace("cbGen", "");
            $.ajax({url: "constanciaController.php", 
                            type: "POST",
                            data: {inFuncion : '18', idSolicitud : idSolicitud},
                            success: function(result){
                                if(result == "200") {
                                    alert("Documento generado correctamente.");
                                    cargarSolicitud();
                                } else {
                                    alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                                }
                                ocultarLoading();
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                                ocultarLoading();
                            }
                });	 
        }   
    });

    $(document).on('click', '.cbDescarga', function() {
            mostrarLoading();
            let idSolicitud = this.id.replace("cbDes", "");
            $.ajax({url: "constanciaController.php", 
                            type: "POST",
                            data: {inFuncion : '19', idSolicitud : idSolicitud},
                            success: function(result){
                                $('#dvAbriVisor').empty();
                                $("#dvAbriVisor").append("<button id='btCerrarVisor' type='button' style='float:right; background-color:red;'><b>x</b></button><iframe style='width:100%; height:89vh; z-index:-2;' src='.." + result + "'></iframe>");
                                $("#dvAbriVisor").css("display", "block");
                                ocultarLoading();
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                                ocultarLoading();
                            }
            });    
    });

    $(document).on('click', '.cbDescarga2', function() {
            mostrarLoading();
            let idSolicitud = this.id.replace("cbDes2", "");
            $.ajax({url: "constanciaController.php", 
                            type: "POST",
                            data: {inFuncion : '19', idSolicitud : idSolicitud},
                            success: function(result){
                                $('#dvAbriVisor').empty();
                                $("#dvAbriVisor").append("<button id='btCerrarVisor' type='button' style='float:right; background-color:red;'><b>x</b></button><iframe style='width:100%; height:89vh; z-index:-2;' src='.." + result + "'></iframe>");
                                $("#dvAbriVisor").css("display", "block");
                                ocultarLoading();
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert("Ocurrió un error, comuníquese con el administrador del sistema.");
                                ocultarLoading();
                            }
            });    
    });    


    $(document).on('click', '#btCerrarVisor', function() {
        $("#dvAbriVisor").css("display", "none");
    });

    $(document).on('click', '.cbHistorial', function() {
        $("#tbHistorio").find("tr:gt(0)").remove();
        let idSolicitud = this.id.replace("cbHis", "");
        mostrarLoading();
        $.ajax({url: "constanciaController.php", 
					    type: "POST",
						data: {inFuncion : '38', idSolicitud : idSolicitud},
						success: function(result){
                            data = jQuery.parseJSON(result);
                            for(let i=0; i<data.length; i++) {
                                let infoRow = "<tr>";
                                infoRow += '<td>' + data[i][0] + '</td>';
                                infoRow += '<td>' + data[i][1] + '</td>';
                                infoRow += '<td>' + data[i][2] + '</td>';
                                infoRow += '</tr>';
                                $('#tbHistorio tr:last').after(infoRow);
                            }
                            ocultarLoading();                 
                            $('#mHistorico').modal('show');  
				  		},
				  		error: function(XMLHttpRequest, textStatus, errorThrown) {
                            ocultarLoading();
				  			alert("Ocurrió un error, comuníquese con el administrador del sistema.");
				  		}
		});	   
    });

    $( "#bmHistoricoClose" ).click(function() {
        $('#mHistorico').modal('hide');
    }); 

    $( "#bmEnvioClose3" ).click(function() {
        $('#mHistorico').modal('hide');
    });     


</script>
<!-- FIN SCRIPT LOGICA -->

</body>
</html>