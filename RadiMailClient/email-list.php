<?php
session_start();

$ruta      = '../bodega';
$ruta_raiz =   "../";  
include ("$ruta_raiz/processConfig.php");
if (!$_SESSION['dependencia'])
	die ("<center>Sesion terminada, vuelve a iniciar sesion <a href='../cerrar_session.php'>aqui.<a></center>");
if (isset($_GET['force'])){
	exec('sh cron-clean.sh');
	//include("auto-radiMail.php");
}
    
foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}

if($_SESSION['usua_email_1'])     $usua_email	= $_SESSION['usua_email_1'];
if($_SESSION['passwd_mail'])	$passwd_mail	= $_SESSION["passwd_mail"];

include_once "../include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
$isql="select * from radimail order by fecha desc limit 100";
$emails = $db->conn->GetArray($isql);
$emailCount=count($emails);
?>
<table id="inbox-table" class="table table-striped table-hover">
	<tbody>
<?php


/* if emails are returned, cycle through each... */
if($emails) {
	/* for every email... */
	$numMail=1;
	foreach($emails as $email) {

		$uid=$email["UNIQUEID"];
		$radi_nume_radi=$email["RADI_NUME_RADI"];
?>
		<tr id="<?=$uid?>" class="unread">
			<td  id="<?=$uid?>" class="inbox-table-icon">
				<div class="">
					<span><span class="label bg-color-orange"><?=$numMail++?></span><span>
				</div>
			</td>
			<td  id="<?=$uid?>" class="inbox-data-from hidden-xs hidden-sm">
				<div>
					<?=$email["DESDE"]?>
				</div>
			</td>
			<td id="<?=$uid?>" class="inbox-data-message">
				<div>
					<?=substr($email['ASUNTO'],0,100)?>
				</div>
			</td>
			<td  id="<?=$uid?>" class="inbox-data-attachment hidden-xs">
<?if (!empty($radi_nume_radi)){?>
				<div style="color:red">
				<b>R</b>
				</div>
<?}?>
			</td>
			<td  id="<?=$uid?>" class="inbox-data-date hidden-xs">
				<div>
					<?=substr($email['FECHA'],4,-5)?>
				</div>
			</td>
		</tr>
		<?
	}
}else{
	header('Location: lock.php');
	die;
}
?>
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
		loadURL("email-opened-live.php?uid="+uid, $('#inbox-content > .table-wrap'));
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
	window.onload = function() {
		$(".inbox-load").html("Inbox (<?=count($emails)?>)");
	}
	
</script>
