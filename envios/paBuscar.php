
<table class="table table-bordered smart-form" >
	<tr></tr>
	<tr>
		<td width="100%" >
			<table align="center" cellspacing="0" cellpadding="0" width="100%" class="table">
				<tr class="tablas">
					<td class="etextomenu" >
						<span class="etextomenu">
						<form name=form_busq_rad action='<?=$pagina_actual?>?<?=session_name()."=".session_id()?>&estado_sal=<?=$estado_sal?>&tpAnulacion=<?=$tpAnulacion?>&estado_sal=<?=$estado_sal?>&estado_sal_max=<?=$estado_sal_max?>&pagina_sig=<?=$pagina_sig?>&dep_sel=<?=$dep_sel?>&nomcarpeta=<?=$nomcarpeta?>' method=POST>
							<small>Buscar radicado(s) (Separados por coma)</small>
                            <div class="row">
                                <div class="col-md-2">
                                    <select name="tipoEnvio">
                                        <option value="">busqueda normal</option>
                                        <option value="E-mail">buscar E-mail</option>
                                        <option value="Físico">buscar Físico</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="input">
                                        <i class="icon-append fa fa-search"></i>
                                        <?php
                                        	$rad = isset($nurad) && $nurad != '' ? $nurad : $busqRadicados;
                                        ?>
                                        <input name="busqRadicados" type="text" size="60" value="<?=$rad?>">
                                    </label>
                                    <input type="checkbox" name="busqueda_avanzada" value="1">Búsqueda Avanzada
                                </div>
                            </div>
							<?php if(isset($selectorUsuariosDependencia)): ?>
								<?php
									try {
										$usuarios = $db->conn->getAll("SELECT usua_nomb, usua_codi FROM usuario WHERE depe_codi = 8230");
										//para hacerlo compatible con la busqueda en asociarImagenes uploadFiles/queryUploadFileRad.php;
										$varBuscada = "r.RADI_NUME_RADI";
									} catch (\Exception $e) {
										echo '$db no esta declarada';
									} 
								?>
								<small>Usuario</small>
								<label for="" class="input">
									<select name="usuarioDependencia" id="usuarioDependencia" data-value="<?=$usuarioDependencia?>">
										<option value="">Seleccionar</option>
										<?php foreach($usuarios as $usuario): ?>
											<option value="<?= $usuario['USUA_CODI'] ?>" <?= $usuario['USUA_CODI'] == $usuarioDependencia ? 'selected' : '' ?>><?= $usuario['USUA_NOMB'] ?></option>
										<?php endforeach; ?>
									</select>
								</label>
							<?php endif; ?>

	<input type="hidden" name="usuaCodiEnvio" value="<?=$usuaCodiEnvio?>" />
  <input type="hidden" name="estado_sal" value="<?=$estado_sal?>" />
  <input type="hidden" name="porEnviar" value="<?=$porEnviar?>" />
	<footer><input type="submit" value="Buscar " name="Buscar" valign="middle" class="btn btn-success" />
	<?
	if($_POST["bTodasDep"]){
	 $datosss = " checked ";
	}else{
	 $datosss = "";
	}
	?>
	<!--<input type=checkbox name=bTodasDep id=bTodasDep <?=$datosss?> value="SelecionaTodas"><small> Buscar en Todas
            las Dependencias</small>-->
	</footer>
	<?
	if ($busqRadicados) {
		$busqRadicados = trim($busqRadicados);
		$textElements = explode (",", $busqRadicados);
		$newText = "";
		$i = 0;
		foreach ($textElements as $item) {
			$item = trim ( $item );
			if ($item) { 
			if ($i != 0) $busq_and = " or "; else $busq_and = " ";
				if(!$varBuscada) $varBuscada = "c.RADI_NUME_RADI";
				$busq_radicados_tmp .= " $busq_and cast($varBuscada as varchar(20)) like '%$item%' ";
				$i++;
			}
		} //FIN foreach

		$dependencia_busq2 .= " and ($busq_radicados_tmp) ";
	} else {
		$busq_radicados_tmp = '1 = 1';
	}//FIN if ($busqRadicados)
?>
	</form>
	 </span>
	</td></tr>
	</table>
	</td>
  </tr>
</table>
