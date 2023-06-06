<!DOCTYPE>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title> ..:: Respuesta Rapida ::..</title>
        <meta   http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script src="../js/libs/jquery-2.0.2.min.js"  type="text/javascript"></script>
        <link href="../estilos/bootstrap.min.css" rel="stylesheet">

        <script language="javascript">

        $(document).ready(function () {
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
        };
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

            .jumbotron {
              top: 50%;
              background-color: white;
            }
        </style>
      <title>..:: Generacion de Documentos ::..</title>
    </head>

    <body>
        <div id="load" style="display:none;">Enviando.....</div>
        <form id="form1" name="form1" class="smart-form" method="post" enctype="multipart/form-data" action='../respuestaRapida/sendMail.php?<!--{$sid}-->' onsubmit="return valFo(this)">
            <input type="hidden" name="usuanomb"   value='<!--{$usuanomb}-->'/>
            <input type="hidden" name="usualog"    value='<!--{$usualog}-->'/>
            <input type="hidden" name="editar"     value='<!--{$editar}-->'/>
            <input type="hidden" name="radPadre"   value='<!--{$radPadre}-->'/>
            <input type="hidden" name="radicado"   value='<!--{$radicado}-->'/>
            <input type="hidden" name="nurad"      value='<!--{$nurad}-->'/>
            <input type="hidden" name="usuacodi"   value='<!--{$usuacodi}-->'/>
            <input type="hidden" name="depecodi"   value='<!--{$depecodi}-->'/>
            <input type="hidden" name="codigoCiu"  value='<!--{$codigoCiu}-->'/>
            <input type="hidden" name="codigoCiu"  value='<!--{$codigoCiu}-->'/>
            <input type="hidden" name="codigoCiu"  value='<!--{$codigoCiu}-->'/>
            <input type="hidden" name="rutaPadre"  value='<!--{$rutaPadre}-->'/>
            <input type="hidden" name="anexo"  value="<!--{$anexo}-->"/>
            <input type="hidden" name="codAnexo"  value="<!--{$anexo}-->"/>
            <input type="hidden" name="path_anex"  value="<!--{$path_anex}-->"/>

        <div class="container">

          <div class="row col-xs-12 col-md-6">
            <div class="well">
              <div class="form-group">
                <label for="delPlant">
                  Correos Electronicos a Enviar
                </label>
                <textarea type="text" id="email"
                          name="email"
                          class="form-control"
                          rows="2"
                          cols="50"><!--{$email}--></textarea>
              </div>

              <label>Radicado a enviar:</label>
              <span ><a href="#"
                        onClick="funlinkArchivo('<!--{$nurad }-->', '..' );">
                        <!--{$nurad }-->
                    </a>
              </span>
            </div>
          </div>

          <div class="row col-xs-12 col-md-6  ">
            <div class="text-center jumbotron">
                <img style="width:120px;" src="<!--{$logoEntidad}-->" valign="middle">
                <input type="submit" name="sendMail"
                       value="Enviar Correo" class="btn btn-primary"/>
            </div>
          </div>
          <div class="row col-xs-12 col-md-12">
            <div class="well">
                <table border="0" width="100%" align="center"  class="table" >
                  <tr>
                    <td></td>
                    <td>Código</td>
                    <td>Descripción</td>
                  </tr>

                  <!--{foreach key=anex_codigo item=anexo from=$anex}-->
                  <!--{if in_array($anex[$anex_codigo],$adjuntosAnex)}-->
                  <tr>
                    <td>
                      <input type="checkbox" name="anex_codigo[<!--{$anex[$anex_codigo]}-->]" id="anex_codigo" value="anexCodigo" checked disabled />
                    </td>
                    <td>
                      <span ><a href="#" onClick="funlinkArchivo('<!--{$anex[$anex_codigo]}-->', '..' );"><!--{$anex[$anex_codigo]}--></a></span>
                    </td>
                    <td>
                      <span ><!--{$desc_anex[$anex_codigo]}--></span>
                    </td>
                  </tr>
                  <!--{/if}-->
                  <!--{/foreach}-->
                </table>
            </div>
          </div>
        </div>
        </form>
    </body>

    <script>
    jQuery(window).bind(
        "beforeunload",
        function() {
            window.parent.opener.$.fn.cargarPagina('./lista_anexos.php', 'tabs-c');
        }
    )
    </script>

</html>
