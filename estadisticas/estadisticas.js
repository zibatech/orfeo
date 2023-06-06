esvar={
    'sProcessing': 'Procesando...',
    'sLengthMenu': 'Mostrar _MENU_ ',
    'sZeroRecords': 'No se encontraron resultados',
    'sEmptyTable': 'Ningún dato disponible en esta tabla',
    'sInfo': 'Registros del _START_ al _END_ de _TOTAL_ registros',
    'sInfoEmpty': 'Mostrando registros del 0 al 0 de un total de 0 registros',
    'sInfoFiltered': '(filtrado de un total de _MAX_ registros)',
    'sInfoPostFix': '',
    'sSearch': 'Buscar:',
    'sUrl': '',
    'sInfoThousands': ',',
    'sLoadingRecords': 'Cargando...',
    'oPaginate': {
        'sFirst': 'Primero',
        'sLast': 'Último',
        'sNext': 'Siguiente',
        'sPrevious': 'Anterior'
    }}; 


$(function () {
    $('#dependencia_busq').change(function () {
        series();
        $("#selSubSerie").empty();
        
        tipodoc();
        usuario();
    })
    $('#selSerie').change(function () {
        subSeries();
        $("#selTipoDoc").empty();
        $("#selSubSerie").empty();
    });
    $('#selSubSerie').change(function () {
        tipodoc();
    });
});

function limpiaselect(campo, valor = 0, txtoption = '- Seleccione --') {
    $("#" + campo).empty();
    $("#" + campo).append('<option value="' + valor + '">' + txtoption + '</option>');

}

function sortselect(campo) {

    var options = $("#" + campo + " option ");
    options.detach().sort(function (a, b) {
        /*var at = $(a).text();
        var bt = $(b).text(); */
        var at = $(a).val();
        var bt = $(b).val();
        return (at > bt) ? 1 : ((at < bt) ? -1 : 0);
    });
    options.appendTo("#" + campo);

}

