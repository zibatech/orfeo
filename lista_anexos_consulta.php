<?php
/**
 * @author Cesar Augusto <aurigadl@gmail.com>
 * @author Jairo Losada  <jlosada@gmail.com>
 * @author Correlibre.org // Tomado de version orginal realizada por JL en SSPD, modificado.
 * @license  GNU AFFERO GENERAL PUBLIC LICENSE
 *
 * @copyleft

OrfeoGpl Models are the data definition of OrfeoGpl Information System
Copyright (C) 2020

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as published by
the Free Software Fou@copyrightndation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

session_start();
$_SESSION["usua_perm_root_email"] = "t";
$_SESSION["digitosDependencia"] = 5;
//$_SESSION["usua_doc"] = "10153900001";
if (!$ruta_raiz) $ruta_raiz= ".";
include "$ruta_raiz/conn.php";
include_once("$ruta_raiz/class_control/anexo.php");
require_once("$ruta_raiz/class_control/TipoDocumento.php");
include "$ruta_raiz/processConfig.php";
include_once("$ruta_raiz/include/db/ConnectionHandler.php");
include_once("$ruta_raiz/include/crypt/Crypt.php");
$ln = $_SESSION["digitosDependencia"];
$opt_ver_anexos_borrados = $_SESSION["opt_ver_anexos_borrados"];
$db = new ConnectionHandler("$ruta_raiz");
define('ADODB_ASSOC_CASE', 1);
$objTipoDocto  = new TipoDocumento($db);
$objTipoDocto->TipoDocumento_codigo($tdoc);
$num_archivos=0;
$anex = new Anexo($db);
$sqlFechaDocto = $db->conn->SQLDate("Y-m-D H:i:s A","a.sgd_fech_doc");
$sqlFechaAnexo = $db->conn->SQLDate("Y-m-D H:i:s A","a.anex_fech_anex");
//$sqlFechaAnexo = "to_char(anex_fech_anex, 'YYYY/DD/MM HH:MI:SS')";
$sqlSubstDesc =  $db->conn->substr."(anex_desc, 0, 100)";

$secret_key = "97SUP3RC0R33UDKA7128409EJA";
//include_once("include/query/busqueda/busquedaPiloto1.php");
//$db->conn->debug = true;
if(!empty($_POST['encrypt']) && !empty($_POST['rad'])){

    $encripted = $encrypted_string = encrypt_decrypt('encrypt',$_POST['rad'],$secret_key);
    die($encripted);
}
$decrypted_string = encrypt_decrypt('decrypt',$_REQUEST['radiNume'],$secret_key);

if(!empty($decrypted_string) && is_numeric($decrypted_string)){
    $verrad = $decrypted_string;
}else{
    $codigo='';
    $texto='No se encontraron anexos adicionales a los incluidos en el correo';
    require "errorTemplate.php";
    die("");
}

$db->limit(324);
$limitMsql = $db->limitMsql;
$limitOci8 = $db->limitOci8;
$limitPsql = $db->limitPsql;

$db->limit(1);
$limit2Oci8 = $db->limitOci8;
$limit2Psql = $db->limitPsql;
$verrad = trim($verrad);
$isql = "select $limitMsql a.anex_codigo AS DOCU
      		,at.anex_tipo_ext AS EXT
			,a.anex_tamano AS TAMA
			,a.anex_solo_lect AS RO
      		,usua_nomb AS CREA
			,$sqlSubstDesc AS DESCR
			,a.anex_nomb_archivo AS NOMBRE
			,a.ANEX_CREADOR
			,a.ANEX_ORIGEN
			,a.ANEX_SALIDA
			,$radi_nume_salida RADI_NUME_SALIDA
			,a.ANEX_ESTADO
			,a.SGD_PNUFE_CODI
			,a.SGD_DOC_SECUENCIA
			,SGD_DIR_TIPO
			,SGD_DOC_PADRE
			,a.SGD_TPR_CODIGO
			,a.SGD_TRAD_CODIGO
			,a.ANEX_TIPO
			,a.ANEX_FECH_ANEX AANEX_FECH_ANEX
			,a.ANEX_FECH_ANEX
			,a.ANEX_RADI_NUME
			,a.ANEX_TIPO_FINAL
			,a.ANEX_ENV_EMAIL
			,tpr.SGD_TPR_DESCRIP
			,$sqlFechaDocto FECDOC
			,$sqlFechaAnexo FEANEX
			,a.ANEX_TIPO NUMEXTDOC
		--,(SELECT d.sgd_dir_nomremdes from sgd_dir_drecciones d where (d.radi_nume_radi=a.anex_radi_nume) AND a.sgd_dir_tipo=d.sgd_dir_tipo  and a.anex_salida=1 limit 1) destino
      ,(SELECT d.sgd_dir_nomremdes from sgd_dir_drecciones d where (d.radi_nume_radi=a.radi_nume_salida) AND a.sgd_dir_tipo=d.sgd_dir_tipo and a.anex_salida=1  limit 1) destino_radicado
      ,rsal.radi_path PATH_RAD_SALIDA
		from  anexos_tipo at ,usuario u,
		anexos a 
		   left join radicado rsal           on (a.radi_nume_salida=rsal.radi_nume_radi)
		   left join sgd_tpr_tpdcumento tpr  on (a.sgd_tpr_codigo=tpr.sgd_tpr_codigo)
	  -- left join sgd_dir_drecciones dir  on ()
      where anex_radi_nume=$verrad and a.anex_tipo=at.anex_tipo_codi
       and a.anex_codigo like '$verrad%'
		   and a.anex_creador=u.usua_login and a.anex_borrado='N' $limitOci8
	   order by a.id, a.anex_codigo, a.ANEX_FECH_ANEX, sgd_dir_tipo,a.anex_radi_nume,a.radi_nume_salida $limitPsql";


$iSqlRadSalida = "SELECT count(*) NANEXOS FROM ANEXOS WHERE RADI_NUME_SALIDA='$verrad'";
//$db->conn->debug = true;

$rsAnexoSalida  = $db->conn->query($iSqlRadSalida);

$tieneAnexoSalida = 0;
if($rsAnexoSalida){
    $tieneAnexoSalida = $rsAnexoSalida->fields["NANEXOS"];
}

//Start::Validar si el memorando tienen al usuarrio

$tieneAsignacion = true;

//End::Validar si el memorando tienen al usuarrio

// case eliminado. No se entiende para qu se realiza.
?>


<html>
<head>
    <title>Documentacion de respuesta</title>
    <!-- <link rel="stylesheet" href="css/structure2.css" type="text/css" /> -->
</head>

<style type="text/css">
    body, html{
        position: absolute;
        width: 100%;
        min-width: 100%;
        max-width: 100%;
        height: 100%;
        min-height: 100%;
        max-height: 100%;
        margin: 0px;
        padding: 0px;
        box-sizing:  content-box;
        background-color: #3175af;
        font-size: 11px;
        font-family: Arial, Helvetica, sans-serif;
    }

    a{
        font-size: 12px;
        font-family: Arial, Helvetica, sans-serif;
        color: black;
        text-decoration: none;
    }

    .h1_estilo{
        background: transparent;
        height: auto;
        width: auto;
        color: black;
        font-weight: bold;
        font-size: larger;
        text-indent: 1px;
        text-align: center;
        display: none;
    }



    #obligado{
        color: #FF0000;
        font-weight: bold;
        text-align: center;
    }

    #centrar{
        text-align: center;
    }

    #botonayuda{
        background-image: url(./images/Help-icon-book.png);
        background-size: cover !important;
        border-color: transparent!important;
        background-repeat: no-repeat!important;
        width:6%;
        height:6%;
    }



    #tdstyle{
        width: 30%;
    }



    #tdtabla{
        width: 100%;
    }



    #tdancho{
        width: 50%;
    }



    #charNum{
        text-align: right;
    }



    fieldset{
        border: 0px solid black;
        padding: 0px;
        margin: 0px;
    }

    .errorClass{
        border: 1px solid red;
    }
    .form-pqrs-contImg{
        text-align:center;

        margin-top:20px;
    }

    .form-pqrs-contImg img{

    }

    .form-pqrs-input{
        width: 50%;
        min-height: 24px;
        box-sizing: border-box;
        border: 0px solid red;
        display: table-cell;
        float: left;
        vertical-align: top;
        text-align: left;
    }

    .form-pqrs-input img{
        margin: 0px;
    }

    .form-pqrs-label{
        width: 50%;
        min-height: 24px;
        box-sizing: border-box;
        border: 0px solid red;
        float: left;
        padding: 0px;
        padding-right: 5px;
        text-align: right;
        vertical-align: top;
    }

    .form-pqrs-label strong{
        font-weight: bold;
        color: #174572;
    }

    .form-pqrs-contInput{
        clear: both;
        margin: 0px auto;
        height: auto;
        width: 100%;
        display: table;
        border: 0px solid red;
        margin: 5px 0;
        box-sizing:  border-box;
    }

    .form-pqrs-cont{
        background-color: white;
        width: 60%;
        min-width: 60%;
        max-width: 60%;
        margin: 0 auto;
        border: 2px solid #3175af;
        border-radius: 10px;
        margin-top: 12px;
    }

    .form-pqrs-contInt{
        border:0px solid Gainsboro;
        border-left:0px solid Gainsboro;
        border-right:0px solid Gainsboro;
        border-radius:0px;
        margin:0px;
        padding:0px 5%;
        box-sizing: border-box;
    }

    .form-pqrs-contInt input:invalid , select:invalid , textarea:invalid{
        border:1px solid red;
    }

    .form-pqrs-contInt input[type="text"]:valid, select:valid, textarea:valid{
        border:1px solid green;
    }

    .imagenes{
        text-align: center;
    }
</style>
<body>
<div class="form-pqrs-cont">
    <div class="form-pqrs-contInt">
        <fieldset>
            <div CLASS="form-pqrs-contInput" >
                <div CLASS="form-pqrs-contImg">
                    <img alt="CRA" src="./bodega/sys_img/CRA.jpg" longdesc="http://www.example.com/description.txt"></img>
                </div>
                <br>
                <br>
                <p style='font-size:13px; text-align:justify;'>
                    Apreciado Usuario:<br/><br/>
                    La Corporación Autónoma Regional del Atlántico se permite gestionar la solicitud radicada con el número <?=$decrypted_string?>, para lo cual se evindencian los archivos correspondientes a su solicitud.
                </p>
            </div>
            <div class="form-pqrs-contInput" >
                <p style='font-size:13px; text-align:justify;'><br>
                <h2 style="text-align: center;">Documentos asociados</h2>

                <table WIDTH="100%" align="center" id="tableDocument" class="table" >
                    <thead>
                    <tr class="pr2">
                        <th style="display: flex; border: 0px;" >

                        </th>
                        <th width='1%'></th>
                        <th width='15%' ><center>Documento</center></th>
                        <th width='5%'> Tama&ntilde;o (Kb)</th>
                        <th width='20%'>Descripcion / Tipo</th>

                    </tr>
                    </thead>
                    <?php
                    include_once "$ruta_raiz/tx/verLinkArchivo.php";
                    $verLinkArchivo = new verLinkArchivo($db);
                    $rowan = array();
                    //$db->conn->debug = true;
                    $rs = $db->conn->query($isql);
                    if (!$ruta_raiz_archivo) $ruta_raiz_archivo = $ruta_raiz;
                    $directoriobase="$ruta_raiz_archivo/bodega/";
                    //Flag que indica si el radicado padre fue generado desde esta area de anexos
                    if(!empty($_REQUEST['radiNumeSalida'])){
                        $verrad = $_REQUEST['radiNumeSalida'];
                    }
                    $swRadDesdeAnex=$anex->radGeneradoDesdeAnexo($verrad);

                    if($rs){

                        $contadorImagenes = 0;

                        while(!$rs->EOF){
                            $aplinteg     	= $rs->fields["SGD_APLI_CODI"];
                            $numextdoc    	= $rs->fields["NUMEXTDOC"];
                            $tpradic      	= $rs->fields["SGD_TRAD_CODIGO"];
                            $tprDescrip   	= $rs->fields["SGD_TPR_DESCRIP"];
                            $coddocu      	= $rs->fields["DOCU"];
                            $origen       	= $rs->fields["ANEX_ORIGEN"];
                            $usuario      	= $rs->fields["ANEX_ORIGEN"];
                            $para_radicar 	= $rs->fields["ANEX_SALIDA"];
                            $pathRadSalida 	= $rs->fields["PATH_RAD_SALIDA"];
                            $radiNumeSalida = $rs->fields["RADI_NUME_SALIDA"];
                            $anexCarpeta 	= $rs->fields["ANEX_CARPETA"];
                            $anexCodigo 	= $rs->fields["DOCU"];
                            $anexTipoFinal 	= $rs->fields["ANEX_TIPO_FINAL"];


                            if ($rs->fields["ANEX_SALIDA"]==1 )	$num_archivos++;
                            $linkarchivo=$directoriobase.substr(trim($coddocu),0,4)."/".intval(substr(trim($radiNumeSalida?:$coddocu),4,$ln))."/docs/".trim($rs->fields["NOMBRE"]);
                            $linkarchivo_vista="$ruta_raiz/bodega/".substr(trim($coddocu),0,4)."/".intval(substr(trim($coddocu),4,$ln))."/docs/".trim($rs->fields["NOMBRE"])."?time=".time();
                            $linkarchivotmp=$directoriobase.substr(trim($coddocu),0,4)."/".intval(substr(trim($coddocu),4,$ln))."/docs/tmp".trim($rs->fields["NOMBRE"]);
                            if(!trim($rs->fields["NOMBRE"])) $linkarchivo = "";

                            if ($tpradic==''){
                                $tip_rest = substr($verrad,-1);
                                if ($tip_rest == 2){$tpradic = 1 ;}
                                else{$tpradic =$tip_rest;}
                            }
                            if ($db->entidad=="CRA"){
                                if ($tpradic!=''){
                                    $tip_rest = substr($verrad,-1);
                                    if ($tip_rest == 2){$tpradic = 1 ;}
                                    else{$tpradic =$tip_rest;}
                                }
                            }


                            $sql2 = "SELECT RADI_NUME_RADI AS RADI_NUME_RADI FROM SGD_RDF_RETDOCF r  WHERE RADI_NUME_RADI = '$radiNumeSalida'";
                            $rsq2=$db->conn->query($sql2);
                            $radiNumeroTrd = $rsq2->fields["RADI_NUME_RADI"];

                            $iSqlRadSalida2 = "SELECT ANEX_FECH_ENVIO FROM ANEXOS WHERE RADI_NUME_SALIDA='$radiNumeSalida'";
                            $rsAnexoSalida2  = $db->conn->query($iSqlRadSalida2);
                            $fechaEnvio = $rsAnexoSalida2->fields["ANEX_FECH_ENVIO"];

                            //Start::validar si el anexo es propio
                            $propio= 0;
                            if($rs->fields["ANEX_CREADOR"]== $krd)
                                $propio = 1;
                            //End::validar si el anexo es propio
                            ?>
                            <tr id="<?=$coddocu?>">
                                <?php

                                $cod_radi = ($rs->fields["RADI_NUME_SALIDA"]!=0)? $rs->fields["RADI_NUME_SALIDA"] : $coddocu;
                                $cod_radi_div = "<a class=\"vinculos\" href=\"#2\" onclick=\"funlinkArchivo('$coddocu','$ruta_raiz');\" id='codRadi$cod_radi'>$cod_radi</a>";
                                $anex_estado = $rs->fields["ANEX_ESTADO"];
                                if($anex_estado<=1) {$img_estado = "<img src='imagenes/docRecibido.gif' title='Se cargo un Archivo. . .'> "; }
                                if($anex_estado==2) {$img_estado = "<img src='imagenes/docRadicado.gif' title='Se Genero Radicado. . .'> "; }
                                if($anex_estado==3) {$img_estado = "<img src='imagenes/docImpreso.gif' title='Se Archivo Radicado y listo para enviar . . .'>"; }
                                if($anex_estado==4) {$img_estado = "<img src='imagenes/docEnviado.gif' title='Archivo Enviado. . .'>"; }
                                ?>
                                <TD height="21" > <font size=1>  </font> </TD>

                                <td width="1%" valign="middle"><font face="Arial, Helvetica, sans-serif">
                                        <?
                                        // Variables para visor modal
                                        if($origenVerradicado){
                                            if(!empty($pathRadSalida)){
                                                if(strpos($pathRadSalida,"/") != 0){
                                                    $pathRadSalida = "/".$pathRadSalida;
                                                }
                                                $linkImagen = "$ruta_raiz/bodega".$pathRadSalida;
                                            } else {
                                                $linkImagen = $linkarchivo;
                                            }

                                            // Valida la extension para tomar el PDF en caso de que exista el mismo archivo en version docx.
                                            $extensionLink = array_pop(explode(".",$linkImagen));
                                            $extensionNombre = array_pop(explode(".",$rs->fields["NOMBRE"]));
                                            if($extensionLink == "docx" &&  $extensionNombre == "pdf"){
                                                $linkImagen = str_replace("docx", "pdf", $linkImagen);
                                            }

                                            $contadorImagenes++;
                                        }

                                        $total_digitos = 11 + $ln;
                                        $ext = $rs->fields["EXT"];

                                        if (strlen($cod_radi) <= $total_digitos){
                                            //Se trata de un Radicado
                                            $resulVali = $verLinkArchivo->valPermisoRadi($cod_radi);
                                            $valImg = $resulVali['verImg'];
                                        }else{
                                            //Se trata de un Anexo sin Radicar
                                            $resulValiA = $verLinkArchivo->valPermisoAnex($coddocu);
                                            $valImg = $resulValiA['verImg'];
                                        }
                                        // Si hay un elemento definitivo,muestra el archivo definitivo.
                                        if($pathRadSalida and substr($pathRadSalida, -10)!=substr(str_replace("d.",".",$linkarchivo), -10)){
                                            //Se trata de un Radicado
                                            $resulValiRs = $verLinkArchivo->valPermisoRadi($radiNumeSalida);
                                            $valImgRs = $resulValiRs['verImg'];
                                            $extRadSalida = array_pop(explode(".",$pathRadSalida));

                                            if(($valImgRs == "SI" or $verradPermisos == "Full")  ){
                                                if($origenVerradicado && $extRadSalida == "pdf"){
                                                    //Muestra el pdf en el modal visor
                                                    echo "<b><a class='vinculos abrirVisor' href='javascript:void(0)' class='abrirVisor' contador=$contadorImagenes link=$linkImagen><img src='img/icono_$extRadSalida.jpg' title='Imagen $extRadSalida' width='25'></a>";
                                                } else {
                                                    echo "<b><a class=\"vinculos\" href=\"#2\" onclick=\"funlinkArchivo('$radiNumeSalida','$ruta_raiz');\"><img src='img/icono_$extRadSalida.jpg' title='Imagen $extRadSalida' width='25'> </a>";
                                                }
                                            }else{
                                                echo "<a  href='javascript:noPermiso()' class=\"vinculos\" ><img src='img/icono_$extRadSalida.jpg' title='Imagen $extRadSalida' width='25'> </a>";
                                            }
                                        }

                                        echo "</td><td>";
                                        if($valImg == "SI" or $verradPermisos == "Full" ){
                                            if($origenVerradicado && $ext == "pdf"){
                                                //Muestra el pdf en el modal visor
                                                echo "<b><a class='vinculos abrirVisor' href='javascript:void(0)' contador=$contadorImagenes link=$linkImagen>
            		<img src='img/icono_$ext.jpg' title='$ext' width='25'>
            		<a class='vinculos abrirVisor' href='javascript:void(0)' contador=$contadorImagenes link=$linkImagen>
            			$cod_radi
            		</a>
            	</a>";
                                            } else {
                                                echo "<b><a class=\"vinculos\" href=\"#2\" onclick=\"funlinkArchivo('$coddocu','$ruta_raiz');\"><img src='img/icono_$ext.jpg' title='$ext' width='25'> $cod_radi_div </a>";
                                            }
                                        }else{
                                            echo "<a class='vinculos' href='javascript:noPermiso()' > $cod_radi_div </a>";
                                        }

                                        //Modal Visor
                                        $visorId = "visor_".$contadorImagenes;
                                        echo "<div id=$visorId style='display:none; 
            position:fixed;
            padding:26px 30px 30px;
            top:0;
            left:0;
            right:0;
            bottom:0;
            z-index:2'>
            <button class='cerrarVisor' type='button' style='float:right; background-color:red;' contador=$contadorImagenes><b>x</b></button>  
            <!--iframe></iframe-->
          </div>";

                                        // si es va mostrar el definitivo del radicado de Salida

                                        //Codigo solo para la migracion de acapella ---- Fin ----
                                        //if($valImg == "SI" or $verradPermisos == "Full" ){
                                        //echo "<b><a class=\"vinculos\" href=\"#2\" onclick=\"funlinkArchivo('$coddocu','$ruta_raiz');\"> $cod_radi </a>";
                                        //}else{
                                        //echo "<a class='vinculos' href='javascript:noPermiso()' > $cod_radi </a>";
                                        //}
                                        ?>
                                        <?php



                                        $no_es_impreso = $rs->fields["ANEX_ESTADO"] <= 3;
                                        $es_extension = ($rs->fields["EXT"]=="rtf" or
                                            $rs->fields["EXT"]=="doc" or
                                            $rs->fields["EXT"]=="docx" or
                                            $rs->fields["EXT"]=="odt" or
                                            $rs->fields["EXT"]=="xml") and $no_es_impreso;

                                        if($es_extension) {
                                           /* if($valImg == "SI"){
                                                echo"<a class=\"vinculos\" style='cursor:pointer;cursor:hand;' onclick=\"vistaPreliminar('$coddocu','$linkarchivo','$linkarchivotmp');\">";
                                            }else{
                                                echo "<a class='vinculos' style='cursor:pointer;cursor:hand;' href='javascript:noPermiso()' >";
                                            }

                                            echo "<span class='glyphicon glyphicon-search'></span>\n";
                                            echo "<font face='Arial, Helvetica, sans-serif' class='etextomenu'>\n";
                                            echo "</a>\n";*/
                                            $radicado = "false";
                                            $anexo = $cod_radi;
                                        }

                                        if($rs->fields["DESTINO_RADICADO"]) $destino = $rs->fields["DESTINO_RADICADO"];
                                        else $destino = $rs->fields["DESTINO"];
                                        ?>
                                    </font>
                                </TD>
                                <td><font size=1><?=$rs->fields["TAMA"]?></font></td>
                                <td><font size=1><?=$rs->fields["DESCR"]?><br><?=$tprDescrip?></font></td>
                                <td ><font size=1>
                                        <?php
                                        $es_pdf = $rs->fields["EXT"] == "pdf";
                                        $anexEnvEmail  = $rs->fields["ANEX_ENV_EMAIL"];
                                        $ruta_archivo_txt = 'bodega/' . substr($rs->fields["DOCU"], 0, 4) . '/' .
                                            substr($rs->fields["DOCU"], 4, 3) . '/docs/' .
                                            $rs->fields["DOCU"] . '.txt';

                                        $existe_txt = file_exists($ruta_archivo_txt);

                                        if($origen!=1 and $linkarchivo  and $verradPermisos == "Full" ) {
                                            $no_esta_enviado = $anex_estado < 4;

                                            if ($no_esta_enviado and $anexEnvEmail !=1) {

                                                if ($es_pdf && $para_radicar && ($existe_txt || $anexTipoFinal>=0)) {
                                                    echo "<a class='vinculos' href=javascript:editar_anexo('$coddocu')><img src='img/icono_modificar.png' title='Modificar Archivo'></a> ";
                                                } else {
                                                    echo "<a class='vinculos' href=javascript:verDetalles('$coddocu','$tpradic','$aplinteg')><img src='img/icono_modificar.png' title='Modificar Archivo'></a> ";
                                                }

                                                if(strlen($cod_radi)<=17){
                                                    echo "<a href='./radicacion/NEW.php?nurad=$cod_radi&Buscar=BuscarDocModUS&".session_name()."=".session_id()."&Submit3=ModificarDocumentos&Buscar1=BuscarOrfeo78956jkgf' notborder ><img src='img/icono_modificar_radicado.png' title='Modificar Datos/Remitentes del Radicado'></a> ";
                                                }

                                            }

                                        }

                                        echo "</font>\n";
                                        echo "</small></td>\n";

                                        //Estas variables se utilizan para verificar si se debe mostrar la opcion de tipificacion de anexo .TIF
                                        $anexTipo = $rs->fields["ANEX_TIPO"];
                                        $anexTPRActual = $rs->fields["SGD_TPR_CODIGO"];

                                        if ($verradPermisos == "Full") {
                                        ?>
                                <td>
                                    <?php
                                    $radiNumeAnexo = $rs->fields["RADI_NUME_SALIDA"];


                                    if($radiNumeAnexo>0 and trim($linkarchivo) ) {
                                        if(!$codserie) $codserie="0";
                                        if(!$tsub) $tsub="0";
                                        echo "<a class=\"vinculos\" href=\"javascript:ver_tipodocuATRD('$radiNumeAnexo',$codserie,$tsub)\";><img src='img/icono_clasificar.png' title='Clasificar Documento'></a> ";

                                        $inact = "javascript:sendMail('$anexCodigo')"; $glyphi='';


                                        $visualizar=false;
                                        if ($radiNumeroTrd  && $visualizar && ($anex_estado==2 || $anex_estado==3)){ // grupo de notificaciones
                                            $variMail = true;
                                            $sql_mailDestino="SELECT
										sgd_dir_drecciones.SGD_DIR_MAIL
									FROM
										sgd_dir_drecciones
										INNER JOIN
										sgd_rad_envios
										ON 
											sgd_dir_drecciones.id = sgd_rad_envios.id_direccion
										INNER JOIN
										anexos
										ON 
											anexos.id = sgd_rad_envios.id_anexo
									WHERE
										sgd_rad_envios.tipo = 'E-mail' and sgd_rad_envios.estado = 1 and sgd_dir_drecciones.radi_nume_radi ='$radiNumeAnexo'";


                                            $emails_destino=$db->conn->getAll($sql_mailDestino);
                                            $email_prueba = implode(';', array_map(function ($entry) {
                                                return $entry['SGD_DIR_MAIL'];
                                            }, $emails_destino));
                                            $varios_mail = explode(";", $email_prueba);
                                            $mal_correos="";

                                            if($email_prueba!=''){
                                                foreach ($varios_mail as $key => $rmail) {
                                                    # code...
                                                    $rmail = trim ($rmail);
                                                    $rmail =  preg_replace('/(?<=.).(?=.*@)/', '*', $rmail);
                                                    $result = (false !== filter_var($rmail, FILTER_VALIDATE_EMAIL));
                                                    $dominio =array_pop(explode("@",$rmail));

                                                    if ($result) {

                                                        /*if(checkdnsrr($dominio, "A")
                                                        || checkdnsrr($dominio, "AAAA")
                                                        || checkdnsrr($dominio, "A6")){*/
                                                        $variMail = true;
                                                        /*}else{
                                                            $variMail = false;
                                                            $mal_correos=$mal_correos." - ".$rmail;
                                                        } supersalud.gov.co no es comptatible dns*/
                                                    }else{
                                                        $variMail = false;
                                                        $mal_correos=$mal_correos." * ".$rmail;
                                                    }

                                                }

                                                if($variMail){
                                                    echo "<a class=\"vinculoTipifAnex\" href=\"$inact\";> <img src='img/sendMail.png' width='30' title='Enviar @mail $email_prueba'><span id=\"span-$anexCodigo\" class=\"glyphicon $glyphi\"></span> </a> ";
                                                }else{
                                                    echo "<a class=\"vinculoTipifAnex\" href=\"javascript:void(0)\";> <img src='img/sendMailMal.png' width='30' title='Error: verifique sus correos ($mal_correos)'><span id=\"span-$anexCodigo\" class=\"glyphicon glyphicon-exclamation-sign\"></span> </a> ";
                                                }
                                            }
                                        }

                                    }elseif ($perm_tipif_anexo == 1 && $anexTipo == 4 && $anexTPRActual == ''){ //Es un anexo de tipo tif (4) y el usuario tiene permiso para Tipificar, ademas el anexo no ha sido tipificado
                                        if(!$codserie) $codserie="0";
                                        if(!$tsub) $tsub="0";
                                        echo "<a class=vinculoTipifAnex href=javascript:ver_tipodocuAnex('$cod_radi','$anexo',$codserie,$tsub);> <img src='img/icono_clasificar.png' title='Clasificar Documento'> </a> ";
                                    }elseif ($perm_tipif_anexo == 1 && $anexTipo == 4 && $anexTPRActual != ''){ //Es un anexo de tipo tif (4) y el usuario tiene permiso para Tipificar, ademas el anexo YA ha sido tipificado antes
                                        if(!$codserie) $codserie="0";
                                        if(!$tsub) $tsub="0";
                                        echo "<a class=vinculoTipifAnex href=javascript:ver_tipodocuAnex('$cod_radi','$anexo',$codserie,$tsub);> <img src='img/icono_clasificar.png' title='Volver a Clasificar Documento'> </a> ";
                                    }
                                    ?>
                                    </small></td>

                                <td>

                                    </small></td>

                            <?
                            }else {

                            }
                            echo "";
                            ?>

                            </tr>
                            <?php
                            $rs->MoveNext();
                        } // close while
                    }

                    ?>

                </table>


                <table  width="100%" align="center" class="table-bordered table-striped table-condensed table-hover smart-form has-tickbox">
                    <tr align="center">
                        <td ><small></small></td>
                    </tr>
                </table>

                <br><br>
                </p>


                <!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
                <br><br>
                <div>
                </div>
                <!-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->


            </div>
        </fieldset>
    </div>
