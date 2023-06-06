<?php
$ruta_raiz="../";
$nurad=$_GET['nurad'];
include_once("connectIMAP2.php");
$codusuario  = $_SESSION["codusuario"];
$dependencia = $_SESSION["dependencia"];
$msgNo=$_GET["msgNo"];
function tamano_archivo($peso , $decimales = 2 ) {
$clase = array(" Bytes", " KB", " MB", " GB", " TB"); 
return round($peso/pow(1024,($i = floor(log($peso, 1024)))),$decimales ).$clase[$i];
}
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
if($msgNo){
$datos = $msg->getHeaders($msgNo);
$mailFrom=$datos["from"][0];
$mailRemite=$datos["from_personal"][0];
$mailFecha=$datos["MailDate"];
$mailAsunto=$datos["fetchsubject"];
    $msgPid  = $msg->structure[$msgNo]["pid"];
    $i=0;
    foreach($msgPid as $key => $value){

        $entro = 2;
        $body  = $msg->getBody($msgNo,$value);

        if($body["ftype"]=="text/html" || $body["ftype"]=="text/plain") {

            if($body["charset"] == 'utf-8')
            {
                $cuerpoMail = $body["message"];

            }elseif($body["charset"] == 'windows-1252' ||
                    $body["charset"] == 'iso-8859-1' )
            {
                $cuerpoMail = "<pre>". utf8_encode($body["message"]);

            }elseif(empty($body["charset"]))
            {
                $cuerpoMail = $body["message"];

            }else
            {
                $cuerpoMail = "<pre>". utf8_encode($body["message"]);
            }
            $entro = 1;
        }

        if($body["ftype"]=="text/plain"){
            $entro = 2;
        }
        if(($body["ftype"]=="image/jpeg" or $body["ftype"]=="image/gif" or $body["ftype"]=="image/png") 
            and !empty($body[fname])){

            $fname = explode('.',$body["fname"],2);
            $buscarReg = '/cid:'.$fname[0].'(.*[a-z0-9])@(.*)"/';
            $buscarReg = '/cid:'.$fname[0].'(.*[a-z0-9])/';

			$imagenPbExt = str_replace("image/","",$body["ftype"]);

			if($imagenPbExt=="jpeg") $imagenPbExt= "jpg";
			
            $imagen      = "../bodega/tmp/".preg_replace('/[^a-zA-Z0-9.]/i', '_',trim($body["fname"]));
            $imagenMail  = $parts[0];
            $imagenMailX = explode('"',$imagenMail,2);
            $imagenMail  = $imagenMailX[0];
            $cuerpoMail  = str_replace(str_replace('"','',$imagenMail),$imagen, $cuerpoMail);
            $file = fopen($imagen,"w");
            
            if(!fputs($file,$body["message"])) echo "<hr> No se guardo Imagen.  $imagen";
            fclose($file);
        }

        if($nurad){
            include_once "$ruta_raiz/include/db/ConnectionHandler.php";
            $db = new ConnectionHandler("$ruta_raiz");
            $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        }

        if($entro==2 and $body["fname"]){
            $iAnexo++;
            $fname  = preg_replace('/[^a-zA-Z0-9.]/i', '', trim(sup_tilde($body["fname"])));
            $imagen = "../bodega/tmp/".$fname;

               $cuerpoMail = str_ireplace($imagen,$fileEmailMsg,$cuerpoMail);
            if(!$nurad){
                $file = fopen($imagen,"w");
                if(!fputs($file,$body["message"])) echo "<hr> No se guardo Archivo.  $imagen";
                fclose($file);
                $mailAdjuntos[$i]["imagen"]=$imagen;
                $mailAdjuntos[$i]["size"]=tamano_archivo(filesize($imagen));
                $mailAdjuntos[$i]["type"]=mime_content_type($imagen);
		$mailAdjuntos[$i++]["name"]=$fname;
            }else{
                $sqll = "SELECT 
                            USUA_LOGIN 
                         FROM 
                            USUARIO 
                         WHERE 
                             USUA_CODI = $codusuario 
                             AND DEPE_CODI = $dependencia";

                $rss    = $db->conn->query($sqll);
                $usulog = $rss->fields["USUA_LOGIN"];

                $aExtension  = substr($fname,-5,5);
                $aExt        = split(".",$fname,2);
                $codigoAnexo = $nurad."000$iAnexo";
                $fina        = explode(".", $aExt[1]);
                $bExt        = $fina[count($fina)-1];
                $iSql        = "SELECT ANEX_TIPO_CODI FROM ANEXOS_TIPO WHERE ANEX_TIPO_EXT = '".$bExt."'";
                $rs          = $db->conn->query($iSql);
                $anexTipo    = $rs->fields["ANEX_TIPO_CODI"];
                if(isset($anexTipo)){
                $nomcort      = substr($aExt[1],-30);
                $nomcort      =  preg_replace('/[^a-zA-Z0-9.]/i', '_',trim($nomcort));
                $tmpNameEmail = $nurad."_000".$iAnexo.".".$bExt;
                $directorio   = substr($nurad,0,4) ."/".$_SESSION["dependencia"] ."/docs/";
                $fileEmailMsg = "../bodega/$directorio".$tmpNameEmail;
                $file         = fopen($fileEmailMsg,"w");
                if(!fputs($file,$body["message"])) echo "<hr> No se guardo Archivo.  $imagen";
                fclose($file);
                $mailAdjuntos[$i]["imagen"]=$imagen;
                $mailAdjuntos[$i]["size"]=tamano_archivo(filesize($imagen));
                $mailAdjuntos[$i]["type"]=mime_content_type($imagen);
		$mailAdjuntos[$i++]["name"]=$fname;
                $cuerpoMail = str_ireplace($imagen,$fileEmailMsg,$cuerpoMail);
                $fecha_hoy  = Date("Y-m-d");
                if(!$db->conn) echo "No hay conexion";
		//$db->conn->debug=true;
                $sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
                $record["ANEX_RADI_NUME"]    = $nurad;
                $record["ANEX_CODIGO"]       = $codigoAnexo;
                $record["ANEX_SOLO_LECT"]    = "'S'";
                $record["ANEX_CREADOR"]      = "'$usulog'";
                $record["ANEX_DESC"]         = "' Archivo:.". $fname."'";
                $record["ANEX_NUMERO"]       = $iAnexo;
                $record["ANEX_NOMB_ARCHIVO"] = "'".$tmpNameEmail."'";
                $record["ANEX_BORRADO"]      = "'N'";
                $record["ANEX_DEPE_CREADOR"] = $dependencia;
                $record["SGD_TPR_CODIGO"]    = '0';
                $record["ANEX_TIPO"]         = $anexTipo;
                $record["ANEX_FECH_ANEX"]    = $sqlFechaHoy;
                $db->insert("anexos", $record, "true");
		}
            }
           }
        if(sup_tilde($msg->header[$msgNo]['from'][0]) and !$pidMail) $pidMail=$value;

        }
}
else{
    print("No hay Correo disponible");
}
?>
<style>
.inbox-message{
margin-right: 10px;
}
.inbox-info-bar{
margin-right: 10px;
}
</style>
<h2 class="email-open-header">
	<?=$mailAsunto?> <span class="label txt-color-white">inbox</span>
	<a href="javascript:void(0);" rel="tooltip" data-placement="left" data-original-title="Print" class="txt-color-darken pull-right"><i class="fa fa-print"></i></a>	
