<?php
/**
 * @author Cesar Gonzalez <aurigadl@gmail.com>
 * @author Jairo Losada   <jlosada@gmail.com>
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 * @copyright

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

$ruta_raiz = "..";
if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");
if($_GET["idSerie"]) $idSerie = $_GET["idSerie"];
if($_GET["idSubSerie"]) $idSubSerie = $_GET["idSubSerie"];
if($_GET["tdoc"]) $tdoc = $_GET["tdoc"];
if($_GET["insertar_registro"]) $insertar_registro = $_GET["insertar_registro"];
if($_GET["actualizar"]) $actualizar = $_GET["actualizar"];
if($_GET["borrar"]) $borrar = $_GET["borrar"];
if($_GET["linkarchivo"]) $linkarchivo = $_GET["linkarchivo"];
if($_GET["ind_ProcAnex"]) $ind_ProcAnex = $_GET["ind_ProcAnex"];
if(!$ind_ProcAnex) $ind_ProcAnex = $_POST["ind_ProcAnex"];

$krd              = $_SESSION["krd"];
$dependencia      = $_SESSION["dependencia"];
$usua_doc         = $_SESSION["usua_doc"];
$codusuario       = $_SESSION["codusuario"];
$seriesVistaTodos = $_SESSION["seriesVistaTodos"];
$nurad            = $_GET["nurad"];
$ar               = intval ($_GET["ar"]);

if(!$idSerie) $idSerie=0;
if(!$idSubSerie) $idSubSerie=0;

//Si la tipificacion llega de los anexos
if($ar > 0){
    $es_anexo = true;
}else{
    $es_anexo=false;
}

if($nurad){
    $ent = substr($nurad,-1);
}

include_once("$ruta_raiz/include/db/ConnectionHandler.php");

$db = new ConnectionHandler("$ruta_raiz");

if (!defined('ADODB_FETCH_ASSOC')) define('ADODB_FETCH_ASSOC',2);
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
include_once "$ruta_raiz/include/tx/Historico.php";
include_once ("$ruta_raiz/include/tx/TipoDocumental.php");
include_once "$ruta_raiz/include/tx/Expediente.php";
include_once "$ruta_raiz/include/tx/Tx.php";
$coddepe = $dependencia;
$codusua = $codusuario;
$isqlDepR = "SELECT RADI_DEPE_ACTU,RADI_USUA_ACTU from RADICADO WHERE RADI_NUME_RADI = '$nurad'";
$rsDepR = $db->conn->Execute($isqlDepR);

$trd             = new TipoDocumental($db);
$Historico       = new Historico($db);
$trdExp          = new Expediente($db);
$numExpediente   = $trdExp->consulta_exp("$nurad");
$mrdCodigo       = $trdExp->consultaTipoExpediente("$numExpediente");
$trdExpediente   = $trdExp->descSerie." / ".$trdExp->descSubSerie;
$descPExpediente = $trdExp->descTipoExp;
$descFldExp      = $trdExp->descFldExp;
$codigoFldExp    = $trdExp->codigoFldExp;
$expUsuaDoc      = $trdExp->expUsuaDoc;

// PARTE DE CODIGO DONDE SE IMPLEMENTA EL CAMBIO DE ESTADO AUTOMATICO AL TIPIFICAR.
include ("$ruta_raiz/include/tx/Flujo.php");
// $texp no viene de ningun lado
if 	(!empty($texp)){
    $objFlujo = new Flujo($db, $texp,$usua_doc);
    $expEstadoActual = $objFlujo->actualNodoExpediente($numExpediente);
    $arrayAristas =$objFlujo->aristasSiguiente($expEstadoActual);
    $aristaSRD = $objFlujo->aristaSRD;
    $aristaSBRD = $objFlujo->aristaSBRD;
    $aristaTDoc = $objFlujo->aristaTDoc;
    $aristaTRad = $objFlujo->aristaTRad;
    $arrayNodos = $objFlujo->nodosSig;
    $aristaAutomatica = $objFlujo->aristaAutomatico;
    $aristaTDoc = $objFlujo->aristaTDoc;
    if($arrayNodos) {
        $i = 0;
        foreach ($arrayNodos as $value) {
            $nodo = $value;
            $arAutomatica =  $aristaAutomatica[$i];
            $aristaActual = $arrayAristas[$i];
            $arSRD =  $aristaSRD[$i];
            $arSBRD = $aristaSBRD[$i];
            $arTDoc = $aristaTDoc[$i];
            $arTRad = $aristaTRad[$i];
            $nombreNodo = $objFlujo->getNombreNodo($nodo,$texp);
            if($arAutomatica==1 and $arSRD==$idSerie and $arSBRD==$idSubSerie and $arTDoc==$tdoc and $arTRad==$ent) {
                if($insertar_registro) {
                    $objFlujo->cambioNodoExpediente($numExpediente,$nurad,$nodo,$aristaActual,1,"Cambio de Estado Automatico.");
                    $codiTRDS = $codiTRD;
                    $i++;
                    $TRD = $codiTRD;
                    $observa = "*TRD*".$idSerie."/".$codiSBRD." (Creacion de Expediente.)";
                    $radicados= $nurad;
                    $tipoTx = 51;


                    $rs=$db->query($sql);
                    $mensaje = "SE REALIZO CAMBIO DE ESTADO AUTOMATICAMENTE AL EXPEDIENTE No. < $numExpediente > <BR>
                        EL NUEVO ESTADO DEL EXPEDIENTE ES  <<< $nombreNodo >>>";
                }else {
                    $mensaje = "SI ESCOGE ESTE TIPO DOCUMENTAL EL ESTADO DEL EXPEDIENTE  < $numExpediente >
                        CAMBIARA EL ESTADO AUTOMATICAMENTE A <BR> <<< $nombreNodo >>>";
                }
                echo "<table width=100% class=borde_tab>
                    <tr><td align=center>
                    <span class=titulosError align=center>
                    $mensaje
                    </span>
                    </td></tr>
                    </table><TABLE><TR><TD></TD></TR></TABLE>";
            }
            $i++;
        }
    }
}
/*
 * Adicion nuevo Registro
 */
