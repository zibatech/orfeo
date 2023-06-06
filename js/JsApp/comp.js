
/*
$(function () {
    $('.btn-visorimage').onclick(function () {
        lnk = $(this).data('link');
        tp = $(this).data('tp');
        rad = $(this).data('rad');
        var Exp = $('#numexp').val();
        $('#ModalviewImg2').modal('show');
        alert();
    })
})*/

function crearExpOLD () {
  
       
        Exp=$('#expOld').val( );
        depe=$('#dependenciaExpO').val( );
        usu=$('#selUsuario2').val( );
        axios({
            method: 'post',
            baseURL: '../expediente/exp-rest.php',
            data: 'fn=OldExp&exp='+Exp+'&depe='+depe+'&usua='+usu
        })
            .then(function (response) {
                // console.log(response);
                data = response.data.data;
                  console.log(data.expdata['RESPONSABLE']);
                // console.log(respotxt);
                $('#respodatox').html("Importado expediente al responsable "+data.expdata['RESPONSABLE'] );
              
                         
            })
            .catch(function (error) {
                $('#animationload').hide();
                if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                    $(this).showError('Error en petición', 'Estado del error: ' + error.response.status + '. Mensaje: ' + error.response.data.error);
                }
                //toastr.error(data.message, 'Error al Modificar ');
            });
 
};

function addexpOld(exp){
  //  alert(exp);
    $('#expOld').val( exp);
    $('#expOld').html( exp);
    $('#respodatox').html('' );
}
/*
$(function () {
    /*   $("#tb_listaexp").on("click", ".btn-visorimage", function () {
   
           vnk = $(this).data('link');
           tp = $(this).data('tp');
           rad = $(this).data('rad');
           var Exp = $('#numexp').val();
           console.log(rad + '' + tp + '' + lnk)
           $('#ModalviewImg2').modal('show');
           yy = rad.substring(0, 4);
           dd = rad.substring(4, 3);
           ruta = '../bodega/' + yy + '/' + dd + '/' + lnk;
           if (tp == 'aexp') { }
           //   yy = rad.substring(0, 4);
           // $('#mainFrameView2').attr('src', ruta);
           console.log(ruta);
           document.getElementById('mainFrameView2').src = ruta;
   
   
       });*/
/*

    $('.btn-o-descarga').click(function () {
        $("#" + $(this).data('xls')).table2excel({
            exclude: ".noExl",
            name: "Excel Lista Expediente",
            filename: "Consulta Expediente"
        });

    })
});*/
/*

*/
function descargaCons(xls) {
    $("#" + xls).table2excel({
        exclude: ".noExl",
        name: "Excel Lista Expediente",
        filename: "Consulta Expediente"
    });

}

function opertx(tptx) {
    $('#tpmasObserva').hide();
    $('#tipoCarpeta').hide();
    $('#tiposubexp').hide();
    //$('#tiposubexp').hide();
    $('#tipoincexp').hide();
    $('#tipoFisico').hide();
    $('#tpopertx').val(tptx);
    $('#txacc').html('');
    //nombusuari = $('select[name="selusers"] option:selected').text();
    var str2 = '';
    var str = '';
    var coma = '';
    var coma2 = '';
    var dataOperar = '';
    $("input[type=checkbox]:checked").each(function () {
        //cada elemento seleccionado
        valor = this.value;
        if (str != '')
            coma = ',';
        if (str2 != '')
            coma2 = ',';
        //alert();
        if (valor.length < 14) {
            str2 = str2 + coma2 + valor;
        } else {
            str = str + coma + valor;
        }
        dataOperar = dataOperar + "<div class='col-4'>" + valor + "</div>";
    });
    if (dataOperar.length === 0) {
        //  alert(dataOperar.length);
        $('#listenviar').html("<div class='alert alert-danger'>Debe selecionar radicados o anexos.</div>");
        return false;
    }
    $('#listRad').val(str);
    $('#listAnex').val(str2);
    $('#listenviar').html('<div class="card" style="width:100%"><div class="card-header">Radicados y/o Anexos Seleccionados</div><div class="card-body"><div class="row">' + dataOperar + '</div></div></div>');
    switch (tptx) {
        case "carpeta":
            $('#tituloOperMas').html('Asignar Carpeta');
            $('#tipoCarpeta').show();
            break;
        case "fisico":
            $('#tituloOperMas').html('Asignar Fisico virtual');
            $('#tipoFisico').show();
            break;
        case "Excluir":
            $('#tituloOperMas').html('Excluir del expediente');
            $('#tpmasObserva').show();
            break;
        case "incExp":
            $('#tituloOperMas').html('Incluir a otro Expediente');
            $('#tipoincexp').show();
            break;
        case "subexp":
            $('#tituloOperMas').html('Asignar Sub-expediente');
            $('#tiposubexp').show();
            break;
        default:

            break;
    }
}

