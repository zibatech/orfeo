<?
  session_start();
/*
 * Lista Subseries documentales
 * @autor Jairo Losada
 * @fecha 2009/06 Modificacion Variables Globales.
 */
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
  $ruta_raiz = "..";
  if(!$_SESSION['dependencia']) include "$ruta_raiz/rec_session.php";
  if(!$coddepe) $coddepe=$dependencia;
  if(!$tsub) $tsub=0;
  if(!$codserie) $codserie=0;
  if(!$idSerie) $idSerie=0;
  if(!$idSubSerie) $idSubSerie=0;
  $fecha_fin = date("Y/m/d") ;
  $where_fecha="";
//error_reporting(7);
?>
<html>
<head>
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
<!--<link rel="stylesheet" href="../estilos/orfeo.css">-->
</head>

<body bgcolor="#FFFFFF" topmargin="0" >
<div id="spiffycalendar" class="text"></div>
<link rel="stylesheet" type="text/css" href="js/spiffyCal/spiffyCal_v2_1.css">
<?
 $ruta_raiz = "..";
 include_once "$ruta_raiz/include/db/ConnectionHandler.php";
 $db = new ConnectionHandler("$ruta_raiz");	
 
 $encabezado = "".session_name()."=".session_id()."&krd=$krd&filtroSelect=$filtroSelect&accion_sal=$accion_sal&dependencia=$dependencia&tpAnulacion=$tpAnulacion&orderNo=";
 $linkPagina = "$PHP_SELF?$encabezado&accion_sal=$accion_sal&orderTipo=$orderTipo&orderNo=$orderNo";
  /*  GENERACION LISTADO DE RADICADOS
	 *  Aqui utilizamos la clase adodb para generar el listado de los radicados
	 *  Esta clase cuenta con una adaptacion a las clases utiilzadas de orfeo.
	 *  el archivo original es adodb-pager.inc.php la modificada es adodb-paginacion.inc.php
  */
  error_reporting(7);
 		
?>
  <form name=formEnviar action='../trd/procModTrdArea.php?<?=session_name()."=".session_id()."&krd=$krd" ?>&estado_sal=<?=$estado_sal?>&estado_sal_max=<?=$estado_sal_max?>&pagina_sig=<?=$pagina_sig?>&dep_sel=<?=$dep_sel?>&nomcarpeta=<?=$nomcarpeta?>&orderNo=<?=$orderNo?>' method=post>
 <?
  if ($activar_trda)
      {
	  $valCambio = '1';
	  }
   if ($desactivar_trda)
      {
	  $valCambio = '0';
	  }
	
    if ($desactivar_trda)
   {
       if ($idSerie!=0)
	      {
	       $var_where = " and sgd_srd_id = '$idSerie'";
		   if ( $idSubSerie!=0 )
               {
			       $var_where = $var_where. " and sgd_sbrd_id = '$idSubSerie'";
				   if ( $tdoc != 0 )
               		  {
					     $var_where = $var_where. " and sgd_tpr_codigo = '$tdoc'";
			           }
				}
		    $bien=true;
   			if ($bien){
   			    $ayer = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
     		    $isqlActi = "update SGD_MRD_MATRIRD set SGD_MRD_ESTA='$valCambio' , sgd_mrd_fechfin = '$ayer' ".
            	    "where depe_codi = '$coddepe'" . $var_where;
                $bien= $db->query($isqlActi);
   			}
            if ($bien){
                $mensaje="Modificado el Estado de la Relacion segun los parametros seleccionados<br> ";
                $db->conn->CommitTrans();
                }
	        else {
                $mensaje="No fue posible Activar la Relacion segun los parametros</br>";
                $db->conn->RollbackTrans();
	        }
		  }
		  else
		  {
		    echo "<script>alert('Debe seleccionar por lo menos la Serie');</script>";
		  }
   }

    if ($activar_trda)
    {
     if ($idSerie!=0)
     {
         $var_where = " and sgd_srd_id = '$idSerie'";
         if ( $idSubSerie!=0 )
         {
             $var_where = $var_where. " and sgd_sbrd_id = '$idSubSerie'";
             if ( $tdoc != 0 )
             {
                 $var_where = $var_where. " and sgd_tpr_codigo = '$tdoc'";
             }
         }
         $bien=true;
         if ($bien){
             $ayer = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
             $isqlActi = "update SGD_MRD_MATRIRD set SGD_MRD_ESTA='$valCambio' , sgd_mrd_fechfin = '2040-12-31 23:00:00' ".
                 "where depe_codi = '$coddepe'" . $var_where;
             $bien= $db->query($isqlActi);

         }
         if ($bien){
             $mensaje="Modificado el Estado de la Relacion segun los parametros seleccionados<br> ";
             $db->conn->CommitTrans();
         }
         else {
             $mensaje="No fue posible Activar la Relacion segun los parametros</br>";
             $db->conn->RollbackTrans();
         }
     }
     else
     {
         echo "<script>alert('Debe seleccionar por lo menos la Serie');</script>";
     }
 }
 ?>
 <br>
 <table class=borde_tab width='100%' cellspacing="5"><tr><td class=titulos2><center>MODIFICACION RELACION TRD</center></td></tr></table>
 <br>
 <table><tr><td></td></tr></table>
 <center>