function tipodoc() {
    $("#selTipoDoc").empty();
    /*   $("#selTipoDoc" ).empty();
       $("#selTipoDoc" ).append('<option value="0">-- Selecione --</option>');*/
    campos = new Array();
    campos['codigo'] = 'codserie';
    campos['nombre'] = 'descrip';

    selectSB = 'selTipoDoc';
    //   cargarselect(data, 'selTipoDoc', campos);
    var depe = $("#dependencia_busq").val();
    var serie = $("#selSerie").val();
    var subserie = $("#selSubSerie").val();
    $('#animationload').show();
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=tpdoc&serie=' + serie + '&depe=' + depe + '&subserie=' + subserie
        })
        .then(function (response) {
            // console.log(response);
            data = response.data;
            $("#" + selectSB).empty();
            $("#" + selectSB).append('<option value="0">-- Selecione --</option>');
            campos = new Array();
            campos['codigo'] = 'COD';
            campos['nombre'] = 'NOMB';
            cargarselect(data.data, selectSB, campos, 2);
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

function cargarselect(vector, campo, campos, tipo = 1) {
  //  $("#" + campo).empty();
  //  $("#" + campo).append('<option value="0">-- Selecione --</option>');
    //  console.log(vector, campo, campos,tipo);
    $.each(vector, function (id, value) {
        if (tipo == 1)
            $("#" + campo).append('<option  value="' + value[campos['codigo']] + '">' + value[campos['codigo']] + ' - ' + value[campos['nombre']] + '</option>');
        else
            $("#" + campo).append('<option  value="' + value[campos['codigo']] + '">' + value[campos['nombre']] + '</option>');
    });
    // console.log('acabo');

}



function series() {
    selectID1 = 'selSerie';
    /*$("#"+selectID1).empty();
    $("#"+selectID1).append('<option value="0">-- Selecione --</option>');*/
    campos = new Array();
    campos['codigo'] = 'codserie';
    campos['nombre'] = 'descrip';
    data = new Array();
    data[0] = new Array();
    data[0]['codserie'] = '1';
    data[0]['descrip'] = 'prueba';
    var depe = $("#dependencia_busq").val();
    // console.log($("#dependencia_busq").val());
    //cargarselect(data, selectID, campos);
    $('#animationload').show();
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=serie&dep_busq=' + depe
        })
        .then(function (response) {
            //   console.log(response.data);
            data = response.data;
            $("#" + selectID1).empty();
            $("#" + selectID1).append('<option value="0">-- Selecione --</option>');
            campos = new Array();
            campos['codigo'] = 'ID';
            campos['nombre'] = 'NOMB';
            console.log('Serie');
            cargarselect(data.data, selectID1, campos, 2);
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

function dependencia2() {
    selectID = 'dependencia_busq';
    campos = new Array();
    campos['codigo'] = 'coddep';
    campos['nombre'] = 'nomb';
    var tpv = $("#tipoEstadistica").val();
    $('#animationload').show();
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=depe&tpd=' + tpv 
        })
        .then(function (response) {
            // console.log(response);
            data = response.data;
            $("#" + selectID).empty();
          //  $("#" + selectID).append('<option value="99999">-- Todas Las Dependencias --</option>');
            campos = new Array();
            campos['codigo'] = 'ID';
            campos['nombre'] = 'NOMB';
            cargarselect(data.data, selectID, campos, 2);
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


function subSeries() {
    selectID = 'selSubSerie';
    /*
    $("#"+selectID).empty();
    $("#"+selectID ).append('<option value="0">-- Selecione --</option>');*/
    campos = new Array();
    campos['codigo'] = 'codserie';
    campos['nombre'] = 'descrip';
    data = new Array();
    data[0] = new Array();
    data[0]['codserie'] = '1';
    data[0]['descrip'] = 'prueba';
    var depe = $("#dependencia_busq").val();
    var serie = $("#selSerie").val();
    //    console.log($("#dependencia_busq").val());
    //cargarselect(data, selectID, campos);
    $('#animationload').show();
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=subSeries&serie=' + serie + '&dep_busq=' + depe
        })
        .then(function (response) {
            // console.log(response);
            data = response.data;
            $("#" + selectID).empty();
            $("#" + selectID).append('<option value="0">-- Selecione --</option>');
            campos = new Array();
            campos['codigo'] = 'ID';
            campos['nombre'] = 'NOMB';
            cargarselect(data.data, selectID, campos, 2);
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

function usuario() {
    selectIDu = 'selUsuario';
    /*$("#"+selectIDu).empty();
    $("#"+selectIDu ).append('<option value="0">-- Selecione --</option>');*/
    var depe = $("#dependencia_busq").val();
    var activo = $("#dependencia_busq").val() ? '1' : '0';
    var tpUs = $("#CHKselUsuario").is(':checked') ? '1' : '0';
    $('#animationload').show();
    //console.log('fn=usuarios&depe='+depe+'&tpus='+tpUs);
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=usuarios&depe=' + depe + '&tpus=' + tpUs
        })
        .then(function (response) {
            // console.log(response);
            data = response.data;
            //  console.log(data);
            $("#" + selectIDu).empty();
            $("#" + selectIDu).append('<option value="0">-- Selecione --</option>');
            campos = new Array();
            campos['codigo'] = 'COD';
            campos['nombre'] = 'NOMB';
            cargarselect(data.data, selectIDu, campos, 2);
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
$(function () {
    $('#tipoEstadistica').change(function () {
        var titulo = $('#tipoEstadistica option:selected').text();
        $('#tipoRadicado').removeAttr( "disabled");
        tprp=$('#tipoEstadistica').val();
        $('#resulEstdatos').hide();
        //var infoRP = $(this).data('rp'+tprp);
        //var infoRP =$(this).find('option:selected').data('inforp');
        $('.INFOalert').html($('#tipoEstadistica option:selected').data('inforp'));
        //var infoRP = $(this).data('rp'+tprp);
        $('#tipoRadicado').val('');
        dependencia2();
        switch (tprp) {
            case '2':
                $('#tipoRadicado').val(2);
               // $('#tipoRadicado').removeAttr( "disabled");
              //  $('#tipoRadicado').attr( "disabled",'true' );     
              break;      
         case '3':
                $('#tipoRadicado').val(1);
                $('#tipoRadicado').attr( "disabled",'true' );           
                break;
            case '9':
            case '10':
                $('#tipoRadicado').val(2);
                $('#tipoRadicado').attr( "disabled",'true' );           
                break;
                case '11':
                $('#tipoRadicado').val(1);
                $('#tipoRadicado').attr( "disabled",'true' );            
                break;
                case '12':
                $('#tipoRadicado').val(3);
                $('#tipoRadicado').attr( "disabled",'true' );           
                break;

            default:
                
                break;
        }

    });
});
$(function () {
    $('#generar').click(function () {
        if($('#tipoEstadistica').val()==0)
            return false;
        $('#resulEstdatos').show();
        $(".btn-generar").prop("disabled", true);
        var titulo = $('#tipoEstadistica option:selected').text();
        var fecha = $('#fecha_ini').val() + ' - ' + $('#fecha_fin').val()
        $('#nomReport').html(titulo.substring(3) + ' (' + fecha + ')');
        switch ($('#tipoEstadistica').val()) {
            case '1':
            case '2':
            case '6':
            case '3':
            case '4':
            case '7':
                report();
                break;
            case '9':
                report9();
                break;
            case '10':
                report10();
                break;
            case '11':
                report11();
                break;
            case '12':
                report12();
                break;
            default:
                break;
        }


    });
}); 

function report() {
    selectIDu = 'selUsuario';
    /*$("#"+selectIDu).empty();
    $("#"+selectIDu ).append('<option value="0">-- Selecione --</option>');*/
    var rp = $('#tipoEstadistica').val();
    var titulo = $('#tipoEstadistica option:selected').text();
    var depe = $("#dependencia_busq").val();
    var serie = $("#selSerie").val();
    var subserie = $("#selSubSerie").val();
    var tpdoc = $("#selTipoDoc").val();
    var tpRad = $("#tipoRadicado").val();
    var usuA = $("#selUsuario").val();
    var tpAds = $("#CHKseldep").is(':checked') ? '1' : '0';
    var fini = $("#fecha_ini").val();
    var ffin = $("#fecha_fin").val();
    datosbtnextra="data-depe='"+depe+"'  data-tpRad='"+tpRad+"'  data-titulo='"+titulo+"' data-serie='"+serie+"' data-subserie='"+subserie+"' data-tpdoc='"+tpdoc+"' data-tpRad='"+tpRad+"' data-usua='"+usuA+"' data-tpads='"+tpAds+"' data-fini='"+fini+"' data-ffin='"+ffin+"'  ";
    //$('#animationload').show();
    $('#processing-modal').modal('show');
    //console.log('fn=usuarios&depe='+depe+'&tpus='+tpUs);
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=rp&reporte='+rp+'&depe=' + depe + '&serie=' + serie + '&subserie=' + subserie + '&tpdoc=' + tpdoc + '&usu=' + usuA + '&fini=' + fini + '&ffin=' + ffin+'&tpAds='+tpAds+'&tpRad='+tpRad
        })
        .then(function (response) {
          //   console.log(rp);
            total=0;
            totalD=0;
            data = response.data.data;
            
                if(rp==1){
                    
                    htmls = '<div class="col-4 float-right"><div class="input-group input-group-sm">'+
                            '<div class="input-group-prepend"><span class="input-group-text tamCab text-left" id="basic-addon1">Total Radicados</span></div>'+
                            '  <input type="text" class="form-control" id="tb_total" readonly aria-describedby="basic-addon3"> <div class="input-group-append">'+
                            '<button class="btn btn-outline-primary btn-rp1det" data-rep="1" data-toggle="modal" data-target="DetEsta" data-btns=""  data-id="T"  type="button" data-toggle="tooltip"  data-placement="top"  title="Ver detalles Total"><i class="fa fa-align-justify"></i></button>'+
                            '<button class="btn btn-outline-success btn-rp1detXLS" type="button" data-rep="1" '+datosbtnextra+'  data-tit="" data-btns="99999" data-id="T" ><i class="fa fa-table"></i></button></div><div id="detXLST" class="float-right"></div>'+
                            '</div></div>';

                            htmls += '<table id="tb_rp" class="table table-bordered table-striped table-hover ">'
                            htmls += ' <thead class="thead-dark"><tr><th>#</th><th>Usuario</th><th>Radicados</th><th></th></tr></thead>';
                            htmls += '<tbody></tbody></table>';//<th>#</th>
                            var htmldt;
                            //'<button class="btn btn-xs btn-primary btn-rp1det" data-rep="1" data-toggle="modal" data-target="DetEsta" data-id="1" data-btns="1" type="button" ><i class="fa fa-align-justify"></i></button>'
                         
                            $("#resultado").html(htmls);
                            $.each(data, function(index, value) {
                                codigo2 = value['USUA_DOC'];
                                codigo = value['COD_USU'];
                                depe = value['DEPE_USUA'];
                                numr = value['NUM'];
                                radi = value['RADICADOS'];
                                usua = value['USUARIO'];
                                btnA = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="1" data-toggle="modal" data-target="DetEsta" data-btns='+depe+'  data-id='+codigo2+' type="button" data-toggle="tooltip"  data-placement="top"  title="Ver detalles" ><i class="fa fa-align-justify"></i></button> ';
                                btnToS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="1" data-toggle="modal" data-target="DetEsta"  data-tit="' + usua +'" data-btns="'+depe+'" '+datosbtnextra+' data-id='+codigo2+'  data-btns=2 type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLST" class="float-right"></div>';
                                $('#tb_rp').append('<tr> <td class = "text-right" > ' + numr + ' </td><td >' + usua +'</td><td class = "text-right">' + radi +'</td><td class="text-center" >' + btnA+btnToS +'</td></tr>');
                                total=total+parseInt(radi);
                            });


                }
                if(rp==2){
                    htmls = '<div class="col-4 float-right"><div class="input-group input-group-sm">'+
                    '<div class="input-group-prepend"><span class="input-group-text tamCab text-left" id="basic-addon1">Total Radicados</span></div>'+
                    '  <input type="text" class="form-control" id="tb_total" readonly aria-describedby="basic-addon3"> <div class="input-group-append">'+
                    '<button class="btn btn-outline-primary btn-rp1det" data-rep="2" data-toggle="modal" data-target="DetEsta" data-btns=""  data-id="T"  type="button" data-toggle="tooltip"  data-placement="top"  title="Ver detalles Total"><i class="fa fa-align-justify"></i></button>'+
                    '<button class="btn btn-outline-success btn-rp1detXLS" type="button" data-rep="2" '+datosbtnextra+'  data-tit="" data-btns="99999" data-id="T" ><i class="fa fa-table"></i></button></div><div id="detXLST" class="float-right"></div>'+
                    '</div></div>';

                    htmls += '<table id="tb_rp" class="table table-bordered table-striped table-hover ">'
                    htmls += ' <thead class="thead-dark"><tr><th>#</th><th>Medio</th><th>Radicados</th><th></th></tr></thead>';
                    htmls += '<tbody></tbody></table>';//<th>#</th>
                    var htmldt;
                    //'<button class="btn btn-xs btn-primary btn-rp1det" data-rep="1" data-toggle="modal" data-target="DetEsta" data-id="1" data-btns="1" type="button" ><i class="fa fa-align-justify"></i></button>'
                    
                    $("#resultado").html(htmls);
                   // console.log(data);
                    $.each(data, function(index, value) {
                        codigo = value['CODI'];
                        depe = value['DEPE_USUA'];
                        numr = value['NUM'];
                        radi = value['RADICADOS'];
                        usua = value['MEDIO_RECEPCION'];
                        btnA = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="2" data-toggle="modal" data-target="DetEsta" data-btns='+codigo+'  data-id='+codigo+' type="button" data-toggle="tooltip"  data-placement="top"  title="Ver detalles" ><i class="fa fa-align-justify"></i></button> ';
                        btnToS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="2" data-toggle="modal" data-target="DetEsta"  data-tit="' + usua +'" data-btns="'+codigo+'" '+datosbtnextra+' data-id='+codigo+'  data-btns=2 type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLST" class="float-right"></div>';
                        $('#tb_rp').append('<tr> <td class = "text-right" > ' + numr + ' </td><td >' + usua +'</td><td class = "text-right">' + radi +'</td><td class="text-center" >' + btnA+btnToS +'</td></tr>');
                        total=total+parseInt(radi);
                        
                    });
                }
                if(rp==3){
                    htmls = '<div class="col-4 float-right"><div class="input-group input-group-sm">'+
                            '<div class="input-group-prepend"><span class="input-group-text tamCab text-left" id="basic-addon1">Total Envíos</span></div>'+
                            '  <input type="text" class="form-control" id="tb_total" readonly aria-describedby="basic-addon3"> <div class="input-group-append">'+
                            '</div><div class="input-group-prepend"><span class="input-group-text tamCab text-left" id="basic-addon1">Total Devoluciones</span></div>'+
                            '  <input type="text" class="form-control" id="tb_totalD" readonly aria-describedby="basic-addon3"> <div class="input-group-append">'+
                            '<button class="btn btn-outline-primary btn-rp1det" data-rep="3" data-toggle="modal" data-target="DetEsta" data-btns=""  data-id="T"  type="button" data-toggle="tooltip"  data-placement="top"  title="Ver detalles Total"><i class="fa fa-align-justify"></i></button>'+
                            '<button class="btn btn-outline-success btn-rp1detXLS" type="button" data-rep="3" '+datosbtnextra+'  data-tit="" data-btns="99999" data-id="T" ><i class="fa fa-table"></i></button></div><div id="detXLST" class="float-right"></div>'+
                            '</div></div></div>';

                            /*'<button class="btn btn-outline-primary btn-rp1det" data-rep="3" data-toggle="modal" data-target="DetEsta" data-btns="T"  data-id="T"  type="button" data-toggle="tooltip"  data-placement="top"  title="Ver detalles Total"><i class="fa fa-align-justify"></i></button>'+
                            '<button class="btn btn-outline-success btn-rp1detXLS" type="button" data-rep="3" '+datosbtnextra+'  data-tit="" data-btns="99999" data-id="T" ><i class="fa fa-table"></i></button></div><div id="detXLST" class="float-right"></div>'+*/
                            htmls += '<table id="tb_rp" class="table table-bordered table-striped table-hover ">'
                            htmls += ' <thead class="thead-dark"><tr><th>Codigo</th><th>Medio de Envío</th><th>Total de Envíos</th><th>Devoluciones</th><th></th></tr></thead>';
                            htmls += '<tbody></tbody></table>';//<th>#</th>
                            var htmldt;
                            //'<button class="btn btn-xs btn-primary btn-rp1det" data-rep="1" data-toggle="modal" data-target="DetEsta" data-id="1" data-btns="1" type="button" ><i class="fa fa-align-justify"></i></button>'
                            
                            $("#resultado").html(htmls);
                            $.each(data, function(index, value) {
                                codigo = value['COD'];
                                Medio = value['NOMB'];
                                numr = value['NUM'];
                                devol= value['DEV']?value['DEV']:0;
                                btnA = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="3" data-toggle="modal" data-target="DetEsta" data-btns='+Medio+'  data-id='+codigo+' type="button" data-toggle="tooltip"  data-placement="top"  title="Ver detalles" ><i class="fa fa-align-justify"></i></button> ';
                                btnToS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="3" data-toggle="modal" data-target="DetEsta"  data-tit="' + Medio +'" data-btns="'+codigo+'" '+datosbtnextra+' data-id='+codigo+'  data-btns=2 type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLST" class="float-right"></div>';
                                $('#tb_rp').append('<tr> <td class = "text-right" > ' + codigo + ' </td><td >' + Medio +'</td><td class = "text-right">' + numr +'</td><td class = "text-right">' + devol +'</td><td class="text-center" >' + btnA+btnToS +'</td></tr>');
                                total=total+parseInt(numr);
                                console.log(devol);
                                totalD=totalD+parseInt(devol);
                            });

                            $('#tb_totalD').val(totalD);
                }
                if(rp==4){
                    htmls = '<div class="col-4 float-right"><div class="input-group input-group-sm">'+
                            '<div class="input-group-prepend"><span class="input-group-text tamCab text-left" id="basic-addon1">Total Radicados</span></div>'+
                            '  <input type="text" class="form-control" id="tb_total" readonly aria-describedby="basic-addon3"> <div class="input-group-append"></div>'+
                            '</div></div></div>';

                            /*'<button class="btn btn-outline-primary btn-rp1det" data-rep="3" data-toggle="modal" data-target="DetEsta" data-btns="T"  data-id="T"  type="button" data-toggle="tooltip"  data-placement="top"  title="Ver detalles Total"><i class="fa fa-align-justify"></i></button>'+
                            '<button class="btn btn-outline-success btn-rp1detXLS" type="button" data-rep="3" '+datosbtnextra+'  data-tit="" data-btns="99999" data-id="T" ><i class="fa fa-table"></i></button></div><div id="detXLST" class="float-right"></div>'+*/
                            htmls += '<table id="tb_rp" class="table table-bordered table-striped table-hover ">'
                            htmls += ' <thead class="thead-dark"><tr><th>Número</th><th>Usuario</th><th>Radicados</th><th>Hojas digitalizadas</th><th></th></tr></thead>';
                            htmls += '<tbody></tbody></table>';//<th>#</th>
                            var htmldt;
                            //'<button class="btn btn-xs btn-primary btn-rp1det" data-rep="1" data-toggle="modal" data-target="DetEsta" data-id="1" data-btns="1" type="button" ><i class="fa fa-align-justify"></i></button>'
                            
                            $("#resultado").html(htmls);
                            $.each(data, function(index, value) {
                                codigo = value['COD_U'];
                                Medio = value['DEPE'];
                                numr = value['NUM'];
                                usuarioc = value['USUARIO'];
                                rAD = value['RADICADOS'];
                                hoj= value['HOJAS_DIGITALIZADAS']?value['HOJAS_DIGITALIZADAS']:0;
                                btnA='';btnToS ='';
                                btnA = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="4" data-toggle="modal" data-target="DetEsta" data-btns='+Medio+'  data-id='+codigo+' type="button" data-toggle="tooltip"  data-placement="top"  title="Ver detalles" ><i class="fa fa-align-justify"></i></button> ';
                                btnToS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="4" data-toggle="modal" data-target="DetEsta"  data-tit="' + Medio +'" data-btns="'+codigo+'" '+datosbtnextra+' data-id='+codigo+'  data-btns=2 type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLST" class="float-right"></div>';
                                $('#tb_rp').append('<tr> <td class = "text-right" > ' + numr + ' </td><td >' + usuarioc +'</td><td class = "text-right">' + rAD +'</td><td class = "text-right">' + hoj +'</td><td class="text-center" >' + btnA+btnToS +'</td></tr>');
                                total=total+parseInt(numr);
                             //   console.log(devol);
                                totalD=totalD+parseInt(rAD);
                            });

                          //  $('#tb_totalD').val(totalD);
                }
                if(rp==6){
                    htmls = '<div class="col-4 float-right"><div class="input-group input-group-sm">'+
                            '<div class="input-group-prepend"><span class="input-group-text tamCab text-left" id="basic-addon1">Total Radicados</span></div>'+
                            '  <input type="text" class="form-control" id="tb_total" readonly aria-describedby="basic-addon3"> <div class="input-group-append">'+
                            '<button class="btn btn-outline-primary btn-rp1det" data-rep="6" data-toggle="modal" data-target="DetEsta" data-btns=""  data-id="T"  type="button" data-toggle="tooltip"  data-placement="top"  title="Ver detalles Total"><i class="fa fa-align-justify"></i></button>'+
                            '<button class="btn btn-outline-success btn-rp1detXLS" type="button" data-rep="6" '+datosbtnextra+'  data-tit="" data-btns="99999" data-id="T" ><i class="fa fa-table"></i></button></div><div id="detXLST" class="float-right"></div>'+
                            '</div></div>';
                            htmls += '<table id="tb_rp" class="table table-bordered table-striped table-hover ">'
                            htmls += ' <thead class="thead-dark"><tr><th>#</th><th>Dependencia</th><th>Radicados</th><th></th></tr></thead>';
                            htmls += '<tbody></tbody></table>';//<th>#</th>
                            $("#resultado").html(htmls);
                            $.each(data, function(index, value) {
                                codigod = value['CODI_DEPE_ACTUAL'];
                                depe = value['DEPENDENCIA_ACTUAL'];
                                numr = value['NUM'];
                                radi = value['RADICADOS'];
                                btnA = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="6" data-toggle="modal" data-target="DetEsta" data-btns='+depe+'  data-id='+codigod+' type="button" data-toggle="tooltip" '+datosbtnextra+' data-placement="top"  title="Ver detalles" ><i class="fa fa-align-justify"></i></button> ';
                                btnToS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="6" data-toggle="modal" data-target="DetEsta"  data-tit="' + depe +'" data-btns="'+depe+'"  data-id='+codigod+'  data-btns=2 '+datosbtnextra+' type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLST" class="float-right"></div>';
                                $('#tb_rp').append('<tr> <td class = "text-right" > ' + numr + ' </td><td >' + depe +'</td><td class = "text-right">' + radi +'</td><td class="text-center" >' + btnA+btnToS +'</td></tr>');
                                total=total+parseInt(radi);
                            });
		}
                if(rp==7){
                    htmls = '<div class="col-4 float-right"><div class="input-group input-group-sm">'+
                            '<div class="input-group-prepend"><span class="input-group-text tamCab text-left" id="basic-addon1">Total Radicados</span></div>'+
                            '  <input type="text" class="form-control" id="tb_total" readonly aria-describedby="basic-addon3"> <div class="input-group-append">'+
                            '<button class="btn btn-outline-primary btn-rp1det" data-rep="7" data-toggle="modal" data-target="DetEsta" data-btns=""  data-id="T"  type="button" data-toggle="tooltip"  data-placement="top"  title="Ver detalles Total"><i class="fa fa-align-justify"></i></button>'+
                            '<button class="btn btn-outline-success btn-rp1detXLS" type="button" data-rep="7" '+datosbtnextra+'  data-tit="" data-btns="99999" data-id="T" ><i class="fa fa-table"></i></button></div><div id="detXLST" class="float-right"></div>'+
                            '</div></div>';

                            htmls += '<table id="tb_rp" class="table table-bordered table-striped table-hover ">'
                            htmls += ' <thead class="thead-dark"><tr><th>Número</th><th>Usuario</th><th>Radicados</th><th></th></tr></thead>';
                            htmls += '<tbody></tbody></table>';//<th>#</th>
                            var htmldt;
                            
                            $("#resultado").html(htmls);
                            $.each(data, function(index, value) {
                                codigo2 = value['HID_DOC_USUARIO'];
                                numr = value['NUM'];
                                usuarioc = value['USUARIO'];
                                rAD = value['RADICADOS'];
                                btnA='';btnToS ='';
                                btnA = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="7" data-toggle="modal" data-target="DetEsta" data-btns='+codigo2+'  data-id='+codigo2+' type="button" data-toggle="tooltip"  data-placement="top"  title="Ver detalles" ><i class="fa fa-align-justify"></i></button> ';
                                btnToS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="7" data-toggle="modal" data-target="DetEsta"  data-tit="" data-btns="'+codigo2+'" '+datosbtnextra+' data-id='+codigo2+'  data-btns=2 type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLST" class="float-right"></div>';
                                $('#tb_rp').append('<tr> <td class = "text-right" > ' + numr + ' </td><td >' + usuarioc +'</td><td class = "text-right">' + rAD +'</td><td class="text-center" >' + btnA+btnToS +'</td></tr>');
                                total=total+parseInt(numr);
                                totalD=totalD+parseInt(rAD);
                                total=totalD;
                            });

                          //  $('#tb_totalD').val(totalD);
                }
                setTimeout(function(){
                    $('#processing-modal').modal('hide');
                  }, 2000);
                //$('#processing-modal').modal('hide');
            $(document).ready(function() { 
                $('#tb_rp').DataTable( {
                    'language': esvar,pageLength: 25,
                } );
            } );
            $('[data-toggle="tooltip"]').tooltip();
            
            $('#tb_total').val(total);
            $('#animationload').fadeOut('slow');
            $('#processing-modal').hide();
            $(".btn-generar").prop("disabled", false);

            $('#processing-modal').modal('hide');
        })
        .catch(function (error) {
            $(".btn-generar").prop("disabled", false);
            $('#animationload').hide();
            $('#processing-modal').modal('hide');
            if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                $(this).showError('Error en petición', 'Estado del error: ' + error.response.status + '. Mensaje: ' + error.response.data.error);
            }
            //toastr.error(data.message, 'Error al Modificar ');
        });
        $('#processing-modal').modal('hide');
        
}


function report9() {
    selectIDu = 'selUsuario';
    /*$("#"+selectIDu).empty();
    $("#"+selectIDu ).append('<option value="0">-- Selecione --</option>');*/
    var titulo = $('#tipoEstadistica option:selected').text();
    var depe = $("#dependencia_busq").val();
    var serie = $("#selSerie").val();
    var subserie = $("#selSubSerie").val();
    var tpdoc = $("#selTipoDoc").val();
    var tpRad = $("#tipoRadicado").val();
    var usuA = $("#selUsuario").val();
    var tpAds = $("#CHKseldep").is(':checked') ? '1' : '0';
    var fini = $("#fecha_ini").val();
    var ffin = $("#fecha_fin").val();
    datosbtnextra="data-depe='"+depe+"' data-serie='"+serie+"' data-subserie='"+subserie+"' data-tpdoc='"+tpdoc+"' data-tpRad='"+tpRad+"' data-usua='"+usuA+"' data-tpads='"+tpAds+"' data-fini='"+fini+"' data-ffin='"+ffin+"' data-btns=2 ";
    //$('#animationload').show();
    $('#processing-modal').modal('show');
    //console.log('fn=usuarios&depe='+depe+'&tpus='+tpUs);
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=rp9&depe=' + depe + '&serie=' + serie + '&subserie=' + subserie + '&tpdoc=' + tpdoc + '&usu=' + usuA + '&fini=' + fini + '&ffin=' + ffin+'&tpAds='+tpAds+'&tpRad='+tpRad
        })
        .then(function (response) {
            // console.log(response);
            
            data = response.data.data;
            total = (data.tramitado * 1) + (data.entramite * 1);
            btnTeS = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="9" data-toggle="modal" data-target="DetEsta" data-id="1" data-btns="2" type="button" ><i class="fa fa-align-justify" data-toggle="tooltip"  data-placement="top"  title="Ver detalles"></i></button>';
            btnEtS = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="9"  data-toggle="modal" data-target="DetEsta" data-id="2" data-btns="2" type="button" ><i class="fa fa-align-justify" data-toggle="tooltip"  data-placement="top"  title="Ver detalles"></i></button>';
            btnToS = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="9" data-toggle="modal" data-target="DetEsta" data-id="T" data-btns="2" type="button" ><i class="fa fa-align-justify" data-toggle="tooltip"  data-placement="top"  title="Ver detalles"></i></button>';
            btnTee = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="9"  data-titulo="'+titulo+'" '+datosbtnextra+' data-id="1" type="button" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel" ><i class="fa fa-table"></i></button> <div id="detXLS1" class="float-right"></div>';
            btnToe = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="9"  data-titulo="'+titulo+'" '+datosbtnextra+' data-id="2" type="button" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"><i class="fa fa-table"></i></button> <div id="detXLS2" class="float-right"></div>';
            btnTep = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="9"  data-titulo="'+titulo+'" '+datosbtnextra+' data-id="T" type="button" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel" ><i class="fa fa-table"></i></button> <div id="detXLST" class="float-right"></div>';
            
            htmls = '<table id="tb_rp" class="table table-bordered table-striped table-hover ">'
            htmls += ' <thead class="thead-dark"><tr><th>#</th><th>Tipo</th><th>Cantidad Radicados</th><th></th></tr></thead>';
            htmls += '<tr><td>1</td><td id="reg1">Finalizado</td><td>' + data.tramitado + '</td><td>' + ' '+ btnTeS+' '+btnTee+'</td></tr>';
            htmls += '<tr><td>2</td><td id="reg2">En tramite</td><td>' + data.entramite + '</td><td>' + ' '+ btnEtS+' '+btnToe+'</td></tr>';
            htmls += '<tr><th colspan=2 id="regT">Total </th><th>' + total + '</th><th>' + btnToS+' '+' '+btnTep+'</th></tr>';
            htmls += '</table>';
            
            setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
            $("#resultado").html(htmls);
            $('[data-toggle="tooltip"]').tooltip();
          //  $('#animationload').fadeOut('slow');
         //   $('#processing-modal').modal('hide');
            $(".btn-generar").prop("disabled", false);
        })
        .catch(function (error) {
            $(".btn-generar").prop("disabled", false);
            $('#animationload').hide();
            if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                $(this).showError('Error en petición', 'Estado del error: ' + error.response.status + '. Mensaje: ' + error.response.data.error);
            }
            //toastr.error(data.message, 'Error al Modificar ');
        });
        
}

