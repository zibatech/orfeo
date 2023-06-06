<?php
/**
 * @author Jairo Losada   <jlosada@gmail.com>
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @author Wilson Hernandez <wilsonhernandezortiz@gmail.com>
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

session_start();

$ruta_raiz = "..";
if (!$_SESSION['dependencia'])
	header ("Location: $ruta_raiz/cerrar_session.php");
require  "../vendor/autoload.php";

$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$tip3Nombre  = $_SESSION["tip3Nombre"];
$tip3desc    = $_SESSION["tip3desc"];
$tip3img     = $_SESSION["tip3img"];

/*
 * Lista Subseries documentales
 * @autor Jairo Losada
 * @fecha 2009/06 Modificacion Variables Globales. Arreglo cambio de los request Gracias a recomendacion de Hollman Ladino
 */

foreach ($_POST as $key => $valor)   ${$key} = $valor;
foreach ($_GET as $key => $valor)   ${$key} = $valor;


if (!$dep_sel) $dep_sel = $dependencia;
?>
<html>
<head>
<title>Sistema de informaci&oacute;n <?=$entidad_largo?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Bootstrap core CSS -->
<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>
<body >
<a class="enviar-todo btn" onclick="enviar_todo()">enviar todos</a>
<script type="text/javascript">
	function enviar_todo(){
		$('.btn-info').click(); 
	}
</script>
<?php

include_once "$ruta_raiz/js/funtionImage.php";
include_once "$ruta_raiz/processConfig.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
if(!$carpeta) $carpeta=0;
if(!$estado_sal)   {$estado_sal=2;}
if(!$estado_sal_max) $estado_sal_max=3;

if($estado_sal==3 && (!$bTodasDep && !$busqradicados  )  ) {
	$accion_sal = "Envio de Documentos";
	$pagina_sig = "cuerpoEnvioNormal.php";
	$nomcarpeta = "Radicados Para Envio";
	if(!$dep_sel) $dep_sel = $dependencia;
	$dependencia_busq1 = " and c.radi_depe_radi = $dep_sel ";
	$dependencia_busq2 = " and c.radi_depe_radi = $dep_sel";
}
$accion_sal = "Envio de Documentos";

if ($orden_cambio==1)  {
	if (!$orderTipo)  {
		$orderTipo="desc";
	}else  {
		$orderTipo="";
	}
}

$encabezado = "".session_name()."=".session_id()."&estado_sal_max=$estado_sal_max&accion_sal=$accion_sal&dependencia_busq2=$dependencia_busq2&dep_sel=$dep_sel&filtroSelect=$filtroSelect&tpAnulacion=$tpAnulacion&nomcarpeta=$nomcarpeta&orderTipo=$orderTipo&orderNo=";
$linkPagina = "$PHP_SELF?$encabezado&orderNo = $orderNo";
$swBusqDep  = "si";
$carpeta    = "nada";
$swListar   = "Si";
include "$ruta_raiz/envios/paEncabeza.php";
include "$ruta_raiz/envios/paBuscar.php";   
include "$ruta_raiz/envios/paOpciones.php";   
$pagina_actual = "$ruta_raiz/envios/cuerpoEnvioNormal.php";
$varBuscada    = "radi_nume_salida";
$pagina_sig    = "$ruta_raiz/envios/envia.php";

/*  GENERACION LISTADO DE RADICADOS
 *  Aqui utilizamos la clase adodb para generar el listado de los radicados
 *  Esta clase cuenta con una adaptacion a las clases utiilzadas de orfeo.
 *  el archivo original es adodb-pager.inc.php la modificada es adodb-paginacion.inc.php
 */

?>

<?php 
if ($orderNo==98 or $orderNo==99) {
	$order=1; 
	if ($orderNo==98)   $orderTipo="desc";
	if ($orderNo==99)   $orderTipo="";
}  
else  {
	if (!$orderNo)  {
		$orderNo=3;
		$orderTipo="desc";
	}
	$order = $orderNo + 1;
}

