<?php
if (!$ruta_raiz) 	$ruta_raiz = "..";
$permArchi     = $_SESSION["permArchi"];
$permVobo      = $_SESSION["permVobo"];
$permRespuesta = $_SESSION["usua_perm_respuesta"];
//Eliminamos aquellos elementos que no son convenientes en el Get
$pattern 		= '/[^\w:()áéíóúÁÉÍÓÚ=#°,.ñÑ]+/';
$rad_asun_res = isset($rad_asun_res) ? $rad_asun_res : '';
$rad_asun_res 	= preg_replace($pattern, ' ', $rad_asun_res);
if(!$mostrar_opc_envio) $mostrar_opc_envio=0;

//Start::Validar si el memorando tienen al usuario

if(isset($numrad)){ 
  $iSqlMemorandoMultipleCuerpo= "SELECT 
                  count(*)  as TOTAL,
                  string_agg(DISTINCT SGD_DIR_DRECCIONES.sgd_dir_nombre, ',') AS DESTINATARIOS,
                  (SELECT count(*) FROM ANEXOS WHERE ANEXOS.radi_nume_salida::text ='$numrad' AND ANEX_ESTADO >= 2 ) AS RADICADO 
              FROM
                  SGD_DIR_DRECCIONES 
              WHERE
                  radi_nume_radi::text =  '$numrad' 
                  AND radi_nume_radi::text LIKE'%3' ";

  $iSqlMemorandoMultiple= "SELECT count(*) EXISTE FROM SGD_DIR_DRECCIONES WHERE radi_nume_radi::text = '$numrad' AND  SGD_DIR_DOC = '$usua_doc' and radi_nume_radi::text like '%3'";
  $rsMemorandoMultiple = $db->conn->query($iSqlMemorandoMultiple);
  $rsMemorandoMultipleCuerpo = $db->conn->query($iSqlMemorandoMultipleCuerpo);
  $tieneAsignacion = 0;
  if ($rsMemorandoMultiple) {
      if($rsMemorandoMultiple->fields["EXISTE"] > 0 && $rsMemorandoMultipleCuerpo->fields["TOTAL"] > 1){
          $tieneAsignacion = true;
      }
  }
}
//End::Validar si el memorando tienen al usuario
?>

<script language="javascript">

function markAll(){
    if(document.form1.elements.checkAll.checked) {
      for(i=2;i<document.form1.elements.length;i++)
        document.form1.elements[i].checked=1;
    } else {
      for(i=2;i<document.form1.elements.length;i++)
        document.form1.elements[i].checked=0;
    }
    clickTx();
}


function clickTx(){
    sw=0;
    for(i=1;i<document.form1.elements.length;i++)
        if (document.form1.elements[i].checked && document.form1.elements[i].name!="checkAll")
            sw=1;
    if (sw==0) {
      document.getElementById('AccionCaliope').style.display = 'none';
      return;
    } else {
      document.getElementById('AccionCaliope').style.display = '';
    }
}


