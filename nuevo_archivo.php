<?php
session_start();
$verrad = $_GET['radi'];
$is_edit = trim($_GET['tpradic']);

$ruta_raiz = ".";
if (!$_SESSION['dependencia']) header ("Location: $ruta_raiz/cerrar_session.php");
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
if(!$numrad) $numrad = $radi;

$subir_archivo  = (isset($subir_archivo))? $subir_archivo : false;
$nuevo_archivo  = (isset($nuevo_archivo))? $nuevo_archivo : false;

define('ADODB_ASSOC_CASE', 1);
include_once "$ruta_raiz/class_control/AplIntegrada.php";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";

if(!$db){
  $db  = new ConnectionHandler($ruta_raiz);
}
include_once "$ruta_raiz/radicacion/busca_direcciones.php";

$CONTENT_PATH =$_SESSION["CONTENT_PATH"];

if($subir_archivo != true){
    $krd         = $_SESSION["krd"];
    $dependencia = $_SESSION["dependencia"];
    $usua_doc    = $_SESSION["usua_doc"];
    $codusuario  = $_SESSION["codusuario"];
    $tpNumRad    = $_SESSION["tpNumRad"];
    $tpPerRad    = $_SESSION["tpPerRad"];
    $tpDescRad   = $_SESSION["tpDescRad"];
    $tip3Nombre  = $_SESSION["tip3Nombre"];

}

if(!$ent){
    $ent = substr(trim($numrad),strlen($numrad)-1,1);
}

$nombreTp3 = $tip3Nombre[3][$ent];

if(!$db){
  $db  = new ConnectionHandler($ruta_raiz);
}

$dbAux = new ConnectionHandler($ruta_raiz);

$conexion      = $db;
$rowar         = array();
$mensaje       = null;
$tipoDocumento = explode("-", $tipoLista);
$tipoDocumentoSeleccionado = $tipoDocumento[1];

$isql = "select USUA_LOGIN,USUA_PASW,CODI_NIVEL from usuario where (usua_login ='$krd') ";
$rs   = $db->conn->Execute($isql);

if ($rs->EOF){
    $mensaje="No tiene permisos para ver el documento";
}else{
    $nivel=$rs->fields["CODI_NIVEL"];
    ($tipo==0) ? $psql = " where  anex_tipo_codi<50 " : $psql=" ";
    $isql = "select ANEX_TIPO_CODI,
                    ANEX_TIPO_DESC,
                    ANEX_TIPO_EXT
              from anexos_tipo
                    $psql
              order by anex_tipo_desc desc";
    $rs=$db->conn->Execute($isql);
}

if ($resp1=="OK"){
    $mensaje = ($subir_archivo)? "<span class=info>Archivo anexado correctamente</span></br>" :
                                "Anexo Modificado Correctamente<br>No se anex&oacute; ning&uacute;n archivo</br>";
}else if ($resp1=="ERROR"){
    $mensaje="<span class=alarmas>Error al anexar archivos</span></br>";
}

include "$ruta_raiz/radicacion/crea_combos_universales.php";

if (!function_exists(return_bytes)){
    // Retorna la cantidad de bytes de una expresion como 7M, 4G u 8K.
    function return_bytes($val){
        $val = trim($val);
        $ultimo = strtolower($val{strlen($val)-1});
        switch($ultimo){
            // El modificador 'G' se encuentra disponible desde PHP 5.1.0
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
        }
        return $val;
    }
}

$consultaESP  = "select r.EESP_CODI from radicado r where r.radi_nume_radi = $numrad";
$rsESP        = $db->conn->Execute($consultaESP);






$datos_envio  = "&otro_us11=$otro_us11&codigo=$codigo&dpto_nombre_us11=$dpto_nombre_us11&direccion_us11=".urlencode($direccion_us11)."&muni_nombre_us11=$muni_nombre_us11&nombret_us11=$nombret_us11";
$datos_envio .="&otro_us2=$otro_us2&dpto_nombre_us2=$dpto_nombre_us2&muni_nombre_us2=$muni_nombre_us2&direccion_us2=".urlencode($direccion_us2)."&nombret_us2=$nombret_us2";
$datos_envio .="&dpto_nombre_us3=$dpto_nombre_us3&muni_nombre_us3=$muni_nombre_us3&direccion_us3=".urlencode($direccion_us3)."&nombret_us3=$nombret_us3";
$variables    = "ent=$ent&".session_name()."=".trim(session_id())."&tipo=$tipo$datos_envio";

