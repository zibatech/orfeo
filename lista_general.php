<?php
/**
* @author Jairo Losada   <jlosada@gmail.com>
* @author Cesar Gonzalez <aurigadl@gmail.com>
* @license  GNU AFFERO GENERAL PUBLIC LICENSE
* @copyright

SIIM2 Models are the data definition of SIIM2 Information System
Copyright (C) 2013 Infometrika Ltda.

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

$ruta_raiz = ".";
if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");

$lkGenerico = "&usuario=$krd&nsesion=".trim(session_id())."&nro=$verradicado"."$datos_envio";

$sqlremitente = "select SGD_DIR_NOMBRE, SGD_DIR_NOMREMDES from SGD_DIR_DRECCIONES t where t.radi_nume_radi = '$numrad'";
$rsRemitente = $db->conn->Execute($sqlremitente);
$SGD_DIR_NOMBRE = $rsRemitente->fields['SGD_DIR_NOMREMDES'];
$isqlDepR = "SELECT RADI_DEPE_ACTU,RADI_USUA_ACTU, RADI_DATO_001, RADI_DATO_002 from radicado	WHERE RADI_NUME_RADI = '$numrad'";
$rsDepR = $db->conn->Execute($isqlDepR);
$coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
$codusua = $rsDepR->fields['RADI_USUA_ACTU'];
$radi_dato_001 = $rsDepR->fields['RADI_DATO_001'];
$radi_dato_002 = $rsDepR->fields['RADI_DATO_002'];
$ind_ProcAnex="N";

$sqlFirmador = "SELECT usua_nomb FROM usuario
where usua_codi= (select radi_usua_firma from radicado where radi_nume_radi = $numrad)
  and depe_codi = (select radi_depe_firma from radicado where radi_nume_radi = $numrad)";
$rsFirmador = $db->conn->Execute($sqlFirmador);

if($rsFirmador && !$rsFirmador->EOF) {
  $usuaFirmador = $rsFirmador->fields['USUA_NOMB'];
} else {
  $usuaFirmador = "No definidio";
}

?>

<script>

function regresar() {	//window.history.go(0);
	window.location.reload();
  //window.location.href='<a href="#" ></a>'
}
function CambiarE(est,numeroExpediente) {
        window.open("<?=$ruta_raiz?>/archivo/cambiar.php?<?=session_name()?>=<?=session_id()?>&numRad=<?=$verrad?>&expediente="+ numeroExpediente +"&est="+ est +"&","Cambio Estado Expediente","height=100,width=100,scrollbars=yes");
}

function modFlujo(numeroExpediente,texp,codigoFldExp) {
window.open("<?=$ruta_raiz?>/flujo/modFlujoExp.php?<?=session_name()?>=<?=session_id()?>&codigoFldExp="+codigoFldExp+"&numRad=<?=$verrad?>&texp="+texp+"&ind_ProcAnex=<?=$ind_ProcAnex?>&codusua=<?=$codusua?>","TexpE<?=$fechaH?>","height=250,width=750,scrollbars=yes");
}

function verVinculoDocto(){
    window.open("./vinculacion/mod_vinculacion.php?verrad=<?=$verrad?>&codusuario=<?=$codusuario?>&dependencia=<?=$dependencia?>","Vinculacion_Documento","height=500,width=750,scrollbars=yes");
}
function update_cExp(){
	$.post("<?=$ruta_raiz?>/include/tx/comiteExpertos.php",{numRad: <?=$numrad?>});
}

function cambiarFechaVencimiento(){
    window.open("<?=$ruta_raiz?>/tx/cambiarFechaVencimiento.php?<?=session_name()?>=<?=session_id()?>&radi_fech_vcmto=<?=$radi_fech_vcmto?>&numRad=<?=$verrad?>&codusua=<?=$codusua?>","TexpE<?=$fechaH?>","height=250,width=750,scrollbars=yes");
}

$(document).ready(function() {
  $('.abrirVisor').click(function(){
    $("#visor").dialog();
  });

  $('.cerrarVisor').click(function(){
    $("#visor").dialog('destroy');
  });
});

</script>
<body>

<table class="table bordered">
<colgroup>
  <col style="width:15.5%"/>
  <col style="width:23.1%"/>
  <col style="width:15.5%"/>
  <col style="width:15.7%"/>
  <col style="width:14.5%"/>
  <col style="width:15.7%"/>
</colgroup>
<tr>
<td class="tdprincipal"><small><b>Asunto</b></small></td><td><small><?=$ra_asun ?></small></td>
<td class="tdprincipal"><small><b>Fecha </b></small></td><td><small><?=$radi_fech_radi ?>&nbsp;&nbsp;</small></td>
<td class="tdprincipal"><small><b>Fecha Vencimiento</b></small></td><td><small> <?=$radi_fech_vcmto ?> &nbsp;&nbsp;</small>
 <?php
   if(isset($_SESSION["fecha_vencimiento"]) && $_SESSION["fecha_vencimiento"] ==2){
 ?>
<input title="Cambiar fecha vencimiento" type=button name=CambiarFechaV value='...'  class='btn btn-primary btn-xs btn-rd'  onClick='cambiarFechaVencimiento();'>
 <?php } ?>
</td>
</tr>
<tr  cellspace=0 cellpad=0>
<td class="tdprincipal"><small><b>  Folios</b></small></td><td><small><?=$radi_nume_folio?>/<?=$radi_nume_hoja ?> </small></td><td class="tdprincipal"><small><b>Anexos</b></small></td><td><small> <?=$radi_nume_anexo?></small></td>
<?php if ($_SESSION["USUA_PERM_RESPUESTA_CONJUNTA"]) { ?>
<td class="tdprincipal"><small><b>Colaboradores</b></small></td>
<td>
    <input title="colaboradores" type=button data-toggle="modal" data-target="#modal1" name=colaboradores value='...'  class="btn btn-primary btn-xs btn-rd">
    <span id="total-colaboradores"></span>
</td>
<?php } ?>
<tr>
<td class="tdprincipal"><small><b> Descripci&oacute;n Anexos</b></small></td><td><small><?=$radi_desc_anex ?></small></td>
<td class="tdprincipal"><small><b> Anexo/Asociado</b></small></td><td><small>
	<?PHP
	if($radi_tipo_deri!=1 and $radi_nume_deri)
	   {	echo $radi_nume_deri;
           	 /*
		  * Modificacion acceso a documentos
		  * @author Liliana Gomez Velasquez
		  * @since 10 noviembre 2009
		 */
		 $resulVali = $verLinkArchivo->valPermisoRadi($radi_nume_deri);
     $verImg = $resulVali['verImg'];
		 if ($verImg == "SI"){
		        echo "<br>(<a class='vinculos' href='$ruta_raiz/verradicado.php?verrad=$radi_nume_deri &session_name()=session_id()' target='VERRAD$radi_nume_deri_".date("Ymdhi")."'>Ver Datos</a>)";}
                 else {
                      echo "<br>(<a class='vinculos' href='javascript:noPermiso()'> Ver Datos</a>)";
                 }
	   }
	 if(($verradPermisos == "Full" and $coddepe!='999') or $datoVer=="985")
		{
	?>
		<input title="Mostrar anexo" type=button name=mostrar_anexo value='...'  class="btn btn-primary btn-xs btn-rd" onClick="verVinculoDocto();">
	<?
		}
	?>