if ($insertar_registro && $tdoc !=0 && $idSubSerie !=0 && $idSerie !=0 ){

    include_once("../include/query/busqueda/busquedaPiloto1.php");

    $sql = "SELECT $radi_nume_radi AS RADI_NUME_RADI
        FROM SGD_RDF_RETDOCF r
        WHERE RADI_NUME_RADI = '$nurad'";
    if($seriesVistaTodos!=1){
        $sql.="  AND  DEPE_CODI =  '$coddepe'";
    }
    #echo "NO discriminañ...";
    $rs=$db->conn->query($sql);
    $radiNumero = $rs->fields["RADI_NUME_RADI"];

    if ($radiNumero !=''){

        $codserie = 0 ;
        $tsub = 0  ;
        $tdoc = 0;
        $mensaje_err = "<HR>
            <center><B><FONT COLOR=RED>
            Ya existe una Clasificacion para esta dependencia <$coddepe> <BR>  VERIFIQUE LA INFORMACION E INTENTE DE NUEVO
            </FONT></B></center>
            <HR>";
    }
    else
    {

        $isqlTRD = "select SGD_MRD_CODIGO
            from SGD_MRD_MATRIRD
            where SGD_SRD_ID = '$idSerie'
            and SGD_SBRD_ID = '$idSubSerie'
            and SGD_TPR_CODIGO = '$tdoc'";
        if($seriesVistaTodos!=1){
            $sql.="  AND  DEPE_CODI =  '$coddepe'";
        }
        $rsTRD = $db->conn->Execute($isqlTRD);
        $i=0;
        while(!$rsTRD->EOF)
        {
            $codiTRDS[$i] = $rsTRD->fields['SGD_MRD_CODIGO'];
            $codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];
            $i++;
            $rsTRD->MoveNext();
        }
										
				$anexRad = Tx::recursiveAnex(array($nurad), $db);								
				
				foreach ($anexRad as $value) {
					$trd->insertarTRD($codiTRDS,$codiTRD,$value,$coddepe,$codusua,$tdoc);					
				}
        
        $TRD = $codiTRD;
        include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";
        //Modificado skina
        $sqlH = "SELECT $radi_nume_radi as RADI_NUME_RADI
            FROM SGD_RDF_RETDOCF r
            WHERE r.RADI_NUME_RADI = '$nurad'
            AND r.SGD_MRD_CODIGO =  '$codiTRD'";
        $rsH=$db->conn->query($sqlH);
				
        while(!$rsH->EOF)
        {
            $codiRegH[] = $rsH->fields['RADI_NUME_RADI'];
            $rsH->MoveNext();
        }

        $observa = "*TRD*$codserie/$tsub (Asigancion tipo documental.)";
        $_Tx_id = 32;

        if($es_anexo==true){
            $nrobs = $codiRegH[0];
            $_Tx_id = 95;
            $observa = "*TRD*$codserie/$tsub (Anexo Tipificado No. $nrobs)";

            //TRAIGO EL RADICADO PADRE
            $_isql ="select radi_nume_deri from radicado where radi_nume_radi = $nrobs";
            $_rsisql =$db->conn->query($_isql);
            $codiRegH[] = $_rsisql->fields['RADI_NUME_DERI'];            
        }
				
				$result = array_unique(array_merge($codiRegH, $anexRad));

        $Historico->insertarHistorico($result, $dependencia, $codusuario, $dependencia, $codusuario, $observa,$_Tx_id);
        
				$radiUp = $trd->actualizarTRD($result,$tdoc);

        $codserie = 0 ;
        $tsub = 0  ;
        $tdoc = 0;
    }
}