if (!empty($codigo)){

    $isql  = "SELECT
                    ANEX_SALIDA,
                    ANEX_TIPO
                FROM
                    ANEXOS
                WHERE
                  ANEX_CODIGO='$codigo'";

    $rest       = $db->conn->Execute($isql);
    $anexsalida = $rest->fields['ANEX_SALIDA'];
    $anexTipoAux = $rest->fields['ANEX_TIPO'];
    if($anexsalida == 1){
        $anexsalida = 'checked';
    }else{
        $anexsalida = '';
    }
}

//compruebo que si es entrada siempre saque una salida
$tip_rest = substr($verrad,-1);if ($tip_rest == 2){$inptpradic = 1 ;}else{$inptpradic =$tip_rest;}

?>

<html>
<head>
<title>Informaci&oacute;n de Anexos</title>
	<meta charset="utf-8">
  
	<link rel="shortcut icon" href="<?=$ruta_raiz?>/img/favicon.png">
	<!-- Bootstrap core CSS -->
	<?php include_once "$ruta_raiz/htmlheader.inc.php"; ?>
  <SCRIPT Language="JavaScript" SRC="js/crea_combos_2.js"></SCRIPT>
<script language="javascript">
  var datasend = '<?=$variables?>'
  console.log(datasend);
  <?php
  // Convertimos los vectores de los paises, dptos y municipios creados en crea_combos_universales.php a vectores en JavaScript.
  echo arrayToJsArray($vpaisesv, 'vp');
  echo arrayToJsArray($vdptosv, 'vd');
  echo arrayToJsArray($vmcposv, 'vm');
  ?>

  function mostrar(nombreCapa){
    document.getElementById(nombreCapa).style.display="";
  }

  function continuar_grabar(){
    var tpradicu = document.forms['formulario']['tpradic'].value;
    document.formulario.tpradic.disabled=false;
    document.formulario.action=document.formulario.action+"&cc=GrabarDestinatario&"+"tpradicu="+tpradicu+"&"+ datasend;
  document.formulario.submit();
  }

  function mostrarNombre(nombreCapa){
    document.formulario.elements[nombreCapa].style.display="";
  }

  function ocultarNombre(nombreCapa){
    document.formulario.elements[nombreCapa].style.display="none";
  }

  function ocultar(nombreCapa){
    document.getElementById(nombreCapa).style.display="none";
  }

  function Start(URL, WIDTH, HEIGHT){
  windowprops = "top=0,left=0,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=1220,height=700";
  preview = window.open(URL , "preview", windowprops);
  }

  function doc_radicado(){
      if (document.formulario.radicado_salida.checked){         
          document.formulario.tpradic.disabled=false;
	        document.forms['formulario']['tpradic'].value = '<?php echo $inptpradic ?>'
      }else{
         document.formulario.tpradic.disabled=true;
	       document.forms['formulario']['tpradic'].value = 0;
      }
  }

  function f_close(){
    opener.regresar();
    window.close();$numrad
  }

  function regresar(){
    f_close();
  }

  function escogio_archivo(){
      var largo;
      var valor;
      var extension;
      archivo_up = document.getElementById('userfile').value;
      valor = 0;
      var mySplitResult = archivo_up.split(".");

      for(i = 0; i < (mySplitResult.length); i++){
          extension = mySplitResult[i];
      }
      extension = extension.toLowerCase();
      <? while (!$rs->EOF){
      echo "
          if (  extension=='".$rs->fields["ANEX_TIPO_EXT"]."'){
              valor=".$rs->fields["ANEX_TIPO_CODI"].";
          }\n";
        $rs->MoveNext();
      }     
      $anexos_isql = $isql;
      ?>
       
      if(valor == 0){
        alert("La extensión ." + extension + " no es permitida");
        document.getElementById('userfile').value = "";
      } 
      document.getElementById('tipo_clase').value = valor;
  }


  function validarGenerico(){

      var i     = 0;
      var marca = 0;
      var envio_pendiente = 0;

      if (document.formulario.radicado_salida.checked && document.formulario.tpradic.value=='null'){
          alert ("Debe seleccionar el tipo de radicación");
          return false;
      }

      if (document.formulario.radicado_salida.checked && document.formulario.tpradic.value==0){
          alert ("Debe seleccionar el tipo de radicación");
          return false;
      }    

      if (document.formulario.radicado_salida.checked && !document.formulario.sololect.checked){
          
          if(document.formulario.tpradic.value == 1 || document.formulario.tpradic.value == 2) {
                $('select[name^="envio_"]').each(function(i, e) {
                  if($(this).val() == '')
                  {
                      envio_pendiente ++;
                  }
                });

                if(envio_pendiente > 0)
                {
                  alert ("Debe seleccionar los medios de envío");
                  return false;
                }
          }
      }

      archivo=document.getElementById('userfile').value;
      
      if (archivo==""){
          <?php
            if($tipo==0 and !$codigo){
                echo "alert('Por favor escoja un archivo'); return false;";
            }else{
                ?>
                if (document.formulario.radicado_salida.checked) {
                  <?php
                  if($anexTipoAux != 18) {
                    echo "alert('Solo documentos con extensión .docx para radicado.'); return false;";
                  } else {
                    echo "return true;";  
                  } 
                 ?>
                }
                <?php                
            }
          ?>
      }

      if (document.formulario.radicado_salida.checked) {
        if(archivo.substring(archivo.lastIndexOf('.')+1, archivo.length) == "docx") {

        } else {
            alert ("Solo documentos con extensión .docx para radicado.");
            return false;          
        }
      }

      copias = document.getElementById('i_copias').value;

//      if(copias==0 && document.getElementById('radicado_salida').checked==true){
//          document.getElementById('radicado_salida').checked=false;
//      }

      return true;
  }
  $(document).ready(function() {

     $( "#b_asunto" ).click(function() {
	      var text = $( "#asunto_padre" ).val();
	      $( "#descr" ).val( text );
      });
      
      $('#sololect').on('change', function(e) {
        var checked = $(this).is(':checked');
        if(checked)
            $('select[name^="envio_"]').hide();
        else
            $('select[name^="envio_"]').show();
      });

      let tipo = '<?= $inptpradic ?>';
      if(tipo != '1') {
        $(`.certificadoCheck`).hide();
      }

      $('body').delegate('select[name^="envio_"]','change',function(){
        if($(this).val() == 'Físico'){
          let id = $(this).data('id')
          $(`input[name=validez_${id}]`).hide()
        }else{
          let id = $(this).data('id')
          if(tipo != '1') {
            $(`input[name=validez_${id}]`).hide()
          } else  {
          $(`input[name=validez_${id}]`).show()
          }
        }
      })


      $("#actualizar").on("click", function(e){
      $("input[name^='_tpradicu']").val($("select[name^='tpradic']").val());
          if (!validarGenerico()){
              return;
          }
          $(this).prop('disabled',true)
          document.formulario.submit();
      });
  });