</small></td><td class="tdprincipal"><small><b>Referencia / Oficio</b></small></td><td><small><?=$cuentai ?></small></td>
</tr>

<?
$muniCodiFac = "";
$dptoCodiFac = "";

if($sector_grb==6 and $cuentai and $espcodi) {
    if($muni_us2 and $codep_us2){
        $muniCodiFac = $muni_us2;
        $dptoCodiFac = $codep_us2;
    } else {
        if($muni_us1 and $codep_us1){
            $muniCodiFac = $muni_us1;
            $dptoCodiFac = $codep_us1;
        }
    }
    echo "<a href='./consultaSUI/facturacionSUI.php?cuentai=<?=$cuentai?>&
        muniCodi=<?=$muniCodiFac?>&deptoCodi=<?=$dptoCodiFac?>&
        espCodi=<?=$espcodi?>'
        target='FacSUI<?=$cuentai?>'>
        <span class='vinculos'>Ver Facturacion</span></a>";
}

//Reescribir $imagenv para que el pdf abra en el visor modal
if(!empty($radi_path)){
  $extension = explode('.',$radi_path);
  if ($extension[1] == 'pdf') {
    if(strpos($radi_path,"/") != 0){
      $radi_path = "/".$radi_path;
    }
    $linkImagen = "$ruta_raiz/bodega".$radi_path;
    $imagenv = "<a 'vinculos' href='javascript:void(0)' class='abrirVisor'>Ver Imagen</a>";
  }
}
?>