$sql = "SELECT RADI_NUME_RADI
    FROM SGD_RDF_RETDOCF
    WHERE RADI_NUME_RADI = '$nurad'";

if($seriesVistaTodos!=1){
    $sql.="  AND  DEPE_CODI =  '$coddepe'";
}

$rs=$db->conn->query($sql);
$radiNumero = $rs->fields["RADI_NUME_RADI"];


?>

<html>
<head>
<title>..:: Clasificar Documento ::..</title>
<?php
include $ruta_raiz."/htmlheader.inc.php";
?>
<script>
function regresar(){
    document.TipoDocu.submit();
}
</script>
</head>
<body >
<div class="container">
    <form method="GET" action="<?=$encabezadol?>" name="TipoDocu" class=smart-form>

    <input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'>
    <input type='hidden' name='nurad'               value='<?=$nurad?>'>
    <input type='hidden' name='ent'                 value='<?=$ent?>'>
    <input type='hidden' name='codiTRDModi'         value='<?=$codiTRDModi?>'>
    <input type='hidden' name='codiTREDli'          value='<?=$codiTREDli?>'>
    <input type='hidden' name='ind_ProcAnex'        value='<?=$ind_ProcAnex?>'>
    <input type='hidden' name='ar'        		value='<?=$ar?>'>
     <table width=70% align="center" class="table table-bordered">
      <tr align="center" >
       <th height="15" ><small>Cuadro de clasificacion documental - Radicado No <?=$nurad?></small></th>
      </tr>
     </table>
     <table width="70%" class="table table-bordered">
      <tr >
       <td  ><small>SERIE</small></td>
       <td  ><label class=select>
<?php
if(!$tdoc) $tdoc = 0;
if(!$idSerie) $idSerie = 0;
if(!$idSubSerie) $idSubSerie = 0;
$fechah=date("dmy") . " ". time("h_m_s");
$fecha_hoy = Date("Y-m-d");
$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
$sqlFechaHoy2 = $db->conn->SQLDate('Y-m-d',$db->conn->sysTimeStamp);
$check=1;
$fechaf=date("dmy") . "_" . time("hms");
$num_car = 4;
$nomb_varc = "s.sgd_srd_codigo";
$nomb_varde = "s.sgd_srd_descrip";
include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";

