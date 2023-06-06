<?
session_start();
$ruta_raiz = "..";
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
$depe_codi_territorial     = $_SESSION["depe_codi_territorial"];
$anoActual = date("Y");
$ruta_raiz = "..";

if($_GET["gen_lisDefi"]) $gen_lisDefi=$_GET["gen_lisDefi"];
if($_GET["dep_sel"]) $dep_sel=$_GET["dep_sel"];
if($_GET["orderNo"]) $orderNo=$_GET["orderNo"];
if($_GET["orderTipo"]) $orderTipo=$_GET["orderTipo"];
if($_GET["busqRadicados"]) $busqRadicados=$_GET["busqRadicados"];
if($_GET["tipoEnvio"]) $tipoEnvio=$_GET["tipoEnvio"];
if($_GET["estado_sal_max"]) $estado_sal_max=$_GET["estado_sal_max"];
if($_GET["estado_sal"]) $estado_sal=$_GET["estado_sal"];
if($_GET["Buscar"]) $Buscar=$_GET["Buscar"];
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");

define('ADODB_FETCH_ASSOC',2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

$anoActual = date("Y");
$ano_ini = date("Y");
$mes_ini = substr("00".(date("m")-1),-2);
if ($mes_ini==0) {$ano_ini=$ano_ini-1; $mes_ini="12";}
$dia_ini = date("d");
$ano_ini = date("Y");
if(!$fecha_ini) $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
$fecha_fin = date("Y/m/d") ;
$where_fecha="";
?>
<html>
<head>
<title>Orfeo. Devolucion de Correspondencia</title>
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
</head>

<body>
<?
include_once "$ruta_raiz/js/funtionImage.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
?>
<script>

pedientesFirma="";
function back() {
    history.go(-1);
}

function recargar(){
	window.location.reload();	
}

function continuar(){
	accion = '<?=$pagina_sig?>?<?=session_name()."=".session_id()."&krd=$krd&fechah=$fechah&dep_sel=$dep_sel&estado_sal=$estado_sal&usua_perm_impresion=$usua_perm_impresion&estado_sal_max=$estado_sal_max" ?>';
	alert (accion);
}

function cambioDependecia (dep){
	document.formDep.action="cuerpo_masiva.php?krd=<?=$krd?>&dep_sel="+dep;
	//alert(document.formDep.action);
	document.formDep.submit();
}

function marcar() {
	marcados = 0;
  for(i=0;i<document.formEnviar.elements.length;i++) {
		if(document.formEnviar.elements[i].checked==1) {
			marcados++;
		}
	}
	if(marcados>=1) {
		if (valPendsFirma())
			document.formEnviar.submit();
	} else {
		alert("Debe seleccionar un radicado");
	}
}

</script>
<?php

if(!$estado_sal)   {$estado_sal=3;}
if(!$estado_sal_max) $estado_sal_max=4;
if($estado_sal==4) {
	if($devolucion==1) {
		$accion_sal = "Devolucion de Documentos para la Gestion";
		$pagina_sig = "dev_corresp_gestion.php";
		$dev_documentos = "";
		$nomcarpeta="Devolucion de Documentos";
	}
	if(!$dep_sel) $dep_sel = $dependencia;
}
if($busq_radicados) {
	$busq_radicados = trim($busq_radicados);
	$textElements = split (",", $busq_radicados);
	$newText = "";
	$i = 0;
	foreach ($textElements as $item) {
		$item = trim ( $item );
		if ( strlen ( $item ) != 0 ) {
		   $i++;
		   if ($i != 1) $busq_and = " and "; else $busq_and = " ";
		   $busq_radicados_tmp .= " $busq_and radi_nume_sal like '%$item%' ";
		  }
     }
	 $dependencia_busq1 .= " $busq_radicados_tmp ";
	 if(!$dep_sel) $dep_sel = $dependencia;
}

$tbbordes = "#CEDFC6";
$tbfondo = "#FFFFCC";
if(!$orno){$orno=1;}
$imagen="flechadesc.gif";


 $encabezado = "".session_name()."=".session_id()."&krd=$krd&filtroSelect=$filtroSelect&accion_sal=$accion_sal&dependencia=$dependencia&tpAnulacion=$tpAnulacion&orderNo=";
 $linkPagina = "$PHP_SELF?$encabezado&accion_sal=$accion_sal&orderTipo=$orderTipo&orderNo=$orderNo";

 $swBusqDep = "si";
 $pagina_actual = "../devolucion/cuerpoDevGestion.php";
 $carpeta = "xx";
 include "../envios/paEncabeza.php";
 $varBuscada = "radi_nume_salida";
 include "../envios/paBuscar.php";
 $accion_sal = "Devolucion de Documentos";
 $pagina_sig = "../devolucion/dev_corresp_gestion.php";
 include "../envios/paOpciones.php";
 $orderNo=98;
 $orderTipo="desc";
 error_reporting(7);
$sqlChar = $db->conn->SQLDate("d-m-Y H:i A","SGD_RENV_FECH");
if($dep_sel != 9999 && $dep_sel != '') {
	$dependencia_busq2 .= " and c.radi_depe_radi  = $dep_sel";
} else {
	$dependencia_busq2 .= '';
}
 $orderNo=98;
 $orderTipo="desc";

if (!empty($tipoEnvio)){
    //Start::se valida si hay registros si no se crean
    $iSqlPreparado= "select a.id as id_anexo,sdd.id as id_dir,'E-mail' as tipo,1 as estado from anexos a
                    join sgd_dir_drecciones sdd on a.anex_radi_nume = sdd.radi_nume_radi
                    where a.anex_radi_nume in ('$busqRadicados') ";
    $rsPreparado = $db->conn->query($iSqlPreparado);

    if ($rsPreparado) {
        while(!$rsPreparado->EOF){
            $iSqlExiste= "SELECT count(*) as EXISTE
                                FROM SGD_RAD_ENVIOS
                                WHERE id_anexo =  '".$rsPreparado->fields['ID_ANEXO']."' and id_direccion = '".$rsPreparado->fields['ID_DIR']."' and tipo = '".$tipoEnvio."'";
            $rsExiste = $db->conn->query($iSqlExiste);
            $iSqlExisteEnviado= "SELECT count(*) as EXISTE
                                FROM SGD_RAD_ENVIOS env
                                JOIN ANEXOS a
                                ON a.id = env.id_anexo
                                WHERE env.id_anexo =  '".$rsPreparado->fields['ID_ANEXO']."' and env.id_direccion = '".$rsPreparado->fields['ID_DIR']."' and env.tipo = '".$tipoEnvio."' and env.estado=2 and a.anex_estado >2";
            $rsExisteEnviado = $db->conn->query($iSqlExisteEnviado);
            if($rsExiste && intval($rsExiste->fields["EXISTE"]) == 0) {
                $isqlPivot = "INSERT INTO SGD_RAD_ENVIOS(
                                        id_anexo, 
                                        id_direccion, 
                                        tipo, 
                                        estado)
                                        VALUES(
                                        ".$rsPreparado->fields['ID_ANEXO'].", 
                                        ".$rsPreparado->fields['ID_DIR'].",
                                        '".$tipoEnvio."', 
                                        ".$rsPreparado->fields['ESTADO'].")";
                $rsPivot=$db->conn->query($isqlPivot);
                if(!$rsPivot){
                    //$this->conexion->conn->RollbackTrans();
                    //die ("$isql <span class='etextomenu'>No se ha podido isertar la informaci&ocute;n en SGD_RAD_ENVIOS");
                }
            }

            if($rsExisteEnviado && intval($rsExisteEnviado->fields["EXISTE"]) > 0 ) {
                $isqlPivot = "UPDATE SGD_RAD_ENVIOS SET estado  =1 where id_anexo = ".$rsPreparado->fields['ID_ANEXO']." AND id_direccion = ".$rsPreparado->fields['ID_DIR'];
                $rsPivot=$db->conn->query($isqlPivot);
                if(!$rsPivot){
                    //$this->conexion->conn->RollbackTrans();
                    //die ("$isql <span class='etextomenu'>No se ha podido isertar la informaci&ocute;n en SGD_RAD_ENVIOS");
                }
            }
            $rsPreparado->MoveNext();
        }
    }
    //End::se valida si hay registros si no se crean
}