function savetxExp(tp = 'm', tptx2 = '') {
    var Exp = $('#numexp').val();
    vartp = '';
    mdtp = '';
    if (tp == 'm') {
        txacc = 'txacc';
        tptx = $('#tpopertx').val();
        listrad = $('#listRad').val();
        listAnex = $('#listAnex').val();
        var lstaocambio = listrad + ',' + listAnex;
        lstdt = lstaocambio.split(',');
        var m_data = new FormData(); //=$('#formRadanex').serialize();
        m_data.append('fn', tptx);
        m_data.append('listRad', listrad);
        m_data.append('listAnex', listAnex);
    } else if (tp == 'i') {
        txacc = 'txaccI';
        tptx = tptx2;
        numerpDoc = $('#numerpDoc').val();
        numerpDocA = $('#numerAexp').val();
        lstdt = [numerpDoc];
        if (numerpDocA)
            lstdt = [numerpDocA];
        var m_data = new FormData(); //=$('#formRadanex').serialize();
        m_data.append('fn', tptx);
        m_data.append('listRad', numerpDoc);
        m_data.append('listAnex', numerpDocA);
        vartp = 'i';
    }
    $('#' + txacc).html('');
    switch (tptx) {
        case "carpeta":
            var carpetax = $('#operCarp' + vartp).val();
            /*     if (carpetax.trim() === '') {
         // alert('Debe llenar el campo carpeta ' + carpetax);
         $('#' + txacc).html('<div class="alert alert-danger">Debe llenar el campo carpeta </div>');
                 return false;
         }*/
            m_data.append('carpeta', carpetax);
            break;
        case "fisico":
            fisicox = $('#operFisicoMas' + vartp).val();
            // alert(fisicox);
            m_data.append('fisico', fisicox);
            break;
        case "Excluir":
            m_data.append('obx', $('#operObs').val());
            break;
        case "incExp":
            m_data.append('incExp', $('#operincexp').val());
            break;
        case "asuntoaexp":
            aepsunto = $('#aexpasunto').val();
            if (aepsunto.trim() === '') {
                // alert('Debe llenar el campo carpeta ' + carpetax);
                $('#' + txacc).html('<div class="alert alert-danger">Debe llenar el campo de asunto</div>');
                return false;
            }
            m_data.append('incExp', $('#aexpasunto').val());
            break;
        case 'tpdocMD':
            mdtp = $('#tpdocMD').val();
            //mdtptext = $('#tpdocMD').text(); 
            var mdtptext = $('select[name="tpdocMD"] option:selected').text();
            m_data.append('modtptext', mdtptext);
            m_data.append('modtp', mdtp);
            break;
        case "subexp":
            subexpx = $('#operaddsube' + vartp).val();
            if (subexpx.trim() === '') {
                // alert('Debe llenar el campo carpeta ' + carpetax);
                $('#' + txacc).html('<div class="alert alert-danger">Debe llenar el campo Sub Expediente </div>');
                return false;
            }
            m_data.append('subexp', subexpx);
            break;
    }
    m_data.append('exp', Exp);
    var url = "../expediente/exp-rest.php";
    $.ajax({
        url: url,
        type: "POST",
        data: m_data,
        dataType: "JSON",
        processData: false,
        contentType: false,
        success: function (data) {
            //alert('resp');

            switch (tptx) {
                case "carpeta":

                    $.each(lstdt, function (index, value) {
                        $('#divCarp' + value).html(carpetax);
                        $('#' + txacc).html('<div class="alert alert-success">Se asigno a la carpeta: ' + carpetax + ' </div>');
                    });
                    break;
                case "fisico":
                    $.each(lstdt, function (index, value) {
                        $('#divFisico' + value).html(fisicox);
                        $('#' + txacc).html('<div class="alert alert-success">Se asigno a la Fisico: ' + fisicox + ' </div>');
                    });
                    break;
                case "subexp":
                    $.each(lstdt, function (index, value) {
                        $('#divSubExp' + value).html(subexpx);
                        $('#' + txacc).html('<div class="alert alert-success">Se asigno al SubExpediente: ' + subexpx + ' </div>');
                    });
                    break;
                case 'tpdocMD':
                    $('#divSubTP' + numerpDocA).html(mdtptext);
                    $('#' + txacc).html('<div class="alert alert-success">Se asigno el tipo de documento : ' + mdtptext + ' </div>');
                    break;
                case "asuntoaexp":
                    $.each(lstdt, function (index, value) {
                        $('#divAnexExpAsunto' + value).html(aepsunto);
                        $('#' + txacc).html('<div class="alert alert-success">Se asigno el asunto: ' + aepsunto + ' </div>');
                    });
                    break;
                case "incExp":
                    resp = '';
                    datosinc = data.inc;
                    if (datosinc['ADD'])
                        resp = '<div class="alert alert-success">Se incluyen al expediente:  ' + $('#operincexp').val() + '' + datosinc['ADD'] + '  </div>';
                    if (datosinc['NADD'])
                        resp = '<div class="alert alert-danger">No se incluyen al expediente: ' + $('#operincexp').val() + ' debido que ya estan incluidos ' + datosinc['NADD'] + ' </div>';
                    $('#' + txacc).html(resp);
                    break;
                case "Excluir":
                    $('#' + txacc).html('<div class="alert alert-success">Se excluyen del expediente </div>');
                    cargartabla('S');
                    break;
            }

        },
        error: function (jqXHR, textStatus, errorThrown) {
            $('#' + txacc).html('<div class="alert alert-danger">No se Realizo la transacción </div>');
            //   $('#diverrorsave').html('<i class="fa fa-close" style="color: red";font-size: 35px;></i> No se realizo la acción');
        }
    });
}