$querySerie = "select distinct($sqlConcat) as detalle, s.id,s.sgd_srd_codigo, UPPER(s.sgd_srd_descrip) as name
    from sgd_mrd_matrird m, sgd_srd_seriesrd s
    where ". ($_SESSION['usua_assign_trd'] ? "s.sgd_srd_codigo <> '900'" : "(cast(m.depe_codi as varchar(100)) = '$coddepe' or cast(m.depe_codi_aplica as varchar(100)) like '%$coddepe%' or cast(m.depe_codi as varchar(100))='$depDireccion')")."
    and s.sgd_srd_codigo = m.sgd_srd_codigo
    and s.sgd_srd_estado  = '1' 
    and ".$db->sysdate()." between m.sgd_mrd_fechini and m.sgd_mrd_fechfin 
    and ".$db->sysdate()." between s.sgd_srd_fechini and s.sgd_srd_fechfin";

if($seriesVistaTodos!=1){
    $querySerie .= " and (cast(m.depe_codi as varchar(100)) = '$coddepe' or m.depe_codi_aplica  like '%$coddepe%' or cast(m.depe_codi as varchar(100))='$depDireccion') ";
}

//and ".$sqlFechaHoy." between $sgd_srd_fechini and $sgd_srd_fechfin
$querySerie .= " ";

$querySerie .= " order by name asc";
$rsD=$db->conn->query($querySerie);
$comentarioDev = "Muestra las Series Docuementales";
include "$ruta_raiz/include/tx/ComentarioTx.php";
print $rsD->GetMenu2("idSerie", $idSerie, "0:-- Seleccione --", false,"","onChange='submit()'  class='form-control'" );
?>
          </label></td>
         </tr>
       <tr>
         <td  ><small>SUBSERIE</small></td>
         <td ><label class=select>
<?php
$nomb_varc = "su.sgd_sbrd_codigo";
$nomb_varde = "su.sgd_sbrd_descrip";
include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
$querySub = "select distinct ($sqlConcat) as detalle,su.id, su.sgd_sbrd_codigo,UPPER(su.sgd_sbrd_descrip) as name
    from sgd_mrd_matrird m, sgd_sbrd_subserierd su
    where
    cast(m.sgd_mrd_esta as numeric(1))       = 1
    and m.sgd_srd_id = $idSerie
    and su.sgd_srd_id = $idSerie
    and su.id = m.sgd_sbrd_id
    and ".$db->sysdate()." between su.sgd_sbrd_fechini and su.sgd_sbrd_fechfin ";
if($seriesVistaTodos!=1){
    $querySub .= " and (cast(m.depe_codi as varchar(100)) = '$coddepe' or m.depe_codi_aplica  like '%$coddepe%' or cast(m.depe_codi as varchar(100))='$depDireccion') ";
}
$querySub .= " order by name asc";
$rsSub=$db->conn->query($querySub);
include "$ruta_raiz/include/tx/ComentarioTx.php";
print $rsSub->GetMenu2("idSubSerie", $idSubSerie, "0:-- Seleccione --", false,"","onChange='submit()' name='tsub' class='select'" );

?>
         </select></label></td>
         </tr>
       <tr>
         <td><small>TIPO DE DOCUMENTO</small></td>
         <td><label class=select>
<?php
$nomb_varc = "t.sgd_tpr_codigo";
$nomb_varde = "t.sgd_tpr_descrip";
include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
if($ent) $queryTrad = " and sgd_tpr_tp$ent >= 1";
$queryTip = "select distinct ($sqlConcat) as detalle, t.sgd_tpr_codigo,UPPER(t.sgd_tpr_descrip) as name
    from sgd_mrd_matrird m, sgd_tpr_tpdcumento t, sgd_sbrd_subserierd sb
    where cast(m.sgd_mrd_esta as numeric(1))       = 1
    and m.sgd_srd_id     = $idSerie
    and m.sgd_sbrd_id    = $idSubSerie
    and t.sgd_tpr_codigo = m.sgd_tpr_codigo
    and sb.id = m.sgd_sbrd_id
    and sb.sgd_srd_id = m.sgd_srd_id
    and t.sgd_tpr_estado = 1
    $queryTrad
    and ".$db->sysdate()." between sb.sgd_sbrd_fechini
    and sb.sgd_sbrd_fechfin ";
