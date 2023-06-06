<?php
/*************************************************************************************/
/* ORFEO GPL:Sistema de Gestion Documental		http://www.orfeogpl.org	             */
/*	Idea Original de la SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS         */
/*				COLOMBIA TEL. (57) (1) 6913005  orfeogpl@gmail.com                   */
/* ===========================                                                       */
/*                                                                                   */
/* Este programa es software libre. usted puede redistribuirlo y/o modificarlo       */
/* bajo los terminos de la licencia GNU General Public publicada por                 */
/* la "Free Software Foundation"; Licencia version 2. 			                     */
/*                                                                                   */
/* Copyright (c) 2005 por :	  	  	                                                 */
/* SSPD "Superintendencia de Servicios Publicos Domiciliarios"                       */
/*   Jairo Hernan Losada  jlosada@gmail.com                Desarrollador             */
/*   Sixto Angel Pinzón López --- angel.pinzon@gmail.com   Desarrollador           */
/* C.R.A.  "COMISION DE REGULACION DE AGUAS Y SANEAMIENTO AMBIENTAL"                 */ 
/*   Liliana Gomez        lgomezv@gmail.com                Desarrolladora            */
/*   Lucia Ojeda          lojedaster@gmail.com             Desarrolladora            */
/* D.N.P. "Departamento Nacional de Planeación"                                     */
/*   Hollman Ladino       hladino@gmail.com                Desarrollador             */
/*                                                                                   */
/* Colocar desde esta lInea las Modificaciones Realizadas Luego de la Version 3.5    */
/*  Nombre Desarrollador   Correo     Fecha   Modificacion                           */
/*  Fabian Mauricio Losada Florez      12-Enero-2007 						     */
/*************************************************************************************/
$krdOld = $krd;
session_start();

if(!$krd) $krd = $krdOld;
if (!$ruta_raiz) $ruta_raiz = "..";
include "$ruta_raiz/rec_session.php";
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once "$ruta_raiz/include/tx/Historico.php";
include_once "$ruta_raiz/include/tx/Expediente.php";
$db = new ConnectionHandler( "$ruta_raiz" );
//$db->debug = true;
$encabezadol = "$PHP_SELF?".session_name()."=".session_id()."&dependencia=$dependencia&krd=$krd&tipo=$tipo&codp=$codp";

?>
<html>
<head>
<title>RELACI&Oacute;N ENTRE TIPOS DE ALMACENAMIENTO</title>
<link rel="stylesheet" href="../estilos/orfeo.css">
</head>
<body bgcolor="#FFFFFF">
<form name="relacionTiposAlmac" action="<?=$encabezadol?>" method="POST" >
<?
if($grabar){
$t=0;
if($tipoAlmacPadre==$codp){
	$sql="insert into sgd_eit_items (sgd_eit_codigo,sgd_eit_cod_padre,sgd_eit_nombre,sgd_eit_sigla) values ( ".$db->conn->nextId( 'SEC_EDIFICIO' ).",'$tipoAlmacPadre','$hijo','$Shijo')";
//$db->conn->debug=true;
$rs=$db->conn->Execute($sql);
}
else
{
for($i=1;$i<=$cantidad;$i++){
$hijoc=$hijo." ".$i;
$Shijoc=$Shijo.$i;
$sql="insert into sgd_eit_items (sgd_eit_codigo,sgd_eit_cod_padre,sgd_eit_nombre,sgd_eit_sigla) values ( ".$db->conn->nextId( 'SEC_EDIFICIO' ).",'$tipoAlmacPadre','$hijoc','$Shijoc')";
//$db->conn->debug=true;
$rs=$db->conn->Execute($sql);
}
}
if($rs->EOF)$t+=1;
if($t==0)echo "No se pudo ingresar el registro";
else echo "El registro fue ingresado";
}
?>
<table border="0" width="90%" cellpadding="0" class="borde_tab">
<tr>
  <td height="35" colspan="5" class="titulos2">
  <center>RELACI&Oacute;N ENTRE TIPOS DE ALMACENAMIENTO</center>
  </td>
</tr>
<tr>
<td class="titulos2">Nombre Padre:<br>
  Cod_pa-Cod-Nombre

