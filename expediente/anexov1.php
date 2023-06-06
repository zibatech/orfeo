<html>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Edición de anexos</title>
  </head>
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


if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$ruta_raiz = "..";
include_once( "$ruta_raiz/include/db/ConnectionHandler.php" );

$db = new ConnectionHandler( "$ruta_raiz" );
$conf_v1='postgres,40.87.59.4,orfeo,kuobY3nB4ANo,orfeo';

  list($driver1,$host1,$user1,$pass1,$dbname1) = explode(',',$conf_v1);
        $db->dbOld = ADONewConnection($driver1);
     //   echo "$host1,$user1,$pass1,$dbname1";
        $db->dbOld->Connect($host1,$user1,$pass1,$dbname1);


if($_POST['actualiza']=='1')
{
	$upd="UPDATE sgd_exp_anexos SET exp_anex_desc='".$_POST['asunto']."' WHERE exp_anex_nomb_archivo='".$_POST['p']."' AND exp_anex_desc='".$_POST['n']."'";
	if($db->dbOld->Execute($upd))
	{
		echo '<div class="alert alert-success" role="alert">
  Actualización exitosa. <button type="button" class="btn btn-success" onclick="window.close();">Continuar</button>
</div>';
	}

}

if($_GET['e']=='1')
{
  $upd="UPDATE sgd_exp_anexos SET exp_anex_borrado='S' WHERE exp_anex_nomb_archivo='".$_GET['p']."' AND exp_anex_desc='".$_GET['n']."'";
  if($db->dbOld->Execute($upd))
  {
    echo '<div class="alert alert-success" role="alert">
  Anexo excluido. <button type="button" class="btn btn-success" onclick="window.close();">Continuar</button>
</div>';
  }


}


$sql="SELECT exp_anex_desc as nombre FROM sgd_exp_anexos WHERE exp_anex_nomb_archivo='".$_GET['p']."' AND exp_anex_desc='".$_GET['a']."'";
$rs_sql=$db->dbOld->Execute($sql);


?>


  <h3>Edición del anexo: <?=$_GET['p']?></h3>
<form method="post" action="anexov1.php?p=<?= $_GET['p']?>">
  <div class="form-group">
    <label for="asunto">Asunto</label>
    <input type="input" class="form-control" id="asunto" name="asunto" value="<?php echo $rs_sql->fields['NOMBRE'];?>" required>
  </div>
  <button type="submit" class="btn btn-primary" onclick="window.opener.location.reload();">Actualizar</button>
  <button type="button" class="btn btn-danger" onclick="window.opener.location.reload();window.close();">Cancelar</button>
  <button type="button" class="btn btn-warning" onclick="window.opener.location.reload();window.location='anexov1.php?p=<?= $_GET['p']?>&e=1&n=<?= $rs_sql->fields['NOMBRE']?>';">Excluir del expediente</button>
  <input type="hidden" name="actualiza" value="1">
  <input type="hidden" name="p" value="<?= $_GET['p']?>">
  <input type="hidden" name="n" value="<?= $rs_sql->fields['NOMBRE']?>">


</form>
</html>