if($seriesVistaTodos!=1){
    $queryTip .= " and (cast(m.depe_codi as varchar(100)) = '$coddepe' or m.depe_codi_aplica  like '%$coddepe%' or cast(m.depe_codi as varchar(100))='$depDireccion') ";
}
$queryTip .= " order by name asc";
$rsTip=$db->conn->query($queryTip);
$ruta_raiz = "..";
include "$ruta_raiz/include/tx/ComentarioTx.php";
print $rsTip->GetMenu2("tdoc", $tdoc, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
?></label>
        </td>
        </tr>
          <tr align="center">
            <td align="center" colspan=3><footer>
                <input name="insertar_registro" type=submit class="btn btn-success btn-xs" value=" Insertar ">
                <input name="actualizar" type="button" class="btn btn-primary btn-xs" id="envia23" value=" Modificar ">
<?php
$respuesta_rap = (isset($_GET['respuesta_rap']))? $_GET['respuesta_rap'] : null;
if ($ind_ProcAnex!="S") {
    if(!$respuesta_rap) echo '<input name="Cerrar" type="button" class="btn btn-default btn-xs" id="envia22" onClick="opener.regresar(); window.close(); " value="Cerrar">';
}else{
    echo "<input type='button' value='Cerrar' id='CerrarTAnexos' class='btn btn-default btn-xs'>";
}
?>
            <input name="respuesta_rap" type="hidden" value="<?=$respuesta_rap?>">
            </footer>
            </td>
        </tr>
        </table>
    <table width="70%" class="table table-bordered">
        <tr align="center">
            <td>
<?php
include "lista_tiposAsignados.php";

?>
        </td>
            </tr>
    </table>
    </form>
    </span>
    <p>
    <?=$mensaje_err?>
    </p>
    </span>

<center><span><p><h4>  <strong> NOTA:</strong> La Clasificación Documental es importante para la organización de los documentos físicos. Por tanto, asegúrese de asignar la TRD de acuerdo a sus funciones en la entidad.  </h4> </p></span></center>

<?php
if ($cerrar) {
    echo '
    <script>
        javascript:window.parent.opener.cargarPagina("' . $recargar_anexos . '","tabs-c");
        top.close();
    </script>';
}
if($ind_ProcAnex="S"){
?>
    <script type="text/javascript">

    function borrarArchivo(anexo,linkarch){
        if (confirm('Esta seguro de borrar este Registro ?')){
            nombreventana="ventanaBorrarR1";
            url="tipificar_documentos_transacciones.php?<?=session_name()."=".session_id()?>&borrar=1&nurad=<?=$nurad?>&codiTRDEli="+anexo+"&linkarchivo="+linkarch;
            window.open(url,nombreventana,'height=250,width=300');
        }
        return;
    }

    $(document).ready(function() {

        $('body').on("click", '#CerrarTAnexos',function(){
            window.opener.$.fn.cargarPagina("./lista_anexos.php","tabs-c"); window.close();
        });

        function procModificar() {
            if ($('select[name="tdoc"]').val() != 0 &&
                $('select[name="idSerie"]').val() != 0  &&
                $('select[name="idSubSerie"]').val() != 0){

                if (confirm('Esta Seguro de Modificar el Registro de su Dependencia ?')) {
                    nombreventana="ventanaModiR1";
                    url="tipificar_documentos_transacciones.php?<?=session_name()."=".session_id()?>&actualizar=1&usua=<?=$krd?>&codusua=<?=$codusua?>&tdoc=<?=$tdoc?>&idSubSerie=<?=$idSubSerie?>&idSerie=<?=$idSerie?>&coddepe=<?=$coddepe?>&codusuario=<?=$codusuario?>&dependencia=<?=$dependencia?>&nurad=<?=$nurad?>";
                    window.open(url,nombreventana,'height=200,width=300');
                }
            } else {
                alert("Campos obligatorios ");
            }

            return;
        }

        $('#envia23').click(procModificar);

    });

    </script>

<?php
}
?>
</html>
</body>
