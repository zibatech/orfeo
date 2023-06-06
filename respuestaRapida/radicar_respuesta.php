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
  define ('ENVIO_EMAIL', '1');
  define ('COLOMBIA',     170);

  foreach ($_GET as $key => $valor)   ${$key} = $valor;
  foreach ($_POST as $key => $valor)   ${$key} = $valor;

  require_once($ruta_raiz."/processConfig.php");
  require_once($ruta_raiz."/include/db/ConnectionHandler.php");
  include_once($ruta_raiz."/class_control/anexo.php");
  include_once($ruta_raiz."/class_control/anex_tipo.php");
  include_once($ruta_raiz."/include/tx/Tx.php");
  include_once($ruta_raiz."/include/tx/Radicacion.php");
  include_once($ruta_raiz."/class_control/Municipio.php");
  include_once($ruta_raiz."/include/PHPMailer_v5.1/class.phpmailer.php");
  require_once($ruta_raiz."/tcpdf/config/lang/eng.php");
  require_once($ruta_raiz."/conf/configPHPMailer.php");
  require_once($ruta_raiz."/tcpdf/tcpdf.php");
  require_once($ruta_raiz."/tcpdf/2dbarcodes.php");
  require_once($ruta_raiz."/tcpdf/tcpdf_barcodes_1d.php");

  $db      = new ConnectionHandler($ruta_raiz);
  $hist    = new Historico($db);
  $Tx      = new Tx($db);
  $anex    = new Anexo($db);
  $anexTip = new Anex_tipo($db);
  $mail    = new PHPMailer(true);

  // Es necesario liberar la variable ya que este se utilizara mas adelante como clase
  unset($anexo);

  $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

  $numRadicadoPadre = $_POST["radPadre"];
  if($editar==false) {
     $auxnumero = $anex->obtenerMaximoNumeroAnexo($numRadicadoPadre);
     $auxnumero++;
  }

  $anexTip->anex_tipo_codigo(7);
  $sqlFechaHoy      = $db->conn->OffsetDate(0, $db->conn->sysTimeStamp);

  $tamanoMax      = 7 * 1024 * 1024; // 7 megabytes
  $fechaGrab      = trim($date1);
  $numramdon      = rand (0,100000);
  $contador       = 0;
  $regFile        = array();
  $conCopiaA      = '';
  $enviadoA       = '';
  $cCopOcu        = '';

  $ddate          = date('d');
  $mdate          = date('m');
  $adate          = date('Y');
  $fechproc4      = substr($adate,2,4);
  $fecha1         = time();
  $fecha          = fechaFormateada($fecha1);

  //DATOS A VALIDAR EN RADICADO //
  $tdoc           = NO_DEFINIDO;
  $tipo_radicado  = (isset($_POST['tipo_radicado']))? $_POST['tipo_radicado'] : null;
  $pais           = COLOMBIA; //OK, codigo pais
  $cont           = 1;        //id del continente
  $radicado_rem   = 7;
  $auxnumero      = str_pad($auxnumero, 5, "0", STR_PAD_LEFT);
  $tipo           = ARCHIVO_PDF;
  $tamano         = 1000;
  $auxsololect    = 'N';
  $radicado_rem   = 1;
  $descr          = 'Pdf respuesta';
  $fechrd         = $ddate.$mdate.$fechproc4;
  $coddepe        = $_SESSION["dependencia"] * 1 ;
  $usua_actu      = $_SESSION["codusuario"];
  $usua           = $_SESSION["krd"];
  $codigoCiu      = $_SESSION["usua_doc"];
  $ln             = $_SESSION["digitosDependencia"];

  $usMailSelect   = $_POST['usMailSelect'];   //correo del emisor de la respuesta
  $destinat       = $_POST["destinatario"];   //correos de los destinanexnexnexnexnexnexnextarios
  $correocopia    = $_POST["concopia"];       //destinatarios con copia
  $conCopOcul     = $_POST["concopiaOculta"]; //con copia oculta
  $anexHtml       = $_POST["anexHtml"];       //con copia oculta
  $docAnex        = $_POST["docAnex"];        //con copia oculta
  //$medioRadicar   = ENVIO_EMAIL;   //con copia oculta
  $asu            = $_POST["respuesta"];
  $tpDepeRad      = $coddepe;
  $radUsuaDoc     = $codigoCiu;
  $usua_doc       = $_SESSION["usua_doc"];
  $usuario        = $_SESSION["usua_nomb"];
  $setAutor       = 'Sistema de Gestion Documental Orfeo';
  $SetTitle       = 'Respuesta a solicitud';
  $SetSubject     = 'Metrovivienda';
  $SetKeywords    = 'metrovivienda, respuesta, salida, generar';

  //DATOS EMPRESA
  $sigla          = 'null';
  $iden           = $db->conn->nextId("sec_ciu_ciudadano");//uniqe key

  //ENLACE DEL ANEXO
  $radano = substr($numRadicadoPadre,0,4);
  $desti = "SELECT
              s.sgd_dir_nomremdes,
              s.sgd_dir_direccion,
              s.sgd_dir_tipo,
              s.sgd_dir_mail,
              s.sgd_dir_telefono,
              s.sgd_sec_codigo,
              r.depe_codi,
              r.radi_path
          FROM
              SGD_DIR_DRECCIONES s,
              RADICADO r
          WHERE
              r.RADI_NUME_RADI     = $numRadicadoPadre
              AND s.RADI_NUME_RADI = r.RADI_NUME_RADI";

  $rssPatth       = $db->conn->Execute($desti);
  $dir_nombre     = $rssPatth->fields["sgd_dir_nomremdes"];
  $dir_tipo       = $rssPatth->fields["sgd_dir_tipo"];
  $dir_mail       = $rssPatth->fields["sgd_dir_mail"];
  $dir_telefono   = $rssPatth->fields["sgd_dir_telefono"];
  $dir_direccion  = $rssPatth->fields["sgd_dir_direccion"];
  $pathPadre      = $rssPatth->fields["radi_path"];
  $depCreadora    = substr($numRadicadoPadre, 4, $digitosDependencia);

  $ruta3  = "/$radano/$depCreadora/docs/".$ruta;