<tr><td class="tdprincipal"><small><b>Imagen</b></small></td><td><small>	<span class='vinculos'><?=$imagenv ?></span> </small></td>

<td class="tdprincipal"><small><b>Flujos</b></small></td>
<td>
    <small>
      <span ><?=$descFldExp?></span>&nbsp;&nbsp;&nbsp;
    <?
    if($verradPermisos == "Full" or $datoVer=="985")
    {
    ?>
      <input title="mostrat causal" type=button name=mostrar_causal value='...' class="btn btn-primary btn-xs btn-rd" onClick="modFlujo('<?=$numExpediente?>',<?=$texp?>,<?=$codigoFldExp?>)">
    <?
    }
    ?>
    </small>
</td>

<td class="tdprincipal"><small><b>Nivel de Seguridad</b></small></td>
<td>
	<small>
	<?php
		if($nivelRad==0)
		{	echo "P&uacute;blico";	}
		elseif ($nivelRad == 1)
		{	echo "Reservado: Solo la dependencia";	}
		elseif ($nivelRad == 2)
		{	echo "Clasificado: Usuario que proyectó, Jéfe y usuario actual del radicado.";	}
		if(($verradPermisos == "Full" and $coddepe!='999')){
	    $varEnvio = "&numRad=$verrad&nivelRad=$nivelRad";
  ?>
		<input title="mostrar seguridad radicado" type=button name=mostrar_causal value="..." class="btn btn-primary btn-xs btn-rd" onClick="window.open('<?=$ruta_raiz?>/seguridad/radicado.php?<?=$varEnvio?>','Cambio Nivel de Seguridad Radicado', 'height=270, width=600,left=350,top=300')" >
	<?php
		}
	?>
  </small>
</td>
</tr>
<tr>
  <td class="tdprincipal"><small><b>Clasificaci&oacute;n Documental</b></small></td>
  <td>
    <div class="centrar">
      <table class="table2">
      <small>
      <td>
      <?php
        if(!$codserie) $codserie = "0";
        if(!$tsub) $tsub = "0";
        if(trim($val_tpdoc_grbTRD)=="///") $val_tpdoc_grbTRD = "";
      ?>
      <?=$serie_nombre ?><font color=black><br></font><?=$subserie_nombre ?><font color=black><br></font><?=$tpdoc_nombreTRD ?> 
  
      </td>
      <?php
        if(($verradPermisos == "Full" and $coddepe!='999') or $datoVer=="985" or $dependencia == '999' or $_SESSION['usua_assign_trd']) {
      ?>
      <td>
          <input type=button name=mosrtar_tipo_doc2 title="Asigne una TRD a su documento." value='...' class="btn btn-primary btn-xs btn-rd" onClick="ver_tipodocuTRD(<?=$codserie?>,<?=$tsub?>);">
      </td>
      <?php
        } else {
      ?>
      <td></td>
      <? } ?>
      </small>
      </table>
    </div>
  </td>
  <?php
    $termino=$db->conn->Execute("select SGD_TPR_TERMINO from sgd_tpr_tpdcumento tp, radicado r where tp.SGD_TPR_CODIGO=r.TDOC_CODI and r.radi_nume_radi=$verrad");
    $termino=$termino->fields["SGD_TPR_TERMINO"]
  ?>
  <td class="tdprincipal"><small><b>Término </b></small></td><td><small><?=$termino?></small></td>
  <?php
    if ($verradPermisos == "Full"  or $datoVer=="985" ) { 
  ?>
      <!--<input type=button name="mostrar_causal" value="..." class="btn btn-primary btn-xs" onClick="window.open(<?=$datosEnviar?>,'Tipificacion_Documento','height=300,width=750,scrollbars=no')">-->
  <?
    }
  ?>
  </td>

  <? if (!empty($esNotificacion)) {  ?>
      <td class="tdprincipal"></td>
      <td><small></small></td>
  <? } else {?>
      <td class="tdprincipal"><small><b>Medio envio </b></small></td>
      <td><small>
         <?php
            if(!empty($medio_recepcion) && strlen($medio_recepcion)>0)
              echo $medio_recepcion;
         ?>
      </small></td>
  <? } ?>

