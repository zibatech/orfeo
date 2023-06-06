<?php
/*************************************************************************************/
/* ORFEO GPL:Sistema de Gestion Documental		http://www.orfeogpl.org	     */
/*	Idea Original de la SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS     */
/*				COLOMBIA TEL. (57) (1) 6913005  orfeogpl@gmail.com   */
/* ===========================                                                       */
/*                                                                                   */
/* Este programa es software libre. usted puede redistribuirlo y/o modificarlo       */
/* bajo los terminos de la licencia GNU General Public publicada por                 */
/* la "Free Software Foundation"; Licencia version 2. 			             */
/*                                                                                   */
/* Copyright (c) 2005 por :	  	  	                                     */
/* SSPS "Superintendencia de Servicios Publicos Domiciliarios"                       */
/*   Jairo Hernan Losada  jlosada@gmail.com                Desarrollador             */
/*   Sixto Angel Pinzón López --- angel.pinzon@gmail.com   Desarrollador             */
/* C.R.A.  "COMISION DE REGULACION DE AGUAS Y SANEAMIENTO AMBIENTAL"                 */ 
/*   Liliana Gomez        lgomezv@gmail.com                Desarrolladora            */
/*   Lucia Ojeda          lojedaster@gmail.com             Desarrolladora            */
/* D.N.P. "Departamento Nacional de Planeación"                                      */
/*   Hollman Ladino       hladino@gmail.com                Desarrollador             */
/*                                                                                   */
/* Colocar desde esta lInea las Modificaciones Realizadas Luego de la Version 3.5    */
/*  Nombre Desarrollador   Correo     Fecha   Modificacion                           */
/*************************************************************************************/
session_start();
$ruta_raiz = "../..";
// Modificado Infom�trika 24-Septiembre-2009
// Compatibilidad con register_globals = Off
$dependencia = $_SESSION["dependencia"];

if (!$dependencia)   include "../../rec_session.php";
$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$tip3Nombre  = $_SESSION["tip3Nombre"];
$tip3desc    = $_SESSION["tip3desc"];
$tip3img     = $_SESSION["tip3img"];

$nomcarpeta=$_GET["carpeta"];
$tipo_carpt=$_GET["tipo_carpt"];
$adodb_next_page=$_GET["adodb_next_page"];
if($_GET["dep_sel"]) $dep_sel=$_GET["dep_sel"];
if($_GET["srd_sel"]) $srd_sel=$_GET["srd_sel"];
if($_GET["orderTipo"]) $orderTipo=$_GET["orderTipo"];
if($_GET["busqRadicados"]) $busqRadicados=$_GET["busqRadicados"];
if($_GET["busq_radicados"]) $busq_radicados=$_GET["busq_radicados"];
if($_GET["depeBuscada"]) $depeBuscada=$_GET["depeBuscada"];

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$ano_ini = date("Y");
$mes_ini = substr("00".(date("m")-1),-2);
if ($mes_ini==0) {$ano_ini=$ano_ini-1; $mes_ini="12";}
$dia_ini = date("d");
$ano_ini = date("Y");
if(!$fecha_ini) $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
$fecha_fin = date("Y/m/d") ;
$where_fecha="";
$radSelec = "";
//$tpDepeRad = "NADA";
?>

<html>
<head>
<title>Envio de Documentos. Orfeo...</title>
<link rel="stylesheet" href="<?=$ruta_raiz."/estilos/".$_SESSION["ESTILOS_PATH"]?>/orfeo.css">
<?php include_once "../../htmlheader.inc.php"; ?>
</head>
<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
<div id="spiffycalendar" class="text"></div>
<link rel="stylesheet" type="text/css" href="js/spiffyCal/spiffyCal_v2_1.css">