// CREACION DEL RADICADO RESPUESTA
  //Para crear el numero de radicado se realiza el siguiente procedimiento
  $isql_consec = "SELECT DEPE_RAD_TP$tipo_radicado as secuencia
                    FROM DEPENDENCIA
                    WHERE DEPE_CODI = $tpDepeRad";

  $creaNoRad   = $db->conn->Execute($isql_consec);
  $tpDepeRad   = $creaNoRad->fields["secuencia"];

  $rad = new Radicacion($db);
  $rad->radiTipoDeri  = 0;       // ok ????
  $rad->radiCuentai   = 'null';  // ok, Cuenta Interna, Oficio, Referencia
  $rad->eespCodi      = $iden;   //codigo emepresa de servicios publicos bodega
  $rad->mrecCodi      = 3;       // medio de correspondencia, 3 internet
  $rad->radiFechOfic  = 'now()'; // igual fecha radicado;
  $rad->radiNumeDeri  = $numRadicadoPadre; //ok, radicado padre
  $rad->radiPais      = $pais;   //OK, codigo pais
  $rad->descAnex      = '.';     //OK anexos
  $rad->raAsun        = "Ref " . $numRadicadoPadre; // ok asunto

  if($tipo_radicado == 1){
     $rad->radiDepeActu = $conf_DependenciaArchivo;
     $rad->radiUsuaActu = $conf_usuarioArchivo;
  }else{
      $rad->radiDepeActu  = $coddepe;   // ok dependencia actual responsable
      $rad->radiUsuaActu  = $usua_actu; // ok usuario actual responsable
  }

  $rad->radiDepeRadi  = $coddepe;   //ok dependencia que radica
  $rad->usuaCodi      = $usua_actu; // ok usuario actual responsable
  $rad->dependencia   = $coddepe;   //ok dependencia que radica
  $rad->trteCodi      =  0;         //ok, tipo de codigo de remitente
  $rad->tdocCodi      = $tdoc;      //ok, tipo documental
  $rad->tdidCodi      = 0;          //ok, ????
  $rad->carpCodi      = 1;          //ok, carpeta entradas
  $rad->carPer        = 0;          //ok, carpeta personal
  $rad->ra_asun       = "Ref " . $numRadicadoPadre;
  $rad->radiPath      = 'null';
  $rad->usuaDoc       = $radUsuaDoc;
  $codTx              = 62;



  $nurad = $rad->newRadicado($tipo_radicado, $tpDepeRad);
  if ($nurad=="-1"){
    header("Location: salidaRespuesta.php?$encabe&error=1");
      die;
  }

  $rad_salida = $nurad;
  $fecha_rad_salida = date("Y-m-d h:i");
  $sql_radi_cuentai = 'SELECT radi_cuentai FROM radicado WHERE radi_nume_radi = ' . $numRadicadoPadre;

  $rs_radi_cuentai = $db->conn->Execute($sql_radi_cuentai);
  $referencia = '';

  if (!$rs_radi_cuentai->EOF)
    $referencia = $rs_radi_cuentai->fields["RADI_CUENTAI"];

  //datos para guardar los anexos en la carpeta del nuevo radicado
  $primerno  = substr($nurad, 0, 4);
  $segundono = $_SESSION["dependencia"];
  $ruta1     = $primerno . "/" . $segundono . "/docs/";
  $adjuntos  = 'bodega/'.$ruta1;

  $nextval   = $db->nextId("sec_dir_drecciones");
  //se buscan los datos del radicado padre y se
  //insertaran en los del radicado hijo

  $isql = "insert into
                SGD_DIR_DRECCIONES(
                    SGD_TRD_CODIGO,
                    SGD_DIR_NOMREMDES,
                    SGD_DIR_DOC,
                    DPTO_CODI,
                    MUNI_CODI,
                    ID_PAIS,
                    ID_CONT,
                    SGD_DOC_FUN,
                    SGD_OEM_CODIGO,
                    SGD_CIU_CODIGO,
                    SGD_ESP_CODI,
                    RADI_NUME_RADI,
                    SGD_SEC_CODIGO,
                    SGD_DIR_DIRECCION,
                    SGD_DIR_TELEFONO,
                    SGD_DIR_MAIL,
                    SGD_DIR_TIPO,
                    SGD_DIR_CODIGO,
                    SGD_DIR_NOMBRE)
                    values( 1,
                    '$dir_nombre',
                    NULL,
                    11,
                    1,
                    170,
                    1,
                    '$usua_doc',
                    NULL,
                    NULL,
                    NULL,
                    $nurad,
                    0,
                    '$dir_direccion',
                    '$dir_telefono',
                    '$dir_mail',
                    1,
                    $nextval,
                    '$dir_nombre')";

  $dignatario       = $dir_nombre;
  $rsg              = $db->conn->Execute($isql);

  $mensajeHistorico  = "Se envia respuesta rapida";

  if(!empty($regFile)){
      $mensajeHistorico .= ", con archivos adjuntos";
  }

  //inserta el evento del radicado padre.
  $radicadosSel[0] = $numRadicadoPadre;

  $hist->insertarHistorico($radicadosSel,
                            $coddepe,
                            $usua_actu,
                            $coddepe,
                            $usua_actu,
                            $mensajeHistorico,
                            $codTx);

  //Inserta el evento del radicado de respuesta nuevo.
  $radicadosSel[0] = $nurad;

  $hist->insertarHistorico($radicadosSel,
                            $coddepe,
                            $usua_actu,
                            $coddepe,
                            $usua_actu,
                            "Nomensaje",
                            2);

  //Agregar un nuevo evento en el historico para que
  //muestre como contestado y no genere alarmas.
  //A la respuesta se le agrega el siguiente evento
  $hist->insertarHistorico($radicadosSel,
                            $coddepe,
                            $usua_actu,
                            $coddepe,
                            $usua_actu,
                            "Imagen asociada desde respuesta rapida",
                            42);

