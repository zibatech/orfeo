<?php
session_start();
if (!$ruta_raiz)
    $ruta_raiz = "..";
if (!$db)
    include_once "../conn.php";

//Codigo ajax respuesta para la modificacion de etiquetas
//Se utiliza desde el archivo include lista_expedientes.php
//se coloca en esta seccion para no crear un nuevo archivo para
//tan poco codigo y por que esta relacionada con la consulta
//original.
if($_POST['saveEtiq']){

    $allPostKeys = implode(',',array_keys($_POST));
    if (preg_match_all('/etique_[0-9]*E/',$allPostKeys,$matches)){
        list($nda, $noExp) = explode("_", $matches[0][0]);
    }

    include_once ("$ruta_raiz/include/tx/Expediente.php");
    #echo "<script> alert('Intento imprimir el mensaje  de actualización de datos');</script>";
    $expediente = new Expediente($db);
    return $expediente->editDatosParamExp($noExp, $_POST[$matches[0][0]]);
    die;
    #Despues de editado un expediente.


}

?>
<script>
function mostrarExpediente(numExpediente,numRadicado){
    document.getElementById('iexpmostrar').src = 'expediente/verExp.php?exp='+numExpediente;
    $('#nombexpbtn').html(numExpediente);

}


    function crearAnexoExpediente(numeroExpediente) {
        window.open("./expediente/crearAnexoExpediente.php?numeroExpediente=" + numeroExpediente, "height=850,width=970,scrollbars=yes");
    }


    function verTipoExpediente(numeroExpediente, codserie, tsub, tdoc, opcionExp) {
        <?php
           if($verrad){
              $isqlDepR = "SELECT RADI_DEPE_ACTU,
                      RADI_USUA_ACTU
                      FROM radicado
                      WHERE RADI_NUME_RADI = '$verrad'";
              $rsDepR = $db->conn->Execute($isqlDepR);
              $coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
              $codusua = $rsDepR->fields['RADI_USUA_ACTU'];
              $ind_ProcAnex = "N";
              $fechaH = Date("Ymdhis");
           }
        ?>
        window.open("./expediente/tipificarExpediente.php?opcionExp=" + opcionExp + "&numeroExpediente=" + numeroExpediente + "&nurad=<?=$verrad?>&codserie=" + codserie + "&tsub=" + tsub + "&tdoc=" + tdoc + "&krd=<?=$krd?>&dependencia=<?=$dependencia?>&fechaExp=<?=$radi_fech_radi?>&codusua=<?=$codusua?>&coddepe=<?=$coddepe?>", "MflujoExp<?=$fechaH?>", "height=850,width=970,scrollbars=yes");
    }

    function verTipoExpediente(numeroExpediente, codserie, tsub, tdoc, opcionExp) {
        <?php
           if($verrad){
              $isqlDepR = "SELECT RADI_DEPE_ACTU,
                      RADI_USUA_ACTU
                      FROM radicado
                      WHERE RADI_NUME_RADI = '$verrad'";
              $rsDepR = $db->conn->Execute($isqlDepR);
              $coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
              $codusua = $rsDepR->fields['RADI_USUA_ACTU'];
              $ind_ProcAnex = "N";
              $fechaH = Date("Ymdhis");
           }
        ?>
        window.open("./expediente/tipificarExpediente.php?opcionExp=" + opcionExp + "&numeroExpediente=" + numeroExpediente + "&nurad=<?=$verrad?>&codserie=" + codserie + "&tsub=" + tsub + "&tdoc=" + tdoc + "&krd=<?=$krd?>&dependencia=<?=$dependencia?>&fechaExp=<?=$radi_fech_radi?>&codusua=<?=$codusua?>&coddepe=<?=$coddepe?>", "MflujoExp<?=$fechaH?>", "height=850,width=970,scrollbars=yes");
    }

    function verHistExpediente(numeroExpediente, codserie, tsub, tdoc, opcionExp) {
        <?php
           if($numrad){
              $isqlDepR = "SELECT RADI_DEPE_ACTU,RADI_USUA_ACTU from radicado
                          WHERE RADI_NUME_RADI = '$numrad'";
              $rsDepR = $db->conn->Execute($isqlDepR);
              $coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
              $codusua = $rsDepR->fields['RADI_USUA_ACTU'];
              $ind_ProcAnex = "N";
           }
      ?>
        window.open("./expediente/verHistoricoExp.php?sessid=<?=session_id()?>&opcionExp=" + opcionExp + "&numeroExpediente=" + numeroExpediente + "&nurad=<?=$verrad?>&krd=<?=$krd?>&ind_ProcAnex=<?=$ind_ProcAnex?>", "HistExp<?=$fechaH?>", "height=800,width=1060,scrollbars=yes");
    }

    function crearProc(numeroExpediente) {
        window.open("./expediente/crearProceso.php?sessid=<?=session_id()?>&numeroExpediente=" + numeroExpediente + "&nurad=<?=$verrad?>&krd=<?=$krd?>&ind_ProcAnex=<?=$ind_ProcAnex?>", "HistExp<?=$fechaH?>", "height=450,width=680,scrollbars=yes");
    }

    function seguridadExp(numeroExpediente, nivelExp) {
        nivelExp = nivelExp || 0;
        window.open("./seguridad/expediente.php?<?=session_name()?>=<?=session_id()?>&num_expediente=" + numeroExpediente + "&nurad=<?=$verrad?>&nivelExp=" + nivelExp + "&ind_ProcAnex=<?=$ind_ProcAnex?>", "HistExp<?=$fechaH?>", "height=350,width=700,scrollbars=yes");
    }

    function expAnexo(numeroExpediente){
        window.open("./expediente/anexarDocumentoExpediente.php?<?=session_name()?>=<?=session_id()?>&num_expediente=" + numeroExpediente, "height=350,width=700,scrollbars=yes");
    }

    function reportePredios(numeroExpediente, predios, vars, tipoReporte) {
        predios = predios || '';
        // tipoReporte = tipoReporte || 'modeloPredial';
        window.open("<?=$servidorBirt?>" + tipoReporte + ".rptdesign&chip=" + predios + "&num_expediente=" + numeroExpediente + "&nurad=<?=$verrad?>" + vars, "HistExp<?=$fechaH?>" + predios, "fullscreen=yes,scrollbars=yes");
    }
    function verTipoExpedienteOld(numeroExpediente) {
        <?php
          if($numrad){
           $isqlDepR = "SELECT RADI_DEPE_ACTU,RADI_USUA_ACTU from radicado WHERE RADI_NUME_RADI = '$numrad'";
           $rsDepR = $db->conn->Execute($isqlDepR);
           $coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
           $codusua = $rsDepR->fields['RADI_USUA_ACTU'];
          }
          $ind_ProcAnex="N";
          ?>
        window.open("./expediente/tipificarExpedienteOld.php?numeroExpediente=" + numeroExpediente + "&nurad=<?=$verrad?>&krd=<?=$krd?>&dependencia=<?=$dependencia?>&fechaExp=<?=$radi_fech_radi?>&codusua=<?=$codusua?>&coddepe=<?=$coddepe?>", "Tipificacion_Documento", "height=450,width=750,scrollbars=yes");
    }
    function modFlujo(numeroExpediente, texp, codigoFldExp, ventana) {

        ventana = ventana || 'default';
        <?php
            $isqlDepR = "SELECT RADI_DEPE_ACTU,RADI_USUA_ACTU from radicado WHERE RADI_NUME_RADI = '$numrad'";
            if($numrad){
             $rsDepR = $db->conn->Execute($isqlDepR);
             $coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
             $codusua = $rsDepR->fields['RADI_USUA_ACTU'];
             $ind_ProcAnex="N";
            }
        ?>
        if (ventana == "Max") {
            opcVentana = "fullscreen=yes, scrollbars=auto";
        } else {
            opcVentana = "height=350,width=850,scrollbars=yes";
        }

        window.open("./flujo/modFlujoExp.php?codigoFldExp=" + codigoFldExp + "&krd=<?=$krd?>&numeroExpediente=" + numeroExpediente + "&numRad=<?=$verrad?>&texp=" + texp + "&krd=<?=$krd?>&ind_ProcAnex=<?=$ind_ProcAnex?>&codusua=<?=$codusua?>&coddepe=<?=$coddepe?>", "TexpE<?=$fechaH?>", opcVentana);
    }

    function Responsable(numeroExpediente) {
         frm = document.form2;
        <?php
          if($numrad){
            $isqlDepR = "SELECT RADI_DEPE_ACTU,RADI_USUA_ACTU
                    FROM radicado
                    WHERE RADI_NUME_RADI = '$numrad'";
            $rsDepR = $db->conn->Execute($isqlDepR);
            $coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
            $codusua = $rsDepR->fields['RADI_USUA_ACTU'];
          }
            $isql = "SELECT USUA_DOC_RESPONSABLE, SGD_EXP_PRIVADO
                    FROM SGD_SEXP_SECEXPEDIENTES
                    WHERE SGD_EXP_NUMERO = '$numeroExpediente'";
            $rs = $db->conn->Execute($isql);
            $responsable= $rs->fields['USUA_DOC_RESPONSABLE'];
            $nivelExp= $rs->fields['SGD_EXP_PRIVADO'];
        ?>
        window.open("./expediente/responsable.php?&numeroExpediente=" + numeroExpediente +
            "&numRad=<?=$verrad?>&krd=<?=$krd?>&ind_ProcAnex=<?=$ind_ProcAnex?>&responsable=<?=$responsable?>&coddepe=<?=$coddepe?>&codusua=<?=$codusua?>", "Responsable", "height=400,width=550,scrollbars=yes");
    }

    function CambiarE(est, numeroExpediente) {
        window.open("./archivo/cambiar.php?krd=<?=$krd?>&numRad=<?=$verrad?>&expediente=" + numeroExpediente + "&est=" + est + "&dependencia=<?=$dependencia?>", "Cambio Estado Expediente", "height=200,width=200,scrollbars=yes");
    }

    function insertarExpediente() {
        window.open("./expediente/insertarExpediente.php?sessid=<?=session_id()?>&nurad=<?=$verrad?>&krd=<?=$krd?>&ind_ProcAnex=<?=$ind_ProcAnex?>", "HistExp<?=$fechaH?>", "height=900,width=1100,scrollbars=yes");
    }

    function insertarExpedienteFast(numExp) {
        window.open("./expediente/insertarExpediente.php?sessid=<?=session_id()?>&nurad=<?=$verrad?>&krd=<?=$krd?>&funExpediente=INSERT_EXP&confirmaIncluirExp&numeroExpediente="+numExp+"&fast=yes&ind_ProcAnex=<?=$ind_ProcAnex?>", "HistExp<?=$fechaH?>", "height=900,width=1100,scrollbars=yes");
    }

    function verWorkFlow(numeroExpediente, codigoProceso) {
        var numeroExpediente = numeroExpediente || '';
        var codigoProceso = codigoProceso || 0;
        <?php
            //  include "./proceso/workFlow.php"; //?verrad=$verrad&numeroExpediente=$numExpediente&".session_name()."=".session_id()."";
            $pWorkFlow = "./process/processCurrent/workFlow.php?verrad=$verrad&".session_name()."=".session_id()."";
        ?>
        window.open("<?=$pWorkFlow?>&HistExp<?=$fechaH?>&numeroExpediente=" + numeroExpediente + "&codigoProceso=" + codigoProceso, "HistExp<?=$fechaH?>" + numeroExpediente, "height=750,width=850,scrollbars=yes");
    }

    function crearExpediente() {
        numExpediente = document.getElementById('num_expediente').value;
        numExpedienteDep = document.getElementById('num_expediente').value.substr(4, 3);
        if (numExpedienteDep ==<?=$dependencia?>) {
            if (numExpediente.length == 13) {
                insertarExpedienteVal = true;
            } else {
                alert("Error. El numero de digitos debe ser de 13.");
                insertarExpedienteVal = false;
            }
        }
        else {
            alert("Error. Para crear un expediente solo lo podra realizar con el codigo de su dependencia. ");
            insertarExpedienteVal = false;
        }
        if (insertarExpedienteVal == true) {
            respuesta = confirm("Esta apunto de crear el EXPEDIENTE No. " + numExpediente + " Esta Seguro ? ");
            insertarExpedienteVal = respuesta;
            if (insertarExpedienteVal == true) {
                dv = digitoControl(numExpediente);
                document.getElementById('num_expediente').value = document.getElementById('num_expediente').value + "E" + dv;
                document.getElementById('funExpediente').value = "CREAR_EXP"
                document.form2.submit();
            }
        }
    }

    var varOrden = 'ASC';
    function ordenarPor(campo) {
        if (document.getElementById('orden').value == 'ASC') {
            varOrden = 'DESC';
        }
        else {
            varOrden = 'ASC';
        }
        document.getElementById('orden').value = varOrden;
        document.getElementById('ordenarPor').value = campo + ' ' + varOrden;
        document.form2.submit();
    }

    var i = 1;
    var numRadicado;
    function cambiarImagen(imagen) {
        numRadicado = imagen.substr(13);
        if (i == 1) {
            document.getElementById('anexosRadicado').value = numRadicado;
            i = 2;
        } else {
            document.getElementById('anexosRadicado').value = "";
            i = 1;
        }

        document.form2.submit();
    }

    function excluirExpediente() {
        window.open("./expediente/excluirExpediente.php?sessid=<?=session_id()?>&nurad=<?=$verrad?>&krd=<?=$krd?>&ind_ProcAnex=<?=$ind_ProcAnex?>", "HistExp<?=$fechaH?>", "height=400,width=730,scrollbars=yes");
    }
    // Incluir Anexos y Asociados a un Expediente.
    function incluirDocumentosExp() {
        var strRadSeleccionados = "";
        frm = document.form2;
        if (typeof frm.check_uno.length != "undefined") {
            for (i = 0; i < frm.check_uno.length; i++) {
                if (frm.check_uno[i].checked) {
                    if (strRadSeleccionados == "") {
                        coma = "";
                    }
                    else {
                        coma = ",";
                    }
                    strRadSeleccionados += coma + frm.check_uno[i].value;
                }
            }
        } else {
            if (frm.check_uno.checked) {
                strRadSeleccionados = frm.check_uno.value;
            }
        }

        if (strRadSeleccionados != "") {
            window.open("./expediente/incluirDocumentosExp.php?sessid=<?=session_id()?>&nurad=<?=$verrad?>&krd=<?=$krd?>&ind_ProcAnex=<?=$ind_ProcAnex?>&strRadSeleccionados=" + strRadSeleccionados, "HistExp<?=$fechaH?>", "height=300,width=600,scrollbars=yes");
        } else {
            alert("Error. Debe seleccionar por lo menos un \n\r documento a incluir en el expediente.");
            return false;
        }
    }

    // Crear Subexpediente
    function incluirSubexpediente(numeroExpediente, numeroRadicado) {
        window.open("./expediente/datosSubexpediente.php?sessid=<?=session_id()?>&nurad=" + numeroRadicado + "&krd=<?=$krd?>&num_expediente=" + numeroExpediente, "HistExp<?=$fechaH?>", "height=350,width=700,scrollbars=yes");
    }


	function funlinkAnexo(pathfile, rutaRaiz){
		  nombreventana="linkAnexo";
			url= rutaRaiz + "/linkAnexo.php?"+"linkAnexo=" + pathfile;
			ventana = window.open(url,nombreventana,'scrollbars=1,height=50,width=250');
			return;
	}


