<?php
session_start();
$ruta_raiz="../";
if (!$_SESSION['dependencia'])
header ("Location: $ruta_raiz/cerrar_session.php");
include ("$ruta_raiz/processConfig.php");
if($_SESSION['usua_email'])     $usua_email      = $_SESSION['usua_email'];

/****Configuración para Gmail, (autenticación SSL)****
#$hostname = '{'."$servidor_mail:$puerto_mail/$protocolo_mail/ssl".'}';
#$username = $usuaEmail;
/******************************************************/

/***Configuración para Exchange sin autenticación SSL**/
/**/$hostname = '{'."$servidor_mail:$puerto_mail/novalidate-cert".'}';
/**/$usua_email = current(explode ("@",$usua_email));
/******************************************************/

$passwd_mail = $_SESSION["passwd_mail"];

/* try to connect */
$inbox = imap_open($hostname,$usua_email,$passwd_mail) or die(header('Location: lock.php'));
include_once "$ruta_raiz/htmlheader.inc.php";
?>
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
		LOADING...

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

		// fix table height
		tableHeightSize();

		$(window).resize(function() {
			tableHeightSize()
		})
		function tableHeightSize() {

			if ($('body').hasClass('menu-on-top')) {
				var menuHeight = 68;
				// nav height

				var tableHeight = ($(window).height() - 224) - menuHeight;
				if (tableHeight < (320 - menuHeight)) {
					$('.table-wrap').css('height', (320 - menuHeight) + 'px');
				} else {
					$('.table-wrap').css('height', tableHeight + 'px');
				}

			} else {
				var tableHeight = $(window).height() - 224;
				if (tableHeight < 320) {
					$('.table-wrap').css('height', 320 + 'px');
				} else {
					$('.table-wrap').css('height', tableHeight + 'px');
				}

			}

		}

		/*
		 * LOAD INBOX MESSAGES
		 */
		loadInbox();
		function loadInbox() {
			loadURL("email-list.php", $('#inbox-content > .table-wrap'))
		}
	
		/*
		 * Buttons (compose mail and inbox load)
		 */
		$(".inbox-load").click(function() {
			location.reload();
		});
	
		// compose email
		$("#compose-mail").click(function() {
			loadURL("ajax/email-compose.php", $('#inbox-content > .table-wrap'));
		});
		
	};
	
	// end pagefunction
	
	// load delete row plugin and run pagefunction
	pagefunction();
	
</script>