// VALIDAR DATOS ADJUNTOS
  if(!empty($_FILES["archs"]["name"][0])){
  // Arreglo para Validar la extension
  $sql1     = " selectncabe?rad_salida=$rad_sal
									anex_tipo_codi as codigo
									, anex_tipo_ext as ext
									, anex_tipo_mime as mime
							from
								anexos_tipo";

  $exte = $db->conn->Execute($sql1);

  while(!$exte->EOF) {
    $codigo     = $exte->fields["codigo"];
    $ext      = $exte->fields["ext"];
    $mime1      = $exte->fields["mime"];
    $mime2      = explode(",",$mime1);

    //arreglo para validar la extension
    $exts[".".$ext] = array ('codigo'   => $codigo,
                             'mime'   => $mime2);
    $exte->MoveNext();
  }

  //Si no existe la carpeta se crea.
  if(!is_dir($ruta_raiz."/".$adjuntos)){
    $rs = mkdir($adjuntos, 0700);
    if(empty($rs)){
      $errores .= empty($errores)? "&error=2" : '-2';
    }
  }

  $i = 0;
  $anexo = new Anexo($db);

  //Validaciones y envio para grabar archivos
  foreach($_FILES["archs"]["name"] as $key => $name){
    $nombre   = strtolower(trim($_FILES["archs"]["name"][$key]));
    $type   = trim($_FILES["archs"]["type"][$key]);
    $tamano   = trim($_FILES["archs"]["size"][$key]);
    $tmporal  = trim($_FILES["archs"]["tmp_name"][$key]);
    $error    = trim($_FILES["archs"]["error"][$key]);
    $ext    = strrchr($nombre,'.');

    if (is_array($exts[$ext])) {
      foreach ($exts[$ext]['mime'] as $value) {

        if(eregi($type,$value)) {
          $bandera = true;

          if($tamano < $tamanoMax) {
              //grabar el registro en la base de datos
            if(strlen($str) > 90) {
                $nombre = substr($nombre, 'en(..): failed to-90:');
            }

            $anexo->anex_radi_nume    = $nurad;
            $anexo->usuaCodi          = $usua_actu;
            $anexo->depe_codi         = $coddepe;
            $anexo->anex_solo_lect    = "'S'";
            $anexo->anex_tamano       = $tamano;
            $anexo->anex_creador      = "'".$usua."'";
            $anexo->anex_desc         = "Adjunto: ". $nombre;
            $anexo->anex_nomb_archivo = $nombre;
            $auxnumero                = $anexo->obtenerMaximoNumeroAnexo($nurad);
            $anexoCodigo              = $anexo->anexarFilaRadicado($auxnumero);
            $nomFinal                 = $anexo->get_anex_nomb_archivo();

            //Guardar el archivo en la carpteta ya creada
            $Grabar_path  = $adjuntos.$nomFinal;
            if (move_uploaded_file($tmporal, $ruta_raiz.$Grabar_path)) {
              //si existen adjuntos los agregamos para enviarlos por correo
              $mail->AddAttachment($ruta_raiz."/".$Grabar_path, $nombre);
            }else {
              $errores .= empty($errores)? "&error=6" : '-6';
            }
          } else {
            $errores .= empty($errores)? "&error=5" : '-5';
          }
        }
      }

      if(empty($bandera)) {
        $errores .= empty($errores)? "&error=4" : '-4';
      }
    } else {
      $errores .= empty($errores)? "&error=3" : '-3';
    }

    $contador ++;
  }
}

