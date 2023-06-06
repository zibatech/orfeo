<?php
    error_reporting(E_ALL);
  //require '../tcpdf/config/lang/eng.php';

  require '../tcpdf/tcpdf.php';
  require '../processConfig.php';

  foreach ($_GET as $key => $valor) {
    ${$key} = $valor;
  }  
  foreach ($_POST as $key => $valor) {
    ${$key} = $valor;
  } 



  define ('AUTOR_PDF',    'Sistema de Gesti&oacute;n Documental Orfeo-Correlibre '.$entidad);
  define ('TITULO_PDF',   'Respuesta a solicitud');
  define ('ASUNTO_PDF',   $entidad);
  define ('KEYWORDS_PDF', 'metrovivienda, respuesta, salida, generar');
  define ('IMAGEN_PDF', '../img/'.$entidad.'.banerPDF.JPG');
  define ('ENTIDAD_DIR', $entidad_dir);
  define ('ENTIDAD_TEL', $entidad_tel);
  define ('PDF_MARGIN_TOP', 0);
  define ('PDF_MARGIN_RIGHT', 0);

if($idPlantilla == 0) {

   $sqlGetIdPlantilla = "select idPlantilla from anexos where anex_codigo = '$anexo'";
   $rsPlan = $db->conn->Execute($sqlGetIdPlantilla);
  if (!$rsPlan->EOF) {
    $idPlantilla = $rsPlan->fields["IDPLANTILLA"];
  }
}  


  //Visualización y guardado según idPlnatilla
  if($idPlantilla == 100000){
    include ('./generadorpdf/resolucion/resolucionprevguar.php');
  } elseif($idPlantilla == 100001) {
    include ('./generadorpdf/ADFL03/ADFL03prevguar.php');
  } elseif($idPlantilla == 100002) {
    include ('./generadorpdf/AIFT02/AIFT02prevguar.php');
  } elseif($idPlantilla == 100003) {
    include ('./generadorpdf/CJFL01/CJFL01prevguar.php');
  } elseif($idPlantilla == 100004) {
    include ('./generadorpdf/CJFL02/CJFL02prevguar.php');
  } elseif($idPlantilla == 100005) {
    include ('./generadorpdf/CJFL04/CJFL04prevguar.php');
  }  elseif($idPlantilla == 100006) {
    include ('./generadorpdf/CJFL11/CJFL11prevguar.php');
  } elseif($idPlantilla == 100007) {
    include ('./generadorpdf/CJFL14/CJFL14prevguar.php');
  } elseif($idPlantilla == 100008) {
    include ('./generadorpdf/CJFL17/CJFL17prevguar.php');
  } elseif($idPlantilla == 100009) {
    include ('./generadorpdf/CJFL22/CJFL22prevguar.php');
  } elseif($idPlantilla == 100010) {
    include ('./generadorpdf/GDFL02/GDFL02prevguar.php');
  } elseif($idPlantilla == 100011) {
    include ('./generadorpdf/GDFL03/GDFL03prevguar.php');
  } elseif($idPlantilla == 100012) {
    include ('./generadorpdf/Salida/salidaprevguar.php');
  } elseif($idPlantilla == 100013) {
    include ('./generadorpdf/Memorando/memorandoprevguar.php');
  } elseif($idPlantilla == 100016) {
    include ('./generadorpdf/CJFL12/CJFL12prevguar.php');
  } elseif($idPlantilla == 100017) {
    include ('./generadorpdf/CJFL13/CJFL13prevguar.php');
  }
   else {


      // Extend the TCPDF class to create custom Header and Footer
      class MYPDF extends TCPDF {
        //Page header
        public function Header() {
          // Logo
          $this->Image('../bodega'.$_SESSION["headerRtaPdf"],
                      6,
                      3,
                      205,
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
          // Position at 15 mm from bottom
          $tbl = '
          <table style="width:100%">
              <tr>
                <td colspan="3" width:80% ><img src="'.dirname(__DIR__, 1).'/bodega/sys_img/FooterUpLine.PNG"/></td>
              </tr>
              <tr>
                <td style="width:100%">
                    Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages() . '<br>
                </td>
              </tr>
            </table>';
            $this->SetY(-35);
            $this->SetFont ('helvetica', '', 8 , '', 'default', true );
            $this->writeHTML($tbl, true, false, false, false, '');
            $this->Image(dirname(__DIR__, 1).'/bodega/sys_img/FooterLogoSGS.PNG', 6, 255, 205, 22, 'PNG', '', 'T', false, 200, '', false, false, 0, false, false, false);
        }
      }
    #$respuesta = $respuesta
      // create new PDF document
      $pdf = new MYPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);

      // set document information
      $pdf->SetCreator(PDF_CREATOR);
      $pdf->SetAuthor(AUTOR_PDF);
      $pdf->SetTitle(TITULO_PDF);
      $pdf->SetSubject(ASUNTO_PDF);
      $pdf->SetKeywords(KEYWORDS_PDF);

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
      //  echo "<hr> $radicado_salida <hr>";
      $style['position'] = 'R';
      //$pdf->write1DBarcode($radicado_salida, 'C39', '', '', '', 7, 0.2, $style, 'N');

      // output the HTML content
      $pdf->writeHTML($respuesta, true, false, true, false, '');

      // Close and output PDF document
      // This method has several options, check the source code documentation for more information.
      $pdf_result = $pdf->Output($archivo_grabar, 'F');

}