function validarEstadoRad() {
    var exp = $('#numexp').val();
            $('#valradicados').show();
            $('#valcontradicados').hide();
            $("#cierreExpdiv").hide();
            $('#botonAnularexp').hide();
            $('#botonReAbrirexp').hide();
            $('#botonCerrarexp').hide();
            $('#titulocierreExp').html('Cierre de Expediente '+exp );
            $('#msgalert').html(' <strong>Recuerde que para cerrar el expediente sus radicados debe estar archivados y perdera acciones sobre ellos.</strong> ');
            var m_data = new FormData();
            m_data.append('numExp', exp);
            m_data.append('fn','validarExp' );

            var url = "../expediente/exp-rest.php";
            $.ajax({
                url: url, type: "POST", data: m_data,dataType: "JSON",processData: false,contentType: false, success: function (data) {                  
                   // console.log(data);
                    data=data.data;    
                        $('#valradicados').html(data.message);
                        classx = 'alert-success';
                        $("#valradicados").attr('class', 'alert ' + classx);
                        if(data.pasa=='si') {
                               $('#botonCerrarexp').show();                              
                        }else{
                            $('#botonCerrarexp').hide();
                            classx = 'alert-danger';
                             if(data.radicados.length==0){
                                 $("#valradicados").hide();
                                 //$("#valradicados").attr('class', 'alert ' + classx);
                                 //$('#valradicados').html('Expediente: '+exp + ' No tiene documentos ');
                             }
                             else{
                                
                                $('#valradicados').html(data.message);
                                $('#valcontradicados').show();  
                                $("#valradicados").attr('class', 'alert ' + classx);
                                $('#valcontradicados').html(data.datosrad);
                               }
                         }
                        if(data.radicados==0){
                             $('#botonAnularexp').show();
                        } 
                        $('#msgalert').show();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#msgalert').hide();
                    $('#botonCerrarexp').hide();
                }
            });
            /*    classx = 'alert-danger';
             $('#mensage').html("<strong>Acción No Realizada</strong> - " + mensaje);
             */

    }

    function CerrarExpModal1(newest) {
        var exp = $('#numexp').val();
            var estado = $('#expEstado').val();
            $msg = '';
                $('expEstado').val(newest);
            dtn = 'block';
            texto=' Cerrado';
            if (newest == 0) {
                    titulo = 'Cerrar Exp';
                    accion = 'Abrir';
                    tip = 1;
                    texto=' Re-abierto'
            }
            if (newest == 1) {

                    texto=' Cerrado'
            }
                if (newest == 2) {

                    texto=' Anulado'
            }
            var m_data = new FormData();
            m_data.append('exp', exp);
            m_data.append('estado',estado );
            m_data.append('newest',newest );
            m_data.append('fn','cambiarEst' );
            var url = "../expediente/exp-rest.php";
            $.ajax({     url: url, type: "POST", data: m_data,dataType: "JSON",processData: false,contentType: false, success: function (data) {                  
                 console.log(data);
                        $("#valcontradicados").hide();
                        $('#msgalert').hide();
                        $('#valradicados').hide();
                        $("#cierreExpdiv").show();
                        $("#botonCerrarexp").hide();
                        
                        $('#botonAnularexp').hide();
                        $('#botonCerrarexp').hide();
                        $('#botonReAbrirexp').hide();
                        $("#cierreExpdiv").html('Expediente ' + exp +' '+ texto);
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                    $('#' + txacc).html('<div class="alert alert-danger">No se Realizo la transacción </div>');
                            //   $('#diverrorsave').html('<i class="fa fa-close" style="color: red";font-size: 35px;></i> No se realizo la acción');
                    }
            });
        }

        function ReabrirExpRad(dff='C'){
            var exp = $('#numexp').val();
                $('#valradicados').show();
                $('#valcontradicados').hide();
                $("#cierreExpdiv").hide();
                $('#botonAnularexp').hide();
                $('#botonCerrarexp').hide();
                $('#valradicados').hide();
                $('#botonReAbrirexp').show();
                if(dff=='A'){
                       $('#titulocierreExp').html('Desanular Expediente '+exp );
                        $('#botonReAbrirexp').html('Des-Anular');
                }
                else{
                
                $('#titulocierreExp').html('Re Abrir Expediente '+exp );
                $('#botonReAbrirexp').html('Re-Abrir');
            }
                $('#msgalert').html(' <strong>Recuerde que al reabrir el expediente se habilitan la opciones.</strong> ');
         }
        
         ///neew fucntion ver imagen