<?php
$i=1;
 	$sqm1="select * from sgd_eit_items where sgd_eit_cod_padre like '$codp'";
	
	$rs1=$db->conn->Execute($sqm1);
	while(!$rs1->EOF){
		$cod_p=$rs1->fields['SGD_EIT_CODIGO'];
		$nom[$i]=$codp."-".$cod_p."-".$rs1->fields['SGD_EIT_NOMBRE'];
		$codi[$i]=$rs1->fields['SGD_EIT_CODIGO'];
		$sqm2="select * from sgd_eit_items where sgd_eit_cod_padre like '".$codi[$i]."'";
		$rs2=$db->conn->Execute($sqm2);
		$i++;
		while(!$rs2->EOF){
			$cod_p=$rs2->fields['SGD_EIT_CODIGO'];
			$cod_p2=$rs2->fields['SGD_EIT_COD_PADRE'];
			$codi[$i]=$rs2->fields['SGD_EIT_CODIGO'];
			$nom[$i]=$cod_p2."-".$cod_p."-".$rs2->fields['SGD_EIT_NOMBRE'];
			$sqm3="select * from sgd_eit_items where sgd_eit_cod_padre like '".$codi[$i]."'";
			$rs3=$db->conn->Execute($sqm3);
			$i++;
			while(!$rs3->EOF){
				$cod_p=$rs3->fields['SGD_EIT_CODIGO'];
				$codi[$i]=$rs3->fields['SGD_EIT_CODIGO'];
				$cod_p2=$rs3->fields['SGD_EIT_COD_PADRE'];
				$nom[$i]=$cod_p2."-".$cod_p."-".$rs3->fields['SGD_EIT_NOMBRE'];
				$sqm4="select * from sgd_eit_items where sgd_eit_cod_padre like '".$codi[$i]."'";
				$rs4=$db->conn->Execute($sqm4);
				$i++;
				while(!$rs4->EOF){
					$cod_p=$rs4->fields['SGD_EIT_CODIGO'];
					$codi[$i]=$rs4->fields['SGD_EIT_CODIGO'];
					$cod_p2=$rs4->fields['SGD_EIT_COD_PADRE'];
					$nom[$i]=$cod_p2."-".$cod_p."-".$rs4->fields['SGD_EIT_NOMBRE'];
					$sqm5="select * from sgd_eit_items where sgd_eit_cod_padre like '".$codi[$i]."'";
					$rs5=$db->conn->Execute($sqm5);
					$i++;
					while(!$rs5->EOF){
						$cod_p=$rs5->fields['SGD_EIT_CODIGO'];
						$codi[$i]=$rs5->fields['SGD_EIT_CODIGO'];
						$cod_p2=$rs5->fields['SGD_EIT_COD_PADRE'];
						$nom[$i]=$cod_p2."-".$cod_p."-".$rs5->fields['SGD_EIT_NOMBRE'];
						$sqm6="select * from sgd_eit_items where sgd_eit_cod_padre like '".$codi[$i]."'";
						$rs6=$db->conn->Execute($sqm6);
						$i++;
						while(!$rs6->EOF){
							$cod_p=$rs6->fields['SGD_EIT_CODIGO'];
							$codi[$i]=$rs6->fields['SGD_EIT_CODIGO'];
							$cod_p2=$rs6->fields['SGD_EIT_COD_PADRE'];
							$nom[$i]=$cod_p2."-".$cod_p."-".$rs6->fields['SGD_EIT_NOMBRE'];
							$i++;
							$rs6->MoveNext();
						}
						$rs5->MoveNext();
					}
					$rs4->MoveNext();
				}
				$rs3->Movenext();
			}
			$rs2->MoveNext();
		}
		$rs1->MoveNext();
	}
	$sqlp="select SGD_EIT_NOMBRE from sgd_eit_items where sgd_eit_codigo like '$codp'";
	$rsp=$db->conn->Execute($sqlp);
	$nom_pa="0-".$codp."-".$rsp->fields['SGD_EIT_NOMBRE'];
	/*
$q_tiposAlmac  = "SELECT SGD_EIT_CODIGO, SGD_EIT_NOMBRE";
$q_tiposAlmac .= " FROM SGD_EIT_ITEMS2";
$q_tiposAlmac .= " ORDER BY SGD_EIT_COD_PADRE ";
$rs_tiposAlmac = $db->query( $q_tiposAlmac );*/
?>
  <td height="30" class="titulos5">
    <div align="center">
      <select name="tipoAlmacPadre" class="select">
	  <option value="<?=$codp?>" >  <?=$nom_pa?> </option>
	  <?
	  for($p=1;$p<$i;$p++)
{    
    if($nom[$p]!=$nom_pa)print "<option value='".$codi[$p]."'>".$nom[$p]." </font></option>";
}
	  ?>
      </select>
    </div>
  </td>
  
  <td class="titulos5">
    <div align="center">
      <b>Tiene</b>
      <input type="text" name="cantidad" value="" size="2" maxlength="2">
    </div>
  </td>
  
  <td class="titulos5">Hijo:
    <input type="text" name="hijo" value="<?=$hijo?>" >
  </td>
  <td class="titulos5">Sigla Hijo:
  <input type="text" name="Shijo" value="<?=$Shijo?>" size="4" maxlength="4">
  </td>
</tr>
<tr>
  <td class="titulos5" colspan="5" align="center">
    <input type="submit" name="grabar" class="botones" value="GRABAR" >
    <input type="button" name="cerrar" class="botones" value="SALIR" onClick="window.close();opener.regresar();">
  </td>
</tr>
</table>

</form>
</body>
</html>