<?php

   include ('encabezadomemorando.php');


      // Extend the TCPDF class to create custom Header and Footer
      class MYPDF extends TCPDF {
        //Page header
        public function Header() {
          // Logo
          $this->Image('../bodega'.$_SESSION["headerRtaPdf"],
                        25,
                        3,
                        167,
                        '',
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
      
        $this->SetY(-30);  
        $this->Line($this->getX(), $this->getY() , $this->getPageWidth() - 22,  $this->getY());
        $pagAct = $this->getAliasNumPage();
        $pagDe =  $this->getAliasNbPages();
        $this->SetTextColor(72, 72, 72); // Grey
        $this->SetFont('helvetica', 'B', 8);
        $this->Cell(0, 0, '                  Página ' . $pagAct . ' de ' . $pagDe . ' – Comunicación Interna', 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->Ln(3);
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 0, 'Carrera 68 A # 24 B-10, Torre 3 - Pisos 4, 9 y 10' , 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->Ln(3);
        $this->Cell(0, 0, 'PBX (571) 744 2000 • Bogotá' , 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->Ln(3);
    $this->Cell(0, 0, 'www.supersalud.gov.co' , 0, false, 'C', 0, '', 0, false, 'T', 'M');
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

      /*$style = array(
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
      

      $style['position'] = 'R';
      $pdf->write1DBarcode($nurad, 'C39', '', '', '', 7, 0.2, $style, 'N');*/

      // output the HTML content
      $marginsAux = $pdf->getMargins();
      $pdf->SetLeftMargin(120);
      $pdf->writeHTML($encabezadoMemorando, true, false, true, false, '');
      $pdf->SetLeftMargin(PDF_MARGIN_LEFT);
      $pdf->writeHTML('', true, false, true, false, '');
      // Close and output PDF document
      // This method has several options, check the source code documentation for more information.
      $pdf->writeHTML($asu, true, false, true, false, '');
      $pdf->Output($ruta_raiz.$ruta2, 'F');



?>