<?
 $ruta_raiz = "../..";
 include_once "$ruta_raiz/include/db/ConnectionHandler.php";
 $db = new ConnectionHandler("$ruta_raiz");	 
 $db->conn->debug=true;
 if (!$dep_sel) $dep_sel = $dependencia;
 $nomcarpeta = "Consulta Usuarios";

 if ($busq_radicados) {
    $busq_radicados = trim($busq_radicados);
    $textElements = split (",", $busq_radicados);
    $newText = "";
    $i = 0;
    foreach ($textElements as $item)  {
    	$item = trim ( $item );
        if ( strlen ( $item ) != 0 ) { 
		   $i++; 
		   if ($i > 1) $busq_and = " and "; else $busq_and = " ";
		      $busq_radicados_tmp .= " $busq_and radi_nume_sal like '%$item%' ";
		}
     }
	 $dependencia_busq1 .= " and $busq_radicados_tmp ";
				
 }else  {
    $sql_masiva = "";
 }

 if ($orden_cambio==1)  {
 	if (!$orderTipo)  {
	   $orderTipo="desc";
	}else  {
	   $orderTipo="";
	}
 }

 $encabezado = "".session_name()."=".session_id()."&krd=$krd&pagina_sig=$pagina_sig&accion_sal=$accion_sal&radSelec=$radSelec&dependencia=$dependencia&dep_sel=$dep_sel&selecdoc=$selecdoc&nomcarpeta=$nomcarpeta&orderTipo=$orderTipo&orderNo=";
 $linkPagina = "$PHP_SELF?$encabezado&radSelec=$radSelec&accion_sal=$accion_sal&nomcarpeta=$nomcarpeta&orderTipo=$orderTipo&orderNo=$orderNo";
 $carpeta = "nada";
 $swBusqDep = "si";
 $pagina_actual = "../usuario/cuerpoConsulta.php";
 $perm_trd=true;
 include "../paEncabeza.php";
 $varBuscada = "usua_login";
 $tituloBuscar = "Buscar Usuario(s) (Separados por coma)";
 include "../paBuscar.php";   
 $pagina_sig = "../usuario/consultaDatosGrales.php";
 $accion_sal = "Grabar"; 
 include "../paOpciones.php";   
 if($busq_radicados_tmp)  {
   $where_fecha=" ";
 }
 else  {
    $fecha_ini = mktime(00,00,00,substr($fecha_ini,5,2),substr($fecha_ini,8,2),substr($fecha_ini,0,4));
	$fecha_fin = mktime(23,59,59,substr($fecha_fin,5,2),substr($fecha_fin,8,2),substr($fecha_fin,0,4));
    $where_fecha = " (a.SGD_RENV_FECH >= ". $db->conn->DBTimeStamp($fecha_ini) ." and a.SGD_RENV_FECH <= ". $db->conn->DBTimeStamp($fecha_fin).") " ;
    $dependencia_busq1 .= " $where_fecha and ";
 } 

	/*  GENERACION LISTADO DE RADICADOS
	 *  Aqui utilizamos la clase adodb para generar el listado de los radicados
	 *  Esta clase cuenta con una adaptacion a las clases utiilzadas de orfeo.
	 *  el archivo original es adodb-pager.inc.php la modificada es adodb-paginacion.inc.php
	 */
 if (!$checkValue){
	include "$ruta_raiz/include/query/administracion/queryCuerpoConsulta.php";
	$isql="select usua_login from usuario u, dependencia d where ".end(explode("where",$isql))." 1";
/*	if($perm_trd){
		echo "<br>";
$sql_series="select distinct concat(s.sgd_srd_codigo,' - ',s.sgd_srd_descrip),s.sgd_srd_codigo  from sgd_mrd_matrird m left join sgd_srd_seriesrd s on (m.sgd_srd_codigo=s.sgd_srd_codigo) where  m.sgd_mrd_esta='1'";
//echo "<pre>$sql_series</pre>";
$rs_series=$db->conn->Execute($sql_series);
print $rs_series->GetMenu2("srd_sel","$srd_sel",false, false, 0," onChange='submit();' class='select'");
	}*/
	$rs=$db->conn->GetArray($isql);
	$_users="'".implode("','",(array_column($rs,"USUA_LOGIN")))."'";
	/*echo $lastSql="update usuario set usua_perm_td=regexp_replace(usua_perm_td,',$srd_sel,',',') where usua_perm_td !=',' and usua_perm_td ilike '%$srd_sel,%' and usua_login in ($_users)";
	$db->conn->Execute($lastSql);*/
 }
 if ($checkValue){
	 $keys=array_keys($checkValue);
	 foreach ($keys as $user){
		 $_users[]=end(explode("-",$user));
	 }
	 $_users="'".implode("','",$_users)."'";
	$lastSql="update usuario set usua_perm_td=regexp_replace(usua_perm_td,',$srd_sel,',',') where usua_perm_td !=',' and usua_perm_td ilike '%$srd_sel,%' and usua_login not in ($_users)";
	$db->conn->Execute($lastSql);
 }