</script>
</head>
<body class="smart-form">
<div>
<form enctype="multipart/form-data" method="POST" name="formulario" id="formulario" action='upload2.php?<?=$variables?>' >

<?php //ESTE INCLUDE PERMITE PASAR HERENCIA A UN ANEXO
include 'datos_rad_padre.php'; ?>

<input type="hidden" name="asunto_padre" id="asunto_padre" value="<?=$asunto?>">
<input type="hidden" name="_tpradicu" value="">
<input type="hidden" name="subir_archivo" value="<?=$subir_archivo?>"> 
<input type="hidden" name="verrad" value="<?=$verrad?>"> 
<input type="hidden" name="nuevo_archivo" value="<?=$nuevo_archivo?>"> 
<?php
$i_copias = 0;


  
  

  

if ($codigo){

    $isql = "SELECT CODI_NIVEL
                  ,a.ANEX_SOLO_LECT
                  ,a.ANEX_CREADOR
                  ,a.ANEX_DESC
                  ,at.ANEX_TIPO_EXT
                  ,a.ANEX_NUMERO
                  ,a.ANEX_RADI_NUME
                  ,a.ANEX_NOMB_ARCHIVO AS nombre
                  ,a.ANEX_SALIDA
                  ,a.ANEX_ESTADO
                  ,a.SGD_DIR_TIPO
                  ,a.RADI_NUME_SALIDA
                  ,SGD_DIR_DIRECCION
                  ,a.ANEX_TIPO_ENVIO
              FROM
                  ANEXOS a,
                  ANEXOS_TIPO at,
                  RADICADO
              WHERE
                  a.ANEX_CODIGO='$codigo'
                  AND a.ANEX_RADI_NUME=RADI_NUME_RADI
                  AND a.ANEX_TIPO=at.ANEX_TIPO_CODI";

  $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
  $rs=$db->conn->Execute($isql);
  if (!$rs->EOF){
    $docunivel        = ($rs->fields["CODI_NIVEL"]);
    $sololect         = ($rs->fields["ANEX_SOLO_LECT"]=="S");
    $remitente        = $rs->fields["SGD_DIR_TIPO"].'remitenteee';
    $num_remitente    = $rs->fields["SGD_DIR_TIPO"];
    $extension        = $rs->fields["ANEX_TIPO_EXT"].'PP';
    $radicado_salida  = $rs->fields["ANEX_SALIDA"];
    $anex_estado      = $rs->fields["ANEX_ESTADO"];
    $descr            = $rs->fields["ANEX_DESC"];
    $radsalida        = $rs->fields["RADI_NUME_SALIDA"];
    $direccionAlterna = $rs->fields["SGD_DIR_DIRECCION"];
    $direccionAlterna = $rs->fields["SGD_DIR_DIRECCION"];
    $medioRadicar     = $rs->fields["ANEX_TIPO_ENVIO"];
    // SGD_DIR_TIPO  = 7 es otro reminte

    if(!empty($remitente)){
        $radicado_rem = $num_remitente;
    }

  }

}
if(!$radicado_rem){
  $radicado_rem = 1;
}

    $idDre = [];
    $iSql = "SELECT * FROM SGD_DIR_DRECCIONES WHERE RADI_NUME_RADI = '$verrad'";
    //$db->conn->debug = true;
    $rsDestinatarios = $db->conn->query($iSql);
    $tablaHtmlDestinatarios = "<table border=0 class='table'>";  
    
    if($ent >= 3 && $ent <=7) {
      $tablaHtmlDestinatarios .= "<thead><tr><th style='color: #333'>Destinatario</th><th style='color: #333'>Medio de envío</th><th style='color: #333'></th></tr></thead>";
    } else {
      $tablaHtmlDestinatarios .= "<thead><tr><th style='color: #333'>Destinatario</th><th style='color: #333'>Medio de envío</th><th style='color: #333'>¿Requiere certificado electrónico del envío? </th></tr></thead>";
    }
    if(!$radicado_rem) $radicado_rem=1;
    while($rsDestinatarios && !$rsDestinatarios->EOF){
      $sgdDirId = $rsDestinatarios->fields["ID"];
      $sgdDirTipoRad = trim($rsDestinatarios->fields["SGD_DIR_TIPO"]);
      $idDre[] = $rsDestinatarios->fields["ID"];
      $nombreRem[$sgdDirTipoRad] = trim($rsDestinatarios->fields["SGD_DIR_NOMBRE"]);
      $direccionRem[$sgdDirTipoRad] = trim($rsDestinatarios->fields["SGD_DIR_DIRECCION"]);

      $muniCodi = trim($rsDestinatarios->fields["MUNI_CODI"]);
      $dptoCodi = trim($rsDestinatarios->fields["DPTO_CODI"]);

      $a = new LOCALIZACION($dptoCodi,$muniCodi,$db);
      $dpto_nombre = $a->departamento;
      $muni_nombre = $a->municipio;
      $dpto_nombre_us[$sgdDirTipoRad] = $dpto_nombre;
      $muni_nombre_us[$sgdDirTipoRad] = $muni_nombre;
      
      $tablaHtmlDestinatarios .= "<!-- Destinatario valor de seleccion - $sgdDirTipoRad - -->"; 
      $tablaHtmlDestinatarios .= "<tr valign='top'> ";
      $tablaHtmlDestinatarios .= "  <td valign='top' colspan='1'><small> ";
      $arrayusuario = $arrayusuario."-"."1";
      if($radicado_rem==$sgdDirTipoRad){$datoss =  " checked ";}else{$datoss =  " ";}
      $tablaHtmlDestinatarios .= "<input type='radio'   name='radicado_rem_p' value='$sgdDirTipoRad'  id='rusuario' $datoss > ";
      $tablaHtmlDestinatarios .= $nombreRem[$sgdDirTipoRad];
      $tablaHtmlDestinatarios .= " <br> ".$direccionRem[$sgdDirTipoRad].' '.$dpto_nombre_us[$sgdDirTipoRad] . " / " . $muni_nombre_us[$sgdDirTipoRad];
      $tablaHtmlDestinatarios .= "</td>";
      $tablaHtmlDestinatarios .= "<td><select name='envio_$sgdDirId' data-id='$sgdDirId' class=''><option value=''>Seleccionar</option><option value='E-mail'>E-mail</option><option value='Físico'>Físico</option><option value='Ambos'>Ambos</option></select></td>";
      $tablaHtmlDestinatarios .= "<td><input class='certificadoCheck' name='validez_$sgdDirId' type=checkbox checked/></td>";
      $tablaHtmlDestinatarios .= "</tr>";
      $rsDestinatarios->MoveNext();
  } 
  $tablaHtmlDestinatarios .= "</table>";

