<?php
 if(!$ruta_raiz) $ruta_raiz = ".";
 if(!$db) include "$ruta_raiz/conn.php";
	   require_once("$ruta_raiz/class_control/Transaccion.php");
		 require_once("$ruta_raiz/class_control/Dependencia.php");
		 require_once("$ruta_raiz/class_control/usuario.php");

	   $trans = new Transaccion($db);
	   $objDep = new Dependencia($db);
	   $objUs = new Usuario($db);
	   $isql = "select USUA_NOMB from usuario where depe_codi=$radi_depe_actu and usua_codi=$radi_usua_actu";
	   $rs = $db->query($isql);
	   $usuario_actual = $rs->fields["USUA_NOMB"];
	   $isql = "select DEPE_NOMB from dependencia where depe_codi=$radi_depe_actu";
	   $rs = $db->query($isql);
	   $dependencia_actual = $rs->fields["DEPE_NOMB"];
	   $isql = "select USUA_NOMB from usuario where depe_codi=$radi_depe_radicacion and usua_codi=$radi_usua_radi";

	   $rs = $db->query($isql);
	   $usuario_rad = $rs->fields["USUA_NOMB"];
	   $isql = "select DEPE_NOMB from dependencia where depe_codi=$radi_depe_radicacion";
	   $rs = $db->query($isql);
	   $dependencia_rad = $rs->fields["DEPE_NOMB"];

?>
<table  width="80%"  align="center"  class="table table-bordered ">
  <tr   align="left" >
    <th class="tdprincipal" width=10% ><small>Usuario Actual</small></th>
    <td  width=15% align="left"><small><?=$usuario_actual?></small></td>
    <th class="tdprincipal"  width=10%><small>Dependencia Actual</small></th>
    <td  width=15%><small><?=$dependencia_actual?></small></td>
  </tr>
</table>
<style type="text/css">
    
.tablescroll {
    max-height: 300px;
    overflow-y: scroll;
}
</style>
<div class="tablescroll">
<table  width="100%" align="center" class="table table-striped table-hover table-bordered" s >
  <thead>
    <tr class="pr2" align="center">
      <th>DEPENDENCIA</th>
      <th>FECHA</th>
      <th>TRANSACCIÓN</th>
      <th>US. ORIGEN</th>
      <th>COMENTARIO</th>
      <th>US. DESTINO</th>
    </tr>
  </thead>
  <?
  $sqlFecha = $db->conn->SQLDate("d-m-Y H:i A","a.HIST_FECH");
	$isql = "select $sqlFecha AS HIST_FECH1
      , a.DEPE_CODI
			, a.USUA_CODI
			,a.RADI_NUME_RADI
			,a.HIST_OBSE
			,a.USUA_CODI_DEST
			,a.USUA_DOC
			,a.HIST_OBSE
			,a.SGD_TTR_CODIGO
			,a.HIST_DOC_DEST
			from hist_eventos a
		 where
			a.radi_nume_radi =$verrad
			order by hist_fech desc ";

	$i=1;
	//$db->conn->debug = true;
	$rs = $db->query($isql);
	if($rs) {
        while(!$rs->EOF) {
            $trans = new Transaccion($db);
            $objDep = new Dependencia($db);
            $objUs = new Usuario($db);
            $usua_doc_dest ="";
            $usua_doc_hist = "";
            $usua_nomb_historico = "";
            $usua_destino = "";
            $numdata =  trim($rs->fields["CARP_CODI"]);
            if($data =="") $rs1->fields["USUA_NOMB"];
            $data = "NULL";
            $numerot = $rs->fields["NUM"];
            $usua_doc_hist = $rs->fields["USUA_DOC"];
            $usua_codi_dest = $rs->fields["USUA_CODI_DEST"];
            $usua_dest=intval(substr($usua_codi_dest,3,3));
            $depe_dest=intval(substr($usua_codi_dest,0,3));
            $usua_codi = $rs->fields["USUA_CODI"];
            $depe_codi = $rs->fields["DEPE_CODI"];
            $codTransac = $rs->fields["SGD_TTR_CODIGO"];
            $descTransaccion = $rs->fields["SGD_TTR_DESCRIP"];
            $histDoctDest=$rs->fields["HIST_DOC_DEST"];
            $iconoLink = "";
            if($codTransac==67) $iconoLink = "<a href='#' Title='Ver Historico de Prestamos del radicado {$verrad}' onClick='verHistPrestamo($verrad);'><img src='img/icono_prestamo.png' width='23'></a>";
            if(!$codTransac) $codTransac = "0";
            $trans->Transaccion_codigo($codTransac);
            $objUs->usuarioDocto($usua_doc_hist);
            $objDep->Dependencia_codigo($depe_codi);
            $destinatarios = '';

            //Start::Validar multiples destinatarios
            $arr = str_split($verrad); // convert string to an array
            if( $codTransac == 32 && end($arr) ) {
                $multiSql = "
                SELECT
                    string_agg(DISTINCT CONCAT(SGD_DIR_DRECCIONES.sgd_dir_nombre,'(',SGD_DIR_DRECCIONES.sgd_dir_direccion,')'), ',') as destinatarios
                FROM
                    SGD_DIR_DRECCIONES
                WHERE
                radi_nume_radi = '$verrad' ";
                $rsMemorandoMultiple = $db->conn->query($iSqlMemorandoMultiple);
                $rsMultiple = $db->conn->Execute("$multiSql");

                if(!empty($rsMultiple->fields["DESTINATARIOS"]))
                    $destinatarios = 'Destinatarios:  '. $rsMultiple->fields["DESTINATARIOS"];
            }
            //End::Validar si el memorando tienen al usuarrio


            if($carpeta==$numdata){
                $imagen="usuarios.gif";
            } else {
                $imagen="usuarios.gif";
            }
            if($i!=10000){
                echo "<tr>";
                $i=1;
            }

            $date = substr($rs->fields["HIST_FECH1"], 0, -3);;
            $newDate = date("Y-m-d H:i", strtotime($date));

    ?>
    <td>
        <small>
            <?=$objDep->getDepe_nomb()?></td>
        </small>
    </td>
    <td>
        <?=$newDate?>
    </td>
    <td>
        <small>
          <?=$trans->getDescripcion()?> <?=$iconoLink?>
        </small>
    </td>
    <td>
        <small>
            <?=$objUs->get_usua_nomb()?>
        </small>
    </td>
    <td>
        <small>
           <?=$rs->fields["HIST_OBSE"]?>
           <?=$destinatarios?>
        </small>
    </td>

    <td class="listado2"><?php
            $isqln = "select USUA_NOMB from usuario where USUA_DOC='".trim($histDoctDest)."'";
            $uprs = $db->query($isqln);
            $usuario_actual = $uprs->fields["USUA_NOMB"];
            echo $usuario_actual;?>
    </td>

  </tr>
        <?  $rs->MoveNext();
        }
    }
  // Finaliza Historicos
	?>