$( document ).ready(function(){

  function returnKrd(){
    return '<?=$krd?>';
  }

  function vistoBueno() {
    changedepesel(9);
    document.getElementById('EnviaraV').value = 'VoBo';
    envioTx();
  }

  function enviar() {
    envioTx();
  }


  function devolver() {
    changedepesel(12);
    envioTx();
  }

  function txAgendar() {
    if (!validaAgendar('SI'))
      return;
    changedepesel(14);
    envioTx();
  }

  function txNoAgendar() {
      changedepesel(15);
      envioTx();
  }

  function archivar() {
      changedepesel(13);
      envioTx();
  }

  function nrr() {
      changedepesel(16);
      envioTx();
  }

  function tipificar(){
    changedepesel(19);
    envioTx();
  }

  function masivaTRD(){
    sw=0;
    var radicados = new Array();
    var list = new Array();
    for(i=1;i<document.form1.elements.length;i++){
      if (document.form1.elements[i].checked && document.form1.elements[i].name!="checkAll") {
      sw++;
      valor = document.form1.elements[i].name;
      valor = valor.replace("checkValue[", "");
      valor = valor.replace("]", "");
      radicados[sw] = valor;
      list.push(valor);
      };
    };

    window.open("accionesMasivas/masivaAsignarTrd.php?<?=session_name()?>=<?=session_id()?>&krd=<?=$krd?>&radicados=" + list, "Masiva_Asignación_TRD", "height=650,width=750,scrollbars=yes");
  };

  function masivaIncluir(){
    sw=0;
      var list = new Array();
      var radicados = new Array();
      for(i=1;i<document.form1.elements.length;i++){

        if (document.form1.elements[i].checked && document.form1.elements[i].name!="checkAll") {
          sw++;
          valor = document.form1.elements[i].name;
          valor = valor.replace("checkValue[", "");
          valor = valor.replace("]", "");
          radicados[sw] = valor;
          list.push(valor);
        };

        window.open("accionesMasivas/masivaIncluirExp.php?<?=session_name()?>=<?=session_id()?>&krd=<?=$krd?>&radicados=" + list, "Masiva_IncluirExp", "height=650,width=750,scrollbars=yes");

      };
    };


    function envioTx(){
      sw=0;
      <? if(!$verrad){ ?>
        for(i=1;i<document.form1.elements.length;i++)
        if (document.form1.elements[i].checked && document.form1.elements[i].name!="checkAll")
            sw=1;
        if (sw==0){
          alert ("Debe seleccionar uno o mas radicados");
          return;
        }
      <?}?>
      document.form1.submit();
    }

  function respuestaTx(){
      var valor = sw = 0;
      var params      = 'width='+screen.width;
          params      += ', height='+screen.height;
          params      += ', top=0, left=0'
          params      += ', scrollbars=yes'
          params      += ', fullscreen=yes';

    <?if(!$verrad){?>
          for(i=1;i<document.form1.elements.length;i++){
              if (document.form1.elements[i].checked && document.form1.elements[i].name!="checkAll"){
                  sw++;
                  valor = document.form1.elements[i].name;
                  valor = valor.replace("checkValue[", "");
                  valor = valor.replace("]", "");
              }
          }

          if (sw != 1){
              alert("Debe seleccionar UN(1) radicado");
              return;
          }


          var url         = "respuestaRapida/index.php?<?=session_name()?>=" +
                            "<?=session_id()?>&radicadopadre=" +
                              + valor + "&krd=<?=$krd?>";
          window.open(url, "Respuesta Rapida", params);

    <?}else{?>
          window.open("respuestaRapida/index.php?<?=session_name()?>=<?=session_id()?>&radicado=" +
                      '<?php print_r($verrad) ?>' + "&radicadopadre=" + '<?php print_r($verrad) ?>' +
                      "&asunto=" + '<?php print_r($rad_asun_res)?>' +
                      "&krd=<?=$krd?>", "Respuesta Rapida", params);
    <?}?>
  }


  function respuestaTx2(){
      var valor = sw = 0;
      var params      = 'width='+screen.width;
          params      += ', height='+screen.height;
          params      += ', top=0, left=0'
          params      += ', scrollbars=yes'
          params      += ', fullscreen=yes';

    <?if(!$verrad){?>
          for(i=1;i<document.form1.elements.length;i++){
              if (document.form1.elements[i].checked && document.form1.elements[i].name!="checkAll"){
                  sw++;
                  valor = document.form1.elements[i].name;
                  valor = valor.replace("checkValue[", "");
                  valor = valor.replace("]", "");
              }
          }

          if (sw != 1){
              alert("Debe seleccionar UN(1) radicado");
              return;
          }


          var url         = "respuestaRapida/index2.php?<?=session_name()?>=" +
                            "<?=session_id()?>&radicadopadre=" +
                              + valor + "&krd=<?=$krd?>";
          window.open(url, "Respuesta Rapida", params);

    <?}else{?>
          window.open("respuestaRapida/index2.php?<?=session_name()?>=<?=session_id()?>&radicado=" +
                      '<?php print_r($verrad) ?>' + "&radicadopadre=" + '<?php print_r($verrad) ?>' +
                      "&asunto=" + '<?php print_r($rad_asun_res)?>' +
                      "&krd=<?=$krd?>", "Respuesta Rapida", params);
    <?}?>
  }

  function window_onload2() {
    <? if ($menu_ver==3) { ?>
          $('#depsel, #carpper, #Enviar').hide();
          $('#AccionCaliope').show();
    <? } ?>
  }

  function window_onload() {
      $('#AccionCaliope, #depsel, #carpper, #Enviar').hide();
      <?  if($verrad){ ?>
      window_onload2();
      <? }

      if($carpeta==11 and $_SESSION["USUA_JEFE_DE_GRUPO"]){
      ?>
        if(document.getElementById('salida') != null)
          document.getElementById('salida').style.display = '';
        if(document.getElementById('enviara') != null)
          document.getElementById('enviara').style.display = 'none';
        if(document.getElementById('Enviar') != null)
          document.getElementById('Enviar').style.display = 'none';
      <? } 
      else {
       echo " ";
      }

      if($carpeta==11 and !$_SESSION["USUA_JEFE_DE_GRUPO"]){
        echo "document.getElementById('enviara').style.display = 'none'; ";
        echo "document.getElementById('Enviar').style.display = 'none'; ";
      }
      ?>
  }

  function optionSelect(control){
    var seleccionados=document.getElementById("seleccion");
    if(control.selected){
      selecionados.value= selecionados.value+","+control.value;
    }else{
      var posicion=selccionados.value.indexOf(control.value);
      if(posicion!=-1){
        selccionados.value=selccionados.value.substr(0,posicion)+selccionados.value.substr(posicion+control.value.length);
      }
    }
  }

  window_onload();

  $('#depsel').on('change', enviar);


  //pestanas.js
  function validaAgendar(argumento){
    fecha_hoy =  '<?=date('Y')."-".date('m')."-".date('d')?>';
    fecha = document.form1.elements['fechaAgenda'].value;

    if (fecha==""&&argumento=="SI"){
      alert("Debe suministrar la fecha de agenda");
      return false;
    }
    if (!fechas_comp_ymd(fecha_hoy,fecha) && argumento=="SI") {
      alert("La fecha de agenda debe ser mayor que la fecha de hoy");
      return false;
    }
    return true;
  }
  // JavaScript Document
  <!-- Esta funcion esconde el combo de las dependencia e inforados Se activan cuando el menu envie una señal de cambio.-->
  function changedepesel1(){
    codAccion= $('#AccionCaliope').val();
    carpeta = '<?= $carpeta ?>';
    carpInhabilitada = "10000" + carpeta;

    //El jefe de area o tramitador solo puede mover radicados entre las carpetas "Entrada" y "Jefe de Area".
    //carp_codi Entrada = 0
    //carp_codi Jefe de Area = 13
    if (codAccion == 10) {
      $('#carpper option[value!="100000"][value!="1000013"]').hide();
      $('#carpper option[value='+carpInhabilitada+']').attr('disabled','disabled');
    }

    changedepesel(codAccion);
  }

  <!-- Cuando existe una señal de cambio el programa ejecuta esta funcion mostrando el combo seleccionado -->

  function changedepesel(enviara){

    document.form1.codTx.value = enviara;
    $('#depsel, #carpper, #Enviar').hide();

    if(enviara==10 ){
      $('#carpper').show();
      $('#depsel, #Enviar').hide();
    }

    //Archivar
    if(enviara==13){
      $('#depsel, #carpper').hide();
      envioTx();
    }

    //nrr
    if(enviara==16 ){
      $('#depsel, #carpper').hide();
      envioTx();
    }

    //Devolver
    if(enviara==12)  {
      envioTx();
    }

    if(enviara==11){
      //document.getElementById('Enviar').value = "ARCHIVAR";
    }

    if(enviara==9){
      $('#depsel').show();
      $('#carpper, #Enviar').hide();
    }

    //Visto bueno
    if(enviara==14){
      $('#carpper, #Enviar').hide();
      document.form1.depsel.value = '<?= $_SESSION["dependencia"] ?>';
      envioTx();
    }

    //Informar
    if(enviara==8 ){
      envioTx();
    }

   //Tramite conjunto
    if(enviara==18 ){
      envioTx();
    }

    //Borrar informado
    if(enviara==19 ){
      var depe=<?=$dependencia?>;
      var entidad='<?=$db->entidad?>';
      if ($("#dt_basic input:checked").length==1 && (depe==420 || depe==410 || depe==900) && entidad=='CRA'){
        $('#dialog-message').dialog('open');
            <?=$scriptJS?>
      }
      else{
          var rads;
          rads="";

          $('input[name^="checkValue"]').each(function(index){
              if($(this).prop('checked') && $(this) != undefined) {
                  rads += $(this).attr('id')+",";
              }
          });
          var observaBorrarInf = prompt("Por favor Introduzca el motivo para borrar el/los Informados seleccionados ", "");
          if(observaBorrarInf){
            $.post("./tx/ajaxBorrarInformado.php", {"rads":rads,"observa":observaBorrarInf}).done(
                function( data ) {
                    $('#informarUsuario').html(data);
                    setTimeout("location.reload(true);", 2500);
                }
            );
          }else{
            alert("Debe escribir un motivo de eliminacion del Documento Informado....!");
         }
      }
    }
    //Borrar conjunto
    if(enviara==101 ){
      var depe=<?=$dependencia?>;
      var entidad='<?=$db->entidad?>';
      if ($("#dt_basic input:checked").length==1 && (depe==420 || depe==410 || depe==900) && entidad=='CRA'){
        $('#dialog-message').dialog('open');
            <?=$scriptJS?>
      }
      else{
          var rads;
          rads="";

          $('input[name^="checkValue"]').each(function(index){
              if($(this).prop('checked') && $(this) != undefined) {
                  rads += $(this).attr('id')+",";
              }
          });
          var observaBorrarInf = prompt("Por favor Introduzca el motivo para borrar el/los documento seleccionado ", "");
          if(observaBorrarInf){
            $.post("./tx/ajaxBorrarTramiteConjunto.php", {"rads":rads,"observa":observaBorrarInf}).done(
                function( data ) {
                    $('#informarUsuario').html(data);
                    setTimeout("location.reload(true);", 2500);
                }
            );
          }else{
            alert("Debe escribir un motivo de eliminacion del Documento ....!");
         }
      }
    }

  }

  function changeFolder(){
   var rads;
   var i;
   codCarpeta= $('#carpper').val();
   pagina = "./tx/ajaxMoveFolder.php";
   rads="";

   $('input[name^="checkValue"]').each(function(index) {
      if($(this).prop('checked') && $(this) != undefined) {
      rads += $(this).attr('id')+",";
      }
   });

   $.post( pagina,{"codCarpeta":+codCarpeta,"rads":rads}, function( data ) {
      $('#message').append(data);
		}).fail(function ($data) {
        alert('Error del servidor');
    });
  }

  $('#AccionCaliope').on('change', changedepesel1);
  $('input[name^="checkValue"]').on('change', clickTx);
  $('#carpper').on('change', changeFolder);
});
</script>