</tr>
<?php 
if (!empty($esNotificacion)) { 
  $opacidad_citacion = 0.4;
  $opacidad_notificacion = 0.4;
  $opacidad_comunicacion = 0.4;
  $opacidad_publicacion = 0.4;
  if ($esNotificacionCircular) {
    $opacidad_publicacion = 1;
  } else {
    foreach ($ordenesNotificacion as $dir_codigo => $orden_codigo) {
      foreach ($orden_codigo as $orden) {
        switch ($orden) {
          case "1":
            $opacidad_citacion = 1;
            break;
          case "2":
            $opacidad_notificacion = 1;
            break;
          case "3":
            $opacidad_comunicacion = 1;
            break;
          case "4":
            $opacidad_publicacion = 1;
            break;
        }
      }
      if ($opacidad_citacion == 1 && $opacidad_notificacion == 1 &&
        $opacidad_comunicacion == 1 && $opacidad_publicacion == 1) {
        break;
      }
    }
  }
?>
<tr>
  <td class="tdprincipal"><small><b>Medio de Publicaci&oacute;n</b></small></td><td><small><?= $medio_pub_desc ?></small></td>
  
  <?  if ($esNotificacionCircular) { ?>
    <td class="tdprincipal" rowspan="4"><small><b></b></small></td>
    <td style="opacity:<?= $opacidad_citacion ?>"><small><b></b></small></td>
  <? } else { ?>
    <td class="tdprincipal" rowspan="4"><small><b>Orden Acto Administrativo</b></small></td>
    <td style="opacity:<?= $opacidad_citacion ?>"><small><b>Cita</b></small></td>
  <? } ?>  
  <td></td>
  <td></td>
</tr>
<tr>
  <td class="tdprincipal"><small><b>Caracter Administrativo</b></small></td><td><small><?= $caracter_adtvo_desc  ?></small></td>
  <!--td class="tdprincipal"></td-->
  <?  if ($esNotificacionCircular) { ?>
      <td style="opacity:<?= $opacidad_notificacion ?>"><small><b></b></small></td>
  <? } else { ?>
      <td style="opacity:<?= $opacidad_notificacion ?>"><small><b>Notifica</b></small></td>
  <? } ?>    
  <td></td>
  <td></td>
</tr>
<tr>
  <td class="tdprincipal"><small><b>SIAD</b></small></td><td><small><?= $siad_preestablecido ?></small></td>
  <!--td class="tdprincipal"></td-->
  <?  if ($esNotificacionCircular) { ?>
      <td style="opacity:<?= $opacidad_comunicacion ?>"><small><b></b></small></td>
  <? } else { ?>
      <td style="opacity:<?= $opacidad_comunicacion ?>"><small><b>Comunica</b></small></td>
  <? } ?>   
  <td></td>
  <td></td>
</tr>
<tr>
  <td class="tdprincipal"><small><b>Prioridad </b></small></td><td><small><?=$prioridad_prestablecido?></small></td>
  <!--td class="tdprincipal"></td-->
  <?  if ($esNotificacionCircular) { ?>
      <td style="opacity:<?= $opacidad_publicacion ?>"><small><b></b></small></td>
  <? } else { ?>
      <td style="opacity:<?= $opacidad_publicacion ?>"><small><b>Publica</b></small></td>
  <? } ?>    
  <td></td>
  <td></td>
</tr>
<?php } ?>

<?php if($esNotificacion) { ?>
<tr>
  <td class="tdprincipal"><small><b>Funcionario designado para firma</b></small></td><td><small><?= 
    $usuaFirmador ?></small></td>
  <td class="tdprincipal"></td><td colspan="3"></td>
</tr>  

<?php } ?>
</table>

<table width="80%" class="table table-bordered ">
<tr>
  <th  class='alert-info pr2'>Nombre </th>
  <th  class='alert-info pr2'>Persona </th>
  <th  class='alert-info pr2'>Direccion</th>
  <th  class='alert-info pr2'>Ciudad / Departamento</th>
  <th  class='alert-info pr2'>E-mail</th>
  <th  class='alert-info pr2'>Telefono</th>
  <?php if ($ent == RESOLUCION || $ent == AUTO) { ?>
    <th  class='alert-info pr2'>Cita </th>
    <th  class='alert-info pr2'>Notifica</th>
    <th  class='alert-info pr2'>Comunica</th>
    <th  class='alert-info pr2'>Publica</th>
    <th  class='alert-info pr2'>Medio de Env&iacute;o</th>
  <?php } ?>
</tr>

<?php

$isql = "select a.* from sgd_dir_drecciones a where a.RADI_NUME_RADI=$verrad";

//$db->conn->debug = true;

$rs = $db->query($isql);

//$db->conn->debug = false;

include_once "$ruta_raiz/jh_class/funciones_sgd.php";