</table>
</div>
  <?
  //empieza datos de envio
include "$ruta_raiz/include/query/queryver_historico.php";

$isql = "select $numero_salida from anexos a where a.anex_radi_nume=$verrad";
$rs = $db->query($isql);
$radicado_d= "";
while(!$rs->EOF)
	{
		$valor = $rs->fields["RADI_NUME_SALIDA"];
		if(trim($valor))
		   {
		      $radicado_d .= "'".trim($valor) ."', ";
		   }
		$rs->MoveNext();
	}

$radicado_d .= "$verrad";
error_reporting(7);
//$db->conn->debug=true;
include "$ruta_raiz/include/query/queryver_historico.php";
$sqlFechaEnvio = $db->conn->SQLDate("d-m-Y H:i A","a.SGD_RENV_FECH");
$isql = "select $sqlFechaEnvio AS SGD_RENV_FECH,
		a.DEPE_CODI,
		a.USUA_DOC,
		a.RADI_NUME_SAL,
		a.SGD_RENV_NOMBRE,
		a.SGD_RENV_DIR,
		a.SGD_RENV_MPIO,
		a.SGD_RENV_DEPTO,
		a.SGD_RENV_PLANILLA,
		b.DEPE_NOMB,
		c.SGD_FENV_DESCRIP,
		$numero_sal,
		a.SGD_RENV_OBSERVA,
		a.SGD_DEVE_CODIGO,
		u.USUA_LOGIN

        from sgd_renv_regenvio a left join dependencia b on (
        a.depe_codi=b.depe_codi)
        left join usuario u on (a.usua_doc=cast (u.usua_doc as numeric))
        , sgd_fenv_frmenvio c
		where
		a.radi_nume_sal in($radicado_d)
		AND a.sgd_fenv_codigo = c.sgd_fenv_codigo
		order by a.SGD_RENV_FECH desc ";
$rs = $db->query($isql);

?>
 <table width="100%" align="center"   >
  <tr>
    <td height="25" class="titulos4 tdtop" align="center"><b>DATOS DE ENVIO</b></td>
  </tr>
</table>
<table width="80%"  align="center"  class="table table-bordered"  >
  <tr  class="pr2" align="center" >
    <td width=10% ><small><b>RADICADO </b></small></td>
    <td width=10% ><small><b>DEPENDENCIA</b></small></td>
    <td width=15% ><small><b>FECHA </b></small></td>
    <td width=15% ><small><b>DESTINATARIO</b></small></td>
    <td width=15% ><small><b>DIRECCION </b></small></td>
    <td width=15% ><small><b>DEPARTAMENTO </b></small></td>
    <td width=15% ><small><b>MUNICIPIO</b></small></td>
    <td width=15% ><small><b>TIPO DE ENVIO</b></small></td>
    <td width=5%  ><small><b> No. PLANILLA</b></small></td>
    <td width=15% ><small><b>OBSERVACIONES</b></small></td>
 <td  width=15%   ><small><b>Realizo Envio</b></small></td>
  </tr>
  <?