include "$ruta_raiz/include/query/devolucion/querycuerpoDevGestion.php";
 $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
?>
  <form name=formEnviar action='../devolucion/dev_corresp_gestion.php?<?=session_name()."=".session_id()."&krd=$krd" ?>&estado_sal=<?=$estado_sal?>&estado_sal_max=<?=$estado_sal_max?>&pagina_sig=<?=$pagina_sig?>&dep_sel=<?=$dep_sel?>&nomcarpeta=<?=$nomcarpeta?>&orderNo=<?=$orderNo?>' method=post>
 <?
	$encabezado = "".session_name()."=".session_id()."&krd=$krd&estado_sal=$estado_sal&estado_sal_max=$estado_sal_max&accion_sal=$accion_sal&dependencia_busq2=$dependencia_busq2&dep_sel=$dep_sel&filtroSelect=$filtroSelect&nomcarpeta=$nomcarpeta&orderTipo=$orderTipo&orderNo=";
        $encabezado = session_name()."=".session_id()."&dep_sel=$dep_sel&krd=$krd&estado_sal=$estado_sal&usua_perm_impresion=$usua_perm_impresion&fechah=$fechah&estado_sal_max=$estado_sal_max&ascdesc=$ascdesc&orno=";
	$linkPagina = "$PHP_SELF?$encabezado&orderTipo=$orderTipo&orderNo=$orderNo";
	$pager = new ADODB_Pager($db,$isql,'adodb', true,$orderNo,$orderTipo);
	$pager->checkAll = false;
	$pager->checkTitulo = true;
	$pager->toRefLinks = $linkPagina;
	$pager->toRefVars = $encabezado;
	if($_GET["adodb_next_page"]) $pager->curr_page = $_GET["adodb_next_page"];
	$pager->Render($rows_per_page=20,$linkPagina,$checkbox=chkEnviar);

 ?>
  </form>
</body>
</html>