</script>
<script language="JavaScript" src="./js/funciones.js"></script>
<input type="hidden" name="ordenarPor" id="ordenarPor" value="">
<input type="hidden" name="orden" id="orden" value="<?php print $orden; ?>">
<input type="hidden" name="verAnexos" id="verAnexos" value="">
<input type="hidden" name="anexosRadicado" id="anexosRadicado" value="">
<?php
function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$time_start = microtime_float();
if (!isset($verBorrados)) {
    print '<input type="hidden" name="verBorrados" id="verBorrados" value="' . $anexosRadicado . '">';
}

include("$ruta_raiz/include/js/digitoControl.js");
$verradicado = $verrad;
if ($menu_ver_tmp) {
    $menu_ver = $menu_ver_tmp;
}
if ($verradicado) {
    $verrad = $verradicado;
}
$numrad = $verrad;
if (!$menu_ver) {
    $menu_ver = 4;
}
$fechah = date("dmy_h_m_s") . " " . time("h_m_s");
$check = 1;
$numeroa = 0;
$numero = 0;
$numeros = 0;
$numerot = 0;
$numerop = 0;
$numeroh = 0;
$rs = $db->conn->Execute($isql);

include_once "$ruta_raiz/tx/verLinkArchivo.php";
$verLinkArch = new verLinkArchivo($db);


