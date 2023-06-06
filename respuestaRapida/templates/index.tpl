<!DOCTYPE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Respuesta Rapida</title>
        <meta   http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link   href="../estilos/jquery.treeview.css" type="text/css"  rel="stylesheet" />
        <link   href="../estilos/jquery-ui.css"       type="text/css"  rel="stylesheet" />
        <script src="../js/libs/jquery-2.0.2.min.js"  type="text/javascript"></script>
        <script src='../js/jquery.form.js'            type="text/javascript" language="javascript"></script>
        <script src='../js/jquery.MetaData.js'        type="text/javascript" language="javascript"></script>
        <script src='../js/jquery.MultiFile.pack.js'  type="text/javascript" language="javascript"></script>
        <script src='../js/jquery.treeview.js'        type="text/javascript" language="javascript"></script>
        <script src='../js/libs/jquery-ui-1.10.4.js'  type="text/javascript" language="javascript"></script>
        <script src="../include/ckeditor/ckeditor.js"></script>
        <script src="../js/functionImage.js"          type="text/javascript" ></script>
        <script src="../js/plugin/dropzone/dropzone.min.js" type="text/javascript" ></script>
        <script src="../js/bootstrap.min.js" type="text/javascript" ></script>

        <link href="../estilos/bootstrap.min.css" rel="stylesheet">
        <!-- font-awesome CSS -->
        <link href="../estilos/font-awesome.css" rel="stylesheet">
        <!-- Bootstrap core CSS -->
        <link href="../estilos/font-awesome.min.css" rel="stylesheet">
        <link href="../estilos/smartadmin-production.css" rel="stylesheet">
        <link href="../estilos/smartadmin-skins.css" rel="stylesheet">
        <link href="../estilos/demo.css" rel="stylesheet">
        <link href="../estilos/siim_temp.css" rel="stylesheet">
        <link href="../radiMail/css/smartadmin-production-plugins.min.css" rel="stylesheet">

        <script language="javascript">


        var auxiliarButtonSubmit = "";
        var auxiliarButtonRadicado = "";


        $(document).ready(function () {

          $('span[ref]').on('click', function(){
            var idString = $(this).attr('ref');
            var textnew  = $( "#" + idString).html();
            $('#idPlantilla').val(idString);            
            CKEDITOR.instances.texrich.setData(textnew);
            $("span[ref]").css({"font-weight":"normal"});
            $("span[ref='" + idString +"']").css({"font-weight":"bold"});
          });

          $('#T7').MultiFile({
            STRING: {
              remove: '<img src="./js/bin.gif" height="16" width="16" alt="x"/>'
            },
            list: '#T7-list'
          });

          $("#browser").treeview();

          $('#form1').submit(function(e){ 
              
              if(auxiliarButtonSubmit == 'true') {
                  
                  e.preventDefault();              
                  var editor = CKEDITOR.instances['texrich'].getData();
                  $('#texrich').val(editor);

                  var $form = $(this);
                  var serializedData = $form.serialize();

                  request = $.ajax({
                          url: "../respuestaRapida/previsualizar_anexo.php",
                          type: "post",
                          data: serializedData
                      });

                      request.done(function (response, textStatus, jqXHR){  
                          if(response.includes("Ha ocurrido un error previsualizando"))  {
                            alert('Ha ocurrido un error previsualizando el documento. Use el boton "Grabar como Anexo" para guardarlo y posteriormente use el visor de documentos para visualizarlo.');
                          } else {                      
                            var urlPfr = response.replaceAll("<hr>", "");
                            urlPfr = urlPfr.trim();
                              window.open('','','width=950,height=650,toolbar=no,menubar=no,resizable=yes').document.write(urlPfr);
                          }
                      });

                      request.fail(function (jqXHR, textStatus, errorThrown){
                          alert('Ha ocurrido un error previsualizando el documento. Use el boton "Grabar como Anexo" para guardarlo y posteriormente use el visor de documentos para visualizarlo.');
                      });


              } else {

                  var tipo_radicado = $('#tipo_radicado_id').val();

                  if(tipo_radicado == 6) {
                      var textFromEditor = CKEDITOR.instances['texrich'].getData();
                      if(!textFromEditor.includes("RA_NOTI_S")) {
                           e.preventDefault(); 
                           alert('Por favor verificar que la plantilla tiene la variable de combinación RA_NOTI_S dentro del documento para poder continuar.');
                           
                      }
                  }

                  if(auxiliarButtonRadicado == 'true') {
                      var debeFirmar = $('#debeFirmar').val();
                      if(debeFirmar == 'NO') {
                          let confirmAction = confirm("Al parecer este radicado debe ser firmado por otro funcionario. ¿Está seguro de que desea firmarlo?");
                          if (!confirmAction) {
                            e.preventDefault(); 
                          } 
                      }                      
                  }                  

              }
          });

          $('#form2').submit(function(e){

            var seg1    = true;
            var texcont =CKEDITOR.instances['texrich'].getData();

            if($('#nivel').val() === ''){
              alert('Selecciona una carpeta');
              seg1 = false;
            };

            if($('#nombre').val() === ''){
              alert('Escribe un nombre');
              seg1 = false;
            };

            if(!seg1){
              e.preventDefault();
              e.stopPropagation();
            }else{
              $('<input />').attr('type', 'hidden')
                .attr('name', 'contplant')
                .attr('value', texcont)
                .appendTo('#form2');
            }
          });

          $('#form3').submit(function(e){
            var seg2 = false;

            $("input[name='planaborrar[]']:checked").each(function (){
                seg2 = true;
            });

            if(!seg2){
              alert('Selecciona una plantilla');
              e.preventDefault();
              e.stopPropagation();
            };
          });
        });

        function valFo(el){
          var result = true;
          var destin = el.destinatario.value;
          var salida = destin.split(";");

          if (destin == ""){
            alert('El campo destinatario es requerido');
            el.destinatario.focus();
            result = false;
          };

          for(i = 0; i < salida.length; i++){
            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(salida[i])){
              result = true;
            }else{
              alert('El destinatario es incorrecto:  ' + salida[i]);
              el.destinatario.focus();
              result = false;
              break;
            }
          }


          return result;
        }

        function setAuilixar(info, radDev, rad) {
             auxiliarButtonSubmit = info;
             auxiliarButtonRadicado = rad;
             if(radDev != 'false')
                $('#radicadoDevuelto').val("true");
        }





      </script>

      <style type="text/css">

            HTML, BODY{
                font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
                margin: 0px;
                height: 100%;
            }

            #load{
                position:absolute;
                z-index:1;
                border:3px double #999;
                background:#f7f7f7;
                width:300px;
                height:300px;
                margin-top:-150px;
                margin-left:-150px;
                top:50%;
                left:50%;
                text-align:center;
                line-height:300px;
                font-family: verdana, arial,tahoma;
                font-size: 14pt;
            }

            img {
                border: 0 none;
            }

            .MultiFile-label{
                float: left;
                margin: 3px 15px 3px 3px;
            }

            .linkCargar{
                background: url(../estilos/images/flechaAzul.gif) no-repeat;
                cursor: pointer;
                padding-bottom: 17px;
                padding-left: 17px;
            }

            .filetree span[ref]:hover {
              cursor: pointer;
            }

            .dropzone{
               min-height: 150px;
               margin: 15px;
            }
        </style>
      <title>..:: Generacion de Documentos en Linea ::..</title>
    </head>

    <body>
    <!--{foreach key=idCarpeta item=carpeta from=$carpetas}-->
      <!--{foreach key=id item=archivo from=$carpeta}-->
          <span id='<!--{$archivo.id}-->' style="display:none;">
            <!--{$archivo.ruta}-->
          </span>
      <!--{/foreach}-->
    <!--{/foreach}-->

    <div id="load" style="display:none;">Enviando.....</div>
    <div class="container">

        <div class="row col-xs-12 col-md-3">
          <div class="well">
              <ul id="browser" class="filetree" style="font-size: 10px;">
                  <div  style="line-height: 30px;"> Administraci&oacute;n de plantillas </div>
                  De click en la plantilla que desea cargar:

                  <form id="form3" name="form3" method="post" enctype="multipart/form-data"
                    action='../respuestaRapida/procPlantilla.php?<!--{$sid}-->'>
                  <!--{foreach key=idCarpeta item=carpeta from=$carpetas}-->
                    <li><span class="folder"><!--{$idCarpeta}--></span>
                      <ul>
                        <!--{foreach key=id item=archivo from=$carpeta}-->
                        <li>
                          <span class="file">
                            <!--{if $archivo.show == true}-->
                                <input type="checkbox" name="planaborrar[]" value="<!--{$archivo.id}-->">
                            <!--{/if}-->
                            <span ref="<!--{$archivo.id}-->" style="margin-left: 9px;">
                                <a><!--{$archivo.nombre}--></a>
                            </span>
                          </span>
                        </li>
                        <!--{/foreach}-->
                      </ul>
                    </li>
                  <!--{/foreach}-->
                  <br>
                   <div class="form-group">
                        <label for="delPlant">
                          Seleccione un plantilla para ser borrada
                        </label>
                        <input type="submit" id="delPlant" name="delPlant"
                               class="btn btn-success btn-xs"
                               value="Borrar"/>

                  </div>
                  </form>
              </ul>

              <H3>Guardar plantilla</h3>

              <form id="form2" name="form2" method="post" enctype="multipart/form-data"
                  action='../respuestaRapida/procPlantilla.php?{$sid}'>
                <div class="form-group">
                  <label for="nombre">
                    Nombre plantilla:
                  </label>
                  <input  class="form-control" type="text" name="nombre" id="nombre"/>
                </div>
                <div class="form-group">
                  <label for="nombre">
                    Ubicación de la plantilla:
                  </label>
                  <select class="form-control" name="nivel" id="nivel">
                      <option value="">Selecciona una Carpeta</option>
                      <!--{section name=nr loop=$perm_carps}-->
                      <option value="<!--{$perm_carps[nr].codigo}-->"><!--{$perm_carps[nr].nombre}--></option>
                      <!--{/section}-->
                  </select>
                </div>
                <input type="submit" name="plantillas" value="Enviar" class="btn btn-info btn-xs">
              </form>

          </div>
        </div>

        <div class="row col-xs-12 col-md-9">
          <div class="well">
            <form name="form1" id="form1"
                  method="post" enctype="multipart/form-data"                  
                  onsubmit="return valFo(this)">

                <input type="hidden" name="usuanomb"   value='<!--{$usuanomb}-->' />
                <input type="hidden" name="usualog"    value='<!--{$usualog}-->'  />
                <input type="hidden" name="editar"     value='<!--{$editar}-->'   />
                <input type="hidden" name="radPadre"   value='<!--{$radPadre}-->' />
                <input type="hidden" name="usuacodi"   value='<!--{$usuacodi}-->' />
                <input type="hidden" name="depecodi"   value='<!--{$depecodi}-->' />
                <input type="hidden" name="codigoCiu"  value='<!--{$codigoCiu}-->'/>
                <input type="hidden" name="nurad"      value='<!--{$nurad}-->'/>
                <input type="hidden" name="codigoCiu"  value='<!--{$codigoCiu}-->'/>
                <input type="hidden" name="codigoCiu"  value='<!--{$codigoCiu}-->'/>
                <input type="hidden" name="rutaPadre"  value='<!--{$rutaPadre}-->'/>
                <input type="hidden" name="anexo"      value='<!--{$anexo}-->'    />
                <input type="hidden" name="anex_tipo_envio"      value='<!--{$anex_tipo_envio}-->'    />
                <input type="hidden" name="TIPOS_RADICADOS"      value='<!--{$TIPOS_RADICADOS}-->'    />
                <input type="hidden" name="expAnexo"      value='<!--{$expAnexo}-->'    />
                <input type="hidden" name="puedeRadicar"      value='<!--{$puedeRadicar}-->'    />
                <input type="hidden" name="debeFirmar" id="debeFirmar" value='<!--{$debeFirmar}-->'    />
                <input type="hidden" name="idPlantilla"   id="idPlantilla"    value='0'    />
                <input type="hidden" name="radicadoDevuelto"   id="radicadoDevuelto"    value='false'    />
                <div class="row">
                    <div class="col-xs-12 col-md-4">
                    <!--{if !$rad_salida }-->
                        <div class="form-group">
                          <label>
                            Radicar como
                          </label>
                          <select name="tipo_radicado" id="tipo_radicado_id" class="form-control">
                          
                              <!--{foreach from=$TIPOS_RADICADOS key=TIPO item=VALOR}-->
                                <!--{if $rad_sgd_trad_cod == 3 && $TIPO == 1 }-->
                                  <option value="<!--{$TIPO}-->"><!--{$VALOR}--></option>
                                <!--{/if}-->
                                <!--{if $rad_sgd_trad_cod == 2 && $TIPO == 1 }-->
                                  <option value="<!--{$TIPO}-->"><!--{$VALOR}--></option>
                                <!--{/if}-->  
                                <!--{if $rad_sgd_trad_cod == 2 && $TIPO == 3 }-->
                                  <option value="<!--{$TIPO}-->"><!--{$VALOR}--></option>
                                <!--{/if}-->                                

                                <!--{if $TIPO==$rad_sgd_trad_cod}-->
                                  <option value="<!--{$TIPO}-->" selected=selected><!--{$VALOR}--></option>
                                <!--{/if}-->
                              <!--{/foreach}-->
    
                          </select>
                        </div>
                    <!--{else}-->
                        <div class="form-group">
                          <label>
                            Radicado No <!--{$rad_salida}--> <h6> <!--{$fecha_rad_salida}--></h6>
                          </label>
                          <input type="hidden" class="form-group"
                                 value="<!--{$tipo_radicado}-->"
                                 name="tipo_radicado"/>
                        </div>
                    <!--{/if}-->
                    <!--{if $esNotificacion}-->
                        <div class="form-group" style="opacity: 0.5; pointer-events: none;">
                          <label> Medio de envio </label>
                          <select name="anex_tipo_envio" class="form-control"></select>
                        </div>
                    <!--{else}-->
                        <div class="form-group">
                          <label> Medio de envio </label>
                          <select name="anex_tipo_envio" class="form-control">
                            <option value="2" <!--{if $anex_tipo_envio==2}--> selected <!--{/if}--> >Envio Solo a Correo Electr&oacute;nico</option>
                          </select>
                        </div>
                    <!--{/if}--> 
                    </div>


                    <div class="col-xs-12 col-md-4">
                        <div class="form-group">
                          <label>
                            Correos Electronicos a Enviar
                          </label>
                          <input id="mailsl"
                                 value="<!--{$mails}-->"
                                 class="form-control"
                                 name="mailsl"/>
                          <input type="hidden" id="mails" name="mails"
                                 rows="2" cols="60" value="<!--{$mails}-->"  >

                            <!--{if $MOSTRAR_ERROR}-->
                            <strong>
                              Debe seleccionar un tipo de radicado
                            </strong>
                            <!--{/if}-->
                        </div>
                        <div class="center-block">
                            <!--{if $GUARDAR_RADICADO}-->
                              <!--<input type="submit" name="Button" value="Guardar Cambios" class="btn btn-primary" onclick="setAuilixar('false'); javascript: form.action='../respuestaRapida/accion_radicar_anexar.php';"/> -->
                            <!--{if $estadoAnexo == 2 && !$esNotificacion}-->      
                                  <input type="submit" name="Button" value="Guardar Cambios" class="btn btn-primary" onclick="setAuilixar('false', 'true', 'false'); javascript: form.action='../respuestaRapida/accion_radicar_anexar.php';"/>                                  
                            <!--{/if}-->                                                           
                            <!--{else}-->
                                <!--{if $anexo}-->
                                 <!--{if $firma_usuario == true}-->
                                        <input type="submit" name="Button" id="btn-radicar" value="Radicar" class="btn btn-primary" onclick="setAuilixar('false', 'false', 'true'); javascript: form.action='../respuestaRapida/accion_radicar_anexar.php';"/>
                                  <!--{/if}-->
                                  <input type="submit" name="Button" value="Grabar como Anexo" class="btn btn-success" onclick="setAuilixar('false', 'false', 'false'); javascript: form.action='../respuestaRapida/accion_radicar_anexar.php';"/>
                                <!--{else}-->
                                  <input type="submit" name="Button" value="Grabar como Anexo" class="btn btn-success" onclick="setAuilixar('false', 'false', 'false'); javascript: form.action='../respuestaRapida/accion_radicar_anexar.php';"/>
                                <!--{/if}-->
                            <!--{/if}-->

                        </div>
                        <br/>

                          <!--{if $PERM_FIRMA}-->
                            <span class="badge badge-warning">Firma Activa</span>
                          <!--{/if}-->
                    </div>


                    <div class="col-xs-12 col-md-4">
                        <label>
                          Expediente:
                        </label>
                        <!--{if  empty($expedientes) }-->
                        El radicado padre no esta incluido en un expediente
                        <!--{else}-->
                        <select name="expAnexo" class="form-control">
                          <!--{foreach key=expediente item=exps from=$expedientes}-->
                          <option value="<!--{$exps}-->" ><!--{$exps}--></option>
                          <!--{/foreach}-->
                        </select>
                        <!--{/if}-->
                    </div>
                </div>
                   <!--{if $estadoAnexo <> 0 && !$esNotificacion && rad_sgd_trad_cod != 3}-->
                    <!--{$tablaHtmlDestinatarios}-->
                    <input type="hidden" name="id_dre" value="<!--{$idDre}-->">
                 <!--{/if}-->   


                <!--{if $esNotificacion}-->
                <span>
                  <b>Metadatos:</b> Campos a ser reemplazados al radicar el documento: Fecha del radicado:  F_RAD_S, DIA_S, MES_S y ANHO_S, N&uacute;mero de radicado: RAD_S, Nombre del firmante: USUA_NOMB_S, Dependencia del firmante: DEPE_NOMB_S.
                </span>
                <!--{else}-->
                <span>
                  <b>Metadatos:</b> Campos a ser reemplazados al generar el documento: Fecha del radicado:  F_RAD_S , Radicado de salida: RAD_S, Dignatario:  DIG_NATARIO*, Referencia: REF_ERENCIA.
                </span>
                <!--{/if}-->
                <br>
                <span>
                        <!--{if !$GUARDAR_RADICADO}-->
                        <input type="submit" name="Button" id="btn_previsualizar" value="Previsualizar" class="btn btn-success" onclick="setAuilixar('true', 'false', 'false'); javascript: form.action='../respuestaRapida/previsualizar_anexo.php';"/>
                        <!--{/if}-->
                </span>        

                <!--{if $rad_salida}-->
                <h3>Anexos del radicado de salida</h3>
                <div class="dropzone"
                    action="../uploadFiles/uploadAnex.php?Realizar=Realizar&valRadio=<!--{$rad_salida}-->">
                </div>


                <table border="0" width="100%" align="center"  class="table" >
                  <tr>
                    <td>Código </td>
                    <td>Descripción </td>
                  </tr>
                  <!--{foreach key=anex_codigo item=anexo from=$anexSal}-->
                  <tr>
                    <td>
                      <span>
                        <a href="#"
                           onClick="funlinkArchivo('<!--{$anexSal[$anex_codigo]}-->', '..' );" >
                          <!--{$anexSal[$anex_codigo]}-->
                        </a>
                      </span>
                    </td>
                    <td>
                      <span>
                        <!--{$desc_anexSal[$anex_codigo] }-->
                      </span>
                    </td>
                  </tr>
                  <!--{/foreach}-->
                </table>

                <!--{/if}-->
                
                <textarea id="texrich" name="respuesta"
                          value="" height="90%"><!--{$asunto}--></textarea>
                <script>
                  // Replace the <textarea id="editor1"> with a CKEditor
                  // instance, using default configuration.
                  CKEDITOR.config.height = '700';
                  CKEDITOR.replace( 'texrich');
                </script>

            </form>

          </div>
        </div>

<!-- Modal contraseña -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Para continuar, digite su contraseña</h4>
      </div>
      <form class="form-horizontal" id="frm-pass">
        <div class="modal-body">

            <div class="form-group">
              <label for="inputPassword" class="col-sm-2 control-label">Contraseña</label>
              <div class="col-sm-10">
                <input type="password" class="form-control" id="inputPassword" placeholder="Contraseña">
              </div>
            </div>
            <p class="text-center" id="mdl-error"></p>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button id="continuar" type="submit" class="btn btn-primary">Continuar</button>
        </div>
        </form>
      </div>
  </div>
</div>
<script>
$('#frm-pass').submit(function(event) {
  event.preventDefault();
  $.post('index.php?a=login',
    {pass: $('#inputPassword').val()}, null,'json')
  .done(function(data) {
    if (data.error == null) {
      $('#btn-radicar').click();
    }
    else {
      $('#mdl-error').text(data.error);
    }
  });
});
$('#myModal').on('hidden.bs.modal', function (e) {
  $('#inputPassword').val('');
  $('#mdl-error').text('');
})
</script>

</body>
</html>