$("#resultado").on("click", ".btn-rp1det", function () {
    $('#DetEsta').modal('show');
    $('#DetEsta').show();
    var tpbusq = $(this).data('id');
    var reporte = $(this).data('rep');
    $('#processing-modal').modal('show');
    switch (reporte) {
       /* case 1:
            var datos = $(this).parents("tr").data();
            console.log( datos ); 
            detallesrp(tpbusq,reporte,btns);
            break;*/
        case 9:
            var btns = $(this).data('btns');
            detallesrp9(tpbusq,reporte,btns);
            break;
        case 10:
            var btns = $(this).data('btns');
            detallesrp10(tpbusq,reporte,btns);
            break;
        case 11:
        case 12:
            var btns = $(this).data('btns');
            detallesrp11x12(tpbusq,reporte,btns);
          break;
        default:
            //var datos = $(this).parents("tr").data();
            var btns = $(this).data('btns');
            detallesrp(tpbusq,reporte,btns);
            break;
    }
    $('#processing-modal').modal('hide');
  //  console.log(codigo);

});

$("#resultado").on("click", ".btn-rp1detXLS", function () {
    //$('#DetEsta').modal('show');
    //$('#DetEsta').show();
    $('#processing-modal').modal('show');
    var tpbusq = $(this).data('id');
    var reporte = $(this).data('rep');
    var titulo = $(this).data('titulo');
    var depe =  $(this).data('depe');
    var serie =  $(this).data('serie');
    var subserie =  $(this).data('subserie');
    var tpdoc =  $(this).data('tpdoc');
    var tpRad =  $(this).data('tprad');
    var usuA =  $(this).data('usua');
    var tpAds =  $(this).data('tpads');
	if(typeof tpAds=='undefined'){
	     tpAds = $("#CHKseldep").is(':checked') ? '1' : '0';
	}
    var fini =  $(this).data('fini');
    var ffin =  $(this).data('ffin');
    var btns = $(this).data('btns');
    var tit = $(this).data('tit');
    var depeN = $('#dependencia_busq  option:selected').text();
//    $(this).prop("disabled", true);
$(".btn-rp1detXLS").prop("disabled", true);
    
  //  console.log('#detXLS'+tpbusq);
    $('#detXLS'+tpbusq).html('<img src="../imagenes/loading.gif" />');
    axios({
        method: 'post',
        baseURL: 'repXLS.php',
        data: 'fn=dtrp'+reporte+'&rp=' + reporte + '&depe=' + depe  + '&depeN=' + depeN + '&titulo='+titulo+ '&serie=' + serie + '&subserie=' + subserie + '&tpdoc=' + tpdoc + '&usu=' + usuA + '&fini=' + fini + '&ffin=' + ffin+'&reporte='+reporte+'&tpbusq='+tpbusq+'&tpAds='+tpAds+'&btns='+btns+'&tit='+tit+'&tpRad='+tpRad+'&'
    })
    .then(function (response) {
        $('#processing-modal').modal('hide');
        data = response.data;
        if(data.error=='FAIL'){
            //$(".btn-rp1detXLS").prop("disabled", false);
            $('#detXLS'+tpbusq).html('ERROR: No se Genero el Archivo');
        }
        if(data.url){
            window.open(data.url,'excel_desc'); //excel_desc
            btn="<a href='"+data.url+"' class='btn btn-xs btn-warning'  data-toggle='tooltip' data-placement='bottom'   title='Descargar detalles generado'><i class='fa fa-download'></i> </a>";
            $('#detXLS'+tpbusq).html('Generado '+btn);
            $('[data-toggle="tooltip"]').tooltip();
            setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
        }else $('#detXLS'+tpbusq).html('ERROR: No se Genero el Archivo');
        $(".btn-rp1detXLS").prop("disabled", false);
        
    })
    .catch(function (error) {
        $('#processing-modal').modal('hide');
      $(".btn-rp1detXLS").prop("disabled", false);
      $('#detXLS'+tpbusq).html('ERROR: No se Genero el Archivo');
        if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
            $(this).showError('Error en petición', 'Estado del error: ' + error.response.status + '. Mensaje: ' + error.response.data.error);
        }
        
        //toastr.error(data.message, 'Error al Modificar ');
    });
    
});