while(!$rs->EOF){

   $nombres     = $rs->fields["SGD_DIR_NOMREMDES"];
   $nombre      = $rs->fields["SGD_DIR_NOMBRE"];
   $dirD        = $rs->fields["SGD_DIR_DIRECCION"];
   $dirMail     = $rs->fields["SGD_DIR_MAIL"];
   $dirTelefono = $rs->fields["SGD_DIR_TELEFONO"];
   $dptoCodigo  = $rs->fields["DPTO_CODI"];
   $muniCodigo  = $rs->fields["MUNI_CODI"];
   $idPais      = $rs->fields["ID_PAIS"];



  $a = new LOCALIZACION($idPais."-".$dptoCodigo,$muniCodigo,$db);

  $dpto_nombre  = $a->departamento;

  $muni_nombre    = $a->municipio;

  // Logica de Notificaciones
  if ($ent == RESOLUCION || $ent == AUTO) {
    include_once("$ruta_raiz/include/tx/notificacion.php");
    $notificacion = new Notificacion($db);

    $dir_codigo = $rs->fields["SGD_DIR_CODIGO"];
    $medio_envio_codi = $rs->fields["MEDIO_ENVIO"];
    $medio_envio_desc = $notificacion->obtenerMedioEnvio($medio_envio_codi);

    $disable_citacion     = "disabled";
    $disable_notificacion = "disabled";
    $disable_comunicacion = "disabled";
    $disable_publicacion  = "disabled";
    foreach ($ordenesNotificacion[$dir_codigo] as $orden_codigo) {
      switch ($orden_codigo) {
        case "1":
          $disable_citacion = "";
          break;
        case "2":
          $disable_notificacion = "";
          break;
        case "3":
          $disable_comunicacion = "";
          break;
        case "4":
          $disable_publicacion = "";
          break;
      }
    }
  }

?>

      <tr>

        <td><?=$nombre  ?></td>

        <td><?=$nombres ?></small></td>

        <td><?=$dirD ?></td>

        <TD><?=$muni_nombre?>/<?=$dpto_nombre?></TD>

        <td><?=$dirMail ?></small></td>

        <td><?=$dirTelefono?></td>

        <?php if ($ent == RESOLUCION || $ent == AUTO) { ?>
          <td align="center"><input type="checkbox" <?= $disable_citacion ?>></td>
          <td align="center"><input type="checkbox" <?= $disable_notificacion ?>></td>
          <td align="center"><input type="checkbox" <?= $disable_comunicacion ?>></td>
          <td align="center"><input type="checkbox" <?= $disable_publicacion ?>></td>
          <td style="text-align:center;"><?= $medio_envio_desc ?></td>
        <?php } ?>

      </tr>

<?php

  $rs->MoveNext();

}

?>
      <tr>
        <td> <?=$nombret_us3 ?> -- <?=$cc_documento_us3?></small></td>
        <td> <?=$direccion_us3 ?></small></td>
        <td> <?=$dpto_nombre_us3."/".$muni_nombre_us3 ?></small></td>
        <td><?=$email["x3"] ?> </small></td>
        <td><?=$telefono["x3"] ?> </small></td>
        <td></td>
        <?php if ($ent == RESOLUCION || $ent == AUTO) { ?>
          <td></small></td>
          <td></small></td>
          <td></small></td>
          <td></small></td>
          <td></small></td>
        <?php } ?>
      </tr>
      </table>

<table width="150" class="table table-bordered ">
<?php
 if(trim($radi_dato_001)){
?>
	<tr>
	 <th  class='alert-info'>Apoderado </th><td><?=$radi_dato_001?></td>
	</tr>
	<?php
	}
  if(trim($radi_dato_002)){
	?>
	<tr>
	 <th  class='alert-info'>Demandante</th><td><?=$radi_dato_002?></td>
	</tr>
	<?php
	}
	?>
</table>

<div id='visor' style='display:none; 
  position:fixed;
  padding:26px 30px 30px;
  top:0;
  left:0;
  right:0;
  bottom:0;
  z-index:2'>
  <button class='cerrarVisor' type='button' style='float:right; background-color:red;'><b>x</b></button> 
  <iframe style='width:100%; height:100%; z-index:-2;' src=<?= $linkImagen ?>></iframe>
</div>

<?php
$sql ="select depe_codi||' - '||depe_nomb as depe_nomb, depe_codi
    from dependencia where depe_estado=1 order by 1";
$dep = $db->conn->Execute($sql);