$select_sql="select usua_doc||'-'||usua_login as usua_login, usua_perm_td from usuario where depe_codi =$dep_sel";
$rs=$db->conn->GetArray($select_sql);
foreach($rs as $kk){
	$perm[$kk["USUA_LOGIN"]]["TD"]=$kk["USUA_PERM_TD"];
	if (strstr($kk["USUA_PERM_TD"],",$srd_sel,") and !empty($srd_sel)){
		$perm[$kk["USUA_LOGIN"]]["perm"]=true;
	}else{
		$perm[$kk["USUA_LOGIN"]]["perm"]=false;
	}
}
echo "<pre>";
//var_dump($perm);
 if ($checkValue){
	 //var_dump($checkValue);
echo "</pre>";
	 $keys=array_keys($checkValue);
	 foreach ($keys as $user){
		 if (!$perm[$user]["perm"]){
			$perm[$user]["perm"]=true;
		 	$users[]=end(explode("-",$user));
		 }
	 }
	 $users="'".implode("','",$users)."'";
	 $update_sql="update usuario set usua_perm_td=usua_perm_td || '$srd_sel,' where usua_login in ($users)";
	 if (!empty($users)){
		$db->conn->Execute($update_sql);
	 }
 }
?>
	<form name=formEnviar action='?<?=$encabezado?>&usModo=2&busqRadicados=<?=$busqRadicados?>' method=post>
  <input type=hidden name=srd_sel value="<?=$srd_sel?>">
  <input type=hidden name=busqRadicados value="<?=$busqRadicados?>">
 <?
    if ($orderNo==98 or $orderNo==99) {
       $order=1; 
	   if ($orderNo==98)   $orderTipo="desc";

       if ($orderNo==99)   $orderTipo="";
	}  
    else  {
	   if (!$orderNo)  {
  		  $orderNo=0;
	   }
	   $order = $orderNo + 1;
    }
	$sqlChar = $db->conn->SQLDate("d-m-Y H:i A","SGD_RENV_FECH");
	$sqlConcat = $db->conn->Concat("a.radi_nume_sal","'-'","a.sgd_renv_codigo","'-'","a.sgd_fenv_codigo","'-'","a.sgd_renv_peso");
	include "$ruta_raiz/include/query/administracion/queryCuerpoConsulta.php";


    $rs=$db->conn->Execute($isql);
	$nregis = $rs->fields["NOMBRE"];		
	if (!$nregis)  {
		echo "<hr><center><b>NO se encontro nada con el criterio de busqueda</center></b></hr>";}
	else  {
		$pager = new ADODB_Pager($db,$isql,'adodb', true,$orderNo,$orderTipo);
		$pager->toRefLinks = $linkPagina;
		$pager->toRefVars = $encabezado;
		$pager->Render($rows_per_page=20,$linkPagina,$checkbox=chkEnviar);
	}
 ?>
  </form>

<?
?>
</body>
<script>
<?
foreach($perm as $k => $v){
	if ($v["perm"]==true){
echo "document.getElementById('$k').checked = true\n";
	}
}
?>
</script>
<a href="srdByUser.php?dep=<?=$dep_sel?>">Reporte series por usuario</a>
<a href="userBySrd.php?srd=<?=$srd_sel?>">Reporte usuarios por serie</a>
</html>
