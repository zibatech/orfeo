{*
*@author Cesar Buelvas
*@mail cejebuto@gmail.com
*@date 01/02/2017
*}
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
		  	{* <div class="input-group">  
			<span class="input-group-addon"><i class="fa fa-search"></i></span> 
				<form method="post" action="./" >
					<input type="text" name ="search_fast" class="form-control" value = "" placeholder="Busqueda Rapida">  
				</form>	
			</div> *}
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
          


