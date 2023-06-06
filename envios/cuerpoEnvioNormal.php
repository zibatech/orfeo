<?php
/**
 * @author Jairo Losada   <jlosada@gmail.com>
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyrigh


 OrfeoGpl 4.5 Models are the data definition of SIIM2 Information System
 Copyright (C) 2013 Infometrika Ltda y Correlibre.

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

$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$tip3Nombre  = $_SESSION["tip3Nombre"];
$tip3desc    = $_SESSION["tip3desc"];
$tip3img     = $_SESSION["tip3img"];

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
</head>
<body >

<?php
include_once "$ruta_raiz/js/funtionImage.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
if(!$carpeta) $carpeta=0;
if(!$estado_sal)   {$estado_sal=2;}
if(!$estado_sal_max) $estado_sal_max=4;

if($estado_sal==4 && (!$bTodasDep && !$busqradicados  )  ) {
    $accion_sal = "Envio de Documentos";
    $pagina_sig = "cuerpoEnvioNormal.php";
    $nomcarpeta = "Radicados Para Envio";
    if(!$dep_sel) $dep_sel = $dependencia;
    $dependencia_busq1 = " and c.radi_depe_radi = $dep_sel ";
    $dependencia_busq2 = " and c.radi_depe_radi = $dep_sel";
}


//$dependencia_busq2 = " and c.radi_depe_radi = $dep_sel";
$dep_sel=str_pad($dep_sel, 5, "0", STR_PAD_LEFT);
$dependencia_busq2 = " and substring(cast(c.radi_nume_radi as varchar),5,5)='$dep_sel'";



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

 <form name=formEnviar action='../envios/envia.php?<?=$encabezado?>' method=GET class="smart_form">
    <input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'>
    <input type='hidden' name='estado_sal' value='<?= $estado_sal ?>'>
    <input type='hidden' name='estado_sal_max' value='<?= $estado_sal_max ?>'>
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

//Start::se valida si hay registros si no se crean que no sean de notificaciones
$iSqlPreparado= "select a.id as id_anexo,sdd.id as id_dir,'Físico' as tipo,1 as estado 
                from anexos a
                join sgd_dir_drecciones sdd on a.anex_radi_nume = sdd.radi_nume_radi
                where a.radi_nume_salida in ('$busqRadicados') 
                and (a.sgd_trad_codigo < 4 or a.sgd_trad_codigo > 7)";
$rsPreparado = $db->conn->query($iSqlPreparado);
if ($rsPreparado) {
    while(!$rsPreparado->EOF){
        $iSqlExiste= "SELECT count(*) as EXISTE
                                FROM SGD_RAD_ENVIOS
                                WHERE id_anexo =  '".$rsPreparado->fields['ID_ANEXO']."' and id_direccion = '".$rsPreparado->fields['ID_DIR']."' and tipo = 'Físico' and estado != -1";
        $rsExiste = $db->conn->query($iSqlExiste);
        $iSqlExisteEnviado= "SELECT count(*) as EXISTE
                                FROM SGD_RAD_ENVIOS env
                                JOIN ANEXOS a
                                ON a.id = env.id_anexo
                                WHERE env.id_anexo =  '".$rsPreparado->fields['ID_ANEXO']."' and env.id_direccion = '".$rsPreparado->fields['ID_DIR']."' and env.tipo = 'Físico' and env.estado=2 and a.anex_estado !=4";
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
                                        'Físico', 
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

include "$ruta_raiz/include/query/envios/queryCuerpoEnvioNormal.php";

/*Busqueda avanzada*/

if($_POST['busqueda_avanzada']=='1')
{
    $isql='
select
    e.id as "HID_ID_EVENTO" ,
    a.anex_estado as "CHU_ESTADO" ,
    a.sgd_deve_codigo as "HID_DEVE_CODIGO" ,
    a.sgd_deve_fech as "HID_SGD_DEVE_FECH" ,
    a.radi_nume_salida as "IMG_Radicado Salida" ,
    substr(a.anex_codigo,
    1,
    4) || \'/\' || substr(a.anex_codigo,
    5,
    3) || \'/docs/\' || a.anex_nomb_archivo as "HID_RADI_PATH" ,
    case
        when cast(dir.sgd_dir_tipo as varchar)= \'1\' then \'\'
        else cast((dir.sgd_dir_tipo-1) as varchar)
    end as "Copia" ,
    a.anex_radi_nume as "Radicado Padre" ,
    c.radi_fech_radi as "Fecha Radicado" ,
    dir.sgd_dir_nomremdes || \'/\' || dir.sgd_dir_nombre || \'
\' || dir.sgd_dir_direccion as "Descripcion" ,
    a.sgd_fech_impres as "Fecha Impresion" ,
    concat(c.radi_nume_folio, \' / \', c.radi_nume_hoja) as "FOLIOS" ,
    c.radi_nume_anexo as "Anexos" ,
    a.anex_creador as "Generado Por" ,
    dir.radi_nume_radi || \'_\' || dir.sgd_dir_tipo || \'_\' || e.id as "CHK_RADI_NUME_SALIDA" ,
    a.sgd_deve_codigo as "HID_DEVE_CODIGO1" ,
    a.anex_estado as "HID_ANEX_ESTADO1" ,
    a.anex_nomb_archivo as "HID_ANEX_NOMB_ARCHIVO" ,
    a.anex_tamano as "HID_ANEX_TAMANO" ,
    a.ANEX_RADI_FECH as "HID_ANEX_RADI_FECH" ,
    \'WWW\' as "HID_WWW" ,
    \'9999\' as "HID_9999" ,
    a.anex_tipo as "HID_ANEX_TIPO" ,
    a.anex_radi_nume as "HID_ANEX_RADI_NUME" ,
    a.sgd_dir_tipo as "HID_SGD_DIR_TIPO" ,
    a.sgd_deve_codigo as "HID_SGD_DEVE_CODIGO"
from
    sgd_rad_envios e,
    anexos a,
    usuario b,
    radicado c,
    sgd_dir_drecciones dir
where
    e.id_anexo = a.id
    and e.tipo = \'Físico\'
    and (a.radi_nume_salida in ('.$busqRadicados.')
        or a.anex_radi_nume in ('.$busqRadicados.'))
    and substring(cast(c.radi_nume_radi as varchar), 5, 5)= \''.$dep_sel.'\'
    and ( cast(c.RADI_NUME_RADI as varchar(20)) like \'%'.$busqRadicados.'%\' )
    and a.radi_nume_salida = c.radi_nume_radi
    and e.id_direccion = dir.id
    and a.anex_creador = b.usua_login
    and a.anex_borrado = \'N\'
    and substring(cast(c.radi_nume_radi as varchar), 17, 1)= \'1\'
order by
    4 desc,
    dir.sgd_dir_tipo';
}


$rs=$db->conn->Execute($isql);
if ($rs->EOF){
    echo "<div id='alertmessage'>
            <div class='alert alert-block alert-info'>
                <a class='close' data-dismiss='alert' href='#'>×</a>
                <h4 class='alert-heading'>¡No se encontro nada con el criterio de busqueda!</h4>
            </div>
        </div>";
} else  {
    $pager = new ADODB_Pager($db,$isql,'adodb', true,$orderNo,$orderTipo);
    $pager->toRefLinks = $linkPagina;
    $pager->toRefVars = $encabezado;
    $pager->Render($rows_per_page=150,$linkPagina,$checkbox=chkEnviar);
}
?>
  </form>


</body>
</html>