function detallesrp(tpbusq,reporte,btns) {
    dt=$('#reg'+tpbusq).text();
    //console.log(dt);
    $('#titDet').html('Detalles '+dt );
    selectIDu = 'selUsuario';
    /*$("#"+selectIDu).empty();
    $("#"+selectIDu ).append('<option value="0">-- Selecione --</option>');*/
    var depe = $("#dependencia_busq").val();
    var serie = $("#selSerie").val();
    var subserie = $("#selSubSerie").val();
    var tpdoc = $("#selTipoDoc").val();
    var tpRad = $("#tipoRadicado").val();
    var usuA = $("#selUsuario").val();
    var tpAds = $("#CHKseldep").is(':checked') ? '1' : '0';
    var fini = $("#fecha_ini").val();
    var ffin = $("#fecha_fin").val();
    //$("#mdRespiues").html('<img src="../imagenes/loading.gif">');
    //$('#animationload').show();
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=dtrp'+reporte+'&depe=' + depe + '&serie=' + serie + '&subserie=' + subserie + '&tpdoc=' + tpdoc + '&usu=' + usuA + '&fini=' + fini + '&ffin=' + ffin+'&reporte='+reporte+'&tpbusq='+tpbusq+'&tpAds='+tpAds+'&btns='+btns+'&tpRad='+tpRad
        })
        .then(function (response) {
  
            data = response.data;
            switch (reporte) {
                case 1:
                    $('#processing-modal').modal('hide');
                    htmls = '<table id="tb_rpDT" class="table table-sm table-bordered table-striped table-hover " style="font-size:10px">'
                    htmls += '<thead class="thead-dark"><tr><th>#</th><th>Radicado</th><th>Fecha Radicacion</th><th>Tipo Documento</th><th>Asunto</th>'+
                             '<th>Usuario</th><th>Proyectado por</th><th>Remitente</th><th>Dignatario</th><th>Dependencia Inicial</th><th>Dependencia Actual</th><th>Usuario Actual</th>'+
                             '<th>Medio de Recpción</th><th>Departamento</th><th>Municipio</th><th>Referenciado o Radicado Padre</th><th>Correo electrónico</th><th>Fecha Digitalización</th><th>Usuario Digitalizador</th><th>Num. Folios</th>'+'</tr></thead>';
                    htmls += '<tbody></tbody></table>';//<th>#</th>
                    $('#processing-modal').modal('hide');
                    $("#mdRespiues").html(htmls);
                    $(document).ready(function() {
                        $('#tb_rpDT').DataTable( {
                            data: data.data,
                            "columns": [
                                { "data": "NUM" },{ "data": "RADI" },{ "data": "RFECH" },{ "data": "TPNOMB"},{ "data": "ASUNTO" },{ "data": "USUAR" },{ "data": "PROYECTO" },
                                { "data": "REM" },{ "data": "DIG" },{ "data": "DEPEI" },{ "data": "DPA" },{ "data": "USUAA" },{ "data": "MREC" },{ "data": "DPTO" },{ "data": "MUNI" },{ "data": "ASOCIADO" },{ "data": "EMAIL" },{ "data": "FECHADIG" },{ "data": "USUD" },
                                 { "data": "FOL" }
                            ],
                            'language': esvar,pageLength: 10,
                        } );
                    } );
                                                  
                    break;
                    case 2:
                        htmls = '<table id="tb_rpDT" class="table table-sm table-bordered table-striped table-hover " style="font-size:10px">'
                        htmls += '<thead class="thead-dark"><tr><th>#</th><th>Radicado</th><th>Fecha Radicación</th><th>Asunto</th><th>Medio Recepción</th><th>Usuario</th>'+
                                 '</tr></thead>';
                        htmls += '<tbody></tbody></table>';//<th>#</th>
                        $("#mdRespiues").html(htmls);
                        $('#processing-modal').modal('hide');
                        $(document).ready(function() {
                            $('#tb_rpDT').DataTable( {
                                data: data.data,
                                "columns": [
                                    { "data": "NUM" }, { "data": "RADI" },{ "data": "RFECH" },{ "data": "ASUNTO" },{ "data": "MEDIO_RECEPCION"},{ "data": "USUARIO"}
                                ],
                                'language': esvar,pageLength: 25,
                            } );
                        } );
                            
                        break;
                    case 3:
                        htmls = 'devueltos: '+ data.DEVUELTOS+' enviados: '+ data.ENVIADOS+' <table id="tb_rpDT" class="table table-sm table-bordered table-striped table-hover " style="font-size:10px">'
                        htmls += '<thead class="thead-dark"><tr><th>#</th><th>Radicado</th><th>Fecha envio</th><th>Numero Envíos</th><th>Devoluciones</th>'+
                                 '</tr></thead>';
                        htmls += '<tbody></tbody></table>';//<th>#</th>
                        $("#mdRespiues").html(htmls);
                        $('#processing-modal').modal('hide');
                        $(document).ready(function() {
                            $('#tb_rpDT').DataTable( {
                                data: data.data,
                                "columns": [
                                    { "data": "NUM" }, { "data": "RADI" },{ "data": "FECH" },{ "data": "ENV" },{ "data": "DEV"}
                                ],
                                'language': esvar,pageLength: 25,
                            } );
                        } );
                            
                        break;
                        case 4:
                            htmls = '<table id="tb_rpDT" class="table table-sm table-bordered table-striped table-hover " style="font-size:10px">'
                            htmls += '<thead class="thead-dark"><tr><th>#</th><th>Radicado</th><th>Usuario Digitalizador</th><th>Observaciones</th><th>Fecha Radicación</th><th>Fecha Digitalización</th><th>Medio Recepción</th><th>Tipo Documento</th>'+
                                     '</tr></thead>';
                            htmls += '<tbody></tbody></table>';//<th>#</th>
                            $("#mdRespiues").html(htmls);
                            $('#processing-modal').modal('hide');
                            $(document).ready(function() {
                                $('#tb_rpDT').DataTable( {
                                    data: data.data,
                                    "columns": [
                                        { "data": "NUM" }, { "data": "RADI" },{ "data": "USUARIO_DIGITALIZADOR" },{ "data": "OBSERVACIONES" },{ "data": "FECHA_RADICACION" },{ "data": "FECHA_DIGITALIZACION" },{ "data": "MEDIO_RECEPCION"},{ "data": "TIPO_DE_DOCUMENTO"}
                                    ],
                                    'language': esvar,pageLength: 25,
                                } );
                            } );
                                
                            break;
                        case 6:
                            htmls = '<table id="tb_rpDT" class="table table-sm table-bordered table-striped table-hover " style="font-size:10px">'
                            htmls += '<thead class="thead-dark"><tr><th>#</th><th>Radicado</th><th>Fecha Radicación</th><th>Tipo Documento</th><th>Asunto</th><th>Usuario</th><th>Remitente</th><th>Dep. Inicial</th><th>Dep. Actual</th><th>Cod. Dep. Actual</th><th>Usuario actual</th><th>Num. Folios</th><th>Num. Anexo</th><th>Desc. Anexo</th><th>Dias Restantes</th>'+
                                     '</tr></thead>';
                            htmls += '<tbody></tbody></table>';//<th>#</th>
                            $("#mdRespiues").html(htmls);
                            $('#processing-modal').modal('hide');
                            $(document).ready(function() {
                                $('#tb_rpDT').DataTable( {
                                    data: data.data,
                                    "columns": [
                                        { "data": "NUM" }, { "data": "RADICADO" },{ "data": "FECHA_RADICADO" },{ "data": "TIPO_DE_DOCUMENTO" },{ "data": "ASUNTO" },{ "data": "USUARIO"},{ "data": "REMITENTE"},{ "data": "DEPENDENCIA_INICIAL"},{ "data": "DEPENDENCIA_ACTUAL"},{ "data": "CODIGO_DEPENDENCIA_ACTUAL"},{ "data": "USUARIO_ACTUAL"},{ "data": "NUMERO_FOLIOS"},{ "data": "NUMERO_ANEXO"},{ "data": "DESCRIPCION_ANEXO"},{ "data": "DIAS_RESTANTES"}
                                    ],
                                    'language': esvar,pageLength: 25,
                                } );
                            } );
                                
                            break;
                        case 7:
                            htmls = '<table id="tb_rpDT" class="table table-sm table-bordered table-striped table-hover " style="font-size:10px">'
                            htmls += '<thead class="thead-dark"><tr><th>#</th><th>Radicado</th><th>Fecha Radicado</th><th>Asunto</th><th>Num. Folios</th><th>Num. Anexo</th><th>Desc. Anexo</th><th>Remitente</th><th>Usuario</th><th>Cod. Dependencia</th><th>Dep. Actual</th><th>Codi. Dep. Actual</th><th>Digitalizador</th><th>Fecha Digitalizacion</th><th>Radicador</th><th>Dependencia Radicador</th><th>Medio</th>'+
                                     '</tr></thead>';
                            htmls += '<tbody></tbody></table>';//<th>#</th>
                            $("#mdRespiues").html(htmls);
                            $('#processing-modal').modal('hide');
                            $(document).ready(function() {
                                $('#tb_rpDT').DataTable( {
                                    data: data.data,
                                    "columns": [
                                        { "data": "NUM" }, { "data": "RADICADO" },{ "data": "FECHA_RADICADO" },{ "data": "ASUNTO" },{ "data": "RADI_NUME_FOLIO"},{ "data": "NUM_ANEXOS"},{ "data": "RADI_DESC_ANEX"},{ "data": "REMITENTE"},{ "data": "USUARIO"},{ "data": "COD_DEPE"},{ "data": "DEPE_CODI_ACTUAL"},{ "data": "DEPE_NOMB_ACTUAL"},{ "data": "DIGITALIZADOR"},{ "data": "FECHA_DIGITALIZACION"},{ "data": "RADICADOR"},{ "data": "DEPENDENCIA_RADICADOR" },{ "data": "MEDIO" }
                                    ],
                                    'language': esvar,pageLength: 25,
                                } );
                            } );
                                
                            break;
            
                default:
                    break;
            }
               
            setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);

            
        })
        .catch(function (error) {
            $('#animationload').hide();
            setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
            if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                $(this).showError('Error en petición', 'Estado del error: ' + error.response.status + '. Mensaje: ' + error.response.data.error);
            }
            //toastr.error(data.message, 'Error al Modificar ');
        });


}

