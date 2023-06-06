<?php

/**
* @author Cesar Gonzalez <aurigadl@gmail.com>
* @author Jairo Losada   <jlosada@gmail.com>
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
* @copyright

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

$ruta_raiz = ".";
if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;


$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$tip3Nombre  = $_SESSION["tip3Nombre"];
$tip3desc    = $_SESSION["tip3desc"];
$tip3img     = $_SESSION["tip3img"];

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler($ruta_raiz);

$numeroa =
$numero  =
$numeros =
$numerot =
$numerop =
$numeroh =0;

$isql = "select
              a.*
              ,b.depe_nomb
         from
              usuario a
              ,dependencia b
          where
             a.depe_codi=b.depe_codi
             and a.USUA_CODI = $codusuario
             and b.DEPE_CODI = $dependencia";

$rs  = $db->query($isql);

$dependencianomb = $rs->fields["DEPE_NOMB"];
$usua_login      = $rs->fields["USUA_LOGIN"];

?>
<html>
    <head>
        <title>Cambio de Contrase&ntilde;as</title>
        <?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
        <style>
           .error {
             color: red;
             margin-left: 5px;
           }

           label.error {
             display: inline;
           }
        </style>
    </head>

    <body>
        <form action='usuarionuevo.php' method="post" id="loginform" class="form-horizontal" role="form">
            <input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'>
            <div class="col-sm-12">
                <!-- widget grid -->
                <section id="widget-grid">
                    <!-- row -->
                    <div class="row">
                      <!-- NEW WIDGET START -->
                      <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <!-- Widget ID (each widget will need unique ID)-->
                        <div class="jarviswidget jarviswidget-color-darken" id="wid-id-1" data-widget-editbutton="false">
                          <header>
                            <h2>
                              Cambio de contrase&ntilde;a<br>
                              <small><?=$tituloCrear ?></small>
                            </h2>
                          </header>
                          <!-- widget content -->
                          <div class="widget-body">
                              <div style="padding-top:30px" class="panel-body" >

                                      <label for="contradrd">Contraseña:</label>
                                      <div class="input-group">
                                          <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                          <input
                                              type="password"
                                              pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{7,}"
                                              name="contradrd"
                                              id="contradrd"
                                              placeholder="Contraseña"
                                              class="form-control"
                                              required>
                                      </div>
                                      <p id="emailHelp" class="form-text text-muted">
                                          La contraseña debe tener mas de 7
                                          caracteres y debe contener numeros,
                                          letras en minusculas y mayusculas.
                                      </p>

                                      <label for="contraver">Confirmar Contraseña:</label>
                                      <div style="margin-bottom: 25px" class="input-group">
                                         <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                         <input
                                             type="password"
                                             pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}"
                                             name="contraver"
                                             id="contraver"
                                             class="form-control"
                                             placeholder="Repetir Contraseña"
                                             required>
                                      </div>

                                      <div style="margin-top:10px" class="form-group">
                                          <!-- Button -->
                                          <div class="col-sm-12 controls">
                                            <input
                                                class="btn btn-success"
                                                type="submit"
                                                value="Aceptar">
                                          </div>
                                      </div>
                              </div>
                      </article>
                </section>
            </div>
        </form>
        <script>
            jQuery.validator.addMethod( 'passwordMatch', function(value, element) {
                // The two password inputs
                var password = $("#contradrd").val();
                var confirmPassword = $("#contraver").val();

                // Check for equality with the password inputs
                if (password != confirmPassword) {
                    return false;
                } else {
                    return true;
                }

            }, "No son iguales las constraseñas");

            $("#loginform").validate({

                submitHandler: function(form) {
                  form.submit();
                },

                rules: {
                    contradrd: {
                        required: true,
                        minlength: 7,
                    } ,

                    contraver: {
                        equalTo: "#contradrd",
                        minlength: 7,
                        passwordMatch: true
                    }
                },

                // messages
                messages: {
                    contradrd: {
                        required: "El campo contraseña es requerido",
                        minlength: "El campo contraseña requiere mas de 7 caracteres",
                    },
                    contraver: {
                        required: "El campo confimar contraseña es requerido",
                        minlength: "El campo contraseña requiere mas de 7 caracteres",
                        passwordMatch: "Las constraseñas no son iguales", // custom message for mismatched passwords
                    }
                }
            });
        </script>
    </body>
</html>