include_once("$ruta_raiz/include/tx/Expediente.php");
$expediente = new Expediente($db);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
if ($verrad){
    $traerRadicadosYAnexos = "No";
    $exp = $expediente->consultaExp($verrad,null,$traerRadicadosYAnexos);
}
$arrExpedientes = $expediente->expedientes;
?>

<!-- widget content -->
<div class="widget-body" height="100%">

    <div  clas='row'>
        <?php
            $iExp = 1;
            $opcionesEXp=$tituloA='';
            foreach ($arrExpedientes as $numExpediente => $datosExp) {
                if ($iExp == 1)
                    $tituloA =$numExpediente ;
                    $opcionesEXp.=" <li><a href='#' onClick='mostrarExpediente(\"$numExpediente\", \"$verrad\");'>$numExpediente</a></li>";
                $iExp++;
            }
            ?>
        <div class="col-md-12">

        <?php
            $indicaBorrador = substr($numrad, 0, 4);
            $indicadorTipoRadi = substr($numrad, -1);
            if($indicaBorrador < 3000 || $indicadorTipoRadi <= 3) {
                ?>
                        <button class="btn btn-warning pull-right" type="button"  onclick="insertarExpediente();"> <span class="fa fa-plus"></span> incluir en...
                            </button>
                <?php
            }
        ?>
            <div class="dropdown pull-right">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id='nombexpbtn'> <?php echo $tituloA?>
                <span class="caret"></span></button>
                <ul class="dropdown-menu">
                <?php echo $opcionesEXp?>
                </div>  
                </div>   