function visorimagex(radicado, tipo,path,exp) {
    //  $('#ModalviewImg2').modal('show');
    //   document.getElementById('mainFra
    console.log(radicado.length+' h '+radicado+', '+tipo+', '+path+', '+exp);
    $('#ModalviewImg2').modal('show');
    $('#tituloimagen').html('Ver Documento ' + radicado);
    if(tipo=='radi' || tipo=='radie') {
 
        document.getElementById('mainFrameView2').src = '../bodega/'+path;
        return true;
    }
    if(tipo=='anexo' ) {
        yyyy=radicado.substring(0, 4) ;
        depe=radicado.substring(4, 8 );
        depe=parseInt(depe);
      //  alert('../bodega/'+yyyy+'/'+depe+'/docs/'+path)
        document.getElementById('mainFrameView2').src = '../bodega/'+yyyy+'/'+depe+'/docs/'+path;
        return true;
    }
    if(tipo=='aexpe'){
        yyyy=path.substring(0, 4) ;
        depe=path.substring(4, 7) ;
      //  console.log('../bodega/'+yyyy+'/'+depe+'/docs/'+path);
        document.getElementById('mainFrameView2').src = '../bodega/'+yyyy+'/'+depe+'/docs/'+path;
    }
    if(tipo=='aexp'){
        yyyy=path.substring(0, 4) ;
        depe=path.substring(4, 8);
        if(exp.length==18)
            depe=path.substring(4, 7);
        if(exp.length==20)
            depe=path.substring(5, 9);
      //  console.log('../bodega/'+yyyy+'/'+depe+'/docs/'+path);
        document.getElementById('mainFrameView2').src = '../bodega/'+yyyy+'/'+depe+'/docs/'+path;
    }
}


