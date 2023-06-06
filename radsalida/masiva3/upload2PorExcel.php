<?php
/**
* @author Jairo Losada   <jlosada@gmail.com>
* @author Cesar Gonzalez <aurigadl@gmail.com>
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
* @copyright

SIIM2 Models are the data definition of SIIM2 Information System
Copyright (C) 2013 Infometrika Ltda.



This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
session_start();
$ruta_raiz = "../..";
include_once $ruta_raiz."/include/tx/sanitize.php";

foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}

if(!isset($_SESSION['dependencia']))  include "$ruta_raiz/rec_session.php";

$krd                = $_SESSION["krd"];
$dependencia        = $_SESSION["dependencia"];
$usua_doc           = $_SESSION["usua_doc"];
$codusuario         = $_SESSION["codusuario"];

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
include_once("$ruta_raiz/include/combos.php");

if (!$db) $db = new ConnectionHandler($ruta_raiz);
//if (!defined('ADODB_FETCH_ASSOC'))  define('ADODB_FETCH_ASSOC',2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

/**
 * Retorna la cantidad de bytes de una expresion como 7M, 4G u 8K.
 *
 * @param char $var
 * @return numeric
 */
function return_bytes($val){
    $val = trim($val);
  $ultimo = strtolower($val{strlen($val)-1});
  switch($ultimo){
      // El modificador 'G' se encuentra disponible desde PHP 5.1.0
    case 'g': $val *= 1024;
    case 'm': $val *= 1024;
    case 'k': $val *= 1024;
  }
  return $val;
}

//Start::seleccion de plantillas
$sql21       ="SELECT
                ID,
                PLAN_PLANTILLA,
                PLAN_NOMBRE,
                PLAN_FECHA,
                DEPE_CODI,
                USUA_CODI,
                PLAN_TIPO
              FROM
                SGD_PLAN_PLANTILLAS";

$plant = $db->conn->Execute($sql21);
$arrayplantillas = [];
while(!$plant->EOF){
  $arrayplantillas [] = $plant->fields;
  $plant->MoveNext();
}
//END::seleccion de plantillas

//Start::divipola departamentos
$sql22       ="SELECT
                DPTO_NOMB
              FROM
                DEPARTAMENTO";

$plant = $db->conn->Execute($sql22);
$arraydepartamentos = [];
while(!$plant->EOF){
  $arraydepartamentos [] = $plant->fields['DPTO_NOMB'];
  $plant->MoveNext();
}
//END::seleccion de plantillas

//Start::divipola municipios
$sql23       ="SELECT
                MUNI_NOMB
              FROM
                MUNICIPIO";

$plant = $db->conn->Execute($sql23);
$arraymunicipios = [];
while(!$plant->EOF){
  $arraymunicipios [] = $plant->fields['MUNI_NOMB'];
  $plant->MoveNext();
}
//END::seleccion de plantillas
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script src="https://use.fontawesome.com/65fc9a6f3f.js"></script>
  <?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
  <script src="../../include/ckeditor/ckeditor.js"></script>
  <script src="../../include/xlsx/jszip.js"></script>
  <script src="../../include/xlsx/xlsx.js"></script>

  <style type="text/css">
    /* [FULL SCREEN SPINNER] */
    #spinner-back, #spinner-front {
      position: fixed;
      width: 100vw;
      transition: all 1s;
      visibility: hidden;
      opacity: 0;
    }
    #spinner-back {
      z-index: 998;
      height: 100vh;
      background: rgba(0, 0, 0, 0.7);
    }
    #spinner-front {
      z-index: 999;
      color: #fff;
      text-align: center;
      margin-top: 50vh;
      transform: translateY(-50%);
    }
    #spinner-back.show, #spinner-front.show {
      visibility: visible;
      opacity: 1;
    }
  </style>
</head>
<body>
  <div id="spinner-back"></div>
  <div id="spinner-front">
    <img src="https://www.v4software.com/Admin/Tpl/V4admin/Public/image/ajax-loaders/ajax-loader-no-color.gif"/><br>
    Cargando...
  </div>
  <script language="JavaScript" type="text/JavaScript">
    function validar() {
      archDocto = document.formAdjuntarArchivos.archivoPlantilla.value;
      codserie  = document.getElementsByName("codserie")[0].value;
      codsubser = document.getElementsByName("tsub")[0].value;
      codtipo   = document.getElementsByName("tipo")[0].value;
      codtipora = document.getElementsByName("tipoRad")[0].value;

      if (codserie == 0 | codsubser == 0 | codtipo == 0 | codtipora == 0){
        alert ("Falta seleccionar uno de los campos");
        return false;
      }

      if ( (archDocto.substring(archDocto.length-1-3,archDocto.length)).indexOf(".xls") == -1){
        alert ("El archivo de datos debe ser .xls");
        return false;
      }

      if (document.formAdjuntarArchivos.archivoPlantilla.value.length<1){
        alert ("Debe ingresar el archivo CSV con los datos");
        return false;
      }

      if (confirm("Tenga cuidado con esta opci\u00F3n ya que se realizar\u00E1n\n" +
              "cambios irreversibles en el sistema.")) {
          return true;
      } else {
          return false;
      }
      return true;
    }

    function cargando(){
      document.getElementById("spinner-back").classList.add("show");
      document.getElementById("spinner-front").classList.add("show");
    }

    function enviar() {
      if (!validar())
        return;
      cargando();
      document.formAdjuntarArchivos.accion.value="PRUEBA";
      document.formAdjuntarArchivos.submit();
    }