$sql ="select c.depe_codi, c.usua_codi, c.obse, u.usua_nomb, d.depe_nomb from colaboradores c
join usuario u on u.depe_codi = c.depe_codi and u.usua_codi = c.usua_codi
join dependencia d on d.depe_codi = c.depe_codi
where c.radi_nume_radi = $numrad";
$rs = $db->conn->Execute($sql);
$col = $rs->GetArray();
$usr = $col ? json_encode($col) : '[]';
?>
<div class="modal fade" id="modal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Colaboradores</h4>
      </div>
      <div class="modal-body">

<div class="form-group">
       <label class='select select-multiple'>Dependencia:</label>
        <?=$dep->GetMenu2("coddepeinf", '$coddepeinf', false, false,5," class='custom-scroll' id='coddepeinf' ")?>
       <label class='select select-multiple'>Usuario:</label>
        <select name="usuariosInformar" id="usuariosInformar" size="5" class="custom-scroll" align="LEFT" >
        </select>
</div>
<div class="form-group">
            <textarea name="observa" id="observa" placeholder="Escriba un Comentario" rows="3" style="width:100%"></textarea>
</div>
        <button id="agregar" type="button" class="btn btn-primary">Agregar</button>
        <div id="msgBorrar"></div>

        <label>Colaboradores seleccionados:</label>
        <div id="usuariosInformados"> No Existen Usuarios colaborando</div>
        <div id="alert" role="alert" hidden></div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button id="guardar" type="button" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>
function b_del(u) {
    return `<a onclick='del(${JSON.stringify(u)})'><span style="color:RED;">Borrar</span></a>`;
}
function tabla() {
    $.post('tx/ajaxColaboradores.php', {accion:'list',usr:usr,rad:<?=$verrad?>}, function(r) {
        var table = $('<table class="table table-striped table-bordered" />');
        for (let u of r) {
            table.append(`
                <tr>
                    <td>${u.DEPE_NOMB}</td>
                    <td>${u.USUA_NOMB}${b_del(u)}</td>
                </tr>
            `);
        }
        $('#usuariosInformados').html(table);
    });
}

function add(u) {
    t = usr.filter(e => JSON.stringify(e) === JSON.stringify(u)).length;
    if (t == 0) {
        usr.push(u);
    }
    tabla();
}

function del(u) {
    const index = usr.findIndex(e => e.DEPE_CODI == u.DEPE_CODI && e.USUA_CODI == u.USUA_CODI);
    if (index > -1) {
        usr.splice(index, 1);
    }
    tabla();
}

function getUsuarios(varAccion, dependencia, var2){
    $.post( "<?=$ruta_raiz?>/include/tx/json/getInfoUsuariosDep.php", { id: dependencia, accion: "usuarios" })
    .done(function( data ) {
        var obj = JSON.parse(data);
        var myObj = JSON.parse(data);
        var txt="";
        var xSel=0;
        document.getElementById("usuariosInformar").length = 0;
        for (x in myObj) {
            document.getElementById("usuariosInformar").options[x] = new Option(myObj[x].USUA_NOMB, myObj[x].USUA_CODI);
            if(myObj[x].USUA_CODI==1) xSel=x;
            }
            document.getElementById("usuariosInformar").options[xSel].selected = true;
        });
}

function total() {
    $('#total-colaboradores').text(usr.length);
}

var usr = <?=$usr?>;

(function() {
    $("#guardar").on("click", function(){
        $.post('tx/ajaxColaboradores.php', {accion:'save',usr:usr,rad:<?=$numrad?>}, function(r) {
            if (r.err) {
                $("#alert").text('Ha ocurrido un error');
                $("#alert").attr("class", "alert alert-danger");
            } else {
                $("#alert").text('Se ha guardado');
                $("#alert").attr("class", "alert alert-success");
            }
            $("#alert").show().delay(5000).fadeOut();
            total();
        });
    });
    $("#coddepeinf").on("click", function(){
        getUsuarios('usuariosInformar',document.getElementById('coddepeinf').value, 0);
    });
    $("#agregar").on("click", function(){
        var u = {}
        u['DEPE_CODI'] = $('#coddepeinf').val();
        u['USUA_CODI'] = $('#usuariosInformar').val();
        u['OBSE'] = $('#observa').val();
        u['DEPE_NOMB'] = $("#coddepeinf option:selected").text().split(' - ')[1];
        u['USUA_NOMB'] = ($("#usuariosInformar option:selected").text());
        add(u);
    });
    tabla();
    total();
})();
</script>

<style>
.modal-body select  {
    width: 100%;
}
</style>