// AGREGAR LOS ADJUNTOS AL RADICADO
$auxnumero    = $anex->obtenerMaximoNumeroAnexo($numRadicadoPadre);

do{
    $auxnumero += 1;
    $codigo     = trim($numRadicadoPadre) . trim(str_pad($auxnumero, 5, "0", STR_PAD_LEFT));
} while ($anex->existeAnexo($anexo));

$isql = "INSERT INTO ANEXOS (SGD_REM_DESTINO,
                            ANEX_RADI_NUME,
                            ANEX_CODIGO,
                            ANEX_ESTADO,
                            ANEX_TIPO,
                            ANEX_TAMANO,
                            ANEX_SOLO_LECT,
                            ANEX_CREADOR,
                            ANEX_DESC,
                            ANEX_NUMERO,
                            ANEX_NOMB_ARCHIVO,
                            ANEX_BORRADO,
                            ANEX_SALIDA,
                            SGD_DIR_TIPO,
                            ANEX_DEPE_CREADOR,
                            SGD_TPR_CODIGO,
                            ANEX_FECH_ANEX,
                            SGD_TRAD_CODIGO,
                            RADI_NUME_SALIDA,
                            SGD_EXP_NUMERO,
                            anex_tipo_final, sgd_dir_mail, anex_tipo_envio,
                            anex_adjuntos_rr)
                    values ($radicado_rem,
                            $numRadicadoPadre,
                            '$codigo',
                            2,
                            '$tipo',
                            $tamano,
                            '$auxsololect',
                            '$usua',
                            '$descr',
                            $auxnumero,
                            '$ruta',
                            'N',
                            1,
                            $radicado_rem,
                            $coddepe,
                            0,
                            $sqlFechaHoy,
                            0,
                            $nurad,
                            NULL
                            ,$anex_tipo_final, '$mails' , $anex_tipo_envio,
                            '$anexosCodigo')";

$bien = $db->conn->Execute($isql);
$anexo = $codigo;


