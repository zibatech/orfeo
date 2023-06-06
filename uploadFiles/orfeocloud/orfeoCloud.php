<?php
session_start();
date_default_timezone_set('America/Bogota');
$ruta_raiz = "../..";
foreach ($_GET as $key => $valor)
    $$key = $valor;
foreach ($_POST as $key => $valor)
    $$key = $valor;
$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$id_rol = $_SESSION["id_rol"];
$id_rol = 5;
$ruta_raiz = "../..";
include('validadte.php');
$script = "operOrfeoCloud.php";
include ('orfeoCloud-class.php');
//include_once "tpDocumento.php";
//$ruta_owonclod='/var/www/html/owncloud/data/';
//include "config-inc.php";
include ($ruta_raiz."/processConfig.php");
//@$ruta_owonclod se importa directamenete del archivo de configuraciones (config.php)
$cloud = new orfeoCloud($ruta_owonclod, $ruta_raiz);
//$cloud->setUserLogin();
$ownUser = $cloud->dataUser();
if (!isset($fechaIni))
    $fechaIni = date('d/m/y');
if (!isset($fechaFin))
    $fechaFin = date('d/m/y');
$optionTp = "";
//colocar permiso
$_SESSION['usua_scan'] =1;
if (!$_SESSION['usua_scan'] == 1) {
    die('Notiene permiso de acceder');
}
/* datos a cconfigurar* */
//$userOwncloud='digitalizador5'; 
$userOwncloud = $cloud->getUserCloud();
$ukrd = $cloud->getUserLogin();
$classR = '';
$classA = '';
if (!empty($tpAxion)){
    $tpAxion = 'ENTRADA';
}

switch ($tpAxion) {
  case 'RADICADOS':
    $dataDD = 'R';
    $classR = ' class="active"';
    break;
  case 'ENTRADA':
    $dataDD = 'F';
    $classE = ' class="active"';
    break;
  case 'info':
    $dataDD = 'I';
    $classI = ' class="active"';
    break;
  default:
    $dataDD = 'A';
    $classA = ' class="active"';
    //inicializa tipos documentales
    if(isset($tdocumentales)){
        $numtp = count($tdocumentales);
        for ($i = 0; $i < $numtp; $i++) {
            $nomTP = $tdocumentales[$i]["DESCRIP"];
            $codTP = $tdocumentales[$i]["CODIGO"];
            $optionTp.="<option value='$codTP'>$nomTP</option>";
        }
    }


        break;
}