<?php
// Si esta en la Carpeta de Visto Bueno no muesta las opciones de reenviar
$radi_usua_actu = isset($radi_usua_actu) ? $radi_usua_actu : '';
if (($mostrar_opc_envio==0) || ($_SESSION['codusuario'] == $radi_usua_actu && $_SESSION['dependencia'] == $radi_depe_actu)) {
  if ($controlAgenda==1){
      //Si el esta consultando la carpeta de documentos agendados entonces muestra el boton de sacar de la agenda
    if ($agendado){
      echo ("<input name='Submit2' type='button' class='btn btn-primary' value=' Sacar de La Agenda &gt;&gt;' onClick='txNoAgendar();'>");
    } else{
      echo(" ");
		}
	}

  if (!$agendado) {
	  if ($_SESSION['depe_codi_padre'] || $_SESSION["USUA_JEFE_DE_GRUPO"]) {
    if(!empty($permVobo) && $permVobo != 0){?>
      
		  <a href="#" onmouseout="MM_swapImgRestore()" onclick="seleccionBarra = 14;vistoBueno();" onmouseover="MM_swapImage('Image12','','<?=$ruta_raiz?>/imagenes/internas/overVobo.gif',1)"></a>
    <?}
  }

  if(!empty($_SESSION["usua_perm_trdmasiva"]) && $_SESSION["usua_perm_trdmasiva"]!=0 ){
		   	?>
		     <a href="#" onMouseOut="MM_swapImgRestore()" onClick="seleccionBarra = 19;tipificar();" onMouseOver="MM_swapImage('Image19','','<?=$ruta_raiz?>/imagenes/internas/tipificarA.gif',1)">
		     </a>
  <?php
  }

      if(!empty($permArchi) && $permArchi != 0) {

      }
  }
}
/* Final de opcion de enviar para carpetas que no son 11 y 0(VoBo)
/* si esta en la Carpeta de Visto Bueno no muesta las opciones de reenviar */

