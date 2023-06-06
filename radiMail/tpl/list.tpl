<table id="inbox-table" class="table table-striped table-hover" style="overflow: auto;display: block;">
<thead>
<tr>
<th></th>
<th>De</th>
<th>Para</th>
<th>CC</th>
<th>Asunto</th>
<th></th>
<th>Recibido</th>
</tr>
</thead>
	<tbody>


{foreach $mails as $mail}
		<tr id="{$mail['uid']}" class={if $mail['seen'] eq '1'}"read"{else}"unread"{/if}>
			<td  id="{$mail['uid']}" class="inbox-table-icon">
				<div class="">
					<span><span class={if $mail['seen'] eq '1'}"label bg-color-grey"{else}"label bg-color-green"{/if}>{$j++}</span><span>
				</div>
			</td>
			<td  id="{$mail['uid']}" class="inbox-data-from hidden-xs hidden-sm">
				<div title="{$mail['mailFrom']}">
					{$mail['mailFrom']}
				</div>
			</td>
			<td  id="{$mail['uid']}" class="inbox-data-from hidden-xs hidden-sm">
				<div title="{$mail['mailToF']}">
					{$mail['mailToF']}
				</div>
			</td>
			<td  id="{$mail['uid']}" class="inbox-data-from hidden-xs hidden-sm">
				<div title="{$mail['mailCC']}">
					{$mail['mailCC']}
				</div>
			</td>
			<td id="{$mail['uid']}" class="inbox-data-message hidden-xs hidden-sm">
				<div title="{$mail['mailAsunto']}">
					{$mail['mailAsunto']}
				</div>
			</td>
			<td  id="{$mail['uid']}" class="inbox-data-attachment hidden-xs">
				<div>
					<a href="javascript:void(0);" rel="tooltip" data-placement="left" data-original-title="FILES: rocketlaunch.jpg, timelogs.xsl" class="txt-color-darken"><i class="fa fa-paperclip fa-lg"></i></a>
				</div>
			</td>
			<td  id="{$mail['uid']}" class="inbox-data-date hidden-xs">
				<div>
					{$mail['mailFecha']}
				</div>
			</td>
		</tr>
{/foreach}













	</tbody>
</table>

<script>
	
	//Gets tooltips activated
	$("#inbox-table [rel=tooltip]").tooltip();

	$("#inbox-table input[type='checkbox']").change(function() {
		$(this).closest('tr').toggleClass("highlight", this.checked);
	});

	$("#inbox-table .inbox-data-message").click(function() {
		$this = $(this);
		getMail($this);
	})
	$("#inbox-table .inbox-data-from").click(function() {
		$this = $(this);
		getMail($this);
	})
	function getMail($this) {
		var uid=($this.attr("id"));
		loadURL("email-opened.php?uid="+uid, $('#inbox-content > .table-wrap'));
	}


	$('.inbox-table-icon input:checkbox').click(function() {
		enableDeleteButton();
	})

	$(".deletebutton").click(function() {
		$('#inbox-table td input:checkbox:checked').parents("tr").rowslide();
		//$(".inbox-checkbox-triggered").removeClass('visible');
		//$("#compose-mail").show();
	});

	function enableDeleteButton() {
		var isChecked = $('.inbox-table-icon input:checkbox').is(':checked');

		if (isChecked) {
			$(".inbox-checkbox-triggered").addClass('visible');
			//$("#compose-mail").hide();
		} else {
			$(".inbox-checkbox-triggered").removeClass('visible');
			//$("#compose-mail").show();
		}
	}
	{*window.onload = function() {
	}*}
	$("#desde").html("{$desde}");
	$("#hasta").html("{$hasta}");
	$("#total").html("{$countMails}");

	$('#left').attr("disabled", false);
	if ($('#desde').html()==1){
		$('#left').attr("disabled", true);
	}

	$('#right').attr("disabled", false);
	if ($('#hasta').html()==countMails){
		$('#right').attr("disabled", true);
	}
	
</script>