// Si actualizo BD correctamente
if (!$bien) {

  $errores .= empty($errores)? "&error=7" : '-7';
} else {
  $ruta   = $anexo . '.pdf';
  $actualizar_anexo = "UPDATE ANEXOS
                          SET ANEX_NOMB_ARCHIVO = '$ruta',
                          sgd_dir_mail = '$mails';
                          WHERE ANEX_CODIGO = '$anexo'";
  $anexo_result = $db->conn->Execute($actualizar_anexo);

  if (!$anexo_result)
    exit('Error actualizando el archivo');

  $ruta2  = "/bodega/$radano/$depCreadora/docs/" . $ruta;

  // Guardando el texto creado

  // Remplazar datos en el documento


  if($fecha_rad_salida) $respuesta = str_replace('F_RAD_S', $fecha_rad_salida, $respuesta);
  if($rad_salida) $respuesta = str_replace('RAD_S', $rad_salida, $respuesta);
  $respuesta = str_replace('RAD_S', $nurad, $respuesta);
  $respuesta = str_replace('*DIGNATARIO*', $dignatario, $respuesta);
  $respuesta = str_replace('*REFERENCIA*', $referencia, $respuesta);
  $respuesta = str_replace("\xe2\x80\x8b", '', $respuesta);

  $archivo_txt = $codigo . '.txt';
  $archivo_grabar_txt = $archivo_txt;
  $file_content   = fopen($archivo_grabar_txt, 'w');

  require_once($ruta_raiz."/respuestaRapida/gencodebar/html/BCGcode128.php");

/* EN ESTA PARTE CREO EL CODIGO DE BARRAS MEDIANTE DE FUENTES
 $barcodeobj = new TCPDFBarcode($nurad, 'C128');
 $barcode = $barcodeobj->getBarcodeHTML(1, 20, 'black');
 #echo $barcode;
 #echo "------------";
 $barcodeobj2 = new TCPDFBarcode($nurad, 'C128');
 $barcode2 = $barcodeobj->getBarcodeHTML(2, 20, 'black');
 #echo $barcode2;
 #echo "------------";
 $barcodeobj3 = new TCPDFBarcode($nurad, 'C128');
 $barcode3 = $barcodeobj->getBarcodeHTML(1, 40, 'black');
 #echo $barcode3;
 #echo "------------";
 */
 $barcodeobj4 = new TCPDFBarcode($nurad, 'C128');
 $barcode4 = $barcodeobj4->getBarcodeHTML(2, 40, 'black');
 //echo $barcode4;
 #exit;
 #echo "--".gettype($barcode4);
 #exit;
 $respuesta = str_replace('*'.$nurad.'*', $barcode4, $respuesta);
 $barcode4 = "";



  $write_result   = fwrite($file_content, $respuesta);
  $closing_result = fclose($file_content);
}



// CREACION DE PDF RESPUESTA AL RADICADO
$cond = "SELECT
            DEP_SIGLA,
            DEPE_NOMB
         FROM
            DEPENDENCIA
         WHERE
            DEPE_CODI = $coddepe";

$exte       = $db->conn->Execute($cond);
$dep_sig  = $exte->fields["DEP_SIGLA"];
$dep_nom  = $exte->fields["DEPE_NOMB"];

 $isqlDepR = "SELECT r.radi_depe_actu, r.radi_usua_actu ,d.depe_nomb 
    FROM dependencia d
    JOIN radicado r on r.radi_depe_actu = d.depe_codi
    WHERE r.radi_nume_radi = '$nurad'";


  $rsDepR = $db->conn->Execute($isqlDepR);

  $coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
  $codusua = $rsDepR->fields['RADI_USUA_ACTU'];
  $depnombAux = $rsDepR->fields['DEPE_NOMB'];
  

