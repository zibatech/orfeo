	{*Este  son las configuraciones iniciales para el paginado con ajax *}
	{$Json_file='paginatorAjax.controller.php'}
	{$Page=1}
	{$Size_page=10}
	{$Order=1}
	{$By=2}

	<div class="panel-body">
		{if $sql_initial|is_string}

		{* incluimos template *}
		<input type="hidden" value="{$Json_file}" name="inputJson_file"/>
		<input type="hidden" value="{$Page}" name="inputPage"/>
		<input type="hidden" value="{$Size_page}" name="inputSize_page"/>
		<input type="hidden" value="{$Order}" name="inputOrder"/>
		<input type="hidden" value="{$By}" name="inputBy"/> 
		<input type="hidden" value="{$sql_initial}" name="inputSqlInitial"/>
		<input type="hidden" value="{$num_values}" name="numValues"/> 

		<div class="box">
			<div class="box-header">
				<div class="row">
				  <div class="col-lg-3">
				  	 <div class="input-group">  
					<span class="input-group-addon"><i class="fa fa-search"></i></span> 
						<form method="post" action="./" >
							<input type="text" name ="search_fast" class="form-control" value = "" placeholder="Búsqueda Rapida">  
						</form>	
					</div> 
				  </div><!-- /.col-lg-6 -->
				  <div class="col-lg-8">
				  </div>
				  <div class="col-lg-1">
				  	<select id="selectdisplayrownum" class="form-control" title="Numero de registros a mostrar">
				  		{* <option value ='1' >1</option>
				  		<option value ='2' >2</option>
				  		<option value ='5' >5</option> *}
				  		<option value ='10' >10</option>
						<option value ='25' >25</option>
						<option value ='50' >50</option>
						<option value ='100' >100</option>
						{* <option value ='200' >200</option> *} 
				  	</select>
				  </div>
				  
				</div><!-- /.row --> 
			</div>
			<!---->
		</div>

			<div style = 'max-height: 500px; overflow: auto;' > 
				<table   class="table table-striped table-condensed table-bordered table-hover "> 
				{* <table class ="table table-striped table-condensed"> *} 
				{* <table  style="table-layout:fixed"; class="table table-striped table-condensed table-bordered table-hover"> *}
				  <thead id = "list_head_data">
					  <tr style='cursor:pointer;' > {$o=3}
					  		<th style = "min-width: 20px; width:20px; max-width: 20px; background-color: #3276b1; color:#fff"><strong>Acción</strong></th>
					  		{foreach name=titulousuario from=$titulousuario item=subtitulo}
					  			{if $o==1}{$cS = '_asc'}{else}{$cS = ''}{/if}
					  			{if $subtitulo|upper != 'ID' and $subtitulo|upper != 'CODIGO'}
					  			<th prop="sorting" style = "min-width: 40px; background-color: #3276b1; color:#fff" id="order_{$o++}" class ="sorting{$cS}" ><strong>{$subtitulo}</strong></th>
					  			{/if}		
							{/foreach}
					  </tr>
				  </thead>   
				  <tbody style="overflow-y: auto; " id="listdata" ></tbody>
				</table>  

			</div>

		{else}
			<div class="alert alert-info">{$sql_initial}</div> 
		{/if}

		{*Paginación*} 
		<ul id ='pagination' class='pagination'></ul>

		<div id="messaggeresult"></div>

	</div>