//echo "<hr>llego aka <hr>";
$carpS = $tpAxion;
//$ruta_owonclod='/var/www/owncloud/data/';
?>
<!DOCTYPE html >
<html lang="es">
    <head>
        <title>Scan OwnCloud Orfeo</title>
        <script language="JavaScript" src="common.js"></script>
        <?php  include_once "$ruta_raiz/htmlheader.inc.php";  ?>
        <!-- JS file -->
        <script src="<?=$ruta_raiz?>/include/EasyAutocomplete/jquery.easy-autocomplete.min.js"></script> 

        <!-- CSS file -->
        <link rel="stylesheet" href="<?=$ruta_raiz?>/include/EasyAutocomplete/easy-autocomplete.min.css"> 

        <!-- Additional CSS Themes file - not required-->
        <link rel="stylesheet" href="<?=$ruta_raiz?>/include/EasyAutocomplete/easy-autocomplete.themes.min.css">         
        <script type="text/javascript">
        function nombreItem(){
    alert($(this).val());
  
  }
    function Listar() {
      var usA = document.getElementById('user').value;
      var poststr = "action=Listar&usA=" + usA;
      url = "<? echo $script; ?>";
      partes(url, 'listados', poststr, '');
    }
    // subir('div20169000002552_4.pdf','20169000002552_4.pdf',1,'admon','454','3','3')
    function subir(div, name, pages, user, peso, acc, k) {
    // var usA = document.getElementById('user').value;

    if (acc == 1 || acc == 2 || acc == 3) {
    if (acc == 1) {
    var txt = 'Se modificara el';
    var r = prompt(txt + " radicado " + name.slice(0, -4) + "\n  Esta Seguro de hacer esta accion \n Por favor Escriba la Obsevacion", "");
    if (r == false)
      return false;
    if (r.length <= 5){
      alert('Observacion debe ser de mas 5 caracteres' );
    return false;
    }

    }
    if (acc == 2) {
      txt = 'Definitivo del';
    var r = confirm(txt + " radicado " + name.slice(0, -4) + " Esta Seguro de hacer esta accion");

    if (r == false)
      return false;
    }
    if (acc == 3) {
      var txt = 'Se va a anexar al ';
      var r = document.getElementsByName("desc")[k].value;
      var idTp = name.replace('.','');
      var tprCodigo = document.getElementById("tpCodigo_"+idTp).value;
    }
    }
    // return false;
    document.getElementById(div).innerHTML = '<center><img  alt="Procesando" src="<?= $ruta_raiz; ?>/imagenes/loading.gif"></center>';
    var poststr = "action=<?php echo $dataDD; ?>subir&name=" + name + "&pages=" + pages + "&userOwn=" + user + "&pesoA=" + peso+'&r='+r+"&tprCodigo="+tprCodigo;
    url = "<?php echo $script; ?>";
    partes(url, div, poststr, '');
    } // Fin de Subir ...
            
            
            function subirf(div, name, pages, user, peso, acc) {
            // var usA = document.getElementById('user').value;

            if (acc == 1 || acc == 2) {
            if (acc == 1) {
            var txt = 'Se modificara el';
            var r = prompt(txt + " radicado " + name.slice(0, -4) + "\n  Esta Seguro de hacer esta accion \n Por favor Escriba la Obsevacion", "");
            
            
            if (r == false)
            return false;
            if (r.length <= 10){
            alert('Observacion debe ser de mas 10 caracteres' );
            return false;
            }

            }
            if (acc == 2) {
            txt = 'Definitivo del';
            var r = confirm(txt + " radicado " + name.slice(0, -4) + " Esta Seguro de hacer esta accion");

            if (r == false)
            return false;
            }
            }
            //         return false;
            document.getElementById(div).innerHTML = '<center><img  alt="Procesando" src="<?= $ruta_raiz; ?>/imagenes/loading.gif"></center>';
            var poststr = "action=<?php echo $dataDD; ?>subirf&name=" + name + "&pages=" + pages + "&userOwn=" + user + "&pesoA=" + peso+'&r='+r;
            url = "<?php echo $script; ?>";
            partes(url, div, poststr, '');
            }
            function subir2(div, name, pages, user, peso, tpdoc) {
            //document.getElementById(div).innerHTML = '<center><img  alt="Procesando" src="<?= $ruta_raiz; ?>/imagenes/loading.gif"></center>';
            var tpdocS = document.getElementById(tpdoc).value;
            //var comentarioS = document.getElementById(comentario).value;
            if (tpdocS == '-') {
            alert('Debe selecionar un tipo de documento');
            return false;
            }
            /*if (comentarioS == '-') {
            alert('Debe selecionar un Comentario');
            return false;
            }*/
	    var txt = 'El anexo al';
            var r = prompt(txt + " radicado " + name.substring(0, 14) + " necesita una observacion \n Por favor Escriba la Obsevacion", "");
            //alert( );
            //if (r == false)
            //return false;
	    document.getElementById(div).innerHTML = '<center><img  alt="Procesando" src="<?= $ruta_raiz; ?>/imagenes/loading.gif"></center>';
            var poststr = "action=<?php echo $dataDD; ?>subir&name=" + name + "&pages=" + pages + "&userOwn=" + user + "&pesoA=" + peso + "&tpdoc=" + tpdocS + "&comentario=" + r;
            url = "<?php echo $script; ?>";
            partes(url, div, poststr, '');

            }
function funlinkArchivo(numrad, rutaRaiz){
	var nombreventana = "linkVistArch";
	var url           = rutaRaiz + "../linkArchivo.php?<? echo session_name()."=".session_id()?>"+"&numrad="+numrad;
	var ventana       = window.open(url,nombreventana,'scrollbars=1,height=50,width=250');
	//setTimeout(nombreventana.close, 70);
	return;
}
        </script>
    <style>
    .ui-autocomplete-loading {
      background: white url("<?=$ruta_raiz?>/img/loading_16x16.gif") right center no-repeat;
    }
    </style>