<TABLE width="550" class="borde_tab" cellspacing="5">       
<TR>
    <TD width="125" height="21"  class='titulos2'> DEPENDENCIA</td>
      <td colspan="3"  class="listado5"> 
        <?		
			include_once "$ruta_raiz/include/query/envios/queryPaencabeza.php";			
 			$sqlConcat = $db->conn->Concat($db->conn->substr."($conversion,1,5) ", "'-'",$db->conn->substr."(depe_nomb,1,30) ");
			$sql = "select $sqlConcat ,depe_codi from dependencia where depe_estado=1
							order by depe_codi
							";
			$rsDep = $db->conn->Execute($sql);
			if(!$depeBuscada) $depeBuscada=$dependencia;
			print $rsDep->GetMenu2("coddepe","$coddepe",false, false, 0," onChange='submit();' class='select'");
?>
</td>
    </tr>
  <TR>
    <TD width="125" height="21"  class='titulos2'> SERIE </td>
      <td colspan="3"  class="listado5"> 
  <?php
    include "$ruta_raiz/trd/actu_matritrd.php";  
    if(!$codserie) $codserie = 0;
	$fechah=date("dmy") . " ". time("h_m_s");
	$fecha_hoy = Date("Y-m-d");
	$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
	$check=1;
	$fechaf=date("dmy") . "_" . time("hms");
	$num_car = 4;
	$nomb_varc = "s.sgd_srd_codigo";
	$nomb_varde = "s.sgd_srd_descrip";
   	include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
	$querySerie = "select distinct ($sqlConcat) as detalle,id, s.sgd_srd_codigo 
	      from sgd_srd_seriesrd s,sgd_mrd_matrird m 
		  where s.id = m.sgd_srd_id 
		  and m.depe_codi = '$coddepe'
		  order by detalle
		  ";
	$rsD=$db->conn->query($querySerie);
	$comentarioDev = "Muestra las Series Docuementales";
	include "$ruta_raiz/include/tx/ComentarioTx.php";
	print $rsD->GetMenu2("idSerie", $idSerie, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
 ?>
   <TR>
    <TD width="125" height="21"  class='titulos2'> SUBSERIE</td>
      <td colspan="3"  class="listado5">
	<?
	$nomb_varc = "su.sgd_sbrd_codigo";
	$nomb_varde = "su.sgd_sbrd_descrip";
	include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
   	$querySub = "select distinct ($sqlConcat) as detalle,su.id, su.sgd_sbrd_codigo 
	      from sgd_sbrd_subserierd su, sgd_mrd_matrird m , sgd_srd_seriesrd s
		  where su.sgd_srd_id = '$idSerie'
 		  and ".$sqlFechaHoy." between su.sgd_sbrd_fechini and su.sgd_sbrd_fechfin
		  and m.depe_codi = '$coddepe'
		  and s.id = m.sgd_srd_id
		   order by detalle
			  ";
			  //$db->conn->debug = true;
	$rsSub=$db->conn->query($querySub);
	include "$ruta_raiz/include/tx/ComentarioTx.php";
	print $rsSub->GetMenu2("idSubSerie", $idSubSerie, "0:-- Todas las subseries documentales --", false,"","onChange='submit()' class='select'" );

?> 
  </td>
  <TR>
    <TD width="125" height="21"  class='titulos2'>TIPO DOCUMENTAL</td>
      <td colspan="3"  class="listado5"> 
	  <?
		$nomb_varc = "t.sgd_tpr_codigo";
		$nomb_varde = "t.sgd_tpr_descrip";
		include "$ruta_raiz/include/query/trd/queryCodiDetalle.php"; 
   		$queryTipDcto = "select distinct ($sqlConcat) as detalle, t.sgd_tpr_codigo 
	          from sgd_tpr_tpdcumento t, sgd_mrd_matrird m ,sgd_sbrd_subserierd su
			  where m.depe_codi = '$coddepe'
			  and m.sgd_srd_id = '$idSerie'
			  and m.sgd_sbrd_id = '$idSubSerie'
			  and m.sgd_tpr_codigo = t.sgd_tpr_codigo
 			  and ".$sqlFechaHoy." between su.sgd_sbrd_fechini and su.sgd_sbrd_fechfin
		      and su.sgd_srd_codigo = m.sgd_srd_codigo 
 		      and su.sgd_sbrd_codigo = m.sgd_sbrd_codigo
			 order by detalle
			  ";
		$rsTipDcto =$db->conn->query($queryTipDcto);
		include "$ruta_raiz/include/tx/ComentarioTx.php";
		print $rsTipDcto->GetMenu2("tdoc", $tdoc, "0:-- Todos los tipos documentales --", false,"","onChange='submit()' class='select'" );
?>
  </td>
  </tr>
 <tr>
       <td height="26" colspan="4" valign="top" class='titulos2'> 
	   <center>
	   <br>
	  <input type=submit name=activar_trda value='Activar' class="btn btn-primary"  >
	  <input type=submit name=desactivar_trda value='Desactivar' class="btn btn-danger"  > 
      </td>
    </tr>
  </table>
<br>
 <? echo "<hr><center><b><span class='alarmas'>$mensaje</span></center></b></hr>"; ?>
  </form>
</body>
</html>