</div>
        <div class="tab-content" align=left>
        <iframe name="iexpmostrar" id="iexpmostrar" style="width: 100%;height: 400px; border: 0" src="expediente/verExp.php?exp=<?php echo $tituloA?>"></iframe>
            <?php
            $iExp = 1;
            if ($arrExpedientes) {
                foreach ($arrExpedientes as $numExpediente => $datosExp) {

                    if ($iExp == 1) $datoss = " active "; else  $datoss = " ";
                    ?>
                    <div class="tab-pane <?= $datoss ?>" id="tab-<?=$numExpediente?>">

                        <?php
                        //$num_expediente = $numExpedente;
                        $num_expediente = $numExpedienteSel;
                        $numeroExpediente = $numExpediente;
                        $texp = 0;

                     //   if($iExp==1) include "lista_expediente.php";
                        ?>
                    </div>
                    <?php
                    $iExp++;
                    //exit;
                }
            } else {
                if (!empty($usuaPermExpediente)) {

                    $inicaiRadicado = substr($verrad, 0, 4);
                    $indicadorTipoRadiado = substr($verrad, -1);

                    if (!$tsub)
                        $tsub = "0";
                    if (!$tdoc)
                        $tdoc = "0";
                    if (!$codserie)
                        $codserie = "0";
                                if($inicaiRadicado < 3000 || $indicadorTipoRadiado == 3 || 
                                    $indicadorTipoRadiado == 1) {
                                ?> 

                                <span class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
                                    <small>Expediente</small>
                                    <b class="caret"></b> </a>
                                <ul class="dropdown-menu">

                                    <li>
                                        <a href="#" onClick="insertarExpediente();">Incluir en...</a>
                                    </li>

                                    <? /* if ($usuaPermExpediente > 1) { ?>
                                        <li>
                                            <a href="#"
                                               onClick="verTipoExpediente('<?= $num_expediente ?>',<?= $codserie ?>,<?= $tsub ?>,<?= $tdoc ?>,'MODIFICAR');">Crear
                                                Nuevo Expediente</a>
                                        </li>
                                    <? } */?>

                                </ul>
                                </span>
                                <?php } ?>
                <? } ?>
            <?php } ?>
        </div>
    </div>
    <!-- end widget content -->
</div>

<? if($inicaiRadicado < 3000) { ?>
<table class="table-bordered table-striped table-condensed table-hover smart-form has-tickbox">
    <tr>
        <td>
            <small>Y ESTA RELACIONADO CON EL(LOS) SIGUIENTE(S) DOCUMENTOS:
                <small>
        </td>
        <td align="center">
            <? if ($usuaPermExpediente and $verradPermisos == "Full" or $dependencia == '999') {
                ?>
                <a href="#tblAnexoAsociado" onClick="incluirDocumentosExp();">
                    <b>
                        <small>Incluir Documentos en Expediente
                    </b></small>
                </a>
            <?
            }
            ?>
        </td>
    </tr>
</table>
<? } ?>

