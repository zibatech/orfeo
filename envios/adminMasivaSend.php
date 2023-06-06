<?php
/**
 * @module index_frame
 *
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright

 SIIM2 Models are the data definition of SIIM2 Information System
 Copyright (C) 2013 Infometrika Ltda.

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

session_start();

$ruta_raiz   = "..";
if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

if (!$_SESSION["usua_admin_sistema"])
    header ("Location: $ruta_raiz/index.php");

$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];

$nurad   = isset($_POST['nurad'])? $_POST['nurad'] : null;

preg_match_all('/\d{12,}/', $nurad, $output);
$output = $output[0];
$radToFind = implode(", ",$output);

if( isset($radToFind) &&
    !empty($radToFind)){

        include_once "$ruta_raiz/include/db/ConnectionHandler.php";
        $db = new ConnectionHandler("$ruta_raiz");

        if($_GET['pruebas'] == 'true'){
            ini_set('display_errors',true);
            $db->conn->debug = true;
        }

        $isql = "select
                    a.id as id_anexo,
                    d.id as id_direccion,
                    a.anex_codigo as ANEX_CODIGO,
                    a.radi_nume_salida as RADICADO
                 from  anexos a,
                       radicado c,
                       sgd_dir_drecciones d
                where
                    a.anex_borrado= 'N'
                and a.radi_nume_salida = c.radi_nume_radi
                and a.sgd_dir_tipo != 7
                and d.radi_nume_radi = c.radi_nume_radi
                and
                ((a.SGD_DEVE_CODIGO >=0 and a.SGD_DEVE_CODIGO <=99)
                OR a.SGD_DEVE_CODIGO IS NULL)
                AND
                ((c.SGD_EANU_CODIGO != 2
                AND c.SGD_EANU_CODIGO != 1)
                or c.SGD_EANU_CODIGO IS NULL)
                AND a.radi_nume_salida in (%s) ";

        $sql21 = vsprintf($isql, $radToFind);
        $rstl = $db->conn->query($sql21);
        $contador = 1;
        $addRad = array();

        while (!$rstl->EOF && $rstl!=false){

            $dataMasiva   = '';
            $id_direccion = $rstl->fields['ID_DIRECCION'];
            $id_anexo     = $rstl->fields['ID_ANEXO'];
            $codigo       = $rstl->fields['ANEX_CODIGO'];
            $radicaMasiva = $rstl->fields['RADICADO'];
            $output = array_diff($output, array($radicaMasiva));

            //Si el radicado actula ya fue enviado entonces entonces no realizamos
            //la acción nuevamente.
            if(in_array($radicaMasiva, $addRad)){
                $rstl->MoveNext();
                continue;
            }

            $addRad[] = $radicaMasiva;

            //Si se agrego el radicado a este listado entonces automaticamente
            //cambiamos el estado a marcado por enviar de la tabla de anexos
            $isqlUp100 = "update anexos set anex_estado = 4 where  anex_codigo = '$codigo'";
            $db->conn->Execute($isqlUp100);

            //Validamos que este creado el registro de envio en la tabla sgd_radi_Envio
            //que se creo para hacer la discriminacion entre envio fisico y correo electronico
            //Si no existe lo creamos para poder continuar con el envio en el script
            //responseEnvioE-mail.php
            $isqlSgdRE = "SELECT ID FROM sgd_rad_envios WHERE id_direccion = $id_direccion";
            $res_isqlSgdRE = $db->conn->Execute($isqlSgdRE);
            $envio = $res_isqlSgdRE->fields['ID'];

            if(!$envio){
                $sql100 = "select count(id) + 1 as NUMB from sgd_rad_envios";
                $res_sql100 = $db->conn->query($sql100);
                $envio = $res_sql100->fields['NUMB'];

                $isqlInsRE = "  INSERT INTO
                                    sgd_rad_envios
                                VALUES (
                                    $envio,
                                    $id_anexo,
                                    $id_direccion,
                                    'E-mail',
                                    2,
                                    NULL)";
                $res_InsRE = $db->conn->query($isqlInsRE);
            }

            $message .= '<tr>';
            $message .= "<td>
                               $contador ) $radicaMasiva
                         </td>";

            include("$ruta_raiz/envios/responseEnvioE-mail.php");
            $message .= "<td>
                            <ul id='$radicaMasiva' class='list-group collapse'>
                                $dataMasiva
                            </ul>
                            <button data-toggle='collapse' data-target='#$radicaMasiva'>
                                $button
                            </button>
                         </td>";

            $message .= '</tr>';
            $contador++;
            $rstl->MoveNext();
        }

        $timed =  date("Y-m-d h:i:sa");
        $success = "<div class='alert alert-success'>Acción realizada $timed</div>";

        if($output){
            $rad = implode(", ",$output);
            $success .= "<div class='alert alert-danger'>
                            No se procesaron los siguientes radicados <br>
                            $rad <br>
                            Comprobar que el radicado tenga dirección,
                            no este borrado o anulado.
                        </div>";
        }
}

?>
<html>
  <head>
  <title>Envio masivo de documentos por Correo Electronico</title>
  <?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>

  <style>
    /*Cargar en envios/adminMasivaSend*/
    #cover-spin {
        position:fixed;
        width:100%;
        left:0;right:0;top:0;bottom:0;
        background-color: rgba(255,255,255,0.7);
        z-index:9999;
        display:none;
    }

    @-webkit-keyframes spin {
        from {-webkit-transform:rotate(0deg);}
        to {-webkit-transform:rotate(360deg);}
    }

    @keyframes spin {
        from {transform:rotate(0deg);}
        to {transform:rotate(360deg);}
    }

    #cover-spin::after {
        content:'';
        display:block;
        position:absolute;
        left:48%;top:40%;
        width:40px;height:40px;
        border-style:solid;
        border-color:black;
        border-top-color:transparent;
        border-width: 4px;
        border-radius:50%;
        -webkit-animation: spin .8s linear infinite;
        animation: spin .8s linear infinite;
    }
  </style>

  </head>

