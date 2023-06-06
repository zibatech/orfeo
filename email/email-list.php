<?php
session_start();
$ruta      = '../bodega';
$ruta_raiz =   "../";  
include ("$ruta_raiz/processConfig.php");
if (!$_SESSION['dependencia'])
    header ("Location: $ruta_raiz/cerrar_session.php");
    
foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}

if($_SESSION['usua_email'])     $usua_email	= $_SESSION['usua_email'];
if($_SESSION['passwd_mail'])	$passwd_mail	= $_SESSION["passwd_mail"];

?>
<table id="inbox-table" class="table table-striped table-hover">
	<tbody>
<?php
function sup_tilde($str){
    $stdchars= array("@","a","e","i","o"
                    ,"u","n","A","E","I"
                    ,"O","U","N"," " ," "
                    ,"!","", " ","", ""
                    ,"","","á","é","í"
                    ,"ó","ú");

    $tildechars= array( "@","=E1","=E9","=ED","=F3"
                        ,"=FA","=F1","=C1","=C9","=CD"
                        ,"=D3","=DA","=D1","=?iso-8859-1?Q?","?=",
                        "=A1","=?Windows-1252?Q?", "=20","=?ISO-8859-1?Q?", "=2C",
                        "=2E", "=?ISO-8859-1?B?", "a?","e?","i?",
                        "o?","u?");
    return str_replace($tildechars,$stdchars, $str);
}


/* connect to gmail */
$hostname = '{'."$servidor_mail:$puerto_mail/novalidate-cert".'}';
/**/$usua_email = current(explode ("@",$usua_email));
/* try to connect */
$inbox = imap_open($hostname,$usua_email,$passwd_mail) or die('Error al conectar a e-mail'/*.imap_last_error()*/);

/* grab emails */
$emails = imap_search($inbox,'ALL');
$emails = array_slice($emails,1,10);

/* if emails are returned, cycle through each... */
if($emails) {
	/* put the newest emails on top */
	rsort($emails);
	/* for every email... */
	$i=1;
	foreach($emails as $email_number) {

		/* get information specific to this email */
		$overview = imap_fetch_overview($inbox,$email_number,0);
		$message = imap_fetchbody($inbox,$email_number,2);

		/* output the email header information */
		//$output.= '<div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
		$output.= $email_number.'<span class="subject">'.$overview[0]->subject.'</span> ';
		//$output.= '<span class="from">'.$overview[0]->from.'</span>';
		//$output.= '<span class="date">on '.$overview[0]->date.'</span>';
		$output.= '</div><br>';

		/* output the email body */
		//$output.= '<div class="body">'.$message.'</div>';
 
		$mailAsunto = imap_utf8(trim($overview[0]->subject));
		$mailFecha  = substr($overview[0]->date,0,12);
		$mailFrom   = imap_utf8(trim($overview[0]->from));
		$mailToF    = imap_utf8(trim($overview[0]->to));
		$mailAttach = "";


?>
		<tr id="<?=$email_number?>" class="unread">
			<td  id="<?=$email_number?>" class="inbox-table-icon">
				<div class="">
					<span><span class="label bg-color-orange"><?=$i++?></span><span>
				</div>
			</td>
			<td  id="<?=$email_number?>" class="inbox-data-from hidden-xs hidden-sm">
				<div>
					<?=$mailFrom?>
				</div>
			</td>
			<td id="<?=$email_number?>" class="inbox-data-message">
				<div>
					<?=$mailAsunto?>
				</div>
			</td>
			<td  id="<?=$email_number?>" class="inbox-data-attachment hidden-xs">
				<div>
					<a href="javascript:void(0);" rel="tooltip" data-placement="left" data-original-title="FILES: rocketlaunch.jpg, timelogs.xsl" class="txt-color-darken"><i class="fa fa-paperclip fa-lg"></i></a>
				</div>
			</td>
			<td  id="<?=$email_number?>" class="inbox-data-date hidden-xs">
				<div>
					<?=$mailFecha?>
				</div>
			</td>
		</tr>
		<?
	}
/* close the connection */
imap_close($inbox);
}else{
	header('Location: lock.php');
	die;
}?>
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
		var msgNo=($this.attr("id"));
		loadURL("email-opened.php?msgNo="+msgNo, $('#inbox-content > .table-wrap'));
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
