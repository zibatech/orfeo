<?php
session_start();
/**
 * @module index_frame
 *
 * @author Jairo Losada   <jlosada@gmail.com>
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

/**
  Funcion para validar el navegador para no permitir el vegador de Microsft y/o Edge
 */
function get_the_browser()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
        return false;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) {
        return false;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Edg') !== false) {
        return false;
    } else {
        return true;
    }
}

$isValidBrowser = get_the_browser();


$drd = false;
$krd = false;
if (isset($_POST["krd"])) {
    $krd = $_POST["krd"];
}
if (isset($_POST["drd"])) {
    $drd = $_POST["drd"];
}

$year = date('Y');

if (isset($_POST["autenticaPorLDAP"])) {
    $autenticaPorLDAP = $_POST["autenticaPorLDAP"];
}

$fechah        = date("dmy") . "_" . time();
$ruta_raiz     = ".";
$usua_nuevo    = 3;
$ValidacionKrd  = "";

include("dbconfig.php");
include("processConfig.php");
$serv = str_replace(".", ".", $_SERVER['REMOTE_ADDR']);

if ($krd) {
    //session_orfeo retorna mensaje de error
    include "$ruta_raiz/session_orfeo.php";
    require_once("$ruta_raiz/class_control/Mensaje.php");

    if ($usua_nuevo == 0 &&  !$autenticaPorLDAP) {
        include($ruta_raiz . "/contraxx.php");
        $ValidacionKrd = "NOOOO";
        if ($j = 1) {
            die("<center> -- </center>");
        }
    }
}
include_once("include/utils/Utils.php");

$krd = strtoupper($krd);

if ($ValidacionKrd == "Si") {
    header("Location: $ruta_raiz/index_frames.php");
    exit();
}

$ico = "$ruta_raiz/bodega/$favicon";
$bac = "$ruta_raiz/bodega/$background";
//$header = "$ruta_raiz/bodega/$headerRtaPdf";
$imgLogin = "$ruta_raiz/bodega/sys_img/imgLogin.png";
//$imgPie = "$ruta_raiz/estilos/images/pie_login.png";

if ($logoEntidad) {
    $log = "$ruta_raiz/bodega/$logoEntidad";
} else {
    $log = "$ruta_raiz/img/orfeo.png";
}
?>

<!DOCTYPE html>
<!--[if IE 8]> <html class="no-js lt-ie9" lang="en" > <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"><!--<![endif]-->

<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="SIIM2">
  <meta name="keywords" content="">
  <link rel="shortcut icon" href="<?= (file_exists("$ico")) ? $ico : "" ?>" onClick="this.reload();">
  <title><?= $entidad ?> / Orfeo</title>
    <link href="./estilos/bootstrap.css" rel="stylesheet">
  <link href="./estilos/<?= (file_exists("./estilos/$entidad.login.css")) ? $entidad . "." : "" ?>login.css" rel="stylesheet">
  <style>
    body {
      background-image: url(<?= $bac ?>);
      background-position: center center;
      background-repeat: no-repeat;
      background-attachment: fixed;
      background-size: cover;
    }

    .login-form p.login-alt-text {
      color: rgb(23, 23, 23);
    }

    .login-form label {
      color: rgb(23, 23, 23);
    }

    ::placeholder {
      /* Chrome, Firefox, Opera, Safari 10.1+ */
      color: rgb(23, 23, 23);
      opacity: 1;
      /* Firefox */
    }

    #footer-text,
    #footer-text a {
      color: black;
      text-shadow: none;
    }
  </style>

</head>

<body>
  <?php
  $err_response = array(
    '400' => 'La petición realizada es inválida.',
    '401' => 'Acceso a recurso no atuorizado.',
    '403' => 'No tiene permisos para acceder a este recurso.',
    '404' => 'La página solicitada no fué encontrada.',
    '500' => 'Lo sentimos, ha ocurrido un error inesperado.'
  );
?>
  <?php if (isset($_GET['code']) &&  array_key_exists($_GET['code'], $err_response)) : ?>
    <h4 style="color: #fff; ">ERROR: <?= $err_response[$_GET['code']]; ?></h4>
  <?php endif; ?>
  <div class="container" id="login-block">
    <div class="row">
      <div class="col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4 col-lg-12">
        <div class="row login-box clearfix animated flipInY">
            <div class="login-form">
              <figure style="text-align: center; margin: 0;">
                <img src="<?= $imgLogin ?>" alt="Logo" style="max-width: 55%;">
              </figure>
            <div class="col-md-12">
              <h3 style="margin-bottom: 0; line-height: 1;">Inicia sesión</h3>
              <p style="margin-bottom: 0;">Rellena el formulario con tus credenciales para continuar</p>
              <?= @$msgindex ?>
            </div>
            <div class="col-md-12">
              <div class="alert alert-error hide">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <h4>Error!</h4>
                Los datos suministrados no son correctos.
              </div>
            </div>
            <div class="col-md-12">
              <br>
            </div>
            <div class="col-md-12">
              <form action="./login.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo Utils::get_token() ?>">
                <div class="form-group">
                  <label for="usr">Usuario</label>
                  <input type="text" name="krd" required="" class="form-control" id="usr" placeholder="Usuario">
                </div>
                <div class="form-group">
                  <label for="pass">Contraseña</label>
                  <input type="password" name="drd" required="" class="form-control" id="pass" placeholder="Contraseña">
                </div>
                <?php if ($isValidBrowser) : ?>
                  <button type="submit" class="btn btn-login" style="background-color: <?= $colorFondo ?>;">INGRESAR</button>
                <?php else : ?>
                  <div class="alert alert-warning " role="alert">
                    <h4>Información!</h4>
                    Para un mejor desempeño, inicie sesión con un navegador diferente a <u>Internet Explorer</u> y/o <u>Microsoft Edge</u>.
                  </div>
                <?php endif ?>
              </form>
            </div>
            <?php if (!empty($mensajeError)) { ?>
              <div class="text-error">
                <?= $mensajeError ?>
              </div>
            <?php } ?>
          </div>
          <div>
            <span id="signinButton">
              <span class="g-signin" data-callback="signinCallback" data-clientid="CLIENT_ID" data-cookiepolicy="single_host_origin" data-requestvisibleactions="http://schemas.google.com/AddActivity" data-scope="https://www.googleapis.com/auth/plus.login">
              </span>
            </span>
          </div>
          <img class="imgPie" src="<?= $imgPie ?>" alt="">
        </div>
      </div>
    </div>
  </div>
  <!-- End Login box -->
  <div>
  </div>
  <footer class="container" role="contentinfo">
    <p id="footer-text"><small style="color: ">Copyleft <?= $year ?>, basado en OrfeoGPL</p>
  </footer>
</body>

</html>