//var_dump($_SESSION); //Depuracion. Borrar.

if (($_SESSION["USUA_JEFE_DE_GRUPO"] || $_SESSION["USUA_TRAMITADOR"]) && 
    ($carpeta == 0 || $carpeta == 13)) {
  $mostrarMoverCarpeta = true;
} else {
  $mostrarMoverCarpeta = false;
}

if ((($mostrar_opc_envio==0) || ($_SESSION['codusuario'] == $radi_usua_actu && $_SESSION['dependencia'] == $radi_depe_actu))){
  	$row1 = array();
  	// Combo en el que se muestran las dependencias, en el caso  de que el usuario escoja reasignar.
  	$dependencianomb=substr($dependencianomb,0,35);
    $subDependencia = $db->conn->substr ."(d.depe_codi||' '||d.depe_nomb,0,80)";

    if($_SESSION["USUA_JEFE_DE_GRUPO"] or
        $_SESSION["usuario_reasignacion"] or
        $_SESSION["usuario_reasigna_jefes"]){
      $whereReasignar = "where d.depe_estado = 1";
    }else{
      $whereReasignar = " where d.depe_codi = $dependencia and d.depe_estado = 1";
    }

    //Start::sql solo con jefes 
      $jefe_sql = ' and( SELECT count(*) from usuario u JOIN autm_membresias m ON m.autu_id = u.id AND autg_id = 2 WHERE u.depe_codi = d.depe_codi) > 0';
    //End::sql solo con jefes 

    $sql = "select $subDependencia, d.depe_codi 
            from DEPENDENCIA d
            join DEPENDENCIA_VISIBILIDAD dv on (d.depe_codi=dv.dependencia_visible 
              and dv.dependencia_observa = $dependencia)

            
            $whereReasignar 
            $jefe_sql 
            ORDER BY DEPE_CODI";

    //$db->conn->debug = true;
    $rs  = $db->query($sql);
    //$db->conn->debug = false;

    echo "<label class='select'>";
    if($rs && !$rs->EOF){
        $depencia = $rs->GetMenu2('depsel',0,"0: -- Escoja una Dependencia --",false,0," id='depsel' class='select' ");
    }
    echo "</label>";

    // genera las dependencias para informar
    $row1 = array();

	// Aqui se muestran las carpetas Personales
	$dependencianomb=substr($dependencianomb,0,35);
	$datoPersonal = "(Personal)";
	$nombreCarpeta = $db->conn->Concat("' $datoPersonal'",'nomb_carp');
	$codigoCarpetaGen = $db->conn->Concat("10000","cast(carp_codi as varchar(10))");
	$codigoCarpetaPer = $db->conn->Concat("11000","cast(codi_carp as varchar(10))");
	$sql = "select carp_desc  as nomb_carp
			,$codigoCarpetaGen as carp_codi, 0 as orden
			from carpeta
			where carp_codi <> 11
			union
			select $nombreCarpeta as nomb_carp
			,$codigoCarpetaPer as carp_codi
			,1 as orden
			from carpeta_per
			where
			usua_codi = $codusuario
			and depe_codi = $dependencia
			order by orden, carp_codi";
	$rs = $db->conn->Execute($sql);
	$sCarpeta  =  "<div><label class='select'>";
	$sCarpeta .= $rs->GetMenu2('carpSel',1,"0:-- Seleccione una Carpeta --",false,0," id='carpper' class='select' ");
	$sCarpeta .= "</label></div>";

	// Fin de Muestra de Carpetas personales
	?>

	<INPUT TYPE=hidden name=enviara value=9>
	<INPUT TYPE=hidden name=EnviaraV id=EnviaraV value=''>
	<input type="button" value='' name="Enviar" id="Enviar" valign='middle' class='botones_2' onClick="envioTx();">
	<input type="hidden" name="codTx" value=>

<? }

