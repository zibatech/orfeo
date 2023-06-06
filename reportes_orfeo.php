<?php 
require_once ("htmlheader.inc.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Reportes</title>
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
	<td height="15" class="titulos5">Reportes</td>
</tr></Table>
<center></P>
  <form action='reportes_orfeo.php'  name="FrmBuscar" class=celdaGris method="POST">
    <table width="80%" class='borde_tab' cellspacing='5'>
  <tr class='titulos2'> 
        <td width="25%" height="49">Tipo</td>
    <td width="55%" class=listado2>
		<select name="tipo" id="tipo">
			<option value=0>Seleccione</option>
			<option value=1>Memorandos con 4 visto bueno</option>
			<option value=2>Radicados de salida con 4 visto bueno</option>
			<option value=3>Cantidad de radicados en bandeja usuarios jefes</option>
			<option value=4>Cantidad de radicados en bandeja x dependencia</option>
			<option value=5>Cantidad de radicados en bandeja</option>
		</select>
     <input type="submit" name="Generar" Value="Generar reporte" class="botones_largo" > 
	 </td>
  </tr>
</table>
</form>
</center>
</body>
</html>
<?php 
 if (isset($_POST['Generar']) && isset($_POST['tipo']) && $_POST['tipo']!=0){
 	switch ($_POST['tipo']) {
 		case 1:
 			$sql='select 
					max (substr(ax.anex_radi_nume,-1)) as tipo, 
					max(ax.anex_radi_nume) as radicado,
					max(r.ra_asun) as asunto,
					max(  u.usua_nomb) as "Nombre usuario",
					max(  u.usua_codi) as "Codigo de usuario",
					max(  d.depe_nomb) as "Dependencia actual"
					from anexos ax 
					inner join  radicado r on ax.anex_radi_nume=r.radi_nume_radi
					left join usuario u on r.radi_usua_actu=u.usua_codi and r.radi_depe_actu=u.depe_codi
					left join dependencia d on r.radi_depe_actu=d.depe_codi
					where 
					ax.anex_estado >=3  and substr(ax.anex_radi_nume,-1)=3 group by ax.anex_radi_nume';
 			break;
 		case 2:
 			$sql='select 
					max (substr(ax.anex_radi_nume,-1)) as tipo, 
					max(ax.anex_radi_nume) as radicado,
					max(r.ra_asun) as asunto,
					max(  u.usua_nomb) as "Nombre usuario",
					max(  u.usua_codi) as "Codigo de usuario",
					max(  d.depe_nomb) as "Dependencia actual"
					from anexos ax 
					inner join  radicado r on ax.anex_radi_nume=r.radi_nume_radi
					left join usuario u on r.radi_usua_actu=u.usua_codi and r.radi_depe_actu=u.depe_codi
					left join dependencia d on r.radi_depe_actu=d.depe_codi
					where 
					ax.anex_estado >=3 and substr(ax.anex_radi_nume,-1)=1 group by ax.anex_radi_nume';
 			break;
 		case 3:
 			$sql='select 
					max(r.radi_usua_actu) as "Codigo usuario",
					max(u.usua_nomb) as "Nombre usuario", 
					max(r.radi_depe_actu) as "Codigo dependencia",
					max(d.depe_nomb) as "Nombre Dependencia",
					max(r.carp_codi) as "Codigo carpeta",
					max(ca.carp_desc) as "Nombre Carpeta",
					count(r.radi_nume_radi) as cantidad 
				  from radicado r
					left join usuario u  on r.radi_depe_actu=u.depe_codi and r.radi_usua_actu = u.usua_codi
					left join dependencia d  on r.radi_depe_actu=d.depe_codi
					left join carpeta ca  on r.carp_codi=ca.carp_codi
				  where r.radi_usua_actu=1 group by r.radi_usua_actu,r.radi_depe_actu,r.carp_codi order by 1,3,4 ';
 			break;
 		case 4:
 			$sql='select 
          max(SUBSTR(r.radi_nume_radi,1,4)) as "Año",
          max(TO_CHAR(RADI_FECH_RADI, \'mm\')) as "Mes",
					max(r.radi_depe_actu) as "Codigo dependencia",
					max(d.depe_nomb) as "Nombre Dependencia",
					max(SUBSTR(r.radi_nume_radi,-1)) as "Tipo radicacion",
					count(r.radi_nume_radi) as cantidad 
				  from radicado r
					left join usuario u  on r.radi_depe_actu=u.depe_codi and r.radi_usua_actu = u.usua_codi
					left join dependencia d  on r.radi_depe_actu=d.depe_codi
					left join carpeta ca  on r.carp_codi=ca.carp_codi
				   group by SUBSTR(r.radi_nume_radi,1,4),TO_CHAR(RADI_FECH_RADI, \'mm\'),r.radi_depe_actu,SUBSTR(r.radi_nume_radi,-1) order by 1,2,3,5';
 			break;
 		case 5:
 			$sql='select 
          max(SUBSTR(r.radi_nume_radi,1,4)) as "Año",
          max(TO_CHAR(RADI_FECH_RADI, \'mm\')) as "Mes",
					max(SUBSTR(r.radi_nume_radi,-1)) as "Tipo radicacion",
					count(r.radi_nume_radi) as cantidad 
				  from radicado r
					left join usuario u  on r.radi_depe_actu=u.depe_codi and r.radi_usua_actu = u.usua_codi
					left join dependencia d  on r.radi_depe_actu=d.depe_codi
					left join carpeta ca  on r.carp_codi=ca.carp_codi
				   group by SUBSTR(r.radi_nume_radi,1,4),TO_CHAR(RADI_FECH_RADI, \'mm\'),SUBSTR(r.radi_nume_radi,-1) order by 1,2,3';
 			break;
 	}


     //echo "query ".$sql."<br/>";
     $ruta_raiz   = ".";
      include_once "include/db/ConnectionHandler.php";
	  $db = new ConnectionHandler($ruta_raiz);
      $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);	 
	  $rs=$db->conn->query($sql);
	  $flag_display_title=true;
	  $html="<center><table width=\"80%\" class='borde_tab' cellspacing='5'>";
	  while (!$rs->EOF){
	  	   if($flag_display_title){
	  	   $html.="<tr class='listado2'>";
	  	   	   foreach ($rs->fields as $key => $value) {
	  	   	   	  $html.="<td>".$key."</td>";
	  	   	   }
	  	       $html.="</tr><tr class='listado1'>";
	  	       $flag_display_title=false;
	  	       $flag_impar=true;
	  	   }else{
	  	   	 if ($flag_impar){
	  	        $html.="<tr class='listado2'>";
	  	        $flag_impar=false;
	  	   	 }else{
	  	        $html.="<tr class='listado1'>";
	  	        $flag_impar=true;
	  	     }
	  	   }
  	   	   foreach ($rs->fields as $key => $value) {
  	   	   	  $html.="<td>".$value."</td>";
  	   	   }
            
	  	   $html.="</tr>";
	  		$rs->MoveNext();
	  }
$fecha=date('Ymdhis');
$linkxls="bodega/tmp/reporte_orfeo_".$fecha.".xls";
$arc=fopen($linkxls,"x");
fputs($arc,$html);
fclose($arc);
echo "<br/><br/>".$html."</center><br/><a href='".$linkxls."'>Descargar xls</a>";
	  

 }

?>