$i=1;

$contadorImagenes = 0;

while(!$rs->EOF)
	{
	$radDev = $rs->fields["SGD_DEVE_CODIGO"];
	$radEnviado = $rs->fields["RADI_NUME_SAL"];
	if($radDev)
	{
		$imgRadDev = "<img src='$ruta_raiz/imagenes/devueltos.gif' alt='Documento Devuelto por empresa de Mensajeria' title='Documento Devuelto por empresa de Mensajeria'>";
	}else
	{
		$imgRadDev = "";
	}
	$numdata =  trim($rs->fields["CARP_CODI"]);
	if($data =="")
		$data = "NULL";
	//$numerot = $rs->RecordCount();
	if($carpeta==$numdata)
		{
		$imagen="usuarios.gif";
		}
	else
		{
		$imagen="usuarios.gif";
		}
	if($i==1)
		{
   ?>
  <tr > <?  $i=1;
			}
			 ?>
    <td  >
	<small><?=$imgRadDev?><?=$radEnviado?></small></td>
    <td  >
	<small><?=$rs->fields["DEPE_NOMB"]?></small></td>
    <td ><small>
	<?
		echo "<a class=vinculos href='./verradicado.php?verrad=$radEnviado&krd=$krd' target='verrad$radEnviado'><span class='timpar'>".$rs->fields["SGD_RENV_FECH"]."</span></a>";
	?></small> </td>
    <td ><small>
	<?=$rs->fields["SGD_RENV_NOMBRE"]
	?> </small></td>
  <td>
	       
        <?php
            if(substr($rs->fields["SGD_RENV_DIR"], 0, 18 ) === "<a target=\"_blank\"") { 
              $porciones = explode("\"", $rs->fields["SGD_RENV_DIR"]);
              $contadorImagenes++;
              echo "<small><b><a class='vinculos abrirVisor' href='javascript:void(0)' class='abrirVisor' 
              contador=$contadorImagenes link='" . $porciones[3] . "'>Certificación del envio de correo</a></small>";
              $visorId = "visor_".$contadorImagenes;
              echo "<div id=$visorId style='display:none; 
                      position:fixed;
                      padding:26px 30px 30px;
                      top:0;
                      left:0;
                      right:0;
                      bottom:0;
                      z-index:2'>
                      <button class='cerrarVisor' type='button' style='float:right; background-color:red;' contador=$contadorImagenes><b>x</b></button>  
                      <!--iframe></iframe-->
                    </div>";
            } else {
        ?>
            <small><?=$rs->fields["SGD_RENV_DIR"]?> </small>
         <?php
                  }
         ?>

  </td>
    <td   >
	 <small><?=$rs->fields["SGD_RENV_DEPTO"] ?> </small></td>
    <td   >
	 <small><?=$rs->fields["SGD_RENV_MPIO"] ?> </small></td>
    <td   >
	 <small><?=$rs->fields["SGD_FENV_DESCRIP"] ?> </small></td>
    <td   >
	 <small><?=$rs->fields["SGD_RENV_PLANILLA"] ?> </small></td>
    <td   >
	 <small><?=$rs->fields["SGD_RENV_OBSERVA"] ?> </small></td>
   <td   >
         <small><?=$rs->fields["USUA_LOGIN"] ?> </small></td>

  </tr>
  <?
	$rs->MoveNext();
  }

  // Finaliza Historicos
	?>
</table>
<script>

$(document).ready(function() {

    $('.abrirVisor').click(function(){
        var contador = $(this).attr('contador');
        var link = $(this).attr('link');
        var visorId = "#visor_" + contador;
        var visorRequest = new Request(link);

        //Valida primero que el archivo exista y se pueda abrir.
        fetch(visorRequest).then(function(response) {
          if(response.status == 200){
            $(visorId ).append("<iframe style='width:100%; height:100%; z-index:-2;' src=" + link + "></iframe>");
            $(visorId).dialog();
          } else {
            visorError(visorId);
          }
        });
    });

    $('.cerrarVisor').click(function(){
        var visorId = "#visor_" + $(this).attr('contador');
        $(visorId).dialog('destroy');
    });        

});


 function verHistPrestamo(radicado){
  window.open('prestamo/historico.php?datoRadicado=QWER2345SDB134123412C1234VFG5SERSH654E465G45G6235G63456&radicado='+radicado, 'Historico de Prestamo de '+radicado , 'width=650,height=500,addressbar=no,top=200,left=300');
 }
</script>
</body>
</html>