?>
<div class="row">
	<div class="col-lg-12">
	<section id="widget-grid" class="">

<table class="table table-bordered">
<tr>
  <td>
  <input type="hidden" name="id_dre" value="<?=implode(',', $idDre)?>">
<input type="hidden" name="anex_origen" value="<?=$tipo?>">
<input type="hidden" name="tipo" value="<?=$tipo?>"  id="tipo_clase">
<input type="hidden" name="numrad" value="<?=$numrad?>">
<input type="hidden" name="tipoLista" value="<?=$tipoLista?>">
<input type="hidden" name="tipoDocumentoSeleccionado" value="<?php echo $tipoDocumentoSeleccionado ?>">
<table width="100%" class="table table-bordered">
  <tr>
    <td >
      <input type="checkbox" class="select"  name="sololect" <?php  if($sololect){echo " checked ";}  ?> id="sololect">
      <small>Solo lectura</small>
    </td>
    <td colspan="3" >
    <table border=0 width=100% cellspacing="1" cellpadding="1">
    <tr>
    <td width=50% >
<?php
$us_1   = "";
$us_2   = "";
$us_3   = "";
$datoss = "";

     

if ($nombreRem[1] && $direccionRem[1] && $muni_nombre_us[1] && $dpto_nombre_us[1]){
    $us_1 = "si"; $usuar=1;
    if($remitente==1) {$datoss1=" checked " ;  }
}else{
    $datoss1=" disabled ";
}