function detallesrp9(tpbusq,reporte,btns) {
    dt=$('#reg'+tpbusq).text();
    //console.log(dt);
    $('#titDet').html('Detalles '+dt );
    selectIDu = 'selUsuario';
    var titulo = $('#tipoEstadistica option:selected').text();
    var depe = $("#dependencia_busq").val();
    var serie = $("#selSerie").val();
    var subserie = $("#selSubSerie").val();
    var tpdoc = $("#selTipoDoc").val();
    var tpRad = $("#tipoRadicado").val();
    var usuA = $("#selUsuario").val();
    var tpAds = $("#CHKseldep").is(':checked') ? '1' : '0';
    var fini = $("#fecha_ini").val();
    var ffin = $("#fecha_fin").val();
    //$("#mdRespiues").html('<img src="../imagenes/loading.gif">');
    $('#animationload').show();
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=dtrp'+reporte+'&depe=' + depe + '&serie=' + serie + '&subserie=' + subserie + '&tpdoc=' + tpdoc + '&usu=' + usuA + '&fini=' + fini + '&ffin=' + ffin+'&reporte='+reporte+'&tpbusq='+tpbusq+'&tpAds='+tpAds+'&btns='+btns
        })
        .then(function (response) {  
           data = response.data;
                htmls = '<table id="tb_rpDT" class="table table-sm table-bordered table-striped table-hover " style="font-size:10px">'
                htmls += '<thead class="thead-dark"><tr><th>Cod. Dep.</th><th>Dependencia</th><th>Mes</th><th>Modalidad para resover</th><th>Canal/Registro</th><th>NURC(Radicado)</th><th>Fecha de Radicacion</th><th>Nombre Peticionario</th>'+
                         '<th>Departameto</th><th>Municipio</th><th>Fecha Asignación Funcionario Respuesta</th><th>Fecha Respuesta del DP</th><th>Tiempo de Respuesta</th><th>NURC Traslado Externos</th>'+
                         '<th>Entidad Traslado</th><th>NURC Respuesta peticionario</th><th>Estado</th><th>Usuario Proyecto</th>'+
                         '</tr></thead>';
                htmls += '<tbody></tbody></table>';//<th>#</th>
                var htmldt;
                $('#processing-modal').modal('hide');
                $("#mdRespiues").html(htmls);
                $(document).ready(function() {
                    $('#tb_rpDT').DataTable( {
                        data: data.data,
                        "columns": [
                            { "data": "DPCLOSE" },{ "data": "DPCLOSEN" },{ "data": "MES" },{ "data": "TPNOMB"},{ "data": "MERC" },{ "data": "RADI" },{ "data": "RFECH" },
                            { "data": "REM" },{ "data": "DEPTO" },{ "data": "MUNI" },{ "data": "FECHASIG" },{ "data": "ENVIO" },{ "data": "DIFFECH" },
                            { "data": "NUMREXT"},{ "data": "NOBRENT" },{ "data": "RESP" },
                             { "data": "EST" },{ "data": "PROY" }
                        ],
                        'language': esvar,pageLength: 15,
                    } );
                } );

                setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
            $('#animationload').hide();
        })
        .catch(function (error) {
            setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
            $('#animationload').hide();
            if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                $(this).showError('Error en petición', 'Estado del error: ' + error.response.status + '. Mensaje: ' + error.response.data.error);
            }
            //toastr.error(data.message, 'Error al Modificar ');
        });


}


