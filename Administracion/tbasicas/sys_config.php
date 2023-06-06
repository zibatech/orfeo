<?php
/**
 * @module config_system
 n*
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright 2020
 *
 * realizaro en tiempo de coronavirus

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
$ruta_raiz = "../..";

if (!$_SESSION['dependencia']){
    header ("Location: $ruta_raiz/cerrar_session.php");
}

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
include_once "$ruta_raiz/processConfig.php";
include_once "$ruta_raiz/include/tx/roles.php";

$db    = new ConnectionHandler("$ruta_raiz");
$roles = new Roles($db);
$drd   = $_POST['pass'];
$krd   = $_SESSION["krd"];
$error = '';
$mess  = False;
$rest  = False;

if(strtoupper($krd) == 'ADMON' && $drd && $roles->traerPermisos($krd,$drd)){
    $_SESSION["sys_config"] = True;
}

if($_SESSION["sys_config"] && $_POST['form_config']=='Guardar'){
    foreach ($_FILES as $key => $val){

        if ($val["size"] == 0) {
            continue;
        }

        $target_dir = $CONTENT_PATH;
        $namefile = '/sys_img/'.basename($val["name"]);
        $target_file = $target_dir . $namefile;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $check = getimagesize($val["tmp_name"]);

        if($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
        }

        if ($val["size"] > 5000000) {
            $uploadOk = 0;
            $error .= 'Tamaño superior a 5M';
        }

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" && $imageFileType != "ico" ) {
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            $error .= empty($error)? $key: ', '. $key;
            $error .= ' parametros incorrectos';
        } else {
            if (move_uploaded_file($val["tmp_name"], $target_file)) {
               $upd = "UPDATE
                            SGD_CONFIG
                        SET CONF_VALOR = '$namefile'
                        WHERE CONF_NOMBRE = '$key'";
                $rest = $db->conn->query($upd);
            } else {
                $error .= empty($error)? $key: ', '. $key;
                $error .= ' en directorio destino';
            }
        }
    }

    foreach ($_POST as $key => $val){
        $upd = "UPDATE
                    SGD_CONFIG
                SET CONF_VALOR = '$val'
                WHERE CONF_NOMBRE = '$key'";
        $rest = $db->conn->query($upd);
        if(!$rest){
            $error .= empty($error)? $key: ', '. $key;
        }
    }

    if(!$error){
        $mess = time();
    }


}

if($_SESSION["sys_config"]){
    $data  = array();
    $data2 = array();
    $query = "SELECT * FROM SGD_CONFIG";
    $rs    = $db->conn->Execute($query);

    while(!$rs->EOF){
        $desc = $rs->fields["CONF_DESCRIPCION"];
        $name = $rs->fields["CONF_NOMBRE"];
        $valu = $rs->fields["CONF_VALOR"];
        $imag = $rs->fields["CONF_IMAGEN"];
        $cons = $rs->fields["CONF_CONSTANTE"];

        if(!$imag){
            $showDat = array(
                'DES' => $desc,
                'NAM' => $name,
                'VAL' => $valu,
                'SIM' => ''
            );

            if(!$cons){
                $showDat['SIM'] = '$';
            }

            $data[] = $showDat;

        }else{

            $data2[] = array(
                'DES' => $desc,
                'NAM' => $name,
                'VAL' => $valu
            );

        }

        $rs->MoveNext();

    }
}

?>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="sys_config">
        <title>Orfeo- Admon de Dependencias.</title>
        <?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
        <style>
            .form-signin {
              max-width: 330px;
              padding: 15px;
            }
            .form-signin .form-signin-heading,
            .form-signin .checkbox {
              margin-bottom: 10px;
            }
            .form-signin .checkbox {
              font-weight: normal;
            }
            .form-signin .form-control {
              position: relative;
              font-size: 16px;
              height: auto;
              padding: 10px;
              -webkit-box-sizing: border-box;
                 -moz-box-sizing: border-box;
                      box-sizing: border-box;
            }
            .form-signin .form-control:focus {
              z-index: 2;
            }
            .form-signin input[type="text"] {
              margin-bottom: -1px;
              border-bottom-left-radius: 0;
              border-bottom-right-radius: 0;
            }
            .form-signin input[type="password"] {
              margin-bottom: 10px;
              border-top-left-radius: 0;
              border-top-right-radius: 0;
            }
        </style>
    </head>
    <body>
                <?php if(!$_SESSION["sys_config"]){?>
                <form class='form-signin' role="form" name="admin" id="admin" method="post"
                    action="<?=$_SERVER['PHP_SELF']?>">
                    <h2 class="semi-bold">Digite su contraseña de administrador</h2>
                    <input name='pass' type="password" class="form-control" placeholder="Contraseña">
                    <button class="btn btn-primary" type="submit">Entrar</button>
                </form>
                <?php }

                if($mess){?>
                <div class="alert alert-success">
                    <i class="fa-fw fa fa-check"></i>
                    <strong id="alert-success">Guardado <?=$mess?></strong>
                </div>
                <?}

                if($error){?>
                <div class="alert alert-error">
                    <i class="fa-fw fa exclamation-triangle"></i>
                    <strong id="alert-error">No se actualizo el registro de:  <?=$error?></strong>
                </div>
                <?}

                if($_SESSION["sys_config"]){?>
                <form role="form" name="form" id="config" method="post"
                    enctype="multipart/form-data"
                    action="<?=$_SERVER['PHP_SELF']?>">
                    <div >
                        <!-- Widget ID (each widget will need unique ID)-->
                        <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-editbutton="false">
                            <header>
                                <h2>Varibles de configuración</h2>
                            </header>
                            <!-- widget div-->
                            <div>
                                <!-- widget content -->
                                <div class="widget-body form-group">
                                  <table id="datatable"  width="100%">
                                      <tbody id="contentable">
                                        <? foreach ($data2 as $key => $val){?>
                                        <tr class="titulos2">
                                          <td width="300px">
                                            <h6>
                                                <?=$key?>) $<?=$val['NAM']?>
                                            </h6>
                                            <?=$val['DES']?>
                                          </td>
                                          <td>
                                              <strong>Valor Actual: </strong><?=$val['VAL']?>
                                              <div class="input-group">
                                                  <div class="custom-file">
                                                      <input type="file" class="custom-file-input" id="inputGroupFile01"
                                                             name ="<?=$val['NAM']?>" aria-describedby="inputGroupFileAddon01">
                                                      <label class="custom-file-label" for="inputGroupFile01">Seleccionar Archivo</label>
                                                  </div>
                                              </div>
                                          </td>
                                        </tr>
                                        <? } ?>
                                        <? foreach ($data as $key => $val){?>
                                        <tr class="titulos2">
                                          <td width="300px">
                                            <h6>
                                                <?=$key?>) <?=$val['SIM']?><?=$val['NAM']?>
                                            </h6>
                                            <?=$val['DES']?>
                                          </td>
                                          <td>
                                            <input
                                              class="form-control"
                                              type ="input"
                                              name ="<?=$val['NAM']?>"
                                              value="<?=$val['VAL']?>"/>
                                          </td>
                                        </tr>
                                        <? } ?>
                                      </tbody>
                                  </table>
                                </div>
                                <!-- end widget content -->
                            </div>
                            <!-- end widget div -->
                        </div>
                        <!-- WIDGET END -->
                        <input class="btn btn-primary" name='form_config' value="Guardar" type="submit" />
                    </div>
                </form>
                <?php }?>
    </body>
</html>