$datoss = "";
if ($nombreRem[2] && $direccionRem[2] && $muni_nombre_us[2] && $dpto_nombre_us[2] )
{ $us_2 = "si"; $predi=1;
  if($remitente==2) $datoss2=" checked  " ;
}
else
{ $datoss2=" disabled ";  }

$datoss = "";
if ($nombreRem[3] && $direccionRem[3] && $muni_nombre_us[3] && $dpto_nombre_us[3] )
{
  $us_3 = "si";
  $empre=1;
  if($remitente==3) $datoss3=" checked  " ;
}
else  {  $datoss3=" disabled " ;}

if ($remitente==7)  $datoss4=" checked  ";
else  $datoss4 = "";

if($us_1 or $us_2 or $us_3){
  echo "<input type='checkbox' class='select' name='radicado_salida' $anexsalida value='1' onClick='doc_radicado();' id='radicado_salida'>";
  echo "<small>  Este documento ser&aacute; radicado</small>";
}else{ ?>
  <small>Este documento no puede ser radicado ya que faltan datos.<br>
  (Para envio son obligatorios Nombre, Direccion, Departamento,
  Municipio)</small>
<?php
}
?>
    </td>
    <td >
<?php
$comboRadOps="";
//if ($ent!=1 ){
  $deshab=" disabled=true ";
//}

$comboRad       = "<label class='select'><select name = 'tpradic' class = 'select' $deshab  $eventoIntegra >";
$comboRadSelecc = "<option selected value = 0 >- Tipos de Radicacion -</option>";
$sel            = "";

if(!$tpradic) $tpradic=$ent;