<table border=0 width="100%">

	<!-- ******************************************************************** -->
	<tr class='titulos5'>
	    <td class="titulos5">
			  <input type="checkbox" name="check_todos" value="checkbox" onClick="todos( document.forms[1] );">
	  	</td>
	    <td align="center">RADICADO</td>
	    <td align="center">FECHA RADICACION</td>
	    <td align="center">TIPO DOCUMENTO</td>
	    <td align="center">ASUNTO</td>
	    <td align="center">TIPO DE RELACION</td>
     </tr>
    <?php
    $arrAnexoAsociado = $expediente->expedienteAnexoAsociado( $verrad );

    if( is_array( $arrAnexoAsociado ) )
    {
        /*
         *  Modificado: 29-Agosto-2006 Supersolidaria
         *  Consulta los datos de los radicados Anexo de (Padre), Anexo y Asociado.
         */
         include_once "$ruta_raiz/include/tx/Radicacion.php";
           $rad = new Radicacion( $db );

        /*
   		 * Modificacion link ingreso a imagenes del documento
	     * @author Liliana Gomez Velasquez
	     *  @since 10 noviembre 2009
	     * @funcion funlinkArchivo
	     */

         foreach( $arrAnexoAsociado as $clave => $datosAnexoAsociado )
         {
					if( $datosAnexoAsociado['radPadre'] != "" && $datosAnexoAsociado['radPadre'] != $verrad && $datosAnexoAsociado['anexo'] == $verrad )
					{
						$arrDatosRad = $rad->getDatosRad( $datosAnexoAsociado['radPadre'] );

						if( $arrDatosRad['ruta'] != "" )
						{
						$varRadi = $datosAnexoAsociado['radPadre'];
				   }else{
				      $rutaRadicado = $datosAnexoAsociado['radPadre'];
				   }

				   $radicadoAnexo = $datosAnexoAsociado['radPadre'];

				   $tipoRelacion = "ANEXO DE (PADRE)";
		       }
			   else if( $datosAnexoAsociado['radPadre'] == $verrad && $datosAnexoAsociado['anexo'] != "" )
			   {
					$arrDatosRad = $rad->getDatosRad( $datosAnexoAsociado['anexo'] );

					if( $arrDatosRad['ruta'] != "" )
					{
			$varRadi = $datosAnexoAsociado['anexo'];
					}else{
					  $rutaRadicado = $datosAnexoAsociado['anexo'];
			      }
		          $radicadoAnexo = $datosAnexoAsociado['anexo'];
  			  $tipoRelacion = "ANEXO";

				}

				else if( $datosAnexoAsociado['radPadre'] == $verrad && $datosAnexoAsociado['asociado'] != "" )
				{
                  $arrDatosRad = $rad->getDatosRad( $datosAnexoAsociado['asociado'] );

                  if( $arrDatosRad['ruta'] != "" )
                  {
                 	  $varRadi = $datosAnexoAsociado['asociado'];
                  }
                  else
                  {
                      $rutaRadicado = $datosAnexoAsociado['asociado'];
                  }

                  $radicadoAnexo = $datosAnexoAsociado['asociado'];

			      $tipoRelacion = "ASOCIADO";

		        }

	            if( $arrDatosRad['ruta'] != "" )
			    {

			         $resulVali = $verLinkArch->valPermisoRadi($varRadi);
			         $verImg = $resulVali['verImg'];
			         $pathImagen = $resulVali['pathImagen'];
				     if($verImg == "SI")
			         {
					      $rutaRadicado =  "<a class=\"vinculos\" href=\"#2\" onclick=\"funlinkArchivo('$varRadi','$ruta_raiz');\">".$varRadi."</a>";

  	       			} else {
   	   	      			$rutaRadicado = "<a href='#2' onclick=\"alert('El documento posee seguridad y no posee los suficientes permisos'); return false;\"> $varRadi</a>";
			  	    }
        		}
   ?>
	<!-- **************************************************************	-->

    <tr>
        <td>
            <input type="checkbox" name="check_uno" value="<?php print $radicadoAnexo; ?>"
                   onClick="uno( document.forms[1] );">
        </td>
        <td>
            <?php
            print $rutaRadicado;
            ?>
        </td>
        <td>
            <?php
            if ($radicadoAnexo){
            $resulVal = $verLinkArch->valPermisoRadi($radicadoAnexo);
            $verImg = $resulVal['verImg'];
            if ($verImg == "NO") {
                echo "<a href='#2' onclick=\"alert('El documento posee seguridad y no posee los suficientes permisos'); return false;\"><span class=leidos>";

            } else {
            ?>
            <a href='./verradicado.php?verrad=<?= $radicadoAnexo ?>&<?= session_name() ?>=<?= session_id() ?>&krd=<?= $krd ?>'
               target="VERRAD<?= $radicadoAnexo ?>">

                <?php
                }
                print $arrDatosRad['fechaRadicacion'];
                }

                ?>
            </a>
        </td>
        <td>
            <?php
            print $arrDatosRad['tipoDocumento'];
            ?>
        </td>
        <td>
            <?php
            print $arrDatosRad['asunto'];
            ?>
        </td>
        <td>
            <?php
            print $tipoRelacion;
            ?>
        </td>
    </tr>
	<?php
		}
	}
	?>
