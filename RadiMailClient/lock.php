<?php
session_start();
$ruta      = '/bodega';
$ruta_raiz="../";
if (!$_SESSION['dependencia'])
header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}


// Descomentar si el sistema posee un unico correo electronico //$_SESSION["usua_email_1"] = $_SESSION["usua_email"];
$usua_email  = $_SESSION["usua_email_1"];
$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$encabezado  = session_name()."          = ".session_id()."";
//var_dump($_SESSION);
if($passwd_mail) {
	$_SESSION["passwd_mail"]=$passwd_mail;
	header('Location: index.php');
}
include_once "htmlheader.inc.php";
if(isset($_SESSION["email"]["error"])){
	$error = $_SESSION["email"]["error"]; 
	if (strpos($error,'Invalid credentials'))
		$error = 'Credenciales invalidas'; 
}
?>

<?php
?>
	<body>
<div width="50px">
		<div id="main" role="main">

			<!-- MAIN CONTENT -->

			<form class="lockscreen animated flipInY" action="lock.php" method="post">
				<div class="logo">
	<h1 class="semi-bold"> Radicación de e-mail</h1>
				</div>
				<div>
				<?php $entidad=$_SESSION["entidad"]; ?>
					<img src="../logoEntidad.png"  width="120" height="80" />
					<div>
						<h1><i class="fa fa-user fa-3x text-muted air air-top-right hidden-mobile"></i><?=current(explode("@",$usua_email))?><small><i class="fa fa-lock text-muted"></i> &nbsp;Locked</small></h1>
						<p class="text-muted">
							<br>
							<input class="form-control" type="email" name="email" placeholder="<?=$usua_email?>" disabled>
						</p>

						<div class="input-group">
							<input class="form-control" type="password" name="passwd_mail" placeholder="Password">
							<div class="input-group-btn">
								<button class="btn btn-primary" type="submit">
									<i class="fa fa-key"></i>
								</button>
							</div>
						</div>
						<p class="no-margin margin-top-5">
							<?=$error?>
						</p>
					</div>

				</div>
				<p class="font-xs margin-top-5">
					Copyright SmartAdmin 2014-2020.

				</p>
			</form>

		</div>
		</div>

		<!--================================================== -->	

		<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
		<script src="js/plugin/pace/pace.min.js"></script>

	    <!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
<!--	    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script> if (!window.jQuery) { document.write('<script src="js/libs/jquery-2.0.2.min.js"><\/script>');} </script>

	    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script> if (!window.jQuery.ui) { document.write('<script src="js/libs/jquery-ui-1.10.3.min.js"><\/script>');} </script>-->



		<!-- JS TOUCH : include this plugin for mobile drag / drop touch events 		
		<script src="js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> -->

		<!-- BOOTSTRAP JS -->		
		<script src="js/bootstrap/bootstrap.min.js"></script>

		<!-- CUSTOM NOTIFICATION -->
		<script src="js/notification/SmartNotification.min.js"></script>

		<!-- JARVIS WIDGETS -->
		<script src="js/smartwidgets/jarvis.widget.min.js"></script>
		
		<!-- EASY PIE CHARTS -->
		<script src="js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>
		
		<!-- SPARKLINES -->
		<script src="js/plugin/sparkline/jquery.sparkline.min.js"></script>
		
		<!-- JQUERY VALIDATE -->
		<script src="js/plugin/jquery-validate/jquery.validate.min.js"></script>
		
		<!-- JQUERY MASKED INPUT -->
		<script src="js/plugin/masked-input/jquery.maskedinput.min.js"></script>
		
		<!-- JQUERY SELECT2 INPUT -->
		<script src="js/plugin/select2/select2.min.js"></script>

		<!-- JQUERY UI + Bootstrap Slider -->
		<script src="js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>
		
		<!-- browser msie issue fix -->
		<script src="js/plugin/msie-fix/jquery.mb.browser.min.js"></script>
		
		<!-- FastClick: For mobile devices -->
		<script src="js/plugin/fastclick/fastclick.min.js"></script>
		
		<!--[if IE 7]>
			
			<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
			
		<![endif]-->
		
		<!-- MAIN APP JS FILE -->
		<script src="js/app.min.js"></script>

	</body>
</html>