function report10() {
    selectIDu = 'selUsuario';
    /*$("#"+selectIDu).empty();
    $("#"+selectIDu ).append('<option value="0">-- Selecione --</option>');*/
    var titulo = $('#tipoEstadistica option:selected').text();
    var idreport=$('#tipoEstadistica').val();
    var depe = $("#dependencia_busq").val();
    var serie = $("#selSerie").val();
    var subserie = $("#selSubSerie").val();
    var tpdoc = $("#selTipoDoc").val();
    var tpRad = $("#tipoRadicado").val();
    var usuA = $("#selUsuario").val();
    var tpAds = $("#CHKseldep").is(':checked') ? '1' : '0';
    var fini = $("#fecha_ini").val();
    var ffin = $("#fecha_fin").val();
    datosbtnextra="data-depe='"+depe+"' data-serie='"+serie+"' data-subserie='"+subserie+"' data-tpdoc='"+tpdoc+"' data-tpRad='"+tpRad+"' data-usua='"+usuA+"' data-tpads='"+tpAds+"' data-fini='"+fini+"' data-ffin='"+ffin+"' data-btns=1 ";
    //$('#animationload').show();
    $('#processing-modal').modal('show');
    //console.log('fn=usuarios&depe='+depe+'&tpus='+tpUs);
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=rp9&depe=' + depe + '&serie=' + serie + '&subserie=' + subserie + '&tpdoc=' + tpdoc + '&usu=' + usuA + '&fini=' + fini + '&ffin=' + ffin+'&tpAds='+tpAds+'&tpRad='+tpRad
        })
        .then(function (response) {
            // console.log(response);
            $('#processing-modal').modal('show');
            data = response.data.data;
            //     console.log(data);
            
            $('#resultado').html(' ');
            total = (data.tramitado * 1) + (data.entramite * 1);
            btnTe = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="'+idreport+'" data-toggle="modal" data-target="DetEsta" data-id="1" data-btns="1" type="button" ><i class="fa fa-align-justify" data-toggle="tooltip"  data-placement="top"  title="Ver detalles"></i></button>';
            btnEt = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="'+idreport+'"  data-toggle="modal" data-target="DetEsta" data-id="2" data-btns="1" type="button" ><i class="fa fa-align-justify" data-toggle="tooltip"  data-placement="top"  title="Ver detalles"></i></button>';
            btnTo = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="'+idreport+'" data-toggle="modal" data-target="DetEsta" data-id="T" data-btns="1" type="button" ><i class="fa fa-align-justify" data-toggle="tooltip"  data-placement="top"  title="Ver detalles"></i></button>';
            btnTeS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="'+idreport+'" data-toggle="modal" data-target="DetEsta" data-id="1" '+datosbtnextra+' type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLS1" class="float-right"></div>';
            btnEtS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="'+idreport+'"  data-toggle="modal" data-target="DetEsta" data-id="2" '+datosbtnextra+' type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLS2" class="float-right"></div>';
            btnToS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="'+idreport+'" data-toggle="modal" data-target="DetEsta" data-id="T" '+datosbtnextra+' type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLST" class="float-right"></div>';
            htmls = '<table id="tb_rp" class="table table-bordered table-striped table-hover ">'
            htmls += ' <thead class="thead-dark"><tr><th>#</th><th>Tipo</th><th>Cantidad Radicados</th><th></th></tr></thead>';
            htmls += '<tr><td>1</td><td id="reg1">Finalizado</td><td>' + data.tramitado + '</td><td><div class=" float-right">' + btnTe+' '+ btnTeS+' </div></td></tr>';
            htmls += '<tr><td>2</td><td id="reg2">En tramite</td><td>' + data.entramite + '</td><td><div class=" float-right">' + btnEt +' '+ btnEtS+' </div></td></tr>';
            htmls += '<tr><th colspan=2 id="regT">Total </th><th>' + total + '</th><th><div class=" float-right">' + btnTo+' '+ btnToS+'</div> </th></tr>';
            htmls += '</table>';
         
            setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
            $("#resultado").html(htmls);
            $('[data-toggle="tooltip"]').tooltip();
            $('#animationload').hide();
            $(".btn-generar").prop("disabled", false);
        })
        .catch(function (error) {
           // $('#animationload').hide();
            
            setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
            $(".btn-generar").prop("disabled", false);
            if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                $(this).showError('Error en petición', 'Estado del error: ' + error.response.status + '. Mensaje: ' + error.response.data.error);
            }
            //toastr.error(data.message, 'Error al Modificar ');
        });
        
}