</table>
<br>
<br>
<br>
<?
/******Esto es un desarrollo unico para la CRA******/
if ($db->entidad=="CRA"){
?>
							<table border="0" width="92%" class="borde_tab" align="center" class="titulos2">
<td align="center" class="titulos2">
<span class="leidos2" class="titulos2" align="center">
<b><input type='button' onClick="insertarExpedienteFast($('input[name=opciones]:checked').val());" name='incluir'  value='INCLUIR' class="botones"></b>
<?
?>
</span>
</td>
<br></table>
<table border="0" width="92%" class="table table-striped table-bordered table-hover dataTable no-footer smart-form" align="center" class="titulos2">
<tr class="titulos2">
<td align=\"center\" class=\"titulos2\">
<span class=\"leidos2\" class=\"titulos2\" align=\"center\">
<b>LA SIGUIENTE ES UNA LISTA DE EXPEDIENTES A LOS QUE POSIBLEMENTE PUEDA PERTENECER EL RADICADO</b>	</span>
			</td>  <tr></table>
<table width="92%" class='table table-striped table-bordered table-hover dataTable no-footer smart-form' cellspacing="0" cellpadding="0" align="center" id="tblAnexoAsociado">
  <tr>
    <td class="titulos5">EXPEDIENTES ESPECIFICOS YA EXISTENTES Y HABILITADOS PARA LA ENTIDAD:</td>
    <td class="titulos5" align="center">
  </td>
  </tr>
</table>
<span class="tituloListado"> </span>
<table border=0 width=92% class="table table-striped table-bordered table-hover dataTable no-footer smart-form" align="center">
  <tr class='titulos5'>
    <td class="titulos5">
	</td>
    <td align="center">EXPEDIENTES</td>
    <td align="center">ETIQUETA</td>
    <td align="center">SERIE</td>
    <td align="center">SUBSERIE</td>
    <td align="center">DEPENDENCIA</td>
  </tr>
  <?php

 $sqle="select r.radi_nume_radi, r.radi_depe_actu, s.sgd_dir_nomremdes, s.sgd_doc_fun, s.sgd_esp_codi,s.sgd_oem_codigo,s.SGD_CIU_CODIGO
  		 from radicado r, sgd_dir_drecciones s
  		where r.radi_nume_radi=s.radi_nume_radi and r.radi_nume_radi='$verrad' ";
  		//$db->conn->debug=true;
  			$rs=$db->conn->query($sqle);
	  if(!$rs->EOF){
		$esp=$rs->fields['SGD_ESP_CODI'];
		$oem=$rs->fields['SGD_OEM_CODIGO'];
		$ciu=$rs->fields['SGD_CIU_CODIGO'];
		$fun=$rs->fields['SGD_DOC_FUN'];
		$dep=$rs->fields['RADI_DEPE_ACTU'];
		 if ($dep==410 or $dep==420 or $dep==430){$dep=401;}
		 if ($dep==212||$dep==213||$dep==214||$dep==215){$dep=211;}
		if($esp>0){
			$sqe="select nombre_de_la_empresa from bodega_empresas where identificador_empresa = $esp";
			$rse=$db->conn->Execute($sqe);
			if(!$rse->EOF)$par=$rse->fields['NOMBRE_DE_LA_EMPRESA'];

		} elseif($oem>0){
			$sqe="select sgd_oem_oempresa from sgd_oem_oempresas where sgd_oem_codigo = $oem";
			$rse=$db->conn->Execute($sqe);
			if(!$rse->EOF)$par=$rse->fields['SGD_OEM_OEMPRESA'];
		}elseif($fun>0){
			$sqe="select (usua_nomb) AS SGD_NOMBRE_COMPLETO  from usuario where usua_doc = $fun";
			$rse=$db->conn->Execute($sqe);
			if(!$rse->EOF)$par=$rse->fields['SGD_NOMBRE_COMPLETO'];
		}elseif($ciu>0){
			$sqe="select (sgd_ciu_nombre) AS CIUDADANO  from SGD_CIU_CIUDADANO  where SGD_CIU_CODIGO = $ciu";
			$rse=$db->conn->Execute($sqe);
			if(!$rse->EOF)$par=$rse->fields['CIUDADANO'];}
	  }
	$formatoFechaExp  = $db->conn->SQLDate('Y/m/d', 's.sgd_sexp_fech');
	$camposConcatenar = "(" . $db->conn->Concat("s.sgd_sexp_parexp1",
                                                    "s.sgd_sexp_parexp2",
                                                    "s.sgd_sexp_parexp3",
                                                    "s.sgd_sexp_parexp4",
                                                    "s.sgd_sexp_parexp5") . ")";
    $where =      "AND (s.sgd_sexp_parexp1 LIKE '%$par%' OR
                        s.sgd_sexp_parexp2 LIKE '%$par%' OR
                        s.sgd_sexp_parexp3 LIKE '%$par%' ORalert('');
                        s.sgd_sexp_parexp4 LIKE '%$par%' OR
                        s.sgd_sexp_parexp5 LIKE '%$par%')";
    $sql3 = "select distinct(s.sgd_exp_numero), s.sgd_srd_codigo,s.sgd_sbrd_codigo,
    				se.sgd_srd_descrip,su.sgd_sbrd_descrip,
                    s.depe_codi, d.depe_nomb, S.SGD_SEXP_FECH,
                    $camposConcatenar as PARAMETRO
            from sgd_sexp_secexpedientes s,
                    dependencia d,
                    sgd_sbrd_subserierd su,
                    sgd_srd_seriesrd se
                    --sgd_exp_expediente e
            where
                    s.depe_codi = d.depe_codi and
                    s.sgd_cerrado is NULL and
                    s.sgd_sbrd_codigo = su.sgd_sbrd_codigo and
                    s.sgd_srd_codigo  = su.sgd_srd_codigo and
                    s.sgd_srd_codigo  = se.sgd_srd_codigo and
                    s.depe_codi not in (999) and
                    s.depe_codi='$dep' and
                    $formatoFechaExp > '2007/11/01'
                    {$where}";
    //$db->conn->debug = true;
    $rs3 = $db->conn->Execute($sql3);
      while(!$rs3->EOF){
	  	$exped=$rs3->fields['SGD_EXP_NUMERO'];
		$etiqueta=$rs3->fields['PARAMETRO'];
		$serie=$rs3->fields['SGD_SRD_DESCRIP'];
      	$subserie=$rs3->fields['SGD_SBRD_DESCRIP'];
      	$depen=$rs3->fields['DEPE_NOMB'];

      ?>
  <tr class='listado5'>
    <td>
	<input type="radio" name="opciones" id="opcion1" value="<?=$exped?>">
	 </td>
    <td class="info" align="left"><?=$exped?></td>
    <td class="info" align="left"><?=$etiqueta?></td>
    <td class="info" align="left"><?=$serie?></td>
    <td class="info" align="left"><?=$subserie?></td>
    <td class="info" align="left"><?=$depen?></td>
<?
    $rs3->MoveNext();
     } ?>
    </td>
  </tr>

</table>
<br><br>
<table width="92%" class='table table-striped table-bordered table-hover dataTable no-footer smart-form' cellspacing="0" cellpadding="0" align="center" id="tblAnexoAsociado">
  <tr>
    <td class="titulos5">EXPEDIENTES GENERALES PARA LA DEPENDENCIA:</td>
    <td class="titulos5" align="center">
  </td>
  </tr>