if($tieneAsignacion){ ?>
    <div>
        <label class="select" >
          <select id="AccionCaliope" name="AccionCaliope" size="1" aria-controls="dt_basic">
            <option value="9" selected="selected">Escoja una accion...</option>
            <option value="18" >Enviar a ...</option>
            <option value="13">Finalizar Tramite...</option>
          </select>
        </label>
        <label class="select" > <?=$depencia?> </label>
    </div>
<?php }
echo $sCarpeta = isset($sCarpeta) ? $sCarpeta : '';

if(isset($verradPermisos) && $verradPermisos=="Full" && !$tieneAsignacion){ ?>
    <div>
        <label class="select" >
          <select title="Seleccione una accion a realizar con el radicado" id="AccionCaliope" name="AccionCaliope" size="1" aria-controls="dt_basic">
            <option value="0" selected="selected">Escoja una accion...</option>
            <option value="9" >Enviar a...</option>
            <option value="14">Enviar a Visto Bueno.</option>
            <?php if ($mostrarMoverCarpeta) {?>
              <option value="10">Mover a Carpeta...</option>
            <?php } ?>
            <option value="8" >a Informar...</option>
            <option value="12">Devolver...</option>
            <option value="21">Masiva Tipificar</option>
            <option value="20">Masiva Expedientes</option>
            <option value="13">Finalizar Tramite...</option>
            <option value="16">NRR...</option>
           <!--  <option value="14">Agendar...</option> -->
          </select>
        </label>
        <label class="select" > <?=$depencia?> </label>
    </div>
<?php }
echo $sCarpeta = isset($sCarpeta) ? $sCarpeta : '';
?>
<div id="message"></div>