if($idPlantilla == 0) {

   $sqlGetIdPlantilla = "select idPlantilla from anexos where anex_codigo = '$anexo'";
   $rsPlan = $db->conn->Execute($sqlGetIdPlantilla);
  if (!$rsPlan->EOF) {
    $idPlantilla = $rsPlan->fields["IDPLANTILLA"];
  }
}

  //Radicar según id de plantilla
  if($idPlantilla == 100000){
     include ('./generadorpdf/resolucion/resolucionradrespuesta.php');
  } elseif($idPlantilla == 100001) {
    include ('./generadorpdf/ADFL03/ADFL03radrespuesta.php');
  } elseif($idPlantilla == 100002) {
    include ('./generadorpdf/AIFT02/AIFT02radrespuesta.php');
  } elseif($idPlantilla == 100003) {
    include ('./generadorpdf/CJFL01/CJFL01radrespuesta.php');
  } elseif($idPlantilla == 100004) {
    include ('./generadorpdf/CJFL02/CJFL02radrespuesta.php');
  } elseif($idPlantilla == 100005) {
    include ('./generadorpdf/CJFL04/CJFL04radrespuesta.php');
  }  elseif($idPlantilla == 100006) {
    include ('./generadorpdf/CJFL11/CJFL11radrespuesta.php');
  } elseif($idPlantilla == 100007) {
    include ('./generadorpdf/CJFL14/CJFL14radrespuesta.php');
  } elseif($idPlantilla == 100008) {
    include ('./generadorpdf/CJFL17/CJFL17radrespuesta.php');
  } elseif($idPlantilla == 100009) {
    include ('./generadorpdf/CJFL22/CJFL22radrespuesta.php');
  } elseif($idPlantilla == 100010) {
    include ('./generadorpdf/GDFL02/GDFL02radrespuesta.php');
  } elseif($idPlantilla == 100011) {
    include ('./generadorpdf/GDFL03/GDFL03rarespuesta.php');
  } elseif($idPlantilla == 100012) {
    include ('./generadorpdf/Salida/salidapradrespuesta.php');
  } elseif($idPlantilla == 100013) {
    include ('./generadorpdf/Memorando/memorandopradrespuesta.php');
  } elseif($idPlantilla == 100016) {
    include ('./generadorpdf/CJFL12/CJFL12radrespuesta.php');
  } elseif($idPlantilla == 100017) {
    include ('./generadorpdf/CJFL13/CJFL13radrespuesta.php');
  }else {

class MYPDF extends TCPDF {

  //Page header
  public function Header() {

            $this->Image('../bodega'.$_SESSION["headerRtaPdf"],
            25,
            3,
            160,
            0,
            'png',
            '',
            'T',
            false,
            300,
            '',
            false,
            false,
            0,
            false,
            false,
            false);
  }

  // Page footer
  public function Footer() {
      global $entidad_dir, $entidad_tel, $httpWebOficial;
    // Position at 15 mm from bottom
      $tbl = '
      <table style="width:100%">
          <tr>
            <td colspan="3" width:80% ><img src="'.dirname(__DIR__, 1).'/bodega/sys_img/FooterUpLine.PNG"/></td>
          </tr>
          <tr>
            <td style="width:80%">
                <br>
                Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages() . '<br>
                '.$entidad_dir.' | '.$entidad_tel.' <br>
                '.$httpWebOficial.'<br>
                CIFL02
            </td>
            <td style="width:2%"><img src="../bodega/sys_img/FooterSideLine.PNG"/></td>
            <td style="width:18%" align="center">
                
            </td>
          </tr>
        </table>';
        $this->SetY(-25);
        $this->SetFont ('helvetica', '', 8 , '', 'default', true );
        $this->writeHTML($tbl, true, false, false, false, '');
        $this->Image(dirname(__DIR__, 1).'/bodega/sys_img/FooterLogoSGS.PNG', 170, 257, 30, 22, 'PNG', '', 'T', false, 200, '', false, false, 0, false, false, false);
  }
}

// create new PDF document
$pdf = new MYPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($setAutor);
$pdf->SetTitle($SetTitle);
$pdf->SetSubject($SetSubject);
$pdf->SetKeywords($SetKeywords);

//Agrego la Fuente para el CODIGO DE BARRASS
//$pdf->addTTFfont($ruta_raiz.'/tcpdf/code128.ttf', '', '', 32);//TrueTypeUnicode
//$pdf->SetFont('code128', '', 20, '', true) ;
// Standard 2 of 5

$pdf->SetFont('helvetica', '', 10);

// define barcode style
$style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => true,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => 'auto',
    'vpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => false,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 4
);


// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);



//set some language-dependent strings
$pdf->setLanguageArray($l);

// set default font subsetting mode
$pdf->setFontSubsetting(true);


// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// CODE 128 AUTO


//$pdf->Ln(2);

//RADICACIÓN CON LA FUNCIÓN TCPDF
//$style['position'] = 'R';

//$pdf->write1DBarcode($nurad, 'C39', '', '', '', 7, 0.2, $style, 'N');

// output the HTML content
$pdf->writeHTML($respuesta, true, false, true, false, '');


// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output($ruta_raiz.$ruta2, 'F');

}

$ruta3  = "/$radano/$depCreadora/docs/".$ruta;

$sqlE = "UPDATE
            RADICADO
         SET
            RADI_PATH = '$ruta3'
         WHERE
            RADI_NUME_RADI = $nurad";

  $db->conn->Execute($sqlE);

  
 
?>