//Si el radicado es una entrada, SIEMPRE se radica una salida, no deben aparecer mas opciones.
//$tip_rest = substr($verrad,-1);if ($tip_rest == 2){$tpradic = 1 ;}else{$tpradic = $tip_rest;}
//var_dump($_SESSION["USUA_PRAD_TPR"]); exit;
foreach ($tpNumRad as $key => $valueTp){

    if(strcmp(trim($tpradic),trim($valueTp))==0){
      	if ($is_edit >=1){
            $sel="selected";
        }else{
            $sel="";
        }
        $comboIntSwSel=1;
    }



    if($valueTp != 9 and $valueTp != 2){      
        //Si se definio prioridad en algun tipo de radicacion
        $valueDesc = $tpDescRad[$key];
//	if ($_SESSION["USUA_PRAD_TPR"][$valueTp]>0){
	if ($_SESSION["tpPerRad"][$valueTp]>0){
        if ($is_edit == $valueTp ){$sel="selected";}else{$sel="";}
      }
	
  
	//Si el radicado es de entrada, siempre va a generar una salida o memorando.
	if ($tpradic == 2){
    	 if($valueTp == 1 ){
        	$comboRadOps =$comboRadOps . "<option value='".$valueTp."' $sel>".$valueDesc."</option>";
        	$sel="";
    	 } 
       if($valueTp == 3 ){
          $comboRadOps =$comboRadOps . "<option value='".$valueTp."' $sel>".$valueDesc."</option>";
          $sel="";
       }
  }else{
          if ($tpradic == $valueTp){ 
            $comboRadOps =$comboRadOps . "<option value='".$valueTp."' $sel>".$valueDesc."</option>";
            $sel="";
          }
          if ($tpradic == 3 && $valueTp == 1){ 
            $comboRadOps =$comboRadOps . "<option value='".$valueTp."' $sel>".$valueDesc."</option>";
            $sel="";
          }

          /*$comboRadOps =$comboRadOps . "<option value='".$valueTp."' $sel>".$valueDesc."</option>";
          $sel="";*/
  }
	$sel="";
	
}
    

}

$comboRad = $comboRad.$comboRadSelecc.$comboRadOps."</select></label>";

?>
   <small> Radicaci&oacute;n  <?=$comboRad?> </small><BR>
<?php

if ($ent==1){
  echo ("<script>doc_radicado();</script>");
}

if (strlen(trim($swDischekRad)) > 0){
  echo ("<script>document.formulario.tpradic.disabled=true;</script>");
}
//habilitar o deshabilitar select dependiento si es una edicion o no
if ($is_edit >= 1){
    echo ("<script>document.formulario.tpradic.disabled=false;</script>");
} else{
    
}
?>
    </td>
    </tr>
    </table>
    </td>
  </tr>

  <tr>
  <td  ><button name="button" type="button" class="btn btn-success" id="b_asunto" <?=$codigo?>> Asunto </button>
 </td>
    <td  valign="top" >
      <textarea name="descr" cols="60" rows="1" class="text" id="descr" maxlength="500"><?=$descr?></textarea>
    </td>
  </tr>
  <tr>
        <td ><small>Expediente:</small></td>
      <td  valign="top"  >
      <table border="0"  class="borde_tab" align="center">
      <tr >
<?php

$q_exp  = "SELECT SGD_EXP_NUMERO as valor,
                  SGD_EXP_NUMERO as etiqueta,
                  SGD_EXP_FECH as fecha";
$q_exp .= " FROM SGD_EXP_EXPEDIENTE ";
$q_exp .= " WHERE RADI_NUME_RADI = " . $numrad;
$q_exp .= " AND SGD_EXP_ESTADO <> 2";
$q_exp .= " ORDER BY fecha desc";
//$db->conn->debug = true;
$rs_exp = $db->conn->Execute( $q_exp );

if( $rs_exp->EOF ){
  $mostrarAlerta  = "<td align=\"center\" >";
  $mostrarAlerta .= "<b><small>EL RADICADO PADRE NO ESTA INCLUIDO  EN UN EXPEDIENTE.</small></b></td>";
  $sqlt = "select RADI_USUA_ACTU,RADI_DEPE_ACTU from RADICADO where RADI_NUME_RADI = '$numrad'";
  $rsE  = $db->conn->Execute($sqlt);
  $depe = $rsE->fields['RADI_DEPE_ACTU'];
  $usua = $rsE->fields['RADI_USUA_ACTU'];
  echo $mostrarAlerta;
}else{
    if($rs_exp && !$rs_exp->EOF){
        $menuSel = $rs_exp->GetMenu( 'expIncluidoAnexo', $expIncluidoAnexo, false, false, 0, "class='select'", false );
        echo "<td align='center'>
                <label class='select'>
                    {$menuSel}
                </label>
              </td>";
    }
}
?>
	 </tr>
	 </table>
	</tr>

	 <tr>
	  <td  align="center"  colspan="2">
	 	 <small>Destinatario </small>
  	</td>
	</tr><td colspan='3'>
    <?=$tablaHtmlDestinatarios ?>
   </td>
