<?php
session_start();
    $ruta_raiz = "../.."; 
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");


if(!isset($_SESSION['dependencia']))	include "$ruta_raiz/rec_session.php";
$values=array();
require "$ruta_raiz/processConfig.php";
require "$ruta_raiz/include/db/ConnectionHandler.php";
require "admin_user.class.php";

$db = new ConnectionHandler("$ruta_raiz");
//$db->conn->debug=true;

if (isset($_POST['btn_action'])){
	switch ($_POST['btn_action']) {
		case 'Convertir a jefe':
		    if (!empty($_POST['userOrigen']) && $_POST['userOrigen']!=''){
            $admin=new adminUser($db,$_POST['userOrigen']);
            $admin->to_boss_user();
		    }
			break;
		
		case 'Mover radicados':
        if (!empty($_POST['userOrigen']) && $_POST['userOrigen']!='' && !empty($_POST['userDestino']) && $_POST['userDestino']!=''){
            $admin=new adminUser($db,$_POST['userOrigen'],$_POST['userDestino']);
            $admin->move_radicados_to_user();
        }
			break;

		case 'Mover usuario':
        if (!empty($_POST['userOrigen']) && $_POST['userOrigen']!='' && $_POST['depeDestino']!=0){
            $admin=new adminUser($db,$_POST['userOrigen'],null,$_POST['depeDestino']);
            $admin->move_user_to_dependence();
        }
			break;
	}

}

foreach ($_POST as $key => $value) {
	$values[$key]=$value;
}
unset($_POST);



?>

<html>
<head>
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
<title>Admin usuario</title>
<!--<link rel="stylesheet" href="../../estilos/orfeo.css">-->
<script language="JavaScript" src="<?=$ruta_raiz?>/js/formchek.js"></script>
</head>
<body>
<?php

?>
<form name='frmadminuser' action='trasladar_radicados.php' method="POST">
<input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'> 
<input type=hidden  name=usModo value='<?=$usModo?>'>
<table width="93%"  border="0" align="center">
  	<tr bordercolor="#FFFFFF">
    <td colspan="2" class="titulos4">
	<center>
	<br>
	<p><B><span class=etexto><h3>Administracion de usuarios</h3></span></B> </p>
	<br>
	<p><B><span class=etexto> <?=$tituloCrear ?></span></B> </p></center>
	</td>
	</tr>
</table>
<table border=0 width=93% class=t_bordeGris align="center">
	<tr class=timparr>
		<td class="titulos2" height="26">Acción  </td>
		<td class="listado2" height="1">
			 <select class="form-control"  name="action" id="action" onchange="document.frmadminuser.submit()">
			 	 <option value=0>Seleccionar</option>
			 	 <option value=1 <? echo $values['action']==1?'SELECTED':'';?>>Convertir a jefe</option>
			 	 <option value=2 <? echo $values['action']==2?'SELECTED':'';?>>Mover radicados a otro usuario</option>
			 	 <option value=3 <? echo $values['action']==3?'SELECTED':'';?>>Mover usuario a dependencia</option>
			 </select>
		</td>
	</tr>
<?	
  if (isset($values['action']) && $values['action']!=0){
  		?>
  		    <tr>
  		      <td>
  		      	Usuario origen:
  		      </td>	
  		      <td>
  		       <input  class="form-control" type="text" value="<? echo $values['userOrigen'];?>" name="userOrigen">
  		      </td>	
  		    </tr>	
  		<?
  	switch ($values['action']) {
  		case 1:
  		?> 
  		  <tr>
  		  	<td colspan=2 align="center">
			<br>
		       <input class="btn btn-info"  type="submit" value="Convertir a jefe" name="btn_action">
  		    </td>	
  		  </tr>
        <?
  		break;
  		case 2:
  		    ?>
  		    <tr>
  		      <td>
  		      	Usuario destino:
  		      </td>	
  		      <td>
  		       <input class="form-control" type="text" value="<? echo $values['userDestino'];?>" name="userDestino">
  		      </td>	
  		    </tr>	
  		  <tr>
  		  	<td colspan=2 align="center">
			<br>
		       <input class="btn btn-info" type="submit" value="Mover radicados" name="btn_action">
  		    </td>	
  		  </tr>
           <?
  			break;
  	      case 3:
  	      ?>
  		    <tr>
  		      <td>
  		      	Dependencia destino:
  		      </td>	
  		      <td>
  		      	<select  class="form-control" name="depeDestino" id="depeDestino">
  		      		<option value=0>Seleccionar</option>
  		      		<?
		      		    $rs_dep = $db->conn->Execute("SELECT DEPE_NOMB, DEPE_CODI FROM DEPENDENCIA");
						while ($dep = $rs_dep->FetchRow()){
								$selected="";
							if ($dep['DEPE_NOMB']==$values['depeDestino']){
								$selected="selected";
							}
							echo "<option value=".$dep['DEPE_CODI']." ".$selected.">".$dep['DEPE_CODI']."-".$dep['DEPE_NOMB']."</option>";
						}
                    ?>
  		      	</select>
  		      </td>	
  		    </tr>	
  		  <tr>
  		  	<td colspan=2 align="center">
			<br>
		       <input class="btn btn-info"  type="submit" value="Mover usuario" name="btn_action">
  		    </td>	
  		  </tr>


  	<?
  	 break;	
  	}
  }?>
</table>
 <table border=1 width=93% class=t_bordeGris align="center">
</table>
</form>
</body>
</html>
