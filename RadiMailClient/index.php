<?php
session_start();
$ruta_raiz="../";

if (!$_SESSION['dependencia']){
	die ("<center>Sesion terminada, vuelve a iniciar sesion <a href='../cerrar_session.php'>aqui.<a></center>");
}

$usua_email = $_SESSION['usua_email_1'];
$passwd_mail = $_SESSION["passwd_mail"];

include ("configRadiMail.php");

$page=!isset($_GET['page'])?1:$_GET['page'];

/*[> try to connect <]
function orDieImap(){
	$_SESSION["email"]["error"]=imap_last_error();
	die(header('Location: lock.php'));
}
$inbox = imap_open($hostname,$usua_email,$passwd_mail) or die(orDieImap());*/
include_once "htmlheader.inc.php";
?>
			<!-- RIBBON -->
			<div id="ribbon">

				<span class="ribbon-button-alignment"> 
					<span id="refresh" class="btn btn-ribbon" data-action="resetWidgets" data-title="refresh"  rel="tooltip" data-placement="bottom"  data-html="true">
						<i class="fa fa-refresh"></i>
					</span> 
				</span>

				<!-- breadcrumb -->
				<ol class="breadcrumb">
					<li>Home</li><li>Inbox</li>
				</ol>
				<!-- end breadcrumb -->

			</div>
			<!-- END RIBBON -->


<div class="inbox-nav-bar no-content-padding">

	<h1 class="page-title txt-color-blueDark hidden-tablet"><i class="fa fa-fw fa-inbox"></i> Inbox &nbsp;
	</h1>

	<div class="btn-group hidden-desktop visible-tablet">
		<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
			Inbox <i class="fa fa-caret-down"></i>
		</button>
		<ul class="dropdown-menu pull-left">
			<li>
				<a href="javascript:void(0);" class="inbox-load">Inbox <i class="fa fa-check"></i></a>
			</li>
		</ul>

	</div>


	<a href="javascript:void(0);" id="compose-mail-mini" class="btn btn-primary pull-right hidden-desktop visible-tablet"> <strong><i class="fa fa-file fa-lg"></i></strong> </a>

	<div class="btn-group pull-right inbox-paging">
		<a href="javascript:void(0);" class="btn btn-default btn-sm"><strong><i class="fa fa-chevron-left"></i></strong></a>
		<a href="javascript:void(0);" class="btn btn-default btn-sm"><strong><i class="fa fa-chevron-right"></i></strong></a>
	</div>
	<span class="pull-right"><strong>1-30</strong> of <strong>3,452</strong></span>

</div>

<div id="inbox-content" class="inbox-body no-content-padding">

	<div class="inbox-side-bar">


		<h6> Folder <a href="javascript:void(0);" rel="tooltip" title="" data-placement="right" data-original-title="Refresh" class="pull-right txt-color-darken"><i class="fa fa-refresh"></i></a></h6>

		<ul class="inbox-menu-lg">
			<li class="active">
				<a class="inbox-load" href="javascript:void(0);"> Inbox (0) </a>
			</li>
		</ul>



	</div>

	<div class="table-wrap custom-scroll animated fast fadeInRight">
		<!-- ajax will fill this area -->
		<?
		include ("email-list.php");
		imap_close($inbox);
		?>

	</div>


</div>

<script type="text/javascript">
	/* DO NOT REMOVE : GLOBAL FUNCTIONS!
	 *
	 * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
	 *
	 * // activate tooltips
	 * $("[rel=tooltip]").tooltip();
	 *
	 * // activate popovers
	 * $("[rel=popover]").popover();
	 *
	 * // activate popovers with hover states
	 * $("[rel=popover-hover]").popover({ trigger: "hover" });
	 *
	 * // activate inline charts
	 * runAllCharts();
	 *
	 * // setup widgets
	 * setup_widgets_desktop();
	 *
	 * // run form elements
	 * runAllForms();
	 *
	 ********************************
	 *
	 * pageSetUp() is needed whenever you load a page.
	 * It initializes and checks for all basic elements of the page
	 * and makes rendering easier.
	 *
	 */

	pageSetUp();

	// PAGE RELATED SCRIPTS

	// pagefunction
	
	var pagefunction = function() {
		/*
		 * LOAD INBOX MESSAGES
		 */
		//loadInbox();

		<?if (isset($radMail)) echo "loadRadMail();";?>
		
		function loadRadMail() {
			var URI="<?=end(explode("?",$_SERVER['REQUEST_URI']))?>";
			loadURL("../radicacion/NEW.php?"+URI, $('#inbox-content > .table-wrap'))
		}
		
		function loadInbox() {
			loadURL("email-list.php", $('#inbox-content > .table-wrap'))
		}
	
		/*
		 * Buttons (compose mail and inbox load)
		 */

		$(".inbox-load").click(function() {
			loadInbox();
		});

		$(".fa-refresh").click(function() {
			loadURL("email-list.php?force", $('#inbox-content > .table-wrap'))
		});
		// compose email
		
	};
	
	// end pagefunction
	
	// load delete row plugin and run pagefunction
	pagefunction();
	
</script>
<!-- IMPORTANT: APP CONFIG -->
<script src="js/app.config.js"></script>
<!-- MAIN APP JS FILE -->
<script src="js/app.min.js"></script>
<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
<script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>
<!--[if IE 8]>
	<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
<![endif]-->