<?php

    if($codigo){ ?>
        <tr><td height='3px' colspan="2"></td></tr>

      <!-- Listado de destinos buscados por usuario-->
      <!-- Destinatario valor de seleccion -codigo dir_direcciones- -->
        <?php
          if($borrar)
          {
            $isql = "delete from sgd_dir_drecciones where sgd_anex_codigo='$codigo' and sgd_dir_tipo = $borrar ";
            $rs=$db->conn->Execute($isql);
          }
            //Si viene la variable cc(Boton de destino copia)
            //envia al modulo de grabacion de datos
           if($cc){
              if (($nombre_us1 or $prim_apel_us1 or $seg_apel_us2)
                  and  $direccion_us1
                  and $muni_us1 and $codep_us1){

                  $isql = "SELECT
                      SGD_DIR_TIPO NUM
                      FROM
                      SGD_DIR_DRECCIONES
                      WHERE
                      SGD_ANEX_CODIGO='$codigo'
                      ORDER BY SGD_DIR_TIPO";

                  $rs = $db->conn->Execute($isql);

                  if (!$rs->EOF)$num_anexos = substr($rs->fields["NUM"],1,2);
                  if(!$nurad){
                      $nurad = $numrad;
                  }
									echo "<hr> llego hasta aka <hr>";
									if($cc_documento_us1) { $documento_us1= $cc_documento_us1; $cc="";}
                  include "$ruta_raiz/radicacion/grb_direcciones.php";
                  $grabar = "<font size=1>Ha sido agregado el destinatario.</font>";
              }else{
                  $grabar = "<font size=1  class='titulosError2'>
                      faltan datos.(Los datos m&iacute;nimos de envio so Nombre,
                      direccion, departamento, municipio)";
              }
          }

          $i_copias = 0; //Cuantos copias se han añadido
          include_once "$ruta_raiz/include/query/queryNuevo_archivo.php";
          $isql = $query1;
          $rs=$db->conn->Execute($isql);

          $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
          $rs=$db->conn->Execute($isql);

          echo "
              <tr>
              <td colspan='2'>
              <table width='100%' class='table table-bordered'> ";

          while(!$rs->EOF && $rs ){
              $i_copias++;
              $sgd_ciu_codigo = "";
              $sgd_esp_codi   = ""; $sgd_oem_codi = "";
              $sgd_dir_codi   = $rs->fields["SGD_DIR_CODIGO"];
              $sgd_ciu_codi   = $rs->fields["SGD_CIU_CODIGO"];
              $sgd_esp_codi   = $rs->fields["SGD_ESP_CODI"];
              $sgd_oem_codi   = $rs->fields["SGD_OEM_CODIGO"];
              $sgd_dir_tipo   = $rs->fields["SGD_DIR_TIPO"];
              $sgd_doc_fun    = $rs->fields["SGD_DOC_FUN"];

              if($sgd_ciu_codi>0){
                  $isql = "SELECT
                      SGD_CIU_NOMBRE AS NOMBRE,
                      SGD_CIU_APELL1 AS APELL1,
                      SGD_CIU_APELL2 AS APELL2,
                      SGD_CIU_CEDULA AS IDENTIFICADOR,
                      SGD_CIU_EMAIL  AS MAIL,
                      SGD_CIU_DIRECCION  AS DIRECCION
                      FROM
                      SGD_CIU_CIUDADANO
                      WHERE
                      SGD_CIU_CODIGo=$sgd_ciu_codi";
              }

              if($sgd_esp_codi>0){
                  $isql = "SELECT
                      NOMBRE_DE_LA_EMPRESA AS NOMBRE,
                      IDENTIFICADOR_EMPRESA AS IDENTIFICADOR,
                      EMAIL AS MAIL,
                      DIRECCION AS DIRECCION
                      FROM
                      BODEGA_EMPRESAS
                      WHERE
                      IDENTIFICADOR_EMPRESA=$sgd_esp_codi";
              }

              if($sgd_oem_codi>0){
                  $isql = "SELECT
                      SGD_OEM_OEMPRESA AS NOMBRE,
                      SGD_OEM_DIRECCION AS DIRECCION,
                      SGD_OEM_CODIGO AS IDENTIFICADOR
                      FROM
                      SGD_OEM_OEMPRESAS
                      WHERE
                      SGD_OEM_CODIGO=$sgd_oem_codi";
              }

              if($sgd_doc_fun>0) {
                  $isql = "SELECT
                      USUA_NOMB AS NOMBRE,
                      D.DEPE_NOMB AS DIRECCION,
                      USUA_DOC AS IDENTIFICADOR,
                      USUA_EMAIL AS MAIL
                      FROM
                      USUARIO U ,
                      DEPENDENCIA D
                      WHERE
                      USUA_DOC='$sgd_doc_fun'
                      and  u.DEPE_CODI = d.DEPE_CODI ";
              }

              $rs2 = $db->conn->Execute($isql);
		      $nombre_otros = "";
              if($rs2 && !$rs2->EOF){
                  $nombre_otros =$rs2->fields["NOMBRE"]."".$rs2->fields["APELL1"]." ".$rs2->fields["APELL2"];
              }
              ?>

              <tr>
		 <td  align="center"  colspan="1" >
		   <small>
		       <input type="radio"   name="radicado_rem" value=<?=$sgd_dir_tipo?>  id="rusuario"
			'<?php  if($radicado_rem==$sgd_dir_tipo){echo " checked ";}?>'>
		   </small>
		 </td>
                  
    <td width='100%' align="center"  colspan="2" >
      <small>
          <?=$nombre_otros?>
          <?=$rs2->fields["DIRECCION"];?>
      </small>
    </td>
    <td align="center"  colspan="1">
      <small>
          <a href='nuevo_archivo.php?<?=$variables?>&borrar=<?=$sgd_dir_tipo?>&tpradic=<?=$tpradic?>&numrad=<?=$numrad?>&aplinteg=<?=$aplinteg?>'>Borrar</a>
      </small>

    </td>
  </tr>

  <?php
	      $arrayusuario = $arrayusuario."-".$sgd_dir_tipo;
              $rs->MoveNext();
          }
          echo "    </table>
                  </td>
                </tr>";
        ?>
        <input name="usuar" type="hidden" id="usuar" value="<?php echo $usuar ?>">
        <input name="predi" type="hidden" id="predi" value="<?php echo $predi ?>">
        <input name="empre" type="hidden" id="empre" value="<?php echo $empre ?>">

	<input type="hidden" name="arrayusuario" value="<?=$arrayusuario?>" >

        <?php
        if($tipo==999999){
                    echo " <div align='left'>
              <font size='1' color='#000000'><b>Ubicaci&oacute;n F&iacute;sica:</b></font>
              <input type='text' name='anex_ubic' value='$anex_ubic'>
              ";
        }
        ?>

    </tr>
      <tr>
	<?php
}
  $maximo_tamano = number_format((return_bytes(ini_get('upload_max_filesize')))/1000000,2);
  $tamano_archivo = return_bytes(ini_get('upload_max_filesize'));