$radiPath = $db->conn->Concat($db->conn->substr."(a.anex_codigo,1,4) ", "'/'",$db->conn->substr."(a.anex_codigo,5,3) ","'/docs/'","a.anex_nomb_archivo");
include "$ruta_raiz/include/query/envios/queryCuerpoEnvioE-mail.php";

//Start::se valida si hay registros si no se crean que no sean de notificaciones
$iSqlPreparado= "select a.id as id_anexo,sdd.id as id_dir,'E-mail' as tipo,1 as estado 
				from anexos a
                join sgd_dir_drecciones sdd on a.anex_radi_nume = sdd.radi_nume_radi
                where a.radi_nume_salida in ('$busqRadicados') 
                and (a.sgd_trad_codigo < 4 or a.sgd_trad_codigo > 7)";
$rsPreparado = $db->conn->query($iSqlPreparado);
if ($rsPreparado) {
    while(!$rsPreparado->EOF){
            $iSqlExiste= "SELECT count(*) as EXISTE
                                FROM SGD_RAD_ENVIOS
                                WHERE id_anexo =  '".$rsPreparado->fields['ID_ANEXO']."' and id_direccion = '".$rsPreparado->fields['ID_DIR']."' and tipo = 'E-mail' and estado != -1";
            $rsExiste = $db->conn->query($iSqlExiste);
            $iSqlExisteEnviado= "SELECT count(*) as EXISTE
                                FROM SGD_RAD_ENVIOS env
                                JOIN ANEXOS a
                                ON a.id = env.id_anexo
                                WHERE env.id_anexo =  '".$rsPreparado->fields['ID_ANEXO']."' and env.id_direccion = '".$rsPreparado->fields['ID_DIR']."' and env.tipo = 'E-mail' and env.estado=2 and a.anex_estado !=4";
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
                                        'E-mail', 
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
//End::se valida si hay registros si no se crean que no sean de notificaciones


$rs=$db->conn->Execute($isql);


$isql2="
select
	r.radi_nume_radi
from
	radicado r,
	anexos a,
	sgd_dir_drecciones d
where
	r.radi_depe_radi = ".$_GET['dep_sel']."
	and a.radi_nume_salida = r.radi_nume_radi
	and a.anex_estado = 3
	and r.mrec_codi = 4
	and r.radi_nume_radi = d.radi_nume_radi
	and d.id not in (
	select
		id_direccion
	from
		sgd_rad_envios)
	and r.sgd_trad_codigo = 1
";

$rs2=$db->conn->Execute($isql2);


if($dep_sel <> '9999')
{

		while(!$rs2->EOF)
		{
					$busqRadicados=$rs2->fields['RADI_NUME_RADI'];

					//Start::se valida si hay registros si no se crean que no sean de notificaciones
					$iSqlPreparado= "select a.id as id_anexo,sdd.id as id_dir,'E-mail' as tipo,1 as estado 
									from anexos a
					                join sgd_dir_drecciones sdd on a.anex_radi_nume = sdd.radi_nume_radi
					                where a.radi_nume_salida =$busqRadicados 
					                and (a.sgd_trad_codigo < 4 or a.sgd_trad_codigo > 7)";



					$rsPreparado = $db->conn->query($iSqlPreparado);
					if ($rsPreparado) {
					    while(!$rsPreparado->EOF){
					            $iSqlExiste= "SELECT count(*) as EXISTE
					                                FROM SGD_RAD_ENVIOS
					                                WHERE id_anexo =  '".$rsPreparado->fields['ID_ANEXO']."' and id_direccion = '".$rsPreparado->fields['ID_DIR']."' and tipo = 'E-mail' and estado != -1";
					            $rsExiste = $db->conn->query($iSqlExiste);
					            $iSqlExisteEnviado= "SELECT count(*) as EXISTE
					                                FROM SGD_RAD_ENVIOS env
					                                JOIN ANEXOS a
					                                ON a.id = env.id_anexo
					                                WHERE env.id_anexo =  '".$rsPreparado->fields['ID_ANEXO']."' and env.id_direccion = '".$rsPreparado->fields['ID_DIR']."' and env.tipo = 'E-mail' and env.estado=2 and a.anex_estado !=4";
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
					                                        'E-mail', 
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
					//End::se valida si hay registros si no se crean que no sean de notificaciones

					$rs2->MoveNext();
		}

}

if ($rs->EOF){
	echo "<table class='table table-bordered' ><tr><td class=titulosError><center>NO se encontro nada con el criterio de busqueda</center></td></tr></table>";
}
else  {
	$sql=$isql;
	$rs = $db->conn->Execute($sql);
	$pager = new ADODB_Pager($db,$sql,'',true,1);
    $pager->toRefLinks = $linkPagina;
	$pager->first='<span class="glyphicon glyphicon-backward"></span>';
	$pager->prev='<span class="glyphicon glyphicon-chevron-left"></span>';
	$pager->next='<span class="glyphicon glyphicon-chevron-right"></span>';
	$pager->last='<span class="glyphicon glyphicon-forward"></span>';
	$pager->globalGridAttributes='class="table"';
	$pager->gridAttributes='class="table table-condensed table-hover"';

	$_eval = '
	$email=$rsTmp->fields["E-MAIL"];
	$codigo=$rsTmp->fields["HID_ANEX_CODIGO"];
	$id_envio=$rsTmp->fields["HID_ID_ENVIO"];
	$onSuccess =  \'
		<a  id="btn-\'.$codigo.\'_\'.$id_envio.\'" class="btn btn-info center-block" onclick=digitalSending("\'.$codigo.\'",\'.$id_envio.\')>
		<span id="span-\'.$codigo.\'_\'.$id_envio.\'" class="glyphicon glyphicon-send">
		</span>
		</a>
		\';
	$onError = \'
		<a  class="btn btn-danger center-block" disabled>
		<span class="glyphicon glyphicon-send">
		</span>
		</a>
		\';
	if (strpos($email, "@")){
		$mail=explode("@",$email);
		$host=end($mail);
		if (strpos($host, "@")===false and strpos($host, ".")){
			$validator=true;
}else
	$validator=false;
	$list = explode(";",$email);
	foreach($list as $itememail){
		 $formated = preg_replace("/\s+/", "" , $itememail);
		if (!filter_var($formated, FILTER_VALIDATE_EMAIL)) {
		    $validator=false;
		}
	}
		
}else
	$validator=false;
	if (!empty($email) and $validator)
	$s .= " <td>$onSuccess</td>\n";
else
	$s .= " <td>$onError</td>\n";
';
	$moreColumns = array(
		"Acciones" => array(
			"eval"=>$_eval
		)
	);

	$pager->moreColumns=$moreColumns;
	$pager->Render($rows_per_page=40,1);
}
?>
<script type="text/javascript">
document.getElementById('Enviar').style.display = 'none';
function digitalSending(codigo, envio){
	$.ajax({
    url:   'responseEnvioE-mail.php?pruebas=<?=$pruebas?>',
		type:  'post',
		data: {
		    "codigo":codigo,
			"envio":envio
        },
dataType: 'json',
beforeSend: function () {
				$("#btn-"+codigo+"_"+envio).prop("disabled","disabled");
	$("#btn-"+codigo+"_"+envio).removeClass("btn-info");
	$("#btn-"+codigo+"_"+envio).addClass("btn-warning");
	$("#span-"+codigo+"_"+envio).removeClass("glyphicon-send");
	$("#span-"+codigo+"_"+envio).addClass("glyphicon-repeat");
},
	success:  function (response) {
		$("#btn-"+codigo+"_"+envio).prop("disabled","disabled");
		$("#btn-"+codigo+"_"+envio).removeClass("btn-warning");
		$("#span-"+codigo+"_"+envio).removeClass("glyphicon-repeat");
		if (response["success"]==true){
			$("#btn-"+codigo+"_"+envio).addClass("btn-success");
			$("#span-"+codigo+"_"+envio).addClass("glyphicon-ok");
		}else{
			alert(response["message"]);
			$("#btn-"+codigo+"_"+envio).addClass("btn-danger");
			$("#span-"+codigo+"_"+envio).addClass("glyphicon-remove");
		}
	}
});
}
</script>
</body>
</html>