</script>

<?
include "tipificar_masivaExcel.php";
$params="dependencia=$dependencia&codiTRD=$codiTRD&tipoRad=$tipoRad&depe_codi_territorial=$depe_codi_territorial&usua_nomb=$usua_nomb&depe_nomb=$depe_nomb&usua_doc=$usua_doc&tipo=$tipo&codusuario=$codusuario";
?>
  <form action="adjuntar_masivaExcel.php?<?=$params?>" method="post" enctype="multipart/form-data" name="formAdjuntarArchivos">
  <input type=hidden name=<?=session_name()?>  value='<?=session_id()?>'>
  <input type=hidden name=pNodo value='<?=$pNodo?>'>
  <input type=hidden name=codProceso value='<?=$codProceso?>'>
  <input type=hidden name=tipoRad value='<?=$tipoRad?>'>
  <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo return_bytes(ini_get('upload_max_filesize')); ?>">
  <input name="accion" type="hidden" id="accion">
    <div class="col-sm-12"> <!-- widget grid -->
      <h2></h2>
      <section id="widget-grid">
        <!-- row -->
        <div class="row">
          <!-- NEW WIDGET START -->
          <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">

              <header>
                <h2>
                Adjuntar archivo con combinaci&oacute;n
                </h2>
              </header>
              <!-- widget div-->
              <div>
                <!-- widget content -->
                <div class="widget-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                      <tr align="center">
                        <td width="16%" class="titulos2">LISTADO </td>
                        <td width="84%" height="30" class="listado2">
                          <div class="alert alert-info" role="alert">
                            Para evitar el problema de caracteres especiales en el texto plano se deben reemplazar si existen los siguientes caracteres:
                            <ul>
                              <li>
                                ‵ o ´  o “ o ”
                                reemplazar por

                                "
                              </li>
                              <li>
                                °
                                reemplazar por
                                o
                              </li>
                               <li>
                                ª
                                reemplazar por
                                a
                              </li>
                               <li>
                                –
                                reemplazar por
                                -
                              </li>
                               <li>
                                #
                                reemplazar por
                                No
                              </li>
                              <li>
                                ┃│ por l
                              </li>
                              <li>
                                ü, Ü
                                por U
                              </li>
                            </ul>
                          </div>
                          <div class="alert alert-info" role="alert">
                            Para salidas el medio de envío es requerido. Evitar dejar vacío, las opciones validas son:
                            <ul>
                              <li>
                                FISICO
                              </li>
                               <li>
                                EMAIL
                              </li>
                               <li>
                                AMBOS
                              </li>
                              <li>
                                <storng>EMAILNC</storng>  Envio de email sin certificado electrónico del envío
                              </li>
                          </div>
                          <input name="archivoPlantilla" type="file" value='<?=$archivoPlantilla?>' class="btn btn-sm btn-primary"  id=archivoPlantilla accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" >
                          <br>
                          <a id="borrarFichero" class="btn btn-danguer"><i class="fa fa-trash" style="font-size: 3rem;" aria-hidden="true"></i>Borrar plantilla para cargar nuevamente</a>
                        </td>
                      </tr>
                      <tr align="center">
                        <td width="16%" class="titulos2">ANEXOS </td>
                        <td width="84%" height="30" class="listado2">
                          <input name="archivoAnexos" type="file" class="btn btn-sm btn-primary" value='<?=$archivoAnexos?>' id=archivoAnexos accept="zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed">
                        </td>
                      </tr>
                       <tr align="center">
                        <td width="16%" class="titulos2">PLANTILLA </td>
                        <td width="84%" height="30" class="listado2">
                          <select id="select-plantillas" class="form-control">
                            <option>Seleccione una plantilla...</option>
                            <?php
                            $tipos= [1=>'generales',3=>'personales'];
                            foreach ($tipos as $key => $tipo) {
                               echo "<optgroup label=".$tipo.">";
                              foreach ($arrayplantillas as $index => $value) {
                                if($value['PLAN_TIPO'] == $key && $value['PLAN_TIPO'] != 3)
                                  echo "<option value=".$index.">".$value['PLAN_NOMBRE']."</option>";
                                if($value['PLAN_TIPO'] == $key && $value['PLAN_TIPO'] == 3 && $value['USUA_CODI']== $_SESSION['usua_id'])
                                    echo "<option value=".$index.">".$value['PLAN_NOMBRE']."</option>";

                              }
                              echo '</optgroup>';
                            }
                            
                            ?>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td colspan=2>
                          <textarea id="texrich" name="respuesta">
                          <p><strong>Bogot&aacute;, <?=date('d/m/Y')?></strong></p>
                          <p><strong>*RAD_S*</strong></p>

                          <p><br />
                          Se&ntilde;or(a)<br />
                          <strong>*NOMBRE*&nbsp;*APELLIDO*<br />
                          *DIGNATARIO*<br />
                          *CARGO*<br />
                          *DIR*<br />
                          *EMAIL*<br />
                          *MUNI_NOMBRE* *DEPTO_NOMBRE* <br /></strong></p>


                          <p>Asunto:&nbsp;*ASUNTO*</p>

                          <p>*CONTENIDO*</p>

                          <p>&nbsp;</p>

                          <p>Cordialmente,</p>

                          <p><br />
                          ${FIRMA}</p>

                          <p>&nbsp;</p>

                          <p><span style="font-size:8px"><strong>*ANEXOS*<br />
                          *DESC_ANEXOS*</strong></span></p>
                          </textarea>
                        </td>
                      </tr>
                      <tr align="center">
                        <td height="30" colspan="2" class="celdaGris">
                          <span class="celdaGris"> <span class="e_texto1">
                          <input type="button" id="previsualizar" class="btn btn-sm btn-default" value="Previsualizar">
                          <input name="enviaPrueba" type="button"  class="btn btn-sm btn-primary" id="envia22"  onClick="enviar();" value="Radicar">
                          </span></span>
                        </td>
                      </tr>
                      <tr align="center">
                        <td height="30" colspan="2" class="celdaGris">
                          <h4><font color="red" height='34px'></font><h4>
                          <br /><br /><br />
                          <div class="alert alert-danger">
                            <strong>Cuidado !</strong> Esta operaci&oacute;n generar&aacute; un radicado
                          por cada registro del archivo de origen. Por favor tenga cuidado con esta opci&oacute;n ya que
                          se realizar&aacute; cambios irreversibles en el sistema.
                          </div>
                          <br /><br /><br />
                          <div class="alert alert-warning" align="left">
                            <strong>Nota!</strong><small> Campo para la combinación : (Pueden usarse otros adicionales)<br>
                              <b>*PAIS_NOMBRE*</b> : Nombre del pais. <br>
                              <b>*ASUNTO*</b> :  Asunto que tendra el radicado Generado.     <br>
                              <b>*FOLIOS*</b> : Opcional, Numero de Fólios del radicado.  <br>
                              <b>*ANEXOS*</b> : Opcional, Numero de Anexos.<br>
                              <b>*DESC_ANEXOS*</b> : Opcional, Descripcion de los anexos del radicado.<br>
                              <b>*NUM_EXPEDIENTE*</b> : Opcional, Numero de expediente al cual se asocia el radicado generado.   <br>
                              <b>*EXP_DE_RADICADO*</b> : Opcional, Asocia el radicado generado a el expediente de un radicado indicado en este campo, es de anotar que si el campo *NUM_EXPEDIENTE*,
                               contiene ya un n&uacute;mero de Expediente, este campo no se tendrá en cuenta.  Adicionalmente si el radicado (*EXP_DE_RADICADO*) indicado se encuentra dos Expedientes el sistema no asocia ninguno, este proceso deberá ser
                               realizado mas adelante de manera manual, el sistema indicara los expedientes que contiene el radicado indicado.</samll>

                          </div>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </article>
        </div>
      </section>
    </div>
  </form>
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
                        <div id="preview"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span id="info"></span>
                <button type="button" class="btn btn-default" id="anterior"><</button>
                <button type="button" class="btn btn-default" id="siguiente">></button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
  </div>
  <script>
    var plantillas = <?php echo json_encode($arrayplantillas) ?>;
    var departamentos = <?php echo json_encode($arraydepartamentos) ?>;
    var municipios = <?php echo json_encode($arraymunicipios) ?>;

    CKEDITOR.config.height = '400';
    CKEDITOR.replace('texrich');
    $(function() {
      var indice = 0;
      var title = [];
      var dataset = [];

      $('#select-plantillas').on('change',function(){
        CKEDITOR.instances.texrich.setData(plantillas[$(this).val()]['PLAN_PLANTILLA']);
      })
      //excel

      var ExcelToJSON = function() {

      this.parseExcel = function(file) {
        var reader = new FileReader();

            reader.onload = function(e) {
              var data = e.target.result;
              var workbook = XLSX.read(data, {
                type: 'binary'
              });
              let = 0;
              workbook.SheetNames.forEach(function(sheetName) {
                // Here is your object

                var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);
                var json_object = JSON.stringify(XL_row_object);
                console.log(JSON.parse(json_object));
                jQuery( '#xlx_json' ).val( json_object );
                if(let == 0)
                  dataset = JSON.parse(json_object);
                let++
              })
            };

            reader.onerror = function(ex) {
              console.log(ex);
            };

            reader.readAsBinaryString(file);
          };
      };

       document.getElementById('archivoPlantilla').addEventListener('change', handleFileSelect, false);

      function handleFileSelect(evt) {
        var files = evt.target.files; // FileList object
        var xl2json = new ExcelToJSON();
        xl2json.parseExcel(files[0]);
      }

      function validar(){
          if(dataset.length > 0)
          {
            let errores = '';
            valido = true
            $.each(dataset, function(k, v) {
                  let tiporadSel;
                  tiporadSel = $('select[name=tipoRad]').val();
                  let envios_tipo = ['FISICO','EMAIL','AMBOS','EMAILNC']
                    if(tiporadSel==1 && v["*MEDIOENVIO*"] === undefined) {
                      valido = false
                      errores +=`una salida debe tener medio de envio EMAIL o EMAILNC o  FISICO o AMBOS valor actual: vacio \n`;
                    }
              $.each(v, function(i, val) {                  

                  if(tiporadSel==1 && i == '*MEDIOENVIO*' && !envios_tipo.includes(val)){
                    valido = false
                    errores +=`una salida debe tener medio de envio EMAIL o EMAILNC o  FISICO o AMBOS valor actual: ${val} \n`;
                  }

                  if(i == '*ANEXOS*' && val != '' && val.length > 0 && !Number.isInteger(parseInt(val)) ){
                    valido = false
                    errores +=`la columna anexos debe ser un número sin espacios ni caracteres, valor actual: ${val} \n`;
                  }

                if(i == '*MUNI_NOMBRE*' && val != '' && !municipios.includes(val)){
                  valido = false
                  errores +=`verifica la divipola municipio con error ${val} \n`;
                  
                }
                if(i == '*DEPTO_NOMBRE*' && val != '' && !departamentos.includes(val) ){
                  valido = false
                  errores +=`verifica la divipola departamento con error ${val} \n`;
                }
                var letters = /[´“”°ª–#'┃│]/g;
                let data = val.match(letters);
                if(data !== null){
                  valido = false
                  errores += `El excel contiene los siguientes caracteres no validos: ${val} \n`
                }
              });
            });
            if(valido == false){
              alert(errores);
              document.getElementById('archivoPlantilla').value= null;
                  throw new Error("error");
            }
          }
        }

        $('#borrarFichero').on('click',function(){
           alert('Plantilla borrada puede cargar nuevamente');
              document.getElementById('archivoPlantilla').value= null;
                  throw new Error("error");
        })

      // leer csv y precargar valores
      /*
      $('#archivoPlantilla').on('change', function(e) {
        if (!window.FileReader ) {
            return alert('No es soportada la previsualización de archivos en su navegador.');
        } else {
          var fileReader = new FileReader();
          fileReader.onload = function () {
            title = [];
            dataset = [];
            var data = fileReader.result;  // data <-- in this var you have the file data in Base64 format
            var resultados = data.split("\n");
            indice = 0;
            size = 0;
            $.each(resultados, function(i, row) {
              var columns = row.split("\t");
              if(i == 0) {
                title = columns.map(function(e) { return e.trim(); });
                size = title.length;
              } else {
                var object = {};
                if(columns.length == size) {
                    $.each(columns, function(i, e) {
                      object[title[i]] = e;
                    });

                    dataset.push(object);
                }
              }
            });
          };
          fileReader.readAsText($('#archivoPlantilla').prop('files')[0], 'utf-8');
        }
      });
      */

      //funcion para reemplazar variables en la plantilla por los registros del csv
      function cargarDatos(id) {
        $('#info').html("Pág. "+((id % dataset.length) + 1) +" de "+dataset.length);
        var html = CKEDITOR.instances.texrich.getData();

        if(dataset.length > 0)
        {
          var new_html = html;
          $.each(dataset[id % dataset.length], function(k, v) {
            new_html = new_html.replace(k, v);
          });
          $('#preview').html(new_html);
        }
      }

      //paginador previsualización
      $('#anterior').on('click', function(e) {
        indice--;
        cargarDatos(Math.abs(indice));
      });

      $('#siguiente').on('click', function(e) {
        indice++;
        cargarDatos(Math.abs(indice));
      });

      $('#previsualizar').on('click', function(e) {
        validar();
        cargarDatos(indice);
        $('#modal').modal('show');
      })
    })
  </script>
</body>
</html>