?>
    <tr><td colspan="2"></td></tr>
    <tr align="center">
          <td align="center" colspan="2" >
          <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $tamano_archivo; ?>">
						   <input name="userfile1" type="file" onChange="escogio_archivo();" id="userfile" value="valor">
						<small>Archivo debe ser menor a <?php echo $maximo_tamano; ?>Mb.</small>
						<p><small class="btn btn-<? if ($resp1=="ERROR"){ echo "danger"; }else{ echo "success";}?>"><?=$mensaje?></small></p>
<!--						<p><small class="btn btn-success"><?=$mostrar_mensaje?></small></p> -->
          </td>
    </tr>

    <tr>
        <TD colspan="2" align="center">
        <footer>
            <button name="button" type="button" class="btn btn-success" id="actualizar" <?=$codigo?>> Actualizar </button>
            <?php
                echo "<input type='button' id='cerraranexar' class='btn btn-default' value='Cerrar'>";
            ?>
        <footer>
      </td>
    </tr>
	 </table>
	 <input type="hidden" name="i_copias" value='<?=$i_copias?>' id="i_copias" >
	 </td>
	 </tr>
	</table>
	</DIV>
	</DIV>
</form>
</div>
</body>

<script type="text/javascript">
  $(document).ready(function() {
      $('body').on("click", '#cerraranexar',function(){
          window.opener.$.fn.cargarPagina("./lista_anexos.php","tabs-c"); window.close();
      });
  });
</script>

</html>