function visorimagex2(rad, tp, lnk) {
    /*lnk = $(this).data('link');
    tp = $(this).data('tp');
    rad = $(this).data('rad');*/
    var Exp = $('#numexp').val();
    console.log(rad + '' + tp + '' + lnk)
   
    ruta = '../bodega/'+lnk;
    if (tp == 'aexp') { 
        exp=lnk.split('_');
        tam=9
        if(exp[0].length==18)
        tam=8
        yy = lnk.substring(0, 4);
        dd = lnk.substring(4, tam);
        ruta = '../bodega/' + yy + '/' + dd + '/docs/' + lnk;
    }
    rad=lnk.split('.');
    if(rad[1]=='pdf' || rad[1]=='html')
        $('#ModalviewImg2').modal('show');
    
    //   yy = rad.substring(0, 4);
    // $('#mainFrameView2').attr('src', ruta);
    console.log(ruta);
    document.getElementById('mainFrameView2').src = ruta;
}

function verRad(radicado,tipo){
    url='../verradicado.php?verrad='+radicado;
    console.log(url);
        $('#ModalviewImg2').modal('show');
        $('#tituloimagen').html('Ver Documento ' + radicado);
        document.getElementById('mainFrameView2').src = url;
}


function usuario5() {
    selectIDu = 'usuaDocExp'
    var depe = $("#dependenciaResp").val();
    var activo = $("#dependenciaResp").val() ? '1' : '0';
   
    $('#animationload').show();
    //console.log('fn=usuarios&depe='+depe+'&tpus='+tpUs);
    axios({
        method: 'post',
        baseURL: '../estadisticas/rest-est.php',
        data: 'fn=usuarios&depe=' + depe 
    })
        .then(function (response) {
            // console.log(response);
            data = response.data;
            //  console.log(data);
            $("#" + selectIDu).empty();
            $("#" + selectIDu).append('<option value="0">-- Selecione --</option>');
            
            
            //data.data['a']['NOMB']=' TODOS';
          //  data.data['a']['COD']='0';
            campos = new Array();
            campos['codigo'] = 'USUA_DOC';
            campos['nombre'] = 'NOMB';
            console.log('prueba');
            cargarselect2(data.data, selectIDu, campos, 2);
            $("#" + selectIDu).append('<option value="0">Todos</option>');
            $('#animationload').hide();
        })
        .catch(function (error) {
            $('#animationload').hide();
            if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                $(this).showError('Error en petición', 'Estado del error: ' + error.response.status + '. Mensaje: ' + error.response.data.error);
            }
            //toastr.error(data.message, 'Error al Modificar ');
        });


}
