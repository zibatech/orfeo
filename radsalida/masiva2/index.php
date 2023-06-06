<?php

session_start();
$ruta_raiz = "../..";
include_once $ruta_raiz."/include/tx/sanitize.php";

foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}

if(!isset($_SESSION['dependencia']))	include "$ruta_raiz/rec_session.php";

$krd                = $_SESSION["krd"];
$dependencia        = $_SESSION["dependencia"];
$usua_doc           = $_SESSION["usua_doc"];
$codusuario         = $_SESSION["codusuario"];

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
if (!$db)	$db = new ConnectionHandler($ruta_raiz);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <script src="../../include/ckeditor/ckeditor.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Carga archivo de combinación</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form action="procesar.php" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="listado">Listado</label>
                            <input type="file" name="listado" id="listado">
                            <p class="help-block">Archivo csv</p>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="anexos">Anexos</label>
                            <input type="file" name="anexos" id="anexos">
                            <p class="help-block">Archivo zip con los anexos</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-info" role="alert">
                                <strong>Notas</strong>
                                Para relacionar anexos debe agregar una columna llamada <strong>*ANEXOS_DIR*</strong> la cual debe contener el nombre exacto del directorio dentro del zip de anexos.
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <textarea id="texrich" name="respuesta">
                                <p><strong>Bogot&aacute;, Miercoles, <?=date('d/m/Y') ?></strong><br />
                                <br />
                                <br />
                                Se&ntilde;or(a)<br />
                                <strong>*NOMBRE*</strong><br />
                                *CORREO*<br />
                                &nbsp;</p>
                            </textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" style="margin-top: 10px;">
                            <button type="button" id="verificar" class="btn btn-default">Verificar</button>
                            <button type="submit" id="verificar" class="btn btn-primary">Cargar</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Campos tomados en cuenta para crear el radicado.</h4>
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <td>Campo</td>
                                        <td>Descripción</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>*CUENTA_I*</td>
                                        <td>Referencia</td>
                                    </tr>
                                    <tr>
                                        <td>*MEDIO_RECEPCION*</td>
                                        <td>1 = Personal, 2 = Correo certificado, 3 = PQRD Web, 4 = Correo electrónico, 5 = Chat, 6 = Telefónico</td>
                                    </tr>
                                    <tr>
                                        <td>*DESCRIPCION_ANEXOS*</td>
                                        <td>Descripción anexos</td>
                                    </tr>
                                    <tr>
                                        <td>*NO_FOLIOS*</td>
                                        <td>Número de folios</td>
                                    </tr>
                                    <tr>
                                        <td>*NIVEL_SEGURIDAD*</td>
                                        <td>0 = Público, 1 = Reservado, 2 = Clasificado</td>
                                    </tr>
                                    <tr>
                                        <td>*TDOC_CODI*</td>
                                        <td>Clasificación previa</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <td>Campo</td>
                                        <td>Descripción</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>*GUIA*</td>
                                        <td>Número de guía</td>
                                    </tr>
                                    <tr>
                                        <td>*FECHA_RADICACION*</td>
                                        <td>Fecha radicación ej: 2020-10-19 10:30:00</td>
                                    </tr>
                                    <tr>
                                        <td>*CODIGO_DEPENDENCIA*</td>
                                        <td>Código de la dependencia</td>
                                    </tr>
                                    <tr>
                                        <td>*NO_ANEXOS*</td>
                                        <td>Número de anexos</td>
                                    </tr>
                                    <tr>
                                        <td>*CODIGO_DEPENDENCIA*</td>
                                        <td>Código de la dependencia</td>
                                    </tr>
                                    <tr>
                                        <td>*ASUNTO*</td>
                                        <td>Asunto</td>
                                    </tr>
                                    <tr>
                                        <td>*TIPO*</td>
                                        <td>1 = Salida, 2 = Entrada, 3 = Salida</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Verifica los datos que se van a cargar</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="prev" class="table table-striped">
                                <thead>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Replace the <textarea id="editor1"> with a CKEditor
        // instance, using default configuration.
        CKEDITOR.config.height = '400';
        CKEDITOR.replace( 'texrich');
        $(function(){
            $('#listado').on('change', function(e) {
                if (!window.FileReader ) {
                    return alert('No es soportada la previsualización de archivos en su navegador.');
                } else {
                    var fileReader = new FileReader();
                    fileReader.onload = function () {
                        var data = fileReader.result;  // data <-- in this var you have the file data in Base64 format
                        var resultados = data.split("\n");

                        size = 0;
                        var title = [];
                        var dataset = [];
                        $.each(resultados, function(i, row) {
                            var columns = row.split(";");
                            if(i == 0) {
                                title = columns.map(function(col) {
                                    return {'title': col};
                                });

                                size = title.length;
                            } else {
                                if(columns.length == size) {
                                    dataset.push(columns);
                                }
                            }
                        });

                        $('#prev').DataTable({
                            data: dataset,
                            columns: title,
                            scrollX: true
                        });

                        $('#modal').modal('show');
                    };
                    fileReader.readAsText($('#listado').prop('files')[0], 'utf-8');
                }
            });
  
            $('#verificar').on('click', function(e) {
                $('#modal').modal('show');
            });
        });
    </script>
</body>
</html>