</h2>

<div class="inbox-info-bar">
	<div class="row">
		<div class="col-sm-9">
			<span class="hidden-mobile"><i><?=$mailFecha?></i></span> 
		</div>
		<div class="col-sm-3 text-right">
			
			<div class="btn-group text-left">
				<button class="btn btn-primary btn-sm replythis">
					<i class="fa fa-reply"></i> Radicar
				</button>
			</div>

		</div>
	</div>
</div>

<div align="center" class="inbox-message">
<?=utf8_encode($cuerpoMail)?>
</div>
<div class="inbox-download">
	<?=$numAdj=count($mailAdjuntos);?> <?=($numAdj==1)?"adjunto":"adjuntos";?>	
	<ul class="inbox-download-list">
	<? 
		$icon=($adjunto["type"]=="image/png" or  $adjunto["type"]=="image/gif" or  $adjunto["type"]=="image/x-ms-bmp" or $adjunto["type"]=="image/jpeg")?"<center><img height='150px' src='".$adjunto["imagen"]."'></img></center>":"<i class='fa fa-file'></i>";
		foreach ($mailAdjuntos as $adjunto){
		$listAdj.='
		<li>
			<div class="well well-sm">
				<span>
					'.$icon.'
				</span>
				
				<br>
				<strong>'.$adjunto["name"].'</strong> 
				<br>
				'.$adjunto["size"].' 
				<br> 
				<a href="'.$adjunto["imagen"].'"> Download</a>
			</div>
		</li>
		';}
		echo $listAdj;
		?>
	</ul>
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
	
	/*
	 * ALL PAGE RELATED SCRIPTS CAN GO BELOW HERE
	 * eg alert("my home function");
	 * 
	 * var pagefunction = function() {
	 *   ...
	 * }
	 * loadScript("js/plugin/_PLUGIN_NAME_.js", pagefunction);
	 * 
	 * TO LOAD A SCRIPT:
	 * var pagefunction = function (){ 
	 *  loadScript(".../plugin.js", run_after_loaded);	
	 * }
	 * 
	 * OR
	 * 
	 * loadScript(".../plugin.js", run_after_loaded);
	 */
	
	
	// PAGE RELATED SCRIPTS
	
	$(".table-wrap [rel=tooltip]").tooltip();

	$(".replythis").click(function(){
		loadURL("<?=$ruta_raiz?>/radicacion/NEW.php?dependencia=900&ent=1&radMail=1&msgNo=<?=$_GET['msgNo']?>", $('#inbox-content > .table-wrap'));
	})

</script>
<?
if (isset($nurad)){
	$data=file_get_contents("plantillaImagenRad.html");
	$data=str_replace("ASUNTO_EMAIL",$mailAsunto,$data);
	$data=str_replace("FECHA_EMAIL",$mailFecha,$data);
	$data=str_replace("CUERPO_MENSAJE",utf8_encode($cuerpoMail),$data);
	$data=str_replace("NUMERO_ADJUNTOS",$numAdj,$data);
	$data=str_replace("LISTADO_ADJUNTOS",$listAdj,$data);
	$fp = fopen('../bodega/tmp/'.$mailFecha.'.html', 'w');
	fwrite($fp, $data);
	fclose($fp);
	$ano=substr($nurad,0,4);
	$pathBodega="/$ano/$dependencia/docs/$nurad.html";
	if (copy("../bodega/tmp/$mailFecha.html","../bodega/$pathBodega")){
		$isqlRadicado = "update radicado set RADI_PATH = '$pathBodega' where radi_nume_radi = $nurad";
		$rs=$db->conn->query($isqlRadicado);
        	if (!$rs)//Si actualizo BD correctamente
        	{	
			echo "Fallo la Actualizacion del Path en radicado < $isqlRadicado >";
		}else{
			$radicadosSel[] = $nurad;
			$codTx = 42;	//Código de la transacción
			$noRadicadoImagen = $nurad;
			$observa = "Mail(".utf8_decode($mailAsunto).")";
			include "$ruta_raiz/include/tx/Historico.php";
			$hist        = new Historico($db);
			$codusuario  = $_SESSION["codusuario"];
			$dependencia = $_SESSION["dependencia"];
			$hist->insertarHistorico($nurad,  $dependencia , $codusuario, $dependencia, $codusuario, $observa, $codTx);
			//include "enviarMail.php";
			echo "Asociado Correctamente";
		}
	}
	else{
		echo "Error al copiar imagen de radicado a la bodega";
	}
}
?>
