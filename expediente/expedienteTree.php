<!-- NEW WIDGET START -->
<article class="col-sm-12 col-md-12" align=left>
<!--widget content -->
<div class="widget-body" >
<div class="tree smart-form fa-folder-open">
	<ul>
	<li>
	<span class="alert-success"  ><i class="fa fa-folder-open" ></i> <?=$numExpediente?></span>
	<ul>
	<?php
	
 error_reporting(1);
 
  $datosExp=$arrExpedientes[$numExpediente];
	foreach($datosExp as $key2 => $value){
	?>
	<li  >
		<span >
		<?
			//if(isset($value['SEXPEDIENTES'])) {
			//var_dump($value);
			$sExp = "";
			$sExp = $value['SEXPEDIENTES'];
			
			if($sExp!=0){ 
			$SExps = "<table><TR><TD COLSPAN=5></small><b>Expedientes Adicionales :</b></small><TD></TR>";
      $iSubExp=0;
			foreach($sExp as $valueExpedientes){
				//var_dump($valueExpedientes);
				if($valueExpedientes["NUMERO"]!=$numeroExpediente){
				 $SExps .= "<tr><td><small> ***".$valueExpedientes["NUMERO"]."&nbsp; </small> </td><td> <small>".$valueExpedientes["PARAM1"]."&nbsp; </small></td><td><small>".$valueExpedientes["PARAM2"]."</small></td></tr>";
				}
         if($iSubExp>=10){
           $SExps .= "<tr><td><small> . . . </small> </td><td> <small>. . .</small></td><td><small></small></td></tr>"; 
           break;
         }
        $iSubExp++;
			}
			$SExps .= "</table>";
			}
			
		$numRadicado = $value["NUM_RADICADO"];
		$pathRadicado = $value["PATH_RADICADO"];
		if (!$pathRadicado) $pathRadicado= "null";
		$fechaRad = "<a href='verradicado.php?verrad=$numRadicado&nomcarpeta=".$nomcarpeta."#tabs-a' title='Ver Datos del Radicado $numRadicado'>".$value["FECHA_RADICADO"] . "</a>";
		if(isset($value['SEXPEDIENTES'])){
		echo "<i class='fa fa-folder-open'></i><TABLE  WIDTH='1050'><TR><TD WIDTH=30> </TD>
		<TD width=0 align=left>";
		echo /* $numRadicado .*/ "</td><TD width=160 align=left>";
		/*$resulVali = $verLinkArchivo->valPermisoRadi($numRadicado);
		$valImg = $resulVali['verImg'];*/
		$extRad = array_pop(explode(".",$pathRadicado));		
	 if(trim($pathRadicado)){
	    if ($pathRadicado=="null"){
	      echo "<b> $numRadicado </b>";
	    }else{
	      echo "<b><a href='javascript:void(0)' onclick=\"funlinkArchivo('$numRadicado','.');\"><img src='./img/icono_$extRad.jpg' title='$extRad' width='25'> $numRadicado </a></b>";
	    }  
	 
	 }
	 echo "</TD>";
		echo "<TD width=100>$fechaRad </TD><TD width=120>".$value["TIPO_DRADICADO"]."</TD><TD width=450>".$value["ASUNTO_RADICADO"]."</TD>
		<TD width=350>$SExps</TD></TR>
		</TABLE>";  } ?>
		</span>
	<ul>
	<?
	
	$carpetaDep = intval(substr($value["NUM_RADICADO"],4,$digitosDependencia));
	$rutaAnexos = "".substr($value["NUM_RADICADO"],0,4). "/$carpetaDep/docs/";
	$numeroRadicadoAnexo = $value["NUM_RADICADO"];
	$anexos = $value["ANEXOS"];
	
	if(!empty($anexos)){
		foreach($anexos as $valueAnexos){

		$anexoPath = $valueAnexos["ANEX_PATH"];
		if(strtoupper(trim($valueAnexos["ANEX_BORRADO"]))!="S"){
        
      $valImg = "SI";
      $extAnexo = array_pop(explode(".",$anexoPath));
      
      $radiNumeSalida     = $valueAnexos["RADI_SALIDA"];
      $anexCodigo         = $valueAnexos["ANEX_CODIGO"];
      $pathRadiNumeSalida = $valueAnexos["RADI_PATH_SALIDA"];
      ?>
      <li style="display:none">
      <span>
      <i class="fa fa-clock-o"></i>
        <?=$valueAnexos["ANEX_NUMERO"]?> - <?=$valueAnexos["ANEX_FECH"]?> - <?=$valueAnexos["RADI_SALIDA"]?>
      <?php
        if($radiNumeSalida){
        //     $resulValiRs   = $verLinkArchivo->valPermisoRadi($radiNumeSalida);
         }else{
          //   $resulValiRs = $verLinkArchivo->valPermisoAnex($anexCodigo);
             $pathRadiNumeSalida = $valueAnexos["RADI_PATH_SALIDA"];
         }

          $extRadSalida = strtolower(array_pop(explode(".",$pathRadiNumeSalida)));
        if($pathRadiNumeSalida and $extRadSalida!='docx'  and $extRadSalida!='doc'  and $extRadSalida!='odt'){	
        if(($verradPermisos == "Full")  ){
          echo "<b><a class=\"vinculos\" href=\"#2\" onclick=\"funlinkArchivo('$radiNumeSalida','.');\"><img src='./img/icono_$extRadSalida.jpg' title='Imagen $extRadSalida' width='25'> </a></b>";
          }
        }else{
          // $valImg = "NO";
        }
        ?>
        <?
        if($valueAnexos["ANEX_PATH"]) {
          echo "<b><a class=\"vinculos\" href=\"#2\" onclick=\"funlinkArchivo('$anexCodigo','.');\"><img src='./img/icono_$extAnexo.jpg' title='$extAnexo' width='25'> </a></b>";
        
      echo "	- ". $valueAnexos["DESCRIPCION"];
    } ?>
    </span>

  <?
    }
	}
			?>
		</li>	
		<?
		}
		?>
		</ul>
	</li>
	<?
	
	}
?>
		</ul>
	</div>
</div>
		<!-- end widget content -->
</article>
	<!-- WIDGET END -->
	
<script type="text/javascript">
    // DO NOT REMOVE : GLOBAL FUNCTIONS!
    pageSetUp();

    // PAGE RELATED SCRIPTS

    $('.tree > ul').attr('role', 'tree').find('ul').attr('role', 'group');
    $('.tree').find('li:has(ul)').addClass('parent_li').attr('role', 'treeitem').find(' > span').attr('title', 'Collapse this branch').on('click', function (e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(':visible')) {
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find(' > i').removeClass().addClass('fa fa-lg fa-plus-circle');
        } else {
            children.show('fast');
            $(this).attr('title', 'Collapse this branch').find(' > i').removeClass().addClass('fa fa-lg fa-minus-circle');
        }
        e.stopPropagation();
    });

</script>
