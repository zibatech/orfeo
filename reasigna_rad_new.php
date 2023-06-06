<?
     $ruta_raiz   = ".";
      include_once "include/db/ConnectionHandler.php";
	  $db = new ConnectionHandler($ruta_raiz);
      $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);	 
      if (isset($_POST['usuario'],$_POST['dependencia'],$_POST['radicado'],$_POST['modificar']) & 
      	  $_POST['usuario']!=0  && $_POST['dependencia']!=0  && $_POST['radicado']!=0){
          $sql='update radicado set radi_usua_actu='.$_POST['usuario'].', radi_depe_actu='.$_POST['dependencia'].' where radi_nume_radi='.$_POST['radicado'];
          $rs=$db->conn->query($sql);
          $flag_modificado=true;
      }	


?>
<html>
<head>
<title>Reasignacion de radicados</title>
<link rel="stylesheet" href="estilos/orfeo.css" type="text/css">
<script >

function solonumeros()
{
 jh =  document.getElementById('nurad').value;
 if(jh)
 {
		
		var1 =  parseInt(jh);
		if(var1 != jh)
		{
			alert("Atencion: El numero de Radicado debe ser de solo Numeros.");
			return false;
		}else{
			numCaracteres = document.getElementById('nurad').value.length;
                         <?php
                           $ln=$_SESSION["digitosDependencia"];
                           $lnr=11+$ln;
                        ?>
			if(numCaracteres>=6)
			{
				document.FrmBuscar.submit();
			}else
			{
				alert("Atencion: El numero de Caracteres del radicado es de <?php echo $lnr; ?>. (Digito :"+numCaracteres+")");
			}
			
		}
 }else{
 	document.FrmBuscar.submit();
 }
}
</script>

</head>

<body onLoad='document.getElementById("nurad").focus();'>
	<table border=0 width=100% class="borde_tab" cellspacing="5">
	<tr align="center" class="titulos5">
	<td height="15" class="titulos5">Reasignacion de radicados mal enviados</td>
</tr></Table>
<center></P>
  <form action='reasigna_rad_new.php'  name="FrmBuscar" class=celdaGris method="POST">
    <table width="80%" class='borde_tab' cellspacing='5'>
  <tr class='titulos2'> 
        <td width="25%" height="49">Numero de radicado</td>
        <td width="55%" class=listado2>
		  <input type="" name="radicado" id="radicado" value=<? if(isset($_POST['radicado'])){echo $_POST['radicado'];}?>>
	    </td>
  </tr>
  <tr class='titulos2'> 
        <td width="25%" height="49">Dependencia</td>
        <td width="55%" class=listado2>
		<select name="dependencia" id="dependecia" onchange="this.form.submit()">
			<option value=0>Seleccione</option>
			<? 
			$sql="select * from dependencia where depe_estado=1 order by 1";
		    $rs=$db->conn->query($sql);
		    $flag_display_title=true;
		    while (!$rs->EOF){
		    	  $selected='';
		    	  if(isset($_POST['dependencia']) &&
		    	  	 $_POST['dependencia']==$rs->fields['DEPE_CODI']){
		    	  	 $selected='selected';
		    	  }
		    	  echo "<option value=".$rs->fields['DEPE_CODI']." ".$selected.">".$rs->fields['DEPE_CODI']."-".$rs->fields['DEPE_NOMB']."</option>";
	  		      $rs->MoveNext();
		    }

			?>
		</select>
	 </td>
  </tr>
  <tr class='titulos2'> 
        <td width="25%" height="49">Usuario</td>
        <td width="55%" class=listado2>
		<select name="usuario" id="usuario" onchange="this.form.submit()">
			<option value=0>Seleccione</option>
			<? 
			$depe=(isset($_POST['dependencia']))?$_POST['dependencia']:0;
			$sql="select * from usuario where usua_esta=1 and depe_codi=".$depe." order by 1";
		    $rs=$db->conn->query($sql);
		    $flag_display_title=true;
		    while (!$rs->EOF){
		    	  $selected='';
		    	  if(isset($_POST['usuario']) &&
		    	  	 $_POST['usuario']==$rs->fields['USUA_CODI']){
		    	  	 $selected='selected';
		    	  }
		    	  echo "<option value=".$rs->fields['USUA_CODI']." ".$selected.">".$rs->fields['USUA_LOGIN']." (".$rs->fields['USUA_NOMB'].")</option>";
	  		      $rs->MoveNext();
		    }
		    ?>
		</select>
	 </td>
  </tr>
      <? if ( $_POST['usuario']!=0  && $_POST['dependencia']!=0  && $_POST['radicado']!=0  &&
              isset($_POST['usuario'],$_POST['dependencia'],$_POST['radicado'])) {?>
  <tr>
     <td colspan="2" align="center">
        <input type="submit" name="modificar" Value="Modificar" class="botones_largo" > 
        <? if (isset($flag_modificado) &&  $flag_modificado){ echo "<br/> <font color=red>El radicado ".$_POST['Radicado']." fue actualizado correctamete.</font></br>";}?>
     </td> 
  </tr>
  <? } ?>
</table>
</form>
</center>
</body>
</html>