</table>
<span class="tituloListado"> </span>
<table border=0 width=92% class="table table-striped table-bordered table-hover dataTable no-footer smart-form" align="center">
  <tr class='titulos5'>
    <td class="titulos5">
	</td>
    <td align="center">EXPEDIENTES</td>
    <td align="center">ETIQUETA</td>
    <td align="center">SERIE</td>
    <td align="center">SUBSERIE</td>
    <td align="center">DEPENDENCIA</td>
  </tr>
  <?php

 if ($oem>0 or $ciu>0 or $fun>0){
    $sql4 = "select distinct(s.sgd_exp_numero), s.sgd_srd_codigo,s.sgd_sbrd_codigo,
    				se.sgd_srd_descrip,su.sgd_sbrd_descrip,
                    s.depe_codi, d.depe_nomb, S.SGD_SEXP_FECH,
                    $camposConcatenar as PARAMETRO
            from sgd_sexp_secexpedientes s,
                    dependencia d,
                    sgd_sbrd_subserierd su,
                    sgd_srd_seriesrd se
                    --sgd_exp_expediente e
            where   s.depe_codi = d.depe_codi and
                    s.sgd_cerrado is NULL and
                    s.sgd_sbrd_codigo = su.sgd_sbrd_codigo and
                    s.sgd_srd_codigo  = su.sgd_srd_codigo and
                    s.sgd_srd_codigo  = se.sgd_srd_codigo and
                    s.depe_codi not in (900,999) and
                    s.depe_codi='$dep' and
                    $formatoFechaExp > '2007/11/01' and
                    s.sgd_tipo_codigo in (1)";
   //$db->conn->debug = true;
    $rs4 = $db->conn->Execute($sql4);
      while(!$rs4->EOF){
	  	$exped=$rs4->fields['SGD_EXP_NUMERO'];
		$etiqueta=$rs4->fields['PARAMETRO'];
		$serie=$rs4->fields['SGD_SRD_DESCRIP'];
      	$subserie=$rs4->fields['SGD_SBRD_DESCRIP'];
      	$depen=$rs4->fields['DEPE_NOMB'];

      ?>
  <tr class='listado5'>
    <td>
	  <input type="radio" name="opciones" id="opcion2"value="<?=$exped?>">
	</td>
    <td class="info" align="left"><?=$exped?></td>
    <td class="info" align="left"><?=$etiqueta?></td>
    <td class="info" align="left"><?=$serie?></td>
    <td class="info" align="left"><?=$subserie?></td>
    <td class="info" align="left"><?=$depen?></td>
<?
    $rs4->MoveNext();
     } ?>
    </td>
  </tr>

</table>
<?
}

 if ($esp>0){

    $sql2 = "select distinct(s.sgd_exp_numero), s.sgd_srd_codigo,s.sgd_sbrd_codigo,
    				se.sgd_srd_descrip,su.sgd_sbrd_descrip,
                    s.depe_codi, d.depe_nomb, S.SGD_SEXP_FECH,
                   $camposConcatenar as PARAMETRO
            from sgd_sexp_secexpedientes s,
                    dependencia d,
                    sgd_sbrd_subserierd su,
                    sgd_srd_seriesrd se
                    --sgd_exp_expediente e
            where   s.depe_codi = d.depe_codi and
                    s.sgd_cerrado is NULL and
                    s.sgd_sbrd_codigo = su.sgd_sbrd_codigo and
                    s.sgd_srd_codigo  = su.sgd_srd_codigo and
                    s.sgd_srd_codigo  = se.sgd_srd_codigo and
                    s.depe_codi not in (900) and
                    s.depe_codi='$dep' and
                    $formatoFechaExp > '2007/11/01' and
                     s.sgd_tipo_codigo in (2)";
    //$db->conn->debug = true;
    $rs2 = $db->conn->Execute($sql2);
      while(!$rs2->EOF){
	  	$exped=$rs2->fields['SGD_EXP_NUMERO'];
		$etiqueta=$rs2->fields['PARAMETRO'];
		$serie=$rs2->fields['SGD_SRD_DESCRIP'];
      	$subserie=$rs2->fields['SGD_SBRD_DESCRIP'];
      	$depen=$rs2->fields['DEPE_NOMB'];

      ?>
  <tr class='listado5'>
    <td>
	 <input type="radio" name="opciones" id="opcion3" value="<?=$exped?>">
	</td>
    <td class="info" align="left"><?=$exped?></td>
    <td class="info" align="left"><?=$etiqueta?></td>
    <td class="info" align="left"><?=$serie?></td>
    <td class="info" align="left"><?=$subserie?></td>
    <td class="info" align="left"><?=$depen?></td>
<?
    $rs2->MoveNext();
     } ?>
    </td>
  </tr>

</table>
<? }else{
//Si no tiene ninguno de los datos, simplemente cerramos.
 ?>
</td>
</tr>
</table>
<?
}
}
/*****************************************/
?>

<script type="text/javascript">
// DO NOT REMOVE : GOBAL FUNCTIONS!
pageSetUp()

// PAGE RELATED SCRIPTS

/*
 * Autostart Carousel
 */
$('.carousel.slide').carousel({
    interval: 3000,
    cycle: true
});
$('.carousel.fade').carousel({
    interval: 3000,
    cycle: true
});

// load bootstrap-progress bar script
loadScript("js/plugin/bootstrap-progressbar/bootstrap-progressbar.js", progressBarAnimate);

// Fill all progress bars with animation
function progressBarAnimate() {
    $('.progress-bar').progressbar({
        display_text: 'fill'
    });
}

/*
 * Smart Notifications
 */
$('#eg1').click(function (e) {

    $.bigBox({
        title: "Big Information box",
        content: "This message will dissapear in 6 seconds!",
        color: "#C46A69",
        //timeout: 6000,
        icon: "fa fa-warning shake animated",
        number: "1",
        timeout: 6000
    });

    e.preventDefault();

})

$('#eg2').click(function (e) {

    $.bigBox({
        title: "Big Information box",
        content: "Lorem ipsum dolor sit amet, test consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
        color: "#3276B1",
        //timeout: 8000,
        icon: "fa fa-bell swing animated",
        number: "2"
    });

    e.preventDefault();
})

$('#eg3').click(function (e) {

    $.bigBox({
        title: "Shield is up and running!",
        content: "Lorem ipsum dolor sit amet, test consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
        color: "#C79121",
        //timeout: 8000,
        icon: "fa fa-shield fadeInLeft animated",
        number: "3"
    });

    e.preventDefault();

})

