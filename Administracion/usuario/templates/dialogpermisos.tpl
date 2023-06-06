<table class="datatable11"  width="100%">

	<colgroup>
		<col/>
		<col/>
		<col/>
	</colgroup>

	<thead>
	<tr>
		<th>Nombre</th>
		<th>Descripci&oacute;n</th>
		<th>Crud</th>
	</tr>
	</thead>

	<tbody id="contentable">

	<-{foreach item=item from=$permisos}->
		<tr>

			<td class="hasinput">
				<-{$item.nombre}->
			</td>

			<td class="hasinput">
				<-{$item.descripcion}->
			</td>

			<td class="hasinput">
				<-{$item.CRUD}->
				<-{foreach item=i from=$crud}->
					<-{if $item.crud eq $i.ID}->
						<-{$i.NOMBRE}->
					<-{/if}->
				<-{/foreach}->
			</td>

		</tr>
	<-{/foreach}->
	</tbody>

</table>

<!-- pager -->
<div class="pager">
	<div class="btn btn-sm"><span class="fa fa-fast-backward
				txt-color-blueLight first"></span></div>
	<div class="btn btn-sm"><span class="fa fa-backward prev
				txt-color-blueLight"></span></div>
	<span class="pagedisplay"></span> <!-- this can be any element, including an input -->
	<div class="btn btn-sm"><span class="fa fa-forward next
				txt-color-blueLight"></span></div>
	<div class="btn btn-sm"><span class="fa fa-fast-forward
				txt-color-blueLight last"></span></div>

	<select class="pagesize" title="Select page size">
		<option selected="selected" value="10">10</option>
		<option value="20">20</option>
		<option value="30">30</option>
		<option value="40">40</option>
	</select>

	<select class="gotoPage" title="Select page number"></select>
</div>

<script type="text/javascript">

	$(".datatable11").tablesorter({
		widgets: ["filter"],
		widgetOptions : {
			filter_reset : '.reset'
		}
	}).tablesorterPager({
		// target the pager markup - see the HTML block below
		container: $(".pager"),
		// use this url format "http:/mydatabase.com?page={page}&size={size}"
		ajaxUrl: null
	});

</script>