function report11() {
    selectIDu = 'selUsuario';
    /*$("#"+selectIDu).empty();
    $("#"+selectIDu ).append('<option value="0">-- Selecione --</option>');*/
    var titulo = $('#tipoEstadistica option:selected').text();
    var idreport=$('#tipoEstadistica').val();
    var depe = $("#dependencia_busq").val();
    var serie = $("#selSerie").val();
    var subserie = $("#selSubSerie").val();
    var tpdoc = $("#selTipoDoc").val();
    var tpRad = $("#tipoRadicado").val();
    var usuA = $("#selUsuario").val();
    var tpAds = $("#CHKseldep").is(':checked') ? '1' : '0';
    var fini = $("#fecha_ini").val();
    var ffin = $("#fecha_fin").val();
    datosbtnextra="data-depe='"+depe+"' data-serie='"+serie+"' data-subserie='"+subserie+"' data-tpdoc='"+tpdoc+"' data-tpRad='"+tpRad+"' data-usua='"+usuA+"' data-tpads='"+tpAds+"' data-fini='"+fini+"' data-ffin='"+ffin+"' data-btns=1 ";
    $('#animationload').show();
    $('#processing-modal').modal('show');
    //console.log('fn=usuarios&depe='+depe+'&tpus='+tpUs);
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=rp11&depe=' + depe + '&serie=' + serie + '&subserie=' + subserie + '&tpdoc=' + tpdoc + '&usu=' + usuA + '&fini=' + fini + '&ffin=' + ffin+'&tpAds='+tpAds+'&tpRad='+tpRad
        })
        .then(function (response) {
            // console.log(response);
            data = response.data.data;
            //     console.log(data);
            $('#resultado').html('prueba');
            total = (data.tramitado * 1) + (data.entramite * 1);
            btnTe = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="'+idreport+'" data-toggle="modal" data-target="DetEsta" data-id="1" data-btns="1" type="button" ><i class="fa fa-align-justify" data-toggle="tooltip"  data-placement="top"  title="Ver detalles"></i></button>';
            btnEt = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="'+idreport+'"  data-toggle="modal" data-target="DetEsta" data-id="2" data-btns="1" type="button" ><i class="fa fa-align-justify" data-toggle="tooltip"  data-placement="top"  title="Ver detalles"></i></button>';
            btnTo = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="'+idreport+'" data-toggle="modal" data-target="DetEsta" data-id="T" data-btns="1" type="button" ><i class="fa fa-align-justify" data-toggle="tooltip"  data-placement="top"  title="Ver detalles"></i></button>';
            btnTeS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="'+idreport+'" data-toggle="modal" data-target="DetEsta" data-id="1" '+datosbtnextra+' type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLS1" class="float-right"></div>';
            btnEtS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="'+idreport+'"  data-toggle="modal" data-target="DetEsta" data-id="2" '+datosbtnextra+' type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLS2" class="float-right"></div>';
            btnToS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="'+idreport+'" data-toggle="modal" data-target="DetEsta" data-id="T" '+datosbtnextra+' type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLST" class="float-right"></div>';
            htmls = '<table id="tb_rp" class="table table-bordered table-striped table-hover ">'
            htmls += ' <thead class="thead-dark"><tr><th>#</th><th>Tipo</th><th>Cantidad Radicados</th><th></th></tr></thead>';
            htmls += '<tr><td>1</td><td id="reg1">Finalizado</td><td>' + data.tramitado + '</td><td><div class=" float-right">' + btnTe+' '+ btnTeS+' </div></td></tr>';
            htmls += '<tr><td>2</td><td id="reg2">En tramite</td><td>' + data.entramite + '</td><td><div class=" float-right">' + btnEt +' '+ btnEtS+' </div></td></tr>';
            htmls += '<tr><th colspan=2 id="regT">Total </th><th>' + total + '</th><th><div class=" float-right">' + btnTo+' '+ btnToS+'</div> </th></tr>';
            htmls += '</table>';
            $('#processing-modal').modal('hide');
            $("#resultado").html(htmls);
            $('[data-toggle="tooltip"]').tooltip();
            $('#animationload').hide();
            $(".btn-generar").prop("disabled", false);
            $('#processing-modal').modal('hide');
            setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
        })
        .catch(function (error) {
            $('#animationload').hide();
            setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
            $(".btn-generar").prop("disabled", false);
            if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                $(this).showError('Error en petición', 'Estado del error: ' + error.response.status + '. Mensaje: ' + error.response.data.error);
            }
            //toastr.error(data.message, 'Error al Modificar ');
        });
        
}

function report12() {
    selectIDu = 'selUsuario';
    /*$("#"+selectIDu).empty();
    $("#"+selectIDu ).append('<option value="0">-- Selecione --</option>');*/
    var titulo = $('#tipoEstadistica option:selected').text();
    var idreport=$('#tipoEstadistica').val();
    var depe = $("#dependencia_busq").val();
    var serie = $("#selSerie").val();
    var subserie = $("#selSubSerie").val();
    var tpdoc = $("#selTipoDoc").val();
    var tpRad = $("#tipoRadicado").val();
    var usuA = $("#selUsuario").val();
    var tpAds = $("#CHKseldep").is(':checked') ? '1' : '0';
    var fini = $("#fecha_ini").val();
    var ffin = $("#fecha_fin").val();
    datosbtnextra="data-depe='"+depe+"' data-serie='"+serie+"' data-subserie='"+subserie+"' data-tpdoc='"+tpdoc+"' data-tpRad='"+tpRad+"' data-usua='"+usuA+"' data-tpads='"+tpAds+"' data-fini='"+fini+"' data-ffin='"+ffin+"' data-btns=1 ";
    //$('#animationload').show();
    $('#processing-modal').modal('show');
    //console.log('fn=usuarios&depe='+depe+'&tpus='+tpUs);
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=rp12&depe=' + depe + '&serie=' + serie + '&subserie=' + subserie + '&tpdoc=' + tpdoc + '&usu=' + usuA + '&fini=' + fini + '&ffin=' + ffin+'&tpAds='+tpAds+'&tpRad='+tpRad
        })
        .then(function (response) {
            // console.log(response);
            data = response.data.data;
            //     console.log(data);
            $('#resultado').html('prueba');
            total = (data.tramitado * 1) + (data.entramite * 1);
            btnTe = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="'+idreport+'" data-toggle="modal" data-target="DetEsta" data-id="1" data-btns="1" type="button" ><i class="fa fa-align-justify" data-toggle="tooltip"  data-placement="top"  title="Ver detalles"></i></button>';
            btnEt = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="'+idreport+'"  data-toggle="modal" data-target="DetEsta" data-id="2" data-btns="1" type="button" ><i class="fa fa-align-justify" data-toggle="tooltip"  data-placement="top"  title="Ver detalles"></i></button>';
            btnTo = '<button class="btn btn-xs btn-primary btn-rp1det" data-rep="'+idreport+'" data-toggle="modal" data-target="DetEsta" data-id="T" data-btns="1" type="button" ><i class="fa fa-align-justify" data-toggle="tooltip"  data-placement="top"  title="Ver detalles"></i></button>';
            btnTeS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="'+idreport+'" data-toggle="modal" data-target="DetEsta" data-id="1" '+datosbtnextra+' type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLS1" class="float-right"></div>';
            btnEtS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="'+idreport+'"  data-toggle="modal" data-target="DetEsta" data-id="2" '+datosbtnextra+' type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLS2" class="float-right"></div>';
            btnToS = '<button class="btn btn-xs btn-success btn-rp1detXLS" data-rep="'+idreport+'" data-toggle="modal" data-target="DetEsta" data-id="T" '+datosbtnextra+' type="button" ><i class="fa fa-table" data-toggle="tooltip"  data-placement="top"  title="Descargar detalles en excel"></i></button> <div id="detXLST" class="float-right"></div>';
            htmls = '<table id="tb_rp" class="table table-bordered table-striped table-hover ">'
            htmls += ' <thead class="thead-dark"><tr><th>#</th><th>Tipo</th><th>Cantidad Radicados</th><th></th></tr></thead>';
            htmls += '<tr><td>1</td><td id="reg1">Finalizado</td><td>' + data.tramitado + '</td><td><div class=" float-right">' + btnTe+' '+ btnTeS+' </div></td></tr>';
            htmls += '<tr><td>2</td><td id="reg2">En tramite</td><td>' + data.entramite + '</td><td><div class=" float-right">' + btnEt +' '+ btnEtS+' </div></td></tr>';
            htmls += '<tr><th colspan=2 id="regT">Total </th><th>' + total + '</th><th><div class=" float-right">' + btnTo+' '+ btnToS+'</div> </th></tr>';
            htmls += '</table>';
            $('#processing-modal').modal('hide');;
            setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
            
            $("#resultado").html(htmls);
            $('[data-toggle="tooltip"]').tooltip();
            $('#animationload').hide();
            $(".btn-generar").prop("disabled", false);
        })
        .catch(function (error) {
            $('#animationload').hide();
            setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
            $(".btn-generar").prop("disabled", false);
            if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                $(this).showError('Error en petición', 'Estado del error: ' + error.response.status + '. Mensaje: ' + error.response.data.error);
            }
            //toastr.error(data.message, 'Error al Modificar ');
        });
        
}