$('#eg4').click(function (e) {

    $.bigBox({
        title: "Success Message Example",
        content: "Lorem ipsum dolor sit amet, test consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
        color: "#739E73",
        //timeout: 8000,
        icon: "fa fa-check",
        number: "4"
    }, function () {
        closedthis();alert('');
    });

    e.preventDefault();

})

$('#eg5').click(function () {

    $.smallBox({
        title: "Ding Dong!",
        content: "Someone's at the door...shall one get it sir? <p class='text-align-right'><a href='javascript:void(0);' class='btn btn-primary btn-sm'>Yes</a> <a href='javascript:void(0);'  onclick='noAnswer();' class='btn btn-danger btn-sm'>No</a></p>",
        color: "#296191",
        //timeout: 8000,
        icon: "fa fa-bell swing animated"
    });

})

function noAnswer() {

    $.smallBox({
        title: "Sure, as you wish sir...",
        content: "",
        color: "#A65858",
        iconSmall: "fa fa-times",
        timeout: 5000
    });alert('');
}


$('#eg6').click(function () {
    $.smallBox({
        title: "Big Information box",
        content: "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam",
        color: "#5384AF",
        //timeout: 8000,
        icon: "fa fa-bell"
    });
})

$('#eg7').click(function () {
    $.smallBox({
        title: "James Simmons liked your comment",
        content: "<i class='fa fa-clock-o'></i> <i>2 seconds ago...</i>",
        color: "#296191",
        iconSmall: "fa fa-thumbs-up bounce animated",
        timeout: 4000
    });
})

function closedthis() {alert('');
    $.smallBox({
        title: "Great! You just closed that last alert!",
        content: "This message will be gone in 5 seconds!",
        color: "#739E73",
        iconSmall: "fa fa-cloud",
        timeout: 5000
    });
}

/*
 * SmartAlerts
 */
// With Callback
$("#smart-mod-eg1").click(function (e) {
    $.SmartMessageBox({
        title: "Smart Alert!",
        content: "This is a confirmation box. Can be programmed for button callback",
        buttons: '[No][Yes]'
    }, function (ButtonPressed) {
        if (ButtonPressed === "Yes") {
            $.smallBox({
                title: "Callback function",
                content: "<i class='fa fa-clock-o'></i> <i>You pressed Yes...</i>",
                color: "#65365",
                iconSmall: "fa fa-check fa-2x fadeInRight animated",
                timeout: 4000
            });
        }

        if (ButtonPressed === "No") {
            $.smallBox({
                title: "Callback function",
                content: "<i class='fa fa-clock-o'></i> <i>You pressed No...</i>",
                color: "#C46A69",
                iconSmall: "fa fa-times fa-2x fadeInRight animated",
                timeout: 4000
            });
        }

    });
    e.preventDefault();
})
// With Input
$("#smart-mod-eg2").click(function (e) {

    $.SmartMessageBox({
        title: "Smart Alert: Input",
        content: "Please enter your user name",
        buttons: "[Accept]",
        input: "text",
        placeholder: "Enter your user name"
    }, function (ButtonPress, Value) {
        alert(ButtonPress + " " + Value);
    });

    e.preventDefault();
})
// With Buttons
$("#smart-mod-eg3").click(function (e) {

    $.SmartMessageBox({
        title: "Smart Notification: Buttons",
        content: "Lots of buttons to go...",
        buttons: '[Need?][You][Do][Buttons][Many][How]'
    });

    e.preventDefault();
})
// With Select
$("#smart-mod-eg4").click(function (e) {

    $.SmartMessageBox({
        title: "Smart Alert: Select",
        content: "You can even create a group of options.",
        buttons: "[Done]",
        input: "select",
        options: "[Costa Rica][United States][Autralia][Spain]"
    }, function (ButtonPress, Value) {
        alert(ButtonPress + " " + Value);
    });

    e.preventDefault();
});

// With Login
$("#smart-mod-eg5").click(function (e) {

    $.SmartMessageBox({
        title: "Login form",
        content: "Please enter your user name",
        buttons: "[Cancel][Accept]",
        input: "text",
        placeholder: "Enter016010000 your user name"
    }, function (ButtonPress, Value) {
        if (ButtonPress == "Cancel") {
            alert("Why did you cancel that? :(");
            return 0;
        }

        Value1 = Value.toUpperCase();
        ValueOriginal = Value;
        $.SmartMessageBox({
            title: "Hey! <strong>" + Value1 + ",</strong>",
            content: "And now please provide your password:",
            buttons: "[Login]",
            input: "password",
            placeholder: "Password"
        }, function (ButtonPress, Value) {
            alert("Username: " + ValueOriginal + " and your password is: " + Value);
        });
    });

    e.preventDefault();
});



</script>




  <script>

$( document ).ready(function() {
  $( "#grabadorapidoexpediente" ).hide();

  $('body').on('click', "button[name^=edittemasexp]", function () {
    $('.showfield').hide();
    $('.editfield').show();
  })

  $('body').on('click', "button[name^='savetemasexp']", function () {
    var complement = $(this).val();
    var datos = $("input[name^='etique_" + complement + "']").serialize() + "&saveEtiq=1";
    $.post( "./expediente/lista_expedientes.php", datos, function( data ) {
      //$( ".result" ).html( data );
    });


    setTimeout(function(){ parent.frames.location.reload();top.location.reload(); }, 90);
    //parent.frames.location.reload();top.location.reload();
  })


  $( "#grabadorapidoexpediente" ).click(function() {
    /*location.reload();*/
    /*document.getElementsByName(mainFrame).contentDocument.location.reload(true);*/
  });

  $( "#editadorapidoexpediente" ).click(function() {
    $( "#grabadorapidoexpediente" ).show();
    $( "#editadorapidoexpediente" ).hide();
  });

});

        </script>