</head>
    <body>
    <nav1><div id="navigation">
            <ul id="apps" class="">
                <li data-id="files_index"><a style="" href="orfeoCloud.php?tpAxion=ENTRADA" title=""  <?php echo $classE; ?>>Entrada</a>
                </li>
                <!-- <li data-id="files_index"><a style="" href="orfeoCloud.php?tpAxion=RADICADOS" title=""  <?php echo $classR; ?>>Radicados</a>
                </li> -->
                <li data-id="gallery_index"><a style="" href="orfeoCloud.php?tpAxion=ANEXOS" <?php echo $classA; ?> title="">Anexos</a>
                </li>
                <!--<li data-id="calendar_index"><a style="" href="orfeoCloud.php?tpAxion=info" <?php echo $classI; ?> title=""><span>Informe</span></a>
                </li>-->
                <!-- <li data-id="calendar_index"><a style="" href="orfeoCloud.php" title=""><span>Ajustes</span></a>
                </li> -->
            </ul>
        </div></nav1>

                            <!--[if IE 8]><style>input[type="checkbox"]{padding:0;}table td{position:static !important;}</style><![endif]-->
        <div id="notification" style="display: none;"></div>
        <?php
        if ($tpAxion == 'info') {
            ?>
            <table class="table table-bordered">
                <thead>
                <form action='' method="post" name='Informacion' class='form-smart'>
                    <tr>
                        <th id="headerName" style="width: 200px">

                            <span class="name">Fecha Inicio</span>
                        </th>
                        <th id="headerSize"> <input readonly="true" type="text" class="tex_area" name="fechaIni"	id="fechaIni" size="10" value="<?php echo $fechaIni; ?>" /> 
                            <script	language="javascript">
                                var A_CALTPL = {'imgpath': '<?= $ruta_raiz ?>/js/calendario/img/',
                                'months': ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                                'weekdays': ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'], 'yearscroll': true, 'weekstart': 1, 'centyear': 70}
                                new tcal({'formname': 'Informacion', 'controlname': 'fechaIni'}, A_CALTPL);
                            </script></th>
                        <th id="headerDate" style="width: 100px">
                            <span id="modified">Fecha Final</span></th>
                        <th id="headerDate" style="width: 100px">
                            <input readonly="true" type="text" class="tex_area" name="fechaFin"	id="fechaFin" size="10" value="<?php echo $fechaFin; ?>" /> 
                            <script	language="javascript">
                                new tcal({'formname': 'Informacion', 'controlname': 'fechaFin'}, A_CALTPL);
                            </script>	</th>
                        <th id="headerDate" style="width: 100px">
                            <input type="submit" value="Cosultar"></th>
                    </tr>
                </thead></table>
            <input type="hidden" name='tpAxion' value='info'>

            </form>
            <?
            echo $cloud->ListarRadSinImagenes(205, $fechaIni, $fechaFin);
            } else {
            ?>
            <table class='table table-bordered'>

                <?php
                if ($tpAxion == 'ANEXOS'){
						unset($ownUser);
						$ownUser[]=strtolower($krd);
	                    echo $cloud->ListarImagenes($ruta_owonclod, $ownUser, $carpS, strtolower($krd), $optionTp);
					}
                else{
					if ($_SESSION['usua_scan'] == 1)
						unset($ownUser);
						$ownUser[]=strtolower($krd);
      		            echo $cloud->ListarImagenesAvanzada($ruta_owonclod, $ownUser, $carpS, strtolower($krd), $optionTp);
                }
                ?>

            </table>
        <?php } 
//echo $ruta_owonclod;
?>
        <!-- config hints for javascript -->
        <input type="hidden" name="allowZipDownload" id="allowZipDownload" value="1" original-title="">

<input id="index-holder" type="hidden" size="100"/>  

<script>  

$("input[id^='tpDesc_']").focusin(function(){
    var indexx = $(this).attr("id");
     $("#index-holder").val(indexx);
    }
  );

var options = {
  url: "<?=$ruta_raiz?>/include/tx/json/getTiposDocumentales.php",
  getValue: "tpDescrip",
  list: {
    match: {
      enabled: true,
    },
     onSelectItemEvent: function(item) {
      var idElement=$("#index-holder").val(); 
      var idElementCodigo = idElement.replace("Desc","Codigo");

      var value = $("#"+idElement).getSelectedItemData().tpCodigo;
      $("#"+idElementCodigo).val(value).trigger("change");
    },
    
  },
  

  ajaxSettings: {
   dataType: "json",
   method: "POST",
   data: {
     dataType: "json",
     tpSearch: function(valor){
         var idElement=$("#index-holder").val();
         return $("#"+idElement).val();
       },
    }
  }
     
  
};  
    
$("input[id^='tpDesc_']").easyAutocomplete(options);




</script>
<br>


</body>
</html>