<body>
  <form action=''  name="FrmBuscar" class=celdaGris method="POST">
    <div class="col-sm-12">
      <!-- widget grid -->
      <h2></h2>
      <section id="widget-grid">
          <!-- NEW WIDGET START -->
         <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <!-- Widget ID (each widget will need unique ID)-->
            <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">
              <header>
                <h2>
                  Envio de documentos por Correo Electronico<br>
                </h2>
              </header>
              <!-- widget div-->
              <div>
                <!-- widget content -->
                <div class="widget-body">
                    <center>
                        <span>
                            <p><h4>
                                <strong> NOTA:</strong>
                                Puede enviar radicados de forma masiva a los
                                correos electronicos que tienen inscritos
                            </h4> </p>
                        </span>
                    </center>
                    <div class="table-responsive">
                        <table class="table">
                          <tr>
                            <td width="25%" height="49">Numeros de radicados</td>
                            <td width="55%">
                                <textarea
                                    row="10"
                                    type='text'
                                    name=nurad
                                    class='form-control'
                                    id=nurad> <?=$radToFind?> </textarea>
                            </td>
                          </tr>

                          <tr>
                            <td width="25%" height="49"></td>
                            <td width="55%">
                                <input
                                type="submit"
                                name="Enviar"
                                class="btn btn-success"
                                onclick="$('#cover-spin').show(0)"
                                value="Enviar"/>
                            </td>
                          </tr>

                        </table>

                        <?if($success){?>
                            <?=$success?>
                            <table class='table'>
                            <tr>
                                <th>Radicado</th>
                                <th>Acciones</th>
                            </tr>
                            <?=$message?>
                            </table>
                        <?}?>

                      </div>
                </div>
              </div>
            </div>
         </article>
      </section>
    </div>
</form>
<div id="cover-spin"></div>
</center>
</body>
</html>