function detallesrp10(tpbusq,reporte,btns) {
    dt=$('#reg'+tpbusq).text();
    //console.log(dt);
    $('#titDet').html('Detalles '+dt );
    selectIDu = 'selUsuario';
    /*$("#"+selectIDu).empty();
    $("#"+selectIDu ).append('<option value="0">-- Selecione --</option>');*/
    var depe = $("#dependencia_busq").val();
    var serie = $("#selSerie").val();
    var subserie = $("#selSubSerie").val();
    var tpdoc = $("#selTipoDoc").val();
    var tpRad = $("#tipoRadicado").val();
    var usuA = $("#selUsuario").val();
    var tpAds = $("#CHKseldep").is(':checked') ? '1' : '0';
    var fini = $("#fecha_ini").val();
    var ffin = $("#fecha_fin").val();
    $("#mdRespiues").html('');
    $('#processing-modal').modal('show');
    //$('#animationload').show();
    //console.log('fn=usuarios&depe='+depe+'&tpus='+tpUs);
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=dtrp10&depe=' + depe + '&serie=' + serie + '&subserie=' + subserie + '&tpdoc=' + tpdoc + '&usu=' + usuA + '&fini=' + fini + '&ffin=' + ffin+'&reporte='+reporte+'&tpbusq='+tpbusq+'&tpAds='+tpAds+'&btns='+btns
        })
        .then(function (response) {
  //           console.log(response);
            data = response.data;
        if(btns==1 ){
            htmls = '<table id="tb_rpDT" class="table table-sm table-bordered table-striped table-hover " style="font-size:10px">'
           htmls += '<thead class="thead-dark"><tr><th>Radicado de Entrada</th><th>Fecha Rad. Entrada</th><th>Estado actual Rad. Entrada</th>'+
                     '<th>Asunto</th><th>Remitente</th><th>Dirección</th><th>e-mail</th><th>Telefono</th><th>Departameto</th>'+
                     '<th>Municipio</th><th>Tipo Documental</th><th>Radicado Respuesta</th><th>Fecha Radicado</th><th>Estado Rad. Respuesta</th>'+
                     '<th>Fecha Envío Rad. Respuesta</th><th>Anexo/Asociado </th><th>Funcionario Elaboro</th><th>Firmante</th><th>Dependencia Actual Rad. Entrada</th><th>Funcionario Actual</th>'+
                     '<th>Estado Ultima Transacción</th><th>Dependencia Finalizó</th><th>Funcionario Finalizó</th><th>Comentario Finalización</th><th>Fecha Finalización</th></tr></thead>';

            htmls += '<tbody></tbody></table>';
            var htmldt;
            $("#mdRespiues").html(htmls);
            $('#processing-modal').modal('hide');
            $(document).ready(function() {
                $('#tb_rpDT').DataTable( {
                    data: data.data,
                    "columns": [
                        { "data": "RADI" },{ "data": "RFECH" },{ "data": "EST" },{ "data": "ASUNTO" },{ "data": "REM" },{ "data": "DIR" },{ "data": "EMAIL" }
                        ,{ "data": "TEL" },{ "data": "DEPTO" },{ "data": "MUNI" },{ "data": "TPNOMB"},{ "data": "RESP" },{ "data": "FECHRESP" },{ "data": "ESTRESP" }
                        , { "data": "ENVIO" },{ "data": "ASOCIADO" }, { "data": "PROYI" }, { "data": "FIRMA" },{ "data": "DEPEAC" },{ "data": "USUAA" }, { "data": "ESTU" }, { "data": "DEPEFIN" },{ "data": "USUAFIN" },{ "data": "COMEN" },{ "data": "FTX" }
                    ],
                    'language': esvar,pageLength: 15,
                    "columnDefs": [ {
                        "searchable": true,
                        "orderable": false,
                        "targets": 0
                    } ],
                    "order": [[ 1, 'asc' ]]
                } );
            } );
          }
          setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
//            $('#animationload').hide();
        })
        .catch(function (error) {
            $('#animationload').hide();
            setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
            if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                $(this).showError('Error en petición', 'Estado del error: ' + error.response.status + '. Mensaje: ' + error.response.data.error);
            }
            //toastr.error(data.message, 'Error al Modificar ');
        });
}



function  detallesrp11x12(tpbusq,reporte,btns) {
    dt=$('#reg'+tpbusq).text();
    //console.log(dt);
    $('#titDet').html('Detalles '+dt );
    selectIDu = 'selUsuario';
    /*$("#"+selectIDu).empty();
    $("#"+selectIDu ).append('<option value="0">-- Selecione --</option>');*/
    var depe = $("#dependencia_busq").val();
    var serie = $("#selSerie").val();
    var subserie = $("#selSubSerie").val();
    var tpdoc = $("#selTipoDoc").val();
    var tpRad = $("#tipoRadicado").val();
    var usuA = $("#selUsuario").val();
    var tpAds = $("#CHKseldep").is(':checked') ? '1' : '0';
    var fini = $("#fecha_ini").val();
    var ffin = $("#fecha_fin").val();
    $("#mdRespiues").html('<div id="imageLoad"></div>');
    //$('#animationload').show();
    $('#processing-modal').modal('show');
    
    //console.log('fn=usuarios&depe='+depe+'&tpus='+tpUs);
    axios({
            method: 'post',
            baseURL: 'rest-est.php',
            data: 'fn=dtrp'+reporte+'&depe=' + depe + '&serie=' + serie + '&subserie=' + subserie + '&tpdoc=' + tpdoc + '&usu=' + usuA + '&fini=' + fini + '&ffin=' + ffin+'&reporte='+reporte+'&tpbusq='+tpbusq+'&tpAds='+tpAds+'&btns='+btns
        })
        .then(function (response) {
  //           console.log(response);
            data = response.data;
        if(reporte==11 ){
            htmls = '<table id="tb_rpDT" class="table table-sm table-bordered table-striped table-hover " style="font-size:10px">'
           htmls += '<thead class="thead-dark"><tr><th>Radicado de Salida</th><th>Radicado de Envío</th><th>Asunto</th><th>Fecha Rad. Salida</th><th>Funcionario Elaboró</th>'+
                     '<th>Destinatario</th><th>Dependencia Actual</th><th>Funcionario Actual</th><th>Estado Ultima Transacción</th><th>Tipo Documental</th>'+
                     '<th>Tipo Envío</th><th>Fecha Envío Rad. Salida</th><th>Dependencia Finalizó</th><th>Funcionario Finalizó</th><th>Comentario Finalización</th><th>Fecha Finalización</th><th>Departamento</th><th>Municipio</th><th>Correo Electronico</th></tr></thead>';
            htmls += '<tbody></tbody></table>';
            var htmldt;
            $('#processing-modal').modal('hide');
            $("#mdRespiues").html(htmls);
            $(document).ready(function() {
                $('#tb_rpDT').DataTable( {
                    data: data.data,
                    "columns": [
                        { "data": "RADI" },{ "data": "RESPSAL" },{ "data": "ASUNTO" },{ "data": "RFECH" }, { "data": "PROYI" },{ "data": "REM" },{ "data": "DEPEAC" },{ "data": "USUAA" },
                        { "data": "ESTU"},{ "data": "TPNOMB" },{ "data": "NENV" },{ "data": "ENVIO" }, { "data": "DEPEFIN" },{ "data": "USUAFIN" },{ "data": "COMEN" },{ "data": "FTX" }
                        ,{ "data": "DEPTO" },{ "data": "MUNI" },{ "data": "EMAIL" }
                    ],
                    'language': esvar,pageLength: 15,
                    "columnDefs": [ {
                        "searchable": true,
                        "orderable": false,
                        "targets": 0
                    } ],
                    "order": [[ 1, 'asc' ]]
                } );
            } );
  

        }
        if(reporte==12 ){
            htmls = '<table id="tb_rpDT" class="table table-sm table-bordered table-striped table-hover " style="font-size:10px">'
           htmls += '<thead class="thead-dark"><tr><th>Radicado de Memorando</th><th>Asunto</th><th>Fecha Rad. Memorando</th><th>Funcionario Elaboró</th>'+
                     '<th>Destinatario</th><th>Dependencia Actual</th><th>Funcionario Actual</th><th>Estado Ultima Transacción</th><th>Tipo Documental</th>'+
                     '<th>Dependencia Finalizo</th><th>Funcionario Finalizo</th><th>Radicado Respuesta</th><th>Radicado Referencia / Asociado</th><th>Comentario Finalización</th><th>Fecha Finalización</th></tr></thead>';
            htmls += '<tbody></tbody></table>';
            var htmldt;
            $('#processing-modal').modal('hide');
            $("#mdRespiues").html(htmls);
            $(document).ready(function() {
                $('#tb_rpDT').DataTable( {
                    data: data.data,
                    "columns": [
                        { "data": "RADI" },{ "data": "ASUNTO" },{ "data": "RFECH" },{ "data": "PROYI" },{ "data": "REM" },{ "data": "DEPEAC" },{ "data": "USUAA" },
                        { "data": "ESTU"},{ "data": "TPNOMB" },{ "data": "DEPEFIN" }, { "data": "USUAFIN" },{ "data": "RESP" },{ "data": "ASOCIADO" }, { "data": "COMEN" },{ "data": "FTX" }
                    ],
                    'language': esvar,pageLength: 15,
                    "columnDefs": [ {
                        "searchable": false,
                        "orderable": false,
                        "targets": 0
                    } ],
                    "order": [[ 1, 'asc' ]]
                } );
            } );
  

        }
           
        setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
            //$('#animationload').hide();
        })
        .catch(function (error) {
            //$('#animationload').hide();
            setTimeout(function(){$('#processing-modal').modal('hide');}, 2000);
            if (error.hasOwnProperty('response') && Object.keys(error.response).length > 0) {
                $(this).showError('Error en petición', 'Estado del error: ' + error.response.status + '. Mensaje: ' + error.response.data.error);
            }
            //toastr.error(data.message, 'Error al Modificar ');
        });


}

function tablefun(tbA) {
    console.log(tbA);
    $(document).ready(function() {
    $('#'.tbA).DataTable( {

    })} );
};

    
    function number_format(amount, decimals) {

      amount += ''; // por si pasan un numero en vez de un string
      amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

      decimals = decimals || 0; // por si la variable no fue fue pasada

      // si no es un numero o es igual a cero retorno el mismo cero
      if (isNaN(amount) || amount === 0)
          return parseFloat(0).toFixed(decimals);

      // si es mayor o menor que cero retorno el valor formateado como numero
      amount = '' + amount.toFixed(decimals);

      var amount_parts = amount.split('.'),
          regexp = /(\d+)(\d{3})/;

      while (regexp.test(amount_parts[0]))
          amount_parts[0] = amount_parts[0].replace(regexp, '$1' + '.' + '$2');

      return amount_parts.join('.');
    }

    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        $('#animationload').fadeOut('slow');
    });