</div>
</body>
</html>

<link rel="stylesheet" href="estilos/bootstrap/css/bootstrap.min.css" media="screen">
<link rel="stylesheet" href="estilos/bootstrap/css/bootstrap-theme.min.css" media="screen"> <!-- Optional theme -->

<style type="text/css">
    body, html{
        position:absolute; width:100%; min-width:100%; max-width:100%; height:100%; min-height:100%; max-height:100%;
        margin:0px; padding:0px; box-sizing:content-box; background-color:#3175af;
    }

    .h1_estilo{background:transparent; height:auto; width:auto; color:black; font-weight:bold; font-size:larger; text-indent:1px; text-align:center; display:none;}

    #obligado{color:#FF0000; font-weight:bold; text-align:center;}

    #centrar{text-align:center;}

    #botonayuda{
        background-image:url(./images/Help-icon-book.png); background-size:cover!important; border-color:transparent!important;
        background-repeat:no-repeat!important; width:6%; height:6%;
    }

    #tdstyle {width:30%;}

    #tdtabla{width:100%;}

    #tdancho{width:50%;}

    #charNum{text-align:right;}

    .fieldset{border:0px solid black; padding:0px; margin:0px;}

    .errorClass {border:1px solid red;}

    select, input, textarea {border-radius:5px; border:1px solid #2A7891; min-height:20px; width:90%; min-width:90%; max-width:90%;}

    input[type="submit"], input[type="reset"]{width:100px; min-width:100px; max-width:100px;}

    @-webkit-keyframes mostrarAnimadoMensaje{from{//height:0px; background:rgba(255, 255, 255, 0.1);} to{//height:800px; background:rgba(255, 255, 255, 1);}}

    @-webkit-keyframes mostrarAnimadoMensaje{from{//height:0px; background:rgba(255, 255, 255, 0.1);} to{//height:800px; background:rgba(255, 255, 255, 1);}}

    @keyframes cerrarAnimadoMensaje{from{height:90px width:100%;} to{height:20px; width:10%;}}

    .form-pqrs-contImg img{width:30%; max-width:70%;}

    .form-pqrs-input{width:50%; min-height:24px; box-sizing:border-box; border:0px solid red; display:table-cell; float:left; vertical-align:top; text-align:left;}

    .form-pqrs-input img{margin: 0px;}

    .form-pqrs-label{width:50%; min-height:24px; box-sizing:border-box; border:0px solid red; float:left; padding:0px; padding-right:5px; text-align:right; vertical-align:top;}

    .form-pqrs-label strong{font-weight:bold; color:#174572;}

    .form-pqrs-contInput{clear:both; margin:0px auto; height:auto; width:100%; display:table; border:0px solid red; margin:5px 0; box-sizing:border-box;}

    .form-pqrs-cont{background-color:white; width:60%; min-width:60%; max-width:60%; margin:0 auto; border:2px solid #3175af; border-radius:10px; margin-top:12px;}

    .form-pqrs-contInt{
        border:0px solid Gainsboro; border-left:0px solid Gainsboro; border-right:0px solid Gainsboro;
        border-radius:0px; margin:0px; padding:0px 5%; box-sizing:border-box;
    }

    .form-pqrs-contInt input:invalid, select:invalid, textarea:invalid{border:1px solid red;}

    .form-pqrs-contInt input[type="text"]:valid, select:valid, textarea:valid{border: 1px solid green;}

    .imagenes{text-align:center;}

    .registros{margin:auto;}

    .modal-dialog{width:50%;}

    .modalDialog{
        position:fixed; font-family:Arial, Helvetica, sans-serif; top:0; right:0; bottom:0; left:0; background:rgba(0,0,0,0.5); z-index:99999; opacity:0;
        -webkit-transition:opacity 400ms ease-in; -moz-transition:opacity 400ms ease-in; transition:opacity 400ms ease-in; pointer-events:none;
    }

    .modalDialog:target{opacity:1; pointer-events:auto;}

    .modalDialog > div{
        width:500px; position:relative; margin:10% auto; padding:5px 20px 13px 20px; border-radius:10px; background:#fff;
        background:-moz-linear-gradient(#fff, #999); background:-webkit-linear-gradient(#fff, #999); background:-o-linear-gradient(#fff, #999);
    }

    .close{
        background:#00a9cc; color:#FFFFFF; line-height:25px; position:absolute; right:-12px; text-align:center; top:-10px; width:24px; text-decoration:none; font-weight:bold;
        -webkit-border-radius:12px; -moz-border-radius:12px; border-radius:12px; -moz-box-shadow: 1px 1px 3px #000; -webkit-box-shadow:1px 1px 3px #000; box-shadow: 1px 1px 3px #000;
    }

    .close:hover{border:1px #fff; color:#000; background:#fff;}

    .buttoAlertError{
        border:solid 1px #e6e6e6; border-radius:3px; -moz-border-radius:3px; -webkit-box-shadow:0px 0px 2px rgba(0,0,0,1.0); -moz-box-shadow:0px 0px 2px rgba(0,0,0,1.0);
        box-shadow:0px 0px 2px rgba(0,0,0,1.0); font-size:20px; color:#fff; padding:1px 17px; background:#F50000; cursor:pointer; text-align:center;
    }

    .buttoAlertError:hover{opacity:0.8;}
</style>




<script language="javascript">
    $(document).ready(function() {
        $('.abrirVisor').click(function(){
            var contador = $(this).attr('contador');
            var link = $(this).attr('link');
            var visorId = "#visor_" + contador;
            var visorRequest = new Request(link);

            //Valida primero que el archivo exista y se pueda abrir.
            fetch(visorRequest).then(function(response) {
                if(response.status == 200){
                    $(visorId ).append("<iframe style='width:100%; height:100%; z-index:-2;' src=" + link + "></iframe>");
                    $(visorId).dialog();
                } else {
                    visorError(visorId);
                }
            });
        });

        $('.cerrarVisor').click(function(){
            var visorId = "#visor_" + $(this).attr('contador');
            $(visorId).dialog('destroy');
        });
    });

    function visorError(visor) {
        var title   = "Imagen documento no encontrado.";
        var tagalert = $( "<div>" ).addClass("alert alert-block")
            .html("<a class='close' data-dismiss='alert' href='#'>×</a>" +
                "<h4 class='alert-heading'><i class='fa fa-check-square-o'></i></h4>");
        newalert = tagalert.clone();
        newalert.find('h4').html(title);
        newalert.removeClass('alert-success');
        newalert.addClass('alert-danger');
        $('div').remove('.alert'); //Para eliminar la alerta en caso de que se haya hecho click de nuevo en el mismo documento inexistente.
        $(visor).after(newalert);
    }

    $( "#clickme" ).click(function() {
        $( "#wrap_delete" ).toggle( "slow" );
    });

    var swradics  = 0;
    var radicando = 0;


    function verDetalles(anexo, tpradic, aplinteg, num){
        optAsigna = "";
        if (swradics==0){
            optAsigna="&verunico=1";
        }
        contadorVentanas=contadorVentanas+1;
        nombreventana="ventanaDetalles"+contadorVentanas;
        url="detalle_archivos.php?usua=<?=$krd?>&radi=<?=$verrad?>&anexo="+anexo+"&anex_codigo="+anexo;
        url="<?=$ruta_raiz?>/nuevo_archivo.php?codigo="+anexo+"&<?=session_name()."=".trim(session_id()) ?>&radSalida=<?=$radSalida?>&<?=$verrad?>&contra=<?=$drde?>&radi=<?=$verrad?>&tipo=<?=$tipo?>&ent=<?=$ent?><?=$datos_envio?>&ruta_raiz=<?=$ruta_raiz?>"+"&tpradic="+tpradic+"&aplinteg="+aplinteg+optAsigna;
        window.open(url,nombreventana,'top=0,height=780,width=870,scrollbars=yes,resizable=yes');
        return;
    }


    function noPermiso(){
        alert ("No tiene permiso para acceder");
    }

    function ver_tipodocuAnex(cod_radi,codserie,tsub)
    {

        window.open("./radicacion/tipificar_anexo.php?krd=<?=$krd?>&nurad="+cod_radi+"&ind_ProcAnex=<?=$ind_ProcAnex?>&codusua=<?=$codusua?>&coddepe=<?=$coddepe?>&tsub="+tsub+"&codserie="+codserie,"Tipificacion_Documento_Anexos","height=300,width=750,scrollbars=yes");
    }

    /*function sendMail(cod_radi,radPadre,codAnexo,tsub)
    {
        window.open("./respuestaRapida/sendMail.php?nurad="+cod_radi+"&coddepe=<?=$coddepe?>&radPadre="+radPadre+"&codAnexo="+codAnexo+"&tsub=","...:::: Envio de Mail :::...","height=500,width=800,top=200,left=100,scrollbars=yes");
	}*/

    function sendMail(codigo){
        $.ajax({
            url:   'envios/responseEnvioE-mail.php',
            type:  'post',
            data: {
                "codigo":codigo,
                "anexo_radicado":'true'
            },
            dataType: 'json',
            beforeSend: function () {
                $("#span-"+codigo).removeClass("glyphicon-send");
                $("#span-"+codigo).addClass("glyphicon-repeat");
            },
            success:  function (response) {
                $("#span-"+codigo).removeClass("glyphicon-repeat");
                if (response["success"]==true){
                    $("#span-"+codigo).removeClass("glyphicon-remove");
                    $("#span-"+codigo).addClass("glyphicon-ok");
                }
            }
        });
    }


    function vistaPreliminar(anexo,linkarch,linkarchtmp){
        var tagalert = $( "<div>" ).addClass("alert alert-block")
            .html("<a class='close' data-dismiss='alert' href='#'>×</a>" +
                "<h4 class='alert-heading'><i class='fa fa-check-square-o'></i></h4>");

        var title1   = "Transaccion exitosa";

        url  =  "<?=$ruta_raiz?>/genarchivo.php?vp=s&<?="krd=$krd&".session_name()."=".trim(session_id()) ?>&radicar_documento=<?=$verrad?>&numrad=<?=$verrad?>&anexo="+anexo+"&linkarchivo="+linkarch+"&linkarchivotmp="+linkarchtmp+"<?=$datos_envio?>"+"&ruta_raiz=<?=$ruta_raiz?>";
        $('#tableDocument').addClass('widget-body-ajax-loading');
        $.post( url, function( data ) {

            if((data.success !== undefined) && (data.success.length>0)){
                var answer = content = '';
                for(var i=0;i < data.success.length; i++){
                    var data_answer = " "+data.success[i]+" ";
                    answer  = (answer.length>0)? answer + data_answer : data_answer ;
                    radinum = data_answer.match(/[\d]{14}/);
                    if((radinum !== undefined)  && (Array.isArray(radinum)) && (radinum.length > 0)){
                        radinum = radinum[0];
                    }
                }
                content  = $("<div></div>").html(answer);
                newalert = tagalert.clone();
                newalert.find('h4').html(title1);
                newalert.addClass('alert-success');
                newalert.removeClass('alert-danger');
                newalert.find('h4').after(content);
                var tdalert = $('<td colspan="16">').append(newalert);
                var tralert = $('<tr>').append(tdalert);
                $('#' + anexo).after(tralert);

                if(radinum){
                    $($('#' + anexo).find('td')[2]).children().children().text(radinum);
                }
            }

            if((data.error !== undefined) && (data.error.length>0)){
                var answer  = '';
                var content = '';
                for(var i=0;i < data.error.length; i++){
                    var data_answer = " "+data.error[i]+" ";
                    answer = (answer.length>0)? answer + data_answer: data_answer ;
                }
                content  = $("<div></div>").html(answer);
                newalert = tagalert.clone();
                newalert.find('h4').html(title2);
                newalert.find('h4').after(content);
                newalert.addClass('alert-danger');
                newalert.removeClass('alert-success');
                var tdalert = $('<td colspan="16">').append(newalert);
                var tralert = $('<tr>').append(tdalert);
                $('#' + anexo).after(tralert);
            }
        });
    }

    function nuevoArchivo(asigna){
        contadorVentanas=contadorVentanas+1;
        optAsigna="";
        if (asigna==1){
            optAsigna="&verunico=1";
        }

        nombreventana="ventanaNuevo"+contadorVentanas;
        url="<?=$ruta_raiz?>/nuevo_archivo.php?codigo=&<?="krd=$krd&".session_name()."=".trim(session_id()) ?>&usua=<?=$krd?>&numrad=<?=$verrad ?>&contra=<?=$drde?>&radi=<?=$verrad?>&tipo=<?=$tipo?>&ent=<?=$ent?>"+"<?=$datos_envio?>"+"&ruta_raiz=<?=$ruta_raiz?>&tdoc=<?=$tdoc?>&nuevo_archivo=true"+optAsigna;
        window.open(url,nombreventana,'height=730,width=840,scrollbars=yes,resizable=yes');
        return;
    }


    function nuevoEditWeb(asigna){
        contadorVentanas=contadorVentanas+1;
        optAsigna="";
        if (asigna==1){
            optAsigna="&verunico=1";
        }

        nombreventana="ventanaNuevo"+contadorVentanas;
        url="<?=$ruta_raiz?>/edicionWeb/editorWeb.php?codigo=&<?="krd=$krd&".session_name()."=".trim(session_id()) ?>&usua=<?=$krd?>&numrad=<?=$verrad ?>&contra=<?=$drde?>&radi=<?=$verrad?>&tipo=<?=$tipo?>&ent=<?=$ent?>"+"<?=$datos_envio?>"+"&ruta_raiz=<?=$ruta_raiz?>&tdoc=<?=$tdoc?>"+optAsigna;
        window.open(url,nombreventana,'height=800,width=700,scrollbars=yes,resizable=yes');
        return;
    }

    function Plantillas(plantillaper1){
        if(plantillaper1==0){
            plantillaper1="";
        }
        contadorVentanas=contadorVentanas+1;
        nombreventana="ventanaNuevo"+contadorVentanas;
        urlp="plantilla.php?<?="krd=$krd&".session_name()."=".trim(session_id()); ?>&verrad=<?=$verrad ?>&numrad=<?=$numrad ?>&plantillaper1="+plantillaper1;
        window.open(urlp,nombreventana,'top=0,left=0,height=800,width=850');
        return;
    }

    function Plantillas_pb(plantillaper1){
        if(plantillaper1==0){
            plantillaper1="";
        }
        contadorVentanas=contadorVentanas+1;
        nombreventana="ventanaNuevo"+contadorVentanas;
        urlp="crea_plantillas/plantilla.php?<?="krd=$krd&".session_name()."=".trim(session_id()); ?>&verrad=<?=$verrad ?>&numrad=<?=$numrad ?>&plantillaper1="+plantillaper1;
        window.open(urlp,nombreventana,'top=0,left=0,height=800,width=850');
        return;
    }

    function respuestaTx2(){
        var valor = sw = 0;
        var params      = 'width='+screen.width;
        params      += ', height='+screen.height;
        params      += ', top=0, left=0'
        params      += ', scrollbars=yes'
        params      += ', fullscreen=yes';

        <?if(!$verrad){?>
        for(i=1;i<document.form1.elements.length;i++){
            if (document.form1.elements[i].checked && document.form1.elements[i].name!="checkAll"){
                sw++;
                valor = document.form1.elements[i].name;
                valor = valor.replace("checkValue[", "");
                valor = valor.replace("]", "");
            }
        }

        if (sw != 1) {
            alert("Debe seleccionar UN(1) radicado");
            return;
        }


        var url         = "respuestaRapida/index.php?<?=session_name()?>=" +
            "<?=session_id()?>&radicadopadre=" +
            + valor + "&krd=<?=$krd?>&editar=false";
        window.open(url, "Respuesta Rapida", params);

        <?}else{?>
        window.open("respuestaRapida/index.php?<?=session_name()?>=<?=session_id()?>&radicado=" +
            '<?php print_r($verrad) ?>' + "&radicadopadre=" + '<?php print_r($verrad) ?>' +
            "&asunto=" + '<?php print_r($rad_asun_res)?>' +
            "&krd=<?=$krd?>&editar=false", "Respuesta Rapida", params);
        <?}?>
    }

    function funlinkArchivo(numrad,rutaRaiz){
        nombreventana="linkVistArch"+numrad;
        url=rutaRaiz + "/linkArchivo.php?"+"&PHPSESSID=140522122803o127001ADMON&numrad="+numrad;
        //url=rutaRaiz + "/linkArchivo.php?"+numrad;
        ventana = window.open(url,nombreventana,'scrollbars=1,height=500,width=500');

        //setTimeout(nombreventana.close, 70);
        return;
    }

    function noPermiso(){
        alert ("No tiene permiso para acceder");
    }

    function abrirArchivo(url){
        nombreventana='Documento';
        window.open(url, nombreventana,  'status, width=900,height=500,screenX=100,screenY=75,left=50,top=75');
        return;
    }

</script>

